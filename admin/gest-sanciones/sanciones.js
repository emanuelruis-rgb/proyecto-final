// desde sanciones.php se manda acá: select-club (id del club seleccionado)
// para poder buscar en la bd, en funcion de ese id club, a los jugadores del club, y mostrarlos 
//en el select de sanciones.php

const selectClub = document.getElementById("select-club");
const selectJugador = document.getElementById("select-jugador");

selectClub.addEventListener("change", function() {
    console.log("Cambió el club");
    console.log(selectClub.value);

    //cada vez que js detecta un cambio de club, ejecuta este fetch,
    //que usa buscar-jugadores-sancion.php con el id club apropiado para que el mismo retorne
    //los jugadores de ese club.
    fetch("buscar-jugadores-sancion.php?idClub=" + selectClub.value)
    .then(function(respuesta) {
        return respuesta.text();
    })
    // aca el js reemplaza, con los datos recibidos de buscar jugadores sancion, los option de select jugador
    .then(function(datos) {
        selectJugador.innerHTML = datos;
    });
});