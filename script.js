const mainMenu = document.getElementById('mainMenu');
const toggleButton = document.getElementById('menuToggle');
const dropdownMenu = document.getElementById('dropdownMenu');

function verificarMenu() {
  const precisaDropdown = mainMenu.scrollWidth > mainMenu.clientWidth || window.innerWidth < 1100;

  if (precisaDropdown) {
    toggleButton.style.display = 'block';
    mainMenu.style.display = 'none';
  } else {
    toggleButton.style.display = 'none';
    mainMenu.style.display = 'flex';
    dropdownMenu.style.display = 'none';
  }
}

toggleButton.onclick = () => {
  const aberto = dropdownMenu.style.display === 'flex';
  dropdownMenu.style.display = aberto ? 'none' : 'flex';
};

window.onresize = verificarMenu;
window.onload = verificarMenu;
