import { fetchApi } from './auth.js';

/**
 * Retorna um objeto de serviço para gerenciar habilidades de um mundo específico.
 * Todas as funções internas já estarão configuradas com o mundoId criptografado.
 * @param {string} mundoIdCriptografado O ID do mundo criptografado.
 * @returns {Object} Um objeto com as funções de gerenciamento de habilidades.
 */
export const habilidadesService = (mundoIdCriptografado) => {

    const urlBase = `/mundos/${mundoIdCriptografado}/habilidades`;
    /**
     * Lista todas as habilidades de um mundo
     * @param {number} offset
     * @returns {Promise}
     */
    const listarHabilidades = async (offset = 0) => {
        const url = offset
            ? `${urlBase}?offset=${offset}`
            : `${urlBase}`;
        return await fetchApi(url);
    };

    /**
     * Cria nova habilidade
     * @param {Object} habilidadeData
     * @returns {Promise}
     */
    const criarHabilidade = async (habilidadeData) => {
        return await fetchApi(`${urlBase}`, {
            method: 'POST',
            body: JSON.stringify(habilidadeData)
        });
    };

    /**
     * Atualiza uma habilidade existente
     * @param {number} id habilidade ID
     * @param {Object} habilidadeData
     * @returns {Promise}
     */
    const atualizarHabilidade = async (id, habilidadeData) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(habilidadeData)
        });
    };

    /**
     * Obtém uma habilidade existente
     * @param {number} id habilidade ID
     * @returns {Promise}
     */
    const obterHabilidade = async (id) => {
        return await fetchApi(`${urlBase}/${id}`);
    };


    /**
     * Exclui uma habilidade
     * @param {number} id
     * @returns {Promise}
     */
    const excluirHabilidade = async (id) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'DELETE'
        });
    };

    // Retorna um objeto com todas as funções para uso externo
    return {
        listarHabilidades,
        criarHabilidade,
        atualizarHabilidade,
        obterHabilidade,
        excluirHabilidade,
    };
};
