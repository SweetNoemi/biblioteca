<?php
// reservar.php — reservar libro (alumno)
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';
require_role('alumno');

if (session_status() === PHP_SESSION_NONE) session_start();

$id = intval($_GET['id'] ?? 0); // id del libro
if ($id <= 0) {
    die("Libro no válido.");
}

// obtener datos del libro
$stmt = $mysqli->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) {
    die("Libro no encontrado.");
}
if ($libro['cantidad_disponible'] <= 0) {
    die("Este libro no está disponible para reservar.");
}

// procesar reserva
$user_id = $_SESSION['user_id'];
$comprobante = strtoupper(bin2hex(random_bytes(4))); // código de reserva único

$stmt = $mysqli->prepare("INSERT INTO reservas (libro_id, user_id, comprobante_codigo) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $id, $user_id, $comprobante);

if ($stmt->execute()) {
    // actualizar cantidad disponible
    $mysqli->query("UPDATE libros SET cantidad_disponible = cantidad_disponible - 1 WHERE id = $id");

    // obtener datos del alumno
    $stmtU = $mysqli->prepare("SELECT nombre, codigo FROM users WHERE id = ?");
    $stmtU->bind_param("i", $user_id);
    $stmtU->execute();
    $usuario = $stmtU->get_result()->fetch_assoc();
} else {
    die("Error al registrar la reserva: " . $stmt->error);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Comprobante de reserva</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="card shadow-sm p-4">
      <h3 class="text-success mb-3">📖 Comprobante de reserva</h3>

      <p><strong>Alumno:</strong> <?= htmlspecialchars($usuario['nombre']) ?> (<?= htmlspecialchars($usuario['codigo']) ?>)</p>
      <p><strong>Libro:</strong> <?= htmlspecialchars($libro['titulo']) ?> — <?= htmlspecialchars($libro['autor']) ?></p>
      <p><strong>Código de reserva:</strong> <span class="fw-bold"><?= $comprobante ?></span></p>
      <p><strong>Fecha:</strong> <?= date("d/m/Y H:i") ?></p>
      <p><span class="badge bg-warning">Estado: Reservado</span></p>

      <div class="mt-4">
        <a href="/biblioteca/dashboards/alumno.php" class="btn btn-outline-secondary">Volver al panel</a>
        <button class="btn btn-primary" onclick="window.print()">Imprimir comprobante</button>
      </div>
    </div>
  </div>
</body>
</html>
