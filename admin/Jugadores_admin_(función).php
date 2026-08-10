<?php
$conexion = new mysqli("localhost", "root", "", "liga");

$jugadores = $conexion->query("SELECT j.idjugador, j.nombre, j.apellido, 
    j.fecha_nacimiento, j.categoria, c.nombre AS club
    FROM jugador j JOIN club c ON j.idclub = c.idclub");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Jugadores</title>
<style>
  body { background:linear-gradient(to left, #50c8ec, #0f0f57); font-family:Arial, sans-serif; padding:30px; }
  table { width:100%; max-width:800px; margin:auto; border-collapse:collapse; color:red; font-size:14px; }
  th { background:#3a3a55; color:white; padding:10px; text-align:left; }
  td { padding:8px 10px; border-bottom:1px solid #4577d3; }
  tr:hover { background:#2a2a40; }
</style>
</head>
<body>

<table>
  <tr>
    <th>Nombre</th><th>Apellido</th><th>Club</th><th>Fecha Nac.</th><th>Categoría</th>
  </tr>
  <?php while ($j = $jugadores->fetch_assoc()): ?>
  <tr>
    <td><?= $j['nombre'] ?></td>
    <td><?= $j['apellido'] ?></td>
    <td><?= $j['club'] ?></td>
    <td><?= $j['fecha_nacimiento'] ?></td>
    <td><?= $j['categoria'] ?></td>
  </tr>
  <?php endwhile; ?>
</table>

</body>
</html>

