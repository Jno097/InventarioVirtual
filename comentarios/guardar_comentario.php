<?php
session_start();
include("funciones.php");

// Verificar sesión y datos POST
if (!isset($_SESSION['id_usuario']) || !isset($_POST['armario_id'], $_POST['titulo'], $_POST['descripcion'])) {
    header("Location: ../backend.php");
    exit;
}

// Validar y sanitizar datos
$armario_id = intval($_POST['armario_id']);
$titulo = sanitizar($_POST['titulo']);
$descripcion = sanitizar($_POST['descripcion']);
$usuario_id = $_SESSION['id_usuario'];
$fecha_actual = date('Y-m-d H:i:s'); // Fecha actual para el comentario

// Verificar que el título y descripción no estén vacíos
if (empty($titulo) || empty($descripcion)) {
    header("Location: ../backend.php?armario_id=$armario_id&comentario=error&razon=campos_vacios");
    exit;
}

// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "");
if (!$conexion) {
    header("Location: ../backend.php?armario_id=$armario_id&comentario=error&razon=conexion_bd");
    exit;
}

mysqli_select_db($conexion, "inventario");

// Escapar datos para prevenir SQL injection
$titulo = mysqli_real_escape_string($conexion, $titulo);
$descripcion = mysqli_real_escape_string($conexion, $descripcion);

// Insertar comentario con fecha
$consulta = "INSERT INTO comentario (titulo, descripcion, id_tabla, id, fecha, leido) 
             VALUES ('$titulo', '$descripcion', $armario_id, $usuario_id, '$fecha_actual', 'no')";

if (mysqli_query($conexion, $consulta)) {
    mysqli_close($conexion);
    header("Location: ../backend.php?armario_id=$armario_id&comentario=exito");
} else {
    $error = mysqli_error($conexion);
    mysqli_close($conexion);
    header("Location: ../backend.php?armario_id=$armario_id&comentario=error&razon=" . urlencode($error));
}
exit;
?>