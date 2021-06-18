/*
*   Date: 09-06-2021
*   Author: Iyad Al-Kassab @ SKITSC
*   Description: js for audio page
*/

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
          audio.pause();
          requestAnimationFrame(whilePlaying);
          playState = 'pause';
          play_icon_container.className = "play";
      } else {
          audio.play();
          cancelAnimationFrame(raf);
          playState = 'play';
          play_icon_container.className = "paused";
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
  
    audio_player.pause();
    audio_player.setAttribute('src', audio_url);
    audio_player.load();
    //audio_player.play();
  
    play_icon_container.className = "paused";
    playState = 'play';
    audio_player.play();
    requestAnimationFrame(whilePlaying);
  }