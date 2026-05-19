@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <section class="pilar-card">
        <h3>Editar Programación</h3>
        <form action="{{ route('schedules.update', $item->id) }}" method="POST" class="pilar-form">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="form-group">
                <label>Nombre de la programación</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $item->nombre ?? '') }}" placeholder="Opcional: nombre descriptivo" />
            </div>
            <div class="form-group">
                <label>Secuencia</label>
                <select id="sequenceSelect" name="sequence_id" class="form-control" required>
                    <option value="">-- seleccionar --</option>
                    @foreach($sequences as $seq)
                        <option value="{{ $seq->id }}" {{ $item->sequence_id == $seq->id ? 'selected' : '' }}>{{ $seq->nombre }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Selecciona la secuencia que se reproducirá en este horario.</small>
            </div>
            <div class="form-group">
                <label>Tipo de programación</label>
                <div>
                    <label class="mr-3"><input type="radio" name="tipo" value="ordinaria" {{ ($item->tipo ?? 'ordinaria') == 'ordinaria' ? 'checked' : '' }}> Ordinaria</label>
                    <label><input type="radio" name="tipo" value="especial" {{ ($item->tipo ?? '') == 'especial' ? 'checked' : '' }}> Especial (fechas concretas)</label>
                </div>
            </div>
            <div id="ordinariaOptions">
                <div class="form-group">
                    <label>Frecuencia</label>
                    <select id="frecuenciaSelect" name="frecuencia" class="form-control">
                        <option value="lunes_a_viernes" {{ ($item->frecuencia ?? '') == 'lunes_a_viernes' ? 'selected' : '' }}>Lunes a Viernes</option>
                        <option value="diario" {{ ($item->frecuencia ?? '') == 'diario' ? 'selected' : '' }}>Diario</option>
                        <option value="una_sola_vez" {{ ($item->frecuencia ?? '') == 'una_sola_vez' ? 'selected' : '' }}>Una sola vez (fecha)</option>
                        <option value="personalizado" {{ ($item->frecuencia ?? '') == 'personalizado' ? 'selected' : '' }}>Personalizado (elegir días)</option>
                    </select>
                </div>
                <div class="form-group" id="singleDateContainer" style="display:none">
                    <label>Fecha única</label>
                    <input type="date" id="singleDate" name="single_date" class="form-control" value="{{ old('single_date', ($item->fechas_especificas[0] ?? '')) }}" />
                </div>
                <div class="form-group" id="personalizadoDaysContainer" style="display:none">
                    <label>Días (selecciona los días para "personalizado")</label>
                    <div class="d-flex gap-2 flex-wrap" id="diasCheckboxes">
                        @foreach(['lunes','martes','miercoles','jueves','viernes','sabado','domingo'] as $d)
                            <label><input type="checkbox" name="dias_semana[]" value="{{ $d }}" {{ in_array($d, $item->dias_semana ?? []) ? 'checked' : '' }}> {{ ucfirst($d) }}</label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Hora</label>
                <input type="time" name="hora" value="{{ $item->hora }}" class="form-control" />
            </div>
            <div class="form-group" id="especialContainer" style="display:none">
                <label>Fechas específicas</label>
                <div class="mb-2 d-flex gap-2 align-items-center">
                    <div id="calendarWidget"></div>
                    <div>
                        <div class="mb-2">Seleccionadas:</div>
                        <div id="datesList" class="d-flex gap-2 flex-wrap pilar-date-list"></div>
                        <small class="text-muted d-block">Haz clic en el calendario para seleccionar varias fechas.</small>
                    </div>
                </div>
            </div>
            <!-- 'Activo' control removed: schedules are active by default -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('schedules.index') }}" class="btn btn-ghost">Cancelar</a>
                <button class="btn btn-gold"><i class="fa fa-save mr-2"></i>Guardar</button>
            </div>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script>
    (function(){
        const seqSelect = document.getElementById('sequenceSelect');
        const soundSelect = document.getElementById('soundSelect');
        const previewBtn = document.getElementById('previewSequence');
        const previewContainer = document.getElementById('sequencePreviewContainer');
        function updateVisibility(){
            try{
                if(seqSelect.value){ if(soundSelect) soundSelect.parentElement.style.display = 'none'; if(previewContainer) previewContainer.style.display = 'block'; }
                else { if(soundSelect) soundSelect.parentElement.style.display = ''; if(previewContainer) previewContainer.style.display = 'none'; }
            }catch(e){ console.warn('updateVisibility guard', e); }
        }
        seqSelect.addEventListener('change', updateVisibility);
        updateVisibility();

        previewBtn?.addEventListener('click', async ()=>{
            const id = seqSelect.value; if(!id) return;
            try{
                const res = await fetch(`/sequences/${id}/preview`);
                if(!res.ok) throw new Error('No preview');
                const data = await res.json();
                const audio = document.createElement('audio'); audio.preload='auto'; document.body.appendChild(audio);
                for(const it of data.items){
                    if(!it.bell_sound || !it.bell_sound.ruta_archivo) continue;
                    let src = it.bell_sound.ruta_archivo; if(src.startsWith('/')) src = window.location.origin + src;
                    audio.src = src;
                    const startMs = Date.now();
                    try{ await audio.play(); }catch(e){ console.warn('Preview play failed', e); }
                    const elapsed = Date.now() - startMs;
                    const waitMs = Math.max((it.interval_seconds||1)*1000 - elapsed, 0);
                    if(waitMs > 0) await new Promise(r=>setTimeout(r, waitMs));
                }
                audio.remove();
            }catch(e){ console.warn(e); alert('No se pudo obtener la secuencia para vista previa.'); }
        });
    })();
    (function(){
        const calendar = document.getElementById('calendarWidget');
        const datesList = document.getElementById('datesList');
        const form = document.querySelector('form');
        const existingDates = @json($item->fechas_especificas ?? []);
        const selected = new Set(existingDates || []);

        function iso(y,m,d){ return `${y.toString().padStart(4,'0')}-${(m+1).toString().padStart(2,'0')}-${d.toString().padStart(2,'0')}`; }

        function renderDatesList(){
            datesList.innerHTML = '';
            Array.from(selected).sort().forEach(val=>{
                const el = document.createElement('div'); el.className='badge bg-secondary p-2 text-white'; el.style.cursor='default';
                el.innerHTML = `<span>${val}</span> <button type="button" class="btn btn-sm btn-link text-white ms-2 remove-date">x</button>`;
                el.querySelector('.remove-date').addEventListener('click', ()=>{ removeDate(val); });
                datesList.appendChild(el);
            });
            Array.from(form.querySelectorAll('input[name="fechas_especificas[]"]')).forEach(i=>i.remove());
            Array.from(selected).sort().forEach(val=>{ const h = document.createElement('input'); h.type='hidden'; h.name='fechas_especificas[]'; h.value = val; form.appendChild(h); });
        }

        function addDate(val){ if(!val) return; if(selected.has(val)) return; selected.add(val); renderDatesList(); }
        function removeDate(val){ if(!selected.has(val)) return; selected.delete(val); renderDatesList(); }

        let cur = new Date();
        function renderCalendar(){
            calendar.innerHTML = '';
            const header = document.createElement('div'); header.className='d-flex justify-content-between align-items-center mb-2';
            const prev = document.createElement('button'); prev.type='button'; prev.className='btn btn-sm btn-outline-secondary'; prev.textContent = '<';
            const next = document.createElement('button'); next.type='button'; next.className='btn btn-sm btn-outline-secondary'; next.textContent = '>';
            const title = document.createElement('div'); title.textContent = cur.toLocaleString('es-ES', {month:'long', year:'numeric'});
            header.appendChild(prev); header.appendChild(title); header.appendChild(next);
            calendar.appendChild(header);

            const grid = document.createElement('div'); grid.style.display='grid'; grid.style.gridTemplateColumns='repeat(7,28px)'; grid.style.gap='4px';
            ['L','M','X','J','V','S','D'].forEach(h=>{ const dh = document.createElement('div'); dh.style.textAlign='center'; dh.style.fontSize='0.8rem'; dh.textContent = h; grid.appendChild(dh); });

            const year = cur.getFullYear(); const month = cur.getMonth();
            const first = new Date(year, month, 1); const startDay = (first.getDay()+6) % 7;
            const days = new Date(year, month+1, 0).getDate();
            for(let i=0;i<startDay;i++){ const empty = document.createElement('div'); grid.appendChild(empty); }
            for(let d=1; d<=days; d++){
                const cell = document.createElement('div'); cell.style.width='28px'; cell.style.height='28px'; cell.style.lineHeight='28px'; cell.style.textAlign='center'; cell.style.cursor='pointer';
                const val = iso(year, month, d);
                cell.textContent = d;
                if(selected.has(val)){ cell.style.background = '#0d6efd'; cell.style.color='#fff'; cell.style.borderRadius='4px'; }
                cell.addEventListener('click', ()=>{ if(selected.has(val)) removeDate(val); else addDate(val); renderCalendar(); });
                grid.appendChild(cell);
            }
            calendar.appendChild(grid);

            prev.addEventListener('click', ()=>{ cur = new Date(year, month-1, 1); renderCalendar(); });
            next.addEventListener('click', ()=>{ cur = new Date(year, month+1, 1); renderCalendar(); });
        }

        renderCalendar(); renderDatesList();
    })();
    // tipo/frecuencia UI logic (initialize based on existing item)
    (function(){
        const tipoRadios = document.querySelectorAll('input[name="tipo"]');
        const especialContainer = document.getElementById('especialContainer');
        const ordinariaOptions = document.getElementById('ordinariaOptions');
        const frecuenciaSelect = document.getElementById('frecuenciaSelect');
        const singleDateContainer = document.getElementById('singleDateContainer');
        const personalizadoDaysContainer = document.getElementById('personalizadoDaysContainer');
        const diasCheckboxes = Array.from(document.querySelectorAll('input[name="dias_semana[]"]'));

        function updateTipo(){
            const tipo = document.querySelector('input[name="tipo"]:checked').value;
            if(tipo === 'especial'){
                especialContainer.style.display = '';
                ordinariaOptions.style.display = 'none';
                personalizadoDaysContainer.style.display = 'none';
                diasCheckboxes.forEach(cb=>{ cb.checked = false; cb.disabled = true; });
            } else {
                especialContainer.style.display = 'none';
                ordinariaOptions.style.display = '';
                diasCheckboxes.forEach(cb=>{ cb.disabled = false; });
                updateFrecuencia();
            }
        }

        function updateFrecuencia(){
            const val = frecuenciaSelect.value;
            personalizadoDaysContainer.style.display = 'none';
            if(val === 'una_sola_vez'){
                singleDateContainer.style.display = '';
                diasCheckboxes.forEach(cb=>{ cb.checked = false; cb.disabled = true; });
                } else if(val === 'lunes_a_viernes'){
                    singleDateContainer.style.display = 'none';
                    const map = ['lunes','martes','miercoles','jueves','viernes'];
                    diasCheckboxes.forEach(cb=>{ cb.checked = map.includes(cb.value); cb.disabled = true; });
            } else if(val === 'diario'){
                singleDateContainer.style.display = 'none';
                diasCheckboxes.forEach(cb=>{ cb.checked = true; cb.disabled = true; });
            } else if(val === 'personalizado'){
                singleDateContainer.style.display = 'none';
                personalizadoDaysContainer.style.display = ''; // leave checkboxes state as per server
                diasCheckboxes.forEach(cb=>{ cb.disabled = false; });
            }
        }

        tipoRadios.forEach(r=> r.addEventListener('change', updateTipo));
        frecuenciaSelect.addEventListener('change', updateFrecuencia);
        // initialize based on checked radio/select
        updateTipo();
    })();
</script>
@endsection
