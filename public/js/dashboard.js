document.addEventListener('DOMContentLoaded', function() {

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

    // --- CONSTANTES GLOBALES DEL DASHBOARD ---
    const dashboardContainer = document.querySelector('.dashboard-container');
    const mainContentContainer = document.querySelector('.main-content-inner');

    // --- FUNCIÓN PARA EJECUTAR SCRIPTS EN CONTENIDO AJAX ---
    const executeScripts = (container) => {
        container.querySelectorAll('script').forEach(script => {
            const newScript = document.createElement('script');
            newScript.textContent = script.textContent;
            document.body.appendChild(newScript).parentNode.removeChild(newScript);
        });
    };
    
    // --- NAVEGACIÓN PRINCIPAL DEL DASHBOARD ---
    const mainContentInner = document.querySelector('.main-content-inner');
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const sectionId = this.getAttribute('data-section');
            const ajaxWrapper = document.getElementById('ajax-content-wrapper');

            // 1. Actualiza el enlace activo en el menú
            navLinks.forEach(navLink => navLink.classList.remove('active'));
            this.classList.add('active');

            // 2. Decide si es un enlace local (#) o una carga AJAX
            if (url.startsWith('#')) {
                // Oculta el contenedor AJAX si estaba visible
                if (ajaxWrapper) ajaxWrapper.style.display = 'none';

                // Muestra solo la sección estática correspondiente
                mainContentInner.querySelectorAll('.dashboard-section').forEach(section => {
                    section.style.display = (section.id === sectionId) ? 'block' : 'none';
                });

            } else {
                // Oculta todas las secciones estáticas
                mainContentInner.querySelectorAll('.dashboard-section').forEach(section => {
                    section.style.display = 'none';
                });

                // Muestra y carga el contenido en el contenedor AJAX
                if (ajaxWrapper) {
                    ajaxWrapper.innerHTML = '<div class="content-placeholder"><p>Cargando...</p></div>'; // Feedback visual
                    ajaxWrapper.style.display = 'block';

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor.');
                            }
                            return response.text();
                        })
                        .then(html => {
                            ajaxWrapper.innerHTML = html;
                            // Si el contenido cargado tiene scripts, los ejecutamos
                            executeScripts(ajaxWrapper);
                        })
                        .catch(error => {
                            console.error('Error al cargar la sección:', error);
                            ajaxWrapper.innerHTML = '<div class="content-placeholder text-center"><p style="color: var(--color-danger);">Error al cargar el contenido. Intenta de nuevo.</p></div>';
                        });
                }
            }

            // 3. Cierra el menú lateral en vista móvil
            if (window.innerWidth <= 991) {
                document.getElementById('sidebar').classList.remove('active');
            }
        });
    });

    
    
    // --- MANEJO DE MODALES ---

    // 1. Lógica para el modal de AGREGAR platillo
    const platilloModal = document.getElementById('platillo-modal');
    if (platilloModal) {
        const platilloForm = document.getElementById('platillo-form');
        const fileInput = document.getElementById('imagen');
        const fileNameDisplay = document.getElementById('file-name');

        if (fileInput && fileNameDisplay) {
            fileInput.addEventListener('change', function() {
                fileNameDisplay.textContent = this.files.length > 0 ? this.files[0].name : 'Ningún archivo seleccionado';
            });
        }

        const closePlatilloModal = () => {
            platilloModal.classList.remove('active');
            dashboardContainer.classList.remove('blurred');
            if (platilloForm) {
                platilloForm.reset();
                if (fileNameDisplay) fileNameDisplay.textContent = 'Ningún archivo seleccionado';
                platilloForm.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            }
        };

        platilloModal.addEventListener('click', (e) => {
            if (e.target.id === 'cancel-platillo-btn' || e.target.closest('.close-modal-btn') || e.target === platilloModal) {
                closePlatilloModal();
            }
        });
        
        const precioInput = document.getElementById('precio');
        if(precioInput) {
            precioInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
                if (parseFloat(this.value) < 0) this.value = '';
            });
        }

        if (platilloForm) {
            platilloForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenMeta) {
                    alert('Error crítico: La etiqueta de seguridad CSRF no se encontró. Recarga la página.');
                    return;
                }
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content'), 'Accept': 'application/json' }
                })
                .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
                .then(() => {
                    closePlatilloModal();
                    document.querySelector('.nav-link[data-section="menu"]').click();
                })
                .catch(error => {
                    if (error.errors) {
                        platilloForm.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                        for (const key in error.errors) {
                            const input = document.getElementById(key);
                            const errorContainer = input.closest('.input-group-modal').querySelector('.error-message');
                            if (errorContainer) {
                                errorContainer.textContent = error.errors[key][0];
                            }
                        }
                    } else {
                        alert('Ocurrió un error inesperado al guardar el platillo.');
                    }
                });
            });
        }
    }

    // 2. Lógica para el modal de DETALLES/EDICIÓN y ELIMINACIÓN
    const detailsModal = document.getElementById('details-modal');
    const deleteModal = document.getElementById('delete-platillo-modal');
    let openDetailsModal = () => {};
    let currentPlatilloId = null; 

    if (detailsModal) {
        const form = detailsModal.querySelector('#details-form');
        const title = detailsModal.querySelector('#details-modal-title');
        const image = detailsModal.querySelector('#details-image');
        const inputs = form.querySelectorAll('input:not([type="file"]), textarea');
        const availabilityCheckbox = form.querySelector('#details-disponible');
        const newImageInput = form.querySelector('#details-new-image');
        const fileNameDisplay = form.querySelector('#details-file-name');
        
        const viewModeBtns = detailsModal.querySelectorAll('.view-mode-btn');
        const editModeBtns = detailsModal.querySelectorAll('.edit-mode-btn');
        const editModeFields = detailsModal.querySelectorAll('.edit-mode-field');
        
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

        const setEditMode = (isEditing) => {
            detailsModal.classList.toggle('is-editing', isEditing);
            title.textContent = isEditing ? 'Editar Platillo' : 'Detalles del Platillo';
            inputs.forEach(input => {
                input.readOnly = !isEditing;
            });
            availabilityCheckbox.disabled = !isEditing;
            viewModeBtns.forEach(btn => btn.style.display = isEditing ? 'none' : 'block');
            editModeBtns.forEach(btn => btn.style.display = isEditing ? 'block' : 'none');
            editModeFields.forEach(field => field.style.display = isEditing ? 'block' : 'none');
        };

        openDetailsModal = (platilloId) => {
            currentPlatilloId = platilloId;
            fetch(`/platillos/${platilloId}`)
                .then(response => response.json())
                .then(data => {
                    image.src = data.imagen_url || '';
                    form.querySelector('#details-nombre').value = data.nombre;
                    form.querySelector('#details-descripcion').value = data.descripcion;
                    form.querySelector('#details-precio').value = data.precio;
                    availabilityCheckbox.checked = data.disponible;
                    setEditMode(false);
                    detailsModal.classList.add('active');
                    dashboardContainer.classList.add('blurred');
                });
        };
        
        const closeDetailsModal = () => {
            detailsModal.classList.remove('active');
            if (!deleteModal.classList.contains('active')) {
                dashboardContainer.classList.remove('blurred');
            }
            form.reset();
            fileNameDisplay.textContent = 'Ningún archivo nuevo';
            form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        };
        
        const openDeleteModal = () => {
            deleteModal.classList.add('active');
            dashboardContainer.classList.add('blurred');
        };

        const closeDeleteModal = () => {
            deleteModal.classList.remove('active');
            if (!detailsModal.classList.contains('active')) {
                dashboardContainer.classList.remove('blurred');
            }
        };
        
        const handleDelete = () => {
            openDeleteModal();
        };

        confirmDeleteBtn.addEventListener('click', () => {
            if (!currentPlatilloId) return;

            fetch(`/platillos/${currentPlatilloId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(() => {
                closeDetailsModal();
                closeDeleteModal(); 
                document.querySelector('.nav-link[data-section="menu"]').click();
            }).catch(err => {
                console.error('Error al eliminar:', err);
                closeDeleteModal();
                alert('No se pudo eliminar el platillo.');
            });
        });
        
        cancelDeleteBtn.addEventListener('click', closeDeleteModal);
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) closeDeleteModal();
        });


        detailsModal.addEventListener('click', (e) => {
            if (e.target.id === 'edit-btn') setEditMode(true);
            if (e.target.id === 'cancel-edit-btn' || e.target === detailsModal || e.target.closest('.close-modal-btn')) {
                closeDetailsModal();
            }
            if (e.target.id === 'delete-btn') {
                handleDelete();
            }
        });

        newImageInput.addEventListener('change', function() {
            fileNameDisplay.textContent = this.files.length > 0 ? this.files[0].name : 'Ningún archivo nuevo';
        });

        const detailsPrecioInput = document.getElementById('details-precio');
        if(detailsPrecioInput) {
            detailsPrecioInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
                if (parseFloat(this.value) < 0) this.value = '';
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            if (!formData.has('disponible')) {
                formData.append('disponible', 0);
            } else {
                formData.set('disponible', 1);
            }
            
            fetch(`/platillos/${currentPlatilloId}`, {
                method: 'POST', body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
            })
            .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
            .then(() => {
                closeDetailsModal();
                document.querySelector('.nav-link[data-section="menu"]').click();
            })
            .catch(error => {
                if (error.errors) {
                    form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                    for (const key in error.errors) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input && input.closest('.input-group-modal').querySelector('.error-message')) {
                            input.closest('.input-group-modal').querySelector('.error-message').textContent = error.errors[key][0];
                        }
                    }
                } else {
                    alert('Ocurrió un error inesperado al actualizar el platillo.');
                }
            });
        });
    }

    // --- LISTENER DE CLICS PRINCIPAL Y DELEGADO ---
    if (dashboardContainer) {
        dashboardContainer.addEventListener('click', function(e) {
            const addBtn = e.target.closest('#add-platillo-btn');
            const detailsBtn = e.target.closest('.btn-details');

            if (addBtn) {
                if (platilloModal) {
                    platilloModal.classList.add('active');
                    dashboardContainer.classList.add('blurred');
                }
            } else if (detailsBtn) {
                const platilloId = detailsBtn.dataset.id;
                openDetailsModal(platilloId);
            }
        });
    }

    // --- LÓGICA GENERAL DEL DASHBOARD (TEMA, PERFIL, LOGOUT, ETC.) ---
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme) {
            document.body.setAttribute('data-theme', currentTheme);
            if (currentTheme === 'dark') themeToggle.checked = true;
        }
        themeToggle.addEventListener('change', function(e) {
            const theme = e.target.checked ? 'dark' : 'light';
            document.body.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        });
    }

    const profileToggle = document.getElementById('profile-toggle');
    const profileDropdown = document.getElementById('profile-dropdown');
    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (!profileDropdown.contains(e.target) && !profileToggle.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });
    }

    const logoutBtn = document.getElementById('logout-btn');
    const confirmationModal = document.getElementById('confirmation-modal');
    if (logoutBtn && confirmationModal) {
        logoutBtn.addEventListener('click', () => confirmationModal.classList.add('active'));
        document.getElementById('cancel-logout-btn').addEventListener('click', () => confirmationModal.classList.remove('active'));
        document.getElementById('confirm-logout-btn').addEventListener('click', () => window.location.href = '/logout');
        confirmationModal.addEventListener('click', (e) => { if (e.target === confirmationModal) confirmationModal.classList.remove('active'); });
    }

    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => sidebar.classList.toggle('active'));
    }

    const initializeProfileSection = () => {
        const profileForm = document.getElementById('profile-form');
        if (!profileForm) return;

        // --- Seleccion de Elementos ---
        const inputs = profileForm.querySelectorAll('input:not([type="password"])');
        const viewModeBtns = profileForm.querySelectorAll('.view-mode-btn');
        const editModeBtns = profileForm.querySelectorAll('.edit-mode-btn');
        const editModeFields = profileForm.querySelectorAll('.edit-mode-field');
        
        // --- Elementos del Nuevo Modal ---
        const notificationModal = document.getElementById('profile-notification-modal');
        const notificationTitle = document.getElementById('profile-notification-title');
        const notificationMessage = document.getElementById('profile-notification-message');
        const notificationCloseBtn = document.getElementById('profile-notification-close-btn');

        let originalValues = {};

        // Inputs específicos para validación
        const addressInput = document.getElementById('profile-restaurant_address');
        const cuisineInput = document.getElementById('profile-cuisine_type');
        const phoneInput = document.getElementById('profile-contact_phone');
        const attentionScheduleInput = document.getElementById('profile-attention_schedule');
        const currentPassInput = document.getElementById('profile-current_password');
        const newPassInput = document.getElementById('profile-new_password');
        const confirmPassInput = document.getElementById('profile-new_password_confirmation');
        
        // --- Lógica del Nuevo Modal ---
        const showProfileNotification = (title, message, isError = false) => {
            notificationTitle.textContent = title;
            notificationMessage.textContent = message;
            notificationModal.classList.toggle('error', isError);
            notificationModal.classList.toggle('success', !isError);
            notificationModal.classList.add('active');
        };

        const closeProfileNotification = () => {
            notificationModal.classList.remove('active');
        };

        if(notificationCloseBtn) {
            notificationCloseBtn.addEventListener('click', closeProfileNotification);
        }
        if(notificationModal) {
            notificationModal.addEventListener('click', (e) => {
                if (e.target === notificationModal) {
                    closeProfileNotification();
                }
            });
        }

        // --- Lógica de Validación en Tiempo Real ---
        const phoneRegex = /^\d{10}$/;

        const validators = {
            'profile-restaurant_address': (val) => val.trim().length > 0 && val.length <= 200,
            'profile-cuisine_type': (val) => val.trim().length > 0 && val.length <= 50,
            'profile-contact_phone': (val) => phoneRegex.test(val),
            'profile-attention_schedule': (val) => val.length <= 255,
            'profile-current_password': (val) => {
                if (newPassInput.value.length === 0 && confirmPassInput.value.length === 0) return true;
                return val.length > 0;
            },
            'profile-new_password': (val) => {
                if (val.length === 0 && currentPassInput.value.length === 0) return true;
                return val.length >= 8;
            },
            'profile-new_password_confirmation': (val) => {
                return val === newPassInput.value;
            }
        };

        const validateField = (input) => {
            if (!input || typeof validators[input.id] !== 'function') return;
            if (profileForm.classList.contains('view-mode')) return;

            const isValid = validators[input.id](input.value);
            input.classList.toggle('is-valid', isValid);
            input.classList.toggle('is-invalid', !isValid);

            if (input.id === 'profile-new_password') {
                validateField(confirmPassInput);
            }
            if (input.id === 'profile-new_password' || input.id === 'profile-new_password_confirmation') {
                validateField(currentPassInput);
            }
        };
        
        const clearAllValidation = () => {
            profileForm.querySelectorAll('input.is-valid, input.is-invalid').forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });
            profileForm.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        };
        
        [addressInput, cuisineInput, phoneInput, attentionScheduleInput, currentPassInput, newPassInput, confirmPassInput].forEach(input => {
            if (input) {
                input.addEventListener('input', () => validateField(input));
            }
        });

        // --- Lógica de Modo Edición/Vista ---
        const setProfileEditMode = (isEditing) => {
                profileForm.classList.toggle('view-mode', !isEditing);
                profileForm.classList.toggle('edit-mode', isEditing);

                const formElements = profileForm.querySelectorAll('input:not([type="password"]), select');

                formElements.forEach(el => {
                    // Campos que nunca cambian
                    if (el.id === 'profile-full_name' || el.id === 'profile-email') {
                        el.readOnly = true;
                        return; // Continuamos al siguiente elemento
                    }
                    
                    // Lógica para el SELECT
                    if (el.tagName === 'SELECT') {
                        el.disabled = !isEditing; // Lo habilitamos o deshabilitamos
                        el.classList.toggle('form-control-plaintext', !isEditing); // Cambiamos su apariencia
                    } 
                    // Lógica para los INPUTS
                    else {
                        el.readOnly = !isEditing;
                    }

                    // Guardamos valores originales al entrar en modo edición
                    if (isEditing) {
                        originalValues[el.name] = el.value;
                        validateField(el); // Validamos el campo al entrar a editar
                    }
                });
                
                // Campos de contraseña
                [currentPassInput, newPassInput, confirmPassInput].forEach(input => {
                    if(input) input.disabled = !isEditing;
                });

                // Limpiamos todo al salir del modo edición
                if (!isEditing) {
                    clearAllValidation();
                    [currentPassInput, newPassInput, confirmPassInput].forEach(input => {
                        if(input) input.value = '';
                    });
                }

                // Mostramos/ocultamos los botones correspondientes
                viewModeBtns.forEach(btn => btn.style.display = !isEditing ? 'inline-block' : 'none');
                editModeBtns.forEach(btn => btn.style.display = isEditing ? 'inline-block' : 'none');
                editModeFields.forEach(field => field.style.display = isEditing ? 'block' : 'none');
            };

        const editBtn = document.getElementById('edit-profile-btn');
        if(editBtn) editBtn.addEventListener('click', () => setProfileEditMode(true));

        const cancelBtn = document.getElementById('cancel-profile-btn');
        if(cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                inputs.forEach(input => {
                    if (originalValues[input.name] !== undefined) {
                        input.value = originalValues[input.name];
                    }
                });
                setProfileEditMode(false);
            });
        }

        // --- LÓGICA PARA MOSTRAR/OCULTAR CONTRASEÑA ---
        profileForm.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const passwordInput = this.previousElementSibling;
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        });

        // --- LÓGICA PARA VALIDACIÓN DE CONTRASEÑA EN TIEMPO REAL ---
        const newPasswordInput = document.getElementById('profile-new_password');
        if (newPasswordInput) {
            const requirements = {
                length: document.querySelector('#password-requirements #length'),
                uppercase: document.querySelector('#password-requirements #uppercase'),
                special: document.querySelector('#password-requirements #special')
            };

            newPasswordInput.addEventListener('keyup', function() {
                const value = this.value;

                // 1. Validar longitud (8-25 caracteres)
                requirements.length.classList.toggle('valid', value.length >= 8 && value.length <= 25);
                
                // 2. Validar mayúscula
                requirements.uppercase.classList.toggle('valid', /[A-Z]/.test(value));
                
                // 3. Validar caracter especial
                requirements.special.classList.toggle('valid', /[!@#$%^&*(),.?":{}|<>]/.test(value));
            });
        }

        // --- Lógica de Envío (Submit) ---
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isFormValid = true;
            const allInputsToValidate = [addressInput, cuisineInput, phoneInput, attentionScheduleInput, currentPassInput, newPassInput, confirmPassInput];
            
            allInputsToValidate.forEach(input => {
                if (input) {
                    validateField(input);
                    if (input.classList.contains('is-invalid')) {
                        isFormValid = false;
                    }
                }
            });

            if (!isFormValid) {
                showProfileNotification('Formulario incompleto', 'Por favor, corrige los campos marcados en rojo.', true);
                return;
            }
            
            const formData = new FormData(this);
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            clearAllValidation();

                formData.append('_method', 'PUT');

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '',
                        'Accept': 'application/json',
                    }
                })
            .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
            .then(data => {
                // Itera sobre todos los datos recibidos y actualiza los campos del formulario
                Object.keys(data).forEach(key => {
                    // Busca el input por su atributo 'name'
                    const input = profileForm.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = data[key];
                    }
                });
                setProfileEditMode(false); // Cambia de nuevo a modo vista
                showProfileNotification('¡Éxito!', 'Tu perfil se ha actualizado correctamente.');
            })
            .catch(error => {
                clearAllValidation(); 
                if (error.errors) {
                    let firstErrorMessage = 'Por favor, revisa los errores en el formulario.';
                    let firstErrorFound = false;
                    
                    for (const key in error.errors) {
                        if (!firstErrorFound) {
                            firstErrorMessage = error.errors[key][0];
                            firstErrorFound = true;
                        }
                        
                        let inputId = `profile-${key}`;
                        if (key === 'new_password_confirmation') inputId = `profile-new_password_confirmation`;
                        
                        const input = document.getElementById(inputId);
                        const errorContainer = input ? input.closest('.form-group').querySelector('.error-message') : null;

                        if (input) input.classList.add('is-invalid');

                        if (errorContainer) {
                            errorContainer.textContent = error.errors[key][0];
                        }
                    }
                    showProfileNotification('Error de validación', firstErrorMessage, true);
                } else {
                    showProfileNotification('Error inesperado', 'Ocurrió un problema al guardar los datos. Inténtalo de nuevo.', true);
                }
            });
        });

        setProfileEditMode(false);
    };
    // --- MODIFICACIÓN EN LA NAVEGACIÓN PRINCIPAL ---
    // Asegúrate de que tu lógica de navegación llame a initializeProfileSection
    // después de cargar el contenido de perfil vía AJAX.
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const sectionId = this.getAttribute('data-section');

            // Quita 'active' de todos y lo pone en el clickeado
            document.querySelectorAll('.nav-link').forEach(navLink => {
                navLink.classList.toggle('active', navLink.getAttribute('data-section') === sectionId);
            });

            // Oculta todas las secciones estáticas y limpia el contenedor AJAX
            mainContentContainer.querySelectorAll('.dashboard-section').forEach(s => s.classList.remove('active'));
            const ajaxWrapper = document.getElementById('ajax-content-wrapper');
            if (ajaxWrapper) ajaxWrapper.innerHTML = ""; // Limpia contenido anterior

            if (url.startsWith('#')) {
                // Si es un enlace interno (#inicio, #pedidos), muestra la sección estática
                const targetSection = document.getElementById(sectionId);
                if (targetSection) {
                    targetSection.classList.add('active');
                }
            } else {
                // Si es una URL real (platillos, perfil), carga por AJAX
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        let dynamicWrapper = document.getElementById('ajax-content-wrapper');
                        if (!dynamicWrapper) {
                            dynamicWrapper = document.createElement('div');
                            dynamicWrapper.id = 'ajax-content-wrapper';
                            mainContentContainer.appendChild(dynamicWrapper);
                        }
                        dynamicWrapper.innerHTML = html;

                        // Ejecuta scripts si los hubiera en el HTML cargado
                        executeScripts(dynamicWrapper);

                        // Muestra notificaciones si existen y las oculta después de 5s
                        const notification = dynamicWrapper.querySelector('.notification.show');
                        if (notification) {
                            setTimeout(() => {
                                notification.classList.remove('show');
                            }, 5000); // Ajusta el tiempo si es necesario
                        }

                        // ---> ¡IMPORTANTE! Aquí llamas a la inicialización específica <---
                        if (sectionId === 'perfil') {
                            initializeProfileSection();
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar la sección:', error);
                         let dynamicWrapper = document.getElementById('ajax-content-wrapper');
                         if(dynamicWrapper) dynamicWrapper.innerHTML = '<p class="error-message">Error al cargar el contenido. Intenta de nuevo.</p>';
                    });
            }

            // Cierra sidebar en móvil y dropdown de perfil
            if (window.innerWidth <= 991 && sidebar) {
                sidebar.classList.remove('active');
            }
            const profileDropdown = document.getElementById('profile-dropdown');
            if (profileDropdown) {
                profileDropdown.classList.remove('active');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA PARA MOSTRAR/OCULTAR CONTRASEÑA ---
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');

    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const passwordInput = this.previousElementSibling;
            // Cambia el tipo de input entre 'password' y 'text'
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Cambia el ícono del ojo
            this.classList.toggle('fa-eye-slash');
        });
    });

    // --- LÓGICA PARA VALIDACIÓN EN TIEMPO REAL DE LA NUEVA CONTRASEÑA ---
    const newPasswordInput = document.getElementById('new_password');
    if (newPasswordInput) {
        const requirements = {
            length: document.getElementById('length'),
            uppercase: document.getElementById('uppercase'),
            special: document.getElementById('special')
        };

        newPasswordInput.addEventListener('keyup', function() {
            const value = this.value;

            // 1. Validar longitud (8-25 caracteres)
            if (value.length >= 8 && value.length <= 25) {
                requirements.length.classList.add('valid');
                requirements.length.classList.remove('invalid');
            } else {
                requirements.length.classList.add('invalid');
                requirements.length.classList.remove('valid');
            }

            // 2. Validar mayúscula
            if (/[A-Z]/.test(value)) {
                requirements.uppercase.classList.add('valid');
                requirements.uppercase.classList.remove('invalid');
            } else {
                requirements.uppercase.classList.add('invalid');
                requirements.uppercase.classList.remove('valid');
            }

            // 3. Validar caracter especial
            if (/[@$!%*?&.]/.test(value)) {
                requirements.special.classList.add('valid');
                requirements.special.classList.remove('invalid');
            } else {
                requirements.special.classList.add('invalid');
                requirements.special.classList.remove('valid');
            }
        });
    }

});