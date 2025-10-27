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
    const sidebar = document.getElementById('sidebar');
    const successModal = document.getElementById('success-modal');
    const errorModal = document.getElementById('error-modal');

    const manageNotificationModal = (modal, message = null) => {
        if (!modal) return;

        const messageElement = modal.querySelector('p');
        if (message && messageElement) {
            messageElement.textContent = message;
        }

        modal.classList.add('active');

        const closeButton = modal.querySelector('.btn-modal-close');
        if (closeButton) {
            closeButton.onclick = () => modal.classList.remove('active');
        }

        // Clic fuera para cerrar
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                modal.classList.remove('active');
            }
        });
    };

    // --- FUNCIÓN PARA EJECUTAR SCRIPTS EN CONTENIDO AJAX ---
    const executeScripts = (container) => {
        container.querySelectorAll('script').forEach(script => {
            const newScript = document.createElement('script');
            newScript.textContent = script.textContent;
            document.body.appendChild(newScript).parentNode.removeChild(newScript);
        });
    };

    // =================================================================
    // === INICIALIZADOR DE LA SECCIÓN "ORDENAR" (BÚSQUEDA Y FILTROS) ===
    // =================================================================
    const initializeOrderSection = () => {
        const platillosContainer = document.getElementById('platillos-container');
        if (!platillosContainer) return;

        // --- Elementos del DOM ---
        const allPlatilloCards = platillosContainer.querySelectorAll('.platillo-card');
        const searchInput = document.getElementById('search-input');
        const noResultsMessage = document.getElementById('no-results-message');
        const filterModal = document.getElementById('filter-modal');
        const openModalBtn = document.getElementById('open-filter-modal-btn');
        const closeModalBtn = document.getElementById('close-filter-modal-btn');
        const applyFiltersBtn = document.getElementById('apply-filters-btn');
        const clearFiltersBtn = document.getElementById('clear-filters-btn');
        const filterForm = document.getElementById('filter-form');
        const priceSliderEl = document.getElementById('price-slider');
        const priceSliderValue = document.getElementById('price-slider-value');
        const cuisineSelect = document.getElementById('cuisine-type-select'); // El nuevo select

        // --- Rango de precios para el slider ---
        const maxPrice = 500; // Fijo como solicitaste

        // --- Inicialización del Slider ---
        let priceSlider;
        if (priceSliderEl) {
            priceSlider = noUiSlider.create(priceSliderEl, {
                start: maxPrice, // Inicia en el valor máximo
                connect: 'lower', // Colorea la barra desde el inicio hasta la manija
                step: 10,
                range: {
                    'min': 0,
                    'max': maxPrice
                },
                format: {
                    to: value => '$' + parseFloat(value).toFixed(2),
                    from: value => Number(value.replace('S/', ''))
                }
            });

            // Actualiza el texto que muestra el valor del slider
            priceSlider.on('update', (values) => {
                priceSliderValue.innerHTML = `Mostrar platillos que tengan un precio de hasta ${values[0]}`;
            });
        }

        // --- Función principal de filtrado ---
        const applySearchAndFilters = () => {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const sliderMax = priceSlider ? priceSlider.get(true) : maxPrice;
            const selectedCuisine = cuisineSelect ? cuisineSelect.value : '';

            let visibleCount = 0;
            allPlatilloCards.forEach(card => {
                const nombre = card.dataset.nombre.toLowerCase();
                const descripcion = card.dataset.descripcion.toLowerCase();
                const restaurante = card.dataset.restaurante.toLowerCase();
                const precio = parseFloat(card.dataset.precio);
                const cocina = card.dataset.cocina;

                const matchesSearch = searchTerm === '' || nombre.includes(searchTerm) || descripcion.includes(searchTerm) || restaurante.includes(searchTerm);
                const matchesPrice = precio <= sliderMax; // La condición ahora es solo "menor o igual que"
                const matchesCuisine = selectedCuisine === '' || cocina === selectedCuisine;

                if (matchesSearch && matchesPrice && matchesCuisine) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        };

        // --- Event Listeners ---
        if (searchInput) searchInput.addEventListener('input', applySearchAndFilters);
        if (openModalBtn) openModalBtn.addEventListener('click', () => filterModal.classList.add('active'));
        if (closeModalBtn) closeModalBtn.addEventListener('click', () => filterModal.classList.remove('active'));
        
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', () => {
                applySearchAndFilters();
                filterModal.classList.remove('active');
            });
        }

        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                // Resetea el formulario (limpia el select)
                if (filterForm) filterForm.reset();
                // Resetea el slider a su posición máxima
                if (priceSlider) {
                    priceSlider.set(maxPrice);
                }
                // Aplica los filtros (que ahora estarán limpios)
                applySearchAndFilters();
            });
        }

        if (filterModal) filterModal.addEventListener('click', e => {
            if (e.target === filterModal) filterModal.classList.remove('active');
        });
    };

    // ... (El resto del archivo `dashboard.js` permanece igual)
    
    // =================================================================
    // === INICIALIZADOR DE LA SECCIÓN "PERFIL" ===
    // =================================================================
    const initializeProfileSection = () => {
        const profileForm = document.getElementById('profile-form');
        if (!profileForm) return;

        const inputs = profileForm.querySelectorAll('input:not([type="password"])');
        const viewModeBtns = profileForm.querySelectorAll('.view-mode-btn');
        const editModeBtns = profileForm.querySelectorAll('.edit-mode-btn');
        const editModeFields = profileForm.querySelectorAll('.edit-mode-field');
        
        const notificationModal = document.getElementById('profile-notification-modal');
        const notificationTitle = document.getElementById('profile-notification-title');
        const notificationMessage = document.getElementById('profile-notification-message');
        const notificationCloseBtn = document.getElementById('profile-notification-close-btn');

        let originalValues = {};

        const addressInput = document.getElementById('profile-address') || document.getElementById('profile-restaurant_address');
        const cuisineInput = document.getElementById('profile-cuisine_type');
        const phoneInput = document.getElementById('profile-contact_phone');
        const attentionScheduleInput = document.getElementById('profile-attention_schedule');
        const currentPassInput = document.getElementById('profile-current_password');
        const newPassInput = document.getElementById('profile-new_password');
        const confirmPassInput = document.getElementById('profile-new_password_confirmation');
        
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

        const phoneRegex = /^\d{10}$/;

        const validators = {
            'profile-address': (val) => val === null || val.length <= 200,
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
        
        [addressInput, cuisineInput, phoneInput, attentionScheduleInput, currentPassInput, newPassInput, confirmPassInput]
        .filter(input => input)
        .forEach(input => {
            if (input) {
                input.addEventListener('input', () => validateField(input));
            }
        });

        const setProfileEditMode = (isEditing) => {
            profileForm.classList.toggle('view-mode', !isEditing);
            profileForm.classList.toggle('edit-mode', isEditing);

            const formElements = profileForm.querySelectorAll('input:not([type="password"]), select');

            formElements.forEach(el => {
                // Si el elemento no tiene un 'name', no es parte del formulario que se envía.
                // Esto excluye los campos de la tarjeta que solo son de visualización.
                if (!el.name) {
                    return;
                }

                if (el.id === 'profile-full_name' || el.id === 'profile-email') {
                    el.readOnly = true;
                    return;
                }
                
                if (el.tagName === 'SELECT') {
                    el.disabled = !isEditing; // Usar 'disabled' para selects es más robusto.
                    el.classList.toggle('form-control-plaintext', !isEditing);
                } else {
                    el.readOnly = !isEditing;
                }

                if (isEditing) {
                    originalValues[el.name] = el.value;
                    validateField(el);
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

        profileForm.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const passwordInput = this.previousElementSibling;
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        });

        const newPasswordInput = document.getElementById('profile-new_password');
        if (newPasswordInput) {
            const requirements = {
                length: document.querySelector('#password-requirements #length'),
                uppercase: document.querySelector('#password-requirements #uppercase'),
                special: document.querySelector('#password-requirements #special')
            };

            newPasswordInput.addEventListener('keyup', function() {
                const value = this.value;
                requirements.length.classList.toggle('valid', value.length >= 8 && value.length <= 25);
                requirements.uppercase.classList.toggle('valid', /[A-Z]/.test(value));
                requirements.special.classList.toggle('valid', /[!@#$%^&*(),.?":{}|<>]/.test(value));
            });
        }

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
                Object.keys(data).forEach(key => {
                    const input = profileForm.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = data[key];
                    }
                });
                setProfileEditMode(false);
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

    // *** EJECUTA LAS INICIALIZACIONES SI EL CONTENIDO YA ESTÁ CARGADO ***
    if (document.getElementById('platillos-container')) {
        initializeOrderSection();
    }
    if (document.getElementById('profile-form')) {
        initializeProfileSection();
    }
    
    // =================================================================
    // === NAVEGACIÓN PRINCIPAL DEL DASHBOARD ===
    // =================================================================
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const sectionId = this.getAttribute('data-section');
            const ajaxWrapper = document.getElementById('ajax-content-wrapper');

            // Actualiza el enlace activo en el menú
            navLinks.forEach(navLink => navLink.classList.remove('active'));
            const sidebarLink = document.querySelector(`#sidebar .nav-link[data-section="${sectionId}"]`);
            if (sidebarLink) {
                sidebarLink.classList.add('active');
            }

            // Decide si es un enlace local (#) o una carga AJAX
            if (url.startsWith('#')) {
                // Oculta el contenedor AJAX si estaba visible
                if (ajaxWrapper) ajaxWrapper.style.display = 'none';

                // Muestra solo la sección estática correspondiente
                if (mainContentContainer) {
                    mainContentContainer.querySelectorAll('.dashboard-section').forEach(section => {
                        section.style.display = (section.id === sectionId) ? 'block' : 'none';
                    });
                }

            } else {
                // Oculta todas las secciones estáticas
                if (mainContentContainer) {
                    mainContentContainer.querySelectorAll('.dashboard-section').forEach(section => {
                        section.style.display = 'none';
                    });
                }

                // Muestra y carga el contenido en el contenedor AJAX
                if (ajaxWrapper) {
                    ajaxWrapper.innerHTML = '<div class="content-placeholder"><p>Cargando...</p></div>';
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
                            executeScripts(ajaxWrapper);

                            if (sectionId === 'ordenar') {
                                initializeOrderSection();
                            } else if (sectionId === 'perfil') {
                                initializeProfileSection();
                            } else if (sectionId === 'pago') {
                                initializePaymentSection();
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar la sección:', error);
                            ajaxWrapper.innerHTML = '<div class="content-placeholder text-center"><p style="color: var(--color-danger);">Error al cargar el contenido. Intenta de nuevo.</p></div>';
                        });
                }
            }

            // Cierra el menú lateral en vista móvil
            if (window.innerWidth <= 991 && sidebar) {
                sidebar.classList.remove('active');
            }
            
            // Cierra dropdown de perfil
            const profileDropdown = document.getElementById('profile-dropdown');
            if (profileDropdown) {
                profileDropdown.classList.remove('active');
            }
        });
    });

    // ... Resto del código (modales, theme, etc.) continúa igual ...

    // --- MANEJO DE MODALES ---
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
            const actionCard = e.target.closest('.action-card:not(.disabled)');

            // --- LISTENER DE CLICS PRINCIPAL Y DELEGADO ---
            if (dashboardContainer) {
                dashboardContainer.addEventListener('click', function(e) {
                    const addPaymentBtn = e.target.closest('#add-payment-method-from-profile');
                    if (addPaymentBtn) {
                        const paymentNavLink = document.querySelector('.nav-link[data-section="pago"]');
                        if (paymentNavLink) {
                            paymentNavLink.click();
                        }
                    }
                });
            }

            if (addBtn) {
                if (platilloModal) {
                    platilloModal.classList.add('active');
                    dashboardContainer.classList.add('blurred');
                }
            } else if (detailsBtn) {
                const platilloId = detailsBtn.dataset.id;
                openDetailsModal(platilloId);
            } else if (actionCard) {
                const section = actionCard.dataset.section;
                if (section) {
                    const navLink = document.querySelector(`.nav-link[data-section="${section}"]`);
                    if (navLink) {
                        navLink.click();
                    }
                }
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
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => sidebar.classList.toggle('active'));
    }

});

