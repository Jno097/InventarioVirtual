<?php
session_start();
include("funciones.php");

// Verificar sesión y permisos
if(!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Debe iniciar sesión primero'); window.location='../../usuarios/login.php';</script>";
    exit;
}

$consulta_admin = "SELECT cate, verificado FROM login WHERE id = " . $_SESSION['id_usuario'];
$resultado_admin = baseDatos($consulta_admin);
$row_admin = mysqli_fetch_assoc($resultado_admin);

$acceso_permitido = false;
if($row_admin['verificado'] == '1') {
    if($row_admin['cate'] == 'admin' || $row_admin['cate'] == 'profe') {
        $acceso_permitido = true;
    }
}

if(!$acceso_permitido) {
    echo "<script>alert('Acceso no autorizado'); window.location='../../backend.php';</script>";
    exit;
}

// Procesar formulario de edición
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['boton'])) {
    $id = intval($_POST['id']);
    $nombre = sanitizar($_POST['nombre']);
    $ubicacion = sanitizar($_POST['ubicacion']);
    $descripcion = sanitizar($_POST['descripcion']);
    
    // Conexión para el escape seguro
    $conexion = mysqli_connect("localhost", "root", "");
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    mysqli_select_db($conexion, "inventario");
    
    // Escapar los datos
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $ubicacion = mysqli_real_escape_string($conexion, $ubicacion);
    $descripcion = mysqli_real_escape_string($conexion, $descripcion);
    
    // Actualizar el armario
    $consulta = "UPDATE armarios SET 
                nombre = '$nombre', 
                ubicacion = '$ubicacion', 
                descrip = '$descripcion' 
                WHERE id_tabla = $id";
    
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    
    if($resultado) {
        echo "<script>alert('Armario actualizado correctamente'); window.location='gestion_armarios.php?success=updated';</script>";
    } else {
        echo "<script>alert('Error al actualizar el armario');</script>";
    }
}

// Obtener datos del armario a editar
$id_armario = isset($_GET['id']) ? intval($_GET['id']) : 0;
$armario = null;

if($id_armario > 0) {
    $consulta = "SELECT * FROM armarios WHERE id_tabla = $id_armario";
    $resultado = baseDatos($consulta);
    $armario = mysqli_fetch_assoc($resultado);
    
    if(!$armario) {
        echo "<script>alert('Armario no encontrado'); window.location='gestion_armarios.php';</script>";
        exit;
    }
} else {
    header("Location: gestion_armarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Super Wang - Editar Armario</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../estilos.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        
        header {
            background-color: #2c3e50;
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo {
            padding: 0 2rem;
        }
        
        .flogo {
            max-height: 60px;
            transition: transform 0.3s ease;
        }
        
        .flogo:hover {
            transform: scale(1.05);
        }
        
        nav {
            display: flex;
            flex-wrap: wrap;
            padding: 0 2rem;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .titulo {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .titulo h1 {
            font-size: 2.2rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            position: relative;
        }
        
        .titulo h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #3498db;
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s;
            text-align: center;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        footer {
            background-color: #2c3e50;
            color: #fff;
            padding: 2rem 0;
            text-align: center;
            margin-top: 2rem;
        }
        
        .logo-footer {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="gestion_armarios.php" title="Volver">
                <img src="../../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
        <nav>
            <a href="gestion_armarios.php" class="btn btn-primary">Volver a gestión</a>
            <a href="../inventario.php" class="btn btn-primary">Gestionar inventario</a>
            <a href="../../usuarios/admin.php" class="btn btn-primary">Administración</a>
        </nav>
    </header>
    
    <main>
        <div class="titulo">
            <h1>Editar Armario</h1>
        </div>
        
        <div class="form-container">
            <form method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($armario['id_tabla']); ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre del Armario:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($armario['nombre']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="ubicacion">Ubicación:</label>
                    <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($armario['ubicacion']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($armario['descrip']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="boton" class="btn btn-success">Guardar Cambios</button>
                    <a href="gestion_armarios.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
    
    <footer>
        <div class="logo-footer">
            <a href="../../backend.php" title="Volver">
                <img src="../../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
    </footer>
</body>
</html>