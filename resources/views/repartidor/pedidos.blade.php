<div class="pedidos-header">
    <h1>Pedidos disponibles</h1>
</div>

<div class="order-board" style="grid-template-columns: repeat(4, 1fr);">
    <div class="order-column">
        <div class="order-column-header">
            <h2>Disponibles para ti</h2>
            <span class="order-count">{{ $pedidosDisponibles->count() }}</span>
        </div>
        <div class="order-cards-container">
            @forelse($pedidosDisponibles as $pedido)
                <div class="order-card">
                    <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>{{ $pedido->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Recoger en:</strong> {{ $pedido->restaurante->full_name }}</p>
                        <p><strong>Dirección:</strong> {{ $pedido->restaurante->restaurant_address }}</p>
                        <hr>
                        <p><strong>Entregar a:</strong> {{ $pedido->cliente->full_name }}</p>
                        <p><strong>Dirección:</strong> {{ $pedido->direccion_entrega }}</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <div class="order-actions">
                            <button class="btn btn-primary btn-sm btn-aceptar-pedido" data-id="{{ $pedido->id }}">Aceptar pedido</button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No hay pedidos disponibles.</p>
            @endforelse
        </div>
    </div>

    <div class="order-column">
        <div class="order-column-header">
            <h2>Mis entregas asignadas</h2>
            <span class="order-count">{{ $pedidosPorRecoger->count() }}</span>
        </div>
        <div class="order-cards-container">
            @forelse($pedidosPorRecoger as $pedido)
                <div class="order-card">
                    <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>Aceptado {{ $pedido->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Recoger en:</strong> {{ $pedido->restaurante->full_name }}</p>
                        <p><strong>Dirección:</strong> {{ $pedido->restaurante->restaurant_address }}</p>
                         <hr>
                        <p><strong>Entregar a:</strong> {{ $pedido->cliente->full_name }}</p>
                        <p><strong>Dirección:</strong> {{ $pedido->direccion_entrega }}</p>
                    </div>
                    <div class="order-card-footer">
                         <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <div class="order-actions">
                            <button class="btn btn-info btn-sm btn-marcar-recogido" data-id="{{ $pedido->id }}">Marcar como recogido</button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No tienes pedidos aceptados.</p>
            @endforelse
        </div>
    </div>

    <div class="order-column">
        <div class="order-column-header">
            <h2>Recogidos</h2>
            <span class="order-count">{{ $pedidosRecogidos->count() }}</span>
        </div>
        <div class="order-cards-container">
            @forelse($pedidosRecogidos as $pedido)
                <div class="order-card">
                    <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>Recogido {{ $pedido->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Entregar a:</strong> {{ $pedido->cliente->full_name }}</p>
                        <p><strong>Dirección:</strong> {{ $pedido->direccion_entrega }}</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <div class="order-actions">
                            <button class="btn btn-success btn-sm btn-marcar-entregado" data-id="{{ $pedido->id }}">Marcar como entregado</button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No tienes pedidos recogidos.</p>
            @endforelse
        </div>
    </div>

    <div class="order-column">
        <div class="order-column-header">
            <h2>Entregados</h2>
            <span class="order-count">{{ $pedidosEntregados->count() }}</span>
        </div>
        <div class="order-cards-container">
            @forelse($pedidosEntregados as $pedido)
                <div class="order-card">
                     <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>Entregado {{ $pedido->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                         <p><strong>Cliente:</strong> {{ $pedido->cliente->full_name }}</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <div class="order-actions">
                            <small>¡Completado!</small>
                        </div>
                    </div>
                </div>
            @empty
                <p>No has entregado pedidos hoy.</p>
            @endforelse
        </div>
    </div>
</div>