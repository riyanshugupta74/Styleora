import { createContext, useContext, useState, useEffect } from 'react';
import api from '../services/api';
import { useToast } from './ToastContext';

const CartContext = createContext();

export const useCart = () => useContext(CartContext);

export const CartProvider = ({ children }) => {
    const [cartCount, setCartCount] = useState(0);
    const [cart, setCart] = useState({});
    const { showToast } = useToast();

    const fetchCartCount = async () => {
        try {
            const response = await api.get('/api/cart/count');
            setCartCount(response.data.count);
        } catch (error) {
            console.error('Failed to fetch cart count');
        }
    };

    const fetchCart = async () => {
        try {
            const response = await api.get('/api/cart');
            setCart(response.data.cart);
            setCartCount(response.data.cartCount);
        } catch (error) {
            console.error('Failed to fetch cart');
        }
    };

    useEffect(() => {
        fetchCartCount();
    }, []);

    const addToCart = async (productId, variantId = null, colorId = null, sizeId = null, quantity = 1) => {
        try {
            const response = await api.post('/api/cart/add', {
                product_id: productId,
                variant_id: variantId,
                color_id: colorId,
                size_id: sizeId,
                quantity
            });
            if (response.data.success) {
                setCartCount(response.data.cartCount);
                showToast(response.data.message || 'Added to bag!', 'success');
                return true;
            }
        } catch (error) {
            showToast(error.response?.data?.message || 'Error adding to cart', 'error');
            return false;
        }
    };

    const removeFromCart = async (id) => {
        try {
            const response = await api.post('/api/cart/remove', { id });
            if (response.data.success) {
                setCart(response.data.cart);
                setCartCount(response.data.cartCount);
                return true;
            }
        } catch (error) {
            showToast('Error removing item', 'error');
            return false;
        }
    };

    const updateQuantity = async (id, quantity) => {
        try {
            const response = await api.post('/api/cart/update', { id, quantity });
            if (response.data.success) {
                setCart(response.data.cart);
                setCartCount(response.data.cartCount);
                return true;
            }
        } catch (error) {
            showToast(error.response?.data?.message || 'Error updating quantity', 'error');
            return false;
        }
    };

    return (
        <CartContext.Provider value={{ cartCount, cart, fetchCart, fetchCartCount, refreshCart: fetchCart, addToCart, removeFromCart, updateQuantity }}>
            {children}
        </CartContext.Provider>
    );
};
