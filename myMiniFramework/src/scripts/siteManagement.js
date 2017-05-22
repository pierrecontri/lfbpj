/* ****************
 * Gestion Site   *
 * Pierre Contri  *
 * le  12/2007    *
 * mdf 07/2016    *
 **************** */

/*
   Provide the XMLHttpRequest constructor for Internet Explorer 5.x-6.x:
   Other browsers (including Internet Explorer 7.x-9.x) do not redefine
   XMLHttpRequest if it already exists.
 
   This example is based on findings at:
   http://blogs.msdn.com/xmlteam/archive/2006/10/23/using-the-right-version-of-msxml-in-internet-explorer.aspx
*/
if (typeof XMLHttpRequest === "undefined") {
  XMLHttpRequest = function () {
    try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); }
      catch (e) {}
    try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); }
      catch (e) {}
    try { return new ActiveXObject("Microsoft.XMLHTTP"); }
      catch (e) {}
    //Microsoft.XMLHTTP points to Msxml2.XMLHTTP and is redundant
    throw new Error("This browser does not support XMLHttpRequest.");
  };
}

var parseXml;

if (typeof window.DOMParser !== "undefined") {
  parseXml = function(xmlStr) {
    return ( new window.DOMParser() ).parseFromString(xmlStr, "text/xml");
  };
} else if (typeof window.ActiveXObject !== "undefined" && new window.ActiveXObject("Microsoft.XMLDOM")) {
  parseXml = function(xmlStr) {
    var xmlDoc = new window.ActiveXObject("Microsoft.XMLDOM");
    xmlDoc.async = "false";
    xmlDoc.loadXML(xmlStr);
    return xmlDoc;
  };
} else {
  throw new Error("No XML parser found");
}

var ie  = document.all ? true : false;
var ns  = document.layers ? true : false;

function getJsonCategory(category) {

  var objAbsUrl = document.getElementById('jsonAbsoluteURL');
  if(objAbsUrl == null) return;

  var urlPage = objAbsUrl.value;
  xmlhttp = new XMLHttpRequest();

  xmlhttp.onreadystatechange = function() {

    if (xmlhttp.readyState === 4 && xmlhttp.status === 200){
      // get the json response
      var jsonObject = JSON.parse(xmlhttp.responseText);

      // Replace the content page by the new one
      var objIdPage = document.getElementById("idPage");
      if(objIdPage != null) objIdPage.innerHTML = jsonObject.contentPage;

      var objDocTitle = document.getElementById("docTitle");
      if(objDocTitle != null) objDocTitle.innerHTML = jsonObject.docTitle;

      var divContentHeader = document.getElementById("divContentHeader");
      if(divContentHeader != null) {
        divContentHeader.innerHTML = jsonObject.headerDescription;
      }

      // change the view
      // hide div if it is subMenu
      //$onClickStr = (false) ? "hide_div('{$menuName}Category');" : "";
      // change css property
      selectMenu(category);
    }
    else {
      if (xmlhttp.readyState === 4) {
        //document.getElementById('idPage').innerHTML = '<h3>Error ' + xmlhttp.status + ' on getting page "' + category + '"</h3>';
      }
    }
  };

  xmlhttp.open("GET", urlPage + "/?getFunction=" + encodeURIComponent(category), true);
  xmlhttp.setRequestHeader('Accept', 'application/json, text/javascript');
  xmlhttp.send();
}

function refreshPage() {
  if(patiente !== null)
    patiente();
  var formSite = document.getElementById('formSite');
  if(formSite)
    formSite.submit();
  else
    cachePatienter();
}

// obsolete, please, use goCategory
function goPage(categoryName) {
  if (arguments.length > 1)
    goCategory(categoryName, arguments[1]);
  else
    goCategory(categoryName);
}

