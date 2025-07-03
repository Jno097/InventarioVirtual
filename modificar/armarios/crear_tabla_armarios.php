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
// Este script crea la tabla armarios si no existe
// Solo necesitas ejecutarlo una vez

// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "");
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Seleccionar la base de datos
if (!mysqli_select_db($conexion, "inventario")) {
    die("Error al seleccionar la base de datos: " . mysqli_error($conexion));
}

// Verificar si la tabla armarios ya existe
$result = mysqli_query($conexion, "SHOW TABLES LIKE 'armarios'");
$tableExists = mysqli_num_rows($result) > 0;

if (!$tableExists) {
    // Crear la tabla armarios
    $sql = "CREATE TABLE armarios (
        id_tabla int(16) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        ubicacion varchar(255) NOT NULL,
        descrip text DEFAULT NULL,
        PRIMARY KEY (id_tabla)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if (mysqli_query($conexion, $sql)) {
        echo "<p>La tabla 'armarios' se ha creado correctamente.</p>";
    } else {
        echo "<p>Error al crear la tabla: " . mysqli_error($conexion) . "</p>";
    }
} else {
    echo "<p>La tabla 'armarios' ya existe.</p>";
}

// Añadir algunos datos de ejemplo si la tabla está vacía
$count_result = mysqli_query($conexion, "SELECT COUNT(*) as count FROM armarios");
$count_row = mysqli_fetch_assoc($count_result);

if ($count_row['count'] == 0) {
    $sql = "INSERT INTO armarios (nombre, ubicacion, descrip) VALUES
            ('Armario 1', 'Oficina Principal', 'Armario de documentos generales'),
            ('Armario 2', 'Almacén', 'Armario de suministros'),
            ('Armario 3', 'Sala de Servidores', 'Rack de equipos de red')";
    
    if (mysqli_query($conexion, $sql)) {
        echo "<p>Se han insertado datos de ejemplo.</p>";
    } else {
        echo "<p>Error al insertar datos: " . mysqli_error($conexion) . "</p>";
    }
} else {
    echo "<p>La tabla ya contiene datos, no se han insertado ejemplos.</p>";
}

mysqli_close($conexion);

echo "<p><a href='agregar_armario.php'>Ir a la gestión de armarios</a></p>";