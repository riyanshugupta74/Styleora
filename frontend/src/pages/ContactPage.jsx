import { useState } from 'react';
import { useToast } from '../context/ToastContext';
import api from '../services/api';

const ContactPage = () => {
    const { showToast } = useToast();
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        subject: '',
        message: ''
    });
    const [loading, setLoading] = useState(false);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        // Assuming there is a generic contact endpoint or just mocking it
        try {
            await new Promise(resolve => setTimeout(resolve, 1000));
            showToast('Your message has been sent successfully! We will get back to you soon.', 'success');
            setFormData({ name: '', email: '', subject: '', message: '' });
        } catch (error) {
            showToast('Failed to send message. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-white min-h-screen pt-8 pb-20">
            <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                
                <div className="text-center mb-16 mt-8">
                    <h1 className="font-outfit text-4xl md:text-5xl font-black text-gray-900 mb-6 uppercase tracking-wider">Contact Us</h1>
                    <p className="text-gray-500 max-w-2xl mx-auto text-lg">We'd love to hear from you. Whether you have a question about products, pricing, or anything else, our team is ready to answer all your questions.</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-12 max-w-5xl mx-auto">
                    
                    {/* Contact Information */}
                    <div className="lg:col-span-1 space-y-8">
                        <div>
                            <h3 className="font-outfit text-2xl font-bold text-gray-900 mb-6">Get in Touch</h3>
                            <div className="space-y-6">
                                <div className="flex items-start">
                                    <div className="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center shrink-0 mr-4">
                                        <i className="fa-solid fa-location-dot text-xl text-[#ff3f6c]"></i>
                                    </div>
                                    <div>
                                        <h4 className="font-bold text-gray-900 text-lg mb-1">Head Office</h4>
                                        <p className="text-gray-600">Styleora Fashion House<br/>123 Fashion Avenue<br/>Mumbai, MH 400001, India</p>
                                    </div>
                                </div>
                                <div className="flex items-start">
                                    <div className="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center shrink-0 mr-4">
                                        <i className="fa-solid fa-envelope text-xl text-[#ff3f6c]"></i>
                                    </div>
                                    <div>
                                        <h4 className="font-bold text-gray-900 text-lg mb-1">Email Us</h4>
                                        <p className="text-gray-600">support@styleora.com<br/>careers@styleora.com</p>
                                    </div>
                                </div>
                                <div className="flex items-start">
                                    <div className="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center shrink-0 mr-4">
                                        <i className="fa-solid fa-phone text-xl text-[#ff3f6c]"></i>
                                    </div>
                                    <div>
                                        <h4 className="font-bold text-gray-900 text-lg mb-1">Call Us</h4>
                                        <p className="text-gray-600">+91 1800 123 4567<br/>Mon-Sat: 9AM to 7PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 className="font-outfit text-xl font-bold text-gray-900 mb-4">Follow Us</h3>
                            <div className="flex gap-4">
                                <a href="#" className="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-[#ff3f6c] hover:text-white transition-colors">
                                    <i className="fa-brands fa-facebook-f"></i>
                                </a>
                                <a href="#" className="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-[#ff3f6c] hover:text-white transition-colors">
                                    <i className="fa-brands fa-twitter"></i>
                                </a>
                                <a href="#" className="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-[#ff3f6c] hover:text-white transition-colors">
                                    <i className="fa-brands fa-instagram"></i>
                                </a>
                                <a href="#" className="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-[#ff3f6c] hover:text-white transition-colors">
                                    <i className="fa-brands fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {/* Contact Form */}
                    <div className="lg:col-span-2">
                        <div className="bg-gray-50 p-8 md:p-10 rounded-2xl border border-gray-100">
                            <h3 className="font-outfit text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Your Name</label>
                                        <input 
                                            type="text" 
                                            name="name" 
                                            value={formData.name}
                                            onChange={handleChange}
                                            required 
                                            className="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-black focus:border-black transition-colors" 
                                            placeholder="John Doe"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Your Email</label>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            value={formData.email}
                                            onChange={handleChange}
                                            required 
                                            className="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-black focus:border-black transition-colors" 
                                            placeholder="john@example.com"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Subject</label>
                                    <input 
                                        type="text" 
                                        name="subject" 
                                        value={formData.subject}
                                        onChange={handleChange}
                                        required 
                                        className="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-black focus:border-black transition-colors" 
                                        placeholder="How can we help?"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Message</label>
                                    <textarea 
                                        name="message" 
                                        value={formData.message}
                                        onChange={handleChange}
                                        required 
                                        rows="5" 
                                        className="w-full border-gray-300 rounded-lg px-4 py-3 focus:ring-black focus:border-black transition-colors" 
                                        placeholder="Write your message here..."
                                    ></textarea>
                                </div>
                                <button 
                                    type="submit" 
                                    disabled={loading}
                                    className={`w-full bg-[#ff3f6c] text-white py-4 rounded-lg font-bold tracking-wider uppercase transition-colors shadow-md ${loading ? 'opacity-75 cursor-not-allowed' : 'hover:bg-[#ed3a64]'}`}
                                >
                                    {loading ? 'Sending...' : 'Send Message'}
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    );
};

export default ContactPage;
