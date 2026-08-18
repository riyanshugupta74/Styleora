import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';
import ProductCard from '../components/ProductCard';
import { useToast } from '../context/ToastContext';

const WishlistPage = () => {
    const { showToast } = useToast();
    const [wishlistProducts, setWishlistProducts] = useState([]);
    const [loading, setLoading] = useState(true);

    const fetchWishlist = async () => {
        setLoading(true);
        try {
            const response = await api.get('/api/wishlist');
            setWishlistProducts(response.data.products || []);
        } catch (error) {
            console.error('Failed to fetch wishlist', error);
            showToast('Failed to load wishlist', 'error');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchWishlist();
        window.scrollTo(0, 0);
    }, []);

    const handleWishlistChange = (data) => {
        // If an item was removed, we should refresh the list
        if (data.action === 'removed') {
            fetchWishlist();
        }
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    const wishlistIds = wishlistProducts.map(p => p.id);

    return (
        <div className="bg-white min-h-screen pt-8 pb-20">
            <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <h1 className="font-outfit text-3xl font-bold text-gray-900 mb-8 uppercase tracking-wider">
                    My Wishlist <span className="text-gray-400 text-sm font-medium normal-case ml-2">- {wishlistProducts.length} items</span>
                </h1>

                {wishlistProducts.length > 0 ? (
                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                        {wishlistProducts.map(product => (
                            <ProductCard 
                                key={product.id} 
                                product={product} 
                                wishlistProductIds={wishlistIds} 
                                onWishlistChange={handleWishlistChange}
                            />
                        ))}
                    </div>
                ) : (
                    <div className="py-20 text-center max-w-lg mx-auto">
                        <i className="fa-regular fa-heart text-6xl text-gray-200 mb-6 block"></i>
                        <h3 className="text-xl font-bold text-gray-900 mb-3">Your wishlist is empty</h3>
                        <p className="text-gray-500 mb-8">Save items that you like in your wishlist. Review them anytime and easily move them to the bag.</p>
                        <Link to="/" className="inline-block border-2 border-black text-black px-8 py-3 font-bold hover:bg-black hover:text-white transition-colors uppercase tracking-wider text-sm rounded">
                            Continue Shopping
                        </Link>
                    </div>
                )}
            </div>
        </div>
    );
};

export default WishlistPage;
