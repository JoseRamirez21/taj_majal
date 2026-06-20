<?php
$tituloPagina = 'Usuarios del Sistema';
$breadcrumb = 'Gestión de personal y accesos';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .user-card-row { display:flex; align-items:center; gap:1rem; padding:0.9rem 1rem; border-radius:12px;
        background: rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06); margin-bottom:0.6rem;
        transition: all 0.2s; animation: popIn 0.35s ease both; }
    .user-card-row:hover { background: rgba(255,215,0,0.03); border-color: rgba(255,215,0,0.15); }
    @keyframes popIn { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
    .user-avatar-big { width:50px; height:50px; border-radius:14px; background: linear-gradient(135deg,#b8860b,#ffd700);
        display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
    .role-badge-admin { background:rgba(255,215,0,0.15); color:var(--oro); border:1px solid rgba(255,215,0,0.3); }
    .role-badge-operador { background:rgba(154,48,255,0.15); color:#9b30ff; border:1px solid rgba(154,48,255,0.3); }
    .role-badge-cajero { background:rgba(46,204,113,0.15); color:#2ecc71; border:1px solid rgba(46,204,113,0.3); }
    .role-badge-mesero { background:rgba(52,152,219,0.15); color:#3498db; border:1px solid rgba(52,152,219,0.3); }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="font-cinzel mb-0" style="color:var(--oro);">👥 Equipo Taj Mahal</h6>
    <button class="btn-gold" onclick="abrirModalUsuario()">➕ Nuevo Usuario</button>
</div>

<div class="card-tm glow">
    <?php
    $iconosRol = ['admin'=>'👑','operador'=>'🎤','cajero'=>'💰','mesero'=>'🍽️'];
    foreach ($usuarios as $i => $u): ?>
    <div class="user-card-row" style="animation-delay:<?= $i*0.03 ?>s; <?= !$u['activo'] ? 'opacity:0.4;' : '' ?>">
        <div class="user-avatar-big"><?= $iconosRol[$u['rol']] ?? '👤' ?></div>
        <div style="flex:1; min-width:0;">
            <div style="font-weight:700; font-size:0.95rem;">
                <?= htmlspecialchars($u['nombre']) ?>
                <?php if ($u['id'] == Auth::id()): ?><span class="badge-tm badge-gold ms-1" style="font-size:0.6rem;">Tú</span><?php endif; ?>
            </div>
            <div style="font-size:0.78rem; color:rgba(240,232,208,0.5);">
                @<?= htmlspecialchars($u['usuario']) ?>
                <?php if ($u['email']): ?> · <?= htmlspecialchars($u['email']) ?><?php endif; ?>
            </div>
        </div>
        <span class="badge-tm role-badge-<?= $u['rol'] ?>"><?= $iconosRol[$u['rol']] ?> <?= ucfirst($u['rol']) ?></span>
        <span class="badge-tm badge-<?= $u['activo'] ? 'success' : 'danger' ?>"><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></span>
        <div class="d-flex gap-1">
            <button class="btn-icon-mini btn-mini-skip" style="background:rgba(52,152,219,0.1); color:#3498db;" onclick='editarUsuario(<?= json_encode($u) ?>)' title="Editar">✏️</button>
            <?php if ($u['id'] != Auth::id()): ?>
            <button class="btn-icon-mini btn-mini-skip" onclick="eliminarUsuario(<?= $u['id'] ?>)" title="Desactivar">🗑️</button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal Usuario -->
<div class="modal fade modal-tm" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-cinzel" style="color:var(--oro);" id="modalUserTitle">Nuevo Usuario</h5>
                <button class="btn-close btn-close-tm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="userId">
                <div class="mb-3">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" id="uNombre" class="form-control">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" id="uUsuario" class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Rol</label>
                        <select id="uRol" class="form-select">
                            <option value="mesero">🍽️ Mesero</option>
                            <option value="cajero">💰 Cajero</option>
                            <option value="operador">🎤 Operador</option>
                            <option value="admin">👑 Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" id="uEmail" class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" id="uTelefono" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña <span id="passHint" style="font-weight:400; color:rgba(240,232,208,0.4);">(dejar vacío para no cambiar)</span></label>
                    <input type="password" id="uPassword" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline-gold" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-gold" onclick="guardarUsuario()">Guardar Usuario</button>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
function abrirModalUsuario() {
    document.getElementById("modalUserTitle").textContent = "Nuevo Usuario";
    document.getElementById("passHint").textContent = "";
    ["userId","uNombre","uUsuario","uEmail","uTelefono","uPassword"].forEach(id => document.getElementById(id).value = "");
    document.getElementById("uRol").value = "mesero";
    new bootstrap.Modal(document.getElementById("modalUsuario")).show();
}

function editarUsuario(u) {
    document.getElementById("modalUserTitle").textContent = "Editar Usuario";
    document.getElementById("passHint").textContent = "(dejar vacío para no cambiar)";
    document.getElementById("userId").value = u.id;
    document.getElementById("uNombre").value = u.nombre;
    document.getElementById("uUsuario").value = u.usuario;
    document.getElementById("uEmail").value = u.email || "";
    document.getElementById("uTelefono").value = u.telefono || "";
    document.getElementById("uRol").value = u.rol;
    document.getElementById("uPassword").value = "";
    new bootstrap.Modal(document.getElementById("modalUsuario")).show();
}

function guardarUsuario() {
    const fd = new FormData();
    fd.append("id", document.getElementById("userId").value);
    fd.append("nombre", document.getElementById("uNombre").value);
    fd.append("usuario", document.getElementById("uUsuario").value);
    fd.append("email", document.getElementById("uEmail").value);
    fd.append("telefono", document.getElementById("uTelefono").value);
    fd.append("rol", document.getElementById("uRol").value);
    fd.append("password", document.getElementById("uPassword").value);

   fetch("' . BASE_URL . '/usuarios/guardar", { method: "POST", body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            Swal.fire({
                toast: true, position: "top-end", icon: "success",
                title: "Usuario guardado", showConfirmButton: false, timer: 1500,
                background: "#1a1a28", color: "#f0e8d0"
            });
            setTimeout(()=>location.reload(), 600);
        } else {
            alertaTm(data.error || "Error al guardar", "error");
        }
    });
}

function eliminarUsuario(id) {
    confirmarAccion({
        titulo: "¿Desactivar este usuario?",
        texto: "El usuario no podrá iniciar sesión hasta que lo reactives.",
        confirmar: "Sí, desactivar",
        icono: "warning"
    }).then((result) => {
        if (!result.isConfirmed) return;
        const fd = new FormData(); fd.append("id", id);
        fetch("' . BASE_URL . '/usuarios/eliminar", { method: "POST", body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.ok) { mostrarToast("Usuario desactivado", "info"); setTimeout(()=>location.reload(), 500); }
                else mostrarToast(data.error || "Error", "danger");
            });
    });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>
