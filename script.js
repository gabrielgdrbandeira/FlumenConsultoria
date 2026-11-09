document.addEventListener('DOMContentLoaded', () => {
  /** MENU MOBILE **/
  const hamburger = document.querySelector('.hamburger');
  const navMenu   = document.querySelector('.nav-menu');

  hamburger.addEventListener('click', () => {
    const isActive = hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
    hamburger.setAttribute('aria-expanded', isActive);
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
    const totalSlides = servicosSwiper.querySelectorAll('.swiper-slide').length;
    // Loop precisa de pelo menos o dobro de slides do slidesPerView para funcionar corretamente
    const enableLoop = totalSlides >= 5;
    
    new Swiper(".servicesSwiper", {
      loop: true,
      slidesPerView: 3,
      slidesPerGroup: 1,              // Troca apenas um card por vez
      spaceBetween: 30,
      watchSlidesProgress: true,
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

document.addEventListener("DOMContentLoaded", function() {
  // 1. Procura a div que criamos no HTML
  const containerNoticias = document.getElementById('lista-noticias');

  // 2. Só executa o código se a div existir nesta página (evita erros em contato.html, por exemplo)
  if (containerNoticias) {
      let htmlNoticias = '';

      // 3. Faz um loop por cada notícia dentro do seu banco
      for (const id in bancoDeNoticias) {
          const noticia = bancoDeNoticias[id];

          // 4. Truque para extrair só o texto do primeiro parágrafo para usar como resumo
          const divTemporaria = document.createElement('div');
          divTemporaria.innerHTML = noticia.texto;
          let resumo = divTemporaria.querySelector('p').innerText;

          // Corta o resumo se tiver mais de 130 caracteres e adiciona "..."
          if (resumo.length > 130) {
              resumo = resumo.substring(0, 130) + '...';
          }

          // 5. Monta o HTML dinamicamente substituindo os dados (crase ` permite quebrar linha)
          htmlNoticias += `
              <article class="news-card big-card" style="background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column;">
                  <div class="news-img" style="height: 220px;">
                      <img src="${noticia.imagem}" alt="${noticia.titulo}" style="width: 100%; height: 100%; object-fit: cover;">
                  </div>
                  <div class="news-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                      <span class="news-date" style="font-size: 0.8rem; color: #888; margin-bottom: 10px;">${noticia.data}</span>
                      <h3 style="font-size: 1.1rem; color: var(--cor-azul-escuro); margin-bottom: 10px;">${noticia.titulo}</h3>
                      <p class="news-excerpt" style="font-size: 0.9rem; color: #666; margin-bottom: 20px; line-height: 1.5;">
                          ${resumo}
                      </p>
                      <a href="#" onclick="abrirModal(${id}); return false;" class="read-more" style="color: var(--cor-verde-destaque); font-weight: bold; text-decoration: none; margin-top: auto;">Ler matéria completa &rarr;</a>
                  </div>
              </article>
          `;
      }

      // 6. Injeta todo o HTML gerado de uma só vez dentro da sua div vazia
      containerNoticias.innerHTML = htmlNoticias;
  }
});