<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>¡Hola, {{ Auth::user()->full_name }}!</h2>
        <p>¿Qué te apetece comer hoy? Aquí tienes algunas sugerencias para empezar.</p>
    </div>
</div>

@if($platillosSugeridos->isNotEmpty())
    <h3 class="section-title">Te sugerimos los siguientes platillos...</h3>
    <div class="platillo-grid">
        @foreach($platillosSugeridos as $platillo)
            <div class="platillo-card">
                <div style="padding: 1rem 1rem 0 1rem; text-align: center; font-weight: 600; color: var(--text-color-light);">
                    {{ $platillo->user->full_name }}
                </div>
                <div class="platillo-card-image">
                    <img src="{{ $platillo->imagen_url ?? 'https://via.placeholder.com/400x200.png?text=Mercy+Food' }}" alt="Imagen de {{ $platillo->nombre }}">
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
    </div>
@else
    <div class="text-center p-5">
        <p>No hay platillos disponibles para sugerir en este momento. ¡Explora los restaurantes!</p>
    </div>
@endif

{{-- Mantenemos los accesos rápidos --}}
<h3 class="section-title mt-5">Accesos rápidos</h3>
<div class="quick-actions-grid">
    {{-- Aquí van las tarjetas de "action-card" que ya tenías --}}
    <div class="action-card" data-section="ordenar">
        <div class="card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/><path d="M9 21v-8a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v8"/><path d="M9 10V7"/><path d="M15 10V7"/></svg>
        </div>
        <h3>Explorar Restaurantes</h3>
        <p>Mira los menús disponibles y haz tu próximo pedido.</p>
    </div>
    <div class="action-card disabled">
         <div class="card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <h3>Mis Pedidos</h3>
        <p>Revisa el historial y el estado de tus órdenes.</p>
        <span>Próximamente</span>
    </div>
</div>