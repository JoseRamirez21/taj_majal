<?php
$tituloPagina = 'Reportes';
$breadcrumb = 'Analítica del negocio';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .rep-stat { background: linear-gradient(145deg, rgba(255,255,255,0.025), rgba(255,255,255,0.005));
        border:1px solid rgba(255,215,0,0.12); border-radius:14px; padding:1.2rem; animation: popIn 0.4s ease both; }
    @keyframes popIn { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform: translateY(0);} }
    .rep-stat .v { font-family:'Cinzel',serif; font-size:1.5rem; font-weight:800; color:var(--oro); }
    .rep-stat .l { font-size:0.75rem; color:rgba(240,232,208,0.5); margin-top:0.2rem; }
    .ranking-row { display:flex; align-items:center; gap:0.7rem; padding:0.55rem 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:0.85rem; }
</style>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="rep-stat" style="animation-delay:0s"><div class="v">S/. <?= number_format($resumen['ventas_mes'],0) ?></div><div class="l">Ventas del Mes</div></div></div>
    <div class="col-6 col-md-3"><div class="rep-stat" style="animation-delay:.05s"><div class="v">S/. <?= number_format($resumen['ticket_promedio'],2) ?></div><div class="l">Ticket Promedio</div></div></div>
    <div class="col-6 col-md-3"><div class="rep-stat" style="animation-delay:.1s"><div class="v"><?= $resumen['total_canciones_cantadas'] ?></div><div class="l">Canciones Cantadas</div></div></div>
    <div class="col-6 col-md-3"><div class="rep-stat" style="animation-delay:.15s"><div class="v"><?= $resumen['total_reservas'] ?></div><div class="l">Reservas Totales</div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card-tm glow h-100">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">📈 Ventas Últimos 30 Días</h6>
            <canvas id="chartVentas30" height="100"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-tm glow h-100">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">💳 Métodos de Pago</h6>
            <canvas id="chartMetodos"></canvas>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-tm glow h-100">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">🍹 Productos Más Vendidos</h6>
            <?php foreach ($topProductos as $i => $p): ?>
            <div class="ranking-row">
                <span><?= $p['icono'] ?? '🍹' ?></span>
                <div style="flex:1; min-width:0;"><?= htmlspecialchars($p['nombre']) ?></div>
                <span class="badge-tm badge-gold"><?= $p['cantidad'] ?> u.</span>
                <span style="color:rgba(240,232,208,0.5); min-width:70px; text-align:right;">S/.<?= number_format($p['total'],2) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-tm glow h-100">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">🎤 Top Cantantes</h6>
            <?php if (empty($topCantantes)): ?>
                <p style="color:rgba(240,232,208,0.3); font-size:0.85rem;" class="text-center py-3">Sin datos aún</p>
            <?php else: $medallas=['🥇','🥈','🥉']; foreach ($topCantantes as $i => $tc): ?>
            <div class="ranking-row">
                <span><?= $medallas[$i] ?? '🎵' ?></span>
                <div style="flex:1;"><?= htmlspecialchars($tc['cantante_nombre']) ?></div>
                <span class="badge-tm badge-gold"><?= $tc['veces'] ?> canciones</span>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
new Chart(document.getElementById("chartVentas30"), {
    type: "bar",
    data: {
        labels: ' . json_encode(array_map(fn($f) => date('d/m', strtotime($f)), array_keys($ventasPorDia))) . ',
        datasets: [{
            label: "Ventas (S/.)",
            data: ' . json_encode(array_map(fn($v) => $v['total'], array_values($ventasPorDia))) . ',
            backgroundColor: "rgba(255,215,0,0.6)",
            borderColor: "#ffd700", borderWidth: 1, borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display:false }, ticks: { color: "rgba(240,232,208,0.4)", maxRotation:0, autoSkip:true, maxTicksLimit:10 } },
            y: { grid: { color: "rgba(255,255,255,0.05)" }, ticks: { color: "rgba(240,232,208,0.5)" } }
        }
    }
});

new Chart(document.getElementById("chartMetodos"), {
    type: "doughnut",
    data: {
        labels: ' . json_encode(array_map(fn($m) => ucfirst($m['metodo_pago']), $metodosTotales)) . ',
        datasets: [{
            data: ' . json_encode(array_map(fn($m) => (float)$m['total'], $metodosTotales)) . ',
            backgroundColor: ["#ffd700","#9b30ff","#2ecc71","#3498db","#e67e22"],
            borderColor: "#12121a", borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position:"bottom", labels: { color: "rgba(240,232,208,0.6)", font: { size: 11 } } } }
    }
});
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>