<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta <?= htmlspecialchars($boleta['numero_boleta']) ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Courier New', monospace; }
        body { background:#fff; color:#000; padding:1rem; max-width:320px; margin:0 auto; }
        .center { text-align:center; }
        .logo { font-size:1.4rem; font-weight:bold; letter-spacing:0.1em; }
        .sub { font-size:0.7rem; color:#444; }
        hr { border:none; border-top:1px dashed #000; margin:0.6rem 0; }
        table { width:100%; font-size:0.75rem; border-collapse:collapse; }
        td { padding:0.15rem 0; }
        .right { text-align:right; }
        .totales td { font-weight:bold; }
        .total-final { font-size:1rem; font-weight:bold; border-top:1px dashed #000; padding-top:0.3rem; }
        .footer-msg { font-size:0.7rem; text-align:center; margin-top:1rem; }
        @media print {
            body { padding:0; }
            .no-print { display:none; }
        }
        .btn-print {
            display:block; width:100%; padding:0.6rem; margin-top:1rem; background:#000; color:#fff;
            border:none; border-radius:4px; cursor:pointer; font-size:0.85rem;
        }
    </style>
</head>
<body>
    <div class="center">
        <div class="logo">🕌 TAJ MAHAL</div>
        <div class="sub">Karaoke Bar</div>
        <div class="sub">Av. La Cultura 123, Ayacucho, Perú</div>
        <div class="sub">RUC: 20XXXXXXXXX</div>
    </div>
    <hr>
    <table>
        <tr><td>Boleta:</td><td class="right"><?= htmlspecialchars($boleta['numero_boleta']) ?></td></tr>
        <tr><td>Mesa:</td><td class="right">#<?= $boleta['mesa_numero'] ?? '-' ?></td></tr>
        <tr><td>Fecha:</td><td class="right"><?= date('d/m/Y H:i', strtotime($boleta['pagado_en'])) ?></td></tr>
        <tr><td>Cajero:</td><td class="right"><?= htmlspecialchars($boleta['cajero_nombre'] ?? '-') ?></td></tr>
    </table>
    <hr>
    <table>
        <thead>
            <tr style="font-weight:bold;"><td>Cant</td><td>Producto</td><td class="right">Subt.</td></tr>
        </thead>
        <tbody>
            <?php foreach ($detalle as $d): ?>
            <tr>
                <td><?= $d['cantidad'] ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($d['nombre'], 0, 16, '..')) ?></td>
                <td class="right">S/.<?= number_format($d['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <table class="totales">
        <tr><td>Subtotal</td><td class="right">S/. <?= number_format($boleta['subtotal'], 2) ?></td></tr>
        <?php if ($boleta['descuento'] > 0): ?>
        <tr><td>Descuento</td><td class="right">- S/. <?= number_format($boleta['descuento'], 2) ?></td></tr>
        <?php endif; ?>
        <tr><td>IGV (incl.)</td><td class="right">S/. <?= number_format($boleta['igv'], 2) ?></td></tr>
        <tr class="total-final"><td>TOTAL</td><td class="right">S/. <?= number_format($boleta['total'], 2) ?></td></tr>
    </table>
    <hr>
    <div class="center sub">Método de pago: <?= ucfirst($boleta['metodo_pago']) ?></div>
    <div class="footer-msg">
        ¡Gracias por cantar con nosotros! 🎤<br>
        Vuelve pronto a Taj Mahal Karaoke
    </div>

    <button class="btn-print no-print" onclick="window.print()">🖨️ Imprimir Boleta</button>
</body>
</html>