<?php
/** @var array $mesas */

$tituloPagina = 'Nuevo Pedido';
$breadcrumb = 'Punto de venta';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .pos-prod { background: rgba(255,255,255,0.025); border:1px solid rgba(255,255,255,0.06); border-radius:12px;
        padding:0.9rem; cursor:pointer; transition: all 0.2s; text-align:center; }
    .pos-prod:hover { background: rgba(255,215,0,0.06); border-color: rgba(255,215,0,0.3); transform: translateY(-2px); }
    .pos-prod .ic { font-size:1.6rem; margin-bottom:0.4rem; }
    .pos-prod .nm { font-size:0.78rem; font-weight:600; }
    .pos-prod .pr { font-size:0.82rem; color:var(--oro); font-weight:700; margin-top:0.2rem; }

    .cart-item { display:flex; align-items:center; gap:0.6rem; padding:0.6rem; background: rgba(255,255,255,0.02);
        border-radius:8px; margin-bottom:0.5rem; }
    .qty-btn { width:24px; height:24px; border-radius:6px; border:none; background:rgba(255,215,0,0.12); color:var(--oro);
        font-weight:700; cursor:pointer; }
    .cart-total-box { background: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(106,13,173,0.1)); border:1px solid rgba(255,215,0,0.25);
        border-radius:12px; padding:1rem; }

    .cat-tab-pos { padding:0.5rem 1rem; border-radius:10px; cursor:pointer; font-size:0.8rem; font-weight:600;
        background: rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06); color:rgba(240,232,208,0.6); white-space:nowrap; }
    .cat-tab-pos.active { background: rgba(255,215,0,0.12); border-color: rgba(255,215,0,0.4); color: var(--oro); }
</style>

<div class="row g-3">
    <!-- Productos -->
    <div class="col-lg-8">
        <div class="d-flex gap-2 flex-nowrap overflow-auto mb-3 pb-1" id="catTabsPos">
            <span class="cat-tab-pos active" data-cat="todas">Todas</span>
            <?php foreach ($categorias as $c): ?>
            <span class="cat-tab-pos" data-cat="<?= $c['id'] ?>"><?= $c['icono'] ?> <?= htmlspecialchars($c['nombre']) ?></span>
            <?php endforeach; ?>
        </div>
        <div class="row g-2" id="posGrid">
            <?php foreach ($productos as $p): ?>
            <div class="col-4 col-md-3 pos-item" data-cat="<?= $p['categoria_id'] ?>">
                <div class="pos-prod" onclick='agregarAlCarrito(<?= $p["id"] ?>, <?= json_encode($p["nombre"]) ?>, <?= $p["precio"] ?>)'>
                    <div class="ic"><?= $p['categoria_icono'] ?? '🍹' ?></div>
                    <div class="nm"><?= htmlspecialchars($p['nombre']) ?></div>
                    <div class="pr">S/. <?= number_format($p['precio'], 2) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Carrito -->
    <div class="col-lg-4">
        <div class="card-tm glow" style="position:sticky; top:90px;">
            <h6 class="font-cinzel mb-3" style="color:var(--oro);">🛒 Pedido Actual</h6>

            <select id="mesaSelectPos" class="form-select mb-2" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
                <option value="">Sin mesa (para llevar)</option>
                <?php foreach ($mesas as $m): ?>
                <option value="<?= $m['id'] ?>"><?= $m['estado']==='libre'?'🟢':'🔴' ?> Mesa #<?= $m['numero'] ?></option>
                <?php endforeach; ?>
            </select>

            <input type="text" id="clienteNombrePos" class="form-control mb-3" placeholder="Nombre del cliente (opcional)"
                style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">

            <div id="cartItems" style="max-height:280px; overflow-y:auto; margin-bottom:1rem;">
                <p style="color:rgba(240,232,208,0.3); font-size:0.85rem;" class="text-center py-3" id="cartEmpty">El carrito está vacío</p>
            </div>

            <div class="cart-total-box mb-3">
                <div class="d-flex justify-content-between" style="font-size:0.85rem;"><span>Subtotal</span><span id="cartSubtotal">S/. 0.00</span></div>
                <div class="d-flex justify-content-between mt-2" style="font-family:'Cinzel',serif; font-weight:800; font-size:1.1rem; color:var(--oro);">
                    <span>Total</span><span id="cartTotal">S/. 0.00</span>
                </div>
            </div>

            <button class="btn-gold w-100" onclick="confirmarPedido()" id="btnConfirmarPedido">
                ✅ Confirmar Pedido
            </button>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
