<?php

// Pierre Contri
// Fichier de tracage des visiteurs
// enregistre dans un fichier text les visites sur le site web, les pages, l'heure, ...

// ouvrir un fichier en ajout d'ecriture
// le nom du fichier contient l'annee et le mois
//date_default_timezone_set('Europe/Paris');
$ficTrace = fopen("./log/logConnexion" . date("Ym") . ".log","a+");

if($ficTrace) {
  // constitution de la ligne a ajouter
  // date; addresseIP; remote host; page selectionnee
  $ligneTrace = date("Y-m-d H:i:s") . ";" . $_SERVER["REMOTE_ADDR"] . ";" . $_SESSION["sheetname"] . ";" . $_SESSION['category'] . ";";
  // ecriture
  fwrite($ficTrace, $ligneTrace . "\r\n");
  // refermer le fichier
  fclose($ficTrace);
}

?>