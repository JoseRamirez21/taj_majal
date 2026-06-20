<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🕌 Taj Mahal Karaoke — En Vivo</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Raleway:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --oro: #ffd700; --oro-oscuro: #b8860b; --purpura: #6a0dad; --purpura-2: #9b30ff;
            --negro: #0a0a0f; --negro-2: #12121a; --blanco: #f0e8d0;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: var(--negro); color: var(--blanco); font-family: 'Raleway', sans-serif;
            min-height: 100vh; overflow: hidden; position: relative;
        }

        #particles-canvas { position: fixed; inset: 0; z-index: 0; pointer-events: none; }

        .bg-radial {
            position: fixed; inset: 0; z-index:0;
            background:
                radial-gradient(ellipse 70% 50% at 50% 0%, rgba(106,13,173,0.3) 0%, transparent 70%),
                radial-gradient(ellipse 50% 40% at 10% 100%, rgba(255,215,0,0.1) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 100%, rgba(106,13,173,0.15) 0%, transparent 60%),
                var(--negro);
        }

        .stage-light { position: fixed; top: -100px; width: 3px; height: 700px; transform-origin: top center; z-index: 1; pointer-events: none; }
        .stage-light::after {
            content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 400px; height: 100%; clip-path: polygon(40% 0%, 60% 0%, 100% 100%, 0% 100%); opacity: 0.07;
        }
        .sl1 { left: 5%;  animation: swingLight 9s ease-in-out infinite; }
        .sl2 { left: 25%; animation: swingLight 7s ease-in-out infinite reverse; }
        .sl3 { left: 50%; animation: swingLight 8s ease-in-out infinite 1s; }
        .sl4 { left: 75%; animation: swingLight 10s ease-in-out infinite 2s; }
        .sl5 { left: 95%; animation: swingLight 6s ease-in-out infinite 0.5s; }
        .sl1::after, .sl5::after { background: linear-gradient(to bottom, rgba(255,215,0,0.9), transparent); }
        .sl2::after, .sl4::after { background: linear-gradient(to bottom, rgba(154,48,255,0.9), transparent); }
        .sl3::after { background: linear-gradient(to bottom, rgba(255,100,0,0.7), transparent); }
        @keyframes swingLight { 0%,100% { transform: rotate(-30deg); } 50% { transform: rotate(30deg); } }

        .tv-container { position: relative; z-index: 10; min-height: 100vh; display: flex; flex-direction: column; padding: 2.5rem 3rem; }

        /* Header */
        .tv-header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem; }
        .tv-logo { display:flex; align-items:center; gap:1rem; }
        .tv-logo-icon { font-size: 3rem; filter: drop-shadow(0 0 15px rgba(255,215,0,0.6)); }
        .tv-logo-text { font-family:'Cinzel',serif; font-weight:900; font-size: 2rem; letter-spacing:0.1em;
            background: linear-gradient(135deg,#ffd700,#fff8dc,#b8860b,#ffd700);
            background-size: 200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            animation: shimmer 5s linear infinite; }
        @keyframes shimmer { 0%{background-position:0% center;} 100%{background-position:200% center;} }
        .tv-logo-sub { font-size: 0.85rem; letter-spacing:0.3em; color: rgba(255,215,0,0.5); text-transform:uppercase; }

        .tv-clock { text-align:right; }
        .tv-clock .time { font-family:'Cinzel',serif; font-size: 2.2rem; font-weight:800; color: var(--oro); }
        .tv-clock .date { font-size: 0.95rem; color: rgba(240,232,208,0.5); text-transform: capitalize; }

        /* Main stage */
        .tv-main { flex:1; display:grid; grid-template-columns: 2fr 1fr; gap: 2rem; min-height:0; }

        .stage-area {
            background: linear-gradient(135deg, rgba(106,13,173,0.25), rgba(18,18,26,0.9));
            border: 2px solid rgba(255,215,0,0.25); border-radius: 28px; padding: 3rem;
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            position: relative; overflow: hidden;
        }
        .stage-area::before {
            content:''; position:absolute; inset:0;
            background: radial-gradient(circle at 30% 20%, rgba(255,215,0,0.12), transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(154,48,255,0.18), transparent 50%);
            animation: rotateGlow 12s linear infinite;
        }
        @keyframes rotateGlow { from { transform: rotate(0deg);} to { transform: rotate(360deg);} }

        .singing-badge {
            position: relative; z-index:2; display:inline-flex; align-items:center; gap:0.6rem;
            background: rgba(231,76,60,0.15); border: 1px solid rgba(231,76,60,0.4); border-radius: 30px;
            padding: 0.5rem 1.4rem; font-size: 1rem; font-weight:700; color:#ff6b6b; margin-bottom: 1.5rem;
        }
        .live-dot-big { width: 11px; height: 11px; background:#e74c3c; border-radius:50%; animation: pulseLive 1.3s infinite; }
        @keyframes pulseLive { 0%,100%{opacity:1; box-shadow:0 0 8px #e74c3c;} 50%{opacity:0.3;} }

        .mic-icon-tv { font-size: 5rem; position:relative; z-index:2; animation: micFloat 2s ease-in-out infinite; margin-bottom:1rem; }
        @keyframes micFloat { 0%,100%{transform:translateY(0) scale(1);} 50%{transform:translateY(-12px) scale(1.05);} }

        .song-title-tv { position:relative; z-index:2; font-family:'Cinzel',serif; font-size: 3.2rem; font-weight:800;
            color:#fff; text-align:center; line-height:1.2; text-shadow: 0 0 30px rgba(255,215,0,0.3); margin-bottom:0.5rem; }
        .song-artist-tv { position:relative; z-index:2; font-size: 1.4rem; color: rgba(240,232,208,0.6); margin-bottom:1.5rem; }
        .singer-name-tv { position:relative; z-index:2; display:inline-flex; align-items:center; gap:0.6rem;
            background: rgba(255,215,0,0.12); border:1px solid rgba(255,215,0,0.35); border-radius:30px;
            padding:0.7rem 1.8rem; font-size: 1.3rem; font-weight:700; color: var(--oro); }

        .equalizer-tv { display:flex; align-items:flex-end; gap:6px; height: 60px; margin-top: 2rem; position:relative; z-index:2; }
        .eq-bar-tv { width: 10px; background: linear-gradient(180deg, #ffd700, #9b30ff); border-radius: 4px; animation: eqBounceTv 0.8s ease-in-out infinite; }
        .eq-bar-tv:nth-child(1){height:35%; animation-delay:0s;}
        .eq-bar-tv:nth-child(2){height:75%; animation-delay:.1s;}
        .eq-bar-tv:nth-child(3){height:50%; animation-delay:.2s;}
        .eq-bar-tv:nth-child(4){height:100%; animation-delay:.3s;}
        .eq-bar-tv:nth-child(5){height:60%; animation-delay:.15s;}
        .eq-bar-tv:nth-child(6){height:30%; animation-delay:.25s;}
        .eq-bar-tv:nth-child(7){height:85%; animation-delay:.05s;}
        .eq-bar-tv:nth-child(8){height:45%; animation-delay:.35s;}
        @keyframes eqBounceTv { 0%,100% { transform: scaleY(0.3); } 50% { transform: scaleY(1); } }

        .stage-empty-tv { text-align:center; position:relative; z-index:2; }
        .stage-empty-tv .icn { font-size: 5rem; opacity:0.3; margin-bottom: 1rem; }
        .stage-empty-tv .txt { font-family:'Cinzel',serif; font-size: 1.8rem; color: rgba(255,215,0,0.4); }
        .stage-empty-tv .sub { font-size: 1.1rem; color: rgba(240,232,208,0.3); margin-top: 0.5rem; }

        /* Queue sidebar */
        .queue-tv {
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,215,0,0.12); border-radius: 24px;
            padding: 1.8rem; display:flex; flex-direction:column; overflow:hidden;
        }
        .queue-tv-title { font-family:'Cinzel',serif; font-size: 1.4rem; color: var(--oro); margin-bottom: 1.2rem;
            display:flex; align-items:center; gap:0.5rem; }
        .queue-tv-list { flex:1; overflow-y: auto; display:flex; flex-direction:column; gap:0.8rem; }
        .queue-tv-item {
            background: rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:14px;
            padding: 1rem 1.2rem; display:flex; align-items:center; gap:1rem; animation: queueInTv 0.4s ease both;
        }
        @keyframes queueInTv { from { opacity:0; transform: translateX(20px);} to { opacity:1; transform: translateX(0);} }
        .queue-tv-pos {
            width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, rgba(255,215,0,0.2), rgba(255,215,0,0.05));
            color: var(--oro); display:flex; align-items:center; justify-content:center; font-weight:900; font-size:1.2rem;
            flex-shrink:0; border:1px solid rgba(255,215,0,0.25);
        }
        .queue-tv-info { flex:1; min-width:0; }
        .queue-tv-song { font-weight:700; font-size: 1.05rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .queue-tv-meta { font-size: 0.85rem; color: rgba(240,232,208,0.45); margin-top:0.15rem; }
        .queue-tv-empty { text-align:center; padding: 3rem 1rem; color: rgba(240,232,208,0.25); font-size: 1.1rem; }

        /* Footer ticker */
        .tv-footer {
            margin-top: 2rem; padding-top: 1.2rem; border-top: 1px solid rgba(255,215,0,0.1);
            display:flex; justify-content:space-between; align-items:center; font-size: 0.95rem;
        }
        .qr-hint { display:flex; align-items:center; gap:0.8rem; color: rgba(255,215,0,0.6); }
        .qr-hint .box { width:38px; height:38px; background:rgba(255,215,0,0.1); border:1px solid rgba(255,215,0,0.3);
            border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
        .social-tv { color: rgba(240,232,208,0.4); }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,215,0,0.3); border-radius: 10px; }
    </style>
</head>
<body>

<div class="bg-radial"></div>
<canvas id="particles-canvas"></canvas>
<div class="stage-light sl1"></div>
<div class="stage-light sl2"></div>
<div class="stage-light sl3"></div>
<div class="stage-light sl4"></div>
<div class="stage-light sl5"></div>

<div class="tv-container">
    <!-- Header -->
    <div class="tv-header">
        <div class="tv-logo">
            <span class="tv-logo-icon">🕌</span>
            <div>
                <div class="tv-logo-text">TAJ MAHAL</div>
                <div class="tv-logo-sub">Karaoke Bar</div>
            </div>
        </div>
        <div class="tv-clock">
            <div class="time" id="tvClock">--:--:--</div>
            <div class="date" id="tvDate">cargando...</div>
        </div>
    </div>

    <!-- Main -->
    <div class="tv-main">
        <div class="stage-area" id="tvStage">
            <div class="stage-empty-tv">
                <div class="icn">🎤</div>
                <div class="txt">Cargando escenario...</div>
            </div>
        </div>

        <div class="queue-tv">
            <div class="queue-tv-title">📋 Próximos en Cola</div>
            <div class="queue-tv-list" id="tvQueueList"></div>
        </div>
    </div>

    <!-- Footer -->
    <div class="tv-footer">
        <div class="qr-hint">
            <div class="box">📱</div>
            <span>Escanea el QR en tu mesa para pedir tu canción</span>
        </div>
        <div class="social-tv">🕌 Taj Mahal Karaoke Bar · Ayacucho, Perú</div>
    </div>
</div>

<script>
// ── Reloj ──────────────────────────────────────────────
function actualizarRelojTv() {
    const ahora = new Date();
    const h = String(ahora.getHours()).padStart(2,'0');
    const m = String(ahora.getMinutes()).padStart(2,'0');
    const s = String(ahora.getSeconds()).padStart(2,'0');
    document.getElementById('tvClock').textContent = `${h}:${m}:${s}`;
    const dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    document.getElementById('tvDate').textContent = `${dias[ahora.getDay()]}, ${ahora.getDate()} de ${meses[ahora.getMonth()]}`;
}
actualizarRelojTv();
setInterval(actualizarRelojTv, 1000);

// ── Partículas ─────────────────────────────────────────
const canvas = document.getElementById('particles-canvas');
const ctx = canvas.getContext('2d');
let W, H, particles = [];
function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
window.addEventListener('resize', resize);
resize();
function rnd(a,b){ return a + Math.random()*(b-a); }
function createParticle() {
    const colors = ['rgba(255,215,0,', 'rgba(255,255,200,', 'rgba(154,48,255,', 'rgba(200,150,255,'];
    return { x: rnd(0,W), y: rnd(0,H), r: rnd(0.5,2.8), color: colors[Math.floor(Math.random()*colors.length)],
        alpha: rnd(0.1,0.7), speedX: rnd(-0.3,0.3), speedY: rnd(-0.6,-0.1), pulse: rnd(0,Math.PI*2), pulseSpeed: rnd(0.01,0.03) };
}
for (let i=0;i<150;i++) particles.push(createParticle());
function animateParticles() {
    ctx.clearRect(0,0,W,H);
    particles.forEach(p => {
        p.pulse += p.pulseSpeed;
        const alpha = p.alpha * (0.6 + 0.4*Math.sin(p.pulse));
        ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
        ctx.fillStyle = p.color + alpha + ')'; ctx.fill();
        p.x += p.speedX; p.y += p.speedY;
        if (p.y<-5) p.y=H+5; if (p.x<-5) p.x=W+5; if (p.x>W+5) p.x=-5;
    });
    requestAnimationFrame(animateParticles);
}
animateParticles();

// ── Datos en vivo ──────────────────────────────────────
function renderStageTv(data) {
    const stage = document.getElementById('tvStage');
    const actual = data.cantando_ahora;

    if (!actual) {
        stage.innerHTML = `
            <div class="stage-empty-tv">
                <div class="icn">🎤</div>
                <div class="txt">Escenario Libre</div>
                <div class="sub">¡Anímate a cantar! Pide tu canción en barra</div>
            </div>`;
        return;
    }

    stage.innerHTML = `
        <div class="singing-badge"><span class="live-dot-big"></span> EN VIVO AHORA</div>
        <div class="mic-icon-tv">🎤</div>
        <div class="song-title-tv">${actual.titulo}</div>
        <div class="song-artist-tv">${actual.artista}</div>
        <div class="singer-name-tv">🎙️ ${actual.cantante_nombre}</div>
        <div class="equalizer-tv">
            <div class="eq-bar-tv"></div><div class="eq-bar-tv"></div><div class="eq-bar-tv"></div><div class="eq-bar-tv"></div>
            <div class="eq-bar-tv"></div><div class="eq-bar-tv"></div><div class="eq-bar-tv"></div><div class="eq-bar-tv"></div>
        </div>`;
}

function renderQueueTv(data) {
    const list = document.getElementById('tvQueueList');
    if (!data.en_espera || data.en_espera.length === 0) {
        list.innerHTML = `<div class="queue-tv-empty">🎶<br>No hay nadie en cola<br><span style="font-size:0.9rem;">¡Sé el primero!</span></div>`;
        return;
    }
    list.innerHTML = data.en_espera.slice(0, 6).map((item, i) => `
        <div class="queue-tv-item" style="animation-delay:${i*0.08}s">
            <div class="queue-tv-pos">${i+1}</div>
            <div class="queue-tv-info">
                <div class="queue-tv-song">${item.titulo}</div>
                <div class="queue-tv-meta">🎙️ ${item.cantante_nombre}</div>
            </div>
        </div>
    `).join("");
}

function refrescarTv() {
    fetch("<?= BASE_URL ?>/api/cola")
        .then(r => r.json())
        .then(data => { renderStageTv(data); renderQueueTv(data); })
        .catch(() => {});
}

refrescarTv();
setInterval(refrescarTv, 4000);
</script>
</body>
</html>