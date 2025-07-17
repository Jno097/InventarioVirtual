<?php
include("funciones.php");

$conexion = mysqli_connect("localhost", "root", "");
mysqli_select_db($conexion, "inventario");

// Obtener todos los usuarios
$consulta = "SELECT id, contrasena FROM login";
$resultado = mysqli_query($conexion, $consulta);

while($usuario = mysqli_fetch_assoc($resultado)) {
    $id = $usuario['id'];
    // Si la contraseña no parece estar hasheada (menos de 50 caracteres)
    if(strlen($usuario['contrasena']) < 50) {
        $nuevo_hash = hashPassword($usuario['contrasena']);
        
        $update = "UPDATE login SET contrasena = '".mysqli_real_escape_string($conexion, $nuevo_hash)."' 
                   WHERE id = $id";
        mysqli_query($conexion, $update);
        echo "Contraseña actualizada para usuario ID: $id<br>";
    }
}

mysqli_close($conexion);
echo "Proceso de migración completado";
?>