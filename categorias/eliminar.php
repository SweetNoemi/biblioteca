<?php
// eliminar.php — eliminar categoría
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';
require_role('admin');

if (session_status() === PHP_SESSION_NONE) session_start();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // poner en NULL los libros que usaban esta categoría
    $mysqli->query("UPDATE libros SET categoria_id = NULL WHERE categoria_id = $id");

    $stmt = $mysqli->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $_SESSION['flash_success'] = "Categoría eliminada correctamente.";
    } else {
        $_SESSION['flash_error'] = "Error eliminando categoría: " . $stmt->error;
    }
} else {
    $_SESSION['flash_error'] = "ID de categoría inválido.";
}

header("Location: listar.php");
exit;

