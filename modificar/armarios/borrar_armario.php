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
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<title>Super Wang - Borrar Armario</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="../../estilos.css">
</head>

<body>

<!-- En la sección de navegación, actualizar los enlaces -->
<header>
    <div class="logo">
        <a href="gestion_armarios.php" title="Volver">
            <img src="../../img/fotos_pag/logo.png" class="flogo">
        </a>
    </div>
    <nav>
        <a href="gestion_armarios.php" title="Volver a gestión">VOLVER</a>
        <a href="../inventario.php" title="Gestionar inventario">INVENTARIO</a>
        <a href="../../usuarios/admin.php" title="Administración">ADMIN</a>
    </nav>
</header>
	<main>
		<div class="titulo">
			<h1>Borrar Armario</h1>
		</div>
		
		<?php
		include("funciones.php");
		
		// Verificar si se ha enviado un ID para eliminar
		if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
			$id = intval($_GET["id"]);
			
			// Verificar si tiene productos asociados
			$consulta_productos = "SELECT COUNT(*) as total FROM inventario WHERE id_tabla = $id";
			$resultado_productos = baseDatos($consulta_productos);
			$fila_productos = mysqli_fetch_assoc($resultado_productos);
			
			if ($fila_productos["total"] > 0) {
				echo "<div class='mensaje-error'>No se puede eliminar este armario porque tiene productos asociados.</div>";
			} else {
				// Intentar eliminar el armario
				$consulta_eliminar = "DELETE FROM armarios WHERE id_tabla = $id";
				$resultado_eliminar = baseDatos($consulta_eliminar);
				
				if ($resultado_eliminar) {
					echo "<div class='mensaje-exito'>Armario eliminado correctamente.</div>";
				} else {
					echo "<div class='mensaje-error'>Error al eliminar el armario.</div>";
				}
			}
		}
		?>
		
		<div class="lista-armarios">
			<h2>Armarios Existentes</h2>
			<table border="1" cellpadding="5" cellspacing="0">
				<tr>
					<th>ID</th>
					<th>Nombre</th>
					<th>Ubicación</th>
					<th>Descripción</th>
					<th>Acciones</th>
				</tr>
				<?php
				// Consulta para obtener todos los armarios
				$consulta = "SELECT id_tabla, nombre, ubicacion, descrip FROM armarios ORDER BY nombre";
				$resultado = baseDatos($consulta);
				
				// Verificar si hay armarios registrados
				if ($resultado && mysqli_num_rows($resultado) > 0) {
					// Iterar sobre todos los armarios
					while ($fila = mysqli_fetch_array($resultado)) {
						$id = htmlspecialchars($fila["id_tabla"]);
						echo "<tr>";
						echo "<td>" . $id . "</td>";
						echo "<td>" . htmlspecialchars($fila["nombre"]) . "</td>";
						echo "<td>" . htmlspecialchars($fila["ubicacion"]) . "</td>";
						echo "<td>" . htmlspecialchars($fila["descrip"]) . "</td>";
						
						// Verificar si el armario tiene productos
						$consulta_productos = "SELECT COUNT(*) as total FROM inventario WHERE id_tabla = $id";
						$resultado_productos = baseDatos($consulta_productos);
						$fila_productos = mysqli_fetch_assoc($resultado_productos);
						
						if ($fila_productos["total"] > 0) {
							echo "<td><button disabled title='No se puede eliminar: tiene productos asociados'>Eliminar</button></td>";
						} else {
							echo "<td><a href='borrar_armario.php?id=" . $id . "' onclick=\"return confirm('¿Está seguro de eliminar este armario?');\"><button>Eliminar</button></a></td>";
						}
						
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan='5'>No hay armarios registrados o se produjo un error en la consulta</td></tr>";
				}
				?>
			</table>
			
			<div class="opciones">
				<p><a href="agregar_armario.php"><button>Agregar Nuevo Armario</button></a></p>
				<p><a href="../../backend.php"><button>Volver al Panel Principal</button></a></p>
			</div>
		</div>
	</main>
	<footer>
		<div class="logo-footer">
			<a href="backend.php" title="Volver">
				<img src="../../img/fotos_pag/logo.png" class="flogo">
			</a>
		</div>
	</footer>

</body>

</html>