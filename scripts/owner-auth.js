// Pet Owner Registration JavaScript
// Simple step-based navigation for pet owner registration

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('owner-register-form');
    if (!form) return;

    // Pet owner specific step management
    const steps = document.querySelectorAll('.step');
    const dots = document.querySelectorAll('.stepper .dot');
    const btnNext = form.querySelector('[data-step-next]');
    const btnBack = form.querySelector('[data-step-back]');
    const btnSubmit = form.querySelector('[data-step-submit]');
    
    let currentStep = 0;
    let emailVerified = false;

    console.log('Pet Owner Registration JS loaded');
    console.log('Steps found:', steps.length);
    console.log('Buttons:', { btnNext, btnBack, btnSubmit });

    // Update progress dots and step visibility
    function updateProgress() {
        console.log('Updating progress for step:', currentStep);
        
        // Update dots
        dots.forEach((dot, index) => {
            dot.classList.toggle('is-active', index <= currentStep);
        });
        
        // Update step visibility
        steps.forEach((step, index) => {
            step.classList.remove('is-active', 'is-left');
            if (index === currentStep) {
                step.classList.add('is-active');
            } else if (index < currentStep) {
                step.classList.add('is-left');
            }
        });
        
        // Update button visibility
        if (btnBack) {
            btnBack.disabled = currentStep === 0;
        }
        
        if (btnNext && btnSubmit) {
            if (currentStep === steps.length - 1) {
                btnNext.style.display = 'none';
                btnSubmit.style.display = 'block';
            } else {
                btnNext.style.display = 'block';
                btnSubmit.style.display = 'none';
            }
        }
    }

    // Email verification for pet owners
    const sendCodeBtn = document.getElementById('send-owner-code-btn');
    const verificationMessage = document.getElementById('owner-verification-message');
    const verificationBoxes = document.querySelectorAll('.verification-box');
    
    if (sendCodeBtn) {
        sendCodeBtn.addEventListener('click', function() {
            const email = form.querySelector('input[name="email"]').value;
            if (!email) {
                verificationMessage.textContent = 'Please enter your email address first.';
                verificationMessage.style.color = '#dc3545';
                return;
            }

            // Mock email verification (in real app, this would call an API)
            verificationMessage.textContent = 'Verification code sent! Check your email.';
            verificationMessage.style.color = '#28a745';
            sendCodeBtn.textContent = 'Code Sent';
            sendCodeBtn.disabled = true;
            
            // Re-enable after 30 seconds
            setTimeout(() => {
                sendCodeBtn.textContent = 'Resend Code';
                sendCodeBtn.disabled = false;
            }, 30000);
        });
    }

    // Handle verification code input
    verificationBoxes.forEach((box, index) => {
        box.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }
            
            // Move to next box
            if (value && index < verificationBoxes.length - 1) {
                verificationBoxes[index + 1].focus();
            }
            
            // Check if all boxes are filled
            const allFilled = Array.from(verificationBoxes).every(b => b.value);
            if (allFilled) {
                const code = Array.from(verificationBoxes).map(b => b.value).join('');
                validateVerificationCode(code);
            }
        });
        
        // Handle backspace
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                verificationBoxes[index - 1].focus();
            }
        });
    });

    function validateVerificationCode(code) {
        // Mock verification (in real app, this would validate with server)
        if (code === '123456') {
            emailVerified = true;
            verificationBoxes.forEach(box => {
                box.classList.add('verified');
                box.disabled = true;
            });
            verificationMessage.textContent = 'Email verified successfully!';
            verificationMessage.style.color = '#28a745';
        } else {
            verificationBoxes.forEach(box => {
                box.classList.add('error');
                setTimeout(() => box.classList.remove('error'), 2000);
            });
            verificationMessage.textContent = 'Invalid code. Please try again.';
            verificationMessage.style.color = '#dc3545';
        }
    }

    // Form validation
    function validateStep(stepIndex) {
        const step = steps[stepIndex];
        const requiredFields = step.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = '#dc3545';
                isValid = false;
            } else {
                field.style.borderColor = '';
            }
        });

        // Step 0: don't block navigation if email isn't verified; just hint user
        if (stepIndex === 0 && !emailVerified) {
            if (verificationMessage) {
                verificationMessage.textContent = 'Tip: verify your email now or continue and verify later (code: 123456).';
                verificationMessage.style.color = '#64748b';
            }
        }

        // Password confirmation validation
        if (stepIndex === 2) {
            const password = form.querySelector('input[name="password"]').value;
            const confirmPassword = form.querySelector('input[name="confirmPassword"]').value;
            
            if (password !== confirmPassword) {
                form.querySelector('input[name="confirmPassword"]').style.borderColor = '#dc3545';
                isValid = false;
            }

            // Terms agreement
            const agreeTerms = form.querySelector('input[name="agreeTerms"]');
            if (!agreeTerms.checked) {
                agreeTerms.style.outline = '2px solid #dc3545';
                isValid = false;
            } else {
                agreeTerms.style.outline = '';
            }
        }

        return isValid;
    }

    // Step navigation event listeners
    if (btnNext) {
        btnNext.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Next button clicked, current step:', currentStep);
            
            if (validateStep(currentStep)) {
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    updateProgress();
                    console.log('Moved to step:', currentStep);
                }
            }
        });
    }
    
    if (btnBack) {
        btnBack.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Back button clicked, current step:', currentStep);
            
            if (currentStep > 0) {
                currentStep--;
                updateProgress();
                console.log('Moved to step:', currentStep);
            }
        });
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateStep(currentStep)) {
            return;
        }

        // Show loading overlay
        document.querySelector('.loading-overlay').setAttribute('aria-hidden', 'false');
        
        // Collect form data
        const formData = new FormData(form);
        const data = {};
        
        // Convert FormData to regular object
        for (let [key, value] of formData.entries()) {
            if (key === 'notifications[]') {
                if (!data.notifications) data.notifications = [];
                data.notifications.push(value);
            } else {
                data[key] = value;
            }
        }

        console.log('Submitting pet owner registration data:', data);

        // Submit to API
        fetch('api/register-owner.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(result => {
            console.log('Registration result:', result);
            document.querySelector('.loading-overlay').setAttribute('aria-hidden', 'true');
            
            if (result.success) {
                // Show success animation
                showSuccessAnimation();
                
                // Redirect after animation
                setTimeout(() => {
                    window.location.href = result.data?.redirect_url || 'login.php';
                }, 3000);
            } else {
                console.error('Registration failed:', result.error);
                alert('Registration failed: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => {
            document.querySelector('.loading-overlay').setAttribute('aria-hidden', 'true');
            console.error('Registration error:', error);
            alert('Registration failed. Please try again.');
        });
    });

    function showSuccessAnimation() {
        const overlay = document.querySelector('.account-creation-overlay');
        const typingText = document.getElementById('typing-text');
        const progressFill = document.querySelector('.progress-fill');
        
        overlay.setAttribute('aria-hidden', 'false');
        
        // Typing animation messages
        const messages = [
            "Creating your pet owner account...",
            "Setting up your pet's profile...",
            "Generating digital pet card...",
            "Configuring notification preferences...",
            "Welcome to HealthPaws! 🎉"
        ];
        
        let messageIndex = 0;
        let charIndex = 0;
        
        function typeMessage() {
            if (messageIndex < messages.length) {
                if (charIndex < messages[messageIndex].length) {
                    typingText.textContent += messages[messageIndex].charAt(charIndex);
                    charIndex++;
                    setTimeout(typeMessage, 50);
                } else {
                    setTimeout(() => {
                        messageIndex++;
                        charIndex = 0;
                        typingText.textContent = '';
                        progressFill.style.width = ((messageIndex + 1) / messages.length * 100) + '%';
                        typeMessage();
                    }, 1000);
                }
            }
        }
        
        typeMessage();
    }

    // Initialize the form
    console.log('Initializing pet owner registration form...');
    updateProgress();
    
    // Auto-focus first input
    const firstInput = form.querySelector('input[type="text"], input[type="email"]');
    if (firstInput) {
        firstInput.focus();
    }
    
    console.log('Pet owner registration form initialized successfully');
});
