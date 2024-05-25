<?php
//location of the audio files to play

// get songs from the specified dir
$curDirEscaped = str_replace(array("[","]"),array("\[","\]"),$curDir);
$songs = glob($curDirEscaped."/*.{mp3,webm,ogg,wav,opus,m4a}", GLOB_BRACE); /**/

//TODO: adjust getting songs as a 2 part deal:
//get all files
//$files = glob($curDir."/*");
//isolate only the song files
//$songs = preg_grep('{some_regex}',$files);


//$curDir = str_replace("#","%23",$curDir);
//var_dump($curDir); /* */

if(is_file($curDir."/folder.jpg")) {
  //TODO: check for other illegal/unhandled charachters?
  $folderpic = str_replace("#","%23",$curDir)."/folder.jpg";
} else {
  $folderpic = "./resources/placeholder.svg";
}
?>
<div class="player">
  <span class="auddir" style="display: none;"><?php
  //this is a hack to pass php var to js indirectly. There's probably a better way, but this works
  echo urlencode($curDir); ?></span>
  <img class="folderpic" src="<?php echo $folderpic;?>">
  <h4 class="nowplaying">-</h4>
  <audio class="playerAudio" controls></audio>
  <div class="plcontrol">
    <p>
      <span title="shuffle"><label><input type="checkbox" name="shuffle" class="shuff"><span>&#10536;</span></label></span>
      <button class="goback">&#x23EE;</button>
      <button class="playpause">&#x23EF;</button>
      <button class="goforward">&#x23ED;</button>
      <span title="loop"><label><input type="checkbox" name="loop" checked class="loop"><span>&#8635;</span></label></span>
    </p>
  </div>
  <div class="playerList">
    <?php
    
    if(is_array($songs)) {
      foreach ($songs as $k=>$s) {
        $name = basename($s);
        //read id3v2 tags
        //TODO: get all the metadatas first, then parse them! That should make it faster
        //possibly this cmd (until we can figure out a faster way with caching or something)
        //echo "[" && find . -iname "*.mp3" -exec ffprobe -print_format json -show_format -v quiet {} \; -exec echo "," \; && echo "]"
        //which should return a json text
        
        //id3 tag processing using ffprobe
        $cmd = 'ffprobe -v quiet -print_format json -show_format "'.$s.'"';
        $o = shell_exec($cmd." 2>&1");
        $o = json_decode($o,TRUE);
        //var_dump($o);
        $tags = Array();
        if(array_key_exists("tags",$o['format'])) {
          $tags = $o['format']['tags'];
        }

        /*
        //old id3 tag processing using id3v2
        $cmd = 'id3v2 --list "'.$s.'"';
        //split by newline, trim off first few lines
        $o = array_slice(preg_split("/\r\n|\n|\r/", $o),1);

        //parse the output further
        $tags = array(); //fianl tags output
        foreach ($o as $i=>$t) {
          $tag = explode(": ",$t,2);
          if(sizeof($tag)==2) {
            $tag[0] = substr($tag[0],0,4);
            $tags[$tag[0]] = $tag[1];
          }
        }
        */
        
        //TODO: add a button on the right side that can add the song to a playlist

        echo "<div data-src='".rawurlencode($name)."' class='song' tabindex='0'>";
        //default value of the filename
        $songhtml = "<span class='title'>".$name."</span>";

        //add the track and title
        if(array_key_exists("title",$tags) && strlen($tags['title'])>0) {
          $songhtml = "<span class='title'>".$tags['track']." - ".$tags['title']."</span>";
        }

        //add the genre
        $songhtml .= "<span class='info'>";
        if(array_key_exists("genre",$tags) && strlen($tags['genre'])>0) {
          $songhtml .= $tags['genre']."&nbsp;|&nbsp;";
        }
        
        //add the release dat
        if(array_key_exists("date",$tags) && strlen($tags['date'])>0) {
          $songhtml .= $tags['date']."&nbsp;|&nbsp;";
        }
        
        //add the duration
        $mm = intval(floatval($o['format']['duration'])/60);
        $ss = intval(floatval($o['format']['duration'])-60*$mm);
        $songhtml .= $mm.":".str_pad($ss,2,"0",STR_PAD_LEFT);
        
        $songhtml .= "</span>";

        echo $songhtml;
        
        echo "</div>";


      }
    } else {
      echo "No songs found!";
    }
  ?>
  </div>
</div>
