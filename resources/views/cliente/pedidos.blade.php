<div class="pedidos-header">
    <h1>Mis pedidos</h1>
</div>

<div class="order-board">
    {{-- Columna: En preparación --}}
    <div class="order-column">
        <div class="order-column-header">
            <h2>En preparación</h2>
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
                        <p><strong>Restaurante:</strong> {{ $pedido->restaurante->full_name }}</p>
                        <p><strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <small>Tu pedido está siendo preparado...</small>
                    </div>
                </div>
            @empty
                <p>No tienes pedidos en preparación.</p>
            @endforelse
        </div>
    </div>

    {{-- Columna: En camino --}}
    <div class="order-column">
        <div class="order-column-header">
            <h2>En camino</h2>
             <span class="order-count">{{ $pedidosEnCamino->count() }}</span>
        </div>
        <div class="order-cards-container">
            @forelse($pedidosEnCamino as $pedido)
                <div class="order-card" data-id="{{ $pedido->id }}">
                     <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>{{ $pedido->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Restaurante:</strong> {{ $pedido->restaurante->full_name }}</p>
                        <p><strong>Repartidor:</strong> ¡Ya va en camino!</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <small>Sigue tu pedido en el mapa.</small>
                    </div>
                </div>
            @empty
                <p>Ningún pedido en camino.</p>
            @endforelse
        </div>
    </div>

    {{-- Columna: Entregados --}}
    <div class="order-column">
        <div class="order-column-header">
            <h2>Entregados</h2>
            <span class="order-count">{{ $pedidosEntregados->count() }}</span>
        </div>
        <div class="order-cards-container">
             @forelse($pedidosEntregados as $pedido)
                <div class="order-card" data-id="{{ $pedido->id }}">
                    <div class="order-card-header">
                        <h3>Pedido #{{ $pedido->id }}</h3>
                        <span>Entregado {{ $pedido->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="order-card-body">
                        <p><strong>Restaurante:</strong> {{ $pedido->restaurante->full_name }}</p>
                    </div>
                    <div class="order-card-footer">
                        <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                        <small>¡Que lo disfrutes!</small>
                    </div>
                </div>
            @empty
                <p>Aún no tienes pedidos entregados.</p>
            @endforelse
        </div>
    </div>
</div>