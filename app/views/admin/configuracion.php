<?php
$tituloPagina = 'Configuración';
$breadcrumb = 'Ajustes generales del sistema';
require_once APP_PATH . '/views/partials/header.php';
?>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-tm glow mb-3">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">🏢 Información del Negocio</h6>
            <div class="mb-3">
                <label class="form-label">Nombre del Bar</label>
                <input type="text" id="cfg_nombre_bar" class="form-control" value="<?= htmlspecialchars($config['nombre_bar'] ?? '') ?>"
                    style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" id="cfg_direccion" class="form-control" value="<?= htmlspecialchars($config['direccion'] ?? '') ?>"
                    style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" id="cfg_telefono" class="form-control" value="<?= htmlspecialchars($config['telefono'] ?? '') ?>"
                        style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="cfg_email" class="form-control" value="<?= htmlspecialchars($config['email'] ?? '') ?>"
                        style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
                </div>
            </div>
        </div>

        <div class="card-tm glow mb-3">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">⏰ Horarios y Operación</h6>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Hora de Apertura</label>
                    <input type="time" id="cfg_hora_apertura" class="form-control" value="<?= htmlspecialchars($config['hora_apertura'] ?? '20:00') ?>"
                        style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Hora de Cierre</label>
                    <input type="time" id="cfg_hora_cierre" class="form-control" value="<?= htmlspecialchars($config['hora_cierre'] ?? '04:00') ?>"
                        style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Máx. canciones en cola</label>
                    <input type="number" id="cfg_max_canciones_cola" class="form-control" value="<?= htmlspecialchars($config['max_canciones_cola'] ?? '50') ?>"
                        style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">% IGV</label>
                    <input type="number" id="cfg_igv_porcentaje" class="form-control" value="<?= htmlspecialchars($config['igv_porcentaje'] ?? '18') ?>"
                        style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
                </div>
            </div>
        </div>

        <div class="card-tm glow">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">🌐 Redes Sociales</h6>
            <div class="mb-3">
                <label class="form-label">Facebook</label>
                <input type="text" id="cfg_facebook" class="form-control" value="<?= htmlspecialchars($config['facebook'] ?? '') ?>"
                    style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
            </div>
            <div class="mb-3">
                <label class="form-label">Instagram</label>
                <input type="text" id="cfg_instagram" class="form-control" value="<?= htmlspecialchars($config['instagram'] ?? '') ?>"
                    style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
            </div>
        </div>

        <button class="btn-gold w-100 mt-3" onclick="guardarConfig()" id="btnGuardarConfig">💾 Guardar Toda la Configuración</button>
    </div>

    <div class="col-lg-5">
        <div class="card-tm glow">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">ℹ️ Información del Sistema</h6>
            <div style="font-size:0.85rem; color:rgba(240,232,208,0.6); line-height:2;">
                <div>📦 Versión: <strong style="color:var(--oro);"><?= APP_VERSION ?></strong></div>
                <div>👤 Usuario actual: <strong><?= htmlspecialchars(Auth::usuario()['nombre']) ?></strong></div>
                <div>🔑 Rol: <strong><?= Auth::rolLabel() ?></strong></div>
                <div>🗄️ Base de datos: <strong><?= DB_NAME ?></strong></div>
                <div>🌍 Zona horaria: <strong><?= TIMEZONE ?></strong></div>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
function guardarConfig() {
    const btn = document.getElementById("btnGuardarConfig");
    btn.disabled = true; btn.textContent = "Guardando...";

    const fd = new FormData();
    fd.append("nombre_bar", document.getElementById("cfg_nombre_bar").value);
    fd.append("direccion", document.getElementById("cfg_direccion").value);
    fd.append("telefono", document.getElementById("cfg_telefono").value);
    fd.append("email", document.getElementById("cfg_email").value);
    fd.append("hora_apertura", document.getElementById("cfg_hora_apertura").value);
    fd.append("hora_cierre", document.getElementById("cfg_hora_cierre").value);
    fd.append("max_canciones_cola", document.getElementById("cfg_max_canciones_cola").value);
    fd.append("igv_porcentaje", document.getElementById("cfg_igv_porcentaje").value);
    fd.append("facebook", document.getElementById("cfg_facebook").value);
    fd.append("instagram", document.getElementById("cfg_instagram").value);

    fetch("' . BASE_URL . '/configuracion/guardar", { method: "POST", body: fd })
        .then(r => r.json())
       .then(data => {
    if (data.ok) {
        Swal.fire({
            title: "¡Configuración guardada!",
            text: "Los cambios se aplicaron correctamente.",
            icon: "success",
            confirmButtonText: "Perfecto",
            background: "#1a1a28", color: "#f0e8d0", confirmButtonColor: "#ffd700",
            customClass: { popup: "swal-tm" }
        });
    } else {
        alertaTm("Error al guardar la configuración", "error");
    }
})
        .finally(() => { btn.disabled = false; btn.textContent = "💾 Guardar Toda la Configuración"; });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>