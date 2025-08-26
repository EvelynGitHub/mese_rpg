<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG - Acesso</title>
    <!-- Inclui o Tailwind CSS para o estilo moderno e responsivo -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Importa a fonte "Inter" do Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0c0a09;
            /* Fundo bem escuro */
        }

        .text-gradient {
            background-image: linear-gradient(to right, #6EE7B7, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="antialiased flex items-center justify-center min-h-screen p-4">

    <!-- Container principal de autenticação -->
    <div class="w-full max-w-md bg-slate-900 p-8 md:p-10 rounded-xl shadow-2xl border border-slate-700">
        <div class="text-center mb-8">
            <h1 class="text-5xl font-extrabold tracking-tight mb-2">
                <span class="text-gradient">RPG</span>
            </h1>
            <p id="subtitle" class="text-gray-400">
                Acesse sua jornada
            </p>
        </div>

        <!-- Formulário de Login -->
        <div id="login-container">
            <form id="login-form" class="space-y-6">
                <!-- Campo de E-mail -->
                <div>
                    <label for="login-email" class="block text-gray-400 text-sm font-medium mb-1">E-mail</label>
                    <input type="email" id="login-email" name="email" placeholder="seu-email@exemplo.com" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                </div>

                <!-- Campo de Senha -->
                <div>
                    <label for="login-password" class="block text-gray-400 text-sm font-medium mb-1">Senha</label>
                    <input type="password" id="login-password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                </div>

                <!-- Botão de Login -->
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                    Entrar
                </button>
            </form>

            <!-- Links para outras ações -->
            <div class="mt-8 text-center text-sm space-y-2">
                <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors duration-300">Esqueceu sua
                    senha?</a>
                <p class="text-gray-400">
                    Não tem uma conta?
                    <a href="#" id="show-register-form"
                        class="text-blue-400 font-medium hover:text-blue-300 transition-colors duration-300">Crie uma
                        aqui</a>
                </p>
            </div>
        </div>

        <!-- Formulário de Registro (inicialmente invisível) -->
        <div id="register-container" class="hidden">
            <form id="register-form" class="space-y-6">
                <!-- Campo de Nome -->
                <div>
                    <label for="register-name" class="block text-gray-400 text-sm font-medium mb-1">Nome</label>
                    <input type="text" id="register-name" name="name" placeholder="Seu nome" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                </div>
                <!-- Campo de E-mail -->
                <div>
                    <label for="register-email" class="block text-gray-400 text-sm font-medium mb-1">E-mail</label>
                    <input type="email" id="register-email" name="email" placeholder="seu-email@exemplo.com" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                </div>

                <!-- Campo de Senha -->
                <div>
                    <label for="register-password" class="block text-gray-400 text-sm font-medium mb-1">Senha</label>
                    <input type="password" id="register-password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                </div>

                <!-- Campo de Confirmação de Senha -->
                <div>
                    <label for="register-password-confirm" class="block text-gray-400 text-sm font-medium mb-1">Confirme
                        a Senha</label>
                    <input type="password" id="register-password-confirm" name="password-confirm" placeholder="••••••••"
                        required
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-300">
                </div>

                <!-- Botão de Registro -->
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                    Registrar
                </button>
            </form>

            <!-- Links para outras ações -->
            <div class="mt-8 text-center text-sm">
                <p class="text-gray-400">
                    Já tem uma conta?
                    <a href="#" id="show-login-form"
                        class="text-blue-400 font-medium hover:text-blue-300 transition-colors duration-300">Entrar
                        aqui</a>
                </p>
            </div>
        </div>

        <!-- Caixa de Mensagens -->
        <div id="message-box" class="hidden mt-6 p-4 rounded-lg text-center" role="alert"></div>
    </div>

    <!-- Inclui o serviço de autenticação -->
    <script type="module">

        import { login, register } from "./js/api/auth.js";

        // Referências aos elementos da interface
        const loginContainer = document.getElementById('login-container');
        const registerContainer = document.getElementById('register-container');
        const showRegisterLink = document.getElementById('show-register-form');
        const showLoginLink = document.getElementById('show-login-form');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const subtitle = document.getElementById('subtitle');
        const messageBox = document.getElementById('message-box');

        /**
         * Alterna a visibilidade entre os formulários de login e registro.
         * @param {string} view - A visualização a ser exibida ('login' ou 'register').
         */
        function toggleAuthForm(view) {
            if (view === 'login') {
                loginContainer.classList.remove('hidden');
                registerContainer.classList.add('hidden');
                subtitle.innerText = 'Acesse sua jornada';
            } else if (view === 'register') {
                loginContainer.classList.add('hidden');
                registerContainer.classList.remove('hidden');
                subtitle.innerText = 'Crie sua conta';
            }
            messageBox.classList.add('hidden'); // Limpa a mensagem ao trocar de formulário
        }

        /**
         * Função para exibir mensagens de sucesso ou erro
         */
        function showMessage(type, message) {
            messageBox.innerHTML = '';
            messageBox.classList.remove('hidden', 'bg-red-900', 'text-red-300', 'bg-green-900', 'text-green-300');

            if (type === 'success') {
                messageBox.classList.add('bg-green-900', 'text-green-300');
            } else {
                messageBox.classList.add('bg-red-900', 'text-red-300');
            }

            messageBox.innerHTML = `<strong>${type === 'success' ? 'Sucesso' : 'Erro'}!</strong> ${message}`;
        }

        // Event listener para mostrar o formulário de registro
        showRegisterLink.addEventListener('click', function (e) {
            e.preventDefault();
            toggleAuthForm('register');
        });

        // Event listener para mostrar o formulário de login
        showLoginLink.addEventListener('click', function (e) {
            e.preventDefault();
            toggleAuthForm('login');
        });

        // Event listener para o envio do formulário de login
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            messageBox.innerHTML = '';
            messageBox.classList.add('hidden');

            try {
                const response = await login({ email, senha: password });
                showMessage('success', 'Login realizado com sucesso!');

                // Redireciona para a dashboard após 1 segundo
                setTimeout(() => {
                    window.location.href = '/home';
                }, 1000);
            } catch (error) {
                showMessage('error', error.message);
            }
        });

        // Event listener para o envio do formulário de registro
        registerForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const nome = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const passwordConfirm = document.getElementById('register-password-confirm').value;

            messageBox.innerHTML = '';
            messageBox.classList.add('hidden');

            // Validação simulada para o registro
            if (password !== passwordConfirm) {
                showMessage('error', 'As senhas não coincidem.');
                return;
            }

            try {
                await register({ nome, email, senha: password });
                showMessage('success', `Usuário ${nome} registrado com sucesso! Você já pode fazer login.`);

                // Reseta o formulário
                registerForm.reset();

                // Volta para o formulário de login após 2 segundos
                setTimeout(() => {
                    toggleAuthForm('login');
                }, 2000);
            } catch (error) {
                showMessage('error', error.message);
            }
        });
    </script>
</body>

</html>
