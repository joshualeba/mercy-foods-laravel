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
                        executeScripts(ajaxWrapper);
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
});