<?php
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';

require_role('admin'); // solo admin puede acceder
$user = $_SESSION['user'];// datos de la sesión
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel Admin - Biblioteca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-option {
      transition: transform .2s;
    }
    .card-option:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="text-success">👋 Bienvenido, <?= htmlspecialchars($user['nombre']) ?> (Administrador)</h3>
      <a class="btn btn-sm btn-outline-danger" href="/biblioteca/logout.php">Cerrar sesión</a>
    </div>

    <p class="lead">Selecciona una opción para gestionar la biblioteca:</p>

    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="card card-option shadow-sm h-100">
          <div class="card-body text-center">
            <h5 class="card-title">📚 Catálogo</h5>
            <p class="card-text">Ver, editar y eliminar libros.</p>
            <a href="/biblioteca/libros/listar.php" class="btn btn-success btn-sm">Ir al catálogo</a>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card card-option shadow-sm h-100">
          <div class="card-body text-center">
            <h5 class="card-title">➕ Registrar libro</h5>
            <p class="card-text">Agregar un nuevo libro.</p>
            <a href="/biblioteca/libros/registrar.php" class="btn btn-primary btn-sm">Registrar</a>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card card-option shadow-sm h-100">
          <div class="card-body text-center">
            <h5 class="card-title">📖 Reservas</h5>
            <p class="card-text">Ver y gestionar reservas.</p>
            <a href="/biblioteca/reservas/reservas.php" class="btn btn-secondary btn-sm">Ver reservas</a>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card card-option shadow-sm h-100">
          <div class="card-body text-center">
            <h5 class="card-title">📊 Reportes</h5>
            <p class="card-text">Ver reportes básicos.</p>
            <a href="/biblioteca/reportes/" class="btn btn-warning btn-sm">Ver reportes</a>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
  <div class="card card-option shadow-sm h-100">
    <div class="card-body text-center">
      <h5 class="card-title">📂 Categorías</h5>
      <p class="card-text">Gestionar categorías de libros.</p>
      <a href="/biblioteca/categorias/listar.php" class="btn btn-info btn-sm">Ver categorías</a>
    </div>
  </div>
</div>

    </div>
  </div>
</body>
</html>