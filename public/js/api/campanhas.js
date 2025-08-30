import { fetchApi } from './auth.js';

/**
 * Retorna um objeto de serviço para gerenciar campanhas de um mundo específico.
 * @param {string} mundoIdCriptografado O ID do mundo criptografado.
 * @returns {Object} Um objeto com as funções de gerenciamento de campanhas.
 */
export const campanhasService = (mundoIdCriptografado) => {

    const urlBase = `/mundos/${mundoIdCriptografado}/campanhas`;

    const listarCampanhas = async () => {
        return await fetchApi(`${urlBase}`);
    };

    const criarCampanha = async (campanhaData) => {
        return await fetchApi(`${urlBase}`, {
            method: 'POST',
            body: JSON.stringify(campanhaData)
        });
    };

    const atualizarCampanha = async (id, campanhaData) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(campanhaData)
        });
    };

    const obterCampanha = async (id) => {
        return await fetchApi(`${urlBase}/${id}`);
    };

    const excluirCampanha = async (id) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'DELETE'
        });
    };

    return {
        listarCampanhas,
        criarCampanha,
        atualizarCampanha,
        obterCampanha,
        excluirCampanha,
    };
};
