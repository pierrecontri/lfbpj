<?php

function printJavaScript($dirName, $urlFile) {
  $strInclude = "";

  $fdir = opendir($dirName);
  if($fdir) {
    while($ficName = readdir($fdir)) {
      $fInfo = pathinfo($ficName);
      if($fInfo['extension'] == "js")
        $strInclude .= <<<EndIncludeScript
    <script type="text/javascript" src="{$urlFile}/{$ficName}"></script>

EndIncludeScript;
    }
    closedir($fdir);
  }
  return $strInclude;
}
?>