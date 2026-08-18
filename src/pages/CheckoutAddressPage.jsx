import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import { useToast } from '../context/ToastContext';
import CheckoutProgress from '../components/CheckoutProgress';

const CheckoutAddressPage = () => {
    const navigate = useNavigate();
    const { showToast } = useToast();
    
    const [addresses, setAddresses] = useState([]);
    const [loading, setLoading] = useState(true);
    
    // Form state
    const [formData, setFormData] = useState({
        full_name: '',
        phone: '',
        address_line_1: '',
        address_line_2: '',
        city: '',
        state: '',
        pincode: ''
    });
    const [errors, setErrors] = useState({});

    useEffect(() => {
        const fetchAddresses = async () => {
            setLoading(true);
            try {
                const response = await api.get('/api/checkout/address');
                setAddresses(response.data.addresses || []);
            } catch (error) {
                console.error('Failed to fetch addresses', error);
                if (error.response?.data?.error === 'Your cart is empty.') {
                    showToast('Your cart is empty', 'error');
                    navigate('/cart');
                } else {
                    showToast('Failed to load addresses', 'error');
                }
            } finally {
                setLoading(false);
            }
        };

        fetchAddresses();
    }, [navigate]);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
        // Clear error for this field
        if (errors[name]) {
            setErrors(prev => ({ ...prev, [name]: null }));
        }
    };

    const handleAddressSelect = async (addressId) => {
        try {
            const response = await api.post('/api/checkout/address', { address_id: addressId });
            if (response.data.success) {
                navigate('/checkout/payment');
            }
        } catch (error) {
            showToast(error.response?.data?.message || 'Failed to select address', 'error');
        }
    };

    const handleNewAddressSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        
        try {
            const response = await api.post('/api/checkout/address', formData);
            if (response.data.success) {
                navigate('/checkout/payment');
            }
        } catch (error) {
            if (error.response?.status === 422) {
                setErrors(error.response.data.errors);
            } else {
                showToast(error.response?.data?.message || 'Failed to add address', 'error');
            }
        }
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    return (
        <div className="bg-gray-50 min-h-screen pt-8 pb-20">
            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <CheckoutProgress />

                <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-8 border-b border-gray-100">
                        <h2 className="text-2xl font-outfit font-bold text-gray-900">Shipping Address</h2>
                        <p className="text-gray-500 text-sm mt-1">Where should we deliver your order?</p>
                    </div>

                    <div className="p-8">
                        {addresses.length > 0 && (
                            <div className="mb-10">
                                <h3 className="font-bold text-gray-900 mb-4 uppercase tracking-wider text-sm">Saved Addresses</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {addresses.map(addr => (
                                        <button 
                                            key={addr.id}
                                            onClick={() => handleAddressSelect(addr.id)} 
                                            className="w-full text-left p-4 border-2 border-gray-200 rounded-lg hover:border-black transition-colors focus:outline-none focus:border-black focus:ring-1 focus:ring-black"
                                        >
                                            <p className="font-bold text-gray-900 mb-1">{addr.full_name} <span className="text-sm font-normal text-gray-500 ml-2">{addr.phone}</span></p>
                                            <p className="text-sm text-gray-600 line-clamp-2">{addr.address_line_1}, {addr.address_line_2}</p>
                                            <p className="text-sm text-gray-600">{addr.city}, {addr.state} - {addr.pincode}</p>
                                            {addr.is_default === 1 && (
                                                <span className="inline-block mt-2 text-xs font-bold bg-gray-100 px-2 py-1 rounded">DEFAULT</span>
                                            )}
                                        </button>
                                    ))}
                                </div>
                                
                                <div className="mt-8 flex items-center">
                                    <div className="flex-grow h-px bg-gray-200"></div>
                                    <span className="px-4 text-sm text-gray-400 font-medium uppercase">Or Add New Address</span>
                                    <div className="flex-grow h-px bg-gray-200"></div>
                                </div>
                            </div>
                        )}

                        <form onSubmit={handleNewAddressSubmit}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input 
                                        type="text" 
                                        name="full_name" 
                                        required 
                                        className="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" 
                                        value={formData.full_name}
                                        onChange={handleInputChange}
                                    />
                                    {errors.full_name && <span className="text-red-500 text-xs mt-1">{errors.full_name[0]}</span>}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                                    <input 
                                        type="text" 
                                        name="phone" 
                                        required 
                                        className="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" 
                                        value={formData.phone}
                                        onChange={handleInputChange}
                                    />
                                    {errors.phone && <span className="text-red-500 text-xs mt-1">{errors.phone[0]}</span>}
                                </div>
                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Address Line 1 (Flat, House no., Building)</label>
                                    <input 
                                        type="text" 
                                        name="address_line_1" 
                                        required 
                                        className="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" 
                                        value={formData.address_line_1}
                                        onChange={handleInputChange}
                                    />
                                    {errors.address_line_1 && <span className="text-red-500 text-xs mt-1">{errors.address_line_1[0]}</span>}
                                </div>
                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Area, Street, Sector, Village) - Optional</label>
                                    <input 
                                        type="text" 
                                        name="address_line_2" 
                                        className="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" 
                                        value={formData.address_line_2}
                                        onChange={handleInputChange}
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">City / Town</label>
                                    <input 
                                        type="text" 
                                        name="city" 
                                        required 
                                        className="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" 
                                        value={formData.city}
                                        onChange={handleInputChange}
                                    />
                                    {errors.city && <span className="text-red-500 text-xs mt-1">{errors.city[0]}</span>}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">State</label>
                                    <input 
                                        type="text" 
                                        name="state" 
                                        required 
                                        className="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" 
                                        value={formData.state}
                                        onChange={handleInputChange}
                                    />
                                    {errors.state && <span className="text-red-500 text-xs mt-1">{errors.state[0]}</span>}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                                    <input 
                                        type="text" 
                                        name="pincode" 
                                        required 
                                        className="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors" 
                                        value={formData.pincode}
                                        onChange={handleInputChange}
                                    />
                                    {errors.pincode && <span className="text-red-500 text-xs mt-1">{errors.pincode[0]}</span>}
                                </div>
                            </div>

                            <div className="mt-10 flex justify-end">
                                <button type="submit" className="bg-black text-white px-8 py-3 rounded-full font-bold uppercase tracking-wider text-sm hover:bg-gray-800 transition-colors shadow-md">
                                    Continue to Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CheckoutAddressPage;
