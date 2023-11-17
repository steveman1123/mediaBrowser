<?php

//where the playlists are stored
$pldir = "../";

$data = json_decode(file_get_contents("php://input"),true);

if(array_key_exists("plfile",$data)) {
  if(file_exists($pldir.$data['plfile'])) {
    $songs = file($pldir.$data['plfile']);
    ?>

  <!--<span class="auddir" style="display: none;"><?php
  //this is a hack to pass php var to js indirectly. There's probably a better way, but this works
  echo $pldir; ?></span>-->
  <h2><?php echo basename($pldir.$data['plfile']); ?></h2>
  <h3><?php echo sizeof($songs); ?> Songs</h3>
  <h4 class="nowplaying">-</h4>
  <audio class="playerAudio" controls></audio>
  <div class="plcontrol">
    <p>
      <button class="goback">prev</button>
      <button class="playpause">play/pause</button>
      <button class="goforward">next</button>
    </p>
    <label><input type="checkbox" name="shuffle" checked class="shuff">shuffle</label>
    <span>  |  </span>
    <label><input type="checkbox" name="loop" checked class="loop">loop</label>
  </div>
  <div class="playerList">

    <?php
    if(is_array($songs)) {
      foreach ($songs as $k=>$s) {
        if(strlen($s)>1) {
          $name = basename($s);
          //read id3v2 tags
          $cmd = 'id3v2 --list "'.trim($pldir.$s).'"';
          //var_dump($cmd);
          //$o = shell_exec($cmd);
          $o = shell_exec($cmd." 2>&1");
          
          //split by newline, trim off first few lines
          $o = array_slice(preg_split("/\r\n|\n|\r/", $o),1);
          //var_dump($o);

          //parse the output further
          $tags = array(); //fianl tags output
          foreach ($o as $i=>$t) {
            $tag = explode(": ", $t,2);
            if(sizeof($tag)==2) {
              $tag[0] = substr($tag[0],0,4);
              $tags[$tag[0]] = $tag[1];
            }
          }
          //ensure that the array key "TPE2" is populated (sometimes only TPE1 is)
          //var_dump($tags);
          if(!array_key_exists("TPE2",$tags)) {
            if(array_key_exists("TPE1",$tags)) {
              $tags['TPE2'] = $tags['TPE1'];
            }
          }
          
          
          //TODO: figure out hoe to display unicode chars
          //var_dump($tags);
          //TODO: add additional id3 tags like artist
          //var_dump(array_key_exists("TIT2",$tags));
          //var_dump(strlen($tags['TIT2']));
          //TODO: add a button to remove a song from the playlist
          $songurl = str_replace("%2F","/",rawurlencode(trim($s)));
          if(array_key_exists("TIT2",$tags) && strlen($tags['TIT2'])>0) {
            //TODO: display more info and break each type apart to clean up output
            echo "<div data-src='".$songurl."' class='song' tabindex='0'><span class='title'>".$tags['TPE2']." - ".$tags['TIT2']."</span></div>";
          } else {
            echo"<div data-src='".$songurl."' class='song' tabindex='0'><span class='title'>".$name."</span></div>";
          }
        }
      }
    } else { 
      echo "No songs found!";
    }
  ?>
  </div>
</div>
    <?php
  }
}
?>
