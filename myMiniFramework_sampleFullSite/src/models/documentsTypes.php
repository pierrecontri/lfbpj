<?php

/**
 * Constantes Definition
 * About : - documents type
 *         - object structure for data (in case of object)
 *         - array extension type
 */

/*
define('DOCTYP_PICTURE','PictureDocument');
define('DOCTYP_MUSIC','MusicDocument');
define('DOCTYP_TEXT','TextDocument');
define('DOCTYP_TOOL','ToolDocument');
define('DOCTYP_DOCUMENTSLIST','DocumentsList');
define('DOCTYP_UNKNOWN','UnknownDocument');

define('OBJDATA_ID','id');
define('OBJDATA_TITLE','title');
define('OBJDATA_PATH','path');
define('OBJDATA_SUBJECT','subject');
define('OBJDATA_COMMENTS','comments');
define('OBJDATA_TYPE','filetype');

define('TYPEXT_PICTURE','jpg,bmp,gif');
define('TYPEXT_MUSIC','mp3,wav,wma');
define('TYPEXT_DOCUMENT','doc,pdf,ppt,pptx,ppsx,pps,docx,odt,txt');
define('TYPEXT_SCRIPT','ps1,psm1,bat,cs,html,xml,htm, xhtml,sh,pl,pm,php,py, pym');
define('TYPEXT_TOOL','zip,7z');
*/

/* interfaces on PHP >= 5.3.5 */
interface iPresentation {
  public function printObj($increment = 4);
  public function getCssClassObj();
}

abstract class Document implements iPresentation {
  var $id = 0;
  var $title = "";
  var $subject = "";
  var $path = "";
  var $htmlPath = "";
  var $comments = "";
  var $directoryPath = "";

  public static $typext = null;
  public static $classContentObj = "ContentObj";

  public function __construct($pathfile = "") {
    $this->path = $pathfile;
  }

  public function getCssClassObj() {
    return static::$classContentObj;
    // PHP 5.1 error on static variable, work around
    //$contentObjClass = (get_class($this) == "ScriptDocument") ? "ContentObjScript" : "ContentObj";
    //return $contentObjClass;
  }

