<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Elige tu próximo platillo</h1>
        <p>Explora los menús de los restaurantes locales disponibles para ti</p>
    </div>
</div>

<div class="search-filter-container">
    <div class="search-wrapper">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="text" id="search-input" placeholder="Buscar por platillo, restaurante o descripción...">
    </div>
    <button class="btn btn-primary" id="open-filter-modal-btn">Filtros</button>
</div>

<div class="content-placeholder">
    @if($platillos->isEmpty())
        <div class="text-center p-5">
            <p>No hay platillos disponibles en este momento. ¡Vuelve a intentarlo más tarde!</p>
        </div>
    @else
        <div class="platillo-grid" id="platillos-container">
            @foreach($platillos as $platillo)
                <div class="platillo-card"
                     data-nombre="{{ strtolower($platillo->nombre) }}"
                     data-descripcion="{{ strtolower($platillo->descripcion) }}"
                     data-restaurante="{{ strtolower($platillo->user->full_name) }}"
                     data-precio="{{ $platillo->precio }}"
                     data-cocina="{{ $platillo->user->cuisine_type }}">
                    
                    <div style="text-align: center; padding: 1rem 0;">
                        <span style="background-color: #FF6347; color: white; padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: 600; display: inline-block;">
                            {{ $platillo->user->full_name }}
                        </span>
                        @if($platillo->user->total_reviews_restaurante > 0)
                            <div style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--text-color-semidark);">
                                <span style="color: #ffc107; font-size: 1rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($platillo->user->average_rating_restaurante))
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </span>
                                <span style="margin-left: 0.3rem;">
                                    {{ number_format($platillo->user->average_rating_restaurante, 1) }} 
                                    ({{ $platillo->user->total_reviews_restaurante }} {{ $platillo->user->total_reviews_restaurante == 1 ? 'reseña' : 'reseñas' }})
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="platillo-card-image">
                        <img src="{{ $platillo->imagen_url }}" alt="Imagen de {{ $platillo->nombre }}">
                    </div>
                    <div class="platillo-card-content">
                        <h3>{{ $platillo->nombre }}</h3>
                        <div class="platillo-card-footer">
                            <span class="platillo-card-price">${{ number_format($platillo->precio, 2) }}</span>
                            <button class="btn btn-sm ver-mas-btn"
                                    style="background-color: #333; color: white; border: 1px solid #555; margin-left: 10px;"
                                    data-nombre="{{ $platillo->nombre }}"
                                    data-descripcion="{{ $platillo->descripcion }}"
                                    data-precio="{{ number_format($platillo->precio, 2) }}"
                                    data-imagen="{{ $platillo->imagen_url }}"
                                    data-restaurante="{{ $platillo->user->full_name }}" 
                                    data-id="{{ $platillo->id }}">
                                Ver más
                            </button>
                        </div>
                    </div>
                    <div class="platillo-card-actions">
                        <button class="btn btn-primary btn-sm btn-ordenar" 
                                style="width: 100%;"
                                data-nombre="{{ $platillo->nombre }}"
                                data-descripcion="{{ $platillo->descripcion }}"
                                data-precio="{{ number_format($platillo->precio, 2) }}"
                                data-imagen="{{ $platillo->imagen_url }}"
                                data-restaurante="{{ $platillo->user->full_name }}" 
                                data-id="{{ $platillo->id }}">
                            Ordenar
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="no-results-message" class="no-results-message" style="display: none;">
            <p>No se encontraron platillos que coincidan con tu búsqueda o filtros.</p>
        </div>
    @endif
</div>

<div class="confirmation-modal-overlay" id="filter-modal">
    <div class="modal-box">
        <button type="button" class="close-modal-btn" id="close-filter-modal-btn">&times;</button>
        <h2>Filtrar Resultados</h2>
        <form id="filter-form">
            
            {{-- CAMBIO 1: Slider de precio máximo --}}
            <div class="input-group-modal">
                <label for="price-slider">Precio máximo</label>
                <div class="price-slider-container">
                    <div id="price-slider"></div>
                    <div id="price-slider-value" style="margin-top: 10px; text-align: center; font-weight: 500;"></div>
                </div>
            </div>
            
            {{-- CAMBIO 2: Lista desplegable para tipo de cocina --}}
            @if($tiposCocina->isNotEmpty())
                <div class="input-group-modal">
                    <label for="cuisine-type-select">Tipo de cocina</label>
                    <select id="cuisine-type-select" name="cuisine_type">
                        <option value="">Cualquier tipo</option>
                        {{-- Opciones tomadas de tu formulario de registro --}}
                        <option value="mexicana">Mexicana</option>
                        <option value="italiana">Italiana</option>
                        <option value="japonesa">Japonesa</option>
                        <option value="americana">Americana</option>
                        <option value="cafeteria">Cafetería</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            @endif

            <div class="modal-buttons">
                <button type="button" class="btn-cancel" id="clear-filters-btn">Limpiar</button>
                <button type="button" class="btn-confirm" id="apply-filters-btn">Aplicar</button>
            </div>
        </form>
    </div>
</div>