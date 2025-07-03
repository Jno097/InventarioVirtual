<?php
session_start();
include("funciones.php");

// Procesar login si se envió el formulario
if(isset($_POST['boton'])) {
    // Sanitizar entradas
    $mail = sanitizar($_POST["mail"]);
    $contraseña = $_POST["contraseña"];

    // Validar el mail
    if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
        $error_message = "El formato del correo electrónico no es válido";
    } else {
        // Escapar para prevenir SQL injection
        $conexion = mysqli_connect("localhost", "root", "");
        mysqli_select_db($conexion, "inventario");
        $mail = mysqli_real_escape_string($conexion, $mail);
        $contraseña = mysqli_real_escape_string($conexion, $contraseña);

        // Consultar la base de datos tabla login
        $consulta = "SELECT * FROM login WHERE mail = '$mail' LIMIT 1";
        $resultado = baseDatos($consulta);

        if(mysqli_num_rows($resultado) > 0) {
            $usuario = mysqli_fetch_assoc($resultado);
            
            // Verificar contraseña
            if($usuario['contraseña'] === $contraseña) {
                // Login exitoso - asignar datos básicos de sesión
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['mail'] = $usuario['mail'];
                $_SESSION['verificado'] = $usuario['verificado'];
                
                // Verificar tipo de usuario basado en la categoría
                if ($usuario['cate'] === 'admin' || $usuario['cate'] == 1) {
                    // Administrador (puede ser 'admin' como string o 1 como número)
                    $_SESSION['cate'] = 'admin';
                    $success_message = "Bienvenido administrador " . $usuario['nombre'];
                    $redirect_url = "admin.php";
                } else if ($usuario['cate'] === 'profe' || $usuario['cate'] == 1) {
                    // Profesor
                    if ($usuario['verificado'] == 0) {
                        // Profesor pendiente de verificación
                        $error_message = "Su cuenta de profesor está pendiente de verificación";
                        session_destroy(); // No permitir inicio de sesión hasta verificación
                    } else {
                        // Profesor verificado
                        $_SESSION['cate'] = 'profe';
                        $success_message = "Bienvenido profesor " . $usuario['nombre'];
                        $redirect_url = "../modificar/inventario.php";
                    }
                } else if ($usuario['cate'] === 'estu' || $usuario['cate'] == 2) {
                    // Estudiante
                    $_SESSION['cate'] = 'estu';
                    if(isset($usuario['curso']) && !empty($usuario['curso'])) {
                        $_SESSION['curso'] = $usuario['curso'];
                    }
                    $success_message = "Bienvenido estudiante " . $usuario['nombre'];
                    $redirect_url = "../backend.php";
                } else {
                    // Usuario normal o cualquier otro tipo
                    $_SESSION['cate'] = 'user';
                    $success_message = "Bienvenido " . $usuario['nombre'];
                    $redirect_url = "../backend.php";
                }
                
                if(isset($success_message) && isset($redirect_url)) {
                    echo "<script>alert('$success_message'); window.location='$redirect_url';</script>";
                    exit;
                }
            } else {
                $error_message = "Contraseña incorrecta";
            }
        } else {
            $error_message = "El usuario no existe";
        }

        mysqli_close($conexion);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ynventaris - Iniciar Sesión</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../estilos.css">
    <style>
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        .form-group {
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="../backend.php" title="Volver">
                <img src="../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
        <nav>
            <a href="multi_busc.php" title="Búsqueda">BUSCAR</a>
            <a href="registro.php" title="Registrarse">REGISTRARSE</a>
        </nav>
    </header>
    <main>
        <div class="titulo">
            <h1>Iniciar Sesión</h1>
        </div>
        
        <?php if(isset($error_message)): ?>
        <div class="error-message">
            <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>
        
        <div class="formulario">
            <form method="post">
                <div class="form-group">
                    <p>Email:
                        <input type="email" name="mail" required 
                               value="<?php echo isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : ''; ?>">
                    </p>
                </div>
                <div class="form-group">
                    <p>Contraseña:
                        <input type="password" name="contraseña" required>
                    </p>
                </div>
                <div class="form-group">
                    <button type="submit" name="boton">Iniciar Sesión</button>
                </div>
                <p>
                    ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
                </p>
            </form>
        </div>
    </main>
    <footer>
        <div class="logo-footer">
            <a href="../backend.php" title="Volver">
                <img src="../img/fotos_pag/logo.png" class="flogo">
            </a>
        </div>
    </footer>
</body>
</html>