  public function tryParseOnItself($enr, $dirName = ".", $columnsNamed = false) {
    try {
      if(!is_array($enr) && count($enr) < 4)
        return false;

      if($columnsNamed) {
        foreach(array_keys($enr) as $var_name)
          $this->$var_name = trim($enr[$var_name]);
      }
      else {
        $idx = 0;
        
        $this->id       = trim($enr[$idx++]);
        $this->title    = (count($enr) > 4)?trim($enr[$idx++]):"";
        $this->path     = trim($enr[$idx++]);
        $this->comments = trim($enr[$idx++]);
        $this->subject  = trim($enr[$idx++]);
      }

      $this->path = $dirName . "/" . $this->path;
      $this->directoryPath = $dirName;

      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public function __toString() {
    $tabReturn = array();
    $reflector = new ReflectionClass($this);
    $docProperties = $reflector->getProperties();
    foreach($docProperties as $docProp) {
      if($docProp->getValue($this) != "")
        $tabReturn []= ucfirst($docProp->getName()) . ": " . $docProp->getValue($this);
    }
    return implode("<br />\n", $tabReturn);
  }

  public function getObjectHiddenField() {
    return <<<ENDObj
<input type="hidden" name="objElem" id="objElem{$this->id}" value="{$this->path};{$this->comments}" />\n
ENDObj;
  }

  function checkAndCorrectPath() {
    $infoFile = pathinfo($this->path);
    $nomFileLower = $infoFile['dirname'] . "/" . strtolower($infoFile['basename']);
    $nomFileUpper = $infoFile['dirname'] . "/" . strtoupper($infoFile['basename']);

    if(file_exists($this->path));
    elseif(file_exists($nomFileLower))
      $this->path = $nomFileLower;
    elseif(file_exists($nomFileUpper))
      $this->path = $nomFileUpper;
    else
      return false;

    $this->htmlPath = Document::convert_to_absURLPath($this->path);
//TODO: remove the strap and code into each printObj function
//    if(! is_a($this, "ScriptDocument") && ! is_a($this, "PictureDocument"))
//      $this->path = $this->htmlPath;

    return true;
  }

  function convert_to_absURLPath($tmpPath) {
    // to do
    // transform the relative path to absolute (due to documents downloading problems)
    global $absUrl;
    if(substr($tmpPath,0,2) == "./")
      $this->tmpPath = substr($tmpPath,2);
    return $absUrl . "/" . $this->tmpPath;
  }

  public function printObj($increment = 4) {
    return (!$this->checkAndCorrectPath())?"File '{$this->path}' does not exist":"No defined type for '{$this->path}'";
  }

  /* only in php 5.3 */
  public static function isDocumentType($filepath) {
    $extension = "";

    try {
      $tmpArr = explode(".",$filepath);
      $extension = strtolower(array_pop($tmpArr));
    } catch(Exception $iep) {
      return false;
    }

    return (strpos(static::$typext,$extension) !== false);
  }

  public static function getHyperLinkForList($htmlLink, $htmlDescription) {
    return "<li><a href=\"javascript:;\" onclick=\"javascript:goPage('{$htmlLink}'); return false;\">{$htmlDescription}</a></li>\n";
  }
}

class PictureDocument extends Document {

  public static $typext = 'jpg,bmp,gif';
  public static $classContentObj = "ContentObj";

  public function printObj($increment = 4) {
    parent::printObj($increment);
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);

    // get compress picture if it exists
    $infoFile = pathinfo($this->path);
    $file2 = $infoFile['dirname'] . "/compress/cmp_" . $infoFile['basename'];
    $file_cmp = (file_exists($file2)) ? $file2 : $this->path;

    $file_cmp = Document::convert_to_absURLPath($file_cmp);

    $strObj  = $strIncrement . "<div class=\"" . get_class($this) . "\">\n";
    $strObj .= $strIncrement . <<<ContentPict
	<img id="pictureImg{$this->id}" src="{$file_cmp}" class="pictureImgTab" alt="{$this->comments}" onclick="javascript:show_picture_fullScreen('{$this->htmlPath}','{$this->comments}');" onmouseover="this.style.cursor='hand';" /><br/>\n
ContentPict;

    if($this->comments != "")
      $strObj .= $strIncrement . "  <div class=\"libele\">{$this->comments}</div>\n";
    $strObj .= $strIncrement . "</div>\n";

    return $strObj;
  }

  public static function printPictureManagement($sourcesList) {

    $isDiaporama = false;
    $isDiapoJavaScript = (int)$isDiaporama;
    $translateSlideShow = ($isDiaporama)?"table":"slideshow";
    $showInSlideShow = (!$isDiaporama)?"style=\"visibility: hidden; display: none;\"":"";

/*

Simplifie

        <!-- Picture management part -->
        <div id="divDiapoManage">
          <input type="hidden" id="isDiaporama" name="isDiaporama" value="{$isDiapoJavaScript}" />
          <input type="button" id="isDiapo" class="btnsubmit" value="Switch to the pictures {$translateSlideShow}" onclick="javascript:switchDiaporama();" />
          <br />
        </div> <!-- divDiapoManage -->

Simplifie
<div id="divPictureContent" class="PictureDocument" {$showInSlideShow} onmouseover="javascript:show_div('divPictureTelecommand');" onmouseout="javascript:hide_div('divPictureTelecommand');">

*/

    return <<<ENDPict

        <!-- Picture Documents -->
        <div id="divPictureContent" class="PictureDocument" {$showInSlideShow}>
          <img id="pictureImg" src="javascript:return null;" class="Picture" onclick="javascript:show_picture_fullScreen(this.src, this.alt);" alt="empty" onmouseover="this.style.cursor='hand';"/>

        <div id="divPictureTelecommand" class="divPictureTelecommand">
          <input type="button" id="bPreviousPicture" class="buttonTel" value=" " onclick="javascript:load_picture('pictureImg', 'objElem', 'previous');" onmouseover="this.style.cursor='hand';" />
          <input type="button" id="bRandomPicture" class="buttonTel" value=" " onclick="javascript:load_picture('pictureImg', 'objElem', 'random');" onmouseover="this.style.cursor='hand';" />
          <input type="button" id="bNextPicture" class="buttonTel" value=" " onclick="javascript:load_picture('pictureImg', 'objElem', 'next');" onmouseover="this.style.cursor='hand';" />

          <input type="text" id="txtTimeOut" class="inputTel" size="1" maxlength="2" value="05" />
          <input type="button" id="bDelimentAuto" class="buttonTelAutoStart" value=" " onclick="javascript:defilStartStop(this.id);defilement_photo('pictureImg', 'objElem', 'next', 'txtTimeOut');" onmouseover="this.style.cursor='hand';" />
          <input type="text" id="inputPageNb" class="inputTel" size="8" value="0" readonly="readonly" />

          <!-- Objects source list -->
          {$sourcesList}
	      <!-- <script type="text/javascript">load_picture('pictureImg', 'objElem', 'random');</script> -->
        </div> <!-- divPictureTelecommand -->

      </div> <!-- divPictureContent -->
ENDPict;
  }
}

class MusicDocument extends Document {

