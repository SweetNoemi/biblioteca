<?php
// eliminar.php — eliminar libro
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';
require_role('admin');

if (session_status() === PHP_SESSION_NONE) session_start();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // eliminar el libro
    $stmt = $mysqli->prepare("DELETE FROM libros WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $_SESSION['flash_success'] = "Libro eliminado correctamente.";
    } else {
        $_SESSION['flash_error'] = "Error eliminando libro: " . $stmt->error;
    }
} else {
    $_SESSION['flash_error'] = "ID de libro inválido.";
}

header("Location: listar.php");
exit;
