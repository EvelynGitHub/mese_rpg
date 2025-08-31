<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Gerenciar NPCs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0c0a09;
            color: #E0E0E0;
        }

        .text-gradient {
            background-image: linear-gradient(to right, #6EE7B7, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-overlay {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s 0.3s, opacity 0.3s ease-in-out;
        }

        .modal-overlay.open {
            visibility: visible;
            opacity: 1;
            transition: visibility 0s 0s, opacity 0.3s ease-in-out;
        }

        .card {
            background-color: #1a1a1a;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            border: 1px solid #2f3747;
            border-top: 4px solid #F9A8D4;
        }

        .loading-animation {
            border-top-color: #F9A8D4;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="antialiased">

    <div class="min-h-screen flex flex-col items-center p-4 md:p-8">

        <!-- Cabeçalho com o nome do Mundo e botão de voltar -->
        <header class="w-full max-w-7xl flex justify-between items-center py-6 px-4">
            <a href="/painel-mestre" class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
                &larr; Voltar para o Painel do Mestre
            </a>
            <h1 class="text-3xl font-bold">
                <span class="text-gradient">RPG</span> - NPCs
            </h1>
        </header>

        <!-- Container para o conteúdo principal da página -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Gerenciar NPCs do Mundo
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Crie e edite os personagens não-jogáveis que habitam o seu mundo.
                </p>

                <!-- Botão que abre o modal -->
                <button id="open-modal-btn"
                    class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                    Adicionar Novo NPC
                </button>
            </section>

            <!-- Seção de Listagem de NPCs em formato de cards -->
            <section class="w-full max-w-7xl mx-auto mt-8">
                <h3 class="text-2xl font-semibold mb-6 text-white">NPCs Existentes</h3>
                <div id="npcs-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards de NPCs serão populados aqui via JavaScript -->
                </div>
                <!-- Elemento sentinela para a rolagem infinita -->
                <div id="sentinela" class="h-1 bg-transparent mt-8"></div>
                <div id="loading-indicator" class="flex flex-col items-center text-center p-4 text-gray-400 hidden">
                    <div class="loading-animation rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    Carregando mais NPCs...
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para o Formulário de NPC -->
    <div id="npc-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <section
            class="bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <h3 class="text-2xl font-semibold mb-6 text-white text-center">Formulário de NPC</h3>
            <form id="npc-form" class="space-y-6">
                <input type="hidden" id="npc-id" name="id">

                <!-- Informações Básicas do NPC -->
                <div>
                    <label for="nome" class="block text-white font-medium mb-2">Nome do NPC</label>
                    <input type="text" id="nome" name="nome" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                        placeholder="Ex: Barman do Canto Obscuro">
                </div>
                <div>
                    <label for="slug" class="block text-white font-medium mb-2">Slug (Identificador Único)</label>
                    <input type="text" id="slug" name="slug" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                        placeholder="Ex: barman-canto-obscuro">
                </div>
                <div>
                    <label for="descricao" class="block text-white font-medium mb-2">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                        placeholder="Uma breve descrição do NPC..."></textarea>
                </div>
                <div>
                    <label for="classe" class="block text-white font-medium mb-2">Classe</label>
                    <input type="text" id="classe" name="classe"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                        placeholder="Ex: Guerreiro, Mago (opcional)">
                </div>
                <div>
                    <label for="origem" class="block text-white font-medium mb-2">Origem</label>
                    <input type="text" id="origem" name="origem"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                        placeholder="Ex: Humano, Elfo (opcional)">
                </div>

                <!-- Seção de Atributos Dinâmicos -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-white">Atributos</h4>
                        <button type="button" id="add-atributo-btn"
                            class="text-purple-400 hover:text-purple-500 font-bold text-lg transition-colors">
                            + Adicionar Atributo
                        </button>
                    </div>
                    <div id="atributos-container" class="space-y-4">
                        <!-- Campos de atributos dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <!-- Seção de Inventário -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-white">Inventário</h4>
                        <button type="button" id="add-inventario-btn"
                            class="text-yellow-400 hover:text-yellow-500 font-bold text-lg transition-colors">
                            + Adicionar Item
                        </button>
                    </div>
                    <div id="inventario-container" class="space-y-4">
                        <!-- Campos de inventário dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <div class="flex justify-between space-x-4">
                    <button type="button" id="close-modal-btn"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Salvar NPC
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de NPCs

        // --- Referências DOM e Estado ---
        const npcsList = document.getElementById('npcs-list');
        const form = document.getElementById('npc-form');
        const modalOverlay = document.getElementById('npc-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const atributosContainer = document.getElementById('atributos-container');
        const addAtributoBtn = document.getElementById('add-atributo-btn');
        const inventarioContainer = document.getElementById('inventario-container');
        const addInventarioBtn = document.getElementById('add-inventario-btn');
        const loadingIndicator = document.getElementById('loading-indicator');

        let offset = 0;
        let isLoading = false;
        let hasMore = true;
        let atributoIndex = 0;
        let inventarioIndex = 0;

        // --- Mock de Dados do Servidor ---
        const mockApi = {
            listarNpcs: async (offset) => {
                const dadosSimulados = [
                    { id: 1, nome: 'Aldo, o Ferreiro', slug: 'aldo-o-ferreiro', descricao: 'Um ferreiro local, conhecido por suas armas de alta qualidade.', atributos: { forca: 18, carisma: 12 }, inventario: ['martelo-grande', 'placa-de-couro'] },
                    { id: 2, nome: 'Vyn, o Alquimista', slug: 'vyn-o-alquimista', descricao: 'Um recluso alquimista que mora nas colinas.', atributos: { inteligencia: 20 }, inventario: ['pocao-cura', 'ervas-raras'] },
                    { id: 3, nome: 'Lady Elara', slug: 'lady-elara', descricao: 'Uma nobre da capital, envolvida em intrigas políticas.', atributos: { carisma: 19 }, inventario: ['anel-de-ouro', 'joias'] },
                    { id: 4, nome: 'Gronk, o Bárbaro', slug: 'gronk-o-barbaro', descricao: 'Um guerreiro imponente com pouca paciência e um grande machado.', atributos: { forca: 22, constituicao: 20 }, inventario: ['machado-de-guerra'] },
                ];
                await new Promise(resolve => setTimeout(resolve, 500));
                return dadosSimulados.slice(offset, offset + 4);
            }
        };

        // --- Utilitários ---
        const openModal = () => modalOverlay.classList.add('open');
        const closeModal = () => {
            form.reset();
            atributosContainer.innerHTML = '';
            inventarioContainer.innerHTML = '';
            atributoIndex = 0;
            inventarioIndex = 0;
            modalOverlay.classList.remove('open');
        };

        const createCard = (npc) => {
            const card = document.createElement('div');
            card.className = 'card relative';
            card.innerHTML = `
                <h4 class="text-xl font-semibold text-white mb-2">${npc.nome}</h4>
                <p class="text-sm font-medium text-purple-300 mb-2">${npc.classe || 'Sem Classe'} / ${npc.origem || 'Sem Origem'}</p>
                <p class="text-sm text-gray-400 mb-4 flex-grow line-clamp-2">${npc.descricao}</p>
                <div class="flex justify-end space-x-2 mt-4">
                    <button class="edit-btn px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors" data-id="${npc.id}">Editar</button>
                    <button class="delete-btn px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors" data-id="${npc.id}">Excluir</button>
                </div>
            `;
            return card;
        };

        const createAtributoField = () => {
            const container = document.createElement('div');
            container.className = 'flex items-center space-x-2';
            container.innerHTML = `
                <input type="text" name="atributos[${atributoIndex}][chave]" required class="w-1/3 px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors" placeholder="Chave (ex: forca)">
                <input type="text" name="atributos[${atributoIndex}][valor]" required class="w-2/3 px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors" placeholder="Valor (ex: 18)">
                <button type="button" class="remove-atributo-btn text-red-400 hover:text-red-500">&times;</button>
            `;
            atributoIndex++;
            return container;
        };

        const createInventarioField = () => {
            const container = document.createElement('div');
            container.className = 'flex items-center space-x-2';
            container.innerHTML = `
                <input type="text" name="inventario[${inventarioIndex}]" required class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors" placeholder="Slug do Item (ex: espada-longa)">
                <button type="button" class="remove-inventario-btn text-red-400 hover:text-red-500">&times;</button>
            `;
            inventarioIndex++;
            return container;
        };

        // --- Renderização e Carregamento ---
        const loadNpcs = async (append = true) => {
            if (isLoading || !hasMore) return;
            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    offset = 0;
                    npcsList.innerHTML = '';
                    hasMore = true;
                }

                const npcs = await mockApi.listarNpcs(offset);
                if (npcs.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        npcsList.innerHTML = `<p class="text-center text-gray-400 col-span-full">Nenhum NPC encontrado. Crie um!</p>`;
                    }
                    return;
                }

                npcs.forEach(o => npcsList.appendChild(createCard(o)));
                offset += npcs.length;
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        };

        // --- Eventos ---
        openModalBtn.addEventListener('click', () => {
            openModal();
        });
        closeModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        addAtributoBtn.addEventListener('click', () => {
            atributosContainer.appendChild(createAtributoField());
        });

        addInventarioBtn.addEventListener('click', () => {
            inventarioContainer.appendChild(createInventarioField());
        });

        // Evento para remover campos dinâmicos
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-atributo-btn')) {
                e.target.closest('div[class*="flex items-center"]').remove();
            }
            if (e.target.classList.contains('remove-inventario-btn')) {
                e.target.closest('div[class*="flex items-center"]').remove();
            }
        });

        // Simulação do formulário de submissão
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Lógica para coletar os atributos dinâmicos
            const atributos = {};
            atributosContainer.querySelectorAll('.flex.items-center').forEach(item => {
                const chave = item.querySelector('input[name*="[chave]"]').value;
                const valor = item.querySelector('input[name*="[valor]"]').value;
                if (chave && valor) {
                    atributos[chave] = valor;
                }
            });

            // Lógica para coletar os itens do inventário
            const inventario = [];
            inventarioContainer.querySelectorAll('input[name^="inventario"]').forEach(item => {
                const valor = item.value;
                if (valor) {
                    inventario.push(valor);
                }
            });

            const payload = {
                ...data,
                atributos: atributos,
                inventario: inventario
            };

            console.log("Payload final para a API:", payload);

            closeModal();
            // Simulação de sucesso
            alert(`NPC "${payload.nome}" salvo com sucesso!`);
        });

        // Carrega os NPCs na inicialização da página
        loadNpcs();
    </script>
</body>

</html>