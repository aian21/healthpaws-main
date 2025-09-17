<?php
// HealthPaws - Pet Owner Registration
$page_title = "Register as Pet Owner — HealthPaws";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Inter", system-ui, sans-serif;
            background: linear-gradient(180deg, #f0f9ff, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        .main-wrapper {
            display: flex;
            align-items: stretch;
            justify-content: center;
            transition: all 0.4s ease;
            width: 100%;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
            min-height: 500px;
            display: flex;
            flex-direction: column;
        }
        .container.with-pet-card {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 1024px) {
            .container.with-pet-card {
                transform: translateX(0);
            }
        }
        h1 {
            font-family: "Poppins", sans-serif;
            font-size: 28px;
            color: #0f172a;
            margin-bottom: 8px;
            text-align: center;
        }
        .subtitle {
            color: #64748b;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-section {
            /* Left side form */
        }
        .progress {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        .progress-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: all 0.3s;
        }
        .progress-dot.active {
            background: #0ea5e9;
        }
        
        /* Pet Card Preview */
        .pet-card-preview {
            width: 350px;
            transition: all 0.4s ease;
            opacity: 0;
            pointer-events: none;
            transform: translateX(100px);
            display: none;
        }
        .pet-card-preview.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(0);
            display: block;
        }
        @media (max-width: 1024px) {
            .pet-card-preview {
                position: relative;
                right: auto;
                top: auto;
                transform: none;
                width: 100%;
                margin-top: 20px;
            }
            .pet-card-preview.show {
                right: auto;
            }
        }
        .pet-card {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            border-radius: 20px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(14, 165, 233, 0.3);
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .pet-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/></svg>');
            animation: rotate 20s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .card-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        .pet-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            transition: all 0.3s;
        }
        .pet-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .pet-species {
            font-size: 16px;
            opacity: 0.9;
        }
        .card-details {
            position: relative;
            z-index: 1;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .detail-label {
            opacity: 0.8;
            font-size: 14px;
        }
        .detail-value {
            font-weight: 600;
            font-size: 14px;
        }
        .qr-placeholder {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            margin: 16px auto 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-align: center;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .step-title {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .step-description {
            color: #64748b;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 640px) {
            .form-row { grid-template-columns: 1fr; }
        }
        label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #0ea5e9;
        }
        .verification-boxes {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 20px 0;
        }
        .verification-box {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
        }
        .demo-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px;
            margin: 16px 0;
            font-size: 14px;
            color: #1e40af;
        }
        .checkbox-group {
            margin: 16px 0;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 12px 0;
        }
        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        .terms-box {
            height: 200px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            overflow-y: auto;
            background: #f9fafb;
            margin: 16px 0;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #64748b;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        .btn-primary {
            background: #0ea5e9;
            color: white;
        }
        .btn-primary:hover {
            background: #0284c7;
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="container">
                    <h1>Create Your Pet Owner Account</h1>
        
        <div class="form-section">
            <div class="progress">
                <div class="progress-dot active"></div>
                <div class="progress-dot"></div>
                <div class="progress-dot"></div>
                <div class="progress-dot"></div>
                <div class="progress-dot"></div>
                </div>
                
        <form id="petOwnerForm">
            <!-- Step 1: Name -->
            <div class="step active" id="step1">
                <div class="step-title">👋 What's your name?</div>
                <div class="step-description">Let's start with your basic information.</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>First name</label>
                                            <input type="text" name="firstName" placeholder="John" required>
                    </div>
                    <div class="form-group">
                        <label>Last name</label>
                                            <input type="text" name="lastName" placeholder="Doe" required>
                                    </div>
                                </div>
                                </div>
                                
            <!-- Step 2: Email -->
            <div class="step" id="step2">
                <div class="step-title">📧 What's your email?</div>
                <div class="step-description">We'll use this to send you important updates about your pet's health.</div>
                
                <div class="form-group">
                    <label>Email address</label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                                        </div>
                                    </div>
                                    
            <!-- Step 3: Phone with Verification -->
            <div class="step" id="step3">
                <div class="step-title">📱 Phone verification</div>
                <div class="step-description">Enter your phone number and verify it with a code.</div>
                
                <div class="form-group">
                    <label>Phone number</label>
                    <input type="tel" name="phone" placeholder="(555) 123-4567" required>
                                    </div>
                                    
                <div class="section" style="margin-top: 24px;">
                    <div class="section-title">📧 Enter verification code</div>
                    <div class="verification-boxes">
                        <input type="text" class="verification-box" maxlength="1" data-index="0" placeholder="1">
                        <input type="text" class="verification-box" maxlength="1" data-index="1" placeholder="2">
                        <input type="text" class="verification-box" maxlength="1" data-index="2" placeholder="3">
                        <input type="text" class="verification-box" maxlength="1" data-index="3" placeholder="4">
                        <input type="text" class="verification-box" maxlength="1" data-index="4" placeholder="5">
                        <input type="text" class="verification-box" maxlength="1" data-index="5" placeholder="6">
                                        </div>
                    <div class="demo-info">
                        <strong>🧪 Demo Mode:</strong> Use verification code: <strong>123456</strong>
                                    </div>
                                </div>
                            </div>

            <!-- Step 4: Pet Basic Info -->
            <div class="step" id="step4">
                <div class="step-title">🐾 Tell us about your pet</div>
                <div class="step-description">Let's create your pet's digital profile.</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Pet's name</label>
                        <input type="text" name="petName" placeholder="Bella" required id="petNameInput">
                    </div>
                    <div class="form-group">
                        <label>Species</label>
                        <select name="species" required id="speciesInput">
                                                <option value="">Select species</option>
                            <option value="Dog">🐕 Dog</option>
                            <option value="Cat">🐱 Cat</option>
                            <option value="Bird">🐦 Bird</option>
                            <option value="Rabbit">🐰 Rabbit</option>
                            <option value="Fish">🐠 Fish</option>
                            <option value="Reptile">🦎 Reptile</option>
                            <option value="Other">🐾 Other</option>
                                            </select>
                                    </div>
                                    </div>
                                </div>
                                
            <!-- Step 5: Pet Details -->
            <div class="step" id="step5">
                <div class="step-title">📝 Pet details</div>
                <div class="step-description">Add some more details to complete your pet's profile.</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Breed</label>
                        <input type="text" name="breed" placeholder="Golden Retriever" id="breedInput">
                                </div>
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" placeholder="3" min="0" max="30" id="ageInput">
                                        </div>
                                    </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Weight (kg)</label>
                        <input type="number" name="weight" placeholder="25.5" step="0.1" min="0" id="weightInput">
                                </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" placeholder="Golden" id="colorInput">
                                </div>
                                        </div>
                                    </div>
                                    
            <div class="buttons">
                <button type="button" id="backBtn" class="btn btn-secondary" style="display:none;">Back</button>
                <button type="button" id="nextBtn" class="btn btn-primary">Next</button>
                <button type="submit" id="submitBtn" class="btn btn-primary" style="display:none;">Create Pet Owner Account</button>
                    </div>
                </form>
                
        <p style="text-align: center; margin-top: 20px; color: #64748b; font-size: 14px;">
            Already have an account? <a href="login.php" style="color: #0ea5e9;">Sign in here</a> | 
            Want to register a clinic instead? <a href="register.php" style="color: #0ea5e9;">Register as Clinic</a>
        </p>
            </div>
        </div>
        
        <!-- Pet Card Preview -->
        <div class="pet-card-preview">
        <div class="pet-card">
            <div class="card-header">
                <div class="pet-avatar" id="petAvatar">🐾</div>
                <div class="pet-name" id="cardPetName">Your Pet</div>
                <div class="pet-species" id="cardPetSpecies">Species</div>
            </div>
            <div class="card-details">
                <div class="detail-row">
                    <span class="detail-label">Owner</span>
                    <span class="detail-value" id="cardOwnerName">Your Name</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Breed</span>
                    <span class="detail-value" id="cardBreed">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Age</span>
                    <span class="detail-value" id="cardAge">-</span>
                    </div>
                <div class="detail-row">
                    <span class="detail-label">Weight</span>
                    <span class="detail-value" id="cardWeight">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Color</span>
                    <span class="detail-value" id="cardColor">-</span>
                </div>
                    </div>
            <div class="qr-placeholder">
                QR Code
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let currentStep = 1;
        const totalSteps = 5;
        
        // Species emoji mapping
        const speciesEmojis = {
            'Dog': '🐕',
            'Cat': '🐱',
            'Bird': '🐦',
            'Rabbit': '🐰',
            'Fish': '🐠',
            'Reptile': '🦎',
            'Other': '🐾'
        };
        
        function updateProgress() {
            // Update progress dots
            document.querySelectorAll('.progress-dot').forEach((dot, index) => {
                dot.classList.toggle('active', index < currentStep);
            });
            
            // Update step visibility
            document.querySelectorAll('.step').forEach((step, index) => {
                step.classList.toggle('active', index === currentStep - 1);
            });
            
            // Update button visibility
            document.getElementById('backBtn').style.display = currentStep === 1 ? 'none' : 'block';
            document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'block';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';
            
            // Show/hide pet card preview (only show during pet steps 4 & 5)
            const petCardPreview = document.querySelector('.pet-card-preview');
            const container = document.querySelector('.container');
            const mainWrapper = document.querySelector('.main-wrapper');
            
            if (currentStep >= 4) {
                petCardPreview.classList.add('show');
                container.classList.add('with-pet-card');
                mainWrapper.classList.add('with-pet-card');
            } else {
                petCardPreview.classList.remove('show');
                container.classList.remove('with-pet-card');
                mainWrapper.classList.remove('with-pet-card');
            }
        }
        
        function validateStep() {
            const currentStepElement = document.getElementById(`step${currentStep}`);
            const requiredFields = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ef4444';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e5e7eb';
                }
            });
            
            return isValid;
        }
        
        function updatePetCard() {
            // Update owner name
            const firstName = document.querySelector('input[name="firstName"]').value;
            const lastName = document.querySelector('input[name="lastName"]').value;
            const ownerName = firstName && lastName ? `${firstName} ${lastName}` : firstName || lastName || 'Your Name';
            document.getElementById('cardOwnerName').textContent = ownerName;
            
            // Update pet info
            const petName = document.getElementById('petNameInput')?.value || 'Your Pet';
            const species = document.getElementById('speciesInput')?.value || '';
            const breed = document.getElementById('breedInput')?.value || '-';
            const age = document.getElementById('ageInput')?.value || '-';
            const weight = document.getElementById('weightInput')?.value || '-';
            const color = document.getElementById('colorInput')?.value || '-';
            
            document.getElementById('cardPetName').textContent = petName;
            document.getElementById('cardPetSpecies').textContent = species || 'Species';
            document.getElementById('cardBreed').textContent = breed;
            document.getElementById('cardAge').textContent = age !== '-' ? `${age} years` : '-';
            document.getElementById('cardWeight').textContent = weight !== '-' ? `${weight} kg` : '-';
            document.getElementById('cardColor').textContent = color;
            
            // Update avatar emoji
            const avatar = document.getElementById('petAvatar');
            if (species && speciesEmojis[species]) {
                avatar.textContent = speciesEmojis[species];
                avatar.style.transform = 'scale(1.1)';
            } else {
                avatar.textContent = '🐾';
                avatar.style.transform = 'scale(1)';
            }
        }
        
        // Add event listeners for live updates
        function addLivePreviewListeners() {
            const inputs = ['firstName', 'lastName', 'petNameInput', 'speciesInput', 'breedInput', 'ageInput', 'weightInput', 'colorInput'];
            inputs.forEach(inputName => {
                const element = document.querySelector(`input[name="${inputName}"], select[name="${inputName}"], #${inputName}`);
                if (element) {
                    element.addEventListener('input', updatePetCard);
                    element.addEventListener('change', updatePetCard);
                }
            });
        }
        
        document.getElementById('nextBtn').addEventListener('click', () => {
            if (validateStep() && currentStep < totalSteps) {
                currentStep++;
                updateProgress();
                
                // Add live preview listeners when we reach pet steps
                if (currentStep >= 4) {
                    setTimeout(addLivePreviewListeners, 100);
                }
            }
        });
        
        document.getElementById('backBtn').addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateProgress();
            }
        });
        
        document.getElementById('petOwnerForm').addEventListener('submit', (e) => {
            e.preventDefault();
            if (validateStep()) {
                alert('Registration successful! Your pet card is ready! (This is a demo)');
            }
        });
        
        // Verification code handling
        document.querySelectorAll('.verification-box').forEach((box, index) => {
            box.addEventListener('input', (e) => {
                if (e.target.value && index < 5) {
                    document.querySelectorAll('.verification-box')[index + 1].focus();
                }
            });
        });
        
        // Initialize
        updateProgress();
        addLivePreviewListeners();
        
        // Update card initially
        updatePetCard();
    </script>
</body>
</html>
