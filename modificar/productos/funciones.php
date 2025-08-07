<?php
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

function sanitizar($input) {
    if (is_array($input)) {
        return array_map('sanitizar', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function subirImagenImgBB($imagen_temp, $api_key) {
    // Verificar que el archivo temporal exista
    if (!file_exists($imagen_temp)) {
        return false;
    }

    // Codificar imagen en base64
    $imagen_data = base64_encode(file_get_contents($imagen_temp));
    
    // Configurar datos para la API
    $postData = [
        'key' => $api_key,
        'image' => $imagen_data
    ];
    
    // Configurar cURL para enviar a ImgBB
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 segundos de timeout
    
    $response = curl_exec($ch);
    
    // Verificar errores de cURL
    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Procesar respuesta
    if ($httpCode == 200) {
        $result = json_decode($response, true);
        if ($result && isset($result['success']) && $result['success']) {
            return [
                'url' => $result['data']['url'],
                'thumb_url' => $result['data']['thumb']['url'],
                'display_url' => $result['data']['display_url']
            ];
        }
    }
    
    return false;
}
?>