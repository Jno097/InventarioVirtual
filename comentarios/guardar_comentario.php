<?php
session_start();
include("funciones.php");

if (!isset($_SESSION['id']) || !isset($_POST['armario_id'], $_POST['titulo'], $_POST['descripcion'])) {
    header("Location: ../backend.php");
    exit;
}

$armario_id = intval($_POST['armario_id']);
$titulo = escapar($_POST['titulo']);
$descripcion = escapar($_POST['descripcion']);
$usuario_id = $_SESSION['id'];

$consulta = "INSERT INTO comentario (titulo, descripcion, id_tabla, id) 
             VALUES ('$titulo', '$descripcion', $armario_id, $usuario_id)";

if (baseDatos($consulta)) {
    header("Location: ../backend.php?armario_id=$armario_id&comentario=exito");
} else {
    header("Location: ../backend.php?armario_id=$armario_id&comentario=error");
}
exit;
?>