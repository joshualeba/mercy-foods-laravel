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

// --- INICIALIZACIÓN DE LIBRERÍAS EXTERNAS ---
var typed = new Typed('#typed-text', {
    strings: ["apoyo local", "sabor sin esperas", "calidad y rapidez", "comunidad a tu puerta", "la comida que amas, más cerca"],
    typeSpeed: 70,
    backSpeed: 40,
    loop: true
});

const swiper = new Swiper('.testimonials-slider', {
    loop: true,
    grabCursor: true,
    spaceBetween: 30,
    autoplay: {
        delay: 5000,
        disableOnInteraction: true,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        640: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
    }
});

// --- LÓGICA PRINCIPAL DE LA PÁGINA ---
document.addEventListener('DOMContentLoaded', function() {
    
    const faqAccordionContainer = document.querySelector('#faq-modal .faq-accordion');

    if (faqAccordionContainer) {
        faqAccordionContainer.addEventListener('click', function (e) {
            const question = e.target.closest('.faq-question');
            if (!question) return;

            const item = question.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const isOpen = item.classList.contains('active');

            faqAccordionContainer.querySelectorAll('.faq-item').forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-answer').style.maxHeight = null;
                }
            });

            if (!isOpen) {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
            } else {
                item.classList.remove('active');
                answer.style.maxHeight = null;
            }
        });
    }

    // --- MANEJO UNIFICADO DE TODOS LOS MODALES ---
    const setupModal = (modalId, openBtnId, beforeOpenCallback = null) => {
        const modal = document.getElementById(modalId);
        const openBtn = document.getElementById(openBtnId);

        if (!modal || !openBtn) return;

        const closeBtn = modal.querySelector('.modal-close-btn');

        const open = (e) => {
            e.preventDefault();
            if (beforeOpenCallback) {
                beforeOpenCallback();
            }
            modal.classList.add('active');
        };

        const close = () => modal.classList.remove('active');

        openBtn.addEventListener('click', open);
        if (closeBtn) {
            closeBtn.addEventListener('click', close);
        }
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                close();
            }
        });
    };

    // Inicializamos cada modal
    setupModal('privacy-modal', 'open-modal-link');
    setupModal('terms-modal', 'open-terms-link');
    setupModal('faq-modal', 'open-faq-link');
});