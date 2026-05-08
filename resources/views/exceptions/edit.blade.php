@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <section class="pilar-card">
        <h3>Editar Excepción</h3>
        <form action="{{ route('exceptions.update', $item->id) }}" method="POST" class="pilar-form">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="form-group">
                <label>Motivo</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $item->nombre) }}" />
            </div>
            <div class="form-group">
                <label>Hora inicio</label>
                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $item->start_time) }}" required />
            </div>
            <div class="form-group">
                <label>Hora fin</label>
                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $item->end_time) }}" required />
            </div>
            <div class="form-group">
                <label>Fechas específicas</label>
                <div id="fechasContainer">
                    @php $fechas = $item->fechas_especificas ?? []; @endphp
                    @if(empty($fechas))
                        <div class="d-flex mb-2 fecha-row"><input type="date" name="fechas_especificas[]" class="form-control" /><button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-fecha" style="display:none">Eliminar</button></div>
                    @else
                        @foreach($fechas as $f)
                            <div class="d-flex mb-2 fecha-row"><input type="date" name="fechas_especificas[]" class="form-control" value="{{ $f }}" /><button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-fecha">Eliminar</button></div>
                        @endforeach
                    @endif
                </div>
                <div class="mt-2"><button type="button" id="addFecha" class="btn btn-sm btn-secondary">Añadir fecha</button></div>
            </div>
            @section('scripts')
            @parent
            <script>
                (function(){
                    function addRow(value){
                        const container = document.getElementById('fechasContainer');
                        const row = document.createElement('div'); row.className = 'd-flex mb-2 fecha-row';
                        const input = document.createElement('input'); input.type='date'; input.name='fechas_especificas[]'; input.className='form-control'; if(value) input.value = value;
                        const btn = document.createElement('button'); btn.type='button'; btn.className='btn btn-outline-danger btn-sm ms-2 remove-fecha'; btn.textContent='Eliminar'; btn.addEventListener('click', ()=>{ row.remove(); updateRemoveButtons(); });
                        row.appendChild(input); row.appendChild(btn); container.appendChild(row); updateRemoveButtons();
                    }
                    function updateRemoveButtons(){
                        const rows = document.querySelectorAll('.fecha-row');
                        rows.forEach((r,i)=>{ const btn = r.querySelector('.remove-fecha'); if(!btn) return; btn.style.display = (rows.length>1)?'inline-block':'none'; });
                    }
                    document.getElementById('addFecha').addEventListener('click', ()=> addRow(''));
                    document.querySelectorAll('.remove-fecha').forEach(b=> b.addEventListener('click', (ev)=>{ ev.target.closest('.fecha-row').remove(); updateRemoveButtons(); }));
                    updateRemoveButtons();
                })();
            </script>
            @endsection
            <div class="form-group">
                <label>Activo</label>
                <select name="activo" class="form-control pilar-filter-select">
                    <option value="1" {{ ($item->activo ?? true) ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ !($item->activo ?? true) ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('exceptions.index') }}" class="btn btn-ghost">Cancelar</a>
                <button class="btn btn-gold"><i class="fa fa-save mr-2"></i>Guardar</button>
            </div>
        </form>
    </section>
</div>
@endsection
