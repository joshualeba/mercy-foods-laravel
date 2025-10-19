document.addEventListener('DOMContentLoaded', function() {

    // --- LÓGICA DE NAVEGACIÓN DEL SIDEBAR ---
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.dashboard-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetSectionId = this.getAttribute('data-section');

            // Oculta todas las secciones y quita la clase activa de los links
            sections.forEach(section => section.classList.remove('active'));
            navLinks.forEach(navLink => navLink.classList.remove('active'));

            // Muestra la sección correcta y activa el link correspondiente
            document.getElementById(targetSectionId).classList.add('active');
            this.classList.add('active');

            // Cierra el sidebar en vista móvil después de hacer clic
            if (window.innerWidth <= 991) {
                sidebar.classList.remove('active');
            }
        });
    });

    // --- LÓGICA DEL TEMA (DÍA/NOCHE) ---
    const themeToggle = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme) {
        document.body.setAttribute('data-theme', currentTheme);
        if (currentTheme === 'dark') {
            if(themeToggle) themeToggle.checked = true;
        }
    }

    function switchTheme(e) {
        if (e.target.checked) {
            document.body.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    }
    if (themeToggle) themeToggle.addEventListener('change', switchTheme);
    
    // --- LÓGICA DEL MENÚ DE PERFIL ---
    const profileToggle = document.getElementById('profile-toggle');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', () => {
            profileDropdown.classList.toggle('active');
        });
        window.addEventListener('click', function(e) {
            if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });
    }

    // --- LÓGICA DEL MODAL DE CIERRE DE SESIÓN ---
    const logoutBtn = document.getElementById('logout-btn');
    const confirmationModal = document.getElementById('confirmation-modal');
    const confirmLogoutBtn = document.getElementById('confirm-logout-btn');
    const cancelLogoutBtn = document.getElementById('cancel-logout-btn');

    const openConfirmationModal = () => confirmationModal?.classList.add('active');
    const closeConfirmationModal = () => confirmationModal?.classList.remove('active');

    const executeLogout = () => {
        // Redirección a una ruta de logout que Laravel manejará
        window.location.href = '/logout'; 
    };

    if(logoutBtn) logoutBtn.addEventListener('click', openConfirmationModal);
    if(confirmLogoutBtn) confirmLogoutBtn.addEventListener('click', executeLogout);
    if(cancelLogoutBtn) cancelLogoutBtn.addEventListener('click', closeConfirmationModal);
    if(confirmationModal) confirmationModal.addEventListener('click', (e) => {
        if (e.target === confirmationModal) closeConfirmationModal();
    });

    // --- LÓGICA DEL MENÚ HAMBURGUESA PARA MÓVIL ---
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
});