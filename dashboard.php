<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'php/db.php';

// Obtener contactos del usuario
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM contactos WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC");
$stmt->execute(['usuario_id' => $userId]);
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Agenda de Contactos - Dashboard</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <h1>Hola, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <a href="php/logout.php">Cerrar sesión</a>

    <h2>Mis Contactos</h2>
    <button onclick="showCreateForm()">Agregar Contacto</button>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nombre</th><th>Apellido</th><th>Teléfono</th><th>Email</th><th>Dirección</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody id="contactos-list">
            <?php foreach ($contactos as $c): ?>
                <tr data-id="<?= $c['id']; ?>">
                    <td><?= htmlspecialchars($c['nombre']); ?></td>
                    <td><?= htmlspecialchars($c['apellido']); ?></td>
                    <td><?= htmlspecialchars($c['telefono']); ?></td>
                    <td><?= htmlspecialchars($c['email']); ?></td>
                    <td><?= htmlspecialchars($c['direccion']); ?></td>
                    <td>
                        <button onclick="showEditForm(<?= $c['id']; ?>)">Editar</button>
                        <button onclick="deleteContacto(<?= $c['id']; ?>)">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulario oculto para crear/editar -->
    <div id="form-container" style="display:none;">
        <h3 id="form-title"></h3>
        <form id="contacto-form">
            <input type="hidden" name="id" id="contacto-id" />
            <label>Nombre: <input type="text" name="nombre" id="nombre" required /></label><br/>
            <label>Apellido: <input type="text" name="apellido" id="apellido" /></label><br/>
            <label>Teléfono: <input type="text" name="telefono" id="telefono" /></label><br/>
            <label>Email: <input type="email" name="email" id="email" /></label><br/>
            <label>Dirección: <input type="text" name="direccion" id="direccion" /></label><br/>
            <button type="submit">Guardar</button>
            <button type="button" onclick="hideForm()">Cancelar</button>
        </form>
        <div id="form-message"></div>
    </div>

    <script>
        function showCreateForm() {
            document.getElementById('form-title').innerText = 'Crear nuevo contacto';
            document.getElementById('contacto-form').reset();
            document.getElementById('contacto-id').value = '';
            document.getElementById('form-container').style.display = 'block';
            document.getElementById('form-message').innerText = '';
        }

        function showEditForm(id) {
            // Buscar datos en la tabla para llenar el formulario
            const row = document.querySelector(`tr[data-id='${id}']`);
            document.getElementById('form-title').innerText = 'Editar contacto';
            document.getElementById('contacto-id').value = id;
            document.getElementById('nombre').value = row.children[0].innerText;
            document.getElementById('apellido').value = row.children[1].innerText;
            document.getElementById('telefono').value = row.children[2].innerText;
            document.getElementById('email').value = row.children[3].innerText;
            document.getElementById('direccion').value = row.children[4].innerText;
            document.getElementById('form-container').style.display = 'block';
            document.getElementById('form-message').innerText = '';
        }

        function hideForm() {
            document.getElementById('form-container').style.display = 'none';
        }

        document.getElementById('contacto-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            let action = formData.get('id') ? 'update' : 'create';

            fetch(`php/contactos.php?action=${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('form-message').innerText = data.error;
                } else {
                    document.getElementById('form-message').innerText = data.success;
                    setTimeout(() => {
                        location.reload(); // Recargar para actualizar lista
                    }, 1000);
                }
            })
            .catch(err => {
                document.getElementById('form-message').innerText = 'Error en la solicitud.';
            });
        });

        function deleteContacto(id) {
            if (!confirm('¿Seguro querés eliminar este contacto?')) return;

            const formData = new FormData();
            formData.append('id', id);

            fetch('php/contactos.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    alert(data.success);
                    location.reload();
                }
            })
            .catch(() => alert('Error en la solicitud.'));
        }
    </script>
</body>
</html>
