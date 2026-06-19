<?php
$tituloPagina = 'Dashboard';
$breadcrumb = 'Resumen general del sistema';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .stat-card {
        background: linear-gradient(145deg, rgba(26,26,40,0.7), rgba(18,18,26,0.9));
        border: 1px solid rgba(255,215,0,0.1);
        border-radius: 16px;
        padding: 1.4rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease both;
    }
    .stat-card:hover { transform: translateY(-3px); border-color: rgba(255,215,0,0.3); box-shadow: 0 10px 30px rgba(255,215,0,0.08); }
    .stat-card::after {
        content: ''; position: absolute; top: -30px; right: -30px; width: 100px; height: 100px;
        border-radius: 50%; opacity: 0.08; background: var(--accent, #ffd700);
    }
    @keyframes fadeInUp { from { opacity:0; transform: translateY(15px); } to { opacity:1; transform: translateY(0); } }
    .stat-card .icon-box {
        width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; margin-bottom: 0.85rem; background: rgba(255,215,0,0.1);
    }
    .stat-card .value { font-family: 'Cinzel', serif; font-size: 1.7rem; font-weight: 700; color: var(--blanco); line-height: 1; }
    .stat-card .label { font-size: 0.78rem; color: rgba(240,232,208,0.5); margin-top: 0.35rem; }
    .stat-card .trend { font-size: 0.72rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.3rem; }

    .live-badge {
        display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.7rem; color: #2ecc71;
        background: rgba(46,204,113,0.1); padding: 0.2rem 0.6rem; border-radius: 20px;
    }
    .live-dot { width: 7px; height: 7px; background: #2ecc71; border-radius: 50%; animation: livePulse 1.5s infinite; }
    @keyframes livePulse { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

    .mesa-mini {
        aspect-ratio: 1; border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center;
        font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.2s; border: 1px solid;
    }
    .mesa-mini:hover { transform: scale(1.08); }
    .mesa-libre  { background: rgba(46,204,113,0.1); border-color: rgba(46,204,113,0.3); color: #2ecc71; }
    .mesa-ocupada{ background: rgba(231,76,60,0.1); border-color: rgba(231,76,60,0.3); color: #e74c3c; }
    .mesa-reservada{ background: rgba(243,156,18,0.1); border-color: rgba(243,156,18,0.3); color: #f39c12; }
    .mesa-mantenimiento{ background: rgba(127,140,141,0.1); border-color: rgba(127,140,141,0.3); color: #95a5a6; }

    .cola-item { display:flex; align-items:center; gap:0.8rem; padding:0.75rem; border-radius:10px;
        background: rgba(255,255,255,0.02); margin-bottom:0.5rem; transition:all 0.2s; }
    .cola-item:hover { background: rgba(255,215,0,0.04); }
    .cola-pos { width:28px; height:28px; border-radius:50%; background: rgba(255,215,0,0.12); color:var(--oro);
        display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; flex-shrink:0; }

    .now-singing {
        background: linear-gradient(135deg, rgba(106,13,173,0.25), rgba(255,215,0,0.08));
        border: 1px solid rgba(255,215,0,0.25);
        border-radius: 16px; padding: 1.5rem; position: relative; overflow: hidden;
    }
    .now-singing::before {
        content:''; position:absolute; inset:0; background: radial-gradient(circle at 20% 20%, rgba(255,215,0,0.15), transparent 60%);
        animation: rotateGlow 8s linear infinite;
    }
    @keyframes rotateGlow { from { transform: rotate(0deg);} to { transform: rotate(360deg);} }
    .mic-pulse { font-size: 2.5rem; animation: micBounce 1.2s ease-in-out infinite; display:inline-block; }
    @keyframes micBounce { 0%,100%{transform:scale(1);} 50%{transform:scale(1.15);} }
</style>

<!-- ═══════════ STAT CARDS ═══════════ -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="--accent:#e74c3c; animation-delay:.0s">
            <div class="icon-box" style="background:rgba(231,76,60,0.12); color:#e74c3c;">🪑</div>
            <div class="value"><?= $stats['mesas_ocupadas'] ?>/<?= $stats['mesas_total'] ?></div>
            <div class="label">Mesas Ocupadas</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="--accent:#9b30ff; animation-delay:.05s">
            <div class="icon-box" style="background:rgba(154,48,255,0.12); color:#9b30ff;">🎤</div>
            <div class="value"><?= $stats['cola_espera'] ?></div>
            <div class="label">En Cola de Espera</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="--accent:#f39c12; animation-delay:.1s">
            <div class="icon-box" style="background:rgba(243,156,18,0.12); color:#f39c12;">🛎️</div>
            <div class="value"><?= $stats['pedidos_pendientes'] ?></div>
            <div class="label">Pedidos Pendientes</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="--accent:#ffd700; animation-delay:.15s">
            <div class="icon-box" style="background:rgba(255,215,0,0.12); color:#ffd700;">💰</div>
            <div class="value">S/. <?= number_format($stats['ventas_hoy'], 2) ?></div>
            <div class="label">Ventas de Hoy</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Ahora cantando -->
    <div class="col-lg-4">
        <div class="now-singing h-100">
            <div style="position:relative; z-index:2;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge-tm badge-gold">🔴 EN VIVO AHORA</span>
                    <span class="live-badge"><span class="live-dot"></span> Live</span>
                </div>
                <?php if ($stats['cantando_ahora']): ?>
                    <div class="text-center py-3">
                        <div class="mic-pulse">🎤</div>
                        <div class="font-cinzel mt-2" style="font-size:1.1rem; color:var(--oro);">
                            <?= htmlspecialchars($stats['cantando_ahora']['titulo']) ?>
                        </div>
                        <div style="font-size:0.85rem; color:rgba(240,232,208,0.6);">
                            <?= htmlspecialchars($stats['cantando_ahora']['artista']) ?>
                        </div>
                        <div style="font-size:0.8rem; color:rgba(255,215,0,0.6); margin-top:0.5rem;">
                            🎙️ <?= htmlspecialchars($stats['cantando_ahora']['cantante_nombre']) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4" style="color:rgba(240,232,208,0.3);">
                        <div style="font-size:2rem;">🎵</div>
                        <p style="font-size:0.85rem; margin-top:0.5rem;">Nadie cantando en este momento</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Próxima cola -->
    <div class="col-lg-4">
        <div class="card-tm glow h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-cinzel mb-0" style="color:var(--oro); font-size:0.95rem;">🎶 Próximos en Cola</h6>
                <a href="<?= BASE_URL ?>/cola" class="btn-outline-gold" style="padding:0.3rem 0.7rem; font-size:0.72rem;">Ver todo</a>
            </div>
            <?php if (empty($proximaCola)): ?>
                <p style="color:rgba(240,232,208,0.3); font-size:0.85rem;" class="text-center py-3">Cola vacía</p>
            <?php else: ?>
                <?php foreach ($proximaCola as $i => $item): ?>
                <div class="cola-item">
                    <div class="cola-pos"><?= $i+1 ?></div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.85rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <?= htmlspecialchars($item['titulo']) ?>
                        </div>
                        <div style="font-size:0.72rem; color:rgba(240,232,208,0.4);">
                            <?= htmlspecialchars($item['cantante_nombre']) ?> · <?= htmlspecialchars($item['artista']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top canciones -->
    <div class="col-lg-4">
        <div class="card-tm glow h-100">
            <h6 class="font-cinzel mb-3" style="color:var(--oro); font-size:0.95rem;">🏆 Top Canciones</h6>
            <?php if (empty($topCanciones)): ?>
                <p style="color:rgba(240,232,208,0.3); font-size:0.85rem;" class="text-center py-3">Sin datos aún</p>
            <?php else: ?>
                <?php $medallas = ['🥇','🥈','🥉','4️⃣','5️⃣']; foreach ($topCanciones as $i => $tc): ?>
                <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.85rem;">
                    <span><?= $medallas[$i] ?? ($i+1) ?></span>
                    <div style="flex:1; min-width:0;">
                        <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:600;">
                            <?= htmlspecialchars($tc['titulo']) ?>
                        </div>
                        <div style="font-size:0.7rem; color:rgba(240,232,208,0.4);"><?= htmlspecialchars($tc['artista']) ?></div>
                    </div>
                    <span class="badge-tm badge-gold"><?= $tc['veces'] ?>x</span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Gráfico de ventas -->
    <div class="col-lg-7">
        <div class="card-tm glow h-100">
            <h6 class="font-cinzel mb-3" style="color:var(--oro); font-size:0.95rem;">📈 Ventas Últimos 7 Días</h6>
            <canvas id="chartVentas" height="110"></canvas>
        </div>
    </div>

    <!-- Estado de mesas -->
    <div class="col-lg-5">
        <div class="card-tm glow h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-cinzel mb-0" style="color:var(--oro); font-size:0.95rem;">🪑 Mapa de Mesas</h6>
                <a href="<?= BASE_URL ?>/mesas" class="btn-outline-gold" style="padding:0.3rem 0.7rem; font-size:0.72rem;">Gestionar</a>
            </div>
            <div class="row g-2">
                <?php foreach ($mesas as $m): ?>
                <div class="col-3">
                    <div class="mesa-mini mesa-<?= $m['estado'] ?>" title="<?= htmlspecialchars($m['sala_nombre'] ?? '') ?>">
                        <span>🪑</span>
                        <span>#<?= $m['numero'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="d-flex gap-3 mt-3 flex-wrap" style="font-size:0.72rem;">
                <span><span style="color:#2ecc71;">●</span> Libre</span>
                <span><span style="color:#e74c3c;">●</span> Ocupada</span>
                <span><span style="color:#f39c12;">●</span> Reservada</span>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos recientes -->
<div class="card-tm glow">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-cinzel mb-0" style="color:var(--oro); font-size:0.95rem;">🛎️ Pedidos Recientes</h6>
        <a href="<?= BASE_URL ?>/pedidos" class="btn-outline-gold" style="padding:0.3rem 0.7rem; font-size:0.72rem;">Ver todos</a>
    </div>
    <div class="tabla-wrap">
        <table class="table-tm">
            <thead>
                <tr><th>#</th><th>Mesa</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Hora</th></tr>
            </thead>
            <tbody>
                <?php if (empty($pedidosRecientes)): ?>
                <tr><td colspan="6" class="text-center" style="color:rgba(240,232,208,0.3);">No hay pedidos recientes</td></tr>
                <?php else: foreach ($pedidosRecientes as $p): 
                    $badgeMap = ['pendiente'=>'warning','en_preparacion'=>'info','listo'=>'success','entregado'=>'success','cancelado'=>'danger'];
                ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td>Mesa <?= $p['mesa_numero'] ?? '-' ?></td>
                    <td><?= htmlspecialchars($p['cliente_nombre'] ?? 'Sin nombre') ?></td>
                    <td>S/. <?= number_format($p['total'], 2) ?></td>
                    <td><span class="badge-tm badge-<?= $badgeMap[$p['estado']] ?? 'info' ?>"><?= ucfirst(str_replace('_',' ',$p['estado'])) ?></span></td>
                    <td><?= date('H:i', strtotime($p['creado_en'])) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$extraScripts = '<script>
const ctxVentas = document.getElementById("chartVentas");
new Chart(ctxVentas, {
    type: "line",
    data: {
        labels: ' . json_encode(array_map(fn($f) => date('d M', strtotime($f)), array_keys($ventasPorDia))) . ',
        datasets: [{
            label: "Ventas (S/.)",
            data: ' . json_encode(array_values($ventasPorDia)) . ',
            borderColor: "#ffd700",
            backgroundColor: "rgba(255,215,0,0.1)",
            borderWidth: 2.5,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: "#ffd700",
            pointBorderColor: "#0a0a0f",
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: "rgba(255,255,255,0.05)" }, ticks: { color: "rgba(240,232,208,0.5)" } },
            y: { grid: { color: "rgba(255,255,255,0.05)" }, ticks: { color: "rgba(240,232,208,0.5)" }, beginAtZero: true }
        }
    }
});
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>
