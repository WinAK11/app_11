<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="{{ asset('css/epub-reader.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
    <script src="https://unpkg.com/epubjs/dist/epub.min.js"></script>
    <title>{{ $ebook->title }} Reader</title>

    <!-- Include EPUB.js -->
    <script src="https://unpkg.com/epubjs/dist/epub.min.js"></script>

</head>

<body>
    <article class="book-reader">
        <input type="checkbox" id="invert" />
        <input type="checkbox" id="fullscreen" />
        <label for="invert"></label>
        <label for="fullscreen"></label>

        <header>
            <a href="#home">Home</a>
            <h1><a href="#">{{ $ebook->title }}</a></h1>

            <!-- Audio Toggle Button -->
            <button class="audio-toggle" id="audioToggle" onclick="toggleAudioPlayer()">
                <i class="fas fa-headphones"></i>
                <span>Listen</span>
            </button>
        </header>

        <nav>
            <ul>
                <li><a href="#" id="prev">previous</a></li>
                <li>
                    <select id="toc-select">
                        <option disabled selected>📖 Table of Contents</option>
                    </select>
                </li>
                <li><a href="#" id="next">next</a></li>
            </ul>
        </nav>

        <section>
            <div id="viewer"></div>
        </section>
    </article>

    <!-- Audio Player -->
    <div class="audio-player" id="audioPlayer">
        <div class="audio-controls">
            <div class="audio-info">
                <img src="{{ asset($ebook->cover_path ?? 'https://via.placeholder.com/40x40') }}" alt="Cover"
                    class="audio-cover">
                <div class="audio-details">
                    <p class="audio-title">{{ $ebook->title }}</p>
                    <p class="audio-author">by {{ $ebook->author }}</p>
                </div>
            </div>

            <div class="playback-controls">
                <button class="control-btn" onclick="skipBackward()">
                    <i class="fas fa-backward"></i>
                </button>
                <button class="control-btn play-pause-btn" onclick="togglePlayback()">
                    <i class="fas fa-play" id="playIcon"></i>
                </button>
                <button class="control-btn" onclick="skipForward()">
                    <i class="fas fa-forward"></i>
                </button>
            </div>

            <div class="progress-container">
                <span class="time" id="currentTime">0:00</span>
                <div class="progress-bar" onclick="seekTo(event)">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <span class="time" id="totalTime">0:00</span>
            </div>

            <div class="volume-control">
                <i class="fas fa-volume-up"></i>
                <div class="volume-bar" onclick="setVolume(event)">
                    <div class="volume-fill" id="volumeFill"></div>
                </div>
            </div>

            <button class="close-audio" onclick="closeAudioPlayer()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Hidden audio element -->
    <audio id="audioElement" preload="metadata" style="display: none;">
        <source src="{{ asset('uploads/audiobook/audio-test.mp3') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <script>
        // Audio Player State - Initialize first
        let audioPlayer = {
            audio: null,
            isPlaying: false,
            volume: 0.7,
            isVisible: false
        };

        // Initialize the book
        // const book = ePub("/uploads/ebooks/mobydick.epub");
        const book = ePub("{{ asset($ebook->file_path) }}");
        const rendition = book.renderTo("viewer", {
            width: "100%",
            height: "100%",
            allowScriptedContent: true,
        });

        // Load and populate the Table of Contents dropdown
        book.loaded.navigation.then((toc) => {
            const tocSelect = document.getElementById("toc-select");

            toc.toc.forEach((item) => {
                const option = document.createElement("option");
                const label = item.label.length > 50 ?
                    item.label.slice(0, 47).trim() + "..." :
                    item.label;

                option.value = item.href;
                option.textContent = label;
                tocSelect.appendChild(option);
            });

            // Handle TOC selection change
            tocSelect.addEventListener("change", (e) => {
                const href = e.target.value;
                rendition.display(href);
            });
        });

        rendition.display();

        let currentTheme = "light";

        // Inject theme styles into each EPUB content iframe when it's loaded
        rendition.hooks.content.register(function(contents) {
            const doc = contents.document;

            const style = doc.createElement("style");
            style.id = "theme-style";
            doc.head.appendChild(style);

            applyTheme(currentTheme, style, doc);
        });

        // Handle checkbox toggle
        const invertToggle = document.getElementById("invert");

        invertToggle.addEventListener("change", (e) => {
            currentTheme = e.target.checked ? "dark" : "light";

            rendition.getContents().forEach((contents) => {
                const doc = contents.document;
                const style = doc.getElementById("theme-style");
                applyTheme(currentTheme, style, doc);
            });

        });

        // Apply theme styles inside the book (iframe content)
        function applyTheme(theme, styleElement, doc) {
            if (theme === "dark") {
                styleElement.textContent = `
            body {
                background: #121212 !important;
                color: #e0e0e0 !important;
            }
            a { color: #90caf9 !important; }
        `;
            } else {
                styleElement.textContent = `
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            a { color: #007bff !important; }
        `;
            }
        }

        const fullscreenToggle = document.getElementById("fullscreen");

        fullscreenToggle.addEventListener("change", (e) => {
            const flowMode = e.target.checked ? "scrolled" : "paginated";

            // Reconfigure the rendition
            rendition.flow(flowMode);

            // Optionally resize to fit screen changes
            rendition.resize();
        });

        // Navigation
        document.getElementById("prev").addEventListener("click", (e) => {
            e.preventDefault();
            rendition.prev();
        });

        document.getElementById("next").addEventListener("click", (e) => {
            e.preventDefault();
            rendition.next();
        });

        // Table of Contents
        document.getElementById("toc").addEventListener("click", (e) => {
            e.preventDefault();
            book.loaded.navigation.then((toc) => {
                const tocList = toc.toc.map(item =>
                    `<li><a href="#" data-href="${item.href}">${item.label}</a></li>`).join("");
                const tocWindow = window.open("", "Table of Contents", "width=300,height=600");
                tocWindow.document.write(`<ul>${tocList}</ul>`);
                tocWindow.document.querySelectorAll('a[data-href]').forEach(link => {
                    link.addEventListener("click", (ev) => {
                        ev.preventDefault();
                        rendition.display(link.dataset.href);
                        tocWindow.close();
                    });
                });
            });
        });

        // Audio Player Functions

        function initAudio() {
            console.log('Initializing audio...');
            audioPlayer.audio = document.getElementById('audioElement');
            if (!audioPlayer.audio) {
                console.error('Audio element not found');
                return false;
            }

            console.log('Audio element found:', audioPlayer.audio);
            console.log('Audio source:', audioPlayer.audio.src);

            // Set initial volume
            audioPlayer.audio.volume = audioPlayer.volume;
            updateVolumeDisplay();

            // Audio event listeners
            audioPlayer.audio.addEventListener('loadedmetadata', () => {
                console.log('Audio metadata loaded, duration:', audioPlayer.audio.duration);
                const totalTimeEl = document.getElementById('totalTime');
                if (totalTimeEl) {
                    totalTimeEl.textContent = formatTime(audioPlayer.audio.duration);
                }
            });

            audioPlayer.audio.addEventListener('timeupdate', () => {
                updateProgress();
            });

            audioPlayer.audio.addEventListener('ended', () => {
                console.log('Audio ended');
                audioPlayer.isPlaying = false;
                updatePlayButton();
            });

            audioPlayer.audio.addEventListener('play', () => {
                console.log('Audio started playing');
                audioPlayer.isPlaying = true;
                updatePlayButton();
            });

            audioPlayer.audio.addEventListener('pause', () => {
                console.log('Audio paused');
                audioPlayer.isPlaying = false;
                updatePlayButton();
            });

            audioPlayer.audio.addEventListener('error', (e) => {
                console.error('Audio error:', e);
                console.error('Audio error details:', audioPlayer.audio.error);
            });

            audioPlayer.audio.addEventListener('canplay', () => {
                console.log('Audio can play');
            });

            audioPlayer.audio.addEventListener('loadstart', () => {
                console.log('Audio loading started');
            });

            // Try to load the audio
            audioPlayer.audio.load();
            console.log('Audio initialized successfully');
            return true;
        }

        function toggleAudioPlayer() {
            console.log('Toggle audio player called');

            // Initialize audio if not already done
            if (!audioPlayer.audio) {
                console.log('Audio not initialized, initializing now...');
                if (!initAudio()) {
                    console.error('Failed to initialize audio');
                    return;
                }
            }

            const player = document.getElementById('audioPlayer');
            const toggle = document.getElementById('audioToggle');
            const bookReader = document.querySelector('.book-reader');

            if (audioPlayer.isVisible) {
                player.classList.remove('show');
                bookReader.classList.remove('with-audio');
                audioPlayer.isVisible = false;
                toggle.classList.remove('playing');
                console.log('Audio player hidden');
            } else {
                player.classList.add('show');
                bookReader.classList.add('with-audio');
                audioPlayer.isVisible = true;
                if (audioPlayer.isPlaying) {
                    toggle.classList.add('playing');
                }
                console.log('Audio player shown');
            }
        }

        function closeAudioPlayer() {
            const player = document.getElementById('audioPlayer');
            const toggle = document.getElementById('audioToggle');
            const bookReader = document.querySelector('.book-reader');

            player.classList.remove('show');
            bookReader.classList.remove('with-audio');
            audioPlayer.isVisible = false;
            toggle.classList.remove('playing');

            // Pause audio when closing
            if (audioPlayer.audio && !audioPlayer.audio.paused) {
                audioPlayer.audio.pause();
            }
        }

        function togglePlayback() {
            console.log('Toggle playback called');

            if (!audioPlayer.audio) {
                console.log('Audio not initialized, initializing now...');
                if (!initAudio()) {
                    console.error('Audio initialization failed');
                    return;
                }
            }

            console.log('Current audio state - playing:', audioPlayer.isPlaying, 'paused:', audioPlayer.audio.paused);

            if (audioPlayer.isPlaying || !audioPlayer.audio.paused) {
                console.log('Pausing audio');
                audioPlayer.audio.pause();
            } else {
                console.log('Attempting to play audio');
                audioPlayer.audio.play().then(() => {
                    console.log('Audio play successful');
                }).catch(e => {
                    console.error('Play prevented:', e);
                    alert(
                        'Audio could not be played. Please check if the file exists at: uploads/audiobook/audio-test.mp3'
                        );
                });
            }
        }

        function skipForward() {
            if (!audioPlayer.audio) return;
            audioPlayer.audio.currentTime = Math.min(audioPlayer.audio.currentTime + 30, audioPlayer.audio.duration);
        }

        function skipBackward() {
            if (!audioPlayer.audio) return;
            audioPlayer.audio.currentTime = Math.max(audioPlayer.audio.currentTime - 30, 0);
        }

        function seekTo(event) {
            if (!audioPlayer.audio) return;

            const progressBar = event.currentTarget;
            const rect = progressBar.getBoundingClientRect();
            const clickPosition = event.clientX - rect.left;
            const percentage = (clickPosition / rect.width) * 100;

            audioPlayer.audio.currentTime = (percentage / 100) * audioPlayer.audio.duration;
        }

        function setVolume(event) {
            if (!audioPlayer.audio) return;

            const volumeBar = event.currentTarget;
            const rect = volumeBar.getBoundingClientRect();
            const clickPosition = event.clientX - rect.left;
            const percentage = (clickPosition / rect.width) * 100;

            audioPlayer.volume = percentage / 100;
            audioPlayer.audio.volume = audioPlayer.volume;
            updateVolumeDisplay();
        }

        function updateProgress() {
            if (!audioPlayer.audio) return;

            const progress = (audioPlayer.audio.currentTime / audioPlayer.audio.duration) * 100;
            const progressFill = document.getElementById('progressFill');
            const currentTimeEl = document.getElementById('currentTime');

            if (progressFill) {
                progressFill.style.width = progress + '%';
            }
            if (currentTimeEl) {
                currentTimeEl.textContent = formatTime(audioPlayer.audio.currentTime);
            }
        }

        function updatePlayButton() {
            const icon = document.getElementById('playIcon');
            const toggle = document.getElementById('audioToggle');

            if (audioPlayer.isPlaying) {
                if (icon) icon.className = 'fas fa-pause';
                if (toggle && audioPlayer.isVisible) {
                    toggle.classList.add('playing');
                }
            } else {
                if (icon) icon.className = 'fas fa-play';
                if (toggle) toggle.classList.remove('playing');
            }
        }

        function updateVolumeDisplay() {
            const volumeFill = document.getElementById('volumeFill');
            if (volumeFill) {
                volumeFill.style.width = (audioPlayer.volume * 100) + '%';
            }
        }

        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
        }

        // Initialize audio when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initAudio();
        });
    </script>
</body>

</html>
