```php
<x-app-layout>

    @section('title', 'STYLEORA | ' . $product->name)

    @php

        /*
        |--------------------------------------------------------------------------
        | PRODUCT IMAGES
        |--------------------------------------------------------------------------
        */

        $primaryImage = $product->images->first();

        if ($primaryImage) {

            if (str_starts_with($primaryImage->image_path, 'http')) {
                $defaultImage = $primaryImage->image_path;
            } elseif (file_exists(public_path('storage/' . $primaryImage->image_path))) {
                $defaultImage = asset('storage/' . $primaryImage->image_path);
            } else {
                $defaultImage = asset('images/product-placeholder.jpg');
            }

        } else {
            $defaultImage = asset('images/product-placeholder.jpg');
        }


        /*
        |--------------------------------------------------------------------------
        | COLORS
        |--------------------------------------------------------------------------
        */

        $colors = $product->variants
            ->filter(fn($variant) => $variant->color)
            ->unique('color_id')
            ->values();

        $singleColor = $colors->first();


        /*
        |--------------------------------------------------------------------------
        | SIZES
        |--------------------------------------------------------------------------
        */

        $sizes = $product->variants
            ->filter(fn($variant) => $variant->size)
            ->unique('size_id')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | SHOE DETECTION
        |--------------------------------------------------------------------------
        |
        | We check:
        | 1. Category name
        | 2. Category slug
        | 3. Product name
        |
        */

        $categoryName = strtolower(
            trim($product->category->name ?? '')
        );

        $categorySlug = strtolower(
            trim($product->category->slug ?? '')
        );

        $productName = strtolower(
            trim($product->name ?? '')
        );


        $shoeKeywords = [
            'shoe',
            'shoes',
            'footwear',
            'sneaker',
            'sneakers',
            'running',
            'sports shoe',
            'sports shoes',
            'casual shoe',
            'casual shoes',
            'formal shoe',
            'formal shoes',
            'loafer',
            'loafers',
            'boot',
            'boots',
            'slipper',
            'slippers',
            'sandal',
            'sandals',
            'heels',
            'heel',
            'moccasin',
            'moccasins',
            'jogger',
            'joggers'
        ];


        $isShoe = false;


        foreach ($shoeKeywords as $keyword) {

            if (
                str_contains($categoryName, $keyword) ||
                str_contains($categorySlug, $keyword) ||
                str_contains($productName, $keyword)
            ) {

                $isShoe = true;

                break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SHOE SIZE MAP
        |--------------------------------------------------------------------------
        |
        | Database:
        |
        | S   -> 6
        | M   -> 7
        | L   -> 8
        | XL  -> 9
        | XXL -> 10
        |
        */

        $shoeSizeMap = [
            'S'   => '6',
            'M'   => '7',
            'L'   => '8',
            'XL'  => '9',
            'XXL' => '10',
        ];

    @endphp


    <div class="bg-white min-h-screen pt-8 pb-20">

        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">


            <!-- ===================================================== -->
            <!-- BREADCRUMBS -->
            <!-- ===================================================== -->

            <nav
                class="flex text-sm text-gray-500 mb-8"
                aria-label="Breadcrumb"
            >

                <ol class="inline-flex items-center space-x-1 md:space-x-3">

                    <li class="inline-flex items-center">

                        <a
                            href="{{ route('home') }}"
                            class="hover:text-black"
                        >
                            Home
                        </a>

                    </li>


                    <li>

                        <div class="flex items-center">

                            <i class="fa-solid fa-chevron-right text-xs mx-2"></i>

                            <a
                                href="{{ url($product->category->slug) }}"
                                class="hover:text-black"
                            >
                                {{ $product->category->name }}
                            </a>

                        </div>

                    </li>


                    <li aria-current="page">

                        <div class="flex items-center">

                            <i class="fa-solid fa-chevron-right text-xs mx-2"></i>

                            <span class="text-gray-900 font-medium">
                                {{ $product->name }}
                            </span>

                        </div>

                    </li>

                </ol>

            </nav>



            <!-- ===================================================== -->
            <!-- PRODUCT CONTAINER -->
            <!-- ===================================================== -->

            <div

                x-data="{

                    variants: {{ $product->variants->toJson() }},

                    selectedColor: null,

                    selectedSize: null,

                    quantity: 1,

                    galleryImage: @js($defaultImage),


                    get activeVariant() {

                        return this.variants.find(

                            v =>

                                v.color_id == this.selectedColor &&

                                v.size_id == this.selectedSize

                        );

                    },


                    get activePrice() {

                        return this.activeVariant

                            ? this.activeVariant.price

                            : {{ $product->discount_price ?? $product->price }};

                    },


                    get hasVariants() {
                        return this.variants.length > 0;
                    },

                    get variantSelected() {
                        return this.selectedColor && this.selectedSize;
                    },

                    get inStock() {

                        if (!this.hasVariants) {
                            return {{ ($product->stock ?? 999) }} > 0;
                        }

                        if (!this.variantSelected) {
                            return true;
                        }

                        return this.activeVariant
                            ? this.activeVariant.stock > 0
                            : false;

                    },


                    get availableStock() {

                        if (!this.hasVariants) {
                            return {{ $product->stock ?? 999 }};
                        }

                        if (!this.variantSelected) {
                            return 999;
                        }

                        return this.activeVariant
                            ? this.activeVariant.stock
                            : 0;

                    }

                }"


                x-init="

                    @if($product->variants->count() > 0)

                        selectedColor = '{{ $product->variants->first()->color_id }}';

                    @endif

                "


                class="flex flex-col lg:flex-row items-center lg:items-start
                       max-w-4xl lg:max-w-6xl mx-auto gap-12"
            >



                <!-- ===================================================== -->
                <!-- PRODUCT IMAGE -->
                <!-- ===================================================== -->

                <div
                    class="w-full max-w-sm lg:max-w-none
                           lg:w-1/2 mx-auto lg:mx-0"
                >


                    <div
                        class="bg-gray-50 rounded overflow-hidden relative
                               h-[350px] lg:h-auto lg:aspect-[3/4]
                               group cursor-pointer w-full mx-auto
                               shadow-sm mb-4"
                    >

                        <div
                            class="w-full h-full
                                   transition-transform duration-700
                                   group-hover:scale-105"
                        >

                            <img
                                :src="galleryImage"
                                src="{{ $defaultImage }}"
                                class="w-full h-full
                                       object-contain lg:object-cover"
                                loading="lazy"
                                alt="{{ $product->name }}"
                            >

                        </div>


                        @if($product->is_new_arrival)

                            <span
                                class="absolute top-4 left-4
                                       bg-black text-white text-xs
                                       font-bold px-3 py-1.5
                                       uppercase tracking-wider
                                       z-10 shadow-sm"
                            >
                                NEW
                            </span>

                        @endif

                    </div>



                    <!-- ================================================= -->
                    <!-- IMAGE GALLERY -->
                    <!-- ================================================= -->

                    @if($product->images->count() > 1)

                        <div
                            class="flex gap-4 overflow-x-auto
                                   pb-2 scrollbar-hide"
                        >

                            @foreach($product->images as $img)

                                @php

                                    if (
                                        str_starts_with(
                                            $img->image_path,
                                            'http'
                                        )
                                    ) {

                                        $galleryImage = $img->image_path;

                                    } elseif (
                                        file_exists(
                                            public_path(
                                                'storage/' .
                                                $img->image_path
                                            )
                                        )
                                    ) {

                                        $galleryImage =
                                            asset(
                                                'storage/' .
                                                $img->image_path
                                            );

                                    } else {

                                        $galleryImage =
                                            asset(
                                                'images/product-placeholder.jpg'
                                            );
                                    }

                                @endphp


                                <button
                                    type="button"

                                    @click="galleryImage = @js($galleryImage)"

                                    class="w-20 h-24 shrink-0
                                           rounded overflow-hidden
                                           border-2 transition-all"

                                    :class="
                                        galleryImage === @js($galleryImage)
                                            ? 'border-[#ff3f6c]'
                                            : 'border-transparent hover:border-gray-300'
                                    "
                                >

                                    <img
                                        src="{{ $galleryImage }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                        alt="{{ $product->name }}"
                                    >

                                </button>

                            @endforeach

                        </div>

                    @endif

                </div>



                <!-- ===================================================== -->
                <!-- PRODUCT INFORMATION -->
                <!-- ===================================================== -->

                <div class="w-full lg:w-2/5 flex flex-col">


                    <!-- ================================================= -->
                    <!-- PRODUCT HEADER -->
                    <!-- ================================================= -->

                    <div
                        class="mb-6 border-b border-gray-200
                               pb-6 text-center lg:text-left"
                    >

                        <div
                            class="flex flex-col items-center
                                   lg:items-start space-y-1"
                        >

                            <h1
                                class="font-outfit text-3xl
                                       font-bold text-gray-900"
                            >
                                {{ $product->brand
                                    ? $product->brand->name
                                    : 'STYLEORA' }}
                            </h1>


                            <p class="text-lg text-gray-500 font-light">
                                {{ $product->name }}
                            </p>


                            <!-- Reviews -->

                            <div class="inline-flex items-center text-sm mt-2">

                                <span
                                    class="font-bold text-gray-900 mr-1"
                                >
                                    4.4
                                </span>


                                <i
                                    class="fa-solid fa-star
                                           text-teal-600
                                           text-[10px] mr-2"
                                ></i>


                                <span class="text-gray-500">
                                    | 697 Ratings
                                </span>

                            </div>

                        </div>



                        <!-- PRICE -->

                        <div
                            class="mt-4 flex flex-col
                                   items-center lg:items-start"
                        >

                            <div class="flex items-baseline space-x-3">

                                <span
                                    class="text-3xl font-bold text-gray-900"

                                    x-text="
                                        '₹' +

                                        Number(activePrice).toLocaleString(
                                            'en-IN',
                                            {
                                                minimumFractionDigits: 2
                                            }
                                        )
                                    "
                                ></span>


                                @if($product->discount_price)

                                    <span class="text-lg text-gray-500">

                                        MRP

                                        <span class="line-through">
                                            ₹{{ number_format($product->price, 2) }}
                                        </span>

                                    </span>


                                    @php

                                        $discount = round(

                                            (
                                                (
                                                    $product->price -
                                                    $product->discount_price
                                                )
                                                /
                                                $product->price
                                            ) * 100

                                        );

                                    @endphp


                                    <span
                                        class="text-xl font-bold
                                               text-orange-500"
                                    >
                                        ({{ $discount }}% OFF)
                                    </span>

                                @endif

                            </div>


                            <p
                                class="text-xs font-bold
                                       text-teal-600 mt-1"
                            >
                                inclusive of all taxes
                            </p>

                        </div>

                    </div>



                    <!-- ================================================= -->
                    <!-- SUCCESS -->
                    <!-- ================================================= -->

                    @if(session('success'))

                        <div
                            class="bg-green-100
                                   border border-green-400
                                   text-green-700
                                   px-4 py-3 rounded
                                   relative mb-6
                                   text-center lg:text-left"
                            role="alert"
                        >

                            {{ session('success') }}

                        </div>

                    @endif



                    <div
                        class="max-w-2xl
                               mx-auto lg:mx-0
                               w-full"
                    >



                        <!-- ================================================= -->
                        <!-- BEST OFFERS -->
                        <!-- ================================================= -->

                        <div
                            class="mb-8 p-4 bg-orange-50
                                   border border-orange-100
                                   rounded-lg"
                        >

                            <h4
                                class="font-bold text-orange-800
                                       mb-4 flex items-center
                                       justify-center
                                       lg:justify-start
                                       text-sm uppercase
                                       tracking-wider"
                            >

                                <i class="fa-solid fa-tag mr-2"></i>

                                Best Offers

                            </h4>


                            <div class="space-y-3">

                                <div
                                    class="flex items-start
                                           justify-center
                                           lg:justify-start
                                           text-left"
                                >

                                    <i
                                        class="fa-solid
                                               fa-building-columns
                                               text-orange-600
                                               mt-1 mr-3 w-4"
                                    ></i>


                                    <p
                                        class="text-sm text-gray-700"
                                    >

                                        <strong>
                                            Bank Offer:
                                        </strong>

                                        Get 10% instant discount on HDFC Bank
                                        Credit Cards, up to ₹1000.

                                    </p>

                                </div>


                                <div
                                    class="flex items-start
                                           justify-center
                                           lg:justify-start
                                           text-left"
                                >

                                    <i
                                        class="fa-solid
                                               fa-mobile-screen
                                               text-orange-600
                                               mt-1 mr-3 w-4"
                                    ></i>


                                    <p
                                        class="text-sm text-gray-700"
                                    >

                                        <strong>
                                            UPI Offer:
                                        </strong>

                                        Get ₹50 flat cashback on first
                                        UPI transaction.

                                    </p>

                                </div>

                            </div>

                        </div>



                        <!-- ================================================= -->
                        <!-- ADD TO CART -->
                        <!-- ================================================= -->

                        <form

                            @submit.prevent="
                                window.addToCartAjax(
                                    {{ $product->id }},
                                    activeVariant
                                        ? activeVariant.id
                                        : null,
                                    '{{ csrf_token() }}'
                                )
                            "

                            class="mb-8"
                        >

                            @csrf


                            <input
                                type="hidden"
                                name="product_id"
                                value="{{ $product->id }}"
                            >



                            <div
                                class="flex flex-col
                                       md:flex-row
                                       lg:flex-col
                                       justify-center
                                       lg:justify-start
                                       gap-8 lg:gap-6
                                       mb-10"
                            >



                                <!-- ================================================= -->
                                <!-- COLOR -->
                                <!-- ================================================= -->

                                @if($singleColor)

                                    <div
                                        class="flex flex-col
                                               items-center
                                               lg:items-start"
                                    >

                                        <h4
                                            class="text-sm font-bold
                                                   text-gray-900 mb-4
                                                   uppercase
                                                   tracking-wider"
                                        >
                                            Select Color
                                        </h4>


                                        <label
                                            class="cursor-pointer
                                                   relative group"
                                        >

                                            <input
                                                x-model="selectedColor"
                                                type="radio"
                                                name="color_id"
                                                value="{{ $singleColor->color_id }}"
                                                class="peer sr-only"
                                                required
                                            >


                                            <div
                                                class="w-14 h-14
                                                       rounded-full
                                                       border-2
                                                       border-[#ff3f6c]
                                                       flex items-center
                                                       justify-center
                                                       transition-all
                                                       shadow-sm
                                                       group-hover:scale-105"
                                            >

                                                <div
                                                    class="w-11 h-11
                                                           rounded-full
                                                           shadow-inner"

                                                    style="
                                                        background-color:
                                                        {{ $singleColor->color->hex_code }}
                                                    "
                                                ></div>

                                            </div>

                                        </label>


                                        <p
                                            class="text-xs text-gray-500
                                                   mt-2 font-medium"
                                        >
                                            {{ $singleColor->color->name }}
                                        </p>

                                    </div>

                                @endif



                                <!-- ================================================= -->
                                <!-- SIZE -->
                                <!-- ================================================= -->

                                @if($sizes->count() > 0)

                                    <div
                                        class="flex flex-col
                                               items-center
                                               lg:items-start"
                                    >


                                        <div
                                            class="flex justify-between
                                                   items-center
                                                   w-full max-w-sm
                                                   mb-4"
                                        >

                                            <h4
                                                class="text-sm font-bold
                                                       text-gray-900
                                                       uppercase
                                                       tracking-wider"
                                            >
                                                Select Size
                                            </h4>


                                            <button
                                                type="button"
                                                class="text-sm font-bold
                                                       text-[#ff3f6c]
                                                       uppercase
                                                       hover:underline
                                                       lg:ml-6"
                                            >

                                                Size Chart

                                                <i
                                                    class="fa-solid
                                                           fa-chevron-right
                                                           text-xs ml-1"
                                                ></i>

                                            </button>

                                        </div>



                                        <div
                                            class="flex flex-wrap
                                                   justify-center
                                                   lg:justify-start
                                                   gap-3"
                                        >

                                            @foreach($sizes as $variant)

                                                @php

                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | ORIGINAL SIZE
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    $originalSize = strtoupper(
                                                        trim(
                                                            $variant->size->name
                                                        )
                                                    );


                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | DISPLAY SIZE
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    if ($isShoe) {

                                                        /*
                                                        | Hide XS from shoes
                                                        */

                                                        if (
                                                            $originalSize === 'XS'
                                                        ) {
                                                            continue;
                                                        }


                                                        /*
                                                        | Convert shoe sizes
                                                        */

                                                        $displaySize =
                                                            $shoeSizeMap[
                                                                $originalSize
                                                            ]
                                                            ??
                                                            $variant->size->name;

                                                    } else {

                                                        /*
                                                        | Clothing:
                                                        | Keep original size
                                                        */

                                                        $displaySize =
                                                            $variant->size->name;

                                                    }

                                                @endphp



                                                <label
                                                    class="cursor-pointer"
                                                >

                                                    <input

                                                        x-model="selectedSize"

                                                        type="radio"

                                                        name="size_id"

                                                        value="{{ $variant->size_id }}"

                                                        class="peer sr-only"

                                                        required
                                                    >


                                                    <div
                                                        class="w-14 h-14
                                                               rounded-full
                                                               border
                                                               border-gray-300
                                                               flex items-center
                                                               justify-center
                                                               text-sm
                                                               font-semibold
                                                               text-gray-700
                                                               hover:border-[#ff3f6c]
                                                               hover:text-[#ff3f6c]
                                                               peer-checked:border-[#ff3f6c]
                                                               peer-checked:text-[#ff3f6c]
                                                               peer-checked:bg-pink-50
                                                               transition-all
                                                               shadow-sm
                                                               hover:shadow"
                                                    >

                                                        {{ $displaySize }}

                                                    </div>

                                                </label>

                                            @endforeach

                                        </div>

                                    </div>

                                @endif

                            </div>



                            <!-- ================================================= -->
                            <!-- QUANTITY -->
                            <!-- ================================================= -->

                            <div
                                class="flex flex-col
                                       items-center
                                       lg:items-start
                                       mb-6"
                            >

                                <h4
                                    class="text-sm font-bold
                                           text-gray-900 mb-2
                                           uppercase
                                           tracking-wider"
                                >
                                    Quantity
                                </h4>


                                <div
                                    class="flex items-center
                                           border border-gray-300
                                           rounded-md"
                                >

                                    <button
                                        type="button"

                                        @click="
                                            quantity > 1
                                                ? quantity--
                                                : null
                                        "

                                        class="px-4 py-2
                                               hover:bg-gray-100
                                               transition
                                               text-lg font-medium"
                                    >
                                        -
                                    </button>


                                    <input

                                        type="number"

                                        name="quantity"

                                        x-model="quantity"

                                        class="w-16
                                               text-center
                                               border-none
                                               focus:ring-0
                                               font-medium
                                               bg-transparent"

                                        readonly
                                    >


                                    <button
                                        type="button"

                                        @click="
                                            quantity < availableStock
                                                ? quantity++
                                                : null
                                        "

                                        class="px-4 py-2
                                               hover:bg-gray-100
                                               transition
                                               text-lg font-medium"
                                    >
                                        +
                                    </button>

                                </div>


                                <p
                                    x-show="
                                        selectedColor &&
                                        selectedSize &&
                                        !inStock
                                    "

                                    class="text-red-500
                                           font-bold
                                           text-sm mt-2"
                                >

                                    <i
                                        class="fa-solid
                                               fa-circle-exclamation
                                               mr-1"
                                    ></i>

                                    Out of Stock

                                </p>


                                <p
                                    x-show="
                                        selectedColor &&
                                        selectedSize &&
                                        inStock &&
                                        availableStock < 5
                                    "

                                    class="text-orange-500
                                           font-bold
                                           text-sm mt-2"
                                >

                                    Only

                                    <span
                                        x-text="availableStock"
                                    ></span>

                                    left in stock!

                                </p>

                            </div>



                            <!-- ================================================= -->
                            <!-- ADD TO BAG -->
                            <!-- ================================================= -->

                            <div
                                class="flex flex-col
                                       sm:flex-row
                                       gap-4 pb-8 mb-8
                                       border-b
                                       border-gray-200
                                       justify-center
                                       lg:justify-start"
                            >

                                <button

                                    type="submit"

                                    :disabled="
                                        !inStock ||
                                        !selectedColor ||
                                        !selectedSize
                                    "

                                    :class="
                                        (
                                            !inStock ||
                                            !selectedColor ||
                                            !selectedSize
                                        )

                                            ? 'bg-gray-400 cursor-not-allowed'

                                            : 'bg-[#ff3f6c] hover:bg-[#ed3a64] hover:shadow-lg'
                                    "

                                    class="flex-1 max-w-xs
                                           text-white py-4
                                           rounded-full
                                           font-bold text-lg
                                           transition-all
                                           flex items-center
                                           justify-center
                                           shadow-md"
                                >

                                    <i
                                        class="fa-solid
                                               fa-bag-shopping
                                               mr-3 text-xl"
                                    ></i>


                                    <span
                                        x-text="
                                            inStock
                                                ? 'ADD TO BAG'
                                                : 'OUT OF STOCK'
                                        "
                                    ></span>

                                </button>

                            </div>

                        </form>



                        <!-- ================================================= -->
                        <!-- WISHLIST -->
                        <!-- ================================================= -->

                        <form

                            @submit.prevent="
                                window.toggleWishlistAjax(
                                    {{ $product->id }},
                                    $event.currentTarget,
                                    '{{ csrf_token() }}'
                                )
                            "

                            class="flex-1 max-w-xs"
                        >

                            <button
                                type="submit"

                                class="w-full
                                       bg-white
                                       border-2
                                       border-gray-300
                                       text-gray-700
                                       py-4
                                       rounded-full
                                       font-bold text-lg
                                       hover:border-gray-800
                                       hover:text-black
                                       hover:shadow-lg
                                       transition-all
                                       flex items-center
                                       justify-center
                                       shadow-sm mb-8"
                            >

                                <i
                                    class="fa-regular
                                           fa-heart
                                           mr-3 text-xl"
                                ></i>

                                WISHLIST

                            </button>

                        </form>



                        <!-- ================================================= -->
                        <!-- SELLER -->
                        <!-- ================================================= -->

                        <div
                            class="mb-8
                                   border-b
                                   border-gray-200
                                   pb-8
                                   text-center
                                   lg:text-left"
                        >

                            <div
                                class="flex items-center
                                       justify-center
                                       lg:justify-start
                                       space-x-2
                                       text-sm mb-2"
                            >

                                @if($product->discount_price)

                                    <span
                                        class="font-bold
                                               text-gray-900
                                               text-base"
                                    >
                                        ₹ {{ number_format(
                                            $product->discount_price,
                                            0
                                        ) }}
                                    </span>


                                    <span
                                        class="text-gray-400
                                               line-through"
                                    >
                                        ₹ {{ number_format(
                                            $product->price,
                                            0
                                        ) }}
                                    </span>


                                    <span
                                        class="font-bold
                                               text-orange-500"
                                    >
                                        ({{ $discount }}% OFF)
                                    </span>

                                @else

                                    <span
                                        class="font-bold
                                               text-gray-900
                                               text-base"
                                    >
                                        ₹ {{ number_format(
                                            $product->price,
                                            0
                                        ) }}
                                    </span>

                                @endif

                            </div>


                            <p
                                class="text-base
                                       text-gray-700
                                       mb-1"
                            >

                                Seller:

                                <span
                                    class="font-bold
                                           text-[#ff3f6c]"
                                >
                                    STYLEORA OFFICIAL
                                </span>

                            </p>


                            <p
                                class="text-sm
                                       font-bold
                                       text-[#ff3f6c]"
                            >
                                1 more seller available
                            </p>

                        </div>



                        <!-- ================================================= -->
                        <!-- DELIVERY -->
                        <!-- ================================================= -->

                        <div

                            class="mb-10
                                   border-b
                                   border-gray-200
                                   pb-10"

                            x-data="{

                                pincode: '',

                                checked: false,

                                available: false,

                                date: ''

                            }"
                        >

                            <h4
                                class="font-bold
                                       text-gray-900
                                       mb-6
                                       flex items-center
                                       justify-center
                                       lg:justify-start
                                       tracking-wider
                                       text-base"
                            >

                                DELIVERY OPTIONS

                                <i
                                    class="fa-solid
                                           fa-truck-fast
                                           ml-3
                                           text-gray-600
                                           text-xl"
                                ></i>

                            </h4>


                            <div
                                class="relative
                                       w-full max-w-md
                                       mx-auto lg:mx-0
                                       mb-6 flex items-center"
                            >

                                <input

                                    type="text"

                                    x-model="pincode"

                                    placeholder="Enter pincode"

                                    class="w-full
                                           border-2
                                           border-gray-300
                                           rounded-full
                                           px-6 py-3
                                           text-sm
                                           focus:outline-none
                                           focus:border-gray-500"

                                    maxlength="6"
                                >


                                <button

                                    type="button"

                                    @click="
                                        if(pincode.length === 6) {

                                            checked = true;

                                            available =
                                                pincode.startsWith('1') ||
                                                pincode.startsWith('4') ||
                                                pincode.startsWith('5') ||
                                                pincode.startsWith('7');

                                            let d = new Date();

                                            d.setDate(
                                                d.getDate() +
                                                3 +
                                                Math.floor(
                                                    Math.random() * 3
                                                )
                                            );

                                            date =
                                                d.toLocaleDateString(
                                                    'en-IN',
                                                    {
                                                        weekday: 'short',
                                                        month: 'short',
                                                        day: 'numeric'
                                                    }
                                                );
                                        }
                                    "

                                    class="absolute
                                           right-1 top-1 bottom-1
                                           px-6
                                           bg-gray-900
                                           text-white
                                           rounded-full
                                           font-bold text-sm
                                           hover:bg-pink-600"
                                >
                                    Check
                                </button>

                            </div>


                            <div
                                x-show="checked"
                                class="mb-4
                                       text-sm
                                       font-medium"
                            >

                                <p
                                    x-show="available"
                                    class="text-green-600"
                                >

                                    <i
                                        class="fa-solid
                                               fa-circle-check
                                               mr-2"
                                    ></i>

                                    Delivery available by

                                    <span
                                        x-text="date"
                                        class="font-bold"
                                    ></span>

                                </p>


                                <p
                                    x-show="!available"
                                    class="text-red-500"
                                >

                                    <i
                                        class="fa-solid
                                               fa-circle-xmark
                                               mr-2"
                                    ></i>

                                    Unfortunately, we do not deliver
                                    to this pincode.

                                </p>

                            </div>


                            <p
                                x-show="!checked"
                                class="text-sm
                                       text-gray-500
                                       mb-6
                                       text-center
                                       lg:text-left"
                            >
                                Please enter PIN code to check delivery
                                time & Pay on Delivery Availability
                            </p>


                            <ul
                                class="text-base
                                       text-gray-700
                                       space-y-4
                                       max-w-md
                                       mx-auto
                                       lg:mx-0"
                            >

                                <li class="flex items-center">

                                    <div
                                        class="w-8 h-8
                                               rounded-full
                                               bg-gray-100
                                               flex items-center
                                               justify-center
                                               mr-4"
                                    >

                                        <i
                                            class="fa-solid
                                                   fa-truck
                                                   text-gray-600"
                                        ></i>

                                    </div>

                                    100% Original Products

                                </li>


                                <li class="flex items-center">

                                    <div
                                        class="w-8 h-8
                                               rounded-full
                                               bg-gray-100
                                               flex items-center
                                               justify-center
                                               mr-4"
                                    >

                                        <i
                                            class="fa-solid
                                                   fa-money-bill-wave
                                                   text-gray-600"
                                        ></i>

                                    </div>

                                    Pay on delivery might be available

                                </li>


                                <li class="flex items-center">

                                    <div
                                        class="w-8 h-8
                                               rounded-full
                                               bg-gray-100
                                               flex items-center
                                               justify-center
                                               mr-4"
                                    >

                                        <i
                                            class="fa-solid
                                                   fa-rotate-left
                                                   text-gray-600"
                                        ></i>

                                    </div>

                                    Easy

                                    <span class="mx-1">
                                        {{ $product->return_window_days ?? 15 }}
                                    </span>

                                    days returns and exchanges

                                </li>


                                <li class="flex items-center">

                                    <div
                                        class="w-8 h-8
                                               rounded-full
                                               bg-gray-100
                                               flex items-center
                                               justify-center
                                               mr-4"
                                    >

                                        <i
                                            class="fa-solid
                                                   fa-credit-card
                                                   text-gray-600"
                                        ></i>

                                    </div>

                                    EMI option available

                                </li>

                            </ul>

                        </div>



                        <!-- ================================================= -->
                        <!-- PRODUCT DETAILS -->
                        <!-- ================================================= -->

                        <div
                            class="mb-10
                                   border-b
                                   border-gray-200
                                   pb-10"
                        >

                            <h4
                                class="font-bold
                                       text-gray-900
                                       mb-6
                                       flex items-center
                                       justify-center
                                       lg:justify-start
                                       tracking-wider
                                       text-base"
                            >

                                PRODUCT DETAILS

                                <i
                                    class="fa-solid
                                           fa-file-lines
                                           ml-3
                                           text-gray-600
                                           text-xl"
                                ></i>

                            </h4>


                            <p
                                class="text-gray-700
                                       text-base
                                       leading-relaxed
                                       mb-8
                                       text-center
                                       lg:text-left
                                       max-w-2xl"
                            >
                                {{ $product->description }}
                            </p>


                            <div class="space-y-4">


                                <div
                                    class="flex items-center
                                           space-x-4 p-3
                                           bg-gray-50 rounded
                                           border
                                           border-gray-100"
                                >

                                    <div
                                        class="w-10 h-10
                                               bg-white
                                               rounded-full
                                               flex items-center
                                               justify-center
                                               border
                                               border-gray-200
                                               shadow-sm
                                               shrink-0"
                                    >

                                        <i
                                            class="fa-solid
                                                   fa-check
                                                   text-gray-600
                                                   text-sm"
                                        ></i>

                                    </div>


                                    <div>

                                        <h5
                                            class="text-sm
                                                   font-bold
                                                   text-gray-900
                                                   tracking-wide
                                                   uppercase
                                                   text-left"
                                        >
                                            Premium Quality
                                        </h5>


                                        <p
                                            class="text-xs
                                                   text-gray-500
                                                   text-left"
                                        >
                                            Crafted with attention to detail
                                        </p>

                                    </div>

                                </div>



                                <div
                                    class="flex items-center
                                           space-x-4 p-3
                                           bg-gray-50 rounded
                                           border
                                           border-gray-100"
                                >

                                    <div
                                        class="w-10 h-10
                                               bg-white
                                               rounded-full
                                               flex items-center
                                               justify-center
                                               border
                                               border-gray-200
                                               shadow-sm
                                               shrink-0"
                                    >

                                        <i
                                            class="fa-solid
                                                   fa-gem
                                                   text-gray-600
                                                   text-sm"
                                        ></i>

                                    </div>


                                    <div>

                                        <h5
                                            class="text-sm
                                                   font-bold
                                                   text-gray-900
                                                   tracking-wide
                                                   uppercase
                                                   text-left"
                                        >
                                            Stylish Design
                                        </h5>


                                        <p
                                            class="text-xs
                                                   text-gray-500
                                                   text-left"
                                        >
                                            Trendsetting modern aesthetics
                                        </p>

                                    </div>

                                </div>



                                <div
                                    class="flex items-center
                                           space-x-4 p-3
                                           bg-gray-50 rounded
                                           border
                                           border-gray-100"
                                >

                                    <div
                                        class="w-10 h-10
                                               bg-white
                                               rounded-full
                                               flex items-center
                                               justify-center
                                               border
                                               border-gray-200
                                               shadow-sm
                                               shrink-0"
                                    >

                                        <i
                                            class="fa-solid
                                                   fa-layer-group
                                                   text-gray-600
                                                   text-sm"
                                        ></i>

                                    </div>


                                    <div>

                                        <h5
                                            class="text-sm
                                                   font-bold
                                                   text-gray-900
                                                   tracking-wide
                                                   uppercase
                                                   text-left"
                                        >
                                            Versatile Usage
                                        </h5>


                                        <p
                                            class="text-xs
                                                   text-gray-500
                                                   text-left"
                                        >
                                            Perfect for multiple occasions
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- FEATURES BANNER -->
            <!-- ===================================================== -->

            <div
                class="mt-16
                       bg-white
                       border border-gray-200
                       shadow-sm
                       rounded-xl
                       p-8
                       flex flex-wrap
                       justify-between
                       items-center
                       text-center
                       gap-6"
            >

                <div class="flex-1 min-w-[150px]">

                    <div
                        class="w-14 h-14
                               mx-auto
                               bg-gray-50
                               rounded-full
                               flex items-center
                               justify-center
                               shadow-inner
                               mb-4"
                    >

                        <i
                            class="fa-solid
                                   fa-bag-shopping
                                   text-xl"
                        ></i>

                    </div>


                    <h5
                        class="font-bold
                               text-xs
                               uppercase
                               tracking-wider
                               text-gray-900
                               mb-1"
                    >
                        Spacious & Practical
                    </h5>

                </div>



                <div class="flex-1 min-w-[150px]">

                    <div
                        class="w-14 h-14
                               mx-auto
                               bg-gray-50
                               rounded-full
                               flex items-center
                               justify-center
                               shadow-inner
                               mb-4"
                    >

                        <i
                            class="fa-solid
                                   fa-shield-halved
                                   text-xl"
                        ></i>

                    </div>


                    <h5
                        class="font-bold
                               text-xs
                               uppercase
                               tracking-wider
                               text-gray-900
                               mb-1"
                    >
                        Durable Material
                    </h5>

                </div>



                <div class="flex-1 min-w-[150px]">

                    <div
                        class="w-14 h-14
                               mx-auto
                               bg-gray-50
                               rounded-full
                               flex items-center
                               justify-center
                               shadow-inner
                               mb-4"
                    >

                        <i
                            class="fa-solid
                                   fa-feather
                                   text-xl"
                        ></i>

                    </div>


                    <h5
                        class="font-bold
                               text-xs
                               uppercase
                               tracking-wider
                               text-gray-900
                               mb-1"
                    >
                        Lightweight & Stylish
                    </h5>

                </div>



                <div class="flex-1 min-w-[150px]">

                    <div
                        class="w-14 h-14
                               mx-auto
                               bg-gray-50
                               rounded-full
                               flex items-center
                               justify-center
                               shadow-inner
                               mb-4"
                    >

                        <i
                            class="fa-solid
                                   fa-gift
                                   text-xl"
                        ></i>

                    </div>


                    <h5
                        class="font-bold
                               text-xs
                               uppercase
                               tracking-wider
                               text-gray-900
                               mb-1"
                    >
                        Perfect for Occasions
                    </h5>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- RELATED PRODUCTS -->
            <!-- ===================================================== -->

            @if($relatedProducts->count() > 0)

                <div class="mt-24">


                    <div
                        class="flex justify-between
                               items-end
                               mb-8
                               border-b
                               border-gray-100
                               pb-4"
                    >

                        <h2
                            class="font-outfit
                                   text-2xl
                                   font-bold
                                   text-gray-900"
                        >
                            You May Also Like
                        </h2>

                    </div>


                    <div
                        class="grid
                               grid-cols-2
                               md:grid-cols-4
                               gap-4
                               md:gap-6"
                    >

                        @foreach($relatedProducts as $related)

                            <div class="text-black">

                                <x-product-card
                                    :product="$related"
                                />

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif


        </div>

    </div>

</x-app-layout>
```
