import axios from 'axios';

const getBaseUrl = () => {
    const defaultPort = '8000';
    let base = import.meta.env.VITE_API_URL || 'http://localhost:8000';
    try {
        const url = new URL(base);
        if (typeof window !== 'undefined' && window.location.hostname) {
            url.hostname = window.location.hostname;
        }
        return url.toString().replace(/\/$/, '');
    } catch (e) {
        return base;
    }
};

const api = axios.create({
    baseURL: getBaseUrl(),
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

api.interceptors.request.use((config) => {
    if (typeof document !== 'undefined') {
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'XSRF-TOKEN' && value) {
                config.headers['X-XSRF-TOKEN'] = decodeURIComponent(value);
                break;
            }
        }
    }
    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            // The request was made and the server responded with a status code
            // that falls out of the range of 2xx
            console.error(
                `API Error [${error.response.status}]: ${error.config.method.toUpperCase()} ${error.config.url}`, 
                error.response.data
            );
            if (error.response.status === 401) {
                // Uncomment to redirect to login on 401
                // window.location.href = '/login';
            }
        } else if (error.request) {
            // The request was made but no response was received
            console.error(`API Network Error: ${error.config.method.toUpperCase()} ${error.config.url}`, error.request);
        } else {
            // Something happened in setting up the request that triggered an Error
            console.error('API Request Error:', error.message);
        }
        return Promise.reject(error);
    }
);

export const getCsrfToken = async () => {
    return api.get('/sanctum/csrf-cookie');
};

export default api;
