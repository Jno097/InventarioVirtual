<?php
session_start();
include("funciones.php");

// Función para actualizar datos de sesión desde la base de datos
function actualizarSesion($id_usuario) {
    $consulta = "SELECT * FROM login WHERE id = $id_usuario";
    $resultado = baseDatos($consulta);
    
    if($row = mysqli_fetch_assoc($resultado)) {
        $_SESSION['id_usuario'] = $row['id'];
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['mail'] = $row['mail'];
        $_SESSION['cate'] = $row['cate'];
        $_SESSION['verificado'] = $row['verificado'];
        $_SESSION['curso'] = $row['curso'];
        return $row['cate'];
    }
    return false;
}

// Verificar si hay sesión activa
if(!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Debe iniciar sesión primero'); window.location='login.php';</script>";
    exit;
}

// Actualizar datos de sesión desde la base de datos para asegurar que estén correctos
$categoria_actual = actualizarSesion($_SESSION['id_usuario']);

// Verificar si el usuario es administrador
if($categoria_actual != 'admin' && $categoria_actual != 1) {
    echo "<script>alert('Acceso no autorizado. Solo administradores pueden acceder.'); window.location='../backend.php';</script>";
    exit;
}

// Variables para mensajes
$success_message = "";
$error_message = "";

// Procesar acciones POST
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // VERIFICAR O RECHAZAR USUARIOS
    if(isset($_POST['id_usuario']) && isset($_POST['accion'])) {
        $id_usuario = (int)$_POST['id_usuario'];
        $accion = sanitizar($_POST['accion']);
        
        if($accion == 'verificar') {
            // Actualizar el estado del usuario a "verificado" en la tabla login
            $consulta = "UPDATE login SET verificado = 1 WHERE id = $id_usuario AND cate = 'profe'";
            $resultado = baseDatos($consulta);
            
            if($resultado) {
                // También eliminar cualquier entrada en comentario si existe
                $consulta_comentario = "DELETE FROM comentario WHERE id = $id_usuario";
                baseDatos($consulta_comentario);
                
                $success_message = "Profesor verificado correctamente";
            } else {
                $error_message = "Error al verificar el profesor";
            }
            
        } elseif($accion == 'rechazar') {
            // Eliminar el usuario rechazado de la tabla login
            $consulta = "DELETE FROM login WHERE id = $id_usuario AND cate = 'profe'";
            $resultado = baseDatos($consulta);
            
            // También eliminar cualquier entrada en comentario si existe
            $consulta_comentario = "DELETE FROM comentario WHERE id = $id_usuario";
            baseDatos($consulta_comentario);
            
            if($resultado) {
                $success_message = "Profesor rechazado y eliminado correctamente";
            } else {
                $error_message = "Error al rechazar el profesor";
            }
        }
    }
    
    // ELIMINAR USUARIOS
    if(isset($_POST['eliminar_usuario'])) {
        $id_usuario = (int)$_POST['eliminar_usuario'];
        
        // No permitir eliminar el administrador principal
        if($id_usuario == $_SESSION['id_usuario']) {
            $error_message = "No puede eliminar su propia cuenta de administrador";
        } else {
            // Eliminar el usuario de la tabla login
            $consulta = "DELETE FROM login WHERE id = $id_usuario";
            $resultado = baseDatos($consulta);
            
            if($resultado) {
                $success_message = "Usuario eliminado correctamente";
            } else {
                $error_message = "Error al eliminar el usuario";
            }
        }
    }
}

// Obtener usuarios pendientes de verificación
$consulta_pendientes = "SELECT * FROM login WHERE cate = 'profe' AND verificado = 0";
$usuarios_pendientes = baseDatos($consulta_pendientes);

// Obtener todos los usuarios (excepto el admin actual)
$consulta_usuarios = "SELECT * FROM login WHERE id != " . $_SESSION['id_usuario'] . " ORDER BY cate, nombre";
$todos_usuarios = baseDatos($consulta_usuarios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn:hover { opacity: 0.8; }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .section {
            margin-bottom: 40px;
        }
        .section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Panel de Administración</h1>
            <div>
                <span>Bienvenido, <?php echo sanitizar($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="btn btn-secondary">Cerrar Sesión</a>
            </div>
        </div>

        <?php if($success_message): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
<!-- Sección de todos los usuarios -->
<div class="section">
            <h2>Gestión de Usuarios</h2>
            <?php if(mysqli_num_rows($todos_usuarios) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Curso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($usuario = mysqli_fetch_assoc($todos_usuarios)): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo sanitizar($usuario['nombre']); ?></td>
                                <td><?php echo sanitizar($usuario['mail']); ?></td>
                                <td>
                                    <?php 
                                    $categoria = $usuario['cate'];
                                    if($categoria == 'admin') {
                                        echo '<span class="badge badge-info">Administrador</span>';
                                    } elseif($categoria == 'profe') {
                                        echo '<span class="badge badge-secondary">Profesor</span>';
                                    } else {
                                        echo '<span class="badge badge-secondary">' . sanitizar($categoria) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if($usuario['verificado'] == 1): ?>
                                        <span class="badge badge-success">Verificado</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo sanitizar($usuario['curso']); ?></td>
                                <td>
                                    <?php if($usuario['cate'] != 'admin'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="eliminar_usuario" value="<?php echo $usuario['id']; ?>">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este usuario?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-info">Administrador</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay otros usuarios registrados.</p>
            <?php endif; ?>
        </div>

        <!-- Sección de usuarios pendientes de verificación -->
        <div class="section">
            <h2>Profesores Pendientes de Verificación</h2>
            <?php if(mysqli_num_rows($usuarios_pendientes) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Curso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($usuario = mysqli_fetch_assoc($usuarios_pendientes)): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo sanitizar($usuario['nombre']); ?></td>
                                <td><?php echo sanitizar($usuario['mail']); ?></td>
                                <td><?php echo sanitizar($usuario['curso']); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                                        <input type="hidden" name="accion" value="verificar">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('¿Verificar este profesor?')">
                                            Verificar
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                                        <input type="hidden" name="accion" value="rechazar">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Rechazar este profesor? Se eliminará de la base de datos.')">
                                            Rechazar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay profesores pendientes de verificación.</p>
            <?php endif; ?>
        </div>

        
        <!-- Enlaces de navegación -->
<!-- Dentro de la sección de navegación en admin.php -->
<div class="section">
    <h2>Navegación</h2>
    <a href="../backend.php" class="btn btn-primary">Volver al Inicio</a>
    <a href="../modificar/inventario.php" class="btn btn-primary">Gestionar Inventario</a>
    <a href="../modificar/armarios/agregar_armario.php" class="btn btn-primary">Gestionar Armarios</a>
</div>
    </div>
</body>
</html>