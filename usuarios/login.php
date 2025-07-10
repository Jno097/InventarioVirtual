<?php
// Asegurar que la ruta es correcta
$ruta_funciones = __DIR__ . '/funciones.php';
if (!file_exists($ruta_funciones)) {
    die("Error: No se encontró el archivo funciones.php en: " . $ruta_funciones);
}

require_once $ruta_funciones;

session_start();

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['boton'])) {
    try {
        // Verificar que la función encriptar existe
        if (!function_exists('encriptar')) {
            throw new Exception("Error: La función de encriptación no está disponible");
        }

        $email = sanitizar($_POST['mail'] ?? '');
        $password = $_POST['contraseña'] ?? '';
        
        if (empty($email) || empty($password)) {
            throw new Exception("Email y contraseña son requeridos");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Formato de email inválido");
        }
        
        // Encriptar el email
        $email_encrypted = encriptar($email);
        
        // Consulta a la base de datos
        $query = "SELECT * FROM login WHERE mail = ?";
        $result = baseDatos($query, [$email_encrypted]);
        
        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['contraseña'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = desencriptar($user['nombre']);
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $user['cate'];
                $_SESSION['user_verified'] = $user['verificado'];
                
                // Redirección
                if ($_SESSION['user_role'] == 'admin') {
                    header("Location: admin.php");
                    exit();
                } elseif ($_SESSION['user_verified'] == 1) {
                    header("Location: backend.php");
                    exit();
                } else {
                    session_destroy();
                    throw new Exception("Tu cuenta requiere verificación");
                }
            } else {
                throw new Exception("Contraseña incorrecta");
            }
        } else {
            throw new Exception("Usuario no encontrado");
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>
<!-- Resto del HTML permanece igual -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ynventaris - Iniciar Sesión</title>
    <link rel="stylesheet" href="../estilos.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .error-message {
            color: #dc3545;
            padding: 10px;
            margin-bottom: 15px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button[type="submit"] {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button[type="submit"]:hover {
            background: #2980b9;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="mail">Email:</label>
                <input type="email" id="mail" name="mail" required>
            </div>
            
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>
            
            <button type="submit" name="boton">Ingresar</button>
            
            <div class="register-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
            </div>
        </form>
    </div>
</body>
</html>