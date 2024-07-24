<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP3 Playlist Player</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        #playlist {
            list-style: none;
            padding: 0;
        }
        .playlist-item {
            background-color: #fff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .playlist-item:hover {
            background-color: #e0e0e0;
        }
        .playlist-item.active {
            background-color: #3498db;
            color: #fff;
        }
        #player-container {
            background-color: #fff;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        #player-container h3 {
            color: #2980b9;
            margin-top: 0;
        }
        #audio-player {
            display: none;
        }
        #custom-player {
            margin-top: 10px;
        }
        #progress-container {
            background-color: #ddd;
            height: 10px;
            position: relative;
            cursor: pointer;
            border-radius: 5px;
        }
        #progress-bar {
            background-color: #3498db;
            height: 100%;
            width: 0;
            border-radius: 5px;
        }
        #time-display {
            text-align: right;
            margin-top: 5px;
            font-size: 14px;
        }
        #controls {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        #controls button {
            margin: 0 5px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        #controls button:hover {
            background-color: #2980b9;
        }
        #volume-control {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        #volume-control label {
            margin-right: 10px;
        }

        #volume {
            width: 100px;
        }
    </style>
</head>
<body>
<h1>MP3 Playlist Player</h1>
<?php
$mp3Files = glob("*.mp3");

if (empty($mp3Files)) {
    echo "<p class='no-mp3'>No MP3 files found in the current directory.</p>";
} else {
    echo "<ul id='playlist'>";
    foreach ($mp3Files as $file) {
        echo "<li class='playlist-item' data-src='" . htmlspecialchars($file) . "'>" . htmlspecialchars($file) . "</li>";
    }
    echo "</ul>";

    echo "<div id='player-container'>";
      echo "<h3 id='now-playing'>Select a song to play</h3>";
      echo "<audio id='audio-player'></audio>";
      echo "<div id='custom-player'>";
        echo "<div id='progress-container'><div id='progress-bar'></div></div>";
        echo "<div id='time-display'>0:00 / 0:00</div>";
      echo "</div>";
      echo "<div id='controls'>";
        echo "<button id='prev'>&#9198;</button>";
        echo "<button id='play-pause'>&#9658;</button>";
        echo "<button id='stop'>&#9724;</button>";
        echo "<button id='next'>&#9197;</button>";
      echo "</div>";
      echo "<div id='volume-control'>";
        echo "<label for='volume'>Volume:</label>";
        echo "<input type='range' id='volume' min='0' max='1' step='0.1' value='1'>";
      echo "</div>";
    echo "</div>";
}
?>

<script>
    const playlist = document.getElementById('playlist');
    const audioPlayer = document.getElementById('audio-player');
    const nowPlaying = document.getElementById('now-playing');
    const prevButton = document.getElementById('prev');
    const playPauseButton = document.getElementById('play-pause');
    const stopButton = document.getElementById('stop');
    const nextButton = document.getElementById('next');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const timeDisplay = document.getElementById('time-display');
    const volumeControl = document.getElementById('volume');
    let currentTrack = 0;

    function playSong(index) {
        const songs = playlist.getElementsByTagName('li');
        if (index >= 0 && index < songs.length) {
            const song = songs[index];
            audioPlayer.src = song.getAttribute('data-src');
            audioPlayer.play();
            nowPlaying.textContent = "Now Playing: " + song.textContent;
            playPauseButton.textContent = "Pause";

            Array.from(songs).forEach(s => s.classList.remove('active'));
            song.classList.add('active');

            currentTrack = index;
        }
    }

    function togglePlayPause() {
        if (audioPlayer.paused) {
            if (!audioPlayer.src) {
                playSong(0);
            } else {
                audioPlayer.play();
            }
            playPauseButton.innerHTML = "&#10074;&#10074;"; // Pause symbol
        } else {
            audioPlayer.pause();
            playPauseButton.innerHTML = "&#9658;"; // Play symbol
        }
    }

    playlist.addEventListener('click', function(e) {
        if (e.target && e.target.nodeName === "LI") {
            const clickedIndex = Array.from(playlist.children).indexOf(e.target);
            playSong(clickedIndex);
        }
    });

    audioPlayer.addEventListener('ended', function() {
        playSong(currentTrack + 1);
    });

    prevButton.addEventListener('click', function() {
        playSong(currentTrack - 1);
    });

    playPauseButton.addEventListener('click', togglePlayPause);

    stopButton.addEventListener('click', function() {
        audioPlayer.pause();
        audioPlayer.currentTime = 0;
        playPauseButton.innerHTML = "&#9658;"; // Play symbol
    });

    nextButton.addEventListener('click', function() {
        playSong(currentTrack + 1);
    });

    audioPlayer.addEventListener('play', function() {
        playPauseButton.innerHTML = "&#10074;&#10074;"; // Pause symbol
    });

    audioPlayer.addEventListener('pause', function() {
        playPauseButton.innerHTML = "&#9658;"; // Play symbol
    });

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        seconds = Math.floor(seconds % 60);
        return `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }

    audioPlayer.addEventListener('timeupdate', function() {
        const progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
        progressBar.style.width = `${progress}%`;
        timeDisplay.textContent = `${formatTime(audioPlayer.currentTime)} / ${formatTime(audioPlayer.duration)}`;
    });

    progressContainer.addEventListener('click', function(e) {
        const clickPosition = (e.pageX - this.offsetLeft) / this.offsetWidth;
        audioPlayer.currentTime = clickPosition * audioPlayer.duration;
    });

    audioPlayer.addEventListener('loadedmetadata', function() {
        timeDisplay.textContent = `0:00 / ${formatTime(audioPlayer.duration)}`;
    });

    volumeControl.addEventListener('input', function() {
        audioPlayer.volume = this.value;
    });

    // Optional: Update volume slider if the audio's volume is changed elsewhere
    audioPlayer.addEventListener('volumechange', function() {
        volumeControl.value = audioPlayer.volume;
    });
</script>
</body>
</html>
