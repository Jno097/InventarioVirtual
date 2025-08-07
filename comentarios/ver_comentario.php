<?php
session_start();
include("funciones.php");

// Verificar si el usuario es admin o profesor
$es_admin = isset($_SESSION['cate']) && ($_SESSION['cate'] == 'admin' || $_SESSION['cate'] == 'profe');

// Obtener todos los comentarios con información de armarios y usuarios
$consulta = "SELECT c.*, a.nombre as nombre_armario, u.nombre as nombre_usuario 
             FROM comentario c
             JOIN armarios a ON c.id_tabla = a.id_tabla
             JOIN login u ON c.id = u.id
             ORDER BY c.fecha DESC";
$resultado = baseDatos($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Comentarios - Ynventaris</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../backend.css">
</head>
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
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .producto-imagen {
            max-width: 60px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover { color: black; }
    </style>
<body>
    
        <div class="container">
        <div class="header">
            <h1>Gestión de Armarios</h1>
            <div>
    <a href="../modificar/productos/inventario.php" class="btn btn-primary">Gestionar Inventario</a>
    <a href="../usuarios/admin.php" class="btn btn-primary">Gestionar Usuarios</a>
    <a href="../modificar/armarios/gestion_armarios.php" class="btn btn-primary">Gestionar Armarios</a>
    <a href="../backend.php" class="btn btn-secundary">Volver al Inicio</a>
            </div>
        </div>
       

    <main>
        <h1>Todos los comentarios</h1>
        
        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <div class="lista-completa">
                <?php while ($comentario = mysqli_fetch_assoc($resultado)): ?>
                    <div class="comentario-detallado">
                        <div class="comentario-cabecera">
                            <h2><?php echo htmlspecialchars($comentario['titulo']); ?></h2>
                            <span class="fecha"><?php echo $comentario['fecha']; ?></span>
                        </div>
                        <p class="usuario">Por: <?php echo htmlspecialchars($comentario['nombre_usuario']); ?></p>
                        <p class="armario">Armario: <?php echo htmlspecialchars($comentario['nombre_armario']); ?></p>
                        <div class="comentario-contenido">
                            <p><?php echo htmlspecialchars($comentario['descripcion']); ?></p>
                        </div>
                        
                        <?php if ($es_admin || $_SESSION['id'] == $comentario['id']): ?>
                            <form action="eliminar_comentario.php" method="post" class="form-eliminar">
                                <input type="hidden" name="id_comentario" value="<?php echo $comentario['id_com']; ?>">
                                <button type="submit" onclick="return confirm('¿Eliminar este comentario permanentemente?')">
                                    Eliminar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No hay comentarios registrados.</p>
        <?php endif; ?>
    </main>
</body>
</html>