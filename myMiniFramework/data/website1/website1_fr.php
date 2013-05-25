<?php
// Mon site musical en php
// Pierre Contri
// Cree le 02/11/2004
// modifie le 21/01/2011
// Editeur : Notepad
// Content the data of this site

// Files database names in environment variables
$_SESSION["ficMorceaux"]  = "./data/website1/audmp3/morceaux_en.csv";

// ---------------------------------------------
// menu
if(!isset($_SESSION["xmlMenu"]))
$_SESSION["xmlMenu"] = <<<EndMenu
<menu name="main">
  <entry name="autresmorceaux" visible="true" default="true">
    <titlePage>Reprises</titlePage>
  </entry>
  <entry name="contact" visible="true">
    <titlePage>Contact</titlePage>
  </entry>
</menu>

EndMenu;
// End of menu
// -----------------------------------------

// -----------------------------------------
// Creating web pages
function autresmorceaux() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Morceaux travaill&eacute;s avec diff&eacute;rents groupes";
  $webPage->metaWords   = "";
  $listElems = getListElems($_SESSION["ficMorceaux"], "mot-cle recherche");
  $webPage->contentPage = <<<EndTxt
<p>Les morceaux pr&eacute;sent&eacute;s ci dessous ont &eacute;t&eacute;s travaill&eacute;s et enregistr&eacute;s dans mon studio. Ce travaille ne repr&eacute;sente qu'une petite partie de ma production. La plupart d'entre eux ne sont encore qu'au stade de maquettes.<br /></p>
{$listElems->fillContentObjects()}
EndTxt;

  return $webPage;
}

function contact() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Ecrivez-moi";
  $webPage->metaWords   = "";
  $webPage->contentPage = <<<ENDContact
    <div>
    <table align="center">
      <tr><td><label>Emetteur :&nbsp;</label></td><td><input type="text" name="from" size="50" value="votre.e-mail@votre.fournisseur" /></td></tr>
      <tr><td><label>Sujet :&nbsp;</label></td><td><input type="text" size="50" name="suject" /></td></tr>
      <tr>
        <td colspan="2">
          <label>Message :&nbsp;</label><br />
          <textarea name="message" cols="60" rows="15"></textarea><br />
          <input type="hidden" name="to" value="pierre.contri@free.fr" />
          <input type="button" class="btnsubmit" value="Envoyer" onclick="javascript:postMail();"/><br />
        </td>
      </tr>
    </table>
    </div>\n
ENDContact;
  return $webPage;
}
// End of content
// -----------------------------------------
?>
