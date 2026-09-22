@forelse($cocineros as $cocinero)
<tr>
    <td>
        <span class="delivery-id">
            #{{ $cocinero->id }}
        </span>
    </td>

    <td>
        <div class="delivery-person">
            <div class="delivery-avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div>
                <strong>
                    {{ $cocinero->name }}
                </strong>

                <small>
                    Cocinero
                </small>
            </div>
        </div>
    </td>

    <td>
        <div class="delivery-contact">
            <span>
                <i class="bi bi-envelope"></i>
                {{ $cocinero->email }}
            </span>

            @if($cocinero->telefono)
                <span>
                    <i class="bi bi-telephone"></i>
                    {{ $cocinero->telefono }}
                </span>
            @endif
        </div>
    </td>

    <td>
        @if($cocinero->estado === 'activo')
            <span class="delivery-estado activo">
                <i class="bi bi-check-circle-fill"></i>
                Activo
            </span>
        @else
            <span class="delivery-estado inactivo">
                <i class="bi bi-x-circle-fill"></i>
                Inactivo
            </span>
        @endif
    </td>

    <td>
        <div class="delivery-actions">
            <a
                href="{{ route('admin.cocineros.edit', $cocinero->id) }}"
                class="btn-delivery editar"
                title="Editar cocinero">
                <i class="bi bi-pencil-square"></i>
            </a>

            @if($cocinero->estado === 'activo')
                <form
                    action="{{ route('admin.cocineros.destroy', $cocinero->id) }}"
                    method="POST">
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn-delivery eliminar"
                        title="Desactivar"
                        onclick="return confirm('¿Deseas desactivar este Cocinero?')">
                        <i class="bi bi-person-dash"></i>
                    </button>
                </form>
            @else
                <form
                    action="{{ route('admin.cocineros.activar', $cocinero->id) }}"
                    method="POST">
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="btn-delivery activar"
                        title="Activar">
                        <i class="bi bi-person-check"></i>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5">
        <div class="deliverys-empty">
            <i class="bi bi-person-badge"></i>

            <h5>
                No se encontraron cocineros con ese email
            </h5>

            <p>
                Prueba con otra parte de la dirección de correo.
            </p>
        </div>
    </td>
</tr>
@endforelse
