@extends('layouts.app')
@section('content')
    <section class="shop-main container d-flex pt-4 pt-xl-5">
        <div class="shop-list flex-grow-1">
            <h4>📚 eBook Samples</h4>
            <div class="products-grid row row-cols-2 row-cols-md-3" id="products-grid">
                @foreach ($ebooks as $ebook)
                    <div class="product-card-wrapper">
                        <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                            <div class="pc__img-wrapper">
                                <div class="swiper-container background-img js-swiper-slider"
                                    data-settings='{"resizeObserver": true}'>
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <a href="#">
                                                {{-- <img loading="lazy"
                                                    src="{{ asset('uploads/products') }}/{{ $ebook->image }}" width="310"
                                                    height="400" alt="{{ $ebook->name }}" class="pc__img" /> --}}
                                                <img src="{{ asset($ebook->cover_path) }}" alt="cover" width="310"
                                                    height="400">
                                            </a>
                                        </div>
                                        <div class="swiper-slide">
                                            @foreach (explode(',', $ebook->images) as $gallery_image)
                                                <a href="#"><img loading="lazy"
                                                        src="{{ asset('uploads/products') }}/{{ $gallery_image }}"
                                                        width="310" height="400" alt="{{ $ebook->name }}"
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
                                    <button
                                        class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium"
                                        title="Add To Cart"
                                        onclick="window.open('{{ route('epub.reader', ['id' => $ebook->id]) }}', '_blank')">
                                        Read Ebook
                                    </button>
                                </div>
                            </div>

                            <div class="pc__info position-relative">
                                <p class="pc__category">{{ $ebook->category }}</p>
                                <h6 class="pc__title"><a href="#">{{ $ebook->title }}</a>
                                </h6>
                                <span class="text-secondary">by {{ $ebook->author }}</span>

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="row mb-5">
                @foreach ($ebooks as $ebook)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5>{{ $ebook->title }}</h5>
                                {{-- <a href="#" target="_blank" class="btn btn-outline-primary btn-sm mt-2">Read Sample</a> --}}
                                <a href="{{ route('epub.reader', ['id' => $ebook->id]) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm mt-2">
                                    Read Sample
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <h4>🎵 Audiobook Samples</h4>
            <div class="row">
                {{-- @foreach ($audiobooks as $product) --}}
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            {{-- <h5>{{ $product->name }}</h5> --}}
                            <audio controls class="w-100 mt-2">
                                <source src="#" type="audio/mpeg">
                            </audio>
                        </div>
                    </div>
                </div>
                {{-- @endforeach --}}
            </div>
        </div>
    </section>
@endsection
