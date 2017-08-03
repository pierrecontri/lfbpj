<?php
// Pierre Contri
// cree le 30/01/2005
// mdf  le 10/06/2009
// lecture et ecriture
// dans fichiers

function ajoutElement($fileName, $tabElems) {
  // recuperation du dernier ID de la base
  $strId = getLastId($fileName) + 1;

  // aller, faut coder
  // preparer la ligne a ecrire
  $strLigne = "| " . $strId . " | ";
  foreach ($tabElems as $elems) {
    if($elems != "null")
      $strLigne .=  $elems . " | ";
  }

  $strLigne = rtrim($strLigne) . "\n";

  // ouvrir le fichier en ecriture et ajout
  $fic = fopen($fileName,"a");
  if(!$fic) die("Erreur d'ouverture du fichier en &eacute;criture "+ $fileName + " !\n");
  else {
    // ecrire la ligne
    if(!fwrite($fic, $strLigne)) {
      echo "Impossible d'écrire dans le fichier " . $fileName;
    }
    // fermer le fichier
    fclose($fic);
  }
}

function getLastId($ficName) {
  $id = 0;
  // ouvrir le fichier
  $fic = fopen($ficName,"r");
  if($fic) {
    // recuperer le dernier id
    while(!feof($fic)) {
      $cols = explode("|",fgets($fic));
      if(count($cols)>1) {
	    $id = trim($cols[1]);
      }
    }
    // fermer le fichier
    fclose($fic);
  }
  return $id;
}
?>
