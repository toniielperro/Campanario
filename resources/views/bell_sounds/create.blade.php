@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="pilar-title">Crear Sonido</h1>
            <p class="pilar-subtitle">Agrega un archivo de audio para usarlo en secuencias.</p>
        </div>
    </div>

    <section class="pilar-card">
        <form action="{{ route('bell_sounds.store') }}" method="POST" enctype="multipart/form-data" class="pilar-form">
            @csrf
            <div class="form-group">
                <label>Nombre</label>
                <input name="nombre" value="{{ old('nombre') }}" class="form-control" placeholder="Ej: Campana mayor" />
            </div>
            <div class="form-group">
                <label>Archivo de audio</label>
                <input type="file" name="ruta_archivo" class="form-control" />
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('bell_sounds.index') }}" class="btn btn-ghost">Cancelar</a>
                <button class="btn btn-gold"><i class="fa fa-save mr-2"></i>Guardar</button>
            </div>
        </form>
    </section>
</div>
@endsection
