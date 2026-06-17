<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Taj Mahal Karaoke</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background:#0a0a0f; color:#f0e8d0; font-family:'Raleway',sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; text-align:center; }
        .code { font-family:'Cinzel',serif; font-size:8rem; font-weight:900; background:linear-gradient(135deg,#ffd700,#b8860b); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1; }
        h2 { color:rgba(255,215,0,0.7); font-family:'Cinzel',serif; font-size:1.2rem; letter-spacing:.2em; margin:.5rem 0 1rem; }
        p { color:rgba(240,232,208,0.4); font-size:.9rem; margin-bottom:2rem; }
        a { display:inline-block; padding:.7rem 2rem; background:linear-gradient(135deg,#b8860b,#ffd700); color:#000; border-radius:8px; font-family:'Cinzel',serif; font-weight:700; letter-spacing:.15em; text-decoration:none; font-size:.85rem; transition:all .3s; }
        a:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(255,215,0,0.4); }
    </style>
</head>
<body>
    <div>
        <div class="code">404</div>
        <h2>Página No Encontrada</h2>
        <p>La canción que buscas no está en nuestro repertorio.</p>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>/dashboard">🏠 Volver al Inicio</a>
    </div>
</body>
</html>
