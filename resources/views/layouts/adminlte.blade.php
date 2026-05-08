<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Campanario</title>
    <link rel="icon" href="{{ asset('campanario.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar { position: relative; padding-bottom: 72px; }
        .sidebar-clock { position: absolute; bottom: 0; left: 0; width: 100%; padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.05); background: transparent; }
        .sidebar-clock #sidebarDate { font-size: 0.9rem; color: rgba(255,255,255,0.85); margin-bottom: 4px; text-transform: capitalize; }
        .sidebar-clock #sidebarTime { font-size: 1.05rem; font-weight: 600; color: #D4A017; }
        .play-toast { position: fixed; right: 24px; bottom: 24px; background: #16213E; color: #fff; padding: 12px 16px; border-radius: 12px; box-shadow: 0 12px 30px rgba(0,0,0,.18); display:none; z-index:9999 }
        /* Pagination tweaks for AdminLTE bootstrap pagination in card footers */
        .card-footer .pagination { margin: 0; }
        .card-footer .pagination .page-item .page-link {
            color: #16213E;
            background: transparent;
            border: 1px solid transparent;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            padding: 0;
            margin-left: 6px;
        }
        .card-footer .pagination .page-item:first-child .page-link { margin-left: 0; }
        .card-footer .pagination .page-item.active .page-link {
            background: #16213E;
            color: #fff;
            border-color: rgba(0,0,0,0.05);
        }
        .card-footer .pagination .page-item.disabled .page-link { color: rgba(0,0,0,0.25); }
        /* Ensure header keeps the original thickness even when its content is minimal */
        .main-header {
            min-height: 56px;
            height: 56px;
        }
        .main-header .navbar {
            min-height: 56px;
            height: 56px;
            align-items: center;
            padding-top: .5rem;
            padding-bottom: .5rem;
        }
    </style>
    @livewireStyles
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-0">
        <ul class="navbar-nav">
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-0">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <i class="fa fa-bell text-gold ml-2 mr-2"></i>
            <span class="brand-text">Campanario</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link text-white">
                            <i class="fa fa-home text-gold"></i>
                            <p>Inicio</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                            <i class="fa fa-bell text-gold"></i>
                            <p>Campanazos <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('bell_sounds.index') }}" class="nav-link text-white">
                                    <i class="fa fa-bell text-gold"></i>
                                    <p>Sonidos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sequences.index') }}" class="nav-link text-white">
                                    <i class="fa fa-list-ol text-gold"></i>
                                    <p>Secuencias</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link text-white">
                            <i class="fa fa-cog text-gold"></i>
                            <p>Gestión <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('schedules.index') }}" class="nav-link text-white">
                                    <i class="fa fa-calendar text-gold"></i>
                                    <p>Programaciones</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('exceptions.index') }}" class="nav-link text-white">
                                    <i class="fa fa-calendar-times text-gold"></i>
                                    <p>Calendario de Excepciones</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('plays.index') }}" class="nav-link text-white">
                            <i class="fa fa-history text-gold"></i>
                            <p>Historial reproducciones</p>
                        </a>
                    </li>
                </ul>
            </nav>
            @unless(request()->routeIs('dashboard'))
                <div class="sidebar-clock">
                    <div id="sidebarDate"></div>
                    <div id="sidebarTime"></div>
                </div>
            @endunless
        </div>
    </aside>

    <div class="content-wrapper p-3 p-md-4">
        <section class="content">
            <div class="container-fluid">
                @yield('content')
                {{ $slot ?? '' }}
            </div>
        </section>
    </div>
    <footer class="main-footer text-center">
        <strong>Escuela Parroquial Nuestra Señora del Pilar</strong>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@livewireScripts
@yield('scripts')
@if(session('pilar_success'))
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const pilarText = @json(session('pilar_success')) || '';
            const title = /restaur/i.test(pilarText) ? 'Restaurado correctamente' : 'Guardado correctamente';
            Swal.fire({
                title: title,
                text: pilarText,
                icon: 'success',
                confirmButtonText: 'Entendido'
            });
        });
    </script>
