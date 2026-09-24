// Controla la apertura y el cierre del menú de usuario del club.
function toggleUserMenu() {
    document.getElementById('userDropdown').classList.toggle('show');
}

// Cierra el menú cuando se hace clic fuera de él.
document.addEventListener('click', function(event) {
    const menu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');

    if (menu && dropdown && !menu.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});
