import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useToast } from '../context/ToastContext';

const RegisterPage = () => {
    const { register } = useAuth();
    const { showToast } = useToast();
    const navigate = useNavigate();

    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
        if (errors[e.target.name]) {
            setErrors({ ...errors, [e.target.name]: null });
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        try {
            await register(formData);
            showToast('Account created successfully!', 'success');
            navigate('/');
        } catch (error) {
            if (error.response?.status === 422) {
                setErrors(error.response.data.errors);
            } else {
                showToast(error.response?.data?.message || 'Registration failed.', 'error');
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-gray-50 min-h-screen py-16 flex items-center justify-center">
            <div className="max-w-md w-full px-4">
                
                <div className="text-center mb-8">
                    <h1 className="font-outfit text-3xl font-bold tracking-tight uppercase text-black mb-2">Create Account</h1>
                    <p className="text-gray-500 text-sm">Join Styleora today and start shopping.</p>
                </div>

                <div className="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div>
                            <label className="block text-xs font-bold text-gray-900 mb-2 uppercase tracking-wider">Full Name</label>
                            <input 
                                type="text" 
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                                required
                                className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors"
                                placeholder="John Doe"
                            />
                            {errors.name && <p className="text-red-600 text-xs mt-1 font-medium">{errors.name[0]}</p>}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-gray-900 mb-2 uppercase tracking-wider">Email Address</label>
                            <input 
                                type="email" 
                                name="email"
                                value={formData.email}
                                onChange={handleChange}
                                required
                                className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors"
                                placeholder="you@example.com"
                            />
                            {errors.email && <p className="text-red-600 text-xs mt-1 font-medium">{errors.email[0]}</p>}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-gray-900 mb-2 uppercase tracking-wider">Password</label>
                            <input 
                                type="password" 
                                name="password"
                                value={formData.password}
                                onChange={handleChange}
                                required
                                className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors"
                                placeholder="••••••••"
                            />
                            {errors.password && <p className="text-red-600 text-xs mt-1 font-medium">{errors.password[0]}</p>}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-gray-900 mb-2 uppercase tracking-wider">Confirm Password</label>
                            <input 
                                type="password" 
                                name="password_confirmation"
                                value={formData.password_confirmation}
                                onChange={handleChange}
                                required
                                className="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-colors"
                                placeholder="••••••••"
                            />
                        </div>

                        <div className="text-xs text-gray-500">
                            By registering, you agree to our <a href="#" className="text-black font-semibold hover:underline">Terms of Service</a> and <a href="#" className="text-black font-semibold hover:underline">Privacy Policy</a>.
                        </div>

                        <button 
                            type="submit" 
                            disabled={loading}
                            className={`w-full bg-black text-white py-3.5 rounded-lg font-bold text-sm tracking-wider uppercase transition-colors shadow-sm ${loading ? 'opacity-70 cursor-not-allowed' : 'hover:bg-gray-900'}`}
                        >
                            {loading ? 'Creating Account...' : 'Sign Up'}
                        </button>
                    </form>

                    <div className="mt-8 text-center text-xs text-gray-600">
                        Already have an account?{' '}
                        <Link to="/login" className="font-bold text-black hover:underline">
                            Sign In
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    );
};

export default RegisterPage;
