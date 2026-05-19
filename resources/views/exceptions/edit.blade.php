@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
        <div>
            <h1 class="pilar-title">Editar Excepción</h1>
            <p class="pilar-subtitle">Actualiza el rango horario y las fechas específicas de bloqueo.</p>
        </div>
    </div>

    <section class="pilar-card">
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

            <div class="form-group">
                <label>Motivo</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $item->nombre) }}" placeholder="Ej: Semana Santa, mantenimiento, acto especial" />
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Hora inicio</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', \Illuminate\Support\Str::of($item->start_time)->substr(0, 5)) }}" required />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Hora fin</label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', \Illuminate\Support\Str::of($item->end_time)->substr(0, 5)) }}" required />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="mb-0">Fechas específicas</label>
                    <button type="button" id="addFecha" class="btn btn-ghost btn-sm">
                        <i class="fa fa-plus mr-2"></i>Añadir fecha
                    </button>
                </div>
                <div id="fechasContainer" class="d-flex flex-column gap-2">
                    @php $fechas = old('fechas_especificas', $item->fechas_especificas ?: ['']); @endphp
                    @foreach($fechas as $fecha)
                        <div class="d-flex align-items-center gap-2 fecha-row">
                            <input type="date" name="fechas_especificas[]" class="form-control" value="{{ $fecha }}" required />
                            <button type="button" class="pilar-icon-btn remove-fecha" title="Quitar fecha"><i class="fa fa-xmark"></i></button>
                        </div>
                    @endforeach
                </div>
                <small class="pilar-muted d-block mt-2">Debes seleccionar al menos una fecha específica.</small>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="activo" class="form-control pilar-filter-select">
                    <option value="1" {{ old('activo', $item->activo ?? true) ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ !old('activo', $item->activo ?? true) ? 'selected' : '' }}>Inactivo</option>
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

@section('scripts')
@parent
<script>
    (function(){
        const container = document.getElementById('fechasContainer');
        const addButton = document.getElementById('addFecha');

        function addRow(value = ''){
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-2 fecha-row';
            row.innerHTML = `
                <input type="date" name="fechas_especificas[]" class="form-control" value="${value}" required />
                <button type="button" class="pilar-icon-btn remove-fecha" title="Quitar fecha"><i class="fa fa-xmark"></i></button>
            `;
            container.appendChild(row);
            updateRemoveButtons();
        }

        function updateRemoveButtons(){
            const rows = Array.from(container.querySelectorAll('.fecha-row'));
            rows.forEach(row => {
                const button = row.querySelector('.remove-fecha');
                button.classList.toggle('d-none', rows.length <= 1);
            });
        }

        addButton.addEventListener('click', () => addRow());
        container.addEventListener('click', (event) => {
            const button = event.target.closest('.remove-fecha');
            if(!button) return;
            button.closest('.fecha-row').remove();
            updateRemoveButtons();
        });
        updateRemoveButtons();
    })();
</script>
@endsection
