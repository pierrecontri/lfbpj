<?php
// Pierre Contri
// cree le 31/10/2015
// mdf  le 22/11/2015
// Global Navigation for all sites

class GlobalNavigation {

  public static function getHtmlSiteEntry($sitename) {
    return "<entry name=\"{$sitename}\"><titlePage>" . ucfirst($sitename) . "</titlePage></entry>";
  }

  public static function getGlobalMenu() {
    $arrSitesList = array_map( array('GlobalNavigation','getHtmlSiteEntry') , $_SESSION['sitesList']);
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
}
?>
