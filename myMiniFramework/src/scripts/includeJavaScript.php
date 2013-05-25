<?php

function printJavaScript($dirName) {
  $strInclude = "";
  $fdir = opendir($dirName);
  if($fdir) {
    while($ficName = readdir($fdir)) {
      $fInfo = pathinfo($ficName);
      if($fInfo['extension'] == "js")
        $strInclude .= <<<EndIncludeScript
    <script type="text/javascript" src="{$dirName}/{$ficName}">
      alert('Erreur chargement fichier {$ficName}');
    </script>

EndIncludeScript;
    }
    closedir($fdir);
  }
  return $strInclude;
}

?>
