<?php
// correct coding for redirection
// but Free has disabled it, so we can not use this page
header('Location: ' . $_SERVER["HTTP_REFERER"] . 'data/myMiniFramework/index.php');
exit;
?>