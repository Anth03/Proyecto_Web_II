<?php
session_start();
require_once("../db/connection.php");
require_once("security.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) exit();

    // Validar entrada
    if (!isValidInput($username) || !isValidInput($password)) {
        $_SESSION['errorUs_message'] = 'Nombre de usuario o contraseña inválidos.';
        header("Location: ../signin.php");
        exit();
    }

    $query = $conn->prepare("SELECT * FROM usuarios WHERE nombre = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Comparar contraseña directamente (sin hash)
        if ($password === $user['contrasena']) {
            $_SESSION["authenticated"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["rol"] = $user['rol'];
            $_SESSION["usuario_id"] = $user['id'];

            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['errorUs_message'] = 'Contraseña incorrecta.';
            header("Location: ../signin.php");
            exit();
        }
    } else {
        $_SESSION['errorUs_message'] = 'Usuario no encontrado.';
        header("Location: ../signin.php");
        exit();
    }
} else {
    header("Location: ../signin.php");
    exit();
}
