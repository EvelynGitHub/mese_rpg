<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Gerenciar Habilidades</title>
    <script src="https://cdn.tailwindcss.com" onload="console.log('CDN do Tailwindcss carregada.')" onerror="
            console.log('Falha ao carregar CDN do Tailwindcss.')
            let script = document.createElement('script')
            script.src = '/js/tailwindcss.es'
            document.head.appendChild(script)
    ">
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            color: #E0E0E0;
        }

        .text-gradient {
            background-image: linear-gradient(to right, #6EE7B7, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Classes para controlar a animação e visibilidade do modal */
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


        #habilidades-list {
            display: grid;
            /* gap: 1.5rem;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); */
            opacity: 0;
            animation: fadeInContainer 0.5s ease-in-out forwards;
        }

        /* Define a animação de fade-in para o container principal */
        @keyframes fadeInContainer {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Define a animação para os cards individuais */
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

        .card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            border: 1px solid #e5e7eb;
            opacity: 0;
        }

        .card-animate {
            animation: fadeInCard 0.5s ease-in-out forwards var(--delay);
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
            font-size: 1.5rem;
        }

        .hidden {
            display: none;
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
                <span class="text-gradient">RPG</span> - Habilidades
            </h1>
        </header>

        <!-- Container para o conteúdo principal da página -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Gerenciar Habilidades
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Crie e edite as habilidades disponíveis no seu mundo.
                </p>

                <!-- Botão que abre o modal -->
                <button id="open-modal-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                    Adicionar Nova Habilidade
                </button>
            </section>

            <!-- Seção de Listagem de Habilidades agora em formato de cards -->
            <section class="w-full max-w-7xl mx-auto mt-8">
                <h3 class="text-2xl font-semibold mb-6 text-white">Habilidades Existentes</h3>
                <div id="habilidades-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards de habilidades serão populados aqui via JavaScript -->
                </div>
                <!-- Elemento sentinela para a rolagem infinita -->
                <div id="sentinela" class="h-1 bg-transparent mt-8"></div>
                <div id="loading-indicator" class="loading flex flex-col items-center text-center p-4 text-gray-400">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    Carregando mais habilidades...
                </div>
            </section>

        </main>
    </div>

    <!-- Modal para o Formulário de Habilidade -->
    <div id="habilidade-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <section
            class="bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-2xl transform scale-95 transition-transform duration-300">
            <h3 class="text-2xl font-semibold mb-6 text-white text-center">Formulário de Habilidade</h3>
            <form id="habilidade-form" class="space-y-6">
                <input type="hidden" id="habilidade-id" name="id">
                <div>
                    <label for="nome" class="block text-white font-medium mb-2">Nome da Habilidade</label>
                    <input type="text" id="nome" name="nome" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: Bola de Fogo">
                </div>
                <div>
                    <label for="slug" class="block text-white font-medium mb-2">Slug (Identificador Único)</label>
                    <input type="text" id="slug" name="slug" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: bola_de_fogo">
                </div>
                <div>
                    <label for="descricao" class="block text-white font-medium mb-2">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Descreva o que a habilidade faz..."></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-white">Tipo</label>
                        <select id="tipo" name="tipo"
                            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                            <option value="ativa">Ativa</option>
                            <option value="passiva">Passiva</option>
                            <option value="condicional">Condicional</option>
                        </select>
                    </div>

                    <div>
                        <label for="usos_max" class="block text-sm font-medium text-white">Usos Máximos</label>
                        <input type="number" id="usos_max" name="usos_max"
                            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                    </div>

                    <div>
                        <label for="usos_reset" class="block text-sm font-medium text-white">Reset de Usos</label>
                        <select id="usos_reset" name="usos_reset"
                            class="mt-1 block w-full rounded-md border-slate-700 bg-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                            <option value="sessao">Sessão</option>
                            <option value="combate">Combate</option>
                            <option value="turno">Turno</option>
                        </select>
                    </div>

                    <div>
                        <label for="stack" class="block text-sm font-medium text-white">Stack</label>
                        <select id="stack" name="stack"
                            class="mt-1 block w-full rounded-md  border-slate-700 bg-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                            <option value="permitir">Permitir</option>
                            <option value="bloquear">Bloquear</option>
                            <option value="sobrescrever">Sobrescrever</option>
                        </select>
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-white mb-2">Bônus (JSON)</h2>
                    <!-- <p class="block text-white font-medium mb-2">Efeitos</p> -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <select name="alvo" id="efeito-alvo"
                            class="col-span-2 md:col-span-1 block w-full p-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            <option value="" disabled selected>Escolha um Alvo</option>
                            <option value="self">Jogador</option>
                            <option value="aliado">Aliado</option>
                            <option value="inimigo">Inimigo</option>
                            <option value="todos_aliados">Todos aliados</option>
                            <option value="todos_inimigos">Todos inimigos</option>
                            <option value="todos">Todos</option>
                        </select>
                        <select name="atributo" id="efeito-atributo"
                            class="col-span-2 md:col-span-1 block w-full p-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            <option value="" disabled selected>Atributo sob efeito</option>
                        </select>
                        <input type="number" id="efeito-modificador" placeholder="Modificador"
                            class="p-2 rounded-md bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <input type="number" id="efeito-duracao" placeholder="Duração (turnos)"
                            class="p-2 rounded-md bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    </div>

                    <button type="button" id="add-efeito"
                        class="w-full inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        Adicionar Efeito
                    </button>
                    <!-- <label for="bonus" class="block text-white font-medium mb-2">Bônus (JSON)</label> -->
                    <!-- <textarea id="bonus" name="bonus" rows="2"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder='Ex: {"dano": "1d6", "tipo": "fogo"}'></textarea> -->

                    <div id="efeitos-lista" class="flex flex-wrap gap-2 mt-4"></div>
                </div>
                <div>
                    <label for="evolucoes" class="block text-white font-medium mb-2">Evoluções</label>
                    <textarea id="evolucoes" name="evolucoes" rows="2"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder='[{"nivel": 2,"mudancas": { "efeitos[0].modificador": 3, "usos.max": 5 } }]'></textarea>
                </div>

                <!-- Botões de salvar e cancelar -->
                <div class="flex justify-between space-x-4">
                    <button type="button" id="close-modal-btn"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Salvar Habilidade
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de Habilidades 🔥  ⚡  ⛈️  🌬️
        import { habilidadesService } from "../js/api/habilidades.js";
        import { atributosService as atributosJS } from "../js/api/atributos.js";
        import { notificar, confirmar } from '../js/ui/notificacao.js';
        import { preencherFormulario } from '../js/ui/form.js';

        const mundoIdCriptografado = "{{ $mundo_id }}";
        const mundoDeHabilidades = habilidadesService(mundoIdCriptografado);
        const atributosService = atributosJS(mundoIdCriptografado);

        // document.addEventListener('DOMContentLoaded', () => {
        // --- Referências DOM ---
        const habilidadesList = document.getElementById('habilidades-list');
        const form = document.getElementById('habilidade-form');
        const modalOverlay = document.getElementById('habilidade-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const inputId = document.getElementById('habilidade-id');
        const inputNome = document.getElementById('nome');
        const inputSlug = document.getElementById('slug');
        const inputDescricao = document.getElementById('descricao');
        const efeitosLista = document.getElementById('efeitos-lista');
        const evolucoesLista = document.getElementById('evolucoes-lista');
        const sentinela = document.getElementById('sentinela');
        const loadingIndicator = document.getElementById('loading-indicator');

        const atributos = document.getElementById('efeito-atributo');

        // --- Estado ---
        let offset = 0;
        let isLoading = false;
        let hasMore = true;
        let efeitos = [];
        let evolucoes = [];
        let cachedFormOptions = null;

        // --- Utilitários ---
        const sucesso = (msg) => notificar(msg, 'sucesso');
        const erro = (msg) => notificar(msg, 'erro');

        const resetarForm = () => {
            inputId.value = '';
            form.reset();

            efeitos = [];
            evolucoes = [];
            renderEfeitos();
        };

        const resetarPaginacao = () => {
            offset = 0;
            hasMore = true;
        };

        const openModal = () => modalOverlay.classList.add('open');

        const closeModal = () => {
            resetarForm();
            modalOverlay.classList.remove('open');
        };

        const criarCard = (habilidade, index) => {
            const card = document.createElement('div');
            card.className = 'card relative bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-cyan-400 flex flex-col h-full';
            card.innerHTML = `
                    <div class="flex items-center mb-2">
                        <span class="text-3xl mr-3">⚡</span>
                        <h4 class="text-xl font-semibold text-white">${habilidade.nome}</h4>
                    </div>
                    <p class="text-gray-400 mb-4 flex-grow line-clamp-2 mb-8">${habilidade.descricao ?? ""}</p>
                    <span class="text-xs text-slate-400 block truncate w-1/2">Slug: ${habilidade.slug}</span>
                    <div class="flex justify-end space-x-2 mt-4 absolute bottom-6 right-6">
                        <button class="edit-btn px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors" data-id="${habilidade.id}">Editar</button>
                        <button class="delete-btn px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors" data-id="${habilidade.id}">Excluir</button>
                    </div>
                `;
            card.classList.add('card-animate');
            card.style.setProperty('--delay', `${index * 0.1}s`);
            return card;
        };

        // --- Renderização ---
        const loadFormOptions = async () => {
            if (cachedFormOptions) return cachedFormOptions;

            const [atributos] = await Promise.all([
                atributosService.listarAtributos()
            ]);
            cachedFormOptions = { atributos };
            return cachedFormOptions;
        };

        const carregarHabilidades = async (append = true) => {
            if (isLoading || !hasMore) return;

            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    resetarPaginacao();
                    habilidadesList.replaceChildren();
                }

                const habilidades = await mundoDeHabilidades.listarHabilidades(offset);

                if (habilidades.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        habilidadesList.innerHTML = `
                            <div class="text-center text-gray-400 col-span-full">
                                Nenhuma habilidade encontrada. Cadastre uma!
                            </div>`;
                    }
                    return;
                }

                habilidades.forEach((h, i) => habilidadesList.appendChild(criarCard(h, i)));
                offset += habilidades.length;

            } catch (error) {
                console.error('Erro na listagem de habilidades:', error);
                habilidadesList.innerHTML = `
                    <div class="text-center text-red-400 col-span-full">
                        Erro ao carregar habilidades. Tente novamente.
                    </div>`;
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        };

        // Função para renderizar a lista de efeitos na tela
        const renderEfeitos = () => {
            efeitosLista.innerHTML = '';
            efeitos.forEach((efeito, index) => {
                const badge = document.createElement('div');
                badge.classList.add('flex', 'flex-col', 'sm:flex-row', 'w-full', 'items-center', 'justify-between', 'p-2', 'bg-slate-800', 'rounded-xl', 'border', 'border-slate-700', 'space-y-4', 'sm:space-y-0');
                badge.innerHTML = `
                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-center sm:justify-start">
                        <span class="text-gray-500 text-sm font-light border-r-2 pr-2 border-r-slate-500 w-20" >Efeito: ${index}</span>
                        <span class="text-gray-200 font-medium pl-2">${efeito.modificador > 0 ? "+" : ""}${efeito.modificador} ${efeito.atributo} em ${efeito.alvo} por ${efeito.duracao_turnos} turnos</span>
                    </div>
                    <div class="flex items-center justify-between w-full sm:w-auto sm:space-x-4 space-x-2">
                        <div class="flex items-center space-x-2 number-input-group">
                            <button type="button" data-index="${index}" class="remove-btn px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors">&times;</button>
                        </div>
                    </div>
                `;
                efeitosLista.appendChild(badge);
            });
        }
        // --- Observer (scroll infinito) ---
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && hasMore && !isLoading) {
                carregarHabilidades(true);
            }
        }, { threshold: 1.0 });

        observer.observe(sentinela);

        // --- Eventos ---
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const id = data.id ? parseInt(data.id) : null;

            // Simulação de campos de seleção que seriam no formulário principal
            const tipo = document.getElementById('tipo').value; // Simula 'ativa'
            const usosMax = document.getElementById('usos_max').value; // Simula 'ativa'
            const stack = document.getElementById('stack').value;// Simula 'permitir'
            const reset = document.getElementById('usos_reset').value; // Simula 'sessao'

            const finalData = {
                nome: document.getElementById('nome').value,
                slug: document.getElementById('nome').value.toLowerCase().replace(/\s/g, '_'),
                descricao: document.getElementById('descricao').value,
                bonus: {
                    tipo,
                    usos: {
                        max: usosMax ?? 3,
                        reset
                    },
                    efeitos,
                    stack,
                    evolucao: document.getElementById('evolucoes').value,
                }
            };

            console.log("Dados do formulário:", finalData);
            console.log(data);



            // Exibir o JSON final
            // jsonOutput.textContent = JSON.stringify(finalData, null, 4);

            try {
                if (id) {
                    await mundoDeHabilidades.atualizarHabilidade(id, finalData);
                    sucesso('Habilidade atualizada com sucesso!');
                } else {
                    await mundoDeHabilidades.criarHabilidade(finalData);
                    sucesso('Habilidade salva com sucesso!');
                }
                resetarPaginacao();
                await carregarHabilidades(false);

            } catch (error) {
                console.error(error);
                erro(id ? "Não foi possível editar habilidade." : "Problema ao cadastrar");
            } finally {
                closeModal();
            }


        });

        habilidadesList.addEventListener('click', async (e) => {
            const target = e.target;
            if (target.classList.contains('delete-btn')) {
                const id = parseInt(target.dataset.id);
                const confirmation = await confirmar('Tem certeza que deseja excluir esta habilidade?');

                if (confirmation) {
                    try {
                        await mundoDeHabilidades.excluirHabilidade(id);
                        sucesso(`Habilidade com ID ${id} excluída.`);
                        resetarPaginacao();
                        await carregarHabilidades(false);
                    } catch (error) {
                        console.error("Erro ao excluir", error);
                        erro("Não foi possível excluir a habilidade.");
                    }
                }
            }

            if (target.classList.contains('edit-btn')) {
                const id = parseInt(target.dataset.id);
                const habilidade = await mundoDeHabilidades.obterHabilidade(id);

                if (habilidade) {
                    preencherFormulario(habilidade, "habilidade-form", preencherPropriedades);
                    // inputId.value = habilidade.id;
                    // inputNome.value = habilidade.nome;
                    // inputSlug.value = habilidade.slug;
                    // inputDescricao.value = habilidade.descricao;
                    // inputBonus.value = habilidade.bonus;
                    openModal();
                }
            }
        });

        openModalBtn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        // Adicionar Efeito
        document.getElementById('add-efeito').addEventListener('click', () => {
            const efeito = {
                alvo: document.getElementById('efeito-alvo').value,
                atributo: document.getElementById('efeito-atributo').value,
                modificador: Number(document.getElementById('efeito-modificador').value),
                duracao_turnos: Number(document.getElementById('efeito-duracao').value),
            };

            if (efeito.alvo === "" || efeito.atributo === "" || isNaN(efeito.modificador) || isNaN(efeito.duracao_turnos)) {
                erro("Por favor, preencha todos os campos do efeito Bonus corretamente.");
                return;
            }

            // Validação simples
            if (efeito.alvo && efeito.atributo && efeito.modificador) {
                efeitos.push(efeito);
                renderEfeitos();
                // Limpar os campos do formulário de efeito
                document.getElementById('efeito-alvo').value = '';
                document.getElementById('efeito-atributo').value = '';
                document.getElementById('efeito-modificador').value = '';
                document.getElementById('efeito-duracao').value = '';
            }
        });

        // Remover Efeito
        efeitosLista.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-btn')) {
                const index = e.target.getAttribute('data-index');
                efeitos.splice(index, 1);
                renderEfeitos();
            }
        });

        // });

        const preencherPropriedades = (valor, form, chave) => {
            console.log("Preenchendo propriedade:", chave, valor);

            if (chave === 'efeitos' && Array.isArray(valor)) {
                efeitos = valor;
                renderEfeitos();
                return;
            } else if (chave === 'bonus') {
                valor.evolucoes = valor.evolucao || [];
                preencherFormulario(valor, form, preencherPropriedades);
            } else if (chave === 'usos') {
                preencherFormulario({
                    usos_max: valor.max,
                    usos_reset: valor.reset,
                }, form, preencherPropriedades);
            }

        }

        // Inicializa o formulário ao carregar a página
        document.addEventListener('DOMContentLoaded', async () => {
            await loadFormOptions()

            cachedFormOptions.atributos.forEach(a => {
                if (!a.chave) return;
                const option = new Option(a.nome, a.chave);
                atributos.add(option);
            });
        });
    </script>
</body>

</html>