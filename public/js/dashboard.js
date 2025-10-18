document.addEventListener('DOMContentLoaded', function() {

    // --- LÓGICA PARA MOSTRAR EL NOMBRE DE USUARIO (MODIFICADA) ---
    const welcomeTitle = document.getElementById('welcome-title');
    // Recuperamos los datos del usuario guardados en localStorage durante el login
    const userData = JSON.parse(localStorage.getItem('mercifood_user'));

    // Verificamos si tenemos los datos del usuario y su nombre
    if (userData && userData.fullName) {
        // Si es así, mostramos el mensaje personalizado y corregido
        welcomeTitle.textContent = `Bienvenido/a, ${userData.fullName}`;
    } else {
        // De lo contrario, mostramos un mensaje genérico
        welcomeTitle.textContent = 'Bienvenido/a';
    }

    // --- LÓGICA PARA EL MODAL DE CIERRE DE SESIÓN ---
    const logoutBtn = document.getElementById('logout-btn');
    const confirmationModal = document.getElementById('confirmation-modal');
    const confirmLogoutBtn = document.getElementById('confirm-logout-btn');
    const cancelLogoutBtn = document.getElementById('cancel-logout-btn');

    // Función para mostrar el modal de confirmación
    const openConfirmationModal = () => {
        if (confirmationModal) {
            confirmationModal.classList.add('active');
        }
    };

    // Función para cerrar el modal
    const closeConfirmationModal = () => {
        if (confirmationModal) {
            confirmationModal.classList.remove('active');
        }
    };

    // La función que ejecuta el cierre de sesión
    const executeLogout = () => {
        localStorage.removeItem('mercifood_user');
        window.location.href = '/login'; // Redirige a la ruta de login de Laravel
    };

    // 1. Al hacer clic en "Cerrar Sesión", abrimos el modal
    if (logoutBtn) {
        logoutBtn.addEventListener('click', openConfirmationModal);
    }

    // 2. Si el usuario confirma, cerramos la sesión
    if (confirmLogoutBtn) {
        confirmLogoutBtn.addEventListener('click', executeLogout);
    }

    // 3. Si el usuario cancela, solo cerramos el modal
    if (cancelLogoutBtn) {
        cancelLogoutBtn.addEventListener('click', closeConfirmationModal);
    }

    // 4. También cerramos el modal si se hace clic fuera de la caja
    if (confirmationModal) {
        confirmationModal.addEventListener('click', (e) => {
            if (e.target === confirmationModal) {
                closeConfirmationModal();
            }
        });
    }

    // --- LÓGICA PARA EL MENÚ RESPONSIVO ---
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
});