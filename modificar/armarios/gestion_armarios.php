<?php
session_start();
include("../funciones.php");

// Verificar sesión y permisos
if(!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Debe iniciar sesión primero'); window.location='../usuarios/login.php';</script>";
    exit;
}

$consulta_admin = "SELECT cate, verificado FROM login WHERE id = " . $_SESSION['id_usuario'];
$resultado_admin = baseDatos($consulta_admin);
$row_admin = mysqli_fetch_assoc($resultado_admin);

$acceso_permitido = false;
if($row_admin['verificado'] == '1' && ($row_admin['cate'] == 'admin' || $row_admin['cate'] == 'profe')) {
    $acceso_permitido = true;
}

if(!$acceso_permitido) {
    echo "<script>alert('Acceso no autorizado'); window.location='../../backend.php';</script>";
    exit;
}

// Variables para mensajes
$success_message = isset($_GET['success']) ? $_GET['success'] : '';
$error_message = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Armarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1400px;
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
            font-size: 14px;
        }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-warning { background-color: #ffc107; color: #212529; }
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
        form.inline-form {
            display: inline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestión de Armarios</h1>
            <div>
                <a href="agregar_armario.php" class="btn btn-success">+ Agregar Armario</a>
                <a href="../inventario.php" class="btn btn-primary">Gestionar Inventario</a>
                <a href="../../usuarios/admin.php" class="btn btn-secondary">Volver a Administración</a>
            </div>
        </div>

        <?php if($success_message): ?>
            <div class="alert alert-success">
                <?php 
                switch($success_message) {
                    case 'added': echo "Armario agregado correctamente"; break;
                    case 'updated': echo "Armario actualizado correctamente"; break;
                    case 'deleted': echo "Armario eliminado correctamente"; break;
                    default: echo "Operación realizada con éxito";
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="alert alert-danger">
                <?php 
                switch($error_message) {
                    case 'add_failed': echo "Error al agregar el armario"; break;
                    case 'update_failed': echo "Error al actualizar el armario"; break;
                    case 'delete_failed': echo "Error al eliminar el armario"; break;
                    case 'has_products': echo "No se puede eliminar: tiene productos asociados"; break;
                    case 'invalid_id': echo "ID de armario no válido"; break;
                    default: echo "Error en la operación";
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Lista de armarios -->
        <div class="section">
            <h2>Armarios Existentes</h2>
            <?php
            $consulta = "SELECT a.*, COUNT(i.id) as productos 
                        FROM armarios a 
                        LEFT JOIN inventario i ON a.id_tabla = i.id_tabla 
                        GROUP BY a.id_tabla 
                        ORDER BY a.nombre";
            $armarios = baseDatos($consulta);
            ?>
            
            <?php if(mysqli_num_rows($armarios) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Descripción</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($armario = mysqli_fetch_assoc($armarios)): ?>
                            <tr>
                                <td><?php echo $armario['id_tabla']; ?></td>
                                <td><?php echo htmlspecialchars($armario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($armario['ubicacion']); ?></td>
                                <td><?php echo htmlspecialchars($armario['descrip']); ?></td>
                                <td><?php echo $armario['productos']; ?></td>
                                <td>
                                    <a href="editar_armario.php?id=<?php echo $armario['id_tabla']; ?>" class="btn btn-warning">Editar</a>
                                    <?php if($armario['productos'] == 0): ?>
                                        <form method="POST" action="borrar_armario_proc.php" class="inline-form">
                                            <input type="hidden" name="id" value="<?php echo $armario['id_tabla']; ?>">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este armario?')">Eliminar</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled title="No se puede eliminar: tiene <?php echo $armario['productos']; ?> productos">Eliminar</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay armarios registrados.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>