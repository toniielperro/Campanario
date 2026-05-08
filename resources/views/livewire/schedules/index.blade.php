<div>
    <div class="mb-3 d-flex justify-content-between">
        <div class="d-flex gap-2">
            <input wire:model.live="search" class="form-control w-50" placeholder="Buscar programaciones..." />
            <button wire:click="toggleTrashed" class="btn btn-outline-secondary">{{ $showTrashed ? 'Ver Activos' : 'Ver Papelera' }}</button>
        </div>
        <a href="{{ route('schedules.create') }}" class="btn btn-primary">Añadir</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr><th>Hora</th><th>Días</th><th>Sonido</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->hora }}</td>
                    <td>{{ implode(', ', $item->dias_semana ?? []) }}</td>
                    <td>{{ $item->bellSound->nombre ?? '' }}</td>
                    <td>
                        @if($showTrashed)
                            <button wire:click="restore({{ $item->id }})" class="btn btn-sm btn-success">Restaurar</button>
                            <button wire:click="forceDelete({{ $item->id }})" class="btn btn-sm btn-danger">Eliminar Permanentemente</button>
                        @else
                            <a href="{{ route('schedules.edit', $item->id) }}" class="btn btn-sm btn-secondary">Editar</a>
                            <button wire:click="delete({{ $item->id }})" class="btn btn-sm btn-danger">Papelera</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $items->links() }}
</div>
