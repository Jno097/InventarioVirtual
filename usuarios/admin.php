<?php
session_start();
require_once "funciones.php";

// Verificar permisos de administrador
verificarAdmin();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['accion']) && isset($_POST['id_usuario'])) {
            $id_usuario = (int)$_POST['id_usuario'];
            $accion = sanitizar($_POST['accion']);
            
            if ($accion == 'verificar') {
                $query = "UPDATE login SET verificado = 1 WHERE id = $id_usuario AND cate = 'profe'";
                baseDatos($query);
                $success_message = "Usuario verificado correctamente";
            } elseif ($accion == 'eliminar') {
                if ($id_usuario == $_SESSION['user_id']) {
                    throw new Exception("No puedes eliminar tu propia cuenta");
                }
                
                $query = "DELETE FROM login WHERE id = $id_usuario";
                baseDatos($query);
                $success_message = "Usuario eliminado correctamente";
            }
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Obtener todos los usuarios
$query = "SELECT id, nombre, mail, cate, verificado, curso FROM login ORDER BY cate, verificado DESC";
$result = baseDatos($query);
$usuarios = [];

while ($row = mysqli_fetch_assoc($result)) {
    $row['nombre'] = desencriptar($row['nombre']);
    $row['mail'] = desencriptar($row['mail']);
    $row['curso'] = !empty($row['curso']) ? desencriptar($row['curso']) : '';
    $usuarios[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Ynventaris</title>
    <link rel="stylesheet" href="../estilos.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #f8f9fa;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-primary {
            background: #007bff;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #212529;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-secondary {
            background: #6c757d;
            color: white;
        }
        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }
        .nav-links {
            margin-top: 20px;
        }
        .nav-links a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1>Panel de Administración</h1>
            <div>
                <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <h2>Gestión de Usuarios</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Curso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['mail']); ?></td>
                        <td>
                            <?php if ($usuario['cate'] == 'admin'): ?>
                                <span class="badge badge-primary">Administrador</span>
                            <?php elseif ($usuario['cate'] == 'profe'): ?>
                                <span class="badge badge-secondary">Profesor</span>
                            <?php else: ?>
                                <span class="badge"><?php echo htmlspecialchars($usuario['cate']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($usuario['verificado'] == 1): ?>
                                <span class="badge badge-success">Verificado</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($usuario['curso']); ?></td>
                        <td>
                            <?php if ($usuario['cate'] == 'profe' && $usuario['verificado'] == 0): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                                    <input type="hidden" name="accion" value="verificar">
                                    <button type="submit" class="btn btn-success btn-sm">Verificar</button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                        Eliminar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="nav-links">
            <a href="../backend.php" class="btn">Volver al Inicio</a>
            <a href="../modificar/inventario.php" class="btn">Gestionar Inventario</a>
        </div>
    </div>
</body>
</html>