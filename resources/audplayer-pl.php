<?php


$data = json_decode(file_get_contents("php://input"),true);
$curdir = $data['curdir'];

//where the playlists are stored
$pldir = "../".$curdir."/";



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
      <span title="shuffle"><label><input type="checkbox" name="shuffle" checked class="shuff"><span>&#10536;</span></label></span>
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
        $s = trim($s);
        if(strlen($s)>1) {
          $name = basename($s);
          //read id3v2 tags
          //$cmd = 'id3v2 --list "'.trim($pldir.$s).'"';
          $cmd = 'ffprobe -v quiet -print_format json -show_format "'.$pldir.$s.'"';
          $o = shell_exec($cmd." 2>&1");
          $o = json_decode($o,TRUE);


          $tags = Array();
          if(array_key_exists("tags",$o['format'])) {
            $tags = $o['format']['tags'];
          }

          //var_dump($tags);


          //TODO: add a button to remove a song from the playlist & reorder
          $songurl = str_replace("%2F","/",rawurlencode(trim($s)));
          echo "<div data-src='".$songurl."' class='song' tabindex='0'>";
          //default text
          $songhtml = "<span class='title'>".$name."</span>";
          if(array_key_exists("title",$tags) && array_key_exists("artist",$tags)) {
            $songhtml = "<span class='title'>".$tags['artist']." - ".$tags['title']."</span>";
          }
          $songhtml .= "<span class='info'>";

          $mm = intval(floatval($o['format']['duration'])/60);
          $ss = intval(floatval($o['format']['duration'])-60*$mm);
          $songhtml .= $mm.":".str_pad($ss,2,"0",STR_PAD_LEFT);

          $songhtml .= "</span>";

          echo $songhtml;
          echo "</div>";

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
