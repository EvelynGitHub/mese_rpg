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
            background-clip: text;
            background-image: linear-gradient(to right, #6EE7B7, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-overlay {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s 0.3s, opacity 0.3s ease-in-out;
        }

        .section-divider {
            height: 1px;
            background-color: #2f3747;
            margin: 2rem 0;
        }

        /* Estilo para a caixa de input numérica com botões */
        .number-input-group input {
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .number-input-group input::-webkit-outer-spin-button,
        .number-input-group input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
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

        /* Novo estilo para as opções selecionadas dinamicamente */
        #habilidade-custom-select option[data-selected] {
            font-weight: bold;
            color: #6EE7B7;
            /* Cor de destaque */
            background-color: #2f3747;
        }

        /* Estilo para a lista de habilidades adicionadas */
        .ability-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #1c2430;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #2f3747;
        }

        .ability-item h4 {
            font-size: 1rem;
            font-weight: 500;
            color: #F9A8D4;
            flex-grow: 1;
        }

        .ability-item input {
            width: 50px;
            margin-left: 0.5rem;
        }
    </style>
</head>

<body class="antialiased">

    <div class="min-h-screen flex flex-col items-center p-4 md:p-8">

        <header class="w-full max-w-7xl flex justify-between items-center py-6 px-4">
            <a href="/painel-mestre/{{ $mundo_id }}"
                class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
                &larr; Voltar para o Painel do Mestre
            </a>
            <h1 class="text-3xl font-bold">
                <span class="text-gradient">RPG</span> - NPCs
            </h1>
        </header>

        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Gerenciar NPCs do Mundo
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Crie e edite os personagens não-jogáveis que habitam o seu mundo.
                </p>

                <button id="open-modal-btn"
                    class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                    Adicionar Novo NPC
                </button>
            </section>

            <section class="w-full max-w-7xl mx-auto mt-8">
                <h3 class="text-2xl font-semibold mb-6 text-white">NPCs Existentes</h3>
                <div id="npcs-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                </div>
                <div id="sentinela" class="h-1 bg-transparent mt-8"></div>
                <div id="loading-indicator" class="flex flex-col items-center text-center p-4 text-gray-400">
                    <div class="loading-animation rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    Carregando mais NPCs...
                </div>
            </section>
        </main>
    </div>

    <div id="npc-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <section
            class="bg-slate-900 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <div class="text-center mb-6">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-200">
                    <span class="text-gradient">Crie seu NPCs</span>
                </h1>
                <p class="text-gray-400">
                    Comece a sua aventura definindo as bases do seu herói.
                </p>
            </div>
            <form id="npc-form" class="space-y-6">
                <h3 class="text-xl font-semibold text-gray-200">Informações Básicas</h3>
                <input type="hidden" id="npc-id" name="id">

                <div class="space-y-6">
                    <div>
                        <label for="nome" class="block text-gray-400 text-sm font-medium mb-1">Nome do NPC</label>
                        <input type="text" id="nome" name="nome" required
                            class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                            placeholder="Ex: Barman do Canto Obscuro">
                    </div>
                    <div>
                        <label for="descricao" class="block text-gray-400 text-sm font-medium mb-1">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="4"
                            class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                            placeholder="Uma breve descrição do NPC..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="classe" class="block text-gray-400 text-sm font-medium mb-1">Classe apresentada
                                aos
                                jogadores</label>
                            <input type="text" id="classe" name="classe"
                                class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                                placeholder="Ex: Guerreiro, Mago (opcional)">
                        </div>
                        <div>
                            <label for="origem" class="block text-gray-400 text-sm font-medium mb-1">Origem apresentada
                                aos
                                jogadores</label>
                            <input type="text" id="origem" name="origem"
                                class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                                placeholder="Ex: Humano, Elfo (opcional)">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="npc-classe" class="block text-gray-400 text-sm font-medium mb-1">Classe</label>
                            <select id="npc-classe" name="classe_id" data-select-type="related-abilities"
                                class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                                <option value="" disabled selected>Selecione uma Classe</option>
                            </select>
                        </div>
                        <div>
                            <label for="npc-origem" class="block text-gray-400 text-sm font-medium mb-1">Origem</label>
                            <select id="npc-origem" name="origem_id" data-select-type="related-abilities"
                                class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                                <option value="" disabled selected>Selecione uma Origem</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="section-divider"></div>
                <div class="space-y-6">
                    <div class="flex justify-between items-center hidden">
                        <h3 class="text-xl font-semibold text-gray-200">Distribuição de Pontos</h3>
                        <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-2 sm:space-y-0">
                            <p class="text-gray-400 text-lg">Base: <span id="available-base-points"
                                    class="font-bold text-green-400">0</span></p>
                            <p class="text-gray-400 text-lg">Dado: <span id="available-dice-points"
                                    class="font-bold text-green-400">0</span></p>
                        </div>
                    </div>
                    <div class="flex justify-between space-x-4 hidden">
                        <button type="button" id="add-atributo-btn"
                            class="mt-4 w-full bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors text-lg">
                            + Adicionar Atributo Personalizado
                        </button>
                    </div>
                    <div class="space-y-6">
                        <h3 class="text-xl font-semibold text-gray-200">Atributos Finais</h3>
                        <div>
                            <label for="atributos_json" class="block text-gray-400 text-sm font-medium mb-1">Atributos
                                JSON</label>
                            <textarea id="atributos_json" name="atributos_json" rows="4"
                                class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                                placeholder="{ vida: { atual: 15, max: 15 }, forca : 8, destreza : 12 }"></textarea>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-semibold text-gray-200">Habilidades</h3>
                        <div class="flex items-end space-x-4">
                            <div class="flex-1">
                                <label for="habilidade-custom-select"
                                    class="block text-gray-400 text-sm font-medium mb-1">Adicionar Habilidade
                                    Personalizada</label>
                                <select id="habilidade-custom-select"
                                    class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                                    <option value="" disabled selected>Selecione uma habilidade...</option>
                                </select>
                            </div>
                            <button type="button" id="add-ability-btn"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                Adicionar
                            </button>
                        </div>
                        <div id="abilities-list" class="space-y-3">
                        </div>
                        <input type="hidden" name="habilidades_json" id="habilidades_json">
                    </div>

                </div>

                <div class="section-divider"></div>

                <div>
                    <label for="inventario_json" class="block text-gray-400 text-sm font-medium mb-1">Inventário
                        JSON</label>
                    <textarea id="inventario_json" name="inventario_json" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-colors"
                        placeholder="[
                                {
                                    tipo: 'generico',
                                    nome: 'Espada Longa',
                                    item_id: null
                                },
                                {
                                    tipo: 'especifico',
                                    item_id: 2
                                }
                            ]"></textarea>
                </div>

                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-white">Inventário</h4>
                        <div>

                        </div>
                        <button type="button" id="add-inventario-btn"
                            class="text-yellow-400 hover:text-yellow-500 font-bold text-lg transition-colors">
                            + Adicionar Item
                        </button>
                    </div>

                    <div id="npc-itens" class="space-y-2 hidden">
                        <h5 class="text-gray-300 font-medium">Itens sugeridos pela Origem</h5>
                        <div id="inventario-container" class="space-y-4 flex flex-wrap gap-2">
                        </div>
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
        import { habilidadesService as habilidadesJS } from "../js/api/habilidades.js";
        import { atributosService as atributosJS } from "../js/api/atributos.js";
        import { origensService as origensJS } from "../js/api/origens.js";
        import { classesService as classesJS } from "../js/api/classes.js";
        import { itensService as itensJS } from "../js/api/itens.js";
        import { npcsService as npcsJS } from "../js/api/npcs.js";
        import { dadosService } from "../js/api/dados.js";
        import { notificar, confirmar } from '../js/ui/notificacao.js';
        import { preencherFormulario } from '../js/ui/form.js';

        const mundoIdCriptografado = "{{ $mundo_id }}";
        const atributosService = atributosJS(mundoIdCriptografado);
        const habilidadesService = habilidadesJS(mundoIdCriptografado);
        const origensService = origensJS(mundoIdCriptografado);
        const classesService = classesJS(mundoIdCriptografado);
        const itensService = itensJS(mundoIdCriptografado);
        const npcsService = npcsJS(mundoIdCriptografado);

        // --- Referências DOM e Estado ---
        // Elementos dinâmicos
        const npcsList = document.getElementById('npcs-list');
        const classeSelect = document.getElementById('npc-classe');
        const origemSelect = document.getElementById('npc-origem');
        const itensBlock = document.getElementById('npc-itens');
        const inventarioContainer = document.getElementById('inventario-container');
        const relatedAbilitySelects = document.querySelectorAll('[data-select-type="related-abilities"]');
        const habilidadeCustomSelect = document.getElementById('habilidade-custom-select');
        const addAbilityBtn = document.getElementById('add-ability-btn');
        const abilitiesList = document.getElementById('abilities-list');

        const form = document.getElementById('npc-form');
        const npcId = document.getElementById('npc-id');
        const modalOverlay = document.getElementById('npc-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const loadingIndicator = document.getElementById('loading-indicator');
        const sentinela = document.getElementById('sentinela');

        let offset = 0;
        let isLoading = false;
        let hasMore = true;
        let cachedFormOptions = null;

        // --- Utilitários ---
        const resetarPaginacao = () => {
            offset = 0;
            hasMore = true;
        };
        const openModal = () => modalOverlay.classList.add('open');
        const closeModal = () => {
            form.reset();
            npcId.value = '';
            abilitiesList.innerHTML = '';
            habilidadeCustomSelect.querySelectorAll('option').forEach(option => {
                option.removeAttribute('data-selected');
            });
            modalOverlay.classList.remove('open');
        };

        const createCard = (npc) => {
            console.log("NPC", npc);

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

        // --- Renderização e Carregamento ---
        const loadNpcs = async (append = true) => {
            if (isLoading || !hasMore) return;
            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    resetarPaginacao()
                    npcsList.replaceChildren();
                }

                const npcs = await npcsService.listarNpcs(offset);
                if (npcs.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        npcsList.innerHTML = `<p class="text-center text-gray-400 col-span-full">Nenhum NPC encontrado. Crie um!</p>`;
                    }
                    return;
                }

                npcs.forEach(o => npcsList.appendChild(createCard(o)));
                offset += npcs.length;
            } catch (error) {
                console.error('Erro na listagem de NPCs:', error);
                npcsList.innerHTML = `
                    <div class="text-center text-red-400 col-span-full">
                        Erro ao carregar NPCs. Tente novamente.
                    </div>`;
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        };

        const loadFormOptions = async () => {
            if (cachedFormOptions) return cachedFormOptions;

            const [
                habilidades, itens, atributos, classes, origens, dados
            ] = await Promise.all([
                habilidadesService.listarHabilidades(),
                itensService.listarItens(),
                atributosService.listarAtributos(),
                classesService.listarClasses(),
                origensService.listarOrigens(),
                dadosService().listarDados()
            ]);
            cachedFormOptions = {
                habilidades, itens, atributos, classes, origens, dados
            };
            return cachedFormOptions;
        };

        // --- Observer (scroll infinito) ---
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && hasMore && !isLoading) {
                loadNpcs(true);
            }
        }, { threshold: 1.0 });

        observer.observe(sentinela);

        // --- Eventos ---
        openModalBtn.addEventListener('click', () => {
            openModal();
            populateSelects();
        });
        closeModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        // Simulação do formulário de submissão
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const id = data.id ? parseInt(data.id) : null;

            // Coleta os dados das habilidades dinâmicas
            const abilitiesData = collectAbilitiesData();

            const atributos = document.getElementById('atributos_json').value ?? "{}";
            const inventario = document.getElementById('inventario_json').value;

            const payload = {
                ...data,
                atributos: `{ "atributos": ${atributos || []}, "habilidades": ${JSON.stringify(abilitiesData || [])} }`,
                inventario: inventario,
            };

            try {
                console.log("Payload final para a API:", payload);
                if (id) {
                    await npcsService.atualizarNpc(id, payload);
                    notificar(`NPC "${payload.nome}" atualizado com sucesso!`, "success");
                } else {
                    await npcsService.criarNpc(payload);
                    notificar(`NPC "${payload.nome}" criado com sucesso!`, "success");
                }
                closeModal();
                resetarPaginacao();
                await loadNpcs(false);
            } catch (error) {
                console.error("Erro ao salvar NPC:", error);
                notificar("Erro ao salvar NPC. Tente novamente.", "erro");
                return;
            }
        });


        // Dados dinâmicos do Formulário
        /**
         * Preenche os seletores com os dados simulados.
         */
        function populateSelects() {
            // Limpa os selects para evitar duplicatas em reaberturas do modal
            classeSelect.innerHTML = '<option value="" disabled selected>Selecione uma Classe</option>';
            origemSelect.innerHTML = '<option value="" disabled selected>Selecione uma Origem</option>';
            habilidadeCustomSelect.innerHTML = '<option value="" disabled selected>Selecione uma habilidade...</option>';

            cachedFormOptions.origens.forEach(origem => {
                const option = new Option(origem.nome, origem.id);
                // Adiciona o atributo de dados para a lógica de habilidades
                if (origem.habilidades) {
                    option.dataset.habilidades = origem.habilidades.map(h => h.habilidade_id).join(',');
                }
                origemSelect.add(option);
            });
            cachedFormOptions.classes.forEach(charClass => {
                const option = new Option(charClass.nome, charClass.id);
                // Adiciona o atributo de dados para a lógica de habilidades
                if (charClass.habilidades) {
                    option.dataset.habilidades = charClass.habilidades.map(h => h.habilidade_id).join(',');
                }
                classeSelect.add(option);
            });

            // Preenche o select de habilidades customizadas com todas as habilidades
            cachedFormOptions.habilidades.forEach(habilidade => {
                const option = new Option(habilidade.nome, habilidade.id);
                habilidadeCustomSelect.add(option);
            });
        }

        /**
        * Adiciona os atributos da classe selecionada no formulário.
        * @param {object} classe - o objeto da classe.
        */
        function renderAttributeDistribution(classe) {
            let attributes = classe.atributos || [];
            let attributesFormated = {};
            attributes.forEach(attr => {
                const slug = cachedFormOptions.atributos.find(a => a.id == attr.atributo_id)?.chave;
                if (slug) {
                    const dado = cachedFormOptions.dados.find(d => d.id == attr.tipo_dado_id);
                    attributesFormated[slug] = {//attributesFormated[attr.atributo_id] = {}
                        editavel: attr.imutavel || false,
                        valor_atual: attr.base_fixa || 0, // OU `${slug}_valor`
                        valor_max: attr.limite_base_fixa || 100, // Ou outro valor configudo via input/interface pelo mestre
                        dado_atual: dado?.codigo
                    }
                }
            });

            const textareaAtributos = document.getElementById('atributos_json');
            textareaAtributos.value = JSON.stringify(attributesFormated, null, 2);
        }

        /**
         * Adiciona uma nova habilidade à lista no DOM.
         * @param {string} id - O ID da habilidade.
         * @param {string} nome - O nome da habilidade.
         * @param {number} nivel - O nível da habilidade.
         * @param {number} usos - O número de usos da habilidade.
         */
        function addAbilityToForm(id, nome, nivel = 1, usos = 3) {
            // Evita duplicatas na lista de habilidades
            if (document.querySelector(`[data-habilidade-id="${id}"]`)) {
                return;
            }

            const abilityItem = document.createElement('div');
            abilityItem.classList.add('ability-item');
            abilityItem.dataset.habilidadeId = id;
            abilityItem.dataset.habilidadeNome = nome;

            abilityItem.innerHTML = `
                <h4>${nome}</h4>
                <div class="flex items-center space-x-2">
                    <label class="text-sm">Nível: <input type="number" data-ability-prop="nivel" value="${nivel}" min="1" class="w-16 px-2 py-1 rounded-lg bg-slate-700 border border-slate-600"></label>
                    <label class="text-sm">Usos: <input type="number" data-ability-prop="usos" value="${usos}" min="0" class="w-16 px-2 py-1 rounded-lg bg-slate-700 border border-slate-600"></label>
                    <button type="button" data-action="remove" class="text-red-400 hover:text-red-500 transition-colors">Remover</button>
                </div>
            `;

            abilitiesList.appendChild(abilityItem);

            abilityItem.querySelector('[data-action="remove"]').addEventListener('click', () => {
                abilityItem.remove();
                updateJsonInput();
            });

            abilityItem.querySelectorAll('input').forEach(input => {
                input.addEventListener('change', updateJsonInput);
            });

            updateJsonInput();
        }

        /**
         * Atualiza o campo hidden com o JSON das habilidades.
         */
        function updateJsonInput() {
            const abilitiesData = collectAbilitiesData();
            document.getElementById('habilidades_json').value = JSON.stringify(abilitiesData, null, 2);
        }

        /**
         * Coleta todos os dados das habilidades da interface.
         */
        function collectAbilitiesData() {
            const selectedAbilities = [];
            abilitiesList.querySelectorAll('[data-habilidade-id]').forEach(item => {
                const id = item.dataset.habilidadeId;
                const nivel = item.querySelector('[data-ability-prop="nivel"]').value;
                const usos = item.querySelector('[data-ability-prop="usos"]').value;

                selectedAbilities.push({
                    habilidade_id: parseInt(id, 10),
                    nivel: parseInt(nivel, 10),
                    usos: {
                        atual: parseInt(usos, 10),
                        max: parseInt(usos, 10)
                    },
                    modificadores_extra: {}
                });
            });
            return selectedAbilities;
        }

        /**
         * Renderiza as habilidades de uma classe/origem na lista do formulário.
         * @param {Array} abilities - Array de habilidades.
         */
        function renderAbilitiesDistribution(abilities = []) {
            // Limpa a lista antes de adicionar as novas habilidades
            abilitiesList.innerHTML = '';

            // Cria um Set para evitar duplicatas de habilidades na mesma chamada
            const processedIds = new Set();

            abilities.forEach(abi => {
                const habilidade = cachedFormOptions.habilidades.find(h => h.id == abi.habilidade_id);

                if (habilidade && !processedIds.has(habilidade.id)) {
                    // Pega os usos padrão da habilidade
                    const usos = habilidade?.bonus?.usos || { atual: 3, max: 3 };

                    addAbilityToForm(
                        habilidade.id,
                        habilidade.nome,
                        1, // Nível 1 por padrão para as habilidades
                        usos.max
                    );
                    processedIds.add(habilidade.id);
                }
            });

            // Atualiza o JSON final
            updateJsonInput();
        }

        // Lógica para adicionar ou remover destaques usando atributos de dados
        function updateHighlights() {
            // Remove o atributo 'data-selected' de todas as opções
            habilidadeCustomSelect.querySelectorAll('option').forEach(option => {
                option.removeAttribute('data-selected');
            });

            const selectedClassOption = classeSelect.options[classeSelect.selectedIndex];
            const selectedOrigemOption = origemSelect.options[origemSelect.selectedIndex];

            const classAbilityIds = selectedClassOption.dataset.habilidades?.split(',') || [];
            const origemAbilityIds = selectedOrigemOption.dataset.habilidades?.split(',') || [];

            const allRelatedIds = new Set([...classAbilityIds, ...origemAbilityIds]);

            allRelatedIds.forEach(id => {
                const optionToHighlight = habilidadeCustomSelect.querySelector(`option[value="${id}"]`);
                if (optionToHighlight) {
                    optionToHighlight.setAttribute('data-selected', 'true');
                }
            });
        }

        // Adiciona event listener para a mudança de classe
        classeSelect.addEventListener('change', (e) => {
            const selectedClass = cachedFormOptions.classes.find(c => c.id == e.target.value);
            renderAttributeDistribution(selectedClass);

            const selectedAbilities = [
                ...(selectedClass.habilidades || []),
                ...(cachedFormOptions.origens.find(o => o.id == origemSelect.value)?.habilidades || [])
            ];
            renderAbilitiesDistribution(selectedAbilities);
            updateHighlights();
        });


        origemSelect.addEventListener('change', () => {
            inventarioContainer.innerHTML = '';
            const selected = cachedFormOptions.origens.find(o => o.id == origemSelect.value);

            if (selected && selected.itens?.length) {
                itensBlock.classList.remove('hidden');
                selected.itens.forEach(i => {
                    const label = document.createElement('label');
                    label.className = 'badge-option';
                    label.setAttribute("for", "itens[]")
                    label.innerHTML = `<input type="checkbox" name="itens[]" value="${i}">${i}`;
                    label.addEventListener('click', () => label.classList.toggle('active'));
                    inventarioContainer.appendChild(label);
                });
            }

            const selectedAbilities = [
                ...(cachedFormOptions.classes.find(c => c.id == classeSelect.value)?.habilidades || []),
                ...(selected?.habilidades || [])
            ];
            renderAbilitiesDistribution(selectedAbilities);
            updateHighlights();
        });

        // Botão para adicionar a habilidade selecionada no select customizado
        addAbilityBtn.addEventListener('click', () => {
            const selectedOption = habilidadeCustomSelect.options[habilidadeCustomSelect.selectedIndex];
            if (selectedOption.value) {
                const habilidade = cachedFormOptions.habilidades.find(h => h.id == selectedOption.value);
                const usos = habilidade?.bonus?.usos || { atual: 3, max: 3 };
                addAbilityToForm(selectedOption.value, selectedOption.textContent, 1, usos.max);
            }
        });

        // Inicializa o formulário ao carregar a página
        document.addEventListener('DOMContentLoaded', async () => {
            await loadFormOptions();
            populateSelects();
            await loadNpcs(false);
        });

        // Evento para editar e excluir origens
        npcsList.addEventListener('click', async (e) => {
            const target = e.target;
            if (target.classList.contains('delete-btn')) {
                const id = parseInt(target.dataset.id);
                const confirmation = await confirmar('Tem certeza que deseja excluir este NPC?');

                if (confirmation) {
                    try {
                        await npcsService.excluirNpc(id);
                        notificar(`Npc com ID ${id} excluído.`, "success");
                        resetarPaginacao();
                        await loadNpcs(false);
                    } catch (error) {
                        console.error("Erro ao excluir", error);
                        notificar(`Não foi possível excluir a Npc. ${error.message || ''}`, "erro");
                    }
                }
            }

            if (target.classList.contains('edit-btn')) {
                const id = parseInt(target.dataset.id);
                const npc = await npcsService.obterNpc(id);

                if (npc) {
                    preencherFormulario(npc, 'npc-form', preencherPropriedades);
                    openModal();
                }
            }
        });

        const preencherPropriedades = (valor, form, chave) => {
            if (chave === "atributos") {
                const textareaAtributos = document.getElementById('atributos_json');
                textareaAtributos.value = JSON.stringify(valor.atributos, null, 2);

                // Limpa a lista de habilidades antes de renderizar as novas
                abilitiesList.innerHTML = '';
                if (valor.habilidades && Array.isArray(valor.habilidades)) {
                    valor.habilidades.forEach(h => {
                        const habilidade = cachedFormOptions.habilidades.find(item => item.id == h.habilidade_id);
                        if (habilidade) {
                            addAbilityToForm(h.habilidade_id, habilidade.nome, h.nivel, h.usos.max);
                        }
                    });
                }
            }
        };
    </script>
</body>

</html>