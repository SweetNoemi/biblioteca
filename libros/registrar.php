<?php
// registrar.php  — agregar libro (solo admin)
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../conexion.php';
require_role('admin'); // sólo admin

// iniciar sesión (auth.php normalmente ya lo hace)
if (session_status() === PHP_SESSION_NONE) session_start();

// CSRF token simple
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

$errors = [];

# obtener categorías para el select
$cats = [];
$res = $mysqli->query("SELECT id, nombre FROM categorias ORDER BY nombre");
while ($r = $res->fetch_assoc()) $cats[] = $r;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $errors[] = "Token inválido. Recarga la página e inténtalo otra vez.";
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
            $stmt = $mysqli->prepare("
                INSERT INTO libros (titulo, autor, categoria_id, cantidad_total, cantidad_disponible, isbn, descripcion)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $cantidad_disponible = $cantidad_total;
            // tipos: s (titulo), s (autor), i (categoria), i (cantidad_total), i (cantidad_disponible), s (isbn), s (descripcion)
            $stmt->bind_param('ssiiiss', $titulo, $autor, $categoria_id, $cantidad_total, $cantidad_disponible, $isbn, $descripcion);
            if ($stmt->execute()) {
                $_SESSION['flash_success'] = "Libro registrado correctamente.";
                header('Location: /biblioteca/libros/listar.php');
                exit;
            } else {
                $errors[] = "Error al guardar el libro: " . $stmt->error;
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registrar libro - Biblioteca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="text-success">Registrar libro</h4>
      <div>
        <a href="/biblioteca/dashboards/admin.php" class="btn btn-outline-secondary btn-sm">Volver al panel</a>
        <a href="/biblioteca/libros/listar.php" class="btn btn-primary btn-sm">Ver catálogo</a>
      </div>
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
          <label class="form-label">Título <span class="text-danger">*</span></label>
          <input type="text" name="titulo" class="form-control" value="<?=htmlspecialchars($_POST['titulo'] ?? '')?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Autor</label>
          <input type="text" name="autor" class="form-control" value="<?=htmlspecialchars($_POST['autor'] ?? '')?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Categoría <span class="text-danger">*</span></label>
          <select name="categoria_id" class="form-select" required>
            <option value="0">-- Seleccionar --</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= $c['id'] ?>" <?= (isset($_POST['categoria_id']) && intval($_POST['categoria_id'])===intval($c['id'])) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Si no hay categorías, créalas en phpMyAdmin o pídemelo y te hago la interfaz.</div>
        </div>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Cantidad total <span class="text-danger">*</span></label>
            <input type="number" name="cantidad_total" min="1" class="form-control" value="<?=htmlspecialchars($_POST['cantidad_total'] ?? 1)?>">
          </div>
          <div class="col-md-8 mb-3">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control" value="<?=htmlspecialchars($_POST['isbn'] ?? '')?>">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" rows="3" class="form-control"><?=htmlspecialchars($_POST['descripcion'] ?? '')?></textarea>
        </div>

        <button class="btn btn-success">Guardar libro</button>
      </form>
    </div>
  </div>
</body>
</html>
