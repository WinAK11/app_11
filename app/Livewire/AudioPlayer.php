<?php

namespace App\Livewire;

use Livewire\Component;

class AudioPlayer extends Component {
    public $visible = false;
    public $isPlaying = false;
    public $progress = 0;
    public $volume = 66;
    protected $listeners = [ 'show-audio-player' => 'show' ];

    public $track = [
        'title' => 'Midnight City',
        'artist' => 'M83',
        'duration' => 225,
    ];

    public function show() {
        logger( 'AudioPlayer opened' );
        $this->visible = true;
    }

    public function hide() {
        $this->visible = false;
    }

    public function togglePlayback() {
        $this->isPlaying = !$this->isPlaying;
    }

    public function render() {
        return view( 'livewire.audio-player' );
    }
}
