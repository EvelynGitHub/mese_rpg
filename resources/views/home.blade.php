<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Dashboard de Mundos</title>
    <!-- Inclui o Tailwind CSS para o estilo moderno e responsivo -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Importa a fonte "Inter" do Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            /* Fundo escuro */
            color: #E0E0E0;
            /* Texto claro */
        }

        .text-gradient {
            background-image: linear-gradient(to right, #6EE7B7, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Estilo para o modal, invisível por padrão */
        .modal {
            display: none;
            /* Esconde o modal por padrão */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            /* Fundo semi-transparente */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #1F1F1F;
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Botão de fechar do modal */
        .close-button {
            color: #aaa;
            float: right;
            font-size: 2rem;
            font-weight: bold;
        }

        .close-button:hover,
        .close-button:focus {
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body class="antialiased">

    <!-- Container principal -->
    <div class="min-h-screen flex flex-col items-center p-4 md:p-8">

        <!-- Cabeçalho -->
        <header class="w-full max-w-7xl flex justify-between items-center py-6 px-4">
            <h1 class="text-3xl font-bold">
                <span class="text-gradient">RPG</span>
            </h1>
            <nav>
                <a href="#" class="mx-2 text-white/70 hover:text-white transition-colors duration-300">Dashboard</a>
                <a href="#" class="mx-2 text-white/70 hover:text-white transition-colors duration-300">Minha Conta</a>
            </nav>
        </header>

        <!-- Seção Hero/Introdução -->
        <section class="text-center py-12 px-4 max-w-4xl">
            <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                Bem-vindo, <span class="text-gradient">Mestre!</span>
            </h2>
            <p class="text-lg text-gray-400 mb-8">
                Aqui você gerencia todos os seus mundos e campanhas. Selecione um mundo para começar sua aventura.
            </p>
            <button id="add-world-btn"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full shadow-lg transition-transform transform hover:scale-105">
                Criar Novo Mundo
            </button>
        </section>

        <!-- Seção de Mundos -->
        <section class="w-full max-w-7xl mt-12 px-4">
            <h3 class="text-3xl font-bold mb-6 text-white">Mundos Criados</h3>
            <div id="worlds-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Cards de Mundo -->
            </div>

            <a href="#" id="ver-mais-mundos"
                class="text-blue-400 hover:text-blue-300 transition-colors duration-300 font-medium">
                Ver mais &rarr;
            </a>
        </section>

        <!-- Seção de Personagens (com lazy loading/paginaçao infinita) -->
        <section class="w-full max-w-7xl mt-12 px-4">
            <h3 class="text-3xl font-bold mb-6 text-white">Meus Personagens</h3>
            <div id="characters-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Cards de personagens serão adicionados dinamicamente aqui -->
            </div>

            <!-- Indicador de carregamento no final da lista, observado pelo IntersectionObserver -->
            <div id="loading-indicator" class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
            </div>

            <!-- Mensagem quando não houver mais dados para carregar -->
            <div id="end-of-list" class="hidden text-center text-gray-400 mt-8">
                <span>Não há mais personagens para exibir.</span>
            </div>
        </section>

    </div>

    <!-- Modal para "Criar Novo Mundo" -->
    <div id="create-world-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="close-modal-btn">&times;</span>
            <h4 class="text-2xl font-bold text-white mb-4">Criar Novo Mundo</h4>
            <form id="create-world-form">
                <div class="mb-4">
                    <label for="world-name" class="block text-gray-400 mb-1">Nome do Mundo</label>
                    <input type="text" id="world-name" name="world-name"
                        class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex: Mundo de Arkania" required>
                </div>
                <div class="mb-4">
                    <label for="world-description" class="block text-gray-400 mb-1">Descrição</label>
                    <textarea id="world-description" name="world-description" rows="4"
                        class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Descreva brevemente seu mundo..." required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg mr-2"
                        id="cancel-modal-btn">Cancelar</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Criar
                        Mundo</button>
                </div>
            </form>
        </div>
    </div>

    <script type="module">

        import { criarMundo, listarMundos } from "./js/api/mundos.js";

        // Lógica para controle do modal, reutilizável para qualquer formulário
        const modal = document.getElementById('create-world-modal');
        const openBtn = document.getElementById('add-world-btn');
        const closeBtn = document.getElementById('close-modal-btn');
        const cancelBtn = document.getElementById('cancel-modal-btn');
        const form = document.getElementById('create-world-form');
        const worldsGrid = document.getElementById('worlds-grid');
        const verMaisMundos = document.getElementById('ver-mais-mundos');
        const cores = ["border-t-emerald-400", "border-t-indigo-400", "border-t-teal-400", "border-t-purple-400"]
        // const maxCores = 4;
        let indexCorCardWorld = 0;
        let indexCorCardPersonagem = 0;
        let offsetMundos = 0;

        // Nova cor do index cardMundo true, false = personagem
        function nextCor(cardMundo) {
            let cor = "";

            if (cardMundo) {
                if (indexCorCardWorld >= cores.length) {
                    indexCorCardWorld = 0;
                }
                cor = cores[indexCorCardWorld];
                indexCorCardWorld++
            } else {
                if (indexCorCardPersonagem >= cores.length) {
                    indexCorCardPersonagem = 0;
                }
                cor = cores[indexCorCardPersonagem];
                indexCorCardPersonagem++
            }

            return cor;
        }

        // Função para mostrar o modal
        function showModal() {
            modal.style.display = 'flex';
        }

        // Função para esconder o modal
        function hideModal() {
            document.getElementById('world-name').value = "";
            document.getElementById("world-description").value = "";
            modal.style.display = 'none';
        }

        // Evento para abrir o modal
        openBtn.addEventListener('click', showModal);

        // Eventos para fechar o modal
        closeBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);
        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                hideModal();
            }
        });

        // Evento de submissão do formulário (simulado)
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const worldName = document.getElementById('world-name').value;
            const worldDescription = document.getElementById("world-description").value;

            try {
                const response = await criarMundo({ nome: worldName, descricao: worldDescription });

                worldsGrid.prepend(createMundoCard({ ...response, mestre: true }))

                alert(`Mundo: "${worldName}". Criado com Sucesso.`);
            } catch (error) {
                console.log('error', error.message);

                alert(`Não foi possível criar o mundo.`);
            }

            hideModal();
        });

        // Função para criação de cards de Mundo
        function createMundoCard(mundo) {
            const cor = nextCor(true);

            const card = document.createElement('div');
            card.className = `bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 ${cor} transition-transform transform hover:scale-105 hover:shadow-2xl`;
            card.innerHTML = `
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">🌍</span>
                    <h4 class="text-xl font-semibold text-white">${mundo.nome}</h4>
                </div>
                <p class="text-gray-400 mb-4">
                    ${mundo.descricao}
                </p>
                <div class="flex justify-between">
                    <span class="inline-block ${mundo.mestre ? 'bg-yellow-600' : 'bg-blue-500'}  text-white text-xs font-semibold px-2 py-1 rounded-full">
                        ${mundo.mestre ? '✨ Criador' : '🎮 Jogador'}
                    </span>
                    <a href="/mundo/${mundo.id}"
                        class="text-blue-400 hover:text-blue-300 transition-colors duration-300 font-medium">
                        Entrar &rarr;
                    </a>
                </div>
            `;
            return card;
        }

        verMaisMundos.addEventListener('click', async () => await loadMoreMundos())

        async function loadMoreMundos() {
            const limit = 10;

            try {
                const response = await listarMundos(offsetMundos);
                response.forEach(mundo => {
                    worldsGrid.prepend(createMundoCard(mundo))
                });

            } catch (error) {
                console.log('error', error.message);

                worldsGrid.insertAdjacentText('beforeend', "Fim dos mundos!");
            }

            offsetMundos += limit;
        }
        // --- Lógica de Paginação Infinita para Personagens ---

        // Variáveis de controle de estado
        let currentPage = 1;
        let isFetching = false;
        let hasMoreCharacters = true;

        const charactersGrid = document.getElementById('characters-grid');
        const loadingIndicator = document.getElementById('loading-indicator');
        const endOfListMessage = document.getElementById('end-of-list');

        // Simula uma chamada de API para buscar personagens
        // Em um projeto real, você usaria `fetch()` aqui.
        function fetchCharacters(page) {
            return new Promise(resolve => {
                setTimeout(() => {
                    const characters = [
                        { name: "Saphira, a Andarilha", class: "Guerreira", avatar: "https://placehold.co/128x128/334155/E2E8F0?text=S" },
                        { name: "Caelum, o Sábio", class: "Mago", avatar: "https://placehold.co/128x128/334155/E2E8F0?text=C" },
                        { name: "Vesperia, a Lâmina", class: "Assassina", avatar: "https://placehold.co/128x128/334155/E2E8F0?text=V" },
                        { name: "Balthazar, o Bárbaro", class: "Bárbaro", avatar: "https://placehold.co/128x128/334155/E2E8F0?text=B" },
                        { name: "Elara, a Arqueira", class: "Arqueira", avatar: "https://placehold.co/128x128/334155/E2E8F0?text=E" },
                        { name: "Finn, o Curandeiro", class: "Clérigo", avatar: "https://placehold.co/128x128/334155/E2E8F0?text=F" }
                    ];

                    // Simula que a página 3 não tem mais dados
                    if (page >= 3) {
                        return resolve([]);
                    }

                    resolve(characters);
                }, 1000); // Simula o tempo de carregamento
            });
        }

        // Função para criar o HTML de um card de personagem
        function createCharacterCard(character) {
            const cor = nextCor(false);

            const card = document.createElement('div');
            card.className = `bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 ${cor} transition-transform transform hover:scale-105 hover:shadow-2xl`;
            card.innerHTML = `
                <div class="flex items-center mb-4">
                    <img src="${character.avatar}" alt="Avatar" class="w-12 h-12 rounded-full mr-4 border-2 ${cor}">
                    <div>
                        <h4 class="text-xl font-semibold text-white">${character.name}</h4>
                        <p class="text-gray-400 text-sm">Classe: ${character.class}</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors duration-300 font-medium">
                        Ver Ficha &rarr;
                    </a>
                </div>
            `;
            return card;
        }

        // Função principal para carregar os personagens
        async function loadMoreCharacters() {
            if (isFetching || !hasMoreCharacters) return;
            isFetching = true;
            loadingIndicator.style.display = 'flex';

            try {
                const newCharacters = await fetchCharacters(currentPage);

                if (newCharacters.length === 0) {
                    hasMoreCharacters = false;
                    endOfListMessage.style.display = 'block';
                    loadingIndicator.style.display = 'none';
                    return;
                }

                newCharacters.forEach(character => {
                    charactersGrid.appendChild(createCharacterCard(character));
                });

                currentPage++;
            } catch (error) {
                console.error("Erro ao carregar personagens:", error);
            } finally {
                isFetching = false;
                loadingIndicator.style.display = 'none';
            }
        }

        // Configuração do IntersectionObserver
        const observer = new IntersectionObserver((entries) => {
            const lastEntry = entries[0];
            if (lastEntry.isIntersecting) {
                // Se o "loading-indicator" estiver visível, carrega mais personagens
                loadMoreCharacters();
            }
        }, {
            root: null, // viewport
            rootMargin: '0px',
            threshold: 1.0 // Dispara quando 100% do elemento está visível
        });

        // Observa o elemento de carregamento
        observer.observe(loadingIndicator);

        // Carrega a primeira página ao carregar a página
        loadMoreCharacters();
        loadMoreMundos();
    </script>
</body>

</html>
