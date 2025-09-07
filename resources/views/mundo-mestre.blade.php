<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Painel do Mestre</title>
    <!-- Inclui o Tailwind CSS para o estilo moderno e responsivo -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Importa a fonte "Inter" do Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            /* Fundo escuro */
            color: #E0E0E0;
            /* Texto claro */
        }

        .text-gradient {
            background-image: linear-gradient(to right, #6EE7B7, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="antialiased">

    <!-- Container principal -->
    <div class="min-h-screen flex flex-col items-center p-4 md:p-8">

        <!-- Cabeçalho fixo para o Painel do Mestre -->
        <header class="w-full max-w-7xl flex justify-between items-center py-6 px-4">
            <h1 class="text-3xl font-bold">
                <span class="text-gradient">RPG</span>
            </h1>
            <nav class="flex items-center space-x-4">
                <div class="text-white/70 py-2 px-4 rounded-lg bg-slate-800">
                    Papel: <span id="current-role">Mestre</span>
                </div>
            </nav>
        </header>

        <!-- Container para o conteúdo principal, que será o painel refatorado -->
        <main id="main-content" class="w-full max-w-7xl mt-8 px-4">
            <section class="text-center py-12 px-4 max-w-4xl mx-auto">
                <h2 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight text-white mb-4">
                    Painel do <span class="text-gradient">Mestre</span>
                </h2>
                <p class="text-lg text-gray-400 mb-8">
                    Gerencie todos os aspectos do mundo de <span class="text-gradient">Reino de Elara</span>.
                </p>
            </section>

            <!-- Cards de navegação para as páginas dedicadas -->
            <div class="w-full max-w-7xl mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card para Atributos -->
                <a href="/atributos/{{ $mundo_id }}"
                    class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-purple-400 transition-transform transform hover:scale-105 hover:shadow-2xl">
                    <h4 class="text-2xl font-semibold text-white mb-2">Atributos</h4>
                    <p class="text-white/70">
                        Gerenciar atributos dinâmicos do mundo.
                    </p>
                    <span class="block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </a>
                <!-- NOVO: Card para Habilidades -->
                <a href="/habilidades/{{ $mundo_id }}"
                    class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-cyan-400 transition-transform transform hover:scale-105 hover:shadow-2xl">
                    <h4 class="text-2xl font-semibold text-white mb-2">Habilidades</h4>
                    <p class="text-white/70">
                        Criar e gerenciar habilidades de personagens.
                    </p>
                    <span class="block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </a>
                <!-- Card para Itens e Armas -->
                <a href="/itens-armas/{{ $mundo_id }}"
                    class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-yellow-400 transition-transform transform hover:scale-105 hover:shadow-2xl">
                    <h4 class="text-2xl font-semibold text-white mb-2">Itens e Armas</h4>
                    <p class="text-white/70">
                        Adicionar e modificar itens e armas do mundo.
                    </p>
                    <span class="block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </a>
                <!-- NOVO: Card para Origens -->
                <a href="/origens/{{ $mundo_id }}"
                    class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-orange-400 transition-transform transform hover:scale-105 hover:shadow-2xl">
                    <h4 class="text-2xl font-semibold text-white mb-2">Origens</h4>
                    <p class="text-white/70">
                        Definir e editar as origens dos personagens.
                    </p>
                    <span class="block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </a>
                <!-- Card para Classes -->
                <a href="/classes/{{ $mundo_id }}"
                    class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-blue-400 transition-transform transform hover:scale-105 hover:shadow-2xl">
                    <h4 class="text-2xl font-semibold text-white mb-2">Classes</h4>
                    <p class="text-white/70">
                        Definir e editar classes de personagens.
                    </p>
                    <span class="block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </a>
                <!-- Card para Personagens -->
                <a href="/personagens/{{ $mundo_id }}"
                    class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-teal-400 transition-transform transform hover:scale-105 hover:shadow-2xl">
                    <h4 class="text-2xl font-semibold text-white mb-2">Personagens</h4>
                    <p class="text-white/70">
                        Visualizar e editar a ficha de todos os personagens.
                    </p>
                    <span class="block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </a>
                <!-- Card para Campanhas e Sessões -->
                <a href="/campanhas/{{ $mundo_id }}"
                    class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-emerald-400 transition-transform transform hover:scale-105 hover:shadow-2xl">
                    <h4 class="text-2xl font-semibold text-white mb-2">Campanhas e Sessões</h4>
                    <p class="text-white/70">
                        Organizar sessões e campanhas.
                    </p>
                    <span class="block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </a>
                <!-- Card para NPCs -->
                <a href="/npcs/{{ $mundo_id }}"
                    class="bg-slate-800 p-6 rounded-xl shadow-lg border-t-4 border-t-red-400 transition-transform transform hover:scale-105 hover:shadow-2xl">
                    <h4 class="text-2xl font-semibold text-white mb-2">NPCs</h4>
                    <p class="text-white/70">
                        Criar e gerenciar personagens não-jogáveis.
                    </p>
                    <span class="block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                        Ver Mais &rarr;
                    </span>
                </a>
            </div>
        </main>
    </div>
</body>

</html>