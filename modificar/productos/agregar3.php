<?php
session_start();
require_once("funciones.php");

// Verificar sesión y permisos (código existente)
if(!isset($_SESSION['id_usuario'])) {
    $_SESSION['error'] = "Debe iniciar sesión primero";
    header("Location: ../../usuarios/login.php");
    exit;
}

$consulta_admin = "SELECT cate, verificado FROM login WHERE id = " . $_SESSION['id_usuario'];
$resultado_admin = baseDatos($consulta_admin);
$row_admin = mysqli_fetch_assoc($resultado_admin);

if(!($row_admin['verificado'] == '1' && in_array($row_admin['cate'], ['admin', 'profe']))) {
    $_SESSION['error'] = "Acceso no autorizado";
    header("Location: ../../backend.php");
    exit;
}

// Configuración de ImgBB (reemplaza con tu API key real)
define('IMGBB_API_KEY', '8c29637bd984b4d9668e66ef0c98333d');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Verificar campos requeridos
$required_fields = ['nombre', 'descrip', 'stock', 'lugar', 'categoria', 'estado'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "El campo $field es requerido";
        header("Location: agregar2.php");
        exit;
    }
}

// Sanitizar datos
$nombre = sanitizar($_POST['nombre']);
$descrip = sanitizar($_POST['descrip']);
$stock = intval($_POST['stock']);
$lugar = intval($_POST['lugar']);
$categoria = sanitizar($_POST['categoria']);
$estado = sanitizar($_POST['estado']);

// Validar stock
if ($stock <= 0) {
    $_SESSION['error'] = "El stock debe ser mayor que cero";
    header("Location: agregar2.php");
    exit;
}

// Procesar imagen
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] != UPLOAD_ERR_OK) {
    $error_msg = "Error al subir la imagen: ";
    switch ($_FILES['imagen']['error'] ?? UPLOAD_ERR_NO_FILE) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE: $error_msg .= "El archivo es demasiado grande (máx. 5MB)"; break;
        case UPLOAD_ERR_PARTIAL: $error_msg .= "La subida fue interrumpida"; break;
        case UPLOAD_ERR_NO_FILE: $error_msg .= "No se seleccionó ningún archivo"; break;
        case UPLOAD_ERR_NO_TMP_DIR: $error_msg .= "Falta carpeta temporal"; break;
        case UPLOAD_ERR_CANT_WRITE: $error_msg .= "Error al escribir en disco"; break;
        case UPLOAD_ERR_EXTENSION: $error_msg .= "Extensión de PHP detuvo la subida"; break;
        default: $error_msg .= "Error desconocido"; break;
    }
    $_SESSION['error'] = $error_msg;
    header("Location: agregar2.php");
    exit;
}

// Validar tamaño de imagen
if ($_FILES['imagen']['size'] > MAX_FILE_SIZE) {
    $_SESSION['error'] = "La imagen es demasiado grande (máximo 5MB permitido)";
    header("Location: agregar2.php");
    exit;
}

// Validar tipo de imagen
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$file_type = mime_content_type($_FILES['imagen']['tmp_name']);
if (!in_array($file_type, $allowed_types)) {
    $_SESSION['error'] = "Solo se permiten imágenes (JPG, PNG, GIF)";
    header("Location: agregar2.php");
    exit;
}

// Subir imagen a ImgBB
$imgbb_data = subirImagenImgBB($_FILES['imagen']['tmp_name'], IMGBB_API_KEY);
if ($imgbb_data === false) {
    $_SESSION['error'] = "Error al subir la imagen a ImgBB. Intente nuevamente.";
    header("Location: agregar2.php");
    exit;
}

// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "");
if (!$conexion) {
    $_SESSION['error'] = "Error de conexión a la base de datos";
    header("Location: agregar2.php");
    exit;
}

mysqli_select_db($conexion, "inventario");

// Escapar datos para SQL
$nombre = mysqli_real_escape_string($conexion, $nombre);
$descrip = mysqli_real_escape_string($conexion, $descrip);
$categoria = mysqli_real_escape_string($conexion, $categoria);
$estado = mysqli_real_escape_string($conexion, $estado);
$imagen_url = mysqli_real_escape_string($conexion, $imgbb_data['url']);

// Insertar en la base de datos
$consulta = "INSERT INTO inventario (nombre, descrip, stock, id_tabla, categoria, estado, imagen) 
             VALUES ('$nombre', '$descrip', $stock, $lugar, '$categoria', '$estado', '$imagen_url')";

if (mysqli_query($conexion, $consulta)) {
    $_SESSION['mensaje'] = "Producto agregado correctamente. Imagen subida a ImgBB.";
} else {
    $_SESSION['error'] = "Error al guardar en la base de datos: " . mysqli_error($conexion);
}

mysqli_close($conexion);
header("Location: agregar2.php");
exit;
?>