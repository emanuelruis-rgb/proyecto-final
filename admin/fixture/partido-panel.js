document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".panel-partido").forEach(function (panel) {
        const estado = panel.querySelector(".sel-estado");
        const marcador = panel.querySelector(".bloque-marcador");

        if (!estado || !marcador) {
            return;
        }

        function actualizarMarcador() {
            marcador.hidden = estado.value !== "jugado";
        }

        estado.addEventListener("change", actualizarMarcador);
        actualizarMarcador();
    });
});