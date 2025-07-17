<?php
session_start();
include("funciones.php");

// Verificación de sesión
$usuario_logueado = isset($_SESSION['id']) && isset($_SESSION['nombre']) && isset($_SESSION['cate']);
$es_profesor_verificado = $usuario_logueado && $_SESSION['cate'] == 'profe' && isset($_SESSION['verificado']) && $_SESSION['verificado'] == 1;

// Modo edición
$modo_edicion = false;
if($es_profesor_verificado && isset($_GET['modo_edicion'])) {
    $_SESSION['modo_edicion'] = $_GET['modo_edicion'] == '1';
    $modo_edicion = $_SESSION['modo_edicion'];
} elseif(isset($_SESSION['modo_edicion'])) {
    $modo_edicion = $_SESSION['modo_edicion'];
}

// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "inventario");
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener y sanitizar parámetros de búsqueda
$termino_busqueda = isset($_GET['q']) ? mysqli_real_escape_string($conexion, trim($_GET['q'])) : '';
$filtro_armario = isset($_GET['armario']) ? intval($_GET['armario']) : 0;
$filtro_categoria = isset($_GET['categoria']) ? mysqli_real_escape_string($conexion, trim($_GET['categoria'])) : '';

// Construir consulta SQL
$consulta = "SELECT i.*, a.nombre as nombre_armario, a.ubicacion as ubicacion_armario 
            FROM inventario i 
            LEFT JOIN armarios a ON i.id_tabla = a.id_tabla 
            WHERE i.estado = 'on'";

$condiciones = array();

if (!empty($termino_busqueda)) {
    if (is_numeric($termino_busqueda)) {
        $condiciones[] = "(i.id = " . intval($termino_busqueda) . " 
                         OR i.nombre LIKE '%" . $termino_busqueda . "%' 
                         OR i.descrip LIKE '%" . $termino_busqueda . "%')";
    } else {
        $condiciones[] = "(i.nombre LIKE '%" . $termino_busqueda . "%' 
                         OR i.descrip LIKE '%" . $termino_busqueda . "%')";
    }
}

if ($filtro_armario > 0) {
    $condiciones[] = "i.id_tabla = " . intval($filtro_armario);
}

if (!empty($filtro_categoria)) {
    $condiciones[] = "i.categoria = '" . $filtro_categoria . "'";
}

if (!empty($condiciones)) {
    $consulta .= " AND " . implode(" AND ", $condiciones);
}

