<?php
session_start();
include("funciones.php");

if (!isset($_SESSION['id']) || !isset($_POST['id_comentario'])) {
    header("Location: ver_comentarios.php");
    exit;
}

$comentario_id = intval($_POST['id_comentario']);
$usuario_id = $_SESSION['id'];

// Verificar permisos
$consulta_permiso = "SELECT id FROM comentario WHERE id_com = $comentario_id";
$resultado = baseDatos($consulta_permiso);
$datos = mysqli_fetch_assoc($resultado);

if ($_SESSION['cate'] == 'admin' || $datos['id'] == $usuario_id) {
    $consulta = "DELETE FROM comentario WHERE id_com = $comentario_id";
    baseDatos($consulta);
}

header("Location: ver_comentarios.php");
exit;
?>