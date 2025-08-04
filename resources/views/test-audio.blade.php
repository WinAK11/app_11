{{-- <html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soundwave Music Player</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/audio-player.css') }}" />
</head>

<body>


    <!-- Controles do player fixados na parte inferior -->
    <div class="player-controls">
        <!-- Informações da faixa -->
        <div class="control-track-info">
            <img src="https://via.placeholder.com/48" alt="Pequena capa do álbum" class="control-cover">
            <div>
                <p class="control-title">Midnight City</p>
                <p class="control-artist">M83</p>
            </div>
        </div>

        <!-- Controles de reprodução -->
        <div class="playback-controls">
            <div class="playback-buttons">
                <button class="control-button">
                    <i class="fas fa-step-backward"></i>
                </button>
                <button class="play-pause-button">
                    <i class="fas fa-play"></i>
                </button>
                <button class="control-button">
                    <i class="fas fa-step-forward"></i>
                </button>
            </div>

            <!-- Barra de progresso -->
            <div class="progress-container">
                <span class="time current-time">1:23</span>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <span class="time total-time">3:45</span>
            </div>
        </div>

        <!-- Controle de volume -->
        <div class="volume-control">
            <i class="fas fa-volume-up"></i>
            <div class="volume-bar">
                <div class="volume-fill"></div>
            </div>
        </div>
    </div>
    </div>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Variáveis para elementos da interface
            const navButtons = document.querySelectorAll('.nav-button');
            const viewContents = document.querySelectorAll('.view-content');
            const playPauseButton = document.querySelector('.play-pause-button');
            const playPauseIcon = playPauseButton.querySelector('i');
            const progressFill = document.querySelector('.progress-fill');
            const volumeFill = document.querySelector('.volume-fill');
            const progressBar = document.querySelector('.progress-bar');
            const volumeBar = document.querySelector('.volume-bar');
            const currentTimeDisplay = document.querySelector('.current-time');
            const totalTimeDisplay = document.querySelector('.total-time');

            // Variáveis de estado
            let isPlaying = false;
            let currentProgress = 33; // Porcentagem inicial (1:23 de 3:45)
            let currentVolume = 66; // Porcentagem inicial

            // Dados da música atual
            const currentTrack = {
                title: "Midnight City",
                artist: "M83",
                album: "Hurry Up, We're Dreaming",
                duration: 225, // 3:45 em segundos
                currentTime: 83 // 1:23 em segundos
            };

            // Função para alternar entre visualizações
            function switchView(viewId) {
                // Remover classe 'active' de todos os botões e views
                navButtons.forEach(button => button.classList.remove('active'));
                viewContents.forEach(view => view.classList.remove('active'));

                // Adicionar classe 'active' ao botão e view correspondente
                const selectedButton = document.querySelector(`[data-view="${viewId}"]`);
                const selectedView = document.getElementById(`${viewId}-view`);

                if (selectedButton && selectedView) {
                    selectedButton.classList.add('active');
                    selectedView.classList.add('active');
                }
            }

            // Alternar reprodução (play/pause)
            function togglePlayback() {
                isPlaying = !isPlaying;

                if (isPlaying) {
                    playPauseIcon.classList.remove('fa-play');
                    playPauseIcon.classList.add('fa-pause');
                    // Aqui você adicionaria lógica para iniciar a reprodução do áudio
                } else {
                    playPauseIcon.classList.remove('fa-pause');
                    playPauseIcon.classList.add('fa-play');
                    // Aqui você adicionaria lógica para pausar a reprodução do áudio
                }
            }

            // Atualizar a barra de progresso
            function updateProgress(percent) {
                progressFill.style.width = `${percent}%`;

                // Calcular e exibir o tempo atual baseado na porcentagem
                const timeInSeconds = Math.floor((percent / 100) * currentTrack.duration);
                currentTimeDisplay.textContent = formatTime(timeInSeconds);
            }

            // Atualizar o volume
            function updateVolume(percent) {
                volumeFill.style.width = `${percent}%`;
                currentVolume = percent;
                // Aqui você adicionaria lógica para alterar o volume real do áudio
            }

            // Formatar segundos em MM:SS
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            }

            // Inicializar os displays de tempo
            function initTimeDisplays() {
                currentTimeDisplay.textContent = formatTime(currentTrack.currentTime);
                totalTimeDisplay.textContent = formatTime(currentTrack.duration);
            }

            // Simular progresso da música
            function simulateProgress() {
                if (isPlaying) {
                    currentProgress += 0.1;
                    if (currentProgress > 100) {
                        currentProgress = 0;
                        togglePlayback(); // Pausa quando a música termina
                    }
                    updateProgress(currentProgress);
                }
            }

            // Event listeners
            navButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const viewId = button.getAttribute('data-view');
                    switchView(viewId);
                });
            });

            playPauseButton.addEventListener('click', togglePlayback);

            // Event listeners para cliques nas barras de progresso e volume
            progressBar.addEventListener('click', (e) => {
                const rect = progressBar.getBoundingClientRect();
                const clickPosition = e.clientX - rect.left;
                const percent = (clickPosition / rect.width) * 100;
                currentProgress = percent;
                updateProgress(percent);
            });

            volumeBar.addEventListener('click', (e) => {
                const rect = volumeBar.getBoundingClientRect();
                const clickPosition = e.clientX - rect.left;
                const percent = (clickPosition / rect.width) * 100;
                updateVolume(percent);
            });

            // Inicializar a interface
            initTimeDisplays();
            updateProgress(currentProgress);
            updateVolume(currentVolume);

            // Adicionar interatividade aos elementos da biblioteca de música
            const cards = document.querySelectorAll('.playlist-card, .album-card, .genre-card');
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    // Aqui você adicionaria lógica para carregar e iniciar uma playlist ou álbum
                    console.log('Card clicado:', card);
                });
            });

            // Botões de ação para a música atual
            const actionButtons = document.querySelectorAll('.action-button');
            actionButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Aqui você adicionaria lógica para favoritar, compartilhar, etc.
                    console.log('Ação:', button);
                });
            });

            // Simular progresso da música a cada 100ms quando estiver tocando
            setInterval(simulateProgress, 100);
        });
    </script>
</body>

</html> --}}
