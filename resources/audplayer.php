<?php
//location of the audio files to play

// get songs from the specified dir
$songs = glob($curDir."/*.{mp3,webm,ogg,wav,opus}", GLOB_BRACE);
if(is_file($curDir."/folder.jpg")) {
  $folderpic = $curDir."/folder.jpg";
} else {
  $folderpic = "./resources/placeholder.jpg";
}
?>
<div id="player">
  <span id="auddir" style="display: none;"><?php
  //this is a hack to pass php var to js indirectly. There's probably a better way, but this works
  echo $curDir; ?></span>
  <img id="folderpic" src="<?php echo $folderpic;?>">
  <audio id="playerAudio" controls></audio>
  <div id="playerList">
    <?php
    if(is_array($songs)) {
      foreach ($songs as $k=>$s) {
        $name = basename($s);
        //read id3v2 tags
        $o = shell_exec('id3v2 --list "'.$s.'"');
        //var_dump($o);
        //split by newline, trim off first few lines
        $o = array_slice(preg_split("/\r\n|\n|\r/", $o),2);
        //parse the output further
        $tags = array(); //fianl tags output
        foreach ($o as $i=>$t) {
          $tag = preg_split("/: /", $t);
          //TODO: if no tag is found, put the filename instead
          if(sizeof($tag)==2) {
            $tag[0] = substr($tag[0],0,4);
            $tags[$tag[0]] = $tag[1];
          }
        }
        //var_dump($tags);
        //TODO: add additional id3 tags like artist, track, album
        //var_dump(array_key_exists("TIT2",$tags));
        //var_dump(strlen($tags['TIT2']));

        if(array_key_exists("TIT2",$tags) && strlen($tags['TIT2'])>0) {
          echo "<div data-src='".rawurlencode($name)."' class='song' tabindex='0'><span class='trck'>".$tags['TRCK']."</span> - <span class='title'>".$tags['TIT2']."</span></div>";
        } else {
          echo"<div data-src='".rawurlencode($name)."' class='song' tabindex='0'><span class='title'>".basename($s)."</span></div>";
        }
      }
    } else { 
      echo "No songs found!";
    }
  ?>
  </div>
</div>
