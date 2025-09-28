<?php
// listar.php — catálogo de libros agrupados por categorías
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';
require_role('admin');

if (session_status() === PHP_SESSION_NONE) session_start();

// obtener libros agrupados por categoría
$sql = "SELECT l.id, l.titulo, l.autor, l.cantidad_disponible, l.cantidad_total, l.isbn, c.nombre AS categoria
        FROM libros l
        LEFT JOIN categorias c ON l.categoria_id = c.id
        ORDER BY c.nombre, l.titulo";
$result = $mysqli->query($sql);

$categorias = [];
while ($row = $result->fetch_assoc()) {
    $cat = $row['categoria'] ?? 'Sin categoría';
    $categorias[$cat][] = $row;
}

// mensajes flash
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Catálogo de libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-book {
      transition: transform .2s;
    }
    .card-book:hover {
      transform: scale(1.03);
    }
    .book-img {
      max-height: 180px;
      object-fit: cover;
    }
  </style>
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="text-success">📚 Catálogo de libros</h3>
      <div>
        <a href="/biblioteca/dashboards/admin.php" class="btn btn-outline-secondary btn-sm">Volver</a>
        <a href="/biblioteca/libros/registrar.php" class="btn btn-primary btn-sm">+ Registrar libro</a>
      </div>
    </div>

    <?php if ($flash_success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div>
    <?php endif; ?>

    <?php if (empty($categorias)): ?>
      <div class="alert alert-info">No hay libros registrados todavía.</div>
    <?php else: ?>
      <?php foreach ($categorias as $cat => $libros): ?>
        <h4 class="mt-4"><?= htmlspecialchars($cat) ?></h4>
        <div class="row">
          <?php foreach ($libros as $l): ?>
            <div class="col-md-3 mb-3">
              <div class="card card-book shadow-sm h-100">
                <img src="/biblioteca/assets/img/libro.png" class="card-img-top book-img" alt="Libro">
                <div class="card-body">
                  <h6 class="card-title"><?= htmlspecialchars($l['titulo']) ?></h6>
                  <p class="card-text"><small><?= htmlspecialchars($l['autor']) ?></small></p>
                  <p>
                    <span class="badge <?= $l['cantidad_disponible'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                      <?= $l['cantidad_disponible'] > 0 ? 'Disponible' : 'No disponible' ?>
                    </span>
                  </p>
                  <p class="text-muted mb-1">ID: <?= $l['id'] ?></p>
                </div>
                <div class="card-footer d-flex justify-content-between">
                  <a href="editar.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                  <a href="eliminar.php?id=<?= $l['id'] ?>"
                     onclick="return confirm('¿Eliminar el libro <?= htmlspecialchars($l['titulo']) ?>?');"
                     class="btn btn-sm btn-outline-danger">
                     Eliminar
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>


