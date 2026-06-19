<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tituloPagina) ? htmlspecialchars($tituloPagina) . ' — ' : '' ?>Taj Mahal Karaoke</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { corePlugins: { preflight: false } }</script>

    <style>
        :root {
            --oro:        #ffd700;
            --oro-oscuro: #b8860b;
            --oro-claro:  #fff3b0;
            --purpura:    #6a0dad;
            --purpura-2:  #9b30ff;
            --negro:      #0a0a0f;
            --negro-2:    #12121a;
            --negro-3:    #1a1a28;
            --negro-4:    #22222f;
            --blanco:     #f0e8d0;
            --verde:      #2ecc71;
            --rojo:       #e74c3c;
            --azul:       #3498db;
            --naranja:    #e67e22;
        }

        * { box-sizing: border-box; }

        body {
            background: var(--negro);
            color: var(--blanco);
            font-family: 'Raleway', sans-serif;
            min-height: 100vh;
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--negro-2); }
        ::-webkit-scrollbar-thumb { background: var(--oro-oscuro); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--oro); }

        .font-cinzel { font-family: 'Cinzel', serif; }

        /* ── Layout ── */
        .app-layout { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--negro-2) 0%, var(--negro) 100%);
            border-right: 1px solid rgba(255,215,0,0.1);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,215,0,0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar-brand .icon { font-size: 1.8rem; filter: drop-shadow(0 0 8px rgba(255,215,0,0.5)); }
        .sidebar-brand .text { font-family: 'Cinzel', serif; font-weight: 900; font-size: 1.1rem;
            background: linear-gradient(135deg, #ffd700, #fff8dc, #b8860b);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1.1; }
        .sidebar-brand .sub { font-size: 0.6rem; letter-spacing: 0.15em; color: rgba(255,215,0,0.4); text-transform: uppercase; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 1rem 0.75rem; }
        .nav-section-title { font-size: 0.65rem; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(240,232,208,0.3);
            padding: 0.75rem 0.75rem 0.4rem; margin-top: 0.5rem; }

        .nav-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 0.9rem; border-radius: 10px;
            color: rgba(240,232,208,0.65); text-decoration: none;
            font-size: 0.88rem; font-weight: 500; margin-bottom: 0.2rem;
            transition: all 0.2s ease; position: relative;
        }
        .nav-item:hover { background: rgba(255,215,0,0.06); color: var(--oro); }
        .nav-item.active {
            background: linear-gradient(90deg, rgba(255,215,0,0.15), rgba(255,215,0,0.02));
            color: var(--oro); font-weight: 600;
        }
        .nav-item.active::before {
            content: ''; position: absolute; left: -0.75rem; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; background: var(--oro); border-radius: 0 4px 4px 0;
            box-shadow: 0 0 8px rgba(255,215,0,0.6);
        }
        .nav-item .ic { font-size: 1.05rem; width: 22px; text-align: center; }
        .nav-item .badge-nav {
            margin-left: auto; background: var(--rojo); color: #fff; font-size: 0.65rem;
            padding: 0.1rem 0.45rem; border-radius: 10px; font-weight: 700;
        }

        .sidebar-footer {
            padding: 1rem; border-top: 1px solid rgba(255,215,0,0.08);
        }
        .user-mini { display: flex; align-items: center; gap: 0.6rem; padding: 0.5rem; border-radius: 10px;
            background: rgba(255,255,255,0.02); }
        .user-mini .avatar { width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--oro-oscuro), var(--oro));
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .user-mini .info { flex: 1; min-width: 0; }
        .user-mini .name { font-size: 0.82rem; font-weight: 600; color: var(--blanco); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-mini .role { font-size: 0.7rem; color: rgba(255,215,0,0.5); }
        .logout-btn { color: rgba(231,76,60,0.7); transition: color 0.2s; text-decoration: none; font-size: 1rem; }
        .logout-btn:hover { color: var(--rojo); }

        /* ── Main content ── */
        .main-content { margin-left: 260px; flex: 1; min-width: 0; }

        .topbar {
            background: rgba(18,18,26,0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,215,0,0.08);
            padding: 1rem 1.75rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 900;
        }
        .topbar h1 { font-family: 'Cinzel', serif; font-size: 1.3rem; color: var(--oro); margin: 0; font-weight: 700; }
        .topbar .breadcrumb-tm { font-size: 0.75rem; color: rgba(240,232,208,0.4); margin-top: 0.15rem; }

        .menu-toggle { display: none; background: none; border: none; color: var(--oro); font-size: 1.4rem; cursor: pointer; }

        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .clock-widget { font-family: 'Cinzel', serif; font-size: 0.85rem; color: rgba(255,215,0,0.7); text-align: right; }
        .clock-widget .time { font-size: 1rem; font-weight: 700; }
        .clock-widget .date { font-size: 0.65rem; color: rgba(240,232,208,0.4); text-transform: capitalize; }

        .notif-bell { position: relative; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,215,0,0.1);
            width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; }
        .notif-bell:hover { background: rgba(255,215,0,0.08); }
        .notif-dot { position: absolute; top: 6px; right: 7px; width: 8px; height: 8px; background: var(--rojo);
            border-radius: 50%; box-shadow: 0 0 6px rgba(231,76,60,0.8); display: none; }

        .page-body { padding: 1.75rem; }

        /* ── Cards generales ── */
        .card-tm {
            background: linear-gradient(145deg, rgba(26,26,40,0.6), rgba(18,18,26,0.8));
            border: 1px solid rgba(255,215,0,0.1);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .card-tm.glow:hover { border-color: rgba(255,215,0,0.3); box-shadow: 0 0 30px rgba(255,215,0,0.08); }

        .badge-tm {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.25rem 0.7rem; border-radius: 20px; font-size: 0.72rem; font-weight: 600;
        }
        .badge-success { background: rgba(46,204,113,0.15); color: #2ecc71; border: 1px solid rgba(46,204,113,0.3); }
        .badge-warning { background: rgba(243,156,18,0.15); color: #f39c12; border: 1px solid rgba(243,156,18,0.3); }
        .badge-danger  { background: rgba(231,76,60,0.15);  color: #e74c3c; border: 1px solid rgba(231,76,60,0.3); }
        .badge-info    { background: rgba(52,152,219,0.15); color: #3498db; border: 1px solid rgba(52,152,219,0.3); }
        .badge-gold    { background: rgba(255,215,0,0.12);  color: #ffd700; border: 1px solid rgba(255,215,0,0.3); }

        .btn-gold {
            background: linear-gradient(135deg, #b8860b 0%, #ffd700 50%, #b8860b 100%);
            background-size: 200% auto; color: #0a0a0f; border: none; font-weight: 700;
            border-radius: 10px; padding: 0.55rem 1.2rem; font-size: 0.85rem;
            transition: all 0.3s; box-shadow: 0 3px 12px rgba(255,215,0,0.25);
        }
        .btn-gold:hover { background-position: right center; box-shadow: 0 5px 20px rgba(255,215,0,0.4); color: #0a0a0f; transform: translateY(-1px); }

        .btn-outline-gold {
            background: transparent; border: 1px solid rgba(255,215,0,0.4); color: var(--oro);
            border-radius: 10px; padding: 0.5rem 1.1rem; font-size: 0.85rem; font-weight: 600; transition: all 0.2s;
        }
        .btn-outline-gold:hover { background: rgba(255,215,0,0.08); color: var(--oro); border-color: var(--oro); }

        .table-tm { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-tm thead th {
            background: rgba(255,215,0,0.04); color: rgba(255,215,0,0.7);
            font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em;
            padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255,215,0,0.1); text-align: left;
        }
        .table-tm tbody td { padding: 0.85rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 0.88rem; color: var(--blanco); }
        .table-tm tbody tr { transition: background 0.15s; }
        .table-tm tbody tr:hover { background: rgba(255,215,0,0.03); }

        .tabla-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid rgba(255,215,0,0.08); }

        .modal-tm .modal-content {
            background: linear-gradient(145deg, var(--negro-3), var(--negro-2));
            border: 1px solid rgba(255,215,0,0.15);
            border-radius: 16px; color: var(--blanco);
        }
        .modal-tm .modal-header { border-bottom: 1px solid rgba(255,215,0,0.1); }
        .modal-tm .modal-footer { border-top: 1px solid rgba(255,215,0,0.1); }
        .modal-tm .form-control, .modal-tm .form-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,215,0,0.15); color: var(--blanco);
        }
        .modal-tm .form-control:focus, .modal-tm .form-select:focus {
            background: rgba(255,215,0,0.05); border-color: rgba(255,215,0,0.4); box-shadow: 0 0 0 3px rgba(255,215,0,0.08); color: var(--blanco);
        }
        .modal-tm .form-label { color: rgba(240,232,208,0.6); font-size: 0.8rem; font-weight: 600; }
        .btn-close-tm { filter: invert(1); }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle { display: block; }
        }

        .toast-tm {
            position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
            background: linear-gradient(145deg, var(--negro-3), var(--negro-2));
            border: 1px solid rgba(255,215,0,0.2); border-left: 4px solid var(--oro);
            border-radius: 10px; padding: 1rem 1.25rem; min-width: 280px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5); animation: slideInRight 0.4s ease;
        }
        @keyframes slideInRight { from { transform: translateX(120%); opacity:0; } to { transform: translateX(0); opacity:1; } }
    </style>
    <?= $extraStyles ?? '' ?>
</head>
<body>

<div class="app-layout">

    <!-- ═══════════ SIDEBAR ═══════════ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="icon">🕌</span>
            <div>
                <div class="text">TAJ MAHAL</div>
                <div class="sub">Karaoke Bar</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php
            $rutaActual = $_GET['url'] ?? 'dashboard';
            $rutaActual = trim($rutaActual === '' ? 'dashboard' : $rutaActual);
            function navActive($match, $actual) {
                return strpos($actual, $match) === 0 ? 'active' : '';
            }
            ?>
            <div class="nav-section-title">Principal</div>
            <a href="<?= BASE_URL ?>/dashboard" class="nav-item <?= navActive('dashboard', $rutaActual) ?>">
                <span class="ic">📊</span> Dashboard
            </a>
            <a href="<?= BASE_URL ?>/cola" class="nav-item <?= navActive('cola', $rutaActual) ?>">
                <span class="ic">🎤</span> Cola de Karaoke
            </a>
            <a href="<?= BASE_URL ?>/canciones" class="nav-item <?= navActive('canciones', $rutaActual) ?>">
                <span class="ic">🎵</span> Catálogo Canciones
            </a>
            <a href="<?= BASE_URL ?>/mesas" class="nav-item <?= navActive('mesas', $rutaActual) ?>">
                <span class="ic">🪑</span> Mesas
            </a>
            <a href="<?= BASE_URL ?>/reservaciones" class="nav-item <?= navActive('reservaciones', $rutaActual) ?>">
                <span class="ic">📅</span> Reservaciones
            </a>

            <div class="nav-section-title">Ventas</div>
            <a href="<?= BASE_URL ?>/pedidos" class="nav-item <?= navActive('pedidos', $rutaActual) ?>">
                <span class="ic">🛎️</span> Pedidos
            </a>
            <?php if (Auth::tieneRol(['admin','cajero'])): ?>
            <a href="<?= BASE_URL ?>/productos" class="nav-item <?= navActive('productos', $rutaActual) ?>">
                <span class="ic">🍹</span> Productos
            </a>
            <a href="<?= BASE_URL ?>/caja" class="nav-item <?= navActive('caja', $rutaActual) ?>">
                <span class="ic">💰</span> Caja
            </a>
            <?php endif; ?>

            <?php if (Auth::esAdmin()): ?>
            <div class="nav-section-title">Administración</div>
            <a href="<?= BASE_URL ?>/salas" class="nav-item <?= navActive('salas', $rutaActual) ?>">
                <span class="ic">🏛️</span> Salas
            </a>
            <a href="<?= BASE_URL ?>/usuarios" class="nav-item <?= navActive('usuarios', $rutaActual) ?>">
                <span class="ic">👥</span> Usuarios
            </a>
            <a href="<?= BASE_URL ?>/reportes" class="nav-item <?= navActive('reportes', $rutaActual) ?>">
                <span class="ic">📈</span> Reportes
            </a>
            <a href="<?= BASE_URL ?>/configuracion" class="nav-item <?= navActive('configuracion', $rutaActual) ?>">
                <span class="ic">⚙️</span> Configuración
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-mini">
                <div class="avatar"><?= Auth::rolIcono() ?></div>
                <div class="info">
                    <div class="name"><?= htmlspecialchars(Auth::usuario()['nombre']) ?></div>
                    <div class="role"><?= Auth::rolLabel() ?></div>
                </div>
                <a href="<?= BASE_URL ?>/logout" class="logout-btn" title="Cerrar sesión" onclick="return confirm('¿Cerrar sesión?')">⏻</a>
            </div>
        </div>
    </aside>

    <!-- ═══════════ MAIN ═══════════ -->
    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:1rem;">
                <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
                <div>
                    <h1><?= $tituloPagina ?? 'Dashboard' ?></h1>
                    <?php if (!empty($breadcrumb)): ?><div class="breadcrumb-tm"><?= $breadcrumb ?></div><?php endif; ?>
                </div>
            </div>
            <div class="topbar-right">
                <div class="clock-widget">
                    <div class="time" id="liveClock">--:--:--</div>
                    <div class="date" id="liveDate">cargando...</div>
                </div>
                <div class="notif-bell" onclick="alert('Sistema de notificaciones')">
                    🔔<span class="notif-dot" id="notifDot"></span>
                </div>
            </div>
        </header>

        <main class="page-body">
