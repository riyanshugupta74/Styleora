import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import { useToast } from '../context/ToastContext';
import { useCart } from '../context/CartContext';
import CheckoutProgress from '../components/CheckoutProgress';
import { formatPrice } from '../utils/helpers';

const CheckoutPaymentPage = () => {
    const navigate = useNavigate();
    const { showToast } = useToast();
    const { refreshCart } = useCart();
    
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [processing, setProcessing] = useState(false);
    
    // Form state
    const [paymentMethod, setPaymentMethod] = useState('');

    useEffect(() => {
        const fetchPaymentDetails = async () => {
            setLoading(true);
            try {
                const response = await api.get('/api/checkout/payment');
                setTotal(response.data.total || 0);
            } catch (error) {
                console.error('Failed to fetch payment details', error);
                const errorMsg = error.response?.data?.error || 'Failed to load payment details';
                showToast(errorMsg, 'error');
                if (error.response?.data?.error === 'Please select an address first.') {
                    navigate('/checkout/address');
                } else {
                    navigate('/cart');
                }
            } finally {
                setLoading(false);
            }
        };

        fetchPaymentDetails();
    }, [navigate]);

    const handlePaymentSubmit = async (e) => {
        e.preventDefault();
        
        if (!paymentMethod) {
            showToast('Please select a payment method', 'error');
            return;
        }

        setProcessing(true);
        try {
            const response = await api.post('/api/checkout/process', { payment_method: paymentMethod });
            if (response.data.success) {
                showToast(response.data.message || 'Order confirmed successfully!', 'success');
                try {
                    if (refreshCart) refreshCart();
                } catch (e) {
                    console.error('Cart refresh error', e);
                }
                navigate('/orders');
            }
        } catch (error) {
            showToast(error.response?.data?.message || error.response?.data?.error || 'Failed to process payment', 'error');
        } finally {
            setProcessing(false);
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
            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <CheckoutProgress />

                <div className="flex flex-col lg:flex-row gap-8">
                    {/* Payment Options */}
                    <div className="w-full lg:w-2/3">
                        <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-8 border-b border-gray-100">
                                <h2 className="text-2xl font-outfit font-bold text-gray-900">Payment Method</h2>
                                <p className="text-gray-500 text-sm mt-1">Select how you want to pay</p>
                            </div>

                            <div className="p-8">
                                <form onSubmit={handlePaymentSubmit} id="payment-form">
                                    <div className="space-y-4">
                                        
                                        {/* UPI Option */}
                                        <label className="block relative border-2 border-gray-200 rounded-lg p-5 cursor-pointer hover:border-gray-300 transition-colors has-[:checked]:border-black has-[:checked]:bg-gray-50">
                                            <div className="flex items-center">
                                                <input 
                                                    type="radio" 
                                                    name="payment_method" 
                                                    value="upi" 
                                                    checked={paymentMethod === 'upi'}
                                                    onChange={(e) => setPaymentMethod(e.target.value)}
                                                    className="h-4 w-4 text-black focus:ring-black border-gray-300" 
                                                    required 
                                                />
                                                <div className="ml-4 flex-1">
                                                    <div className="flex items-center justify-between">
                                                        <span className="font-bold text-gray-900 block">UPI (Google Pay, PhonePe, Paytm)</span>
                                                        <i className="fa-solid fa-mobile-screen text-xl text-gray-400"></i>
                                                    </div>
                                                    <p className="text-sm text-gray-500 mt-1">Pay instantly using any UPI app</p>
                                                </div>
                                            </div>
                                        </label>

                                        {/* Credit/Debit Card Option */}
                                        <label className="block relative border-2 border-gray-200 rounded-lg p-5 cursor-pointer hover:border-gray-300 transition-colors has-[:checked]:border-black has-[:checked]:bg-gray-50">
                                            <div className="flex items-center">
                                                <input 
                                                    type="radio" 
                                                    name="payment_method" 
                                                    value="card" 
                                                    checked={paymentMethod === 'card'}
                                                    onChange={(e) => setPaymentMethod(e.target.value)}
                                                    className="h-4 w-4 text-black focus:ring-black border-gray-300" 
                                                    required 
                                                />
                                                <div className="ml-4 flex-1">
                                                    <div className="flex items-center justify-between">
                                                        <span className="font-bold text-gray-900 block">Credit / Debit Card</span>
                                                        <div className="flex gap-2">
                                                            <i className="fa-brands fa-cc-visa text-xl text-gray-400"></i>
                                                            <i className="fa-brands fa-cc-mastercard text-xl text-gray-400"></i>
                                                        </div>
                                                    </div>
                                                    <p className="text-sm text-gray-500 mt-1">Pay securely with your bank card</p>
                                                </div>
                                            </div>
                                        </label>

                                        {/* COD Option */}
                                        <label className="block relative border-2 border-gray-200 rounded-lg p-5 cursor-pointer hover:border-gray-300 transition-colors has-[:checked]:border-black has-[:checked]:bg-gray-50">
                                            <div className="flex items-center">
                                                <input 
                                                    type="radio" 
                                                    name="payment_method" 
                                                    value="cod" 
                                                    checked={paymentMethod === 'cod'}
                                                    onChange={(e) => setPaymentMethod(e.target.value)}
                                                    className="h-4 w-4 text-black focus:ring-black border-gray-300" 
                                                    required 
                                                />
                                                <div className="ml-4 flex-1">
                                                    <div className="flex items-center justify-between">
                                                        <span className="font-bold text-gray-900 block">Cash on Delivery</span>
                                                        <i className="fa-solid fa-money-bill-wave text-xl text-gray-400"></i>
                                                    </div>
                                                    <p className="text-sm text-gray-500 mt-1">Pay in cash when your order arrives</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <div className="mt-8 text-sm text-gray-500 flex items-center justify-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                                        <i className="fa-solid fa-shield-halved text-gray-400 mr-2 text-lg"></i>
                                        Payments are 100% secure and encrypted.
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {/* Order Summary Sidebar */}
                    <div className="w-full lg:w-1/3">
                        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-8">
                            <h3 className="font-outfit text-xl font-bold mb-6 text-gray-900 border-b border-gray-100 pb-4">Order Summary</h3>
                            
                            <div className="space-y-4 mb-6 text-sm">
                                <div className="flex justify-between text-gray-600">
                                    <span>Cart Total</span>
                                    <span>{formatPrice(total)}</span>
                                </div>
                                <div className="flex justify-between text-gray-600">
                                    <span>Shipping</span>
                                    <span className="text-[#20bb79] font-medium">FREE</span>
                                </div>
                                <div className="flex justify-between text-gray-600">
                                    <span>Discount</span>
                                    <span>-₹0.00</span>
                                </div>
                            </div>

                            <div className="border-t border-gray-100 pt-4 mb-8">
                                <div className="flex justify-between items-center text-lg font-bold text-gray-900">
                                    <span>Total Amount</span>
                                    <span>{formatPrice(total)}</span>
                                </div>
                                <p className="text-xs text-teal-600 font-bold mt-1 text-right">inclusive of all taxes</p>
                            </div>

                            <button 
                                type="submit" 
                                form="payment-form" 
                                disabled={processing}
                                className={`w-full text-white py-4 rounded-full font-bold transition-colors shadow-md tracking-wider uppercase text-sm flex items-center justify-center ${processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-[#ff3f6c] hover:bg-[#ed3a64]'}`}
                            >
                                {processing ? (
                                    <><i className="fa-solid fa-spinner fa-spin mr-2"></i> Processing...</>
                                ) : (
                                    <><i className="fa-solid fa-lock mr-2"></i> Pay & Place Order</>
                                )}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CheckoutPaymentPage;
