import { useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { useToast } from '../context/ToastContext';
import api from '../services/api';

const ProfilePage = () => {
    const { user, checkAuth } = useAuth();
    const { showToast } = useToast();
    
    const [profileData, setProfileData] = useState({
        name: user?.name || '',
        email: user?.email || '',
    });
    
    const [passwordData, setPasswordData] = useState({
        current_password: '',
        password: '',
        password_confirmation: ''
    });

    const [loadingProfile, setLoadingProfile] = useState(false);
    const [loadingPassword, setLoadingPassword] = useState(false);
    
    const [profileErrors, setProfileErrors] = useState({});
    const [passwordErrors, setPasswordErrors] = useState({});

    const handleProfileChange = (e) => {
        setProfileData({ ...profileData, [e.target.name]: e.target.value });
        if (profileErrors[e.target.name]) {
            setProfileErrors({ ...profileErrors, [e.target.name]: null });
        }
    };

    const handlePasswordChange = (e) => {
        setPasswordData({ ...passwordData, [e.target.name]: e.target.value });
        if (passwordErrors[e.target.name]) {
            setPasswordErrors({ ...passwordErrors, [e.target.name]: null });
        }
    };

    const handleProfileSubmit = async (e) => {
        e.preventDefault();
        setLoadingProfile(true);
        setProfileErrors({});
        
        try {
            await api.put('/account/profile', profileData);
            showToast('Profile updated successfully', 'success');
            checkAuth(); // Refresh user data
        } catch (error) {
            if (error.response?.status === 422) {
                setProfileErrors(error.response.data.errors);
            } else {
                showToast('Failed to update profile', 'error');
            }
        } finally {
            setLoadingProfile(false);
        }
    };

    const handlePasswordSubmit = async (e) => {
        e.preventDefault();
        setLoadingPassword(true);
        setPasswordErrors({});
        
        try {
            await api.put('/password', passwordData);
            showToast('Password updated successfully', 'success');
            setPasswordData({ current_password: '', password: '', password_confirmation: '' });
        } catch (error) {
            if (error.response?.status === 422) {
                setPasswordErrors(error.response.data.errors);
            } else {
                showToast('Failed to update password', 'error');
            }
        } finally {
            setLoadingPassword(false);
        }
    };

    if (!user) {
        return (
            <div className="min-h-screen flex items-center justify-center pb-32">
                <i className="fa-solid fa-spinner fa-spin text-4xl text-gray-300"></i>
            </div>
        );
    }

    return (
        <div className="bg-gray-50 min-h-screen pt-8 pb-20">
            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="mb-8">
                    <h1 className="font-outfit text-3xl font-bold text-gray-900">My Profile</h1>
                    <p className="text-gray-500 mt-1">Manage your account settings and preferences.</p>
                </div>

                <div className="space-y-8">
                    
                    {/* Profile Information */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <div className="border-b border-gray-100 pb-6 mb-6">
                            <h2 className="text-xl font-bold text-gray-900">Profile Information</h2>
                            <p className="text-sm text-gray-500 mt-1">Update your account's profile information and email address.</p>
                        </div>
                        
                        <form onSubmit={handleProfileSubmit} className="max-w-xl space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    value={profileData.name} 
                                    onChange={handleProfileChange} 
                                    className="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors"
                                    required 
                                />
                                {profileErrors.name && <p className="text-red-500 text-xs mt-1">{profileErrors.name[0]}</p>}
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value={profileData.email} 
                                    onChange={handleProfileChange} 
                                    className="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors"
                                    required 
                                />
                                {profileErrors.email && <p className="text-red-500 text-xs mt-1">{profileErrors.email[0]}</p>}
                            </div>
                            
                            <div className="pt-2">
                                <button 
                                    type="submit" 
                                    disabled={loadingProfile}
                                    className={`bg-black text-white px-6 py-2.5 rounded-lg font-bold uppercase tracking-wider text-sm transition-colors ${loadingProfile ? 'opacity-75 cursor-not-allowed' : 'hover:bg-gray-800'}`}
                                >
                                    {loadingProfile ? 'Saving...' : 'Save Changes'}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Update Password */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <div className="border-b border-gray-100 pb-6 mb-6">
                            <h2 className="text-xl font-bold text-gray-900">Update Password</h2>
                            <p className="text-sm text-gray-500 mt-1">Ensure your account is using a long, random password to stay secure.</p>
                        </div>
                        
                        <form onSubmit={handlePasswordSubmit} className="max-w-xl space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <input 
                                    type="password" 
                                    name="current_password" 
                                    value={passwordData.current_password} 
                                    onChange={handlePasswordChange} 
                                    className="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors"
                                    required 
                                />
                                {passwordErrors.current_password && <p className="text-red-500 text-xs mt-1">{passwordErrors.current_password[0]}</p>}
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    value={passwordData.password} 
                                    onChange={handlePasswordChange} 
                                    className="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors"
                                    required 
                                />
                                {passwordErrors.password && <p className="text-red-500 text-xs mt-1">{passwordErrors.password[0]}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    value={passwordData.password_confirmation} 
                                    onChange={handlePasswordChange} 
                                    className="w-full border-gray-300 rounded-lg px-4 py-2.5 focus:ring-black focus:border-black transition-colors"
                                    required 
                                />
                            </div>
                            
                            <div className="pt-2">
                                <button 
                                    type="submit" 
                                    disabled={loadingPassword}
                                    className={`bg-black text-white px-6 py-2.5 rounded-lg font-bold uppercase tracking-wider text-sm transition-colors ${loadingPassword ? 'opacity-75 cursor-not-allowed' : 'hover:bg-gray-800'}`}
                                >
                                    {loadingPassword ? 'Saving...' : 'Update Password'}
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    );
};

export default ProfilePage;