let carrito = [];

document.querySelectorAll(".cat-tab-pos").forEach(tab => {
    tab.addEventListener("click", function() {
        document.querySelectorAll(".cat-tab-pos").forEach(t => t.classList.remove("active"));
        this.classList.add("active");
        const cat = this.dataset.cat;
        document.querySelectorAll(".pos-item").forEach(item => {
            item.style.display = (cat === "todas" || item.dataset.cat === cat) ? "" : "none";
        });
    });
});

function agregarAlCarrito(id, nombre, precio) {
    const existente = carrito.find(i => i.producto_id === id);
    if (existente) { existente.cantidad++; }
    else { carrito.push({ producto_id: id, nombre: nombre, precio: precio, cantidad: 1 }); }
    renderCarrito();
    mostrarToast("➕ " + nombre, "success");
}

function cambiarCantidad(id, delta) {
    const item = carrito.find(i => i.producto_id === id);
    if (!item) return;
    item.cantidad += delta;
    if (item.cantidad <= 0) carrito = carrito.filter(i => i.producto_id !== id);
    renderCarrito();
}

function renderCarrito() {
    const box = document.getElementById("cartItems");
    const empty = document.getElementById("cartEmpty");

    if (carrito.length === 0) {
        box.innerHTML = "";
        box.appendChild(empty);
        document.getElementById("cartSubtotal").textContent = "S/. 0.00";
        document.getElementById("cartTotal").textContent = "S/. 0.00";
        return;
    }

    let total = 0;
    box.innerHTML = carrito.map(item => {
        const sub = item.precio * item.cantidad;
        total += sub;
        return `
            <div class="cart-item">
                <div style="flex:1; min-width:0;">
                    <div style="font-size:0.82rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${item.nombre}</div>
                    <div style="font-size:0.72rem; color:rgba(240,232,208,0.4);">S/. ${item.precio.toFixed(2)} c/u</div>
                </div>
                <button class="qty-btn" onclick="cambiarCantidad(${item.producto_id}, -1)">-</button>
                <span style="min-width:20px; text-align:center; font-weight:700;">${item.cantidad}</span>
                <button class="qty-btn" onclick="cambiarCantidad(${item.producto_id}, 1)">+</button>
                <span style="font-weight:700; min-width:60px; text-align:right; font-size:0.85rem;">S/. ${sub.toFixed(2)}</span>
            </div>`;
    }).join("");

    document.getElementById("cartSubtotal").textContent = "S/. " + total.toFixed(2);
    document.getElementById("cartTotal").textContent = "S/. " + total.toFixed(2);
}

function confirmarPedido() {
    if (carrito.length === 0) { mostrarToast("El carrito está vacío", "warning"); return; }

    const btn = document.getElementById("btnConfirmarPedido");
    btn.disabled = true; btn.textContent = "Procesando...";

    const fd = new FormData();
    fd.append("mesa_id", document.getElementById("mesaSelectPos").value);
    fd.append("cliente_nombre", document.getElementById("clienteNombrePos").value);
    fd.append("items", JSON.stringify(carrito));

    fetch("' . BASE_URL . '/pedidos/guardar", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                mostrarToast("🎉 Pedido #" + data.id + " creado con éxito", "success");
                setTimeout(() => window.location.href = "' . BASE_URL . '/pedidos", 900);
            } else {
                mostrarToast(data.error || "Error al crear pedido", "danger");
                btn.disabled = false; btn.textContent = "✅ Confirmar Pedido";
            }
        });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>