@if($platillos->isEmpty())
    <div class="text-center p-5">
        <p>No se encontraron platillos que coincidan con los filtros aplicados.</p>
    </div>
@else
    <div class="platillo-grid-v3">
        @foreach($platillos as $platillo)
            {{-- La nueva tarjeta de platillo que incluye el nombre del restaurante --}}
            <div class="platillo-card-v3">
                <div class="platillo-card-header-v3">
                    <h3>{{ $platillo->user->full_name }}</h3>
                </div>
                <div class="platillo-card-image-v3">
                    <img src="{{ Storage::url($platillo->imagen_url) }}" alt="{{ $platillo->nombre }}">
                </div>
                <div class="platillo-card-content-v3">
                    <h4>{{ $platillo->nombre }}</h4>
                    <p class="platillo-description">{{ $platillo->descripcion }}</p>
                    <div class="platillo-card-footer-v3">
                        <span class="platillo-card-price-v3">${{ number_format($platillo->precio, 2) }}</span>
                        <button class="btn-add-to-cart">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                            <span>Añadir</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif