import { fetchApi } from './auth.js';

/**
 * Retorna um objeto de serviço para gerenciar origens de um mundo específico.
 * @param {string} mundoIdCriptografado O ID do mundo criptografado.
 * @returns {Object} Um objeto com as funções de gerenciamento de origens.
 */
export const origensService = (mundoIdCriptografado) => {

    const urlBase = `/mundos/${mundoIdCriptografado}/origens`;

    const listarOrigens = async (offset = 0) => {
        const url = offset
            ? `${urlBase}?offset=${offset}`
            : `${urlBase}`;
        return await fetchApi(url);
    };

    const criarOrigem = async (origemData) => {
        return await fetchApi(`${urlBase}`, {
            method: 'POST',
            body: JSON.stringify(origemData)
        });
    };

    const atualizarOrigem = async (id, origemData) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(origemData)
        });
    };

    const obterOrigem = async (id) => {
        return await fetchApi(`${urlBase}/${id}`);
    };

    const excluirOrigem = async (id) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'DELETE'
        });
    };

    const listarEfeitosDeOrigem = async (origemId) => {
        return await fetchApi(`${urlBase}/${origemId}/efeitos`);
    };

    const criarEfeitoEmOrigem = async (origemId, efeitoData) => {
        return await fetchApi(`${urlBase}/${origemId}/efeitos`, {
            method: 'POST',
            body: JSON.stringify(efeitoData)
        });
    };

    const atualizarEfeitoEmOrigem = async (origemId, efeitoId, efeitoData) => {
        return await fetchApi(`${urlBase}/${origemId}/efeitos/${efeitoId}`, {
            method: 'PATCH',
            body: JSON.stringify(efeitoData)
        });
    };

    const excluirEfeitoDeOrigem = async (origemId, efeitoId) => {
        return await fetchApi(`${urlBase}/${origemId}/efeitos/${efeitoId}`, {
            method: 'DELETE'
        });
    };

    return {
        listarOrigens,
        criarOrigem,
        atualizarOrigem,
        obterOrigem,
        excluirOrigem,
        listarEfeitosDeOrigem,
        criarEfeitoEmOrigem,
        atualizarEfeitoEmOrigem,
        excluirEfeitoDeOrigem,
    };
};
