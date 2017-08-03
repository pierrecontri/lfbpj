<?php

// ----------------------------

/*
<menu name="">
  <entry name="" visible="true" default="true">
    <titlePage></titlePage>
    <menu name=""></menu>
  </entry>

  <entry name="" visible="true">
    <titlePage></titlePage>
    <menu name="">
      <entry name="" visible="true">
        <titlePage></titlePage>
        <menu></menu>
      </entry>
    </menu>
  </entry>

</menu>
*/

class EntryMenu {

  public  $name         = "";
  public  $titlePage    = "";
  public  $subMenu      = null;
  public  $visible      = true;
  private $defaultEntry = false;
  public  $useAjax      = false;

  public function __construct($name, $pageTitle, $visible = true, $defaultEntry = false, $useAjax = false) {
    $this->name         = $name;
    $this->titlePage    = $pageTitle;
    $this->visible      = $visible;
    $this->useAjax      = $useAjax;
    $this->defaultEntry = $defaultEntry;
  }

  public function __toString() {
    return "Name: {$this->name}, TitlePage: {$this->titlePage}, " .
           "Visible: {$this->visible}, Default: {$this->defaultEntry}, " .
           "useAjax: {$this->useAjax}";
  }

  public function __get($attr) {
    if($attr == "isDefaultEntry")
      return $this->defaultEntry;
  }

  public function isEntryContaintCategory($searchName) {
    if($this->name == $searchName) {
      return true;
    } elseif($this->subMenu != null && is_a($this->subMenu, "Menu")) {
      foreach($this->subMenu as $tmpEntry) {
        if($tmpEntry->isEntryContaintCategory($searchName))
          return true;
      }
    }
    return false;
  }

  public function isSubMenuContainsVisibleCategories() {
    if($this->subMenu != null && is_a($this->subMenu, "Menu")) {
      foreach($this->subMenu as $tmpEntry) {
        if($tmpEntry->visible)
          return true;
      }
    }
    return false;
  }

  public function printEntry($strPrintCategory, $increment = 10) {
    if(!$this->visible) return "";
    global $absUrl;
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);
    $cssPageSelect = ($this->isEntryContaintCategory($strPrintCategory))?"selected":"unselected";
    $cssSelect = ($strPrintCategory == $this->name)?"selected":"unselected";

    $strCat = $strIncrement . "<li id=\"{$this->name}Entry\" class=\"nav tab {$cssPageSelect}\"";

    $onClickHL = "";
    if($this->subMenu != null) {
      $strCat .= " onmouseover=\"javascript:show_div('{$this->name}Category');\" onmouseout=\"javascript:hide_div('{$this->name}Category');\"";
      $onClickHL = " onclick=\"javascript:hide_div('{$this->name}Category');\"";
    }

    $strCat .= ">\n";

    $functJS = ($this->useAjax) ? "getJsonCategory" : "goCategory";
    $strCat .= $strIncrement . "  <a id=\"{$this->name}TextEntry\" class=\"{$cssSelect}\" href=\"javascript:{$functJS}('{$this->name}');\"{$onClickHL}>{$this->titlePage}";

	if($this->isSubMenuContainsVisibleCategories())
      $strCat .= "<span class=\"caret\"></span>";

	$strCat .= "</a>\n";

    // add new nav with second level menu
    if($this->subMenu != null && is_a($this->subMenu, 'Menu')) {
      $strCat .= $this->subMenu->printCategory($strPrintCategory, $increment + 2, true);
    }

    $strCat .= $strIncrement . "</li> <!-- end li nav tab {$this->name} -->\n";

    return $strCat;
  }
}

class Menu extends ArrayObject {
  public $name = "";

  public function __construct($tmpName = "menu") {
    $this->name = $tmpName;
  }

  public function appendEntry($newEntry) {
    if(!is_a($newEntry, "EntryMenu"))
      return false;
    $this[$newEntry->name] = $newEntry;
  }

  public function importMenu($tmpMenu) {
    if(!is_a($tmpMenu, "Menu"))
      return false;

    foreach($tmpMenu as $tmpEntry)
      $this->appendEntry($tmpEntry);
  }

  public function hasVisibledEntries() {
    foreach($this as $tmpEntry) {
      if($tmpEntry->visible) return true;
    }
    return false;
  }

  function printCategory($strCategorySelected, $increment = 8, $hiddenabled = false, $catName = "_") {

    if(!$this->hasVisibledEntries()) return "";

    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);

    $strCat  = $strIncrement . "<ul id=\"{$this->name}Category\" class=\"nav\"";
    if($hiddenabled)
      $strCat .= " onmouseout=\"javascript:hide_div(this.id);\" style=\"display: none; visibility: hidden;\"";
    $strCat .= ">\n";
    foreach($this as $tmpEntry) {
      $strCat .= $tmpEntry->printEntry($strCategorySelected, $increment + 2);
    }
    $strCat .= $strIncrement . "</ul> <!-- end ul tabs {$this->name} -->\n";

