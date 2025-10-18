document.addEventListener('DOMContentLoaded', function() {
    
    const userData = JSON.parse(localStorage.getItem('mercifood_user'));

    const welcomeTitle = document.getElementById('welcome-title');
    if (welcomeTitle) {
        if (userData && userData.fullName) {
            welcomeTitle.textContent = `Bienvenido/a, ${userData.fullName}`;
        } else {
            welcomeTitle.textContent = 'Bienvenido/a';
        }
    }

    const profileName = document.getElementById('profile-name');
    const profileEmail = document.getElementById('profile-email');
    const profilePic = document.getElementById('profile-pic');
    
    if (userData) {
        if (profileName) profileName.textContent = userData.fullName || 'Usuario';
        if (profileEmail) profileEmail.textContent = userData.email || 'No email';
        if (profilePic) {
            profilePic.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(userData.fullName || 'U')}&background=FF6347&color=fff&bold=true`;
        }
    }

    const themeToggle = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme) {
        document.body.setAttribute('data-theme', currentTheme);
        if (currentTheme === 'dark') {
            themeToggle.checked = true;
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

    if (themeToggle) {
        themeToggle.addEventListener('change', switchTheme, false);
    }

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

    const logoutBtn = document.getElementById('logout-btn');
    const confirmationModal = document.getElementById('confirmation-modal');
    const confirmLogoutBtn = document.getElementById('confirm-logout-btn');
    const cancelLogoutBtn = document.getElementById('cancel-logout-btn');

    const openConfirmationModal = () => {
        if (confirmationModal) confirmationModal.classList.add('active');
    };

    const closeConfirmationModal = () => {
        if (confirmationModal) confirmationModal.classList.remove('active');
    };

    const executeLogout = () => {
        localStorage.removeItem('mercifood_user');
        window.location.href = '/login';
    };

    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openConfirmationModal();
        });
    }
    if (confirmLogoutBtn) confirmLogoutBtn.addEventListener('click', executeLogout);
    if (cancelLogoutBtn) cancelLogoutBtn.addEventListener('click', closeConfirmationModal);
    if (confirmationModal) {
        confirmationModal.addEventListener('click', (e) => {
            if (e.target === confirmationModal) closeConfirmationModal();
        });
    }

    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
});
