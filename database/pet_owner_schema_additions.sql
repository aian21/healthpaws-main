-- Pet Owner Schema Enhancements
-- Additional tables and modifications needed for pet owner functionality

-- 1. Digital Pet Card Table
-- Stores QR code and digital card information for each pet
CREATE TABLE DigitalPetCard (
    card_id INT PRIMARY KEY AUTO_INCREMENT,
    pet_id INT NOT NULL,
    card_code VARCHAR(255) UNIQUE NOT NULL, -- QR code identifier
    card_status VARCHAR(50) DEFAULT 'Active', -- Active, Inactive, Lost, Replaced
    issued_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_accessed TIMESTAMP,
    access_count INT DEFAULT 0,
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id) ON DELETE CASCADE,
    INDEX idx_card_code (card_code),
    INDEX idx_pet_card (pet_id)
);

-- 2. Emergency Contacts Table
-- Multiple emergency contacts per pet owner
CREATE TABLE EmergencyContact (
    contact_id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    contact_name VARCHAR(255) NOT NULL,
    relationship VARCHAR(100), -- Friend, Family, Neighbor, etc.
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    address TEXT,
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES Owner(owner_id) ON DELETE CASCADE,
    INDEX idx_owner_emergency (owner_id)
);

-- 3. Data Sharing Permissions Table
-- Controls what data pet owners share with which clinics/vets
CREATE TABLE DataSharingPermission (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    pet_id INT NOT NULL,
    clinic_id INT,
    vet_id INT,
    permission_type VARCHAR(50) NOT NULL, -- 'full', 'medical_only', 'vaccination_only', 'basic_info'
    can_view_medical_records BOOLEAN DEFAULT FALSE,
    can_view_vaccinations BOOLEAN DEFAULT TRUE,
    can_view_appointments BOOLEAN DEFAULT TRUE,
    can_add_records BOOLEAN DEFAULT FALSE,
    can_schedule_appointments BOOLEAN DEFAULT FALSE,
    granted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_date TIMESTAMP NULL, -- NULL means no expiration
    is_active BOOLEAN DEFAULT TRUE,
    granted_by_owner_id INT NOT NULL,
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES Clinic(clinic_id) ON DELETE CASCADE,
    FOREIGN KEY (vet_id) REFERENCES Veterinarian(vet_id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by_owner_id) REFERENCES Owner(owner_id),
    INDEX idx_pet_permissions (pet_id),
    INDEX idx_clinic_permissions (clinic_id),
    INDEX idx_vet_permissions (vet_id)
);

-- 4. Notification Preferences Table
-- Pet owner notification settings
CREATE TABLE NotificationPreference (
    preference_id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    notification_type VARCHAR(100) NOT NULL, -- 'vaccination_reminder', 'appointment_reminder', 'medical_update', etc.
    delivery_method VARCHAR(50) NOT NULL, -- 'email', 'sms', 'push', 'none'
    is_enabled BOOLEAN DEFAULT TRUE,
    advance_days INT DEFAULT 7, -- How many days in advance to notify
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES Owner(owner_id) ON DELETE CASCADE,
    UNIQUE KEY unique_owner_notification (owner_id, notification_type),
    INDEX idx_owner_preferences (owner_id)
);

-- 5. Pet Health Alerts Table
-- System-generated health alerts and reminders
CREATE TABLE PetHealthAlert (
    alert_id INT PRIMARY KEY AUTO_INCREMENT,
    pet_id INT NOT NULL,
    alert_type VARCHAR(100) NOT NULL, -- 'vaccination_due', 'checkup_due', 'medication_reminder', etc.
    title VARCHAR(255) NOT NULL,
    message TEXT,
    priority VARCHAR(20) DEFAULT 'medium', -- 'low', 'medium', 'high', 'urgent'
    due_date DATE,
    is_read BOOLEAN DEFAULT FALSE,
    is_dismissed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    dismissed_at TIMESTAMP NULL,
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id) ON DELETE CASCADE,
    INDEX idx_pet_alerts (pet_id),
    INDEX idx_alert_due_date (due_date),
    INDEX idx_unread_alerts (is_read, is_dismissed)
);

-- 6. Pet Owner Activity Log
-- Track important actions for security and audit purposes
CREATE TABLE OwnerActivityLog (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    pet_id INT,
    activity_type VARCHAR(100) NOT NULL, -- 'login', 'data_shared', 'permission_granted', 'card_accessed', etc.
    description TEXT,
    ip_address VARCHAR(45), -- IPv4 or IPv6
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES Owner(owner_id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES Pet(pet_id) ON DELETE SET NULL,
    INDEX idx_owner_activity (owner_id),
    INDEX idx_activity_date (created_at),
    INDEX idx_activity_type (activity_type)
);

-- 7. Enhance existing Pet table with additional fields for pet owners
ALTER TABLE Pet ADD COLUMN microchip_number VARCHAR(50);
ALTER TABLE Pet ADD COLUMN insurance_provider VARCHAR(255);
ALTER TABLE Pet ADD COLUMN insurance_policy_number VARCHAR(100);
ALTER TABLE Pet ADD COLUMN special_needs TEXT; -- Allergies, medications, behavioral notes
ALTER TABLE Pet ADD COLUMN is_active BOOLEAN DEFAULT TRUE;
ALTER TABLE Pet ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE Pet ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add indexes for better performance
ALTER TABLE Pet ADD INDEX idx_microchip (microchip_number);
ALTER TABLE Pet ADD INDEX idx_owner_pets (owner_id);

-- 8. Enhance Owner table with additional fields
ALTER TABLE Owner ADD COLUMN address TEXT;
ALTER TABLE Owner ADD COLUMN city VARCHAR(100);
ALTER TABLE Owner ADD COLUMN state VARCHAR(100);
ALTER TABLE Owner ADD COLUMN postal_code VARCHAR(20);
ALTER TABLE Owner ADD COLUMN country VARCHAR(100) DEFAULT 'Philippines';
ALTER TABLE Owner ADD COLUMN date_of_birth DATE;
ALTER TABLE Owner ADD COLUMN preferred_language VARCHAR(10) DEFAULT 'en';
ALTER TABLE Owner ADD COLUMN timezone VARCHAR(50) DEFAULT 'Asia/Manila';
ALTER TABLE Owner ADD COLUMN is_active BOOLEAN DEFAULT TRUE;
ALTER TABLE Owner ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE Owner ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add indexes
ALTER TABLE Owner ADD INDEX idx_owner_location (city, state);
ALTER TABLE Owner ADD INDEX idx_owner_active (is_active);

-- Insert default notification preferences for existing notification types
INSERT INTO NotificationPreference (owner_id, notification_type, delivery_method, advance_days) 
SELECT owner_id, 'vaccination_reminder', 'email', 7 FROM Owner WHERE owner_id NOT IN (SELECT owner_id FROM NotificationPreference WHERE notification_type = 'vaccination_reminder');

INSERT INTO NotificationPreference (owner_id, notification_type, delivery_method, advance_days) 
SELECT owner_id, 'appointment_reminder', 'email', 1 FROM Owner WHERE owner_id NOT IN (SELECT owner_id FROM NotificationPreference WHERE notification_type = 'appointment_reminder');

INSERT INTO NotificationPreference (owner_id, notification_type, delivery_method, advance_days) 
SELECT owner_id, 'checkup_reminder', 'email', 30 FROM Owner WHERE owner_id NOT IN (SELECT owner_id FROM NotificationPreference WHERE notification_type = 'checkup_reminder');

