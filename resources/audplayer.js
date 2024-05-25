//TODO: handle more special chars like $
//run init fxn on load
document.onload = initplaylists();
document.ogtitle = document.title;

//init the players
function initplaylists() {
  //get all the players
  var players = document.getElementsByClassName("player");
  var audios = [];

  //console.log(players);
  //for each player, set up its environment
  for(var p=0;p<players.length;p++) {
    //the player environment
    let aud = {
      //everything is relative to the player wrapper
      player: players[p], //this player
      goback: null, //button to go back a song
      playpause: null, //button to play/pause
      goforward: null, //button go to next song
      audio: null, //html audio element
      isPlaying: false, //is the audio currently playing?
      origpl: null, //div containing list of songs - original playlist
      playingpl: null, //list same length as origpl, toggled to shuffle or not
      id: 0,
      now: 0, //currently playing song
      shuffletog: null, //button toggling shuffling
      loop: true, //toggle playlist looping

      init : () => {
        //get the audio features (player, controls, playlist, and directory)
        aud.audio = aud.player.querySelectorAll(".playerAudio")[0];

        aud.goback = aud.player.querySelectorAll(".goback")[0];
        aud.playpause = aud.player.querySelectorAll(".playpause")[0];
        aud.goforward = aud.player.querySelectorAll(".goforward")[0];
        aud.shuffletog = aud.player.querySelectorAll(".shuff")[0];
        aud.loop = aud.player.querySelectorAll(".loop")[0];
        aud.playlistdiv = aud.player.querySelectorAll(".playerList")[0];
        aud.origpl = aud.player.querySelectorAll(".playerList .song");
        aud.playingpl = [...Array(aud.origpl.length).keys()];

        
        auddir = decodeURI(document.getElementById("auddir").innerText+"/");
        auddir = auddir.replace("#","%23"); //TODO: check for other potentially illegal/unhandled characters?
        //alert(auddir);
        //assign event handlers to the playlist elements
        for(let i=0;i<aud.origpl.length;i++) {
          aud.origpl[i].onclick = () => { aud.click2play(i); };
          aud.origpl[i].onkeypress = (e) => { if(e.keyCode == 13) {aud.click2play(i);} };
        }
        
        aud.audio.oncanplay = aud.audio.play;
        aud.audio.onended = aud.playnext;
        aud.goback.onclick = aud.playprev;
        aud.playpause.onclick = aud.playerpause;
        aud.goforward.onclick = aud.playnext;
        aud.shuffletog.onchange = aud.shuffle;
        aud.shuffle();
        //aud.play(); //autoplay

        navigator.mediaSession.setActionHandler("previoustrack", aud.playprev);
        navigator.mediaSession.setActionHandler("nexttrack", aud.playnext);
        
      },


      playprev : () => {
        aud.id = Math.max(0,aud.id-1);
        if(aud.id<0) {
          aud.id = aud.playingpl.length-1;
        }
        aud.play(aud.playingpl[aud.id]);
      },

      playnext : () => {
        aud.id = Math.min(aud.playingpl.length,aud.id+1);
        if(aud.id>=aud.playingpl.length) {
          if(aud.loop.checked) {
            //done with pl and looping
            //reset and play
            //console.log("done with pl and looping")
            aud.id = 0;
            aud.play(aud.playingpl[aud.id]);
          } else {
            //done with pl and no looping
            //don't play
            //console.log("done with pl and not playing")
          }
        } else {
          //console.log("not done with pl, playing")
          //not done with playlist
          //play
          aud.play(aud.playingpl[aud.id]);
        }
      },

      playerpause : () => {
        if(aud.isPlaying) {
          aud.audio.pause();
          aud.isPlaying = false;
        } else {
          if(aud.audio.src.length==0) {
            aud.play();
          }
          aud.audio.play();
          aud.isPlaying = true;
        }
        //console.log(aud.isPlaying);
      },

      
      shuffle : () => {
        if(aud.shuffletog.checked) {
          for (let i = aud.playingpl.length - 1; i > 0; i--) {
            let j = Math.floor(Math.random() * (i + 1));
            [aud.playingpl[i], aud.playingpl[j]] = [aud.playingpl[j], aud.playingpl[i]];
          }
          aud.id = 0;
        } else {
          aud.playingpl = [...Array(aud.origpl.length).keys()];
          aud.id = aud.now;
        }
        //console.log(aud.playingpl);
        //console.log(aud.now);
        //console.log(aud.id);
      },

      //handle when clicking on a song, it should populate the aud.id and play
      click2play : id => {
        aud.play(id);
        aud.shuffle();
      },

      
      //play the song
      play : id => {
        //id = index of origpl to play
        //aud.id = index of playingpl
        //aud.playingpl = shuffled or non-shuffled playlist
        //aud.origpl = non shuffled playlist

        //console.log("id: "+id);
        //console.log("aud.id: "+aud.id);

        //get the one now playing
        aud.now = id;
        //get and display the now playing in the playlist and the title
        title = decodeURI(aud.origpl[id].firstElementChild.innerText);
        aud.player.getElementsByClassName("nowplaying")[0].innerText = title;
        document.title = title+" | "+document.ogtitle;
        //set the audio source as the one now playing
        aud.audio.src = auddir+aud.origpl[id].dataset.src;
        //assign the playlist element now-playing classes
        for(let i=0; i<aud.origpl.length;i++) {
          if(i==id) {
            aud.origpl[i].classList.add("now");
            //scroll pl div to show now playing in middle
            aud.playlistdiv.scrollTop = aud.origpl[i].offsetTop - aud.playlistdiv.offsetTop - aud.playlistdiv.offsetHeight / 2 + aud.origpl[i].offsetHeight;
          } else {
            aud.origpl[i].classList.remove("now");
          }
        }
        aud.isPlaying = true;
      }
    };

    audios.push(aud);
  }
  //init the players
  for(var i=0;i<audios.length;i++) { audios[i].init(); }
}


function tog(e) { e.style.display = (e.style.display=='block' ? 'none' : 'block'); }


function loadpl(e) {
  //goal of this function is to add a playlist div and populate it with the files in the playlist file

  const plfile = e.innerHTML;
  var pldiv = document.getElementById(plfile.split(".")[0]);
  
  //check if the div exists already
  if(pldiv) {
    //pl already present, toggle displaying it
    tog(pldiv);

  } else {

    //create the div
    pldiv = document.createElement("div");
    pldiv.classList.add("player");
    pldiv.style.display = "block";
    pldiv.id = plfile.split(".")[0];
    dirplaywrap = document.getElementById("dirplaywrap");
    dirplaywrap.appendChild(pldiv);
    pldiv.innerHTML = "Loading...";

    //get the playlist innerhtml from the api
    var url = "./resources/audplayer-pl.php";
    var curdir = document.getElementById("auddir").innerText;
    fetch(url, {
      method: "POST",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({'plfile':plfile,'curdir':curdir})
    })
    .then((response) => response.text())
    .then((text) => {
      pldiv.innerHTML = text;
      initplaylists();
    });
  }
}
