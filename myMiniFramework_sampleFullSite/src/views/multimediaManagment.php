<?php

/* CompressImg() */
function compressImg($Image, $TailleX) {
  // On cherche a obtenir les info concernant l'image (repertoire de base, nom et extension)
  $InfoFile = pathinfo($Image);
  $Extension = $InfoFile['extension'];
 
  // On verifie l'extension
  switch ( strtolower($Extension) ) {
    case "jpg":
      $RessourceImage = imagecreatefromjpeg($Image);
      $Valide = true;
      break;
 
    case "jpeg":
      $RessourceImage = imagecreatefromjpeg($Image);
      $Valide = true;
      break;
 
    case "gif":
      $RessourceImage = imagecreatefromgif($Image);
      $Valide = true;
      break;
 
    case "png":
      $RessourceImage = imagecreatefrompng($Image);
      $Valide = true;
      break;
 
    // Si l'extension est inconnue
    default : $Valide = false;
  }
 
  // Si le format n'est pas reconnu
  if ( !$Valide ) {
    echo "Erreur !<br>L'image $ImageACompresser est dans un format inconnu !<br>";
    exit();
  }
 
  // On recupere la largeur et la hauteur de l'image
  $ImgLargeur= imagesx($RessourceImage);
  $ImgHauteur = imagesy($RessourceImage);
 
  // On calcule le ratio
  $Coef = $ImgLargeur/$TailleX;
 
  // On calcule la hauteur voulue avec le coef trouve
  $Largeur = $TailleX;
  $Hauteur = round(($ImgHauteur/$Coef ), 0);
 
  // On cree une image vide de la taille voulu
  $Img = imagecreatetruecolor($Largeur, $Hauteur);
 
  // On copie l'ancienne image dans la nouvelle redimmensionnee
  imagecopyresampled($Img, $RessourceImage, 0, 0, 0, 0, $Largeur, $Hauteur, $ImgLargeur, $ImgHauteur);
 
  // On finalise l'operation
  $NomImage = "cmp_".$Image;
  imagejpeg($Img, $NomImage);
}

function sendFile($label, $url, $repDest, $bddName, $sujet, $max_size_file)
{
  $strSend = <<<EndSendFile
      <p><br /><br /></p>
      <div id="accesFile">
          <table>
            <tr><td colspan="2" style="text-align:left;">{$label}</td></tr>
            <input type="hidden" name="url" value="{$url}" />
            <input type="hidden" name="repDest" value="{$repDest}" />
            <input type="hidden" name="bddName" value="{$bddName}" />
            <input type="hidden" name="sujet" value="{$sujet}" />
            <input type="hidden" name="MAX_FILE_SIZE" value="{$max_size_file}" />
            <tr><td>Transf&egrave;re le fichier</td><td><input type="file" name="fileToSend" /></td></tr>
            <input type="hidden" name="titre" value="null" />
            <tr><td>Commentaire</td><td><input type="text" name="comment" /></td></tr>
            <tr><td colspan="2" style="text-align:right;"><input type="button" value="Envoyer" onclick="javascript:document.getElementById('formSite').action='fileupload.php'; document.getElementById('formSite').enctype = 'multipart/form-data'; document.getElementById('formSite').submit();" /></td></tr>
          </table>
      </div>;
EndSendFile;

  return $strSend;
}
?>