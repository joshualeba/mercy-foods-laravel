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
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const sectionId = this.getAttribute('data-section');
            document.querySelectorAll('.nav-link').forEach(navLink => navLink.classList.toggle('active', navLink.getAttribute('data-section') === sectionId));

            if (url.startsWith('#')) {
                mainContentContainer.querySelectorAll('.dashboard-section').forEach(s => s.classList.remove('active'));
                const ajaxWrapper = document.getElementById('ajax-content-wrapper');
                if(ajaxWrapper) ajaxWrapper.innerHTML = "";
                const targetSection = document.getElementById(sectionId);
                if (targetSection) targetSection.classList.add('active');
            } else {
                mainContentContainer.querySelectorAll('.dashboard-section').forEach(s => s.classList.remove('active'));
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        let ajaxWrapper = document.getElementById('ajax-content-wrapper');
                        if (!ajaxWrapper) {
                            ajaxWrapper = document.createElement('div');
                            ajaxWrapper.id = 'ajax-content-wrapper';
                            mainContentContainer.appendChild(ajaxWrapper);
                        }
                        ajaxWrapper.innerHTML = html;
                        const notification = document.querySelector('.notification.show');
                            if (notification) {
                                setTimeout(() => {
                                    notification.classList.remove('show');
                                }, 5000);
                            }
                    })
                    .catch(error => console.error('Error al cargar la sección:', error));
            }

            if (window.innerWidth <= 991) document.getElementById('sidebar').classList.remove('active');
            document.getElementById('profile-dropdown')?.classList.remove('active');
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

    // 2. Lógica para el modal de DETALLES/EDICIÓN
    const detailsModal = document.getElementById('details-modal');
    let openDetailsModal = () => {};

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
        let currentPlatilloId = null;

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
            dashboardContainer.classList.remove('blurred');
            form.reset();
            fileNameDisplay.textContent = 'Ningún archivo nuevo';
            form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        };
        
        const handleDelete = () => {
            if (confirm('¿Estás seguro de que quieres eliminar este platillo? Esta acción no se puede deshacer.')) {
                fetch(`/platillos/${currentPlatilloId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(() => {
                    closeDetailsModal();
                    document.querySelector('.nav-link[data-section="menu"]').click();
                }).catch(err => console.error('Error al eliminar:', err));
            }
        };

        detailsModal.addEventListener('click', (e) => {
            if (e.target.id === 'edit-btn') setEditMode(true);
            if (e.target.id === 'cancel-edit-btn' || e.target === detailsModal || e.target.closest('.close-modal-btn')) {
                closeDetailsModal();
            }
            if (e.target.id === 'delete-btn') handleDelete();
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
        const profileNotification = document.getElementById('profile-notification');
        const profileNotificationMessage = document.getElementById('profile-notification-message');
        let originalValues = {};

        // Inputs específicos para validación
        const fullNameInput = document.getElementById('profile-full_name');
        const addressInput = document.getElementById('profile-restaurant_address');
        const cuisineInput = document.getElementById('profile-cuisine_type');
        const phoneInput = document.getElementById('profile-contact_phone');
        const attentionScheduleInput = document.getElementById('profile-attention_schedule');
        const emailInput = document.getElementById('profile-email');
        const currentPassInput = document.getElementById('profile-current_password');
        const newPassInput = document.getElementById('profile-new_password');
        const confirmPassInput = document.getElementById('profile-new_password_confirmation');
        
        // --- Lógica de Validación en Tiempo Real ---

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^\d{10}$/;

        // Objeto con las funciones de validación
        const validators = {
            'profile-restaurant_address': (val) => val.length > 0 && val.length <= 200,
            'profile-cuisine_type': (val) => val.length > 0 && val.length <= 50,
            'profile-contact_phone': (val) => phoneRegex.test(val),
            'profile-attention_schedule': (val) => val.length <= 255,
            'profile-current_password': (val) => {
                if (newPassInput.value.length === 0 && confirmPassInput.value.length === 0) return true;
                return val.length > 0;
            },
            'profile-new_password': (val) => {
                if (val.length === 0) return true;
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
        
        // --- CAMBIO 1 ---
        // Quitamos fullNameInput y emailInput de la validación en tiempo real
        [addressInput, cuisineInput, phoneInput, attentionScheduleInput, currentPassInput, newPassInput, confirmPassInput].forEach(input => {
            if (input) {
                input.addEventListener('input', () => validateField(input));
            }
        });

        // --- Lógica de Modo Edición/Vista ---
        const setProfileEditMode = (isEditing) => {
            profileForm.classList.toggle('view-mode', !isEditing);
            profileForm.classList.toggle('edit-mode', isEditing);

            inputs.forEach(input => {
                // --- CAMBIO 2 ---
                // Añadimos una condición para que nombre y email SIEMPRE sean readonly
                if (input.id === 'profile-full_name' || input.id === 'profile-email') {
                    input.readOnly = true;
                } else {
                    input.readOnly = !isEditing;
                }
                // --- FIN DEL CAMBIO ---

                if (isEditing) {
                    originalValues[input.name] = input.value;
                    // Validamos solo los campos que SÍ son editables
                    if (input.id !== 'profile-full_name' && input.id !== 'profile-email') {
                        validateField(input);
                    }
                }
            });
            
            [currentPassInput, newPassInput, confirmPassInput].forEach(input => {
                if(input) input.disabled = !isEditing;
            });

            if (!isEditing) {
                clearAllValidation();
                [currentPassInput, newPassInput, confirmPassInput].forEach(input => {
                    if(input) input.value = '';
                });
            }

            viewModeBtns.forEach(btn => btn.style.display = isEditing ? 'none' : 'inline-block');
            editModeBtns.forEach(btn => btn.style.display = isEditing ? 'inline-block' : 'none');
            editModeFields.forEach(field => field.style.display = isEditing ? 'block' : 'none');
        };

        const editBtn = document.getElementById('edit-profile-btn');
        if(editBtn) {
            editBtn.addEventListener('click', () => setProfileEditMode(true));
        }

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

        // --- Lógica de Envío (Submit) ---
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isFormValid = true;
            
            // --- CAMBIO 3 ---
            // Quitamos fullNameInput y emailInput de la validación final antes de enviar
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
                profileNotificationMessage.textContent = 'Por favor, corrige los campos marcados en rojo.';
                profileNotification.className = 'notification error show';
                profileNotification.style.display = 'block';
                setTimeout(() => profileNotification.classList.remove('show'), 3000);
                return;
            }
            
            const formData = new FormData(this);
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            profileNotification.style.display = 'none';
            clearAllValidation();

            // --- CAMBIO 4 (Opcional pero bueno) ---
            // Evitamos enviar los campos no editables al backend.
            // Aunque el backend no debería procesarlos, es más limpio.
            // formData.delete('full_name'); // Descomenta si tu backend falla al recibir campos que no espera actualizar
            // formData.delete('email');     // Descomenta si tu backend falla
            

            fetch('{{ route("restaurante.perfil.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
            .then(data => {
                // Éxito
                Object.keys(data).forEach(key => {
                    const input = profileForm.querySelector(`[name="${key}"]`);
                    if (input) input.value = data[key];
                });
                setProfileEditMode(false);
                
                profileNotificationMessage.textContent = 'Perfil actualizado con éxito.';
                profileNotification.className = 'notification success show';
                profileNotification.style.display = 'block';
                setTimeout(() => profileNotification.classList.remove('show'), 3000);

                // Actualizar dropdown (esto ya no es necesario para nombre/email si no cambian, pero lo dejamos)
                const profileNameDropdown = document.getElementById('profile-name');
                if (profileNameDropdown && data.full_name) {
                    profileNameDropdown.textContent = data.full_name;
                    const profilePic = document.getElementById('profile-pic');
                    if (profilePic) {
                        profilePic.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(data.full_name)}&background=FF6347&color=fff&bold=true`;
                    }
                }
                const profileEmailDropdown = document.getElementById('profile-email');
                if (profileEmailDropdown && data.email) {
                    profileEmailDropdown.textContent = data.email;
                }
            })
            .catch(error => {
                // Error de servidor (validación, etc.)
                clearAllValidation(); 

                if (error.errors) {
                    for (const key in error.errors) {
                        let inputId = `profile-${key}`;
                        if (key === 'new_password_confirmation') inputId = `profile-new_password_confirmation`;
                        
                        const input = document.getElementById(inputId);
                        const errorContainer = input ? input.closest('.form-group').querySelector('.error-message') : null;

                        if (input) input.classList.add('is-invalid');

                        if (errorContainer) {
                            errorContainer.textContent = error.errors[key][0];
                        } else {
                            profileNotificationMessage.textContent = `Error: ${error.errors[key][0]}`;
                            profileNotification.className = 'notification error show';
                            profileNotification.style.display = 'block';
                            setTimeout(() => profileNotification.classList.remove('show'), 5000);
                        }
                    }
                } else {
                    profileNotificationMessage.textContent = 'Ocurrió un error inesperado.';
                    profileNotification.className = 'notification error show';
                    profileNotification.style.display = 'block';
                    setTimeout(() => profileNotification.classList.remove('show'), 5000);
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
                        // Agrega aquí otras inicializaciones si cargas más secciones por AJAX
                        // if (sectionId === 'otraSeccion') {
                        //     initializeOtraSeccion();
                        // }

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