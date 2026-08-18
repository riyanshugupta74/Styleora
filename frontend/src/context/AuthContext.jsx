import { createContext, useContext, useState, useEffect } from 'react';
import api, { getCsrfToken } from '../services/api';

const AuthContext = createContext();

export const useAuth = () => useContext(AuthContext);

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [isAdmin, setIsAdmin] = useState(false);
    const [loading, setLoading] = useState(true);

    const checkUser = async () => {
        try {
            const response = await api.get('/api/user');
            setUser(response.data.user);
            setIsAdmin(response.data.isAdmin || false);
        } catch (error) {
            setUser(null);
            setIsAdmin(false);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        checkUser();
    }, []);

    const login = async (credentials) => {
        await getCsrfToken();
        const response = await api.post('/api/login', credentials);
        setUser(response.data.user);
        setIsAdmin(response.data.isAdmin);
        return response.data;
    };

    const register = async (data) => {
        await getCsrfToken();
        const response = await api.post('/api/register', data);
        setUser(response.data.user);
        return response.data;
    };

    const logout = async () => {
        await api.post('/api/logout');
        setUser(null);
        setIsAdmin(false);
    };

    return (
        <AuthContext.Provider value={{ user, isAdmin, loading, login, register, logout, checkUser, isAuthenticated: !!user }}>
            {children}
        </AuthContext.Provider>
    );
};
