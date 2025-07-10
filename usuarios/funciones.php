<?php
// Evitar múltiples inclusiones
if (defined('FUNCIONES_CARGADAS')) {
    return;
}
define('FUNCIONES_CARGADAS', true);

// Configuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir constantes solo si no existen
if (!defined('ENCRYPTION_KEY')) {
    define('ENCRYPTION_KEY', 'tu_clave_secreta_123!@#ABC');
}

if (!defined('ENCRYPTION_METHOD')) {
    define('ENCRYPTION_METHOD', 'AES-256-CBC');
}

// Función de conexión a base de datos
function baseDatos($consulta, $params = []) {
    $conexion = mysqli_connect("localhost", "root", "", "inventario");
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conexion, "utf8mb4");
    
    if (empty($params)) {
        $resultado = mysqli_query($conexion, $consulta);
        if (!$resultado) {
            $error = mysqli_error($conexion);
            mysqli_close($conexion);
            die("Error en la consulta: " . $error);
        }
        mysqli_close($conexion);
        return $resultado;
    }
    
    $stmt = mysqli_prepare($conexion, $consulta);
    if (!$stmt) {
        $error = mysqli_error($conexion);
        mysqli_close($conexion);
        die("Error en la preparación: " . $error);
    }
    
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        die("Error en la ejecución: " . $error);
    }
    
    $resultado = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    
    return $resultado;
}

// Función de encriptación
function encriptar($data) {
    if (empty($data)) return $data;
    
    $iv_length = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = openssl_random_pseudo_bytes($iv_length);
    
    $encrypted = openssl_encrypt(
        $data,
        ENCRYPTION_METHOD,
        ENCRYPTION_KEY,
        0,
        $iv
    );
    
    if ($encrypted === false) {
        throw new Exception("Error al encriptar los datos");
    }
    
    return base64_encode($encrypted . '::' . $iv);
}

// Función de desencriptación
function desencriptar($data) {
    if (empty($data)) return $data;
    
    $parts = explode('::', base64_decode($data), 2);
    if (count($parts) != 2) {
        throw new Exception("Formato de datos encriptados inválido");
    }
    
    list($encrypted_data, $iv) = $parts;
    
    $decrypted = openssl_decrypt(
        $encrypted_data,
        ENCRYPTION_METHOD,
        ENCRYPTION_KEY,
        0,
        $iv
    );
    
    if ($decrypted === false) {
        throw new Exception("Error al desencriptar los datos");
    }
    
    return $decrypted;
}

// Funciones de verificación
function verificarSesion() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function verificarAdmin() {
    verificarSesion();
    if ($_SESSION['user_role'] != 'admin') {
        header("Location: backend.php");
        exit();
    }
}
?>