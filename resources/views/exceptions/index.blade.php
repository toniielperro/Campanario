@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
        <div>
            <h1 class="pilar-title">Calendario de Excepciones</h1>
            <p class="pilar-subtitle">Bloqueos puntuales para días u horarios especiales.</p>
        </div>
        <a href="{{ route('exceptions.create') }}" class="btn btn-gold mt-3 mt-lg-0">
            <i class="fa fa-plus mr-2"></i>Añadir excepción
        </a>
    </div>

    <section class="pilar-card">
        @livewire('schedule-exceptions-index')
    </section>
</div>
@endsection