$consulta .= " ORDER BY i.nombre";
$resultado = mysqli_query($conexion, $consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Buscar Productos</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../backend.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="backend.php" title="Inicio">
                 <img src="../../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
        <nav>
            <a href="buscar.php" title="Búsqueda" class="active">BUSCAR</a>
            <div class="dropdown">
                <button class="dropbtn">ARMARIOS</button>
                <div class="dropdown-content">
                    <a href="backend.php">Todos los armarios</a>
                    <?php if($es_profesor_verificado): ?>
                        <a href="../armarios/agregar_armario.php">Agregar armario</a>
                    <?php endif; ?>
                    <?php
                    $consulta_nav_armarios = "SELECT * FROM armarios ORDER BY nombre";
                    $resultado_nav_armarios = mysqli_query($conexion, $consulta_nav_armarios);
                    
                    while ($armario_nav = mysqli_fetch_assoc($resultado_nav_armarios)) {
                        echo '<a href="../../backend.php?armario_id=' . $armario_nav['id_tabla'] . '">' . htmlspecialchars($armario_nav['nombre']) . '</a>';
                    }
                    ?>
                </div>
            </div>
            <?php if($usuario_logueado): ?>
                <?php if($es_profesor_verificado): ?>
                    <div class="modo-edicion-switch">
                        <span class="edit-label">Modo edición:</span>
                        <label class="switch">
                            <input type="checkbox" id="modoEdicionSwitch" <?php echo $modo_edicion ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                <?php endif; ?>
                <a href="../../usuarios/logout.php">CERRAR SESIÓN (<?php echo htmlspecialchars($_SESSION['nombre']); ?>)</a>
            <?php else: ?>
                <a href="../../usuarios/login.php">INICIAR SESIÓN</a>
                <a href="../../usuarios/registro.php">REGISTRARSE</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <div class="titulo">
            <h1>Buscar Productos</h1>
        </div>
        
        <form class="formulario-busqueda" method="get" action="buscar.php">
            <div class="grupo-campos">
                <div class="campo">
                    <label for="q">Término de búsqueda</label>
                    <input type="text" id="q" name="q" placeholder="ID, nombre o descripción" value="<?php echo htmlspecialchars($termino_busqueda); ?>">
                </div>
                
                <div class="campo">
                    <label for="armario">Filtrar por armario</label>
                    <select id="armario" name="armario">
                        <option value="0">Todos los armarios</option>
                        <?php
                        $consulta_armarios = "SELECT * FROM armarios ORDER BY nombre";
                        $resultado_armarios = mysqli_query($conexion, $consulta_armarios);
                        
                        while ($armario = mysqli_fetch_assoc($resultado_armarios)) {
                            $selected = ($filtro_armario == $armario['id_tabla']) ? 'selected' : '';
                            echo '<option value="' . $armario['id_tabla'] . '" ' . $selected . '>' . 
                                 htmlspecialchars($armario['nombre']) . ' (' . htmlspecialchars($armario['ubicacion']) . ')</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="campo">
                    <label for="categoria">Filtrar por categoría</label>
                    <select id="categoria" name="categoria">
                        <option value="">Todas las categorías</option>
                        <?php
                        $consulta_categorias = "SELECT DISTINCT categoria FROM inventario WHERE estado = 'on' ORDER BY categoria";
                        $resultado_categorias = mysqli_query($conexion, $consulta_categorias);
                        
                        while ($categoria = mysqli_fetch_assoc($resultado_categorias)) {
                            $selected = ($filtro_categoria == $categoria['categoria']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($categoria['categoria']) . '" ' . $selected . '>' . 
                                 htmlspecialchars($categoria['categoria']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <button type="submit">Buscar</button>
            <button type="button" class="limpiar" onclick="limpiarFormulario()">Limpiar</button>
            <?php if($modo_edicion): ?>
                <input type="hidden" name="modo_edicion" value="1">
            <?php endif; ?>
        </form>
        
        <div class="resultados-busqueda">
            <?php
            if (!empty($termino_busqueda) || $filtro_armario > 0 || !empty($filtro_categoria)) {
                if ($resultado) {
                    $num_resultados = mysqli_num_rows($resultado);
                    
                    echo "<h2>Resultados de la búsqueda (" . $num_resultados . ")</h2>";
                    
                    if ($num_resultados > 0) {
                        echo '<div class="productos ' . ($modo_edicion ? 'modo-edicion' : '') . '">';
                        
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            $id = $fila["id"];
                            $nombre = htmlspecialchars($fila["nombre"]);
                            $descrip = htmlspecialchars($fila["descrip"]);
                            $stock = $fila["stock"];
                            $categoria = htmlspecialchars($fila["categoria"]);
                            $ruta_imagen = $fila["imagen"];
                            $nombre_armario = $fila["nombre_armario"] ? htmlspecialchars($fila["nombre_armario"]) : "No asignado";
                            $ubicacion_armario = $fila["ubicacion_armario"] ? htmlspecialchars($fila["ubicacion_armario"]) : "";
                            
                            $imagen_tag = '';
                            if (!empty($ruta_imagen)) {
                                if (filter_var($ruta_imagen, FILTER_VALIDATE_URL) || strpos($ruta_imagen, 'img/') === 0) {
                                    $imagen_tag = "<img src='" . htmlspecialchars($ruta_imagen) . "' alt='$nombre'>";
                                } else {
                                    $imagen_tag = "<img src='data:image/jpeg;base64," . base64_encode($ruta_imagen) . "' alt='$nombre'>";
                                }
                            } else {
                                $imagen_tag = "<img src='img/no-image.png' alt='Sin imagen'>";
                            }
                            
                            echo "
                            <div class='item'>
                                <div class='foto'>
                                    <a href='backend.php?armario_id=" . $fila['id_tabla'] . "' title='Ver en armario'>
                                        $imagen_tag
                                    </a>
                                </div>
                                <div class='nombre'>$nombre</div>
                                <div class='info-adicional'>
                                    <p><strong>ID:</strong> $id</p>
                                    <p><strong>Descripción:</strong> $descrip</p>
                                    <p><strong>Stock:</strong> $stock</p>
                                    <p><strong>Armario:</strong> $nombre_armario</p>
                                    <p><strong>Categoría:</strong> $categoria</p>
                                </div>";
                                
                                if($modo_edicion && $es_profesor_verificado) {
                                    echo "
                                    <div class='edit-buttons'>
                                        <a href='editar_producto.php?id=$id'><button class='edit-btn'>Editar</button></a>
                                        <a href='eliminar_producto.php?id=$id' onclick='return confirm(\"¿Está seguro que desea eliminar este producto?\")'>
                                            <button class='delete-btn'>Eliminar</button>
                                        </a>
                                    </div>";
                                }
                                
                            echo "</div>";
                        }
                        
                        echo '</div>';
                    } else {
                        echo '<div class="sin-resultados">
                                <h3>No se encontraron productos</h3>
                                <p>No se encontraron productos que coincidan con los criterios de búsqueda.</p>
                              </div>';
                    }
                } else {
                    echo '<div class="sin-resultados">
                            <h3>Error en la búsqueda</h3>
                            <p>Ocurrió un error al realizar la búsqueda.</p>
                          </div>';
                }
            } else {
                echo '<div class="sin-resultados">
                        <h3>Búsqueda de productos</h3>
                        <p>Ingrese al menos un criterio de búsqueda.</p>
                      </div>';
            }
            
            mysqli_close($conexion);
            ?>
        </div>
    </main>
    
    <script>
        // Manejar el interruptor de modo edición
        if (document.getElementById('modoEdicionSwitch')) {
            document.getElementById('modoEdicionSwitch').addEventListener('change', function() {
                window.location.href = 'backend.php?modo_edicion=' + (this.checked ? '1' : '0');
            });
        }

        // Limpiar formulario
        function limpiarFormulario() {
            document.getElementById('q').value = '';
            document.getElementById('armario').value = '0';
            document.getElementById('categoria').value = '';
            document.querySelector('.formulario-busqueda').submit();
        }

        // Buscar al presionar Enter
        document.getElementById('q').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('.formulario-busqueda').submit();
            }
        });
    </script>
</body>
</html>