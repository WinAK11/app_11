@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Audiobook Chapters - {{ $ebook->title }}</h3>
                <ul class="breadcrumbs flex items-center gap10">
                    <li><a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><a href="{{ route('admin.audiobook.add') }}">
                            <div class="text-tiny">Audiobooks</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">Chapters</div>
                    </li>
                </ul>
            </div>

            <!-- Ebook Info -->
            <div class="wg-box mb-20">
                <div class="flex items-center gap20">
                    @if ($ebook->cover_path)
                        <img src="{{ asset($ebook->cover_path) }}" alt="Cover"
                            style="width: 80px; height: 120px; object-fit: cover; border-radius: 8px;">
                    @else
                        <div
                            style="width: 80px; height: 120px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 2px dashed #dee2e6;">
                            <i class="icon-book" style="font-size: 24px; color: #6c757d;"></i>
                        </div>
                    @endif
                    <div>
                        <h4 style="margin: 0 0 8px 0;">{{ $ebook->title }}</h4>
                        <p style="margin: 0 0 4px 0; color: #6c757d;">by {{ $ebook->author }}</p>
                        <p style="margin: 0; font-size: 14px; color: #6c757d;">{{ $chapters->count() }} chapters extracted
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="wg-box mb-20">
                <div class="flex items-center gap10">
                    <button id="generate-all-btn" class="tf-button">
                        <i class="icon-volume-2"></i> Generate All Audio
                    </button>
                    <button id="stop-generation-btn" class="tf-button" style="background: #dc3545; display: none;">
                        <i class="icon-square"></i> Stop Generation
                    </button>
                    <form method="POST" action="{{ route('admin.audiobook.chapters.delete', $ebook->id) }}"
                        style="display: inline;"
                        onsubmit="return confirm('Are you sure you want to delete all chapters and audio files?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" style="font-weight: bold">
                            <i class="icon-trash-2"></i> Delete All Chapters
                        </button>
                    </form>
                    <a href="{{ route('admin.audiobook.regenerate', $ebook->id) }}" class="tf-button"
                        style="background: #17a2b8;"
                        onclick="return confirm('This will delete existing chapters. Continue?')">
                        <i class="icon-refresh-cw"></i> Re-extract Chapters
                    </a>
                </div>
            </div>

            <!-- Progress Overview -->
            <div id="overall-progress" class="wg-box mb-20" style="display: none;">
                <h5>Generation Progress</h5>
                <div class="progress-bar"
                    style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden; margin: 10px 0;">
                    <div id="overall-progress-fill"
                        style="background: #28a745; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <div id="overall-progress-text" style="font-size: 14px; color: #6c757d;">
                    Ready to generate audio...
                </div>
            </div>

            <!-- Chapters List -->
            <div class="wg-box">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Chapter</th>
                                <th>Title</th>
                                <th style="width: 120px;">Text Length</th>
                                <th style="width: 120px;">Audio Status</th>
                                <th style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chapters as $chapter)
                                <tr id="chapter-row-{{ $chapter->id }}">
                                    <td>
                                        <span class="badge"
                                            style="background: #007bff; color: white; padding: 4px 8px; border-radius: 12px;">
                                            {{ $chapter->index }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="max-width: 300px;">
                                            <div style="font-weight: 600; margin-bottom: 4px;">
                                                {{ $chapter->title ?: 'Chapter ' . $chapter->index }}</div>
                                            <div
                                                style="font-size: 12px; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Str::limit($chapter->text, 100) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size: 12px; color: #6c757d;">
                                            {{ number_format(strlen($chapter->text)) }} chars
                                        </span>
                                    </td>
                                    <td>
                                        <div id="status-{{ $chapter->id }}">
                                            @if ($chapter->audio_path)
                                                <span class="badge"
                                                    style="background: #28a745; color: white; padding: 4px 8px; border-radius: 12px;">
                                                    <i class="icon-check"></i> Generated
                                                </span>
                                            @else
                                                <span class="badge"
                                                    style="background: #6c757d; color: white; padding: 4px 8px; border-radius: 12px;">
                                                    <i class="icon-clock"></i> Pending
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            @if ($chapter->audio_path)
                                                <button class="btn-lg btn-primary"
                                                    onclick="playAudio('{{ asset($chapter->audio_path) }}', {{ $chapter->id }})"
                                                    style="background: #17a2b8;">
                                                    <i class="icon-play"></i>
                                                </button>
                                                <a href="{{ asset($chapter->audio_path) }}" download
                                                    class="btn-lg btn-primary" style="background: #28a745;">
                                                    <i class="icon-download"></i>
                                                </a>
                                            @endif
                                            <button class="btn-lg btn-secondary generate-single-btn"
                                                data-chapter-id="{{ $chapter->id }}" style="background: #ffc107;">
                                                <i class="icon-volume-2"></i>
                                            </button>
                                            <button class="btn-lg btn-secondary"
                                                onclick="viewChapterText({{ $chapter->id }})"
                                                style="background: #6f42c1;">
                                                <i class="icon-eye"></i>
                                            </button>
                                        </div>
                                        <div id="chapter-progress-{{ $chapter->id }}"
                                            style="display: none; margin-top: 8px;">
                                            <div class="progress-bar"
                                                style="background: #e9ecef; height: 8px; border-radius: 4px; overflow: hidden;">
                                                <div class="progress-fill"
                                                    style="background: #ffc107; height: 100%; width: 0%; transition: width 0.3s;">
                                                </div>
                                            </div>
                                            <div class="progress-text"
                                                style="font-size: 11px; color: #6c757d; margin-top: 2px;">
                                                Generating...
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Player Modal -->
    <div id="audio-modal" class="modal"
        style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background: white; margin: 5% auto; padding: 20px; width: 500px; border-radius: 8px; position: relative;">
            <span class="close" onclick="closeAudioModal()"
                style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer;">&times;</span>
            <h5 id="audio-modal-title">Audio Player</h5>
            <audio id="audio-player" controls style="width: 100%; margin-top: 15px;">
                Your browser does not support the audio element.
            </audio>
        </div>
    </div>

    <!-- Chapter Text Modal -->
    <div id="text-modal" class="modal"
        style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background: white; margin: 2% auto; padding: 20px; width: 80%; max-width: 800px; height: 80%; border-radius: 8px; position: relative; overflow-y: auto;">
            <span class="close" onclick="closeTextModal()"
                style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer;">&times;</span>
            <h5 id="text-modal-title">Chapter Text</h5>
            <div id="text-modal-content" style="margin-top: 15px; line-height: 1.6; font-size: 14px;">
                <!-- Chapter text will be loaded here -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isGenerating = false;
            let currentGeneration = null;

            // Chapter data for JavaScript access
            const chapters = {!! json_encode(
                $chapters->map(function ($chapter) {
                    return [
                        'id' => $chapter->id,
                        'title' => $chapter->title ?: 'Chapter ' . $chapter->index,
                        'text' => $chapter->text,
                        'has_audio' => !empty($chapter->audio_path),
                    ];
                }),
            ) !!};

            // Generate all audio
            document.getElementById('generate-all-btn').addEventListener('click', function() {
                if (isGenerating) return;
                generateAllAudio();
            });

            // Stop generation
            document.getElementById('stop-generation-btn').addEventListener('click', function() {
                stopGeneration();
            });

            // Generate single chapter audio
            document.querySelectorAll('.generate-single-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chapterId = this.dataset.chapterId;
                    generateSingleAudio(chapterId);
                });
            });

            async function generateAllAudio() {
                isGenerating = true;
                document.getElementById('generate-all-btn').style.display = 'none';
                document.getElementById('stop-generation-btn').style.display = 'inline-block';
                document.getElementById('overall-progress').style.display = 'block';

                const chaptersToGenerate = chapters.filter(ch => !ch.has_audio);
                let completed = 0;

                for (const chapter of chaptersToGenerate) {
                    if (!isGenerating) break;

                    updateOverallProgress((completed / chaptersToGenerate.length) * 100,
                        `Generating audio for: ${chapter.title}`);

                    await generateSingleAudio(chapter.id, false);
                    completed++;
                }

                updateOverallProgress(100, 'All audio generation completed!');
                resetGenerationState();
            }

            async function generateSingleAudio(chapterId, showIndividualProgress = true) {
                const chapter = chapters.find(ch => ch.id == chapterId);
                if (!chapter) return;

                const statusEl = document.getElementById(`status-${chapterId}`);
                const progressEl = document.getElementById(`chapter-progress-${chapterId}`);

                if (showIndividualProgress) {
                    progressEl.style.display = 'block';
                    updateChapterProgress(chapterId, 0, 'Starting generation...');
                }

                statusEl.innerHTML =
                    '<span class="badge" style="background: #ffc107; color: white; padding: 4px 8px; border-radius: 12px;"><i class="icon-loader"></i> Generating...</span>';

                try {
                    // Simulate TTS generation (replace with actual API call)
                    if (showIndividualProgress) {
                        updateChapterProgress(chapterId, 25, 'Processing text...');
                    }

                    // TODO: Replace this with actual TTS API call
                    // For now, we'll simulate the process
                    await new Promise(resolve => setTimeout(resolve, 2000));

                    if (showIndividualProgress) {
                        updateChapterProgress(chapterId, 75, 'Generating audio...');
                    }

                    await new Promise(resolve => setTimeout(resolve, 1000));

                    if (showIndividualProgress) {
                        updateChapterProgress(chapterId, 100, 'Complete!');
                    }

                    // TODO: Make actual API call to generate audio
                    const response = await fetch('{{ route('admin.audiobook.generate.chapter') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            chapter_id: chapterId
                        })
                    });

                    if (response.ok) {
                        const result = await response.json();
                        statusEl.innerHTML =
                            '<span class="badge" style="background: #28a745; color: white; padding: 4px 8px; border-radius: 12px;"><i class="icon-check"></i> Generated</span>';

                        // Update the row with new audio controls
                        location.reload(); // Simple reload for now, can be optimized
                    } else {
                        throw new Error('Generation failed');
                    }

                } catch (error) {
                    console.error('Audio generation error:', error);
                    statusEl.innerHTML =
                        '<span class="badge" style="background: #dc3545; color: white; padding: 4px 8px; border-radius: 12px;"><i class="icon-x"></i> Failed</span>';

                    if (showIndividualProgress) {
                        updateChapterProgress(chapterId, 0, 'Generation failed');
                    }
                } finally {
                    if (showIndividualProgress) {
                        setTimeout(() => {
                            progressEl.style.display = 'none';
                        }, 2000);
                    }
                }
            }

            function stopGeneration() {
                isGenerating = false;
                resetGenerationState();
            }

            function resetGenerationState() {
                document.getElementById('generate-all-btn').style.display = 'inline-block';
                document.getElementById('stop-generation-btn').style.display = 'none';
                setTimeout(() => {
                    document.getElementById('overall-progress').style.display = 'none';
                }, 3000);
                isGenerating = false;
            }

            function updateOverallProgress(percentage, text) {
                document.getElementById('overall-progress-fill').style.width = percentage + '%';
                document.getElementById('overall-progress-text').textContent = text;
            }

            function updateChapterProgress(chapterId, percentage, text) {
                const progressEl = document.querySelector(`#chapter-progress-${chapterId} .progress-fill`);
                const textEl = document.querySelector(`#chapter-progress-${chapterId} .progress-text`);

                if (progressEl) progressEl.style.width = percentage + '%';
                if (textEl) textEl.textContent = text;
            }

            // Audio player functions
            window.playAudio = function(audioUrl, chapterId) {
                const modal = document.getElementById('audio-modal');
                const player = document.getElementById('audio-player');
                const title = document.getElementById('audio-modal-title');

                const chapter = chapters.find(ch => ch.id == chapterId);
                title.textContent = chapter ? chapter.title : 'Audio Player';

                player.src = audioUrl;
                modal.style.display = 'block';
            };

            window.closeAudioModal = function() {
                const modal = document.getElementById('audio-modal');
                const player = document.getElementById('audio-player');

                player.pause();
                player.src = '';
                modal.style.display = 'none';
            };

            // Chapter text viewer functions
            window.viewChapterText = function(chapterId) {
                const chapter = chapters.find(ch => ch.id == chapterId);
                if (!chapter) return;

                const modal = document.getElementById('text-modal');
                const title = document.getElementById('text-modal-title');
                const content = document.getElementById('text-modal-content');

                title.textContent = chapter.title;
                content.innerHTML = `<p style="white-space: pre-wrap; line-height: 1.8;">${chapter.text}</p>`;

                modal.style.display = 'block';
            };

            window.closeTextModal = function() {
                document.getElementById('text-modal').style.display = 'none';
            };

            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                const audioModal = document.getElementById('audio-modal');
                const textModal = document.getElementById('text-modal');

                if (event.target === audioModal) {
                    closeAudioModal();
                }
                if (event.target === textModal) {
                    closeTextModal();
                }
            });
        });
    </script>
@endpush
