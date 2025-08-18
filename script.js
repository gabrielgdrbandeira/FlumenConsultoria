document.addEventListener('DOMContentLoaded', () => {
  /** MENU MOBILE **/
  const hamburger = document.querySelector('.hamburger');
  const navMenu   = document.querySelector('.nav-menu');

  hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
  });

  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      hamburger.classList.remove('active');
      navMenu.classList.remove('active');
    });
  });

  /** CARROSSEL SWIPER (apenas se existir no HTML) **/
  const servicosSwiper = document.querySelector('.servicesSwiper');
  if (servicosSwiper) {
    new Swiper(".servicesSwiper", {
      loop: true,
      slidesPerView: 1,
      spaceBetween: 30,
      autoHeight: true,              // ⭐ faz o swiper acompanhar a altura de cada slide
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      }
    });
  }
});