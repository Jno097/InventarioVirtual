<?php
session_start();
include("funciones.php");

// Verificar si el usuario ha iniciado sesión
$usuario_logueado = isset($_SESSION['id_usuario']);
$es_profesor_verificado = $usuario_logueado && $_SESSION['cate'] == 'profe' && $_SESSION['verificado'] == 2;

// Control del modo edición
$modo_edicion = false;
if($es_profesor_verificado && isset($_GET['modo_edicion'])) {
    $_SESSION['modo_edicion'] = ($_GET['modo_edicion'] == '1');
    header("Location: backend.php" . (isset($_GET['armario_id']) ? "?armario_id=" . $_GET['armario_id'] : ""));
    exit;
}

// Obtener el estado actual del modo edición de la sesión
$modo_edicion = $es_profesor_verificado && isset($_SESSION['modo_edicion']) && $_SESSION['modo_edicion'];

// Obtener información del armario si se ha especificado uno
$armario_info = null;
$armario_id = isset($_GET['armario_id']) ? intval($_GET['armario_id']) : null;
if ($armario_id) {
    $consulta_armario = "SELECT * FROM armarios WHERE id_tabla = $armario_id";
    $resultado_armario = baseDatos($consulta_armario);
    if (mysqli_num_rows($resultado_armario) > 0) {
        $armario_info = mysqli_fetch_assoc($resultado_armario);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ynventaris</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="backend.css">
    <script>
    // Función para mostrar/ocultar detalles del producto
    function mostrarDetalle(id) {
        // Ocultar todos los detalles primero
        var detalles = document.getElementsByClassName('detalle-producto');
        for (var i = 0; i < detalles.length; i++) {
            detalles[i].style.display = 'none';
        }
        
        // Mostrar el detalle del producto seleccionado
        var detalleActual = document.getElementById('detalle-' + id);
        if (detalleActual) {
            detalleActual.style.display = 'block';
            
            // Desplazarse suavemente hasta el detalle
            detalleActual.scrollIntoView({ behavior: 'smooth' });
        }
    }
    </script>
    <style>
    /* Estilos CSS existentes... */
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <a href="backend.php" title="Inicio">
                 <img src="../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
        <nav>
            <a href="buscar.php" title="Búsqueda">BUSCAR</a>
            <div class="dropdown">
                <button class="dropbtn">ARMARIOS &#9662;</button>
                <div class="dropdown-content">
                    <a href="backend.php">Todos los armarios</a>
                    <?php
                    $consulta_nav_armarios = "SELECT * FROM armarios ORDER BY nombre";
                    $resultado_nav_armarios = baseDatos($consulta_nav_armarios);
                    
                    while ($armario_nav = mysqli_fetch_assoc($resultado_nav_armarios)) {
                        echo '<a href="backend.php?armario_id=' . $armario_nav['id_tabla'] . '">' . htmlspecialchars($armario_nav['nombre']) . '</a>';
                    }
                    ?>
                </div>
            </div>
            <?php if($usuario_logueado): ?>
                <?php if($_SESSION['cate'] == 'profe' && $_SESSION['verificado'] == 2): ?>
                    <a href="agregar_pro.php" title="Agregar productos">AGREGAR</a>
                    <div class="modo-edicion-switch">
                        <span class="edit-label">Modo edición:</span>
                        <label class="switch">
                            <input type="checkbox" id="modoEdicionSwitch" <?php echo $modo_edicion ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                <?php endif; ?>
                <a href="usuarios/logout.php">CERRAR SESIÓN (<?php echo htmlspecialchars($_SESSION['nombre']); ?>)</a>
            <?php else: ?>
                <a href="usuarios/login.php">INICIAR SESIÓN</a>
                <a href="usuarios/registro.php">REGISTRARSE</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <div class="titulo">
            <?php
            if ($armario_info) {
                echo "<h1>Armario: " . htmlspecialchars($armario_info['nombre']) . "</h1>";
                echo "<p>" . htmlspecialchars($armario_info['descrip']) . "</p>";
                echo "<p>Ubicación: " . htmlspecialchars($armario_info['ubicacion']) . "</p>";
                echo '<a href="backend.php" class="volver-btn">Volver a todos los armarios</a>';
            } else {
                echo "<h1>Todos los armarios</h1>";
                
                // Mostrar lista de armarios
                $consulta_armarios = "SELECT * FROM armarios ORDER BY nombre";
                $resultado_armarios = baseDatos($consulta_armarios);
                
                if (mysqli_num_rows($resultado_armarios) > 0) {
                    echo '<div class="armarios-lista">';
                    echo '<h2>Selecciona un armario para ver sus productos:</h2>';
                    echo '<ul>';
                    
                    while ($armario = mysqli_fetch_assoc($resultado_armarios)) {
                        echo '<li><a href="backend.php?armario_id=' . $armario['id_tabla'] . '">' . 
                             htmlspecialchars($armario['nombre']) . ' (' . htmlspecialchars($armario['ubicacion']) . ')</a></li>';
                    }
                    
                    echo '</ul>';
                    echo '</div>';
                }
            }
            
            // Mensaje del modo edición
            if($modo_edicion) {
                echo '<p style="color: #2196F3; font-weight: bold;">Modo edición activado - Puede editar o eliminar productos</p>';
            }
            ?>
        </div>
        
        <!-- Sección de productos principales -->
        <div class="productos <?php echo $modo_edicion ? 'modo-edicion' : ''; ?>">
            <?php
            // Armo la consulta - solo productos con estado "on"
            $consulta = "SELECT * FROM inventario WHERE estado = 'on'";
            
            // Si se especificó un armario, filtrar por él
            if ($armario_id) {
                $consulta .= " AND id_tabla = $armario_id";
            }
            
            $consulta .= " ORDER BY nombre";
            $resultado = baseDatos($consulta);

            // Calculo cantidad de filas
            $n = mysqli_num_rows($resultado);

            if($n == 0) {
                if ($armario_id) {
                    echo "<p>No se encontraron productos en este armario</p>";
                } else {
                    echo "<p>No se encontraron productos</p>";
                }
            }

            for ($i = 0; $i < $n; $i++) {
                // Me posiciono en fila
                mysqli_data_seek($resultado, $i);
                // La guardo en un array
                $fila = mysqli_fetch_array($resultado);
                // Separo cada dato en variables diferentes
                $id = $fila["id"];
                $nombre = htmlspecialchars($fila["nombre"]);
                $descrip = htmlspecialchars($fila["descrip"]);
                $stock = $fila["stock"];
                $categoria = htmlspecialchars($fila["categoria"]);
                $ruta_imagen = $fila["imagen"];

                // Verificar si la imagen es una ruta o datos binarios
                $imagen_tag = '';
                if (!empty($ruta_imagen)) {
                    if (filter_var($ruta_imagen, FILTER_VALIDATE_URL) || strpos($ruta_imagen, 'http') === 0 || strpos($ruta_imagen, 'img/') === 0) {
                        // Es una URL externa (imgbb) o ruta local
                        $imagen_tag = "<img src='" . htmlspecialchars($ruta_imagen) . "' alt='$nombre'>";
                    } else {
                        // Si es un dato binario, crear una imagen en base64
                        $imagen_tag = "<img src='data:image/jpeg;base64," . base64_encode($ruta_imagen) . "' alt='$nombre'>";
                    }
                } else {
                    $imagen_tag = "<img src='img/no-image.png' alt='Sin imagen'>";
                }

                echo "
                <div class='item'>
                    <div class='foto'>
                        <a href='javascript:void(0)' onclick='mostrarDetalle($id)' title='Ver detalles'>
                            $imagen_tag
                        </a>
                    </div>
                    <div class='nombre'>$nombre</div>";
                    
                    // Mostrar botones de edición solo en modo edición
                    if($modo_edicion && $es_profesor_verificado) {
                        echo "
                        <div class='edit-buttons'>
                            <a href='editar_producto.php?id=$id'><button class='edit-btn'>Editar</button></a>
                            <a href='eliminar_producto.php?id=$id' onclick='return confirm(\"¿Está seguro que desea eliminar este producto?\")'><button class='delete-btn'>Eliminar</button></a>
                        </div>";
                    }
                    
                echo "</div>";
                
                // Creamos un div oculto para el detalle de cada producto
                echo "<div id='detalle-$id' class='detalle-producto'>";
                echo "<div class='cerrar-detalle'><button onclick='this.parentNode.parentNode.style.display=\"none\"'>Cerrar</button></div>";
                echo "<div class='producto-principal'>";
                echo "<div class='imagen-principal'>$imagen_tag</div>";
                echo "<div class='info-principal'>";
                echo "<h1>$nombre</h1>";
                echo "<p><strong>Descripción:</strong> $descrip</p>";
                echo "<p><strong>Stock:</strong> $stock</p>";
                
                // Obtener información del armario para este producto
                $id_tabla = $fila["id_tabla"];
                $info_armario = "No asignado";
                $ubicacion_armario = "";
                
                if ($id_tabla > 0) {
                    $consulta_arm = "SELECT * FROM armarios WHERE id_tabla = $id_tabla";
                    $resultado_arm = baseDatos($consulta_arm);
                    if (mysqli_num_rows($resultado_arm) > 0) {
                        $armario_data = mysqli_fetch_assoc($resultado_arm);
                        $info_armario = htmlspecialchars($armario_data['nombre']);
                        $ubicacion_armario = htmlspecialchars($armario_data['ubicacion']);
                    }
                }
                
                echo "<p><strong>Armario:</strong> $info_armario</p>";
                if (!empty($ubicacion_armario)) {
                    echo "<p><strong>Ubicación del armario:</strong> $ubicacion_armario</p>";
                }
                echo "<p><strong>Categoría:</strong> $categoria</p>";
                
                // Botones de edición en el detalle solo en modo edición
                if($modo_edicion && $es_profesor_verificado) {
                    echo "
                    <div class='edit-buttons'>
                        <a href='editar_producto.php?id=$id'><button class='edit-btn'>Editar este producto</button></a>
                        <a href='eliminar_producto.php?id=$id' onclick='return confirm(\"¿Está seguro que desea eliminar este producto?\")'><button class='delete-btn'>Eliminar este producto</button></a>
                    </div>";
                }
                
                echo "</div>"; // info-principal
                echo "</div>"; // producto-principal
                
                // Buscamos componentes relacionados (estado = off) con el mismo nombre base
                $nombre_base = explode(' ', $nombre)[0]; // Tomamos la primera palabra como base
                $consulta_rel = "SELECT * FROM inventario WHERE estado = 'off' AND nombre LIKE '$nombre_base%' ORDER BY nombre";
                $resultado_rel = baseDatos($consulta_rel);
                
                if(mysqli_num_rows($resultado_rel) > 0) {
                    echo "<div class='productos-relacionados'>";
                    echo "<h2>Componentes relacionados</h2>";
                    echo "<div class='mini-items'>";
                    
                    while($rel = mysqli_fetch_array($resultado_rel)) {
                        $id_rel = $rel["id"];
                        $nombre_rel = htmlspecialchars($rel["nombre"]);
                        $descrip_rel = htmlspecialchars($rel["descrip"]);
                        $stock_rel = $rel["stock"];
                        $ruta_imagen_rel = $rel["imagen"];
                        
                        // Verificar si la imagen es una ruta o datos binarios
                        $imagen_rel_tag = '';
                        if (!empty($ruta_imagen_rel)) {
                            if (filter_var($ruta_imagen_rel, FILTER_VALIDATE_URL) || strpos($ruta_imagen_rel, 'http') === 0 || strpos($ruta_imagen_rel, 'img/') === 0) {
                                // Es una URL externa (imgbb) o ruta local
                                $imagen_rel_tag = "<img src='" . htmlspecialchars($ruta_imagen_rel) . "' alt='$nombre_rel'>";
                            } else {
                                // Si es un dato binario, crear una imagen en base64
                                $imagen_rel_tag = "<img src='data:image/jpeg;base64," . base64_encode($ruta_imagen_rel) . "' alt='$nombre_rel'>";
                            }
                        } else {
                            $imagen_rel_tag = "<img src='img/no-image.png' alt='Sin imagen'>";
                        }
                        
                        echo "<div class='mini-item'>";
                        echo "<div class='mini-foto'>$imagen_rel_tag</div>";
                        echo "<div class='mini-info'>";
                        echo "<h3>$nombre_rel</h3>";
                        echo "<p>$descrip_rel</p>";
                        echo "<p>Stock: $stock_rel</p>";
                        
                        // Botones de edición para componentes relacionados
                        if($modo_edicion && $es_profesor_verificado) {
                            echo "
                            <div class='edit-buttons'>
                                <a href='editar_producto.php?id=$id_rel'><button class='edit-btn'>Editar</button></a>
                                <a href='eliminar_producto.php?id=$id_rel' onclick='return confirm(\"¿Está seguro que desea eliminar este componente?\")'><button class='delete-btn'>Eliminar</button></a>
                            </div>";
                        }
                        
                        echo "</div>"; // mini-info
                        echo "</div>"; // mini-item
                    }
                    
                    echo "</div>"; // mini-items
                    echo "</div>"; // productos-relacionados
                } else {
                    echo "<p>Este producto no tiene componentes relacionados.</p>";
                }
                
                echo "</div>"; // detalle-producto
            }
            ?>
        </div> 
    </main>

    <script>
        // Script para manejar el cambio en el interruptor de modo edición
        document.getElementById('modoEdicionSwitch').addEventListener('change', function() {
            // Redireccionar con el nuevo estado del modo edición
            window.location.href = 'backend.php?modo_edicion=' + (this.checked ? '1' : '0');
        });
    </script>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <a href="https://www.instagram.com/00__facundo__00/" target="_blank">@00__facundo__00</a><br>
                <a href="https://www.instagram.com/_demianleiva/" target="_blank">@_demianleiva</a>
            </div>
            <div class="footer-col logo-footer">
                <div class="footer-ig-2">
                    <a href="https://www.instagram.com/j4noo.097/" target="_blank">@j4noo.097</a><br>
                    <a href="https://www.instagram.com/sr_abou/" target="_blank">@sr_abou</a>
                </div>
            </div>
            <div class="footer-col footer-rights">
                &copy; 2025 Ynventaris<br>Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>