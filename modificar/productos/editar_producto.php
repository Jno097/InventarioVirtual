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

// Configuración de imgBB (REEMPLAZA CON TU API KEY REAL)
define('IMGBB_API_KEY', 'tu_api_key_aqui');

// Variables para mensajes
$success_message = "";
$error_message = "";
$producto = null;

// Validar ID del producto
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: inventario.php");
    exit;
}

$id_producto = (int)$_GET['id'];

// Obtener datos del producto
$consulta_producto = "SELECT i.*, a.nombre as armario_nombre FROM inventario i 
                     LEFT JOIN armarios a ON i.id_tabla = a.id_tabla 
                     WHERE i.id = $id_producto";
$resultado_producto = baseDatos($consulta_producto);

if(!$resultado_producto || mysqli_num_rows($resultado_producto) == 0) {
    header("Location: inventario.php");
    exit;
}

$producto = mysqli_fetch_assoc($resultado_producto);

// Función mejorada para subir imagen a imgBB con manejo de errores detallado
function uploadToImgBB($image_temp) {
    // 1. Verificar archivo temporal
    if (!file_exists($image_temp)) {
        return ['error' => 'El archivo temporal no existe'];
    }

    if (!is_readable($image_temp)) {
        return ['error' => 'No se puede leer el archivo temporal'];
    }

    // 2. Verificar tamaño del archivo (opcional)
    $file_size = filesize($image_temp);
    if ($file_size > 10 * 1024 * 1024) { // 10MB máximo
        return ['error' => 'El archivo es demasiado grande (máximo 10MB)'];
    }

    // 3. Verificar tipo MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $image_temp);
    finfo_close($finfo);

    $allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!array_key_exists($mime, $allowed_types)) {
        return ['error' => 'Solo se permiten imágenes JPG o PNG'];
    }

    // 4. Leer y codificar la imagen
    $image_data = file_get_contents($image_temp);
    if ($image_data === false) {
        return ['error' => 'Error al leer el archivo'];
    }

    $base64_image = base64_encode($image_data);
    if (!$base64_image) {
        return ['error' => 'Error al codificar la imagen'];
    }

    // 5. Configurar cURL
    $ch = curl_init();
    if ($ch === false) {
        return ['error' => 'Error al inicializar cURL'];
    }

    $post_fields = [
        'key' => IMGBB_API_KEY,
        'image' => $base64_image
    ];

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.imgbb.com/1/upload',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post_fields,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => ['Content-Type: multipart/form-data']
    ]);

    // 6. Ejecutar la petición
    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => "Error cURL: $error"];
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 7. Procesar respuesta
    if ($http_code !== 200) {
        return ['error' => "Error HTTP $http_code al conectar con ImgBB"];
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Error al decodificar la respuesta JSON'];
    }

    if (!isset($result['data']['url'])) {
        return ['error' => 'Respuesta inesperada de ImgBB'];
    }

    return ['success' => $result['data']['url']];
}

// Procesar formulario de edición
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_producto'])) {
    $nombre = sanitizar($_POST["nombre"]);
    $descrip = sanitizar($_POST["descrip"]);
    $stock = (int)$_POST["stock"];
    $lugar = (int)$_POST["lugar"];
    $categoria = sanitizar($_POST["categoria"]);
    $estado = sanitizar($_POST["estado"]);
    
    // Manejo de imagen
    if(isset($_FILES["imagen"]["tmp_name"]) && $_FILES["imagen"]["error"] == 0) {
        $upload_result = uploadToImgBB($_FILES["imagen"]["tmp_name"]);
        
        if(isset($upload_result['success'])) {
            $imgbb_url = $upload_result['success'];
            $consulta = "UPDATE inventario SET 
                        nombre='$nombre', 
                        descrip='$descrip', 
                        stock=$stock, 
                        id_tabla=$lugar, 
                        categoria='$categoria', 
                        estado='$estado', 
                        imagen='$imgbb_url' 
                        WHERE id=$id_producto";
        } else {
            $error_message = $upload_result['error'];
        }
    } else {
        // Actualizar sin cambiar la imagen
        $consulta = "UPDATE inventario SET 
                    nombre='$nombre', 
                    descrip='$descrip', 
                    stock=$stock, 
                    id_tabla=$lugar, 
                    categoria='$categoria', 
                    estado='$estado' 
                    WHERE id=$id_producto";
    }
    
    if(empty($error_message)) {
        $resultado = baseDatos($consulta);
        if($resultado) {
            $success_message = "Producto actualizado correctamente.";
            // Actualizar datos mostrados
            $resultado_producto = baseDatos($consulta_producto);
            $producto = mysqli_fetch_assoc($resultado_producto);
        } else {
            $error_message = "Error al actualizar el producto en la base de datos.";
        }
    }
}

