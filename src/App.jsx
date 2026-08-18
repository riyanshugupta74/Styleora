import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import AppLayout from './layouts/AppLayout';
import GuestLayout from './layouts/GuestLayout';
import ProtectedRoute from './components/ProtectedRoute';

// Pages
import HomePage from './pages/HomePage';
import CategoryPage from './pages/CategoryPage';
import ProductPage from './pages/ProductPage';
import SearchPage from './pages/SearchPage';
import CartPage from './pages/CartPage';
import WishlistPage from './pages/WishlistPage';
import CheckoutAddressPage from './pages/CheckoutAddressPage';
import CheckoutPaymentPage from './pages/CheckoutPaymentPage';
import OrdersPage from './pages/OrdersPage';
import OrderDetailsPage from './pages/OrderDetailsPage';
import TrackOrderPage from './pages/TrackOrderPage';
import ContactPage from './pages/ContactPage';
import ProfilePage from './pages/ProfilePage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';

function App() {
  return (
    <Router>
      <Routes>
        {/* Auth Routes */}
        <Route path="/login" element={<GuestLayout><LoginPage /></GuestLayout>} />
        <Route path="/register" element={<GuestLayout><RegisterPage /></GuestLayout>} />

        {/* Main App Routes */}
        <Route path="/" element={<AppLayout><HomePage /></AppLayout>} />
        
        {/* Shop Routes */}
        <Route path="/men" element={<AppLayout><CategoryPage type="men" /></AppLayout>} />
        <Route path="/women" element={<AppLayout><CategoryPage type="women" /></AppLayout>} />
        <Route path="/sale" element={<AppLayout><CategoryPage type="sale" /></AppLayout>} />
        <Route path="/new-arrivals" element={<AppLayout><CategoryPage type="new-arrivals" /></AppLayout>} />
        <Route path="/trending" element={<AppLayout><CategoryPage type="trending" /></AppLayout>} />
        
        <Route path="/product/:slug" element={<AppLayout><ProductPage /></AppLayout>} />
        <Route path="/search" element={<AppLayout><SearchPage /></AppLayout>} />
        
        {/* Cart & Checkout */}
        <Route path="/cart" element={<AppLayout><CartPage /></AppLayout>} />
        <Route path="/checkout/address" element={<ProtectedRoute><AppLayout><CheckoutAddressPage /></AppLayout></ProtectedRoute>} />
        <Route path="/checkout/payment" element={<ProtectedRoute><AppLayout><CheckoutPaymentPage /></AppLayout></ProtectedRoute>} />
        
        {/* Protected User Routes */}
        <Route path="/wishlist" element={<ProtectedRoute><AppLayout><WishlistPage /></AppLayout></ProtectedRoute>} />
        <Route path="/orders" element={<ProtectedRoute><AppLayout><OrdersPage /></AppLayout></ProtectedRoute>} />
        <Route path="/orders/:id" element={<ProtectedRoute><AppLayout><OrderDetailsPage /></AppLayout></ProtectedRoute>} />
        <Route path="/account/profile" element={<ProtectedRoute><AppLayout><ProfilePage /></AppLayout></ProtectedRoute>} />
        
        {/* Other */}
        <Route path="/track-order" element={<AppLayout><TrackOrderPage /></AppLayout>} />
        <Route path="/contact" element={<AppLayout><ContactPage /></AppLayout>} />
        
      </Routes>
    </Router>
  );
}

export default App;
