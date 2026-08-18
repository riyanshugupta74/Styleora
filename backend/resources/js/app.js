import Alpine from 'alpinejs';

window.Alpine = Alpine;


// =====================================================
// GLOBAL TOAST SYSTEM
// =====================================================

window.showToast = function (message, type = 'success') {
    window.dispatchEvent(
        new CustomEvent('notify', {
            detail: {
                message: message,
                type: type
            }
        })
    );
};


// =====================================================
// UPDATE CART BADGE
// =====================================================

function updateCartBadge(count) {

    count = Number(count || 0);

    document
        .querySelectorAll('.cart-count-badge')
        .forEach(element => {

            if (count > 0) {

                element.textContent = count;
                element.classList.remove('hidden');
                element.style.display = '';

            } else {

                element.textContent = '';
                element.classList.add('hidden');
                element.style.display = 'none';

            }

        });
}


// =====================================================
// UPDATE WISHLIST BADGE
// =====================================================

function updateWishlistBadge(count) {

    count = Number(count || 0);

    document
        .querySelectorAll('.wishlist-count-badge')
        .forEach(element => {

            if (count > 0) {

                element.textContent = count;

                element.classList.remove('hidden');

                element.style.display = '';

            } else {

                // IMPORTANT
                // Completely clear old count

                element.textContent = '';

                element.classList.add('hidden');

                element.style.display = 'none';

            }

        });
}


// =====================================================
// AJAX CART - ADD PRODUCT
// =====================================================

window.addToCartAjax = async function (
    productId,
    variantId,
    csrf
) {

    try {

        const response = await fetch('/cart/api/add', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },

            body: JSON.stringify({
                product_id: productId,
                variant_id: variantId
            })
        });


        const data = await response.json();


        // -------------------------------------------------
        // ERROR
        // -------------------------------------------------

        if (!response.ok || !data.success) {

            window.showToast(
                data.message || 'Error adding product to cart.',
                'error'
            );

            return;
        }


        // -------------------------------------------------
        // SUCCESS
        // -------------------------------------------------

        window.showToast(
            data.message || 'Added to bag!',
            'success'
        );


        // -------------------------------------------------
        // UPDATE CART BADGE
        // -------------------------------------------------

        updateCartBadge(data.cartCount);

    } catch (error) {

        console.error(
            'Add to cart error:',
            error
        );

        window.showToast(
            'Something went wrong while adding the product.',
            'error'
        );
    }
};


// =====================================================
// AJAX WISHLIST TOGGLE
// =====================================================

window.toggleWishlistAjax = async function (
    productId,
    btnElement,
    csrf
) {

    try {

        const response = await fetch(
            '/wishlist/api/toggle',
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },

                body: JSON.stringify({
                    product_id: productId
                })
            }
        );


        // -------------------------------------------------
        // NOT LOGGED IN
        // -------------------------------------------------

        if (response.status === 401) {

            window.location.href = '/login';

            return;
        }


        const data = await response.json();


        // -------------------------------------------------
        // ERROR
        // -------------------------------------------------

        if (!response.ok || !data.success) {

            window.showToast(
                data.message || 'Unable to update wishlist.',
                'error'
            );

            return;
        }


        // -------------------------------------------------
        // SUCCESS MESSAGE
        // -------------------------------------------------

        window.showToast(
            data.message || 'Wishlist updated.',
            'success'
        );


        // =================================================
        // UPDATE HEART ICON
        // =================================================

        if (btnElement) {

            const icon = btnElement.querySelector('i');

            if (icon) {

                if (data.action === 'added') {

                    icon.classList.remove(
                        'fa-regular'
                    );

                    icon.classList.add(
                        'fa-solid',
                        'text-[#ff3f6c]'
                    );

                } else {

                    icon.classList.remove(
                        'fa-solid',
                        'text-[#ff3f6c]'
                    );

                    icon.classList.add(
                        'fa-regular'
                    );
                }
            }
        }


        // =================================================
        // UPDATE WISHLIST BADGE
        // =================================================

        updateWishlistBadge(
            data.wishlistCount
        );


        // =================================================
        // REMOVE FROM WISHLIST PAGE
        // =================================================

        if (
            data.action === 'removed' &&
            btnElement
        ) {

            const wishlistItem =
                btnElement.closest(
                    '[data-wishlist-item]'
                );

            if (wishlistItem) {

                wishlistItem.remove();
            }
        }


        // =================================================
        // EMPTY WISHLIST MESSAGE
        // =================================================

        if (
            data.action === 'removed' &&
            Number(data.wishlistCount || 0) === 0
        ) {

            const wishlistItems =
                document.querySelector(
                    '[data-wishlist-items]'
                );

            if (wishlistItems) {

                wishlistItems.innerHTML = `
                    <div class="w-full text-center py-16">
                        <div class="text-gray-400 text-5xl mb-4">
                            <i class="fa-regular fa-heart"></i>
                        </div>

                        <h2 class="text-xl font-semibold text-gray-800">
                            Your wishlist is empty
                        </h2>

                        <p class="text-gray-500 mt-2">
                            Save your favorite products here.
                        </p>
                    </div>
                `;
            }
        }

    } catch (error) {

        console.error(
            'Wishlist error:',
            error
        );

        window.showToast(
            'Something went wrong while updating wishlist.',
            'error'
        );
    }
};


// =====================================================
// INITIALIZE BADGES
// =====================================================

function initializeBadges() {

    document
        .querySelectorAll('.wishlist-count-badge')
        .forEach(element => {

            const count =
                Number(
                    element.textContent.trim() || 0
                );

            updateWishlistBadge(count);
        });


    document
        .querySelectorAll('.cart-count-badge')
        .forEach(element => {

            const count =
                Number(
                    element.textContent.trim() || 0
                );

            updateCartBadge(count);
        });
}


// =====================================================
// FADE-IN ANIMATION
// =====================================================

document.addEventListener(
    'DOMContentLoaded',
    () => {

        // -------------------------------------------------
        // INITIALIZE BADGES
        // -------------------------------------------------

        initializeBadges();


        // -------------------------------------------------
        // INTERSECTION OBSERVER
        // -------------------------------------------------

        const observer =
            new IntersectionObserver(
                (entries) => {

                    entries.forEach(entry => {

                        if (
                            entry.isIntersecting
                        ) {

                            entry.target.classList.add(
                                'opacity-100',
                                'translate-y-0'
                            );

                            entry.target.classList.remove(
                                'opacity-0',
                                'translate-y-8'
                            );

                            observer.unobserve(
                                entry.target
                            );
                        }
                    });

                },
                {
                    threshold: 0.1
                }
            );


        // -------------------------------------------------
        // FADE-IN ELEMENTS
        // -------------------------------------------------

        document
            .querySelectorAll('.fade-in-up')
            .forEach(element => {

                element.classList.add(
                    'opacity-0',
                    'translate-y-8',
                    'transition-all',
                    'duration-700',
                    'ease-out'
                );

                observer.observe(element);
            });
    }
);


// =====================================================
// START ALPINE
// =====================================================

Alpine.start();