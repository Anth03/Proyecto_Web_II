<?php
session_start();
require_once("../db/connection.php");
include 'security.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['tecnico', 'administrador'])) {
    exit("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comentario_id'])) {
    $comentario_id = $_POST['comentario_id'];
    $nuevo_texto = $_POST['comentario'];

    if (!isValidInput($nuevo_texto)) {
        exit("Comentario inválido.");
    }

    if ($_SESSION['rol'] === 'tecnico') {
        $stmt_check = $conn->prepare("SELECT tecnico_id FROM comentarios WHERE id = ?");
        $stmt_check->bind_param("i", $comentario_id);
        $stmt_check->execute();
        $stmt_check->bind_result($autor);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($autor != $_SESSION['usuario_id']) {
            exit("No puedes editar este comentario.");
        }
    }

    $stmt = $conn->prepare("UPDATE comentarios SET comentario = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_texto, $comentario_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=comentario_editado");
    } else {
        echo "Error al actualizar.";
    }
    $stmt->close();
}
?>
