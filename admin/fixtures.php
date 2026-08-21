<?php
// Esta vista presenta los partidos agrupados por fecha de jornada.
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Define metadatos y estilos propios de la tabla de fixtures. -->
  <meta charset="UTF-8">
  <title>Fixtures</title>
  <link rel="stylesheet" href="css admin/fixtures-style.css">
</head>
<body>

<!-- Encabezado identificativo de la sección de fixtures. -->
<header>
  <img alt="logo-atrivia" src="../img/logo-atrivia.png" class="logo">
  <h1>Fixtures</h1>
</header>

<main>

  <!-- Primera jornada con partidos ya finalizados. -->
  <h2 class="jornada">Fecha 1</h2>
  <div class="partidos">
    <div class="partido">
      <div class="equipo local">Deportivo Norte</div>
      <div class="centro">
        <div class="resultado">2 - 1</div>
      </div>
      <div class="equipo visita">Atlético Sur</div>
    </div>
    <div class="partido">
      <div class="equipo local">Los Halcones</div>
      <div class="centro">
        <div class="resultado">0 - 0</div>
      </div>
      <div class="equipo visita">Unión Central</div>
    </div>
  </div>

  <!-- Segunda jornada con partidos programados. -->
  <h2 class="jornada">Fecha 2</h2>
  <div class="partidos">
    <div class="partido">
      <div class="equipo local">Atlético Sur</div>
      <div class="centro">
        <div class="hora">Sáb 20:00</div>
      </div>
      <div class="equipo visita">Los Halcones</div>
    </div>
    <div class="partido">
      <div class="equipo local">Unión Central</div>
      <div class="centro">
        <div class="hora">Dom 18:30</div>
      </div>
      <div class="equipo visita">Deportivo Norte</div>
    </div>
  </div>

</main>

<footer>
  <!-- Identifica la aplicación y el año de la vista. -->
  Atrivia © 2026
</footer>

</body>
</html>