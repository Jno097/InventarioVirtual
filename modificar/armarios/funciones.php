<?php

function baseDatos($consulta)
{
    // Conexión
    $conexion = mysqli_connect("localhost", "root", "");
    // Selección de BD
    mysqli_select_db($conexion, "inventario");
    
    // Ejecutar consulta
    $resultado = mysqli_query($conexion, $consulta);
    
    // Cerrar conexión
    mysqli_close($conexion);
    
    // Devolver resultado
    return $resultado;
}

// Función para sanitizar entradas (ayuda a prevenir ataques XSS)
function sanitizar($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitizar($value);
        }
    } else {
        $input = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    return $input;
}

// Función para evitar inyección SQL básica
function escapar($conexion, $input) {
    $conexion = mysqli_connect("localhost", "root", "");
    mysqli_select_db($conexion, "inventario");
    
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = escapar($conexion, $value);
        }
    } else {
        $input = mysqli_real_escape_string($conexion, $input);
    }
    
    mysqli_close($conexion);
    return $input;
}

// Función para generar hash de contraseña
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Función para verificar contraseña
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>