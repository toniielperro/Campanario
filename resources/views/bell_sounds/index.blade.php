@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
        <div>
            <h1 class="pilar-title">Sonidos</h1>
            <p class="pilar-subtitle">Biblioteca de audios disponibles para campanazos y secuencias.</p>
        </div>
        <a href="{{ route('bell_sounds.create') }}" class="btn btn-gold mt-3 mt-lg-0">
            <i class="fa fa-plus mr-2"></i>Añadir sonido
        </a>
    </div>

    <section class="pilar-card {{ request('activo') === '0' ? 'pilar-trash' : '' }}">
        <form method="GET" class="pilar-toolbar" id="filtersForm">
            <div class="pilar-search mb-3 mb-md-0">
                <i class="fa fa-search"></i>
                <input name="search" value="{{ $search ?? request('search') }}" class="form-control" placeholder="Buscar sonidos..." oninput="window.pilarSearchSubmit(this)" />
            </div>
            <div class="d-flex gap-2">
                <input type="hidden" name="activo" id="activoInput" value="{{ request('activo', '1') }}" />
                <button type="button" id="toggleTrashButton" class="btn btn-ghost">{{ request('activo', '1') === '0' ? 'Ver activos' : 'Ver papelera' }}</button>
            </div>
        </form>

        @if(request('activo') === '0')
            <div class="pilar-card-soft pilar-trash mb-4">
                <strong>Papelera de sonidos</strong>
                <div class="pilar-muted mt-1">Estos registros están inactivos. Puedes restaurarlos o eliminarlos definitivamente.</div>
            </div>
        @endif

        <div class="table-responsive pilar-table-wrap">
            <table class="pilar-table">
                <thead>
                    <tr><th>Nombre</th><th>Archivo</th><th class="text-right">Acciones</th></tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="font-weight-bold">{{ $item->nombre }}</td>
                        <td class="pilar-muted">{{ $item->ruta_archivo }}</td>
                        <td class="text-right">
                            @if($item->ruta_archivo)
                                <button type="button" class="pilar-icon-btn btn-play-sound" data-src="{{ $item->ruta_archivo }}" title="Reproducir"><i class="fa fa-play"></i></button>
                            @endif
                            @if(method_exists($item, 'trashed') && $item->trashed())
                                <form method="POST" action="{{ route('bell_sounds.restore', $item->id) }}" class="d-inline">
                                    @csrf
                                    <button class="pilar-icon-btn" title="Restaurar"><i class="fa fa-undo"></i></button>
                                </form>
                                <form method="POST" action="{{ route('bell_sounds.forceDelete', $item->id) }}" class="d-inline pilar-confirm-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button class="pilar-icon-btn" title="Eliminar definitivamente"><i class="fa fa-trash"></i></button>
                                </form>
                            @else
                                <a href="{{ route('bell_sounds.edit', $item->id) }}" class="pilar-icon-btn" title="Editar"><i class="fa fa-pen"></i></a>
                                <form method="POST" action="{{ route('bell_sounds.destroy', $item->id) }}" class="d-inline pilar-confirm-trash">@csrf @method('DELETE')<button class="pilar-icon-btn" title="Enviar a papelera"><i class="fa fa-trash"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center pilar-muted py-4">No hay sonidos para mostrar.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $items->appends(request()->all())->links() }}
        </div>
    </section>
</div>
@endsection

@section('scripts')
@parent
<script>
    window.pilarSearchSubmit = window.pilarSearchSubmit || function(input){
        clearTimeout(input._timer);
        input._timer = setTimeout(() => input.form.submit(), 350);
    };
    // Toggle activo (ver papelera / ver activos) like exceptions toolbar
    (function(){
        const btn = document.getElementById('toggleTrashButton');
        const input = document.getElementById('activoInput');
        const form = document.getElementById('filtersForm');
        if(btn && input && form){
            btn.addEventListener('click', function(){
                const current = String(input.value || '1');
                const next = current === '0' ? '1' : '0';
                input.value = next;
                // update button text to match exceptions behaviour
                btn.textContent = next === '0' ? 'Ver papelera' : 'Ver activos';
                form.submit();
            });
        }
    })();
    (function(){
        let player = document.getElementById('preview-audio');
        if(!player){ player = document.createElement('audio'); player.id = 'preview-audio'; player.preload = 'auto'; document.body.appendChild(player); }
        document.addEventListener('click', function(e){
            const btn = e.target.closest && e.target.closest('.btn-play-sound');
            if(!btn) return;
            let src = btn.getAttribute('data-src');
            if(!src) return;
            if(src.startsWith('/')) src = window.location.origin + src;
            player.pause();
            player.currentTime = 0;
            player.src = src;
            player.play().catch(() => Swal.fire({title:'Reproducción bloqueada', text:'Haz clic nuevamente para permitir el audio.', icon:'warning'}));
        });
    })();
</script>
@endsection
