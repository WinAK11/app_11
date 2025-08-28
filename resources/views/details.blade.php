@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-md-1 pb-md-3"></div>
        <section class="product-single container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="product-single__media" data-media-type="vertical-thumbnail">
                        <div class="product-single__image">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide product-single__image-item">
                                        <img loading="lazy" class="h-auto"
                                            src="{{ asset('uploads/products') }}/{{ $product->image }}" width="674"
                                            height="674" alt="" />
                                        <a data-fancybox="gallery"
                                            href="{{ asset('uploads/products') }}/{{ $product->image }}"
                                            data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_zoom" />
                                            </svg>
                                        </a>
                                    </div>

                                    @foreach (explode(',', $product->images) as $gallery_image)
                                        <div class="swiper-slide product-single__image-item">
                                            <img loading="lazy" class="h-auto"
                                                src="{{ asset('uploads/products') }}/{{ $gallery_image }}" width="674"
                                                height="674" alt="" />
                                            <a data-fancybox="gallery"
                                                href="{{ asset('uploads/products') }}/{{ $gallery_image }}"
                                                data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <use href="#icon_zoom" />
                                                </svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <use href="#icon_prev_sm" />
                                    </svg></div>
                                <div class="swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <use href="#icon_next_sm" />
                                    </svg></div>
                            </div>
                        </div>
                        <div class="product-single__thumbnail">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide product-single__image-item"><img loading="lazy" class="h-auto"
                                            src="{{ asset('uploads/products/thumbnails') }}/{{ $product->image }}"
                                            width="104" height="104" alt="" /></div>
                                    @foreach (explode(',', $product->images) as $gallery_image)
                                        <div class="swiper-slide product-single__image-item"><img loading="lazy"
                                                class="h-auto"
                                                src="{{ asset('uploads/products/thumbnails') }}/{{ $gallery_image }}"
                                                width="104" height="104" alt="" /></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex justify-content-between mb-4 pb-md-2">
                        <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                            <a href="{{ route('home.index') }}"
                                class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
                            <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                            <a href="{{ route('shop.index') }}"
                                class="menu-link menu-link_us-s text-uppercase fw-medium">The Shop</a>
                        </div><!-- /.breadcrumb -->
                    </div>
                    <h1 class="product-single__name">{{ $product->name }}</h1>
                    <div class="product-single__rating">
                        <div class="reviews-group d-flex">
                            <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_star" />
                            </svg>
                            <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_star" />
                            </svg>
                            <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_star" />
                            </svg>
                            <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_star" />
                            </svg>
                            <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_star" />
                            </svg>
                        </div>
                        <span class="reviews-note text-lowercase text-secondary ms-1">{{ $product->reviews_count }}
                            review {{ $product->reviews_count > 2 ? 's' : '' }}</span>
                    </div>
                    <div class="product-single__price">
                        <span class="current-price">
                            @if ($product->sale_price)
                                <s>{{ number_format($product->regular_price, 0, ',', ',') }}đ</s>
                                {{ number_format($product->sale_price, 0, ',', ',') }}đ
                            @else
                                {{ number_format($product->regular_price, 0, ',', ',') }}đ
                            @endif
                        </span>
                    </div>
                    <div class="product-single__short-desc">
                        <p>{{ $product->short_description }}</p>
                    </div>
                    @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                        <a href = "{{ route('cart.index') }}" class = "btn btn-warning mb-3">Go to cart </a>
                    @else
                        <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">
                            @csrf
                            <div class="product-single__addtocart">
                                <div class="qty-control position-relative">
                                    <input type="number" name="quantity" value="1" min="1"
                                        class="qty-control__number text-center">
                                    <div class="qty-control__reduce">-</div>
                                    <div class="qty-control__increase">+</div>
                                </div><!-- .qty-control -->
                                <input type="hidden" name="id" value="{{ $product->id }}" />
                                <input type="hidden" name="name" value="{{ $product->name }}" />
                                <input type="hidden" name="price"
                                    value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}" />
                                <button type="submit" class="btn btn-primary btn-addtocart" data-aside="cartDrawer">Add
                                    to Cart</button>
                            </div>
                        </form>
                    @endif
                    <div class="product-single__addtolinks">
                        @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
                            <form method="post"
                                action="{{ route('wishlist.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}"
                                id="frm-remove-item">
                                @csrf
                                @method('delete')
                                <a href="javascript:void(0)" class="menu-link menu-link_us-s add-to-wishlist"
                                    style="color:red;" onclick="document.getElementById('frm-remove-item').submit()"><svg
                                        width="16" height="16" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <use href="#icon_heart" />
                                    </svg><span> REMOVE FROM WISHLIST</span></a>
                            </form>
                        @else
                            <form method="post" action="{{ route('wishlist.add') }}" id="wishlist-form">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->id }}" />
                                <input type="hidden" name="name" value="{{ $product->name }}" />
                                <input type="hidden" name="quantity" value="1" />
                                <input type="hidden" name="price"
                                    value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}" />
                                <a href="javascript:void(0)" class="menu-link menu-link_us-s add-to-wishlist"
                                    onclick="document.getElementById('wishlist-form').submit();"><svg width="16"
                                        height="16" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <use href="#icon_heart" />
                                    </svg><span> ADD TO WISHLIST</span></a>
                            </form>
                        @endif
                        <script src="js/details-disclosure.html" defer="defer"></script>
                        <script src="js/share.html" defer="defer"></script>
                    </div>
                    <div class="product-single__meta-info">
                        <div class="meta-item">
                            <label>SKU:</label>
                            <span>{{ $product->SKU }}</span>
                        </div>
                        <div class="meta-item">
                            <label>Categories:</label>
                            <span>{{ $product->category->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-single__details-tab">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
                            href="#tab-description" role="tab" aria-controls="tab-description"
                            aria-selected="true">Description</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link nav-link_underscore" id="tab-reviews-tab" data-bs-toggle="tab"
                            href="#tab-reviews" role="tab" aria-controls="tab-reviews" aria-selected="false">Reviews
                            ({{ $product->reviews_count }})</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-description" role="tabpanel"
                        aria-labelledby="tab-description-tab">
                        <div class="product-single__description">
                            {{ $product->description }}
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-reviews" role="tabpanel" aria-labelledby="tab-reviews-tab">
                        <h2 class="product-single__reviews-title">Reviews ({{ $product->reviews_count }})</h2>

                        <!-- Review Statistics -->
                        <div class="review-statistics mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="average-rating text-center">
                                        <div class="rating-number h2 mb-2">{{ $product->average_rating }}</div>
                                        <div class="stars mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $product->star_display['full'])
                                                    {{-- Full star --}}
                                                    <svg class="review-star" style="fill: gold;" viewBox="0 0 9 9"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <use href="#icon_star" />
                                                    </svg>
                                                @elseif ($i == $product->star_display['full'] + 1 && $product->star_display['half'] > 0)
                                                    {{-- Half star --}}
                                                    <svg class="review-star" viewBox="0 0 9 9"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <defs>
                                                            <mask id="half-mask">
                                                                <rect x="0" y="0" width="4.5" height="9"
                                                                    fill="white" />
                                                                <rect x="4.5" y="0" width="4.5" height="9"
                                                                    fill="black" />
                                                            </mask>
                                                        </defs>
                                                        <!-- Background star (empty) -->
                                                        <path fill="#ccc"
                                                            d="M4.0172 0.313075L2.91869 2.64013L0.460942 3.0145C0.0201949 3.08129 -0.15644 3.64899 0.163185 3.97415L1.94131 5.78447L1.52075 8.34177C1.44505 8.80402 1.91103 9.15026 2.30131 8.93408L4.5 7.72661L6.69869 8.93408C7.08897 9.14851 7.55495 8.80402 7.47925 8.34177L7.05869 5.78447L8.83682 3.97415C9.15644 3.64899 8.97981 3.08129 8.53906 3.0145L6.08131 2.64013L4.9828 0.313075C4.78598 -0.101718 4.2157 -0.10699 4.0172 0.313075Z" />
                                                        <!-- Half filled star -->
                                                        <path fill="gold" mask="url(#half-mask)"
                                                            d="M4.0172 0.313075L2.91869 2.64013L0.460942 3.0145C0.0201949 3.08129 -0.15644 3.64899 0.163185 3.97415L1.94131 5.78447L1.52075 8.34177C1.44505 8.80402 1.91103 9.15026 2.30131 8.93408L4.5 7.72661L6.69869 8.93408C7.08897 9.14851 7.55495 8.80402 7.47925 8.34177L7.05869 5.78447L8.83682 3.97415C9.15644 3.64899 8.97981 3.08129 8.53906 3.0145L6.08131 2.64013L4.9828 0.313075C4.78598 -0.101718 4.2157 -0.10699 4.0172 0.313075Z" />
                                                    </svg>
                                                @else
                                                    {{-- Empty star --}}
                                                    <svg class="review-star" style="fill: #ccc;" viewBox="0 0 9 9"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <use href="#icon_star" />
                                                    </svg>
                                                @endif
                                            @endfor

                                        </div>
                                        <div class="text-muted">Based on {{ $product->reviews_count }} reviews</div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="rating-distribution">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <div class="rating-bar d-flex align-items-center mb-2">
                                                <span class="rating-label me-2">{{ $i }} stars</span>
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $product->rating_percentages[$i] ?? 0 }}%"></div>
                                                </div>
                                                <span
                                                    class="rating-count text-muted">{{ $product->rating_distribution[$i] ?? 0 }}</span>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews List -->
                        <div id="reviews-container">
                            <div class="product-single__reviews-list" id="reviews-list">
                                <!-- Reviews will be loaded here dynamically -->
                            </div>
                            <div class="text-center" id="reviews-loading">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="text-center" id="reviews-empty" style="display: none;">
                                <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                            </div>
                            <div class="text-center" id="load-more-container" style="display: none;">
                                <button class="btn btn-outline-primary" id="load-more-reviews">Load More Reviews</button>
                            </div>
                        </div>

                        <!-- Review Form -->
                        @auth
                            @if (!$product->hasUserReviewed(auth()->id()))
                                <div class="product-single__review-form mt-5">
                                    <h5>Write a Review</h5>
                                    <p>Your email address will not be published. Required fields are marked *</p>
                                    <form id="review-form" data-product-id="{{ $product->id }}">
                                        @csrf
                                        <div class="select-star-rating mb-4">
                                            <label class="form-label">Your rating *</label>
                                            <div class="star-rating" id="star-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="star-rating__star-icon" width="20" height="20"
                                                        fill="#ccc" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"
                                                        data-rating="{{ $i }}">
                                                        <path
                                                            d="M11.1429 5.04687C11.1429 4.84598 10.9286 4.76562 10.7679 4.73884L7.40625 4.25L5.89955 1.20312C5.83929 1.07589 5.72545 0.928571 5.57143 0.928571C5.41741 0.928571 5.30357 1.07589 5.2433 1.20312L3.73661 4.25L0.375 4.73884C0.207589 4.76562 0 4.84598 0 5.04687C0 5.16741 0.0870536 5.28125 0.167411 5.3683L2.60491 7.73884L2.02902 11.0871C2.02232 11.1339 2.01563 11.1741 2.01563 11.221C2.01563 11.3951 2.10268 11.5558 2.29688 11.5558C2.39063 11.5558 2.47768 11.5223 2.56473 11.4754L5.57143 9.89509L8.57813 11.4754C8.65848 11.5223 8.75223 11.5558 8.84598 11.5558C9.04018 11.5558 9.12054 11.3951 9.12054 11.221C9.12054 11.1741 9.12054 11.1339 9.11384 11.0871L8.53795 7.73884L10.9688 5.3683C11.0558 5.28125 11.1429 5.16741 11.1429 5.04687Z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            <input type="hidden" id="form-input-rating" name="rating" value=""
                                                required />
                                            <div class="invalid-feedback">Please select a rating.</div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="form-input-review" class="form-label">Your Review *</label>
                                            <textarea id="form-input-review" name="comment" class="form-control form-control_gray"
                                                placeholder="Write your review here..." cols="30" rows="8" required></textarea>
                                            <div class="invalid-feedback">Please write a review.</div>
                                        </div>
                                        <div class="form-action">
                                            <button type="submit" class="btn btn-primary" id="submit-review">Submit
                                                Review</button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-info mt-4">
                                    <p class="mb-0">You have already reviewed this product.</p>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning mt-4">
                                <p class="mb-0">Please <a href="{{ route('login') }}">login</a> to write a review.</p>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </section>
        <section class="products-carousel container">
            <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Products</strong></h2>

            <div id="related_products" class="position-relative">
                <div class="swiper-container js-swiper-slider"
                    data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": true,
            "pagination": {
              "el": "#related_products .products-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": "#related_products .products-carousel__next",
              "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 2,
                "slidesPerGroup": 2,
                "spaceBetween": 14
              },
              "768": {
                "slidesPerView": 3,
                "slidesPerGroup": 3,
                "spaceBetween": 24
              },
              "992": {
                "slidesPerView": 4,
                "slidesPerGroup": 4,
                "spaceBetween": 30
              }
            }
          }'>
                    <div class="swiper-wrapper">
                        @foreach ($related_products as $related_product)
                            <div class="swiper-slide product-card">
                                <div class="pc__img-wrapper">
                                    <a
                                        href="{{ route('shop.product.details', ['product_slug' => $related_product->slug]) }}">
                                        <img loading="lazy"
                                            src="{{ asset('uploads/products') }}/{{ $related_product->image }}"
                                            width="330" height="400" alt="{{ $related_product->name }}"
                                            class="pc__img">
                                        @foreach (explode(',', $related_product->images) as $gallery_image)
                                            <img loading="lazy"
                                                src="{{ asset('uploads/products') }}/{{ $gallery_image }}"
                                                width="330" height="400" alt="{{ $related_product->name }}"
                                                class="pc__img pc__img-second">
                                        @endforeach
                                    </a>
                                    @if (Cart::instance('cart')->content()->where('id', $related_product->id)->count() > 0)
                                        <a href = "{{ route('cart.index') }}"
                                            class = "pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go
                                            to cart </a>
                                    @else
                                        <form name = "addtocart-form" method = "post"
                                            action = "{{ route('cart.add') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $related_product->id }}" />
                                            <input type="hidden" name="quantity" value="1" />
                                            <input type="hidden" name="name" value="{{ $related_product->name }}" />
                                            <input type="hidden" name="price"
                                                value="{{ $related_product->sale_price == '' ? $related_product->regular_price : $related_product->sale_price }}" />
                                            <button
                                                class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium"
                                                data-aside="cartDrawer" title="Add To Cart">Add To Cart</button>
                                        </form>
                                    @endif
                                </div>

                                <div class="pc__info position-relative">
                                    <p class="pc__category">{{ $related_product->category->name }}</p>
                                    <h6 class="pc__title"><a href="details.html">{{ $related_product->name }}</a></h6>
                                    <div class="product-card__price d-flex">
                                        <span class="money price">
                                            @if ($related_product->sale_price)
                                                <s>{{ number_format($related_product->regular_price, 0, ',', ',') }}đ</s>
                                                {{ number_format($related_product->sale_price, 0, ',', ',') }}đ
                                            @else
                                                {{ number_format($related_product->regular_price, 0, ',', ',') }}đ
                                            @endif
                                        </span>
                                    </div>

                                    @if (Cart::instance('wishlist')->content()->where('id', $related_product->id)->count() > 0)
                                        <form method="post"
                                            action="{{ route('wishlist.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $related_product->id)->first()->rowId]) }}">
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
                                            <input type="hidden" name="id" value="{{ $related_product->id }}" />
                                            <input type="hidden" name="name" value="{{ $related_product->name }}" />
                                            <input type="hidden" name="quantity" value="1" />
                                            <input type="hidden" name="price"
                                                value="{{ $related_product->sale_price == '' ? $related_product->regular_price : $related_product->sale_price }}" />

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
                        @endforeach
                    </div><!-- /.swiper-wrapper -->
                </div><!-- /.swiper-container js-swiper-slider -->

                <div
                    class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_prev_md" />
                    </svg>
                </div><!-- /.products-carousel__prev -->
                <div
                    class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_next_md" />
                    </svg>
                </div><!-- /.products-carousel__next -->

                <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
                <!-- /.products-pagination -->
            </div><!-- /.position-relative -->

        </section><!-- /.products-carousel container -->
    </main>

    <!-- Review System JavaScript -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productId = {{ $product->id }};
            let currentPage = 1;
            let isLoading = false;

            // Star rating functionality
            const starRating = document.getElementById('star-rating');
            const ratingInput = document.getElementById('form-input-rating');

            if (starRating) {
                starRating.addEventListener('click', function(e) {
                    if (e.target.closest('.star-rating__star-icon')) {
                        const star = e.target.closest('.star-rating__star-icon');
                        const rating = parseInt(star.dataset.rating);

                        // Update hidden input
                        ratingInput.value = rating;

                        // Update star display
                        const stars = starRating.querySelectorAll('.star-rating__star-icon');
                        stars.forEach((s, index) => {
                            if (index < rating) {
                                s.style.fill = '#ffc107'; // Gold color
                            } else {
                                s.style.fill = '#ccc'; // Gray color
                            }
                        });

                        // Remove validation error
                        ratingInput.classList.remove('is-invalid');
                    }
                });
            }

            // Load reviews on page load
            loadReviews();

            // Load more reviews button
            const loadMoreBtn = document.getElementById('load-more-reviews');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    currentPage++;
                    loadReviews(currentPage);
                });
            }

            // Review form submission
            const reviewForm = document.getElementById('review-form');
            if (reviewForm) {
                reviewForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitReview();
                });
            }

            function loadReviews(page = 1) {
                if (isLoading) return;

                isLoading = true;
                const reviewsList = document.getElementById('reviews-list');
                const loadingEl = document.getElementById('reviews-loading');
                const emptyEl = document.getElementById('reviews-empty');
                const loadMoreContainer = document.getElementById('load-more-container');

                if (page === 1) {
                    reviewsList.innerHTML = '';
                    loadingEl.style.display = 'block';
                    emptyEl.style.display = 'none';
                    loadMoreContainer.style.display = 'none';
                }

                fetch(`/products/${productId}/reviews?page=${page}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const text = await response.text();
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Response was not JSON:', text);
                            throw new Error('Non-JSON response');
                        }
                    })
                    .then(data => {
                        loadingEl.style.display = 'none';

                        if (data.reviews.length === 0 && page === 1) {
                            emptyEl.style.display = 'block';
                            return;
                        }

                        if (page === 1) {
                            reviewsList.innerHTML = '';
                        }

                        data.reviews.forEach(review => {
                            const reviewHtml = createReviewHtml(review);
                            reviewsList.insertAdjacentHTML('beforeend', reviewHtml);
                        });

                        // Update product stats
                        updateProductStats(data.product_stats);

                        // Show/hide load more button
                        if (data.pagination.current_page < data.pagination.last_page) {
                            loadMoreContainer.style.display = 'block';
                        } else {
                            loadMoreContainer.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading reviews:', error);
                        loadingEl.style.display = 'none';
                        if (page === 1) {
                            emptyEl.innerHTML =
                                '<p class="text-muted">Error loading reviews. Please try again later.</p>';
                            emptyEl.style.display = 'block';
                        }
                    })
                    .finally(() => {
                        isLoading = false;
                    });
            }

            function createReviewHtml(review) {
                const deleteButton = review.can_delete ?
                    `<button class="btn btn-sm text-decoration-underline text-danger" onclick="deleteReview(${review.id})">Delete</button>` :
                    '';

                // Create star rating HTML
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= review.rating) {
                        starsHtml +=
                            '<svg class="review-star" style="fill: gold;"><use href="#icon_star" /></svg> ';
                    } else {
                        starsHtml += '<svg class="review-star" style="fill: #ccc;"><use href="#icon_star" /></svg>';
                    }
                }

                return `
            <div class="product-single__reviews-item" data-review-id="${review.id}">
                <div class="customer-avatar">
                    <img loading="lazy" src="/images/avatar/user-1.png" alt="" />
                </div>
                <div class="customer-review">
                    <div class="customer-name">
                        <h6>${review.user_name} ${deleteButton}</h6>
                        <div class="reviews-group d-flex">
                            ${starsHtml}
                        </div>
                    </div>
                    <div class="review-date">${review.created_at}</div>
                    <div class="review-text">
                        <p>${review.comment}</p>
                    </div>
                </div>
            </div>
        `;
            }

            function updateProductStats(stats) {
                // Update average rating
                const ratingNumber = document.querySelector('.rating-number');
                if (ratingNumber) {
                    ratingNumber.textContent = parseFloat(stats.average_rating || 0).toFixed(1);
                }

                // Update reviews count
                const reviewsTitle = document.querySelector('.product-single__reviews-title');
                if (reviewsTitle) {
                    reviewsTitle.textContent = `Reviews (${stats.reviews_count || 0})`;
                }

                // Update tab title
                const tabReviews = document.getElementById('tab-reviews-tab');
                if (tabReviews) {
                    tabReviews.textContent = `Reviews (${stats.reviews_count || 0})`;
                }

                // Update the main product rating stars display
                updateMainRatingStars(stats.average_rating || 0);

                // Update rating distribution
                if (stats.rating_distribution) {
                    for (let i = 5; i >= 1; i--) {
                        const progressBar = document.querySelector(`.rating-bar:nth-child(${6-i}) .progress-bar`);
                        const ratingCount = document.querySelector(`.rating-bar:nth-child(${6-i}) .rating-count`);
                        if (progressBar && ratingCount) {
                            progressBar.style.width = `${stats.rating_percentages[i] || 0}%`;
                            ratingCount.textContent = stats.rating_distribution[i] || 0;
                        }
                    }
                }

                // Update "Based on X reviews" text
                const reviewsCountText = document.querySelector('.average-rating .text-muted');
                if (reviewsCountText) {
                    reviewsCountText.textContent = `Based on ${stats.reviews_count || 0} reviews`;
                }
            }

            function updateMainRatingStars(averageRating) {
                // Find the main rating stars container in the review statistics
                const mainStarsContainer = document.querySelector('.average-rating .stars');
                if (!mainStarsContainer) return;

                // Clear existing stars
                mainStarsContainer.innerHTML = '';

                const fullStars = Math.floor(averageRating);
                const hasHalfStar = (averageRating - fullStars) >= 0.5;
                const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

                // Add full stars
                for (let i = 0; i < fullStars; i++) {
                    mainStarsContainer.innerHTML += `
                <svg class="review-star" style="fill: gold;" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.0172 0.313075L2.91869 2.64013L0.460942 3.0145C0.0201949 3.08129 -0.15644 3.64899 0.163185 3.97415L1.94131 5.78447L1.52075 8.34177C1.44505 8.80402 1.91103 9.15026 2.30131 8.93408L4.5 7.72661L6.69869 8.93408C7.08897 9.14851 7.55495 8.80402 7.47925 8.34177L7.05869 5.78447L8.83682 3.97415C9.15644 3.64899 8.97981 3.08129 8.53906 3.0145L6.08131 2.64013L4.9828 0.313075C4.78598 -0.101718 4.2157 -0.10699 4.0172 0.313075Z" />
                </svg>
            `;
                }

                // Add half star if needed
                if (hasHalfStar) {
                    mainStarsContainer.innerHTML += `
                <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <mask id="half-mask-main-${Date.now()}">
                            <rect x="0" y="0" width="4.5" height="9" fill="white"/>
                            <rect x="4.5" y="0" width="4.5" height="9" fill="black"/>
                        </mask>
                    </defs>
                    <path fill="#ccc" d="M4.0172 0.313075L2.91869 2.64013L0.460942 3.0145C0.0201949 3.08129 -0.15644 3.64899 0.163185 3.97415L1.94131 5.78447L1.52075 8.34177C1.44505 8.80402 1.91103 9.15026 2.30131 8.93408L4.5 7.72661L6.69869 8.93408C7.08897 9.14851 7.55495 8.80402 7.47925 8.34177L7.05869 5.78447L8.83682 3.97415C9.15644 3.64899 8.97981 3.08129 8.53906 3.0145L6.08131 2.64013L4.9828 0.313075C4.78598 -0.101718 4.2157 -0.10699 4.0172 0.313075Z" />
                    <path fill="gold" mask="url(#half-mask-main-${Date.now()})" d="M4.0172 0.313075L2.91869 2.64013L0.460942 3.0145C0.0201949 3.08129 -0.15644 3.64899 0.163185 3.97415L1.94131 5.78447L1.52075 8.34177C1.44505 8.80402 1.91103 9.15026 2.30131 8.93408L4.5 7.72661L6.69869 8.93408C7.08897 9.14851 7.55495 8.80402 7.47925 8.34177L7.05869 5.78447L8.83682 3.97415C9.15644 3.64899 8.97981 3.08129 8.53906 3.0145L6.08131 2.64013L4.9828 0.313075C4.78598 -0.101718 4.2157 -0.10699 4.0172 0.313075Z" />
                </svg>
            `;
                }

                // Add empty stars
                for (let i = 0; i < emptyStars; i++) {
                    mainStarsContainer.innerHTML += `
                <svg class="review-star" style="fill: #ccc;" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.0172 0.313075L2.91869 2.64013L0.460942 3.0145C0.0201949 3.08129 -0.15644 3.64899 0.163185 3.97415L1.94131 5.78447L1.52075 8.34177C1.44505 8.80402 1.91103 9.15026 2.30131 8.93408L4.5 7.72661L6.69869 8.93408C7.08897 9.14851 7.55495 8.80402 7.47925 8.34177L7.05869 5.78447L8.83682 3.97415C9.15644 3.64899 8.97981 3.08129 8.53906 3.0145L6.08131 2.64013L4.9828 0.313075C4.78598 -0.101718 4.2157 -0.10699 4.0172 0.313075Z" />
                </svg>
            `;
                }
            }

            function submitReview() {
                const form = document.getElementById('review-form');
                const formData = new FormData(form);
                const submitBtn = document.getElementById('submit-review');

                // Validation
                let isValid = true;
                if (!ratingInput.value) {
                    ratingInput.classList.add('is-invalid');
                    isValid = false;
                }
                if (!formData.get('comment').trim()) {
                    document.getElementById('form-input-review').classList.add('is-invalid');
                    isValid = false;
                }

                if (!isValid) return;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';

                fetch(`/products/${productId}/reviews`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);

                            // Reset form
                            form.reset();
                            ratingInput.value = '';
                            starRating.querySelectorAll('.star-rating__star-icon').forEach(star => {
                                star.style.fill = '#ccc';
                            });

                            // Reload reviews from page 1
                            currentPage = 1;
                            loadReviews();

                            // Hide the review form and show "already reviewed" message
                            const reviewFormContainer = document.querySelector('.product-single__review-form');
                            if (reviewFormContainer) {
                                reviewFormContainer.innerHTML =
                                    '<div class="alert alert-info"><p class="mb-0">You have already reviewed this product.</p></div>';
                            }
                        } else {
                            showAlert('danger', data.error || 'Failed to submit review');
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting review:', error);
                        showAlert('danger', 'Failed to submit review. Please try again.');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit Review';
                    });
            }

            function showAlert(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

                const reviewsContainer = document.getElementById('reviews-container');
                reviewsContainer.insertBefore(alertDiv, reviewsContainer.firstChild);

                setTimeout(() => {
                    if (alertDiv.parentNode) alertDiv.remove();
                }, 5000);
            }

            // IMPORTANT: Define deleteReview as a global function
            window.deleteReview = function(reviewId) {
                if (!confirm('Are you sure you want to delete this review?')) {
                    return;
                }

                fetch(`/reviews/${reviewId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const text = await response.text();
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Non-JSON response');
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            // Remove review from DOM
                            const reviewElement = document.querySelector(`[data-review-id="${reviewId}"]`);
                            if (reviewElement) {
                                reviewElement.remove();
                            }

                            // Update product stats
                            updateProductStats(data.product_stats);

                            // FIXED: Restore the review form if user deleted their own review
                            restoreReviewForm();

                            showAlert('success', data.message);
                        } else {
                            showAlert('danger', data.error || 'Failed to delete review');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting review:', error);
                        showAlert('danger', 'Failed to delete review. Please try again.');
                    });
            };

            // NEW: Function to restore review form after deleting user's own review
            function restoreReviewForm() {
                const reviewFormContainer = document.querySelector('.product-single__review-form');
                const alreadyReviewedMessage = reviewFormContainer ? reviewFormContainer.querySelector(
                    '.alert-info') : null;

                if (alreadyReviewedMessage) {
                    // Restore the original review form HTML
                    reviewFormContainer.innerHTML = `
                <h5>Write a Review</h5>
                <p>Your email address will not be published. Required fields are marked *</p>
                <form id="review-form" data-product-id="${productId}">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <div class="select-star-rating mb-4">
                        <label class="form-label">Your rating *</label>
                        <div class="star-rating" id="star-rating">
                            ${Array.from({length: 5}, (_, i) => ` <
                        svg class = "star-rating__star-icon"
                    width = "20"
                    height = "20"
                    fill = "#ccc"
                    viewBox = "0 0 12 12"
                    xmlns = "http://www.w3.org/2000/svg"
                    data - rating = "${i + 1}" >
                        <
                        path d =
                        "M11.1429 5.04687C11.1429 4.84598 10.9286 4.76562 10.7679 4.73884L7.40625 4.25L5.89955 1.20312C5.83929 1.07589 5.72545 0.928571 5.57143 0.928571C5.41741 0.928571 5.30357 1.07589 5.2433 1.20312L3.73661 4.25L0.375 4.73884C0.207589 4.76562 0 4.84598 0 5.04687C0 5.16741 0.0870536 5.28125 0.167411 5.3683L2.60491 7.73884L2.02902 11.0871C2.02232 11.1339 2.01563 11.1741 2.01563 11.221C2.01563 11.3951 2.10268 11.5558 2.29688 11.5558C2.39063 11.5558 2.47768 11.5223 2.56473 11.4754L5.57143 9.89509L8.57813 11.4754C8.65848 11.5223 8.75223 11.5558 8.84598 11.5558C9.04018 11.5558 9.12054 11.3951 9.12054 11.221C9.12054 11.1741 9.12054 11.1339 9.11384 11.0871L8.53795 7.73884L10.9688 5.3683C11.0558 5.28125 11.1429 5.16741 11.1429 5.04687Z" /
                        >
                        <
                        /svg>
                    `).join('')}
                        </div>
                        <input type="hidden" id="form-input-rating" name="rating" value="" required />
                        <div class="invalid-feedback">Please select a rating.</div>
                    </div>
                    <div class="mb-4">
                        <label for="form-input-review" class="form-label">Your Review *</label>
                        <textarea id="form-input-review" name="comment" class="form-control form-control_gray"
                                  placeholder="Write your review here..." cols="30" rows="8" required></textarea>
                        <div class="invalid-feedback">Please write a review.</div>
                    </div>
                    <div class="form-action">
                        <button type="submit" class="btn btn-primary" id="submit-review">Submit Review</button>
                    </div>
                </form>
            `;

                    // Re-initialize the form event listeners
                    const newForm = document.getElementById('review-form');
                    const newStarRating = document.getElementById('star-rating');
                    const newRatingInput = document.getElementById('form-input-rating');

                    if (newForm) {
                        newForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            submitReview();
                        });
                    }

                    if (newStarRating) {
                        newStarRating.addEventListener('click', function(e) {
                            if (e.target.closest('.star-rating__star-icon')) {
                                const star = e.target.closest('.star-rating__star-icon');
                                const rating = parseInt(star.dataset.rating);

                                newRatingInput.value = rating;

                                const stars = newStarRating.querySelectorAll('.star-rating__star-icon');
                                stars.forEach((s, index) => {
                                    if (index < rating) {
                                        s.style.fill = '#ffc107';
                                    } else {
                                        s.style.fill = '#ccc';
                                    }
                                });

                                newRatingInput.classList.remove('is-invalid');
                            }
                        });
                    }
                }
            }

            // Also expose other functions globally if needed
            window.showAlert = showAlert;
        });
    </script>
@endsection
