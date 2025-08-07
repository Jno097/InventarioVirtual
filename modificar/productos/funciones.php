<?php
// Función para ejecutar consultas SQL
function baseDatos($consulta) {
    $conexion = mysqli_connect("localhost", "root", "");
    if (!$conexion) {
        return false;
    }
    
    mysqli_select_db($conexion, "inventario");
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    
    return $resultado;
}

// Función para sanitizar entradas
function sanitizar($input) {
    if (is_array($input)) {
        return array_map('sanitizar', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Función para subir imagen a ImgBB
function subirImagenImgBB($imagen_temp, $api_key) {
    if (!file_exists($imagen_temp)) {
        return false;
    }

    $imagen_data = base64_encode(file_get_contents($imagen_temp));
    
    $postData = [
        'key' => $api_key,
        'image' => $imagen_data
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $result = json_decode($response, true);
        if ($result['success']) {
            return [
                'url' => $result['data']['url'],
                'thumb_url' => $result['data']['thumb']['url'],
                'display_url' => $result['data']['display_url'],
                'delete_url' => $result['data']['delete_url']
            ];
        }
    }
    return false;
}

// Función para mostrar mensajes (debería usarse en agregar2.php)
function mostrarMensaje() {
    if (isset($_SESSION['mensaje'])) {
        echo '<div class="mensaje-exito">'.$_SESSION['mensaje'].'</div>';
        unset($_SESSION['mensaje']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="mensaje-error">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
}
?>