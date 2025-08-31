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
            <a href="/painel-mestre" class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
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
                <div>
                    <label for="bonus" class="block text-white font-medium mb-2">Bônus (JSON)</label>
                    <textarea id="bonus" name="bonus" rows="2"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder='Ex: {"dano": "1d6", "tipo": "fogo"}'></textarea>
                </div>
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
        import { notificar, confirmar } from '../js/ui/notificacao.js';

        const mundoIdCriptografado = "{{ $mundo_id }}";
        const mundoDeHabilidades = habilidadesService(mundoIdCriptografado);

        document.addEventListener('DOMContentLoaded', () => {
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
            const inputBonus = document.getElementById('bonus');
            const sentinela = document.getElementById('sentinela');
            const loadingIndicator = document.getElementById('loading-indicator');

            // --- Estado ---
            let offset = 0;
            let isLoading = false;
            let hasMore = true;

            // --- Utilitários ---
            const sucesso = (msg) => notificar(msg, 'sucesso');
            const erro = (msg) => notificar(msg, 'erro');

            const resetarForm = () => {
                inputId.value = '';
                form.reset();
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

                try {
                    if (id) {
                        await mundoDeHabilidades.atualizarHabilidade(id, data);
                        sucesso('Habilidade atualizada com sucesso!');
                    } else {
                        await mundoDeHabilidades.criarHabilidade(data);
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
                        inputId.value = habilidade.id;
                        inputNome.value = habilidade.nome;
                        inputSlug.value = habilidade.slug;
                        inputDescricao.value = habilidade.descricao;
                        inputBonus.value = habilidade.bonus;
                        openModal();
                    }
                }
            });

            openModalBtn.addEventListener('click', openModal);
            closeModalBtn.addEventListener('click', closeModal);
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) closeModal();
            });
        });
    </script>
</body>

</html>