  public static $typext = 'mp3,wav,wma';
  public static $classContentObj = "ContentObj";

  public function printObj($increment = 4) {
    parent::printObj($increment);
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);
    $strObj = $strIncrement . "<div class=\"" . get_class($this) . "\">\n";
    $strObj .= $strIncrement . "  <a href=\"javascript:return false;\" onclick=\"javascript:listen('{$this->htmlPath}','{$this->title}','{$this->comments}');\">{$this->title}</a><br />\n";
    if($this->comments != "" && $this->comments != "NULL")
      $strObj .= $strIncrement . "  " . $this->comments . "\n";
    $strObj .= $strIncrement . "</div>\n";

    return $strObj;
  }
}

class TextDocument extends Document {

  public static $typext = 'doc,pdf,ppt,pptx,ppsx,pps,docx,odt,txt';
  public static $classContentObj = "ContentObj";

  public function printObj($increment = 4) {
    parent::printObj($increment);
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);
    return $strIncrement . <<<ENDTxt
	<a href="." onclick="javascript:window.open('{$this->htmlPath}');return false;">{$this->title}</a><br />
ENDTxt;
  }
}

class ScriptDocument extends Document {

  public static $typext = 'ps1,psm1,bat,cs,html,xml,htm,xhtml,sh,pl,pm,php,py,pym,js,vbs,c,cpp,vb';
  public static $classContentObj = "ScriptContentObj";
  
  public function printObj($increment = 4) {
    parent::printObj($increment);
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);

    $contentScript = "comming soon...";

    if (file_exists($this->path))
      $contentScript = htmlspecialchars(file_get_contents($this->path));

    // unique identifier for this object without knowing the number of object in the list
    $nbrRand = rand();

    return <<<ENDScript
        <h3 onclick="javascript:show_hide_div('code{$nbrRand}', 'scriptSign{$nbrRand}');"><span id="scriptSign{$nbrRand}" style="border:1px dotted">+</span>&nbsp;{$this->comments}</h3>
        <code id="code{$nbrRand}" class="script" style="visibility: hidden; display: none;">
          <pre>{$contentScript}</pre>
        </code>
ENDScript;
  }
}

class ToolDocument extends Document {

  public static $typext = 'zip,7z,tar,gz,tar.gz,tgz';
  public static $classContentObj = "ContentObj";

  public function printObj($increment = 4) {
    parent::printObj($increment);
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);
    return $strIncrement . "<a href=\".\" onclick=\"javascript:window.open('{$this->path}'); return false;\">{$this->title}</a>";
  }
}

class UnknownDocument extends Document {

  public static $typext = '';
  public static $classContentObj = "ContentObj";

  public function printObj($increment = 4) {
    parent::printObj($increment);
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);

