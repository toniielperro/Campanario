@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="pilar-title">Editar Sonido</h1>
            <p class="pilar-subtitle">Actualiza el nombre o reemplaza el archivo de audio.</p>
        </div>
    </div>

    <section class="pilar-card">
        <form action="{{ route('bell_sounds.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="pilar-form">
            @csrf
            <div class="form-group">
                <label>Nombre</label>
                <input name="nombre" value="{{ old('nombre', $item->nombre) }}" class="form-control" />
            </div>
            <div class="form-group">
                <label>Archivo de audio</label>
                <input type="file" name="ruta_archivo" class="form-control" />
                @if($item->ruta_archivo)
                    <small class="pilar-muted d-block mt-2">Actual: {{ $item->ruta_archivo }}</small>
                @endif
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('bell_sounds.index') }}" class="btn btn-ghost">Cancelar</a>
                <button class="btn btn-gold"><i class="fa fa-save mr-2"></i>Guardar</button>
            </div>
        </form>
    </section>
</div>
@endsection
