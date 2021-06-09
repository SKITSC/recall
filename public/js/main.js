/* distributed file for client js */

var total_calls_global = 0;

document.addEventListener("DOMContentLoaded", (event) => {

    load_total_calls_plivo();
    load_total_size_recordings();
    sync_worker();
});

// responsive hamburger menu

function toggle_menu() {

    var toggle_element = document.getElementById("toggle-menu");
    if (toggle_element.className === "menu-nav") {
        toggle_element.className += " responsive";
    } else {
        toggle_element.className = "menu-nav";
    }
}

function load_total_calls_plivo() {

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        total_calls_global = this.responseText;
        document.getElementById("total-calls-plivo-cloud").innerHTML = total_calls_global;
      }
    };
    xhttp.open("GET", "utils/total_recordings.php", true);
    xhttp.send();
}

function load_total_size_recordings() {

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("total-size-recordings").innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", "utils/size_recordings.php", true);
    xhttp.send();
}

// web worker

var w;

function sync_worker() {

  if (typeof(Worker) !== "undefined") {
    if (typeof(w) == "undefined") {
      w = new Worker("static/js/sync-worker.min.js");
    }
    w.onmessage = function(event) {
      document.getElementById("sync-recordings").innerHTML = event.data;
    };
  } else {
    document.getElementById("sync-recordings").innerHTML = "Sorry! No Web Worker support.";
  }
}

// audio player

const audio_player_container = document.getElementById('audio-player-container');
const audio = document.querySelector('audio');
const play_icon_container = document.getElementById('play-icon');
const seek_slider = document.getElementById('seek-slider');
const duration_container = document.getElementById('duration');
const current_time_container = document.getElementById('current-time');
let raf = null;

let playState = 'play';

play_icon_container.addEventListener('click', () => {
    if (playState === 'play') {
        audio.play();
        requestAnimationFrame(whilePlaying);
        playState = 'pause';
    } else {
        audio.pause();
        cancelAnimationFrame(raf);
        playState = 'play';
    }
});

const showRangeProgress = (rangeInput) => {
    if(rangeInput === seek_slider) audio_player_container.style.setProperty('--seek-before-width', rangeInput.value / rangeInput.max * 100 + '%');
    else audio_player_container.style.setProperty('--volume-before-width', rangeInput.value / rangeInput.max * 100 + '%');
}

seek_slider.addEventListener('input', (e) => {
    showRangeProgress(e.target);
});

const calculateTime = (secs) => {
    const minutes = Math.floor(secs / 60);
    const seconds = Math.floor(secs % 60);
    const returnedSeconds = seconds < 10 ? `0${seconds}` : `${seconds}`;
    return `${minutes}:${returnedSeconds}`;
}

const displayDuration = () => {
    duration_container.textContent = calculateTime(audio.duration);
}

const setSliderMax = () => {
    seek_slider.max = Math.floor(audio.duration);
}

const displayBufferedAmount = () => {
    const bufferedAmount = Math.floor(audio.buffered.end(audio.buffered.length - 1));
    audio_player_container.style.setProperty('--buffered-width', `${(bufferedAmount / seek_slider.max) * 100}%`);
}

const whilePlaying = () => {
    seek_slider.value = Math.floor(audio.currentTime);
    current_time_container.textContent = calculateTime(seek_slider.value);
    audio_player_container.style.setProperty('--seek-before-width', `${seek_slider.value / seek_slider.max * 100}%`);
    raf = requestAnimationFrame(whilePlaying);
}

if (audio.readyState > 0) {
    displayDuration();
    setSliderMax();
    displayBufferedAmount();
} else {
    audio.addEventListener('loadedmetadata', () => {
        displayDuration();
        setSliderMax();
        displayBufferedAmount();
    });
}

audio.addEventListener('progress', displayBufferedAmount);

seek_slider.addEventListener('input', () => {
    current_time_container.textContent = calculateTime(seek_slider.value);
    if(!audio.paused) {
        cancelAnimationFrame(raf);
    }
});

seek_slider.addEventListener('change', () => {
    audio.currentTime = seek_slider.value;
    if(!audio.paused) {
        requestAnimationFrame(whilePlaying);
    }
});

function play_audio(audio_url) {
  var audio_player_container = document.getElementById("audio-player-container");
  var audio_player = document.getElementById("audio-player");
  audio_player_container.style.display = "block";

  if (play_icon_container.className === "paused") {
    play_icon_container.className = "play";
  } else {
    play_icon_container.className = "paused";
  }

  audio_player.pause();
  audio_player.setAttribute('src', audio_url);
  audio_player.load();
  //audio_player.play();

  playState = 'pause';
  audio_player.play();
  requestAnimationFrame(whilePlaying);
}