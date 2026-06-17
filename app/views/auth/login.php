<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taj Mahal Karaoke Bar — Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --oro:        #ffd700;
            --oro-oscuro: #b8860b;
            --purpura:    #6a0dad;
            --purpura-2:  #9b30ff;
            --negro:      #0a0a0f;
            --negro-2:    #12121a;
            --negro-3:    #1a1a28;
            --blanco:     #f0e8d0;
            --glow:       0 0 20px rgba(255,215,0,0.4);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: var(--negro);
            font-family: 'Raleway', sans-serif;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Canvas de partículas ── */
        #particles-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        /* ── Luces de escenario ── */
        .stage-light {
            position: fixed;
            top: -100px;
            width: 2px;
            height: 500px;
            transform-origin: top center;
            z-index: 1;
            pointer-events: none;
        }
        .stage-light::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
            height: 100%;
            clip-path: polygon(40% 0%, 60% 0%, 100% 100%, 0% 100%);
            opacity: 0.06;
        }
        .sl1 { left: 10%;  animation: swingLight 8s ease-in-out infinite; }
        .sl2 { left: 30%;  animation: swingLight 6s ease-in-out infinite reverse; }
        .sl3 { left: 55%;  animation: swingLight 7s ease-in-out infinite 1s; }
        .sl4 { left: 75%;  animation: swingLight 9s ease-in-out infinite 2s; }
        .sl5 { left: 90%;  animation: swingLight 5s ease-in-out infinite 0.5s; }

        .sl1::after { background: linear-gradient(to bottom, rgba(255,215,0,0.8), transparent); }
        .sl2::after { background: linear-gradient(to bottom, rgba(154,48,255,0.8), transparent); }
        .sl3::after { background: linear-gradient(to bottom, rgba(255,100,0,0.6), transparent); }
        .sl4::after { background: linear-gradient(to bottom, rgba(0,200,255,0.6), transparent); }
        .sl5::after { background: linear-gradient(to bottom, rgba(255,215,0,0.8), transparent); }

        @keyframes swingLight {
            0%, 100% { transform: rotate(-25deg); }
            50%       { transform: rotate(25deg);  }
        }

        /* ── Fondo con degradado radial ── */
        .bg-radial {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 40% at 50% 0%, rgba(106,13,173,0.25) 0%, transparent 70%),
                radial-gradient(ellipse 40% 30% at 20% 100%, rgba(255,215,0,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 40% 30% at 80% 100%, rgba(106,13,173,0.12) 0%, transparent 60%),
                var(--negro);
            z-index: 0;
        }

        /* ── Tarjeta de login ── */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 1rem;
            animation: fadeInUp 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        .login-card {
            background: linear-gradient(145deg, rgba(26,26,40,0.95), rgba(18,18,26,0.98));
            border: 1px solid rgba(255,215,0,0.2);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow:
                0 0 0 1px rgba(255,215,0,0.05),
                0 25px 60px rgba(0,0,0,0.7),
                0 0 80px rgba(106,13,173,0.15),
                inset 0 1px 0 rgba(255,215,0,0.1);
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        /* Línea dorada superior */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 10%; right: 10%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--oro), transparent);
            border-radius: 0 0 2px 2px;
        }

        /* Brillo esquina */
        .login-card::after {
            content: '';
            position: absolute;
            top: -50%; right: -50%;
            width: 100%; height: 100%;
            background: radial-gradient(circle, rgba(255,215,0,0.03) 0%, transparent 60%);
            pointer-events: none;
        }

        /* ── Logo ── */
        .logo-area {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            position: relative;
            animation: pulseGlow 3s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { filter: drop-shadow(0 0 8px rgba(255,215,0,0.5));  }
            50%       { filter: drop-shadow(0 0 20px rgba(255,215,0,0.9)); }
        }

        .logo-icon svg { width: 100%; height: 100%; }

        .logo-title {
            font-family: 'Cinzel', serif;
            font-weight: 900;
            font-size: 1.8rem;
            letter-spacing: 0.15em;
            background: linear-gradient(135deg, #ffd700 0%, #fff8dc 40%, #b8860b 70%, #ffd700 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 4s linear infinite;
            line-height: 1.1;
        }

        @keyframes shimmer {
            0%   { background-position: 0% center;   }
            100% { background-position: 200% center; }
        }

        .logo-subtitle {
            font-family: 'Raleway', sans-serif;
            font-size: 0.7rem;
            letter-spacing: 0.4em;
            color: rgba(255,215,0,0.5);
            text-transform: uppercase;
            margin-top: 0.3rem;
        }

        /* ── Formulario ── */
        .form-label {
            color: rgba(240,232,208,0.7);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .form-control-tm {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,215,0,0.15);
            border-radius: 10px;
            color: var(--blanco);
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            font-family: 'Raleway', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 100%;
            outline: none;
        }

        .form-control-tm::placeholder { color: rgba(240,232,208,0.25); }

        .form-control-tm:focus {
            border-color: rgba(255,215,0,0.5);
            background: rgba(255,215,0,0.05);
            box-shadow: 0 0 0 3px rgba(255,215,0,0.08), 0 0 15px rgba(255,215,0,0.1);
            color: var(--blanco);
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            pointer-events: none;
            z-index: 2;
            opacity: 0.5;
        }

        .toggle-pass {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,215,0,0.4);
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            transition: color 0.2s;
            z-index: 2;
        }
        .toggle-pass:hover { color: var(--oro); }

        /* ── Botón ── */
        .btn-tm {
            width: 100%;
            padding: 0.85rem;
            border: none;
            border-radius: 10px;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #b8860b 0%, #ffd700 50%, #b8860b 100%);
            background-size: 200% auto;
            color: #0a0a0f;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255,215,0,0.3);
            margin-top: 0.5rem;
        }

        .btn-tm:hover {
            background-position: right center;
            box-shadow: 0 6px 30px rgba(255,215,0,0.5);
            transform: translateY(-1px);
        }

        .btn-tm:active { transform: translateY(0); }

        .btn-tm .btn-ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }

        /* ── Alerta de error ── */
        .alert-tm {
            background: rgba(220,53,69,0.15);
            border: 1px solid rgba(220,53,69,0.3);
            border-left: 3px solid #dc3545;
            border-radius: 8px;
            color: #ff8a8a;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: shakeX 0.5s ease;
        }

        @keyframes shakeX {
            0%,100% { transform: translateX(0);   }
            20%,60% { transform: translateX(-6px); }
            40%,80% { transform: translateX(6px);  }
        }

        /* ── Separador ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0 1rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,215,0,0.1);
        }
        .divider span {
            font-size: 0.7rem;
            letter-spacing: 0.2em;
            color: rgba(255,215,0,0.3);
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* ── Footer de la tarjeta ── */
        .card-footer-tm {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255,215,0,0.08);
        }
        .card-footer-tm p {
            font-size: 0.7rem;
            color: rgba(240,232,208,0.25);
            letter-spacing: 0.1em;
        }
        .card-footer-tm strong {
            color: rgba(255,215,0,0.4);
        }

        /* ── Credenciales demo ── */
        .demo-hint {
            background: rgba(106,13,173,0.15);
            border: 1px solid rgba(154,48,255,0.2);
            border-radius: 8px;
            padding: 0.6rem 0.9rem;
            margin-top: 0.75rem;
        }
        .demo-hint p {
            font-size: 0.72rem;
            color: rgba(200,170,255,0.7);
            margin: 0;
            letter-spacing: 0.05em;
        }
        .demo-hint code {
            color: rgba(255,215,0,0.8);
            font-size: 0.75rem;
            background: rgba(255,215,0,0.06);
            padding: 0 4px;
            border-radius: 3px;
        }

        /* ── Nota del sistema ── */
        .note-version {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.65rem;
            color: rgba(240,232,208,0.15);
            letter-spacing: 0.2em;
        }

        /* ── Loading spinner en botón ── */
        .btn-tm .spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(0,0,0,0.3);
            border-top-color: #000;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-tm.loading .btn-text { display: none; }
        .btn-tm.loading .spinner  { display: block; }

        @media (max-width: 480px) {
            .login-card { padding: 2rem 1.5rem; }
            .logo-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- Fondo -->
<div class="bg-radial"></div>
<canvas id="particles-canvas"></canvas>

<!-- Luces de escenario -->
<div class="stage-light sl1"></div>
<div class="stage-light sl2"></div>
<div class="stage-light sl3"></div>
<div class="stage-light sl4"></div>
<div class="stage-light sl5"></div>

<!-- Tarjeta de Login -->
<div class="login-wrapper">
    <div class="login-card">

        <!-- Logo -->
        <div class="logo-area">
            <div class="logo-icon">
                <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                    <!-- Cúpula estilo taj mahal -->
                    <defs>
                        <radialGradient id="domeGrad" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#ffd700"/>
                            <stop offset="100%" stop-color="#b8860b"/>
                        </radialGradient>
                        <linearGradient id="bodyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffd700" stop-opacity="0.9"/>
                            <stop offset="100%" stop-color="#8B6914"/>
                        </linearGradient>
                    </defs>
                    <!-- Base -->
                    <rect x="12" y="52" width="56" height="22" rx="2" fill="url(#bodyGrad)" opacity="0.9"/>
                    <!-- Arcos centrales -->
                    <path d="M30 74 Q30 60 40 56 Q50 60 50 74 Z" fill="rgba(0,0,0,0.3)"/>
                    <path d="M14 74 Q14 64 20 62 Q26 64 26 74 Z" fill="rgba(0,0,0,0.2)"/>
                    <path d="M54 74 Q54 64 60 62 Q66 64 66 74 Z" fill="rgba(0,0,0,0.2)"/>
                    <!-- Cuerpo central -->
                    <rect x="28" y="40" width="24" height="14" rx="1" fill="url(#bodyGrad)"/>
                    <!-- Cúpulas secundarias -->
                    <ellipse cx="20" cy="48" rx="8" ry="10" fill="url(#domeGrad)" opacity="0.8"/>
                    <ellipse cx="60" cy="48" rx="8" ry="10" fill="url(#domeGrad)" opacity="0.8"/>
                    <!-- Cúpula principal -->
                    <ellipse cx="40" cy="32" rx="12" ry="14" fill="url(#domeGrad)"/>
                    <!-- Punta -->
                    <line x1="40" y1="18" x2="40" y2="10" stroke="#ffd700" stroke-width="2"/>
                    <circle cx="40" cy="9" r="2.5" fill="#ffd700"/>
                    <!-- Nota musical (karaoke) -->
                    <text x="36" y="36" font-size="10" fill="rgba(0,0,0,0.6)" font-weight="bold">♪</text>
                </svg>
            </div>
            <div class="logo-title">TAJ MAHAL</div>
            <div class="logo-subtitle">Karaoke Bar &nbsp;·&nbsp; Sistema de Gestión</div>
        </div>

        <!-- Error -->
        <?php if (!empty($error)): ?>
        <div class="alert-tm">
            <span>⚠️</span>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="<?= BASE_URL ?>/login/procesar" method="POST" id="loginForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

            <div class="mb-1">
                <label class="form-label">Usuario</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input
                        type="text"
                        name="usuario"
                        class="form-control-tm"
                        placeholder="Tu usuario"
                        autocomplete="username"
                        value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label">Contraseña</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        name="password"
                        id="passwordInput"
                        class="form-control-tm"
                        placeholder="Tu contraseña"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="toggle-pass" onclick="togglePass()" title="Ver contraseña">
                        <span id="eyeIcon">👁️</span>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-tm" id="btnLogin">
                <span class="btn-text">🎤 &nbsp; Ingresar al Sistema</span>
                <div class="spinner"></div>
            </button>
        </form>

        <!-- Hint de acceso -->
        <div class="demo-hint">
            <p>👑 Admin: <code>admin</code> / <code>password</code></p>
            <p>🎤 Operador: <code>operador</code> / <code>password</code></p>
            <p>💰 Cajero: <code>cajero</code> / <code>password</code></p>
        </div>

        <div class="card-footer-tm">
            <p>© <?= date('Y') ?> <strong>Taj Mahal Karaoke Bar</strong> — Todos los derechos reservados</p>
        </div>
    </div>

    <div class="note-version">v<?= APP_VERSION ?> &nbsp;·&nbsp; Ayacucho, Perú</div>
</div>

<script>
// ── Partículas doradas flotantes ──────────────────────────
const canvas  = document.getElementById('particles-canvas');
const ctx     = canvas.getContext('2d');
let W, H, particles = [];

function resize() {
    W = canvas.width  = window.innerWidth;
    H = canvas.height = window.innerHeight;
}
window.addEventListener('resize', resize);
resize();

function randomBetween(a, b) { return a + Math.random() * (b - a); }

function createParticle() {
    const colors = [
        'rgba(255,215,0,',
        'rgba(255,255,200,',
        'rgba(154,48,255,',
        'rgba(200,150,255,',
    ];
    return {
        x:     randomBetween(0, W),
        y:     randomBetween(0, H),
        r:     randomBetween(0.5, 2.5),
        color: colors[Math.floor(Math.random() * colors.length)],
        alpha: randomBetween(0.1, 0.7),
        speedX: randomBetween(-0.3, 0.3),
        speedY: randomBetween(-0.6, -0.1),
        pulse:  randomBetween(0, Math.PI * 2),
        pulseSpeed: randomBetween(0.01, 0.03),
    };
}

for (let i = 0; i < 120; i++) particles.push(createParticle());

function animateParticles() {
    ctx.clearRect(0, 0, W, H);
    particles.forEach(p => {
        p.pulse += p.pulseSpeed;
        const alpha = p.alpha * (0.6 + 0.4 * Math.sin(p.pulse));
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = p.color + alpha + ')';
        ctx.fill();

        p.x += p.speedX;
        p.y += p.speedY;

        if (p.y < -5)  p.y = H + 5;
        if (p.x < -5)  p.x = W + 5;
        if (p.x > W+5) p.x = -5;
    });
    requestAnimationFrame(animateParticles);
}
animateParticles();

// ── Toggle contraseña ──────────────────────────────────────
function togglePass() {
    const inp  = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
        inp.type  = 'text';
        icon.textContent = '🙈';
    } else {
        inp.type  = 'password';
        icon.textContent = '👁️';
    }
}

// ── Ripple en botón ───────────────────────────────────────
document.getElementById('btnLogin').addEventListener('click', function(e) {
    const ripple = document.createElement('span');
    ripple.className = 'btn-ripple';
    const rect = this.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left  = (e.clientX - rect.left  - size/2) + 'px';
    ripple.style.top   = (e.clientY - rect.top   - size/2) + 'px';
    this.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
});

// ── Loading al enviar ─────────────────────────────────────
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnLogin');
    btn.classList.add('loading');
    btn.disabled = true;
});
</script>
</body>
</html>
