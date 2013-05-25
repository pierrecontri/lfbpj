<?php
  isset($_POST) or
  die("Echec de r&eacute;cup&eacute;ration des donn&eactue;es du mail");

  $options = "From: " . $_POST['from'];

?>

<!DOCTYPE doctype PUBLIC "-//w3c//dtd html 4.0 transitional//en">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Envoi de mail</title>
</head>
<body>
<h1>Envoi du nouveau mail</h1>

<?php
  if(!eregi("^.+@.+$",$options)) {
    print("<p>Echec d'envoi de mail; mauvaise adresse mail.</p>");
  }
  else if($_POST['suject'] == "") {
    print("<p>Echec d'envoi de mail; pas de sujet.</p>");
  }
  else if($_POST['message'] == "") {
    print("<p>Echec d'envoi de mail; pas de corps de message.</p>");
  }
  else {

    if(mail($_POST['to'],stripslashes($_POST['suject']),stripslashes($_POST['message']),$options))
    {
      echo '<p>Le mail est bien envoy&eacute;.</p><script>setTimeout("window.close()",1500);</script>';
    }
    else
    {
      print("<p>Echec d'envoi de mail. Merci de recommencer ulterieurement</p>");
    }
  }
?>

  <br>
  <a href="javascript:window.close();">Fermer la fen&ecirc;tre.</a>

  <br>
</body>
</html>
