@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <section class="shop-main container d-flex pt-4 pt-xl-5">
            <div class="shop-sidebar side-sticky bg-body" id="searchFilter">
                <div class="aside-header d-flex d-lg-none align-items-center">
                    <h3 class="text-uppercase fs-6 mb-0">Filter By</h3>
                    <button class="btn-close-lg js-close-aside btn-close-aside ms-auto"></button>
                </div>

                <div class="pt-4 pt-lg-0"></div>

                <!-- Search Type Filter -->
                <div class="accordion" id="search-type-filters">
                    <div class="accordion-item mb-4 pb-3">
                        <h5 class="accordion-header" id="accordion-heading-search-type">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accordion-filter-search-type"
                                aria-expanded="true" aria-controls="accordion-filter-search-type">
                                Search Type
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-search-type" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-search-type" data-bs-parent="#search-type-filters">
                            <div class="accordion-body px-0 pb-0 pt-3">
                                <ul class="list list-inline mb-0">
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="search_type" value="hybrid" class="chk-search-type"
                                                {{ $search_type == 'hybrid' ? 'checked' : '' }} />
                                            Hybrid Search (Recommended)
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="search_type" value="vector" class="chk-search-type"
                                                {{ $search_type == 'vector' ? 'checked' : '' }} />
                                            AI-Powered Search
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="search_type" value="text" class="chk-search-type"
                                                {{ $search_type == 'text' ? 'checked' : '' }} />
                                            Text Search
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="accordion" id="sort-filters">
                    <div class="accordion-item mb-4 pb-3">
                        <h5 class="accordion-header" id="accordion-heading-sort">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accordion-filter-sort" aria-expanded="true"
                                aria-controls="accordion-filter-sort">
                                Sort By
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-sort" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-sort" data-bs-parent="#sort-filters">
                            <div class="accordion-body px-0 pb-0 pt-3">
                                <ul class="list list-inline mb-0">
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="sort" value="relevance" class="chk-sort"
                                                {{ $sort == 'relevance' ? 'checked' : '' }} />
                                            Most Relevant
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="sort" value="name_asc" class="chk-sort"
                                                {{ $sort == 'name_asc' ? 'checked' : '' }} />
                                            Name (A-Z)
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="sort" value="name_desc" class="chk-sort"
                                                {{ $sort == 'name_desc' ? 'checked' : '' }} />
                                            Name (Z-A)
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="sort" value="price_asc" class="chk-sort"
                                                {{ $sort == 'price_asc' ? 'checked' : '' }} />
                                            Price (Low-High)
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="sort" value="price_desc" class="chk-sort"
                                                {{ $sort == 'price_desc' ? 'checked' : '' }} />
                                            Price (High-Low)
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="sort" value="rating" class="chk-sort"
                                                {{ $sort == 'rating' ? 'checked' : '' }} />
                                            Highest Rated
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="radio" name="sort" value="newest" class="chk-sort"
                                                {{ $sort == 'newest' ? 'checked' : '' }} />
                                            Newest First
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Filter -->
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

                <!-- Authors Filter -->
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

                <!-- Price Filter -->
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

                <!-- Rating Filter -->
                <div class="accordion" id="rating-filters">
                    <div class="accordion-item mb-4 pb-3">
                        <h5 class="accordion-header" id="accordion-heading-rating">
                            <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accordion-filter-rating" aria-expanded="true"
                                aria-controls="accordion-filter-rating">
                                Rating
                                <svg class="accordion-button__icon type2" viewBox="0 0 10 6"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                                        <path
                                            d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                                    </g>
                                </svg>
                            </button>
                        </h5>
                        <div id="accordion-filter-rating" class="accordion-collapse collapse show border-0"
                            aria-labelledby="accordion-heading-rating" data-bs-parent="#rating-filters">
                            <div class="accordion-body px-0 pb-0 pt-3">
                                <ul class="list list-inline mb-0">
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="checkbox" name="rating" value="5" class="chk-rating"
                                                {{ in_array('5', explode(',', $f_rating)) ? 'checked' : '' }} />
                                            <div class="reviews-group d-flex">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="review-star" style="fill: gold;" viewBox="0 0 9 9"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <use href="#icon_star" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="checkbox" name="rating" value="4" class="chk-rating"
                                                {{ in_array('4', explode(',', $f_rating)) ? 'checked' : '' }} />
                                            <div class="reviews-group d-flex">
                                                @for ($i = 1; $i <= 4; $i++)
                                                    <svg class="review-star" style="fill: gold;" viewBox="0 0 9 9"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <use href="#icon_star" />
                                                    </svg>
                                                @endfor
                                                <svg class="review-star" style="fill: #ccc;" viewBox="0 0 9 9"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <use href="#icon_star" />
                                                </svg>
                                            </div>
                                            & Up
                                        </span>
                                    </li>
                                    <li class="list-item">
                                        <span class="menu-link py-1">
                                            <input type="checkbox" name="rating" value="3" class="chk-rating"
                                                {{ in_array('3', explode(',', $f_rating)) ? 'checked' : '' }} />
                                            <div class="reviews-group d-flex">
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <svg class="review-star" style="fill: gold;" viewBox="0 0 9 9"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <use href="#icon_star" />
                                                    </svg>
                                                @endfor
                                                @for ($i = 1; $i <= 2; $i++)
                                                    <svg class="review-star" style="fill: #ccc;" viewBox="0 0 9 9"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <use href="#icon_star" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            & Up
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="shop-list flex-grow-1">
                <!-- Search Header -->
                <div class="search-header mb-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h4 mb-1">Search Results</h2>
                            <p class="text-muted mb-0">
                                @if ($query)
                                    Showing results for "<strong>{{ $query }}</strong>"
                                @else
                                    All Products
                                @endif
                                ({{ $products->total() }} {{ $products->total() == 1 ? 'result' : 'results' }})
                            </p>
                        </div>
                        <div class="search-type-badge">
                            @if ($search_type == 'vector')
                                <span class="badge bg-primary">AI-Powered Search</span>
                            @elseif($search_type == 'text')
                                <span class="badge bg-secondary">Text Search</span>
                            @else
                                <span class="badge bg-success">Hybrid Search</span>
                            @endif
                        </div>
                    </div>

                    <!-- Search Suggestions -->
                    @if ($products->total() == 0 && $query)
                        <div class="alert alert-info">
                            <h5>No results found for "{{ $query }}"</h5>
                            <p class="mb-2">Try these suggestions:</p>
                            <ul class="mb-0">
                                <li>Check your spelling</li>
                                <li>Try different keywords</li>
                                <li>Use more general terms</li>
                                <li>Try searching by author or category</li>
                                <li>Try switching search types (AI-Powered, Text, or Hybrid)</li>
                            </ul>
                            <div class="mt-3">
                                <a href="{{ route('search.results') }}" class="btn btn-outline-primary">
                                    Browse All Products
                                </a>
                                <a href="{{ route('shop.index') }}" class="btn btn-primary ms-2">
                                    Go to Shop
                                </a>
                            </div>
                        </div>
                    @elseif($products->total() == 0 && !$query)
                        <div class="alert alert-info">
                            <h5>Browse All Products</h5>
                            <p class="mb-3">Use the filters on the left to narrow down your search, or search for
                                specific items using the search box.</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">
                                Go to Shop
                            </a>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between mb-4 pb-md-2">
                    <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                        <a href="{{ route('home.index') }}"
                            class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
                        <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                        <a href="{{ route('shop.index') }}"
                            class="menu-link menu-link_us-s text-uppercase fw-medium">Shop</a>
                        <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                        <span class="menu-link menu-link_us-s text-uppercase fw-medium">Search Results</span>
                    </div>

                    <div
                        class="shop-acs d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">
                        <select class="shop-acs__select form-select w-auto border-0 py-0 order-1 order-md-0"
                            aria-label="Sort Items" name="orderby" id="orderby">
                            <option value="relevance" {{ $sort == 'relevance' ? 'selected' : '' }}>Most Relevant</option>
                            <option value="name_asc" {{ $sort == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="name_desc" {{ $sort == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                            <option value="price_asc" {{ $sort == 'price_asc' ? 'selected' : '' }}>Price (Low-High)
                            </option>
                            <option value="price_desc" {{ $sort == 'price_desc' ? 'selected' : '' }}>Price (High-Low)
                            </option>
                            <option value="rating" {{ $sort == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest First</option>
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
                                data-aside="searchFilter">
                                <svg class="d-inline-block align-middle me-2" width="14" height="10"
                                    viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_filter" />
                                </svg>
                                <span class="text-uppercase fw-medium d-inline-block align-middle">Filter</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="products-grid row row-cols-2 row-cols-md-3" id="products-grid">
                    @foreach ($products as $product)
                        <div class="product-card-wrapper">
                            <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                                <div class="pc__img-wrapper">
                                    <div class="swiper-container background-img js-swiper-slider"
                                        data-settings='{"resizeObserver": true}'>
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <a
                                                    href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                                    <img loading="lazy"
                                                        src="{{ $product->image
                                                            ? Storage::disk('s3')->url('uploads/products/' . $product->image)
                                                            : asset('uploads/book_placeholder.png') }}"
                                                        width="310" height="400" alt="{{ $product->name }}"
                                                        class="pc__img" />
                                                </a>
                                            </div>
                                            @if ($product->images)
                                                <div class="swiper-slide">
                                                    @foreach (explode(',', $product->images) as $gallery_image)
                                                        <a
                                                            href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                                            <img loading="lazy"
                                                                src="{{ Storage::disk('s3')->url('uploads/products/' . $gallery_image) }}"
                                                                width="310" height="400" alt="{{ $product->name }}"
                                                                class="pc__img" />
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
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
                                        <form name = "addtocart-form" method = "post"
                                            action = "{{ route('cart.add') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}" />
                                            <input type="hidden" name="quantity" value="1" />
                                            <input type="hidden" name="name" value="{{ $product->name }}" />
                                            <input type="hidden" name="price"
                                                value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}" />
                                            <button
                                                class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium"
                                                data-aside="cartDrawer" title="Add To Cart">Add To Cart</button>
                                        </form>
                                    @endif
                                </div>

                                <div class="pc__info position-relative">
                                    <p class="pc__category">{{ $product->category->name }}</p>
                                    <h6 class="pc__title"><a
                                            href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">{{ $product->name }}</a>
                                    </h6>
                                    <span class="text-secondary">by {{ $product->author->name }}</span>
                                    <div class="product-card__price d-flex">
                                        <span class="money price">
                                            @if ($product->sale_price)
                                                <s>{{ number_format($product->regular_price, 0, ',', ',') }}đ</s>
                                                {{ number_format($product->sale_price, 0, ',', ',') }}đ
                                            @else
                                                {{ number_format($product->regular_price, 0, ',', ',') }}đ
                                            @endif
                                        </span>
                                    </div>
                                    <div class="product-card__review d-flex align-items-center">
                                        <div class="reviews-group d-flex">
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
                                                            <mask id="half-mask-{{ $product->id }}-{{ $i }}">
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
                                                        <path fill="gold"
                                                            mask="url(#half-mask-{{ $product->id }}-{{ $i }})"
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
                                        <span class="reviews-note text-lowercase text-secondary ms-1">
                                            {{ $product->reviews_count }}
                                            {{ $product->reviews_count == 1 ? 'review' : 'reviews' }}
                                            @if ($product->average_rating > 0)
                                                ({{ number_format($product->average_rating, 1) }})
                                            @endif
                                        </span>
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

    <form id="frmfilter" method="GET" action="{{ route('search.results') }}">
        <input type="hidden" name="q" value="{{ $query }}" />
        <input type="hidden" name="page" value="{{ $products->currentPage() }}" />
        <input type="hidden" name="sort" id="sort" value="{{ $sort }}" />
        <input type="hidden" name="search_type" id="search_type" value="{{ $search_type }}" />
        <input type="hidden" name="authors" id="hdnAuthors" />
        <input type="hidden" name="categories" id="hdnCategories" />
        <input type="hidden" name="rating" id="hdnRating" />
        <input type="hidden" name="min" id="hdnMinPrice" value="{{ $min_price }}" />
        <input type="hidden" name="max" id="hdnMaxPrice" value="{{ $max_price }}" />
    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            // Sort dropdown change
            $("#orderby").on("change", function() {
                $("#sort").val($("#orderby option:selected").val());
                $("#frmfilter").submit();
            });

            // Search type radio buttons
            $("input[name='search_type']").on("change", function() {
                $("#search_type").val($(this).val());
                $("#frmfilter").submit();
            });

            // Sort radio buttons
            $("input[name='sort']").on("change", function() {
                $("#sort").val($(this).val());
                $("#frmfilter").submit();
            });

            // Authors filter
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

            // Categories filter
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

            // Rating filter
            $("input[name='rating']").on("change", function() {
                var ratings = "";
                $("input[name='rating']:checked").each(function() {
                    if (ratings == "") {
                        ratings += $(this).val();
                    } else {
                        ratings += "," + $(this).val();
                    }
                });
                $("#hdnRating").val(ratings);
                $("#frmfilter").submit();
            });

            // Price range slider
            $("[name='price_range']").on("slideStop", function() {
                var min = $(this).val().split(',')[0];
                var max = $(this).val().split(',')[1];
                $("#hdnMinPrice").val(min);
                $("#hdnMaxPrice").val(max);
                $("#frmfilter").submit();
            });
        });
    </script>
@endpush
