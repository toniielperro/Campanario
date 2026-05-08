@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
        <div>
            <h1 class="pilar-title">Secuencias</h1>
            <p class="pilar-subtitle">Conjuntos de sonidos para repiques y campanazos complejos.</p>
        </div>
        <a href="{{ route('sequences.create') }}" class="btn btn-gold mt-3 mt-lg-0">
            <i class="fa fa-plus mr-2"></i>Añadir secuencia
        </a>
    </div>

    <section class="pilar-card {{ request('activo') === '0' ? 'pilar-trash' : '' }}">
        <form method="GET" id="filtersForm" class="pilar-toolbar">
            <div class="pilar-search">
                <i class="fa fa-search"></i>
                <input name="search" value="{{ request('search') ?? '' }}" class="form-control" id="sequenceSearch" placeholder="Buscar secuencias..." />
            </div>
            <div class="d-flex gap-2">
                <input type="hidden" name="activo" id="activoInput" value="{{ $activo ?? request('activo', '1') }}" />
                <button type="button" id="toggleTrashButton" class="btn btn-ghost">{{ ($activo ?? request('activo', '1')) === '0' ? 'Ver activos' : 'Ver papelera' }}</button>
            </div>
        </form>
        @if(($activo ?? request('activo')) === '0')
            <div class="pilar-card-soft pilar-trash mb-4">
                <strong>Papelera de secuencias</strong>
                <div class="pilar-muted mt-1">Estos registros están inactivos. Puedes restaurarlos o eliminarlos definitivamente.</div>
            </div>
        @endif
        <div class="table-responsive pilar-table-wrap">
            <table class="pilar-table" id="sequenceTable">
                <thead>
                    <tr><th>Nombre</th><th>Items</th><th class="text-right">Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse($sequences as $seq)
                        <tr>
                            <td class="font-weight-bold">{{ $seq->nombre }}</td>
                            <td><span class="pilar-badge">{{ $seq->items_count }} sonidos</span></td>
                            <td class="text-right">
                                @if(($activo ?? request('activo', '1')) === '0')
                                        @if($seq->items_count > 0)
                                            <button type="button" class="pilar-icon-btn play-seq" data-id="{{ $seq->id }}" title="Reproducir"><i class="fa fa-play"></i></button>
                                        @else
                                            <button class="pilar-icon-btn" title="Sin sonidos" disabled><i class="fa fa-play"></i></button>
                                        @endif
                                        <form method="POST" action="{{ route('sequences.restore', $seq->id) }}" class="d-inline">
                                            @csrf
                                            <button class="pilar-icon-btn" title="Restaurar"><i class="fa fa-undo"></i></button>
                                        </form>
                                        <form method="POST" action="{{ route('sequences.forceDelete', $seq->id) }}" class="d-inline pilar-confirm-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button class="pilar-icon-btn" title="Eliminar definitivamente"><i class="fa fa-trash"></i></button>
                                        </form>
                                @else
                                    <button class="pilar-icon-btn play-seq" data-id="{{ $seq->id }}" title="Reproducir"><i class="fa fa-play"></i></button>
                                    <a href="{{ route('sequences.edit', $seq) }}" class="pilar-icon-btn" title="Editar"><i class="fa fa-pen"></i></a>
                                    <form action="{{ route('sequences.destroy', $seq) }}" method="POST" class="d-inline pilar-confirm-trash">
                                        @csrf
                                        @method('DELETE')
                                        <button class="pilar-icon-btn" title="Eliminar"><i class="fa fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center pilar-muted py-4">No hay secuencias para mostrar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-4">
            {{ $sequences->links() }}
        </div>
    </section>
</div>
@endsection

@section('scripts')
@parent
<script>
    (function(){
        // search filter
        document.getElementById('sequenceSearch')?.addEventListener('input', function(){
            const q = this.value.toLowerCase();
            document.querySelectorAll('#sequenceTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        // toggle activo / papelera
        (function(){
            const btn = document.getElementById('toggleTrashButton');
            const input = document.getElementById('activoInput');
            const form = document.getElementById('filtersForm');
            if(btn && input && form){
                btn.addEventListener('click', function(){
                    const current = String(input.value || '1');
                    const next = current === '0' ? '1' : '0';
                    input.value = next;
                    btn.textContent = next === '0' ? 'Ver papelera' : 'Ver activos';
                    form.submit();
                });
            }
        })();

        function waitForEnd(player, maxMs = 60000){
            return new Promise((res)=>{
                let done = false;
                const onEnd = ()=>{ if(done) return; done = true; player.removeEventListener('ended', onEnd); clearTimeout(timer); res(); };
                player.addEventListener('ended', onEnd);
                const timer = setTimeout(()=>{ if(!done){ done = true; player.removeEventListener('ended', onEnd); res(); } }, maxMs);
            });
        }
        const player = document.createElement('audio'); player.preload='auto'; document.body.appendChild(player);
        document.querySelectorAll('.play-seq').forEach(btn=>{
            btn.addEventListener('click', async ()=>{
                const id = btn.getAttribute('data-id');
                try{
                    const res = await fetch('/sequences/' + id + '/preview');
                    if(!res.ok) throw new Error('no');
                    const data = await res.json();
                    for(const it of data.items){
                        if(!it.bell_sound || !it.bell_sound.ruta_archivo) continue;
                        const src = it.bell_sound.ruta_archivo.startsWith('/') ? window.location.origin + it.bell_sound.ruta_archivo : it.bell_sound.ruta_archivo;
                        player.src = src;
                        await player.play().catch(()=>{});
                        await waitForEnd(player, 60000);
                        await new Promise(r=>setTimeout(r, (it.interval_seconds||1)*1000));
                    }
                }catch(e){
                    Swal.fire({title:'No se pudo reproducir', icon:'warning', confirmButtonText:'Entendido'});
                }
            });
        });
    })();
</script>
@endsection
