const BASE_URL = '/api';

/**
 * Obtém os headers padrão para requisições autenticadas
 * @returns {Object}
 */
export const getHeaders = () => {
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        // ...(token ? { 'Authorization': `Bearer ${token}` } : {})
    };
};

/**
 * Faz uma requisição à API
 * @param {string} endpoint
 * @param {Object} options
 * @returns {Promise}
 */
export const fetchApi = async (endpoint, options = {}) => {
    const url = `${BASE_URL}${endpoint}`;

    try {
        const response = await fetch(url, {
            ...options,
            headers: getHeaders(),
            credentials: 'include',
        });

        // 1. Lida com o status 401 Unauthorized
        if (response.status === 401) {
            // Redireciona para a página de login
            window.location.href = '/login';
            // Retorna um erro para parar a execução
            throw new Error('Acesso não autorizado. Redirecionando para login.');
        }

        // 2. Lida com o status 204 No Content
        // A resposta 204 não tem corpo, então não podemos chamar response.json()
        if (response.status === 204) {
            return null;
        }

        // 3. Lida com outros erros de resposta
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Erro na requisição');
        }

        // 4. Retorna o JSON da resposta
        return await response.json();

    } catch (error) {
        console.error('Erro em fetchApi:', error);
        throw error; // Propaga o erro para ser capturado no código que chamou a função
    }
};

/**
 * Realiza o login do usuário
 * @param {Object} credentials
 * @returns {Promise}
 */
export const login = async (credentials) => {
    const data = await fetchApi('/auth/login', {
        method: 'POST',
        // credentials: 'include',
        body: JSON.stringify(credentials)
    });

    return data;
};

/**
 * Registra um novo usuário
 * @param {Object} userData
 * @returns {Promise}
 */
export const register = async (userData) => {
    return await fetchApi('/auth/register', {
        method: 'POST',
        body: JSON.stringify(userData)
    });
};

/**
 * Obtém informações do usuário logado
 * @returns {Promise}
 */
export const getMe = async () => {
    return await fetchApi('/auth/me');
};

/**
 * Faz logout do usuário
 */
export const logout = async () => {
    window.location.href = '/login';
    return await fetchApi('/auth/logout');
};
