<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Gerenciar Origens</title>
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
            border-top: 4px solid #6EE7B7;
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
                <span class="text-gradient">RPG</span> - Origens
            </h1>
        </header>

        <!-- Container para o conteúdo principal da página -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Gerenciar Origens do Mundo
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Defina origens como Humano ou Elfo (ou regiões com Deserto e Floresta), com habilidades e efeitos
                    que moldam a história.
                </p>

                <!-- Botão que abre o modal -->
                <button id="open-modal-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                    Adicionar Nova Origem
                </button>
            </section>

            <!-- Seção de Listagem de Origens em formato de cards -->
            <section class="w-full max-w-7xl mx-auto mt-8">
                <h3 class="text-2xl font-semibold mb-6 text-white">Origens Existentes</h3>
                <div id="origens-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards de origens serão populados aqui via JavaScript -->
                </div>
                <!-- Elemento sentinela para a rolagem infinita -->
                <div id="sentinela" class="h-1 bg-transparent mt-8"></div>
                <div id="loading-indicator" class="flex flex-col items-center text-center p-4 text-gray-400">
                    <div class="loading-animation rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    Carregando mais origens...
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para o Formulário de Origem -->
    <div id="origem-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <section
            class="bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-200">
                    Crie uma nova Origem
                </h2>
                <p class="text-gray-400">
                    Defina o que te torna único por vim desse lugar
                </p>
                <!-- Botão para fechar o modal -->
                <button type="button" data-close-modal
                    class="absolute top-4 right-4 text-gray-500 hover:text-red-500 transition-colors duration-300 text-4xl">
                    &times;
                </button>
            </div>

            <form id="origem-form" class="space-y-6">
                <input type="hidden" id="origem-id" name="id">

                <!-- Informações Básicas da Origem -->
                <div>
                    <label for="nome" class="block text-white font-medium mb-2">Nome da Origem</label>
                    <input type="text" id="nome" name="nome" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: Humano">
                </div>
                <div>
                    <label for="slug" class="block text-white font-medium mb-2">Slug (Identificador Único)</label>
                    <input type="text" id="slug" name="slug" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: humano">
                </div>
                <div>
                    <label for="descricao" class="block text-white font-medium mb-2">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Uma breve descrição da origem..."></textarea>
                </div>

                <!-- Seção de Habilidades da Origem (Tabela origens_habilidades) -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-white">Habilidades</h4>
                        <button type="button" id="add-habilidade-rule"
                            class="text-green-400 hover:text-green-500 font-bold text-lg transition-colors">
                            + Adicionar Habilidade
                        </button>
                    </div>
                    <div id="habilidades-container" class="space-y-4">
                        <!-- Campos de habilidades dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <!-- Seção de Efeitos da Origem (Tabela origens_efeitos) -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-white">Efeitos</h4>
                        <button type="button" id="add-efeito-rule"
                            class="text-green-400 hover:text-green-500 font-bold text-lg transition-colors">
                            + Adicionar Efeito
                        </button>
                    </div>
                    <div id="efeitos-container" class="space-y-4">
                        <!-- Campos de efeitos dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <div class="flex justify-between space-x-4">
                    <button type="button" data-close-modal
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Salvar Origem
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de Origens, com base no novo esquema
        import { habilidadesService as habilidadesJS } from "../js/api/habilidades.js";
        import { atributosService as atributosJS } from "../js/api/atributos.js";
        import { origensService as origensJS } from "../js/api/origens.js";
        import { notificar, confirmar } from '../js/ui/notificacao.js';

        const mundoIdCriptografado = "{{ $mundo_id }}";
        const atributosService = atributosJS(mundoIdCriptografado);
        const habilidadesService = habilidadesJS(mundoIdCriptografado);
        const origensService = origensJS(mundoIdCriptografado);

        // --- Referências DOM e Estado ---
        const origensList = document.getElementById('origens-list');
        const form = document.getElementById('origem-form');
        const origemId = document.getElementById('origem-id');
        const modalOverlay = document.getElementById('origem-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const habilidadesContainer = document.getElementById('habilidades-container');
        const efeitosContainer = document.getElementById('efeitos-container');
        const addHabilidadeBtn = document.getElementById('add-habilidade-rule');
        const addEfeitoBtn = document.getElementById('add-efeito-rule');
        const loadingIndicator = document.getElementById('loading-indicator');

        let offset = 0;
        let isLoading = false;
        let hasMore = true;
        let habilidadeIndex = 0;
        let efeitoIndex = 0;
        let cachedFormOptions = null;

        // --- Mock de Dados do Servidor ---
        const mockApi = {
            // listarOrigens: async (offset) => {
            //     const dadosSimulados = [
            //         { id: 1, nome: 'Humano', slug: 'humano', descricao: 'O mais comum e adaptável de todos os povos.' },
            //         { id: 2, nome: 'Elfo', slug: 'elfo', descricao: 'Criaturas esbeltas e de longa vida.' },
            //         { id: 3, nome: 'Anão', slug: 'anao', descricao: 'Povo robusto e hábil em metalurgia.' },
            //         { id: 4, nome: 'Meio-Orc', slug: 'meio-orc', descricao: 'Uma vida de conflito entre duas culturas.' },
            //     ];
            //     await new Promise(resolve => setTimeout(resolve, 500));
            //     return dadosSimulados.slice(offset, offset + 4);
            // },
            // listarHabilidades: async () => {
            //     // Simula a tabela `habilidades`
            //     await new Promise(resolve => setTimeout(resolve, 200));
            //     return [
            //         { id: 1, nome: 'Fúria de Bárbaro' },
            //         { id: 2, nome: 'Bola de Fogo' },
            //         { id: 3, nome: 'Invisibilidade' },
            //         { id: 4, nome: 'Visão no Escuro' },
            //     ];
            // },
            // listarAtributos: async () => {
            //     // Simula a tabela `atributos`
            //     await new Promise(resolve => setTimeout(resolve, 200));
            //     return [
            //         { id: 1, nome: 'Força' },
            //         { id: 2, nome: 'Inteligência' },
            //         { id: 3, nome: 'Destreza' },
            //         { id: 4, nome: 'Carisma' },
            //         { id: 5, nome: 'Constituição' },
            //     ];
            // },
            listarItens: async () => {
                // Simula a tabela `itens`
                await new Promise(resolve => setTimeout(resolve, 200));
                return [
                    { id: 1, nome: 'Espada Longa' },
                    { id: 2, nome: 'Poção de Cura' },
                    { id: 3, nome: 'Anel de Proteção' },
                ];
            }
        };

        // --- Utilitários ---
        const openModal = () => modalOverlay.classList.add('open');
        const closeModal = () => {
            form.reset();
            origemId.valeu = '';
            habilidadesContainer.innerHTML = '';
            efeitosContainer.innerHTML = '';
            habilidadeIndex = 0;
            efeitoIndex = 0;
            modalOverlay.classList.remove('open');
        };

        const createCard = (origem, index) => {
            const card = document.createElement('div');
            card.className = 'card relative';
            card.innerHTML = `
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">🌬️</span>
                    <h4 class="text-xl font-semibold text-white">${origem.nome}</h4>
                </div>
                <p class="text-sm text-gray-400 mb-4 flex-grow line-clamp-2">${origem.descricao}</p>
                <div class="flex justify-end space-x-2 mt-4">
                    <button class="edit-btn px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors" data-id="${origem.id}">Editar</button>
                    <button class="delete-btn px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors" data-id="${origem.id}">Excluir</button>
                </div>
            `;
            card.classList.add('card-animate');
            card.style.setProperty('--delay', `${index * 0.1}s`);
            return card;
        };

        const createHabilidadeField = (habilidades) => {
            const container = document.createElement('div');
            container.className = 'flex items-center space-x-2 bg-slate-800 p-3 rounded-md relative';
            // container.innerHTML = `
            //     <button type="button" class="remove-field absolute top-2 right-2 text-red-400 hover:text-red-500">&times;</button>
            //     <div class="flex-grow">
            //         <label class="sr-only">Habilidade</label>
            //         <select name="abilities[${habilidadeIndex}][habilidade_id]" required class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
            //             <option value="">Selecione uma Habilidade</option>
            //             ${habilidades.map(h => `<option value="${h.id}">${h.nome}</option>`).join('')}
            //         </select>
            //     </div>
            // `;
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

        const createEfeitoField = (atributos, habilidades, itens) => {
            const container = document.createElement('div');
            container.className = 'flex flex-col space-y-4 bg-slate-800 p-4 rounded-md relative';
            container.innerHTML = `
                <button type="button" class="remove-field absolute top-2 right-2 text-red-400 hover:text-red-500">&times;</button>
                <div>
                    <label class="block text-white font-medium mb-1">Tipo de Efeito</label>
                    <select name="effects[${efeitoIndex}][tipo]" required class="tipo-efeito w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <option value="">Selecione o Tipo</option>
                        <option value="delta_atributo">Delta de Atributo</option>
                        <option value="conceder_habilidade">Conceder Habilidade</option>
                        <option value="conceder_item">Conceder Item</option>
                        <option value="custom">Efeito Personalizado</option>
                    </select>
                </div>
                <div class="detalhes-efeito space-y-4">
                    <!-- Detalhes dinâmicos do efeito serão inseridos aqui -->
                </div>
            `;

            const tipoEfeitoSelect = container.querySelector('.tipo-efeito');
            const detalhesContainer = container.querySelector('.detalhes-efeito');

            tipoEfeitoSelect.addEventListener('change', () => {
                detalhesContainer.innerHTML = ''; // Limpa os campos anteriores
                const tipo = tipoEfeitoSelect.value;
                let htmlContent = '';

                if (tipo === 'delta_atributo') {
                    htmlContent = `
                        <div>
                            <label class="block text-white font-medium mb-1">Atributo</label>
                            <select name="effects[${efeitoIndex}][atributo_id]" required class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <option value="">Selecione um Atributo</option>
                                ${atributos.map(a => `<option value="${a.id}">${a.nome}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-white font-medium mb-1">Delta (Valor)</label>
                            <input type="number" name="effects[${efeitoIndex}][delta]" required class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        </div>
                    `;
                } else if (tipo === 'conceder_habilidade') {
                    htmlContent = `
                        <div>
                            <label class="block text-white font-medium mb-1">Habilidade</label>
                            <select name="effects[${efeitoIndex}][habilidade_id]" required class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <option value="">Selecione uma Habilidade</option>
                                ${habilidades.map(h => `<option value="${h.id}">${h.nome}</option>`).join('')}
                            </select>
                        </div>
                    `;
                } else if (tipo === 'conceder_item') {
                    htmlContent = `
                        <div>
                            <label class="block text-white font-medium mb-1">Item</label>
                            <select name="effects[${efeitoIndex}][item_id]" required class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <option value="">Selecione um Item</option>
                                ${itens.map(i => `<option value="${i.id}">${i.nome}</option>`).join('')}
                            </select>
                        </div>
                    `;
                } else if (tipo === 'custom') {
                    htmlContent = `
                        <div>
                            <label class="block text-white font-medium mb-1">Notas (JSON)</label>
                            <textarea name="effects[${efeitoIndex}][notas]" rows="3" class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors" placeholder="Ex: {'regra': 'somente a noite'}"></textarea>
                        </div>
                    `;
                }

                detalhesContainer.innerHTML = htmlContent;
            });

            efeitoIndex++;
            return container;
        };

        const resetarPaginacao = () => {
            offset = 0;
            hasMore = true;
        };
        // --- Renderização e Carregamento ---
        const loadOrigens = async (append = true) => {
            if (isLoading || !hasMore) return;

            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    resetarPaginacao()
                    origensList.replaceChildren();
                }

                const origens = await origensService.listarOrigens(offset);
                if (origens.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        origensList.innerHTML = `<p class="text-center text-gray-400 col-span-full">Nenhuma origem encontrada. Crie uma!</p>`;
                    }
                    return;
                }

                origens.forEach((o, i) => origensList.appendChild(createCard(o, i)));
                offset += origens.length;
            } catch (error) {
                console.error('Erro na listagem de origens:', error);
                origensList.innerHTML = `
                    <div class="text-center text-red-400 col-span-full">
                        Erro ao carregar origens. Tente novamente.
                    </div>`;
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        };

        const loadFormOptions = async () => {
            if (cachedFormOptions) return cachedFormOptions;

            const [habilidades, atributos, itens] = await Promise.all([
                habilidadesService.listarHabilidades(),
                atributosService.listarAtributos(),
                mockApi.listarItens()
            ]);
            cachedFormOptions = { habilidades, atributos, itens };
            return cachedFormOptions;
        };

        // --- Observer (scroll infinito) ---
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && hasMore && !isLoading) {
                loadOrigens(true);
            }
        }, { threshold: 1.0 });

        observer.observe(sentinela);

        // --- Eventos ---
        openModalBtn.addEventListener('click', async () => {
            // await loadOrigens(); // Garante que a lista está carregada antes de abrir o modal
            openModal();
        });

        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        addHabilidadeBtn.addEventListener('click', async () => {
            const { habilidades } = await loadFormOptions();
            habilidadesContainer.appendChild(createHabilidadeField(habilidades));
        });

        addEfeitoBtn.addEventListener('click', async () => {
            const { atributos, habilidades, itens } = await loadFormOptions();
            efeitosContainer.appendChild(createEfeitoField(atributos, habilidades, itens));
        });

        document.addEventListener('click', (e) => {
            // Delegação única para fechar modal
            if (e.target.hasAttribute('data-close-modal')) {
                closeModal();
            }
            // Evsento para remover campos dinâmicos
            if (e.target.classList.contains('remove-field')) {
                e.target.closest('div[class*="bg-slate-800"]').remove();
            }
        });

        // Simulação do formulário de submissão
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const id = data.id ? parseInt(data.id) : null;

            // Lógica para coletar os campos dinâmicos e montar o payload
            const habilidades = [];
            habilidadesContainer.querySelectorAll('select[name*="abilities"]').forEach(select => {
                habilidades.push({
                    habilidade_id: select.value
                });
            });

            const efeitos = [];
            efeitosContainer.querySelectorAll('div[class*="bg-slate-800"]').forEach(item => {
                const tipoEfeito = item.querySelector('.tipo-efeito').value;
                const efeitoData = { tipo: tipoEfeito };

                if (tipoEfeito === 'delta_atributo') {
                    efeitoData.atributo_id = item.querySelector('select[name*="[atributo_id]"]').value;
                    efeitoData.delta = item.querySelector('input[name*="[delta]"]').value;
                } else if (tipoEfeito === 'conceder_habilidade') {
                    efeitoData.habilidade_id = item.querySelector('select[name*="[habilidade_id]"]').value;
                } else if (tipoEfeito === 'conceder_item') {
                    efeitoData.item_id = item.querySelector('select[name*="[item_id]"]').value;
                } else if (tipoEfeito === 'custom') {
                    try {
                        efeitoData.notas = JSON.parse(item.querySelector('textarea[name*="[notas]"]').value);
                    } catch (err) {
                        efeitoData.notas = {};
                        console.error('JSON inválido nas notas.', err);
                    }
                }
                efeitos.push(efeitoData);
            });

            const payload = {
                ...data,
                habilidades: habilidades,
                efeitos: efeitos
            };

            console.log("Payload final para a API:", payload);

            try {
                if (id) {
                    await origensService.atualizarOrigem(id, payload);
                    notificar('Origem atualizada com sucesso!');
                } else {
                    await origensService.criarOrigem(payload);
                    notificar('Origem salva com sucesso!');
                }
                resetarPaginacao();
                closeModal();
                await loadOrigens(false);
            } catch (error) {
                console.error(error);
                notificar(`Erro ao salvar a Origem. ${error.message || ''}`, "erro");
            }
        });

        // Evento para editar e excluir origens
        origensList.addEventListener('click', async (e) => {
            const target = e.target;
            if (target.classList.contains('delete-btn')) {
                const id = parseInt(target.dataset.id);
                const confirmation = await confirmar('Tem certeza que deseja excluir esta classe?');

                if (confirmation) {
                    try {
                        await origensService.excluirOrigem(id);
                        notificar(`Origem com ID ${id} excluído.`);
                        resetarPaginacao();
                        await loadOrigens(false);
                    } catch (error) {
                        console.error("Erro ao excluir", error);
                        notificar(`Não foi possível excluir a Origem. ${error.message || ''}`, "erro");
                    }
                }
            }

            if (target.classList.contains('edit-btn')) {
                const id = parseInt(target.dataset.id);
                console.log("ID", id);

                const origem = await origensService.obterOrigem(id);

                console.log(origem);
                if (origem) {
                    preencherFormulario(origem);
                    openModal();
                }
            }
        });

        const preencherFormulario = async (origem) => {
            // 1. Resetar formulário
            form.reset();
            efeitosContainer.innerHTML = '';
            habilidadesContainer.innerHTML = '';
            efeitoIndex = 0;
            habilidadeIndex = 0;

            // 2. Popular campos básicos
            form.querySelector('#origem-id').value = origem.id || '';
            form.querySelector('#nome').value = origem.nome || '';
            form.querySelector('#slug').value = origem.slug || '';
            form.querySelector('#descricao').value = origem.descricao || '';

            // 3. Carregar opções de atributos, tiposDado e habilidades
            const { atributos, habilidades, itens } = await loadFormOptions();

            // 4. Preencher atributos existentes
            if (origem.efeitos && origem.efeitos.length) {
                // Criando o evento de change
                const event = new Event('change');

                origem.efeitos.forEach(efe => {
                    const field = createEfeitoField(atributos, habilidades, itens);
                    const selectTipo = field.querySelector(`select[name^="effects"][name$="[tipo]"]`);

                    if (selectTipo) {
                        selectTipo.value = efe.tipo || '';
                        // Disparando o evento de change
                        selectTipo.dispatchEvent(event);
                    }

                    const inputDelta = field.querySelector(`input[name^="effects"][name$="[delta]"]`);
                    const selectItem = field.querySelector(`select[name^="effects"][name$="[item_id]"]`);
                    const textAreaNotas = field.querySelector(`textarea[name^="effects"][name$="[notas]"]`);
                    const selectAtributo = field.querySelector(`select[name^="effects"][name$="[atributo_id]"]`);
                    const selectHabilidade = field.querySelector(`select[name^="effects"][name$="[habilidade_id]"]`);

                    if (inputDelta) inputDelta.value = efe.delta || '';
                    if (selectItem) selectItem.value = efe.itemId || '';
                    if (textAreaNotas) textAreaNotas.value = efe.notas || '';
                    if (selectAtributo) selectAtributo.value = efe.atributoId || '';
                    if (selectHabilidade) selectHabilidade.value = efe.habilidadeId || '';
                    efeitosContainer.appendChild(field);
                    efeitoIndex++;
                });
            }

            // 5. Preencher habilidades existentes
            if (origem.habilidades && origem.habilidades.length) {
                origem.habilidades.forEach(hab => {
                    const field = createHabilidadeField(habilidades);
                    const selectHabilidade = field.querySelector(`select[name^="abilities"][name$="[habilidade_id]"]`);
                    if (selectHabilidade) selectHabilidade.value = hab.habilidade_id || '';
                    habilidadesContainer.appendChild(field);
                });
            }
        };

        // Carrega as origens na inicialização da página
        // loadOrigens();
    </script>
</body>

</html>