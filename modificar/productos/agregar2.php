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
    <title>Super Wang</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../estilos.css">
    <style>
        /* Estilos generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        /* Header */
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

        /* Main content */
        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
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

        /* Form styles */
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

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: #fff;
            padding: 2rem 0;
            text-align: center;
        }

        .logo-footer {
            margin-bottom: 1rem;
        }

        .logo-footer img {
            max-height: 50px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 1rem 0;
            }
            
            .logo {
                margin-bottom: 1rem;
            }
            
            nav {
                justify-content: center;
                margin-top: 1rem;
            }
            
            nav a {
                margin-bottom: 0.5rem;
            }
            
            .form-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="../../backend.php" title="Volver">
                <img src="../../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
        <nav>
            <a href="multi_busc.php" title="Búsqueda" target="blank">BUSCAR</a>
            <a href="agregar_pro.php" title="Agregar productos" target="blank">AGREGAR</a>
            <a href="borrar_pro.php" title="Borrar productos" target="blank">ELIMINAR</a>
            <a href="#" title="Modificar productos" target="blank">EDITAR</a>
            <a href="../armarios/borrar_armario.php" title="Borras armarios" target="blank">ELIMINAR ARMARIOS</a>
            <a href="agregar_armario.php" title="Administrar armarios">ARMARIOS</a>
        </nav>
    </header>
    
    <main>
        <div class="titulo">
            <h1>Agregar productos</h1>
        </div>
        
        <div class="form-container">
            <form action="agregar3.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="descrip">Descripción:</label>
                    <input type="text" id="descrip" name="descrip" required>
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required min="1" max="99999">
                </div>
                
                <div class="form-group">
    <label for="lugar">Lugar de ubicación:</label>
    <select id="lugar" name="lugar" required>
        <?php
        // Consulta para obtener todos los armarios disponibles
        $consulta = "SELECT id_tabla, nombre FROM armarios ORDER BY nombre";
        $resultado = baseDatos($consulta);
        
        if ($resultado === false) {
            // Error en la consulta
            echo "<option value=''>Error al cargar armarios</option>";
        } else {
            $n = mysqli_num_rows($resultado);
            
            if ($n >= 1) {
                // Iterar sobre todos los armarios encontrados
                while ($fila = mysqli_fetch_assoc($resultado)) {
                    $id_tabla = $fila["id_tabla"];
                    $nombre = $fila["nombre"];
                    echo "<option value='$id_tabla'>$nombre</option>";
                }
            } else {
                // Si no hay armarios
                echo "<option value=''>No hay armarios disponibles</option>";
            }
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
                    <label for="imagen">Imagen (solo JPG):</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpg" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="boton">Enviar</button>
                </div>
            </form>
        </div>
    </main>
    
    <footer>
        <div class="logo-footer">
            <a href="../../backend.php" title="Volver">
                <img src="../../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
    </footer>
</body>
</html>