@endif
<script>
    function updateSidebarClock(){
        try{
            const d = new Date();
            const date = d.toLocaleDateString('es-ES', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
            const time = d.toLocaleTimeString('es-ES', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
            const dateEl = document.getElementById('sidebarDate');
            const timeEl = document.getElementById('sidebarTime');
            if(dateEl) dateEl.textContent = date.charAt(0).toUpperCase() + date.slice(1);
            if(timeEl) timeEl.textContent = time;
        }catch(e){console.error(e)}
    }
    updateSidebarClock();
    setInterval(updateSidebarClock, 1000);
</script>
    <script>
        // Global audio player and client-side schedule checker
        (function(){
            const player = document.createElement('audio');
            player.id = 'globalPlayer';
            player.style.display = 'none';
            player.preload = 'auto';
            document.body.appendChild(player);
            const toast = document.createElement('div'); toast.className = 'play-toast'; toast.id = 'playToast'; document.body.appendChild(toast);

            // default to enabled unless explicitly disabled (restore previous auto-play behavior)
            let masterEnabled = localStorage.getItem('masterEnabled') !== '0';
            const masterButtons = () => Array.from(document.querySelectorAll('[data-master-toggle]'));
            function updateMasterButton(){
                masterButtons().forEach((masterBtn) => {
                    const compact = masterBtn.dataset.labelMode === 'compact';
                    masterBtn.setAttribute('aria-pressed', masterEnabled ? 'true' : 'false');
                    masterBtn.classList.toggle('is-on', masterEnabled);
                    if(masterBtn.dataset.keepText !== 'true'){
                        masterBtn.textContent = masterEnabled ? (compact ? 'Activo' : 'Audio activado') : (compact ? 'Inactivo' : 'Activar audio');
                    }
                    if(!masterBtn.classList.contains('pilar-master')){
                        masterBtn.classList.toggle('btn-success', masterEnabled);
                        masterBtn.classList.toggle('btn-emergency', !masterEnabled);
                    }
                });
            }
            document.addEventListener('click', function(e){
                const masterBtn = e.target.closest && e.target.closest('[data-master-toggle]');
                if(masterBtn){
                    masterEnabled = !masterEnabled;
                    localStorage.setItem('masterEnabled', masterEnabled ? '1' : '0');
                    updateMasterButton();
                    if(masterEnabled) player.play().catch(()=>{});
                }
                const panicBtn = e.target.closest && e.target.closest('[data-panic-stop]');
                if(panicBtn){
                    player.pause();
                    player.currentTime = 0;
                    showToast('Audio detenido');
                    if(window.Swal){
                        Swal.fire({
                            title: 'Audio detenido',
                            text: 'Todo sonido activo fue interrumpido.',
                            icon: 'warning',
                            confirmButtonText: 'Entendido'
                        });
                    }
                }
            });
            updateMasterButton();
            window.PilarAudio = {
                stop(){
                    player.pause();
                    player.currentTime = 0;
                    showToast('Audio detenido');
                },
                isEnabled(){ return masterEnabled; },
                player
            };

            // fetch schedules and keep them updated periodically
            let schedules = [];
            async function fetchSchedules(){
                try{
                    const res = await fetch('/_monitor/active-schedules');
                    if(!res.ok) return;
                    schedules = await res.json();
                }catch(e){console.error(e)}
            }
            fetchSchedules();
            setInterval(fetchSchedules, 60_000); // refresh every minute

            // track last played per schedule to avoid double play
            const lastPlayed = {};
            const playingNow = {};
            // cross-tab / cross-page played keys
            const playedKeys = new Set();
            // load played keys from localStorage
            for(let i=0;i<localStorage.length;i++){
                const k = localStorage.key(i);
                if(k && k.startsWith('played_')){
                    playedKeys.add(k.replace('played_',''));
                }
            }
            // BroadcastChannel to notify other open tabs/windows (if supported)
            let bc = null;
            try{
                if(typeof BroadcastChannel !== 'undefined'){
                    bc = new BroadcastChannel('schedule-plays');
                    bc.onmessage = (ev) => {
                        try{
                            const key = ev.data && ev.data.key;
                            if(!key) return;
                            playedKeys.add(key);
                            const parts = key.split('_');
                            const sid = parts[0];
                            if(sid) lastPlayed[sid] = key;
                        }catch(e){console.warn(e)}
                    };
                }
            }catch(e){console.warn('BC init failed', e)}

            function pad(n){ return n.toString().padStart(2,'0'); }
            function waitForEnd(player, maxMs = 60000){
                return new Promise((res)=>{
                    let done = false;
                    const onEnd = ()=>{ if(done) return; done = true; player.removeEventListener('ended', onEnd); clearTimeout(timer); res(); };
                    player.addEventListener('ended', onEnd);
                    const timer = setTimeout(()=>{ if(!done){ done = true; player.removeEventListener('ended', onEnd); res(); } }, maxMs);
                });
            }

            function checkAndPlay(){
                try{
                    const d = new Date();
                    const hh = pad(d.getHours());
                    const mm = pad(d.getMinutes());
                    const ss = pad(d.getSeconds());
                    const timeStr = `${hh}:${mm}:00`;
                    const day = d.toLocaleDateString('en-US', {weekday:'long'}).toLowerCase();
                    schedules.forEach(s => {
                        if(!s || !s.hora) return;
                        // rely on server-provided applies_today flag (handles weekdays and specific dates)
                        if(!s.applies_today) return;
                        // skip if blocked by exception
                        if(s.blocked_by_exception) return;
                        if(s.played) return; // already played (server-recorded) for this schedule/minute
                        if(s.hora !== timeStr) return;
                        // mark once per minute (so it plays only once during the scheduled minute)
                        const dateKey = `${d.getFullYear()}-${d.getMonth()+1}-${d.getDate()}`;
                        const minuteKey = `${s.id}_${dateKey}_${hh}:${mm}`;
                        if (lastPlayed[s.id] === minuteKey) return; // already played this minute
                        if (playedKeys.has(minuteKey)) { lastPlayed[s.id] = minuteKey; return; } // another tab/page already marked it
                        // prevent race: mark as played before attempting to play to avoid duplicate triggers
                        lastPlayed[s.id] = minuteKey;
                        // also mark in localStorage and notify other pages immediately to avoid race on navigation
                        try{
                            localStorage.setItem('played_' + minuteKey, '1');
                            playedKeys.add(minuteKey);
                            if(bc) bc.postMessage({ key: minuteKey });
                        }catch(e){console.warn('persist played key failed', e)}
                            if (playingNow[s.id]) return;
                        playingNow[s.id] = true;
                        if(masterEnabled){
                            // if this schedule references a sequence, play items sequentially
                            if(s.sequence && Array.isArray(s.sequence.items) && s.sequence.items.length){
                                (async function(){
                                    try{
                                        for(const it of s.sequence.items){
                                                if(!it || !it.bell_sound || !it.bell_sound.ruta_archivo) continue;
                                                let src = it.bell_sound.ruta_archivo;
                                                if(src.startsWith('/')) src = window.location.origin + src;
                                                try{
                                                    player.src = src;
                                                    await player.play();
                                                    showToast('Reproduciendo: ' + (it.bell_sound.nombre || 'sonido'));
                                                    // wait for audio to finish (or timeout)
                                                    await waitForEnd(player, 60000);
                                                }catch(err){ console.warn('Item play failed', err); }
                                                // after item, wait interval_seconds before next
                                                await new Promise(r => setTimeout(r, (it.interval_seconds || 1) * 1000));
                                            }
                                    }finally{
                                        // record play once sequence finished
                                        try{
                                            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                            fetch('/_monitor/record-play', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                                                body: JSON.stringify({ schedule_id: s.id })
                                            }).catch(e => console.warn('Record play failed', e));
                                        }catch(e){console.warn(e)}
                                        playingNow[s.id] = false;
                                    }
                                })();
                            } else if(s.bell_sound && s.bell_sound.ruta_archivo){
                                (async function(){
                                    try{
                                        let src = s.bell_sound.ruta_archivo;
                                        if(src.startsWith('/')) src = window.location.origin + src;
                                        player.src = src;
                                        await player.play();
                                        showToast('Reproduciendo: ' + (s.bell_sound.nombre || 'sonido'));
                                        // wait until finished (or timeout)
                                        await waitForEnd(player, 60000);
                                        // record play on server so reloads/other pages don't replay
                                        try{
                                            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                            fetch('/_monitor/record-play', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                                                body: JSON.stringify({ schedule_id: s.id })
                                            }).catch(e => console.warn('Record play failed', e));
                                        }catch(e){console.warn(e)}
                                    }catch(err){
                                        console.warn('Play blocked or failed', err);
                                        showToast('Error reproduciendo audio');
                                    }finally{
                                        playingNow[s.id] = false;
                                    }
                                })();
                            } else {
                                // not enabled or missing file
                                playingNow[s.id] = false;
                            }
                        } else {
                            // master not enabled
                            playingNow[s.id] = false;
                        }
                    });
                }catch(e){console.error(e)}
            }

            // check every second (matches the sidebar clock granularity)
            setInterval(checkAndPlay, 1000);
            function showToast(msg){
                try{
                    const t = document.getElementById('playToast');
                    if(!t) return;
                    t.textContent = msg;
                    t.style.display = 'block';
                    clearTimeout(t._hideTimer);
                    t._hideTimer = setTimeout(()=>{ t.style.display = 'none'; }, 5000);
                }catch(e){console.error(e)}
            }
        })();
    </script>
    <script>
        document.addEventListener('submit', function(e){
            const form = e.target;
            if(!form.classList || (!form.classList.contains('pilar-confirm-delete') && !form.classList.contains('pilar-confirm-trash'))) return;
            if(form.dataset.confirmed === 'true') return;
            e.preventDefault();
            const permanent = form.classList.contains('pilar-confirm-delete');
            Swal.fire({
                title: permanent ? 'Eliminar definitivamente' : 'Enviar a papelera',
                text: permanent ? 'Esta acción no se puede deshacer.' : 'El registro quedará inactivo y podrás restaurarlo después.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: permanent ? 'Eliminar' : 'Enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed){
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        }, true);
    </script>
</body>
</html>
