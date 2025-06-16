document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('inputEmail');
    const passwordInput = document.getElementById('inputPassword');
    const rememberCheckbox = document.getElementById('inputRememberPassword');
    const submitButton = loginForm.querySelector('button[type="submit"]');

    const spinner = document.createElement('span');
    spinner.className = 'spinner-border spinner-border-sm me-2';
    spinner.setAttribute('role', 'status');
    spinner.setAttribute('aria-hidden', 'true');

    // Mostrar valores recordados
    const rememberedEmail = localStorage.getItem('rememberedEmail');
    const rememberedPassword = localStorage.getItem('rememberedPassword');

    if (rememberedEmail && rememberedPassword) {
        emailInput.value = rememberedEmail;
        passwordInput.value = rememberedPassword;
        rememberCheckbox.checked = true;
    }

    // Validación
    function validateEmail() {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            setInvalid(emailInput, 'El correo electrónico es requerido');
            return false;
        } else if (!emailRegex.test(email)) {
            setInvalid(emailInput, 'Ingrese un correo electrónico válido');
            return false;
        } else {
            setValid(emailInput);
            return true;
        }
    }

    function validatePassword() {
        const password = passwordInput.value.trim();
        if (!password) {
            setInvalid(passwordInput, 'La contraseña es requerida');
            return false;
        } else if (password.length < 5) {
            setInvalid(passwordInput, 'La contraseña debe tener al menos 6 caracteres');
            return false;
        } else {
            setValid(passwordInput);
            return true;
        }
    }

    function setInvalid(input, message) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        input.nextElementSibling?.remove();
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        input.parentElement.appendChild(feedback);
    }

    function setValid(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        input.nextElementSibling?.remove();
    }

    // Evento submit
    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const emailValid = validateEmail();
        const passwordValid = validatePassword();

        if (emailValid && passwordValid) {
            submitButton.disabled = true;
            submitButton.innerHTML = '';
            submitButton.appendChild(spinner);
            submitButton.innerHTML += ' Ingresando...';

            if (rememberCheckbox.checked) {
                localStorage.setItem('rememberedEmail', emailInput.value);
                localStorage.setItem('rememberedPassword', passwordInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
                localStorage.removeItem('rememberedPassword');
            }

            setTimeout(() => {
                loginForm.submit();
            }, 1500);
        }
    });

    emailInput.addEventListener('input', validateEmail);
    passwordInput.addEventListener('input', validatePassword);

    // Mostrar/ocultar contraseña
    const toggle = document.createElement('span');
    toggle.className = 'password-toggle';
    toggle.innerHTML = '<i class="fas fa-eye"></i>';
    passwordInput.parentElement.appendChild(toggle);

    toggle.addEventListener('click', () => {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            passwordInput.type = 'password';
            toggle.innerHTML = '<i class="fas fa-eye"></i>';
        }
    });
});
