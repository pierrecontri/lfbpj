<?php
// Pierre Contri
// cree le 11/06/2009
// mdf  le 24/02/2010
// lecture de la BDD csv

//require_once('../models/documentsTypes.php');

class DataManagement {

  // region : inline functions used for the array_walk

  public static function createDoc($enr, $listEnreg, $dirName, $isHeader, $subject) {
    $newDoc = FactoryDocument::createDocument($enr, $isHeader);
    $newDoc->tryParseOnItself($enr, $dirName, $isHeader);
    // filtrage de la ligne
    // $newDoc->subject is a list of subject separated by ';'
    $arraySubject = explode(";", $newDoc->subject);
    if(count($arraySubject) > 0 && in_array($subject, $arraySubject) || $listEnreg->subject == "*")
      $listEnreg[$newDoc->id] = $newDoc;
  }

  public static function dsvExtractLine($line) {
    return str_getcsv(trim($line), "|");
  }

  public static function name_columns(&$row, $key, $headerKeys) {
    $row = array_combine($headerKeys, $row);
  }

  public static function walkCreateDoc(&$row, $key, $arr_arg) {
    if($row != "" && count($row) > 3 && !preg_match("/^[\/]{2}.*$/",(($arr_arg[2])?$row["id"]:$row[0])))
      DataManagement::createDoc($row, $arr_arg[0], $arr_arg[1], $arr_arg[2], $arr_arg[3]);
  }

  // endregion : inline functions

  /**
   * Function : getListElems
   *   Browse a csv file and return an array
   *   with the selected suject
   * Inputs : - file name (string)
   *          - subject (optional string) filter
   * Output : - infos array of Documents (DocumentsList)
   * Author : Pierre Contri
   */
  public static function getListElems($ficNameListElem, $subject = "*", $isHeader = true) {
    $listEnreg = new DocumentsList();
    $listEnreg->subject = $subject;

    if(!file_exists($ficNameListElem))
      return $listEnreg;
  
    $finfo = pathinfo($ficNameListElem);
    $dirName = $finfo['dirname'];

    $lines = array_map('DataManagement::dsvExtractLine', file($ficNameListElem));

    if($isHeader) {

      $headerCols = array_shift($lines);

      array_walk($lines, 'DataManagement::name_columns', $headerCols);
    }

    array_walk($lines, 'DataManagement::walkCreateDoc', array($listEnreg, $dirName, $isHeader, $subject));

/*
    else {

      $ficListElem = fopen($ficNameListElem, "r");
      if($ficListElem === false)
    	  return $listEnreg;

      while(!feof($ficListElem)) {
        $enr = fgetcsv($ficListElem, 1000, "|");
        if($enr != "" && count($enr) > 3 && !preg_match("/^[\/]{2}.*$/",$enr[0]))
          DataManagement::createDoc($enr, $listEnreg, $dirName, $isHeader, $subject);
      }
      fclose($ficListElem);
    }
*/
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
  public static function getChildrenFiles($directoryPath) {
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
  public static function getChildDirectories($directoryPath) {
    $dirArray = array();
    $contentDir = self::getChildrenFiles($directoryPath);
    foreach($contentDir as $f) {
  	if(strstr($f,'.') == false)
  	  $dirArray []= $f;
    }
    return $dirArray;
  }
  
  /**
   * Function : getContentList
   *   
   * Inputs : - directory path (string)
   *          - type of document (subject in the CSV DataBase)
   * Output : - infos array of Documents (DocumentsList)
   * Author : Pierre Contri
   */
  public static function getContentList($repertoire, $type = "*", $recursive = true) {
  
    $contentTbl = new DocumentsList();
    if(!file_exists($repertoire)) return $contentTbl;
  
    $contentDir = self::getChildrenFiles($repertoire);
  
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
  	  $contentTbl->childrenDocumentsList []= self::getContentList($repertoire . "/" . $f, $type);
    }
  
    return $contentTbl;
  }
  
  /**
   * Function : getChildrenEntryName
   *   
   * Inputs : - xml document
   *          - tag name to select the childs
   *          - name of the type to seach
   * Output : - array of tag names
   * Author : Pierre Contri
   */
  public static function getChildrenEntryName($xmlDocument, $xmlTagName, $xmlMainEntry) {
    $xmlDoc = new SimpleXMLElement($xmlDocument);
    $xpathExpression = "/menu/{$xmlTagName}[@name='{$xmlMainEntry}']/menu/{$xmlTagName}/@name";
    $result = $xmlDoc->xpath($xpathExpression);

    $childrenEntryName = array();
    while(list(,$node) = each($result)) {
      $entryName = (string)$node;
      if($entryName != "")
        $childrenEntryName []= $entryName;
    }

    return $childrenEntryName;
  }

  /* depreciated function */
  private static function fillContentObjects($listElems) {
    /* management by array to object */
    if($listElems == null || !is_a($listElems, "DocumentsList"))
  	return "<p>Error on the list to print</p>\n";
  
    return $listElems->fillContentObjects();
  }
}
?>