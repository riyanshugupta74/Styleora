import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import { useToast } from '../context/ToastContext';
import api from '../services/api';
import { formatPrice, getImageUrl } from '../utils/helpers';

const ProductCard = ({ product, wishlistProductIds = [], onWishlistChange }) => {
    const { isAuthenticated } = useAuth();
    const { addToCart } = useCart();
    const { showToast } = useToast();
    const navigate = useNavigate();

    const [loading, setLoading] = useState(false);
    const inWishlist = wishlistProductIds.includes(product.id);

    const toggleWishlist = async (e) => {
        e.preventDefault(); // Prevent link click
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }

        setLoading(true);
        try {
            const response = await api.post('/api/wishlist/toggle', { product_id: product.id });
            if (response.data.success) {
                showToast(response.data.message, 'success');
                if (onWishlistChange) {
                    onWishlistChange(response.data);
                }
            } else {
                showToast(response.data.message, 'error');
            }
        } catch (error) {
            showToast('Network error', 'error');
        } finally {
            setLoading(false);
        }
    };

    const handleAddToCart = async (e) => {
        e.preventDefault();
        const success = await addToCart(product.id);
        if (!success) {
            navigate(`/product/${product.slug}`);
        }
    };

    const primaryImage = product.images?.find(img => img.is_primary === 1) || product.images?.[0];
    const imageUrl = primaryImage ? getImageUrl(primaryImage.image_path) : '/images/product-placeholder.jpg';

    const discountPercentage = product.discount_price && product.discount_price < product.price
        ? Math.round(((product.price - product.discount_price) / product.price) * 100)
        : null;

    return (
        <div className="group relative bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            
            {/* Image Wrapper */}
            <Link to={`/product/${product.slug}`} className="block relative aspect-[4/5] bg-gray-50 overflow-hidden">
                <img 
                    src={imageUrl} 
                    alt={product.name} 
                    loading="lazy"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                />
                     
                {/* Badges */}
                <div className="absolute top-3 left-3 flex flex-col gap-1 z-10">
                    {discountPercentage && (
                        <span className="bg-[#ff3f6c] text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm tracking-wider">
                            SALE {discountPercentage}%
                        </span>
                    )}
                    {product.is_new_arrival === 1 && (
                        <span className="bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm tracking-wider">
                            NEW
                        </span>
                    )}
                    {product.is_trending === 1 && (
                        <span className="bg-purple-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm tracking-wider">
                            TRENDING
                        </span>
                    )}
                </div>
            </Link>

            {/* Wishlist Button */}
            <div className="absolute top-3 right-3 z-20">
                <button 
                    onClick={toggleWishlist}
                    disabled={loading}
                    className={`w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:scale-110 transition-transform focus:outline-none ${inWishlist ? 'text-[#ff3f6c]' : 'text-gray-400 hover:text-black'}`}
                >
                    {!loading ? (
                        <i className={`fa-heart text-sm transition-colors ${inWishlist ? 'fa-solid' : 'fa-regular'}`}></i>
                    ) : (
                        <i className="fa-solid fa-spinner fa-spin text-xs text-gray-500"></i>
                    )}
                </button>
            </div>

            {/* Product Info */}
            <div className="p-4">
                <p className="text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider line-clamp-1">{product.brand?.name || 'STYLEORA'}</p>
                <Link to={`/product/${product.slug}`} className="block">
                    <h3 className="text-sm font-medium text-gray-900 mb-2 line-clamp-1 group-hover:text-[#ff3f6c] transition-colors">{product.name}</h3>
                </Link>
                
                <div className="flex items-center gap-2 mb-3">
                    {product.discount_price && product.discount_price < product.price ? (
                        <>
                            <span className="text-sm font-bold text-gray-900">{formatPrice(product.discount_price)}</span>
                            <span className="text-xs text-gray-400 line-through">{formatPrice(product.price)}</span>
                        </>
                    ) : (
                        <span className="text-sm font-bold text-gray-900">{formatPrice(product.price)}</span>
                    )}
                </div>

                <button 
                    onClick={handleAddToCart} 
                    className="w-full bg-white border border-gray-200 text-gray-900 py-2 rounded-lg text-sm font-bold hover:bg-black hover:text-white hover:border-black transition-all"
                >
                    ADD TO BAG
                </button>
            </div>
        </div>
    );
};

export default ProductCard;
