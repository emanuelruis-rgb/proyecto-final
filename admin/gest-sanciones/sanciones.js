// desde sanciones.php se manda acá: select-club (id del club seleccionado)
// para poder buscar en la bd, en funcion de ese id club, a los jugadores del club, y mostrarlos 
//en el select de sanciones.php

const selectClub = document.getElementById("select-club");
const selectJugador = document.getElementById("select-jugador");

const jugadorInicial = selectJugador.dataset.jugadorInicial; //guarda el jugador seleccionado inicialmente

function cargarJugadores() { //funcion para cargar jugadores
    if (!selectClub.value) return; //comprueba si hay un club seleccionado

    fetch("buscar-jugadores-sancion.php?idClub=" + selectClub.value) //solicita los jugadores al archivo php
        //comprueba si la solicitud funcionó
        .then(function (respuesta) {
            if (!respuesta.ok) {
                throw new Error("No se pudieron cargar los jugadores");
            }
            return respuesta.text();
        })
        //muestra los jugadores en el select de jugadores
        .then(function (datos) {
            selectJugador.innerHTML = datos;

            if (jugadorInicial) {
                selectJugador.value = jugadorInicial;
            }
        })
        //si ocurre un error, lo muestra en la consola
        .catch(function (error) {
            console.error(error);
        });
}
//cuando se cambia el club, se cargan los jugadores del club seleccionado
selectClub.addEventListener("change", cargarJugadores);
//si hay un club seleccionado al cargar la página, se cargan los jugadores del club
if (selectClub.value) {
    cargarJugadores();
}