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
            <a href="/painel-mestre" class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
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
            class="bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-3xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
            <h3 class="text-2xl font-semibold mb-6 text-white text-center">Formulário de Classe</h3>
            <form id="classe-form" class="space-y-6">
                <input type="hidden" id="classe-id" name="id">

                <!-- Informações Básicas da Classe -->
                <div>
                    <label for="nome" class="block text-white font-medium mb-2">Nome da Classe</label>
                    <input type="text" id="nome" name="nome" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: Bárbaro">
                </div>
                <div>
                    <label for="slug" class="block text-white font-medium mb-2">Slug (Identificador Único)</label>
                    <input type="text" id="slug" name="slug" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Ex: barbaro">
                </div>
                <div>
                    <label for="descricao" class="block text-white font-medium mb-2">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        placeholder="Uma breve descrição da classe..."></textarea>
                </div>

                <!-- Seção de Regras de Atributos (Tabela classes_atributos) -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-white">Regras de Atributos</h4>
                        <button type="button" id="add-atributo-rule"
                            class="text-green-400 hover:text-green-500 font-bold text-lg transition-colors">
                            + Adicionar Atributo
                        </button>
                    </div>
                    <div id="atributos-container" class="space-y-4">
                        <!-- Campos de atributos dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <!-- Seção de Regras de Habilidades (Tabela classes_habilidades) -->
                <div class="bg-slate-900 p-4 rounded-lg border border-slate-700">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-white">Habilidades da Classe</h4>
                        <button type="button" id="add-habilidade-rule"
                            class="text-green-400 hover:text-green-500 font-bold text-lg transition-colors">
                            + Adicionar Habilidade
                        </button>
                    </div>
                    <div id="habilidades-container" class="space-y-4">
                        <!-- Campos de habilidades dinâmicos serão adicionados aqui -->
                    </div>
                </div>

                <div class="flex justify-between space-x-4">
                    <button type="button" id="close-modal-btn"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-md">
                        Salvar Classe
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de Classes, com base no novo esquema

        // --- Referências DOM e Estado ---
        const classesList = document.getElementById('classes-list');
        const form = document.getElementById('classe-form');
        const modalOverlay = document.getElementById('classe-modal');
        const openModalBtn = document.getElementById('open-modal-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const atributosContainer = document.getElementById('atributos-container');
        const habilidadesContainer = document.getElementById('habilidades-container');
        const addAtributoBtn = document.getElementById('add-atributo-rule');
        const addHabilidadeBtn = document.getElementById('add-habilidade-rule');
        const loadingIndicator = document.getElementById('loading-indicator');

        let offset = 0;
        let isLoading = false;
        let hasMore = true;
        let atributoIndex = 0;
        let habilidadeIndex = 0;

        // --- Mock de Dados do Servidor ---
        // Simulação do serviço de API que buscaria dados do mundo
        const mockApi = {
            listarClasses: async (offset) => {
                const dadosSimulados = [
                    { id: 1, nome: 'Bárbaro', slug: 'barbaro', descricao: 'Um guerreiro selvagem e implacável.' },
                    { id: 2, nome: 'Mago', slug: 'mago', descricao: 'Um estudioso das artes arcanas.' },
                    { id: 3, nome: 'Ladrão', slug: 'ladrao', descricao: 'Especialista em furtividade e astúcia.' },
                    { id: 4, nome: 'Clérigo', slug: 'clerigo', descricao: 'Um devoto que cura e protege.' },
                ];
                await new Promise(resolve => setTimeout(resolve, 500));
                return dadosSimulados.slice(offset, offset + 4);
            },
            listarAtributos: async () => {
                // Simula a tabela `atributos`
                await new Promise(resolve => setTimeout(resolve, 200));
                return [
                    { id: 1, nome: 'Força' },
                    { id: 2, nome: 'Inteligência' },
                    { id: 3, nome: 'Destreza' },
                    { id: 4, nome: 'Carisma' },
                    { id: 5, nome: 'Constituição' },
                ];
            },
            listarTiposDado: async () => {
                // Simula a tabela `tipos_dado`
                await new Promise(resolve => setTimeout(resolve, 200));
                return [
                    { id: 1, codigo: 'd4' },
                    { id: 2, codigo: 'd6' },
                    { id: 3, codigo: 'd8' },
                    { id: 4, codigo: 'd10' },
                    { id: 5, codigo: 'd12' },
                    { id: 6, codigo: 'd20' },
                ];
            },
            listarHabilidades: async () => {
                // Simula a tabela `habilidades`
                await new Promise(resolve => setTimeout(resolve, 200));
                return [
                    { id: 1, nome: 'Fúria de Bárbaro' },
                    { id: 2, nome: 'Bola de Fogo' },
                    { id: 3, nome: 'Invisibilidade' },
                    { id: 4, nome: 'Cura Divina' },
                ];
            }
        };

        // --- Utilitários ---
        const openModal = () => modalOverlay.classList.add('open');
        const closeModal = () => {
            form.reset();
            atributosContainer.innerHTML = '';
            habilidadesContainer.innerHTML = '';
            atributoIndex = 0;
            habilidadeIndex = 0;
            modalOverlay.classList.remove('open');
        };

        const createCard = (classe) => {
            const card = document.createElement('div');
            card.className = 'card relative';
            card.innerHTML = `
                <h4 class="text-xl font-semibold text-white mb-2">${classe.nome}</h4>
                <p class="text-sm text-gray-400 mb-4 flex-grow line-clamp-2">${classe.descricao}</p>
                <div class="flex justify-end space-x-2 mt-4">
                    <button class="edit-btn px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors" data-id="${classe.id}">Editar</button>
                    <button class="delete-btn px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors" data-id="${classe.id}">Excluir</button>
                </div>
            `;
            return card;
        };

        const createAtributoField = (atributos, tiposDado) => {
            const container = document.createElement('div');
            container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-800 p-4 rounded-md relative';
            container.innerHTML = `
                <button type="button" class="remove-field absolute top-2 right-2 text-red-400 hover:text-red-500">&times;</button>
                <div class="col-span-full">
                    <label class="block text-white font-medium mb-1">Atributo</label>
                    <select name="attributes[${atributoIndex}][atributo_id]" required class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <option value="">Selecione um Atributo</option>
                        ${atributos.map(a => `<option value="${a.id}">${a.nome}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-white font-medium mb-1">Dado Base</label>
                    <select name="attributes[${atributoIndex}][tipo_dado_id]" class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <option value="">Nenhum</option>
                        ${tiposDado.map(d => `<option value="${d.id}">${d.codigo}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-white font-medium mb-1">Base Fixa</label>
                    <input type="number" name="attributes[${atributoIndex}][base_fixa]" value="0" class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-white font-medium mb-1">Imutável?</label>
                    <input type="checkbox" name="attributes[${atributoIndex}][imutavel]" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-white font-medium mb-1">Limite Base</label>
                    <input type="number" name="attributes[${atributoIndex}][limite_base_fixa]" class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-white font-medium mb-1">Limite Dado</label>
                    <select name="attributes[${atributoIndex}][limite_tipo_dado_id]" class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <option value="">Nenhum</option>
                        ${tiposDado.map(d => `<option value="${d.id}">${d.codigo}</option>`).join('')}
                    </select>
                </div>
            `;
            atributoIndex++;
            return container;
        };

        const createHabilidadeField = (habilidades) => {
            const container = document.createElement('div');
            container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 bg-slate-800 p-4 rounded-md relative';
            container.innerHTML = `
                <button type="button" class="remove-field absolute top-2 right-2 text-red-400 hover:text-red-500">&times;</button>
                <div class="col-span-full">
                    <label class="block text-white font-medium mb-1">Habilidade</label>
                    <select name="abilities[${habilidadeIndex}][habilidade_id]" required class="w-full px-4 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <option value="">Selecione uma Habilidade</option>
                        ${habilidades.map(h => `<option value="${h.id}">${h.nome}</option>`).join('')}
                    </select>
                </div>
            `;
            habilidadeIndex++;
            return container;
        };

        // --- Renderização e Carregamento ---
        const loadClasses = async (append = true) => {
            if (isLoading || !hasMore) return;
            isLoading = true;
            loadingIndicator.classList.remove('hidden');

            try {
                if (!append) {
                    offset = 0;
                    classesList.innerHTML = '';
                    hasMore = true;
                }

                const classes = await mockApi.listarClasses(offset);
                if (classes.length === 0) {
                    hasMore = false;
                    if (offset === 0) {
                        classesList.innerHTML = `<p class="text-center text-gray-400 col-span-full">Nenhuma classe encontrada. Crie uma!</p>`;
                    }
                    return;
                }

                classes.forEach(c => classesList.appendChild(createCard(c)));
                offset += classes.length;
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        };

        const loadFormOptions = async () => {
            const [atributos, tiposDado, habilidades] = await Promise.all([
                mockApi.listarAtributos(),
                mockApi.listarTiposDado(),
                mockApi.listarHabilidades()
            ]);
            return { atributos, tiposDado, habilidades };
        };

        // --- Eventos ---
        openModalBtn.addEventListener('click', async () => {
            await loadClasses(); // Garante que a lista está carregada antes de abrir o modal
            openModal();
        });
        closeModalBtn.addEventListener('click', closeModal);
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

        // Evento para remover campos dinâmicos
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-field')) {
                e.target.closest('div[class*="bg-slate-800"]').remove();
            }
        });

        // Simulação do formulário de submissão
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

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

            closeModal();
            alert('Classe salva com sucesso! (Simulação)');
        });

        // Carrega as classes na inicialização da página
        loadClasses();
    </script>
</body>

</html>