    return $strIncrement . (string)$this;
  }
}

class DocumentsList extends ArrayObject implements iPresentation {

  var $subject = "";
  var $childrenDocumentsList = array();
  public static $classContentObj = "ContentObj";

  public function __construct($array = array()) {
    if(count($array))
      $this->importArray($tmpArray);
  }

  public function getCssClassObj() {
    return static::$classContentObj;
  }

  public function checkAndCorrectPath() {
    return true;
  }

  public function importArray($tmpArray) {
    foreach($tmpArray as $tmpEntry)
      $this []= $tmpEntry;
  }

  public function printObj($recurse = true, $increment = 4) {
    $strDoc = "";
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);
    $pInfo = pathinfo($this->subject);
    $tName = str_replace("_", " ", ucfirst($pInfo['basename']));
    $strDoc .= $strIncrement . "<div style=\"text-align: left;\">\n";
    $strDoc .= $strIncrement . "<h3>{$tName}</h3>\n";
    $strDoc .= $strIncrement . "  <ul>\n";
    foreach($this as $f) {
      $strDoc .= $strIncrement . "    <li>" . $f->printObj($increment) . "</li>\n";
    }
    $strDoc .= $strIncrement . "  </ul>\n" . $strIncrement . "</div>\n";

    if($recurse)
    foreach($this->childrenDocumentsList as $f)
      if(is_a($f, 'TextDocument') && $f->subject != "") $strDoc .= $f->printObj($increment);

    return $strDoc;
  }

  public function fillContentObjects($increment = 4) {
    $strIncrement = str_pad("", $increment, " ", STR_PAD_LEFT);

    if(!count($this) && count($this->childrenDocumentsList)) {
      $this->importArray($this->childrenDocumentsList);
      $this->childrenDocumentsList = array();
    }

    if(!count($this))
      return "<p>There is no content to print</p>\n";

    $strContent = "";

    /* Picture Management */
    if(is_object($this) && $this->isArrayContaintMultimediaCategory('PictureDocument') && count($this) > 1) {
      $strContent .= PictureDocument::printPictureManagement($this->getObjectsHiddenFields());
    }

    // filling array
    $strContent .= $strIncrement . "<div id=\"contentObjects\">\n";
    foreach($this as $enr) {
      $contentObjClass = $enr->getCssClassObj();
      $strContent .= $strIncrement . "  <div class=\"{$contentObjClass}\">\n";
      $strContent .= $enr->printObj($increment + 4);
      $strContent .= $strIncrement . "  </div> <!-- {$contentObjClass} -->\n";
    }
    $strContent .= $strIncrement . "</div> <!-- contentObjects -->\n";

    return $strContent;
  }

  public function getObjectsHiddenFields($separator = "") {
    $inputObjectsList = "";
    foreach($this as $obj) {
      $inputObjectsList .= $obj->getObjectHiddenField() . $separator;
    }
    return $inputObjectsList;
  }

  public function isArrayContaintMultimediaCategory($searchType) {
    foreach($this as $tmpElem) {
      if(is_a($tmpElem, $searchType))
        return true;
    }
    return false;
  }

  public static function generateListHyperLink($arrayHyperlink) {

    $returnStr = "  <ol>\n";

    foreach($arrayHyperlink as $hlk) {
      $hlkSummary = $hlk(true);
      $returnStr .= Document::getHyperLinkForList($hlk, $hlkSummary);
    }

    $returnStr .= "  </ol>\n";

    return $returnStr;
  }
}

function isClassImplementDocument($tmpClass) {
  return is_subclass_of($tmpClass, 'Document');
}

class FactoryDocument {
  public static $instance = null;
  var $typeDocumentsList = array();

  private function __construct() {
    // get list of class
    $classList = get_declared_classes();
    // filter class with implement 'Document'
    $this->typeDocumentsList = array_filter($classList, "isClassImplementDocument");
  }

  public static function getInstance() {
    if(self::$instance == null) {
            $className = __CLASS__;
            self::$instance = new $className;
    }
    return self::$instance;
  }

