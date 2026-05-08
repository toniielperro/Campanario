<div>
    <div class="pilar-toolbar">
        <div class="pilar-search">
            <i class="fa fa-search"></i>
            <input wire:model.live="search" class="form-control" placeholder="Buscar por motivo o fecha..." />
        </div>
        <button wire:click="toggleTrashed" class="btn btn-ghost">{{ $showTrashed ? 'Ver activos' : 'Ver papelera' }}</button>
    </div>

    @if($showTrashed)
        <div class="pilar-card-soft pilar-trash mb-4">
            <strong>Papelera de excepciones</strong>
            <div class="pilar-muted mt-1">Registros inactivos disponibles para restaurar.</div>
        </div>
    @endif

    <div class="row">
        @forelse($items as $item)
            <div class="col-md-6 col-xl-4 mb-3">
                <article class="pilar-mini-card {{ $showTrashed ? 'pilar-trash' : '' }} h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="pilar-section-title">{{ $item->nombre ?? 'Sin motivo' }}</h3>
                            <div class="pilar-muted">{{ substr($item->start_time,0,5) }} - {{ substr($item->end_time,0,5) }}</div>
                        </div>
                        <i class="fa fa-calendar-times text-gold"></i>
                    </div>
                    @if(!empty($item->fechas_especificas))
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            @foreach($item->fechas_especificas as $fecha)
                                <span class="pilar-badge">{{ $fecha }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        @if(method_exists($item,'trashed') && $item->trashed())
                            <form method="POST" action="{{ route('exceptions.restore',$item->id) }}">@csrf<button class="btn btn-restore btn-sm">Restaurar</button></form>
                            <form method="POST" action="{{ route('exceptions.forceDelete',$item->id) }}" class="pilar-confirm-delete">@csrf @method('DELETE')<button class="btn btn-wine btn-sm">Eliminar</button></form>
                        @else
                            <a href="{{ route('exceptions.edit',$item->id) }}" class="pilar-icon-btn" title="Editar"><i class="fa fa-pen"></i></a>
                            <form method="POST" action="{{ route('exceptions.destroy',$item->id) }}" class="pilar-confirm-trash">@csrf @method('DELETE')<button class="pilar-icon-btn" title="Enviar a papelera"><i class="fa fa-trash"></i></button></form>
                        @endif
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="pilar-card-soft text-center pilar-muted">No hay excepciones para mostrar.</div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $items->links() }}
    </div>
</div>
