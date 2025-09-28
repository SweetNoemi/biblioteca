<?php
// create_users.php — Ejecutar UNA vez desde el navegador y luego BORRAR por seguridad
require_once 'conexion.php';

$users = [
    ['codigo'=>'admin001','nombre'=>'Administrador','email'=>'admin@biblioteca.local','password'=>'AdminPass123!','role'=>'admin'],
    ['codigo'=>'alumno001','nombre'=>'Juan Pérez','email'=>'juan@ejemplo.com','password'=>'Alumno123!','role'=>'alumno'],
    ['codigo'=>'alumno002','nombre'=>'María López','email'=>'maria@ejemplo.com','password'=>'Alumno456!','role'=>'alumno']
];

foreach ($users as $u) {
    // Verificar existencia
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE codigo = ?");
    $stmt->bind_param('s', $u['codigo']);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r->num_rows > 0) {
        echo "Ya existe usuario: {$u['codigo']}<br>";
        continue;
    }
    $hash = password_hash($u['password'], PASSWORD_DEFAULT);
    $ins = $mysqli->prepare("INSERT INTO users (codigo, nombre, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $ins->bind_param('sssss', $u['codigo'], $u['nombre'], $u['email'], $hash, $u['role']);
    if ($ins->execute()) {
        echo "Creado: {$u['codigo']} (contraseña: {$u['password']})<br>";
    } else {
        echo "Error creando {$u['codigo']}: " . $ins->error . "<br>";
    }
}
echo "<hr>¡Terminado! Borra este archivo create_users.php por seguridad.";
?>
