const fs = require('fs');
const path = require('path');

const pages = [
  'HomePage', 'CategoryPage', 'ProductPage', 'SearchPage', 'CartPage',
  'WishlistPage', 'CheckoutAddressPage', 'CheckoutPaymentPage', 'OrdersPage',
  'OrderDetailsPage', 'TrackOrderPage', 'ContactPage', 'ProfilePage',
  'LoginPage', 'RegisterPage'
];

const dir = path.join(__dirname, 'frontend', 'src', 'pages');

if (!fs.existsSync(dir)) {
  fs.mkdirSync(dir, { recursive: true });
}

pages.forEach(page => {
  const content = `const ${page} = () => {
    return (
        <div className="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 className="text-3xl font-bold">${page}</h1>
            <p className="mt-4 text-gray-600">Coming soon...</p>
        </div>
    );
};

export default ${page};
`;
  fs.writeFileSync(path.join(dir, `${page}.jsx`), content);
});

console.log('Pages created!');
