<?php
session_start();
include("funciones.php");

// Verificar sesión y permisos
if(!isset($_SESSION['id_usuario'])) {
    header("Location: ../../usuarios/login.php");
    exit;
}

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

// Mostrar mensajes de éxito/error
if(isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../../estilos.css">
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
            background: #2c3e50;
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
            color: #2c3e50;
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
            background: #3498db;
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
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
            }
            
            .preview-section {
                flex: 1;
            }
        }
        
        /* Estilos para la navegación */
        nav {
            display: flex;
            flex-wrap: wrap;
            padding: 0 2rem;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .titulo {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .titulo h1 {
            font-size: 2.2rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            position: relative;
        }
        
        .titulo h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #3498db;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestión de Inventario</h1>
            <nav>
                <a href="agregar2.php" class="btn btn-success">Agregar Producto</a>
                <a href="../../usuarios/admin.php" class="btn btn-primary">Gestionar Usuarios</a>
                <a href="../armarios/gestion_armarios.php" class="btn btn-primary">Gestionar Armarios</a>
                <a href="../../comentarios/ver_comentario.php" class="btn btn-primary">Gestionar Comentarios</a>
                <a href="../../backend.php" class="btn btn-secondary">Volver al inicio</a>
            </nav>
        </div>
        
        <div class="content">
            <div class="titulo">
                <h1>Agregar productos</h1>
            </div>
            
            <?php if(isset($mensaje)): ?>
                <div class="alert alert-success">✅ <?php echo $mensaje; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form id="form-producto" action="agregar3.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="5242880"> <!-- 5MB -->
                
                <div class="form-container">
                    <!-- Formulario principal -->
                    <div class="form-section">
                        <h3>📝 Información del Producto</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" id="nombre" name="nombre" required autofocus>
                            </div>
                            
                            <div class="form-group">
                                <label for="descrip">Descripción:</label>
                                <textarea id="descrip" name="descrip" required rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="stock">Stock:</label>
                                <input type="number" id="stock" name="stock" required min="1" max="99999">
                            </div>
                            
                            <div class="form-group">
                                <label for="lugar">Lugar de ubicación:</label>
                                <select id="lugar" name="lugar" required>
                                    <?php
                                    $consulta = "SELECT id_tabla, nombre FROM armarios ORDER BY nombre";
                                    $resultado = baseDatos($consulta);
                                    
                                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                                        while ($fila = mysqli_fetch_assoc($resultado)) {
                                            echo "<option value='{$fila['id_tabla']}'>{$fila['nombre']}</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No hay armarios disponibles</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="categoria">Categoría:</label>
                                <input type="text" id="categoria" name="categoria" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="estado">Estado:</label>
                                <select id="estado" name="estado" required>
                                    <option value="on">🟢 Principal</option>
                                    <option value="off">🟡 Secundario</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vista previa y nueva imagen -->
                    <div class="form-section preview-section">
                        <h3>🖼️ Imagen del Producto</h3>
                        
                        <div class="current-image">
                            <div class="no-image" id="imagePreview">📷 Sin imagen</div>
                        </div>
                        
                        <div class="form-group">
                            <label>Imagen del Producto (requerido):</label>
                            <div class="file-input-wrapper">
                                <span id="file-name">📁 Seleccionar imagen</span>
                                <input type="file" name="imagen" id="imagen" required
                                       accept=".jpg,.jpeg,.png" 
                                       onchange="previewImage(this); updateFileName(this)">
                            </div>
                            <small style="color: #666; font-size: 13px;">Formatos permitidos: JPG, PNG (Máximo 5MB)</small>
                        </div>
                    </div>
                </div>
                
                <div class="actions">
                    <button type="submit" name="boton" class="btn btn-primary">
                        💾 Guardar Producto
                    </button>
                    <a href="../inventario.php" class="btn btn-secondary">
                        ← Volver
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para mostrar el nombre del archivo seleccionado
        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : '📁 Seleccionar imagen';
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
                
                // Validar tamaño
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (input.files[0].size > maxSize) {
                    alert('Error: La imagen es demasiado grande (máximo 5MB permitido)');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="max-width:100%;height:200px;object-fit:contain;">`;
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