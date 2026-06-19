<?php
// app/models/Cancion.php

defined('BASEPATH') or die('Acceso denegado');

class Cancion {

    public static function todas(int $limit = 100): array {
        return Database::fetchAll(
            "SELECT * FROM canciones WHERE activa = 1 ORDER BY popularidad DESC, titulo ASC LIMIT ?",
            [$limit]
        );
    }

    public static function buscar(string $q): array {
        $like = "%$q%";
        return Database::fetchAll(
            "SELECT * FROM canciones
             WHERE activa = 1 AND (titulo LIKE ? OR artista LIKE ? OR codigo LIKE ?)
             ORDER BY popularidad DESC LIMIT 30",
            [$like, $like, $like]
        );
    }

    public static function porId(int $id): ?array {
        return Database::fetch("SELECT * FROM canciones WHERE id = ?", [$id]);
    }

    public static function crear(array $d): int {
        return Database::insert(
            "INSERT INTO canciones (titulo, artista, album, anio, genero, idioma, duracion_seg, codigo, youtube_id, portada)
             VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$d['titulo'], $d['artista'], $d['album'] ?? null, $d['anio'] ?: null, $d['genero'] ?? null,
             $d['idioma'] ?? 'Español', $d['duracion_seg'] ?? 0, $d['codigo'] ?? null, $d['youtube_id'] ?? null, $d['portada'] ?? null]
        );
    }

    public static function actualizar(int $id, array $d): bool {
        Database::execute(
            "UPDATE canciones SET titulo=?, artista=?, album=?, anio=?, genero=?, idioma=?, duracion_seg=?, codigo=?, youtube_id=?
             WHERE id=?",
            [$d['titulo'], $d['artista'], $d['album'] ?? null, $d['anio'] ?: null, $d['genero'] ?? null,
             $d['idioma'] ?? 'Español', $d['duracion_seg'] ?? 0, $d['codigo'] ?? null, $d['youtube_id'] ?? null, $id]
        );
        return true;
    }

    public static function eliminar(int $id): bool {
        Database::execute("UPDATE canciones SET activa = 0 WHERE id = ?", [$id]);
        return true;
    }

    public static function generos(): array {
        return Database::fetchAll("SELECT DISTINCT genero FROM canciones WHERE genero IS NOT NULL ORDER BY genero");
    }

    public static function contarTotal(): int {
        return Database::fetch("SELECT COUNT(*) c FROM canciones WHERE activa=1")['c'] ?? 0;
    }
}