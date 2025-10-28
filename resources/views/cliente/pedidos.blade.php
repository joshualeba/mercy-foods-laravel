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
        <div class="order-cards-container scrollable-orders">
            @forelse($pedidosEnPreparacion as $pedido)
                <div class="accordion-item">
                    <button class="accordion-header">
                        <span>Pedido #{{ $pedido->id }} - {{ $pedido->restaurante->full_name }}</span>
                        <span class="accordion-icon">+</span>
                    </button>
                    <div class="accordion-content">
                        <div class="order-card-body">
                            <p><strong>Platillos:</strong></p>
                            <ul>
                                @foreach($pedido->detalles as $detalle)
                                    <li>{{ $detalle->cantidad }} x {{ $detalle->platillo->nombre }}</li>
                                @endforeach
                            </ul>
                            <hr>
                            <p><strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</p>
                            @if ($pedido->estado == 'pendiente')
                                <button class="btn btn-danger btn-sm mt-2 cancel-order-btn" data-id="{{ $pedido->id }}">
                                    Cancelar pedido
                                </button>
                            @endif
                        </div>
                        <div class="order-card-footer">
                            <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                            <small>Tu pedido está en cocina.</small>
                        </div>
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
        <div class="order-cards-container scrollable-orders">
            @forelse($pedidosEnCamino as $pedido)
                <div class="accordion-item">
                    <button class="accordion-header">
                        <span>Pedido #{{ $pedido->id }} - {{ $pedido->restaurante->full_name }}</span>
                        <span class="accordion-icon">+</span>
                    </button>
                    <div class="accordion-content">
                        <div class="order-card-body">
                            <p><strong>Platillos:</strong></p>
                            <ul>
                                @foreach($pedido->detalles as $detalle)
                                    <li>{{ $detalle->cantidad }} x {{ $detalle->platillo->nombre }}</li>
                                @endforeach
                            </ul>
                            <hr>
                            <p><strong>Repartidor:</strong> {{ $pedido->repartidor->full_name ?? 'Asignando...' }}</p>
                        </div>
                        <div class="order-card-footer">
                            <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                            <small>¡Tu pedido va en camino!</small>
                        </div>
                    </div>
                </div>
            @empty
                <p>No tienes pedidos en camino.</p>
            @endforelse
        </div>
    </div>

    {{-- Columna: Recibidos --}}
    <div class="order-column">
        <div class="order-column-header">
            <h2>Recibidos</h2>
            <span class="order-count">{{ $pedidosEntregados->count() }}</span>
        </div>
        <div class="order-cards-container scrollable-orders">
            @forelse($pedidosEntregados as $pedido)
                <div class="accordion-item">
                    <button class="accordion-header">
                        <span>Pedido #{{ $pedido->id }} - {{ $pedido->restaurante->full_name }}</span>
                        <span class="accordion-icon">+</span>
                    </button>
                    <div class="accordion-content">
                        <div class="order-card-body">
                            <p><strong>Platillos:</strong></p>
                            <ul>
                                @foreach($pedido->detalles as $detalle)
                                    <li>{{ $detalle->cantidad }} x {{ $detalle->platillo->nombre }}</li>
                                @endforeach
                            </ul>
                            <hr>
                            <p><strong>Entregado por:</strong> {{ $pedido->repartidor->full_name ?? 'N/A' }}</p>
                        </div>
                        <div class="order-card-footer">
                            <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                            <small>Entregado {{ $pedido->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <p>No tienes pedidos recientes.</p>
            @endforelse
        </div>
    </div>
</div>