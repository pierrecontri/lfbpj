<?php
// Mon site musical en php
// Pierre Contri
// Cree le 02/11/2004
// modifie le 12/02/2010
// Editeur : Notepad
// Content the data of this site

// Files database names in environment variables
// Aucune base necessaire pour l'exemple Hello World
/*
$_SESSION["ficPhotos"]    = $_SESSION["dirPhotos"]   . "/photos_en.csv";
$_SESSION["ficMorceaux"]  = $_SESSION["dirMorceaux"] . "/morceaux_en.csv";
*/

// ---------------------------------------------
// menu

if(!isset($_SESSION["xmlMenu"]))
$_SESSION["xmlMenu"] = <<<EndMenu
<menu name="main">
  <entry name="helloworld" visible="true" default="true">
    <titlePage>Hello World</titlePage>
  </entry>
</menu>

EndMenu;
// -----------------------------------------

function helloworld() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Hello World";
  $webPage->metaWords   = "test myMiniFramework par Hello World";
  $webPage->contentPage = <<<EndTxt
<h1>Hello World !</<h1>

EndTxt;
  return $webPage;
}
// End of content
// -----------------------------------------
?>
