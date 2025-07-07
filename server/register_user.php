<?php
session_start();
require_once("../db/connection.php");
include 'security.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $rol = $_POST['role'];

    if (
        isValidInput($username) &&
        isValidInput($password) &&
        in_array($rol, ['administrador', 'tecnico', 'cliente'])
    ) {
        // Verificar si el nombre de usuario ya existe
        $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE nombre = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            echo "El nombre de usuario ya está registrado.";
            $stmt_check->close();
            exit();
        }
        $stmt_check->close();

        // Insertar usuario sin encriptación (según tu decisión actual)
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, contrasena, rol) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $rol);

        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=usuario_registrado");
        } else {
            echo "Error al registrar el usuario.";
        }

        $stmt->close();
    } else {
        echo "Datos inválidos. Verifica los campos.";
    }
}
?>
