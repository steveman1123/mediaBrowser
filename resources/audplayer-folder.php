<?php
//location of the audio files to play

// get songs from the specified dir
$songs = glob($curDir."/*.{mp3,webm,ogg,wav,opus,m4a}", GLOB_BRACE); /**/
//TODO: adjust getting songs as a 2 part deal:
//get all files
//$files = glob($curDir."/*");
//isolate only the song files
//$songs = preg_grep('{some_regex}',$files);


if(is_file($curDir."/folder.jpg")) {
  $folderpic = $curDir."/folder.jpg";
} else {
  $folderpic = "./resources/placeholder.jpg";
}
?>
<div class="player">
  <span class="auddir" style="display: none;"><?php
  //this is a hack to pass php var to js indirectly. There's probably a better way, but this works
  echo $curDir; ?></span>
  <img class="folderpic" src="<?php echo $folderpic;?>">
  <h4 class="nowplaying">-</h4>
  <audio class="playerAudio" controls></audio>
  <div class="plcontrol">
    <p>
      <button class="goback">prev</button>
      <button class="playpause">play/pause</button>
      <button class="goforward">next</button>
    </p>
    <label><input type="checkbox" name="shuffle" class="shuff">shuffle</label>
    <span>  |  </span>
    <label><input type="checkbox" name="loop" checked class="loop">loop</label>
  </div>
  <div class="playerList">
    <?php
    
    if(is_array($songs)) {
      foreach ($songs as $k=>$s) {
        $name = basename($s);
        //read class3v2 tags
        $cmd = 'id3v2 --list "'.$s.'"';
        //var_dump($cmd);
        $o = shell_exec($cmd." 2>&1");
        //split by newline, trim off first few lines
        $o = array_slice(preg_split("/\r\n|\n|\r/", $o),1);
        //var_dump($o);
        //parse the output further
        $tags = array(); //fianl tags output
        foreach ($o as $i=>$t) {
          $tag = explode(": ",$t,2);
          if(sizeof($tag)==2) {
            $tag[0] = substr($tag[0],0,4);
            $tags[$tag[0]] = $tag[1];
          }
        }
        //TODO: add additional class3 tags like artist, track, album
        //var_dump(array_key_exists("TIT2",$tags));
        //var_dump(strlen($tags['TIT2']));
        //var_dump($tags);

        //TODO: add a button on the right side that can add the song to a playlist
        if(array_key_exists("TIT2",$tags) && strlen($tags['TIT2'])>0) {
          echo "<div data-src='".rawurlencode($name)."' class='song' tabindex='0'><span class='title'>".$tags['TRCK']." - ".$tags['TIT2']."</span></div>";
        } else {
          echo"<div data-src='".rawurlencode($name)."' class='song' tabindex='0'><span class='title'>".$name."</span></div>";
        }
      }
    } else { 
      echo "No songs found!";
    }
  ?>
  </div>
</div>
