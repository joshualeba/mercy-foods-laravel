<div class="pedidos-header">
    <h1>Mis pedidos</h1>
</div>

{{-- Mensajes de alerta --}}
@if(session('success'))
    <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
                            <p><strong>Estado:</strong> El restaurante está preparando tu orden.</p>
                        </div>
                        <div class="order-card-footer">
                            <span class="order-total">${{ number_format($pedido->total, 2) }}</span>
                            <small>Actualizado {{ $pedido->updated_at->diffForHumans() }}</small>
                        </div>
                        <div style="margin-top: 10px;">
                            <button type="button" 
                                    class="cancel-order-btn" 
                                    data-id="{{ $pedido->id }}"
                                    style="background-color: #dc3545; color: #fff; border: none; padding: 8px 16px; border-radius: 5px; font-weight: 500; cursor: pointer; width: 100%; transition: background-color 0.3s ease;"
                                    onmouseover="this.style.backgroundColor='#c82333'"
                                    onmouseout="this.style.backgroundColor='#dc3545'">
                                Cancelar pedido
                            </button>
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
                        <div style="margin-top: 10px;">
                            <button type="button" 
                                    class="cancel-order-btn" 
                                    data-id="{{ $pedido->id }}"
                                    style="background-color: #dc3545; color: #fff; border: none; padding: 8px 16px; border-radius: 5px; font-weight: 500; cursor: pointer; width: 100%; transition: background-color 0.3s ease;"
                                    onmouseover="this.style.backgroundColor='#c82333'"
                                    onmouseout="this.style.backgroundColor='#dc3545'">
                                Cancelar pedido
                            </button>
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
                            
                            @if(!$pedido->review)
                                {{-- Botón para abrir el modal con estilos inline para asegurar el color rojo --}}
                                <button type="button" 
                                        style="background-color: #e74c3c; color: #fff; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; width: 100%; margin-top: 10px;"
                                        data-pedido-id="{{ $pedido->id }}"
                                        data-tiene-repartidor="{{ $pedido->repartidor_id ? '1' : '0' }}"
                                        onclick="abrirModalResena({{ $pedido->id }})"
                                        onmouseover="this.style.backgroundColor='#c0392b'"
                                        onmouseout="this.style.backgroundColor='#e74c3c'">
                                    Calificar Pedido
                                </button>
                            @else
                                <div class="mt-2 text-success small">
                                    <i class="fas fa-check-circle"></i> ¡Gracias por tu calificación!
                                </div>
                            @endif
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

{{-- Modal de Reseña --}}
<div class="confirmation-modal-overlay" id="modalResena">
    <div class="modal-box" style="max-width: 500px; text-align: left; padding: 25px;">
        <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 20px;">Calificar Pedido</h2>
        
        <form action="{{ route('reviews.store') }}" method="POST" id="reviewForm">
            @csrf
            <input type="hidden" name="pedido_id" id="input_pedido_id">
            <input type="hidden" name="tiene_repartidor" id="input_tiene_repartidor" value="1">
            
            <div class="review-section">
                <label class="review-label">Comida (Restaurante) <span style="color: red;">*</span></label>
                <div class="star-rating">
                    <input type="radio" name="rating_restaurante" value="5" id="rest-5" required><label for="rest-5">★</label>
                    <input type="radio" name="rating_restaurante" value="4" id="rest-4"><label for="rest-4">★</label>
                    <input type="radio" name="rating_restaurante" value="3" id="rest-3"><label for="rest-3">★</label>
                    <input type="radio" name="rating_restaurante" value="2" id="rest-2"><label for="rest-2">★</label>
                    <input type="radio" name="rating_restaurante" value="1" id="rest-1"><label for="rest-1">★</label>
                </div>
                <textarea name="comentario_restaurante" class="custom-textarea" placeholder="¿Qué te pareció el sabor? (Opcional)" rows="3"></textarea>
            </div>

            <div class="review-section mt-3" id="repartidorSection">
                <label class="review-label">Entrega (Repartidor) <span style="color: red;" id="repartidorRequired">*</span></label>
                <div class="star-rating">
                    <input type="radio" name="rating_repartidor" value="5" id="rep-5" required><label for="rep-5">★</label>
                    <input type="radio" name="rating_repartidor" value="4" id="rep-4"><label for="rep-4">★</label>
                    <input type="radio" name="rating_repartidor" value="3" id="rep-3"><label for="rep-3">★</label>
                    <input type="radio" name="rating_repartidor" value="2" id="rep-2"><label for="rep-2">★</label>
                    <input type="radio" name="rating_repartidor" value="1" id="rep-1"><label for="rep-1">★</label>
                </div>
                <textarea name="comentario_repartidor" class="custom-textarea" placeholder="¿Llegó a tiempo? ¿Fue amable? (Opcional)" rows="3"></textarea>
            </div>

            <div class="modal-buttons" style="margin-top: 20px;">
                <button type="button" class="btn-cancel" onclick="cerrarModalResena()">Cancelar</button>
                <button type="submit" class="btn-confirm">Enviar Calificación</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalResena(idPedido) {
        // Resetear el formulario
        document.getElementById('reviewForm').reset();
        
        // Asignar el ID al formulario
        document.getElementById('input_pedido_id').value = idPedido;
        
        // Buscar el pedido en la página para verificar si tiene repartidor
        const pedidoElement = document.querySelector(`[data-pedido-id="${idPedido}"]`);
        const tieneRepartidor = pedidoElement ? pedidoElement.dataset.tieneRepartidor === '1' : true;
        
        // Actualizar el campo oculto
        document.getElementById('input_tiene_repartidor').value = tieneRepartidor ? '1' : '0';
        
        // Ajustar la sección de repartidor según corresponda
        const repartidorSection = document.getElementById('repartidorSection');
        const repartidorInputs = document.querySelectorAll('input[name="rating_repartidor"]');
        
        if (!tieneRepartidor) {
            // Si no hay repartidor, hacer la calificación opcional
            repartidorInputs.forEach(input => input.required = false);
            document.getElementById('repartidorRequired').style.display = 'none';
        } else {
            // Si hay repartidor, hacer la calificación requerida
            repartidorInputs.forEach(input => input.required = true);
            document.getElementById('repartidorRequired').style.display = 'inline';
        }
        
        // Mostrar el modal forzando el display flex y luego la clase active
        const modal = document.getElementById('modalResena');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('active');
        }, 10);
    }

    function cerrarModalResena() {
        const modal = document.getElementById('modalResena');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            // Resetear el formulario al cerrar
            document.getElementById('reviewForm').reset();
        }, 300); // Espera la transición
    }

    // Cerrar al hacer clic fuera
    document.getElementById('modalResena').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalResena();
        }
    });
</script>