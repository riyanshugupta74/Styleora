import Header from '../components/Header';
import Footer from '../components/Footer';

const AppLayout = ({ children }) => {
    return (
        <div className="flex flex-col min-h-screen pt-24 sm:pt-28">
            <Header />
            <main className="flex-grow">
                {children}
            </main>
            <Footer />
        </div>
    );
};

export default AppLayout;
