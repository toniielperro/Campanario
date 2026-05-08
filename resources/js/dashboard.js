(function(){
    const clock = document.getElementById('dashboardClock');
    const date = document.getElementById('dashboardDate');
    const audioState = document.getElementById('audioStateText');
    const list = document.getElementById('nextEvents');
    const activeCount = document.getElementById('activeCount');
    const blockedCount = document.getElementById('blockedCount');
    const playedCount = document.getElementById('playedCount');

    function pad(n){ return String(n).padStart(2, '0'); }
    function updateClock(){
        const now = new Date();
        clock.textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        date.textContent = now.toLocaleDateString('es-ES', { weekday:'long', day:'2-digit', month:'long', year:'numeric' });
        audioState.textContent = localStorage.getItem('masterEnabled') === '0' ? 'Audio inactivo' : 'Audio activo';
    }

    function minutesUntil(hora){
        const now = new Date();
        const [h, m] = hora.slice(0, 5).split(':').map(Number);
        const target = new Date(now);
        target.setHours(h, m, 0, 0);
        if(target < now) target.setDate(target.getDate() + 1);
        return Math.max(0, Math.round((target - now) / 60000));
    }

    function eventName(item){
        return item.nombre || item.sequence?.nombre || item.bell_sound?.nombre || 'Campanazo programado';
    }

    function renderEvents(items, todayPlaysCount = 0){
        const active = items.filter(i => i.applies_today && !i.blocked_by_exception);
        const blocked = items.filter(i => i.applies_today && i.blocked_by_exception);
        activeCount.textContent = active.length;
        blockedCount.textContent = blocked.length;
        playedCount.textContent = todayPlaysCount || items.filter(i => i.played).length;

        const upcoming = active
            .map(i => ({...i, remaining: minutesUntil(i.hora)}))
            .sort((a, b) => a.remaining - b.remaining)
            .slice(0, 5);

        if(!upcoming.length){
            list.innerHTML = '<div class="pilar-card-soft"><div class="font-weight-bold">No hay campanazos pendientes hoy</div><div class="pilar-muted mt-1">La agenda está despejada.</div></div>';
            return;
        }

        list.innerHTML = upcoming.map(item => `
            <article class="d-flex align-items-center justify-content-between p-3 rounded-xl" style="border:1px solid #E8EAF0;background:#fff">
                <div class="d-flex align-items-center" style="gap:.85rem">
                    <span class="pilar-icon-btn" style="background:#fffdf3"><i class="fa fa-bell text-gold"></i></span>
                    <div>
                        <div class="font-weight-bold" style="color:#1A2238">${eventName(item)}</div>
                        <div class="pilar-muted">${item.hora.slice(0,5)}</div>
                    </div>
                </div>
                <span class="pilar-badge">En ${item.remaining} min</span>
            </article>
        `).join('');
    }

    async function loadEvents(){
        try{
            const res = await fetch('/_monitor/active-schedules');
            if(!res.ok) return;
            const data = await res.json();
            const items = Array.isArray(data) ? data : (data.schedules || []);
            const todayPlaysCount = data.today_plays_count || 0;
            renderEvents(items, todayPlaysCount);
        }catch(e){
            console.warn(e);
        }
    }

    // initialize
    if(clock && date && audioState && list && activeCount && blockedCount && playedCount){
        updateClock();
        loadEvents();
        setInterval(updateClock, 1000);
        setInterval(loadEvents, 30000);
    }
})();
