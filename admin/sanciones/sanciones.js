// desde sanciones.php se manda acá: select-club (id del club seleccionado) y, opcionalmente,
// select-categoria. Con eso se buscan en la bd los jugadores y se muestran en el select de sanciones.php

const selectClub = document.getElementById("select-club");
const selectCategoria = document.getElementById("select-categoria"); // no existe en editar-sancion.php
const selectJugador = document.getElementById("select-jugador");

let jugadorInicial = selectJugador.dataset.jugadorInicial; //guarda el jugador seleccionado inicialmente

function cargarJugadores() { //funcion para cargar jugadores
    if (!selectClub.value) return; //comprueba si hay un club seleccionado

    let url = "buscar-jugadores-sancion.php?idClub=" + selectClub.value;
    if (selectCategoria && selectCategoria.value) {
        url += "&idCategoria=" + selectCategoria.value; //si hay categoría elegida, filtra también por ella
    }

    fetch(url) //solicita los jugadores al archivo php
        .then(function (respuesta) {
            if (!respuesta.ok) {
                throw new Error("No se pudieron cargar los jugadores");
            }
            return respuesta.text();
        })
        .then(function (datos) {
            selectJugador.innerHTML = datos;

            if (jugadorInicial) {
                selectJugador.value = jugadorInicial;
                jugadorInicial = ""; //solo en la primera carga, para no pisar lo que elija el admin después
            }
        })
        .catch(function (error) {
            console.error(error);
        });
}
//cuando se cambia el club o la categoría, se cargan los jugadores
selectClub.addEventListener("change", cargarJugadores);
if (selectCategoria) {
    selectCategoria.addEventListener("change", cargarJugadores);
}
//si hay un club seleccionado al cargar la página, se cargan los jugadores del club
if (selectClub.value) {
    cargarJugadores();
}