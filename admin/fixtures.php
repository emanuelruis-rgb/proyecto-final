<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Fixtures</title>
<style>
  :root {
    --azul: #14243f;
    --azul-claro: #1f3a63;
    --fondo: #eef1f4;
    --texto: #14243f;
    --gris: #6b7280;
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    font-family: Georgia, 'Times New Roman', serif;
    background: var(--fondo);
    color: var(--texto);
  }

  header {
    background: var(--azul);
    color: #fff;
    padding: 22px 30px;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  header .logo {
    width: 40px;
    height: 40px;
    border: 2px solid #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
  }

  header h1 {
    font-size: 22px;
    margin: 0;
    font-weight: normal;
  }

  main {
    max-width: 800px;
    margin: 40px auto;
    padding: 0 20px;
  }

  h2.jornada {
    background: var(--azul-claro);
    color: #fff;
    padding: 10px 16px;
    border-radius: 6px 6px 0 0;
    font-size: 17px;
    font-weight: normal;
    margin: 30px 0 0 0;
  }

  .partidos {
    background: #fff;
    border-radius: 0 0 6px 6px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    overflow: hidden;
  }

  .partido {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
  }

  .partido:last-child {
    border-bottom: none;
  }

  .equipo {
    flex: 1;
    font-size: 16px;
  }

  .equipo.local { text-align: right; }
  .equipo.visita { text-align: left; }

  .centro {
    width: 90px;
    text-align: center;
    flex-shrink: 0;
    padding: 0 10px;
  }

  .resultado {
    font-size: 18px;
    font-weight: bold;
    color: var(--azul);
  }

  .hora {
    font-size: 13px;
    color: var(--gris);
  }

  footer {
    text-align: center;
    padding: 20px;
    color: var(--gris);
    font-size: 13px;
  }
</style>
</head>
<body>

<header>
  <div class="logo">⚽</div>
  <h1>Atrivia — Fixtures</h1>
</header>

<main>

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
  Atrivia © 2026
</footer>

</body>
</html>