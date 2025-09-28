<?php
// inc/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function login_user($row) {
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $row['id'],
        'codigo' => $row['codigo'],
        'nombre' => $row['nombre'],
        'role' => $row['role']
    ];
}

function is_logged_in() {
    return !empty($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: /biblioteca/index.php?error=login");
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo "<h3>Acceso denegado</h3>";
        echo "<p>No tienes permisos para ver esta página.</p>";
        echo "<a href='/biblioteca/index.php'>Volver</a>";
        exit;
    }
}

function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
?>
