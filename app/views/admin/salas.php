<?php
$tituloPagina = 'Gestión de Salas';
$breadcrumb = 'Ambientes y espacios del karaoke';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .sala-card {
        background: linear-gradient(145deg, rgba(106,13,173,0.1), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,215,0,0.15); border-radius:18px; padding:1.4rem; position:relative; overflow:hidden;
        transition: all 0.3s; animation: popIn 0.4s ease both;
    }
    .sala-card:hover { transform: translateY(-4px); border-color: rgba(255,215,0,0.35); }
    @keyframes popIn { from { opacity:0; transform: scale(0.95);} to { opacity:1; transform: scale(1);} }
    .sala-tipo-badge { position:absolute; top:1rem; right:1rem; }
    .sala-icon { font-size:2.2rem; margin-bottom:0.6rem; }
    .sala-name { font-family:'Cinzel',serif; font-weight:800; font-size:1.15rem; color:#fff; }
    .sala-desc { font-size:0.8rem; color:rgba(240,232,208,0.5); margin:0.5rem 0; min-height:2.5rem; }
    .sala-stats { display:flex; gap:1rem; margin-top:0.8rem; font-size:0.78rem; }
    .occupancy-bar { width:100%; height:6px; background:rgba(255,255,255,0.08); border-radius:3px; overflow:hidden; margin-top:0.6rem; }
    .occupancy-fill { height:100%; background: linear-gradient(90deg,#2ecc71,#f39c12,#e74c3c); }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="font-cinzel mb-0" style="color:var(--oro);">🏛️ Salas y Ambientes</h6>
    <button class="btn-gold" onclick="abrirModalSala()">➕ Nueva Sala</button>
</div>

<div class="row g-3">
    <?php foreach ($salas as $i => $s):
        $pct = $s['total_mesas'] > 0 ? round(($s['mesas_ocupadas'] / $s['total_mesas']) * 100) : 0;
        $tipoIcono = ['publica'=>'🌐','vip'=>'⭐','privada'=>'🔒'];
        $tipoColor = ['publica'=>'info','vip'=>'gold','privada'=>'warning'];
    ?>
    <div class="col-md-6 col-lg-4">
        <div class="sala-card" style="animation-delay:<?= $i*0.05 ?>s">
            <span class="badge-tm badge-<?= $tipoColor[$s['tipo']] ?> sala-tipo-badge"><?= $tipoIcono[$s['tipo']] ?> <?= ucfirst($s['tipo']) ?></span>
            <div class="sala-icon">🕌</div>
            <div class="sala-name"><?= htmlspecialchars($s['nombre']) ?></div>
            <div class="sala-desc"><?= htmlspecialchars(mb_strimwidth($s['descripcion'] ?? '', 0, 80, '...')) ?></div>
            <div class="sala-stats">
                <span>👥 <?= $s['capacidad'] ?> personas</span>
                <span>🪑 <?= $s['mesas_ocupadas'] ?>/<?= $s['total_mesas'] ?> mesas</span>
                <?php if ($s['precio_hora'] > 0): ?><span>💰 S/.<?= number_format($s['precio_hora'],0) ?>/h</span><?php endif; ?>
            </div>
            <div class="occupancy-bar"><div class="occupancy-fill" style="width:<?= $pct ?>%;"></div></div>
            <button class="btn-outline-gold w-100 mt-3" style="padding:0.4rem; font-size:0.78rem;" onclick='editarSala(<?= json_encode($s) ?>)'>✏️ Editar</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal Sala -->
<div class="modal fade modal-tm" id="modalSala" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-cinzel" style="color:var(--oro);" id="modalSalaTitle">Nueva Sala</h5>
                <button class="btn-close btn-close-tm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="salaId">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" id="sNombre" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea id="sDescripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-4 mb-3">
                        <label class="form-label">Capacidad</label>
                        <input type="number" id="sCapacidad" class="form-control" value="10">
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label">Tipo</label>
                        <select id="sTipo" class="form-select">
                            <option value="publica">Pública</option>
                            <option value="vip">VIP</option>
                            <option value="privada">Privada</option>
                        </select>
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label">S/. por hora</label>
                        <input type="number" id="sPrecio" class="form-control" value="0" step="0.5">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline-gold" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-gold" onclick="guardarSala()">Guardar Sala</button>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
function abrirModalSala() {
    document.getElementById("modalSalaTitle").textContent = "Nueva Sala";
    document.getElementById("salaId").value = "";
    document.getElementById("sNombre").value = "";
    document.getElementById("sDescripcion").value = "";
    document.getElementById("sCapacidad").value = "10";
    document.getElementById("sPrecio").value = "0";
    document.getElementById("sTipo").value = "publica";
    new bootstrap.Modal(document.getElementById("modalSala")).show();
}

function editarSala(s) {
    document.getElementById("modalSalaTitle").textContent = "Editar Sala";
    document.getElementById("salaId").value = s.id;
    document.getElementById("sNombre").value = s.nombre;
    document.getElementById("sDescripcion").value = s.descripcion || "";
    document.getElementById("sCapacidad").value = s.capacidad;
    document.getElementById("sPrecio").value = s.precio_hora;
    document.getElementById("sTipo").value = s.tipo;
    new bootstrap.Modal(document.getElementById("modalSala")).show();
}

function guardarSala() {
    const fd = new FormData();
    fd.append("id", document.getElementById("salaId").value);
    fd.append("nombre", document.getElementById("sNombre").value);
    fd.append("descripcion", document.getElementById("sDescripcion").value);
    fd.append("capacidad", document.getElementById("sCapacidad").value);
    fd.append("tipo", document.getElementById("sTipo").value);
    fd.append("precio_hora", document.getElementById("sPrecio").value);

   fetch("' . BASE_URL . '/salas/guardar", { method: "POST", body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            Swal.fire({
                toast: true, position: "top-end", icon: "success",
                title: "Sala guardada", showConfirmButton: false, timer: 1500,
                background: "#1a1a28", color: "#f0e8d0"
            });
            setTimeout(()=>location.reload(), 600);
        } else {
            alertaTm(data.error || "Error al guardar", "error");
        }
    });
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>
