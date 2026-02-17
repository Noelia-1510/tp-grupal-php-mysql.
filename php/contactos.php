<?php
session_start();
require_once 'db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener ID de usuario actual para filtrar contactos
$userId = $_SESSION['user_id'];

// Acción a realizar (create, read, update, delete)
$action = $_GET['action'] ?? 'read';

// Función para sanitizar inputs
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = sanitize($_POST['nombre'] ?? '');
            $apellido = sanitize($_POST['apellido'] ?? '');
            $telefono = sanitize($_POST['telefono'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $direccion = sanitize($_POST['direccion'] ?? '');

            // Validar nombre mínimo
            if (empty($nombre)) {
                echo json_encode(['error' => 'El nombre es obligatorio']);
                exit();
            }

            $sql = "INSERT INTO contactos (usuario_id, nombre, apellido, telefono, email, direccion) 
                    VALUES (:usuario_id, :nombre, :apellido, :telefono, :email, :direccion)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $userId,
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':telefono' => $telefono,
                ':email' => $email,
                ':direccion' => $direccion
            ]);

            echo json_encode(['success' => 'Contacto creado correctamente']);
        }
        break;

    case 'read':
        $sql = "SELECT * FROM contactos WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $userId]);
        $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retornar JSON para frontend o mostrar en tabla si prefieres HTML directo
        echo json_encode($contactos);
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $nombre = sanitize($_POST['nombre'] ?? '');
            $apellido = sanitize($_POST['apellido'] ?? '');
            $telefono = sanitize($_POST['telefono'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $direccion = sanitize($_POST['direccion'] ?? '');

            if ($id <= 0 || empty($nombre)) {
                echo json_encode(['error' => 'ID o nombre inválido']);
                exit();
            }

            $sql = "UPDATE contactos SET nombre = :nombre, apellido = :apellido, telefono = :telefono, 
                    email = :email, direccion = :direccion WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':telefono' => $telefono,
                ':email' => $email,
                ':direccion' => $direccion,
                ':id' => $id,
                ':usuario_id' => $userId
            ]);

            echo json_encode(['success' => 'Contacto actualizado correctamente']);
        }
        break;

    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['error' => 'ID inválido']);
                exit();
            }

            $sql = "DELETE FROM contactos WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id, ':usuario_id' => $userId]);

            echo json_encode(['success' => 'Contacto eliminado correctamente']);
        }
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
}
