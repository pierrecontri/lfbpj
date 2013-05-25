<?php

function getHtmlSiteEntry($sitename) {
  return "<entry name=\"{$sitename}\"><titlePage>" . ucfirst($sitename) . "</titlePage></entry>";
}

function getGlobalMenu() {

$arrSitesList = array_map('getHtmlSiteEntry', $_SESSION['sitesList']);
$strSitesList = implode($arrSitesList, "\n");

  return <<<EndMenu
<menu name="globalMenu">
  <entry name="sitemap">
    <titlePage>Site Map</titlePage>
  </entry>
  <entry name="sitechoosing">
    <titlePage>Other WebSites</titlePage>
    <menu>
{$strSitesList}
    </menu>
  </entry>
</menu>
EndMenu;
}
?>
