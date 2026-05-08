@extends('layouts.adminlte')

@section('content')
<div class="pilar-page space-y-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
        <div>
            <h1 class="pilar-title">Centro de Mando</h1>
            <p class="pilar-subtitle">Estado general de campanas, audio y próximos toques.</p>
        </div>
        <div class="mt-3 mt-lg-0 d-flex flex-wrap gap-2">
            <a href="{{ route('schedules.create') }}" class="btn btn-gold">
                <i class="fa fa-plus mr-2"></i>Añadir evento
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-7 mb-4">
            <section class="pilar-card h-100">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-4">
                    <div>
                        <span class="pilar-badge"><i class="fa fa-circle text-gold"></i> Sistema parroquial</span>
                        <div id="dashboardDate" class="mt-4 text-uppercase font-weight-bold text-muted" style="letter-spacing:.08em;font-size:.78rem"></div>
                        <div id="dashboardClock" class="mt-2 font-weight-bold" style="font-size:clamp(3rem,8vw,6.2rem);line-height:1;color:#1A2238;letter-spacing:0">--:--</div>
                        <p class="pilar-muted mt-2 mb-0">Hora del sistema para la reproducción automática.</p>
                    </div>
                    <div class="d-flex flex-column justify-content-between align-items-md-end">
                        <div class="text-md-right">
                            <div class="font-weight-bold mb-2" style="color:#1A2238">Activar Audio</div>
                            <button type="button" data-master-toggle data-keep-text="true" class="pilar-master" aria-label="Activar Audio"></button>
                            <div id="audioStateText" class="mt-3 pilar-muted">Audio activo</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-5 mb-4">
            <section class="pilar-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="h5 font-weight-bold mb-1" style="color:#1A2238">Próximos campanazos</h2>
                        <p class="pilar-muted mb-0">Eventos activos de hoy.</p>
                    </div>
                    <i class="fa fa-bell text-gold fa-lg"></i>
                </div>
                <div id="nextEvents" class="d-flex flex-column gap-3">
                    <div class="pilar-card-soft">
                        <div class="pilar-muted">Cargando programación...</div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <section class="pilar-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="pilar-muted">Eventos activos</div>
                        <div id="activeCount" class="h2 font-weight-bold mb-0" style="color:#1A2238">0</div>
                    </div>
                    <span class="pilar-icon-btn"><i class="fa fa-calendar-check text-gold"></i></span>
                </div>
            </section>
        </div>
        <div class="col-lg-4 mb-4">
            <section class="pilar-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="pilar-muted">Bloqueados hoy</div>
                        <div id="blockedCount" class="h2 font-weight-bold mb-0" style="color:#1A2238">0</div>
                    </div>
                    <span class="pilar-icon-btn"><i class="fa fa-calendar-times text-gold"></i></span>
                </div>
            </section>
        </div>
        <div class="col-lg-4 mb-4">
            <section class="pilar-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="pilar-muted">Reproducidos hoy</div>
                        <div id="playedCount" class="h2 font-weight-bold mb-0" style="color:#1A2238">0</div>
                    </div>
                    <span class="pilar-icon-btn"><i class="fa fa-history text-gold"></i></span>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
@vite('resources/js/dashboard.js')
@endsection
