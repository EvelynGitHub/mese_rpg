<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Gerenciar Habilidades</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        /* Efeito de transição suave para o formulário */
        #add-habilidade-details>section {
            transition: max-height 0.5s ease-in-out, opacity 0.5s ease-in-out;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
        }

        #add-habilidade-details[open]>section {
            max-height: 1000px;
            opacity: 1;
        }

        /**
         * A magia da animação
         * Estas classes serão adicionadas via JavaScript
         */
        #add-habilidade-details.animating-open>section {
            max-height: 1000px;
            opacity: 1;
        }

        #add-habilidade-details.animating-close>section {
            max-height: 0;
            opacity: 0;
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

                <!-- Componente de Formulário Adicionar/Editar - Oculto por padrão -->
                <details id="add-habilidade-details" class="mb-12">
                    <summary
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full inline-block cursor-pointer transition-colors shadow-md">
                        Adicionar Nova Habilidade
                    </summary>
                    <section
                        class="mt-8 bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 conteudo-animado">
                        <h3 class="text-2xl font-semibold mb-6 text-white">Formulário de Habilidade</h3>
                        <form id="habilidade-form" class="space-y-6">
                            <input type="hidden" id="habilidade-id" name="id">
                            <div>
                                <label for="nome" class="block text-white font-medium mb-2">Nome da Habilidade</label>
                                <input type="text" id="nome" name="nome" required
                                    class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                                    placeholder="Ex: Bola de Fogo">
                            </div>
                            <div>
                                <label for="slug" class="block text-white font-medium mb-2">Slug (Identificador
                                    Único)</label>
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
                                <button type="button" id="button-cancelar"
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
                </details>
            </section>

            <!-- Seção de Listagem de Habilidades agora em formato de cards -->
            <section class="w-full max-w-7xl mx-auto">
                <h3 class="text-2xl font-semibold mb-6 text-white">Habilidades Existentes</h3>
                <div id="habilidades-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards de habilidades serão populados aqui via JavaScript -->
                    <div> Nenhuma habilidade encontrada. Cadastre uma!</div>
                </div>
            </section>

        </main>
    </div>

    <script type="module">
        // Lógica JavaScript para gerenciar a página de Habilidades 🔥  ⚡  ⛈️  🌬️

        import { habilidadesService } from "../js/api/habilidades.js";

        const mundoIdCriptografado = "{{ $mundo_id }}";
        const mundoDeHabilidades = habilidadesService(mundoIdCriptografado);

        document.addEventListener('DOMContentLoaded', () => {
            const habilidadesList = document.getElementById('habilidades-list');
            const form = document.getElementById('habilidade-form');
            const detailsElement = document.getElementById('add-habilidade-details');
            const summaryElement = detailsElement.querySelector('summary');
            const sectionToAnimate = detailsElement.querySelector('section');
            const inputId = document.getElementById('habilidade-id');
            const inputNome = document.getElementById('nome');
            const inputSlug = document.getElementById('slug');
            const inputDescricao = document.getElementById('descricao');
            const inputBonus = document.getElementById('bonus');
            const buttonCancelar = document.getElementById('button-cancelar');

            // Função para renderizar as habilidades na página em formato de cards
            const renderHabilidades = async () => {
                try {
                    const habilidades = await mundoDeHabilidades.listarHabilidades();

                    habilidadesList.innerHTML = ''; // Limpa a lista
                    habilidades.forEach(habilidade => {
                        const card = document.createElement('div');
                        card.className = 'relative bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-cyan-400 flex flex-col h-full';
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
                        habilidadesList.appendChild(card);
                    });

                } catch (error) {
                    console.log('error Listagem ', error.message);
                }
            };

            // Evento de submit do formulário
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                const id = data.id ? parseInt(data.id) : null;
                if (id) {
                    // Lógica de edição
                    try {
                        const response = await mundoDeHabilidades.atualizarHabilidade(id, data);
                        alert('Habilidade atualizada com sucesso!');
                    } catch (error) {
                        console.log('error', error.message);
                        alert(`Não foi possível editar habilidade.`);
                    }
                } else {
                    // Lógica de criação
                    try {
                        const response = await mundoDeHabilidades.criarHabilidade(data);
                        alert('Habilidade salva com sucesso!');
                    } catch (error) {
                        console.log(error);
                        alert("Problema ao cadastrar")
                    }
                }

                inputId.value = ''
                form.reset();
                detailsElement.open = false; // Fecha o formulário
                renderHabilidades(); // Recarrega a lista
            });

            // Lógica para botões de edição e exclusão (usando event delegation)
            habilidadesList.addEventListener('click', async (e) => {
                const target = e.target;
                if (target.classList.contains('delete-btn')) {
                    const id = parseInt(target.dataset.id);
                    const confirmation = confirm('Tem certeza que deseja excluir esta habilidade?');
                    if (confirmation) {
                        console.log("Exclui");

                        const aq = await mundoDeHabilidades.excluirHabilidade(id);
                        console.log(`Habilidade com ID ${id} excluída.`, aq);
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
                        detailsElement.open = true; // Abre o formulário para edição
                    }
                }
                await renderHabilidades(); // Atualiza a lista
            });

            // Intercepta o clique da summary
            summaryElement.addEventListener('click', (e) => {
                e.preventDefault(); // Impede o comportamento padrão do details

                if (detailsElement.open) {
                    // Se está aberto, prepare-se para fechar
                    detailsElement.classList.add('animating-close');
                    sectionToAnimate.addEventListener('transitionend', () => {
                        detailsElement.open = false; // Fecha o details
                        detailsElement.classList.remove('animating-close');
                    }, {
                        once: true
                    });
                } else {
                    // Se está fechado, prepare-se para abrir
                    detailsElement.open = true; // Abre o details instantaneamente, mas invisível
                    detailsElement.classList.add('animating-open');
                    sectionToAnimate.addEventListener('transitionend', () => {
                        detailsElement.classList.remove('animating-open');
                    }, {
                        once: true
                    });
                }
            })

            // Lógica para fechar E limpar o formulário
            buttonCancelar.addEventListener('click', () => {
                // console.log("Cancela");
                // inputId.value = ''
                // form.reset();
                // detailsElement.open = false;
                inputId.value = '';
                form.reset();
                // Dispara a animação de fechamento
                if (detailsElement.open) {
                    detailsElement.classList.add('animating-close');
                    sectionToAnimate.addEventListener('transitionend', () => {
                        detailsElement.open = false;
                        detailsElement.classList.remove('animating-close');
                    }, {
                        once: true
                    });
                }
            });

            // Carrega as habilidades na inicialização da página
            renderHabilidades();
        });
    </script>
</body>

</html>