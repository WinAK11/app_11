@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="contact-us container">
            <div class="mw-930">
                <h2 class="page-title">About US</h2>
            </div>
            {{-- class="w-100 h-auto d-block" --}}
            <div class="about-us__content pb-5 mb-5">
                <p class="mb-5">
                    <img loading="lazy" class="w-100 h-auto d-block"
                        src="{{ asset('assets/images/about/aboutus.png') }}" width="940" height="366"
                        alt="" style="max-width: 70%; margin-left: 15%;
                " />

                </p>
                <div class="mw-930">
                    <h3 class="mb-4">OUR STORY</h3>
                    <p class="fs-6 fw-medium mb-4">We’re a young and passionate team of readers, dreamers, and lifelong
                        learners who believe that books are more than just paper and ink — they’re gateways to new ideas,
                        emotions, and possibilities.</p>
                    <br class="mb-4">We started small — just a few titles, a few clicks, and a lot of heart.
                    Today, we’re growing fast, fueled by curiosity, creativity, and the love of good books.<br />
                    Whether you're into thrillers, self-help, romance, or academic reads, we’re here to help
                    you discover books that move you, challenge you, and make you think.<br />
                    This is just the beginning. And we’re so excited to have you on this journey with us.</p>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-3">Our Mission</h5>
                            <p class="mb-3"> To inspire a love of reading and make books accessible to everyone,
                                everywhere.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Our Vision</h5>
                            <p class="mb-3">we envision a world where books are a part of everyone’s everyday life — not
                                just for learning, but for dreaming, growing, and connecting.</p>
                        </div>
                    </div>
                </div>
                <div class="mw-930 d-lg-flex align-items-lg-center">
                    <div class="image-wrapper col-lg-6">
                        <img class="h-auto" loading="lazy" src="{{ asset('assets/images/about/about-1.png') }}"
                            width="450" height="500" alt="">
                    </div>
                    <div class="content-wrapper col-lg-6 px-lg-4">
                        <h5 class="mb-3">The Company</h5>
                        <p>We’re a young and newly launched online bookstore, with a simple goal:

                            to make books easier to discover and more exciting to read.<br />
                            Based in Viet Name, we are a young startup with big dreams — combining love for literature with
                            the power of technology to create a smarter, smoother, and more enjoyable book-buying
                            experience. <br />
                            While we may be small now, we’re growing fast — one happy reader at a time.

                            Driven by curiosity, powered by innovation, and inspired by our community, we are here to
                            reimagine what a modern bookstore can be.</p>
                    </div>
                </div>
            </div>
        </section>


    </main>
@endsection
