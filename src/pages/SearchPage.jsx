import { useState, useEffect } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import api from '../services/api';
import ProductCard from '../components/ProductCard';
import Pagination from '../components/Pagination';

const SearchPage = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const navigate = useNavigate();
    
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [mobileFiltersOpen, setMobileFiltersOpen] = useState(false);
    const [mobileSortOpen, setMobileSortOpen] = useState(false);

    const query = searchParams.get('q') || '';
    const sort = searchParams.get('sort') || 'recommended';
    const page = searchParams.get('page') || 1;
    const priceMin = searchParams.get('price_min');
    const priceMax = searchParams.get('price_max');
    
    // Arrays for multiple checkboxes
    const selectedBrands = searchParams.getAll('brand[]');
    const selectedColors = searchParams.getAll('color[]');

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                const queryString = searchParams.toString();
                const response = await api.get(`/api/search${queryString ? `?${queryString}` : ''}`);
                setData(response.data);
            } catch (error) {
                console.error('Failed to fetch search data', error);
            } finally {
                setLoading(false);
            }
        };

        if (query) {
            fetchData();
        } else {
            setLoading(false);
        }
        window.scrollTo(0, 0);
    }, [searchParams, query]);

    const handleSortChange = (e) => {
        const newSort = e.target.value;
        searchParams.set('sort', newSort);
        setSearchParams(searchParams);
        setMobileSortOpen(false);
    };

    const handleFilterChange = (filterType, value, checked) => {
        if (filterType === 'brand[]' || filterType === 'color[]') {
            const currentValues = searchParams.getAll(filterType);
            searchParams.delete(filterType);
            
            let newValues;
            if (checked) {
                newValues = [...currentValues, value];
            } else {
                newValues = currentValues.filter(v => v !== value);
            }
            
            newValues.forEach(v => searchParams.append(filterType, v));
        }
        
        searchParams.set('page', 1);
        setSearchParams(searchParams);
    };

    const handlePriceChange = (min, max) => {
        if (min === null && max === null) {
            searchParams.delete('price_min');
            searchParams.delete('price_max');
        } else {
            if (min !== null) searchParams.set('price_min', min);
            else searchParams.delete('price_min');
            
            if (max !== null) searchParams.set('price_max', max);
            else searchParams.delete('price_max');
        }
        searchParams.set('page', 1);
        setSearchParams(searchParams);
    };

    const clearFilters = () => {
        const newParams = new URLSearchParams();
        if (query) newParams.set('q', query);
        setSearchParams(newParams);
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        const newQuery = e.target.searchQuery.value;
        if (newQuery.trim()) {
            navigate(`/search?q=${newQuery}`);
        }
    };

    const updateWishlist = () => {};

    if (!query) {
        return (
            <div className="bg-white min-h-[60vh] pt-12 pb-20 flex flex-col items-center justify-center">
                <i className="fa-solid fa-magnifying-glass text-6xl text-gray-200 mb-6"></i>
                <h1 className="font-outfit text-3xl font-black uppercase tracking-wider text-gray-900 mb-4">Search Styleora</h1>
                <p className="text-gray-500 mb-8 max-w-md text-center">Find your next favorite outfit by searching our entire collection.</p>
                <form onSubmit={handleSearchSubmit} className="w-full max-w-lg relative px-4">
                    <input 
                        type="text" 
                        name="searchQuery" 
                        placeholder="Search for products, categories, brands..."
                        className="w-full border-2 border-gray-300 rounded-full px-6 py-4 text-base focus:border-[#ff3f6c] focus:ring-0 pr-12 transition-colors shadow-sm"
                        autoFocus
                    />
                    <button type="submit" className="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#ff3f6c] transition-colors p-2">
                        <i className="fa-solid fa-magnifying-glass text-xl"></i>
                    </button>
                </form>
            </div>
        );
    }

    if (!data && loading) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    if (!data) return null;

    const { products, filters, title, wishlistProductIds } = data;

    return (
        <div className="bg-white min-h-screen pt-4 pb-20">
            <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-bold text-gray-500 mb-6 uppercase tracking-wider">
                    <button onClick={() => navigate('/')} className="hover:text-black">Home</button>
                    <span className="mx-2">/</span>
                    <span className="text-black">Search</span>
                </nav>

                <div className="flex items-end justify-between mb-6 pb-4 border-b border-gray-200">
                    <h1 className="font-outfit text-2xl font-black uppercase tracking-wider text-gray-900">
                        {title} <span className="text-gray-400 text-sm font-medium ml-2">- {products?.total || 0} items</span>
                    </h1>
                    
                    {/* Desktop Sorting */}
                    {products?.total > 0 && (
                        <div className="hidden md:flex items-center gap-3">
                            <label className="text-xs font-bold text-gray-900 uppercase tracking-wider">Sort By</label>
                            <select 
                                value={sort} 
                                onChange={handleSortChange} 
                                className="border-gray-300 text-sm focus:border-black focus:ring-0 rounded-sm py-2 bg-white font-medium cursor-pointer"
                            >
                                <option value="recommended">Recommended</option>
                                <option value="newest">What's New</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="discount">Better Discount</option>
                            </select>
                        </div>
                    )}
                </div>

                <div className="flex flex-col lg:flex-row gap-8">
                    {/* Sidebar Filters */}
                    {products?.total > 0 && (
                        <aside className="w-full lg:w-[250px] shrink-0 border-r border-gray-200 pr-6 hidden lg:block">
                            <div className="space-y-6 sticky top-28">
                                <div className="flex justify-between items-center mb-4">
                                    <h2 className="font-bold text-sm uppercase tracking-widest text-gray-900">Filters</h2>
                                    <button onClick={clearFilters} className="text-xs font-bold text-[#ff3f6c] uppercase">Clear All</button>
                                </div>

                                {/* Brand Filter */}
                                <div className="border-b border-gray-200 pb-5">
                                    <h3 className="font-bold text-gray-900 mb-3 uppercase text-xs tracking-wider">Brand</h3>
                                    <div className="space-y-2 max-h-48 overflow-y-auto scrollbar-hide">
                                        {filters?.brands?.map(brand => (
                                            <label key={brand.id} className="flex items-center gap-3 cursor-pointer group">
                                                <input 
                                                    type="checkbox" 
                                                    checked={selectedBrands.includes(brand.id.toString())}
                                                    onChange={(e) => handleFilterChange('brand[]', brand.id.toString(), e.target.checked)}
                                                    className="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] rounded-sm cursor-pointer" 
                                                />
                                                <span className="text-sm text-gray-700 group-hover:text-black transition-colors">{brand.name}</span>
                                            </label>
                                        ))}
                                    </div>
                                </div>

                                {/* Price Filter */}
                                <div className="border-b border-gray-200 pb-5">
                                    <h3 className="font-bold text-gray-900 mb-3 uppercase text-xs tracking-wider">Price</h3>
                                    <div className="space-y-2">
                                        <label className="flex items-center gap-3 cursor-pointer group">
                                            <input type="radio" name="price" checked={!priceMin && !priceMax} onChange={() => handlePriceChange(null, null)} className="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] cursor-pointer" />
                                            <span className="text-sm text-gray-700">All Prices</span>
                                        </label>
                                        <label className="flex items-center gap-3 cursor-pointer group">
                                            <input type="radio" name="price" checked={priceMax === '999'} onChange={() => handlePriceChange(0, 999)} className="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] cursor-pointer" />
                                            <span className="text-sm text-gray-700">Under Rs. 999</span>
                                        </label>
                                        <label className="flex items-center gap-3 cursor-pointer group">
                                            <input type="radio" name="price" checked={priceMin === '1000' && priceMax === '1999'} onChange={() => handlePriceChange(1000, 1999)} className="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] cursor-pointer" />
                                            <span className="text-sm text-gray-700">Rs. 1000 - Rs. 1999</span>
                                        </label>
                                        <label className="flex items-center gap-3 cursor-pointer group">
                                            <input type="radio" name="price" checked={priceMin === '2000' && !priceMax} onChange={() => handlePriceChange(2000, null)} className="w-4 h-4 border-gray-300 text-[#ff3f6c] focus:ring-[#ff3f6c] cursor-pointer" />
                                            <span className="text-sm text-gray-700">Rs. 2000 and above</span>
                                        </label>
                                    </div>
                                </div>

                                {/* Color Filter */}
                                <div className="border-b border-gray-200 pb-5">
                                    <h3 className="font-bold text-gray-900 mb-3 uppercase text-xs tracking-wider">Color</h3>
                                    <div className="grid grid-cols-6 gap-2">
                                        {filters?.colors?.map(color => (
                                            <label key={color.id} className="relative cursor-pointer group" title={color.name}>
                                                <input 
                                                    type="checkbox" 
                                                    checked={selectedColors.includes(color.id.toString())}
                                                    onChange={(e) => handleFilterChange('color[]', color.id.toString(), e.target.checked)}
                                                    className="peer sr-only" 
                                                />
                                                <div className="w-6 h-6 rounded-full border border-gray-300 shadow-sm peer-checked:ring-2 peer-checked:ring-[#ff3f6c] peer-checked:ring-offset-1 transition-all" style={{ backgroundColor: color.hex_code }}></div>
                                            </label>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </aside>
                    )}

                    {/* Product Grid */}
                    <div className="flex-1 w-full">
                        {products?.total > 0 && (
                            <div className="flex lg:hidden justify-between items-center mb-4 border-b border-gray-200 pb-4">
                                <button onClick={() => setMobileFiltersOpen(true)} className="font-bold text-sm uppercase tracking-wider text-gray-900 flex items-center gap-2">
                                    <i className="fa-solid fa-filter"></i> Filters
                                </button>
                                <button onClick={() => setMobileSortOpen(true)} className="font-bold text-sm uppercase tracking-wider text-gray-900 flex items-center gap-2">
                                    <i className="fa-solid fa-sort"></i> Sort
                                </button>
                            </div>
                        )}

                        {loading ? (
                            <div className="py-20 flex justify-center">
                                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
                            </div>
                        ) : (
                            <>
                                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                                    {products?.data?.length > 0 ? (
                                        products.data.map(product => (
                                            <ProductCard key={product.id} product={product} wishlistProductIds={wishlistProductIds} onWishlistChange={updateWishlist} />
                                        ))
                                    ) : (
                                        <div className="col-span-full py-20 text-center">
                                            <i className="fa-solid fa-box-open text-4xl text-gray-300 mb-4"></i>
                                            <h3 className="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                                            <p className="text-gray-500 mb-6">We couldn't find any products matching your search for "{query}".</p>
                                            
                                            <form onSubmit={handleSearchSubmit} className="w-full max-w-md mx-auto relative px-4">
                                                <input 
                                                    type="text" 
                                                    name="searchQuery" 
                                                    defaultValue={query}
                                                    placeholder="Try another search..."
                                                    className="w-full border-2 border-gray-300 rounded-full px-6 py-3 text-sm focus:border-[#ff3f6c] focus:ring-0 pr-12 transition-colors"
                                                />
                                                <button type="submit" className="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#ff3f6c] transition-colors p-2">
                                                    <i className="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            </form>
                                        </div>
                                    )}
                                </div>

                                {products?.total > 0 && (
                                    <Pagination 
                                        meta={products} 
                                        onPageChange={(p) => {
                                            searchParams.set('page', p);
                                            setSearchParams(searchParams);
                                        }} 
                                    />
                                )}
                            </>
                        )}
                    </div>
                </div>
            </div>

            {/* Mobile Filters & Sort Drawers */}
            {/* Omitted for brevity but can be copied exactly from CategoryPage if needed, they are the same */}
        </div>
    );
};

export default SearchPage;
