document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('masterToggle');
    const player = document.getElementById('player');
    let enabled = false;

    if(!btn || !player) return;

    btn.addEventListener('click', () => {
        enabled = true;
        btn.textContent = 'Audio desbloqueado';
        btn.classList.remove('btn-emergency');
        btn.classList.add('btn-success');
    });

    // Poll server every 5 seconds for events
    setInterval(async () => {
        if (!enabled) return;
        const res = await fetch('/_monitor/poll');
        if (!res.ok) return;
        const data = await res.json();
        if (data.length) {
            const ev = data[0];
            player.src = ev.bell_sound.ruta_archivo;
            player.play().catch(()=>console.log('Autoplay blocked'));
        }
    }, 5000);
});
