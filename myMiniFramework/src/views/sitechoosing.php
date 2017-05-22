<?php

$_SESSION["dirPhotos"]     = "";
$_SESSION["dirDocuments"]  = "";
$_SESSION["dirTools"]      = "";
$_SESSION["ficPhotos"]     = "";

if(!isset($_SESSION["xmlMenu"]))
$_SESSION["xmlMenu"] = <<<EndMenu
<menu>
  <entry name="siteslist" default="true">
    <titlePage>Site Choosing</titlePage>
  </entry>
</menu>
EndMenu;

function siteslist() {
  $webPage = new WebPage();
  $webPage->docTitle  = "Welcome on Pierre Contri Web Site";
  $webPage->metaWords = "pierre contri, musique instrumentale, startmenuwin8, developpement, blue bassin, programming Python Php DotNet";
  $webPage->headerDescription = <<<ENDHeader
  <div id="homeDiv">
    <h2>Hi and welcome on my internet web site.</h2>
    <p>This site is dedicated to three very different interests :</p>
    <ul>
      <li>The first relates to the music. You can hear my composition, as well as covers made with small groups. (instrumental, bluebasin, covering)</li>
      <li> The second concerns my travels. You'll find photographs of some parts of France especially magnificent. (pictures)</li>
      <li>The third is finally all that is attached to the computer. I present a breakout game, and several documents on my work computer, and those of others. (programming, pong2, myMiniFramework, cv)</li>
    </ul>
  </div>\n
ENDHeader;

  $absolutepath = realpath(".");
  $siteList = DataManagement::getChildDirectories("{$absolutepath}/data");

  $strContent_auto = "";
  foreach($siteList as $sitename) {
    $sitenametitle = ucfirst($sitename);

    $strContent_auto .= <<<ENDTxt
<div class="panel panel-default col-md-3 nav" onclick="javascript:goCategory('{$sitename}');">
  <div class="panel-heading">
    <h3 class="panel-title">{$sitename}</h3>
  </div>
  <div class="panel-body">
    <img class="img-rounded" src="./data/{$sitename}/{$sitename}.jpg" alt="{$sitename}" />
  </div>
</div>

ENDTxt;
  }

  $webPage->contentPage = <<<ENDTxt2
  
<div class="categoryList row">
  <!-- <ul> -->
{$strContent_auto}
  <!-- </ul> -->

</div> <!-- categoryList -->

ENDTxt2;

  return $webPage;
}

?>
