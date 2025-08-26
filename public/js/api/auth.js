const BASE_URL = '/api';
const TOKEN_KEY = 'rpg_token';

/**
 * Obtém o token JWT armazenado
 * @returns {string|null}
 */
export const getToken = () => localStorage.getItem(TOKEN_KEY);

/**
 * Armazena o token JWT
 * @param {string} token
 */
export const setToken = (token) => localStorage.setItem(TOKEN_KEY, token);

/**
 * Remove o token JWT
 */
export const removeToken = () => localStorage.removeItem(TOKEN_KEY);

/**
 * Obtém os headers padrão para requisições autenticadas
 * @returns {Object}
 */
export const getHeaders = () => {
    // const token = getToken();
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
    const response = await fetch(url, {
        ...options,
        headers: getHeaders(),
        credentials: 'include',
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.message || 'Erro na requisição');
    }

    return data;
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

    // if (data.token) {
    //     setToken(data.token);
    // }

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
 * Verifica se o usuário está autenticado
 * @returns {boolean}
 */
export const isAuthenticated = () => {
    return !!getToken();
};

/**
 * Faz logout do usuário
 */
export const logout = async () => {
    // removeToken();
    // window.location.href = '/login';
    return await fetchApi('/auth/logout');
};
