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
      slidesPerView: 3,
      slidesPerGroup: 1,              // Troca apenas um card por vez
      spaceBetween: 30,
      autoplay: {
        delay: 3000,                  // Muda automaticamente a cada 3 segundos
        disableOnInteraction: false,  // Continua mesmo após interação do usuário
        pauseOnMouseEnter: true,      // Pausa quando o mouse está sobre o carrossel
      },
      autoHeight: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      breakpoints: {
        // Quando a largura é >= 320px (mobile)
        320: {
          slidesPerView: 1,
          spaceBetween: 20,
        },
        // Quando a largura é >= 768px (tablet)
        768: {
          slidesPerView: 2,
          spaceBetween: 25,
        },
        // Quando a largura é >= 1024px (desktop)
        1024: {
          slidesPerView: 3,
          spaceBetween: 30,
        },
      }
    });
  }
});