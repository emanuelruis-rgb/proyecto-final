<?php
// Esta vista muestra una tabla estática con los jugadores registrados.
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Define metadatos y estilos de la tabla de jugadores. -->
  <meta charset="UTF-8">
  <title>Jugadores - Liga Amateur</title>
  <link rel="stylesheet" href="css admin/jugadores-style.css">
</head>
<body>

<!-- Encabezado identificativo de la sección de jugadores. -->
<header>
  <img alt="logo-atrivia" src="../img/logo-atrivia.png" class="logo">
  <h1>Jugadores</h1>
</header>

<main>

  <!-- Tabla con los datos resumidos de cada jugador. -->
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Club</th>
        <th>Posición</th>
        <th>Edad</th>
        <th>Goles</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Martín Rodríguez</td>
        <td>Deportivo Norte</td>
        <td>Delantero</td>
        <td>24</td>
        <td>7</td>
      </tr>
      <tr>
        <td>Federico Suárez</td>
        <td>Atlético Sur</td>
        <td>Mediocampista</td>
        <td>27</td>
        <td>3</td>
      </tr>
      <tr>
        <td>Nicolás Fernández</td>
        <td>Los Halcones</td>
        <td>Defensor</td>
        <td>22</td>
        <td>1</td>
      </tr>
      <tr>
        <td>Diego Álvarez</td>
        <td>Unión Central</td>
        <td>Arquero</td>
        <td>29</td>
        <td>0</td>
      </tr>
    </tbody>
  </table>

</main>

</body>
</html>
