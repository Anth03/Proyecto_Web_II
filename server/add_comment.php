<?php
session_start();
require_once("../db/connection.php");
include 'security.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['tecnico', 'administrador'])) {
    echo "Acceso denegado.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tecnico_id = $_SESSION['usuario_id'];
    $comentario = $_POST['comment_text'];
    $detalle = $_POST['comment_description'];
    $ticket_id = $_POST['ticket_id'];

    if (
        isValidInput($comentario) &&
        isValidInput($detalle) &&
        isNumeric($ticket_id)
    ) {
        $stmt = $conn->prepare("INSERT INTO comentarios (ticket_id, tecnico_id, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $ticket_id, $tecnico_id, $comentario);

        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=comentario_agregado");
        } else {
            echo "Error al guardar el comentario.";
        }

        $stmt->close();
    } else {
        echo "Datos inválidos.";
    }
}
?>
