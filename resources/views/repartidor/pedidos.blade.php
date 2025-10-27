<div class="pedidos-header">
    <h1>Mis entregas asignadas</h1>
</div>

<div class="order-board">
    <div class="order-column">
        <div class="order-column-header">
            <h2>Pedidos por recoger</h2>
            <span class="order-count">{{ $pedidosPorRecoger->count() }}</span>
        </div>
        <div class="order-cards-container">
            @forelse($pedidosPorRecoger as $pedido)
                <div class="order-card">
                    <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>{{ $pedido->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Restaurante:</strong> {{ $pedido->restaurante->full_name }}</p>
                        <p><strong>Dirección del restaurante:</strong> {{ $pedido->restaurante->restaurant_address }}</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">Cliente: {{ $pedido->cliente->full_name }}</span>
                        <div class="order-actions">
                            <button class="btn btn-primary btn-sm">Marcar como recogido</button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No tienes pedidos por recoger.</p>
            @endforelse
        </div>
    </div>
</div>