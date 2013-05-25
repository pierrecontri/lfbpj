<?php
/*
header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="eBook_myMiniFramework.pdf"');
readfile('eBook_myMiniFramework.pdf');
*/

header('Location: ' . $_SERVER["HTTP_REFERER"] . 'data/eBook_myMiniFramework/myMiniFramework_eBook.pdf');
?>