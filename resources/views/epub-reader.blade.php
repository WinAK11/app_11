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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.4/howler.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.4/howler.min.js"></script>
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

        .hover-time-preview {
            position: absolute;
            bottom: 100%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.85);
            color: #fff;
            padding: 2px 6px;
            font-size: 12px;
            border-radius: 4px;
            white-space: nowrap;
            display: none;
            pointer-events: none;
            z-index: 10;
        }
    </style>
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

        .hover-time-preview {
            position: absolute;
            bottom: 100%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.85);
            color: #fff;
            padding: 2px 6px;
            font-size: 12px;
            border-radius: 4px;
            white-space: nowrap;
            display: none;
            pointer-events: none;
            z-index: 10;
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
                <div class="progress-bar" id="progressBar" style="cursor: pointer; position: relative;">
                <div class="progress-bar" id="progressBar" style="cursor: pointer; position: relative;">
                    <div class="progress-fill" id="progressFill"></div>
                    <div class="hover-time-preview" id="hoverTimePreview"></div>
                    <div class="hover-time-preview" id="hoverTimePreview"></div>
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

    <script>
        // CLEAN HOWLER.JS AUDIO PLAYER IMPLEMENTATION

        // Audio Player State - ONLY HOWLER
        // CLEAN HOWLER.JS AUDIO PLAYER IMPLEMENTATION

        // Audio Player State - ONLY HOWLER
        let audioPlayer = {
            sound: null, // Howler sound instance ONLY
            sound: null, // Howler sound instance ONLY
            isPlaying: false,
            volume: 0.7,
            isVisible: false,
            chapters: [],
            currentChapterIndex: 0,
            isLoading: false,
            currentAudioPath: null,
            duration: 0
        };

        // Progress updater
        let progressInterval = null;
            isVisible: false,
            chapters: [],
            currentChapterIndex: 0,
            isLoading: false,
            currentAudioPath: null,
            duration: 0
        };

        // Progress updater
        let progressInterval = null;

        // Initialize the book (ePub.js part - keep this)
        // Initialize the book (ePub.js part - keep this)
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

            tocSelect.addEventListener("change", (e) => {
                const href = e.target.value;
                rendition.display(href);
            });
        });

        rendition.display();

        // Theme handling
        // Theme handling
        let currentTheme = "light";

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
                    body { background: #121212 !important; color: #e0e0e0 !important; }
                    a { color: #90caf9 !important; }
                `;
            } else {
                styleElement.textContent = `
                    body { background: #ffffff !important; color: #000000 !important; }
                    a { color: #007bff !important; }
                `;
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

        // ========== HOWLER AUDIO PLAYER FUNCTIONS ==========

        // Load chapters
        async function loadChapters() {
            try {
                console.log('Loading chapters for ebook ID: {{ $ebook->id }}');
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

        // Render chapter dropdown
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
                         onclick="selectChapter(${index})">
                        <span class="chapter-title">${title}</span>
                    </div>
                `;
        // ========== HOWLER AUDIO PLAYER FUNCTIONS ==========

        // Load chapters
        async function loadChapters() {
            try {
                console.log('Loading chapters for ebook ID: {{ $ebook->id }}');
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

        // Render chapter dropdown
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

        // Load chapter with Howler
        function loadChapterAudio(audioPath) {
            console.log('Loading audio with Howler:', audioPath);

            if (audioPlayer.currentAudioPath === audioPath && audioPlayer.sound) {
                console.log('Audio already loaded');
                return;

            dropdown.innerHTML = html;
        }

        function renderNoChapters() {
            const dropdown = document.getElementById('chapterDropdown');
            dropdown.innerHTML = '<div class="no-chapters">No audiobook chapters available</div>';
        }

        // Load chapter with Howler
        function loadChapterAudio(audioPath) {
            console.log('Loading audio with Howler:', audioPath);

            if (audioPlayer.currentAudioPath === audioPath && audioPlayer.sound) {
                console.log('Audio already loaded');
                return;
            }

            const wasPlaying = audioPlayer.isPlaying;

            // Clean up previous sound
            if (audioPlayer.sound) {
                audioPlayer.sound.stop();
                audioPlayer.sound.unload();
            }

            audioPlayer.isLoading = true;
            audioPlayer.currentAudioPath = audioPath;

            const fullPath = `{{ asset('') }}${audioPath}`;

            // Create Howler instance
            audioPlayer.sound = new Howl({
                src: [fullPath],
                html5: false,
                volume: audioPlayer.volume,

                onload: function() {
                    console.log('✅ Audio loaded successfully');
                    audioPlayer.isLoading = false;
                    audioPlayer.duration = audioPlayer.sound.duration();

                    const totalTimeEl = document.getElementById('totalTime');
                    if (totalTimeEl) {
                        totalTimeEl.textContent = formatTime(audioPlayer.duration);
                    }

                    if (wasPlaying) {
                        audioPlayer.sound.play();
                    }
                },

                onloaderror: function(id, error) {
                    console.error('❌ Audio load error:', error);
                    audioPlayer.isLoading = false;
                    alert('Failed to load audio file.');
                },

                onplay: function() {
                    audioPlayer.isPlaying = true;
                    updatePlayButton();
                    startProgressUpdater();
                },

                onpause: function() {
                    audioPlayer.isPlaying = false;
                    updatePlayButton();
                },

                onend: function() {
                    playNextChapter();
                }
            });
        }

        // Progress updater
        function startProgressUpdater() {
            if (progressInterval) clearInterval(progressInterval);

            progressInterval = setInterval(() => {
                if (audioPlayer.sound && audioPlayer.isPlaying) {
                    updateProgress();
                }
            }, 100);
        }

        function stopProgressUpdater() {
            if (progressInterval) {
                clearInterval(progressInterval);
                progressInterval = null;
            }
        }

        function updateProgress() {
            if (!audioPlayer.sound || audioPlayer.isLoading) return;

            const currentTime = audioPlayer.sound.seek() || 0;
            const duration = audioPlayer.duration;

            if (duration > 0) {
                const progress = (currentTime / duration) * 100;
                const progressFill = document.getElementById('progressFill');
                if (progressFill) {
                    progressFill.style.width = Math.max(0, Math.min(100, progress)) + '%';
                }
            }

            const currentTimeEl = document.getElementById('currentTime');
            if (currentTimeEl) {
                currentTimeEl.textContent = formatTime(currentTime);
            }
        }

        // ⭐ FIXED SEEK FUNCTION - NO MORE RESTARTS!
        function seekTo(event) {
            event.stopPropagation();

            if (!audioPlayer.sound || audioPlayer.sound.state() !== 'loaded') {
                console.warn("Audio is not loaded yet, cannot seek.");
                return;
            }

            const duration = audioPlayer.sound.duration();
            if (!duration || isNaN(duration)) {
                console.warn("Invalid audio duration.");
                return;
            }

            const progressBar = event.currentTarget;
            const rect = progressBar.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const percentage = Math.max(0, Math.min(1, clickX / rect.width));
            const targetTime = percentage * duration;

            console.log(`⏩ Seeking to ${targetTime.toFixed(2)}s (of ${duration}s)`);

            audioPlayer.sound.seek(targetTime);
            updateProgress();

        }

        // Chapter selection
        function selectChapter(chapterIndex) {
            if (chapterIndex < 0 || chapterIndex >= audioPlayer.chapters.length) return;

            audioPlayer.currentChapterIndex = chapterIndex;
            const chapter = audioPlayer.chapters[chapterIndex];

            updateChapterUI();

            if (chapter.audio_path) {
                loadChapterAudio(chapter.audio_path);
            } else {
                alert('This chapter does not have an audio file yet.');
            }

            document.getElementById('chapterDropdown').classList.remove('show');
        }

        function updateChapterUI() {
            const chapterItems = document.querySelectorAll('.chapter-item');
            chapterItems.forEach((item, index) => {
                item.classList.toggle('active', index === audioPlayer.currentChapterIndex);
            // Create Howler instance
            audioPlayer.sound = new Howl({
                src: [fullPath],
                html5: false,
                volume: audioPlayer.volume,

                onload: function() {
                    console.log('✅ Audio loaded successfully');
                    audioPlayer.isLoading = false;
                    audioPlayer.duration = audioPlayer.sound.duration();

                    const totalTimeEl = document.getElementById('totalTime');
                    if (totalTimeEl) {
                        totalTimeEl.textContent = formatTime(audioPlayer.duration);
                    }

                    if (wasPlaying) {
                        audioPlayer.sound.play();
                    }
                },

                onloaderror: function(id, error) {
                    console.error('❌ Audio load error:', error);
                    audioPlayer.isLoading = false;
                    alert('Failed to load audio file.');
                },

                onplay: function() {
                    audioPlayer.isPlaying = true;
                    updatePlayButton();
                    startProgressUpdater();
                },

                onpause: function() {
                    audioPlayer.isPlaying = false;
                    updatePlayButton();
                },

                onend: function() {
                    playNextChapter();
                }
            });
        }

        // Progress updater
        function startProgressUpdater() {
            if (progressInterval) clearInterval(progressInterval);

            progressInterval = setInterval(() => {
                if (audioPlayer.sound && audioPlayer.isPlaying) {
                    updateProgress();
                }
            }, 100);
        }

        function stopProgressUpdater() {
            if (progressInterval) {
                clearInterval(progressInterval);
                progressInterval = null;
            }
        }

        function updateProgress() {
            if (!audioPlayer.sound || audioPlayer.isLoading) return;

            const currentTime = audioPlayer.sound.seek() || 0;
            const duration = audioPlayer.duration;

            if (duration > 0) {
                const progress = (currentTime / duration) * 100;
                const progressFill = document.getElementById('progressFill');
                if (progressFill) {
                    progressFill.style.width = Math.max(0, Math.min(100, progress)) + '%';
                }
            }

            const currentTimeEl = document.getElementById('currentTime');
            if (currentTimeEl) {
                currentTimeEl.textContent = formatTime(currentTime);
            }
        }

        // ⭐ FIXED SEEK FUNCTION - NO MORE RESTARTS!
        function seekTo(event) {
            event.stopPropagation();

            if (!audioPlayer.sound || audioPlayer.sound.state() !== 'loaded') {
                console.warn("Audio is not loaded yet, cannot seek.");
                return;
            }

            const duration = audioPlayer.sound.duration();
            if (!duration || isNaN(duration)) {
                console.warn("Invalid audio duration.");
                return;
            }

            const progressBar = event.currentTarget;
            const rect = progressBar.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const percentage = Math.max(0, Math.min(1, clickX / rect.width));
            const targetTime = percentage * duration;

            console.log(`⏩ Seeking to ${targetTime.toFixed(2)}s (of ${duration}s)`);

            audioPlayer.sound.seek(targetTime);
            updateProgress();

        }

        // Chapter selection
        function selectChapter(chapterIndex) {
            if (chapterIndex < 0 || chapterIndex >= audioPlayer.chapters.length) return;

            audioPlayer.currentChapterIndex = chapterIndex;
            const chapter = audioPlayer.chapters[chapterIndex];

            updateChapterUI();

            if (chapter.audio_path) {
                loadChapterAudio(chapter.audio_path);
            } else {
                alert('This chapter does not have an audio file yet.');
            }

            document.getElementById('chapterDropdown').classList.remove('show');
        }

        function updateChapterUI() {
            const chapterItems = document.querySelectorAll('.chapter-item');
            chapterItems.forEach((item, index) => {
                item.classList.toggle('active', index === audioPlayer.currentChapterIndex);
            });
        }

        function playNextChapter() {
            const nextIndex = audioPlayer.currentChapterIndex + 1;
            if (nextIndex < audioPlayer.chapters.length) {
                selectChapter(nextIndex);
            } else {
                console.log('End of audiobook reached');
        }

        function playNextChapter() {
            const nextIndex = audioPlayer.currentChapterIndex + 1;
            if (nextIndex < audioPlayer.chapters.length) {
                selectChapter(nextIndex);
            } else {
                console.log('End of audiobook reached');
                audioPlayer.isPlaying = false;
                updatePlayButton();
                stopProgressUpdater();
            }
        }

        // Playback controls
        function togglePlayback() {
            if (!audioPlayer.sound) {
                if (audioPlayer.chapters.length === 0) {
                    loadChapters().then(() => {
                        if (audioPlayer.chapters.length > 0) {
                            selectChapter(0);
                        }
                    });
                    return;
                }

                const currentChapter = audioPlayer.chapters[audioPlayer.currentChapterIndex];
                if (currentChapter?.audio_path) {
                    loadChapterAudio(currentChapter.audio_path);
                stopProgressUpdater();
            }
        }

        // Playback controls
        function togglePlayback() {
            if (!audioPlayer.sound) {
                if (audioPlayer.chapters.length === 0) {
                    loadChapters().then(() => {
                        if (audioPlayer.chapters.length > 0) {
                            selectChapter(0);
                        }
                    });
                    return;
                }

                const currentChapter = audioPlayer.chapters[audioPlayer.currentChapterIndex];
                if (currentChapter?.audio_path) {
                    loadChapterAudio(currentChapter.audio_path);
                    return;
                }
                return;
                return;
            }

            if (audioPlayer.isLoading) {
                console.log('Audio loading...');
                return;
            }

            if (audioPlayer.isPlaying) {
                audioPlayer.sound.pause();
            } else {
                audioPlayer.sound.play();
            }
        }

        function skipForward() {
            const nextIndex = audioPlayer.currentChapterIndex + 1;
            if (nextIndex < audioPlayer.chapters.length) {
                selectChapter(nextIndex);
            }
        }

        function skipBackward() {
            if (!audioPlayer.sound) return;

            const currentTime = audioPlayer.sound.seek();
            if (currentTime <= 5 && audioPlayer.currentChapterIndex > 0) {
                selectChapter(audioPlayer.currentChapterIndex - 1);
            } else {
                audioPlayer.sound.seek(0);
            }
        }

        // Volume control - FIXED FOR HOWLER
        function setVolume(event) {
            const volumeBar = event.currentTarget;
            const rect = volumeBar.getBoundingClientRect();
            const clickPosition = event.clientX - rect.left;
            const percentage = Math.max(0, Math.min(1, clickPosition / rect.width));

            audioPlayer.volume = percentage;

            if (audioPlayer.sound) {
                audioPlayer.sound.volume(audioPlayer.volume);
            }

            updateVolumeDisplay();
        }

        // Audio player UI controls
        function toggleAudioPlayer() {
            if (audioPlayer.isLoading) {
                console.log('Audio loading...');
                return;
            }

            if (audioPlayer.isPlaying) {
                audioPlayer.sound.pause();
            } else {
                audioPlayer.sound.play();
            }
        }

        function skipForward() {
            const nextIndex = audioPlayer.currentChapterIndex + 1;
            if (nextIndex < audioPlayer.chapters.length) {
                selectChapter(nextIndex);
            }
        }

        function skipBackward() {
            if (!audioPlayer.sound) return;

            const currentTime = audioPlayer.sound.seek();
            if (currentTime <= 5 && audioPlayer.currentChapterIndex > 0) {
                selectChapter(audioPlayer.currentChapterIndex - 1);
            } else {
                audioPlayer.sound.seek(0);
            }
        }

        // Volume control - FIXED FOR HOWLER
        function setVolume(event) {
            const volumeBar = event.currentTarget;
            const rect = volumeBar.getBoundingClientRect();
            const clickPosition = event.clientX - rect.left;
            const percentage = Math.max(0, Math.min(1, clickPosition / rect.width));

            audioPlayer.volume = percentage;

            if (audioPlayer.sound) {
                audioPlayer.sound.volume(audioPlayer.volume);
            }

            updateVolumeDisplay();
        }

        // Audio player UI controls
        function toggleAudioPlayer() {
            const player = document.getElementById('audioPlayer');
            const toggle = document.getElementById('audioToggle');
            const bookReader = document.querySelector('.book-reader');

            if (audioPlayer.isVisible) {
                player.classList.remove('show');
                bookReader.classList.remove('with-audio');
                audioPlayer.isVisible = false;
                toggle.classList.remove('playing');
                stopProgressUpdater();
                stopProgressUpdater();
            } else {
                player.classList.add('show');
                bookReader.classList.add('with-audio');
                audioPlayer.isVisible = true;

                if (audioPlayer.chapters.length === 0) {
                    loadChapters();
                }


                if (audioPlayer.chapters.length === 0) {
                    loadChapters();
                }

                if (audioPlayer.isPlaying) {
                    toggle.classList.add('playing');
                    startProgressUpdater();
                    startProgressUpdater();
                }
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

            if (audioPlayer.sound) {
                audioPlayer.sound.pause();
            if (audioPlayer.sound) {
                audioPlayer.sound.pause();
            }
            stopProgressUpdater();
        }

        function toggleChapterDropdown() {
            const dropdown = document.getElementById('chapterDropdown');
            dropdown.classList.toggle('show');

            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.closest('.chapter-selector')) {
                    dropdown.classList.remove('show');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }

        // UI helper functions
            stopProgressUpdater();
        }

        function toggleChapterDropdown() {
            const dropdown = document.getElementById('chapterDropdown');
            dropdown.classList.toggle('show');

            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.closest('.chapter-selector')) {
                    dropdown.classList.remove('show');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }

        // UI helper functions
        function updatePlayButton() {
            const icon = document.getElementById('playIcon');
            const toggle = document.getElementById('audioToggle');

            if (audioPlayer.isPlaying) {
                if (icon) icon.className = 'fas fa-pause';
                if (toggle && audioPlayer.isVisible) toggle.classList.add('playing');
                if (toggle && audioPlayer.isVisible) toggle.classList.add('playing');
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

        // Initialize everything
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎵 Initializing Howler audio player...');

            // Attach progress bar click handler
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.addEventListener('click', seekTo);
                console.log('✅ Progress bar click handler attached');
            }

            // Set initial volume display
            updateVolumeDisplay();
        });

        // Initialize everything
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎵 Initializing Howler audio player...');

            // Attach progress bar click handler
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.addEventListener('click', seekTo);
                console.log('✅ Progress bar click handler attached');
            }

            // Set initial volume display
            updateVolumeDisplay();
        });

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎵 Initializing Howler audio player...');

            const progressBar = document.getElementById('progressBar');
            const hoverPreview = document.getElementById('hoverTimePreview');

            if (progressBar) {
                progressBar.addEventListener('mousemove', (e) => {
                    if (!audioPlayer.sound || audioPlayer.isLoading || isNaN(audioPlayer.duration)) return;

                    const rect = progressBar.getBoundingClientRect();
                    const hoverX = e.clientX - rect.left;
                    const percentage = Math.max(0, Math.min(1, hoverX / rect.width));
                    const hoverTime = percentage * audioPlayer.duration;

                    hoverPreview.style.display = 'block';
                    hoverPreview.textContent = formatTime(hoverTime);
                    hoverPreview.style.left = `${hoverX}px`;
                });

                progressBar.addEventListener('mouseleave', () => {
                    hoverPreview.style.display = 'none';
                });

                progressBar.addEventListener('mouseenter', () => {
                    if (audioPlayer.sound && !isNaN(audioPlayer.duration)) {
                        hoverPreview.style.display = 'block';
                    }
                });
            }

            updateVolumeDisplay();
        });


        // Cleanup
        window.addEventListener('beforeunload', function() {
            if (audioPlayer.sound) {
                audioPlayer.sound.unload();
            }
            stopProgressUpdater();
            console.log('🎵 Initializing Howler audio player...');

            const progressBar = document.getElementById('progressBar');
            const hoverPreview = document.getElementById('hoverTimePreview');

            if (progressBar) {
                progressBar.addEventListener('mousemove', (e) => {
                    if (!audioPlayer.sound || audioPlayer.isLoading || isNaN(audioPlayer.duration)) return;

                    const rect = progressBar.getBoundingClientRect();
                    const hoverX = e.clientX - rect.left;
                    const percentage = Math.max(0, Math.min(1, hoverX / rect.width));
                    const hoverTime = percentage * audioPlayer.duration;

                    hoverPreview.style.display = 'block';
                    hoverPreview.textContent = formatTime(hoverTime);
                    hoverPreview.style.left = `${hoverX}px`;
                });

                progressBar.addEventListener('mouseleave', () => {
                    hoverPreview.style.display = 'none';
                });

                progressBar.addEventListener('mouseenter', () => {
                    if (audioPlayer.sound && !isNaN(audioPlayer.duration)) {
                        hoverPreview.style.display = 'block';
                    }
                });
            }

            updateVolumeDisplay();
        });


        // Cleanup
        window.addEventListener('beforeunload', function() {
            if (audioPlayer.sound) {
                audioPlayer.sound.unload();
            }
            stopProgressUpdater();
        });
    </script>
</body>

</html>
