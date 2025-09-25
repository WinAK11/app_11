<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            margin: 0;
            background: #f8f8f8;
        }

        audio {
            width: 100%;
            height: 60px;
        }
    </style>
</head>

<body>
    @persist('player')
        <audio controls autoplay>
            <source src="{{ secure_asset('uploads/audiobooks/audio-test.mp3') }}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    @endpersist

</body>

</html>
