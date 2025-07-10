<?php
session_start();
require_once "funciones.php";

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['boton'])) {
    try {
        $name = sanitizar($_POST['nombre'] ?? '');
        $email = sanitizar($_POST['mail'] ?? '');
        $password = $_POST['contraseña'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = sanitizar($_POST['cate'] ?? '');
        $curso = ($role == 'estu') ? sanitizar($_POST['curso'] ?? '') : null;
        
        // Validaciones
        if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
            throw new Exception("Todos los campos obligatorios son requeridos");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Formato de email inválido");
        }
        
        if ($password != $confirm_password) {
            throw new Exception("Las contraseñas no coinciden");
        }
        
        if (strlen($password) < 6) {
            throw new Exception("La contraseña debe tener al menos 6 caracteres");
        }
        
        if ($role == 'estu' && empty($curso)) {
            throw new Exception("Los estudiantes deben especificar su curso");
        }
        
        // Verificar si el email ya existe
        $email_encrypted = encriptar($email);
        $query_check = "SELECT id FROM login WHERE mail = ?";
        $result_check = baseDatos($query_check, [$email_encrypted]);
        
        if (mysqli_num_rows($result_check) > 0) {
            throw new Exception("Este email ya está registrado");
        }
        
        // Preparar datos para inserción
        $name_encrypted = encriptar($name);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $curso_encrypted = ($role == 'estu' && !empty($curso)) ? encriptar($curso) : null;
        $verified = ($role == 'profe') ? 0 : 1;
        
        // Insertar nuevo usuario
        $query = "INSERT INTO login (nombre, mail, contraseña, cate, verificado, curso) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $name_encrypted,
            $email_encrypted,
            $hashed_password,
            $role,
            $verified,
            $curso_encrypted
        ];
        
        $result = baseDatos($query, $params);
        
        if ($result !== false) {
            $success_message = "Registro exitoso. " . 
                ($role == 'profe' ? 
                "Tu cuenta de profesor está pendiente de verificación." : 
                "Ahora puedes iniciar sesión.");
        } else {
            throw new Exception("Error al registrar el usuario");
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>
<!-- Resto del HTML del formulario de registro -->