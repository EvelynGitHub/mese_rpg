import { fetchApi } from './auth.js';

/**
 * Retorna um objeto de serviço para gerenciar atributos de um mundo específico.
 * @param {string} mundoIdCriptografado O ID do mundo criptografado.
 * @returns {Object} Um objeto com as funções de gerenciamento de atributos.
 */
export const atributosService = (mundoIdCriptografado) => {

    const urlBase = `/mundos/${mundoIdCriptografado}/atributos`;

    const listarAtributos = async (offset = 0) => {
        const url = offset
            ? `${urlBase}?offset=${offset}`
            : `${urlBase}`;
        return await fetchApi(url);
    };

    const criarAtributo = async (atributoData) => {
        return await fetchApi(`${urlBase}`, {
            method: 'POST',
            body: JSON.stringify(atributoData)
        });
    };

    const atualizarAtributo = async (id, atributoData) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(atributoData)
        });
    };

    const obterAtributo = async (id) => {
        return await fetchApi(`${urlBase}/${id}`);
    };

    const excluirAtributo = async (id) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'DELETE'
        });
    };

    return {
        listarAtributos,
        criarAtributo,
        atualizarAtributo,
        obterAtributo,
        excluirAtributo,
    };
};
