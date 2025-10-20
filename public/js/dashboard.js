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
    const navLinks = document.querySelectorAll('.nav-link');
    const mainContentContainer = document.querySelector('.main-content-inner');
    const dashboardContainer = document.querySelector('.dashboard-container');
    const platilloModal = document.getElementById('platillo-modal');
    
    // --- FUNCIÓN PARA EJECUTAR SCRIPTS EN CONTENIDO AJAX ---
    const executeScripts = (container) => {
        container.querySelectorAll('script').forEach(script => {
            const newScript = document.createElement('script');
            newScript.textContent = script.textContent;
            document.body.appendChild(newScript).parentNode.removeChild(newScript);
        });
    };
    
    // --- NAVEGACIÓN PRINCIPAL DEL DASHBOARD ---
    if (navLinks.length > 0 && mainContentContainer) {
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                const sectionId = this.getAttribute('data-section');

                navLinks.forEach(navLink => navLink.classList.toggle('active', navLink.getAttribute('data-section') === sectionId));

                if (url.startsWith('#')) {
                    const allSections = mainContentContainer.querySelectorAll('.dashboard-section');
                    allSections.forEach(s => s.classList.remove('active'));
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
    }
    
    // --- MANEJO DE MODALES Y CLICS DINÁMICOS ---
    if (dashboardContainer) {
        dashboardContainer.addEventListener('click', function(e) {
            // Abre el modal de agregar platillo
            if (e.target.closest('#add-platillo-btn')) {
                if (platilloModal) {
                    platilloModal.classList.add('active');
                    dashboardContainer.classList.add('blurred');
                }
            }
        });
    }

    // Lógica del modal de platillo (cerrar y enviar formulario)
    if (platilloModal) {
        const platilloForm = document.getElementById('platillo-form');
        const fileInput = document.getElementById('imagen');
        const fileNameDisplay = document.getElementById('file-name');

        // Lógica para mostrar el nombre del archivo seleccionado
        if (fileInput && fileNameDisplay) {
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileNameDisplay.textContent = this.files[0].name;
                } else {
                    fileNameDisplay.textContent = 'Ningún archivo seleccionado';
                }
            });
        }
        
        const closeModal = () => {
            platilloModal.classList.remove('active');
            dashboardContainer.classList.remove('blurred');
            if (platilloForm) {
                platilloForm.reset();
                if (fileNameDisplay) {
                    fileNameDisplay.textContent = 'Ningún archivo seleccionado';
                }
                platilloForm.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            }
        };

        platilloModal.addEventListener('click', (e) => {
            if (e.target.id === 'cancel-platillo-btn' || e.target === platilloModal) {
                closeModal();
            }
        });

        // Validación en tiempo real para el precio
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

                // Verificación de seguridad para el token CSRF
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenMeta) {
                    alert('Error crítico: La etiqueta de seguridad CSRF no se encontró. Recarga la página.');
                    return;
                }

                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content'), // Usamos la variable verificada
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
                .then(() => {
                    closeModal();
                    // Recarga el contenido del menú
                    const menuLink = document.querySelector('.nav-link[data-section="menu"]');
                    if (menuLink) menuLink.click();
                })
                .catch(error => {
                    if (error.errors) {
                        platilloForm.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                        for (const key in error.errors) {
                            const input = document.getElementById(key);
                            if (input && input.closest('.input-group-modal').querySelector('.error-message')) {
                                input.closest('.input-group-modal').querySelector('.error-message').textContent = error.errors[key][0];
                            }
                        }
                    } else {
                        console.error('Error:', error);
                        alert('Ocurrió un error inesperado al guardar el platillo.');
                    }
                });
            });
        }
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