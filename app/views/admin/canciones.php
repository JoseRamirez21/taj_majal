<?php
$tituloPagina = 'Catálogo de Canciones';
$breadcrumb = 'Biblioteca musical del karaoke';
require_once APP_PATH . '/views/partials/header.php';
?>

<style>
    .song-row-card {
        background: rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06); border-radius:12px;
        padding:0.85rem 1rem; margin-bottom:0.6rem; display:flex; align-items:center; gap:1rem;
        transition: all 0.2s; animation: popIn 0.35s ease both;
    }
    .song-row-card:hover { background: rgba(255,215,0,0.04); border-color: rgba(255,215,0,0.2); }
    @keyframes popIn { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
    .song-thumb { width:46px; height:46px; border-radius:10px; background: linear-gradient(135deg,#6a0dad,#9b30ff);
        display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
    .song-code { font-size:0.68rem; color:rgba(255,215,0,0.5); background:rgba(255,215,0,0.08); padding:0.1rem 0.4rem; border-radius:6px; }
    .pop-bar { width:60px; height:5px; background:rgba(255,255,255,0.08); border-radius:3px; overflow:hidden; }
    .pop-bar-fill { height:100%; background: linear-gradient(90deg,#b8860b,#ffd700); }
</style>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div style="position:relative; max-width:380px; flex:1;">
        <input type="text" id="buscarCancion" class="form-control" placeholder="🔍 Buscar canción, artista o código..."
            value="<?= htmlspecialchars($q ?? '') ?>"
            style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,215,0,0.15); color:#f0e8d0;">
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge-tm badge-gold">🎵 <?= $total ?> canciones</span>
        <?php if (Auth::tieneRol(['admin','operador'])): ?>
        <button class="btn-gold" onclick="abrirModalCancion()">➕ Nueva Canción</button>
        <?php endif; ?>
    </div>
</div>

<div class="card-tm glow">
    <div id="songsList">
        <?php if (empty($canciones)): ?>
            <p class="text-center py-4" style="color:rgba(240,232,208,0.3);">No se encontraron canciones</p>
        <?php else: foreach ($canciones as $i => $c): ?>
        <div class="song-row-card" style="animation-delay:<?= min($i*0.02, 0.6) ?>s">
            <div class="song-thumb">🎵</div>
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <span style="font-weight:700; font-size:0.92rem;"><?= htmlspecialchars($c['titulo']) ?></span>
                    <?php if ($c['codigo']): ?><span class="song-code"><?= htmlspecialchars($c['codigo']) ?></span><?php endif; ?>
                </div>
                <div style="font-size:0.78rem; color:rgba(240,232,208,0.5);">
                    <?= htmlspecialchars($c['artista']) ?>
                    <?php if ($c['genero']): ?> · <?= htmlspecialchars($c['genero']) ?><?php endif; ?>
                    <?php if ($c['idioma']): ?> · <?= htmlspecialchars($c['idioma']) ?><?php endif; ?>
                </div>
            </div>
            <div class="d-none d-md-flex align-items-center gap-2" title="Popularidad">
                <div class="pop-bar"><div class="pop-bar-fill" style="width:<?= min(100,$c['popularidad']) ?>%;"></div></div>
            </div>
            <?php if (Auth::tieneRol(['admin','operador'])): ?>
            <div class="d-flex gap-1">
                <button class="btn-icon-mini btn-mini-skip" style="background:rgba(52,152,219,0.1); color:#3498db;" onclick='editarCancion(<?= json_encode($c) ?>)' title="Editar">✏️</button>
                <?php if (Auth::esAdmin()): ?>
                <button class="btn-icon-mini btn-mini-skip" onclick="eliminarCancion(<?= $c['id'] ?>)" title="Eliminar">🗑️</button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<!-- Modal Canción -->
<div class="modal fade modal-tm" id="modalCancion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-cinzel" style="color:var(--oro);" id="modalCancionTitle">Nueva Canción</h5>
                <button class="btn-close btn-close-tm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancionId">
                <div class="row">
                    <div class="col-8 mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" id="cTitulo" class="form-control">
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label">Código</label>
                        <input type="text" id="cCodigo" class="form-control" placeholder="KAR021">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Artista</label>
                    <input type="text" id="cArtista" class="form-control">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Género</label>
                        <input type="text" id="cGenero" class="form-control" list="generosList">
                        <datalist id="generosList">
                            <?php foreach ($generos as $g): ?>
                            <option value="<?= htmlspecialchars($g['genero']) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Idioma</label>
                        <select id="cIdioma" class="form-select">
                            <option>Español</option><option>Inglés</option><option>Portugués</option><option>Otro</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Año</label>
                        <input type="number" id="cAnio" class="form-control" min="1900" max="2030">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">YouTube ID</label>
                        <input type="text" id="cYoutube" class="form-control" placeholder="ej: dQw4w9WgXcQ">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline-gold" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-gold" onclick="guardarCancion()">Guardar Canción</button>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '<script>
let timeoutBuscarCancion = null;

document.getElementById("buscarCancion").addEventListener("input", function() {
    clearTimeout(timeoutBuscarCancion);
    const q = this.value;
    timeoutBuscarCancion = setTimeout(() => {
        window.location.href = "' . BASE_URL . '/canciones?q=" + encodeURIComponent(q);
    }, 500);
});

function abrirModalCancion() {
    document.getElementById("modalCancionTitle").textContent = "Nueva Canción";
    ["cancionId","cTitulo","cCodigo","cArtista","cGenero","cAnio","cYoutube"].forEach(id => document.getElementById(id).value = "");
    document.getElementById("cIdioma").value = "Español";
    new bootstrap.Modal(document.getElementById("modalCancion")).show();
}

function editarCancion(c) {
    document.getElementById("modalCancionTitle").textContent = "Editar Canción";
    document.getElementById("cancionId").value = c.id;
    document.getElementById("cTitulo").value = c.titulo;
    document.getElementById("cCodigo").value = c.codigo || "";
    document.getElementById("cArtista").value = c.artista;
    document.getElementById("cGenero").value = c.genero || "";
    document.getElementById("cIdioma").value = c.idioma || "Español";
    document.getElementById("cAnio").value = c.anio || "";
    document.getElementById("cYoutube").value = c.youtube_id || "";
    new bootstrap.Modal(document.getElementById("modalCancion")).show();
}

function guardarCancion() {
    const fd = new FormData();
    fd.append("id", document.getElementById("cancionId").value);
    fd.append("titulo", document.getElementById("cTitulo").value);
    fd.append("codigo", document.getElementById("cCodigo").value);
    fd.append("artista", document.getElementById("cArtista").value);
    fd.append("genero", document.getElementById("cGenero").value);
    fd.append("idioma", document.getElementById("cIdioma").value);
    fd.append("anio", document.getElementById("cAnio").value);
    fd.append("youtube_id", document.getElementById("cYoutube").value);

    fetch("' . BASE_URL . '/canciones/guardar", { method: "POST", body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) { mostrarToast("🎵 Canción guardada", "success"); setTimeout(()=>location.reload(), 600); }
            else mostrarToast(data.error || "Error", "danger");
        });
}

function eliminarCancion(id) {
    confirmarAccion({
        titulo: "¿Eliminar canción?",
        texto: "Esta acción la quitará del catálogo activo.",
        confirmar: "Sí, eliminar",
        icono: "warning"
    }).then((result) => {
        if (!result.isConfirmed) return;
        const fd = new FormData(); fd.append("id", id);
        fetch("' . BASE_URL . '/canciones/eliminar", { method: "POST", body: fd })
            .then(r => r.json())
            .then(() => { mostrarToast("Canción eliminada", "info"); setTimeout(()=>location.reload(), 500); });
    });
}
</script>';
require_once APP_PATH . '/views/partials/footer.php';
?>
