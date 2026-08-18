import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';
import { useToast } from '../context/ToastContext';
import { formatPrice, getImageUrl } from '../utils/helpers';
import Pagination from '../components/Pagination';

const OrdersPage = () => {
    const { showToast } = useToast();
    
    const [orders, setOrders] = useState({ data: [], current_page: 1, last_page: 1 });
    const [loading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    
    // Cancellation Modal State
    const [cancelModalOpen, setCancelModalOpen] = useState(false);
    const [selectedItemId, setSelectedItemId] = useState(null);
    const [cancelReason, setCancelReason] = useState('');
    const [cancelNote, setCancelNote] = useState('');
    const [processingCancel, setProcessingCancel] = useState(false);

    const fetchOrders = async (pageNumber) => {
        setLoading(true);
        try {
            const response = await api.get(`/api/orders?page=${pageNumber}`);
            const fetchedOrders = response.data?.orders || response.data;
            if (fetchedOrders && Array.isArray(fetchedOrders.data)) {
                setOrders(fetchedOrders);
            } else {
                setOrders({ data: [], current_page: 1, last_page: 1 });
            }
        } catch (error) {
            console.error('Failed to fetch orders', error);
            showToast('Failed to load orders', 'error');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchOrders(page);
        window.scrollTo(0, 0);
    }, [page]);

    const openCancelModal = (itemId) => {
        setSelectedItemId(itemId);
        setCancelReason('');
        setCancelNote('');
        setCancelModalOpen(true);
    };

    const closeCancelModal = () => {
        setCancelModalOpen(false);
        setSelectedItemId(null);
    };

    const handleCancelSubmit = async (e) => {
        e.preventDefault();
        if (!cancelReason) {
            showToast('Please select a reason', 'error');
            return;
        }

        setProcessingCancel(true);
        try {
            const response = await api.post(`/api/orders/${selectedItemId}/cancel`, {
                cancellation_reason: cancelReason,
                cancellation_note: cancelNote
            });
            if (response.data.success) {
                showToast(response.data.message, 'success');
                closeCancelModal();
                fetchOrders(page); // Refresh list
            }
        } catch (error) {
            showToast(error.response?.data?.message || 'Failed to cancel item', 'error');
        } finally {
            setProcessingCancel(false);
        }
    };

    const formatDate = (dateString) => {
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-GB', options);
    };

    const ordersList = orders?.data || [];

    if (loading && ordersList.length === 0) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    return (
        <div className="bg-gray-50 min-h-screen pt-8 pb-20">
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div className="flex items-center justify-between mb-8">
                    <h1 className="font-outfit text-3xl font-bold text-gray-900">My Orders</h1>
                    <Link to="/" className="text-sm font-bold text-[#ff3f6c] hover:underline">
                        Continue Shopping <i className="fa-solid fa-arrow-right ml-1 text-xs"></i>
                    </Link>
                </div>

                {ordersList.length > 0 ? (
                    <>
                        <div className="space-y-8">
                            {ordersList.map(order => (
                                <div key={order.id} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                    {/* Order Header */}
                                    <div className="bg-gray-100 px-6 py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
                                        <div className="flex flex-wrap gap-8">
                                            <div>
                                                <p className="text-xs text-gray-500 uppercase font-bold mb-1">Order Placed</p>
                                                <p className="text-sm text-gray-900 font-medium">{formatDate(order.created_at)}</p>
                                            </div>
                                            <div>
                                                <p className="text-xs text-gray-500 uppercase font-bold mb-1">Total</p>
                                                <p className="text-sm text-gray-900 font-medium">{formatPrice(order.total)}</p>
                                            </div>
                                            <div>
                                                <p className="text-xs text-gray-500 uppercase font-bold mb-1">Payment</p>
                                                <p className="text-sm text-gray-900 font-medium uppercase">
                                                    {order.payment_method} - <span className={order.payment_status === 'completed' ? 'text-green-600' : 'text-orange-500'}>{order.payment_status}</span>
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-xs text-gray-500 uppercase font-bold mb-1">Order Status</p>
                                                <p className="text-sm font-medium uppercase">
                                                    {order.status === 'delivered' ? (
                                                        <span className="text-green-600"><i className="fa-solid fa-circle-check mr-1"></i> Delivered</span>
                                                    ) : order.status === 'cancelled' ? (
                                                        <span className="text-red-500"><i className="fa-solid fa-circle-xmark mr-1"></i> Cancelled</span>
                                                    ) : (
                                                        <span className="text-blue-500"><i className="fa-solid fa-truck-fast mr-1"></i> {order.status.replace(/_/g, ' ')}</span>
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-xs text-gray-500 uppercase font-bold mb-1">Order #</p>
                                            <p className="text-sm font-mono text-gray-900 font-bold">{order.order_number}</p>
                                        </div>
                                    </div>

                                    {/* Order Items */}
                                    <div className="p-6">
                                        <div className="space-y-8">
                                            {order.items.map((item, index) => {
                                                const isLast = index === order.items.length - 1;
                                                
                                                // Check return eligibility
                                                const isReturnable = item.product ? item.product.is_returnable : false;
                                                const returnWindow = item.product ? item.product.return_window_days : 15;
                                                const deliveryDate = new Date(order.updated_at);
                                                const daysSinceDelivery = Math.floor((new Date() - deliveryDate) / (1000 * 60 * 60 * 24));
                                                const canReturn = isReturnable && (daysSinceDelivery <= returnWindow);

                                                return (
                                                    <div key={item.id} className={`flex flex-col md:flex-row items-start gap-6 pb-6 ${!isLast ? 'border-b border-gray-100' : ''}`}>
                                                        {/* Image */}
                                                        <div className="shrink-0 relative group">
                                                            <Link to={`/orders/${order.id}`}>
                                                                <img 
                                                                    src={getImageUrl(item.image_snapshot || (item.product?.images?.[0]?.image_path))} 
                                                                    className="w-24 h-32 object-cover rounded bg-gray-50 border border-gray-200 group-hover:opacity-90 transition-opacity"
                                                                    alt={item.product_name_snapshot}
                                                                />
                                                            </Link>
                                                        </div>
                                                        
                                                        {/* Details */}
                                                        <div className="flex-1">
                                                            <h4 className="font-bold text-gray-900 text-lg mb-1">{item.product_name_snapshot || (item.product ? item.product.name : 'Unknown Product')}</h4>
                                                            
                                                            <div className="flex flex-wrap items-center gap-4 text-sm text-gray-600 mt-2 mb-3">
                                                                {item.color_snapshot && <span className="bg-gray-100 px-2 py-1 rounded border border-gray-200">Color: <strong>{item.color_snapshot}</strong></span>}
                                                                {item.size_snapshot && <span className="bg-gray-100 px-2 py-1 rounded border border-gray-200">Size: <strong>{item.size_snapshot}</strong></span>}
                                                                <span className="bg-gray-100 px-2 py-1 rounded border border-gray-200">Qty: <strong>{item.quantity}</strong></span>
                                                            </div>
                                                            
                                                            <p className="font-bold text-gray-900 text-lg">{formatPrice(item.price)}</p>

                                                            {/* Item Status visualization (simplified) */}
                                                            <div className="mt-4">
                                                                {['cancelled', 'return_requested', 'returned', 'refunded'].includes(item.status) ? (
                                                                    <div className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${item.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700'}`}>
                                                                        <i className="fa-solid fa-circle-info mr-2"></i> Item {item.status.replace(/_/g, ' ')}
                                                                    </div>
                                                                ) : (
                                                                    <div className="text-sm font-medium text-gray-600">
                                                                        Status: <span className="uppercase font-bold text-green-600">{item.status.replace(/_/g, ' ')}</span>
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </div>
                                                        
                                                        {/* Action Buttons */}
                                                        <div className="shrink-0 flex flex-col gap-3 min-w-[140px] mt-4 md:mt-0">
                                                            <Link to={`/orders/${order.id}`} className="w-full text-center text-sm font-bold text-white bg-[#ff3f6c] px-4 py-2 rounded hover:bg-[#d82a54] transition-colors">
                                                                View Details
                                                            </Link>

                                                            {['placed', 'confirmed'].includes(order.status) && ['placed', 'confirmed'].includes(item.status) ? (
                                                                <button onClick={() => openCancelModal(item.id)} className="w-full text-sm font-bold text-red-600 border border-red-600 px-4 py-2 rounded hover:bg-red-50 transition-colors">
                                                                    Cancel Order
                                                                </button>
                                                            ) : order.status !== 'cancelled' && !['delivered', 'returned', 'exchanged'].includes(order.status) ? (
                                                                <p className="text-xs text-gray-500 text-center">This order can no longer be cancelled.</p>
                                                            ) : null}
                                                            
                                                            {item.status === 'delivered' && (
                                                                <>
                                                                    {canReturn ? (
                                                                        <>
                                                                            <button className="w-full text-sm font-bold text-[#ff3f6c] border border-[#ff3f6c] px-4 py-2 rounded hover:bg-pink-50 transition-colors">Return</button>
                                                                            {item.product?.is_exchangeable && (
                                                                                <button className="w-full text-sm font-bold text-blue-600 border border-blue-600 px-4 py-2 rounded hover:bg-blue-50 transition-colors">Exchange</button>
                                                                            )}
                                                                        </>
                                                                    ) : (
                                                                        <p className="text-xs text-gray-500 font-medium text-center bg-gray-50 px-2 py-1 rounded">Return window closed</p>
                                                                    )}
                                                                    <button className="w-full text-sm font-medium text-gray-600 border border-gray-300 px-4 py-2 rounded hover:bg-gray-50 transition-colors mt-2">Write Review</button>
                                                                </>
                                                            )}
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Pagination */}
                        <Pagination 
                            meta={orders} 
                            onPageChange={(p) => setPage(p)} 
                        />
                    </>
                ) : (
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-16 text-center">
                        <div className="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i className="fa-solid fa-box text-4xl text-gray-400"></i>
                        </div>
                        <h2 className="text-2xl font-bold text-gray-900 mb-2">No orders found</h2>
                        <p className="text-gray-500 mb-8 max-w-md mx-auto">Looks like you haven't made any purchases yet. Start shopping to fill this space!</p>
                        <Link to="/" className="inline-block bg-black text-white px-8 py-3 rounded-full font-bold uppercase tracking-wider text-sm hover:bg-gray-800 transition-colors shadow-md">
                            Browse Products
                        </Link>
                    </div>
                )}
            </div>

            {/* Cancel Modal */}
            {cancelModalOpen && (
                <div className="fixed inset-0 z-50 bg-black/50 p-4 flex items-center justify-center">
                    <div className="bg-white rounded-xl p-6 max-w-md w-full text-left">
                        <h3 className="font-bold text-lg text-gray-900">Why do you want to cancel this item?</h3>
                        <form onSubmit={handleCancelSubmit}>
                            <select 
                                value={cancelReason}
                                onChange={(e) => setCancelReason(e.target.value)}
                                required 
                                className="mt-4 w-full rounded border-gray-300 focus:border-black focus:ring-0 text-sm"
                            >
                                <option value="">Select a reason</option>
                                <option value="ordered_by_mistake">Ordered by mistake</option>
                                <option value="better_price">Found a better price</option>
                                <option value="delivery_too_long">Delivery is taking too long</option>
                                <option value="changed_mind">Changed my mind</option>
                                <option value="not_required">Product no longer required</option>
                                <option value="wrong_size_or_color">Ordered wrong size/color</option>
                                <option value="duplicate_order">Duplicate order</option>
                                <option value="other">Other</option>
                            </select>
                            
                            <textarea 
                                value={cancelNote}
                                onChange={(e) => setCancelNote(e.target.value)}
                                maxLength="1000" 
                                rows="3" 
                                className="mt-3 w-full rounded border-gray-300 focus:border-black focus:ring-0 text-sm" 
                                placeholder="Additional comments (optional)"
                            ></textarea>
                            
                            <div className="mt-6 flex justify-end gap-3">
                                <button type="button" onClick={closeCancelModal} className="px-4 py-2 font-medium text-gray-600 hover:text-gray-900">Keep item</button>
                                <button type="submit" disabled={processingCancel} className={`px-4 py-2 bg-red-600 text-white rounded font-bold transition-colors ${processingCancel ? 'opacity-75 cursor-not-allowed' : 'hover:bg-red-700'}`}>
                                    {processingCancel ? 'Cancelling...' : 'Confirm Cancellation'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
};

export default OrdersPage;
