<table class="clientes-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Nombre</th>

                        <th>Email</th>

                        <th>Teléfono</th>

                        <th>Pedidos</th>

                        <th>Estado</th>

                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($clientes as $cliente)

                    <tr>

                        {{-- ID --}}
                        <td>

                            <span class="cliente-id">
                                #{{ $cliente->id }}
                            </span>

                        </td>


                        {{-- NOMBRE --}}
                        <td>

                            <div class="cliente-info">

                                <div class="cliente-avatar">

                                    {{ strtoupper(substr($cliente->name, 0, 1)) }}

                                </div>

                                <div class="cliente-nombre">

                                    {{ $cliente->name }}

                                </div>

                            </div>

                        </td>


                        {{-- EMAIL --}}
                        <td>

                            <span class="cliente-email">

                                {{ $cliente->email }}

                            </span>

                        </td>


                        {{-- TELEFONO --}}
                        <td>

                            <span class="cliente-telefono">

                                {{ $cliente->telefono ?? 'No registrado' }}

                            </span>

                        </td>


                        {{-- PEDIDOS --}}
                        <td>

                            <span class="cliente-pedidos">

                                {{ $cliente->pedidos_count }}

                            </span>

                        </td>


                        {{-- ESTADO --}}
                        <td>

                            @if($cliente->estado === 'activo')

                            <span class="cliente-estado activo">
                                Activo
                            </span>

                            @else

                            <span class="cliente-estado inactivo">
                                Inactivo
                            </span>

                            @endif

                        </td>


                        {{-- ACCIONES --}}
                        <td>

                            <div class="cliente-acciones">

                                {{-- VER --}}
                                <a href="{{ route('admin.clientes.show', $cliente->id) }}"
                                    class="cliente-btn cliente-btn-ver">

                                    <i class="bi bi-eye-fill me-1"></i>
                                    Ver

                                </a>


                                {{-- ACTIVAR --}}
                                @if($cliente->estado === 'inactivo')

                                <form action="{{ route('admin.clientes.activar', $cliente->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="cliente-btn cliente-btn-activar">

                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Activar

                                    </button>

                                </form>

                                @else

                                {{-- DESACTIVAR --}}
                                <form action="{{ route('admin.clientes.desactivar', $cliente->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="cliente-btn cliente-btn-desactivar">

                                        <i class="bi bi-pause-circle-fill me-1"></i>
                                        Desactivar

                                    </button>

                                </form>

                                @endif


                                {{-- ELIMINAR --}}
                                <form action="{{ route('admin.clientes.destroy', $cliente->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="cliente-btn cliente-btn-eliminar">

                                        <i class="bi bi-trash-fill me-1"></i>
                                        Eliminar

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7"
                            class="clientes-empty">

                            <i class="bi bi-people clientes-empty-icon"></i>

                            No hay clientes registrados.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>