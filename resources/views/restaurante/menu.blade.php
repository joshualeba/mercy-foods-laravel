<section id="menu" class="dashboard-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mi Menú</h1>
        <a href="{{ route('platillos.create') }}" class="btn btn-primary">Añadir Platillo</a>
    </div>

    <div class="content-placeholder">
        @if($platillos->isEmpty())
            <div class="text-center p-5">
                <p>Aún no tienes platillos en tu menú.</p>
                <a href="{{ route('platillos.create') }}" class="btn btn-primary mt-3">¡Crea tu primer platillo!</a>
            </div>
        @else
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Disponibilidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($platillos as $platillo)
                    <tr>
                        <td>{{ $platillo->nombre }}</td>
                        <td>{{ Str::limit($platillo->descripcion, 50) }}</td>
                        <td>${{ number_format($platillo->precio, 2) }}</td>
                        <td>{{ $platillo->disponible ? 'Sí' : 'No' }}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">Editar</a>
                            <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</section>