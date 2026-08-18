import { Link } from 'react-router-dom';

const GuestLayout = ({ children }) => {
    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
            <div>
                <Link to="/" className="text-3xl font-black font-outfit uppercase tracking-widest text-black">
                    STYLEORA
                </Link>
            </div>

            <div className="w-full sm:max-w-md mt-8 px-6 py-8 bg-white shadow-sm overflow-hidden sm:rounded-2xl border border-gray-100">
                {children}
            </div>
        </div>
    );
};

export default GuestLayout;
