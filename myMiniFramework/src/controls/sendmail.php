<?php
  if(!isset($_POST))
    die("Echec de r&eacute;cup&eacute;ration des donn&eactue;es du mail");

  $options = "From: " . $_POST['mail_from'];
  $mail_to = (isset($_POST['mail_to']))?$_POST['mail_to']:"pierre.contri@free.fr";
  $mail_message = (isset($_POST['mail_message']))?$_POST['mail_message']:"";
  $mail_subject = (isset($_POST['mail_subject']))?$_POST['mail_subject']:"";
?>
<!DOCTYPE html>
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Envoi de mail</title>
</head>
<body>
<h1>Envoi du nouveau mail</h1>

<?php
  if(!preg_match('/^.+@.+$/',$options)) {
    print("<p>Echec d'envoi de mail; mauvaise adresse mail.</p>");
  }
  else if($mail_subject == "") {
    print("<p>Echec d'envoi de mail; pas de sujet.</p>");
  }
  else if($mail_message == "") {
    print("<p>Echec d'envoi de mail; pas de corps de message.</p>");
  }
  else if($mail_to == "") {
    print("<p>Echec d'envoi de mail; pas de destinataire.</p>");
  }
  else {

    if(mail($mail_to,stripslashes($mail_subject),stripslashes($mail_message),$options))
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
