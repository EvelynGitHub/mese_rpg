// /js/ui/notificacao.js

function criarElementosUI() {
    if (document.getElementById('toast') || document.getElementById('confirm-modal')) return;

    // Toast
    const toast = document.createElement('div');
    toast.id = 'toast';
    toast.className =
        'fixed top-5 right-5 z-50 bg-green-600 text-white px-4 py-3 rounded shadow-lg hidden transition-opacity duration-300 opacity-0';
    toast.innerHTML = '<span id="toast-message"></span>';
    document.body.appendChild(toast);

    // Modal de Confirmação
    const confirmModal = document.createElement('div');
    confirmModal.id = 'confirm-modal';
    confirmModal.className =
        'modal-overlay fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50 hidden';
    confirmModal.innerHTML = `
        <div class="bg-slate-800 p-6 rounded-xl shadow-lg border border-slate-700 max-w-sm w-full text-center">
            <p class="text-white mb-6" id="confirm-message">Você tem certeza?</p>
            <div class="flex justify-center gap-4">
                <button id="confirm-yes" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Sim</button>
                <button id="confirm-no" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold">Cancelar</button>
            </div>
        </div>
    `;
    document.body.appendChild(confirmModal);
}

// Notificação tipo toast
export function notificar(mensagem, tipo = 'sucesso') {
    criarElementosUI();

    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    toastMessage.textContent = mensagem;

    // ⏱ tempo base + extra por quantidade de caracteres
    const tempoBase = 3000; // 3s mínimo
    const tempoExtraPorChar = 50; // +50ms por caractere
    const tempoMaximo = 10000; // 10s máximo
    const duracao = Math.min(
        tempoBase + mensagem.length * tempoExtraPorChar,
        tempoMaximo
    );

    toast.className =
        `fixed top-5 right-5 z-50 text-white px-4 py-3 rounded shadow-lg transition-opacity duration-300 opacity-0
        max-w-sm break-words whitespace-normal
        ${tipo === 'erro' ? 'bg-red-600' : 'bg-green-600'}`;

    toast.classList.remove('hidden');
    requestAnimationFrame(() => {
        toast.classList.add('opacity-100');
    });

    setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, duracao);
}

// Modal de confirmação com Promise
export function confirmar(mensagem = 'Tem certeza?') {
    return new Promise((resolve) => {
        criarElementosUI();

        const modal = document.getElementById('confirm-modal');
        const messageEl = document.getElementById('confirm-message');
        const yesBtn = document.getElementById('confirm-yes');
        const noBtn = document.getElementById('confirm-no');

        messageEl.textContent = mensagem;
        modal.classList.remove('hidden');
        modal.classList.add('open');

        const fechar = () => {
            modal.classList.remove('open');
            setTimeout(() => modal.classList.add('hidden'), 200);
        };

        const limpar = () => {
            yesBtn.onclick = null;
            noBtn.onclick = null;
        };

        yesBtn.onclick = () => {
            limpar();
            fechar();
            resolve(true);
        };

        noBtn.onclick = () => {
            limpar();
            fechar();
            resolve(false);
        };
    });
}
