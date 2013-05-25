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

  var $name         = "";
  var $titlePage    = "";
  var $subMenu      = null;
  var $visible      = true;
  var $defaultEntry = false;

  public function __construct($name, $pageTitle, $visible = true) {
    $this->name = $name;
    $this->titlePage = $pageTitle;
    $this->visible = $visible;
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

  public function printEntry($strCategory, $increment = 10) {
    if(!$this->visible) return "";

    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);
    $strIsParentPage = ($this->isEntryContaintCategory($strCategory))?"selected":"unselected";
    $strIsCategory = ($strCategory == $this->name)?"selected":"unselected";

    $strCat = $strIncrement . "<li id=\"{$this->name}Entry\" class=\"nav tab {$strIsParentPage}\"";
    if($this->subMenu != null)
      $strCat .= " onmouseover=\"javascript:show_div('{$this->name}Category');\" onmouseout=\"javascript:hide_div('{$this->name}Category');\"";

    $strCat .= ">\n";
    $strCat .= $strIncrement . "  <a class=\"{$strIsCategory}\" href=\"javascript:goCategory('{$this->name}');\">{$this->titlePage}</a>\n";

    // add new nav with second level menu
    if($this->subMenu != null && is_a($this->subMenu, 'Menu')) {
      /* php 5.1 : bug info the next function
                   adding 4th parameter in waiting new version */
      $strCat .= $this->subMenu->printCategory($strCategory, $increment + 2, true, $this->name);
    }

    $strCat .= $strIncrement . "</li> <!-- end li nav tab {$this->name} -->\n";

    return $strCat;
  }
}

class Menu extends ArrayObject {
  public $name = "";

  public function __construct($tmpName) {
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
    /* due to a bug in php 5.1,
       insert of 4th parameter (name of category) */
    $tmpName = ($this->name != "")?$this->name:$catName;

    if(!$this->hasVisibledEntries()) return "";

    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);

    $strCat  = $strIncrement . "<ul id=\"{$tmpName}Category\" class=\"nav\"";
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

  protected function tryParseMenu($menuArray) {
    if(!count($menuArray)) return false;

    foreach($menuArray->entry as $tmpXmlEntry) {
      if(!count($tmpXmlEntry)) continue;
      $xmlEntry = $tmpXmlEntry;

      try {
        if(!isset($xmlEntry['name']) || !isset($xmlEntry->titlePage)) continue;
        $newEntry = new EntryMenu((string)$xmlEntry['name'], (string)$xmlEntry->titlePage);

        if(isset($xmlEntry['visible'])) {
          $newEntry->visible = (strtolower($xmlEntry['visible']) == "true");
        }

        if(isset($xmlEntry['default'])) {
          $newEntry->defaultEntry = (strtolower($xmlEntry['default']) == "true");
        }

        if(isset($xmlEntry->menu)) {
          if ($newEntry->subMenu == null) {
            $newEntry->subMenu = new Menu($newEntry->name);
          }
          $newEntry->subMenu->tryParseMenu($xmlEntry->menu);
        }

        $this->appendEntry($newEntry);

      } catch(Exception $e) {
        return false;
      }
    }

    return true;
  }

  function tryXmlParse($xmlStrMenu) {
    try {
      $xmlparsed = simplexml_load_string("<?xml version='1.0'?>\n" . $xmlStrMenu);
      return $this->tryParseMenu($xmlparsed);
    } catch (Exception $e) {
      return false;
    }
  }

  function getSiteMap($spaceCaract = "&nbsp;", $indentation = 4) {
    $strSitemap = "";
    foreach($this as $tmpEntry) {
      $strSitemap .= str_pad("", (strlen($spaceCaract)*$indentation), $spaceCaract, STR_PAD_LEFT);
      $strSitemap .= "<a href=\"javascript:;\" onclick=\"javascript:goPage('{$tmpEntry->name}');\">{$tmpEntry->titlePage}</a><br/>\n";
      if($tmpEntry->subMenu != null) {
        $strSitemap .= $tmpEntry->subMenu->getSiteMap($spaceCaract, ($indentation != 0)?$indentation + $indentation:4);
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
