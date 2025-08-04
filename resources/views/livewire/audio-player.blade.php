<div x-data x-show="@entangle('visible')" x-transition
    class="player-controls fixed bottom-0 left-0 right-0 z-50 bg-gray-900 text-white">

    <!-- Close Button -->
    <button @click="$wire.hide()" class="absolute top-2 right-4 text-white text-xl hover:text-gray-300 z-50">
        &times;
    </button>

    <div class="player-controls relative" x-data="{
        isPlaying: @entangle('isPlaying'),
        progress: @entangle('progress'),
        volume: @entangle('volume'),
        duration: {{ $track['duration'] }},
        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        },
        get currentTime() {
            return Math.floor(this.progress / 100 * this.duration);
        }
    }" x-init="setInterval(() => {
        if (isPlaying) {
            progress += 0.2;
            if (progress >= 100) {
                progress = 0;
                isPlaying = false;
            }
        }
    }, 100)">

        <!-- Track Info -->
        <div class="control-track-info">
            <img src="https://via.placeholder.com/48" alt="Capa do álbum" class="control-cover">
            <div>
                <p class="control-title">{{ $track['title'] }}</p>
                <p class="control-artist">{{ $track['artist'] }}</p>
            </div>
        </div>

        <!-- Playback Controls -->
        <div class="playback-controls">
            <div class="playback-buttons">
                <button class="control-button">
                    <i class="fas fa-step-backward"></i>
                </button>
                <button class="play-pause-button" @click="$wire.togglePlayback()">
                    <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play'"></i>
                </button>
                <button class="control-button">
                    <i class="fas fa-step-forward"></i>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <span class="time current-time" x-text="formatTime(currentTime)"></span>
                <div class="progress-bar"
                    @click="e => {
                    const rect = $el.getBoundingClientRect();
                    progress = ((e.clientX - rect.left) / rect.width) * 100;
                }">
                    <div class="progress-fill" :style="`width: ${progress}%`"></div>
                </div>
                <span class="time total-time" x-text="formatTime(duration)"></span>
            </div>
        </div>

        <!-- Volume Control -->
        <div class="volume-control">
            <i class="fas fa-volume-up"></i>
            <div class="volume-bar"
                @click="e => {
                const rect = $el.getBoundingClientRect();
                volume = ((e.clientX - rect.left) / rect.width) * 100;
            }">
                <div class="volume-fill" :style="`width: ${volume}%`"></div>
            </div>
        </div>
    </div>
</div>
