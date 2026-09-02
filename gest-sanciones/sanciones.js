// desde sanciones.php se manda acá: select-club (id del club seleccionado)
// para poder buscar en la bd, en funcion de ese id club, a los jugadores del club, y mostrarlos 
//en el select de sanciones.php

const selectClub = document.getElementById("select-club");
const selectJugador = document.getElementById("select-jugador");

selectClub.addEventListener("change", function() {
    console.log("Cambió el club");
    console.log(selectClub.value);

    fetch("buscar-jugadores-sancion.php?idClub=" + selectClub.value)
    .then(function(respuesta) {
        return respuesta.text();
    })
    .then(function(datos) {
        selectJugador.innerHTML = datos;
    });
});