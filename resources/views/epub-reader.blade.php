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

    <style>
        /* Chapter selection styles */
        .chapter-selector {
            position: relative;
            display: inline-block;
        }

        .chapter-dropdown {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 0;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            min-width: 250px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }

        .chapter-dropdown.show {
            display: block;
        }

        .chapter-item {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            margin-bottom: 5px;
            color: white;
            font-size: 14px;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .chapter-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .chapter-item.active {
            background: rgba(74, 144, 226, 0.3);
        }

        .chapter-item.playing {
            background: rgba(74, 144, 226, 0.5);
        }

        .chapter-title {
            flex: 1;
            margin-right: 10px;
        }

        .chapter-duration {
            font-size: 12px;
            opacity: 0.7;
        }

        .chapter-selector-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
        }

        .chapter-selector-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .no-chapters {
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            padding: 20px;
            font-style: italic;
        }

        .loading-chapters {
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            padding: 20px;
        }

        /* Audio player enhancements */
        .audio-controls {
            position: relative;
        }
    </style>
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

            <!-- Chapter Selector -->
            <div class="chapter-selector">
                <button class="chapter-selector-btn" onclick="toggleChapterDropdown()">
                    <i class="fas fa-list"></i>
                </button>
                <div class="chapter-dropdown" id="chapterDropdown">
                    <div class="loading-chapters" id="loadingChapters">Loading chapters...</div>
                </div>
            </div>

            <button class="close-audio" onclick="closeAudioPlayer()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Hidden audio element -->
    <audio id="audioElement" preload="metadata" style="display: none;">
        Your browser does not support the audio element.
    </audio>

    <script>
        // Audio Player State
        let audioPlayer = {
            audio: null,
            isPlaying: false,
            volume: 0.7,
            isVisible: false,
            chapters: [],
            currentChapterIndex: 0,
            isLoading: false
        };

        // Initialize the book
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

        // Theme handling (existing code)
        rendition.hooks.content.register(function(contents) {
            const doc = contents.document;
            const style = doc.createElement("style");
            style.id = "theme-style";
            doc.head.appendChild(style);
            applyTheme(currentTheme, style, doc);
        });

        const invertToggle = document.getElementById("invert");
        invertToggle.addEventListener("change", (e) => {
            currentTheme = e.target.checked ? "dark" : "light";
            rendition.getContents().forEach((contents) => {
                const doc = contents.document;
                const style = doc.getElementById("theme-style");
                applyTheme(currentTheme, style, doc);
            });
        });

        function applyTheme(theme, styleElement, doc) {
            if (theme === "dark") {
                styleElement.textContent = `
                    body { background: #121212 !important; color: #e0e0e0 !important; }
                    a { color: #90caf9 !important; }
                `;
            } else {
                styleElement.textContent = `
                    body { background: #ffffff !important; color: #000000 !important; }
                    a { color: #007bff !important; }
                `;
            }
        }

        const fullscreenToggle = document.getElementById("fullscreen");
        fullscreenToggle.addEventListener("change", (e) => {
            const flowMode = e.target.checked ? "scrolled" : "paginated";
            rendition.flow(flowMode);
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

        // Audio Player Functions

        function initAudio() {
            console.log('Initializing audio...');
            audioPlayer.audio = document.getElementById('audioElement');
            if (!audioPlayer.audio) {
                console.error('Audio element not found');
                return false;
            }

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
                console.log('Audio ended, playing next chapter');
                playNextChapter();
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

            console.log('Audio initialized successfully');
            return true;
        }

        // Load audiobook chapters
        async function loadChapters() {
            try {
                console.log('Loading chapters for ebook ID: {{ $ebook->id }}');

                // You'll need to create this endpoint in your Laravel routes
                const response = await fetch('/api/ebook/{{ $ebook->id }}/chapters');

                if (!response.ok) {
                    throw new Error('Failed to fetch chapters');
                }

                const data = await response.json();
                audioPlayer.chapters = data.chapters || [];

                console.log('Chapters loaded:', audioPlayer.chapters.length);
                renderChapterDropdown();

            } catch (error) {
                console.error('Error loading chapters:', error);
                renderNoChapters();
            }
        }

        function renderChapterDropdown() {
            const dropdown = document.getElementById('chapterDropdown');

            if (audioPlayer.chapters.length === 0) {
                renderNoChapters();
                return;
            }

            let html = '';
            audioPlayer.chapters.forEach((chapter, index) => {
                const isActive = index === audioPlayer.currentChapterIndex;
                const title = chapter.title || `Chapter ${chapter.index}`;

                html += `
                    <div class="chapter-item ${isActive ? 'active' : ''}"
                         data-chapter-index="${index}"
                         onclick="selectChapter(${index})">
                        <span class="chapter-title">${title}</span>
                    </div>
                `;
            });

            dropdown.innerHTML = html;
        }

        function renderNoChapters() {
            const dropdown = document.getElementById('chapterDropdown');
            dropdown.innerHTML = '<div class="no-chapters">No audiobook chapters available</div>';
        }

        function toggleChapterDropdown() {
            const dropdown = document.getElementById('chapterDropdown');
            dropdown.classList.toggle('show');

            // Close dropdown when clicking outside
            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.closest('.chapter-selector')) {
                    dropdown.classList.remove('show');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }

        function selectChapter(chapterIndex) {
            if (chapterIndex < 0 || chapterIndex >= audioPlayer.chapters.length) {
                console.error('Invalid chapter index:', chapterIndex);
                return;
            }

            audioPlayer.currentChapterIndex = chapterIndex;
            const chapter = audioPlayer.chapters[chapterIndex];

            console.log('Selecting chapter:', chapter);

            // Update UI
            updateChapterUI();

            // Load and play the chapter
            if (chapter.audio_path) {
                loadChapterAudio(chapter.audio_path);
            } else {
                console.error('Chapter has no audio file:', chapter);
                alert('This chapter does not have an audio file yet.');
            }

            // Close dropdown
            document.getElementById('chapterDropdown').classList.remove('show');
        }

        function updateChapterUI() {
            // Update chapter dropdown active state
            const chapterItems = document.querySelectorAll('.chapter-item');
            chapterItems.forEach((item, index) => {
                item.classList.toggle('active', index === audioPlayer.currentChapterIndex);
            });
        }

        function loadChapterAudio(audioPath) {
            if (!audioPlayer.audio) {
                console.error('Audio element not initialized');
                return;
            }

            console.log('Loading audio:', audioPath);

            // Set the audio source
            audioPlayer.audio.src = `{{ asset('') }}${audioPath}`;

            // Load the audio
            audioPlayer.audio.load();

            // Auto-play if player was already playing
            if (audioPlayer.isPlaying) {
                audioPlayer.audio.play().catch(e => {
                    console.error('Auto-play failed:', e);
                });
            }
        }

        function playNextChapter() {
            const nextIndex = audioPlayer.currentChapterIndex + 1;
            if (nextIndex < audioPlayer.chapters.length) {
                selectChapter(nextIndex);
            } else {
                console.log('Reached end of audiobook');
                audioPlayer.isPlaying = false;
                updatePlayButton();
            }
        }

        function playPreviousChapter() {
            const prevIndex = audioPlayer.currentChapterIndex - 1;
            if (prevIndex >= 0) {
                selectChapter(prevIndex);
            }
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

                // Load chapters if not already loaded
                if (audioPlayer.chapters.length === 0) {
                    loadChapters();
                }

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

            // If no chapters loaded or no current chapter, load first available chapter
            if (audioPlayer.chapters.length === 0) {
                console.log('No chapters loaded, loading now...');
                loadChapters().then(() => {
                    if (audioPlayer.chapters.length > 0) {
                        selectChapter(0);
                    }
                });
                return;
            }

            // If no audio source is set, load current chapter
            if (!audioPlayer.audio.src || audioPlayer.audio.src === location.href) {
                const currentChapter = audioPlayer.chapters[audioPlayer.currentChapterIndex];
                if (currentChapter && currentChapter.audio_path) {
                    loadChapterAudio(currentChapter.audio_path);
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
                    alert('Audio could not be played. Please check if the audiobook file exists.');
                });
            }
        }

        function skipForward() {
            if (!audioPlayer.audio) return;
            audioPlayer.audio.currentTime = Math.min(audioPlayer.audio.currentTime + 30, audioPlayer.audio.duration);
        }

        function skipBackward() {
            if (!audioPlayer.audio) return;
            const newTime = audioPlayer.audio.currentTime - 30;

            if (newTime < 0 && audioPlayer.currentChapterIndex > 0) {
                // Skip to previous chapter
                playPreviousChapter();
            } else {
                audioPlayer.audio.currentTime = Math.max(newTime, 0);
            }
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
