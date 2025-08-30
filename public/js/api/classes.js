import { fetchApi } from './auth.js';

/**
 * Retorna um objeto de serviço para gerenciar classes de um mundo específico.
 * @param {string} mundoIdCriptografado O ID do mundo criptografado.
 * @returns {Object} Um objeto com as funções de gerenciamento de classes.
 */
export const classesService = (mundoIdCriptografado) => {

    const urlBase = `/mundos/${mundoIdCriptografado}/classes`;

    const listarClasses = async (offset = 0) => {
        const url = offset
            ? `${urlBase}?offset=${offset}`
            : `${urlBase}`;
        return await fetchApi(url);
    };

    const criarClasse = async (classeData) => {
        return await fetchApi(`${urlBase}`, {
            method: 'POST',
            body: JSON.stringify(classeData)
        });
    };

    const atualizarClasse = async (id, classeData) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'PATCH',
            body: JSON.stringify(classeData)
        });
    };

    const obterClasse = async (id) => {
        return await fetchApi(`${urlBase}/${id}`);
    };

    const excluirClasse = async (id) => {
        return await fetchApi(`${urlBase}/${id}`, {
            method: 'DELETE'
        });
    };

    const adicionarAtributoAClasse = async (classeId, atributoData) => {
        return await fetchApi(`${urlBase}/${classeId}/atributos`, {
            method: 'POST',
            body: JSON.stringify(atributoData)
        });
    };

    return {
        listarClasses,
        criarClasse,
        atualizarClasse,
        obterClasse,
        excluirClasse,
        adicionarAtributoAClasse,
    };
};
