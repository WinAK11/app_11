@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <section class="shop-main container d-flex pt-4 pt-xl-5">
            <div class="shop-sidebar side-sticky bg-body" id="shopFilter">
                <div class="aside-header d-flex d-lg-none align-items-center">
                    <h3 class="text-uppercase fs-6 mb-0">Filter By</h3>
                    <button class="btn-close-lg js-close-aside btn-close-aside ms-auto"></button>
                </div>

                <div class="pt-4 pt-lg-0"></div>

                <div class="accordion" id="categories-list">
                    <div class="accordion-item mb-4 pb-3">
                        <h5 class="accordion-header" id="accordion-heading-1">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accordion-filter-1" aria-expanded="true"
                                aria-controls="accordion-filter-1">
                                Product Categories
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-1" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-1" data-bs-parent="#categories-list">
                            <div class="accordion-body px-0 pb-0 pt-3 category-list">
                                <ul class="list list-inline mb-0">
                                    @foreach ($categories as $category)
                                        <li class="list-item">
                                            <span class="menu-link py-1">
                                                <input type="checkbox" class="chk-category" name="categories"
                                                    value="{{ $category->id }}"
                                                    @if (in_array($category->id, explode(',', $f_categories))) checked="checked" @endif />
                                                {{ $category->name }}
                                            </span>
                                            <span class="text-right float-end">{{ $category->products->count() }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion" id="author-filters">
                    <div class="accordion-item mb-4 pb-3">
                        <h5 class="accordion-header" id="accordion-heading-author">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accordion-filter-author" aria-expanded="true"
                                aria-controls="accordion-filter-author">
                                Authors
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-author" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-author" data-bs-parent="#author-filters">
                            <div class="search-field multi-select accordion-body px-0 pb-0">
                                <ul class="list list-inline mb-0 author-list">
                                    @foreach ($authors as $author)
                                        <li class="list-item">
                                            <span class="menu-link py-1">
                                                <input type="checkbox" name="authors" value="{{ $author->id }}"
                                                    class="chk-author"
                                                    @if (in_array($author->id, explode(',', $f_authors))) checked="checked" @endif>
                                                {{ $author->name }}
                                            </span>
                                            <span class="text-right float-end">
                                                {{ $author->products->count() }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="accordion" id="price-filters">
                    <div class="accordion-item mb-4">
                        <h5 class="accordion-header mb-2" id="accordion-heading-price">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accordion-filter-price" aria-expanded="true"
                                aria-controls="accordion-filter-price">
                                Price
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-price" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-price" data-bs-parent="#price-filters">
                            <input class="price-range-slider" type="text" name="price_range" value=""
                                data-slider-min="0" data-slider-max="1000000" data-slider-step="1000"
                                data-slider-value="[{{ $min_price }},{{ $max_price }}]" data-currency="đ" />
                            <div class="price-range__info d-flex align-items-center mt-2">
                                <div class="me-auto">
                                    <span class="text-secondary">Min Price: </span>
                                    <span class="price-range__min">{{ $min_price }}đ</span>
                                </div>
                                <div>
                                    <span class="text-secondary">Max Price: </span>
                                    <span class="price-range__max">{{ $max_price }}đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="shop-list flex-grow-1">
                <div class="swiper-container js-swiper-slider slideshow slideshow_small slideshow_split"
                    data-settings='{
                                        "autoplay": {
                                            "delay": 5000
                                        },
                                        "slidesPerView": 1,
                                        "effect": "fade",
                                        "loop": true,
                                        "pagination": {
                                            "el": ".slideshow-pagination",
                                            "type": "bullets",
                                            "clickable": true
                                        }
                                    }'>
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="slide-split h-100 d-block d-md-flex overflow-hidden">
                                <div class="slide-split_text position-relative d-flex align-items-center"
                                    style="background-color: #f5e6e0;">
                                    <div class="slideshow-text container p-3 p-xl-5">
                                        <h3
                                            class="section-title fw-normal mb-3 animate animate_fade animate_btt animate_delay-2">
                                            Which book has changed the way you see the world?</h3>

                                    </div>
                                </div>
                                <div class="slide-split_media position-relative">
                                    <div class="slideshow-bg" style="background-color: #f5e6e0;">
                                        <img loading="lazy" src="{{ asset('assets/images/shop/shop-banner1.jpg') }}"
                                            width="630" height="450" alt=""
                                            class="slideshow-bg__img object-fit-cover" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide">
                            <div class="slide-split h-100 d-block d-md-flex overflow-hidden">
                                <div class="slide-split_text position-relative d-flex align-items-center"
                                    style="background-color: #f5e6e0;">
                                    <div class="slideshow-text container p-3 p-xl-5">
                                        <h3
                                            class="section-title fw-normal mb-3 animate animate_fade animate_btt animate_delay-2">
                                            If you could live in any book's universe, which one would it be?</h3>

                                    </div>
                                </div>
                                <div class="slide-split_media position-relative">
                                    <div class="slideshow-bg" style="background-color: #f5e6e0;">
                                        <img loading="lazy" src="{{ asset('assets/images/shop/shop-banner2.jpg') }}"
                                            width="630" height="450" alt="Women's accessories"
                                            class="slideshow-bg__img object-fit-cover" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide">
                            <div class="slide-split h-100 d-block d-md-flex overflow-hidden">
                                <div class="slide-split_text position-relative d-flex align-items-center"
                                    style="background-color: #f5e6e0;">
                                    <div class="slideshow-text container p-3 p-xl-5">
                                        <h3
                                            class="section-title fw-normal mb-3 animate animate_fade animate_btt animate_delay-2">
                                            What’s the one book you think everyone should read at least once?</h3>

                                    </div>
                                </div>
                                <div class="slide-split_media position-relative">
                                    <div class="slideshow-bg" style="background-color: #f5e6e0;">
                                        <img loading="lazy" src="{{ asset('assets/images/shop/shop-banner3.jpg') }}"
                                            width="630" height="450" alt="Women's accessories"
                                            class="slideshow-bg__img object-fit-cover" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container p-3 p-xl-5">
                        <div
                            class="slideshow-pagination d-flex align-items-center position-absolute bottom-0 mb-4 pb-xl-2">
                        </div>

                    </div>
                </div>

                <div class="mb-3 pb-2 pb-xl-3"></div>

                <div class="d-flex justify-content-between mb-4 pb-md-2">
                    <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                        <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
                        <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                        <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">The Shop</a>
                    </div>

                    <div
                        class="shop-acs d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">
                        <select class="shop-acs__select form-select w-auto border-0 py-0 order-1 order-md-0"
                            aria-label="Sort Items" name="orderby" id="orderby">
                            <option value="-1" {{ $order == -1 ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="1" {{ $order == 1 ? 'selected' : '' }}>Name (Z-A)</option>
                            <option value="2" {{ $order == 2 ? 'selected' : '' }}>Price (Low-High)</option>
                            <option value="3" {{ $order == 3 ? 'selected' : '' }}>Price (High-Low)</option>
                        </select>

                        <div class="shop-asc__seprator mx-3 bg-light d-none d-md-block order-md-0"></div>

                        <div class="col-size align-items-center order-1 d-none d-lg-flex">
                            <span class="text-uppercase fw-medium me-2">View</span>
                            <button class="btn-link fw-medium me-2 js-cols-size" data-target="products-grid"
                                data-cols="2">2</button>
                            <button class="btn-link fw-medium me-2 js-cols-size" data-target="products-grid"
                                data-cols="3">3</button>
                            <button class="btn-link fw-medium js-cols-size" data-target="products-grid"
                                data-cols="4">4</button>
                        </div>

                        <div class="shop-filter d-flex align-items-center order-0 order-md-3 d-lg-none">
                            <button class="btn-link btn-link_f d-flex align-items-center ps-0 js-open-aside"
                                data-aside="shopFilter">
                                <svg class="d-inline-block align-middle me-2" width="14" height="10"
                                    viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_filter" />
                                </svg>
                                <span class="text-uppercase fw-medium d-inline-block align-middle">Filter</span>
                            </button>
                        </div>
                    </div>
                </div>

                @if (request()->query('query'))
                    <div class="search-results-header mb-4">
                        <h4 class="fs-5">Kết quả tìm kiếm cho: <span
                                class="text-warning fst-italic">"{{ e(request()->query('query')) }}"</span></h4>
                    </div>
                @endif

                <div class="products-grid row row-cols-2 row-cols-md-3" id="products-grid">
                    @foreach ($products as $product)
                        <div class="product-card-wrapper">
                            <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                                <div class="pc__img-wrapper">
                                    <div class="swiper-container background-img js-swiper-slider"
                                        data-settings='{"resizeObserver": true}'>
                                        <div class="swiper-wrapper">
                                            {{-- <div class="swiper-slide">
                                                <a
                                                    href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                                    <img loading="lazy"
                                                        src="{{ asset('uploads/products') }}/{{ $product->image }}"
                                                        width="310" height="400" alt="{{ $product->name }}"
                                                        class="pc__img" />
                                                </a>
                                            </div> --}}
                                            <div class="swiper-slide">
                                                <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                                    <img loading="lazy"
                                                        src="{{ $product->image ? asset('uploads/products/' . $product->image) : asset('uploads/book_placeholder.png') }}"
                                                        width="310" height="400" alt="{{ $product->name }}"
                                                        class="pc__img" />
                                                </a>
                                            </div>
                                            <div class="swiper-slide">
                                                @foreach (explode(',', $product->images) as $gallery_image)
                                                    <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}"><img
                                                            loading="lazy"
                                                            src="{{ asset('uploads/products') }}/{{ $gallery_image }}"
                                                            width="310" height="400" alt="{{ $product->name }}"
                                                            class="pc__img" />
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        <span class="pc__img-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_prev_sm" />
                                            </svg></span>
                                        <span class="pc__img-next"><svg width="7" height="11" viewBox="0 0 7 11"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_next_sm" />
                                            </svg></span>
                                    </div>
                                    @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                                        <a href = "{{ route('cart.index') }}"
                                            class = "pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go
                                            to cart </a>
                                    @else
                                        @if ($product->quantity > 0)
                                            <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="name" value="{{ $product->name }}">
                                                <input type="hidden" name="price" value="{{ $product->sale_price ?? $product->regular_price }}">
                                                <button class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium" data-aside="cartDrawer" title="Add To Cart">
                                                    Add To Cart
                                                </button>
                                            </form>
                                        @else
                                            <button class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium out-of-stock-wrapper">
                                                Out of Stock
                                            </button>
                                        @endif
                                    @endif
                                </div>

                                <div class="pc__info position-relative">
                                    <p class="pc__category">{{ $product->category->name }}</p>
                                    <h6 class="pc__title"><a
                                            href="{{ $product->quantity > 0 ? route('shop.product.details', ['product_slug' => $product->slug]) : 'javascript:void(0);' }}">{{ $product->name }}</a>
                                    </h6>
                                    <span class="text-secondary">by {{ $product->author->name }}</span>
                                    <div class="product-card__price d-flex align-items-center">
                                        <span class="money price">
                                            @if ($product->sale_price)
                                                <s>{{ number_format($product->regular_price, 0, ',', ',') }}đ</s>
                                                {{ number_format($product->sale_price, 0, ',', ',') }}đ
                                            @else
                                                {{ number_format($product->regular_price, 0, ',', ',') }}đ
                                            @endif
                                        </span>
                                        @if ($product->quantity == 0)
                                            <span class="badge bg-danger ms-2">Out of stock</span>
                                        @elseif ($product->quantity < 10)
                                            <span class="badge bg-warning text-dark ms-2">Only {{ $product->quantity }} left</span>
                                        @endif
                                    </div>
                                    <div class="product-card__review d-flex align-items-center">
                                        <div class="reviews-group d-flex">
                                            <svg class="review-star" viewBox="0 0 9 9"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_star" />
                                            </svg>
                                            <svg class="review-star" viewBox="0 0 9 9"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_star" />
                                            </svg>
                                            <svg class="review-star" viewBox="0 0 9 9"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_star" />
                                            </svg>
                                            <svg class="review-star" viewBox="0 0 9 9"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_star" />
                                            </svg>
                                            <svg class="review-star" viewBox="0 0 9 9"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_star" />
                                            </svg>
                                        </div>
                                        <span class="reviews-note text-lowercase text-secondary ms-1">8k+ reviews</span>
                                    </div>

                                    @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
                                        <form method="post"
                                            action="{{ route('wishlist.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}">
                                            @csrf
                                            @method('delete')
                                            <button
                                                class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist"
                                                title="Remove from Wishlist" style="color: red">
                                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <use href="#icon_heart" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('wishlist.add') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}" />
                                            <input type="hidden" name="name" value="{{ $product->name }}" />
                                            <input type="hidden" name="quantity" value="1" />
                                            <input type="hidden" name="price"
                                                value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}" />

                                            <button
                                                class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist"
                                                title="Add To Wishlist">
                                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <use href="#icon_heart" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </section>
    </main>

    <form id="frmfilter" method="GET" action="{{ route('shop.index') }}">
        <input type="hidden" name="page" value="{{ $products->currentPage() }}" />
        <input type="hidden" name="query" value="{{ request()->query('query') }}" />
        <input type="hidden" name="order" id="order" value="{{ $order }}" />
        <input type="hidden" name="authors" id="hdnAuthors" />
        <input type="hidden" name="categories" id="hdnCategories" />
        <input type="hidden" name="min" id="hdnMinPrice" value="{{ $min_price }}" />
        <input type="hidden" name="max" id="hdnMaxPrice" value="{{ $max_price }}" />
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.out-of-stock-wrapper').forEach(wrapper => {
                wrapper.addEventListener('click', function(e) {
                    e.preventDefault();
                    swal({
                        title: "Out of Stock",
                        text: "Sorry, this book is out of stock now.",
                        icon: "error",
                        button: "OK",
                    });
                });
            });
        });

        $(function() {
            $("#orderby").on("change", function() {
                $("#order").val($("#orderby option:selected").val());
                $("#frmfilter").submit();
            });

            $("input[name='authors']").on("change", function() {
                var authors = "";
                $("input[name='authors']:checked").each(function() {
                    if (authors == "") {
                        authors += $(this).val();
                    } else {
                        authors += "," + $(this).val();
                    }
                });
                $("#hdnAuthors").val(authors);
                $("#frmfilter").submit();
            });

            $("input[name='categories']").on("change", function() {
                var categories = "";
                $("input[name='categories']:checked").each(function() {
                    if (categories == "") {
                        categories += $(this).val();
                    } else {
                        categories += "," + $(this).val();
                    }
                });
                $("#hdnCategories").val(categories);
                $("#frmfilter").submit();
            });

            $("[name='price_range']  ").on("slideStop", function() {
                var min = $(this).val().split(',')[0];
                var max = $(this).val().split(',')[1];
                $("#hdnMinPrice").val(min);
                $("#hdnMaxPrice").val(max);
                $("#frmfilter").submit();
            });
        });
    </script>
@endpush
