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
    echo "<script>alert('Acceso no autorizado. Solo administradores y profesores verificados pueden acceder.'); window.location='../../backend.php';</script>";
    exit;
}

// Variables para mensajes usando parámetros GET
$success_message = "";
$error_message = "";

// Leer mensajes de los parámetros GET
if(isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'added':
            $success_message = "Producto agregado correctamente";
            break;
        case 'updated':
            $success_message = "Producto actualizado correctamente";
            break;
        case 'deleted':
            $success_message = "Producto eliminado correctamente";
            break;
    }
}

if(isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'add_failed':
            $error_message = "Error al agregar el producto";
            break;
        case 'update_failed':
            $error_message = "Error al actualizar el producto";
            break;
        case 'delete_failed':
            $error_message = "Error al eliminar el producto";
            break;
        case 'image_upload':
            $error_message = "Error al subir la imagen";
            break;
        case 'no_image':
            $error_message = "Debe seleccionar una imagen";
            break;
    }
}

// Procesar acciones POST
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // AGREGAR PRODUCTO
    if(isset($_POST['agregar_producto'])) {
        $nombre = sanitizar($_POST["nombre"]);
        $descrip = sanitizar($_POST["descrip"]);
        $stock = (int)$_POST["stock"];
        $lugar = (int)$_POST["lugar"];
        $categoria = sanitizar($_POST["categoria"]);
        $estado = sanitizar($_POST["estado"]);
        
        // Manejo de imágenes
        if(isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $imagen_nombre = $_FILES["imagen"]["name"];
            $imagen_temp = $_FILES["imagen"]["tmp_name"];
            $upload_dir = "img/productos/";
            
            // Crear directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generar nombre único para la imagen
            $imagen_nombre_final = time() . '_' . $imagen_nombre;
            $ruta_completa = $upload_dir . $imagen_nombre_final;
            
            // Subir imagen
            if (move_uploaded_file($imagen_temp, $ruta_completa)) {
                $consulta = "INSERT INTO inventario (nombre, descrip, stock, id_tabla, categoria, estado, imagen) 
                            VALUES ('$nombre', '$descrip', $stock, $lugar, '$categoria', '$estado', '$ruta_completa')";
                $resultado = baseDatos($consulta);
                
                if($resultado) {
                    // Redirigir con mensaje de éxito
                    header("Location: inventario.php?success=added");
                    exit;
                } else {
                    // Redirigir con mensaje de error
                    header("Location: inventario.php?error=add_failed");
                    exit;
                }
            } else {
                header("Location: inventario.php?error=image_upload");
                exit;
            }
        } else {
            header("Location: inventario.php?error=no_image");
            exit;
        }
    }
    
    // EDITAR PRODUCTO
    if(isset($_POST['editar_producto'])) {
        $id = (int)$_POST['id'];
        $nombre = sanitizar($_POST["nombre"]);
        $descrip = sanitizar($_POST["descrip"]);
        $stock = (int)$_POST["stock"];
        $lugar = (int)$_POST["lugar"];
        $categoria = sanitizar($_POST["categoria"]);
        $estado = sanitizar($_POST["estado"]);
        
        // Verificar si se subió una nueva imagen
        if(isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $imagen_nombre = $_FILES["imagen"]["name"];
            $imagen_temp = $_FILES["imagen"]["tmp_name"];
            $upload_dir = "img/productos/";
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $imagen_nombre_final = time() . '_' . $imagen_nombre;
            $ruta_completa = $upload_dir . $imagen_nombre_final;
            
            if (move_uploaded_file($imagen_temp, $ruta_completa)) {
                $consulta = "UPDATE inventario SET nombre='$nombre', descrip='$descrip', stock=$stock, 
                            id_tabla=$lugar, categoria='$categoria', estado='$estado', imagen='$ruta_completa' 
                            WHERE id=$id";
            } else {
                header("Location: inventario.php?error=image_upload");
                exit;
            }
        } else {
            // Actualizar sin cambiar la imagen
            $consulta = "UPDATE inventario SET nombre='$nombre', descrip='$descrip', stock=$stock, 
                        id_tabla=$lugar, categoria='$categoria', estado='$estado' WHERE id=$id";
        }
        
        $resultado = baseDatos($consulta);
        if($resultado) {
            header("Location: inventario.php?success=updated");
            exit;
        } else {
            header("Location: inventario.php?error=update_failed");
            exit;
        }
    }
    
    // ELIMINAR PRODUCTO
    if(isset($_POST['eliminar_producto'])) {
        $id = (int)$_POST['eliminar_producto'];
        
        // Obtener la ruta de la imagen antes de eliminar
        $consulta_imagen = "SELECT imagen FROM inventario WHERE id = $id";
        $resultado_imagen = baseDatos($consulta_imagen);
        $fila_imagen = mysqli_fetch_assoc($resultado_imagen);
        
        // Eliminar el producto
        $consulta = "DELETE FROM inventario WHERE id = $id";
        $resultado = baseDatos($consulta);
        
     
    }
}

// Obtener todos los productos
$consulta_productos = "SELECT i.*, a.nombre as armario_nombre FROM inventario i 
                      LEFT JOIN armarios a ON i.id_tabla = a.id_tabla 
                      ORDER BY i.nombre";
$productos = baseDatos($consulta_productos);

// Obtener armarios para los selects
$consulta_armarios = "SELECT id_tabla, nombre FROM armarios ORDER BY nombre";
$armarios = baseDatos($consulta_armarios);

