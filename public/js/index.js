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
    let faqsLoaded = false;

    const faqs = [
        {
            question: '¿Cómo funciona Mercy Food para los clientes?',
            answer: 'Es muy fácil. Simplemente buscas tu platillo o restaurante favorito en nuestra plataforma, seleccionas lo que quieres ordenar del menú, pagas de forma segura a través de la aplicación y uno de nuestros repartidores afiliados te lo llevará directamente a tu puerta. ¡Así de simple apoyas a los negocios locales!'
        },
        {
            question: '¿Qué métodos de pago son aceptados y qué tan seguros son?',
            answer: 'Aceptamos las principales tarjetas de crédito y débito. La seguridad es nuestra máxima prioridad, por lo que toda tu información de pago viaja encriptada y procesamos las transacciones cumpliendo con los más altos estándares de seguridad para proteger tus datos en todo momento.'
        },
        {
            question: 'Tengo un restaurante, ¿cómo puedo asociarme con Mercy Food?',
            answer: '¡Nos encantaría que te unieras! El proceso es sencillo: ve a nuestra página de registro, selecciona la opción "Soy restaurante" y completa la información de tu negocio. Nuestro equipo revisará tu solicitud para darte acceso a las herramientas con las que podrás gestionar tu menú, recibir pedidos y aumentar tus ventas llegando a miles de nuevos clientes.'
        },
        {
            question: 'Quiero ser repartidor, ¿qué necesito para empezar?',
            answer: 'Para ser repartidor de Mercy Food, solo necesitas ser mayor de edad, tener un vehículo (motocicleta, bicicleta o automóvil) y un smartphone. Regístrate en nuestra plataforma seleccionando "Soy repartidor", completa tu perfil y, una vez aprobado, podrás empezar a recibir notificaciones de pedidos para generar ingresos con un horario totalmente flexible.'
        },
        {
            question: '¿Qué debo hacer si hay un problema con mi pedido?',
            answer: 'Si tienes algún inconveniente, como un retraso o un error en tu orden, por favor contacta a nuestro equipo de soporte a través del chat en vivo disponible en la aplicación. Nuestro objetivo es ayudarte a resolver cualquier problema de la manera más rápida y eficiente posible para garantizar tu satisfacción.'
        }
    ];

    const loadFaqs = () => {
        if (faqsLoaded || !faqAccordionContainer) return;

        faqAccordionContainer.innerHTML = '';
        faqs.forEach(faq => {
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
    };

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
    setupModal('faq-modal', 'open-faq-link', loadFaqs); // Le pasamos loadFaqs como la acción a ejecutar antes de abrir
});