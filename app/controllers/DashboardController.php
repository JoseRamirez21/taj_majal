<?php
// app/controllers/DashboardController.php

defined('BASEPATH') or die('Acceso denegado');

class DashboardController {

    public function index(): void {
        $usuario = Auth::usuario();

        // ── Estadísticas generales ──────────────────────────
        $stats = [
            'mesas_ocupadas' => Database::fetch(
                "SELECT COUNT(*) c FROM mesas WHERE estado = 'ocupada'"
            )['c'] ?? 0,
            'mesas_total' => Database::fetch(
                "SELECT COUNT(*) c FROM mesas"
            )['c'] ?? 0,
            'cola_espera' => Database::fetch(
                "SELECT COUNT(*) c FROM cola_karaoke WHERE estado = 'en_espera'"
            )['c'] ?? 0,
            'pedidos_pendientes' => Database::fetch(
                "SELECT COUNT(*) c FROM pedidos WHERE estado IN ('pendiente','en_preparacion')"
            )['c'] ?? 0,
            'ventas_hoy' => Database::fetch(
                "SELECT COALESCE(SUM(total),0) t FROM boletas WHERE estado='pagada' AND DATE(pagado_en) = CURDATE()"
            )['t'] ?? 0,
            'reservas_hoy' => Database::fetch(
                "SELECT COUNT(*) c FROM reservaciones WHERE fecha = CURDATE() AND estado != 'cancelada'"
            )['c'] ?? 0,
            'total_canciones' => Database::fetch(
                "SELECT COUNT(*) c FROM canciones WHERE activa = 1"
            )['c'] ?? 0,
            'cantando_ahora' => Database::fetch(
                "SELECT ck.*, c.titulo, c.artista FROM cola_karaoke ck
                 JOIN canciones c ON c.id = ck.cancion_id
                 WHERE ck.estado = 'cantando' LIMIT 1"
            ),
        ];

        // ── Próximos en cola ─────────────────────────────────
        $proximaCola = Database::fetchAll(
            "SELECT ck.*, c.titulo, c.artista FROM cola_karaoke ck
             JOIN canciones c ON c.id = ck.cancion_id
             WHERE ck.estado = 'en_espera'
             ORDER BY ck.solicitado_en ASC LIMIT 5"
        );

        // ── Ventas últimos 7 días (para gráfico) ─────────────
        $ventas7dias = Database::fetchAll(
            "SELECT DATE(pagado_en) fecha, COALESCE(SUM(total),0) total
             FROM boletas
             WHERE estado='pagada' AND pagado_en >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY DATE(pagado_en)
             ORDER BY fecha ASC"
        );

        // Rellenar días faltantes con 0
        $ventasPorDia = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $ventasPorDia[$fecha] = 0;
        }
        foreach ($ventas7dias as $v) {
            $ventasPorDia[$v['fecha']] = (float)$v['total'];
        }

        // ── Top 5 canciones más pedidas ──────────────────────
        $topCanciones = Database::fetchAll(
            "SELECT c.titulo, c.artista, COUNT(ck.id) veces
             FROM cola_karaoke ck
             JOIN canciones c ON c.id = ck.cancion_id
             GROUP BY ck.cancion_id
             ORDER BY veces DESC LIMIT 5"
        );

        // ── Pedidos recientes ─────────────────────────────────
        $pedidosRecientes = Database::fetchAll(
            "SELECT p.*, m.numero AS mesa_numero
             FROM pedidos p
             LEFT JOIN mesas m ON m.id = p.mesa_id
             ORDER BY p.creado_en DESC LIMIT 6"
        );

        // ── Mesas con su estado ───────────────────────────────
        $mesas = Database::fetchAll(
            "SELECT m.*, s.nombre AS sala_nombre
             FROM mesas m
             LEFT JOIN salas s ON s.id = m.sala_id
             ORDER BY m.numero ASC"
        );

        require_once APP_PATH . '/views/admin/dashboard.php';
    }

  public function notificaciones(): void {
    header('Content-Type: application/json; charset=utf-8');
    $rol = Auth::rol();
    echo json_encode([
        'notificaciones' => Notificacion::paraRol($rol, 15),
        'no_leidas'      => Notificacion::noLeidasCount($rol),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
public function marcarLeida(): void {
    header('Content-Type: application/json; charset=utf-8');
    $id = (int)($_POST['id'] ?? 0);
    if ($id) Notificacion::marcarLeida($id);
    echo json_encode(['ok' => true]);
    exit;
}

public function marcarTodasLeidas(): void {
    header('Content-Type: application/json; charset=utf-8');
    Notificacion::marcarTodasLeidas(Auth::rol());
    echo json_encode(['ok' => true]);
    exit;
}
}
