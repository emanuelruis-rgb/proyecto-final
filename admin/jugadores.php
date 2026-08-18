<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Jugadores - Liga Amateur</title>
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
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  }

  thead {
    background: var(--azul-claro);
  }

  th {
    color: #fff;
    text-align: left;
    padding: 12px 16px;
    font-weight: normal;
    font-size: 15px;
  }

  td {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 15px;
  }

  tr:last-child td {
    border-bottom: none;
  }

  tr:hover td {
    background: #f4f6f8;
  }

  .club {
    color: var(--gris);
    font-size: 13px;
  }

</style>
</head>
<body>

<header>
  <div class="logo">⚽</div>
  <h1>Atrivia — Jugadores</h1>
</header>

<main>

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
