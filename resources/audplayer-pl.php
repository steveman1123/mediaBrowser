<?php
// get playlist files
$plfiles = glob($curDir."/*.{m3u}", GLOB_BRACE);
//var_dump($plfiles);

foreach ($plfiles as $k=>$plfile) {
  //TODO: read unicode files
  //https://stackoverflow.com/questions/15092764/how-to-read-unicode-text-file-in-php#15100848
  $songs = file($plfile);
  //var_dump($songs);
?>

<div class="player">
  <span class="auddir" style="display: none;"><?php
  //this is a hack to pass php var to js indirectly. There's probably a better way, but this works
  echo $curDir; ?></span>
  <h1><?php echo basename($plfile); ?></h1>
  <h3><?php echo sizeof($songs); ?> Songs</h3>
  <h4 class="nowplaying"></h4>
  <audio class="playerAudio" controls></audio>
  <div class="plcontrol">
    <input type="checkbox" name="shuffle" checked="checked" class="shuff">shuffle
    <span>  |  </span>
    <input type="checkbox" name="loop" checked="checked" class="loop">loop
    </div>
  <div class="playerList">
    <?php
    
    
    if(is_array($songs)) {
      foreach ($songs as $k=>$s) {
        $name = basename($s);
        //read id3v2 tags
        $cmd = 'id3v2 --list "'.trim($s).'"';
        //var_dump($cmd);
        //$o = shell_exec($cmd);
        $o = shell_exec($cmd." 2>&1");
        
        //var_dump($o);
      
        //split by newline, trim off first few lines
        $o = array_slice(preg_split("/\r\n|\n|\r/", $o),1);
        //var_dump($o);

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
    } else { 
      echo "No songs found!";
    }
    
  ?>
  </div>
</div>
<?php
}

?>
