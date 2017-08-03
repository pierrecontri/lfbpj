<?php
// Pierre Contri
// cree le 31/10/2015
// mdf  le 31/10/2015
// generate sitemap for a site

class SitemapManagement {
  /**
   * Function : generateXmlSitemap
   *
   * Inputs : - menu         : menu from webMasterPage (Object)
   *          - webSiteName  : name of the webSite
   *          - absUrl       : name of absolute URL of the site
   *          - absolutePath : absolute path in server
   * Output : - Text result of Xml creating
   *          - Xml Document write on disk
   * Author : Pierre Contri
   */
  public static function generateXmlSitemap($menu, $webSiteName, $absUrl, $absolutepath) {
  
    $strContentSitemap = $menu->getXmlSitemap($webSiteName);
  
    $strSitemap = <<<EndSitemap
<?xml version="1.0" encoding="UTF-8"?>
<urlset
   xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
  <site base_url="{$absUrl}" default_encoding="UTF-8" />
{$strContentSitemap}</urlset>
EndSitemap;

    // write on the webfile server
    $fileCreated = file_put_contents("{$absolutepath}/sitemap_{$webSiteName}.xml", $strSitemap);
  
    return ($fileCreated === false)?"Error on sitemap file creating<br />":"Created on sitemap_{$webSiteName}.xml<br />";
  }
  
  /**
   * Function : generateGlobalXmlSitemap
   *   
   * Inputs : - listSites    : list of site names
   *          - absUrl       : name of absolute URL of the site
   *          - absolutePath : absolute path in server
   * Output : - Xml document in string
   * Author : Pierre Contri
   */
  public static function generateGlobalXmlSitemap($listSites, $absUrl, $absolutepath) {
    $actualDate = (new DateTime())->format(DateTime::W3C);

    $strGlobalSitemap  = <<<EndHeaderSitemap
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

EndHeaderSitemap;
  
    foreach($listSites as $siteName) {
      // create the index file
      $strGlobalSitemap .= <<<EndGlobalSitemap
  <sitemap>
    <loc>{$absUrl}/sitemap_{$siteName}.xml</loc>
    <lastmod>{$actualDate}</lastmod>
  </sitemap>

EndGlobalSitemap;


      // generate sitemap for this site automatically if the curl module is enable
      if(function_exists("curl_init") && ($ch = curl_init()) !== false) {
        print("Generating sitemap for {$siteName}    with url: {$absUrl}/?site={$siteName}&generateXmlSitemap={$siteName}<br />");
        curl_setopt($ch, CURLOPT_URL, "{$absUrl}/?site={$siteName}&generateXmlSitemap={$siteName}");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
      }
      else {
        print("<a href=\"{$absUrl}/?site={$siteName}&generateXmlSitemap={$siteName}\" target=\"generate{$siteName}\">Click here to generate the sitemap for {$siteName}</a><br />");
      }
    }

    $strGlobalSitemap .= '</sitemapindex>';

    // write the global sitemap file
    $fileCreated = file_put_contents("{$absolutepath}/sitemap.xml", $strGlobalSitemap);

    return ($fileCreated === false)?"Error on sitemap files creating<br />":"Created sitemap.xml<br />";
  }
  
  /**
   * Function : checkXmlSitemap
   *     Check if the sitemap (with name in parameter)
   *     is realy stored and if this file is not expired
   * Inputs : - sitename     : name of the site in String
   *          - absUrl       : name of absolute URL of the site
   *          - absolutePath : absolute path in server
   * Output : - boolean (true if not exists or need to regenerate)
   * Author : Pierre Contri
   */
  public static function checkXmlSitemap($siteName = "", $absUrl, $absolutepath) {
    $actualDate = date("Y-m-d");

    // the file name depends of type sitemap (general / sitemap for a site)
    $filename = (strlen($siteName) > 0) ? 
                   "{$absolutepath}/sitemap_{$siteName}.xml" :
                   "{$absolutepath}/sitemap.xml";

    if (file_exists($filename)) {
      $modificationDateFile = filemtime($filename);
      // return false if the file has been modified more than 1 month
      return ($modificationDateFile > strtotime("-1 month"));
    }
  
    return false;
  }

  /**
   * Function : processSitemap
   *     Call the Check sitemap
   *     Call the generate sitemap if needed
   * Inputs : - siteName     : name of the site
   *          - menu         : menu for the site
   *          - absUrl       : name of absolute URL of the site
   *          - absolutePath : absolute path in server
   * Output : - string       : status after generated
   * Author : Pierre Contri
   */
  public static function processSitemap($siteName, $menu, $absUrl, $absolutepath) {
    $strResult = "";

    if(! SitemapManagement::checkXmlSitemap($siteName, $absUrl, $absolutepath)) {
      $strResult = SitemapManagement::generateXmlSitemap($menu, $siteName, $absUrl, $absolutepath);
    }
    else {
      $strResult = "The Xml Sitemap is always generated for the site \"{$siteName}\"<br />";
    }

    return $strResult;
  }
}
?>