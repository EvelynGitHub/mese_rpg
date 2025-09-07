import { fetchApi } from './auth.js';

/**
 * Retorna um objeto de serviço para gerenciar itens de um mundo específico.
 * @param {string} mundoIdCriptografado O ID do mundo criptografado.
 * @returns {Object} Um objeto com as funções de gerenciamento de itens.
 */
export const itensService = (mundoIdCriptografado) => {

    const urlBase = `/mundos/${mundoIdCriptografado}/itens`;

    const listarItens = async (offset = 0) => {
        const url = offset
            ? `${urlBase}?offset=${offset}`
            : `${urlBase}`;
        return await fetchApi(url);
    };

    const criarItem = async (itemData) => {
        return await fetchApi(`${urlBase}`, {
            method: 'POST',
            body: JSON.stringify(itemData)
        });
    };

    const atualizarItem = async (id, itemData) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(itemData)
        });
    };

    const obterItem = async (id) => {
        return await fetchApi(`${urlBase}/${id}`);
    };

    const excluirItem = async (id) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'DELETE'
        });
    };

    return {
        listarItens,
        criarItem,
        atualizarItem,
        obterItem,
        excluirItem,
    };
};