  public static function createDocument($enr, $isColumnsNamed = false) {
    $doc = null;
    $tmpPath = "";
    try {
      // if title is present, switch to new column num2, else num1
      if(is_array($enr)) {
        $tmpPath = trim(($isColumnsNamed) ? $enr['path'] : $enr[(count($enr) > 4)?2:1]);
      }
      else
        $tmpPath = $enr;

      $factoryDocument = FactoryDocument::getInstance();

      $docType = $factoryDocument->getMultimediaType($tmpPath);

      $doc = new $docType($tmpPath);
    } catch (Exception $exp) {
      $doc = new UnknowDocument();
    }
    return $doc;
  }

  public function getMultimediaType($filepath) {

    /* php 5.1 */
    /*
    $tmpArr = null;
    $extension = "";

    try {
      $tmpArr = explode(".",$filepath);
      $extension = strtolower(array_pop($tmpArr));
    } catch(Exception $iep) {
      return false;
    }

    if(strpos(PictureDocument::$typext,$extension) !== false) return 'PictureDocument';
    else if(strpos(MusicDocument::$typext,$extension) !== false) return 'MusicDocument';
    else if(strpos(TextDocument::$typext,$extension) !== false) return 'TextDocument';
    else if(strpos(ToolDocument::$typext,$extension) !== false) return 'ToolDocument';
    else if(strpos(ScriptDocument::$typext, $extension) !== false) return 'ScriptDocument';
    else return 'UnknownDocument';
    */
    /* end of php 5.1 */

    /* php 5.3 */
    /* ask to each documents if the file is anything of it */
    foreach($this->typeDocumentsList as $tmpDocType) {
      if($tmpDocType::isDocumentType($filepath)) {
        return $tmpDocType;
      }
    }
    return 'UnknownDocument';
  }
}

class WebPage implements \JsonSerializable {
  public $name        = "";
  public $docTitle    = "";
  public $metaWords   = "";
  public $contentPage = "";
  public $headerDescription = "";

  public function __construct() {
  }

  public function getPageArray() {
    return array("docTitle"          => $this->docTitle,
                 "contentpage"       => $this->contentPage,
                 "metaWords"         => $this->metaWords,
                 "headerDescription" => $this->headerDescription);
  }

  public function __toString() {
    return "Name : {$this->name} / Title : {$this->docTitle} / ContentPage : {$this->contentPage} / MetaWords : {$this->metaWords}";
  }

  public function jsonSerialize() {
    return $this;
  }
}

class WebMasterPage extends WebPage {
  private $pageTitle   = "";
  public  $category    = "";
  public  $styleFile   = "";
  public  $webSiteName = "";
  public  $javascripts = "";
  public  $menu        = null;

  public function __construct($wsName = "", $wpCategory = "", $cpyTempPage = null) {
    if($wsName != "")
      $this->webSiteName = $wsName;
    if($wpCategory != "")
      $this->setWebPage($wpCategory, $cpyTempPage);
  }

  public function __get($attr) {
    if($attr == "pageTitle" && $this->menu != null && (($tmpCategory = $this->menu->getEntryByName($this->category)) !== false))
      return $tmpCategory->titlePage;
    elseif(isset($this->$attr)) return $this->$attr;
    //else throw new Exception("Unknow attribute {$attr_pageTitle}");
    return false;
  }

  public function setWebPage($wpCategory, $newWebPage) {
    $this->category = $wpCategory;
    $this->importWebPage($newWebPage);
  }

  public function importWebPage($cpyWebPage) {
    if ($cpyWebPage != null && is_a($cpyWebPage, 'WebPage')) {
        $this->docTitle    = $cpyWebPage->docTitle;
        $this->metaWords   = $cpyWebPage->metaWords;
        $this->contentPage = $cpyWebPage->contentPage;
		$this->headerDescription = $cpyWebPage->headerDescription;
    }
  }

  public function getMenuHTML($increment = 8) {
    if ($this->menu == null) return "";
    return $this->menu->printCategory($this->category, $increment);
  }
}
?>