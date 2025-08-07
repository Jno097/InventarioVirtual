<?php
session_start();
require_once("funciones.php");

// Verificar sesión
if(!isset($_SESSION['id_usuario'])) {
    header("Location: ../../usuarios/login.php");
    exit;
}

// Verificar permisos
$consulta_admin = "SELECT cate, verificado FROM login WHERE id = " . $_SESSION['id_usuario'];
$resultado_admin = baseDatos($consulta_admin);
$row_admin = mysqli_fetch_assoc($resultado_admin);

$acceso_permitido = false;
if($row_admin['verificado'] == '1' && in_array($row_admin['cate'], ['admin', 'profe'])) {
    $acceso_permitido = true;
}

if(!$acceso_permitido) {
    header("Location: ../../backend.php");
    exit;
}

// Configuración (debería estar en un archivo de configuración separado)
define('IMGBB_API_KEY', '8c29637bd984b4d9668e66ef0c98333d'); // Reemplazar con tu API key
define('MAX_FILE_SIZE', 2097152); // 2MB

// Verificar que todos los campos requeridos estén presentes
$required_fields = ['nombre', 'descrip', 'stock', 'lugar', 'categoria', 'estado', 'boton'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) {
        mostrarError("El campo $field es requerido");
    }
}

// Sanitizar y validar datos
$nombre = sanitizar($_POST["nombre"]);
$descrip = sanitizar($_POST["descrip"]);
$stock = intval($_POST["stock"]);
$lugar = intval($_POST["lugar"]);
$categoria = sanitizar($_POST["categoria"]);
$estado = sanitizar($_POST["estado"]);

if (empty($nombre) || empty($descrip) || empty($categoria)) {
    mostrarError("Todos los campos de texto son requeridos");
}

if ($stock <= 0) {
    mostrarError("El stock debe ser mayor que cero");
}

// Procesar imagen
if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["error"] != UPLOAD_ERR_OK) {
    $error_msg = "Error al subir la imagen: ";
    switch ($_FILES["imagen"]["error"]) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE: $error_msg .= "El archivo es demasiado grande (máx. 2MB)"; break;
        case UPLOAD_ERR_PARTIAL: $error_msg .= "La subida fue interrumpida"; break;
        case UPLOAD_ERR_NO_FILE: $error_msg .= "No se seleccionó ningún archivo"; break;
        case UPLOAD_ERR_NO_TMP_DIR: $error_msg .= "Falta carpeta temporal"; break;
        case UPLOAD_ERR_CANT_WRITE: $error_msg .= "Error al escribir en disco"; break;
        case UPLOAD_ERR_EXTENSION: $error_msg .= "Extensión de PHP detuvo la subida"; break;
        default: $error_msg .= "Error desconocido"; break;
    }
    mostrarError($error_msg);
}

// Validar tipo y tamaño de imagen
$allowed_types = ['image/jpeg', 'image/jpg'];
$file_type = mime_content_type($_FILES["imagen"]["tmp_name"]);

if (!in_array($file_type, $allowed_types)) {
    mostrarError("Solo se permiten imágenes JPG/JPEG");
}

if ($_FILES["imagen"]["size"] > MAX_FILE_SIZE) {
    mostrarError("La imagen es demasiado grande (máximo 2MB permitido)");
}

// Subir imagen a ImgBB
$imgbb_data = subirImagenImgBB($_FILES["imagen"]["tmp_name"], IMGBB_API_KEY);
if ($imgbb_data === false) {
    mostrarError("Error al subir la imagen al servidor remoto");
}

// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "");
if (!$conexion) {
    mostrarError("Error de conexión a la base de datos");
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

$resultado = mysqli_query($conexion, $consulta);

if ($resultado) {
    // Éxito - redirigir con mensaje de éxito
    $_SESSION['mensaje'] = "Producto agregado correctamente";
    header("Location: agregar2.php");
} else {
    mostrarError("Error al guardar en la base de datos: " . mysqli_error($conexion));
}

mysqli_close($conexion);

// Función para mostrar errores y redirigir
function mostrarError($mensaje) {
    $_SESSION['error'] = $mensaje;
    header("Location: agregar2.php");
    exit;
}
?>