<?php
$tituloPagina = 'Pedidos';
$breadcrumb = 'Gestión de comandas del bar';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .kanban-col { background: rgba(255,255,255,0.015); border:1px solid rgba(255,255,255,0.05); border-radius:14px; padding:1rem; min-height:200px; }
    .kanban-col h6 { font-family:'Cinzel',serif; font-size:0.85rem; margin-bottom:0.9rem; display:flex; align-items:center; justify-content:space-between; }
    .order-card {
        background: rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px;
        padding:0.85rem; margin-bottom:0.7rem; animation: popIn 0.3s ease both; cursor:pointer; transition: all 0.2s;
    }
    .order-card:hover { border-color: rgba(255,215,0,0.3); transform: translateX(2px); }
    @keyframes popIn { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform: translateY(0);} }
    .order-mesa { font-weight:800; font-size:0.95rem; color:var(--oro); }
    .order-time { font-size:0.7rem; color:rgba(240,232,208,0.4); }
    .order-total { font-family:'Cinzel',serif; font-weight:700; color:#fff; font-size:1rem; margin-top:0.4rem; }
    .order-actions { display:flex; gap:0.4rem; margin-top:0.6rem; }
    .btn-order-action { flex:1; border:none; border-radius:8px; padding:0.4rem; font-size:0.72rem; font-weight:700; cursor:pointer; transition:all 0.2s; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="badge-tm badge-gold">📋 <?= count($activos) ?> pedidos activos</span>
    <a href="<?= BASE_URL ?>/pedidos/nuevo" class="btn-gold">➕ Nuevo Pedido</a>
</div>

<div class="row g-3 mb-4">
    <?php
    $columnas = [
        'pendiente'      => ['titulo' => '🆕 Pendientes',     'color' => '#f39c12'],
        'en_preparacion' => ['titulo' => '👨‍🍳 En Preparación', 'color' => '#3498db'],
        'listo'          => ['titulo' => '✅ Listos',          'color' => '#2ecc71'],
    ];
    foreach ($columnas as $estado => $info):
        $pedidosCol = array_filter($activos, fn($p) => $p['estado'] === $estado);
    ?>
    <div class="col-md-4">
        <div class="kanban-col">
            <h6 style="color:<?= $info['color'] ?>;"><?= $info['titulo'] ?> <span class="badge-tm" style="background:<?= $info['color'] ?>22; color:<?= $info['color'] ?>;"><?= count($pedidosCol) ?></span></h6>
            <?php if (empty($pedidosCol)): ?>
                <p style="color:rgba(240,232,208,0.25); font-size:0.8rem;" class="text-center py-3">Vacío</p>
            <?php else: foreach ($pedidosCol as $p): ?>
            <div class="order-card" data-id="<?= $p['id'] ?>">
                <div class="d-flex justify-content-between">
                    <span class="order-mesa">Mesa #<?= $p['mesa_numero'] ?? '-' ?></span>
                    <span class="order-time"><?= date('H:i', strtotime($p['creado_en'])) ?></span>
                </div>
                <div style="font-size:0.78rem; color:rgba(240,232,208,0.5);"><?= htmlspecialchars($p['cliente_nombre'] ?: 'Sin nombre') ?></div>
                <div class="order-total">S/. <?= number_format($p['total'], 2) ?></div>
                <div class="order-actions">
                    <?php if ($estado === 'pendiente'): ?>
                        <button class="btn-order-action" style="background:rgba(52,152,219,0.15); color:#3498db;" onclick="cambiarEstado(<?= $p['id'] ?>,'en_preparacion')">👨‍🍳 Preparar</button>
                    <?php elseif ($estado === 'en_preparacion'): ?>
                        <button class="btn-order-action" style="background:rgba(46,204,113,0.15); color:#2ecc71;" onclick="cambiarEstado(<?= $p['id'] ?>,'listo')">✅ Listo</button>
                    <?php else: ?>
                        <button class="btn-order-action" style="background:rgba(255,215,0,0.15); color:var(--oro);" onclick="cambiarEstado(<?= $p['id'] ?>,'entregado')">🛎️ Entregar</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Historial completo -->
<div class="card-tm glow">
    <h6 class="font-cinzel mb-3" style="color:var(--oro);">📜 Historial de Pedidos</h6>
    <div class="tabla-wrap">
        <table class="table-tm">
            <thead><tr><th>#</th><th>Mesa</th><th>Cliente</th><th>Mesero</th><th>Total</th><th>Estado</th><th>Fecha</th></tr></thead>
            <tbody>
                <?php $badgeMap = ['pendiente'=>'warning','en_preparacion'=>'info','listo'=>'success','entregado'=>'success','cancelado'=>'danger']; ?>
                <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td>Mesa <?= $p['mesa_numero'] ?? '-' ?></td>
                    <td><?= htmlspecialchars($p['cliente_nombre'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($p['mesero_nombre'] ?? '-') ?></td>
                    <td>S/. <?= number_format($p['total'], 2) ?></td>
                    <td><span class="badge-tm badge-<?= $badgeMap[$p['estado']] ?? 'info' ?>"><?= ucfirst(str_replace('_',' ',$p['estado'])) ?></span></td>
                    <td><?= date('d/m H:i', strtotime($p['creado_en'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$extraScripts = '<script>
function cambiarEstado(id, estado) {
    const fd = new FormData();
    fd.append("id", id);
    fd.append("estado", estado);
    fetch("' . BASE_URL . '/pedidos/estado", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                Swal.fire({
                    toast: true, position: "top-end", icon: "success",
                    title: "Pedido actualizado", showConfirmButton: false, timer: 1500,
                    background: "#1a1a28", color: "#f0e8d0"
                });
                setTimeout(()=>location.reload(), 500);
            }
        });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>