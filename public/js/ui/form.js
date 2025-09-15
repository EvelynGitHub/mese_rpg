const preencherFormulario = async (objeto, idFormulario, callIfValueObject) => {

    const formulario = (idFormulario instanceof HTMLFormElement) ? idFormulario : document.getElementById(idFormulario);
    if (!formulario) {
        console.error(`Formulário com ID "${idFormulario}" não encontrado.`);
        return;
    }

    // Itera sobre cada chave e valor do objeto JSON
    for (const chave in objeto) {
        const valor = objeto[chave];

        // if (typeof valor === 'object' && valor !== null && !Array.isArray(valor)) {
        if (typeof valor === 'object' && valor !== null) {
            // Chama uma função para lidar com as propriedades dinâmicas
            console.warn(`Chave "${chave}" é um objeto. Chamando função para lidar com propriedades dinâmicas.`);
            callIfValueObject(valor, formulario, chave);
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
            else {
                console.warn(`Campo para a chave "${chave}" não encontrado no formulário.`);
            }
        }
    }
};

export { preencherFormulario };
