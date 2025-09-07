<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Gerenciar Personagens</title>
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
            border-top: 4px solid #A78BFA;
        }

        .loading-animation {
            border-top-color: #A78BFA;
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
            <a href="/painel-mestre/{{ $mundo_id }}"
                class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
                &larr; Voltar para o Painel do Mestre
            </a>
            <h1 class="text-3xl font-bold">
                <span class="text-gradient">RPG</span> - Personagens
            </h1>
        </header>

        <!-- Container para o conteúdo principal da página -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Gerenciar Personagens do Mundo
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Crie e edite os personagens jogadores que interagem com o seu mundo.
                </p>

                <!-- Botão que abre o modal -->
                <button id="open-modal-btn"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                    Adicionar Novo Personagem
                </button>
            </section>

            <!-- Seção de Listagem de Personagens em formato de cards -->
            <section class="w-full max-w-7xl mx-auto mt-8">
                <h3 class="text-2xl font-semibold mb-6 text-white">Personagens Existentes</h3>
                <div id="personagens-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards de personagens serão populados aqui via JavaScript -->
                </div>
                <!-- Elemento sentinela para a rolagem infinita -->
                <div id="sentinela" class="h-1 bg-transparent mt-8"></div>
                <div id="loading-indicator" class="flex flex-col items-center text-center p-4 text-gray-400 hidden">
                    <div class="loading-animation rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    Carregando mais personagens...
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para o Formulário de Personagem -->
    <div id="personagem-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <section
            class="bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <h3 class="text-2xl font-semibold mb-6 text-white text-center">Formulário de Personagem</h3>
            <form id="personagem-form" class="space-y-6">
                <input type="hidden" id="personagem-id" name="id">

                <!-- Informações Básicas do Personagem -->
                <div>
                    <label for="nome" class="block text-white font-medium mb-2">Nome do Personagem</label>
                    <input type="text" id="nome" name="nome" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors"
                        placeholder="Ex: Kaelen, o Aventureiro">
                </div>
                <div>
                    <label for="descricao" class="block text-white font-medium mb-2">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors"
                        placeholder="Uma breve história do personagem..."></textarea>
                </div>
                <div>
                    <label for="idade" class="block text-white font-medium mb-2">Idade</label>
                    <input type="number" id="idade" name="idade"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors"
                        placeholder="Ex: 25">
                </div>
                <div>
                    <label for="classe_id" class="block text-white font-medium mb-2">Classe</label>
                    <select id="classe_id" name="classe_id"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                    </select>
                </div>
                <div>
                    <label for="origem_id" class="block text-white font-medium mb-2">Origem</label>
                    <select id="origem_id" name="origem_id"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                    </select>
                </div>

                <!-- Seção de Distribuição de Pontos e Atributos -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <h4 class="text-xl font-semibold text-white mb-4">Distribuição de Atributos</h4>
                    <div class="flex justify-between items-center mb-4 text-sm font-bold">
                        <div>
                            Pontos Base Disponíveis: <span id="pontos-base" class="text-green-400">0</span>
                        </div>
                        <div>
                            Pontos de Dado Disponíveis: <span id="pontos-dados" class="text-green-400">0</span>
                        </div>
                    </div>
                    <div id="atributos-container" class="space-y-4">
                        <!-- Campos de atributos dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <!-- Seção de Habilidades e Inventário -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <h4 class="text-xl font-semibold text-white mb-4">Habilidades & Inventário</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="habilidades_select"
                                class="block text-white font-medium mb-2">Habilidades</label>
                            <select id="habilidades_select" multiple
                                class="w-full h-32 px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                                <!-- Opções de habilidades serão populadas aqui -->
                            </select>
                        </div>
                        <div>
                            <label for="inventario_select" class="block text-white font-medium mb-2">Itens no
                                Inventário</label>
                            <select id="inventario_select" multiple
                                class="w-full h-32 px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                                <!-- Opções de itens serão populadas aqui -->
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between space-x-4">
                    <button type="button" id="close-modal-btn"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Salvar Personagem
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de Personagens

        // --- Referências DOM e Estado ---
        const personagensList = document.getElementById('personagens-list');
        const form = document.getElementById('personagem-form');
        const modalOverlay = document.getElementById('personagem-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const atributosContainer = document.getElementById('atributos-container');
        const pontosBaseEl = document.getElementById('pontos-base');
        const pontosDadosEl = document.getElementById('pontos-dados');
        const loadingIndicator = document.getElementById('loading-indicator');
        const classeSelect = document.getElementById('classe_id');
        const origemSelect = document.getElementById('origem_id');
        const habilidadesSelect = document.getElementById('habilidades_select');
        const inventarioSelect = document.getElementById('inventario_select');

        let offset = 0;
        let isLoading = false;
        let hasMore = true;

        const STARTING_BASE_POINTS = 10;
        const STARTING_DICE_POINTS = 5;
        let availableBasePoints = STARTING_BASE_POINTS;
        let availableDicePoints = STARTING_DICE_POINTS;
        let distributedBasePoints = {};
        let distributedDicePoints = {};

        // --- Mock de Dados do Servidor ---
        const mockApi = {
            listarPersonagens: async (offset) => {
                const dadosSimulados = [
                    { id: 1, nome: 'Aragorn', descricao: 'Um ranger de linhagem nobre, protetor da floresta.', classe: 'Guerreiro', origem: 'Humano', atributos: { forca: 18, destreza: 16 } },
                    { id: 2, nome: 'Gandalf', descricao: 'Um poderoso mago, mentor e conselheiro.', classe: 'Mago', origem: 'Maia', atributos: { inteligencia: 22, sabedoria: 20 } },
                    { id: 3, nome: 'Legolas', descricao: 'Um elfo ágil e habilidoso com arco.', classe: 'Arqueiro', origem: 'Elfo', atributos: { destreza: 20, agilidade: 18 } },
                ];
                await new Promise(resolve => setTimeout(resolve, 500));
                return dadosSimulados.slice(offset, offset + 3);
            },
            listarClasses: async () => {
                await new Promise(resolve => setTimeout(resolve, 200));
                return [
                    { id: 1, nome: 'Guerreiro', atributos: [{ id: 1, nome: 'Força', base_fixa: 10, dado_fixo: 'd8' }, { id: 2, nome: 'Constituição', base_fixa: 8, dado_fixo: 'd6' }] },
                    { id: 2, nome: 'Mago', atributos: [{ id: 3, nome: 'Inteligência', base_fixa: 12, dado_fixo: 'd6' }, { id: 4, nome: 'Sabedoria', base_fixa: 10, dado_fixo: 'd4' }] },
                ];
            },
            listarOrigens: async () => {
                await new Promise(resolve => setTimeout(resolve, 200));
                return [
                    { id: 1, nome: 'Humano' },
                    { id: 2, nome: 'Elfo' },
                    { id: 3, nome: 'Anão' },
                ];
            },
            listarHabilidades: async () => {
                await new Promise(resolve => setTimeout(resolve, 200));
                return [
                    { id: 1, nome: 'Bola de Fogo', slug: 'bola-de-fogo' },
                    { id: 2, nome: 'Ataque Furtivo', slug: 'ataque-furtivo' },
                    { id: 3, nome: 'Cura Acelerada', slug: 'cura-acelerada' },
                ];
            },
            listarItens: async () => {
                await new Promise(resolve => setTimeout(resolve, 200));
                return [
                    { id: 1, nome: 'Espada Longa', slug: 'espada-longa' },
                    { id: 2, nome: 'Poção de Cura', slug: 'pocao-de-cura' },
                    { id: 3, nome: 'Capa Élfica', slug: 'capa-elfica' },
                ];
            }
        };

        // --- Utilitários ---
        const openModal = () => modalOverlay.classList.add('open');
        const closeModal = () => {
            form.reset();
            atributosContainer.innerHTML = '';
            availableBasePoints = STARTING_BASE_POINTS;
            availableDicePoints = STARTING_DICE_POINTS;
            updatePointsDisplay();
            distributedBasePoints = {};
            distributedDicePoints = {};
            modalOverlay.classList.remove('open');
        };

        const updatePointsDisplay = () => {
            pontosBaseEl.textContent = availableBasePoints;
            pontosDadosEl.textContent = availableDicePoints;
            pontosBaseEl.className = availableBasePoints < 0 ? 'text-red-400' : 'text-green-400';
            pontosDadosEl.className = availableDicePoints < 0 ? 'text-red-400' : 'text-green-400';
        };

        const createCard = (personagem) => {
            const card = document.createElement('div');
            card.className = 'card relative';
            card.innerHTML = `
                <h4 class="text-xl font-semibold text-white mb-2">${personagem.nome}</h4>
                <p class="text-sm font-medium text-purple-300 mb-2">${personagem.classe || 'Sem Classe'} / ${personagem.origem || 'Sem Origem'}</p>
                <p class="text-sm text-gray-400 mb-4 flex-grow line-clamp-2">${personagem.descricao}</p>
                <div class="flex justify-end space-x-2 mt-4">
                    <button class="edit-btn px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors" data-id="${personagem.id}">Editar</button>
                    <button class="delete-btn px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors" data-id="${personagem.id}">Excluir</button>
                </div>
            `;
            return card;
        };

        const createAtributoField = (attr) => {
            const container = document.createElement('div');
            container.className = 'flex items-center space-x-2';
            container.innerHTML = `
                <label class="w-1/4 text-white font-medium">${attr.nome}</label>
                <div class="flex-grow flex items-center space-x-2">
                    <span class="text-sm text-gray-400">Base:</span>
                    <button type="button" class="btn-base-down text-red-400 text-2xl font-bold">&ndash;</button>
                    <span class="base-value w-8 text-center">${attr.base_fixa}</span>
                    <button type="button" class="btn-base-up text-green-400 text-2xl font-bold">+</button>
                    <span class="text-sm text-gray-400">Dado:</span>
                    <button type="button" class="btn-dice-down text-red-400 text-2xl font-bold">&ndash;</button>
                    <span class="dice-value w-8 text-center">${attr.dado_fixo}</span>
                    <button type="button" class="btn-dice-up text-green-400 text-2xl font-bold">+</button>
                </div>
            `;

            const baseValueEl = container.querySelector('.base-value');
            const diceValueEl = container.querySelector('.dice-value');

            distributedBasePoints[attr.id] = 0;
            distributedDicePoints[attr.id] = 0;

            container.querySelector('.btn-base-up').addEventListener('click', () => {
                if (availableBasePoints > 0) {
                    distributedBasePoints[attr.id]++;
                    availableBasePoints--;
                    baseValueEl.textContent = attr.base_fixa + distributedBasePoints[attr.id];
                    updatePointsDisplay();
                } else {
                    alert('Pontos Base esgotados!');
                }
            });

            container.querySelector('.btn-base-down').addEventListener('click', () => {
                if (distributedBasePoints[attr.id] > 0) {
                    distributedBasePoints[attr.id]--;
                    availableBasePoints++;
                    baseValueEl.textContent = attr.base_fixa + distributedBasePoints[attr.id];
                    updatePointsDisplay();
                }
            });

            const diceOrder = ['d4', 'd6', 'd8', 'd10', 'd12', 'd20'];
            const initialDiceIndex = diceOrder.indexOf(attr.dado_fixo);

            container.querySelector('.btn-dice-up').addEventListener('click', () => {
                const currentDiceIndex = initialDiceIndex + distributedDicePoints[attr.id];
                if (availableDicePoints > 0 && currentDiceIndex < diceOrder.length - 1) {
                    distributedDicePoints[attr.id]++;
                    availableDicePoints--;
                    diceValueEl.textContent = diceOrder[currentDiceIndex + 1];
                    updatePointsDisplay();
                } else if (currentDiceIndex >= diceOrder.length - 1) {
                    alert('Dado já está no limite!');
                } else {
                    alert('Pontos de Dado esgotados!');
                }
            });

            container.querySelector('.btn-dice-down').addEventListener('click', () => {
                if (distributedDicePoints[attr.id] > 0) {
                    distributedDicePoints[attr.id]--;
                    availableDicePoints++;
                    const currentDiceIndex = initialDiceIndex + distributedDicePoints[attr.id];
                    diceValueEl.textContent = diceOrder[currentDiceIndex];
                    updatePointsDisplay();
                }
            });

            return container;
        };

        // --- Renderização e Carregamento ---
        const loadPersonagens = async (append = true) => {
            if (isLoading || !hasMore) return;
            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    offset = 0;
                    personagensList.innerHTML = '';
                    hasMore = true;
                }

                const personagens = await mockApi.listarPersonagens(offset);
                if (personagens.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        personagensList.innerHTML = `<p class="text-center text-gray-400 col-span-full">Nenhum personagem encontrado. Crie um!</p>`;
                    }
                    return;
                }

                personagens.forEach(o => personagensList.appendChild(createCard(o)));
                offset += personagens.length;
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        };

        const loadFormOptions = async () => {
            const [classes, origens, habilidades, itens] = await Promise.all([
                mockApi.listarClasses(),
                mockApi.listarOrigens(),
                mockApi.listarHabilidades(),
                mockApi.listarItens(),
            ]);

            classeSelect.innerHTML = `<option value="">Selecione uma Classe</option>` + classes.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
            origemSelect.innerHTML = `<option value="">Selecione uma Origem</option>` + origens.map(o => `<option value="${o.id}">${o.nome}</option>`).join('');
            habilidadesSelect.innerHTML = habilidades.map(h => `<option value="${h.slug}">${h.nome}</option>`).join('');
            inventarioSelect.innerHTML = itens.map(i => `<option value="${i.slug}">${i.nome}</option>`).join('');
        };

        // --- Eventos ---
        openModalBtn.addEventListener('click', async () => {
            await loadFormOptions();
            openModal();
        });
        closeModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        // Evento para atualizar atributos quando a classe é selecionada
        classeSelect.addEventListener('change', async (e) => {
            const classeId = e.target.value;
            atributosContainer.innerHTML = ''; // Limpa os campos anteriores
            if (classeId) {
                const classes = await mockApi.listarClasses();
                const selectedClass = classes.find(c => c.id == classeId);
                if (selectedClass) {
                    selectedClass.atributos.forEach(attr => {
                        atributosContainer.appendChild(createAtributoField(attr));
                    });
                }
            }
        });

        // Simulação do formulário de submissão
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Coleta os valores finais dos atributos
            const atributos = {};
            document.querySelectorAll('#atributos-container > div').forEach(item => {
                const nomeAtributo = item.querySelector('label').textContent.trim();
                const baseValue = parseInt(item.querySelector('.base-value').textContent);
                const diceValue = item.querySelector('.dice-value').textContent;
                atributos[nomeAtributo.toLowerCase()] = baseValue;
                atributos[`${nomeAtributo.toLowerCase()}_dado`] = diceValue;
            });

            // Coleta as habilidades e itens selecionados
            const habilidadesSelecionadas = Array.from(habilidadesSelect.selectedOptions).map(option => option.value);
            const itensSelecionados = Array.from(inventarioSelect.selectedOptions).map(option => option.value);

            const payload = {
                ...data,
                atributos: atributos,
                habilidades: habilidadesSelecionadas,
                inventario: itensSelecionados,
                pontos: {
                    base_distribuidos: STARTING_BASE_POINTS - availableBasePoints,
                    dados_distribuidos: STARTING_DICE_POINTS - availableDicePoints,
                }
            };

            console.log("Payload final para a API:", payload);

            closeModal();
            alert('Personagem salvo com sucesso! (Simulação)');
        });

        // Carrega os personagens na inicialização da página
        loadPersonagens();
    </script>
</body>

</html>