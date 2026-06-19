<?php
$tituloPagina = 'Productos';
$breadcrumb = 'Catálogo de bebidas y comida';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .cat-tab {
        padding:0.55rem 1.1rem; border-radius:12px; cursor:pointer; font-size:0.85rem; font-weight:600;
        background: rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06); color:rgba(240,232,208,0.6);
        white-space:nowrap; transition: all 0.2s;
    }
    .cat-tab.active { background: rgba(255,215,0,0.12); border-color: rgba(255,215,0,0.4); color: var(--oro); }
    .prod-card {
        background: linear-gradient(145deg, rgba(255,255,255,0.025), rgba(255,255,255,0.005));
        border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 1.1rem;
        transition: all 0.25s; position:relative; animation: popIn 0.4s ease both;
    }
    .prod-card:hover { transform: translateY(-3px); border-color: rgba(255,215,0,0.25); }
    @keyframes popIn { from { opacity:0; transform: scale(0.95);} to { opacity:1; transform: scale(1);} }
    .prod-icon { width:50px; height:50px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:0.7rem; }
    .prod-price { font-family:'Cinzel',serif; font-weight:800; font-size:1.2rem; color:var(--oro); }
    .stock-tag { font-size:0.7rem; padding:0.15rem 0.5rem; border-radius:10px; }
    .stock-ok { background:rgba(46,204,113,0.12); color:#2ecc71; }
    .stock-low { background:rgba(231,76,60,0.12); color:#e74c3c; }
    .destacado-badge { position:absolute; top:0.7rem; right:0.7rem; font-size:0.9rem; }
</style>

<?php if (!empty($stockBajo)): ?>
<div class="card-tm mb-3" style="border-color: rgba(231,76,60,0.3); background: rgba(231,76,60,0.06);">
    <div class="d-flex align-items-center gap-2">
        <span style="font-size:1.3rem;">⚠️</span>
        <div>
            <strong style="color:#e74c3c; font-size:0.85rem;">Stock bajo:</strong>
            <span style="font-size:0.82rem; color:rgba(240,232,208,0.7);">
                <?= implode(', ', array_map(fn($p) => htmlspecialchars($p['nombre']) . " ({$p['stock']})", array_slice($stockBajo, 0, 5))) ?>
            </span>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap" id="catTabs">
        <span class="cat-tab active" data-cat="todas">Todas</span>
        <?php foreach ($categorias as $c): ?>
        <span class="cat-tab" data-cat="<?= $c['id'] ?>"><?= $c['icono'] ?> <?= htmlspecialchars($c['nombre']) ?></span>
        <?php endforeach; ?>
    </div>
    <?php if (Auth::esAdmin()): ?>
    <button class="btn-gold" onclick="abrirModalProducto()">➕ Nuevo Producto</button>
    <?php endif; ?>
</div>

<div class="row g-3" id="productosGrid">
    <?php foreach ($productos as $i => $p): ?>
    <div class="col-6 col-md-4 col-lg-3 prod-item" data-cat="<?= $p['categoria_id'] ?>">
        <div class="prod-card" style="animation-delay:<?= $i * 0.02 ?>s">
            <?php if ($p['destacado']): ?><span class="destacado-badge">⭐</span><?php endif; ?>
            <div class="prod-icon" style="background:<?= $p['categoria_color'] ?? '#ffd700' ?>22; color:<?= $p['categoria_color'] ?? '#ffd700' ?>;">
                <?= $p['categoria_icono'] ?? '🍹' ?>
            </div>
            <div style="font-weight:700; font-size:0.92rem;"><?= htmlspecialchars($p['nombre']) ?></div>
            <div style="font-size:0.72rem; color:rgba(240,232,208,0.4); min-height:2rem; margin:0.3rem 0;">
                <?= htmlspecialchars(mb_strimwidth($p['descripcion'] ?? '', 0, 50, '...')) ?>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="prod-price">S/. <?= number_format($p['precio'], 2) ?></span>
                <span class="stock-tag <?= $p['stock'] <= $p['stock_minimo'] ? 'stock-low' : 'stock-ok' ?>">
                    <?= $p['stock'] ?> u.
                </span>
            </div>
            <?php if (Auth::esAdmin()): ?>
            <div class="d-flex gap-2 mt-2">
                <button class="btn-outline-gold w-100" style="padding:0.35rem; font-size:0.72rem;" onclick='editarProducto(<?= json_encode($p) ?>)'>✏️ Editar</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (Auth::esAdmin()): ?>
<div class="modal fade modal-tm" id="modalProducto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-cinzel" style="color:var(--oro);" id="modalProdTitle">Nuevo Producto</h5>
                <button class="btn-close btn-close-tm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="prodId">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" id="prodNombre" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea id="prodDescripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Precio S/.</label>
                        <input type="number" id="prodPrecio" class="form-control" step="0.01">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" id="prodStock" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select id="prodCategoria" class="form-select">
                        <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['icono'] ?> <?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="prodDestacado" class="form-check-input">
                    <label class="form-check-label" for="prodDestacado" style="font-size:0.85rem;">⭐ Producto destacado</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline-gold" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-gold" onclick="guardarProducto()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$extraScripts = '<script>
document.querySelectorAll(".cat-tab").forEach(tab => {
    tab.addEventListener("click", function() {
        document.querySelectorAll(".cat-tab").forEach(t => t.classList.remove("active"));
        this.classList.add("active");
        const cat = this.dataset.cat;
        document.querySelectorAll(".prod-item").forEach(item => {
            item.style.display = (cat === "todas" || item.dataset.cat === cat) ? "" : "none";
        });
    });
});

function abrirModalProducto() {
    document.getElementById("modalProdTitle").textContent = "Nuevo Producto";
    document.getElementById("prodId").value = "";
    document.getElementById("prodNombre").value = "";
    document.getElementById("prodDescripcion").value = "";
    document.getElementById("prodPrecio").value = "";
    document.getElementById("prodStock").value = "";
    document.getElementById("prodDestacado").checked = false;
    new bootstrap.Modal(document.getElementById("modalProducto")).show();
}

function editarProducto(p) {
    document.getElementById("modalProdTitle").textContent = "Editar Producto";
    document.getElementById("prodId").value = p.id;
    document.getElementById("prodNombre").value = p.nombre;
    document.getElementById("prodDescripcion").value = p.descripcion || "";
    document.getElementById("prodPrecio").value = p.precio;
    document.getElementById("prodStock").value = p.stock;
    document.getElementById("prodCategoria").value = p.categoria_id;
    document.getElementById("prodDestacado").checked = p.destacado == 1;
    new bootstrap.Modal(document.getElementById("modalProducto")).show();
}

function guardarProducto() {
    const fd = new FormData();
    fd.append("id", document.getElementById("prodId").value);
    fd.append("nombre", document.getElementById("prodNombre").value);
    fd.append("descripcion", document.getElementById("prodDescripcion").value);
    fd.append("precio", document.getElementById("prodPrecio").value);
    fd.append("stock", document.getElementById("prodStock").value);
    fd.append("categoria_id", document.getElementById("prodCategoria").value);
    if (document.getElementById("prodDestacado").checked) fd.append("destacado", "1");

    fetch("' . BASE_URL . '/productos/guardar", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) { mostrarToast("✅ Producto guardado", "success"); setTimeout(()=>location.reload(), 600); }
            else mostrarToast(data.error || "Error", "danger");
        });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>