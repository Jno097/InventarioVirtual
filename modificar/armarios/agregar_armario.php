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

// Procesar formulario de agregar
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['boton'])) {
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
    
    // Insertar el nuevo armario
    $consulta = "INSERT INTO armarios (nombre, ubicacion, descrip) 
                VALUES ('$nombre', '$ubicacion', '$descripcion')";
    
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    
    if($resultado) {
        echo "<script>alert('Armario agregado correctamente'); window.location='gestion_armarios.php?success=added';</script>";
    } else {
        echo "<script>alert('Error al agregar el armario');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Super Wang - Agregar Armario</title>
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
            <h1>Agregar Armario</h1>
        </div>
        
        <div class="formulario">
            <form method="post">
                <p>Nombre del Armario:
                    <input type="text" name="nombre" required autofocus>
                </p>
                
                <p>Ubicación:
                    <input type="text" name="ubicacion" required>
                </p>
                
                <p>Descripción:
                    <textarea name="descripcion"></textarea>
                </p>
                
                <p>
                    <button type="submit" name="boton" class="btn btn-success">Guardar Armario</button>
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