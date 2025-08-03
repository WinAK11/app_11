@extends('layouts.app')
@section('content')
    <main class="pt-90">
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
                                    <p class="pc__category">{{ $ebook->category->name }}</p>
                                    <h6 class="pc__title"><a href="#">{{ $ebook->title }}</a></h6>
                                    <span class="text-secondary">by {{ $ebook->author }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


                <h4>🎵 Audiobook Samples</h4>
                <div class="row">
                    {{-- Sample audiobook cards with your test file --}}
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 audiobook-card">
                            <div class="card-body d-flex flex-column">
                                <div class="audiobook-cover mb-3 text-center">
                                    <img src="https://placehold.co/150x200?text=Hello+World" alt="Sample Audiobook 1"
                                        class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                                </div>
                                <h5 class="audiobook-title">Sample Audiobook 1</h5>
                                <p class="text-muted mb-2">by Sample Author</p>
                                <p class="text-secondary small mb-3">Mystery & Thriller</p>

                                {{-- Play button for the global player --}}
                                <button class="btn btn-primary btn-sm mt-auto play-audiobook-btn" data-book-id="1"
                                    onclick="playAudiobook(1, 'Sample Audiobook 1', 'Sample Author', 'https://placehold.co/150x200?text=Hello+World', '{{ asset('uploads/audiobook/audio-test.mp3') }}')">
                                    <i class="fas fa-play me-1"></i> Play Sample
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Additional sample cards --}}
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 audiobook-card">
                            <div class="card-body d-flex flex-column">
                                <div class="audiobook-cover mb-3 text-center">
                                    <img src="https://placehold.co/150x200?text=Hello+World" alt="Sample Audiobook 2"
                                        class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                                </div>
                                <h5 class="audiobook-title">Sample Audiobook 2</h5>
                                <p class="text-muted mb-2">by Another Author</p>
                                <p class="text-secondary small mb-3">Romance</p>

                                <button class="btn btn-primary btn-sm mt-auto play-audiobook-btn" data-book-id="2"
                                    onclick="playAudiobook(2, 'Sample Audiobook 2', 'Another Author', 'https://placehold.co/150x200?text=Hello+World', '{{ asset('uploads/audiobook/audio-test.mp3') }}')">
                                    <i class="fas fa-play me-1"></i> Play Sample
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card h-100 audiobook-card">
                            <div class="card-body d-flex flex-column">
                                <div class="audiobook-cover mb-3 text-center">
                                    <img src="https://placehold.co/150x200?text=Hello+World" alt="Sample Audiobook 3"
                                        class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                                </div>
                                <h5 class="audiobook-title">Sample Audiobook 3</h5>
                                <p class="text-muted mb-2">by Third Author</p>
                                <p class="text-secondary small mb-3">Science Fiction</p>

                                <button class="btn btn-primary btn-sm mt-auto play-audiobook-btn" data-book-id="3"
                                    onclick="playAudiobook(3, 'Sample Audiobook 3', 'Third Author', 'https://placehold.co/150x200?text=Hello+World', '{{ asset('uploads/audiobook/audio-test.mp3') }}')">
                                    <i class="fas fa-play me-1"></i> Play Sample
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic audiobooks section (when you have actual data) --}}
                    {{-- Uncomment and modify this when you have audiobooks from database --}}
                    {{--
                @foreach ($audiobooks as $audiobook)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 audiobook-card">
                            <div class="card-body d-flex flex-column">
                                <div class="audiobook-cover mb-3 text-center">
                                    <img src="{{ asset($audiobook->cover_path ?? 'https://via.placeholder.com/150x200') }}"
                                         alt="{{ $audiobook->title }}"
                                         class="img-fluid rounded"
                                         style="max-height: 200px; object-fit: cover;">
                                </div>
                                <h5 class="audiobook-title">{{ $audiobook->title }}</h5>
                                <p class="text-muted mb-2">by {{ $audiobook->author }}</p>
                                <p class="text-secondary small mb-3">{{ $audiobook->category->name ?? 'Uncategorized' }}</p>

                                <button
                                    class="btn btn-primary btn-sm mt-auto play-audiobook-btn"
                                    onclick="playAudiobook({
                                        id: {{ $audiobook->id }},
                                        title: '{{ addslashes($audiobook->title) }}',
                                        artist: '{{ addslashes($audiobook->author) }}',
                                        cover_url: '{{ asset($audiobook->cover_path ?? 'https://via.placeholder.com/150x200') }}',
                                        audio_url: '{{ asset($audiobook->audio_path) }}'
                                    })"
                                >
                                    <i class="fas fa-play me-1"></i> Play Sample
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
                --}}
                </div>
            </div>
        </section>

        {{-- Include the Livewire audiobook player --}}
        @livewire('audiobook-player')
    </main>

    {{-- JavaScript to handle audiobook playback --}}
@endsection

@push('scripts')
    <script>
        function playAudiobook(id, title, artist, cover_url, audio_url) {
            // Dispatch the Livewire event to start playing
            Livewire.dispatch('playBook', {
                id: id,
                title: title,
                artist: artist,
                cover_url: cover_url,
                audio_url: audio_url
            });

            // Optional: Add visual feedback
            const clickedButton = event.target.closest('.play-audiobook-btn');
            if (clickedButton) {
                // Remove active state from all buttons
                document.querySelectorAll('.play-audiobook-btn').forEach(btn => {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.innerHTML = '<i class="fas fa-play me-1"></i> Play Sample';
                });

                // Add active state to clicked button
                clickedButton.classList.remove('btn-primary');
                clickedButton.classList.add('btn-success');
                clickedButton.innerHTML = '<i class="fas fa-headphones me-1"></i> Now Playing';
            }
        }

        // Listen for player state changes to update button states
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('bookStarted', (event) => {
                console.log('Audiobook started playing:', event);
            });
        });
    </script>
@endpush

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
    </style>
@endpush
