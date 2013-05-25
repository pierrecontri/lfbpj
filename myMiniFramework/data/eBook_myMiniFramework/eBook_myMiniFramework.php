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
  <entry name="eBook_myMiniFramework" visible="true" default="true">
    <titlePage>Hello World</titlePage>
  </entry>
</menu>

EndMenu;
// -----------------------------------------

function eBook_myMiniFramework() {
  $webPage = new WebPage();
  $webPage->docTitle    = "eBook_myMiniFramework";
  $webPage->metaWords   = "eBook myMiniFramework";
  $webPage->contentPage = <<<EndTxt

<script type="text/javascript">
location = './data/eBook_myMiniFramework/readbook.php';
</script>

EndTxt;
//window.open({$_SERVER["HTTP_REFERER"]} . 'data/eBook_myMiniFramework/myMiniFramework_eBook.pdf');

  return $webPage;
}
// End of content
// -----------------------------------------
?>
