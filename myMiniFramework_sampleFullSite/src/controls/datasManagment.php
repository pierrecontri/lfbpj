<?php
// Pierre Contri
// cree le 11/06/2009
// mdf  le 24/02/2010
// lecture de la BDD csv

require_once('documentsTypes.php');

/**
 * Function : getListElems
 *   Browse a csv file and return an array
 *   with the selected suject
 * Inputs : - file name (string)
 *          - subject (optional string) filter
 * Output : - infos array of Documents (DocumentsList)
 * Author : Pierre Contri
 */
function getListElems($ficNameListElem, $subject = "*") {
  $listEnreg = new DocumentsList();
  $listEnreg->subject = $subject;

  if(!file_exists($ficNameListElem))
    return $listEnreg;

  $finfo = pathinfo($ficNameListElem);
  $dirName = $finfo['dirname'];

  $ficListElem = fopen($ficNameListElem, "r");
  if($ficListElem === false)
    return $listEnreg;

  while(!feof($ficListElem)) {
    $enr = fgetcsv($ficListElem, 500, "|");

    //Recuperation des colonnes
    if($enr != "" && count($enr) > 3 && !preg_match("/^[\/]{2}.*$/",$enr[0])) {

      $newDoc = FactoryDocument::createDocument($enr);
      $newDoc->tryParseOnItself($enr, $dirName);

      // filtrage de la ligne
      if($newDoc->subject == $subject || $listEnreg->subject == "*")
        $listEnreg[$newDoc->id] = $newDoc;
    }
  }
  fclose($ficListElem);

  return $listEnreg;
}

/**
 * Function : getChildrenFiles
 *   Open the directory in parameters
 *   browse it, read it and get content
 * Inputs : - directory path (string)
 * Output : - array of directory content (only files)
 * Author : Pierre Contri
 */
function getChildrenFiles($directoryPath) {
  $contentDir = array();
  $d = opendir($directoryPath);
  if($d) {
    while($content = readdir($d))
      $contentDir[] = $content;
    closedir($d);
  }
  sort($contentDir);

  return $contentDir;
}

/**
 * Function : getChildrenDirectories
 *   Open the directory in parameters
 *   browse it, read it and get content
 * Inputs : - directory path (string)
 * Output : - array of directory content (only directories)
 * Author : Pierre Contri
 */
function getChildDirectories($directoryPath) {
  $dirArray = array();
  $contentDir = getChildrenFiles($directoryPath);
  foreach($contentDir as $f) {
    if(strstr($f,'.') == false)
      $dirArray []= $f;
  }
  return $dirArray;
}

/**
 * Function : 
 *   
 * Inputs : - directory path (string)
 *          - type of document (subject in the CSV DataBase)
 * Output : - infos array of Documents (DocumentsList)
 * Author : Pierre Contri
 */
function getContentList($repertoire, $type = "*", $recursive = true) {

  $contentTbl = new DocumentsList();
  if(!file_exists($repertoire)) return $contentTbl;

  $contentDir = getChildrenFiles($repertoire);

  $idx = 0;
  $contentTbl->subject = $repertoire;
  foreach($contentDir as $f) {
    $fInfo = pathinfo($f);
    if($fInfo['basename'][0] == '.') continue;

    $fileExt = (isset($fInfo['extension']))?$fInfo['extension']:"";

    if(strtolower($fileExt) == $type || $type == '*') {
      $newDoc = FactoryDocument::createDocument($repertoire ."/" . $f);
      $newDoc->id = $idx++;
      $newDoc->title = str_replace(array("_", "." . $fileExt), array(" ", ""), ucfirst($fInfo['basename']));
      $contentTbl[] = $newDoc;
	}

    if($fileExt == '' && $recursive)
      $contentTbl->childrenDocumentsList []= getContentList($repertoire . "/" . $f, $type);
  }

  return $contentTbl;
}

/* depreciated function */
function fillContentObjects($listElems) {
  /* management by array to object */
  if($listElems == null || !is_a($listElems, "DocumentsList"))
    return "<p>Error on the list to print</p>\n";

  return $listElems->fillContentObjects();
}
?>
