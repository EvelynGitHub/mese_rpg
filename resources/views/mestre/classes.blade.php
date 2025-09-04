<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Gerenciar Classes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Scroll personalizado para Webkit (Chrome, Edge, Safari) */
        #classe-modal section {
            max-height: 90vh;

            overflow-y: auto;
            padding-right: 12px;
        }

        #classe-modal section::-webkit-scrollbar {
            width: 8px;
        }

        #classe-modal section::-webkit-scrollbar-track {
            background: #0000000F;
            border-radius: 4px;
            margin: 4px;
        }

        #classe-modal section::-webkit-scrollbar-thumb {
            background-color: #3b82f630;
            /* cor da “pegada” */
            border-radius: 4px;
            border: 2px solid #0000000F;
            /* deixa um espacinho ao redor */
        }

        /* Scroll em Firefox */
        #classe-modal section {
            scrollbar-width: thin;
            scrollbar-color: #3b82f630 #0000000F;
            /* #1e293b #0000000F */
        }

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

        .section-divider {
            height: 1px;
            background-color: #2f3747;
            margin: 2rem 0;
        }

        .modal-overlay {
            visibility: hidden;
            background-color: rgba(0, 0, 0, 0.75);
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
            border-top: 4px solid #6EE7B7;
            opacity: 0;
        }

        .card-animate {
            animation: fadeInCard 0.5s ease-in-out forwards var(--delay);
        }

        #classes-list {
            display: grid;
            opacity: 0;
            animation: fadeInContainer 0.5s ease-in-out forwards;
        }

        @keyframes fadeInContainer {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInCard {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading-animation {
            border-top-color: #3B82F6;
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
                <span class="text-gradient">RPG</span> - Classes
            </h1>
        </header>

        <!-- Container para o conteúdo principal da página -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Gerenciar Classes do Mundo
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Defina classes como Bárbaro ou Mago, com suas regras de atributos e habilidades.
                </p>

                <!-- Botão que abre o modal -->
                <button id="open-modal-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                    Adicionar Nova Classe
                </button>
            </section>

            <!-- Seção de Listagem de Classes em formato de cards -->
            <section class="w-full max-w-7xl mx-auto mt-8">
                <h3 class="text-2xl font-semibold mb-6 text-white">Classes Existentes</h3>
                <div id="classes-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards de classes serão populados aqui via JavaScript -->
                </div>
                <!-- Elemento sentinela para a rolagem infinita -->
                <div id="sentinela" class="h-1 bg-transparent mt-8"></div>
                <div id="loading-indicator" class="flex flex-col items-center text-center p-4 text-gray-400">
                    <div class="loading-animation rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    Carregando mais classes...
                </div>
            </section>

        </main>
    </div>

    <!-- Modal para o Formulário de Classe -->
    <div id="classe-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <section
            class="bg-slate-900 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-200">
                    Crie uma nova Classe
                </h2>
                <p class="text-gray-400">
                    Defina suas regras, atributos e habilidades.
                </p>
                <!-- Botão para fechar o modal -->
                <button type="button" data-close-modal
                    class="absolute top-4 right-4 text-gray-500 hover:text-red-500 transition-colors duration-300 text-4xl">
                    &times;
                </button>
            </div>
            <!-- Formulário da Classe -->
            <form id="classe-form" class="space-y-8">
                <input type="hidden" id="classe-id" name="id">

                <!-- Informações Básicas da Classe -->
                <div class="space-y-6">
                    <h3 class="text-xl font-semibold text-gray-200">Informações da Classe</h3>
                    <!-- Campo de Nome -->
                    <div>
                        <label for="nome" class="block text-gray-400 text-sm font-medium mb-1">Nome da Classe</label>
                        <input type="text" id="nome" name="nome" required
                            class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                            placeholder="Ex: Bárbaro">
                    </div>
                    <!-- Campo de Slug -->
                    <div>
                        <label for="slug" class="block text-gray-400 text-sm font-medium mb-1">Chave (ex: mago,
                            guerreiro) (Identificador Único)</label>
                        <input type="text" id="slug" name="slug" required
                            class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                            placeholder="Ex: barbaro">
                    </div>
                    <!-- Campo de Descrição -->
                    <div>
                        <label for="descricao" class="block text-gray-400 text-sm font-medium mb-1">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="4"
                            class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300"
                            placeholder="Uma breve descrição da classe..."></textarea>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Seção de Atributos -->
                <!-- <div class="space-y-6 bg-slate-900 p-4 rounded-lg border border-slate-700"> -->
                <div class="space-y-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-gray-200">Atributos</h4>
                        <button type="button" id="add-atributo"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-lg transition-transform transform hover:scale-105">
                            + Atributo
                        </button>
                    </div>
                    <div id="atributos-container" class="space-y-4">
                        <!-- Campos de atributos dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Seção de Habilidades -->
                <!-- <div class="space-y-6 bg-slate-900 p-4 rounded-lg border border-slate-700"> -->
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h4 class="text-xl font-semibold text-white">Habilidades da Classe</h4>
                        <button type="button" id="add-habilidade-rule"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-lg transition-transform transform hover:scale-105">
                            + Habilidade
                        </button>
                    </div>
                    <div id="habilidades-container" class="space-y-4">
                        <!-- Campos de habilidades dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <div class="section-divider"></div>
                <!-- Botão de Envio e Cancelamento Principal -->
                <div class="flex justify-between space-x-4 py-3">
                    <button type="button" data-close-modal
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md hover:scale-105">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md hover:scale-105">
                        Salvar Classe
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de Classes, com base no novo esquema

        import { habilidadesService as habilidadesJS } from "../js/api/habilidades.js";
        import { atributosService as atributosJS } from "../js/api/atributos.js";
        import { classesService as classesJS } from "../js/api/classes.js";
        import { dadosService as dadosJS } from "../js/api/dados.js";
        import { notificar, confirmar } from '../js/ui/notificacao.js';

        const mundoIdCriptografado = "{{ $mundo_id }}";
        const atributosService = atributosJS(mundoIdCriptografado);
        const habilidadesService = habilidadesJS(mundoIdCriptografado);
        const classesService = classesJS(mundoIdCriptografado);
        const dadosService = dadosJS();

        // --- Referências DOM e Estado ---
        const classesList = document.getElementById('classes-list');
        const form = document.getElementById('classe-form');
        const classeId = document.getElementById('classe-id');
        const modalOverlay = document.getElementById('classe-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const atributosContainer = document.getElementById('atributos-container');
        const habilidadesContainer = document.getElementById('habilidades-container');
        const addAtributoBtn = document.getElementById('add-atributo');
        const addHabilidadeBtn = document.getElementById('add-habilidade-rule');
        const loadingIndicator = document.getElementById('loading-indicator');

        let offset = 0;
        let isLoading = false;
        let hasMore = true;
        let atributoIndex = 0;
        let habilidadeIndex = 0;
        let cachedFormOptions = null;

        // --- Utilitários ---
        const openModal = () => modalOverlay.classList.add('open');
        const closeModal = () => {
            form.reset();
            classeId.value = '';
            atributosContainer.innerHTML = '';
            habilidadesContainer.innerHTML = '';
            atributoIndex = 0;
            habilidadeIndex = 0;
            modalOverlay.classList.remove('open');
        };

        const createCard = (classe, index) => {
            const card = document.createElement('div');
            card.className = 'card relative';
            card.innerHTML = `
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">🛡️</span>
                    <h4 class="text-xl font-semibold text-white">${classe.nome}</h4>
                </div>
                <p class="text-gray-400 mb-4 flex-grow line-clamp-2">${classe.descricao}</p>
                <div class="flex justify-end space-x-2 mt-4">
                    <button class="edit-btn px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors" data-id="${classe.id}">Editar</button>
                    <button class="delete-btn px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors" data-id="${classe.id}">Excluir</button>
                </div>
            `;

            card.classList.add('card-animate');
            card.style.setProperty('--delay', `${index * 0.1}s`);
            return card;
        };

        const createAtributoField = (atributos, tiposDado) => {
            const container = document.createElement('div');
            // container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-800 p-4 rounded-md relative';
            container.classList.add('space-y-4', 'p-4', 'bg-slate-800', 'rounded-xl', 'border', 'border-slate-700', 'relative');
            container.innerHTML = `
                <button type="button" class="remove-field absolute top-2 right-3 text-gray-500 hover:text-red-500 transition-colors duration-300 text-4xl">&times;</button>
                <!-- Dropdown de Atributos -->
                <div class="col-span-full">
                    <label class="block text-gray-400 text-sm font-medium mb-1" for="attributes[${atributoIndex}][atributo_id]">Atributo</label>
                    <select name="attributes[${atributoIndex}][atributo_id]" required
                            class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione um Atributo</option>
                        ${atributos.map(a => `<option value="${a.id}">${a.nome}</option>`).join('')}
                    </select>
                </div>
                <!-- Campos de Regra do Atributo -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm font-medium mb-1">Dado Inicial</label>
                        <select name="attributes[${atributoIndex}][tipo_dado_id]" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            <option value="">Nenhum</option>
                            ${tiposDado.map(d => `<option value="${d.id}">${d.codigo}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm font-medium mb-1">Limite Dado</label>
                        <select name="attributes[${atributoIndex}][limite_tipo_dado_id]" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            <option value="" disabled selected>Nenhum</option>
                            ${tiposDado.map(d => `<option value="${d.id}">${d.codigo}</option>`).join('')}
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm font-medium mb-1">Base Fixa</label>
                        <input type="number" name="attributes[${atributoIndex}][base_fixa]" value="0" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm font-medium mb-1">Limite Base</label>
                        <input type="number" name="attributes[${atributoIndex}][limite_base_fixa]" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    </div>
                </div>
                <div class="flex items-center mt-2">
                    <label class="ml-2 text-gray-400 text-sm font-medium">
                        <input type="hidden" name="attributes[${atributoIndex}][imutavel]" value="0">
                        <input type="checkbox" name="attributes[${atributoIndex}][imutavel]" checked value='1'
                            class="w-4 h-4 text-blue-600 bg-slate-900 border-slate-700 rounded focus:ring-blue-500">
                        Imutável?
                    </label>
                </div>
            `;
            atributoIndex++;
            return container;
        };

        const createHabilidadeField = (habilidades) => {
            const container = document.createElement('div');
            // container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 bg-slate-800 p-4 rounded-md relative';
            container.classList.add('flex', 'items-center', 'space-x-2', 'p-4', 'bg-slate-800', 'rounded-xl', 'border', 'border-slate-700', 'relative');
            container.innerHTML = `
                <select name="abilities[${habilidadeIndex}][habilidade_id]" required
                    class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                >
                    <option value="" disabled selected>Selecione uma Habilidade</option>
                    ${habilidades.map(h => `<option value="${h.id}">${h.nome}</option>`).join('')}
                </select>
                <button type="button" class="remove-field text-gray-500 hover:text-red-500 transition-colors duration-300 text-4xl">&times;</button>
            `;
            habilidadeIndex++;
            return container;
        };

        const resetarPaginacao = () => {
            offset = 0;
            hasMore = true;
        };

        // --- Renderização e Carregamento ---
        const loadClasses = async (append = true) => {
            if (isLoading || !hasMore) return;

            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    resetarPaginacao()
                    classesList.replaceChildren();
                }

                const classes = await classesService.listarClasses(offset);
                if (classes.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        classesList.innerHTML = `<p class="text-center text-gray-400 col-span-full">Nenhuma classe encontrada. Crie uma!</p>`;
                    }
                    return;
                }

                classes.forEach((c, i) => classesList.appendChild(createCard(c, i)));
                offset += classes.length;
            } catch (error) {
                console.error('Erro na listagem de classes:', error);
                classesList.innerHTML = `
                    <div class="text-center text-red-400 col-span-full">
                        Erro ao carregar classes. Tente novamente.
                    </div>`;
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        };

        const loadFormOptions = async () => {
            if (cachedFormOptions) return cachedFormOptions;

            const [atributos, tiposDado, habilidades] = await Promise.all([
                atributosService.listarAtributos(),
                dadosService.listarDados(),
                habilidadesService.listarHabilidades()
            ]);

            cachedFormOptions = { atributos, tiposDado, habilidades };
            return cachedFormOptions;
        };

        // --- Observer (scroll infinito) ---
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && hasMore && !isLoading) {
                loadClasses(true);
            }
        }, { threshold: 1.0 });

        observer.observe(sentinela);

        // --- Eventos ---
        document.addEventListener('click', (event) => {
            // Delegação única para fechar modal
            if (event.target.hasAttribute('data-close-modal')) {
                closeModal();
            }
            // Evento para remover campos dinâmicos
            if (event.target.classList.contains('remove-field')) {
                event.target.closest('div[class*="bg-slate-800"]').remove();
            }
        });

        openModalBtn.addEventListener('click', async () => {
            await loadClasses(); // Garante que a lista está carregada antes de abrir o modal
            openModal();
        });

        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        addAtributoBtn.addEventListener('click', async () => {
            const { atributos, tiposDado } = await loadFormOptions();
            atributosContainer.appendChild(createAtributoField(atributos, tiposDado));
        });

        addHabilidadeBtn.addEventListener('click', async () => {
            const { habilidades } = await loadFormOptions();
            habilidadesContainer.appendChild(createHabilidadeField(habilidades));
        });

        // Simulação do formulário de submissão
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const id = data.id ? parseInt(data.id) : null;

            // Lógica para coletar os campos dinâmicos e montar o payload
            const atributos = [];
            atributosContainer.querySelectorAll('div[class*="bg-slate-800"]').forEach(item => {
                const attrData = {};
                item.querySelectorAll('input, select').forEach(input => {
                    const name = input.name.split('[')[2].replace(']', '');
                    attrData[name] = input.type === 'checkbox' ? input.checked : input.value;
                });
                atributos.push(attrData);
            });

            const habilidades = [];
            habilidadesContainer.querySelectorAll('div[class*="bg-slate-800"]').forEach(item => {
                const habData = {};
                item.querySelectorAll('select').forEach(input => {
                    const name = input.name.split('[')[2].replace(']', '');
                    habData[name] = input.value;
                });
                habilidades.push(habData);
            });

            const payload = {
                ...data,
                atributos: atributos,
                habilidades: habilidades
            };

            console.log("Payload final para a API:", payload);

            try {
                if (id) {
                    await classesService.atualizarClasse(id, payload);
                    notificar('Classe atualizado com sucesso!');
                } else {
                    await classesService.criarClasse(payload);
                    notificar('Classe salvo com sucesso!');
                }
                resetarPaginacao();
                closeModal();
                await loadClasses(false);
            } catch (error) {
                console.error(error);
                notificar(`Erro ao salvar a classe. ${error.message || ''}`, "erro");
            }
        });

        // Evento para editar e excluir classes
        classesList.addEventListener('click', async (e) => {
            const target = e.target;
            if (target.classList.contains('delete-btn')) {
                const id = parseInt(target.dataset.id);
                const confirmation = await confirmar('Tem certeza que deseja excluir esta classe?');

                if (confirmation) {
                    try {
                        await classesService.excluirClasse(id);
                        notificar(`Classe com ID ${id} excluído.`);
                        resetarPaginacao();
                        await loadClasses(false);
                    } catch (error) {
                        console.error("Erro ao excluir", error);
                        notificar(`Não foi possível excluir a Classe. ${error.message || ''}`, "erro");
                    }
                }
            }

            if (target.classList.contains('edit-btn')) {
                const id = parseInt(target.dataset.id);
                const classe = await classesService.obterClasse(id);

                if (classe) {
                    preencherFormulario(classe);
                    openModal();
                }
            }
        });

        const preencherFormulario = async (classe) => {
            // 1. Resetar formulário
            form.reset();
            atributosContainer.innerHTML = '';
            habilidadesContainer.innerHTML = '';
            atributoIndex = 0;
            habilidadeIndex = 0;

            // 2. Popular campos básicos
            form.querySelector('#classe-id').value = classe.id || '';
            form.querySelector('#nome').value = classe.nome || '';
            form.querySelector('#slug').value = classe.slug || '';
            form.querySelector('#descricao').value = classe.descricao || '';

            // 3. Carregar opções de atributos, tiposDado e habilidades
            const { atributos, tiposDado, habilidades } = await loadFormOptions();

            // 4. Preencher atributos existentes
            if (classe.atributos && classe.atributos.length) {
                classe.atributos.forEach(attr => {
                    const field = createAtributoField(atributos, tiposDado);
                    // Seleciona os elementos do campo
                    const selectAtributo = field.querySelector(`select[name^="attributes"][name$="[atributo_id]"]`);
                    const selectTipoDado = field.querySelector(`select[name^="attributes"][name$="[tipo_dado_id]"]`);
                    const selectLimiteDado = field.querySelector(`select[name^="attributes"][name$="[limite_tipo_dado_id]"]`);
                    const inputBaseFixa = field.querySelector(`input[name^="attributes"][name$="[base_fixa]"]`);
                    const inputLimiteBase = field.querySelector(`input[name^="attributes"][name$="[limite_base_fixa]"]`);
                    const inputImutavel = field.querySelector(`input[type="checkbox"][name^="attributes"][name$="[imutavel]"]`);

                    // Setar valores do atributo
                    if (selectAtributo) selectAtributo.value = attr.atributo_id || '';
                    if (selectTipoDado) selectTipoDado.value = attr.tipo_dado_id || '';
                    if (selectLimiteDado) selectLimiteDado.value = attr.limite_tipo_dado_id || '';
                    if (inputBaseFixa) inputBaseFixa.value = attr.base_fixa ?? 0;
                    if (inputLimiteBase) inputLimiteBase.value = attr.limite_base_fixa ?? '';
                    if (inputImutavel) inputImutavel.checked = !!attr.imutavel;

                    atributosContainer.appendChild(field);
                });
            }

            // 5. Preencher habilidades existentes
            if (classe.habilidades && classe.habilidades.length) {
                classe.habilidades.forEach(hab => {
                    const field = createHabilidadeField(habilidades);
                    const selectHabilidade = field.querySelector(`select[name^="abilities"][name$="[habilidade_id]"]`);
                    if (selectHabilidade) selectHabilidade.value = hab.habilidade_id || '';
                    habilidadesContainer.appendChild(field);
                });
            }
        };

        // Carrega as classes na inicialização da página
        // loadClasses();
    </script>
</body>

</html>