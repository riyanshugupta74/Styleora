import { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import api from '../services/api';
import ProductCard from '../components/ProductCard';
import { useCart } from '../context/CartContext';
import { useToast } from '../context/ToastContext';
import { formatPrice, getImageUrl } from '../utils/helpers';

const ProductPage = () => {
    const { slug } = useParams();
    const navigate = useNavigate();
    const { addToCart } = useCart();
    const { showToast } = useToast();

    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    
    // Product State
    const [selectedColor, setSelectedColor] = useState('');
    const [selectedSize, setSelectedSize] = useState('');
    const [quantity, setQuantity] = useState(1);
    const [galleryImage, setGalleryImage] = useState('');
    
    // Delivery State
    const [pincode, setPincode] = useState('');
    const [deliveryChecked, setDeliveryChecked] = useState(false);
    const [deliveryAvailable, setDeliveryAvailable] = useState(false);
    const [deliveryDate, setDeliveryDate] = useState('');

    useEffect(() => {
        const fetchProductData = async () => {
            setLoading(true);
            try {
                const response = await api.get(`/api/shop/product/${slug}`);
                setData(response.data);
                
                // Set initial states
                const { product } = response.data;
                const primaryImage = product.images.find(img => img.is_primary === 1) || product.images[0];
                setGalleryImage(primaryImage ? getImageUrl(primaryImage.image_path) : '/images/product-placeholder.jpg');
                
                if (product.variants?.length > 0) {
                    setSelectedColor(product.variants[0].color_id?.toString() || '');
                }
                
                // Reset states on product change
                setSelectedSize('');
                setQuantity(1);
                setPincode('');
                setDeliveryChecked(false);
            } catch (error) {
                console.error("Failed to fetch product", error);
                if (error.response?.status === 404) {
                    navigate('/');
                }
            } finally {
                setLoading(false);
            }
        };

        fetchProductData();
        window.scrollTo(0, 0);
    }, [slug, navigate]);

    if (loading || !data) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    const { product, relatedProducts, wishlistProductIds } = data;

    // Derived State
    const hasVariants = product.variants?.length > 0;
    const colors = hasVariants ? Array.from(new Map(product.variants.filter(v => v.color).map(v => [v.color_id, v.color])).values()) : [];
    const sizes = hasVariants ? Array.from(new Map(product.variants.filter(v => v.size).map(v => [v.size_id, v.size])).values()) : [];

    const activeVariant = hasVariants ? product.variants.find(v => 
        v.color_id?.toString() === selectedColor && v.size_id?.toString() === selectedSize
    ) : null;

    const basePrice = activeVariant?.price || product.price;
    let activePrice = product.discount_price || product.price;

    if (activeVariant) {
        if (activeVariant.discount_price && activeVariant.discount_price < activeVariant.price) {
            activePrice = activeVariant.discount_price;
        } else if (product.discount_price && product.discount_price < product.price) {
            if (activeVariant.price === product.price || !activeVariant.price) {
                activePrice = product.discount_price;
            } else {
                const ratio = product.discount_price / product.price;
                activePrice = Math.round(activeVariant.price * ratio);
            }
        } else {
            activePrice = activeVariant.price || product.price;
        }
    }

    const variantSelected = selectedColor && selectedSize;
    
    const availableStock = hasVariants 
        ? (variantSelected && activeVariant ? activeVariant.stock : 0)
        : (product.stock ?? 999);
        
    const inStock = availableStock > 0;

    const discountPercentage = basePrice > activePrice
        ? Math.round(((basePrice - activePrice) / basePrice) * 100)
        : null;

    // Handlers
    const checkDelivery = () => {
        if (pincode.length === 6) {
            setDeliveryChecked(true);
            const available = pincode.startsWith('1') || pincode.startsWith('4') || pincode.startsWith('5') || pincode.startsWith('7');
            setDeliveryAvailable(available);
            
            if (available) {
                const d = new Date();
                d.setDate(d.getDate() + 3 + Math.floor(Math.random() * 3));
                setDeliveryDate(d.toLocaleDateString('en-IN', { weekday: 'short', month: 'short', day: 'numeric' }));
            }
        }
    };

    const handleAddToCart = async (e) => {
        e.preventDefault();
        
        if (hasVariants && !variantSelected) {
            showToast('Please select both Color and Size.', 'error');
            return;
        }

        if (quantity > availableStock) {
            showToast(`Only ${availableStock} items available in stock.`, 'error');
            return;
        }

        try {
            await addToCart(product.id, activeVariant?.id, selectedColor, selectedSize, quantity);
        } catch (error) {
            console.error(error);
        }
    };

    return (
        <div className="bg-white min-h-screen pt-8 pb-20">
            <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                
                {/* Breadcrumbs */}
                <nav className="flex text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
                    <ol className="inline-flex items-center space-x-1 md:space-x-3">
                        <li className="inline-flex items-center">
                            <Link to="/" className="hover:text-black">Home</Link>
                        </li>
                        <li>
                            <div className="flex items-center">
                                <i className="fa-solid fa-chevron-right text-xs mx-2"></i>
                                {product.category ? (
                                    <Link to={`/${product.category.slug}`} className="hover:text-black">{product.category.name}</Link>
                                ) : (
                                    <span className="hover:text-black">Category</span>
                                )}
                            </div>
                        </li>
                        <li aria-current="page">
                            <div className="flex items-center">
                                <i className="fa-solid fa-chevron-right text-xs mx-2"></i>
                                <span className="text-gray-900 font-medium">{product.name}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div className="flex flex-col lg:flex-row items-center lg:items-start max-w-4xl lg:max-w-6xl mx-auto gap-12">
                    
                    {/* PRODUCT IMAGE */}
                    <div className="w-full max-w-sm lg:max-w-none lg:w-1/2 mx-auto lg:mx-0">
                        <div className="bg-gray-50 rounded overflow-hidden relative h-[350px] lg:h-auto lg:aspect-[3/4] group w-full mx-auto shadow-sm mb-4">
                            <div className="w-full h-full transition-transform duration-700 group-hover:scale-105">
                                <img src={galleryImage} className="w-full h-full object-contain lg:object-cover" alt={product.name} />
                            </div>
                            {product.is_new_arrival === 1 && (
                                <span className="absolute top-4 left-4 bg-black text-white text-xs font-bold px-3 py-1.5 uppercase tracking-wider z-10 shadow-sm">NEW</span>
                            )}
                        </div>

                        {/* IMAGE GALLERY */}
                        {product.images?.length > 1 && (
                            <div className="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                                {product.images.map(img => (
                                    <button 
                                        key={img.id}
                                        type="button"
                                        onClick={() => setGalleryImage(getImageUrl(img.image_path))}
                                        className={`w-20 h-24 shrink-0 rounded overflow-hidden border-2 transition-all ${galleryImage === getImageUrl(img.image_path) ? 'border-[#ff3f6c]' : 'border-transparent hover:border-gray-300'}`}
                                    >
                                        <img src={getImageUrl(img.image_path)} className="w-full h-full object-cover" alt={product.name} />
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* PRODUCT INFORMATION */}
                    <div className="w-full lg:w-2/5 flex flex-col">
                        
                        {/* PRODUCT HEADER */}
                        <div className="mb-6 border-b border-gray-200 pb-6 text-center lg:text-left">
                            <div className="flex flex-col items-center lg:items-start space-y-1">
                                <h1 className="font-outfit text-3xl font-bold text-gray-900">{product.brand?.name || 'STYLEORA'}</h1>
                                <p className="text-lg text-gray-500 font-light">{product.name}</p>
                                
                                {/* Reviews */}
                                <div className="inline-flex items-center text-sm mt-2">
                                    <span className="font-bold text-gray-900 mr-1">4.4</span>
                                    <i className="fa-solid fa-star text-teal-600 text-[10px] mr-2"></i>
                                    <span className="text-gray-500">| 697 Ratings</span>
                                </div>
                            </div>

                            {/* PRICE */}
                            <div className="mt-4 flex flex-col items-center lg:items-start">
                                <div className="flex items-baseline space-x-3">
                                    <span className="text-3xl font-bold text-gray-900">{formatPrice(activePrice)}</span>
                                    {discountPercentage && (
                                        <>
                                            <span className="text-lg text-gray-500">MRP <span className="line-through">{formatPrice(product.price)}</span></span>
                                            <span className="text-xl font-bold text-orange-500">({discountPercentage}% OFF)</span>
                                        </>
                                    )}
                                </div>
                                <p className="text-xs font-bold text-teal-600 mt-1">inclusive of all taxes</p>
                            </div>
                        </div>

                        <div className="max-w-2xl mx-auto lg:mx-0 w-full">
                            
                            {/* BEST OFFERS */}
                            <div className="mb-8 p-4 bg-orange-50 border border-orange-100 rounded-lg">
                                <h4 className="font-bold text-orange-800 mb-4 flex items-center justify-center lg:justify-start text-sm uppercase tracking-wider">
                                    <i className="fa-solid fa-tag mr-2"></i> Best Offers
                                </h4>
                                <div className="space-y-3">
                                    <div className="flex items-start justify-center lg:justify-start text-left">
                                        <i className="fa-solid fa-building-columns text-orange-600 mt-1 mr-3 w-4"></i>
                                        <p className="text-sm text-gray-700"><strong>Bank Offer:</strong> Get 10% instant discount on HDFC Bank Credit Cards, up to ₹1000.</p>
                                    </div>
                                    <div className="flex items-start justify-center lg:justify-start text-left">
                                        <i className="fa-solid fa-mobile-screen text-orange-600 mt-1 mr-3 w-4"></i>
                                        <p className="text-sm text-gray-700"><strong>UPI Offer:</strong> Get ₹50 flat cashback on first UPI transaction.</p>
                                    </div>
                                </div>
                            </div>

                            {/* ADD TO CART FORM */}
                            <form onSubmit={handleAddToCart} className="mb-8">
                                
                                <div className="flex flex-col md:flex-row lg:flex-col justify-center lg:justify-start gap-8 lg:gap-6 mb-10">
                                    
                                    {/* COLOR */}
                                    {colors.length > 0 && (
                                        <div className="flex flex-col items-center lg:items-start">
                                            <h4 className="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Select Color</h4>
                                            <div className="flex items-center space-x-3">
                                                {colors.map(color => (
                                                    <label key={color.id} className="relative cursor-pointer group" title={color.name}>
                                                        <input 
                                                            type="radio" 
                                                            name="color_id" 
                                                            value={color.id}
                                                            checked={selectedColor === color.id.toString()}
                                                            onChange={(e) => { setSelectedColor(e.target.value); setSelectedSize(''); }}
                                                            className="peer sr-only" 
                                                        />
                                                        <div 
                                                            className={`w-10 h-10 rounded-full border border-gray-300 shadow-sm transition-all ${selectedColor === color.id.toString() ? 'ring-2 ring-gray-900 ring-offset-2' : 'hover:scale-110'}`} 
                                                            style={{ backgroundColor: color.hex_code }}
                                                        ></div>
                                                    </label>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {/* SIZE */}
                                    {sizes.length > 0 && (
                                        <div className="flex flex-col items-center lg:items-start">
                                            <div className="flex items-center justify-between w-full mb-4">
                                                <h4 className="text-sm font-bold text-gray-900 uppercase tracking-wider">Select Size</h4>
                                                <button type="button" className="text-xs font-bold text-[#ff3f6c] uppercase">Size Chart</button>
                                            </div>
                                            <div className="flex flex-wrap justify-center lg:justify-start gap-3">
                                                {sizes.map(size => {
                                                    const variantExists = product.variants.some(v => v.color_id?.toString() === selectedColor && v.size_id === size.id);
                                                    return (
                                                        <label key={size.id} className={`relative ${variantExists ? 'cursor-pointer' : 'cursor-not-allowed opacity-40'} group`}>
                                                            <input 
                                                                type="radio" 
                                                                name="size_id" 
                                                                value={size.id}
                                                                disabled={!variantExists}
                                                                checked={selectedSize === size.id.toString()}
                                                                onChange={(e) => setSelectedSize(e.target.value)}
                                                                className="peer sr-only" 
                                                            />
                                                            <div className={`w-12 h-12 rounded-full border flex items-center justify-center text-sm font-bold transition-all ${selectedSize === size.id.toString() ? 'bg-black text-white border-black' : 'border-gray-300 text-gray-700 bg-white hover:border-black'}`}>
                                                                {size.name}
                                                            </div>
                                                        </label>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    )}
                                </div>

                                {/* QUANTITY & BUTTON */}
                                <div className="flex flex-col sm:flex-row items-center gap-4">
                                    <div className="flex items-center border border-gray-300 rounded overflow-hidden">
                                        <button type="button" onClick={() => setQuantity(Math.max(1, quantity - 1))} className="px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold transition-colors">
                                            <i className="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" name="quantity" value={quantity} readOnly className="w-12 text-center py-3 border-none focus:ring-0 text-sm font-bold bg-white" />
                                        <button type="button" onClick={() => setQuantity(quantity + 1)} className="px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold transition-colors">
                                            <i className="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                    
                                    <button 
                                        type="submit" 
                                        disabled={!inStock}
                                        className={`flex-1 py-4 px-6 text-sm font-bold uppercase tracking-wider rounded flex items-center justify-center shadow-lg transition-all ${inStock ? 'bg-[#ff3f6c] hover:bg-[#ed3a64] text-white hover:shadow-xl hover:-translate-y-1' : 'bg-gray-200 text-gray-400 cursor-not-allowed shadow-none'}`}
                                    >
                                        <i className="fa-solid fa-bag-shopping mr-2"></i> {inStock ? 'ADD TO BAG' : 'OUT OF STOCK'}
                                    </button>
                                </div>
                            </form>

                            {/* DELIVERY */}
                            <div className="mb-10 border-b border-gray-200 pb-10">
                                <h4 className="font-bold text-gray-900 mb-6 flex items-center justify-center lg:justify-start tracking-wider text-base">
                                    DELIVERY OPTIONS <i className="fa-solid fa-truck-fast ml-3 text-gray-600 text-xl"></i>
                                </h4>
                                <div className="relative w-full max-w-md mx-auto lg:mx-0 mb-6 flex items-center">
                                    <input 
                                        type="text" 
                                        value={pincode}
                                        onChange={(e) => setPincode(e.target.value.replace(/\D/g, ''))}
                                        placeholder="Enter pincode" 
                                        className="w-full border-2 border-gray-300 rounded-full px-6 py-3 text-sm focus:outline-none focus:border-gray-500" 
                                        maxLength="6" 
                                    />
                                    <button 
                                        type="button" 
                                        onClick={checkDelivery}
                                        className="absolute right-1 top-1 bottom-1 px-6 bg-gray-900 text-white rounded-full font-bold text-sm hover:bg-pink-600"
                                    >
                                        Check
                                    </button>
                                </div>
                                
                                {deliveryChecked && (
                                    <div className="mb-4 text-sm font-medium">
                                        {deliveryAvailable ? (
                                            <p className="text-green-600"><i className="fa-solid fa-circle-check mr-2"></i> Delivery available by <span className="font-bold">{deliveryDate}</span></p>
                                        ) : (
                                            <p className="text-red-500"><i className="fa-solid fa-circle-xmark mr-2"></i> Unfortunately, we do not deliver to this pincode.</p>
                                        )}
                                    </div>
                                )}
                                
                                {!deliveryChecked && (
                                    <p className="text-sm text-gray-500 mb-6 text-center lg:text-left">Please enter PIN code to check delivery time & Pay on Delivery Availability</p>
                                )}

                                <ul className="text-base text-gray-700 space-y-4 max-w-md mx-auto lg:mx-0">
                                    <li className="flex items-center">
                                        <div className="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                                            <i className="fa-solid fa-truck text-gray-500 text-sm"></i>
                                        </div>
                                        100% Original Products
                                    </li>
                                    <li className="flex items-center">
                                        <div className="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                                            <i className="fa-solid fa-hand-holding-dollar text-gray-500 text-sm"></i>
                                        </div>
                                        Pay on delivery might be available
                                    </li>
                                    <li className="flex items-center">
                                        <div className="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                                            <i className="fa-solid fa-arrow-rotate-left text-gray-500 text-sm"></i>
                                        </div>
                                        Easy 14 days returns and exchanges
                                    </li>
                                </ul>
                            </div>

                            {/* DETAILS */}
                            <div>
                                <h4 className="font-bold text-gray-900 mb-4 flex items-center justify-center lg:justify-start tracking-wider text-base uppercase">
                                    Product Details <i className="fa-solid fa-clipboard-list ml-3 text-gray-600 text-xl"></i>
                                </h4>
                                <div className="text-gray-600 text-sm leading-relaxed mb-6 whitespace-pre-wrap text-center lg:text-left">
                                    {product.description || 'No description available for this product.'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* RELATED PRODUCTS */}
                {relatedProducts?.length > 0 && (
                    <div className="mt-24 border-t border-gray-200 pt-16">
                        <div className="flex justify-between items-end mb-8">
                            <h2 className="text-xl md:text-2xl font-black uppercase tracking-widest text-gray-900 flex items-center gap-4">
                                <span className="w-2 h-8 bg-[#ff3f6c]"></span> SIMILAR PRODUCTS
                            </h2>
                        </div>
                        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
                            {relatedProducts.map(relProduct => (
                                <ProductCard key={relProduct.id} product={relProduct} wishlistProductIds={wishlistProductIds} />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default ProductPage;
