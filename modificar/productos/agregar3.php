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

include("funciones.php");

// Sanitizar todas las entradas
$nombre = sanitizar($_POST["nombre"]);
$descrip = sanitizar($_POST["descrip"]);
$stock = intval($_POST["stock"]);
$lugar = intval($_POST["lugar"]);
$categoria = sanitizar($_POST["categoria"]);
$estado = sanitizar($_POST["estado"]);

// Configuración de ImgBB - REEMPLAZA CON TU API KEY REAL
$imgbb_api_key = "8c29637bd984b4d9668e66ef0c98333d";

/**
 * Función para subir imagen a ImgBB y obtener URL
 */
function subirImagenImgBB($imagen_temp, $api_key) {
    $imagen_data = base64_encode(file_get_contents($imagen_temp));
    
    $postData = array(
        'key' => $api_key,
        'image' => $imagen_data
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $result = json_decode($response, true);
        if ($result['success']) {
            return array(
                'url' => $result['data']['url'],
                'thumb_url' => $result['data']['thumb']['url'],
                'display_url' => $result['data']['display_url'],
                'delete_url' => $result['data']['delete_url']
            );
        }
    }
    return false;
}

// Verificar que se haya subido una imagen correctamente
if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == UPLOAD_ERR_OK) {
    $imagen_temp = $_FILES["imagen"]["tmp_name"];
    
    // Subir imagen a ImgBB
    $imgbb_data = subirImagenImgBB($imagen_temp, $imgbb_api_key);
    
    if ($imgbb_data !== false) {
        // Conexión a la base de datos
        $conexion = mysqli_connect("localhost", "root", "");
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }
        
        mysqli_select_db($conexion, "inventario");
        
        // Escapar datos para seguridad
        $nombre = escapar($conexion, $nombre);
        $descrip = escapar($conexion, $descrip);
        $categoria = escapar($conexion, $categoria);
        $estado = escapar($conexion, $estado);
        $imagen_url = escapar($conexion, $imgbb_data['url']);
        
        // Insertar en la base de datos
        $consulta = "INSERT INTO inventario (nombre, descrip, stock, id_tabla, categoria, estado, imagen) 
                    VALUES ('$nombre', '$descrip', $stock, $lugar, '$categoria', '$estado', '$imagen_url')";
        
        $resultado = mysqli_query($conexion, $consulta);
        
        if ($resultado) {
            echo "<script>
                    alert('Producto agregado correctamente.\\nImagen subida exitosamente.');
                    window.location='agregar2.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al guardar en la base de datos: " . addslashes(mysqli_error($conexion)) . "');
                    window.location='agregar2.php';
                  </script>";
        }
        
        mysqli_close($conexion);
        
    } else {
        echo "<script>
                alert('Error al subir la imagen a ImgBB. Verifica tu API key y conexión a internet.');
                window.location='agregar2.php';
              </script>";
    }
} else {
    $error_msg = "Error desconocido";
    switch ($_FILES["imagen"]["error"]) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $error_msg = "El archivo es demasiado grande";
            break;
        case UPLOAD_ERR_PARTIAL:
            $error_msg = "La subida del archivo fue interrumpida";
            break;
        case UPLOAD_ERR_NO_FILE:
            $error_msg = "No se seleccionó ningún archivo";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $error_msg = "Falta la carpeta temporal";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $error_msg = "No se pudo escribir el archivo en el disco";
            break;
        case UPLOAD_ERR_EXTENSION:
            $error_msg = "Una extensión de PHP detuvo la subida del archivo";
            break;
    }
    
    echo "<script>
            alert('Error al procesar la imagen: $error_msg');
            window.location='agregar2.php';
          </script>";
}
?>