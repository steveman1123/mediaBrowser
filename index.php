<?php
$exclude = ["./resources","./index.php"]; //exclude these files/directories from being displayed - path must be relative to this file

//if the directory var is set,
if(isset($_GET['d'])) {
  //encode it to url type, sanitize, redecode from url type
  $curDir = urldecode(filter_var(urlencode($_GET['d']),FILTER_SANITIZE_URL));
} else {
  $curDir = "."; //else, set it to the top level directory
}
$curDir = str_replace(["../",".."],"",$curDir)|"."; //remove higher directory listing - for security

$oneUp = implode("/",array_splice(explode("/",$curDir),0,-1)); //convert path var to array, remove last element, convert back to path
?>

<!DOCTYPE html>
<html>
  <head>
    <title><?php echo end(explode('/',$curDir)); ?> | Steve's Media Library</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./resources/medio.css" rel="stylesheet" type="text/css">
    <link href="./resources/audplayer.css" rel="stylesheet" type="text/css">
    <script src="./resources/audplayer.js"></script>
  </head>
  <body>
    <p><?php echo $curDir;?>
    <div id="dirplaywrap">
      <div id="dirlist">
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
$scanDir = rtrim($curDir,'/').'/*';
//get the directories
$dirs = glob($scanDir,GLOB_ONLYDIR);
//remove the dirs and exclude files, then trim the "/"
$files = str_replace($curDir.'/','',array_diff(array_diff(glob($scanDir),$dirs),$exclude));
//remove the specified exclude dirs, then trim the "/"
$dirs = str_replace($curDir.'/','',array_diff($dirs,$exclude));

foreach($dirs as $d) {
  $link = $curDir."/".$d; //make the link to the file/dir
  echo '<p>(dir) <a href="?d='.urlencode($link).'">'.$d.'</a></p>';
}
//specify valid audio extentions
$audext = array("mp3","opus","ogg"); //TODO: possibly also check webm, m4a, wav, wma, but only if they can be played in most browsers
foreach($files as $f) {
  foreach ($audext as $e) {
    if (substr($f, -1*strlen($e))===$e)
      $hasaud=TRUE;
  }
  $link = $curDir."/".$f; //make the link to the file/dir
  echo '<p><a href="'.$link.'">'.$f.'</a></p>';
}
?>
    
    
    <div id="metadata">
      <p><?php echo sizeof($files);?> files</p>
      <p><?php echo sizeof($dirs);?> directories</p>
    </div>

<?php
    echo "</div>";

    if($hasaud) {
      include "./resources/audplayer.php";
    } else {
      echo "<div></div>";
    }
?>
    </div>
  </body>
</html>
