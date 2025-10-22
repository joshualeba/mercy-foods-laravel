<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Elige tu próximo platillo</h1>
        <p>Explora los menús de los restaurantes locales disponibles para ti.</p>
    </div>
</div>

<div class="content-placeholder">
    @php
        $hayPlatillos = false;
        foreach ($restaurantes as $restaurante) {
            if ($restaurante->platillos->isNotEmpty()) {
                $hayPlatillos = true;
                break;
            }
        }
    @endphp

    @if(!$hayPlatillos)
        <div class="text-center p-5">
            <p>No hay platillos disponibles en este momento. ¡Vuelve a intentarlo más tarde!</p>
        </div>
    @else
        <div class="platillo-grid">
            @foreach($restaurantes as $restaurante)
                @foreach($restaurante->platillos as $platillo)
                    <div class="platillo-card">
                        <div style="padding: 1rem 1rem 0 1rem; text-align: center; font-weight: 600; color: var(--text-color-light);">
                            {{ $restaurante->full_name }}
                        </div>
                        <div class="platillo-card-image">
                            <img src="{{ $platillo->imagen_url }}" alt="Imagen de {{ $platillo->nombre }}">
                        </div>
                        <div class="platillo-card-content">
                            <h3>{{ $platillo->nombre }}</h3>
                            <p>{{ $platillo->descripcion }}</p>
                            <div class="platillo-card-footer">
                                <span class="platillo-card-price">${{ number_format($platillo->precio, 2) }}</span>
                            </div>
                        </div>
                        <div class="platillo-card-actions">
                            <button class="btn btn-primary btn-sm" style="width: 100%;">Ordenar</button>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    @endif
</div>