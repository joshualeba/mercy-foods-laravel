<div class="menu-header">
    <h1>Mi Menú</h1>
    <button class="btn btn-primary" id="add-platillo-btn">Agregar Platillo</button>
</div>

<div class="content-placeholder">
    @if($platillos->isEmpty())
        <div class="text-center">
            <p>Aún no has agregado ningún platillo. ¡Comienza a construir tu menú!</p>
        </div>
    @else
        <div class="platillo-grid">
            @foreach($platillos as $platillo)
                <div class="platillo-card">
                    <div class="platillo-card-image">
                        <img src="{{ $platillo->imagen_url }}" alt="{{ $platillo->nombre }}">
                    </div>
                    <div class="platillo-card-content">
                        <h3>{{ $platillo->nombre }}</h3>
                        <p>{{ Str::limit($platillo->descripcion, 80) }}</p>
                        <span class="platillo-card-price">${{ number_format($platillo->precio, 2) }}</span>
                    </div>
                    <div class="platillo-card-actions">
                        <a href="#" class="btn btn-primary btn-sm">Ver Detalles</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>