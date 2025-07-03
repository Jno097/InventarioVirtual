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
    <link rel="stylesheet" type="text/css" href="../../estilos.css">
    <style>
        .formulario {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .formulario input[type="text"],
        .formulario textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .formulario textarea {
            height: 100px;
        }
        .formulario button {
            padding: 8px 16px;
            margin-right: 10px;
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
            <a href="gestion_armarios.php" title="Volver a gestión">VOLVER</a>
            <a href="../inventario.php" title="Gestionar inventario">INVENTARIO</a>
            <a href="../../usuarios/admin.php" title="Administración">ADMIN</a>
        </nav>
    </header>
    
    <main>
        <div class="titulo">
            <h1>Editar Armario</h1>
        </div>
        
        <div class="formulario">
            <form method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($armario['id_tabla']); ?>">
                
                <p>Nombre del Armario:
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($armario['nombre']); ?>" required>
                </p>
                
                <p>Ubicación:
                    <input type="text" name="ubicacion" value="<?php echo htmlspecialchars($armario['ubicacion']); ?>" required>
                </p>
                
                <p>Descripción:
                    <textarea name="descripcion"><?php echo htmlspecialchars($armario['descrip']); ?></textarea>
                </p>
                
                <p>
                    <button type="submit" name="boton" class="btn btn-success">Guardar Cambios</button>
                    <a href="gestion_armarios.php" class="btn btn-secondary">Cancelar</a>
                </p>
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