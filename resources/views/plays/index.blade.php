@extends('layouts.adminlte')

@section('content')
<div class="pilar-page">
    <div class="mb-4">
        <h1 class="pilar-title">Historial de reproducciones</h1>
        <p class="pilar-subtitle">Registro de campanazos ejecutados por el sistema.</p>
    </div>

    <section class="pilar-card">
        <div class="pilar-toolbar">
            <div class="pilar-search">
                <i class="fa fa-search"></i>
                <input class="form-control" id="playsSearch" placeholder="Buscar en el historial..." />
            </div>
        </div>
        <div class="table-responsive pilar-table-wrap">
            <table class="pilar-table" id="playsTable">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Fecha</th><th>Hora</th></tr>
                </thead>
                <tbody>
                    @forelse($plays as $play)
                        <tr>
                            <td>{{ $play->id }}</td>
                            <td class="font-weight-bold">{{ $play->schedule->nombre ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($play->played_at)->format('Y-m-d') }}</td>
                            <td><span class="pilar-badge">{{ \Carbon\Carbon::parse($play->played_at)->format('H:i:s') }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center pilar-muted py-4">No hay reproducciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-4">
            {!! $plays->withQueryString()->links('pagination::bootstrap-4') !!}
        </div>
    </section>
</div>
@endsection

@section('scripts')
@parent
<script>
    document.getElementById('playsSearch')?.addEventListener('input', function(){
        const q = this.value.toLowerCase();
        document.querySelectorAll('#playsTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endsection