function goCategory(categoryName) {
  if(patiente !== null)
    patiente();

  var sitename = "";
  if (arguments.length > 1)
    sitename = arguments[1];

  var formSite = document.getElementById('formSite');
  if(formSite) {
    if (sitename !== "")
      formSite.site.value = sitename;
    formSite.category.value = categoryName;

    formSite.submit();
  }
}

function goSite(sitepath) {
  goCategory('', sitepath);
}

function is_div_visible(divSearch) {
  var obj = document.getElementById(divSearch);
  if(!obj) return false;
  return obj.style.visibility == 'visible';
}

function is_div_hidden(divSearch) {
  var obj = document.getElementById(divSearch);
  if(!obj) return false;
  return obj.style.visibility == 'hidden';
}

function show_div(divSearch) {
  var obj = document.getElementById(divSearch);
  if(!obj) return false;

  obj.style.visibility = 'visible';
  obj.style.display = 'block';

  return true;
}

function hide_div(divSearch) {
  var obj = document.getElementById(divSearch);
  if(!obj) return false;

  obj.style.visibility = 'hidden';
  obj.style.display = 'none';

  return true;
}

function show_hide_div(divSearch, objSignText) {
  var objSign = (objSignText != null)?document.getElementById(objSignText):null;
  if (is_div_visible(divSearch)) {
    hide_div(divSearch);
    if(objSign != null)
      objSign.innerHTML = "+";
  }
  else {
    show_div(divSearch);
    if(objSign != null)
      objSign.innerHTML = "-";
  }
}

function selectMenu(entryName) {
  // unselect all objects selected
  var objSelectet = document.getElementsByClassName("selected");
  if(objSelectet) {
    var i;
    for(i = objSelectet.length - 1; i >= 0; i--) {
      objSelectet[i].className = objSelectet[i].className.replace("selected","unselected");
    }
  }

  var parentControl = document.getElementById(entryName + 'Entry');
  var itemControl = document.getElementById(entryName + 'TextEntry');

  if(parentControl == null || itemControl == null)
    return false;

  // select the new one (li)
  parentControl.className = parentControl.className.replace("unselected","selected");

  // select the new one (hyperlink)
  itemControl.className = itemControl.className.replace("unselected","selected");

  // get the ul parent
  var parentCategoryControl = parentControl.parentNode;
  if(parentCategoryControl.id == 'mainCategory' || parentCategoryControl.id.toString().indexOf('Category') < 2)
    return false;

  // get the main menu (li parent)
  var parentMenuEntry = parentCategoryControl.parentNode;
  parentMenuEntry.className = parentMenuEntry.className.replace("unselected","selected");
}

function postMail() {
  var frmSite = document.getElementById('formSite');
  if(!frmSite) return false;

  var mail_subject = document.getElementById('input_mail_subject');
  var mail_from    = document.getElementById('input_mail_from');
  var mail_to      = document.getElementById('input_mail_to');
  var mail_message = document.getElementById('input_mail_message');

  frmSite.appendChild(mail_subject);
  frmSite.appendChild(mail_from);
  frmSite.appendChild(mail_to);
  frmSite.appendChild(mail_message);

  frmSite.action = "src/controls/sendmail.php";
  frmSite.target = "sendmail";
  frmSite.submit();

  frmSite.action = "index.php";
  frmSite.target = "_self";
  frmSite.submit();

  return true;
}

function getAbsolutePosition(element) {
  var r = {x: element.offsetLeft, y: element.offsetTop};
  if(element.offsetParent) {
    var tmp = getAbsolutePosition(element.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
  }
  return r;
}

function moveIconeByMouse() {
  var icone = document.getElementById('icone');
  if(icone) {
    icone.style.top = event.y;
    icone.style.left = event.x;
  }
}

function getLeft(l) {
  if (l.offsetParent) return (l.offsetLeft + getLeft(l.offsetParent));
  else return (l.offsetLeft);
}

function getTop(l) {
  if (l.offsetParent) return (l.offsetTop + getTop(l.offsetParent));
  else return (l.offsetTop);
}