// =================================================================
// === CÓDIGO ADICIONAL PARA VALIDACIÓN DE CONTRASEÑAS ===
// =================================================================
document.addEventListener('DOMContentLoaded', function() {
    
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');

    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const passwordInput = this.previousElementSibling;
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    });

    const newPasswordInput = document.getElementById('new_password');
    if (newPasswordInput) {
        const requirements = {
            length: document.getElementById('length'),
            uppercase: document.getElementById('uppercase'),
            special: document.getElementById('special')
        };

        newPasswordInput.addEventListener('keyup', function() {
            const value = this.value;

            if (value.length >= 8 && value.length <= 25) {
                requirements.length.classList.add('valid');
                requirements.length.classList.remove('invalid');
            } else {
                requirements.length.classList.add('invalid');
                requirements.length.classList.remove('valid');
            }

            if (/[A-Z]/.test(value)) {
                requirements.uppercase.classList.add('valid');
                requirements.uppercase.classList.remove('invalid');
            } else {
                requirements.uppercase.classList.add('invalid');
                requirements.uppercase.classList.remove('valid');
            }

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

/**
 * Muestra el modal de notificación con un título y mensaje específicos.
 * @param {string} title - El título del modal.
 * @param {string} message - El mensaje a mostrar en el cuerpo del modal.
 * @param {boolean} isSuccess - Si es `true`, el título se verá en verde; si es `false`, en rojo.
 */
function showModal(title, message, isSuccess) {
    const modalOverlay = document.getElementById('notification-modal-overlay');
    const modalTitle = document.getElementById('notification-modal-title');
    const modalMessage = document.getElementById('notification-modal-message');

    if (!modalOverlay || !modalTitle || !modalMessage) {
        console.error('No se encontraron los elementos del modal en el DOM.');
        return;
    }

    // Actualizar contenido del modal
    modalTitle.textContent = title;
    modalMessage.textContent = message;

    // Cambiar color del título según el resultado
    modalTitle.style.color = isSuccess ? 'var(--success-color)' : 'var(--error-color)';

    // Mostrar el modal
    modalOverlay.classList.add('active');

    // Lógica para cerrar el modal
    const closeModal = () => modalOverlay.classList.remove('active');
    
    // Asignar eventos a los botones de cierre
    const closeBtn = document.getElementById('notification-modal-close-btn');
    const acceptBtn = document.getElementById('notification-modal-accept-btn');
    
    if(closeBtn) closeBtn.onclick = closeModal;
    if(acceptBtn) acceptBtn.onclick = closeModal;
}

/**
 * Inicializa la lógica de la sección de métodos de pago,
 * incluyendo la validación y el envío del formulario.
 */
function initializePaymentSection() {
    const form = document.getElementById('payment-form');
    if (!form) return;

    // Inicialización de Cleave.js para formatear los inputs
    const cardNumberCleave = new Cleave('#card_number', {
        creditCard: true,
        delimiter: ' ',
        onCreditCardTypeChanged: function (type) {
            // Lógica para mostrar el logo de la tarjeta (opcional)
        }
    });

    const cardExpiryCleave = new Cleave('#card_expiry', {
        date: true,
        datePattern: ['m', 'y']
    });

    const cardCvcCleave = new Cleave('#card_cvc', {
        numericOnly: true,
        blocks: [4]
    });


    form.addEventListener('submit', function(event) {
        event.preventDefault();

        let isValid = true;
        const fields = [
            { id: 'card_name', name: 'nombre', min: 5, max: 50 },
            { id: 'card_number', name: 'número de tarjeta', min: 19, max: 19 },
            { id: 'card_expiry', name: 'expiración', min: 5, max: 5 },
            { id: 'card_cvc', name: 'CVC', min: 3, max: 4 }
        ];

        fields.forEach(fieldInfo => {
            const input = document.getElementById(fieldInfo.id);
            const errorMessageContainer = input.closest('.form-group').querySelector('.error-message');
            let message = '';

            if (!input.value.trim()) {
                message = `El campo ${fieldInfo.name} es obligatorio.`;
            } else if (input.value.length < fieldInfo.min) {
                message = `El ${fieldInfo.name} debe tener al menos ${fieldInfo.min} caracteres.`;
            } else if (input.value.length > fieldInfo.max) {
                message = `El ${fieldInfo.name} no puede exceder los ${fieldInfo.max} caracteres.`;
            }

            if (message) {
                isValid = false;
                errorMessageContainer.textContent = message;
                input.classList.add('is-invalid');
            } else {
                errorMessageContainer.textContent = '';
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            showModal('Validación Fallida', 'Por favor, corrige los errores en el formulario.', false);
            return;
        }

        const submitButton = document.getElementById('submit-payment-btn');
        const formData = new FormData(form);
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');

        submitButton.disabled = true;
        submitButton.textContent = 'Procesando...';

        // Lógica de simulación y envío real
        const cardNumber = document.getElementById('card_number').value;
        if (!cardNumber.startsWith('4242')) {
            setTimeout(() => {
                showModal('Pago Rechazado', 'La tarjeta fue rechazada. Por favor, intenta con otra.', false);
                submitButton.disabled = false;
                submitButton.textContent = 'Guardar Método de Pago';
            }, 1500);
            return;
        }

        // Si la tarjeta es la de prueba, procedemos a guardar en el backend
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (ok) {
                showModal('¡Éxito!', data.message, true);
                // Opcional: limpiar el formulario después de guardar
                form.reset();
            } else {
                // Muestra el error que viene del backend
                showModal('Error al guardar', data.message || 'No se pudo guardar la tarjeta.', false);
            }
        })
        .catch(error => {
            console.error('Error en el fetch:', error);
            showModal('Error de Conexión', 'No se pudo comunicar con el servidor. Intenta más tarde.', false);
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = 'Guardar Método de Pago';
        });
    });
}