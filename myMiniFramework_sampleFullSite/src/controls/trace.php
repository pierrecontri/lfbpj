<?php
// Pierre Contri
// Fichier de tracage des visiteurs
// enregistre dans un fichier text les visites sur le site web, les pages, l'heure, ...

class TraceLog {

  public static function setTimezone($timezoneName = 'Europe/Paris') {
    date_default_timezone_set($timezoneName);
  }

  /**
   * Method: writeInfos
   *    ouvrir un fichier en ajout d'ecriture
   *    le nom du fichier contient l'annee et le mois
   *    save the informations about visitor (site, page, category
   * Inputs: 
   *    - hostsrv   (string) : the hostname or IP Address of visitor
   *    - sheetname (string) : the visiting site
   *    - category  (string) : the page of the actual site
   */
  public static function writeInfos($hostsrv, $sheetname, $category) {
    $ficTrace = fopen("./log/logConnexion" . date("Ym") . ".log","a+");
    if($ficTrace !== false) {
      // constitution de la ligne a ajouter
      // date; addresseIP; remote host; page selectionnee
      $strdate = date("Y-m-d H:i:s");
      $ligneTrace = "{$strdate};{$hostsrv};{$sheetname};{$category};\r\n";
      // ecriture
      fwrite($ficTrace, $ligneTrace);
      // refermer le fichier
      fclose($ficTrace);
      return true;
    }
    return false;
  }
}

TraceLog::writeInfos($_SERVER["REMOTE_ADDR"], $_SESSION["sheetname"], $_SESSION['category']);
?>