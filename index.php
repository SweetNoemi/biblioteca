<?php
require_once 'conexion.php';
require_once 'inc/auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($codigo === '' || $password === '') {
        $errors[] = "Rellena código y contraseña.";
    } else {
        $stmt = $mysqli->prepare("SELECT id, codigo, nombre, password, role FROM users WHERE codigo = ? LIMIT 1");
        $stmt->bind_param('s', $codigo);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                login_user($row);
                // redirigir según rol
                if ($row['role'] === 'admin') {
                    header("Location: /biblioteca/dashboards/admin.php");
                    exit;
                } else {
                    header("Location: /biblioteca/dashboards/alumno.php");
                    exit;
                }
            } else {
                $errors[] = "Código o contraseña incorrectos.";
            }
        } else {
            $errors[] = "Código o contraseña incorrectos.";
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Biblioteca Montserrat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="col-md-5">
    <div class="card login-card p-4 text-center">
      <!-- Logo del colegio -->
      <img src="assets/img/logo.jpg" alt="Logo Colegio" class="logo mx-auto d-block">

      <!-- Nombre de la institución -->
      <h5 class="text-muted mb-1">Institución Educativa</h5>
      <h3 class="login-title mb-4">Nuestra Señora de Montserrat</h3>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger text-start">
          <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
        </div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3 text-start">
          <label class="form-label">Código</label>
          <input type="text" name="codigo" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-login w-100 text-white">Iniciar sesión</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>

