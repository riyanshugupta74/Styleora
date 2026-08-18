import { useState, useRef, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import api from '../services/api';
import { getImageUrl } from '../utils/helpers';

const Header = () => {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [searchOpen, setSearchOpen] = useState(false);
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const [query, setQuery] = useState('');
    const [results, setResults] = useState([]);
    const [isSearching, setIsSearching] = useState(false);

    const { user, isAdmin, logout } = useAuth();
    const { cartCount } = useCart();
    const navigate = useNavigate();
    const searchRef = useRef();
    const userMenuRef = useRef();

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (searchRef.current && !searchRef.current.contains(event.target)) {
                setSearchOpen(false);
            }
            if (userMenuRef.current && !userMenuRef.current.contains(event.target)) {
                setUserMenuOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    useEffect(() => {
        const fetchSuggestions = async () => {
            if (query.length < 2) {
                setResults([]);
                return;
            }
            setIsSearching(true);
            try {
                const res = await api.get(`/api/search/suggestions?q=${query}`);
                setResults(res.data);
            } catch (err) {
                console.error(err);
            } finally {
                setIsSearching(false);
            }
        };

        const timer = setTimeout(fetchSuggestions, 300);
        return () => clearTimeout(timer);
    }, [query]);

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        if (query.trim()) {
            navigate(`/search?q=${query}`);
            setSearchOpen(false);
        }
    };

    const handleLogout = async () => {
        await logout();
        navigate('/');
    };

    return (
        <header className="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 transition-all duration-300">
            {/* Top Announcement Bar */}
            <div className="bg-black text-white text-[10px] sm:text-xs py-2 px-4 flex justify-center sm:justify-between items-center">
                <div className="flex space-x-6 text-center sm:text-left w-full sm:w-auto justify-center sm:justify-start">
                    <span className="tracking-wider uppercase font-medium">✨ EXTRA 10% OFF on first order! Code: STYLE10</span>
                </div>
                <div className="hidden sm:flex space-x-4 tracking-wider uppercase font-medium">
                    <Link to="/track-order" className="hover:text-gray-300 transition">Track Order</Link>
                    <span className="text-gray-500">|</span>
                    <Link to="/contact" className="hover:text-gray-300 transition">Customer Service</Link>
                </div>
            </div>

            {/* Main Header Container */}
            <div className="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8">
                
                {/* Mobile Top Row: STYLEORA Logo (Left) | Profile, Wishlist & Cart Icons (Right) */}
                <div className="flex lg:hidden items-center justify-between h-12 pt-2">
                    {/* Left: STYLEORA Logo */}
                    <Link to="/" className="font-outfit font-black text-xl tracking-[0.18em] text-black">
                        STYLEORA
                    </Link>

                    {/* Right: Mobile Profile, Wishlist & Cart Icons */}
                    <div className="flex items-center gap-4 sm:gap-6">
                        <Link to={user ? "/account/profile" : "/login"} className="text-gray-800 hover:text-[#ff3f6c] p-2 transition">
                            <i className="fa-regular fa-user text-xl"></i>
                        </Link>
                        <Link to="/wishlist" className="text-gray-800 hover:text-[#ff3f6c] p-2 transition">
                            <i className="fa-regular fa-heart text-xl"></i>
                        </Link>
                        <Link to="/cart" className="text-gray-800 hover:text-[#ff3f6c] p-2 transition relative">
                            <i className="fa-solid fa-bag-shopping text-xl"></i>
                            {cartCount > 0 && (
                                <span className="absolute -top-1 -right-1 bg-[#ff3f6c] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white">
                                    {cartCount}
                                </span>
                            )}
                        </Link>
                    </div>
                </div>

                {/* Mobile Bottom Row: Search Space Input */}
                <div className="lg:hidden pb-3 pt-2" ref={searchRef}>
                    <form onSubmit={handleSearchSubmit} className="relative">
                        <input 
                            type="text" 
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                            placeholder="Search products, brands..." 
                            className="w-full bg-gray-100 border border-transparent focus:bg-white focus:border-black rounded-lg py-2 px-4 pl-9 text-xs font-medium focus:outline-none transition-all placeholder-gray-400" 
                        />
                        <i className="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        {query && (
                            <button type="button" onClick={() => setQuery('')} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black text-xs">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        )}
                    </form>

                    {/* Mobile Search Suggestions Dropdown */}
                    {results.length > 0 && query.length > 1 && (
                        <div className="absolute left-3 right-3 top-full mt-1 bg-white shadow-2xl border border-gray-200 p-3 rounded-xl z-50 max-h-[50vh] overflow-y-auto">
                            {results.map(product => (
                                <Link 
                                    key={product.id} 
                                    to={`/product/${product.slug}`} 
                                    onClick={() => setQuery('')}
                                    className="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition"
                                >
                                    <img src={getImageUrl(product.image)} className="w-10 h-12 object-cover rounded" alt={product.name} />
                                    <div className="flex-1 min-w-0">
                                        <p className="text-[10px] font-bold text-gray-400 uppercase truncate">{product.brand}</p>
                                        <p className="text-xs font-medium text-gray-900 truncate">{product.name}</p>
                                        <p className="text-xs font-bold text-black mt-0.5">₹{product.discount_price || product.price}</p>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    )}
                </div>

                {/* Desktop Header Row */}
                <div className="hidden lg:flex justify-between items-center h-20">
                    <div className="hidden lg:flex flex-shrink-0 items-center">
                        <Link to="/" className="font-outfit font-black text-2xl tracking-[0.2em] text-black flex items-center">
                            STYLEORA
                        </Link>
                    </div>

                    {/* Desktop Navigation */}
                    <nav className="hidden lg:flex space-x-12 items-center h-full ml-12">
                        <Link to="/men" className="text-sm font-bold text-gray-900 hover:text-black py-7 transition-all border-b-4 border-transparent hover:border-[#ff3f6c] tracking-wider uppercase">MEN</Link>
                        <Link to="/women" className="text-sm font-bold text-gray-900 hover:text-black py-7 transition-all border-b-4 border-transparent hover:border-[#ff3f6c] tracking-wider uppercase">WOMEN</Link>
                        <Link to="/sale" className="text-sm font-bold text-red-600 hover:text-red-700 py-7 transition-all border-b-4 border-transparent hover:border-red-600 tracking-wider uppercase">SALE</Link>
                        <span className="text-gray-300">|</span>
                        <Link to="/new-arrivals" className="text-sm font-bold text-gray-900 hover:text-black py-7 transition-all border-b-4 border-transparent hover:border-black tracking-wider uppercase">NEW ARRIVALS</Link>
                        <Link to="/trending" className="text-sm font-bold text-gray-900 hover:text-black py-7 transition-all border-b-4 border-transparent hover:border-black tracking-wider uppercase">TRENDING</Link>
                    </nav>

                    {/* Desktop Right Icons (Hidden on Mobile) */}
                    <div className="hidden lg:flex items-center space-x-7 z-10">

                        {/* Advanced Search */}
                        <div className="relative group" ref={searchRef}>
                            <button onClick={() => setSearchOpen(!searchOpen)} className="text-gray-800 hover:text-[#ff3f6c] transition p-2 flex flex-col items-center gap-1 group">
                                <i className="fa-solid fa-magnifying-glass text-lg group-hover:scale-110 transition-transform"></i>
                                <span className="text-[10px] font-bold tracking-wider xl:block">SEARCH</span>
                            </button>

                            {/* Search Dropdown */}
                            {searchOpen && (
                                <div className="absolute right-0 top-full mt-2 w-[500px] bg-white shadow-2xl border border-gray-100 p-4 rounded-xl z-50">
                                    <form onSubmit={handleSearchSubmit} className="relative">
                                        <input
                                            type="text"
                                            value={query}
                                            onChange={(e) => setQuery(e.target.value)}
                                            placeholder="Search for products, categories..."
                                            className="w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-[#ff3f6c] focus:ring-0 rounded-lg py-3 px-5 pl-12 text-sm transition-all"
                                            autoFocus
                                        />
                                        <i className="fa-solid fa-magnifying-glass absolute left-4 top-4 text-gray-400 text-sm"></i>
                                        {isSearching && (
                                            <div className="absolute right-4 top-4">
                                                <i className="fa-solid fa-circle-notch fa-spin text-gray-400"></i>
                                            </div>
                                        )}
                                    </form>

                                    {/* Search Results */}
                                    {results.length > 0 && (
                                        <div className="mt-4 flex flex-col gap-3 max-h-[60vh] overflow-y-auto">
                                            <p className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Suggestions</p>
                                            {results.map(product => (
                                                <Link
                                                    key={product.id}
                                                    to={`/product/${product.slug}`}
                                                    onClick={() => setSearchOpen(false)}
                                                    className="flex items-center gap-4 p-2 hover:bg-gray-50 rounded-lg transition"
                                                >
                                                    <img src={getImageUrl(product.image)} className="w-12 h-16 object-cover rounded-md" alt={product.name} />
                                                    <div className="flex-1">
                                                        <p className="text-xs text-gray-500 font-bold uppercase tracking-wider">{product.brand}</p>
                                                        <p className="text-sm text-gray-900 font-medium line-clamp-1">{product.name}</p>
                                                        <div className="flex items-center gap-2 mt-1 text-sm">
                                                            <span className="font-bold text-gray-900">₹{product.discount_price || product.price}</span>
                                                            {product.discount_price && (
                                                                <span className="text-gray-400 line-through text-xs">₹{product.price}</span>
                                                            )}
                                                        </div>
                                                    </div>
                                                </Link>
                                            ))}
                                            <Link to={`/search?q=${query}`} onClick={() => setSearchOpen(false)} className="block text-center text-sm font-bold text-[#ff3f6c] hover:underline mt-2 p-2">
                                                View all results
                                            </Link>
                                        </div>
                                    )}
                                    {query.length > 1 && results.length === 0 && !isSearching && (
                                        <div className="mt-4 p-4 text-center text-gray-500 text-sm">
                                            No products found matching your search.
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>

                        {/* Account */}
                        <div className="relative group" ref={userMenuRef}>
                            <button
                                onClick={() => setUserMenuOpen(!userMenuOpen)}
                                onMouseEnter={() => setUserMenuOpen(true)}
                                className="text-gray-800 hover:text-[#ff3f6c] transition p-2 flex flex-col items-center gap-1 group"
                            >
                                <i className="fa-regular fa-user text-lg group-hover:scale-110 transition-transform"></i>
                                <span className="text-[10px] font-bold tracking-wider xl:block">PROFILE</span>
                            </button>

                            {userMenuOpen && (
                                <div
                                    onMouseLeave={() => setUserMenuOpen(false)}
                                    className="absolute right-0 top-full w-56 bg-white shadow-2xl border border-gray-100 py-3 rounded-xl z-50"
                                >
                                    {user ? (
                                        <>
                                            <div className="px-5 py-3 border-b border-gray-100 mb-2">
                                                <p className="text-xs text-gray-500 font-medium">Hello,</p>
                                                <p className="text-sm font-bold text-gray-900 truncate">{user.name}</p>
                                            </div>
                                            {isAdmin && (
                                                <>
                                                    <a href="/admin" className="flex items-center gap-2 mx-4 my-2 px-4 py-2 bg-gray-900 text-white text-sm font-bold rounded-md hover:bg-[#ff3f6c] transition">
                                                        <i className="fa-solid fa-gauge"></i> Admin Dashboard
                                                    </a>
                                                    <div className="border-t border-gray-100 mt-1 mb-1"></div>
                                                </>
                                            )}
                                            <Link to="/account/profile" onClick={() => setUserMenuOpen(false)} className="block px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-pink-50 hover:text-[#ff3f6c] transition-colors"><i className="fa-solid fa-user w-5 mr-2"></i> My Profile</Link>
                                            <Link to="/orders" onClick={() => setUserMenuOpen(false)} className="block px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-pink-50 hover:text-[#ff3f6c] transition-colors"><i className="fa-solid fa-box-open w-5 mr-2"></i> My Orders</Link>
                                            <Link to="/wishlist" onClick={() => setUserMenuOpen(false)} className="block px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-pink-50 hover:text-[#ff3f6c] transition-colors"><i className="fa-solid fa-heart w-5 mr-2"></i> Wishlist</Link>
                                            <div className="border-t border-gray-100 mt-2 pt-2">
                                                <button onClick={() => { handleLogout(); setUserMenuOpen(false); }} className="block w-full text-left px-5 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors"><i className="fa-solid fa-arrow-right-from-bracket w-5 mr-2"></i> Logout</button>
                                            </div>
                                        </>
                                    ) : (
                                        <>
                                            <div className="px-5 py-3 border-b border-gray-100 mb-2">
                                                <p className="text-sm font-bold text-gray-900 mb-1">Welcome to Styleora</p>
                                                <p className="text-xs text-gray-500">Sign in to access your orders, offers and wishlist.</p>
                                            </div>
                                            <Link to="/login" onClick={() => setUserMenuOpen(false)} className="block mx-4 my-2 px-4 py-2.5 bg-[#ff3f6c] text-white text-center text-sm font-bold rounded-md hover:bg-[#ed3a64] transition shadow-md">SIGN IN</Link>
                                            <Link to="/register" onClick={() => setUserMenuOpen(false)} className="block px-5 py-2 text-sm text-center font-medium text-gray-600 hover:text-[#ff3f6c] hover:underline transition">Create an account</Link>
                                        </>
                                    )}
                                </div>
                            )}
                        </div>

                        {/* Wishlist Icon */}
                        <Link to="/wishlist" className="text-gray-800 hover:text-[#ff3f6c] transition p-2 flex flex-col items-center gap-1 group relative">
                            <i className="fa-regular fa-heart text-lg group-hover:scale-110 transition-transform"></i>
                            <span className="text-[10px] font-bold tracking-wider xl:block">WISHLIST</span>
                        </Link>

                        {/* Cart Icon */}
                        <Link to="/cart" className="text-gray-800 hover:text-[#ff3f6c] transition p-2 flex flex-col items-center gap-1 group relative">
                            <i className="fa-solid fa-bag-shopping text-xl group-hover:scale-110 transition-transform"></i>
                            <span className="text-[10px] font-bold tracking-wider xl:block">BAG</span>
                            {cartCount > 0 && (
                                <span className="absolute top-0 right-0 bg-[#ff3f6c] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white">
                                    {cartCount}
                                </span>
                            )}
                        </Link>
                    </div>
                </div>
            </div>
        </header>
    );
};

export default Header;
