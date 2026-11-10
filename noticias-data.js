/* ==========================================================================
   BANCO DE NOTÍCIAS DA FLUMEN
   Único lugar onde as notícias são cadastradas. Ele alimenta:
   - noticias.html (todas as notícias, com a janela "Ler matéria completa")
   - index.html (as 5 mais recentes, na seção "Últimas Notícias")

   PARA ADICIONAR UMA NOTÍCIA: copie o último bloco, use o PRÓXIMO número
   (ex.: 12) e preencha os campos. Maior número = notícia mais recente.
     imagem:       caminho da imagem em imagens/
     data:         "3 de setembro de 2026 • Leitura: 2 min"
     titulo:       título completo
     tituloCurto:  (opcional) título menor para o card da página inicial
     texto:        HTML; o primeiro <p> vira o resumo do card
   ========================================================================== */
const bancoDeNoticias = {
            
            1: {
                imagem: "imagens/Imagem_Rio_NovaLei.png",
                data: "Abril de 2026 • Leitura: 2 min e 30 seg",
                titulo: "O que muda na lista de peixes e invertebrados aquáticos ameaçados",
                texto: `
                <h3>O que é a Portaria 1.667?</h3>
                <p>A Portaria GM/MMA N° 1.667, de 27 de abril de 2026, reconhece formalmente as espécies de peixes e invertebrados aquáticos da fauna brasileira que se encontram ameaçadas de extinção, atualizando a Lista Nacional Oficial de Espécies da Fauna Ameaçadas de Extinção — Peixes e Invertebrados Aquáticos.</p>
                <p>Trata-se de um documento de referência obrigatória em estudos ambientais, EIAs, RIMAs e projetos de conservação que envolvam ambientes aquáticos.</p>
                <p>2014 — Publicação da lista anterior, primeira compilação abrangente de espécies aquáticas ameaçadas.</p>
                <p>2022 — Atualização parcial da lista, com revisão de categorias de risco de algumas espécies.</p>
                <p>Abril 2026 — Publicação da Portaria 1.667, que substitui integralmente a versão anterior.</p>
                <h3>Por que essa atualização importa?</h3>
                <p>A lista oficial é o instrumento que determina quais espécies recebem proteção legal no Brasil. Alterações em sua composição têm impacto direto em:</p>
                <h3>Categorias de ameaça utilizadas</h3>
                <p>A portaria segue os critérios da IUCN (União Internacional para a Conservação da Natureza), adotados pelo ICMBio, distribuindo as espécies nas seguintes categorias:</p>
                <ul>
                    <li>EX — Extinta</li>
                    <li>EW — Extinta na Natureza</li>
                    <li>CR — Criticamente em Perigo</li>
                    <li>EN — Em Perigo</li>
                    <li>VU — Vulnerável</li>
                </ul>
                <h3>Alterações na forma de trabalho</h3>
                <p>A Flumen Consultoria está acompanhando de perto as implicações da Portaria 1.667/2026 e pode apoiar sua empresa na adequação de estudos ambientais, diagnósticos de fauna aquática e revisão de programas de monitoramento.</p>
                `.trim(),
            },

            2: {
            imagem: "imagens/Retrospectiva15Anos.JPG",
            data: "Julho de 2026 • Leitura: 1 min e 30 seg",
            titulo: "Retrospectiva: 15 Anos de Flumen",
            texto: `
            <h3>Encontros Institucionais com Empresas Parceiras</h3>
            <p>Priorizamos a parceria com nossos clientes e o objetivo não é apenas prestar serviços de consultoria, mas também, atender e fidelizar todos os clientes garantindo a sua satisfaçãol. Por meio de encontros institucionais, buscamos soluções conjuntas em prol do meio ambiente, além de cultivar a curiosidade e o aprendizado contínuo com as novas gerações.</p>
            
            <h3>Congresso em Ilhéus</h3>
            <p>Em 2024, marcamos presença no Simpósio de Biodiversidade Aquática da Mata Atlântica (SIMBAMA), em Ilhéus (BA). Entendemos a importância vital de estar na linha de frente das discussões em eventos de grande relevância sobre a biodiversidade aquática e a conservação da Mata Atlântica.</p>
            
            <h3>Atualização e Nivelamento da Equipe</h3>
            <p>Para assegurar uma entrega de qualidade aos nossos clientes, nossa equipe participa constantemente de cursos e treinamentos. Manter nosso conhecimento atualizado é o que garante o padrão de excelência nos serviços que prestamos, refletindo o compromisso de cada um de nossos profissionais.</p>
            
            <h3>Parceria CRBio</h3>
            <p>Tivemos a honra de participar como palestrantes no 1º Congresso de Biólogos da 4ª Região, realizado em Belo Horizonte (MG) no ano de 2025, reforçando nosso compromisso ético e técnico com o desenvolvimento científico.</p>
            `.trim(),
        },


            3: {
            imagem: "imagens/noticia-limnologia.jpg",
            data: "24 de julho de 2026 • Leitura: 2 min",
            titulo: "Flumen participa do X Congresso Brasileiro de Limnologia, em Juiz de Fora",
            tituloCurto: "Flumen participa do X Congresso Brasileiro de Limnologia",
            texto: `
            <p>A Flumen participou do X Congresso Brasileiro de Limnologia, realizado entre os dias 20 e 24 de julho de 2026, em Juiz de Fora, Minas Gerais. A empresa foi representada por Sandra Francischetti Rocha, Gerente Técnica, e por Manoela Brini, Gerente de Qualidade.</p>
            <p>Com o tema “Água: Ciência, Gestão e Política”, o congresso colocou em pauta questões fundamentais para a compreensão, a conservação e a administração dos ecossistemas aquáticos continentais.</p>
            <h3>Conhecimento que se constrói em conjunto</h3>
            <p>Eventos como esse reúnem pesquisadores, profissionais e instituições que compartilham experiências, resultados de estudos e conhecimentos sobre a limnologia. São espaços valiosos de atualização profissional, de contato com novas pesquisas e de aproximação com outros profissionais da área ambiental.</p>
            <p>A integração entre ciência, gestão ambiental e políticas públicas é essencial para a conservação e o uso sustentável dos recursos hídricos, e discutir esses temas em conjunto fortalece o trabalho de quem atua na área.</p>
            <h3>Um compromisso da Flumen</h3>
            <p>Para a Flumen, participar de eventos científicos faz parte do compromisso com o conhecimento, com a atualização técnica e com o desenvolvimento da ciência voltada aos ambientes aquáticos. É assim que seguimos aprendendo, trocando experiências e aprimorando a qualidade dos trabalhos que desenvolvemos.</p>
            `.trim(),
        },

        4: {
            imagem: "imagens/noticia-dia-do-biologo.JPG",
            data: "3 de setembro de 2026 • Leitura: 2 min",
            titulo: "Dia do Biólogo: ciência que transforma, conhecimento que preserva",
            texto: `
            <p>No dia 3 de setembro, a Flumen celebra o Dia do Biólogo e presta sua homenagem aos profissionais que dedicam a carreira a compreender a vida e a proteger o meio ambiente. Biólogos: ciência que transforma, conhecimento que preserva.</p>
            <p>É por meio do trabalho desses profissionais que conhecemos o funcionamento dos ecossistemas, identificamos alterações ambientais e construímos caminhos para a conservação da biodiversidade.</p>
            <h3>O que fazem os biólogos</h3>
            <p>A atuação é ampla e, muitas vezes, começa em campo, em ambientes aquáticos, com observação atenta e muitas anotações. Entre as atividades estão:</p>
            <ul style="margin: 0 0 20px 20px; line-height: 1.8;">
                <li>levantamentos e monitoramentos da biodiversidade;</li>
                <li>identificação e caracterização de espécies;</li>
                <li>avaliação das condições ambientais dos ecossistemas;</li>
                <li>coleta de dados em campo;</li>
                <li>estudos sobre comunidades aquáticas e terrestres;</li>
                <li>análise de impactos ambientais;</li>
                <li>produção de informações técnicas e científicas para subsidiar decisões relacionadas à conservação ambiental.</li>
            </ul>
            <h3>A biologia na rotina da Flumen</h3>
            <p>Os ecossistemas aquáticos continentais estão no centro do trabalho da Flumen, e é a atuação técnica e científica dos biólogos que permite compreender esses ambientes, identificar mudanças e orientar estratégias de conservação.</p>
            <p>Feliz Dia do Biólogo a toda a nossa equipe e a todos os profissionais que fazem da ciência uma ferramenta para orientar decisões e proteger a natureza!</p>
            `.trim(),
        },

        5: {
            imagem: "imagens/noticia-damsweek.png",
            data: "26 de setembro de 2026 • Leitura: 3 min",
            titulo: "Parceria entre Flumen e Ecodinâmica leva ao DamsWeek os resultados do monitoramento limnológico da UHE Guilman Amorim",
            tituloCurto: "Parceria Flumen e Ecodinâmica é destaque no DamsWeek",
            texto: `
            <p>A Flumen e a Ecodinâmica mantêm uma parceria técnica e científica de longa data, que inclui o monitoramento limnológico da Usina Hidrelétrica (UHE) Guilman Amorim, localizada no município de Rio Piracicaba, em Minas Gerais. O trabalho acompanha as condições ambientais do reservatório e reúne informações acumuladas ao longo do tempo, desde o seu enchimento.</p>
            <p>Os resultados dessa série histórica foram apresentados no DamsWeek, evento promovido pelo Comitê Brasileiro de Barragens (CBDB) e realizado em Belém, no Pará, entre 20 e 26 de setembro de 2026.</p>
            <h3>Uma série histórica para entender as comunidades biológicas</h3>
            <p>Um dos principais destaques do estudo é o uso de dados acumulados ao longo dos anos para investigar o comportamento e os padrões das comunidades biológicas do ambiente aquático. O estudo contempla quatro grupos:</p>
            <ul style="margin: 0 0 20px 20px; line-height: 1.8;">
                <li><strong>Fitoplâncton:</strong> organismos microscópicos fotossintetizantes que vivem na coluna d’água e desempenham papel fundamental na produção primária dos ecossistemas aquáticos.</li>
                <li><strong>Perifíton:</strong> comunidade de organismos que se desenvolve aderida a superfícies submersas, como rochas, sedimentos e vegetação.</li>
                <li><strong>Zooplâncton:</strong> conjunto de organismos, geralmente microscópicos, que vivem na coluna d’água e participam das relações alimentares dos ambientes aquáticos.</li>
                <li><strong>Macroinvertebrados:</strong> invertebrados de dimensões relativamente maiores, frequentemente utilizados como indicadores das condições ambientais e ecológicas dos ecossistemas aquáticos.</li>
            </ul>
            <p>A análise dessas comunidades permite investigar mudanças nas condições ecológicas do reservatório, identificar padrões de ocorrência e compreender a dinâmica dos organismos ao longo do tempo.</p>
            <h3>Monitorar é mais do que coletar dados</h3>
            <p>O monitoramento ambiental não se limita à coleta de dados: envolve interpretação científica, identificação de padrões ecológicos e produção de informações que podem subsidiar a gestão dos recursos hídricos. Por isso, a continuidade dos programas de monitoramento e a colaboração técnica entre instituições são tão importantes.</p>
            <p>Levar esses resultados ao DamsWeek foi uma oportunidade de compartilhar conhecimento técnico e científico e de promover a integração entre profissionais e instituições que atuam no setor de recursos hídricos e barragens.</p>
            `.trim(),
        },
};