// Para el formulario de edición
$producto_editar = null;
if(isset($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    $consulta_editar = "SELECT * FROM inventario WHERE id = $id_editar";
    $resultado_editar = baseDatos($consulta_editar);
    $producto_editar = mysqli_fetch_assoc($resultado_editar);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
            font-size: 14px;
        }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-warning { background-color: #ffc107; color: #212529; }
        .btn:hover { opacity: 0.8; }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .section {
            margin-bottom: 40px;
        }
        .section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .producto-imagen {
            max-width: 60px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover { color: black; }
    </style>
</head>
<body>
    <div class="container">
        <!-- En la sección de header, agregar el enlace -->
<div class="header">
    <h1>Gestión de Inventario</h1>
    <div>
        <a href="../armarios/gestion_armarios.php" class="btn btn-success">Gestionar Armarios</a>

    </div>
</div>

        <?php if($success_message): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Lista de productos -->
        <div class="section">
            <h2>Productos en Inventario</h2>
            <?php if(mysqli_num_rows($productos) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                            <th>Ubicación</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($producto = mysqli_fetch_assoc($productos)): ?>
                            <tr>
                                <td>
                                    <?php if($producto['imagen'] && file_exists($producto['imagen'])): ?>
                                        <img src="<?php echo $producto['imagen']; ?>" class="producto-imagen" alt="Imagen del producto">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background-color: #eee; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                            Sin imagen
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo sanitizar($producto['nombre']); ?></td>
                                <td><?php echo sanitizar($producto['descrip']); ?></td>
                                <td><?php echo $producto['stock']; ?></td>
                                <td><?php echo sanitizar($producto['armario_nombre'] ?? 'Sin asignar'); ?></td>
                                <td><?php echo sanitizar($producto['categoria']); ?></td>
                                <td>
                                    <?php if($producto['estado'] == 'on'): ?>
                                        <span class="badge badge-success">Principal</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Secundario</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                <a href="productos/editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-warning">Editar</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="eliminar_producto" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay productos en el inventario.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para agregar producto -->
    <div id="agregarModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('agregarModal')">&times;</span>
            <h2>Agregar Nuevo Producto</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="descrip">Descripción:</label>
                        <textarea name="descrip" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" required min="0" max="99999">
                    </div>
                    <div class="form-group">
                        <label for="lugar">Ubicación:</label>
                        <select name="lugar" required>
                            <option value="">Seleccione un armario</option>
                            <?php
                            mysqli_data_seek($armarios, 0);
                            while($armario = mysqli_fetch_assoc($armarios)):
                            ?>
                                <option value="<?php echo $armario['id_tabla']; ?>"><?php echo sanitizar($armario['nombre']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoría:</label>
                        <input type="text" name="categoria" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select name="estado" required>
                            <option value="on">Principal</option>
                            <option value="off">Secundario</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="imagen">Imagen:</label>
                        <input type="file" name="imagen" accept="image/*" required>
                    </div>
                </div>
                <button type="submit" name="agregar_producto" class="btn btn-success">Agregar Producto</button>
            </form>
        </div>
    </div>

    <!-- Modal para editar producto -->
    <div id="editarModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editarModal')">&times;</span>
            <h2>Editar Producto</h2>
            <form method="POST" enctype="multipart/form-data" id="editarForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_nombre">Nombre:</label>
                        <input type="text" name="nombre" id="edit_nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_descrip">Descripción:</label>
                        <textarea name="descrip" id="edit_descrip" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_stock">Stock:</label>
                        <input type="number" name="stock" id="edit_stock" required min="0" max="99999">
                    </div>
                    <div class="form-group">
                        <label for="edit_lugar">Ubicación:</label>
                        <select name="lugar" id="edit_lugar" required>
                            <option value="">Seleccione un armario</option>
                            <?php
                            mysqli_data_seek($armarios, 0);
                            while($armario = mysqli_fetch_assoc($armarios)):
                            ?>
                                <option value="<?php echo $armario['id_tabla']; ?>"><?php echo sanitizar($armario['nombre']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_categoria">Categoría:</label>
                        <input type="text" name="categoria" id="edit_categoria" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_estado">Estado:</label>
                        <select name="estado" id="edit_estado" required>
                            <option value="on">Principal</option>
                            <option value="off">Secundario</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_imagen">Nueva Imagen (opcional):</label>
                        <input type="file" name="imagen" accept="image/*">
                        <small>Deje vacío para mantener la imagen actual</small>
                    </div>
                </div>
                <button type="submit" name="editar_producto" class="btn btn-warning">Actualizar Producto</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editarProducto(id) {
            // Hacer una petición AJAX para obtener los datos del producto
            fetch(`get_producto.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('edit_id').value = data.producto.id;
                        document.getElementById('edit_nombre').value = data.producto.nombre;
                        document.getElementById('edit_descrip').value = data.producto.descrip;
                        document.getElementById('edit_stock').value = data.producto.stock;
                        document.getElementById('edit_lugar').value = data.producto.id_tabla;
                        document.getElementById('edit_categoria').value = data.producto.categoria;
                        document.getElementById('edit_estado').value = data.producto.estado;
                        
                        openModal('editarModal');
                    } else {
                        alert('Error al cargar los datos del producto');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del producto');
                });
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for(let i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = 'none';
                }
            }
        }
        
        // Limpiar la URL después de mostrar el mensaje
        if (window.location.search.includes('success=') || window.location.search.includes('error=')) {
            setTimeout(function() {
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 3000); // Limpiar después de 3 segundos
        }
    </script>
</body>
</html>