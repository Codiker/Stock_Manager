document.addEventListener('DOMContentLoaded', function() {
  // Password toggle functionality
  const passwordInputs = document.querySelectorAll('input[type="password"]');
  
  passwordInputs.forEach(input => {
    const toggle = document.createElement('span');
    toggle.className = 'password-toggle';
    toggle.innerHTML = '<i class="fas fa-eye"></i>';
    toggle.style.position = 'absolute';
    toggle.style.right = '15px';
    toggle.style.top = '50%';
    toggle.style.transform = 'translateY(-50%)';
    toggle.style.cursor = 'pointer';
    toggle.style.color = '#6c757d';
    
    input.parentElement.style.position = 'relative';
    input.parentElement.appendChild(toggle);
    
    toggle.addEventListener('click', () => {
      if (input.type === 'password') {
        input.type = 'text';
        toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
      } else {
        input.type = 'password';
        toggle.innerHTML = '<i class="fas fa-eye"></i>';
      }
    });
  });

  // Form validation
  const form = document.querySelector('form');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirm_password');
  
  form.addEventListener('submit', function(e) {
    let isValid = true;
    
    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => {
      el.classList.remove('is-invalid');
    });
    
    document.querySelectorAll('.invalid-feedback').forEach(el => {
      el.remove();
    });
    
    // Password match validation
    if (password.value !== confirmPassword.value) {
      showError(confirmPassword, 'Las contraseñas no coinciden');
      isValid = false;
    }
    
    // Password strength validation
    if (password.value.length < 8) {
      showError(password, 'La contraseña debe tener al menos 8 caracteres');
      isValid = false;
    }
    
    if (!isValid) {
      e.preventDefault();
    } else {
      // Show loading state
      const submitBtn = document.querySelector('button[type="submit"]');
      submitBtn.classList.add('btn-loading');
      submitBtn.disabled = true;
      submitBtn.innerHTML = 'Registrando...';
    }
  });
  
  function showError(input, message) {
    input.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    errorDiv.style.color = 'var(--error-color)';
    errorDiv.style.fontSize = '0.85rem';
    errorDiv.style.marginTop = '0.25rem';
    
    input.parentNode.appendChild(errorDiv);
  }
  
  // Password strength indicator
  if (password) {
    const strengthBar = document.createElement('div');
    strengthBar.className = 'password-strength';
    strengthBar.innerHTML = '<div class="password-strength-bar"></div>';
    password.parentNode.appendChild(strengthBar);
    
    password.addEventListener('input', function() {
      const strength = calculatePasswordStrength(this.value);
      const bar = strengthBar.querySelector('.password-strength-bar');
      
      bar.style.width = `${strength.percentage}%`;
      bar.style.backgroundColor = strength.color;
    });
  }
  
  function calculatePasswordStrength(password) {
    let strength = 0;
    
    // Length check
    if (password.length > 7) strength += 1;
    if (password.length > 11) strength += 1;
    
    // Character variety
    if (/[A-Z]/.test(password)) strength += 1;
    if (/[0-9]/.test(password)) strength += 1;
    if (/[^A-Za-z0-9]/.test(password)) strength += 1;
    
    const percentage = Math.min(strength * 20, 100);
    
    let color;
    if (percentage < 40) color = '#ff4444';
    else if (percentage < 70) color = '#ffbb33';
    else color = '#00C851';
    
    return { percentage, color };
  }
});