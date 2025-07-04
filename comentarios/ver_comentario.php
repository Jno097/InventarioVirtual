<?php
session_start();
include("funciones.php");

// Verificar si el usuario es admin o profesor
$es_admin = isset($_SESSION['cate']) && ($_SESSION['cate'] == 'admin' || $_SESSION['cate'] == 'profe');

// Obtener todos los comentarios con información de armarios y usuarios
$consulta = "SELECT c.*, a.nombre as nombre_armario, u.nombre as nombre_usuario 
             FROM comentario c
             JOIN armarios a ON c.id_tabla = a.id_tabla
             JOIN login u ON c.id = u.id
             ORDER BY c.fecha DESC";
$resultado = baseDatos($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Comentarios - Ynventaris</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="backend.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="backend.php" title="Inicio">
                <img src="../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
        <nav>
            <a href="backend.php">VOLVER</a>
            <?php if(isset($_SESSION['id_usuario'])): ?>
                <a href="usuarios/logout.php">CERRAR SESIÓN</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h1>Todos los comentarios</h1>
        
        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <div class="lista-completa">
                <?php while ($comentario = mysqli_fetch_assoc($resultado)): ?>
                    <div class="comentario-detallado">
                        <div class="comentario-cabecera">
                            <h2><?php echo htmlspecialchars($comentario['titulo']); ?></h2>
                            <span class="fecha"><?php echo $comentario['fecha']; ?></span>
                        </div>
                        <p class="usuario">Por: <?php echo htmlspecialchars($comentario['nombre_usuario']); ?></p>
                        <p class="armario">Armario: <?php echo htmlspecialchars($comentario['nombre_armario']); ?></p>
                        <div class="comentario-contenido">
                            <p><?php echo htmlspecialchars($comentario['descripcion']); ?></p>
                        </div>
                        
                        <?php if ($es_admin || $_SESSION['id'] == $comentario['id']): ?>
                            <form action="eliminar_comentario.php" method="post" class="form-eliminar">
                                <input type="hidden" name="id_comentario" value="<?php echo $comentario['id_com']; ?>">
                                <button type="submit" onclick="return confirm('¿Eliminar este comentario permanentemente?')">
                                    Eliminar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No hay comentarios registrados.</p>
        <?php endif; ?>
    </main>
</body>
</html>