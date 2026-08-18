import { useState, useEffect } from 'react';
import { useSearchParams, Link } from 'react-router-dom';
import api from '../services/api';
import { useToast } from '../context/ToastContext';
import { formatPrice, getImageUrl } from '../utils/helpers';

const TrackOrderPage = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const { showToast } = useToast();
    
    const [orderNumber, setOrderNumber] = useState(searchParams.get('order_number') || '');
    const [order, setOrder] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const trackOrder = async (queryOrderNumber) => {
        if (!queryOrderNumber) return;
        
        setLoading(true);
        setError(null);
        try {
            const response = await api.get(`/api/track-order?order_number=${queryOrderNumber}`);
            setOrder(response.data.order);
        } catch (err) {
            console.error('Failed to track order', err);
            setOrder(null);
            setError(err.response?.data?.error || 'Failed to find order');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        const queryOrderNumber = searchParams.get('order_number');
        if (queryOrderNumber) {
            setOrderNumber(queryOrderNumber);
            trackOrder(queryOrderNumber);
        }
        window.scrollTo(0, 0);
    }, [searchParams]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (orderNumber.trim()) {
            setSearchParams({ order_number: orderNumber.trim() });
        }
    };

    const formatDate = (dateString) => {
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-GB', options);
    };

    const calculateExpectedDelivery = (dateString) => {
        const date = new Date(dateString);
        date.setDate(date.getDate() + 5);
        const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('en-GB', options);
    };

    let timeline = [];
    if (order) {
        timeline = order.timeline_status || [];
        if (timeline.length === 0 && !['cancelled', 'returned'].includes(order.status)) {
            timeline = [
                { label: 'Order Placed', completed: true, current: false },
                { label: 'Confirmed', completed: ['confirmed', 'shipped', 'out_for_delivery', 'delivered'].includes(order.status), current: order.status === 'confirmed' },
                { label: 'Shipped', completed: ['shipped', 'out_for_delivery', 'delivered'].includes(order.status), current: order.status === 'shipped' },
                { label: 'Out for Delivery', completed: ['out_for_delivery', 'delivered'].includes(order.status), current: order.status === 'out_for_delivery' },
                { label: 'Delivered', completed: order.status === 'delivered', current: order.status === 'delivered' }
            ];
        }
    }

    return (
        <div className="bg-gray-50 min-h-screen pt-12 pb-20">
            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div className="text-center mb-12">
                    <h1 className="font-outfit text-3xl md:text-4xl font-bold text-gray-900 mb-4">Track Your Order</h1>
                    <p className="text-gray-500 max-w-lg mx-auto">Enter your Order ID to track the current status of your delivery.</p>
                </div>

                {/* Search Form */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-12 max-w-2xl mx-auto">
                    <form onSubmit={handleSubmit} className="flex flex-col sm:flex-row gap-4">
                        <div className="flex-1">
                            <label htmlFor="order_number" className="sr-only">Order ID</label>
                            <input 
                                type="text" 
                                name="order_number" 
                                id="order_number" 
                                value={orderNumber}
                                onChange={(e) => setOrderNumber(e.target.value)}
                                required
                                className="w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black font-mono uppercase" 
                                placeholder="Enter Order ID (e.g., ORD-12345)"
                            />
                        </div>
                        <button 
                            type="submit" 
                            disabled={loading}
                            className={`bg-black text-white px-8 py-3 rounded-md font-bold uppercase tracking-wider transition-colors shrink-0 ${loading ? 'opacity-75 cursor-not-allowed' : 'hover:bg-gray-800'}`}
                        >
                            {loading ? 'Tracking...' : 'Track'}
                        </button>
                    </form>
                    
                    {error && (
                        <div className="mt-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded text-sm flex items-start">
                            <i className="fa-solid fa-circle-exclamation mt-0.5 mr-2"></i>
                            <span>{error}</span>
                        </div>
                    )}
                </div>

                {/* Tracking Results */}
                {order && !loading && (
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all">
                        <div className="bg-gray-900 text-white p-6 sm:px-8 flex flex-wrap justify-between items-center gap-4">
                            <div>
                                <p className="text-gray-400 text-sm uppercase tracking-wider font-bold mb-1">Order Number</p>
                                <p className="text-xl font-mono font-bold">{order.order_number}</p>
                            </div>
                            <div className="text-right">
                                <p className="text-gray-400 text-sm uppercase tracking-wider font-bold mb-1">Order Date</p>
                                <p className="text-lg font-medium">{formatDate(order.created_at)}</p>
                            </div>
                        </div>

                        <div className="p-6 sm:p-8">
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                {/* Left: Timeline */}
                                <div>
                                    <h3 className="font-bold text-gray-900 mb-8 uppercase tracking-wider">Delivery Status</h3>
                                    
                                    {['cancelled', 'return_requested', 'returned'].includes(order.status) ? (
                                        <div className="bg-gray-50 border border-gray-200 rounded p-6 text-center">
                                            <div className={`w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 ${order.status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600'}`}>
                                                <i className={`fa-solid ${order.status === 'cancelled' ? 'fa-xmark' : 'fa-rotate-left'} text-2xl`}></i>
                                            </div>
                                            <h4 className="text-lg font-bold text-gray-900 mb-2 uppercase">{order.status.replace(/_/g, ' ')}</h4>
                                            <p className="text-gray-500 text-sm">This order is no longer active for delivery.</p>
                                        </div>
                                    ) : (
                                        <>
                                            <div className="relative pl-8 max-w-sm mx-auto lg:mx-0">
                                                <div className="absolute left-3 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                                                
                                                {timeline.map((stage, idx) => (
                                                    <div key={idx} className="relative mb-8 last:mb-0">
                                                        <div className={`absolute -left-8 top-0 w-6 h-6 rounded-full flex items-center justify-center border-4 border-white ${stage.completed || stage.current ? 'bg-green-500' : 'bg-gray-200'}`}>
                                                            {stage.completed ? (
                                                                <i className="fa-solid fa-check text-[10px] text-white"></i>
                                                            ) : stage.current ? (
                                                                <div className="w-2 h-2 bg-white rounded-full"></div>
                                                            ) : null}
                                                        </div>
                                                        
                                                        <div>
                                                            <p className={`font-bold text-lg ${stage.completed || stage.current ? 'text-gray-900' : 'text-gray-400'}`}>
                                                                {stage.label}
                                                            </p>
                                                            {stage.current && (
                                                                <p className="text-sm font-medium text-[#ff3f6c] mt-1">Current Status</p>
                                                            )}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                            
                                            {['shipped', 'out_for_delivery'].includes(order.status) && (
                                                <div className="mt-8 bg-blue-50 border border-blue-200 rounded p-4 flex items-start text-blue-800">
                                                    <i className="fa-solid fa-calendar-check mt-1 mr-3 text-blue-600"></i>
                                                    <div>
                                                        <p className="font-bold">Expected Delivery</p>
                                                        <p className="text-sm mt-1">{calculateExpectedDelivery(order.created_at)}</p>
                                                    </div>
                                                </div>
                                            )}
                                        </>
                                    )}
                                </div>

                                {/* Right: Order Summary */}
                                <div>
                                    <h3 className="font-bold text-gray-900 mb-6 uppercase tracking-wider">Order Summary</h3>
                                    
                                    <div className="space-y-6">
                                        {order.items?.slice(0, 3).map(item => (
                                            <div key={item.id} className="flex gap-4">
                                                <div className="w-16 h-20 shrink-0 bg-gray-100 rounded border border-gray-200 overflow-hidden">
                                                    <img 
                                                        src={getImageUrl(item.image_snapshot || (item.product?.images?.[0]?.image_path))} 
                                                        className="w-full h-full object-cover" 
                                                        alt={item.product_name_snapshot}
                                                    />
                                                </div>
                                                <div>
                                                    <p className="font-medium text-gray-900 text-sm line-clamp-2">{item.product_name_snapshot || (item.product ? item.product.name : 'Unknown Product')}</p>
                                                    <p className="text-gray-500 text-xs mt-1">Qty: {item.quantity}</p>
                                                    <p className="font-bold text-gray-900 text-sm mt-1">{formatPrice(item.price)}</p>
                                                </div>
                                            </div>
                                        ))}
                                        
                                        {order.items?.length > 3 && (
                                            <p className="text-sm text-gray-500 text-center font-medium">+ {order.items.length - 3} more items</p>
                                        )}
                                    </div>
                                    
                                    <div className="mt-8 pt-6 border-t border-gray-100">
                                        <div className="flex justify-between items-center mb-2">
                                            <span className="text-gray-500">Order Amount</span>
                                            <span className="font-bold text-gray-900 text-lg">{formatPrice(order.total)}</span>
                                        </div>
                                        <div className="flex justify-between items-center text-sm">
                                            <span className="text-gray-500">Payment</span>
                                            <span className="font-medium text-gray-900 uppercase">{order.payment_method} ({order.payment_status})</span>
                                        </div>
                                    </div>
                                    
                                    <div className="mt-8">
                                        <Link to={`/orders/${order.id}`} className="block w-full text-center bg-gray-100 text-gray-900 font-bold py-3 rounded-md hover:bg-gray-200 transition-colors">
                                            View Full Order Details
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default TrackOrderPage;
