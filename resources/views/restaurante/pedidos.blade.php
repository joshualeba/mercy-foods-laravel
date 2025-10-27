<div class="pedidos-header">
    <h1>Gestionar Pedidos</h1>
</div>

<div class="order-board">
    <div class="order-column">
        <div class="order-column-header">
            <h2>Nuevos</h2>
            <span class="order-count">{{ $pedidosNuevos->count() }}</span>
        </div>
        <div class="order-cards-container">
            @forelse($pedidosNuevos as $pedido)
                <div class="order-card" data-id="{{ $pedido->id }}">
                    <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>{{ $pedido->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Cliente:</strong> {{ $pedido->cliente->full_name }}</p>
                        </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <div class="order-actions">
                            <button class="btn btn-primary btn-sm" onclick="actualizarEstado({{ $pedido->id }}, 'en_preparacion')">Aceptar</button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No hay pedidos nuevos.</p>
            @endforelse
        </div>
    </div>

    <div class="order-column">
        <div class="order-column-header">
            <h2>En Preparación</h2>
             <span class="order-count">{{ $pedidosEnPreparacion->count() }}</span>
        </div>
        <div class="order-cards-container">
            @forelse($pedidosEnPreparacion as $pedido)
                <div class="order-card" data-id="{{ $pedido->id }}">
                     <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>{{ $pedido->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Cliente:</strong> {{ $pedido->cliente->full_name }}</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <div class="order-actions">
                            <button class="btn btn-primary btn-sm" onclick="actualizarEstado({{ $pedido->id }}, 'listo_para_recoger')">Marcar como Listo</button>
                        </div>
                    </div>
                </div>
            @empty
                <p>Ningún pedido en preparación.</p>
            @endforelse
        </div>
    </div>

    <div class="order-column">
        <div class="order-column-header">
            <h2>Listos para Recoger</h2>
            <span class="order-count">{{ $pedidosListos->count() }}</span>
        </div>
        <div class="order-cards-container">
             @forelse($pedidosListos as $pedido)
                <div class="order-card" data-id="{{ $pedido->id }}">
                    <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>{{ $pedido->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Cliente:</strong> {{ $pedido->cliente->full_name }}</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <div class="order-actions">
                            <small>Esperando repartidor...</small>
                        </div>
                    </div>
                </div>
            @empty
                <p>Ningún pedido listo.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
function actualizarEstado(pedidoId, nuevoEstado) {
    fetch(`/pedidos/${pedidoId}/estado`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ estado: nuevoEstado })
    })
    .then(response => response.json())
    .then(data => {
        // Recargar la sección de pedidos para ver los cambios
        document.querySelector('.nav-link[data-section="pedidos"]').click();
    })
    .catch(error => console.error('Error:', error));
}
</script>