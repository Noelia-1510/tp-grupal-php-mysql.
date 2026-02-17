<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php'; // conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        header("Location: ../index.php?error=Datos inválidos");
        exit();
    }

    // Buscar usuario
    $stmt = $pdo->prepare("SELECT id, nombre, password FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_name'] = $usuario['nombre'];
        header("Location: ../dashboard.php");
        exit();
    } else {
        header("Location: ../index.php?error=Email o contraseña incorrectos");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
