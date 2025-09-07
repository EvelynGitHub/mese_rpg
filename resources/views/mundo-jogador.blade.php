<h1>Mundo Jogador</h1>


<h2>ID {{ $mundo_id }}</h2>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Dashboard</title>
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

        /* Estilo para simular um dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #1F1F1F;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.5);
            z-index: 1;
            right: 0;
            top: 100%;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .dropdown:hover .dropdown-content,
        .dropdown-content.show {
            display: block;
        }

        .dropdown-content a {
            color: #E0E0E0;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #333;
        }
    </style>
</head>

<body class="antialiased">

    <!-- Container principal -->
    <div class="min-h-screen flex flex-col items-center p-4 md:p-8">

        <!-- Cabeçalho -->
        <header class="w-full max-w-7xl flex justify-between items-center py-6 px-4">
            <h1 class="text-3xl font-bold cursor-pointer" onclick="goHome()">
                <span class="text-gradient">RPG</span>
            </h1>
            <nav class="flex items-center space-x-4">
                <div class="dropdown">
                    <button
                        class="text-white/70 hover:text-white transition-colors duration-300 py-2 px-4 rounded-lg bg-slate-800">
                        Papel: <span id="current-role">Jogador</span> ▼
                    </button>
                    <div id="role-selector" class="dropdown-content">
                        <a href="#" data-role="jogador">Jogador</a>
                        <a href="#" data-role="mestre">Mestre</a>
                    </div>
                </div>
                <a href="#" class="mx-2 text-white/70 hover:text-white transition-colors duration-300">Minha Conta</a>
            </nav>
        </header>

        <!-- Container para o conteúdo principal, que será dinâmico -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <!-- O conteúdo da página será injetado aqui pelo JavaScript -->
            <div class="flex justify-center items-center h-96">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-gray-400"></div>
            </div>
        </main>
    </div>

    <!-- Modal para "Criar Nova Entidade" -->
    <div id="dynamic-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="close-modal-btn">&times;</span>
            <h4 id="modal-title" class="text-2xl font-bold text-white mb-4"></h4>
            <div id="modal-body"></div>
        </div>
    </div>

    <script>
        // --- Estado Global da Aplicação ---
        const state = {
            userRole: 'jogador',
            userId: 'user-123',
            worldId: null,
            currentPage: 1,
            isFetching: false,
            hasMoreData: true,
        };

        // --- Mock Data (Simulando o Backend) ---
        const mockData = {
            mundos: [
                { id: 'mundo-elara', nome: 'Reino de Elara', descricao: 'Um mundo de fantasia medieval.', icon: '🌍' },
                { id: 'mundo-neoterra', nome: 'Cidades de Neo-Terra', descricao: 'Um futuro cyberpunk distópico.', icon: '🚀' },
                { id: 'mundo-floresta', nome: 'O Pesadelo da Floresta', descricao: 'Cenário de horror e sobrevivência.', icon: '🧟‍♂️' }
            ],
            personagens: (worldId) => {
                const characters = {
                    'mundo-elara': [
                        { id: 'char-1', nome: "Saphira, a Andarilha", classe: { nome: "Guerreira" }, origem: { nome: "Humana" }, atributos: { Força: { valor: 18, dado: "d20" }, Destreza: { valor: 12, dado: "d12" }, Inteligência: { valor: 8, dado: "d4" } }, inventario: ["Espada Longa", "Escudo de Madeira"], historico: ["Mestre alterou Força de 16 para 18 em 25/08/2025"], avatar: "https://placehold.co/128x128/334155/E2E8F0?text=S" },
                        { id: 'char-2', nome: "Finn, o Curandeiro", classe: { nome: "Clérigo" }, origem: { nome: "Elfo" }, atributos: { Força: { valor: 10, dado: "d6" }, Destreza: { valor: 14, dado: "d10" }, Inteligência: { valor: 16, dado: "d12" } }, inventario: ["Cajado de Cura"], historico: ["Personagem criado em 24/08/2025"], avatar: "https://placehold.co/128x128/334155/E2E8F0?text=F" },
                        { id: 'char-3', nome: "Geralt, o Bruxo", classe: { nome: "Mago" }, origem: { nome: "Bruxo" }, atributos: { Força: { valor: 12, dado: "d8" }, Destreza: { valor: 16, dado: "d10" }, Inteligência: { valor: 20, dado: "d20" } }, inventario: ["Capa da Noite", "Vara Arcana"], historico: ["Personagem criado em 23/08/2025"], avatar: "https://placehold.co/128x128/334155/E2E8F0?text=G" }
                    ],
                    'mundo-neoterra': [
                        { id: 'char-4', nome: "Kira 7, a Cyber Ninja", classe: { nome: "Assassina" }, origem: { nome: "Cyborg" }, atributos: { Habilidade: { valor: 20, dado: "d20" }, Carisma: { valor: 10, dado: "d6" }, Tecnologia: { valor: 18, dado: "d20" } }, inventario: ["Lâminas Cibernéticas"], historico: ["Personagem criado em 22/08/2025"], avatar: "https://placehold.co/128x128/334155/E2E8F0?text=K" },
                        { id: 'char-5', nome: "Ryo, o Hacker", classe: { nome: "Técnico" }, origem: { nome: "Humano" }, atributos: { Habilidade: { valor: 14, dado: "d10" }, Carisma: { valor: 16, dado: "d12" }, Tecnologia: { valor: 18, dado: "d20" } }, inventario: ["Hack-Deck"], historico: ["Personagem criado em 21/08/2025"], avatar: "https://placehold.co/128x128/334155/E2E8F0?text=R" }
                    ],
                    'mundo-floresta': [
                        { id: 'char-6', nome: "Jack, o Sobrevivente", classe: { nome: "Bárbaro" }, origem: { nome: "Humano" }, atributos: { Força: { valor: 18, dado: "d20" }, Destreza: { valor: 12, dado: "d12" }, Inteligência: { valor: 8, dado: "d4" } }, inventario: ["Faca de Caça"], historico: ["Personagem criado em 20/08/2025"], avatar: "https://placehold.co/128x128/334155/E2E8F0?text=J" }
                    ]
                };
                return characters[worldId] || [];
            },
            classes: (worldId) => {
                const classes = {
                    'mundo-elara': [{ nome: 'Guerreiro' }, { nome: 'Mago' }, { nome: 'Arqueiro' }],
                    'mundo-neoterra': [{ nome: 'Assassino' }, { nome: 'Hacker' }, { nome: 'Piloto' }]
                };
                return classes[worldId] || [];
            },
            atributos: (worldId) => {
                const atributos = {
                    'mundo-elara': ['Força', 'Destreza', 'Inteligência', 'Sabedoria', 'Carisma'],
                    'mundo-neoterra': ['Habilidade', 'Carisma', 'Tecnologia']
                };
                return atributos[worldId] || [];
            }
        };

        // --- Funções de Ajuda (Helpers) ---

        // Função genérica para abrir o modal
        function openModal(title, content) {
            const modal = document.getElementById('dynamic-modal');
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-body').innerHTML = content;
            modal.style.display = 'flex';
        }

        // Função genérica para fechar o modal
        function closeModal() {
            document.getElementById('dynamic-modal').style.display = 'none';
        }

        // Função genérica para criar um card
        function createCard(data, type) {
            const card = document.createElement('div');
            card.className = "bg-slate-800 p-6 rounded-xl shadow-lg transition-transform transform hover:scale-105 hover:shadow-2xl cursor-pointer";

            let icon = '';
            let title = '';
            let description = '';

            if (type === 'mundo') {
                card.classList.add('border-t-4', 'border-t-emerald-400');
                icon = data.icon;
                title = data.nome;
                description = data.descricao;
                card.dataset.id = data.id;
                card.addEventListener('click', () => {
                    state.worldId = data.id;
                    renderWorldDashboard();
                });
            } else if (type === 'personagem') {
                card.classList.add('border-t-4', 'border-t-teal-400');
                const avatar = data.avatar || "https://placehold.co/128x128/334155/E2E8F0?text=P";
                icon = `<img src="${avatar}" alt="Avatar" class="w-12 h-12 rounded-full mr-4 border-2 border-teal-400">`;
                title = data.nome;
                description = `Classe: ${data.classe.nome}`;
                card.dataset.id = data.id; // Adiciona o ID do personagem para uso posterior
                card.addEventListener('click', () => renderCharacterSheet(data.id));
            } else if (type === 'classe') {
                card.classList.add('border-t-4', 'border-t-purple-400');
                icon = '🛡️';
                title = data.nome;
                description = "Descrição da classe aqui...";
            } else if (type === 'atributo') {
                card.classList.add('border-t-4', 'border-t-indigo-400');
                icon = '💪';
                title = data;
                description = "Descrição do atributo aqui...";
            }

            card.innerHTML = `
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">${icon}</span>
                    <h4 class="text-xl font-semibold text-white">${title}</h4>
                </div>
                <p class="text-gray-400 mb-4">${description}</p>
                <div class="flex justify-end">
                    <span class="text-blue-400 hover:text-blue-300 transition-colors duration-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </div>
            `;
            return card;
        }

        // Função para voltar para a tela inicial
        function goHome() {
            state.worldId = null;
            renderHomePage();
        }

        // --- Funções de Renderização das Telas (Views) ---

        function renderHomePage() {
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = `
                <section class="text-center py-12 px-4 max-w-4xl mx-auto">
                    <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                        Selecione seu <span class="text-gradient">Mundo</span>
                    </h2>
                    <p class="text-lg text-gray-400 mb-8">
                        Escolha um mundo que você já faz parte ou crie um novo para começar uma aventura.
                    </p>
                    <button id="add-world-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full shadow-lg transition-transform transform hover:scale-105">
                        Criar Novo Mundo
                    </button>
                </section>

                <section class="w-full max-w-7xl mt-12 px-4">
                    <h3 class="text-3xl font-bold mb-6 text-white">Mundos Criados</h3>
                    <div id="worlds-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Cards de mundos serão adicionados dinamicamente aqui -->
                    </div>
                </section>
            `;
            document.getElementById('add-world-btn').addEventListener('click', () => {
                openModal('Criar Novo Mundo', `
                    <form id="create-world-form">
                        <div class="mb-4">
                            <label for="world-name" class="block text-gray-400 mb-1">Nome do Mundo</label>
                            <input type="text" id="world-name" name="world-name" class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: Mundo de Arkania" required>
                        </div>
                        <div class="mb-4">
                            <label for="world-description" class="block text-gray-400 mb-1">Descrição</label>
                            <textarea id="world-description" name="world-description" rows="4" class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Descreva brevemente seu mundo..." required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg mr-2" onclick="closeModal()">Cancelar</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Criar Mundo</button>
                        </div>
                    </form>
                `);
                document.getElementById('create-world-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    alert("Simulando criação de mundo.");
                    closeModal();
                });
            });

            // Renderiza os cards de mundos
            const worldsGrid = document.getElementById('worlds-grid');
            mockData.mundos.forEach(world => {
                worldsGrid.appendChild(createCard(world, 'mundo'));
            });
        }

        function renderWorldDashboard() {
            const mainContent = document.getElementById('main-content');
            const world = mockData.mundos.find(w => w.id === state.worldId);
            document.querySelector('.text-gradient').innerText = world.nome;
            document.getElementById('current-role').innerText = state.userRole === 'mestre' ? 'Mestre' : 'Jogador';

            if (state.userRole === 'mestre') {
                renderMasterDashboard(world);
            } else {
                renderPlayerDashboard(world);
            }
        }

        function renderMasterDashboard(world) {
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = `
                <section class="text-center py-12 px-4 max-w-4xl mx-auto">
                    <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                        Painel do <span class="text-gradient">Mestre</span>
                    </h2>
                    <p class="text-lg text-gray-400 mb-8">
                        Gerencie todos os aspectos do mundo de <span class="text-gradient">${world.nome}</span>.
                    </p>
                </section>
                <div class="w-full max-w-7xl mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-purple-400">
                        <h4 class="text-2xl font-semibold text-white mb-2">Catálogos</h4>
                        <ul class="text-white/70 space-y-2">
                            <li><a href="#" class="hover:text-white" data-view="atributos">Atributos</a></li>
                            <li><a href="#" class="hover:text-white" data-view="classes">Classes</a></li>
                            <li><a href="#" class="hover:text-white">Origens</a></li>
                            <li><a href="#" class="hover:text-white">Habilidades</a></li>
                            <li><a href="#" class="hover:text-white">Itens / Armas</a></li>
                            <li><a href="#" class="hover:text-white">NPCs</a></li>
                        </ul>
                    </div>
                    <div class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-blue-400">
                        <h4 class="text-2xl font-semibold text-white mb-2">Campanhas e Sessões</h4>
                        <ul class="text-white/70 space-y-2">
                            <li><a href="#" class="hover:text-white">Ver Campanhas</a></li>
                            <li><a href="#" class="hover:text-white">Iniciar Nova Sessão</a></li>
                        </ul>
                    </div>
                    <div class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-teal-400">
                        <h4 class="text-2xl font-semibold text-white mb-2">Personagens</h4>
                        <p class="text-white/70">
                            Visualize e edite a ficha de todos os personagens do mundo.
                        </p>
                        <a href="#" class="block mt-4 text-blue-400 hover:text-blue-300" data-view="personagens">Ver Personagens &rarr;</a>
                    </div>
                </div>
            `;
            // Adiciona event listeners para os links de navegação do Mestre
            document.querySelectorAll('[data-view]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const view = e.target.dataset.view;
                    if (view === 'atributos') {
                        renderAttributesPage(world);
                    } else if (view === 'classes') {
                        renderClassesPage(world);
                    } else if (view === 'personagens') {
                        renderCharactersPage(world);
                    }
                });
            });
        }

        function renderPlayerDashboard(world) {
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = `
                <section class="text-center py-12 px-4 max-w-4xl mx-auto">
                    <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                        Seus Personagens
                    </h2>
                    <p class="text-lg text-gray-400 mb-8">
                        Selecione um de seus personagens para ver a ficha completa e entrar em uma campanha.
                    </p>
                </section>

                <section class="w-full max-w-7xl mt-12 px-4">
                    <h3 class="text-3xl font-bold mb-6 text-white">Personagens em <span class="text-gradient">${world.nome}</span></h3>
                    <div id="characters-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Cards de personagens serão adicionados dinamicamente aqui -->
                    </div>
                    <div id="loading-indicator" class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    </div>
                    <div id="end-of-list" class="hidden text-center text-gray-400 mt-8">
                        <span>Não há mais personagens para exibir.</span>
                    </div>
                </section>
            `;
            // Reseta a paginação e inicia o carregamento
            state.currentPage = 1;
            state.hasMoreData = true;
            loadMoreCharacters();
        }

        // Nova tela de Personagens (para Mestre e Jogador)
        function renderCharactersPage(world) {
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = `
                <section class="py-12 px-4 max-w-4xl mx-auto">
                    <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                        Personagens do Mundo
                    </h2>
                    <p class="text-lg text-gray-400 mb-8">
                        Lista completa de todos os personagens em <span class="text-gradient">${world.nome}</span>.
                    </p>
                </section>
                <div class="w-full max-w-7xl mt-12 px-4">
                    <div id="characters-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Cards de personagens serão adicionados dinamicamente aqui -->
                    </div>
                    <div id="loading-indicator" class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    </div>
                    <div id="end-of-list" class="hidden text-center text-gray-400 mt-8">
                        <span>Não há mais personagens para exibir.</span>
                    </div>
                </div>
            `;
            // Reseta a paginação e inicia o carregamento
            state.currentPage = 1;
            state.hasMoreData = true;
            loadMoreCharacters();
        }

        // --- NOVA FUNÇÃO: Renderiza a Ficha do Personagem ---
        function renderCharacterSheet(charId) {
            const character = mockData.personagens(state.worldId).find(c => c.id === charId);
            if (!character) {
                // Simula uma resposta 404
                document.getElementById('main-content').innerHTML = `
                    <div class="py-12 px-4 text-center">
                        <h2 class="text-5xl font-extrabold text-white mb-4">404 - Personagem Não Encontrado</h2>
                        <p class="text-lg text-gray-400">O personagem que você procura não existe neste mundo.</p>
                        <button class="mt-8 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" onclick="renderWorldDashboard()">Voltar</button>
                    </div>
                `;
                return;
            }

            const mainContent = document.getElementById('main-content');
            let contentHtml = '';

            // Visão do Jogador (apenas leitura)
            if (state.userRole === 'jogador') {
                contentHtml = `
                    <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                        <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                            Ficha de <span class="text-gradient">${character.nome}</span>
                        </h2>
                        <div class="flex flex-col md:flex-row items-center justify-center my-8">
                            <img src="${character.avatar}" alt="Avatar do Personagem" class="w-32 h-32 rounded-full border-4 border-teal-400 mb-4 md:mb-0 md:mr-8">
                            <div>
                                <p class="text-lg text-gray-400">**Classe**: ${character.classe.nome}</p>
                                <p class="text-lg text-gray-400">**Origem**: ${character.origem.nome}</p>
                            </div>
                        </div>
                    </section>
                    <div class="w-full max-w-7xl mt-8 px-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Atributos -->
                            <div class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-indigo-400">
                                <h4 class="text-2xl font-semibold text-white mb-4">Atributos</h4>
                                ${Object.keys(character.atributos).map(attr => `
                                    <div class="flex justify-between items-center py-2 border-b border-gray-700 last:border-b-0">
                                        <span>${attr}:</span>
                                        <span class="font-bold text-white">${character.atributos[attr].valor} (${character.atributos[attr].dado})</span>
                                    </div>
                                `).join('')}
                            </div>
                            <!-- Inventário -->
                            <div class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-yellow-400">
                                <h4 class="text-2xl font-semibold text-white mb-4">Inventário</h4>
                                <ul class="list-disc list-inside space-y-2">
                                    ${character.inventario.map(item => `
                                        <li>${item}</li>
                                    `).join('')}
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            }
            // Visão do Mestre (com overrides e histórico)
            else if (state.userRole === 'mestre') {
                contentHtml = `
                    <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                        <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                            Gerenciar <span class="text-gradient">${character.nome}</span>
                        </h2>
                        <div class="flex flex-col md:flex-row items-center justify-center my-8">
                            <img src="${character.avatar}" alt="Avatar do Personagem" class="w-32 h-32 rounded-full border-4 border-teal-400 mb-4 md:mb-0 md:mr-8">
                            <div>
                                <p class="text-lg text-gray-400">**Classe**: ${character.classe.nome}</p>
                                <p class="text-lg text-gray-400">**Origem**: ${character.origem.nome}</p>
                            </div>
                        </div>
                    </section>
                    <div class="w-full max-w-7xl mt-8 px-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Atributos com Override -->
                        <div class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-indigo-400 col-span-1 lg:col-span-2">
                            <h4 class="text-2xl font-semibold text-white mb-4">Atributos (Override do Mestre)</h4>
                            <form id="attribute-override-form" class="space-y-4">
                                ${Object.keys(character.atributos).map(attr => `
                                    <div>
                                        <label for="${attr}-valor" class="block text-gray-400 mb-1">${attr}:</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="number" id="${attr}-valor" name="${attr}-valor" value="${character.atributos[attr].valor}" class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <select id="${attr}-dado" name="${attr}-dado" class="p-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                ${['d4', 'd6', 'd8', 'd10', 'd12', 'd20'].map(dado => `
                                                    <option value="${dado}" ${dado === character.atributos[attr].dado ? 'selected' : ''}>${dado}</option>
                                                `).join('')}
                                            </select>
                                        </div>
                                    </div>
                                `).join('')}
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Salvar Override</button>
                                </div>
                            </form>
                        </div>
                        <!-- Histórico de Auditoria -->
                        <div class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-red-400">
                            <h4 class="text-2xl font-semibold text-white mb-4">Histórico de Auditoria</h4>
                            <ul class="list-none space-y-2">
                                ${character.historico.map(event => `
                                    <li class="text-sm text-gray-400 border-b border-gray-700 pb-2 last:border-b-0">${event}</li>
                                `).join('')}
                            </ul>
                        </div>
                        <!-- Inventário (Editable) -->
                        <div class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-yellow-400 col-span-1">
                            <h4 class="text-2xl font-semibold text-white mb-4">Inventário</h4>
                            <ul id="inventory-list" class="list-none space-y-2">
                                ${character.inventario.map(item => `
                                    <li class="flex items-center justify-between py-1">
                                        <span class="text-white">${item}</span>
                                        <button class="text-red-400 hover:text-red-300 transition-colors" onclick="alert('Simulando a remoção de item: ${item}')">&times;</button>
                                    </li>
                                `).join('')}
                            </ul>
                            <div class="mt-4">
                                <input type="text" id="new-item" placeholder="Adicionar novo item..." class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button onclick="alert('Simulando a adição de item.')" class="mt-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg w-full">Adicionar Item</button>
                            </div>
                        </div>
                    </div>
                `;
            }

            mainContent.innerHTML = contentHtml;

            // Adiciona o botão de voltar para a navegação
            const backButtonHtml = `<button class="fixed top-8 left-8 bg-slate-700 hover:bg-slate-600 text-white font-bold py-2 px-4 rounded-full shadow-lg" onclick="renderCharactersPage(mockData.mundos.find(w => w.id === state.worldId))">&larr; Voltar</button>`;
            mainContent.insertAdjacentHTML('afterbegin', backButtonHtml);
        }

        // Nova tela de Atributos (apenas para Mestre)
        function renderAttributesPage(world) {
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = `
                <section class="py-12 px-4 max-w-4xl mx-auto">
                    <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                        Atributos do Mundo
                    </h2>
                    <p class="text-lg text-gray-400 mb-8">
                        Atributos definidos para <span class="text-gradient">${world.nome}</span>.
                    </p>
                    <div class="flex justify-start mb-6">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                            Adicionar Novo Atributo
                        </button>
                    </div>
                </section>
                <div id="attributes-grid" class="w-full max-w-7xl mt-8 px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                </div>
            `;
            const attributesGrid = document.getElementById('attributes-grid');
            mockData.atributos(state.worldId).forEach(attr => {
                attributesGrid.appendChild(createCard(attr, 'atributo'));
            });
        }

        // Nova tela de Classes (apenas para Mestre)
        function renderClassesPage(world) {
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = `
                <section class="py-12 px-4 max-w-4xl mx-auto">
                    <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                        Classes do Mundo
                    </h2>
                    <p class="text-lg text-gray-400 mb-8">
                        Classes disponíveis em <span class="text-gradient">${world.nome}</span>.
                    </p>
                    <div class="flex justify-start mb-6">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                            Adicionar Nova Classe
                        </button>
                    </div>
                </section>
                <div id="classes-grid" class="w-full max-w-7xl mt-8 px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                </div>
            `;
            const classesGrid = document.getElementById('classes-grid');
            mockData.classes(state.worldId).forEach(cls => {
                classesGrid.appendChild(createCard(cls, 'classe'));
            });
        }

        // --- Lógica de Paginação Infinita para Personagens ---

        // Simula uma chamada de API para buscar personagens
        function fetchCharactersSimulado(worldId, page) {
            return new Promise(resolve => {
                setTimeout(() => {
                    const allCharacters = mockData.personagens(worldId);
                    const perPage = 3;
                    const start = (page - 1) * perPage;
                    const end = start + perPage;
                    const pageCharacters = allCharacters.slice(start, end);
                    resolve(pageCharacters);
                }, 1000); // Simula o tempo de carregamento
            });
        }

        // Função principal para carregar os personagens
        async function loadMoreCharacters() {
            if (state.isFetching || !state.hasMoreData) return;
            state.isFetching = true;
            const loadingIndicator = document.getElementById('loading-indicator');
            const endOfListMessage = document.getElementById('end-of-list');
            loadingIndicator.style.display = 'flex';

            try {
                const newCharacters = await fetchCharactersSimulado(state.worldId, state.currentPage);
                const charactersGrid = document.getElementById('characters-grid');

                if (newCharacters.length === 0) {
                    state.hasMoreData = false;
                    endOfListMessage.style.display = 'block';
                    loadingIndicator.style.display = 'none';
                    return;
                }

                newCharacters.forEach(character => {
                    charactersGrid.appendChild(createCard(character, 'personagem'));
                });

                state.currentPage++;
            } catch (error) {
                console.error("Erro ao carregar personagens:", error);
            } finally {
                state.isFetching = false;
                loadingIndicator.style.display = 'none';
            }
        }

        // --- Inicia a aplicação ---

        // Event listener para o seletor de papel
        document.getElementById('role-selector').addEventListener('click', (e) => {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                state.userRole = e.target.dataset.role;
                document.getElementById('current-role').innerText = state.userRole === 'mestre' ? 'Mestre' : 'Jogador';
                if (state.worldId) {
                    renderWorldDashboard();
                } else {
                    renderHomePage();
                }
            }
        });

        // Configuração do IntersectionObserver para rolagem infinita
        let observer = new IntersectionObserver((entries) => {
            const loadingIndicator = document.getElementById('loading-indicator');
            if (loadingIndicator && entries[0].isIntersecting) {
                loadMoreCharacters();
            }
        }, { root: null, rootMargin: '0px', threshold: 1.0 });

        // Função de inicialização
        function initialize() {
            renderHomePage();
            // O observer precisa ser re-observado sempre que a página com o indicador é renderizada.
            // Para isso, faremos a chamada do observer dentro das funções de renderização, se o elemento existir.
        }

        window.onload = initialize;

    </script>
</body>

</html>
