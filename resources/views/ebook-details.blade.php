@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-md-1 pb-md-3"></div>
        <section class="product-single container">
            <div class="row">
                {{-- Ebook Cover --}}
                <div class="col-lg-7">
                    <div class="product-single__media">
                        <div class="product-single__image">
                            <div class="ebook-cover-container text-center">
                                {{-- <img loading="lazy" class="ebook-cover-image"
                                    src="{{ asset($ebook->cover_path ?? 'https://via.placeholder.com/400x600?text=No+Cover') }}" --}}
                                <img loading="lazy" class="ebook-cover-image"
                                    src="{{ $ebook->cover_path ? Storage::disk('s3')->url($ebook->cover_path) : 'https://via.placeholder.com/400x600?text=No+Cover' }}"
                                    alt="{{ $ebook->title }}"
                                    style="max-width: 100%; max-height: 600px; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);" />
                                <a data-fancybox="gallery" {{-- href="{{ asset($ebook->cover_path ?? 'https://via.placeholder.com/400x600?text=No+Cover') }}" --}}
                                    href="{{ $ebook->cover_path ? Storage::disk('s3')->url($ebook->cover_path) : 'https://via.placeholder.com/400x600?text=No+Cover' }}"
                                    data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <use href="#icon_zoom" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ebook Details --}}
                <div class="col-lg-5">
                    <div class="d-flex justify-content-between mb-4 pb-md-2">
                        <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                            <a href="{{ route('home.index') }}"
                                class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
                            <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                            <a href="{{ route('free.products') }}"
                                class="menu-link menu-link_us-s text-uppercase fw-medium">Free Ebooks</a>
                        </div>
                    </div>

                    <h1 class="product-single__name">{{ $ebook->title }}</h1>

                    <div class="ebook-author mb-3">
                        <span class="text-muted">by</span> <strong>{{ $ebook->author }}</strong>
                    </div>

                    <div class="ebook-category mb-3">
                        <span class="badge bg-primary">{{ $ebook->category->name ?? 'Uncategorized' }}</span>
                        <span class="badge bg-secondary ms-2">{{ strtoupper($ebook->format) }}</span>
                    </div>

                    <div class="product-single__price">
                        <span class="current-price text-red h4">FREE</span>
                    </div>

                    {{-- @if ($ebook->description)
                        <div class="product-single__short-desc mb-4">
                            <p>{{ $ebook->description }}</p>
                        </div>
                    @endif --}}

                    {{-- Action Buttons --}}
                    <div class="ebook-actions mb-4">
                        <div class="d-grid gap-2 d-md-flex">
                            <button class="btn btn-primary btn-lg flex-fill"
                                onclick="window.open('{{ route('epub.reader', ['id' => $ebook->id]) }}', '_blank')">
                                <i class="fas fa-book-open me-2"></i> Read Online
                            </button>

                            {{-- @if ($ebook->file_path && file_exists(public_path($ebook->file_path)))
                                <a href="{{ asset($ebook->file_path) }}" class="btn btn-outline-primary btn-lg flex-fill" --}}
                            @if ($ebook->file_path && Storage::disk('s3')->exists($ebook->file_path))
                                <a href="{{ Storage::disk('s3')->url($ebook->file_path) }}"
                                    class="btn btn-outline-primary btn-lg flex-fill"
                                    download="{{ $ebook->title }}.{{ $ebook->format }}">
                                    <i class="fas fa-download me-2"></i> Download
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Audiobook Section (if available) --}}
                    @if ($ebook->chapters && $ebook->chapters->whereNotNull('audio_path')->count() > 0)
                        <div class="audiobook-section mb-4 p-3 bg-light rounded">
                            <h5 class="mb-3">
                                <i class="fas fa-headphones me-2"></i> Also Available as Audiobook
                            </h5>
                            <p class="text-muted small mb-3">
                                {{ $ebook->chapters->whereNotNull('audio_path')->count() }} chapters available
                            </p>
                            <button class="btn btn-success"
                                onclick="window.open('{{ route('epub.reader', ['id' => $ebook->id]) }}', '_blank')">
                                <i class="fas fa-play me-2"></i> Listen Now
                            </button>
                        </div>
                    @endif

                    {{-- Meta Information --}}
                    <div class="product-single__meta-info">
                        <div class="meta-item">
                            <label>Format:</label>
                            <span>{{ strtoupper($ebook->format) }}</span>
                        </div>
                        <div class="meta-item">
                            <label>Category:</label>
                            <span>{{ $ebook->category->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div class="meta-item">
                            <label>Author:</label>
                            <span>{{ $ebook->author }}</span>
                        </div>
                        <div class="meta-item">
                            <label>Added:</label>
                            <span>{{ $ebook->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ebook Description Tab --}}
            @if ($ebook->description)
                <div class="product-single__details-tab mt-5">
                    <ul class="nav nav-tabs" id="ebookTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
                                href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">
                                Description
                            </a>
                        </li>
                        @if ($ebook->chapters && $ebook->chapters->count() > 0)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link nav-link_underscore" id="tab-chapters-tab" data-bs-toggle="tab"
                                    href="#tab-chapters" role="tab" aria-controls="tab-chapters" aria-selected="false">
                                    Chapters ({{ $ebook->chapters->count() }})
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-description" role="tabpanel"
                            aria-labelledby="tab-description-tab">
                            <div class="product-single__description">
                                <p>{{ $ebook->description }}</p>
                            </div>
                        </div>

                        @if ($ebook->chapters && $ebook->chapters->count() > 0)
                            <div class="tab-pane fade" id="tab-chapters" role="tabpanel" aria-labelledby="tab-chapters-tab">
                                <div class="chapters-list">
                                    <h5>Available Chapters</h5>
                                    <div class="list-group">
                                        @foreach ($ebook->chapters->sortBy('index') as $chapter)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">
                                                        Chapter {{ $chapter->index }}
                                                        @if ($chapter->title)
                                                            - {{ $chapter->title }}
                                                        @endif
                                                    </h6>
                                                </div>
                                                @if ($chapter->audio_path)
                                                    <span class="badge bg-success rounded-pill">
                                                        <i class="fas fa-headphones"></i> Audio Available
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        {{-- Related Ebooks Section --}}
        @if ($related_ebooks && $related_ebooks->count() > 0)
            <section class="products-carousel container mt-5">
                <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Ebooks</strong></h2>

                <div class="row">
                    @foreach ($related_ebooks as $related_ebook)
                        <div class="col-6 col-md-3 mb-4">
                            <div class="card h-100 ebook-card">
                                <div class="card-body d-flex flex-column">
                                    <div class="ebook-cover mb-3 text-center">
                                        {{-- <img src="{{ asset($related_ebook->cover_path ?? 'https://via.placeholder.com/150x200') }}" --}}
                                        <img src="{{ $related_ebook->cover_path ? Storage::disk('s3')->url($related_ebook->cover_path) : 'https://via.placeholder.com/150x200' }}"
                                            alt="{{ $related_ebook->title }}" class="img-fluid rounded"
                                            style="max-height: 150px; object-fit: cover;">
                                    </div>
                                    <h6 class="ebook-title">{{ Str::limit($related_ebook->title, 40) }}</h6>
                                    <p class="text-muted small mb-2">by {{ $related_ebook->author }}</p>
                                    <p class="text-secondary small mb-3">
                                        {{ $related_ebook->category->name ?? 'Uncategorized' }}</p>
                                    <a href="{{ route('ebook.details', $related_ebook->id) }}"
                                        class="btn btn-outline-primary btn-sm mt-auto">
                                        <i class="fas fa-book-open me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
@endsection

@push('styles')
    <style>
        .ebook-cover-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .ebook-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .ebook-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .meta-item {
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .meta-item:last-child {
            border-bottom: none;
        }

        .meta-item label {
            font-weight: 600;
            margin-right: 0.5rem;
            min-width: 80px;
            display: inline-block;
        }

        .ebook-actions .btn {
            border-radius: 8px;
            font-weight: 600;
        }

        .audiobook-section {
            border-left: 4px solid #28a745;
        }

        .chapters-list .list-group-item {
            border: 1px solid #dee2e6;
            margin-bottom: 0.25rem;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .ebook-actions .d-md-flex {
                flex-direction: column;
            }

            .ebook-actions .btn {
                margin-bottom: 0.5rem;
            }
        }
    </style>
@endpush
