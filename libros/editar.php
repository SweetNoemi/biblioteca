<?php
// editar.php — editar libro (solo admin)
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';
require_role('admin');

if (session_status() === PHP_SESSION_NONE) session_start();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID de libro inválido.");
}

// obtener datos actuales del libro
$stmt = $mysqli->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
if (!$book) die("Libro no encontrado.");

// categorías
$cats = [];
$res = $mysqli->query("SELECT id, nombre FROM categorias ORDER BY nombre");
while ($r = $res->fetch_assoc()) $cats[] = $r;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['csrf'] !== $_SESSION['csrf_token']) {
        $errors[] = "Token inválido. Recarga la página e inténtalo de nuevo.";
    } else {
        $titulo = trim($_POST['titulo'] ?? '');
        $autor  = trim($_POST['autor'] ?? '');
        $categoria_id = intval($_POST['categoria_id'] ?? 0);
        $cantidad_total = intval($_POST['cantidad_total'] ?? 1);
        $isbn = trim($_POST['isbn'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($titulo === '') $errors[] = "El título es obligatorio.";
        if ($categoria_id <= 0) $errors[] = "Selecciona una categoría.";
        if ($cantidad_total <= 0) $errors[] = "La cantidad total debe ser al menos 1.";

        if (empty($errors)) {
            // recalcular disponibilidad si cambió la cantidad total
            $diferencia = $cantidad_total - $book['cantidad_total'];
            $nueva_disponible = max(0, $book['cantidad_disponible'] + $diferencia);

            $stmt = $mysqli->prepare("
                UPDATE libros
                SET titulo=?, autor=?, categoria_id=?, cantidad_total=?, cantidad_disponible=?, isbn=?, descripcion=?
                WHERE id=?
            ");
            $stmt->bind_param(
                "ssiiissi",
                $titulo,
                $autor,
                $categoria_id,
                $cantidad_total,
                $nueva_disponible,
                $isbn,
                $descripcion,
                $id
            );
            if ($stmt->execute()) {
                $_SESSION['flash_success'] = "Libro actualizado correctamente.";
                header("Location: listar.php");
                exit;
            } else {
                $errors[] = "Error al actualizar: " . $stmt->error;
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar libro - Biblioteca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="text-success">✏️ Editar libro</h4>
      <a href="listar.php" class="btn btn-outline-secondary btn-sm">Volver al catálogo</a>
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
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control"
                 value="<?= htmlspecialchars($_POST['titulo'] ?? $book['titulo']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Autor</label>
          <input type="text" name="autor" class="form-control"
                 value="<?= htmlspecialchars($_POST['autor'] ?? $book['autor']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Categoría</label>
          <select name="categoria_id" class="form-select" required>
            <option value="0">-- Seleccionar --</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= $c['id'] ?>"
                <?= (($_POST['categoria_id'] ?? $book['categoria_id']) == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Cantidad total</label>
            <input type="number" name="cantidad_total" min="1" class="form-control"
                   value="<?= htmlspecialchars($_POST['cantidad_total'] ?? $book['cantidad_total']) ?>">
            <div class="form-text">Actual: <?= $book['cantidad_total'] ?> total,
              <?= $book['cantidad_disponible'] ?> disponibles</div>
          </div>
          <div class="col-md-8 mb-3">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control"
                   value="<?= htmlspecialchars($_POST['isbn'] ?? $book['isbn']) ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" rows="3" class="form-control"><?= htmlspecialchars($_POST['descripcion'] ?? $book['descripcion']) ?></textarea>
        </div>

        <button class="btn btn-success">Guardar cambios</button>
      </form>
    </div>
  </div>
</body>
</html>
