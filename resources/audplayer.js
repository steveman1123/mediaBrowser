//run init fxn on load
document.onload = initplaylists();

//init the players
function initplaylists() {
  //get all the players
  var players = document.getElementsByClassName("player");
  var audios = [];

  //for each player, set up its environment
  for(var p=0;p<players.length;p++) {
    //the player environment
    let aud = {
      //everything is relative to the player wrapper
      player: players[p],
      audio: null,
      playlist: null,
      now: 0,
      shuffle: true,
      loop: true,

      init : () => {
        //get the audio features (player, playlist, and directory
        aud.audio = aud.player.querySelectorAll(".playerAudio")[0];
        aud.playlist = aud.player.querySelectorAll(".playerList .song");
        auddir = aud.player.querySelectorAll(".auddir")[0].innerHTML.replace("&amp;","&")+"/";
        //assign event handlers to the playlist elements
        for(let i=0;i<aud.playlist.length;i++) {
          aud.playlist[i].onclick = () => { aud.play(i); };
          aud.playlist[i].onkeypress = (e) => { if(e.keyCode == 13) {aud.play(i);} };
        }
        //autoplay
        aud.audio.oncanplay = aud.audio.play;

        //at the end of each song, check if shuffle and loop are enabled
        aud.audio.onended = () => {
          aud.shuffle = aud.player.querySelectorAll(".shuff")[0].checked;
          aud.loop = aud.player.querySelectorAll(".loop")[0].checked;

          if(aud.shuffle) {
            aud.now = Math.floor(Math.random() * aud.playlist.length);
          } else {
            aud.now++;
          }
          
          if(aud.loop) {
            if(aud.now>=aud.playlist.length) {
              aud.now = 0;
            }
          }
          //play the song
          aud.play(aud.now);
        };
      },

      //play the song
      play : id => {
        //get the one now playing
        aud.now = id;

        aud.player.getElementsByClassName("nowplaying")[0].innerHTML = aud.playlist[id].innerHTML;
        //set the audio source as the one now playing
        aud.audio.src = auddir + aud.playlist[id].dataset.src;
        //assign the playlist element now-playing classes
        for(let i=0; i<aud.playlist.length;i++) {
          if(i==id) { aud.playlist[i].classList.add("now"); }
          else { aud.playlist[i].classList.remove("now"); }
        }
      }
    };
    //add the new player to the list of them
    audios.push(aud);
  }
  //init the players
  for(var i=0;i<audios.length;i++) { audios[i].init(); }
}

