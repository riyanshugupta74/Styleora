import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../services/api';
import { useToast } from '../context/ToastContext';
import { formatPrice, getImageUrl } from '../utils/helpers';

const OrderDetailsPage = () => {
    const { id } = useParams();
    const { showToast } = useToast();
    
    const [order, setOrder] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchOrderDetails = async () => {
            setLoading(true);
            try {
                const response = await api.get(`/api/orders/${id}`);
                setOrder(response.data.order);
            } catch (error) {
                console.error('Failed to fetch order details', error);
                showToast('Failed to load order details', 'error');
            } finally {
                setLoading(false);
            }
        };

        fetchOrderDetails();
        window.scrollTo(0, 0);
    }, [id]);

    const formatDate = (dateString, includeTime = false) => {
        const date = new Date(dateString);
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        if (includeTime) {
            options.hour = '2-digit';
            options.minute = '2-digit';
            options.hour12 = true;
        }
        return date.toLocaleString('en-IN', options);
    };

    if (loading || !order) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    // Determine timeline from order or mock one
    let timeline = order.timeline_status || [];
    if (timeline.length === 0 && !['cancelled', 'returned'].includes(order.status)) {
        timeline = [
            { label: 'Order Placed', completed: true, current: false },
            { label: 'Confirmed', completed: ['confirmed', 'shipped', 'out_for_delivery', 'delivered'].includes(order.status), current: order.status === 'confirmed' },
            { label: 'Shipped', completed: ['shipped', 'out_for_delivery', 'delivered'].includes(order.status), current: order.status === 'shipped' },
            { label: 'Out for Delivery', completed: ['out_for_delivery', 'delivered'].includes(order.status), current: order.status === 'out_for_delivery' },
            { label: 'Delivered', completed: order.status === 'delivered', current: order.status === 'delivered' }
        ];
    }

    return (
        <div className="bg-gray-50 min-h-screen pt-8 pb-20">
            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div className="mb-6 flex items-center">
                    <Link to="/orders" className="text-gray-500 hover:text-gray-900 mr-4">
                        <i className="fa-solid fa-arrow-left text-xl"></i>
                    </Link>
                    <h1 className="font-outfit text-3xl font-bold text-gray-900">Order Details</h1>
                </div>

                {/* Order Summary Header */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <p className="text-sm text-gray-500 uppercase font-bold mb-1">Order ID</p>
                        <p className="text-lg font-mono text-gray-900 font-bold">{order.order_number}</p>
                    </div>
                    <div>
                        <p className="text-sm text-gray-500 uppercase font-bold mb-1">Order Date</p>
                        <p className="text-lg text-gray-900 font-medium">{formatDate(order.created_at, true)}</p>
                    </div>
                    <div>
                        <p className="text-sm text-gray-500 uppercase font-bold mb-1">Order Total</p>
                        <p className="text-lg text-gray-900 font-bold">{formatPrice(order.total)}</p>
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {/* Left Column: Products & Timeline */}
                    <div className="md:col-span-2 space-y-8">
                        {order.items.map(item => (
                            <div key={item.id} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                {/* Product Info */}
                                <div className="p-6 border-b border-gray-100 flex flex-col sm:flex-row gap-6">
                                    <div className="shrink-0">
                                        <img 
                                            src={getImageUrl(item.image_snapshot || (item.product?.images?.[0]?.image_path))} 
                                            className="w-32 h-40 object-cover rounded bg-gray-50 border border-gray-200"
                                            alt={item.product_name_snapshot}
                                        />
                                    </div>
                                    <div className="flex-1">
                                        <h3 className="font-bold text-gray-900 text-xl mb-1">{item.product_name_snapshot || (item.product ? item.product.name : 'Unknown Product')}</h3>
                                        <p className="text-gray-500 mb-3 text-sm">{item.product ? item.product.brand?.name || 'Brand' : 'Brand'}</p>
                                        
                                        <div className="grid grid-cols-2 gap-y-2 text-sm mb-4">
                                            {item.color_snapshot && <div><span className="text-gray-500">Color:</span> <span className="font-medium text-gray-900">{item.color_snapshot}</span></div>}
                                            {item.size_snapshot && <div><span className="text-gray-500">Size:</span> <span className="font-medium text-gray-900">{item.size_snapshot}</span></div>}
                                            <div><span className="text-gray-500">Qty:</span> <span className="font-medium text-gray-900">{item.quantity}</span></div>
                                            <div><span className="text-gray-500">Price:</span> <span className="font-bold text-gray-900">{formatPrice(item.price)}</span></div>
                                        </div>
                                    </div>
                                </div>

                                {/* Tracking Timeline */}
                                <div className="p-6 bg-gray-50">
                                    <h4 className="font-bold text-gray-900 mb-6 uppercase text-sm tracking-wider">Delivery Tracking</h4>
                                    
                                    {['cancelled', 'return_requested', 'returned'].includes(order.status) ? (
                                        <div className="flex items-center text-red-600 font-bold bg-red-50 p-4 rounded border border-red-200">
                                            <i className="fa-solid fa-circle-exclamation text-xl mr-3"></i>
                                            <div>
                                                <p className="text-sm uppercase tracking-wider mb-1">Order Status</p>
                                                <p className="text-lg">{order.status.replace(/_/g, ' ')}</p>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="relative pl-8">
                                            <div className="absolute left-3 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                                            
                                            {timeline.map((stage, idx) => (
                                                <div key={idx} className="relative mb-6 last:mb-0">
                                                    <div className={`absolute -left-8 top-1 w-6 h-6 rounded-full flex items-center justify-center border-4 border-white shadow-sm ${stage.completed || stage.current ? 'bg-green-500' : 'bg-gray-200'}`}>
                                                        {stage.completed ? (
                                                            <i className="fa-solid fa-check text-[10px] text-white"></i>
                                                        ) : stage.current ? (
                                                            <div className="w-2 h-2 bg-white rounded-full"></div>
                                                        ) : null}
                                                    </div>
                                                    
                                                    <div>
                                                        <p className={`font-bold ${stage.completed || stage.current ? 'text-gray-900' : 'text-gray-400'}`}>
                                                            {stage.label}
                                                        </p>
                                                        {stage.current && (
                                                            <p className="text-xs text-gray-500 mt-1">Current status</p>
                                                        )}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Right Column: Shipping & Payment */}
                    <div className="space-y-8">
                        {/* Delivery Address */}
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 className="font-bold text-gray-900 mb-4 uppercase text-sm tracking-wider flex items-center">
                                <i className="fa-solid fa-location-dot mr-2 text-gray-400"></i> Delivery Address
                            </h3>
                            {order.address ? (
                                <>
                                    <p className="font-bold text-gray-900 mb-1">{order.address.name || order.address.full_name}</p>
                                    <p className="text-sm text-gray-600 leading-relaxed">
                                        {order.address.address_line_1}<br/>
                                        {order.address.address_line_2 && <>{order.address.address_line_2}<br/></>}
                                        {order.address.city}, {order.address.state} {order.address.pincode}<br/>
                                        {order.address.country || 'India'}
                                    </p>
                                    <p className="text-sm font-medium text-gray-900 mt-3">
                                        <i className="fa-solid fa-phone mr-1 text-gray-400"></i> {order.address.phone}
                                    </p>
                                </>
                            ) : (
                                <p className="text-sm text-gray-500">Address details not available.</p>
                            )}
                        </div>

                        {/* Payment Information */}
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 className="font-bold text-gray-900 mb-4 uppercase text-sm tracking-wider flex items-center">
                                <i className="fa-solid fa-credit-card mr-2 text-gray-400"></i> Payment Information
                            </h3>
                            
                            <div className="space-y-3 text-sm mb-6 pb-6 border-b border-gray-100">
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Method</span>
                                    <span className="font-medium text-gray-900 uppercase">{order.payment_method}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Status</span>
                                    <span className={`font-bold uppercase ${order.payment_status === 'completed' ? 'text-green-600' : 'text-orange-500'}`}>{order.payment_status}</span>
                                </div>
                                {order.payments && order.payments.length > 0 && (
                                    <div className="flex justify-between">
                                        <span className="text-gray-500">Transaction ID</span>
                                        <span className="font-mono text-xs text-gray-900">{order.payments[0].transaction_id || 'N/A'}</span>
                                    </div>
                                )}
                            </div>

                            {/* Price Breakdown */}
                            <div className="space-y-3 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Item Total</span>
                                    <span className="font-medium text-gray-900">{formatPrice(order.subtotal)}</span>
                                </div>
                                {order.discount > 0 && (
                                    <div className="flex justify-between">
                                        <span className="text-gray-500">Discount</span>
                                        <span className="font-medium text-green-600">-{formatPrice(order.discount)}</span>
                                    </div>
                                )}
                                {order.coupon_discount > 0 && (
                                    <div className="flex justify-between">
                                        <span className="text-gray-500">Coupon Discount</span>
                                        <span className="font-medium text-green-600">-{formatPrice(order.coupon_discount)}</span>
                                    </div>
                                )}
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Shipping</span>
                                    <span className="font-medium text-gray-900">{order.shipping > 0 ? formatPrice(order.shipping) : 'Free'}</span>
                                </div>
                                <div className="flex justify-between pt-3 border-t border-gray-200 text-base font-bold">
                                    <span>Grand Total</span>
                                    <span>{formatPrice(order.total)}</span>
                                </div>
                            </div>
                        </div>

                        {['cancelled', 'return_requested', 'returned'].includes(order.status) && (
                            <div className="bg-gray-50 rounded-xl border border-gray-200 p-6 text-sm text-gray-600">
                                <p className="font-bold text-gray-900 mb-2">Refund Status</p>
                                <p>Refund amounts typically reflect in your original payment method within 5-7 business days.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default OrderDetailsPage;
