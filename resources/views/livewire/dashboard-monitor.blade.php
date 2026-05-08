<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Dashboard Monitor</h3>
        <div>
            <button id="masterToggle" class="btn btn-emergency">Desbloquear audio</button>
        </div>
    </div>

    <div>
        <p>Eventos próximos o en ejecución:</p>
        <ul id="eventsList">
            @foreach($events as $e)
                <li>{{ $e->hora }} - {{ $e->bellSound->nombre }}</li>
            @endforeach
        </ul>
    </div>

    <audio id="player"></audio>
    @vite('resources/js/monitor.js')
</div>
