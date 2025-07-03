<?php
// Iniciar sesión al principio del script, antes de cualquier salida
session_start();

// Incluir funciones sin espacios en blanco antes de <?php
include("../funciones.php");

// Verificar sesión y permisos
if(!isset($_SESSION['id_usuario'])) {
    // Usar JavaScript para redirección si no hay sesión
    echo "<script>alert('Debe iniciar sesión primero'); window.location='../usuarios/login.php';</script>";
    exit;
}

// Obtener información del usuario
$consulta_admin = "SELECT cate, verificado FROM login WHERE id = " . $_SESSION['id_usuario'];
$resultado_admin = baseDatos($consulta_admin);

if(!$resultado_admin) {
    die("Error en la consulta de permisos");
}

$row_admin = mysqli_fetch_assoc($resultado_admin);
$acceso_permitido = false;

if($row_admin && $row_admin['verificado'] == '1' && ($row_admin['cate'] == 'admin' || $row_admin['cate'] == 'profe')) {
    $acceso_permitido = true;
}

if(!$acceso_permitido) {
    // Usar JavaScript para redirección si no tiene permisos
    echo "<script>alert('Acceso no autorizado'); window.location='../../backend.php';</script>";
    exit;
}

// Verificar si se envió un ID válido
if(isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id_armario = (int)$_POST['id'];
    
    // Conexión directa para manejar transacciones
    $conexion = mysqli_connect("localhost", "root", "", "inventario");
    if(!$conexion) {
        die(json_encode(['success' => false, 'error' => 'Error de conexión']));
    }
    
    // Verificar si el armario tiene productos
    $consulta_productos = "SELECT COUNT(*) as total FROM inventario WHERE id_tabla = $id_armario";
    $resultado_productos = mysqli_query($conexion, $consulta_productos);
    
    if(!$resultado_productos) {
        mysqli_close($conexion);
        die(json_encode(['success' => false, 'error' => 'Error al verificar productos']));
    }
    
    $fila_productos = mysqli_fetch_assoc($resultado_productos);
    
    if($fila_productos['total'] > 0) {
        mysqli_close($conexion);
        die(json_encode(['success' => false, 'error' => 'has_products']));
    }
    
    // Eliminar el armario
    $consulta_eliminar = "DELETE FROM armarios WHERE id_tabla = $id_armario";
    $resultado_eliminar = mysqli_query($conexion, $consulta_eliminar);
    
    mysqli_close($conexion);
    
    if($resultado_eliminar) {
        die(json_encode(['success' => true]));
    } else {
        die(json_encode(['success' => false, 'error' => 'delete_failed']));
    }
} else {
    die(json_encode(['success' => false, 'error' => 'invalid_id']));
}
?>