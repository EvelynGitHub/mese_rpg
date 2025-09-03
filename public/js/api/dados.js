import { fetchApi } from './auth.js';

export const dadosService = () => {

    const listarDados = async () => {
        return await fetchApi(`/dados`);
    };

    return {
        listarDados,
    };
};
