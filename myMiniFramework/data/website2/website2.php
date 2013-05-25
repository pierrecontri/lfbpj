<?php
// Mon site musical en php
// Pierre Contri
// Cree le 02/11/2004
// modifie le 25/09/2006
// Sous Notepad

// Definition des variables
// Files database names in environment variables
$_SESSION["dirPhotos"]    = dirname($_SESSION['sheetname']) . "/photos";
$_SESSION["dirMorceaux"]  = dirname($_SESSION['sheetname']) . "/audmp3";
$_SESSION["dirDocuments"] = dirname($_SESSION['sheetname']) . "/audmp3";
$_SESSION["ficPhotos"]    = $_SESSION["dirPhotos"]   . "/photos.csv";
$_SESSION["ficMorceaux"]  = $_SESSION["dirMorceaux"] . "/morceaux_fr.csv";

// ------------------------------------------
// Menu
if(!isset($_SESSION["xmlMenu"]))
$_SESSION["xmlMenu"] = <<<EndMenu
<menu name="main">
  <entry name="grpecho" default="true">
    <titlePage>Echo</titlePage>
  </entry>
  <entry name="morceauxEcho">
    <titlePage>Morceaux</titlePage>
    <menu name="menuMorceauxEcho">
      <entry name="concert201204">
        <titlePage>Concert du 04-2012</titlePage>
      </entry>
    </menu>
  </entry>
  <entry name="photosEcho">
    <titlePage>Photos</titlePage>
    <menu name="menuPhotosEcho">
      <entry name="photosPremierConcert">
        <titlePage>Premier Concert</titlePage>
      </entry>
      <entry name="photosConcertLux201204">
        <titlePage>Au Café du Commerce</titlePage>
      </entry>
      <entry name="videosEcho">
        <titlePage>Vidéos</titlePage>
      </entry>
    </menu>
  </entry>
</menu>

EndMenu;
// ------------------------------------------

function grpecho() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Echo";
  $webPage->metaWords   = "Echo,Reprises Pop-Rock";
  $webPage->contentPage = <<<ENDComments
  <p style="font-style: italic;">Pr&eacute;sentation du groupe</p>
  <p>Ce groupe a &eacute;t&eacute; fond&eacute; en 2010.
Cinq personnes composent ce dernier, dont quatre venant de Melting Potes.</p>
<ul>
  <li>Alexandre (Batterie)</li>
  <li>Fabrice (Guitare)</li>
  <li>Julien (Chant)</li>
  <li>Pascal (Guitare)</li>
  <li>Pierre (Basse)</li>
</ul>
ENDComments;
  return $webPage;
}

function morceauxEcho() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Morceaux &agrave; travailler";
  $webPage->metaWords   = "Echo,Reprises Pop-Rock";
  $listElems = getContentList($_SESSION["dirDocuments"], "txt");
  $listElems2 = getListElems($_SESSION["ficMorceaux"], "reprisesEcho");

  $webPage->contentPage = <<<ENDTXT
  <p>Liste des morceaux &agrave; bosser</p>
{$listElems->fillContentObjects()}

  <p>Mocreaux enregistr&eacute;s en repette</p>
{$listElems2->fillContentObjects()}

ENDTXT;

  return $webPage;
}

function concert201204() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Au Caf&eacute; du Commerce";
  $webPage->metaWords   = "Echo,Reprises Pop-Rock";
  $listElems = getListElems($_SESSION["ficMorceaux"], "concert201204");
  $webPage->contentPage = <<<ENDTxt
  <p>Ce concert a eu lieu au Caf&eacute; du Commerce au Luxembourg.</p>
  <p>Notre r&eacute;pertoire ce soir l&agrave; f&ucirc;t compos&eacute; de 14 reprises pop-rock connues de tous</p>
{$listElems->fillContentObjects()}
ENDTxt;

  return $webPage;
}

function photosEcho() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Photos du groupe";
  $webPage->metaWords   = "Echo,Reprises Pop-Rock";
  $webPage->contentPage = <<<END
  <p>Dans cette section, vous trouverez les diff&eacute;rentes photos r&eacute;alis&eacute;es lors de nos concerts</p>
END;

  return $webPage;
}

function photosPremierConcert() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Notre premier concert";
  $webPage->metaWords   = "Echo,Reprises Pop-Rock";
  $listElems = getListElems($_SESSION["ficPhotos"], "AnniversaireFabrice");
  $webPage->contentPage = <<<ENDTxt
  <p>Fabrice nous a permis de faire notre premi&egrave;re sc&egrave;ne lors de son anniversaire.</p>
  <p>Ce f&ucirc;t r&eacute;ussi !</p>
{$listElems->fillContentObjects()}
ENDTxt;

  return $webPage;
}

function photosConcertLux201204() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Au Caf&eacute; du Commerce";
  $webPage->metaWords   = "Echo,Reprises Pop-Rock";
  $listElems = getListElems($_SESSION["ficPhotos"], "ConcertLux201204");
  $webPage->contentPage = <<<ENDTxt
  <p>Ce concert a eu lieu au Caf&eacute; du Commerce au Luxembourg.</p>
  <p>Notre r&eacute;pertoire ce soir l&agrave; f&ucirc;t compos&eacute; de 14 reprises pop-rock connues de tous</p>
{$listElems->fillContentObjects()}
ENDTxt;

  return $webPage;
}

function videosEcho() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Vid&eacute;os Echo";
  $webPage->metaWords   = "Echo,Reprises Pop-Rock";
  $webPage->contentPage = <<<TxtVideos
  <p>Bient&ocirc;t une vid&eacute;o de notre dernier concert.</p>
TxtVideos;
  return $webPage;
}
?>
