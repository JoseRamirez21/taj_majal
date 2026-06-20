        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Reloj en vivo ──────────────────────────────────────
    function actualizarReloj() {
        const ahora = new Date();
        const horas = String(ahora.getHours()).padStart(2,'0');
        const min   = String(ahora.getMinutes()).padStart(2,'0');
        const seg   = String(ahora.getSeconds()).padStart(2,'0');
        document.getElementById('liveClock').textContent = `${horas}:${min}:${seg}`;

        const dias = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
        const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        document.getElementById('liveDate').textContent =
            `${dias[ahora.getDay()]}, ${ahora.getDate()} de ${meses[ahora.getMonth()]}`;
    }
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    // ── Toast helper global ────────────────────────────────
    function mostrarToast(mensaje, tipo = 'info') {
        const colores = { success:'#2ecc71', danger:'#e74c3c', warning:'#f39c12', info:'#ffd700' };
        const toast = document.createElement('div');
        toast.className = 'toast-tm';
        toast.style.borderLeftColor = colores[tipo] || colores.info;
        toast.innerHTML = `<div style="font-size:0.88rem;">${mensaje}</div>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.4s ease reverse';
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    }
</script>
<script>
function confirmarLogout() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: 'Tendrás que volver a iniciar sesión para continuar.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar',
        background: '#1a1a28',
        color: '#f0e8d0',
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#3a3a4a',
        customClass: { popup: 'swal-tm' }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= BASE_URL ?>/logout";
        }
    });
}
</script>
<script>
// ── Helper genérico de confirmación con SweetAlert2 ──────────
function confirmarAccion(opciones) {
    return Swal.fire({
        title: opciones.titulo || '¿Estás seguro?',
        text: opciones.texto || '',
        icon: opciones.icono || 'warning',
        showCancelButton: true,
        confirmButtonText: opciones.confirmar || 'Sí, continuar',
        cancelButtonText: opciones.cancelar || 'Cancelar',
        background: '#1a1a28',
        color: '#f0e8d0',
        confirmButtonColor: opciones.colorConfirmar || '#e74c3c',
        cancelButtonColor: '#3a3a4a',
        customClass: { popup: 'swal-tm' }
    });
}

// ── Helper de alerta simple (reemplaza alert()) ───────────────
function alertaTm(mensaje, icono = 'info') {
    Swal.fire({
        text: mensaje,
        icon: icono,
        background: '#1a1a28',
        color: '#f0e8d0',
        confirmButtonColor: '#ffd700',
        customClass: { popup: 'swal-tm' }
    });
}
</script>
<script>
let notifAbierto = false;

function toggleNotifDropdown() {
    notifAbierto = !notifAbierto;
    document.getElementById("notifDropdown").classList.toggle("show", notifAbierto);
    if (notifAbierto) cargarNotificaciones();
}

document.addEventListener("click", function(e) {
    const dropdown = document.getElementById("notifDropdown");
    const bell = document.getElementById("notifBellBtn");
    if (notifAbierto && !dropdown.contains(e.target) && !bell.contains(e.target)) {
        notifAbierto = false;
        dropdown.classList.remove("show");
    }
});

function tiempoRelativo(fechaStr) {
    const fecha = new Date(fechaStr.replace(" ", "T"));
    const ahora = new Date();
    const diffMin = Math.floor((ahora - fecha) / 60000);
    if (diffMin < 1) return "Justo ahora";
    if (diffMin < 60) return diffMin + " min";
    const diffH = Math.floor(diffMin / 60);
    if (diffH < 24) return diffH + " h";
    return Math.floor(diffH / 24) + " d";
}

const iconosPorTipo = { pedido: "🛎️", reserva: "📅", cola: "🎤", sistema: "⚙️", alerta: "⚠️" };

function cargarNotificaciones() {
    fetch("<?= BASE_URL ?>/api/notificaciones")
        .then(r => r.json())
        .then(data => {
            const dot = document.getElementById("notifDot");
            dot.style.display = data.no_leidas > 0 ? "block" : "none";

            const list = document.getElementById("notifList");
            if (!data.notificaciones || data.notificaciones.length === 0) {
                list.innerHTML = '<div class="notif-empty">🔔 No tienes notificaciones</div>';
                return;
            }

            list.innerHTML = data.notificaciones.map(n => `
                <div class="notif-item ${n.leida == 0 ? 'unread' : ''}" onclick="marcarLeida(${n.id})">
                    <div class="notif-icon-box">${iconosPorTipo[n.tipo] || "🔔"}</div>
                    <div class="notif-content">
                        <div class="notif-title-txt">${n.titulo}</div>
                        <div class="notif-msg-txt">${n.mensaje}</div>
                        <div class="notif-time-txt">${tiempoRelativo(n.creado_en)}</div>
                    </div>
                </div>
            `).join("");
        });
}

function marcarLeida(id) {
    const fd = new FormData();
    fd.append("id", id);
    fetch("<?= BASE_URL ?>/api/notificaciones/leer", { method: "POST", body: fd })
        .then(() => cargarNotificaciones());
}

function marcarTodasLeidas() {
    fetch("<?= BASE_URL ?>/api/notificaciones/leertodas", { method: "POST" })
        .then(() => cargarNotificaciones());
}

// Polling: revisa notificaciones nuevas cada 10 segundos
cargarNotificaciones();
setInterval(cargarNotificaciones, 10000);
</script>
<style>
.swal-tm { border: 1px solid rgba(255,215,0,0.2) !important; border-radius: 16px !important; }
</style>
<style>
.swal-tm { border: 1px solid rgba(255,215,0,0.2) !important; border-radius: 16px !important; }
</style>
<?= $extraScripts ?? '' ?>
</body>
</html>
