<?php

/** @var array $mesas */

$tituloPagina = 'Gestión de Mesas';
$breadcrumb = 'Mapa visual y control de estados';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .resumen-pill {
        display:flex; align-items:center; gap:0.6rem; padding:0.8rem 1.2rem; border-radius:14px;
        background: rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06); flex:1; min-width:140px;
    }
    .resumen-pill .num { font-family:'Cinzel',serif; font-size:1.5rem; font-weight:800; }
    .resumen-pill .lbl { font-size:0.75rem; color:rgba(240,232,208,0.5); }

    .mesa-card {
        background: linear-gradient(145deg, rgba(255,255,255,0.025), rgba(255,255,255,0.005));
        border: 2px solid; border-radius: 16px; padding: 1.1rem; cursor:pointer;
        transition: all 0.25s ease; position: relative; overflow:hidden;
        animation: popIn 0.4s ease both;
    }
    @keyframes popIn { from { opacity:0; transform: scale(0.9);} to { opacity:1; transform: scale(1);} }
    .mesa-card:hover { transform: translateY(-4px) scale(1.02); }

    .mesa-card.m-libre { border-color: rgba(46,204,113,0.35); background: linear-gradient(145deg, rgba(46,204,113,0.07), rgba(255,255,255,0.01)); }
    .mesa-card.m-ocupada { border-color: rgba(231,76,60,0.4); background: linear-gradient(145deg, rgba(231,76,60,0.09), rgba(255,255,255,0.01)); }
    .mesa-card.m-reservada { border-color: rgba(243,156,18,0.4); background: linear-gradient(145deg, rgba(243,156,18,0.09), rgba(255,255,255,0.01)); }
    .mesa-card.m-mantenimiento { border-color: rgba(149,165,166,0.35); background: linear-gradient(145deg, rgba(149,165,166,0.07), rgba(255,255,255,0.01)); opacity:0.7; }

    .mesa-card .top-row { display:flex; justify-content:space-between; align-items:flex-start; }
    .mesa-number { font-family:'Cinzel',serif; font-size:1.6rem; font-weight:800; }
    .mesa-icon-big { font-size:1.8rem; opacity:0.8; }
    .mesa-card .sala-tag { font-size:0.7rem; color:rgba(240,232,208,0.45); margin-top:0.2rem; }
    .mesa-card .cap-tag { font-size:0.75rem; color:rgba(240,232,208,0.55); margin-top:0.6rem; }

    .status-dot { width:9px; height:9px; border-radius:50%; display:inline-block; margin-right:5px; }
    .dot-libre { background:#2ecc71; box-shadow:0 0 8px rgba(46,204,113,0.6); }
    .dot-ocupada { background:#e74c3c; box-shadow:0 0 8px rgba(231,76,60,0.6); animation: pulseLiveDot 1.5s infinite; }
    .dot-reservada { background:#f39c12; box-shadow:0 0 8px rgba(243,156,18,0.6); }
    .dot-mantenimiento { background:#95a5a6; }
    @keyframes pulseLiveDot { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

    .filter-pills { display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .filter-pill {
        padding:0.4rem 0.9rem; border-radius:20px; font-size:0.78rem; font-weight:600; cursor:pointer;
        border: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.02); color: rgba(240,232,208,0.6);
        transition: all 0.2s;
    }
    .filter-pill.active { background: rgba(255,215,0,0.12); border-color: rgba(255,215,0,0.4); color: var(--oro); }
</style>

<!-- Resumen -->
<div class="d-flex gap-3 flex-wrap mb-4">
    <div class="resumen-pill"><span class="status-dot dot-libre"></span><div><div class="num" style="color:#2ecc71;"><?= $resumen['libre'] ?></div><div class="lbl">Libres</div></div></div>
    <div class="resumen-pill"><span class="status-dot dot-ocupada"></span><div><div class="num" style="color:#e74c3c;"><?= $resumen['ocupada'] ?></div><div class="lbl">Ocupadas</div></div></div>
    <div class="resumen-pill"><span class="status-dot dot-reservada"></span><div><div class="num" style="color:#f39c12;"><?= $resumen['reservada'] ?></div><div class="lbl">Reservadas</div></div></div>
    <div class="resumen-pill"><span class="status-dot dot-mantenimiento"></span><div><div class="num" style="color:#95a5a6;"><?= $resumen['mantenimiento'] ?></div><div class="lbl">Mantenimiento</div></div></div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="filter-pills" id="filterPills">
        <span class="filter-pill active" data-filter="todas">Todas</span>
        <span class="filter-pill" data-filter="libre">🟢 Libres</span>
        <span class="filter-pill" data-filter="ocupada">🔴 Ocupadas</span>
        <span class="filter-pill" data-filter="reservada">🟡 Reservadas</span>
    </div>
    <?php if (Auth::esAdmin()): ?>
    <button class="btn-gold" onclick="abrirModalMesa()">➕ Nueva Mesa</button>
    <?php endif; ?>
</div>

<div class="row g-3" id="mesasGrid">
    <?php foreach ($mesas as $i => $m): ?>
    <div class="col-6 col-md-4 col-lg-3 mesa-item" data-estado="<?= $m['estado'] ?>">
        <div class="mesa-card m-<?= $m['estado'] ?>" style="animation-delay:<?= $i * 0.03 ?>s" onclick="abrirAccionesMesa(<?= $m['id'] ?>, <?= $m['numero'] ?>, '<?= $m['estado'] ?>')">
            <div class="top-row">
                <div>
                    <div class="mesa-number">#<?= $m['numero'] ?></div>
                    <div class="sala-tag"><?= htmlspecialchars($m['sala_nombre'] ?? 'Sin sala') ?></div>
                </div>
                <div class="mesa-icon-big">🪑</div>
            </div>
            <div class="cap-tag">👥 <?= $m['capacidad'] ?> personas</div>
            <div class="mt-2">
                <span class="status-dot dot-<?= $m['estado'] ?>"></span>
                <span style="font-size:0.78rem; text-transform:capitalize;"><?= $m['estado'] ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal: Acciones rápidas de mesa -->
<div class="modal fade modal-tm" id="modalAcciones" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-cinzel" style="color:var(--oro);">Mesa #<span id="accMesaNum"></span></h5>
                <button class="btn-close btn-close-tm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="color:rgba(240,232,208,0.5); font-size:0.85rem;">Cambiar estado de la mesa:</p>
                <div class="d-grid gap-2">
                    <button class="btn-control" style="background:rgba(46,204,113,0.15); color:#2ecc71; border:1px solid rgba(46,204,113,0.3); padding:0.7rem; border-radius:10px; font-weight:600;" onclick="cambiarEstadoMesa('libre')">🟢 Marcar como Libre</button>
                    <button class="btn-control" style="background:rgba(231,76,60,0.15); color:#e74c3c; border:1px solid rgba(231,76,60,0.3); padding:0.7rem; border-radius:10px; font-weight:600;" onclick="cambiarEstadoMesa('ocupada')">🔴 Marcar como Ocupada</button>
                    <button class="btn-control" style="background:rgba(243,156,18,0.15); color:#f39c12; border:1px solid rgba(243,156,18,0.3); padding:0.7rem; border-radius:10px; font-weight:600;" onclick="cambiarEstadoMesa('reservada')">🟡 Marcar como Reservada</button>
                    <button class="btn-control" style="background:rgba(149,165,166,0.15); color:#95a5a6; border:1px solid rgba(149,165,166,0.3); padding:0.7rem; border-radius:10px; font-weight:600;" onclick="cambiarEstadoMesa('mantenimiento')">⚙️ Mantenimiento</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (Auth::esAdmin()): ?>
<!-- Modal: Nueva mesa -->
<div class="modal fade modal-tm" id="modalMesa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-cinzel" style="color:var(--oro);">Nueva Mesa</h5>
                <button class="btn-close btn-close-tm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Número de Mesa</label>
                    <input type="number" id="nuevoNumero" class="form-control" min="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Capacidad</label>
                    <input type="number" id="nuevaCapacidad" class="form-control" value="4" min="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sala</label>
                    <select id="nuevaSala" class="form-select">
                        <option value="">Sin asignar</option>
                        <?php foreach ($salas as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline-gold" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-gold" onclick="guardarMesa()">Guardar Mesa</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$extraScripts = '<script>
let mesaActivaId = null;

// ── Filtros ──────────────────────────────────────────────
document.querySelectorAll(".filter-pill").forEach(pill => {
    pill.addEventListener("click", function() {
        document.querySelectorAll(".filter-pill").forEach(p => p.classList.remove("active"));
        this.classList.add("active");
        const filtro = this.dataset.filter;
        document.querySelectorAll(".mesa-item").forEach(item => {
            item.style.display = (filtro === "todas" || item.dataset.estado === filtro) ? "" : "none";
        });
    });
});

// ── Modal acciones ───────────────────────────────────────
function abrirAccionesMesa(id, numero, estadoActual) {
    mesaActivaId = id;
    document.getElementById("accMesaNum").textContent = numero;
    new bootstrap.Modal(document.getElementById("modalAcciones")).show();
}

function cambiarEstadoMesa(estado) {
    const fd = new FormData();
    fd.append("id", mesaActivaId);
    fd.append("estado", estado);
    fetch("' . BASE_URL . '/mesas/estado", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                mostrarToast("✅ Estado actualizado", "success");
                setTimeout(() => location.reload(), 600);
            } else {
                mostrarToast("Error al actualizar", "danger");
            }
        });
}

// ── Nueva mesa ───────────────────────────────────────────
function abrirModalMesa() {
    new bootstrap.Modal(document.getElementById("modalMesa")).show();
}

function guardarMesa() {
    const fd = new FormData();
    fd.append("numero", document.getElementById("nuevoNumero").value);
    fd.append("capacidad", document.getElementById("nuevaCapacidad").value);
    fd.append("sala_id", document.getElementById("nuevaSala").value);

    fetch("' . BASE_URL . '/mesas/guardar", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                mostrarToast("🪑 Mesa creada con éxito", "success");
                setTimeout(() => location.reload(), 600);
            } else {
                mostrarToast(data.error || "Error", "danger");
            }
        });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>