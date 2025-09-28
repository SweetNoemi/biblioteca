<?php
// listar.php — listar categorías
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';
require_role('admin');

if (session_status() === PHP_SESSION_NONE) session_start();

// Obtener categorías
$result = $mysqli->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = [];
while ($row = $result->fetch_assoc()) $categorias[] = $row;

// mensajes flash
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Categorías - Biblioteca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="text-success">📂 Categorías</h3>
      <div>
        <a href="/biblioteca/dashboards/admin.php" class="btn btn-outline-secondary btn-sm">Volver</a>
        <a href="registrar.php" class="btn btn-primary btn-sm">+ Nueva categoría</a>
      </div>
    </div>

    <?php if ($flash_success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($categorias)): ?>
              <tr><td colspan="3" class="text-center text-muted">No hay categorías registradas.</td></tr>
            <?php else: ?>
              <?php foreach ($categorias as $c): ?>
                <tr>
                  <td><?= $c['id'] ?></td>
                  <td><?= htmlspecialchars($c['nombre']) ?></td>
                  <td class="text-end">
                    <a href="editar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <a href="eliminar.php?id=<?= $c['id'] ?>"
                       onclick="return confirm('¿Eliminar la categoría <?= htmlspecialchars($c['nombre']) ?>?\nLos libros asociados quedarán sin categoría.');"
                       class="btn btn-sm btn-outline-danger">
                       Eliminar
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>

