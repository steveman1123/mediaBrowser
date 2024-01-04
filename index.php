<?php
$exclude = ["./resources","./index.php","./LICENSE","./README.md"]; //exclude these files/directories from being displayed - path must be relative to this file
$globalexclude = ["lost+found"]; //exclude any instance of these

//if the directory var is set,
if(isset($_GET['d'])) {
  //encode it to url type, sanitize, redecode from url type
  $curDir = urldecode(filter_var(urlencode($_GET['d']),FILTER_SANITIZE_URL));
} else {
  $curDir = "."; //else, set it to the top level directory
}

//remove higher directory listing, so it's sandboxed to this directory and below
if($curDir == "..") $curDir=".";
$curDir = str_replace(["../"],"",$curDir)|".";

$oneUp = implode("/",array_splice(explode("/",$curDir),0,-1)); //convert path var to array, remove last element, convert back to path
?>

<!DOCTYPE html>
<html>
  <head>
    <title><?php echo end(explode('/',$curDir)); ?> | Media Library</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link href="./resources/medio.css" rel="stylesheet" type="text/css">
    <link href="./resources/audplayer.css" rel="stylesheet" type="text/css">
  </head>
  <body>
    <div id="dirplaywrap">
      <div id="dirlist">
        <p id="auddir"><?php echo $curDir;?></p>
        <p><a href="/">Back to Home</a></p>
<?php if(strcmp($curDir,".")) { ?>
        <p><a href=".">Go to Top</a></p>
        <p><a href="?d=<?php echo urlencode($oneUp); ?>">Go Up One Folder</a></p>
<?php } else { ?><p>&nbsp;</p><p>&nbsp;</p><?php }?>
        <div class="break"></div>
<?php
//$files = preg_grep('/^([^.])/', scandir($curDir)); //remove hidden files/dirs
$hasaud = FALSE;
//isolate the directory to scan
$scanDir = rtrim($curDir,'/').'/*'; /**/
//escape square brackets (TODO: probably should escape all regex?) so glob can understand it
$scanDirEscaped = str_replace(array('[',']'),array('\[','\]'),$scanDir);
//get the directories
$dirs = glob($scanDirEscaped,GLOB_ONLYDIR);

//remove the dirs and excluded files, then trim the "/"
$files = str_replace($curDir.'/','',array_diff(array_diff(glob($scanDirEscaped),$dirs),$exclude));
$files = array_diff($files,$globalexclude);
//remove the specified exclude dirs, then trim the "/"
$dirs = str_replace($curDir.'/','',array_diff($dirs,$exclude));
//remove the global excludes from the disr
$dirs = array_diff($dirs,$globalexclude);


foreach($dirs as $d) {
  $link = $curDir."/".$d; //make the link to the file/dir
  echo '<p>(dir) <a href="?d='.urlencode($link).'">'.$d.'</a></p>';
}
//specify valid audio extentions
//TODO: possibly also check webm, wav, wma, but only if they can be played in most browsers
$audext = array("mp3","opus","ogg","m4a");

$hasaud = FALSE;
foreach($files as $f) {
  $ext = strtolower(pathinfo($f,PATHINFO_EXTENSION));

  $hasaud = in_array($ext,$audext) || $hasaud;

  //don't make the playlists links, instead make them load/toggle the playlist
  if($ext !== "m3u") {
    $link = $curDir."/".$f; //make the link to the file/dir
    echo '<p><a href="'.$link.'">'.$f.'</a></p>';
  } else {
    echo '<p><a href="javascript:;" onclick="loadpl(this);">'.$f.'</a></p>';
  }
}
?>
        <div id="metadata">
          <p><?php echo sizeof($files);?> files</p>
          <p><?php echo sizeof($dirs);?> directories</p>
        </div>
      </div>
<?php
    if($hasaud) {
      include "./resources/audplayer-folder.php";
    } else {
      echo "<div></div>";
    }
?>
    </div>
    <script src="./resources/audplayer.js" defer></script>
  </body>
</html>
