<div>
    <form wire:submit.prevent="save" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nombre</label>
            <input wire:model.live="nombre" class="form-control" />
        </div>
        <div class="form-group">
            <label>Archivo (subir)</label>
            <input type="file" wire:model="ruta_archivo" class="form-control" />
        </div>
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
