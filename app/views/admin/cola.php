<?php
$tituloPagina = 'Cola de Karaoke';
$breadcrumb = 'Control en vivo del show';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .stage-card {
        background: linear-gradient(135deg, rgba(106,13,173,0.2), rgba(18,18,26,0.95));
        border: 1px solid rgba(255,215,0,0.2);
        border-radius: 20px;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
        min-height: 280px;
    }
    .stage-card::before {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(circle at 30% 20%, rgba(255,215,0,0.1), transparent 50%),
                    radial-gradient(circle at 80% 80%, rgba(154,48,255,0.15), transparent 50%);
        pointer-events: none;
    }
    .stage-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height: 230px; color:rgba(240,232,208,0.3); }
    .stage-empty .icn { font-size:3rem; margin-bottom:0.5rem; opacity:0.4; }

    .now-playing-content { position: relative; z-index: 2; }
    .singer-badge {
        display:inline-flex; align-items:center; gap:0.5rem; background: rgba(255,215,0,0.12);
        border: 1px solid rgba(255,215,0,0.3); border-radius: 30px; padding: 0.4rem 1rem; font-size: 0.85rem; font-weight:600; color: var(--oro);
    }
    .song-title-big { font-family:'Cinzel',serif; font-size: 1.6rem; font-weight:700; color:#fff; margin: 0.75rem 0 0.2rem; }
    .song-artist-big { font-size: 0.95rem; color: rgba(240,232,208,0.6); }

    .equalizer { display:flex; align-items:flex-end; gap:3px; height:30px; margin: 1rem 0; }
    .eq-bar { width: 4px; background: linear-gradient(180deg, #ffd700, #9b30ff); border-radius: 2px; animation: eqBounce 0.8s ease-in-out infinite; }
    .eq-bar:nth-child(1){height:40%; animation-delay:0s;}
    .eq-bar:nth-child(2){height:80%; animation-delay:.1s;}
    .eq-bar:nth-child(3){height:50%; animation-delay:.2s;}
    .eq-bar:nth-child(4){height:100%; animation-delay:.3s;}
    .eq-bar:nth-child(5){height:65%; animation-delay:.15s;}
    .eq-bar:nth-child(6){height:35%; animation-delay:.25s;}
    .eq-bar:nth-child(7){height:90%; animation-delay:.05s;}
    @keyframes eqBounce { 0%,100% { transform: scaleY(0.4); } 50% { transform: scaleY(1); } }

    .control-btns { display:flex; gap:0.6rem; margin-top:1rem; flex-wrap: wrap; }
    .btn-control {
        border:none; border-radius:10px; padding:0.6rem 1.1rem; font-size:0.85rem; font-weight:700;
        display:flex; align-items:center; gap:0.4rem; cursor:pointer; transition: all 0.2s;
    }
    .btn-skip { background: rgba(231,76,60,0.15); color:#e74c3c; border:1px solid rgba(231,76,60,0.3); }
    .btn-skip:hover { background: rgba(231,76,60,0.25); }
    .btn-next { background: linear-gradient(135deg,#b8860b,#ffd700); color:#0a0a0f; }
    .btn-next:hover { box-shadow: 0 4px 16px rgba(255,215,0,0.4); transform: translateY(-1px); }

    /* Cola en espera */
    .queue-item {
        display:flex; align-items:center; gap:0.9rem; background: rgba(255,255,255,0.025);
        border: 1px solid rgba(255,255,255,0.05); border-radius:12px; padding:0.85rem 1rem; margin-bottom:0.6rem;
        transition: all 0.25s ease; animation: queueIn 0.4s ease both;
    }
    @keyframes queueIn { from { opacity:0; transform: translateX(-15px);} to { opacity:1; transform: translateX(0);} }
    .queue-item:hover { background: rgba(255,215,0,0.04); border-color: rgba(255,215,0,0.15); transform: translateX(3px); }
    .queue-pos {
        width:34px; height:34px; border-radius:10px; background: linear-gradient(135deg, rgba(255,215,0,0.15), rgba(255,215,0,0.05));
        color:var(--oro); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.95rem; flex-shrink:0;
        border: 1px solid rgba(255,215,0,0.2);
    }
    .queue-info { flex:1; min-width:0; }
    .queue-title { font-weight:700; font-size:0.92rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .queue-meta { font-size:0.75rem; color:rgba(240,232,208,0.45); }
    .queue-actions { display:flex; gap:0.4rem; }
    .btn-icon-mini {
        width:30px; height:30px; border-radius:8px; border:none; display:flex; align-items:center; justify-content:center;
        cursor:pointer; transition:all 0.2s; font-size:0.85rem;
    }
    .btn-mini-skip { background: rgba(231,76,60,0.1); color:#e74c3c; }
    .btn-mini-skip:hover { background: rgba(231,76,60,0.2); }

    /* Buscador de canciones */
    .song-search-result {
        display:flex; align-items:center; gap:0.75rem; padding:0.7rem 0.85rem; border-radius:10px;
        cursor:pointer; transition: all 0.15s; border: 1px solid transparent;
    }
    .song-search-result:hover { background: rgba(255,215,0,0.06); border-color: rgba(255,215,0,0.2); }
    .song-search-result.selected { background: rgba(255,215,0,0.12); border-color: rgba(255,215,0,0.4); }
    .song-icon-mini { width:38px; height:38px; border-radius:8px; background: linear-gradient(135deg,#6a0dad,#9b30ff);
        display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }

    .history-mini { display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.04); font-size:0.82rem; }
    .history-mini:last-child { border:none; }

    .pulse-live { animation: pulseLiveDot 1.5s infinite; }
    @keyframes pulseLiveDot { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
</style>

<div class="row g-3">
    <!-- ═══ ESCENARIO EN VIVO ═══ -->
    <div class="col-lg-8">
        <div class="stage-card mb-3" id="stageCard">
            <!-- Contenido dinámico vía JS -->
        </div>

        <!-- Cola en espera -->
        <div class="card-tm glow">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-cinzel mb-0" style="color:var(--oro); font-size:1rem;">
                    📋 Cola en Espera <span class="badge-tm badge-gold ms-1" id="badgeTotalCola">0</span>
                </h6>
                <button class="btn-outline-gold" style="padding:0.35rem 0.8rem; font-size:0.75rem;" onclick="refrescarCola()">🔄 Actualizar</button>
            </div>
            <div id="queueList">
                <div class="text-center py-4" style="color:rgba(240,232,208,0.3);">Cargando...</div>
            </div>
        </div>
    </div>

    <!-- ═══ PANEL LATERAL ═══ -->
    <div class="col-lg-4">
        <!-- Agregar canción -->
        <div class="card-tm glow mb-3">
            <h6 class="font-cinzel mb-3" style="color:var(--oro); font-size:1rem;">➕ Agregar a la Cola</h6>

            <input type="text" id="searchSong" class="form-control mb-2" placeholder="🔍 Buscar canción o artista..."
                style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">

            <div id="songResults" style="max-height:220px; overflow-y:auto; margin-bottom:0.75rem;"></div>

            <div id="selectedSongBox" style="display:none;" class="mb-2 p-2" >
                <div class="badge-tm badge-gold w-100 text-start" style="display:block; padding:0.5rem 0.8rem;">
                    🎵 <span id="selectedSongName"></span>
                    <button type="button" class="btn-close btn-close-tm float-end" style="font-size:0.6rem;" onclick="limpiarSeleccion()"></button>
                </div>
            </div>

            <input type="text" id="cantanteName" class="form-control mb-2" placeholder="Nombre del cantante"
                style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">

            <select id="mesaSelect" class="form-select mb-3" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
                <option value="">Mesa (opcional)</option>
                <?php foreach ($mesas as $m): ?>
                <option value="<?= $m['id'] ?>">Mesa #<?= $m['numero'] ?></option>
                <?php endforeach; ?>
            </select>

            <button class="btn-gold w-100" onclick="agregarACola()" id="btnAgregarCola">
                🎤 Agregar a la Cola
            </button>
        </div>

        <!-- Historial reciente -->
        <div class="card-tm glow">
            <h6 class="font-cinzel mb-3" style="color:var(--oro); font-size:0.95rem;">🕓 Historial Reciente</h6>
            <?php if (empty($historial)): ?>
                <p style="color:rgba(240,232,208,0.3); font-size:0.82rem;" class="text-center py-3">Aún no hay historial</p>
            <?php else: foreach ($historial as $h): ?>
            <div class="history-mini">
                <span>🎶</span>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($h['titulo']) ?></div>
                    <div style="color:rgba(240,232,208,0.4); font-size:0.7rem;"><?= htmlspecialchars($h['artista']) ?></div>
                </div>
                <?php if ($h['aplausos'] > 0): ?><span style="font-size:0.75rem;">👏<?= $h['aplausos'] ?></span><?php endif; ?>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
let cancionSeleccionada = null;
let timeoutBusqueda = null;

// ── Render del escenario ──────────────────────────────────
function renderStage(data) {
    const stage = document.getElementById("stageCard");
    const actual = data.cantando_ahora;

    if (!actual) {
        stage.innerHTML = `
            <div class="stage-empty">
                <div class="icn">🎤</div>
                <div style="font-family:Cinzel,serif; font-size:1.1rem; color:rgba(255,215,0,0.4);">Escenario Libre</div>
                <p style="font-size:0.85rem; margin-top:0.3rem;">Selecciona "Siguiente" para iniciar el show</p>
            </div>`;
        return;
    }

    stage.innerHTML = `
        <div class="now-playing-content">
            <div class="d-flex justify-content-between align-items-start">
                <span class="singer-badge"><span class="pulse-live">🔴</span> EN VIVO · ${actual.cantante_nombre}</span>
                ${actual.mesa_numero ? `<span class="badge-tm badge-info">Mesa #${actual.mesa_numero}</span>` : ""}
            </div>
            <div class="song-title-big">${actual.titulo}</div>
            <div class="song-artist-big">${actual.artista}</div>
            <div class="equalizer">
                <div class="eq-bar"></div><div class="eq-bar"></div><div class="eq-bar"></div>
                <div class="eq-bar"></div><div class="eq-bar"></div><div class="eq-bar"></div><div class="eq-bar"></div>
            </div>
            <div class="control-btns">
                <button class="btn-control btn-next" onclick="siguienteCancion()">⏭️ Siguiente Canción</button>
                <button class="btn-control btn-skip" onclick="saltarCancion(${actual.id})">⏹️ Saltar</button>
                <button class="btn-control btn-skip" style="background:rgba(255,215,0,0.12); color:var(--oro); border-color:rgba(255,215,0,0.3);" onclick="puntuarRapido(${actual.id})">👏 Aplaudir</button>
            </div>
        </div>`;
}

// ── Render de la cola ──────────────────────────────────────
function renderQueue(data) {
    const list = document.getElementById("queueList");
    document.getElementById("badgeTotalCola").textContent = data.total_espera;

    if (!data.en_espera || data.en_espera.length === 0) {
        list.innerHTML = `<div class="text-center py-4" style="color:rgba(240,232,208,0.3);">No hay nadie en cola. ¡Agrega una canción!</div>`;
        return;
    }

    list.innerHTML = data.en_espera.map((item, i) => `
        <div class="queue-item" style="animation-delay:${i * 0.05}s">
            <div class="queue-pos">${i + 1}</div>
            <div class="queue-info">
                <div class="queue-title">${item.titulo}</div>
                <div class="queue-meta">🎙️ ${item.cantante_nombre} · ${item.artista}${item.mesa_numero ? " · Mesa #" + item.mesa_numero : ""}</div>
            </div>
            <div class="queue-actions">
                <button class="btn-icon-mini btn-mini-skip" onclick="saltarCancion(${item.id})" title="Quitar de cola">✕</button>
            </div>
        </div>
    `).join("");
}

// ── Cargar estado completo ──────────────────────────────────
function refrescarCola() {
    fetch("' . BASE_URL . '/api/cola")
        .then(r => r.json())
        .then(data => { renderStage(data); renderQueue(data); })
        .catch(() => mostrarToast("Error al cargar la cola", "danger"));
}

// ── Buscar canciones ─────────────────────────────────────────
document.getElementById("searchSong").addEventListener("input", function() {
    clearTimeout(timeoutBusqueda);
    const q = this.value.trim();
    timeoutBusqueda = setTimeout(() => {
        fetch("' . BASE_URL . '/cola/buscar?q=" + encodeURIComponent(q))
            .then(r => r.json())
            .then(renderSongResults);
    }, 300);
});

function renderSongResults(canciones) {
    const box = document.getElementById("songResults");
    if (canciones.length === 0) {
        box.innerHTML = `<div class="text-center py-2" style="color:rgba(240,232,208,0.3); font-size:0.8rem;">Sin resultados</div>`;
        return;
    }
    box.innerHTML = canciones.slice(0, 8).map(c => `
        <div class="song-search-result" onclick=\'seleccionarCancion(${c.id}, "${c.titulo.replace(/"/g,"&quot;")} - ${c.artista.replace(/"/g,"&quot;")}")\'>
            <div class="song-icon-mini">🎵</div>
            <div style="flex:1; min-width:0;">
                <div style="font-weight:600; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${c.titulo}</div>
                <div style="font-size:0.72rem; color:rgba(240,232,208,0.4);">${c.artista} · ${c.genero || ""}</div>
            </div>
        </div>
    `).join("");
}

function seleccionarCancion(id, nombre) {
    cancionSeleccionada = id;
    document.getElementById("selectedSongName").textContent = nombre;
    document.getElementById("selectedSongBox").style.display = "block";
    document.getElementById("songResults").innerHTML = "";
    document.getElementById("searchSong").value = "";
}

function limpiarSeleccion() {
    cancionSeleccionada = null;
    document.getElementById("selectedSongBox").style.display = "none";
}

// ── Agregar a la cola ─────────────────────────────────────
function agregarACola() {
    if (!cancionSeleccionada) { mostrarToast("Selecciona una canción primero", "warning"); return; }
    const btn = document.getElementById("btnAgregarCola");
    btn.disabled = true; btn.textContent = "Agregando...";

    const fd = new FormData();
    fd.append("cancion_id", cancionSeleccionada);
    fd.append("cantante_nombre", document.getElementById("cantanteName").value || "Anónimo");
    fd.append("mesa_id", document.getElementById("mesaSelect").value);

    fetch("' . BASE_URL . '/cola/agregar", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                mostrarToast("🎤 " + data.mensaje, "success");
                limpiarSeleccion();
                document.getElementById("cantanteName").value = "";
                refrescarCola();
            } else {
                mostrarToast(data.error || "Error al agregar", "danger");
            }
        })
        .finally(() => { btn.disabled = false; btn.innerHTML = "🎤 Agregar a la Cola"; });
}

// ── Controles de escenario ─────────────────────────────────
function siguienteCancion() {
    fetch("' . BASE_URL . '/cola/siguiente", { method: "POST" })
        .then(r => r.json())
        .then(() => { mostrarToast("⏭️ Siguiente canción cargada", "success"); refrescarCola(); });
}

function saltarCancion(id) {
    confirmarAccion({
        titulo: "¿Quitar esta canción?",
        texto: "Se eliminará de la cola de espera.",
        confirmar: "Sí, quitar",
        icono: "warning"
    }).then((result) => {
        if (!result.isConfirmed) return;
        const fd = new FormData(); fd.append("id", id);
        fetch("' . BASE_URL . '/cola/saltar", { method: "POST", body: fd })
            .then(r => r.json())
            .then(() => { mostrarToast("Canción removida", "info"); refrescarCola(); });
    });
}

function puntuarRapido(colaId) {
    const fd = new FormData();
    fd.append("cola_id", colaId);
    fd.append("puntos", 10);
    fd.append("aplauso", "1");
    fetch("' . BASE_URL . '/cola/puntuar", { method: "POST", body: fd })
        .then(() => mostrarToast("👏 ¡Aplauso registrado!", "success"));
}

// ── Auto-refresh cada 5s ───────────────────────────────────
refrescarCola();
setInterval(refrescarCola, 5000);
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>