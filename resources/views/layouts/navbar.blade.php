<div class="header-mobile header_sticky">
    <div class="container d-flex align-items-center h-100">
        <a class="mobile-nav-activator d-block position-relative" href="#">
            <svg class="nav-icon" width="25" height="18" viewBox="0 0 25 18" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_nav" />
            </svg>
            <button class="btn-close-lg position-absolute top-0 start-0 w-100"></button>
        </a>

        <div class="logo">
            <a href="{{ route('home.index') }}">
                <img src="{{ asset('assets/images/bookstore.jpg') }}" alt="Uomo"
                    class="logo__image d-block" />
            </a>
        </div>

        <a href="#" class="header-tools__item header-tools__cart js-open-aside" data-aside="cartDrawer">
            <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_cart" />
            </svg>
            <span class="cart-amount d-block position-absolute js-cart-items-count">3</span>
        </a>
    </div>

    <nav
        class="header-mobile__navigation navigation d-flex flex-column w-100 position-absolute top-100 bg-body overflow-auto">
        <div class="container">
            <form action="#" method="GET" class="search-field position-relative mt-4 mb-3">
                <div class="position-relative">
                    <input class="search-field__input w-100 border rounded-1" type="text" name="search-keyword"
                        placeholder="Search products" />
                    <button class="btn-icon search-popup__submit pb-0 me-2" type="submit">
                        <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_search" />
                        </svg>
                    </button>
                    <button class="btn-icon btn-close-lg search-popup__reset pb-0 me-2" type="reset"></button>
                </div>

                <div class="position-absolute start-0 top-100 m-0 w-100">
                    <div class="search-result"></div>
                </div>
            </form>
        </div>

        <div class="container">
            <div class="overflow-hidden">
                <ul class="navigation__list list-unstyled position-relative">
                    <li class="navigation__item">
                        <a href="{{ route('home.index') }}" class="navigation__link">Home</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('shop.index') }}" class="navigation__link">Shop</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('cart.index') }}" class="navigation__link">Cart</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('home.aboutus') }}" class="navigation__link">About</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('home.contact') }}" class="navigation__link">Contact</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('free.products') }}" class="navigation__link">Free Products</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-top mt-auto pb-2">
            <div class="customer-links container mt-4 mb-2 pb-1">
                <svg class="d-inline-block align-middle" width="20" height="20" viewBox="0 0 20 20"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_user" />
                </svg>
                <span class="d-inline-block ms-2 text-uppercase align-middle fw-medium">My Account</span>
            </div>



            <ul class="container social-links list-unstyled d-flex flex-wrap mb-0">
                <li>
                    <a href="#" class="footer__social-link d-block ps-0">
                        <svg class="svg-icon svg-icon_facebook" width="9" height="15" viewBox="0 0 9 15"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_facebook" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="footer__social-link d-block">
                        <svg class="svg-icon svg-icon_twitter" width="14" height="13" viewBox="0 0 14 13"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_twitter" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="footer__social-link d-block">
                        <svg class="svg-icon svg-icon_instagram" width="14" height="13" viewBox="0 0 14 13"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_instagram" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="footer__social-link d-block">
                        <svg class="svg-icon svg-icon_youtube" width="16" height="11" viewBox="0 0 16 11"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.0117 1.8584C14.8477 1.20215 14.3281 0.682617 13.6992 0.518555C12.5234 0.19043 7.875 0.19043 7.875 0.19043C7.875 0.19043 3.19922 0.19043 2.02344 0.518555C1.39453 0.682617 0.875 1.20215 0.710938 1.8584C0.382812 3.00684 0.382812 5.46777 0.382812 5.46777C0.382812 5.46777 0.382812 7.90137 0.710938 9.07715C0.875 9.7334 1.39453 10.2256 2.02344 10.3896C3.19922 10.6904 7.875 10.6904 7.875 10.6904C7.875 10.6904 12.5234 10.6904 13.6992 10.3896C14.3281 10.2256 14.8477 9.7334 15.0117 9.07715C15.3398 7.90137 15.3398 5.46777 15.3398 5.46777C15.3398 5.46777 15.3398 3.00684 15.0117 1.8584ZM6.34375 7.68262V3.25293L10.2266 5.46777L6.34375 7.68262Z" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="footer__social-link d-block">
                        <svg class="svg-icon svg-icon_pinterest" width="14" height="15" viewBox="0 0 14 15"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_pinterest" />
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<header id="header" class="header header-fullwidth header-transparent-bg">
    <div class="container">
        <div class="header-desk header-desk_type_1">
            <div class="logo">
                <a href="{{ route('home.index') }}">
                    <img src="{{ asset('assets/images/bookstore.jpg') }}" alt="Uomo"
                        class="logo__image d-block" height="50" />
                </a>
            </div>

            <nav class="navigation">
                <ul class="navigation__list list-unstyled d-flex">
                    <li class="navigation__item">
                        <a href="{{ route('home.index') }}" class="navigation__link">Home</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('shop.index') }}" class="navigation__link">Shop</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('cart.index') }}" class="navigation__link">Cart</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('home.aboutus') }}" class="navigation__link">About</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('home.contact') }}" class="navigation__link">Contact</a>
                    </li>
                    <li class="navigation__item">
                        <a href="{{ route('free.products') }}" class="navigation__link">Free Products</a>
                    </li>
                </ul>
            </nav>

            <div class="header-tools d-flex align-items-center">
                <div class="header-tools__item hover-container">
                    <div class="js-hover__open position-relative">
                        <a class="js-search-popup search-field__actor" href="#">
                            <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_search" />
                            </svg>
                            <i class="btn-icon btn-close-lg"></i>
                        </a>
                    </div>

                    <div class="search-popup js-hidden-content">
                        <form action="{{ route('search.results') }}" method="GET" class="search-field container">
                            <p class="text-uppercase text-secondary fw-medium mb-4">What are you looking for?</p>
                            <div class="position-relative">
                                <input class="search-field__input search-popup__input w-100 fw-medium" type="text"
                                    name="q" placeholder="Search for books" id="search-input" />
                                <button class="btn-icon search-popup__submit" type="submit">
                                    <svg class="d-block" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <use href="#icon_search" />
                                    </svg>
                                </button>
                                <button class="btn-icon btn-close-lg search-popup__reset" type="reset"></button>
                                <!-- Microphone button for speech-to-text -->
                                <button type="button" id="mic-btn" class="btn-icon"
                                    style="position:absolute;right:25px;top:0px;background:none;border:none;display:flex;align-items:center;justify-content:center;">
                                    <svg width="24" height="24" viewBox="0 0 20 20" fill="none"
                                        stroke="currentColor" stroke-width="1" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="7" y="4" width="6" height="10" rx="3" />
                                        <path d="M10 17v-2" />
                                        <path d="M7 14a3 3 0 0 0 6 0" />
                                    </svg>
                                </button>
                                <select id="lang-select" style="position:absolute;right:70px;top:3px;">
                                    <option value="vi-VN">🇻🇳 VN</option>
                                    <option value="en-US">🇺🇸 EN</option>
                                </select>
                            </div>

                            <!-- Search Type Selector -->
                            <div class="mt-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <small class="text-muted">Search Type:</small>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search-type"
                                            id="search-hybrid" value="hybrid" checked>
                                        <label class="form-check-label" for="search-hybrid">
                                            <small>AI + Text</small>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search-type"
                                            id="search-vector" value="vector">
                                        <label class="form-check-label" for="search-vector">
                                            <small>AI Only</small>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="search-type"
                                            id="search-text" value="text">
                                        <label class="form-check-label" for="search-text">
                                            <small>Text Only</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="search-popup__results">
                                <ul id="box-content-search">
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>

                @guest
                    <div class="header-tools__item hover-container">
                        <a href="{{ route('login') }}" class="header-tools__item">
                            <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_user" />
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="header-tools__item hover-container">
                        <a href="{{ Auth::user()->usertype === 'ADM' ? route('admin.index') : route('user.index') }}"
                            class="header-tools__item">
                            <span class="pr-6px">{{ Auth::user()->name }}</span>
                            <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <use href="#icon_user" />
                            </svg>
                        </a>
                    </div>
                @endguest

                <a href="{{ route('wishlist.index') }}" class="header-tools__item header-tools__cart">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_heart" />
                    </svg>
                    @if (Cart::instance('wishlist')->content()->count() > 0)
                        <span
                            class="cart-amount d-block position-absolute js-cart-items-count">{{ Cart::instance('wishlist')->content()->count() }}</span>
                    @endif
                </a>

                <a href="{{ route('cart.index') }}" class="header-tools__item header-tools__cart">
                    <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_cart" />
                    </svg>
                    @if (Cart::instance('cart')->content()->count() > 0)
                        <span
                            class="cart-amount d-block position-absolute js-cart-items-count">{{ Cart::instance('cart')->content()->count() }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</header>
