<?php
// fileupload.php
// Pierre Contri
// cree le 22/09/2005
// modifie le 22/09/2006
// Telechargement de morceaux et photos

include "./gestionFichiers.php";

// tests des différents parametres d'entree
if(isset($_POST["repDest"])
   && isset($_FILES["fileToSend"]["name"])
   && isset($_POST["titre"]))
{
  // ok
}
else
{
  // Afficher l'erreur
  print("<script type=\"text/javascript\">alert(\"Il manque un param&egrave; d'entr&eacute;e !\");</script>\n");
  exit();
}

    $repertoireDestination = $_POST["repDest"];
    $nomDestination        = $_FILES["fileToSend"]["name"];

    // Pour raison de sécurité nous ajouterons aux fichiers
    // portant une extension .php .php3, l'extension .txt
    if (eregi(".php", $nomDestination)) {
        $nomDestination .= ".txt";
    }

    if (is_uploaded_file($_FILES["fileToSend"]["tmp_name"])) {
        if (rename($_FILES["fileToSend"]["tmp_name"],
                   $repertoireDestination.$nomDestination)) {
            echo "Le fichier " . $nomDestination . " est t&eacute;l&eacute;charg&eacute;<br />\n";
            // enregistrer les donnees en bdd
            // champ 2 : titre
            // champ 3 : chemin
            // champ 4 : commentaire
            // champ 5 : sujet
            $tabElems = array($_POST["titre"],$repertoireDestination . $nomDestination,((isset($_POST["comment"]))?$_POST["comment"]:""),$_POST["sujet"]);
            // chemin de la bdd
            $bddName = $repertoireDestination . $_POST["bddName"] . ".txt";

            ajoutElement($bddName, $tabElems);
        } else {
            echo "Le fichier " . $nomDestination . " n'a pas &eactue;t&eacute; copi&eacute; !<br />\n";
       }          
    } else {
       echo "Le fichier n'a pas été upload&eacute (trop gros ? > 2Mo)";
    }

  echo "<script type=\"text/javascript\">location.href = '" . $_POST["url"] . "';</script>\n";
  exit();
?>
