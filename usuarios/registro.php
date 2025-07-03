<?php
session_start();
include("funciones.php");

// Procesar registro si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['boton'])) {
    // Obtener datos del formulario
    $nombre = sanitizar($_POST["nombre"]);
    $mail = sanitizar($_POST["mail"]);
    $contraseña = $_POST["contraseña"];
    $cate = sanitizar($_POST["cate"]);
    $curso = isset($_POST["curso"]) ? sanitizar($_POST["curso"]) : null;

    // Validar campos obligatorios
    if (empty($nombre) || empty($mail) || empty($contraseña) || empty($cate)) {
        $error_message = "Todos los campos obligatorios deben estar completos";
    }
    // Validar el mail
    else if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
        $error_message = "El formato del correo electrónico no es válido";
    }
    // Validar que si es estudiante, tenga curso
    else if($cate == 'estu' && empty($curso)) {
        $error_message = "Los estudiantes deben especificar su curso";
    }
    else {
        // Conectar a la base de datos
        $conexion = mysqli_connect("localhost", "root", "");
        if (!$conexion) {
            $error_message = "Error al conectar con la base de datos";
        } else {
            mysqli_select_db($conexion, "inventario");

            // Escapar valores para prevenir SQL injection
            $nombre = mysqli_real_escape_string($conexion, $nombre);
            $mail = mysqli_real_escape_string($conexion, $mail);
            $contraseña = mysqli_real_escape_string($conexion, $contraseña);
            $cate = mysqli_real_escape_string($conexion, $cate);
            if ($curso) {
                $curso = mysqli_real_escape_string($conexion, $curso);
            }

            // Verificar si el mail ya existe
            $consulta_check = "SELECT * FROM login WHERE mail = '$mail'";
            $resultado_check = mysqli_query($conexion, $consulta_check);

            if(mysqli_num_rows($resultado_check) > 0) {
                $error_message = "Este correo electrónico ya está registrado";
            } else {
                // Determinar valores según el tipo de usuario
                $verificado = 0; // Por defecto no verificado

                if($cate == 'profe') {
                    $verificado = 0; // Profesores necesitan verificación
                    $curso_valor = null; // Los profesores no tienen curso específico
                } else if($cate == 'estu') {
                    $verificado = 1; // Los estudiantes se verifican automáticamente
                    $curso_valor = $curso; // Guardar el curso del estudiante
                }

                // Preparar la consulta de inserción
                if ($curso_valor) {
                    $consulta = "INSERT INTO login (nombre, mail, contraseña, cate, verificado, curso) 
                                 VALUES ('$nombre', '$mail', '$contraseña', '$cate', '$verificado', '$curso_valor')";
                } else {
                    $consulta = "INSERT INTO login (nombre, mail, contraseña, cate, verificado, curso) 
                                 VALUES ('$nombre', '$mail', '$contraseña', '$cate', '$verificado', NULL)";
                }

                // Ejecutar la consulta
                $resultado = mysqli_query($conexion, $consulta);

                if ($resultado) {
                    if($cate == 'profe') {
                        $success_message = "Registro exitoso. Su cuenta de profesor será verificada por un administrador.";
                    } else {
                        $success_message = "Registro exitoso. Ahora puede iniciar sesión.";
                    }
                    $redirect_to_login = true;
                } else {
                    $error_message = "Error al registrar el usuario: " . mysqli_error($conexion);
                }
            }

            // Cerrar la conexión
            mysqli_close($conexion);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ynventaris - Registro</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../estilos.css">
    <style>
        .match-indicator {
            margin-left: 10px;
            font-weight: bold;
        }
        .match-success {
            color: green;
        }
        .match-error {
            color: red;
        }
        .error-message {
            color: red;
            display: none;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        .server-error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        .input-error {
            border: 1px solid red;
        }
        .input-success {
            border: 1px solid green;
        }
        .form-group {
            margin: 10px 0;
        }
        .hidden {
            display: none;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.6;
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
            <a href="login.php" title="Iniciar Sesión">INICIAR SESIÓN</a>
        </nav>
    </header>
    <main>
        <div class="titulo">
            <h1>Registrarse</h1>
        </div>
        
        <?php if(isset($success_message)): ?>
        <div class="success-message">
            <strong>Éxito:</strong> <?php echo htmlspecialchars($success_message); ?>
            <?php if(isset($redirect_to_login)): ?>
                <br><a href="login.php">Ir a iniciar sesión</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
        <div class="server-error-message">
            <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>
        
        <div class="formulario">
            <form method="post" onsubmit="return validarFormulario()">
                <div class="form-group">
                    <p>Nombre:
                        <input type="text" name="nombre" id="nombre" autofocus required minlength="2"
                               value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                    </p>
                </div>
                
                <div class="form-group">
                    <p>Email:
                        <input type="email" name="mail" id="email" required oninput="validarEmailEnTiempoReal()"
                               value="<?php echo isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : ''; ?>">
                    </p>
                </div>
                
                <div class="form-group">
                    <p>Confirmar Email:
                        <input type="email" name="confirm_mail" id="confirm_mail" required oninput="validarEmailEnTiempoReal()">
                        <span class="match-indicator" id="email_match_indicator"></span>
                        <div class="error-message" id="email_error">Los correos electrónicos no coinciden</div>
                    </p>
                </div>
                
                <div class="form-group">
                    <p>Contraseña:
                        <input type="password" name="contraseña" id="password" required minlength="4" oninput="validarPasswordEnTiempoReal()">
                    </p>
                </div>
                
                <div class="form-group">
                    <p>Confirmar Contraseña:
                        <input type="password" name="confirm_password" id="confirm_password" required oninput="validarPasswordEnTiempoReal()">
                        <span class="match-indicator" id="password_match_indicator"></span>
                        <div class="error-message" id="password_error">Las contraseñas no coinciden</div>
                    </p>
                </div>
                
                <div class="form-group">
                    <p>¿Qué cargo tienes?:
                        <select name="cate" id="tipoUsuario" onchange="mostrarCamposCurso()" required>
                            <option value="">Seleccione una opción</option>
                            <option value="profe" <?php echo (isset($_POST['cate']) && $_POST['cate'] == 'profe') ? 'selected' : ''; ?>>Profesor</option>
                            <option value="estu" <?php echo (isset($_POST['cate']) && $_POST['cate'] == 'estu') ? 'selected' : ''; ?>>Estudiante</option>
                        </select>
                    </p>
                </div>
                
                <div class="form-group <?php echo (!isset($_POST['cate']) || $_POST['cate'] != 'estu') ? 'hidden' : ''; ?>" id="cursoContainer">
                    <p>Curso:
                        <input type="text" id="curso" name="curso" placeholder="Ej: 4to A, 2do B, etc."
                               value="<?php echo isset($_POST['curso']) ? htmlspecialchars($_POST['curso']) : ''; ?>">
                    </p>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="boton" id="submitBtn">Registrarse</button>
                </div>
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

    <script>
        function mostrarCamposCurso() {
            const tipoUsuario = document.getElementById('tipoUsuario').value;
            const cursoContainer = document.getElementById('cursoContainer');
            const cursoInput = document.getElementById('curso');
            
            if (tipoUsuario === 'estu') {
                cursoContainer.classList.remove('hidden');
                cursoInput.required = true;
            } else {
                cursoContainer.classList.add('hidden');
                cursoInput.required = false;
                cursoInput.value = ''; // Limpiar el valor
            }
            
            actualizarEstadoBoton();
        }

        // Validar email en tiempo real
        function validarEmailEnTiempoReal() {
            const email = document.getElementById('email').value;
            const confirmEmail = document.getElementById('confirm_mail').value;
            const emailError = document.getElementById('email_error');
            const indicator = document.getElementById('email_match_indicator');
            
            // Solo validar si ambos campos tienen contenido
            if (email && confirmEmail) {
                if (email === confirmEmail) {
                    // Coinciden
                    document.getElementById('email').classList.remove('input-error');
                    document.getElementById('confirm_mail').classList.remove('input-error');
                    document.getElementById('email').classList.add('input-success');
                    document.getElementById('confirm_mail').classList.add('input-success');
                    emailError.style.display = 'none';
                    indicator.textContent = '✓';
                    indicator.className = 'match-indicator match-success';
                } else {
                    // No coinciden
                    document.getElementById('email').classList.remove('input-success');
                    document.getElementById('confirm_mail').classList.remove('input-success');
                    document.getElementById('email').classList.add('input-error');
                    document.getElementById('confirm_mail').classList.add('input-error');
                    emailError.style.display = 'block';
                    indicator.textContent = '✗';
                    indicator.className = 'match-indicator match-error';
                }
            } else {
                // Reiniciar si algún campo está vacío
                document.getElementById('email').classList.remove('input-error', 'input-success');
                document.getElementById('confirm_mail').classList.remove('input-error', 'input-success');
                emailError.style.display = 'none';
                indicator.textContent = '';
            }
            
            actualizarEstadoBoton();
        }

        // Validar contraseña en tiempo real
        function validarPasswordEnTiempoReal() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const passwordError = document.getElementById('password_error');
            const indicator = document.getElementById('password_match_indicator');
            
            // Solo validar si ambos campos tienen contenido
            if (password && confirmPassword) {
                if (password === confirmPassword) {
                    // Coinciden
                    document.getElementById('password').classList.remove('input-error');
                    document.getElementById('confirm_password').classList.remove('input-error');
                    document.getElementById('password').classList.add('input-success');
                    document.getElementById('confirm_password').classList.add('input-success');
                    passwordError.style.display = 'none';
                    indicator.textContent = '✓';
                    indicator.className = 'match-indicator match-success';
                } else {
                    // No coinciden
                    document.getElementById('password').classList.remove('input-success');
                    document.getElementById('confirm_password').classList.remove('input-success');
                    document.getElementById('password').classList.add('input-error');
                    document.getElementById('confirm_password').classList.add('input-error');
                    passwordError.style.display = 'block';
                    indicator.textContent = '✗';
                    indicator.className = 'match-indicator match-error';
                }
            } else {
                // Reiniciar si algún campo está vacío
                document.getElementById('password').classList.remove('input-error', 'input-success');
                document.getElementById('confirm_password').classList.remove('input-error', 'input-success');
                passwordError.style.display = 'none';
                indicator.textContent = '';
            }
            
            actualizarEstadoBoton();
        }

        // Actualizar estado del botón de envío
        function actualizarEstadoBoton() {
            const nombre = document.getElementById('nombre').value.trim();
            const email = document.getElementById('email').value.trim();
            const confirmEmail = document.getElementById('confirm_mail').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const tipoUsuario = document.getElementById('tipoUsuario').value;
            const curso = document.getElementById('curso').value.trim();
            const submitBtn = document.getElementById('submitBtn');
            
            // Verificar campos básicos
            let camposCompletos = nombre.length > 0 && 
                                email.length > 0 && 
                                confirmEmail.length > 0 && 
                                password.length >= 4 && 
                                confirmPassword.length >= 4 && 
                                tipoUsuario.length > 0;
            
            // Si es estudiante, verificar que tenga curso
            if (tipoUsuario === 'estu') {
                camposCompletos = camposCompletos && curso.length > 0;
            }
            
            // Verificar que los emails coincidan (solo si ambos tienen contenido)
            const emailsMatch = email.length === 0 || confirmEmail.length === 0 || email === confirmEmail;
            
            // Verificar que las contraseñas coincidan (solo si ambas tienen contenido)
            const passwordsMatch = password.length === 0 || confirmPassword.length === 0 || password === confirmPassword;
            
            const botonHabilitado = camposCompletos && emailsMatch && passwordsMatch;
            
            submitBtn.disabled = !botonHabilitado;
        }

        // Validación final al enviar el formulario
        function validarFormulario() {
            const email = document.getElementById('email').value;
            const confirmEmail = document.getElementById('confirm_mail').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const tipoUsuario = document.getElementById('tipoUsuario').value;
            const curso = document.getElementById('curso').value.trim();
            
            if (email !== confirmEmail) {
                alert('Los correos electrónicos no coinciden');
                return false;
            }
            
            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (password.length < 4) {
                alert('La contraseña debe tener al menos 4 caracteres');
                return false;
            }
            
            if (tipoUsuario === 'estu' && !curso) {
                alert('Los estudiantes deben especificar su curso');
                return false;
            }
            
            return true;
        }

        // Agregar event listeners cuando la página cargue
        document.addEventListener('DOMContentLoaded', function() {
            // Mantener la visibilidad del campo curso si se seleccionó estudiante
            <?php if(isset($_POST['cate']) && $_POST['cate'] == 'estu'): ?>
            mostrarCamposCurso();
            <?php endif; ?>
            
            // Obtener todos los elementos
            const nombre = document.getElementById('nombre');
            const email = document.getElementById('email');
            const confirmEmail = document.getElementById('confirm_mail');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const tipoUsuario = document.getElementById('tipoUsuario');
            const curso = document.getElementById('curso');
            
            // Agregar listeners a todos los campos
            if (nombre) {
                nombre.addEventListener('input', actualizarEstadoBoton);
                nombre.addEventListener('keyup', actualizarEstadoBoton);
            }
            
            if (email) {
                email.addEventListener('input', function() {
                    validarEmailEnTiempoReal();
                    actualizarEstadoBoton();
                });
            }
            
            if (confirmEmail) {
                confirmEmail.addEventListener('input', function() {
                    validarEmailEnTiempoReal();
                    actualizarEstadoBoton();
                });
            }
            
            if (password) {
                password.addEventListener('input', function() {
                    validarPasswordEnTiempoReal();
                    actualizarEstadoBoton();
                });
            }
            
            if (confirmPassword) {
                confirmPassword.addEventListener('input', function() {
                    validarPasswordEnTiempoReal();
                    actualizarEstadoBoton();
                });
            }
            
            if (tipoUsuario) {
                tipoUsuario.addEventListener('change', function() {
                    mostrarCamposCurso();
                    actualizarEstadoBoton();
                });
            }
            
            if (curso) {
                curso.addEventListener('input', actualizarEstadoBoton);
                curso.addEventListener('keyup', actualizarEstadoBoton);
            }
            
            // Estado inicial
            setTimeout(function() {
                actualizarEstadoBoton();
            }, 100);
        });
    </script>
</body>
</html>