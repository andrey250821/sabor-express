@forelse($clientes as $cliente)
<tr>
    <td>
        <span class="cliente-id">
            #{{ $cliente->id }}
        </span>
    </td>

    <td>
        <div class="cliente-info">
            <div class="cliente-avatar">
                @if($cliente->foto_perfil_url)
                    <img
                        src="{{ $cliente->foto_perfil_url }}"
                        alt="Foto de {{ $cliente->name }}">
                @else
                    {{ strtoupper(substr($cliente->name, 0, 1)) }}
                @endif
            </div>

            <div class="cliente-nombre">
                {{ $cliente->name }}
            </div>
        </div>
    </td>

    <td>
        <span class="cliente-email">
            {{ $cliente->email }}
        </span>
    </td>

    <td>
        <span class="cliente-telefono">
            {{ $cliente->telefono ?? 'No registrado' }}
        </span>
    </td>

    <td>
        <span class="cliente-pedidos">
            {{ $cliente->pedidos_count }}
        </span>
    </td>

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

    <td>
        <div class="cliente-acciones">
            <a
                href="{{ route('admin.clientes.show', $cliente->id) }}"
                class="cliente-btn cliente-btn-ver">
                <i class="bi bi-eye-fill me-1"></i>
                Ver
            </a>

            @if($cliente->estado === 'inactivo')
                <form
                    action="{{ route('admin.clientes.activar', $cliente->id) }}"
                    method="POST">
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="cliente-btn cliente-btn-activar">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Activar
                    </button>
                </form>
            @else
                <form
                    action="{{ route('admin.clientes.desactivar', $cliente->id) }}"
                    method="POST">
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="cliente-btn cliente-btn-desactivar">
                        <i class="bi bi-pause-circle-fill me-1"></i>
                        Desactivar
                    </button>
                </form>
            @endif

            <form
                action="{{ route('admin.clientes.destroy', $cliente->id) }}"
                method="POST"
                onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">
                @csrf
                @method('DELETE')

                <button
                    type="submit"
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
    <td colspan="7" class="clientes-empty">
        <i class="bi bi-people clientes-empty-icon"></i>
        No se encontraron clientes con ese email.
    </td>
</tr>
@endforelse
