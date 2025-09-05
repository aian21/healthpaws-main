// HealthPaws Authentication JavaScript
// Simple client-side validation + API integration

// Global variables
let emailVerified = false;

function serialize(form) {
  const data = {};
  new FormData(form).forEach((v, k) => { 
    data[k] = typeof v === 'string' ? v.trim() : v; 
  });
  return data;
}

function showToast(msg, type = 'info') {
  // Create a simple toast notification
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.textContent = msg;
  toast.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#17a2b8'};
    color: white;
    padding: 12px 20px;
    border-radius: 6px;
    z-index: 10000;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  `;
  
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.remove();
  }, 5000);
}

// API functions
async function apiCall(url, data = null, method = 'POST') {
  try {
    console.log('API Call:', method, url, data);
    
    const options = {
      method: method,
      headers: {
        'Content-Type': 'application/json',
      }
    };
    
    if (data && method !== 'GET') {
      options.body = JSON.stringify(data);
    }
    
    console.log('Fetch options:', options);
    
    const response = await fetch(url, options);
    console.log('Response status:', response.status);
    console.log('Response ok:', response.ok);
    
    const result = await response.json();
    console.log('Response data:', result);
    
    if (!response.ok) {
      throw new Error(result.error || 'API request failed');
    }
    
    return result;
  } catch (error) {
    console.error('API call error:', error);
    throw error;
  }
}

// Check subdomain availability
async function checkSubdomainAvailability(subdomain) {
  try {
    const result = await apiCall(`api/check-subdomain.php?subdomain=${encodeURIComponent(subdomain)}`, null, 'GET');
    return result.data.available;
  } catch (error) {
    console.error('Subdomain check failed:', error);
    return false;
  }
}

// Check email availability
async function checkEmailAvailability(email) {
  try {
    const result = await apiCall(`api/check-email.php?email=${encodeURIComponent(email)}`, null, 'GET');
    return result.data;
  } catch (error) {
    console.error('Email check failed:', error);
    return { exists: false, message: 'Email check failed' };
  }
}

// Handle form submissions
document.addEventListener('submit', async function(e) {
  const form = e.target;
  
  console.log('Form submitted:', form.id);
  
  if (form.id === 'login-form' || form.id === 'register-form') {
    e.preventDefault();
    
    if (!form.checkValidity()) {
      showToast('Please fill in required fields.', 'error');
      return;
    }
    
    // Check if we're on the last step and terms need to be read
    if (form.id === 'register-form') {
      const submitBtn = form.querySelector('[data-step-submit]');
      if (submitBtn && submitBtn.disabled) {
        showToast('Please read the terms and conditions completely before creating your account.', 'error');
        return;
      }
      
      // Check if email verification is completed
      if (!emailVerified) {
        showToast('Please complete email verification before creating your account.', 'error');
        return;
      }
    }
    
    const data = serialize(form);
    
    try {
      if (form.id === 'login-form') {
        // Handle login
        const result = await apiCall('api/login.php', {
          email: data.email,
          password: data.password
        });
        
        if (result.success) {
          showToast('Login successful! Redirecting to dashboard...', 'success');
          setTimeout(() => {
            window.location.href = result.data.redirect_url;
          }, 1500);
        }
      } else if (form.id === 'register-form') {
        // Handle registration
        console.log('Registration data:', data); // Debug log
        
        try {
          console.log('Making API call to register.php...');
          const result = await apiCall('api/register.php', {
            clinic_name: data.clinicName,
            business_email: data.businessEmail,
            subdomain: data.subdomain,
            owner_password: data.ownerPassword,
            plan: data.plan,
            address: data.address || '',
            clinic_phone: data.clinicPhone || '',
            owner_fname: data.ownerFname || 'Owner',
            owner_lname: data.ownerLname || 'Admin',
            owner_phone: data.ownerPhone || '',
            vet_fname: data.vetFname || '',
            vet_lname: data.vetLname || '',
            specialization: data.specialization || 'General Practice',
            license_number: data.licenseNumber || ''
          });
          
          console.log('Registration result:', result); // Debug log
          
          if (result.success && result.data && result.data.clinic_id) {
            // Only show success if we actually got a clinic ID back
            showToast('Account created successfully!', 'success');
            
            // Show account creation loading screen
            const creationOverlay = document.querySelector('.account-creation-overlay');
            if (creationOverlay) {
              creationOverlay.setAttribute('aria-hidden', 'false');
              startTypingAnimation();
            }
            
            // Store registration data for later use
            window.registrationData = result.data;
          } else {
            // Show error if the response doesn't contain expected data
            const errorMsg = result.error || 'Registration failed - no clinic ID returned';
            showToast(errorMsg, 'error');
            console.error('Registration failed:', result);
          }
        } catch (error) {
          console.error('Registration error:', error);
          let errorMessage = error.message || 'Registration failed';
          
          // Provide more specific error messages
          if (errorMessage.includes('already registered')) {
            errorMessage = 'This email is already registered. Please login to your existing account or use a different email.';
          } else if (errorMessage.includes('subdomain')) {
            errorMessage = 'This subdomain is already taken. Please choose a different one.';
          } else if (errorMessage.includes('verification')) {
            errorMessage = 'Please complete email verification before creating your account.';
          }
          
          showToast(errorMessage, 'error');
        }
      }
    } catch (error) {
      showToast(error.message, 'error');
    }
  }
});

// Register wizard logic
(function() {
  const form = document.getElementById('register-form');
  if (!form) return;
  
  const steps = Array.from(form.querySelectorAll('.step'));
  const cardsByStep = steps.map((s) => Array.from(s.querySelectorAll('.field-card')));
  const dots = Array.from(document.querySelectorAll('.stepper .dot'));
  
  console.log('Steps found:', steps.length);
  console.log('Cards by step:', cardsByStep.map((cards, i) => `Step ${i}: ${cards.length} cards`));
  const btnNext = form.querySelector('[data-step-next]');
  const btnBack = form.querySelector('[data-step-back]');
  const btnSubmit = form.querySelector('[data-step-submit]');
  let index = 0;
  let cardIndex = 0;
  const stepsContainer = form.querySelector('.steps');
  
  console.log('Form elements found:');
  console.log('btnNext:', btnNext);
  console.log('btnBack:', btnBack);
  console.log('btnSubmit:', btnSubmit);
  console.log('stepsContainer:', stepsContainer);

  function resizeToActive() {
    if (!stepsContainer) return;
    const active = steps[index];
    if (!active) return;
    const height = active.scrollHeight;
    stepsContainer.style.height = height + 'px';
  }

  function setActive(i) {
    steps.forEach((s, idx) => {
      s.classList.remove('is-active', 'is-left');
      if (idx === i) { 
        s.classList.add('is-active'); 
      } else if (idx < i) { 
        s.classList.add('is-left'); 
      }
      s.setAttribute('aria-hidden', idx === i ? 'false' : 'true');
    });
    
    // Inner cards
    cardsByStep.forEach((cards, stepIdx) => {
      cards.forEach((card, cIdx) => {
        const active = stepIdx === i && cIdx === cardIndex;
        card.classList.toggle('is-active', active);
      });
    });
    
    dots.forEach((d, idx) => d.classList.toggle('is-active', idx === i));
    
    const lastStep = i === steps.length - 1;
    const lastCardInStep = cardIndex === (cardsByStep[i]?.length || 1) - 1;
    btnBack.disabled = i === 0 && cardIndex === 0;
    btnNext.style.display = lastStep && lastCardInStep ? 'none' : '';
    btnSubmit.style.display = lastStep && lastCardInStep ? '' : 'none';
    
    // Hide submit button on terms step until terms are read
    if (lastStep && lastCardInStep && btnSubmit) {
      btnSubmit.style.display = 'none';
      btnSubmit.disabled = true;
    }
    
    const bar = document.querySelector('.progress .bar');
    if (bar) { 
      bar.style.width = `${(i) / (steps.length - 1) * 100}%`; 
    }
    
    // Focus first field in current step
    const scope = cardsByStep[i]?.[cardIndex] || steps[i];
    const first = scope.querySelector('input, select, textarea');
    if (first) { 
      first.focus(); 
    }
    
    // Check if we need to auto-send verification code
    if (i === 0 && cardIndex === 4) {
      setTimeout(checkAndSendCode, 100);
    }
    
    // Adjust container height
    requestAnimationFrame(resizeToActive);
  }

  function validateStep(i) {
    const scope = cardsByStep[i]?.[cardIndex] || steps[i];
    const fields = scope.querySelectorAll('input, select, textarea');
    
    console.log(`Validating step ${i}, card ${cardIndex}`);
    console.log('Scope:', scope);
    console.log('Fields found:', fields.length);
    
    for (const field of fields) {
      console.log('Checking field:', field.name, 'Value:', field.value, 'Valid:', field.checkValidity());
      if (!field.checkValidity()) {
        console.log('Field validation failed:', field.name);
        field.reportValidity();
        return false;
      }
    }
    
    // Special validation for email
    if (i === 0 && cardIndex === 1) {
      const emailInput = form.querySelector('input[name="businessEmail"]');
      if (emailInput) {
        const emailStatus = emailInput.parentNode.querySelector('.email-status');
        if (emailStatus && emailStatus.classList.contains('unavailable')) {
          showToast('Please use a different email address or login to your existing account.', 'error');
          return false;
        }
      }
    }
    
    // Special validation for subdomain
    if (i === 1 && cardIndex === 1) {
      const subdomainInput = form.querySelector('input[name="subdomain"]');
      if (subdomainInput) {
        const subdomain = subdomainInput.value;
        if (!subdomain || subdomain.trim() === '') {
          showToast('Please enter a subdomain.', 'error');
          return false;
        }
        if (!/^[a-z0-9-]{3,20}$/.test(subdomain)) {
          showToast('Subdomain must be 3-20 characters, lowercase letters, numbers, and hyphens only.', 'error');
          return false;
        }
        
        // Check if subdomain is available
        const subdomainStatus = subdomainInput.parentNode.querySelector('.subdomain-status');
        if (subdomainStatus && subdomainStatus.classList.contains('unavailable')) {
          showToast('This subdomain is already taken. Please choose a different one.', 'error');
          return false;
        }
      }
    }
    
    console.log('Step validation passed');
    return true;
  }

  btnNext && btnNext.addEventListener('click', () => {
    console.log('Next button clicked!');
    console.log('Current step:', index, 'Current card:', cardIndex);
    
    if (!validateStep(index)) {
      console.log('Validation failed, not proceeding');
      return;
    }
    
    console.log('Validation passed, proceeding to next card/step');
    
    const cards = cardsByStep[index] || [];
    console.log('Cards in current step:', cards.length);
    
    if (cardIndex < cards.length - 1) {
      cardIndex += 1;
      console.log('Moving to next card:', cardIndex);
    } else {
      index = Math.min(index + 1, steps.length - 1);
      cardIndex = 0;
      console.log('Moving to next step:', index);
    }
    setActive(index);
  });
  
  btnBack && btnBack.addEventListener('click', () => {
    const cards = cardsByStep[index] || [];
    if (cardIndex > 0) {
      cardIndex -= 1;
    } else {
      index = Math.max(index - 1, 0);
      const prevCards = cardsByStep[index] || [];
      cardIndex = Math.max(0, prevCards.length - 1);
    }
    setActive(index);
  });
  
  // Enter to next, Shift+Enter to back
  form.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const isTextarea = (e.target && e.target.tagName === 'TEXTAREA');
      if (isTextarea) return; // Allow multiline
      
      e.preventDefault();
      if (!validateStep(index)) return;
      
      const cards = cardsByStep[index] || [];
      if (cardIndex < cards.length - 1) { 
        cardIndex += 1; 
        setActive(index); 
        return; 
      }
      if (index < steps.length - 1) { 
        index += 1; 
        cardIndex = 0; 
        setActive(index); 
      }
    }
    if (e.key === 'Enter' && e.shiftKey) {
      e.preventDefault(); 
      index = Math.max(0, index - 1); 
      setActive(index);
    }
  });
  
  setActive(index);
  window.addEventListener('resize', resizeToActive);

  // Email validation and availability check
  const emailInput = form.querySelector('input[name="businessEmail"]');
  if (emailInput) {
    emailInput.addEventListener('input', () => {
      const email = emailInput.value.trim();
      
      // Clear any existing status
      const existingStatus = emailInput.parentNode.querySelector('.email-status');
      if (existingStatus) {
        existingStatus.remove();
      }
      
      // Check email availability after a delay
      clearTimeout(emailInput.availabilityTimer);
      emailInput.availabilityTimer = setTimeout(async () => {
        if (email && emailInput.checkValidity()) {
          try {
            const emailCheck = await checkEmailAvailability(email);
            const statusElement = createEmailStatusElement(emailInput.parentNode);
            
            if (emailCheck.exists) {
              statusElement.innerHTML = `
                <div style="color: #dc3545; font-size: 12px; margin-top: 4px;">
                  ❌ Email already registered
                  <br>
                  <a href="login.php" style="color: #007bff; text-decoration: underline;">Login to your account</a>
                </div>
              `;
              statusElement.className = 'email-status unavailable';
            } else {
              statusElement.textContent = '✅ Email is available';
              statusElement.className = 'email-status available';
              statusElement.style.cssText = 'color: #28a745; font-size: 12px; margin-top: 4px;';
            }
          } catch (error) {
            console.error('Email validation error:', error);
            // Don't show error to user, just log it
          }
        }
      }, 500);
    });
  }

  // Subdomain sanitization and availability check
  const subInput = form.querySelector('input[name="subdomain"]');
  if (subInput) {
    subInput.addEventListener('input', () => {
      const clean = subInput.value.toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-{2,}/g, '-').replace(/^-+|-+$/g, '');
      subInput.value = clean;
      
      // Clear any existing status
      const existingStatus = subInput.parentNode.querySelector('.subdomain-status');
      if (existingStatus) {
        existingStatus.remove();
      }
      
      // Check availability after a delay
      clearTimeout(subInput.availabilityTimer);
      subInput.availabilityTimer = setTimeout(async () => {
        if (clean.length >= 3) {
          try {
            const isAvailable = await checkSubdomainAvailability(clean);
            const statusElement = createSubdomainStatusElement(subInput.parentNode);
            
            statusElement.textContent = isAvailable ? '✅ Available' : '❌ Not available';
            statusElement.className = `subdomain-status ${isAvailable ? 'available' : 'unavailable'}`;
            statusElement.style.cssText = `font-size: 12px; margin-top: 4px; color: ${isAvailable ? '#28a745' : '#dc3545'};`;
          } catch (error) {
            console.error('Subdomain validation error:', error);
            // Don't show error to user, just log it
          }
        }
      }, 500);
    });
  }
  
  function createEmailStatusElement(parent) {
    const status = document.createElement('div');
    status.className = 'email-status';
    status.style.cssText = 'font-size: 12px; margin-top: 4px;';
    parent.appendChild(status);
    return status;
  }
  
  function createSubdomainStatusElement(parent) {
    const status = document.createElement('div');
    status.className = 'subdomain-status';
    status.style.cssText = 'font-size: 12px; margin-top: 4px;';
    parent.appendChild(status);
    return status;
  }

  // Plan card updater
  const planSelect = form.querySelector('select[name="plan"]');
  const planCard = form.querySelector('.plan-card');
  const planInfo = {
    starter: { price: '$49/mo', features: ['1 location', '3 users', 'Appointments & EMR', 'Email reminders'] },
    pro: { price: '$129/mo', features: ['3 locations', '10 users', 'Billing & payments', 'SMS reminders'] },
    enterprise: { price: 'Custom', features: ['Unlimited users', 'SLA & SSO', 'Dedicated success', 'Custom integrations'] }
  };
  
  function renderPlanCard() {
    if (!planSelect || !planCard) return;
    const p = planInfo[planSelect.value] || planInfo.pro;
    planCard.innerHTML = `
      <strong>Selected plan: ${planSelect.value.toUpperCase()} — ${p.price}</strong>
      <ul style="margin-top:6px; padding-left:18px">
        ${p.features.map(f => `<li>${f}</li>`).join('')}
      </ul>
    `;
  }
  
  planSelect && planSelect.addEventListener('change', () => { 
    renderPlanCard(); 
    resizeToActive(); 
  });
  renderPlanCard();

  // Mock payment
  const payBtn = form.querySelector('[data-mock-pay]');
  if (payBtn) {
    payBtn.addEventListener('click', () => {
      const overlay = document.querySelector('.loading-overlay');
      overlay && overlay.setAttribute('aria-hidden', 'false');
      
      setTimeout(() => {
        overlay && overlay.setAttribute('aria-hidden', 'true');
        const paid = form.querySelector('input[name="paid"]');
        if (paid) { 
          paid.value = 'true'; 
        }
        showToast('Mock payment successful.', 'success');
        
        // Advance to staff step
        index = 3; 
        setActive(index);
      }, 1200);
    });
  }

  // Email verification system
  const verificationBoxes = form.querySelectorAll('.verification-box');
  const sendCodeBtn = form.querySelector('#send-code-btn');
  const verificationMessage = document.querySelector('#verification-message');
  
  const mockVerificationCode = '123456';
  
  // Send verification code
  if (sendCodeBtn && emailInput) {
    sendCodeBtn.addEventListener('click', async () => {
      const email = emailInput.value.trim();
      if (!email || !emailInput.checkValidity()) {
        emailInput.reportValidity();
        return;
      }
      
      try {
        sendCodeBtn.disabled = true;
        sendCodeBtn.textContent = 'Sending...';
        
        // Get clinic name for email
        const clinicName = document.querySelector('input[name="clinicName"]').value || '';
        
        const result = await apiCall('api/send-verification.php', {
          email: email,
          clinic_name: clinicName
        });
        
        if (result.success) {
          verificationMessage.textContent = `Verification code sent to ${email}! Check your inbox.`;
          verificationMessage.style.color = '#28a745';
          verificationBoxes[0].focus();
          sendCodeBtn.textContent = 'Resend Code';
          sendCodeBtn.disabled = false;
        } else {
          throw new Error(result.error || 'Failed to send verification code');
        }
      } catch (error) {
        console.error('Send verification error:', error);
        verificationMessage.textContent = `Error: ${error.message}`;
        verificationMessage.style.color = '#dc3545';
        sendCodeBtn.textContent = 'Send Code';
        sendCodeBtn.disabled = false;
      }
    });
  }
  
  // Auto-send verification code when reaching verification card
  function checkAndSendCode() {
    if (index === 0 && cardIndex === 4) {
      const email = emailInput.value.trim();
      if (email && emailInput.checkValidity()) {
        verificationMessage.textContent = 'Click "Send Code" to receive your verification code.';
        verificationMessage.style.color = '#666';
        sendCodeBtn.textContent = 'Send Code';
      } else {
        verificationMessage.textContent = 'Please go back and enter a valid email address first.';
        verificationMessage.style.color = '#dc3545';
      }
    }
  }
  
  // Handle verification box input and auto-focus
  if (verificationBoxes.length > 0) {
    verificationBoxes.forEach((box, index) => {
      box.addEventListener('input', (e) => {
        const value = e.target.value;
        
        // Only allow numbers
        if (!/^[0-9]$/.test(value)) {
          e.target.value = '';
          return;
        }
        
        // Auto-focus to next box
        if (value && index < verificationBoxes.length - 1) {
          verificationBoxes[index + 1].focus();
        }
        
        // Check if all boxes are filled
        checkVerificationComplete();
      });
      
      // Handle backspace to go to previous box
      box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
          verificationBoxes[index - 1].focus();
        }
      });
    });
  }
  
  // Check if verification is complete
  async function checkVerificationComplete() {
    const enteredCode = Array.from(verificationBoxes).map(box => box.value).join('');
    if (enteredCode.length === 6) {
      try {
        const email = emailInput.value.trim();
        
        const result = await apiCall('api/verify-email.php', {
          email: email,
          code: enteredCode
        });
        
        if (result.success) {
          emailVerified = true;
          verificationBoxes.forEach(box => {
            box.classList.add('verified');
            box.classList.remove('error');
          });
          verificationMessage.textContent = '✅ Email verified successfully! You can now proceed to the next step.';
          verificationMessage.style.color = '#28a745';
        } else {
          emailVerified = false;
          verificationBoxes.forEach(box => {
            box.classList.add('error');
            box.classList.remove('verified');
          });
          verificationMessage.textContent = `❌ ${result.error || 'Invalid verification code'}`;
          verificationMessage.style.color = '#dc3545';
        }
      } catch (error) {
        console.error('Verification error:', error);
        emailVerified = false;
        verificationBoxes.forEach(box => {
          box.classList.add('error');
          box.classList.remove('verified');
        });
        verificationMessage.textContent = '❌ Verification failed. Please try again.';
        verificationMessage.style.color = '#dc3545';
      }
    }
  }
  
  // Enhanced step validation to check email verification
  const originalValidateStep = validateStep;
  validateStep = function(i) {
    if (!originalValidateStep(i)) return false;
    
    // Check if we're on the first step and need email verification
    if (i === 0 && cardIndex === 4) {
      if (!emailVerified) {
        verificationMessage.textContent = '❌ Please verify your email before proceeding to the next step.';
        verificationMessage.style.color = '#dc3545';
        return false;
      }
    }
    
    // Check if we're trying to submit the form and email is not verified
    if (i === 4 && cardIndex === 0) { // Last step, terms card
      if (!emailVerified) {
        showToast('Please complete email verification before creating your account.', 'error');
        return false;
      }
    }
    
    return true;
  };

  // Staff add
  const addStaffBtn = form.querySelector('[data-add-staff]');
  const staffList = document.getElementById('staff-list');
  
  function addStaffRow() {
    const row = document.createElement('div');
    row.className = 'staff-item';
    row.innerHTML = `
      <input type="email" name="staffEmail" placeholder="Staff email" required>
      <input type="password" name="staffPassword" placeholder="Temporary password" required>
    `;
    staffList && staffList.appendChild(row);
    resizeToActive();
  }
  
  addStaffBtn && addStaffBtn.addEventListener('click', addStaffRow);
  
  // Terms and conditions scrolling
  const termsContainer = document.querySelector('.terms-container');
  const termsStatus = document.querySelector('#terms-status');
  const submitBtn = form.querySelector('[data-step-submit]');
  
  // Initialize submit button state
  if (submitBtn) {
    submitBtn.style.display = 'none';
    submitBtn.disabled = true;
  }
  
  if (termsContainer && submitBtn) {
    termsContainer.addEventListener('scroll', () => {
      const scrollTop = termsContainer.scrollTop;
      const scrollHeight = termsContainer.scrollHeight;
      const clientHeight = termsContainer.clientHeight;
      const scrollPercentage = (scrollTop + clientHeight) / scrollHeight;
      
      if (scrollPercentage >= 0.95) { // 95% scrolled
        termsStatus.textContent = '✅ Terms read completely. You can now create your account.';
        termsStatus.style.color = '#28a745';
        submitBtn.style.display = 'inline-block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Account';
        
        // Remove any existing click handler to allow normal form submission
        submitBtn.onclick = null;
      } else {
        termsStatus.textContent = 'Please scroll to the bottom of the terms to continue';
        termsStatus.style.color = '#666';
        submitBtn.style.display = 'none';
      }
    });
  }
  
  
  
  // Go to dashboard function
  window.goToDashboard = function() {
    // Get clinic data from form
    const clinicName = document.querySelector('input[name="clinicName"]').value || 'Demo Veterinary Clinic';
    const subdomain = document.querySelector('input[name="subdomain"]').value || 'demo';
    
    // Redirect to dashboard with subdomain parameter
    window.location.href = `dashboard.php?subdomain=${encodeURIComponent(subdomain)}&clinic=${encodeURIComponent(clinicName)}`;
  };
})();

// Typing animation function (global scope)
window.startTypingAnimation = function() {
  const typingText = document.querySelector('#typing-text');
  const progressFill = document.querySelector('.progress-fill');
  const progressText = document.querySelector('.progress-text');
  
  // Get clinic data from form
  const clinicName = document.querySelector('input[name="clinicName"]').value || 'Vet Shop';
  const subdomain = document.querySelector('input[name="subdomain"]').value || 'vetname';
  
  const phrases = [
    `Preparing your ${clinicName}! 🏥`,
    `Creating ${subdomain}.healthpaws.co 🌐`,
    "Setting up patient database 📊",
    "Configuring appointment system 📅",
    "Initializing medical records 📋",
    "Setting up billing system 💳",
    "Creating staff accounts 👥",
    "Finalizing your clinic setup ✨"
  ];
  
  let currentPhraseIndex = 0;
  let currentCharIndex = 0;
  let isDeleting = false;
  
  function typeNextChar() {
    const currentPhrase = phrases[currentPhraseIndex];
    
    if (isDeleting) {
      // Deleting effect
      typingText.textContent = currentPhrase.substring(0, currentCharIndex - 1);
      currentCharIndex--;
    } else {
      // Typing effect
      typingText.textContent = currentPhrase.substring(0, currentCharIndex + 1);
      currentCharIndex++;
    }
    
    // Update progress bar
    const phraseProgress = (currentPhraseIndex / phrases.length) * 100;
    const charProgress = (currentCharIndex / currentPhrase.length) * (100 / phrases.length);
    const totalProgress = Math.min(phraseProgress + charProgress, 100);
    progressFill.style.width = totalProgress + '%';
    
    if (!isDeleting && currentCharIndex === currentPhrase.length) {
      // Finished typing current phrase, wait then start deleting
      setTimeout(() => {
        isDeleting = true;
        typeNextChar();
      }, 1500);
      return;
    }
    
    if (isDeleting && currentCharIndex === 0) {
      // Finished deleting, move to next phrase
      isDeleting = false;
      currentPhraseIndex++;
      
      if (currentPhraseIndex >= phrases.length) {
        // All phrases completed
        progressFill.style.width = '100%';
        progressText.textContent = 'Setup complete!';
        
        // Show completion message after a short delay
        setTimeout(() => {
          showCompletionScreen();
        }, 1000);
        return;
      }
      
      // Wait before starting next phrase
      setTimeout(typeNextChar, 500);
      return;
    }
    
    // Continue typing/deleting
    const speed = isDeleting ? 50 : 100;
    setTimeout(typeNextChar, speed);
  }
  
  // Start the typing animation
  typeNextChar();
};

// Show completion screen function (global scope)
window.showCompletionScreen = function() {
  const creationLoader = document.querySelector('.creation-loader');
  const typingContainer = document.querySelector('.typing-container');
  const creationProgress = document.querySelector('.creation-progress');
  
  // Hide typing and progress
  typingContainer.style.display = 'none';
  creationProgress.style.display = 'none';
  
  // Show completion message
  creationLoader.innerHTML = `
    <div class="completion-message">
      <div class="completion-icon">🎉</div>
      <h2 class="completion-title">Your clinic is now ready!</h2>
      <p class="completion-subtitle">Welcome to HealthPaws! Your veterinary practice has been successfully set up.</p>
      <button class="btn btn-primary completion-btn" onclick="goToDashboard()">Go to Dashboard</button>
    </div>
  `;
};

