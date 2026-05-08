@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
        <div>
            <h1 class="pilar-title">Programaciones</h1>
            <p class="pilar-subtitle">Horarios, frecuencia y secuencia asignada a cada campanazo.</p>
        </div>
        <a href="{{ route('schedules.create') }}" class="btn btn-gold mt-3 mt-lg-0">
            <i class="fa fa-plus mr-2"></i>Añadir evento
        </a>
    </div>

    <section class="pilar-card {{ request('activo') === '0' ? 'pilar-trash' : '' }}">
        <form method="GET" class="pilar-toolbar">
            <div class="pilar-search mb-3 mb-md-0">
                <i class="fa fa-search"></i>
                <input name="search" value="{{ $search ?? request('search') }}" class="form-control" placeholder="Buscar programaciones..." oninput="window.pilarSearchSubmit(this)" />
            </div>
            <select name="activo" class="form-control pilar-filter-select" onchange="this.form.submit()">
                <option value="1" {{ request('activo', '1') === '1' ? 'selected' : '' }}>Activas</option>
                <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Papelera</option>
            </select>
        </form>

        @if(request('activo') === '0')
            <div class="pilar-card-soft pilar-trash mb-4">
                <strong>Papelera de programaciones</strong>
                <div class="pilar-muted mt-1">Eventos inactivos o enviados a papelera.</div>
            </div>
        @endif

        <div class="table-responsive pilar-table-wrap">
            <table class="pilar-table">
                <thead><tr><th>Nombre</th><th>Hora</th><th>Frecuencia</th><th>Secuencia</th><th>Fechas</th><th class="text-right">Acciones</th></tr></thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="font-weight-bold">{{ $item->nombre ?? 'Campanazo programado' }}</td>
                        <td><span class="pilar-badge"><i class="fa fa-clock"></i>{{ substr($item->hora, 0, 5) }}</span></td>
                        <td>
                            @php
                                $freq = $item->frecuencia ?? null;
                                $label = ($item->tipo ?? '') === 'especial' ? 'Especial' : match($freq) {
                                    'lunes_a_viernes' => 'Lunes a Viernes',
                                    'diario' => 'Diario',
                                    'una_sola_vez' => 'Una sola vez',
                                    'personalizado' => 'Personalizado',
                                    default => '-',
                                };
                            @endphp
                            {{ $label }}
                        </td>
                        <td>{{ $item->sequence->nombre ?? '-' }}</td>
                        <td class="pilar-muted">{{ implode(', ', $item->fechas_especificas ?? []) ?: '-' }}</td>
                        <td class="text-right">
                            @if(method_exists($item, 'trashed') && $item->trashed())
                                <form method="POST" action="{{ route('schedules.restore', $item->id) }}" class="d-inline">@csrf<button class="btn btn-restore btn-sm">Restaurar</button></form>
                                <form method="POST" action="{{ route('schedules.forceDelete', $item->id) }}" class="d-inline pilar-confirm-delete">@csrf @method('DELETE')<button class="btn btn-wine btn-sm">Eliminar</button></form>
                            @else
                                <a href="{{ route('schedules.edit', $item->id) }}" class="pilar-icon-btn" title="Editar"><i class="fa fa-pen"></i></a>
                                <form method="POST" action="{{ route('schedules.destroy', $item->id) }}" class="d-inline pilar-confirm-trash">@csrf @method('DELETE')<button class="pilar-icon-btn" title="Enviar a papelera"><i class="fa fa-trash"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center pilar-muted py-4">No hay programaciones para mostrar.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $items->appends(request()->all())->links() }}
        </div>
    </section>
</div>
@endsection

@section('scripts')
@parent
<script>
    window.pilarSearchSubmit = window.pilarSearchSubmit || function(input){
        clearTimeout(input._timer);
        input._timer = setTimeout(() => input.form.submit(), 350);
    };
</script>
@endsection
