export const formatPrice = (price) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 2,
    }).format(price).replace('₹', '₹');
};

export const getImageUrl = (path) => {
    if (!path) return '/images/product-placeholder.jpg';
    if (path.startsWith('http')) return path;
    const baseUrl = (typeof window !== 'undefined' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1')
        ? window.location.origin
        : (import.meta.env.VITE_API_URL || 'http://localhost:8000');
    return `${baseUrl}/storage/${path.replace(/^\//, '')}`;
};
