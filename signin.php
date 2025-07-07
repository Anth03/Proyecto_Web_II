<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - HelpDesk360</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<nav>
  <ul>
    <li><a href="index.html">Inicio</a></li>
  </ul>
</nav>

<form method="POST" action="server/authenticate.php">
    <h1>Inicio de Sesión</h1>

    <?php
    session_start();
    if (isset($_SESSION['errorUs_message'])) {
        echo '<p id="error">' . $_SESSION['errorUs_message'] . '</p>';
        unset($_SESSION['errorUs_message']);
    }
    ?>

    <label for="username">Nombre de usuario:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Ingresar</button>
</form>
</body>
</html>
