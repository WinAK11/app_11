@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Text-to-Speech Settings</h3>
                <ul class="breadcrumbs flex items-center gap10">
                    <li><a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">TTS Settings</div>
                    </li>
                </ul>
            </div>

            <!-- Connection Test -->
            <div class="wg-box mb-20">
                <div class="flex items-center justify-between mb-20">
                    <h5>Amazon Polly Connection</h5>
                    <button id="test-connection-btn" class="tf-button" style="background: #17a2b8;">
                        <i class="icon-wifi"></i> Test Connection
                    </button>
                </div>
                <div id="connection-status" class="alert" style="display: none;">
                    <!-- Connection status will be displayed here -->
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>AWS Region:</label>
                            <div style="padding: 8px; background: #f8f9fa; border-radius: 4px; font-family: monospace;">
                                {{ env('AWS_DEFAULT_REGION', 'Not configured') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Access Key:</label>
                            <div style="padding: 8px; background: #f8f9fa; border-radius: 4px; font-family: monospace;">
                                {{ env('AWS_ACCESS_KEY_ID') ? '••••••••' . substr(env('AWS_ACCESS_KEY_ID'), -4) : 'Not configured' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voice Selection -->
            <div class="wg-box mb-20">
                <div class="flex items-center justify-between mb-20">
                    <h5>Available Voices</h5>
                    <button id="refresh-voices-btn" class="tf-button">
                        <i class="icon-refresh-cw"></i> Refresh Voices
                    </button>
                </div>

                <div class="row mb-20">
                    <div class="col-md-6">
                        <select id="language-filter" class="form-control">
                            <option value="">All Languages</option>
                            <option value="en-US">English (US)</option>
                            <option value="en-GB">English (UK)</option>
                            <option value="en-AU">English (AU)</option>
                            <option value="es-ES">Spanish (Spain)</option>
                            <option value="es-MX">Spanish (Mexico)</option>
                            <option value="fr-FR">French</option>
                            <option value="de-DE">German</option>
                            <option value="it-IT">Italian</option>
                            <option value="pt-BR">Portuguese (Brazil)</option>
                            <option value="ja-JP">Japanese</option>
                            <option value="ko-KR">Korean</option>
                            <option value="zh-CN">Chinese (Mandarin)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select id="gender-filter" class="form-control">
                            <option value="">All Genders</option>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                        </select>
                    </div>
                </div>

                <div id="voices-loading" style="text-align: center; padding: 20px; display: none;">
                    <i class="icon-loader" style="animation: spin 1s linear infinite;"></i> Loading voices...
                </div>

                <div id="voices-container">
                    <!-- Voices will be loaded here -->
                </div>
            </div>

            <!-- Current Settings -->
            <div class="wg-box mb-20">
                <h5>Current Default Settings</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Default Voice:</label>
                            <div style="padding: 8px; background: #f8f9fa; border-radius: 4px;">
                                {{ env('AWS_POLLY_VOICE_ID', 'Joanna') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Output Format:</label>
                            <div style="padding: 8px; background: #f8f9fa; border-radius: 4px;">
                                {{ env('AWS_POLLY_OUTPUT_FORMAT', 'mp3') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sample Rate:</label>
                            <div style="padding: 8px; background: #f8f9fa; border-radius: 4px;">
                                {{ env('AWS_POLLY_SAMPLE_RATE', '22050') }} Hz
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Total Audiobooks:</label>
                            <div style="padding: 8px; background: #f8f9fa; border-radius: 4px;">
                                {{ App\Models\Ebook::whereHas('chapters', function ($q) {$q->whereNotNull('audio_path');})->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="wg-box">
                <h5>Usage Statistics</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card"
                            style="background: #e3f2fd; padding: 20px; border-radius: 8px; text-align: center;">
                            <h4 style="margin: 0; color: #1976d2;">
                                {{ App\Models\Chapter::whereNotNull('audio_path')->count() }}</h4>
                            <p style="margin: 5px 0 0 0; color: #666;">Chapters with Audio</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card"
                            style="background: #e8f5e8; padding: 20px; border-radius: 8px; text-align: center;">
                            <h4 style="margin: 0; color: #388e3c;">
                                {{ App\Models\Chapter::whereNull('audio_path')->count() }}</h4>
                            <p style="margin: 5px 0 0 0; color: #666;">Pending Chapters</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card"
                            style="background: #fff3e0; padding: 20px; border-radius: 8px; text-align: center;">
                            <h4 style="margin: 0; color: #f57c00;">
                                @php
                                    // $totalDuration = App\Models\Chapter::whereNotNull('audio_duration')->sum(
                                    //     'audio_duration',
                                    // );
                                    $totalDuration = 0;
                                    $hours = floor($totalDuration / 3600);
                                    $minutes = floor(($totalDuration % 3600) / 60);
                                    echo "{$hours}h {$minutes}m";
                                @endphp
                            </h4>
                            <p style="margin: 5px 0 0 0; color: #666;">Total Audio Duration</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card"
                            style="background: #fce4ec; padding: 20px; border-radius: 8px; text-align: center;">
                            <h4 style="margin: 0; color: #c2185b;">
                                @php
                                    $audioPath = public_path('uploads/audiobooks');
                                    $totalSize = 0;
                                    if (is_dir($audioPath)) {
                                        $files = new RecursiveIteratorIterator(
                                            new RecursiveDirectoryIterator($audioPath),
                                        );
                                        foreach ($files as $file) {
                                            if (
                                                $file->isFile() &&
                                                in_array($file->getExtension(), ['mp3', 'wav', 'ogg'])
                                            ) {
                                                $totalSize += $file->getSize();
                                            }
                                        }
                                    }
                                    echo number_format($totalSize / 1048576, 1) . ' MB';
                                @endphp
                            </h4>
                            <p style="margin: 5px 0 0 0; color: #666;">Storage Used</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Voice Preview Modal -->
    <div id="voice-preview-modal" class="modal"
        style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background: white; margin: 5% auto; padding: 20px; width: 500px; border-radius: 8px; position: relative;">
            <span class="close" onclick="closePreviewModal()"
                style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer;">&times;</span>
            <h5 id="preview-modal-title">Voice Preview</h5>
            <div id="preview-content" style="margin-top: 15px;">
                <textarea id="preview-text"
                    style="width: 100%; height: 80px; margin-bottom: 15px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                    placeholder="Enter text to preview this voice...">Hello! This is a sample of how this voice sounds when reading your audiobook content. You can edit this text to test different phrases.</textarea>
                <div style="margin-bottom: 15px;">
                    <button id="generate-preview-btn" class="tf-button">
                        <i class="icon-volume-2"></i> Generate Preview
                    </button>
                    <span id="preview-status" style="margin-left: 10px; font-size: 12px; color: #6c757d;"></span>
                </div>
                <audio id="preview-audio" controls style="width: 100%; display: none;">
                    Your browser does not support the audio element.
                </audio>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let allVoices = [];

            // Load voices on page load
            loadVoices();

            // Event listeners
            document.getElementById('test-connection-btn').addEventListener('click', testConnection);
            document.getElementById('refresh-voices-btn').addEventListener('click', loadVoices);
            document.getElementById('language-filter').addEventListener('change', filterVoices);
            document.getElementById('gender-filter').addEventListener('change', filterVoices);

            async function testConnection() {
                const btn = document.getElementById('test-connection-btn');
                const statusEl = document.getElementById('connection-status');

                btn.disabled = true;
                btn.innerHTML =
                    '<i class="icon-loader" style="animation: spin 1s linear infinite;"></i> Testing...';

                try {
                    const response = await fetch('{{ route('admin.audiobook.polly.test') }}');
                    const result = await response.json();

                    statusEl.style.display = 'block';
                    if (result.success) {
                        statusEl.className = 'alert alert-success';
                        statusEl.innerHTML = `<i class="icon-check"></i> ${result.message}`;
                    } else {
                        statusEl.className = 'alert alert-danger';
                        statusEl.innerHTML = `<i class="icon-x"></i> ${result.message}`;
                    }

                } catch (error) {
                    statusEl.style.display = 'block';
                    statusEl.className = 'alert alert-danger';
                    statusEl.innerHTML = `<i class="icon-x"></i> Connection test failed: ${error.message}`;
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="icon-wifi"></i> Test Connection';
                }
            }

            async function loadVoices() {
                const btn = document.getElementById('refresh-voices-btn');
                const container = document.getElementById('voices-container');
                const loading = document.getElementById('voices-loading');

                btn.disabled = true;
                btn.innerHTML =
                    '<i class="icon-loader" style="animation: spin 1s linear infinite;"></i> Loading...';
                loading.style.display = 'block';
                container.innerHTML = '';

                try {
                    const response = await fetch('{{ route('admin.audiobook.voice.get') }}');
                    const result = await response.json();

                    if (result.success) {
                        allVoices = result.voices;
                        displayVoices(allVoices);
                    } else {
                        container.innerHTML =
                            `<div class="alert alert-danger">Failed to load voices: ${result.message}</div>`;
                    }

                } catch (error) {
                    container.innerHTML =
                        `<div class="alert alert-danger">Error loading voices: ${error.message}</div>`;
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="icon-refresh-cw"></i> Refresh Voices';
                    loading.style.display = 'none';
                }
            }

            function displayVoices(voices) {
                const container = document.getElementById('voices-container');

                if (!voices || voices.length === 0) {
                    container.innerHTML = '<div class="alert alert-info">No voices found.</div>';
                    return;
                }

                const voicesHtml = voices.map(voice => {
                    const isDefault = voice.Id === '{{ env('AWS_POLLY_VOICE_ID', 'Joanna') }}';

                    return `
                        <div class="voice-card" style="border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; margin-bottom: 10px; ${isDefault ? 'background: #e8f5e8; border-color: #28a745;' : ''}">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h6 style="margin: 0; color: #007bff;">
                                        ${voice.Name}
                                        ${isDefault ? '<span class="badge" style="background: #28a745; color: white; font-size: 10px; margin-left: 8px;">DEFAULT</span>' : ''}
                                    </h6>
                                    <small style="color: #6c757d;">${voice.Id}</small>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge" style="background: ${voice.Gender === 'Female' ? '#e91e63' : '#2196f3'}; color: white; padding: 4px 8px; border-radius: 12px;">
                                        ${voice.Gender}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <small style="color: #6c757d;">
                                        <code>${voice.LanguageCode}</code>
                                    </small>
                                </div>
                                <div class="col-md-2">
                                    ${voice.SupportedEngines ? voice.SupportedEngines.map(engine =>
                                        `<span class="badge" style="background: #f8f9fa; color: #6c757d; font-size: 10px; margin: 2px;">${engine}</span>`
                                    ).join('') : ''}
                                </div>
                                <div class="col-md-2">
                                    <button class="tf-button-small" onclick="previewVoice('${voice.Id}', '${voice.Name}')" style="background: #17a2b8;">
                                        <i class="icon-volume-2"></i> Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                container.innerHTML = voicesHtml;
            }

            function filterVoices() {
                const languageFilter = document.getElementById('language-filter').value;
                const genderFilter = document.getElementById('gender-filter').value;

                let filteredVoices = allVoices;

                if (languageFilter) {
                    filteredVoices = filteredVoices.filter(voice => voice.LanguageCode === languageFilter);
                }

                if (genderFilter) {
                    filteredVoices = filteredVoices.filter(voice => voice.Gender === genderFilter);
                }

                displayVoices(filteredVoices);
            }

            window.previewVoice = function(voiceId, voiceName) {
                const modal = document.getElementById('voice-preview-modal');
                const title = document.getElementById('preview-modal-title');
                const textArea = document.getElementById('preview-text');
                const audio = document.getElementById('preview-audio');

                title.textContent = `Preview: ${voiceName}`;
                audio.style.display = 'none';
                audio.src = '';

                modal.style.display = 'block';

                // Set up preview generation
                document.getElementById('generate-preview-btn').onclick = function() {
                    generateVoicePreview(voiceId, textArea.value);
                };
            };

            async function generateVoicePreview(voiceId, text) {
                const btn = document.getElementById('generate-preview-btn');
                const status = document.getElementById('preview-status');
                const audio = document.getElementById('preview-audio');

                if (!text.trim()) {
                    alert('Please enter some text to preview.');
                    return;
                }

                btn.disabled = true;
                btn.innerHTML =
                    '<i class="icon-loader" style="animation: spin 1s linear infinite;"></i> Generating...';
                status.textContent = 'Generating preview...';

                try {
                    const response = await fetch('{{ route('admin.audiobook.generate.preview') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            voice_id: voiceId,
                            text: text.substring(0, 500) // Limit preview text
                        })
                    });

                    const result = await response.json();

                    if (result.success && result.audio_url) {
                        audio.src = result.audio_url;
                        audio.style.display = 'block';
                        audio.play();
                        status.textContent = 'Preview generated successfully!';
                    } else {
                        status.textContent = 'Preview generation failed: ' + (result.message ||
                            'Unknown error');
                    }

                } catch (error) {
                    status.textContent = 'Error generating preview: ' + error.message;
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="icon-volume-2"></i> Generate Preview';

                    setTimeout(() => {
                        status.textContent = '';
                    }, 5000);
                }
            }

            window.closePreviewModal = function() {
                const modal = document.getElementById('voice-preview-modal');
                const audio = document.getElementById('preview-audio');

                audio.pause();
                audio.src = '';
                modal.style.display = 'none';
            };

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('voice-preview-modal');
                if (event.target === modal) {
                    closePreviewModal();
                }
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closePreviewModal();
                }
            });
        });
    </script>

    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .voice-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush
