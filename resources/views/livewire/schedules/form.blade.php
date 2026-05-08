<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Sonido</label>
            <select wire:model="bell_sound_id" class="form-control">
                <option value="">-- seleccionar --</option>
                @foreach($sounds as $s)
                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Hora</label>
            <input type="time" wire:model="hora" class="form-control" />
        </div>
        <div class="form-group">
            <label>Días de la semana</label>
            <div class="d-flex gap-2">
                @foreach(['lunes','martes','miercoles','jueves','viernes','sabado','domingo'] as $d)
                    <label><input type="checkbox" value="{{ $d }}" wire:model="dias_semana"> {{ ucfirst($d) }}</label>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label>Activo</label>
            <input type="checkbox" wire:model="activo" />
        </div>
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
