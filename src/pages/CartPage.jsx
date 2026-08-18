import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import api from '../services/api';
import { useCart } from '../context/CartContext';
import { useToast } from '../context/ToastContext';
import { formatPrice, getImageUrl } from '../utils/helpers';
import CheckoutProgress from '../components/CheckoutProgress';

const CartPage = () => {
    const { refreshCart } = useCart();
    const { showToast } = useToast();
    const navigate = useNavigate();
    
    const [cart, setCart] = useState({});
    const [loading, setLoading] = useState(true);

    const fetchCart = async () => {
        setLoading(true);
        try {
            const response = await api.get('/api/cart');
            setCart(response.data.cart || {});
        } catch (error) {
            console.error('Failed to fetch cart', error);
            showToast('Failed to load cart', 'error');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchCart();
    }, []);

    const handleRemove = async (id) => {
        try {
            const response = await api.post('/api/cart/remove', { id });
            if (response.data.success) {
                showToast(response.data.message, 'success');
                setCart(response.data.cart);
                refreshCart();
            }
        } catch (error) {
            showToast('Failed to remove item', 'error');
        }
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    const cartEntries = Object.entries(cart);
    const totalMrp = cartEntries.reduce((sum, [_, item]) => sum + ((item.original_price || item.price) * item.quantity), 0);
    const totalPayable = cartEntries.reduce((sum, [_, item]) => sum + (item.price * item.quantity), 0);
    const totalDiscount = Math.max(0, totalMrp - totalPayable);

    return (
        <div className="bg-white min-h-screen pt-8 pb-20">
            <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <h1 className="font-outfit text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

                {cartEntries.length > 0 && <CheckoutProgress />}

                {cartEntries.length > 0 ? (
                    <div className="flex flex-col lg:flex-row gap-12">
                        <div className="w-full lg:w-2/3">
                            <div className="overflow-x-auto">
                                <table className="w-full text-left border-collapse min-w-[600px]">
                                    <thead>
                                        <tr className="border-b border-gray-200">
                                            <th className="pb-4 font-semibold text-gray-900">Product</th>
                                            <th className="pb-4 font-semibold text-gray-900">Price</th>
                                            <th className="pb-4 font-semibold text-gray-900">Quantity</th>
                                            <th className="pb-4 font-semibold text-gray-900">Total</th>
                                            <th className="pb-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {cartEntries.map(([id, details]) => {
                                            const originalPrice = details.original_price || details.price;
                                            const hasDiscount = originalPrice > details.price;
                                            return (
                                                <tr key={id} className="border-b border-gray-100">
                                                    <td className="py-6 flex items-center gap-4">
                                                        <img 
                                                            src={getImageUrl(details.image)} 
                                                            className="w-20 h-24 object-cover rounded" 
                                                            alt={details.name} 
                                                        />
                                                        <div>
                                                            <Link to={`/product/${details.product_id}`} className="font-medium text-gray-900 hover:text-[#ff3f6c] transition-colors">{details.name}</Link>
                                                            {(details.color || details.size || details.color_name || details.size_name) && (
                                                                <div className="text-xs text-gray-500 mt-1 flex flex-wrap gap-2">
                                                                    {(details.color || details.color_name) && <span>Color: <strong>{details.color || details.color_name}</strong></span>}
                                                                    {(details.size || details.size_name) && <span>Size: <strong>{details.size || details.size_name}</strong></span>}
                                                                </div>
                                                            )}
                                                        </div>
                                                    </td>
                                                    <td className="py-6 text-gray-600">
                                                        <div>
                                                            <span className="font-semibold text-gray-900">{formatPrice(details.price)}</span>
                                                            {hasDiscount && (
                                                                <span className="text-xs text-gray-400 line-through ml-2">{formatPrice(originalPrice)}</span>
                                                            )}
                                                        </div>
                                                    </td>
                                                    <td className="py-6 text-gray-600">
                                                        <span className="bg-gray-50 px-3 py-1 rounded border border-gray-200 font-medium">
                                                            {details.quantity}
                                                        </span>
                                                    </td>
                                                    <td className="py-6 font-semibold text-gray-900">{formatPrice(details.price * details.quantity)}</td>
                                                    <td className="py-6 text-right">
                                                        <button 
                                                            onClick={() => handleRemove(id)} 
                                                            className="text-red-500 hover:text-red-700 w-8 h-8 rounded-full hover:bg-red-50 flex items-center justify-center transition-colors"
                                                        >
                                                            <i className="fa-solid fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div className="w-full lg:w-1/3">
                            <div className="bg-gray-50 p-8 rounded-lg border border-gray-200 sticky top-28">
                                <h2 className="font-outfit text-xl font-bold mb-6">Order Summary</h2>
                                <div className="flex justify-between mb-4 text-gray-600">
                                    <span>Total MRP</span>
                                    <span>{formatPrice(totalMrp)}</span>
                                </div>
                                {totalDiscount > 0 && (
                                    <div className="flex justify-between mb-4 text-green-600 font-medium">
                                        <span>Discount Savings</span>
                                        <span>-{formatPrice(totalDiscount)}</span>
                                    </div>
                                )}
                                <div className="flex justify-between mb-6 text-gray-600 border-b border-gray-200 pb-6">
                                    <span>Shipping</span>
                                    <span className="text-green-600 font-medium">Free</span>
                                </div>
                                <div className="flex justify-between mb-8 text-lg font-bold text-gray-900">
                                    <span>Total Payable</span>
                                    <span>{formatPrice(totalPayable)}</span>
                                </div>
                                <button 
                                    onClick={() => navigate('/checkout/address')} 
                                    className="block w-full bg-[#ff3f6c] text-white text-center py-4 rounded font-bold hover:bg-[#ed3a64] shadow-md hover:shadow-lg transition-all tracking-wider uppercase text-sm"
                                >
                                    Proceed to Checkout
                                </button>
                            </div>
                        </div>
                    </div>
                ) : (
                    <div className="py-20 text-center max-w-lg mx-auto">
                        <i className="fa-solid fa-cart-shopping text-6xl text-gray-200 mb-6 block"></i>
                        <h3 className="text-xl font-bold text-gray-900 mb-3">Your bag is empty</h3>
                        <p className="text-gray-500 mb-8">Looks like you haven't added anything to your bag yet.</p>
                        <Link to="/" className="inline-block bg-[#ff3f6c] hover:bg-[#ed3a64] text-white px-8 py-4 rounded font-bold transition-all shadow-md hover:shadow-lg tracking-wider uppercase text-sm">
                            Continue Shopping
                        </Link>
                    </div>
                )}
            </div>
        </div>
    );
};

export default CartPage;
