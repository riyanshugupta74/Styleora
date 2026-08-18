import { Link, useLocation } from 'react-router-dom';

const CheckoutProgress = () => {
    const location = useLocation();
    const currentStep = location.pathname.includes('/address') ? 1 : 
                        location.pathname.includes('/payment') ? 2 : 0;

    return (
        <div className="mb-12">
            <div className="flex items-center justify-center">
                <div className="flex items-center w-full max-w-3xl">
                    
                    {/* Cart Step */}
                    <div className="relative flex flex-col items-center flex-1">
                        <Link to="/cart" className="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm shadow-md z-10 hover:scale-110 transition-transform">
                            <i className="fa-solid fa-bag-shopping"></i>
                        </Link>
                        <span className="text-xs font-bold mt-2 uppercase tracking-wider text-black">BAG</span>
                    </div>

                    <div className={`flex-auto border-t-2 transition-colors duration-500 ${currentStep >= 1 ? 'border-black' : 'border-gray-200'}`}></div>

                    {/* Address Step */}
                    <div className="relative flex flex-col items-center flex-1">
                        <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md z-10 transition-colors duration-500 ${currentStep >= 1 ? 'bg-black text-white' : 'bg-white border-2 border-gray-200 text-gray-400'}`}>
                            <i className="fa-solid fa-location-dot"></i>
                        </div>
                        <span className={`text-xs font-bold mt-2 uppercase tracking-wider transition-colors duration-500 ${currentStep >= 1 ? 'text-black' : 'text-gray-400'}`}>ADDRESS</span>
                    </div>

                    <div className={`flex-auto border-t-2 transition-colors duration-500 ${currentStep >= 2 ? 'border-black' : 'border-gray-200'}`}></div>

                    {/* Payment Step */}
                    <div className="relative flex flex-col items-center flex-1">
                        <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md z-10 transition-colors duration-500 ${currentStep >= 2 ? 'bg-black text-white' : 'bg-white border-2 border-gray-200 text-gray-400'}`}>
                            <i className="fa-solid fa-credit-card"></i>
                        </div>
                        <span className={`text-xs font-bold mt-2 uppercase tracking-wider transition-colors duration-500 ${currentStep >= 2 ? 'text-black' : 'text-gray-400'}`}>PAYMENT</span>
                    </div>

                </div>
            </div>
        </div>
    );
};

export default CheckoutProgress;
