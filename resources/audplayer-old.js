//TODO: adjust for multiple playlists since it's now using classes instead of id's
var aud = {
  // init playe
  player : null,   // html <audio> element
  playlist : null, // html playlist
  now : 0,         // current song
  

  init : () => {
    // get html elements for the player, song list, and audio directory
    aud.player = document.getElementsByClassName("playerAudio");
    alert(aud.player[0]);
    aud.playlist = document.querySelectorAll(".playerList .song");
    //make sure that there is a trailing "/" and that ampersands are encoded properly
    auddir = document.getElementById("auddir").innerHTML.replace("&amp;","&")+"/";

    // apply event handlers (click/enter to play)
    for (let i=0; i<aud.playlist.length; i++) {
      aud.playlist[i].onclick = () => { aud.play(i); };
      aud.playlist[i].onkeypress = (e) => { if(e.keyCode == 13) {aud.play(i);} };
    }

    // auto-play when loaded
    aud.player.oncanplay = aud.player.play;

    // autoplay when current song ends (increment/loop, then play)
    aud.player.onended = () => {
      alert(document.getElementById("shuff"));
      aud.now++;
      if (aud.now>=aud.playlist.length) { aud.now = 0; }
      aud.play(aud.now);
    };
  },


  //play selected song
  play : id => {
    // update source
    aud.now = id;
    aud.player.src = auddir + aud.playlist[id].dataset.src;
    //document.getElementById("title").innerHTML = aud.playlist[id].getElementsByClassName("title")[0].innerHTML;
    //identify which song is currently playing (for css)
    for (let i=0; i<aud.playlist.length; i++) {
      if (i==id) { aud.playlist[i].classList.add("now"); }
      else { aud.playlist[i].classList.remove("now"); }
    }
  }
};
window.addEventListener("DOMContentLoaded", aud.init);
