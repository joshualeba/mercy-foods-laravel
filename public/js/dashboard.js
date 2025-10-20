document.addEventListener('DOMContentLoaded', function() {

    // --- LÓGICA DE NAVEGACIÓN UNIFICADA ---
    const navLinks = document.querySelectorAll('.nav-link');
    const mainContentContainer = document.querySelector('.main-content-inner');

    if (navLinks.length > 0 && mainContentContainer) {
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                const sectionId = this.getAttribute('data-section');

                // Activa TODOS los enlaces que apunten a la misma sección
                navLinks.forEach(navLink => {
                    navLink.classList.toggle('active', navLink.getAttribute('data-section') === sectionId);
                });

                // CASO 1: Navegación interna (Dashboard de Cliente)
                if (url.startsWith('#')) {
                    const allSections = mainContentContainer.querySelectorAll('.dashboard-section');
                    const targetSection = document.getElementById(sectionId);

                    allSections.forEach(s => s.classList.remove('active'));
                    
                    // Limpia el contenido AJAX si existiera
                    const ajaxWrapper = document.getElementById('ajax-content-wrapper');
                    if(ajaxWrapper) ajaxWrapper.innerHTML = "";

                    if (targetSection) {
                        targetSection.classList.add('active');
                    }
                } 
                // CASO 2: Carga de contenido externo (Dashboard de Restaurante)
                else {
                    const allSections = mainContentContainer.querySelectorAll('.dashboard-section');
                    allSections.forEach(s => s.classList.remove('active'));

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
                        })
                        .catch(error => {
                            console.error('Error al cargar la sección:', error);
                            mainContentContainer.innerHTML = '<p>Error al cargar el contenido.</p>';
                        });
                }

                // Cierra el sidebar en móvil
                if (window.innerWidth <= 991) {
                    document.getElementById('sidebar').classList.remove('active');
                }

                document.getElementById('profile-dropdown')?.classList.remove('active');
            });
        });
    }

    // --- TU CÓDIGO ORIGINAL PARA TODO LO DEMÁS (TEMA, PERFIL, LOGOUT, ETC.) ---
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
        const openModal = () => confirmationModal.classList.add('active');
        const closeModal = () => confirmationModal.classList.remove('active');
        logoutBtn.addEventListener('click', openModal);
        document.getElementById('cancel-logout-btn').addEventListener('click', closeModal);
        document.getElementById('confirm-logout-btn').addEventListener('click', () => { window.location.href = '/logout'; });
        confirmationModal.addEventListener('click', (e) => { if (e.target === confirmationModal) closeModal(); });
    }

    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => sidebar.classList.toggle('active'));
    }
});