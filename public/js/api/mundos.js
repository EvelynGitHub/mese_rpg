import { fetchApi } from './auth.js';

/**
 * Lista todos os mundos
 * @returns {Promise}
 */
export const listarMundos = async (offset = 0) => {
    if (offset) {
        return await fetchApi(`/mundos?offset=${offset}`);
    }
    return await fetchApi('/mundos');
};

/**
 * Cria um novo mundo
 * @param {Object} mundoData
 * @returns {Promise}
 */
export const criarMundo = async (mundoData) => {
    return await fetchApi('/mundos', {
        method: 'POST',
        body: JSON.stringify(mundoData)
    });
};

/**
 * Atualiza um mundo existente
 * @param {number} id
 * @param {Object} mundoData
 * @returns {Promise}
 */
export const atualizarMundo = async (id, mundoData) => {
    return await fetchApi(`/mundos/${id}`, {
        method: 'PATCH',
        body: JSON.stringify(mundoData)
    });
};

/**
 * Exclui um mundo
 * @param {number} id
 * @returns {Promise}
 */
export const excluirMundo = async (id) => {
    return await fetchApi(`/mundos/${id}`, {
        method: 'DELETE'
    });
};

/**
 * Adiciona um membro ao mundo
 * @param {number} mundoId
 * @param {Object} membroData
 * @returns {Promise}
 */
export const adicionarMembro = async (mundoId, membroData) => {
    return await fetchApi(`/mundos/${mundoId}/membros`, {
        method: 'POST',
        body: JSON.stringify(membroData)
    });
};
