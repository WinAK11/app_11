{{-- resources/views/livewire/audiobook-player.blade.php --}}
<div id="audiobook-player" data-turbo-permanent class="player-controls" style="display: none;">

    {{-- Close button --}}
    <button class="player-close-btn" onclick="closeAudioPlayer()" title="Close player">
        <i class="fas fa-times"></i>
    </button>

    {{-- Hidden audio element --}}
    <audio id="audio-element" data-turbo-permanent wire:ignore
        @if ($audioUrl) src="{{ $audioUrl }}" @endif preload="metadata">
    </audio>

    {{-- Track Info --}}
    <div class="control-track-info">
        <img src="{{ $coverUrl }}" alt="Capa do audiobook" class="control-cover">
        <div>
            <p class="control-title">{{ $title }}</p>
            <p class="control-artist">{{ $artist }}</p>
        </div>
    </div>

    {{-- Playback Controls --}}
    <div class="playback-controls">
        <div class="playback-buttons">
            <button class="control-button" wire:click="previousTrack">
                <i class="fas fa-step-backward"></i>
            </button>
            <button class="play-pause-button" wire:click="togglePlayback">
                <i class="fas {{ $isPlaying ? 'fa-pause' : 'fa-play' }}"></i>
            </button>
            <button class="control-button" wire:click="nextTrack">
                <i class="fas fa-step-forward"></i>
            </button>
        </div>

        {{-- Progress Bar --}}
        <div class="progress-container">
            <span class="time current-time">{{ $this->formatTime($currentTime) }}</span>
            <div class="progress-bar" wire:ignore>
                <div class="progress-fill" style="width: {{ $progress }}%"></div>
            </div>
            <span class="time total-time">{{ $this->formatTime($duration) }}</span>
        </div>
    </div>

    {{-- Volume Control --}}
    <div class="volume-control">
        <i class="fas fa-volume-up"></i>
        <div class="volume-bar" wire:ignore>
            <div class="volume-fill" style="width: {{ $volume }}%"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Global state to track initialization
        if (!window.audioPlayerState) {
            window.audioPlayerState = {
                initialized: false,
                eventListenersAttached: false,
                isVisible: false
            };
        }

        // Function to close/hide the audio player
        function closeAudioPlayer() {
            const playerContainer = document.getElementById('audiobook-player');
            const audio = document.getElementById('audio-element');
            
            if (playerContainer) {
                playerContainer.style.display = 'none';
                window.audioPlayerState.isVisible = false;
            }
            
            // Pause audio when closing
            if (audio && !audio.paused) {
                audio.pause();
            }
            
            // Reset button states
            document.querySelectorAll('.play-audiobook-btn').forEach(btn => {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                btn.innerHTML = '<i class="fas fa-play me-1"></i> Play Sample';
            });
            
            // Clear the current book state via Livewire
            if (typeof livewireCall === 'function') {
                livewireCall('clearPlayer')
                    .catch(e => console.warn('Failed to clear player state:', e));
            }
        }

        // Improved livewireCall function with retry logic
        function livewireCall(method, ...params) {
            return new Promise((resolve, reject) => {
                let attempts = 0;
                const maxAttempts = 10;
                
                function tryCall() {
                    attempts++;
                    
                    // Try multiple ways to find the Livewire component
                    let component = null;
                    
                    // Method 1: Try to find by wire:id attribute
                    const componentEl = document.querySelector('[wire\\:id]');
                    if (componentEl) {
                        const componentId = componentEl.getAttribute('wire:id');
                        if (componentId && window.Livewire) {
                            component = window.Livewire.find(componentId);
                        }
                    }
                    
                    // Method 2: Try to get first component if no specific ID found
                    if (!component && window.Livewire && window.Livewire.components) {
                        const components = window.Livewire.components;
                        if (components && components.length > 0) {
                            component = components[0];
                        }
                    }
                    
                    if (component && typeof component.call === 'function') {
                        try {
                            component.call(method, ...params);
                            resolve();
                        } catch (error) {
                            console.warn('Livewire call failed:', error);
                            reject(error);
                        }
                    } else if (attempts < maxAttempts) {
                        // Retry after a short delay
                        setTimeout(tryCall, 100);
                    } else {
                        console.warn('Livewire component not found after', maxAttempts, 'attempts');
                        reject(new Error('Livewire component not available'));
                    }
                }
                
                tryCall();
            });
        }

        function initializeAudioPlayer() {
            if (window.audioPlayerState.initialized) {
                console.log('Audio player already initialized, skipping...');
                return;
            }

            const audio = document.getElementById('audio-element');
            const progressBar = document.querySelector('.progress-bar');
            const progressFill = document.querySelector('.progress-fill');
            const volumeBar = document.querySelector('.volume-bar');
            const volumeFill = document.querySelector('.volume-fill');
            const playerContainer = document.getElementById('audiobook-player');
            const currentTimeEl = document.querySelector('.current-time');
            const totalTimeEl = document.querySelector('.total-time');

            if (!audio) {
                console.log('Audio element not found, will retry...');
                setTimeout(initializeAudioPlayer, 100);
                return;
            }

            // Also check if other essential elements are present
            if (!progressBar || !progressFill || !volumeBar || !volumeFill) {
                console.log('Some player elements not found, will retry...');
                setTimeout(initializeAudioPlayer, 100);
                return;
            }

            console.log('Initializing audio player...');

            // Initialize audio volume
            audio.volume = {{ $volume / 100 }};

            // Only show player if there's a current book AND it should be visible
            @if ($currentBook)
                if (window.audioPlayerState.isVisible) {
                    playerContainer.style.display = 'block';
                    @if ($isPlaying && $audioUrl)
                        setTimeout(() => {
                            audio.currentTime = {{ $currentTime }};
                            audio.play().catch(e => console.log('Auto-play prevented:', e));
                        }, 100);
                    @endif
                }
            @endif

            // Only attach event listeners once
            if (!window.audioPlayerState.eventListenersAttached) {
                // Livewire event listeners
                window.addEventListener('loadAudio', event => {
                    console.log('Loading audio:', event.detail.audioUrl);
                    audio.src = event.detail.audioUrl;
                    audio.load();
                    
                    // Show the player when audio is loaded
                    playerContainer.style.display = 'block';
                    window.audioPlayerState.isVisible = true;

                    audio.addEventListener('canplay', function() {
                        audio.play().catch(e => console.log('Auto-play prevented:', e));
                    }, {
                        once: true
                    });
                });

                window.addEventListener('toggleAudio', () => {
                    console.log('Toggle audio, paused:', audio.paused);
                    if (audio.paused) {
                        audio.play().catch(e => console.log('Play prevented:', e));
                    } else {
                        audio.pause();
                    }
                });

                window.addEventListener('seekAudio', event => {
                    console.log('Seeking to:', event.detail.percentage + '%');
                    if (audio.duration && !isNaN(audio.duration)) {
                        audio.currentTime = (event.detail.percentage / 100) * audio.duration;
                    }
                });

                window.addEventListener('setAudioVolume', event => {
                    console.log('Setting volume:', event.detail.volume);
                    audio.volume = event.detail.volume;
                });

                // Audio event listeners
                let lastUpdate = 0;
                let isNavigating = false;

                // Listen for navigation events to pause updates and audio
                document.addEventListener('turbo:before-visit', () => {
                    isNavigating = true;
                    // Temporarily pause audio during navigation to improve performance
                    if (audio && !audio.paused) {
                        audio.pause();
                        // Resume after navigation completes
                        setTimeout(() => {
                            if (!isNavigating && window.audioPlayerState.isVisible) {
                                audio.play().catch(e => console.log('Resume after navigation prevented:', e));
                            }
                        }, 500);
                    }
                });

                document.addEventListener('turbo:load', () => {
                    isNavigating = false;
                });

                audio.addEventListener('timeupdate', () => {
                    if (audio.duration && !isNaN(audio.duration)) {
                        const progress = (audio.currentTime / audio.duration) * 100;
                        progressFill.style.width = Math.min(progress, 100) + '%';

                        if (currentTimeEl) {
                            currentTimeEl.textContent = formatTime(Math.floor(audio.currentTime));
                        }
                        if (totalTimeEl) {
                            totalTimeEl.textContent = formatTime(Math.floor(audio.duration));
                        }

                        // Only update Livewire if not navigating and throttle updates
                        const now = Date.now();
                        if (!isNavigating && now - lastUpdate > 3000) { // Increased to 3000ms for better performance
                            lastUpdate = now;
                            livewireCall('updateProgress', Math.floor(audio.currentTime), Math.floor(audio.duration))
                                .catch(e => console.warn('Failed to update progress:', e));
                        }
                    }
                });

                audio.addEventListener('loadedmetadata', () => {
                    console.log('Audio metadata loaded. Duration:', audio.duration);
                    if (totalTimeEl && audio.duration) {
                        totalTimeEl.textContent = formatTime(Math.floor(audio.duration));
                    }
                    livewireCall('updateProgress', Math.floor(audio.currentTime), Math.floor(audio.duration))
                        .catch(e => console.warn('Failed to update metadata progress:', e));
                });

                audio.addEventListener('ended', () => {
                    console.log('Audio ended');
                    progressFill.style.width = '0%';
                    livewireCall('audioEnded')
                        .catch(e => console.warn('Failed to handle audio ended:', e));
                });

                // Progress bar click handler
                if (progressBar) {
                    progressBar.addEventListener('click', (e) => {
                        e.preventDefault();
                        const rect = progressBar.getBoundingClientRect();
                        const clickPosition = e.clientX - rect.left;
                        const percentage = Math.max(0, Math.min(100, (clickPosition / rect.width) * 100));

                        progressFill.style.width = percentage + '%';
                        if (audio.duration && !isNaN(audio.duration)) {
                            audio.currentTime = (percentage / 100) * audio.duration;
                        }
                        livewireCall('seekTo', percentage)
                            .catch(e => console.warn('Failed to seek:', e));
                    });
                }

                // Volume bar click handler
                if (volumeBar) {
                    volumeBar.addEventListener('click', (e) => {
                        e.preventDefault();
                        const rect = volumeBar.getBoundingClientRect();
                        const clickPosition = e.clientX - rect.left;
                        const percentage = Math.max(0, Math.min(100, (clickPosition / rect.width) * 100));

                        volumeFill.style.width = percentage + '%';
                        audio.volume = percentage / 100;
                        livewireCall('setVolume', percentage)
                            .catch(e => console.warn('Failed to set volume:', e));
                    });
                }

                window.audioPlayerState.eventListenersAttached = true;
            }

            function formatTime(seconds) {
                if (isNaN(seconds)) return '0:00';
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
            }

            window.audioPlayerState.initialized = true;
            console.log('Audio player initialized successfully');
        }

        // Initialize on various events
        function setupInitialization() {
            // Reset initialization state on navigation
            window.audioPlayerState.initialized = false;
            
            // Wait a bit for DOM to be ready
            setTimeout(initializeAudioPlayer, 50);
        }

        document.addEventListener('DOMContentLoaded', setupInitialization);
        document.addEventListener('livewire:load', setupInitialization);
        document.addEventListener('livewire:navigated', setupInitialization);
        document.addEventListener('turbo:load', function() {
            console.log('Turbo navigation completed');
            setupInitialization();
        });
        document.addEventListener('turbo:before-cache', function() {
            console.log('Turbo caching page');
        });
    </script>
@endpush

@push('styles')
<style>
    .player-close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        transition: background-color 0.3s ease;
    }

    .player-close-btn:hover {
        background: rgba(0, 0, 0, 0.7);
    }

    .player-close-btn i {
        font-size: 14px;
    }

    /* Ensure player controls have relative positioning for close button */
    .player-controls {
        position: relative;
    }
</style>
@endpush
