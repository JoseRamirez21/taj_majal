<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>🎤 Pide tu Canción — Taj Mahal Karaoke</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --oro:#ffd700; --oro-oscuro:#b8860b; --purpura:#6a0dad; --purpura-2:#9b30ff;
            --negro:#0a0a0f; --negro-2:#12121a; --negro-3:#1a1a28; --blanco:#f0e8d0;
        }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color: transparent; }
        body {
            background: var(--negro); color: var(--blanco); font-family:'Raleway',sans-serif;
            min-height: 100vh; min-height: 100dvh;
        }
        .bg-radial {
            position: fixed; inset:0; z-index:0;
            background: radial-gradient(ellipse 80% 50% at 50% 0%, rgba(106,13,173,0.25) 0%, transparent 70%),
                        radial-gradient(ellipse 60% 30% at 50% 100%, rgba(255,215,0,0.06) 0%, transparent 60%), var(--negro);
        }
        .wrap { position:relative; z-index:5; max-width: 480px; margin:0 auto; padding: 1.5rem 1.2rem 2rem; min-height:100vh; min-height:100dvh; display:flex; flex-direction:column; }

        .header-mob { text-align:center; padding: 1rem 0 1.5rem; }
        .logo-mob { font-size: 2.4rem; filter: drop-shadow(0 0 10px rgba(255,215,0,0.5)); }
        .title-mob { font-family:'Cinzel',serif; font-weight:900; font-size:1.4rem; letter-spacing:0.1em;
            background: linear-gradient(135deg,#ffd700,#fff8dc,#b8860b); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .sub-mob { font-size:0.75rem; color: rgba(255,215,0,0.5); letter-spacing:0.2em; text-transform:uppercase; margin-top:0.2rem; }
        .mesa-tag { display:inline-block; margin-top:0.8rem; background: rgba(255,215,0,0.1); border:1px solid rgba(255,215,0,0.3);
            border-radius: 20px; padding: 0.35rem 1rem; font-size:0.85rem; font-weight:700; color: var(--oro); }

        .now-playing-mob {
            background: linear-gradient(135deg, rgba(106,13,173,0.2), rgba(18,18,26,0.9));
            border:1px solid rgba(255,215,0,0.2); border-radius:18px; padding:1.2rem; margin-bottom:1.2rem; text-align:center;
        }
        .np-label { font-size:0.7rem; color:#ff6b6b; letter-spacing:0.15em; text-transform:uppercase; display:flex; align-items:center; justify-content:center; gap:0.4rem; }
        .live-dot-mob { width:7px; height:7px; background:#e74c3c; border-radius:50%; animation: pulseLiveM 1.3s infinite; }
        @keyframes pulseLiveM { 0%,100%{opacity:1;} 50%{opacity:0.3;} }
        .np-song { font-family:'Cinzel',serif; font-weight:700; font-size:1.1rem; margin-top:0.5rem; }
        .np-artist { font-size:0.8rem; color:rgba(240,232,208,0.5); }
        .np-empty { color: rgba(240,232,208,0.3); font-size:0.85rem; padding: 0.5rem 0; }

        .search-box { position:relative; margin-bottom:1rem; }
        .search-input {
            width:100%; padding: 0.9rem 1rem 0.9rem 2.8rem; border-radius:14px; border:1px solid rgba(255,215,0,0.2);
            background: rgba(255,255,255,0.04); color: var(--blanco); font-size:1rem; outline:none;
        }
        .search-input:focus { border-color: rgba(255,215,0,0.5); background: rgba(255,215,0,0.05); }
        .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); font-size:1.1rem; opacity:0.5; }

        .results-mob { display:flex; flex-direction:column; gap:0.6rem; margin-bottom:1.2rem; max-height: 260px; overflow-y:auto; }
        .song-result-mob {
            display:flex; align-items:center; gap:0.8rem; background: rgba(255,255,255,0.025);
            border:1px solid rgba(255,255,255,0.06); border-radius:14px; padding:0.8rem 1rem; cursor:pointer; transition: all 0.2s;
        }
        .song-result-mob:active, .song-result-mob.selected { background: rgba(255,215,0,0.1); border-color: rgba(255,215,0,0.4); }
        .song-icon-mob { width:42px; height:42px; border-radius:10px; background: linear-gradient(135deg,#6a0dad,#9b30ff);
            display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
        .song-info-mob { flex:1; min-width:0; }
        .song-name-mob { font-weight:700; font-size:0.92rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .song-artist-mob { font-size:0.75rem; color: rgba(240,232,208,0.45); }

        .form-section { display:none; }
        .form-section.show { display:block; animation: fadeInUp 0.4s ease; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(15px);} to { opacity:1; transform:translateY(0);} }

        .selected-card {
            background: linear-gradient(135deg, rgba(255,215,0,0.12), rgba(255,215,0,0.03));
            border:1px solid rgba(255,215,0,0.35); border-radius:16px; padding:1rem; margin-bottom:1rem;
            display:flex; align-items:center; gap:0.8rem;
        }
        .selected-card .ic { font-size:1.6rem; }
        .selected-card .change-btn { margin-left:auto; background:none; border:none; color: rgba(255,215,0,0.6); font-size:0.78rem; text-decoration:underline; cursor:pointer; }

        .name-input {
            width:100%; padding: 0.9rem 1rem; border-radius:14px; border:1px solid rgba(255,215,0,0.2);
            background: rgba(255,255,255,0.04); color: var(--blanco); font-size:1rem; outline:none; margin-bottom:1rem;
        }
        .name-input:focus { border-color: rgba(255,215,0,0.5); }

        .btn-submit-mob {
            width:100%; padding:1rem; border:none; border-radius:14px; font-family:'Cinzel',serif; font-weight:700;
            font-size:1rem; letter-spacing:0.1em; text-transform:uppercase; cursor:pointer;
            background: linear-gradient(135deg,#b8860b,#ffd700,#b8860b); background-size:200% auto; color:#0a0a0f;
            box-shadow: 0 4px 20px rgba(255,215,0,0.3); transition: all 0.2s;
        }
        .btn-submit-mob:active { transform: scale(0.98); }
        .btn-submit-mob:disabled { opacity:0.5; }

        .queue-preview { margin-top: 1.5rem; }
        .queue-preview-title { font-size:0.85rem; color: rgba(255,215,0,0.6); margin-bottom:0.7rem; font-weight:700; }
        .queue-mini-item { display:flex; align-items:center; gap:0.6rem; padding:0.55rem 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:0.82rem; }
        .queue-mini-pos { width:24px; height:24px; border-radius:7px; background:rgba(255,215,0,0.1); color:var(--oro);
            display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800; flex-shrink:0; }

        .footer-mob { text-align:center; margin-top:auto; padding-top:1.5rem; font-size:0.7rem; color: rgba(240,232,208,0.25); }
    </style>
</head>
<body>
<div class="bg-radial"></div>

<div class="wrap">
    <div class="header-mob">
        <div class="logo-mob">🕌</div>
        <div class="title-mob">TAJ MAHAL</div>
        <div class="sub-mob">Pide tu Canción</div>
        <?php if ($mesaNumero): ?>
        <div class="mesa-tag">🪑 Mesa #<?= $mesaNumero ?></div>
        <?php endif; ?>
    </div>

    <!-- Now playing -->
    <div class="now-playing-mob" id="npBox">
        <div class="np-label"><span class="live-dot-mob"></span> Sonando ahora</div>
        <div id="npContent"><div class="np-empty">Cargando...</div></div>
    </div>

    <!-- Búsqueda -->
    <div id="searchSection">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" class="search-input" placeholder="Busca tu canción o artista...">
        </div>
        <div class="results-mob" id="resultsBox"></div>
    </div>

    <!-- Formulario (aparece tras seleccionar) -->
    <div class="form-section" id="formSection">
        <div class="selected-card">
            <span class="ic">🎵</span>
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; font-size:0.92rem;" id="selSongName"></div>
                <div style="font-size:0.75rem; color:rgba(240,232,208,0.5);" id="selSongArtist"></div>
            </div>
            <button class="change-btn" onclick="cambiarSeleccion()">Cambiar</button>
        </div>

        <input type="text" id="nombreInput" class="name-input" placeholder="✍️ Tu nombre o apodo" maxlength="40">

        <button class="btn-submit-mob" onclick="enviarPedido()" id="btnEnviar">🎤 Agregar a la Cola</button>
    </div>

    <!-- Vista previa de cola -->
    <div class="queue-preview">
        <div class="queue-preview-title">📋 Próximos en cola</div>
        <div id="queuePreviewList"></div>
    </div>

    <div class="footer-mob">🕌 Taj Mahal Karaoke Bar · Ayacucho, Perú</div>
</div>

<script>
const MESA_ID = <?= $mesaId ? (int)$mesaId : 'null' ?>;
let cancionSeleccionada = null;
let timeoutSearch = null;

// ── Buscar canciones ───────────────────────────────────
document.getElementById("searchInput").addEventListener("input", function() {
    clearTimeout(timeoutSearch);
    const q = this.value.trim();
    timeoutSearch = setTimeout(() => {
        fetch("<?= BASE_URL ?>/cola/buscar?q=" + encodeURIComponent(q))
            .then(r => r.json())
            .then(renderResultados);
    }, 300);
});

function renderResultados(canciones) {
    const box = document.getElementById("resultsBox");
    if (canciones.length === 0) {
        box.innerHTML = `<div style="text-align:center; padding:1rem; color:rgba(240,232,208,0.3); font-size:0.85rem;">Sin resultados</div>`;
        return;
    }
    box.innerHTML = canciones.slice(0, 10).map(c => `
        <div class="song-result-mob" onclick='seleccionar(${c.id}, ${JSON.stringify(c.titulo)}, ${JSON.stringify(c.artista)})'>
            <div class="song-icon-mob">🎵</div>
            <div class="song-info-mob">
                <div class="song-name-mob">${c.titulo}</div>
                <div class="song-artist-mob">${c.artista}</div>
            </div>
        </div>
    `).join("");
}

function seleccionar(id, titulo, artista) {
    cancionSeleccionada = id;
    document.getElementById("selSongName").textContent = titulo;
    document.getElementById("selSongArtist").textContent = artista;
    document.getElementById("searchSection").style.display = "none";
    document.getElementById("formSection").classList.add("show");
    document.getElementById("nombreInput").focus();
}

function cambiarSeleccion() {
    cancionSeleccionada = null;
    document.getElementById("formSection").classList.remove("show");
    document.getElementById("searchSection").style.display = "block";
    document.getElementById("searchInput").value = "";
    document.getElementById("resultsBox").innerHTML = "";
}

// ── Enviar pedido ──────────────────────────────────────
function enviarPedido() {
    const nombre = document.getElementById("nombreInput").value.trim();
    if (!nombre) {
        Swal.fire({ text: "Escribe tu nombre para continuar", icon: "warning", background:"#1a1a28", color:"#f0e8d0", confirmButtonColor:"#ffd700" });
        return;
    }

    const btn = document.getElementById("btnEnviar");
    btn.disabled = true; btn.textContent = "Enviando...";

    const fd = new FormData();
    fd.append("cancion_id", cancionSeleccionada);
    fd.append("cantante_nombre", nombre);
    if (MESA_ID) fd.append("mesa_id", MESA_ID);

    fetch("<?= BASE_URL ?>/cola/agregar", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                Swal.fire({
                    title: "¡Listo! 🎉", text: "Tu canción fue agregada a la cola",
                    icon: "success", confirmButtonText: "Genial",
                    background:"#1a1a28", color:"#f0e8d0", confirmButtonColor:"#ffd700"
                }).then(() => {
                    cambiarSeleccion();
                    document.getElementById("nombreInput").value = "";
                    cargarEstado();
                });
            } else {
                Swal.fire({ text: data.error || "Error al enviar", icon: "error", background:"#1a1a28", color:"#f0e8d0", confirmButtonColor:"#ffd700" });
            }
        })
        .finally(() => { btn.disabled = false; btn.innerHTML = "🎤 Agregar a la Cola"; });
}

// ── Estado en vivo (now playing + cola preview) ─────────
function cargarEstado() {
    fetch("<?= BASE_URL ?>/api/cola")
        .then(r => r.json())
        .then(data => {
            const np = document.getElementById("npContent");
            if (data.cantando_ahora) {
                np.innerHTML = `<div class="np-song">${data.cantando_ahora.titulo}</div><div class="np-artist">${data.cantando_ahora.artista} · 🎙️ ${data.cantando_ahora.cantante_nombre}</div>`;
            } else {
                np.innerHTML = `<div class="np-empty">Escenario libre — ¡anímate!</div>`;
            }

            const qList = document.getElementById("queuePreviewList");
            if (!data.en_espera || data.en_espera.length === 0) {
                qList.innerHTML = `<div style="text-align:center; padding:1rem; color:rgba(240,232,208,0.3); font-size:0.82rem;">Cola vacía</div>`;
            } else {
                qList.innerHTML = data.en_espera.slice(0,5).map((item, i) => `
                    <div class="queue-mini-item">
                        <div class="queue-mini-pos">${i+1}</div>
                        <div style="flex:1; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${item.titulo} — ${item.cantante_nombre}</div>
                    </div>
                `).join("");
            }
        });
}

cargarEstado();
setInterval(cargarEstado, 6000);
</script>
</body>
</html>