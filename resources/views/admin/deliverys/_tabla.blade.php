@forelse($deliverys as $delivery)
<tr>
    <td>
        <span class="delivery-id">
            #{{ $delivery->id }}
        </span>
    </td>

    <td>
        <div class="delivery-person">
            <div class="delivery-avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div>
                <strong>
                    {{ $delivery->name }}
                </strong>

                <small>
                    Delivery
                </small>
            </div>
        </div>
    </td>

    <td>
        <div class="delivery-contact">
            <span>
                <i class="bi bi-envelope"></i>
                {{ $delivery->email }}
            </span>

            @if($delivery->telefono)
                <span>
                    <i class="bi bi-telephone"></i>
                    {{ $delivery->telefono }}
                </span>
            @endif
        </div>
    </td>

    <td>
        <span class="delivery-asignaciones">
            <i class="bi bi-box-seam"></i>
            {{ $delivery->asignaciones_delivery_count }}
        </span>
    </td>

    <td>
        @if($delivery->estado === 'activo')
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
                href="{{ route('admin.deliverys.edit', $delivery->id) }}"
                class="btn-delivery editar"
                title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>

            @if($delivery->estado === 'activo')
                <form
                    action="{{ route('admin.deliverys.destroy', $delivery->id) }}"
                    method="POST">
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn-delivery eliminar"
                        title="Desactivar"
                        onclick="return confirm('¿Deseas desactivar este Delivery?')">
                        <i class="bi bi-person-dash"></i>
                    </button>
                </form>
            @else
                <form
                    action="{{ route('admin.deliverys.activar', $delivery->id) }}"
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
    <td colspan="6">
        <div class="deliverys-empty">
            <i class="bi bi-bicycle"></i>

            <h5>
                No se encontraron Delivery con ese email
            </h5>

            <p>
                Prueba con otra parte de la dirección de correo.
            </p>
        </div>
    </td>
</tr>
@endforelse
