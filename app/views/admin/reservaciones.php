<?php
$tituloPagina = 'Reservaciones';
$breadcrumb = 'Agenda de reservas';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .resa-card {
        background: rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06); border-left:3px solid var(--oro);
        border-radius: 10px; padding:0.9rem 1.1rem; margin-bottom:0.6rem; animation: popIn 0.35s ease both;
    }
    @keyframes popIn { from { opacity:0; transform: translateX(-8px);} to { opacity:1; transform: translateX(0);} }
    .resa-time { font-family:'Cinzel',serif; font-weight:800; color:var(--oro); font-size:1.05rem; }
    .resa-card.confirmada { border-left-color:#2ecc71; }
    .resa-card.pendiente { border-left-color:#f39c12; }
    .resa-card.cancelada { border-left-color:#e74c3c; opacity:0.5; }
    .resa-card.completada { border-left-color:#3498db; }
</style>

<div class="row g-3">
    <!-- Selector de fecha + reservas del día -->
    <div class="col-lg-7">
        <div class="card-tm glow mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <input type="date" id="filtroFecha" class="form-control" style="max-width:200px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;" value="<?= htmlspecialchars($fecha) ?>">
                <button class="btn-gold" onclick="abrirModalReserva()">➕ Nueva Reserva</button>
            </div>
        </div>

        <div class="card-tm glow">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">📅 Reservas del <?= date('d/m/Y', strtotime($fecha)) ?></h6>
            <?php if (empty($reservasDelDia)): ?>
                <p class="text-center py-4" style="color:rgba(240,232,208,0.3);">No hay reservas para esta fecha</p>
            <?php else: foreach ($reservasDelDia as $r): ?>
            <div class="resa-card <?= $r['estado'] ?>">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="resa-time"><?= substr($r['hora_inicio'],0,5) ?> - <?= substr($r['hora_fin'],0,5) ?></span>
                        <span class="badge-tm badge-<?= ['pendiente'=>'warning','confirmada'=>'success','cancelada'=>'danger','completada'=>'info'][$r['estado']] ?> ms-2">
                            <?= ucfirst($r['estado']) ?>
                        </span>
                    </div>
                    <span style="font-size:0.75rem; color:rgba(240,232,208,0.4);">👥 <?= $r['n_personas'] ?> personas</span>
                </div>
                <div style="font-weight:700; margin-top:0.4rem;"><?= htmlspecialchars($r['cliente_nombre']) ?></div>
                <div style="font-size:0.78rem; color:rgba(240,232,208,0.5);">
                    📞 <?= htmlspecialchars($r['cliente_telefono'] ?: 'Sin teléfono') ?>
                    <?php if ($r['sala_nombre']): ?> · 🏛️ <?= htmlspecialchars($r['sala_nombre']) ?><?php endif; ?>
                    <?php if ($r['mesa_numero']): ?> · 🪑 Mesa #<?= $r['mesa_numero'] ?><?php endif; ?>
                </div>
                <?php if ($r['estado'] === 'pendiente'): ?>
                <div class="d-flex gap-2 mt-2">
                    <button class="btn-outline-gold" style="padding:0.3rem 0.7rem; font-size:0.72rem;" onclick="cambiarEstadoResa(<?= $r['id'] ?>,'confirmada')">✅ Confirmar</button>
                    <button class="btn-outline-gold" style="padding:0.3rem 0.7rem; font-size:0.72rem; color:#e74c3c; border-color:rgba(231,76,60,0.3);" onclick="cambiarEstadoResa(<?= $r['id'] ?>,'cancelada')">✕ Cancelar</button>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Próximas reservas -->
    <div class="col-lg-5">
        <div class="card-tm glow">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">⏰ Próximas Reservas</h6>
            <?php if (empty($proximas)): ?>
                <p class="text-center py-4" style="color:rgba(240,232,208,0.3);">No hay reservas próximas</p>
            <?php else: foreach ($proximas as $p): ?>
            <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.83rem; padding:0.5rem 0; border-bottom:1px solid rgba(255,255,255,0.04);">
                <div style="text-align:center; min-width:46px;">
                    <div style="font-weight:800; color:var(--oro);"><?= date('d', strtotime($p['fecha'])) ?></div>
                    <div style="font-size:0.65rem; color:rgba(240,232,208,0.4); text-transform:uppercase;"><?= date('M', strtotime($p['fecha'])) ?></div>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($p['cliente_nombre']) ?></div>
                    <div style="font-size:0.7rem; color:rgba(240,232,208,0.4);"><?= substr($p['hora_inicio'],0,5) ?> · <?= $p['n_personas'] ?>p</div>
                </div>
                <span class="badge-tm badge-<?= $p['estado']==='confirmada'?'success':'warning' ?>" style="font-size:0.65rem;"><?= ucfirst($p['estado']) ?></span>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<!-- Modal nueva reserva -->
<div class="modal fade modal-tm" id="modalReserva" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-cinzel" style="color:var(--oro);">Nueva Reserva</h5>
                <button class="btn-close btn-close-tm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre del Cliente</label>
                    <input type="text" id="rNombre" class="form-control">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" id="rTelefono" class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">N° Personas</label>
                        <input type="number" id="rPersonas" class="form-control" value="2" min="1">
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" id="rFecha" class="form-control">
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label">Hora inicio</label>
                        <input type="time" id="rHoraInicio" class="form-control">
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label">Hora fin</label>
                        <input type="time" id="rHoraFin" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sala</label>
                    <select id="rSala" class="form-select">
                        <option value="">Sin asignar</option>
                        <?php foreach ($salas as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea id="rObs" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline-gold" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-gold" onclick="guardarReserva()">Guardar Reserva</button>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
document.getElementById("filtroFecha").addEventListener("change", function() {
    window.location.href = "' . BASE_URL . '/reservaciones?fecha=" + this.value;
});

function abrirModalReserva() {
    document.getElementById("rFecha").value = document.getElementById("filtroFecha").value;
    new bootstrap.Modal(document.getElementById("modalReserva")).show();
}

function guardarReserva() {
    const fd = new FormData();
    fd.append("cliente_nombre", document.getElementById("rNombre").value);
    fd.append("cliente_telefono", document.getElementById("rTelefono").value);
    fd.append("n_personas", document.getElementById("rPersonas").value);
    fd.append("fecha", document.getElementById("rFecha").value);
    fd.append("hora_inicio", document.getElementById("rHoraInicio").value);
    fd.append("hora_fin", document.getElementById("rHoraFin").value);
    fd.append("sala_id", document.getElementById("rSala").value);
    fd.append("observaciones", document.getElementById("rObs").value);

    fetch("' . BASE_URL . '/reservaciones/guardar", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) { mostrarToast("📅 Reserva creada", "success"); setTimeout(()=>location.reload(), 600); }
            else mostrarToast(data.error || "Error", "danger");
        });
}

function cambiarEstadoResa(id, estado) {
    if (estado === "cancelada") {
        confirmarAccion({
            titulo: "¿Cancelar esta reserva?",
            texto: "Esta acción no se puede deshacer.",
            confirmar: "Sí, cancelar",
            icono: "warning"
        }).then((result) => {
            if (!result.isConfirmed) return;
            ejecutarCambioResa(id, estado);
        });
    } else {
        ejecutarCambioResa(id, estado);
    }
}

function ejecutarCambioResa(id, estado) {
    const fd = new FormData();
    fd.append("id", id);
    fd.append("estado", estado);
    fetch("' . BASE_URL . '/reservaciones/estado", { method: "POST", body: fd })
        .then(r => r.json())
        .then(() => {
            Swal.fire({
                toast: true, position: "top-end", icon: "success",
                title: "Reserva actualizada", showConfirmButton: false, timer: 1500,
                background: "#1a1a28", color: "#f0e8d0"
            });
            setTimeout(()=>location.reload(), 500);
        });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>
