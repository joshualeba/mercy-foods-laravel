// --- LÓGICA PARA OCULTAR EL LOADER ---
window.addEventListener('load', function() {
    const loaderWrapper = document.getElementById('loader-wrapper');
    if (loaderWrapper) {
        loaderWrapper.style.opacity = '0';
        setTimeout(() => {
            loaderWrapper.style.display = 'none';
        }, 500);
    }
});

document.addEventListener('DOMContentLoaded', function() {

    // --- DECLARACIÓN DE TODAS LAS CONSTANTES ---
    const modal = document.getElementById('notification-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const closeModalBtn = document.getElementById('modal-close-btn');
    const passwordToggle = document.querySelector('.password-toggle-icon');
    const form = document.getElementById('login-form');
    const emailInput = document.getElementById('login-email');
    const passInput = document.getElementById('login-pass');
    const submitBtn = form.querySelector('.submit-btn');

    // --- LÓGICA DEL MODAL DE NOTIFICACIONES ---
    const showNotification = (title, message) => {
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modal.classList.add('active');
    };

    const closeModal = () => {
        modal.classList.remove('active');
    };

    closeModalBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // --- LÓGICA PARA MOSTRAR/OCULTAR CONTRASEÑA ---
    if (passwordToggle) {
        passwordToggle.addEventListener('click', () => {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            passwordToggle.classList.toggle('fa-eye-slash');
        });
    }

    // --- LÓGICA DE VALIDACIÓN DEL FORMULARIO ---
    const validationStatus = { email: false, password: false };

    const showError = (input, message) => {
        const group = input.closest('.input-group');
        group.classList.remove('success');
        group.classList.add('error');
        group.querySelector('.error-message').textContent = message;
    };

    const showSuccess = (input) => {
        const group = input.closest('.input-group');
        group.classList.remove('error');
        group.classList.add('success');
    };

    const checkFormValidity = () => {
        const allValid = Object.values(validationStatus).every(status => status === true);
        submitBtn.disabled = !allValid;
    };

    emailInput.addEventListener('input', () => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailRegex.test(emailInput.value)) {
            showSuccess(emailInput);
            validationStatus.email = true;
        } else {
            showError(emailInput, 'Introduce un correo electrónico válido.');
            validationStatus.email = false;
        }
        checkFormValidity();
    });

    passInput.addEventListener('input', () => {
        if (passInput.value.length > 0) {
            showSuccess(passInput);
            validationStatus.password = true;
        } else {
            showError(passInput, 'La contraseña es requerida.');
            validationStatus.password = false;
        }
        checkFormValidity();
    });

    // --- ENVÍO DEL FORMULARIO ---
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        if (!submitBtn.disabled) {
            const email = emailInput.value;
            const password = passInput.value;

            fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                }),
            })
            .then(response => response.json())
            .then(data => {
                // Si la respuesta contiene el objeto 'user', el login fue exitoso
                if (data.user) {
                    // Llamamos a la función showNotification que ya existe
                    showNotification('¡Éxito!', data.message);

                    localStorage.setItem('mercifood_user', JSON.stringify(data.user));

                    setTimeout(() => {
                        switch (data.user.role) {
                            case 'cliente':
                                window.location.href = '/cliente-dashboard';
                                break;
                            case 'restaurante':
                                window.location.href = '/restaurante-dashboard';
                                break;
                            case 'repartidor':
                                window.location.href = '/repartidor-dashboard';
                                break;
                            default:
                                window.location.href = '/';
                                break;
                        }
                    }, 1500);
                } else {
                    // Si no, mostramos el mensaje de error
                    showNotification('Error de inicio de sesión', data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                showNotification('Error de conexión', 'Ocurrió un error al intentar iniciar sesión.');
            });
        }
    });
});