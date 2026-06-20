<?php



$tituloPagina = 'Caja';
$breadcrumb = 'Cobro de mesas y boletas';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .mesa-cobro-card {
        background: linear-gradient(145deg, rgba(255,215,0,0.06), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,215,0,0.2); border-radius: 14px; padding: 1.1rem;
        cursor: pointer; transition: all 0.25s; animation: popIn 0.4s ease both;
    }
    .mesa-cobro-card:hover { transform: translateY(-3px); border-color: rgba(255,215,0,0.5); box-shadow:0 8px 25px rgba(255,215,0,0.1); }
    @keyframes popIn { from { opacity:0; transform: scale(0.95);} to { opacity:1; transform: scale(1);} }
    .mesa-cobro-num { font-family:'Cinzel',serif; font-size:1.3rem; font-weight:800; color:var(--oro); }
    .mesa-cobro-total { font-family:'Cinzel',serif; font-size:1.4rem; font-weight:800; color:#fff; margin-top:0.5rem; }

    .resumen-pago-pill { display:flex; align-items:center; justify-content:space-between; padding:0.6rem 1rem;
        background: rgba(255,255,255,0.02); border-radius:10px; margin-bottom:0.5rem; font-size:0.85rem; }
</style>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-tm glow">
            <div style="font-size:0.78rem; color:rgba(240,232,208,0.5);">💰 Ventas de Hoy</div>
            <div class="font-cinzel" style="font-size:1.8rem; color:var(--oro); font-weight:800;">S/. <?= number_format($totalHoy, 2) ?></div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card-tm glow">
            <div style="font-size:0.78rem; color:rgba(240,232,208,0.5); margin-bottom:0.6rem;">Métodos de Pago Hoy</div>
            <div class="d-flex gap-2 flex-wrap">
                <?php
                $iconos = ['efectivo'=>'💵','tarjeta'=>'💳','yape'=>'📱','plin'=>'📲','transferencia'=>'🏦'];
                if (empty($resumenPagos)): ?>
                    <span style="color:rgba(240,232,208,0.3); font-size:0.85rem;">Sin ventas registradas aún</span>
                <?php else: foreach ($resumenPagos as $rp): ?>
                <span class="badge-tm badge-gold"><?= $iconos[$rp['metodo_pago']] ?? '💰' ?> <?= ucfirst($rp['metodo_pago']) ?>: S/. <?= number_format($rp['total'],2) ?></span>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<h6 class="font-cinzel mb-3" style="color:var(--oro);">🪑 Mesas Pendientes de Cobro</h6>
<div class="row g-3 mb-4">
    <?php if (empty($mesasPorCobrar)): ?>
        <div class="col-12">
            <div class="card-tm text-center py-4" style="color:rgba(240,232,208,0.3);">
                ✅ No hay mesas pendientes de cobro
            </div>
        </div>
    <?php else: foreach ($mesasPorCobrar as $i => $m): ?>
    <div class="col-md-4 col-lg-3">
        <div class="mesa-cobro-card" style="animation-delay:<?= $i*0.05 ?>s" onclick="abrirCobro(<?= $m['mesa_id'] ?>, <?= $m['numero'] ?>, <?= $m['total'] ?>)">
            <div class="mesa-cobro-num">Mesa #<?= $m['numero'] ?></div>
            <div style="font-size:0.75rem; color:rgba(240,232,208,0.5);"><?= $m['num_pedidos'] ?> pedido(s)</div>
            <div class="mesa-cobro-total">S/. <?= number_format($m['total'], 2) ?></div>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>

<!-- Historial boletas -->
<div class="card-tm glow">
    <h6 class="font-cinzel mb-3" style="color:var(--oro);">🧾 Boletas Emitidas</h6>
    <div class="tabla-wrap">
        <table class="table-tm">
            <thead><tr><th>N°</th><th>Mesa</th><th>Cajero</th><th>Total</th><th>Método</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
                <?php if (empty($boletasHoy)): ?>
                <tr><td colspan="8" class="text-center" style="color:rgba(240,232,208,0.3);">Sin boletas aún</td></tr>
                <?php else: foreach ($boletasHoy as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['numero_boleta']) ?></td>
                    <td>Mesa <?= $b['mesa_numero'] ?? '-' ?></td>
                    <td><?= htmlspecialchars($b['cajero_nombre'] ?? '-') ?></td>
                    <td>S/. <?= number_format($b['total'], 2) ?></td>
                    <td><?= $iconos[$b['metodo_pago']] ?? '' ?> <?= ucfirst($b['metodo_pago']) ?></td>
                    <td><span class="badge-tm badge-<?= $b['estado']==='pagada'?'success':'danger' ?>"><?= ucfirst($b['estado']) ?></span></td>
                    <td><?= date('d/m H:i', strtotime($b['creado_en'])) ?></td>
                    <td><a href="<?= BASE_URL ?>/caja/boleta/<?= $b['id'] ?>" target="_blank" class="btn-outline-gold" style="padding:0.25rem 0.6rem; font-size:0.72rem;">🖨️ Ver</a></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal cobro -->
<div class="modal fade modal-tm" id="modalCobro" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-cinzel" style="color:var(--oro);">Cobrar Mesa #<span id="cobroMesaNum"></span></h5>
                <button class="btn-close btn-close-tm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="cart-total-box mb-3 text-center" style="background:rgba(255,215,0,0.08); border:1px solid rgba(255,215,0,0.25); border-radius:12px; padding:1rem;">
                    <div style="font-size:0.78rem; color:rgba(240,232,208,0.5);">Total a Pagar</div>
                    <div class="font-cinzel" style="font-size:2rem; font-weight:800; color:var(--oro);" id="cobroTotalDisplay">S/. 0.00</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descuento (S/.)</label>
                    <input type="number" id="cobroDescuento" class="form-control" value="0" step="0.5" min="0" oninput="actualizarTotalFinal()">
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago</label>
                    <div class="d-flex gap-2 flex-wrap" id="metodosPago">
                        <button type="button" class="metodo-btn btn-outline-gold active" data-metodo="efectivo">💵 Efectivo</button>
                        <button type="button" class="metodo-btn btn-outline-gold" data-metodo="tarjeta">💳 Tarjeta</button>
                        <button type="button" class="metodo-btn btn-outline-gold" data-metodo="yape">📱 Yape</button>
                        <button type="button" class="metodo-btn btn-outline-gold" data-metodo="plin">📲 Plin</button>
                    </div>
                </div>

                <div style="font-size:0.85rem; color:rgba(240,232,208,0.6);">
                    Total final: <strong style="color:var(--oro);" id="totalFinalTexto">S/. 0.00</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline-gold" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-gold" onclick="confirmarCobro()" id="btnConfirmarCobro">✅ Confirmar Pago</button>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
let cobroMesaId = null;
let cobroTotalOriginal = 0;
let metodoSeleccionado = "efectivo";

document.querySelectorAll(".metodo-btn").forEach(btn => {
    btn.addEventListener("click", function() {
        document.querySelectorAll(".metodo-btn").forEach(b => b.classList.remove("active"));
        this.classList.add("active");
        metodoSeleccionado = this.dataset.metodo;
    });
});

function abrirCobro(mesaId, numero, total) {
    cobroMesaId = mesaId;
    cobroTotalOriginal = total;
    document.getElementById("cobroMesaNum").textContent = numero;
    document.getElementById("cobroTotalDisplay").textContent = "S/. " + total.toFixed(2);
    document.getElementById("cobroDescuento").value = 0;
    actualizarTotalFinal();
    new bootstrap.Modal(document.getElementById("modalCobro")).show();
}

function actualizarTotalFinal() {
    const desc = parseFloat(document.getElementById("cobroDescuento").value) || 0;
    const final = Math.max(0, cobroTotalOriginal - desc);
    document.getElementById("totalFinalTexto").textContent = "S/. " + final.toFixed(2);
}

function confirmarCobro() {
    const btn = document.getElementById("btnConfirmarCobro");
    btn.disabled = true; btn.textContent = "Procesando...";

    const fd = new FormData();
    fd.append("mesa_id", cobroMesaId);
    fd.append("metodo_pago", metodoSeleccionado);
    fd.append("descuento", document.getElementById("cobroDescuento").value || 0);

    fetch("' . BASE_URL . '/caja/cobrar", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
          if (data.ok) {
                Swal.fire({
                    title: "¡Pago registrado!",
                    text: "La boleta se abrirá en una nueva pestaña.",
                    icon: "success",
                    confirmButtonText: "Perfecto",
                    background: "#1a1a28", color: "#f0e8d0", confirmButtonColor: "#ffd700",
                    customClass: { popup: "swal-tm" }
                });
                window.open("' . BASE_URL . '/caja/boleta/" + data.boleta_id, "_blank");
                setTimeout(() => location.reload(), 1200);
            } else {
                mostrarToast(data.error || "Error al cobrar", "danger");
                btn.disabled = false; btn.textContent = "✅ Confirmar Pago";
            }
        });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>