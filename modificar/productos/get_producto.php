<?php
session_start();
include("funciones.php");

// Verificar si hay sesión activa
if(!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Debe iniciar sesión primero'); window.location='../usuarios/login.php';</script>";
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
    echo "<script>alert('Acceso no autorizado. Solo administradores y profesores verificados pueden acceder.'); window.location='../backend.php';</script>";
    exit;
}

// Verificar que se envió el ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID no válido']);
    exit;
}

$id = (int)$_GET['id'];

// Obtener los datos del producto
$consulta = "SELECT * FROM inventario WHERE id = $id";
$resultado = baseDatos($consulta);

if($resultado && mysqli_num_rows($resultado) > 0) {
    $producto = mysqli_fetch_assoc($resultado);
    
    // Sanitizar los datos antes de enviarlos
    $producto_sanitizado = array(
        'id' => $producto['id'],
        'nombre' => sanitizar($producto['nombre']),
        'descrip' => sanitizar($producto['descrip']),
        'stock' => $producto['stock'],
        'id_tabla' => $producto['id_tabla'],
        'categoria' => sanitizar($producto['categoria']),
        'estado' => $producto['estado'],
        'imagen' => $producto['imagen']
    );
    
    // Si la imagen es una URL de imgBB, devolverla directamente
    if (filter_var($producto['imagen'], FILTER_VALIDATE_URL)) {
        $producto_sanitizado['imagen_url'] = $producto['imagen'];
    } else {
        // Si es una ruta local, construir la URL completa
        $producto_sanitizado['imagen_url'] = !empty($producto['imagen']) ? 
            (strpos($producto['imagen'], 'http') === 0 ? $producto['imagen'] : 'https://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($producto['imagen'], '/')) : 
            null;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'producto' => $producto_sanitizado]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
}
?>