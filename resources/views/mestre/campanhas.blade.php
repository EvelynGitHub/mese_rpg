<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Campanhas & Sessões</title>
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
            border-top: 4px solid #FCD34D;
        }

        .loading-animation {
            border-top-color: #FCD34D;
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
                <span class="text-gradient">RPG</span> - Campanhas & Sessões
            </h1>
        </header>

        <!-- Container para o conteúdo principal da página -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-8 px-4 max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Gerenciar Campanhas do Mundo
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Crie e organize as grandes histórias que seus jogadores viverão.
                </p>

                <!-- Botão que abre o modal de criação de campanha -->
                <button id="open-modal-btn"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                    Criar Nova Campanha
                </button>
            </section>

            <!-- Seção de Listagem de Campanhas em formato de cards -->
            <section class="w-full max-w-7xl mx-auto mt-8">
                <h3 class="text-2xl font-semibold mb-6 text-white">Campanhas Existentes</h3>
                <div id="campanhas-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards de campanhas serão populados aqui via JavaScript -->
                </div>
                <!-- Elemento sentinela para a rolagem infinita -->
                <div id="sentinela" class="h-1 bg-transparent mt-8"></div>
                <div id="loading-indicator" class="flex flex-col items-center text-center p-4 text-gray-400 hidden">
                    <div class="loading-animation rounded-full h-8 w-8 border-b-2 border-gray-400"></div>
                    Carregando mais campanhas...
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para o Formulário de Campanha -->
    <div id="campanha-modal"
        class="modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <section
            class="bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <h3 class="text-2xl font-semibold mb-6 text-white text-center">Formulário de Campanha</h3>
            <form id="campanha-form" class="space-y-6">
                <input type="hidden" id="campanha-id" name="id">

                <!-- Informações Básicas da Campanha -->
                <div>
                    <label for="nome" class="block text-white font-medium mb-2">Nome da Campanha</label>
                    <input type="text" id="nome" name="nome" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors"
                        placeholder="Ex: A Ascensão do Lich">
                </div>
                <div>
                    <label for="descricao" class="block text-white font-medium mb-2">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors"
                        placeholder="Uma breve sinopse da história da campanha..."></textarea>
                </div>

                <div class="flex justify-between space-x-4">
                    <button type="button" id="close-modal-btn"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Salvar Campanha
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de Campanhas

        // --- Referências DOM e Estado ---
        const campanhasList = document.getElementById('campanhas-list');
        const form = document.getElementById('campanha-form');
        const modalOverlay = document.getElementById('campanha-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const loadingIndicator = document.getElementById('loading-indicator');

        let offset = 0;
        let isLoading = false;
        let hasMore = true;

        // --- Mock de Dados do Servidor ---
        const mockApi = {
            listarCampanhas: async (offset) => {
                const dadosSimulados = [
                    { id: 1, nome: 'A Ruína dos Anões', descricao: 'Uma épica jornada para recuperar uma cidade anã perdida nas montanhas. O mal se esconde nas profundezas.', sessao_count: 5, jogador_count: 4 },
                    { id: 2, nome: 'O Mistério de Eldoria', descricao: 'Investigue o desaparecimento de uma antiga relíquia élfica. Os indícios apontam para um culto sombrio.', sessao_count: 2, jogador_count: 3 },
                    { id: 3, nome: 'A Ascensão do Dragão', descricao: 'Um antigo dragão dourado desperta de seu sono, e o mundo está em perigo. Os heróis devem unir forças para impedi-lo.', sessao_count: 12, jogador_count: 5 },
                    { id: 4, nome: 'Sombras sobre Valoria', descricao: 'Uma campanha de horror gótico em que os heróis lutam contra criaturas da noite.', sessao_count: 8, jogador_count: 4 },
                    { id: 5, nome: 'A Profecia Esquecida', descricao: 'Uma missão para desvendar uma antiga profecia que pode salvar o reino ou destruí-lo.', sessao_count: 6, jogador_count: 3 },
                    { id: 6, nome: 'O Segredo da Floresta', descricao: 'Desvende os segredos de uma floresta encantada e ajude a restaurar a paz.', sessao_count: 1, jogador_count: 2 },
                ];
                await new Promise(resolve => setTimeout(resolve, 500));
                return dadosSimulados.slice(offset, offset + 3);
            },
        };

        // --- Utilitários ---
        const openModal = () => modalOverlay.classList.add('open');
        const closeModal = () => {
            form.reset();
            modalOverlay.classList.remove('open');
        };

        const createCard = (campanha) => {
            const card = document.createElement('div');
            card.className = 'card relative';
            card.innerHTML = `
                <h4 class="text-xl font-semibold text-white mb-2">${campanha.nome}</h4>
                <p class="text-sm text-gray-400 mb-4 flex-grow line-clamp-3">${campanha.descricao}</p>
                <div class="mt-4 text-sm text-gray-300">
                    <p class="mb-1"><strong class="text-yellow-400">${campanha.sessao_count}</strong> sessões jogadas</p>
                    <p><strong class="text-yellow-400">${campanha.jogador_count}</strong> jogadores participando</p>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button class="edit-btn px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors" data-id="${campanha.id}">Editar</button>
                    <button class="delete-btn px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors" data-id="${campanha.id}">Excluir</button>
                </div>
            `;
            return card;
        };

        // --- Renderização e Carregamento ---
        const loadCampanhas = async (append = true) => {
            if (isLoading || !hasMore) return;
            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    offset = 0;
                    campanhasList.innerHTML = '';
                    hasMore = true;
                }

                const campanhas = await mockApi.listarCampanhas(offset);
                if (campanhas.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        campanhasList.innerHTML = `<p class="text-center text-gray-400 col-span-full">Nenhuma campanha encontrada. Crie a sua!</p>`;
                    }
                    return;
                }

                campanhas.forEach(o => campanhasList.appendChild(createCard(o)));
                offset += campanhas.length;
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        };

        // --- Eventos ---
        openModalBtn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        // Simulação do formulário de submissão
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            console.log("Payload final para a API:", data);

            closeModal();
            alert('Campanha salva com sucesso! (Simulação)');
        });

        // Eventos para os botões de ação (editar e excluir) nos cards
        campanhasList.addEventListener('click', (e) => {
            if (e.target.classList.contains('edit-btn')) {
                const id = e.target.dataset.id;
                alert(`Simulando edição da campanha com ID: ${id}`);
            } else if (e.target.classList.contains('delete-btn')) {
                const id = e.target.dataset.id;
                alert(`Simulando exclusão da campanha com ID: ${id}`);
            }
        });

        // Carrega as campanhas na inicialização da página
        loadCampanhas();
    </script>
</body>

</html>