import { fetchApi } from './auth.js';

/**
 * Retorna um objeto de serviço para gerenciar NPCs de um mundo específico.
 * @param {string} mundoIdCriptografado O ID do mundo criptografado.
 * @returns {Object} Um objeto com as funções de gerenciamento de NPCs.
 */
export const npcsService = (mundoIdCriptografado) => {

    const urlBase = `/mundos/${mundoIdCriptografado}/npcs`;

    const listarNpcs = async (offset = 0) => {
        const url = offset
            ? `${urlBase}?offset=${offset}`
            : `${urlBase}`;
        return await fetchApi(`${url}`);
    };

    const criarNpc = async (npcData) => {
        return await fetchApi(`${urlBase}`, {
            method: 'POST',
            body: JSON.stringify(npcData)
        });
    };

    const atualizarNpc = async (id, npcData) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(npcData)
        });
    };

    const obterNpc = async (id) => {
        return await fetchApi(`${urlBase}/${id}`);
    };

    const excluirNpc = async (id) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'DELETE'
        });
    };

    return {
        listarNpcs,
        criarNpc,
        atualizarNpc,
        obterNpc,
        excluirNpc,
    };
};
