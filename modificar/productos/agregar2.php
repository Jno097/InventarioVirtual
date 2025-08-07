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
        
        
        header {
            background-color: #2c3e50;
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo {
            padding: 0 2rem;
        }
        
        .flogo {
            max-height: 60px;
            transition: transform 0.3s ease;
        }
        
        .flogo:hover {
            transform: scale(1.05);
        }
        
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
        
        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group input[type="file"] {
            padding: 0.5rem;
        }
        
        .form-group button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
            font-weight: 600;
            width: 100%;
        }
        
        .form-group button:hover {
            background-color: #2980b9;
        }
        
        .mensaje {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            text-align: center;
        }
        
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        footer {
            background-color: #2c3e50;
            color: #fff;
            padding: 2rem 0;
            text-align: center;
        }
        
        .logo-footer {
            margin-bottom: 1rem;
        }
        
       
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
    
</div>
<div>
        <a href="agregar2.php" class="btn btn-success">Agregar Producto</a>
    <a href="../../usuarios/admin.php" class="btn btn-primary">Gestionar Usuarios</a>
    <a href="../armarios/gestion_armarios.php" class="btn btn-primary">Gestionar Armarios</a>
    <a href="../../comentarios/ver_comentario.php" class="btn btn-primary">Gestionar Comentarios</a>
        <a href="../../backend.php" class="btn btn-secondary">Volver al inicio</a>
    </div>
    
    <main>
        <div class="titulo">
            <h1>Agregar productos</h1>
        </div>
        
        <?php if(isset($mensaje)): ?>
            <div class="mensaje mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="mensaje mensaje-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form id="form-producto" action="agregar3.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="5242880"> <!-- 5MB -->
                
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
                        <option value="on">Principal</option>
                        <option value="off">Secundario</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="imagen">Archivo adjunto (cualquier tipo, máximo 5MB):</label>
                    <input type="file" id="imagen" name="imagen" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="boton">Enviar</button>
                </div>
            </form>
        </div>
    </main>
    

    <script>
        document.getElementById('form-producto').addEventListener('submit', function(e) {
            const archivo = document.getElementById('imagen').files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (archivo && archivo.size > maxSize) {
                alert('El archivo es demasiado grande (máximo 5MB permitido)');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>