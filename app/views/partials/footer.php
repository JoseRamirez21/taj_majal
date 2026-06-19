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
<?= $extraScripts ?? '' ?>
</body>
</html>
