<?php
// editar.php — editar categoría (solo admin)
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';
require_role('admin');

if (session_status() === PHP_SESSION_NONE) session_start();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// obtener ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID de categoría inválido.");
}

// obtener categoría actual
$stmt = $mysqli->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$categoria = $stmt->get_result()->fetch_assoc();
if (!$categoria) {
    die("Categoría no encontrada.");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['csrf'] !== $_SESSION['csrf_token']) {
        $errors[] = "Token inválido. Recarga la página e inténtalo de nuevo.";
    } else {
        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            $errors[] = "El nombre de la categoría es obligatorio.";
        } else {
            // verificar si existe otra con el mismo nombre
            $stmt = $mysqli->prepare("SELECT id FROM categorias WHERE nombre = ? AND id != ?");
            $stmt->bind_param("si", $nombre, $id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows > 0) {
                $errors[] = "Ya existe otra categoría con ese nombre.";
            } else {
                $stmt = $mysqli->prepare("UPDATE categorias SET nombre = ? WHERE id = ?");
                $stmt->bind_param("si", $nombre, $id);
                if ($stmt->execute()) {
                    $_SESSION['flash_success'] = "Categoría actualizada correctamente.";
                    header("Location: listar.php");
                    exit;
                } else {
                    $errors[] = "Error al actualizar categoría: " . $stmt->error;
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar categoría - Biblioteca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="text-success">✏️ Editar categoría</h4>
      <a href="listar.php" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
      </div>
    <?php endif; ?>

    <div class="card p-3 shadow-sm">
      <form method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <div class="mb-3">
          <label class="form-label">Nombre de la categoría</label>
          <input type="text" name="nombre" class="form-control"
                 value="<?= htmlspecialchars($_POST['nombre'] ?? $categoria['nombre']) ?>" required>
        </div>
        <button class="btn btn-success">Guardar cambios</button>
      </form>
    </div>
  </div>
</body>
</html>
