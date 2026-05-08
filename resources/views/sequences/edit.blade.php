@extends('layouts.adminlte')

@section('content')
    <div class="pilar-page">
        <section class="pilar-card">
        <div class="mb-4">
            <h3 class="card-title">Editar Secuencia</h3>
        </div>
        <div>
            <form method="POST" action="{{ route('sequences.update', $sequence) }}" class="pilar-form">
                @csrf
                @method('POST')
                <div class="form-group">
                    <label>Nombre</label>
                    <input name="nombre" class="form-control" required value="{{ $sequence->nombre }}">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control">{{ $sequence->descripcion }}</textarea>
                </div>
                <hr>
                <h5>Items</h5>
                <p class="text-muted">Explicación: <strong>Intervalo (s)</strong> = segundos a esperar después de reproducir este item antes del siguiente. <strong>Repeticiones</strong> = cuántas veces suena este audio consecutivamente. <strong>Orden</strong> = posición en la secuencia.</p>
                <div class="form-row font-weight-bold mb-2">
                    <div class="col-6">Sonido</div>
                    <div class="col-3">Intervalo (s)</div>
                    <div class="col-2">Orden</div>
                    <div class="col-1">Acción</div>
                </div>
                <div id="itemsContainer"></div>
                <div class="mt-2">
                    <button type="button" id="addItem" class="btn btn-sm btn-outline-primary">Agregar item</button>
                    <button type="button" id="previewSequence" class="btn btn-sm btn-outline-secondary">Vista previa</button>
                </div>
                <hr>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('sequences.index') }}" class="btn btn-ghost">Cancelar</a>
                    <button class="btn btn-gold"><i class="fa fa-save mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
        </section>
    </div>

    <script>
        const sounds = @json($sounds->map(function($s){ return ['id'=>$s->id,'nombre'=>$s->nombre,'ruta_archivo'=>$s->ruta_archivo]; }));
        const existing = @json($sequence->items->map(function($it){ return ['bell_sound_id'=>$it->bell_sound_id,'interval_seconds'=>$it->interval_seconds,'orden'=>$it->orden]; }));
        const container = document.getElementById('itemsContainer');

        function refreshOrderInputs(){
            Array.from(container.children).forEach((row, i)=>{
                const orden = row.querySelector('input[name$="[orden]"]');
                if(orden) orden.value = i + 1; // enforce 1-based positive order
            });
        }

        function makeRow(idx, item){
            const row = document.createElement('div'); row.className='form-row mb-2'; row.draggable = true;
            row.innerHTML = `
                <div class="col-6">
                    <select name="items[${idx}][bell_sound_id]" class="form-control">
                        ${sounds.map(s=>`<option value="${s.id}" ${s.id==item.bell_sound_id? 'selected':''}>${s.nombre}</option>`).join('')}
                    </select>
                </div>
                <div class="col-3">
                    <input name="items[${idx}][interval_seconds]" value="${item.interval_seconds || 1}" class="form-control" type="number" min="0" title="Segundos antes del siguiente item">
                </div>
                <div class="col-2">
                    <input name="items[${idx}][orden]" value="${item.orden || (idx + 1)}" min="1" class="form-control" type="number">
                </div>
                <div class="col-1"><button type="button" class="btn btn-danger btn-sm remove">-</button></div>
            `;
            row.addEventListener('dragstart', (e)=>{ e.dataTransfer.setData('text/plain',''); row.classList.add('dragging'); });
            row.addEventListener('dragend', ()=>{ row.classList.remove('dragging'); });
            row.addEventListener('dragover', (e)=>{ e.preventDefault(); const dragging = container.querySelector('.dragging'); if(!dragging || dragging===row) return; const rect = row.getBoundingClientRect(); const after = (e.clientY - rect.top) > rect.height/2; if(after) row.parentNode.insertBefore(dragging, row.nextSibling); else row.parentNode.insertBefore(dragging, row); refreshOrderInputs(); });

            row.querySelector('.remove').addEventListener('click', ()=>{ row.remove(); refreshOrderInputs(); });
            return row;
        }

        existing.forEach((it, i)=>{ const r = makeRow(i, it); container.appendChild(r); });
        document.getElementById('addItem').addEventListener('click', ()=>{
            const idx = container.children.length;
            const row = makeRow(idx, {bell_sound_id: sounds[0]?.id, interval_seconds:1, orden: idx});
            container.appendChild(row);
            refreshOrderInputs();
        });

        // preview current unsaved sequence (or existing)
        function waitForEnd(player, maxMs = 60000){
            return new Promise((res)=>{
                let done = false;
                const onEnd = ()=>{ if(done) return; done = true; player.removeEventListener('ended', onEnd); clearTimeout(timer); res(); };
                player.addEventListener('ended', onEnd);
                const timer = setTimeout(()=>{ if(!done){ done = true; player.removeEventListener('ended', onEnd); res(); } }, maxMs);
            });
        }

        document.getElementById('previewSequence')?.addEventListener('click', async ()=>{
            let items = Array.from(container.children).map(row=>{
                const sel = row.querySelector('select');
                const sid = sel ? sel.value : null;
                const interval = parseInt(row.querySelector('input[name$="[interval_seconds]"]')?.value||1,10);
                const orden = parseInt(row.querySelector('input[name$="[orden]"]')?.value||0,10);
                return { bell_sound_id: sid, interval_seconds: interval, orden: orden };
            }).filter(i=>i.bell_sound_id);
            if(!items.length) return alert('No hay items en la secuencia.');
            items.sort((a,b)=> parseInt(a.orden||0,10) - parseInt(b.orden||0,10));
            try{
                const audio = document.createElement('audio'); audio.preload='auto'; document.body.appendChild(audio);
                for(const it of items){
                    const bs = sounds.find(s=>s.id == it.bell_sound_id);
                    if(!bs || !bs.ruta_archivo) continue;
                    let src = bs.ruta_archivo; if(src.startsWith('/')) src = window.location.origin + src;
                    audio.src = src;
                    try{ await audio.play(); }catch(e){ console.warn('preview play failed', e); }
                    await waitForEnd(audio, 60000); // wait until audio ends (or timeout)
                    await new Promise(r=>setTimeout(r,(it.interval_seconds||1)*1000));
                }
                audio.remove();
            }catch(e){ console.warn(e); alert('Error vista previa'); }
        });
    </script>

@endsection
