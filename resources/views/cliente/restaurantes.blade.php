<div class="restaurants-section-container">
    <h1>Explora y ordena lo que se te antoje</h1>

    {{-- Formulario de búsqueda y filtros --}}
    <form id="search-filter-form" action="{{ route('cliente.restaurantes') }}" method="GET">
        <div class="search-bar-full-width">
            <input type="text" name="search" id="search-input" class="form-control" placeholder="Busca tu platillo o restaurante favorito...">
            <button type="button" id="toggle-filters-btn" class="btn-filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
                <span>Filtros</span>
            </button>
        </div>

        <div id="filter-options" class="filter-options-container" style="display: none;">
            <div class="filter-group">
                <label for="tipo_comida">Tipo de Comida</label>
                <select name="tipo_comida" id="tipo_comida" class="form-control">
                    <option value="">Todos</option>
                    @foreach($tiposDeComida as $tipo)
                        <option value="{{ $tipo }}">{{ ucfirst($tipo) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Rango de Precio</label>
                <div class="price-range">
                    <input type="number" name="min_precio" id="min_precio" class="form-control" placeholder="Mín.">
                    <span>-</span>
                    <input type="number" name="max_precio" id="max_precio" class="form-control" placeholder="Máx.">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            </div>
        </div>
    </form>

    {{-- Contenedor para los resultados (se actualizará con AJAX) --}}
    <div id="platillos-result-container" class="mt-4">
        @include('cliente.partials.lista-platillos', ['platillos' => $platillos])
    </div>
</div>


{{-- ESTILOS PARA LA NUEVA INTERFAZ --}}
<style>
    /* ... (Estilos anteriores que quieras conservar) ... */
    
    /* Barra de búsqueda y botón de filtros */
    .search-bar-full-width {
        display: flex;
        gap: 1rem;
        width: 100%;
        margin-bottom: 1rem;
    }
    .search-bar-full-width .form-control {
        flex-grow: 1; /* Hace que la barra de búsqueda ocupe el espacio disponible */
        padding: 12px 20px;
        border-radius: 50px;
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-color-dark);
        font-size: 1rem;
    }
    .btn-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 1.5rem;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 50px;
        color: var(--text-color-dark);
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .btn-filter:hover {
        background-color: var(--main-bg);
    }

    /* Contenedor de filtros desplegables */
    .filter-options-container {
        background-color: var(--card-bg);
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        align-items: end;
    }
    .filter-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    .price-range {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Grid para las nuevas tarjetas de platillos */
    .platillo-grid-v3 {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    /* Tarjeta de platillo final */
    .platillo-card-v3 {
        background-color: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .platillo-card-v3:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .platillo-card-header-v3 {
        padding: 0.75rem 1rem;
        background-color: var(--main-bg);
        border-bottom: 1px solid var(--border-color);
    }
    .platillo-card-header-v3 h3 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }
    .platillo-card-image-v3 {
        width: 100%;
        height: 180px;
        overflow: hidden;
    }
    .platillo-card-image-v3 img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .platillo-card-content-v3 {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .platillo-card-content-v3 h4 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .platillo-card-footer-v3 {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 1rem;
    }
    .platillo-card-price-v3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
    }
</style>


{{-- SCRIPT PARA LA LÓGICA DE FILTRADO AJAX --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('search-filter-form');
    const resultContainer = document.getElementById('platillos-result-container');
    const searchInput = document.getElementById('search-input');
    const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
    const filterOptions = document.getElementById('filter-options');

    // Mostrar/ocultar el panel de filtros
    toggleFiltersBtn.addEventListener('click', () => {
        if (filterOptions.style.display === 'none') {
            filterOptions.style.display = 'grid';
        } else {
            filterOptions.style.display = 'none';
        }
    });

    // Función para ejecutar la búsqueda y el filtrado
    const fetchFilteredPlatillos = async () => {
        // Muestra un estado de carga
        resultContainer.innerHTML = '<div class="content-placeholder"><p>Buscando...</p></div>';

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        try {
            const response = await fetch(`${form.action}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Importante para que Laravel detecte que es una petición AJAX
                }
            });

            if (!response.ok) {
                throw new Error('La respuesta del servidor no fue exitosa.');
            }
            
            const html = await response.text();
            resultContainer.innerHTML = html;

        } catch (error) {
            console.error('Error al filtrar:', error);
            resultContainer.innerHTML = '<div class="content-placeholder text-center"><p style="color: var(--color-danger);">Ocurrió un error al cargar los resultados.</p></div>';
        }
    };

    // Evento para cuando se envía el formulario (al hacer clic en "Aplicar Filtros")
    form.addEventListener('submit', (e) => {
        e.preventDefault(); // Evita que la página se recargue
        fetchFilteredPlatillos();
    });

    // Evento para búsqueda en tiempo real (opcional, pero mejora la experiencia)
    let typingTimer;
    const doneTypingInterval = 500; // 500ms de espera

    searchInput.addEventListener('keyup', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(fetchFilteredPlatillos, doneTypingInterval);
    });
});
</script>