<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Contactos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>📒 Agenda de Contactos</h2>
    <p style="text-align:center;">Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
    <div style="text-align:center;">
        <a href="../dashboard.php">🏠 Volver al Dashboard</a>
        <a href="logout.php">🔒 Cerrar sesión</a>
    </div>

    <table id="tablaContactos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Se cargan con JS -->
        </tbody>
    </table>

    <script>
        async function cargarContactos() {
            try {
                const respuesta = await fetch('contactos.php?action=read');
                const datos = await respuesta.json();

                const tbody = document.querySelector('#tablaContactos tbody');
                tbody.innerHTML = ''; // Limpiar

                if (Array.isArray(datos)) {
                    datos.forEach(contacto => {
                        const fila = document.createElement('tr');

                        fila.innerHTML = `
                            <td>${contacto.nombre}</td>
                            <td>${contacto.apellido || ''}</td>
                            <td>${contacto.telefono || ''}</td>
                            <td>${contacto.email || ''}</td>
                            <td>${contacto.direccion || ''}</td>
                            <td>
                                <button onclick="editar(${contacto.id})">✏️</button>
                                <button onclick="eliminar(${contacto.id})">🗑️</button>
                            </td>
                        `;

                        tbody.appendChild(fila);
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="6">No se encontraron contactos.</td></tr>`;
                }
            } catch (error) {
                console.error('Error al cargar contactos:', error);
            }
        }

        function editar(id) {
            alert('Funcionalidad de editar pendiente para ID: ' + id);
        }

        function eliminar(id) {
            if (confirm('¿Seguro que querés eliminar este contacto?')) {
                fetch('contactos.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                })
                .then(resp => resp.json())
                .then(data => {
                    if (data.success) {
                        alert(data.success);
                        cargarContactos();
                    } else {
                        alert(data.error || 'Error al eliminar');
                    }
                });
            }
        }

        cargarContactos();
    </script>
</body>
</html>