    return $strCat;
  }

  public function getDefaultEntry($recurse = true) {
    // function to change with php array filter
    foreach($this as $tmpEntry) {
      if($tmpEntry->isDefaultEntry)
        return $tmpEntry;
      //get recursive
      if($recurse && $tmpEntry->subMenu != null) {
        $tmpSubEntry = $tmpEntry->subMenu->getDefaultEntry();
        if($tmpSubEntry)
          return $tmpSubEntry;
      }
    }
    return false;
  }

  public function getEntryByName($entryName, $recurse = true) {
    // function to change with php array filter
    if (array_key_exists($entryName, $this))
      return $this[$entryName];

    // get recursive
    if($recurse)
    foreach($this as $tmpEntry) {
      if($tmpEntry->subMenu != null) {
        $tmpSubEntry = $tmpEntry->subMenu->getEntryByName($entryName);
        if($tmpSubEntry)
          return $tmpSubEntry;
      }
    }

    return false;
  }

  protected function tryParseMenu($menuArray, $useAjax = false) {
    if(!count($menuArray)) return false;

    foreach($menuArray->entry as $tmpXmlEntry) {
      if(!count($tmpXmlEntry)) continue;
      $xmlEntry = $tmpXmlEntry;

      try {
        if(!isset($xmlEntry['name']) || !isset($xmlEntry->titlePage)) continue;
        if($xmlEntry->titlePage == "") continue;

        $tmpIsVisible = (isset($xmlEntry['visible'])) ? (strtolower($xmlEntry['visible']) == "true") : true;
        $tmpIsDefault = (isset($xmlEntry['default'])) ? (strtolower($xmlEntry['default']) == "true") : false;

        $newEntry = new EntryMenu((string)$xmlEntry['name'], (string)$xmlEntry->titlePage, $tmpIsVisible, $tmpIsDefault, $useAjax);

        if(isset($xmlEntry->menu)) {
          if ($newEntry->subMenu == null) {
            $newEntry->subMenu = new Menu($newEntry->name);
          }
          $newEntry->subMenu->tryParseMenu($xmlEntry->menu, $useAjax);
        }

        $this->appendEntry($newEntry);

      } catch(Exception $e) {
        return false;
      }
    }

    return true;
  }

  function tryXmlParse($xmlStrMenu, $useAjax = false) {
    try {
      $xmlparsed = simplexml_load_string("<?xml version='1.0'?>\n" . $xmlStrMenu);
      return $this->tryParseMenu($xmlparsed, $useAjax);
    } catch (Exception $e) {
      return false;
    }
  }

  function getSiteMap($spaceCaract = "&nbsp;", $indentation = 4) {
    $strSitemap = "";
    foreach($this as $tmpEntry) {
      $strSitemap .= str_pad("", (strlen($spaceCaract)*$indentation), $spaceCaract, STR_PAD_LEFT);
      $strSitemap .= "<a href=\"javascript:goPage('{$tmpEntry->name}');\">{$tmpEntry->titlePage}</a><br/>\n";
      if($tmpEntry->subMenu != null) {
        $strSitemap .= $tmpEntry->subMenu->getSiteMap($spaceCaract, ($indentation != 0)?$indentation + $indentation:4);
      }
    }
    return $strSitemap;
  }

  function getXmlSitemap($webSiteName) {
    $actualDate = date("Y-m-d");
    $strSitemap = "";
    $exceptedPages = array("sitemap", "sitechoosing");
    global $absUrl;

    foreach($this as $tmpEntry) {
      if(in_array($tmpEntry->name, $exceptedPages)) continue;
      $strSitemap .= <<<EndSitemap
  <url>
    <loc>http://{$absUrl}?site={$webSiteName}&amp;category={$tmpEntry->name}</loc>
    <lastmod>{$actualDate}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>

EndSitemap;

      if($tmpEntry->subMenu != null && !in_array($tmpEntry->name, $exceptedPages)) {
        $strSitemap .= $tmpEntry->subMenu->getXmlSitemap($webSiteName);
      }
    }

    return $strSitemap;
  }
}

// ----------------------------
// Site Map foreach sites
function sitemap() {
  // sitemap
  $webPage = new WebPage();
  $webPage->docTitle    = "Choose your website";
  $webPage->metaWords   = "";

  $tmpMenu = $_SESSION["webMasterPage"]->menu;
  $strTab  = ($tmpMenu != null)?$tmpMenu->getSiteMap("-", 0):"Error on getting menu";
  $webPage->contentPage = <<<ENDSitemap
  <div id="planSite">
    <div style="text-align: left;">
{$strTab}
    </div>
  </div>\n
ENDSitemap;

  return $webPage;
}
// ----------------------------
?>
