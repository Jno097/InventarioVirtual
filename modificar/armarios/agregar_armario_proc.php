<?php
session_start();
include("funciones.php");

// Verificar si hay sesión activa
if(!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Debe iniciar sesión primero'); window.location='../../usuarios/login.php';</script>";
    exit;
}

// Verificar si el usuario es administrador o profesor verificado
$consulta_admin = "SELECT cate, verificado FROM login WHERE id = " . $_SESSION['id_usuario'];
$resultado_admin = baseDatos($consulta_admin);
$row_admin = mysqli_fetch_assoc($resultado_admin);

// Verificar permisos: admin, profe (todos deben estar verificados)
$acceso_permitido = false;

if($row_admin['verificado'] == '1') { // Verificamos como string ya que viene así de la BD
    if($row_admin['cate'] == 'admin' || $row_admin['cate'] == 'profe') {
        $acceso_permitido = true;
    }
}

if(!$acceso_permitido) {
    echo "<script>alert('Acceso no autorizado. Solo administradores y profesores verificados pueden acceder.'); window.location='../../backend.php';</script>";
    exit;
}

// Verificar si se ha enviado el formulario
if (isset($_POST["boton"])) {
    // Recoger y sanitizar datos del formulario
    $nombre = sanitizar($_POST["nombre"]);
    $ubicacion = sanitizar($_POST["ubicacion"]);
    $descripcion = sanitizar($_POST["descripcion"]);
    
    // Manejar el campo capacidad (opcional)
    $capacidad = isset($_POST["capacidad"]) && !empty($_POST["capacidad"]) ? 
                intval($_POST["capacidad"]) : 
                "NULL";
    
    // Conexión directa para poder manejar errores específicos
    $conexion = mysqli_connect("localhost", "root", "");
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_select_db($conexion, "inventario");
    
    // Escapar strings para prevenir inyección SQL
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $ubicacion = mysqli_real_escape_string($conexion, $ubicacion);
    $descripcion = mysqli_real_escape_string($conexion, $descripcion);
    
    try {
        // Insertar el nuevo armario
        $consulta = "INSERT INTO armarios (nombre, ubicacion, descrip) 
                    VALUES ('$nombre', '$ubicacion', '$descripcion')";
        
        $resultado = mysqli_query($conexion, $consulta);
        
        if ($resultado) {
            echo "<script>alert('El armario ha sido agregado correctamente');
                  window.location='agregar_armario.php';</script>";
        } else {
            throw new Exception("Error al agregar el armario: " . mysqli_error($conexion));
        }
    } catch (Exception $e) {
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');
              window.location='agregar_armario.php';</script>";
    }
    
    mysqli_close($conexion);
} else {
    // Si alguien accede directamente a esta página sin enviar el formulario
    header("Location: agregar_armario.php");
    exit();
}