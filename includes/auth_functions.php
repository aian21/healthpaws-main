<?php
// HealthPaws Authentication Functions
// Suppress error display to ensure clean output
error_reporting(0);
ini_set('display_errors', 0);

session_start();

try {
    require_once __DIR__ . '/../config/database.php';
} catch (Exception $e) {
    // Log error but don't display it
    error_log("Database config error: " . $e->getMessage());
    throw new Exception('Database configuration error');
}

class Auth {
    private $db;
    
    public function __construct() {
        try {
            $this->db = new Database();
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception('Database connection failed');
        }
    }
    
    /**
     * Register a new clinic and owner account
     */
    public function registerClinic($data) {
        try {
            error_log("🔍 registerClinic called with data: " . json_encode($data));
            
            $conn = $this->db->getConnection();
            if (!$conn) {
                error_log("❌ Database connection failed");
                throw new Exception('Database connection failed');
            }
            
            error_log("✅ Database connection successful");
            
            $conn->beginTransaction();
            error_log("🔄 Transaction started");
            
            // Validate required fields
            $required_fields = ['clinic_name', 'business_email', 'subdomain', 'owner_password', 'plan'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    error_log("❌ Missing required field: $field");
                    throw new Exception("Missing required field: $field");
                }
            }
            
            error_log("✅ Required fields validated");
            
            // Check if subdomain is available
            if (!$this->isSubdomainAvailable($data['subdomain'])) {
                error_log("❌ Subdomain not available: " . $data['subdomain']);
                throw new Exception("Subdomain '{$data['subdomain']}' is already taken");
            }
            
            error_log("✅ Subdomain availability checked: " . $data['subdomain']);
            
            // Check if email is already registered
            if ($this->isEmailRegistered($data['business_email'])) {
                error_log("❌ Email already registered: " . $data['business_email']);
                throw new Exception("Email '{$data['business_email']}' is already registered");
            }
            
            error_log("✅ Email availability checked: " . $data['business_email']);
            
            // Create clinic
            error_log("🏥 Creating clinic...");
            $clinic_id = $this->createClinic($data);
            error_log("✅ Clinic created with ID: " . $clinic_id);
            
            // Check if user already exists (from email verification)
            $existing_user = $this->getVerifiedUser($data['business_email']);
            if ($existing_user) {
                // Update existing user with password and username
                $user_id = $this->updateUserForRegistration($existing_user['user_id'], $data['business_email'], $data['owner_password']);
                error_log("✅ Updated existing user with ID: " . $user_id);
            } else {
                // Create new user account
                error_log("👤 Creating user account...");
                $user_id = $this->createUser($data['business_email'], $data['owner_password']);
                error_log("✅ User created with ID: " . $user_id);
            }
            
            // Create owner record
            error_log("👨‍💼 Creating owner record...");
            $owner_id = $this->createOwner($user_id, $data);
            error_log("✅ Owner created with ID: " . $owner_id);
            
            // Create user-clinic relationship with Admin role
            error_log("🔗 Creating UserClinic relationship...");
            $this->createUserClinic($user_id, $clinic_id, 1); // Role ID 1 = Admin
            error_log("✅ UserClinic relationship created");
            
            // Create subscription
            error_log("💳 Creating subscription...");
            $this->createSubscription($clinic_id, $data['plan']);
            error_log("✅ Subscription created");
            
            // Create default veterinarian record if provided
            if (!empty($data['vet_fname']) && !empty($data['vet_lname'])) {
                error_log("🐾 Creating veterinarian record...");
                $this->createVeterinarian($user_id, $clinic_id, $data);
                error_log("✅ Veterinarian record created");
            }
            
            error_log("💾 Committing transaction...");
            $conn->commit();
            error_log("🎉 Transaction committed successfully");
            
            $result = [
                'success' => true,
                'clinic_id' => $clinic_id,
                'user_id' => $user_id,
                'subdomain' => $data['subdomain']
            ];
            
            error_log("📤 Returning success result: " . json_encode($result));
            return $result;
            
        } catch (Exception $e) {
            error_log("💥 Registration error in registerClinic: " . $e->getMessage());
            error_log("💥 Error trace: " . $e->getTraceAsString());
            
            if (isset($conn) && $conn instanceof PDO) {
                try {
                    error_log("🔄 Rolling back transaction...");
                    $conn->rollback();
                    error_log("✅ Transaction rolled back");
                } catch (Exception $rollbackError) {
                    error_log("❌ Rollback error: " . $rollbackError->getMessage());
                }
            }
            
            $errorResult = [
                'success' => false,
                'error' => $e->getMessage()
            ];
            
            error_log("📤 Returning error result: " . json_encode($errorResult));
            return $errorResult;
        }
    }
    
    /**
     * Create a new clinic
     */
    private function createClinic($data) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO Clinic (clinic_name, address, clinic_phone, clinic_email, clinic_subdomain) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['clinic_name'],
            $data['address'] ?? '',
            $data['clinic_phone'] ?? '',
            $data['business_email'],
            $data['subdomain']
        ]);
        
        return $conn->lastInsertId();
    }
    
    /**
     * Create a new user account
     */
    private function createUser($email, $password) {
        $conn = $this->db->getConnection();
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("
            INSERT INTO User (username, email, password, email_verified) 
            VALUES (?, ?, ?, TRUE)
        ");
        
        $stmt->execute([
            $email, // Use email as username for now
            $email,
            $hashed_password
        ]);
        
        return $conn->lastInsertId();
    }
    
    /**
     * Create owner record
     */
    private function createOwner($user_id, $data) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO Owner (user_id, owner_fname, owner_lname, owner_phone) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $user_id,
            $data['owner_fname'] ?? 'Owner',
            $data['owner_lname'] ?? 'Admin',
            $data['owner_phone'] ?? ''
        ]);
        
        return $conn->lastInsertId();
    }
    
    /**
     * Create veterinarian record
     */
    private function createVeterinarian($user_id, $clinic_id, $data) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO Veterinarian (user_id, vet_fname, vet_lname, specialization, license_number, clinic_id) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $user_id,
            $data['vet_fname'],
            $data['vet_lname'],
            $data['specialization'] ?? 'General Practice',
            $data['license_number'] ?? '',
            $clinic_id
        ]);
        
        return $conn->lastInsertId();
    }
    
    /**
     * Create user-clinic relationship with role
     */
    private function createUserClinic($user_id, $clinic_id, $role_id) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO UserClinic (user_id, clinic_id, role_id) 
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([$user_id, $clinic_id, $role_id]);
    }
    
    /**
     * Create subscription
     */
    private function createSubscription($clinic_id, $plan) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO Subscription (clinic_id, start_date, end_date, status, plan) 
            VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'Trial', ?)
        ");
        
        $stmt->execute([$clinic_id, $plan]);
    }
    
    /**
     * Check if email is already registered (has completed full registration)
     */
    public function isEmailRegistered($email) {
        try {
            $conn = $this->db->getConnection();
            
            // Check if user exists AND has a real password (meaning they've completed registration)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as count 
                FROM User 
                WHERE email = ? AND password IS NOT NULL AND password != '' AND password != 'TEMP_VERIFICATION'
            ");
            $stmt->execute([$email]);
            
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Email check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if subdomain is available
     */
    public function isSubdomainAvailable($subdomain) {
        try {
            $conn = $this->db->getConnection();
            
            // Check if subdomain is already taken
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Clinic WHERE clinic_subdomain = ?");
            $stmt->execute([$subdomain]);
            
            $result = $stmt->fetch();
            return $result['count'] === 0;
        } catch (Exception $e) {
            error_log("Subdomain check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Authenticate user login
     */
    public function login($email, $password) {
        try {
            $conn = $this->db->getConnection();
            
            // Get user with role and clinic information through UserClinic table
            $stmt = $conn->prepare("
                SELECT u.*, r.role_name, c.clinic_id, c.clinic_name, c.clinic_subdomain, uc.role_id
                FROM User u
                INNER JOIN UserClinic uc ON u.user_id = uc.user_id
                INNER JOIN Role r ON uc.role_id = r.role_id
                INNER JOIN Clinic c ON uc.clinic_id = c.clinic_id
                WHERE u.email = ?
                LIMIT 1
            ");
            
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'error' => 'Invalid email or password'
                ];
            }
            
            if (!$user['email_verified']) {
                return [
                    'success' => false,
                    'error' => 'Please verify your email before logging in'
                ];
            }
            
            // Set session data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['clinic_id'] = $user['clinic_id'];
            $_SESSION['clinic_name'] = $user['clinic_name'];
            $_SESSION['clinic_subdomain'] = $user['clinic_subdomain'];
            
            return [
                'success' => true,
                'user' => $user
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Login failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        try {
            session_destroy();
            return ['success' => true];
        } catch (Exception $e) {
            error_log("Logout error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user data
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $conn = $this->db->getConnection();
            
            $stmt = $conn->prepare("
                SELECT u.*, r.role_name, c.clinic_id, c.clinic_name, c.clinic_subdomain
                FROM User u
                INNER JOIN UserClinic uc ON u.user_id = uc.user_id
                INNER JOIN Role r ON uc.role_id = r.role_id
                INNER JOIN Clinic c ON uc.clinic_id = c.clinic_id
                WHERE u.user_id = ?
            ");
            
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get current user error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Send verification email (placeholder)
     */
    public function sendVerificationEmail($email, $code) {
        // In a real system, this would send an actual email
        // For now, we'll just return success
        return [
            'success' => true,
            'message' => "Verification code sent to $email (Demo: $code)"
        ];
    }
    
    /**
     * Get verified user by email (who hasn't completed full registration yet)
     */
    private function getVerifiedUser($email) {
        try {
            $conn = $this->db->getConnection();
            
            $stmt = $conn->prepare("
                SELECT user_id, email, email_verified 
                FROM User 
                WHERE email = ? AND email_verified = TRUE 
                AND password = 'TEMP_VERIFICATION'
                LIMIT 1
            ");
            
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get verified user error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update user password
     */
    private function updateUserPassword($user_id, $password) {
        try {
            $conn = $this->db->getConnection();
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("
                UPDATE User 
                SET password = ? 
                WHERE user_id = ?
            ");
            
            $stmt->execute([$hashed_password, $user_id]);
            return $user_id;
        } catch (Exception $e) {
            error_log("Update user password error: " . $e->getMessage());
            throw new Exception('Failed to update user password');
        }
    }
    
    /**
     * Update user for registration (password and username)
     */
    private function updateUserForRegistration($user_id, $email, $password) {
        try {
            $conn = $this->db->getConnection();
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("
                UPDATE User 
                SET username = ?, password = ?, email_verified = TRUE 
                WHERE user_id = ?
            ");
            
            $stmt->execute([$email, $hashed_password, $user_id]);
            return $user_id;
        } catch (Exception $e) {
            error_log("Update user for registration error: " . $e->getMessage());
            throw new Exception('Failed to update user for registration');
        }
    }
    
    /**
     * Verify email with code
     */
    public function verifyEmail($email, $code) {
        try {
            $conn = $this->db->getConnection();
            
            // For demo purposes, accept any 6-digit code
            if (strlen($code) === 6 && is_numeric($code)) {
                $stmt = $conn->prepare("
                    UPDATE User 
                    SET email_verified = TRUE, verification_code = NULL 
                    WHERE email = ?
                ");
                
                $stmt->execute([$email]);
                
                return [
                    'success' => true,
                    'message' => 'Email verified successfully'
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Invalid verification code'
            ];
            
        } catch (Exception $e) {
            error_log("Email verification error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Verification failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Register a new pet owner account
     */
    public function registerPetOwner($data) {
        try {
            error_log("🔍 registerPetOwner called with data: " . json_encode($data));
            
            $conn = $this->db->getConnection();
            if (!$conn) {
                error_log("❌ Database connection failed");
                throw new Exception('Database connection failed');
            }
            
            error_log("✅ Database connection successful");
            
            $conn->beginTransaction();
            error_log("🔄 Transaction started");
            
            // Validate required fields
            $required_fields = ['firstName', 'lastName', 'email', 'phone', 'address', 'password', 'petName', 'species'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    error_log("❌ Missing required field: $field");
                    throw new Exception("Missing required field: $field");
                }
            }
            error_log("✅ Required fields validation passed");
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT user_id FROM User WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                error_log("❌ Email already exists: " . $data['email']);
                throw new Exception('An account with this email already exists');
            }
            error_log("✅ Email uniqueness check passed");
            
            // Generate username from email
            $username = strtolower(explode('@', $data['email'])[0]);
            $original_username = $username;
            $counter = 1;
            
            // Ensure username uniqueness
            while (true) {
                $stmt = $conn->prepare("SELECT user_id FROM User WHERE username = ?");
                $stmt->execute([$username]);
                if (!$stmt->fetch()) {
                    break;
                }
                $username = $original_username . $counter;
                $counter++;
            }
            error_log("✅ Generated unique username: $username");
            
            // Hash password
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            error_log("✅ Password hashed");
            
            // Create User record
            $stmt = $conn->prepare("
                INSERT INTO User (username, email, password, email_verified, created_at) 
                VALUES (?, ?, ?, TRUE, NOW())
            ");
            $stmt->execute([$username, $data['email'], $hashed_password]);
            $user_id = $conn->lastInsertId();
            error_log("✅ User created with ID: $user_id");
            
            // Assign Owner role
            $stmt = $conn->prepare("SELECT role_id FROM Role WHERE role_name = 'Owner'");
            $stmt->execute();
            $owner_role = $stmt->fetch();
            
            if (!$owner_role) {
                error_log("❌ Owner role not found");
                throw new Exception('Owner role not found in system');
            }
            
            $stmt = $conn->prepare("
                INSERT INTO UserRole (user_id, role_id) 
                VALUES (?, ?)
            ");
            $stmt->execute([$user_id, $owner_role['role_id']]);
            error_log("✅ Owner role assigned");
            
            // Create Owner record with enhanced fields
            $stmt = $conn->prepare("
                INSERT INTO Owner (
                    user_id, owner_fname, owner_lname, owner_phone, 
                    address, city, state, postal_code, country,
                    preferred_language, timezone, is_active, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, '', '', '', 'Philippines', 'en', 'Asia/Manila', TRUE, NOW(), NOW())
            ");
            $stmt->execute([
                $user_id,
                $data['firstName'],
                $data['lastName'],
                $data['phone'],
                $data['address']
            ]);
            $owner_id = $conn->lastInsertId();
            error_log("✅ Owner created with ID: $owner_id");
            
            // Create Pet record
            $birthday = !empty($data['birthday']) ? $data['birthday'] : null;
            $weight = !empty($data['weight']) ? floatval($data['weight']) : null;
            
            $stmt = $conn->prepare("
                INSERT INTO Pet (
                    owner_id, pet_name, species, breed, gender, birthday, weight,
                    microchip_number, insurance_provider, insurance_policy_number,
                    special_needs, is_active, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE, NOW(), NOW())
            ");
            $stmt->execute([
                $owner_id,
                $data['petName'],
                $data['species'],
                $data['breed'],
                $data['gender'],
                $birthday,
                $weight,
                $data['microchipNumber'],
                $data['insuranceProvider'],
                $data['insurancePolicyNumber'],
                $data['specialNeeds']
            ]);
            $pet_id = $conn->lastInsertId();
            error_log("✅ Pet created with ID: $pet_id");
            
            // Generate Digital Pet Card
            $card_code = $this->generateDigitalCardCode($pet_id);
            $stmt = $conn->prepare("
                INSERT INTO DigitalPetCard (pet_id, card_code, card_status, issued_date, access_count)
                VALUES (?, ?, 'Active', NOW(), 0)
            ");
            $stmt->execute([$pet_id, $card_code]);
            error_log("✅ Digital pet card created with code: $card_code");
            
            // Create Emergency Contact if provided
            if (!empty($data['emergencyName']) && !empty($data['emergencyPhone'])) {
                $stmt = $conn->prepare("
                    INSERT INTO EmergencyContact (
                        owner_id, contact_name, relationship, phone, 
                        is_primary, is_active, created_at
                    ) VALUES (?, ?, ?, ?, TRUE, TRUE, NOW())
                ");
                $stmt->execute([
                    $owner_id,
                    $data['emergencyName'],
                    $data['emergencyRelationship'],
                    $data['emergencyPhone']
                ]);
                error_log("✅ Emergency contact created");
            }
            
            // Create Notification Preferences
            $notification_types = [
                'vaccination_reminder' => 7,
                'appointment_reminder' => 1,
                'checkup_reminder' => 30,
                'medical_update' => 0
            ];
            
            $selected_notifications = $data['notifications'] ?? [];
            $notification_method = $data['notificationMethod'] ?? 'email';
            
            foreach ($notification_types as $type => $default_days) {
                $is_enabled = in_array($type, $selected_notifications);
                
                $stmt = $conn->prepare("
                    INSERT INTO NotificationPreference (
                        owner_id, notification_type, delivery_method, 
                        is_enabled, advance_days, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([
                    $owner_id,
                    $type,
                    $notification_method,
                    $is_enabled,
                    $default_days
                ]);
            }
            error_log("✅ Notification preferences created");
            
            // Log the registration activity
            $stmt = $conn->prepare("
                INSERT INTO OwnerActivityLog (
                    owner_id, pet_id, activity_type, description, 
                    ip_address, user_agent, created_at
                ) VALUES (?, ?, 'registration', 'Pet owner account created', ?, ?, NOW())
            ");
            $stmt->execute([
                $owner_id,
                $pet_id,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            error_log("✅ Activity logged");
            
            $conn->commit();
            error_log("✅ Transaction committed successfully");
            
            return [
                'success' => true,
                'user_id' => $user_id,
                'owner_id' => $owner_id,
                'pet_id' => $pet_id,
                'digital_card_code' => $card_code,
                'message' => 'Pet owner account created successfully'
            ];
            
        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollback();
                error_log("🔄 Transaction rolled back due to error");
            }
            error_log("❌ registerPetOwner error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate a unique digital card code for a pet
     */
    private function generateDigitalCardCode($pet_id) {
        // Generate a unique code: HP (HealthPaws) + YYYY + random string + pet_id
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return "HP{$year}{$random}" . str_pad($pet_id, 4, '0', STR_PAD_LEFT);
    }
}

// Initialize auth instance
try {
    $auth = new Auth();
    error_log("✅ Auth instance created successfully");
} catch (Exception $e) {
    error_log("❌ Failed to create Auth instance: " . $e->getMessage());
    $auth = null;
}
?>
