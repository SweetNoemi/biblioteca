<?php
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';

require_role('alumno'); // solo alumnos
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel Alumno - Biblioteca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="text-primary">📚 Bienvenido, <?= htmlspecialchars($user['nombre']) ?> (Alumno)</h3>
      <a class="btn btn-sm btn-outline-danger" href="/biblioteca/logout.php">Cerrar sesión</a>
    </div>
    <p class="lead">Aquí podrás ver el catálogo de libros y hacer reservas.</p>

    <a href="/biblioteca/reservas/reservar.php?id=<?= $l['id'] ?>"
   class="btn btn-sm btn-success"
   onclick="return confirm('¿Deseas reservar este libro?');">
   Reservar
   </a>

  </div>
</body>
</html>
