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
        delay: 4000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    breakpoints: {
        640: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
    }
});


// --- LÓGICA PRINCIPAL DE LA PÁGINA ---
document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA PARA EL ACORDEÓN DE FAQ ---
    const faqAccordionContainer = document.querySelector('#faq-modal .faq-accordion');
    let faqsLoaded = false;

    // Función para manejar el click en las preguntas (la funcionalidad que faltaba)
    const setupFaqEventListeners = () => {
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                const answer = item.querySelector('.faq-answer');
                const isOpen = item.classList.contains('active');

                // Cierra todos los demás items
                faqItems.forEach(i => {
                    i.classList.remove('active');
                    i.querySelector('.faq-answer').style.maxHeight = '0px';
                });

                // Si el item no estaba abierto, lo abre
                if (!isOpen) {
                    item.classList.add('active');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });
    };
    
    // Función para cargar los datos de la API
    const loadFaqs = () => {
        if (faqsLoaded) return;

        fetch('/faq') // Usando la ruta de web.php
            .then(response => response.json())
            .then(data => {
                faqAccordionContainer.innerHTML = '';
                data.faqs.forEach(faq => {
                    const item = document.createElement('div');
                    item.className = 'faq-item';
                    item.innerHTML = `
                        <button class="faq-question">
                            <span>${faq.question}</span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p>${faq.answer}</p>
                        </div>
                    `;
                    faqAccordionContainer.appendChild(item);
                });
                faqsLoaded = true;
                // Una vez creados los elementos, les añadimos la funcionalidad de clic
                setupFaqEventListeners(); 
            })
            .catch(error => {
                console.error('Error al cargar las FAQs:', error);
                faqAccordionContainer.innerHTML = '<p style="color: white;">No se pudieron cargar las preguntas frecuentes.</p>';
            });
    };

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
    setupModal('faq-modal', 'open-faq-link', loadFaqs); // Le pasamos loadFaqs como la acción a ejecutar antes de abrir
});