<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class AudiobookPlayer extends Component {
    public $currentBook = null;
    public $isPlaying = false;
    public $currentTime = 0;
    public $duration = 0;
    public $volume = 66;
    public $progress = 0;

    // Track info
    public $title = '';
    public $artist = '';
    public $coverUrl = '';
    public $audioUrl = '';

    protected $listeners = [
        'playBook' => 'playBook'
    ];

    public function mount() {
        // Initialize with default values or load from session
        $this->loadPlayerState();
    }

    #[ On( 'playBook' ) ]

    public function playBook( $id, $title, $artist, $cover_url, $audio_url ) {
        $this->currentBook = $id;
        $this->title = $title;
        $this->artist = $artist;
        $this->coverUrl = $cover_url;
        $this->audioUrl = $audio_url;
        $this->isPlaying = true;
        $this->currentTime = 0;
        $this->progress = 0;

        $this->savePlayerState();
        $this->dispatch( 'loadAudio', audioUrl: $this->audioUrl );
    }

    public function togglePlayback() {
        $this->isPlaying = !$this->isPlaying;
        $this->savePlayerState();
        $this->dispatch( 'toggleAudio' );
    }

    public function previousTrack() {
        // Implement previous track logic
        $this->dispatch( 'previousTrack' );
    }

    public function nextTrack() {
        // Implement next track logic
        $this->dispatch( 'nextTrack' );
    }

    public function seekTo( $percentage ) {
        $this->progress = $percentage;
        $this->dispatch( 'seekAudio', percentage: $percentage );
    }

    public function setVolume( $percentage ) {
        $this->volume = $percentage;
        $this->savePlayerState();
        $this->dispatch( 'setAudioVolume', volume: $percentage / 100 );
    }

    public function updateProgress( $currentTime, $duration ) {
        $this->currentTime = $currentTime;
        $this->duration = $duration;
        $this->progress = $duration > 0 ? ( $currentTime / $duration ) * 100 : 0;
        $this->savePlayerState();
    }

    public function audioEnded() {
        $this->isPlaying = false;
        $this->progress = 0;
        $this->currentTime = 0;
        $this->savePlayerState();
    }

    private function savePlayerState() {
        session( [
            'player_state' => [
                'currentBook' => $this->currentBook,
                'isPlaying' => $this->isPlaying,
                'currentTime' => $this->currentTime,
                'duration' => $this->duration,
                'volume' => $this->volume,
                'progress' => $this->progress,
                'title' => $this->title,
                'artist' => $this->artist,
                'coverUrl' => $this->coverUrl,
                'audioUrl' => $this->audioUrl,
            ]
        ] );
    }

    private function loadPlayerState() {
        $state = session( 'player_state', [] );

        $this->currentBook = $state[ 'currentBook' ] ?? null;
        $this->isPlaying = $state[ 'isPlaying' ] ?? false;
        $this->currentTime = $state[ 'currentTime' ] ?? 0;
        $this->duration = $state[ 'duration' ] ?? 0;
        $this->volume = $state[ 'volume' ] ?? 66;
        $this->progress = $state[ 'progress' ] ?? 0;
        $this->title = $state[ 'title' ] ?? '';
        $this->artist = $state[ 'artist' ] ?? '';
        $this->coverUrl = $state[ 'coverUrl' ] ?? 'https://via.placeholder.com/48';
        $this->audioUrl = $state[ 'audioUrl' ] ?? '';
    }

    public function formatTime( $seconds ) {
        $minutes = floor( $seconds / 60 );
        $remainingSeconds = $seconds % 60;
        return sprintf( '%d:%02d', $minutes, $remainingSeconds );
    }

    public function render() {
        return view( 'livewire.audiobook-player' );
    }
}
