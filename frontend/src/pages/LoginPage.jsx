import { useState } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useToast } from '../context/ToastContext';

const LoginPage = () => {
    const { login } = useAuth();
    const { showToast } = useToast();
    const navigate = useNavigate();
    const location = useLocation();

    const [formData, setFormData] = useState({
        email: '',
        password: '',
        remember: false
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);

    const from = location.state?.from?.pathname || '/';

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value
        });
        if (errors[name]) {
            setErrors({ ...errors, [name]: null });
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        try {
            await login(formData);
            showToast('Successfully logged in', 'success');
            navigate(from, { replace: true });
        } catch (error) {
            if (error.response?.status === 422) {
                setErrors(error.response.data.errors);
            } else {
                showToast(error.response?.data?.message || 'Login failed. Please check your credentials.', 'error');
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-gray-50 min-h-screen py-16 flex items-center justify-center">
            <div className="max-w-md w-full px-4">
                
                <div className="text-center mb-8">
                    <h1 className="font-outfit text-3xl font-bold tracking-tight uppercase text-black mb-2">Welcome Back</h1>
                    <p className="text-gray-500 text-sm">Please sign in to access your account.</p>
                </div>

                <div className="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
                    <form onSubmit={handleSubmit} autoComplete="off" className="space-y-5">
                        <div>
                            <label className="block text-xs font-bold text-gray-900 mb-2 uppercase tracking-wider">Email Address</label>
                            <input 
                                type="email" 
                                name="email"
                                value={formData.email}
                                onChange={handleChange}
                                required
                                autoComplete="off"
                                className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors"
                                placeholder="you@example.com"
                            />
                            {errors.email && <p className="text-red-600 text-xs mt-1 font-medium">{errors.email[0]}</p>}
                        </div>

                        <div>
                            <div className="flex justify-between items-center mb-2">
                                <label className="block text-xs font-bold text-gray-900 uppercase tracking-wider">Password</label>
                                <a href="#" className="text-xs font-medium text-gray-600 hover:text-black hover:underline transition-colors">Forgot Password?</a>
                            </div>
                            <input 
                                type="password" 
                                name="password"
                                value={formData.password}
                                onChange={handleChange}
                                required
                                autoComplete="new-password"
                                className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors"
                                placeholder="••••••••"
                            />
                            {errors.password && <p className="text-red-600 text-xs mt-1 font-medium">{errors.password[0]}</p>}
                        </div>

                        <div className="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                checked={formData.remember}
                                onChange={handleChange}
                                className="h-4 w-4 text-black focus:ring-black border-gray-300 rounded cursor-pointer"
                            />
                            <label htmlFor="remember" className="ml-2 block text-xs font-medium text-gray-700 cursor-pointer">
                                Remember me for 30 days
                            </label>
                        </div>

                        <button 
                            type="submit" 
                            disabled={loading}
                            className={`w-full bg-black text-white py-3.5 rounded-lg font-bold text-sm tracking-wider uppercase transition-colors shadow-sm ${loading ? 'opacity-70 cursor-not-allowed' : 'hover:bg-gray-900'}`}
                        >
                            {loading ? 'Signing In...' : 'Sign In'}
                        </button>
                    </form>

                    <div className="mt-8 text-center text-xs text-gray-600">
                        Don't have an account?{' '}
                        <Link to="/register" className="font-bold text-black hover:underline">
                            Create an account
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    );
};

export default LoginPage;
