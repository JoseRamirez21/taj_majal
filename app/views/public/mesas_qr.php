<?php
$tituloPagina = 'Códigos QR de Mesas';
$breadcrumb = 'Genera e imprime los QR para cada mesa';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .qr-card {
        background: #fff; border-radius: 16px; padding: 1.3rem; text-align:center;
        display:flex; flex-direction:column; align-items:center; animation: popIn 0.4s ease both;
    }
    @keyframes popIn { from { opacity:0; transform: scale(0.95);} to { opacity:1; transform: scale(1);} }
    .qr-card img { width: 160px; height: 160px; margin-bottom: 0.8rem; }
    .qr-mesa-label { font-family:'Cinzel',serif; font-weight:800; color:#0a0a0f; font-size:1.1rem; }
    .qr-bar-label { font-size: 0.75rem; color:#555; margin-top:0.2rem; }

    @media print {
        body * { visibility: hidden; }
        #printArea, #printArea * { visibility: visible; }
        #printArea { position: absolute; top:0; left:0; width:100%; }
        .no-print { display:none !important; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <span class="badge-tm badge-gold">🖨️ <?= count($mesas) ?> códigos QR generados</span>
    <button class="btn-gold" onclick="window.print()">🖨️ Imprimir Todos</button>
</div>

<div class="row g-3" id="printArea">
    <?php foreach ($mesas as $i => $m):
        $url = BASE_URL . '/pedir/' . $m['id'];
        $qrApi = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&color=10-10-15&bgcolor=255-255-255&data=" . urlencode($url);
    ?>
    <div class="col-6 col-md-3">
        <div class="qr-card" style="animation-delay:<?= $i*0.03 ?>s">
            <img src="<?= $qrApi ?>" alt="QR Mesa <?= $m['numero'] ?>" loading="lazy">
            <div class="qr-mesa-label">Mesa #<?= $m['numero'] ?></div>
            <div class="qr-bar-label">🕌 Taj Mahal Karaoke</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once APP_PATH . '/views/partials/footer.php'; ?>