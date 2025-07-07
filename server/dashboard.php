<?php
session_start();

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header("Location: ../index.html");
    exit();
}

$rol = $_SESSION['rol'] ?? '';
$isAdmin = $rol === 'administrador';
$isTecnico = $rol === 'tecnico';
$isCliente = $rol === 'cliente';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - HelpDesk360</title>
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>

<nav>
  <ul>
    <li><button onclick="mostrarSeccion('citas')">TICKETS</button></li>
    <?php if ($isAdmin || $isTecnico): ?>
      <li><button onclick="mostrarSeccion('comentarios')">COMENTARIOS</button></li>
    <?php endif; ?>
    <?php if ($isAdmin): ?>
      <li><button onclick="mostrarSeccion('usuarios')">REGISTRAR USUARIOS</button></li>
    <?php endif; ?>
    <li><label id="bien"></label></li>
    <a id="cerrar" href="logout.php"><button>Cerrar Sesión</button></a>
  </ul>
</nav>

<div id="citas" class="seccion" style="display: none;">
  <div id="mensajeErrorCitas" class="error"></div>
  <div id="mensajeExitoCitas" class="success"></div>
  <h2>TICKETS</h2>

  <?php if ($isCliente): ?>
    <form action="add_ticket.php" method="POST">
      <label>Cliente:</label> <strong><?= htmlspecialchars($_SESSION['username']) ?></strong><br>
      <input type="hidden" name="cliente" value="<?= htmlspecialchars($_SESSION['username']) ?>">

      <label for="tecnico_id">Asignar a Técnico:</label>
      <select name="tecnico_id" required>
        <?php
        require_once("../db/connection.php");
        $tecnicos = $conn->query("SELECT id, nombre FROM usuarios WHERE rol = 'tecnico'");
        while ($row = $tecnicos->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
        }
        ?>
      </select><br>

      <label for="categoria">Categoría:</label>
      <input type="text" id="categoria" name="categoria" required><br>

      <label for="fecha">Fecha:</label>
      <input type="date" id="fecha" name="fecha" required><br>

      <label for="hora">Hora:</label>
      <input type="time" id="hora" name="hora" required><br>

      <label for="descripcion">Descripción:</label>
      <textarea name="descripcion" id="descripcion" required></textarea><br>

      <button type="submit">Agendar</button>
    </form>
  <?php endif; ?>

  <table id="tablaCitas">
    <tr>
      <th>Cliente</th>
      <th>Fecha</th>
      <th>Hora</th>
      <?php if ($isAdmin): ?>
        <th>Editar</th>
        <th>Eliminar</th>
      <?php endif; ?>
      <?php if ($isAdmin || $isTecnico): ?>
        <th>Completar</th>
      <?php endif; ?>
    </tr>
  </table>
</div>

<div id="comentarios" class="seccion" style="display: none;">
  <div id="mensajeErrorComentarios" class="error"></div>
  <div id="mensajeExitoComentarios" class="success"></div>
  <h2>COMENTARIOS</h2>

  <?php if ($isAdmin || $isTecnico): ?>
    <form action="add_comment.php" method="POST">
      <label for="comment_text">Comentario:</label>
      <input type="text" id="comment_text" name="comment_text" required><br>
      <label for="comment_description">Detalle:</label>
      <textarea id="comment_description" name="comment_description" required></textarea><br>
      <label for="ticket_id">Relacionado a Ticket ID:</label>
      <input type="number" id="ticket_id" name="ticket_id" required><br>
      <button type="submit">Agregar</button>
    </form>
  <?php endif; ?>

  <table id="tablaComentarios">
    <tr>
      <th>Comentario</th>
      <th>Ticket ID</th>
      <th>Detalle</th>
      <th>Editar</th>
      <th>Eliminar</th>
    </tr>
  </table>
</div>

<div id="usuarios" class="seccion" style="display: none;">
  <h2>REGISTRAR USUARIO</h2>
  <form action="register_user.php" method="POST">
    <label for="username">Nombre de usuario:</label>
    <input type="text" id="username" name="username" required><br>
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required><br>
    <label for="role">Rol:</label>
    <select id="role" name="role" required>
      <option value="administrador">Administrador</option>
      <option value="tecnico">Técnico</option>
      <option value="cliente">Cliente</option>
    </select><br>
    <button type="submit">Registrar</button>
  </form>
</div>

<script>
const rolUsuario = "<?= $_SESSION['rol'] ?>";

window.addEventListener('DOMContentLoaded', () => {
  mostrarSeccion('citas');
});

function mostrarSeccion(id) {
  document.querySelectorAll('.seccion').forEach(sec => sec.style.display = 'none');
  const sec = document.getElementById(id);
  if (sec) sec.style.display = 'block';

  if (id === 'citas') cargarTickets();
  if (id === 'comentarios') cargarComentarios();
}

function cargarTickets() {
  fetch('get_ticket.php')
    .then(res => res.json())
    .then(data => {
      const tabla = document.getElementById("tablaCitas");
      let columnas = `<tr><th>Cliente</th><th>Fecha</th><th>Hora</th>`;
      if (rolUsuario === 'administrador') {
        columnas += `<th>Editar</th><th>Eliminar</th>`;
      }
      if (rolUsuario === 'administrador' || rolUsuario === 'tecnico') {
        columnas += `<th>Completar</th>`;
      }
      columnas += `</tr>`;
      tabla.innerHTML = columnas;

      data.forEach(t => {
        let fila = `<td>${t.user_name}</td><td>${t.fecha}</td><td>${t.hora}</td>`;
        if (rolUsuario === 'administrador') {
          fila += `<td><a href="edit_ticket.php?id=${t.id}"><button>Editar</button></a></td>
                    <td><a href="delete_ticket.php?id=${t.id}"><button>Eliminar</button></a></td>`;
        }
        if (rolUsuario === 'administrador' || rolUsuario === 'tecnico') {
          fila += `<td><button onclick="completarTicket(${t.id})">Completar</button></td>`;
        }
        tabla.innerHTML += `<tr>${fila}</tr>`;
      });
    });
}

function cargarComentarios() {
  fetch('get_comment.php')
    .then(res => res.json())
    .then(data => {
      const tabla = document.getElementById("tablaComentarios");
      tabla.innerHTML = `<tr>
        <th>Comentario</th><th>Ticket ID</th><th>Detalle</th>
        <th>Editar</th><th>Eliminar</th></tr>`;
      data.forEach(c => {
        tabla.innerHTML += `
          <tr>
            <td>${c.comment_text}</td>
            <td>${c.ticket_id}</td>
            <td>${c.comment_description}</td>
            <td><a href="edit_comment.php?id=${c.id}"><button>Editar</button></a></td>
            <td><a href="delete_comment.php?id=${c.id}"><button>Eliminar</button></a></td>
          </tr>`;
      });
    });
}

function completarTicket(id) {
  if (confirm("¿Completar y eliminar este ticket?")) {
    fetch('delete_ticket.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(result => {
      if (result.status === 'success') {
        alert('Ticket completado.');
        cargarTickets();
      } else {
        alert('Error: ' + result.message);
      }
    });
  }
}
</script>
</body>
</html>
