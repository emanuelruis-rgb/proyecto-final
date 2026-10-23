document.addEventListener("DOMContentLoaded", function () {
    const buscador = document.getElementById("buscador");
    const filas = document.querySelectorAll("tbody tr");

    buscador.addEventListener("input", function () {
        const texto = buscador.value.toLowerCase();

        filas.forEach(function (fila) {
            fila.style.display = fila.textContent.toLowerCase().includes(texto) ? "" : "none";
        });
    });
});