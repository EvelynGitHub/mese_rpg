<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Gerenciar Itens</title>
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
                <span class="text-gradient">RPG</span> - Itens
            </h1>
        </header>

        <!-- Container para o conteúdo principal da página -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Gerenciar Itens do Mundo
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Crie e edite os itens, armas e equipamentos que os personagens podem encontrar.
                </p>

                <!-- Botão que abre o modal -->
                <button id="open-modal-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                    Adicionar Novo Item
                </button>
            </section>

            <!-- Seção de Listagem de Itens em formato de cards -->
            <section class="w-full max-w-7xl mx-auto mt-8">
                <h3 class="text-2xl font-semibold mb-6 text-white">Itens Existentes</h3>
                <div id="itens-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards de itens serão populados aqui via JavaScript -->
                </div>
                <!-- Elemento sentinela para a rolagem infinita -->
                <div id="sentinela" class="h-1 bg-transparent mt-8"></div>
                <div id="loading-indicator" class="flex flex-col items-center text-center p-4 text-gray-400">
                    <div class="loading-animation rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    Carregando mais itens...
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para o Formulário de Item -->
    <div id="item-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <section
            class="bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <h3 class="text-2xl font-semibold mb-6 text-white text-center">Formulário de Item</h3>
            <form id="item-form" class="space-y-6">
                <input type="hidden" id="item-id" name="id">

                <!-- Informações Básicas do Item -->
                <div>
                    <label for="nome" class="block text-white font-medium mb-2">Nome do Item</label>
                    <input type="text" id="nome" name="nome" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: Espada de Mithril">
                </div>
                <div>
                    <label for="slug" class="block text-white font-medium mb-2">Slug (Identificador Único)</label>
                    <input type="text" id="slug" name="slug" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: espada-de-mithril">
                </div>
                <div>
                    <label for="descricao" class="block text-white font-medium mb-2">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Uma breve descrição do item..."></textarea>
                </div>
                <div>
                    <label for="tipo" class="block text-white font-medium mb-2">Tipo de Item</label>
                    <select id="tipo" name="tipo" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <option value="">Selecione um Tipo</option>
                        <option value="arma">Arma</option>
                        <option value="armadura">Armadura</option>
                        <option value="consumivel">Consumível</option>
                        <option value="acessorio">Acessório</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
                <div>
                    <label for="dados_dano" class="block text-white font-medium mb-2">Dados de Dano</label>
                    <input type="text" id="dados_dano" name="dados_dano"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: 1d8, 2d6+2 (opcional para armas)">
                </div>

                <!-- Seção de Propriedades Dinâmicas -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-white">Propriedades</h4>
                        <button type="button" id="add-propriedade-btn"
                            class="text-green-400 hover:text-green-500 font-bold text-lg transition-colors">
                            + Adicionar Propriedade
                        </button>
                    </div>
                    <div id="propriedades-container" class="space-y-4">
                        <!-- Campos de propriedades dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <div class="flex justify-between space-x-4">
                    <button type="button" id="close-modal-btn"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Salvar Item
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de Itens
        import { habilidadesService as habilidadesJS } from "../js/api/habilidades.js";
        import { atributosService as atributosJS } from "../js/api/atributos.js";
        import { origensService as origensJS } from "../js/api/origens.js";
        import { itensService as itensJS } from "../js/api/itens.js";
        import { notificar, confirmar } from '../js/ui/notificacao.js';

        const mundoIdCriptografado = "{{ $mundo_id }}";
        const atributosService = atributosJS(mundoIdCriptografado);
        const habilidadesService = habilidadesJS(mundoIdCriptografado);
        const origensService = origensJS(mundoIdCriptografado);
        const itensService = itensJS(mundoIdCriptografado);

        // --- Referências DOM e Estado ---
        const itensList = document.getElementById('itens-list');
        const form = document.getElementById('item-form');
        const itemIdForm = document.getElementById('item-id');
        const modalOverlay = document.getElementById('item-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const propriedadesContainer = document.getElementById('propriedades-container');
        const addPropriedadeBtn = document.getElementById('add-propriedade-btn');
        const loadingIndicator = document.getElementById('loading-indicator');

        let offset = 0;
        let isLoading = false;
        let hasMore = true;
        let propriedadeIndex = 0;

        // --- Utilitários ---
        const openModal = () => modalOverlay.classList.add('open');
        const closeModal = () => {
            form.reset();
            itemIdForm.value = '';
            propriedadesContainer.innerHTML = '';
            propriedadeIndex = 0;
            modalOverlay.classList.remove('open');
        };

        const createCard = (item) => {
            const card = document.createElement('div');
            card.className = 'card relative';
            card.innerHTML = `
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">⚔️</span>
                    <h4 class="text-xl font-semibold text-white">${item.nome}</h4>
                </div>
                <p class="text-sm font-medium text-blue-300 mb-2">${item.tipo.toUpperCase()}</p>
                <p class="text-sm text-gray-400 mb-4 flex-grow line-clamp-2">${item.descricao ?? 'N/A'}</p>
                <div class="flex justify-end space-x-2 mt-4">
                    <button class="edit-btn px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors" data-id="${item.id}">Editar</button>
                    <button class="delete-btn px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors" data-id="${item.id}">Excluir</button>
                </div>
            `;
            return card;
        };

        const createPropriedadeField = (props) => {
            const container = document.createElement('div');
            container.className = 'flex items-center space-x-2';
            container.innerHTML = `
                <input type="text" name="propriedades[${propriedadeIndex}][chave]" value="${props ? props.chave : ''}" required class="w-1/3 px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors" placeholder="Chave (ex: peso)">
                <input type="text" name="propriedades[${propriedadeIndex}][valor]" value="${props ? props.valor : ''}" required class="w-2/3 px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors" placeholder="Valor (ex: 5 kg)">
                <button type="button" class="remove-propriedade-btn text-red-400 hover:text-red-500">&times;</button>
            `;
            propriedadeIndex++;
            return container;
        };

        const resetarPaginacao = () => {
            offset = 0;
            hasMore = true;
        };

        // --- Renderização e Carregamento ---
        const loadItens = async (append = true) => {
            if (isLoading || !hasMore) return;
            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    resetarPaginacao()
                    itensList.replaceChildren();
                }

                const itens = await itensService.listarItens(offset);
                if (itens.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        itensList.innerHTML = `<p class="text-center text-gray-400 col-span-full">Nenhum item encontrado. Crie um!</p>`;
                    }
                    return;
                }

                itens.forEach(o => itensList.appendChild(createCard(o)));
                offset += itens.length;
            } catch (error) {
                console.error('Erro na listagem de armar e itens:', error);
                itensList.innerHTML = `
                    <div class="text-center text-red-400 col-span-full">
                        Erro ao carregar itens. Tente novamente.
                    </div>`;
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

        addPropriedadeBtn.addEventListener('click', () => {
            propriedadesContainer.appendChild(createPropriedadeField());
        });

        // Evento para remover campos dinâmicos
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-propriedade-btn')) {
                e.target.closest('div[class*="flex items-center"]').remove();
            }
        });

        // Simulação do formulário de submissão
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const id = data.id ? parseInt(data.id) : null;

            // Lógica para coletar as propriedades dinâmicas e montar o JSONB
            const propriedades = {};
            propriedadesContainer.querySelectorAll('.flex.items-center').forEach(item => {
                const chave = item.querySelector('input[name*="[chave]"]').value;
                const valor = item.querySelector('input[name*="[valor]"]').value;
                if (chave && valor) {
                    propriedades[chave] = valor;
                }
            });

            const payload = {
                ...data,
                propriedades: propriedades
            };

            console.log("Payload final para a API:", payload);

            // closeModal();
            // alert('Item salvo com sucesso! (Simulação)');
            try {
                if (id) {
                    await itensService.atualizarItem(id, payload);
                    notificar('Item atualizada com sucesso!');
                } else {
                    await itensService.criarItem(payload);
                    notificar('Item salva com sucesso!');
                }
                resetarPaginacao();
                closeModal();
                await loadItens(false);
            } catch (error) {
                console.error(error);
                notificar(`Erro ao salvar a Item. ${error.message || ''}`, "erro");
            }
        });

        // Evento para editar e excluir origens
        itensList.addEventListener('click', async (e) => {
            const target = e.target;
            if (target.classList.contains('delete-btn')) {
                const id = parseInt(target.dataset.id);
                const confirmation = await confirmar('Tem certeza que deseja excluir este ITEM?');

                if (confirmation) {
                    try {
                        await itensService.excluirItem(id);
                        notificar(`Item com ID ${id} excluído.`);
                        resetarPaginacao();
                        await loadItens(false);
                    } catch (error) {
                        console.error("Erro ao excluir", error);
                        notificar(`Não foi possível excluir a Item. ${error.message || ''}`, "erro");
                    }
                }
            }

            if (target.classList.contains('edit-btn')) {
                const id = parseInt(target.dataset.id);
                console.log("ID", id);

                const item = await itensService.obterItem(id);

                console.log(item);
                if (item) {
                    preencherFormulario(item, form);
                    openModal();
                }
            }
        });


        const preencherFormulario = async (objeto, formulario) => {
            // const formulario = document.getElementById(idFormulario);
            // if (!formulario) {
            //     console.error(`Formulário com ID "${idFormulario}" não encontrado.`);
            //     return;
            // }
            // Itera sobre cada chave e valor do objeto JSON
            for (const chave in objeto) {
                const valor = objeto[chave];

                if (typeof valor === 'object' && valor !== null && !Array.isArray(valor)) {
                    // Chama uma função para lidar com as propriedades dinâmicas
                    preencherPropriedadesDinamicas(valor, formulario);
                } else {
                    // Pega o campo do formulário que tem o mesmo "name" ou "id" que a chave
                    const campo = formulario.querySelector(`[name="${chave}"]`);
                    // Se o campo for encontrado...
                    if (campo) {
                        // Verifica o tipo do campo para preencher corretamente
                        if (campo.type === 'checkbox') {
                            campo.checked = objeto[chave];
                        } else {
                            campo.value = objeto[chave];
                        }
                    }
                    // else {
                    //     console.warn(`Campo para a chave "${chave}" não encontrado no formulário.`);
                    // }
                }
            }
        };

        function preencherPropriedadesDinamicas(propriedades, formulario) {
            for (const chave in propriedades) {
                const valor = propriedades[chave];
                propriedadesContainer.appendChild(createPropriedadeField({ chave, valor }));
            }
        }

        // Carrega os itens na inicialização da página
        loadItens();
    </script>
</body>

</html>