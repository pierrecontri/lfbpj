<?php

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
  $webPage->docTitle  = "Please, choose your site";
  $webPage->metaWords = "";

  $siteList = getChildDirectories('./data');

  $strContent = "<div class=\"categoryList\">";

  foreach($siteList as $sitename) {
    $sitenametitle = ucfirst($sitename);
    $strContent .= <<<ENDTxt2
      <!-- {$sitename} -->
        <div class="divCategory2" style="float: left; width: 33%;">

          <!-- <a href="javascript:;" onclick="javascript:goSite('{$sitename}');"> -->
          <!-- <a href="javascript:;" onclick="javascript:goPage('','{$sitename}');"> -->
          <a href="javascript:goCategory('{$sitename}');">
          <!-- <a href="./?site={$sitename}" onclick="return !window.open(this.href);"> -->
            {$sitenametitle}<br />
            <img class="content" src="./data/{$sitename}/{$sitename}.jpg" alt="{$sitename}" />
          </a>
        </div>

ENDTxt2;
  }

  $webPage->contentPage = $strContent . "</div> <!-- categoryList -->";

  return $webPage;
}
?>
