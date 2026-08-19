<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Carga dependencias y estilos de la página principal del club. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="pagina-principal-club.css">
    <title>Página Principal</title>
</head>
<body>
    <!-- Contiene el resumen de información y las acciones disponibles. -->
    <div class="contenido-club">
        <h1>Bienvenido, Liverpool F.C.</h1>

        <!-- Muestra los indicadores principales del club. -->
        <div class="cards">

            <div class="card-info">
                <h2>Jugadores</h2>
                <p>28 registrados</p>
            </div>

            <div class="card-info">
                <h2>Posición</h2>
                <p>2° Lugar</p>
            </div>

            <div class="card-info">
                <h2>Próximo partido</h2>
                <p>Liverpool vs Nacional</p>
            </div>

            <div class="card-info">
                <h2>Lesionados</h2>
                <p>2 jugadores</p>
            </div>
        </div>

        <!-- Agrupa las acciones rápidas para consultar la información de la liga. -->
        <div class="acciones">
            <h2>Acciones rápidas</h2>

            <button>Ver Jugadores</button>
            <button>Ver Fixture</button>
            <button>Tabla de Posiciones</button>
            <button>Ver Boletines</button>
        </div>
    </div>

</body>
</html>