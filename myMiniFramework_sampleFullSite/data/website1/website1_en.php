<?php
// Mon site musical en php
// Pierre Contri
// Cree le 02/11/2004
// modifie le 12/02/2010
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
    <titlePage>Covers</titlePage>
  </entry>
  <entry name="contact" visible="true">
    <titlePage>Contact</titlePage>
  </entry>
</menu>

EndMenu;
// -----------------------------------------

function autresmorceaux() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Pieces worked with various groups";
  $webPage->metaWords   = "";

  $listElems = getListElems($_SESSION["ficMorceaux"], "mot-cle recherche");
  $webPage->contentPage = <<<EndTxt
<p>The pieces presented below have worked summers and recorded in my studio. This work represents only a small part of my production. Most of them are still only models.<br /></p>
{$listElems->fillContentObjects()}
EndTxt;
  return $webPage;
}

function contact() {
  $webPage = new WebPage();
  $webPage->docTitle    = "Send me mail";
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
