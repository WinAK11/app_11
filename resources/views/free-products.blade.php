@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <section class="shop-main container d-flex pt-4 pt-xl-5">
            {{-- Sidebar Filter --}}
            <div class="shop-sidebar side-sticky bg-body" id="ebookFilter">
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
                                Ebook Categories
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
                                            <span class="text-right float-end">{{ $category->ebooks->count() }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="shop-list flex-grow-1">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between mb-4 pb-md-2">
                    <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                        <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
                        <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                        <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">Free Ebooks</a>
                    </div>

                    <div
                        class="shop-acs d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">
                        <select class="shop-acs__select form-select w-auto border-0 py-0 order-1 order-md-0"
                            aria-label="Sort Items" name="orderby" id="orderby">
                            <option value="-1" {{ $order == -1 ? 'selected' : '' }}>Title (A-Z)</option>
                            <option value="1" {{ $order == 1 ? 'selected' : '' }}>Title (Z-A)</option>
                            <option value="2" {{ $order == 2 ? 'selected' : '' }}>Author (A-Z)</option>
                            <option value="3" {{ $order == 3 ? 'selected' : '' }}>Author (Z-A)</option>
                        </select>

                        <div class="shop-asc__seprator mx-3 bg-light d-none d-md-block order-md-0"></div>

                        <div class="col-size align-items-center order-1 d-none d-lg-flex">
                            <span class="text-uppercase fw-medium me-2">View</span>
                            <button class="btn-link fw-medium me-2 js-cols-size" data-target="ebooks-grid"
                                data-cols="2">2</button>
                            <button class="btn-link fw-medium me-2 js-cols-size" data-target="ebooks-grid"
                                data-cols="3">3</button>
                            <button class="btn-link fw-medium js-cols-size" data-target="ebooks-grid"
                                data-cols="4">4</button>
                        </div>

                        <div class="shop-filter d-flex align-items-center order-0 order-md-3 d-lg-none">
                            <button class="btn-link btn-link_f d-flex align-items-center ps-0 js-open-aside"
                                data-aside="ebookFilter">
                                <svg class="d-inline-block align-middle me-2" width="14" height="10"
                                    viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <use href="#icon_filter" />
                                </svg>
                                <span class="text-uppercase fw-medium d-inline-block align-middle">Filter</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Page Title --}}
                <h4 class="mb-4">📚 Free Ebooks</h4>

                {{-- Ebooks Grid --}}
                <div class="row row-cols-2 row-cols-md-3" id="ebooks-grid">
                    @foreach ($ebooks as $ebook)
                        <div class="col mb-3">
                            <div class="card h-100 audiobook-card">
                                <div class="card-body d-flex flex-column">
                                    {{-- Make cover image clickable --}}
                                    <div class="audiobook-cover mb-3 text-center">
                                        <a href="{{ route('ebook.details', $ebook->id) }}">
                                            {{-- <img src="{{ asset($ebook->cover_path ?? 'https://via.placeholder.com/150x200') }}" --}}
                                            <img src="{{ $ebook->cover_path ? Storage::disk('s3')->url($ebook->cover_path) : 'https://via.placeholder.com/150x200' }}"
                                                alt="{{ $ebook->title }}" class="img-fluid rounded"
                                                style="max-height: 200px; object-fit: cover;">
                                        </a>
                                    </div>

                                    {{-- Make title clickable --}}
                                    <h5 class="audiobook-title">
                                        <a href="{{ route('ebook.details', $ebook->id) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $ebook->title }}
                                        </a>
                                    </h5>

                                    <p class="text-muted mb-2">by {{ $ebook->author }}</p>
                                    <p class="text-secondary small mb-3">
                                        {{ $ebook->category->name ?? 'Uncategorized' }}
                                    </p>

                                    <button class="btn btn-primary btn-sm mt-auto play-audiobook-btn"
                                        onclick="window.open('{{ route('epub.reader', ['id' => $ebook->id]) }}', '_blank')">
                                        <i class="fas fa-book-open me-1"></i> Read Ebook
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- <button class="btn btn-primary btn-sm mt-auto play-audiobook-btn"
                    onclick="window.open('{{ route('epub.reader', ['id' => $ebook->id]) }}', '_blank')">
                    <i class="fas fa-book-open me-1"></i> Read Ebook
                </button> --}}

                {{-- Pagination --}}
                @if ($ebooks->hasPages())
                    <div class="divider"></div>
                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                        {{ $ebooks->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </main>

    {{-- Hidden Filter Form --}}
    <form id="frmfilter" method="GET" action="{{ route('free.products') }}">
        <input type="hidden" name="page" value="{{ $ebooks->currentPage() }}" />
        <input type="hidden" name="order" id="order" value="{{ $order }}" />
        <input type="hidden" name="categories" id="hdnCategories" />
    </form>
@endsection

{{-- Additional CSS for audiobook cards --}}
@push('styles')
    <style>
        .audiobook-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .audiobook-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .play-audiobook-btn {
            transition: all 0.3s ease;
        }

        .play-audiobook-btn:hover {
            transform: scale(1.05);
        }

        .audiobook-cover img {
            transition: transform 0.3s ease;
        }

        .audiobook-card:hover .audiobook-cover img {
            transform: scale(1.05);
        }

        /* Ensure main content has enough bottom padding for the fixed player */
        .shop-main {
            margin-bottom: 100px;
        }

        /* Shop sidebar styling */
        .shop-sidebar {
            width: 300px;
            margin-right: 2rem;
        }

        @media (max-width: 991px) {
            .shop-sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 300px;
                height: 100vh;
                z-index: 1050;
                transition: left 0.3s ease;
                overflow-y: auto;
            }

            .shop-sidebar.active {
                left: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            // Order by functionality
            $("#orderby").on("change", function() {
                $("#order").val($("#orderby option:selected").val());
                $("#frmfilter").submit();
            });

            // Category filter functionality
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

            // Grid column size functionality
            $(".js-cols-size").on("click", function(e) {
                e.preventDefault();
                var cols = $(this).data("cols");
                var target = $(this).data("target");

                $("#" + target).removeClass("row-cols-2 row-cols-md-3 row-cols-lg-4")
                    .addClass("row-cols-" + cols);

                // Update active state
                $(".js-cols-size").removeClass("active");
                $(this).addClass("active");
            });

            // Mobile filter toggle
            $(".js-open-aside").on("click", function() {
                var aside = $(this).data("aside");
                $("#" + aside).addClass("active");
            });

            $(".js-close-aside").on("click", function() {
                $(this).closest(".shop-sidebar").removeClass("active");
            });
        });
    </script>
@endpush
