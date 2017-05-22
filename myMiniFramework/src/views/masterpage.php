<?php
// master page for MVC viewing

// using variables for this masterpage

/*
class WebPage {
  var $docTitle    = "";
  var $metaWords   = "";
  var $contentPage = "";
}

class WebMasterPage extends WebPage {
  private $pageTitle   = ""; //(getter)
  public $category     = "";
  public $styleFile    = "";
  public $webSiteName  = "";
  public $javascripts  = "";
  public $menu         = null;
}
*/

// head
//$jsMenu = $_SESSION["jsMenu"];

global $absUrl;

// body
$webMasterPage = $_SESSION["webMasterPage"];

// redirect URI
$redirectURI = (isset($_SERVER['REQUEST_URI']))?$_SERVER['REQUEST_URI']:"index.php";

if(strpos($redirectURI, "?") > 0) {
  $redirectURI_array = explode("?",$redirectURI);
  if(count($redirectURI) > 0) {
    $redirectURI = $redirectURI_array[0];
  }
}

// header page calculation
// print the header paragraph for presentation
$contentHeader = "";
if ($webMasterPage->headerDescription != "") {
  // <!-- page header -->
  $contentHeader = <<<ENDCH
      <div id="divContentHeader" class="jumbotron">
        {$webMasterPage->headerDescription}
      </div>
ENDCH;
}

// title page calculation
$titlePageHTML = "";
$titleContaintPage = "";
//print title page only if exists
if($webMasterPage->docTitle != "") {
  $ucTitle = ucfirst($webMasterPage->webSiteName);
  // <!-- title of the page -->
  $titlePageHTML = <<<EndTitlePage
    <nav class="navbar">
      <div id="divTitlePage" class="container">
        <div class="navbar-header">
          <a class="navbar-brand" onclick="javascript:goCategory('sitechoosing');" href="javascript:;">
            <h1>{$ucTitle}<small id="docTitle" style="margin-left:50px; display: inline-block;">{$webMasterPage->docTitle}</small></h1>
          </a>
        </div>
      </div><!-- /.container  &nbsp;WebSite -->
    </nav>
EndTitlePage;
  $titleContaintPage = <<<EndTitleContaintPage
    <nav class="navbar">
      <div id="divTitlePage" class="container">
        <div class="navbar-header">
          <h1 id="docTitle">{$webMasterPage->docTitle}</h1>
        </div>
      </div>
    </nav>
EndTitleContaintPage;
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print($_SESSION["lang"]); ?>" lang="<?php print($_SESSION["lang"]); ?>">
  <head>
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <title>Pierre Contri <?php print($webMasterPage->pageTitle); ?></title>
    <meta name="verify-v1" content="TdbAVqqwU3CMfo3TZke0itZ8djlyuhTbe8jEmtdxCns=" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="author" content="Pierre Contri" />
    <meta name="description" content="Site web de composition musicales et publications informatiques" />
    <meta name="keywords" content="<?php print($webMasterPage->metaWords); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="./src/views/stylesheets/<?php print($webMasterPage->styleFile); ?>_small.css" type="text/css" media="screen and (max-width: 480px)" />
    <link rel="stylesheet" href="./src/views/stylesheets/<?php print($webMasterPage->styleFile); ?>_medium.css" type="text/css" media="screen and (min-width: 480px) and (max-width: 1024px)" />
    <link rel="stylesheet" href="./src/views/stylesheets/<?php print($webMasterPage->styleFile); ?>_large.css" type="text/css" media="screen and (min-width: 1024px)" />
    <link rel="stylesheet" href="./src/views/stylesheets/print.css" type="text/css" media="print" />

<?php print($webMasterPage->javascripts); ?>
  </head>

  <body id="corps">
    <noscript>Your browser does not support JavaScript!</noscript>
<?php /* print($titlePageHTML);*/ ?>
    <!-- mini menu -->
    <div id="divMiniMenu" class="miniMenu" onclick="javascript:document.isMiniDivMenu = true; show_hide_div('divMenu', null);">
      <span class="glyphicon glyphicon-menu-hamburger box-shadow-menu"></span>
    </div>
    <div id="containtDivMenu" onclick="javascript:if(document.isMiniDivMenu){hide_div('containtDivMenu'); document.isMiniDivMenu = false;}">
    <!-- menu -->
    <div id="divMenu" class="nav" onclick="javascript:if(document.isMiniDivMenu === true){hide_div('divMenu'); document.isMiniDivMenu = false;}">
<?php print($webMasterPage->getMenuHTML()); ?>
    </div><!-- end divMenu -->
    </div><!-- end containtDivMenu -->
	<!-- end menu -->

    <div id="divCorps" class="container">
<?php print($titleContaintPage); ?>
<?php print($contentHeader); ?>	
      <!-- Page content -->
      <div class="page" id="idPage">
<?php print($webMasterPage->contentPage); ?>

      </div> <!-- end div page -->
    </div> <!-- end div corps -->

    <!-- Sheets & tabs management -->
    <form id="formSite" action="<?php print($redirectURI); ?>" method="post">
      <!-- divManageForm -->
      <div id="divManageForm">
        <input type="hidden" id="category" name="category" value="<?php print($webMasterPage->category); ?>" />
        <input type="hidden" id="site" name="site" value="<?php print($webMasterPage->webSiteName); ?>" />
        <input type="submit" id="submitbutton" name="submitbutton" value="Refresh" />
      </div>
      <!-- end divManageForm -->
    </form>
    <input type="hidden" id="jsonAbsoluteURL" name="jsonAbsoluteURL" value="<?php print($absUrl); ?>"/>
  </body>
</html>