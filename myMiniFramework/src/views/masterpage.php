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

// body
$webMasterPage = $_SESSION["webMasterPage"];

// doctype includeed in index.php due to problem with IE

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <title>Pierre Contri / <?php print($webMasterPage->pageTitle); ?></title>
    <meta name="verify-v1" content="TdbAVqqwU3CMfo3TZke0itZ8djlyuhTbe8jEmtdxCns=" />
    <!-- <meta charset="UTF-8" /> -->
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Language" content="fr" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="author" content="Pierre Contri" />
    <meta name="description" content="Site web de composition musicales et publications informatiques" />
    <meta name="keywords" content="<?php print($webMasterPage->metaWords); ?>" />

    <!--[if lte IE 8]>
    <style type="text/css" media="screen" title="Style par defaut">@import url(./src/views/stylesheets_old_ie/<?php print($webMasterPage->styleFile); ?>);</style>
    <![endif]-->

    <!--[if gte IE 9]>
    <style type="text/css" media="screen" title="Style par defaut">@import url(./src/views/stylesheets/<?php print($webMasterPage->styleFile); ?>);</style>
    <![endif]-->

    <!--[if !IE]><!-->
    <style type="text/css" media="screen">
        @import url(./src/views/stylesheets/<?php print($webMasterPage->styleFile); ?>) screen;
    </style>
    <!-- <![endif]-->
    <style type="text/css" media="print">
        @import url(./src/views/stylesheets/print.css);
    </style>
<?php print($webMasterPage->javascripts); ?>
<?php
/*print($jsMenu);*/
/*
<script type="text/javascript">
function initPage() {
//add this in body tag : onload="javascript:initPage();"

  //var xmlMenu = parseXml(jsMenu);
  //var menuContent = printMenu(xmlMenu.documentElement.childNodes, false);
  //document.getElementById('divMenu').innerHTML = menuContent;
}
</script>
*/
?>
  </head>

  <body id="corps">
    <div id="divCorps">

      <!-- title of the page -->
      <div id="divTitlePage" class="titlePage"><?php print($webMasterPage->docTitle); ?></div>

      <!-- Tabs and menus -->
      <div id="divMenu" class="nav">
<?php print($webMasterPage->getMenuHTML()); ?>
      </div><!-- divMenu --><!--

   --><!-- Page content --><!--
   --><div class="page" id="idPage">
<?php print($webMasterPage->contentPage); ?>
      </div> <!-- divPage -->

    </div> <!-- divCorps -->

      <!-- Validation W3C.org -->
      <!-- <div id="divValid" class="w3valid">
        <a href="http://validator.w3.org/check?uri=referer" class="aValidPage">
          <img class="validPage"
               src="./src/images/valid-xhtml10-blue.png"
               alt="Valid XHTML 1.0 Strict" style="width: 60px;"/></a>&nbsp;
        <a href="http://jigsaw.w3.org/css-validator/check/referer" class="aValidPage">
          <img class="validPage"
               src="./src/images/vcss-blue.gif"
               alt="Valid CSS!" style="width: 60px;" />
        </a>
      </div> -->  <!-- divValid -->

      <!-- Sheets & tabs management -->
    <form id="formSite" action="." method="post">
      <div id="divManageForm">
        <input type="hidden" id="category" name="category" value="<?php print($webMasterPage->category); ?>" />
        <input type="hidden" id="site" name="site" value="<?php print($webMasterPage->webSiteName); ?>" />
      </div> <!-- divManageForm -->
    </form>
  </body>
</html>
