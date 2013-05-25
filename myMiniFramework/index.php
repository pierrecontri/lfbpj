<?php
// -----------------------------------------
// index.php
// Pierre Contri
// sitmus de mon site web situe chez free.fr
// main page
// page controler
// last modifications : 04-2012
// -----------------------------------------

// -------------------------------
// Link about many framework files

require_once('./src/controls/documentsTypes.php'); // documents type management
include "./src/controls/datasManagment.php";       // BDD using
include "./src/views/tabsMenuesManagment.php";     // creating menus and submenus
include "./src/scripts/includeJavaScript.php";     // JavaScripts adding into scripts folder

// ------------------
// Sessions managment
session_name("SIDMINIFRWRK");
session_start();

// -----------------------------------------------------
// langage calculation
if(!isset($_SESSION["lang"])) {
  $langageTab = preg_split("/[\s-,;]+/",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
  $_SESSION["lang"] = ($langageTab[0] == "fr")?"fr":"en";
}

// mode HTML 5 : must be insert before all includes
// problem with IE if it's not here
print("<!DOCTYPE html>\n");
//print('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
//print("\n");

// -----------------
// WebSite variables
// Init Sessions var
if(!isset($_SESSION['websitename']))
  $_SESSION['websitename'] = "";
// get the list of sites (folder names into ./data)
if(!isset($_SESSION['sitesList'])) {
  $_SESSION['sitesList'] = getChildDirectories("./data");
}

// including content menu sites and global function sitemap
if(!isset($_SESSION["GlobalMenu"])) {
  //import the global menu for navigation
  include "./src/controls/global_nav.php";
  // prepare the global menu in an object for a future use
  $xmlGlobalMenu = getGlobalMenu();
  $menuGlobal = new Menu("global");
  $menuGlobal->tryXmlParse($xmlGlobalMenu);
  $_SESSION["GlobalMenu"] = $menuGlobal;
}

// -------------------------
// Gestion des inputs postes
$siteName = "sitechoosing";

if(isset($_REQUEST['site']) && $_REQUEST['site'] != "")
  $siteName = $_REQUEST['site'];
elseif(isset($_SESSION["websitename"]) && $siteName != "sitechoosing")
  $siteName = $_SESSION["websitename"];

$_SESSION['category'] = (isset($_REQUEST['category']))?$_REQUEST['category']:"";

if(in_array($_SESSION['category'], $_SESSION['sitesList']) || $_SESSION['category'] == "sitechoosing") {
  $siteName = $_SESSION['category'];
  $_SESSION['category'] = "";
}

// suppress the menu if use change site
// save the new site path file on session
$sheetname = (isset($_SESSION['sheetname']))?$_SESSION['sheetname']:"";
if($siteName != "" && $siteName != $_SESSION['websitename']) {
  if(isset($_SESSION["xmlMenu"]))
    unset($_SESSION["xmlMenu"]);
  if(isset($_SESSION["webMasterPage"]))
    unset($_SESSION["webMasterPage"]);

  if($siteName == "sitechoosing") {
    $sheetname = "./src/views/sitechoosing.php";
  }
  else {
    // no language
    $sheetname = "./data/{$siteName}/{$siteName}.php";
    // language test if nolang exists
    if(!file_exists($sheetname))
      $sheetname = "./data/{$siteName}/{$siteName}_{$_SESSION['lang']}.php";
    if(!file_exists($sheetname))
      $sheetname = "./data/{$siteName}.php";
    if(!file_exists($sheetname))
      $sheetname = "";
  }

  // saving the file name about this site
  $_SESSION['sheetname'] = $sheetname;
  $_SESSION["websitename"] = $siteName;
}

if($sheetname != "")
  include($sheetname); // define menu list and data fonctions

// -------------------
// get content of menu
// defaut meta words
$_SESSION["headerMetaWords"] = "Pierre Contri, musique instrumentale, programmation informatique, jeu du pong, Melting Potes, Moselle, Altroff, Echo";

/* transform the listpage array to a MenuList in object */
$xmlMenu = (isset($_SESSION["xmlMenu"]))?$_SESSION["xmlMenu"]:"";
$menu = new Menu("main");
$menu->tryXmlParse($xmlMenu);
if(isset($_SESSION["GlobalMenu"]))
  $menu->importMenu($_SESSION["GlobalMenu"]);

// $_SESSION["jsMenu"] = "<script type=\"text/javascript\">\n  var jsMenu = {$xmlMenu};\n</script>\n";

if($_SESSION['category'] == "") {
  $defaultEntry = $menu->getDefaultEntry();
  $_SESSION['category'] = ($defaultEntry !== false)?$defaultEntry->name:$menu[0]->name;
}

// get content of this page
// if header redirect, it's ok, because no printing on webpage at now
$webContentPage = (function_exists($_SESSION['category']))?$_SESSION['category']():"";

$webPage = null;
if(is_a($webContentPage, 'WebPage')) {
  $webPage = $webContentPage;
} else {
  $webPage = new WebPage();
  $webPage->docTitle    = "Error in getting web page";
  $webPage->metaWords   = "";
  $webPage->contentPage = "<p>Sorry, we can not get the asked web page</p><br/><p>Please, try again.</p>";
}

if(!isset($_SESSION["webMasterPage"])) {
  $webMasterPage = new WebMasterPage();
  $webMasterPage->webSiteName = $siteName;
  $webMasterPage->styleFile   = $siteName . ".css";
  $webMasterPage->menu        = $menu;
  $webMasterPage->javascripts = printJavaScript("./src/scripts");
  $_SESSION["webMasterPage"]  = $webMasterPage;
}

$_SESSION["webMasterPage"]->setWebPage($_SESSION["category"], $webPage);
if($_SESSION["webMasterPage"]->metaWords == "")
  $_SESSION["webMasterPage"]->metaWords = $_SESSION["headerMetaWords"];

// if it's a XMLRequest, don't send anything
// else
// use MVC viewing part for screen printing
if(isset($_REQUEST['getFunction'])) {
  if(function_exists($_REQUEST['getFunction']))
    var_export($_REQUEST['getFunction']());
}
else {
  include "./src/views/masterpage.php";
}

// Recuperation des addresses IP, plus trace sur la page
include "./src/controls/trace.php";
?>