// Obtener armarios para el select
$consulta_armarios = "SELECT id_tabla, nombre FROM armarios ORDER BY nombre";
$armarios = baseDatos($consulta_armarios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: #4facfe;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .form-section {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        
        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .form-grid {
            display: grid;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .preview-section {
            flex: 0 0 350px;
        }
        
        .current-image {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .current-image img {
            max-width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 5px;
        }
        
        .no-image {
            width: 100%;
            height: 200px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            color: #777;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            background: #4facfe;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
        
        .btn-primary {
            background: #4facfe;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
            }
            
            .preview-section {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Editar Producto</h1>
            <p>Modifica la información del producto seleccionado</p>
        </div>
        
        <div class="content">
            <?php if($success_message): ?>
                <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if($error_message): ?>
                <div class="alert alert-danger">❌ <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-container">
                    <!-- Formulario principal -->
                    <div class="form-section">
                        <h3>📝 Información del Producto</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nombre">Nombre del Producto:</label>
                                <input type="text" name="nombre" id="nombre" 
                                       value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="descrip">Descripción:</label>
                                <textarea name="descrip" id="descrip" required><?php echo htmlspecialchars($producto['descrip']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="stock">Cantidad en Stock:</label>
                                <input type="number" name="stock" id="stock" 
                                       value="<?php echo (int)$producto['stock']; ?>" required min="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="lugar">Ubicación (Armario):</label>
                                <select name="lugar" id="lugar" required>
                                    <option value="">Seleccione un armario</option>
                                    <?php 
                                    mysqli_data_seek($armarios, 0);
                                    while($armario = mysqli_fetch_assoc($armarios)): ?>
                                        <option value="<?php echo (int)$armario['id_tabla']; ?>" 
                                                <?php echo ($armario['id_tabla'] == $producto['id_tabla']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($armario['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="categoria">Categoría:</label>
                                <input type="text" name="categoria" id="categoria" 
                                       value="<?php echo htmlspecialchars($producto['categoria']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="estado">Estado:</label>
                                <select name="estado" id="estado" required>
                                    <option value="on" <?php echo ($producto['estado'] == 'on') ? 'selected' : ''; ?>>🟢 Principal</option>
                                    <option value="off" <?php echo ($producto['estado'] == 'off') ? 'selected' : ''; ?>>🟡 Secundario</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vista previa y nueva imagen -->
                    <div class="form-section preview-section">
                        <h3>🖼️ Imagen del Producto</h3>
                        
                        <div class="current-image">
                            <?php if(!empty($producto['imagen'])): ?>
                                <?php if(filter_var($producto['imagen'], FILTER_VALIDATE_URL)): ?>
                                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen actual" id="imagePreview">
                                <?php elseif(file_exists($producto['imagen'])): ?>
                                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen actual" id="imagePreview">
                                <?php else: ?>
                                    <div class="no-image" id="imagePreview">📷 Imagen no disponible</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="no-image" id="imagePreview">📷 Sin imagen</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Nueva Imagen (opcional):</label>
                            <div class="file-input-wrapper">
                                <span id="file-name">📁 Seleccionar nueva imagen</span>
                                <input type="file" name="imagen" id="imagen" 
                                       accept=".jpg,.jpeg,.png" 
                                       onchange="previewImage(this); updateFileName(this)">
                            </div>
                            <small style="color: #666; font-size: 13px;">Formatos permitidos: JPG, PNG</small>
                        </div>
                    </div>
                </div>
                
                <div class="actions">
                    <button type="submit" name="editar_producto" class="btn btn-primary">
                        💾 Guardar Cambios
                    </button>
                    <a href="../inventario.php" class="btn btn-secondary">
                        ← Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para mostrar el nombre del archivo seleccionado
        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : '📁 Seleccionar nueva imagen';
            document.getElementById('file-name').textContent = fileName;
        }

        // Función para vista previa de imagen
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                // Validar tipo de imagen
                const validTypes = ['image/jpeg', 'image/png'];
                if (!validTypes.includes(input.files[0].type)) {
                    alert('Error: Solo se permiten imágenes JPG o PNG');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="max-width:100%;height:200px;object-fit:contain;">`;
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Detectar cambios en el formulario
        let formChanged = false;
        document.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('change', () => formChanged = true);
        });

        window.addEventListener('beforeunload', (e) => {
            if (formChanged) {
                e.preventDefault();
                return e.returnValue = '¿Estás seguro de salir sin guardar los cambios?';
            }
        });

        document.querySelector('form').addEventListener('submit', () => {
            formChanged = false;
        });
    </script>
</body>
</html>