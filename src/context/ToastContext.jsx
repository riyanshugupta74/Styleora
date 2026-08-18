import { createContext, useContext, useState, useCallback } from 'react';

const ToastContext = createContext();

export const useToast = () => useContext(ToastContext);

export const ToastProvider = ({ children }) => {
    const [toasts, setToasts] = useState([]);

    const showToast = useCallback((message, type = 'success') => {
        const id = Date.now() + Math.random();
        setToasts((prev) => [...prev, { id, message, type }]);

        setTimeout(() => {
            setToasts((prev) => prev.filter((toast) => toast.id !== id));
        }, 4000);
    }, []);

    const removeToast = useCallback((id) => {
        setToasts((prev) => prev.filter((toast) => toast.id !== id));
    }, []);

    return (
        <ToastContext.Provider value={{ toasts, showToast, removeToast }}>
            {children}
            {/* Toast Container */}
            <div className="fixed bottom-4 right-4 z-[60] flex flex-col gap-2">
                {toasts.map((toast) => (
                    <div
                        key={toast.id}
                        className={`flex items-center p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${
                            toast.type === 'success' ? 'bg-[#ff3f6c]' : 'bg-red-500'
                        }`}
                        role="alert"
                    >
                        <div className="mr-3 text-xl">
                            {toast.type === 'success' ? (
                                <i className="fa-solid fa-circle-check"></i>
                            ) : (
                                <i className="fa-solid fa-circle-exclamation"></i>
                            )}
                        </div>
                        <div className="text-sm font-medium">{toast.message}</div>
                        <button
                            onClick={() => removeToast(toast.id)}
                            className="ml-4 text-white/80 hover:text-white"
                        >
                            <i className="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                ))}
            </div>
        </ToastContext.Provider>
    );
};
