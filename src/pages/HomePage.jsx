import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';
import ProductCard from '../components/ProductCard';
import { useToast } from '../context/ToastContext';

const HomePage = () => {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const { showToast } = useToast();

    useEffect(() => {
        const fetchHomeData = async () => {
            try {
                const response = await api.get('/api/home');
                setData(response.data);
            } catch (error) {
                console.error("Failed to fetch home data", error);
                showToast("Failed to load data. Please refresh.", "error");
            } finally {
                setLoading(false);
            }
        };

        fetchHomeData();
    }, []);

    const updateWishlist = (responseData) => {
        // Handled by ProductCard and Toast via API
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    if (!data) return null;

    const { highlightCats, saleProducts, newProducts, trendingProducts, wishlistProductIds } = data;

    return (
        <div className="pb-16 pt-2">
            {/* Hero Section */}
            <section className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-12">
                <div className="relative w-full h-[500px] md:h-[600px] rounded-xl overflow-hidden shadow-xl bg-gray-900 group">
                    <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&q=80&w=1600" alt="Fashion Hero" className="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000" />
                    <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
                    <div className="absolute inset-0 flex flex-col justify-center px-6 md:px-20 max-w-2xl">
                        <span className="text-[#ff3f6c] font-bold text-xs sm:text-sm tracking-[0.25em] uppercase mb-3">New Season Arrival</span>
                        <h1 className="text-white font-outfit font-black text-3xl sm:text-5xl md:text-7xl leading-snug sm:leading-none mb-5 sm:mb-6 tracking-wide">THE GRAND<br/>FASHION SALE</h1>
                        <p className="text-gray-200 text-xs sm:text-lg md:text-xl mb-6 sm:mb-8 font-light leading-relaxed line-clamp-2 sm:line-clamp-none">Elevate your style with up to <span className="font-bold text-white">70% OFF</span> on premium brands. Limited time only.</p>
                        <div className="flex flex-row gap-4 sm:gap-6">
                            <Link to="/women" className="bg-[#ff3f6c] hover:bg-[#ed3a64] text-white font-bold py-2.5 sm:py-4 px-6 sm:px-10 text-xs sm:text-sm md:text-base text-center tracking-widest transition-colors rounded-sm shadow-md uppercase">WOMEN</Link>
                            <Link to="/men" className="bg-white hover:bg-gray-100 text-black font-bold py-2.5 sm:py-4 px-6 sm:px-10 text-xs sm:text-sm md:text-base text-center tracking-widest transition-colors rounded-sm shadow-md uppercase">MEN</Link>
                        </div>
                    </div>
                </div>
            </section>

            {/* Deal Highlights (Circular Cards) */}
            {highlightCats && highlightCats.length > 0 && (
                <section className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                    <h2 className="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 mb-8 flex items-center gap-4">
                        <span className="w-2 h-8 bg-[#ff3f6c]"></span> Top Categories
                    </h2>
                    <div className="flex overflow-x-auto gap-6 md:gap-8 pb-4 scrollbar-hide snap-x">
                        {highlightCats
                            .filter(cat => !['men', 'women'].includes(cat.name.toLowerCase()))
                            .map(cat => (
                                <Link key={cat.id} to={`/search?q=${encodeURIComponent(cat.name)}`} className="flex-shrink-0 snap-start group flex flex-col items-center gap-3">
                                    <div className="w-24 h-24 md:w-32 md:h-32 rounded-full overflow-hidden border-2 border-transparent group-hover:border-[#ff3f6c] p-1 transition-all">
                                        <img src={cat.image && cat.image !== 'image' ? cat.image : `https://placehold.co/400x400/111111/ffffff?text=${encodeURIComponent(cat.name)}`} alt={cat.name} className="w-full h-full object-cover rounded-full group-hover:scale-110 transition-transform duration-500" />
                                    </div>
                                    <span className="font-bold text-sm text-gray-800 uppercase tracking-wider group-hover:text-[#ff3f6c] transition-colors">{cat.name}</span>
                                </Link>
                            ))}
                    </div>
                </section>
            )}

            {/* Big Sale Banner */}
            <section className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                <Link to="/sale" className="block relative w-full h-[200px] md:h-[300px] rounded-xl overflow-hidden shadow-md group">
                    <img src="https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&q=80&w=1600" alt="Sale" className="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000" />
                    <div className="absolute inset-0 bg-black/50"></div>
                    <div className="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                        <h3 className="text-white font-outfit font-black text-3xl md:text-5xl tracking-widest mb-2">DEAL OF THE DAY</h3>
                        <p className="text-white/90 text-lg md:text-xl font-medium mb-4 tracking-wider">FLAT 50% - 70% OFF</p>
                        <span className="bg-white text-black font-bold py-2 px-6 text-sm uppercase tracking-wider hover:bg-gray-100 transition-colors">Explore Deals</span>
                    </div>
                </Link>
            </section>

            {/* Trending Collection Grid */}
            <section className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                <div className="flex justify-between items-end mb-8">
                    <h2 className="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 flex items-center gap-4">
                        <span className="w-2 h-8 bg-purple-600"></span> Trending Now
                    </h2>
                    <Link to="/trending" className="text-sm font-bold text-gray-500 hover:text-black uppercase tracking-wider transition-colors hidden sm:block">View All <i className="fa-solid fa-arrow-right ml-1"></i></Link>
                </div>

                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
                    {trendingProducts && trendingProducts.length > 0 ? (
                        trendingProducts.map(product => (
                            <ProductCard key={product.id} product={product} wishlistProductIds={wishlistProductIds} onWishlistChange={updateWishlist} />
                        ))
                    ) : (
                        <div className="col-span-full text-center text-gray-500 py-10">No products found.</div>
                    )}
                </div>
                <div className="mt-6 text-center sm:hidden">
                    <Link to="/trending" className="inline-block border border-gray-300 text-gray-700 font-bold py-3 px-8 text-sm uppercase tracking-wider rounded w-full">View All Trending</Link>
                </div>
            </section>

            {/* Sale Collection Grid */}
            <section className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                <div className="flex justify-between items-end mb-8">
                    <h2 className="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 flex items-center gap-4">
                        <span className="w-2 h-8 bg-[#ff3f6c]"></span> Mega Sale
                    </h2>
                    <Link to="/sale" className="text-sm font-bold text-gray-500 hover:text-black uppercase tracking-wider transition-colors hidden sm:block">View All <i className="fa-solid fa-arrow-right ml-1"></i></Link>
                </div>

                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
                    {saleProducts && saleProducts.length > 0 ? (
                        saleProducts.map(product => (
                            <ProductCard key={product.id} product={product} wishlistProductIds={wishlistProductIds} onWishlistChange={updateWishlist} />
                        ))
                    ) : (
                        <div className="col-span-full text-center text-gray-500 py-10">No products found.</div>
                    )}
                </div>
                <div className="mt-6 text-center sm:hidden">
                    <Link to="/sale" className="inline-block border border-gray-300 text-gray-700 font-bold py-3 px-8 text-sm uppercase tracking-wider rounded w-full">View All Sale</Link>
                </div>
            </section>

            {/* New Arrivals Grid */}
            <section className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                <div className="flex justify-between items-end mb-8">
                    <h2 className="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 flex items-center gap-4">
                        <span className="w-2 h-8 bg-blue-600"></span> New Arrivals
                    </h2>
                    <Link to="/new-arrivals" className="text-sm font-bold text-gray-500 hover:text-black uppercase tracking-wider transition-colors hidden sm:block">View All <i className="fa-solid fa-arrow-right ml-1"></i></Link>
                </div>

                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
                    {newProducts && newProducts.length > 0 ? (
                        newProducts.map(product => (
                            <ProductCard key={product.id} product={product} wishlistProductIds={wishlistProductIds} onWishlistChange={updateWishlist} />
                        ))
                    ) : (
                        <div className="col-span-full text-center text-gray-500 py-10">No products found.</div>
                    )}
                </div>
                <div className="mt-6 text-center sm:hidden">
                    <Link to="/new-arrivals" className="inline-block border border-gray-300 text-gray-700 font-bold py-3 px-8 text-sm uppercase tracking-wider rounded w-full">View All New</Link>
                </div>
            </section>
            
            {/* App Download Banner */}
            <section className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <div className="bg-gray-100 rounded-xl p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div className="max-w-xl text-center md:text-left">
                        <h3 className="text-2xl md:text-4xl font-black font-outfit uppercase tracking-tight mb-4">Get the Styleora App</h3>
                        <p className="text-gray-600 mb-6 text-lg">Download our app for exclusive deals, early access to sales, and a faster checkout experience.</p>
                        <div className="flex gap-4 justify-center md:justify-start">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store" className="h-10 cursor-pointer hover:opacity-80 transition-opacity" />
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Play Store" className="h-10 cursor-pointer hover:opacity-80 transition-opacity" />
                        </div>
                    </div>
                    <div className="hidden md:block w-48 opacity-80">
                        <i className="fa-solid fa-mobile-screen-button text-[150px] text-gray-300"></i>
                    </div>
                </div>
            </section>
        </div>
    );
};

export default HomePage;
