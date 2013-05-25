/******************
 * Gestion Site   *
 * Pierre Contri  *
 * le  12/2007    *
 * mdf 04/2012    *
 ******************/

/*
   Provide the XMLHttpRequest constructor for Internet Explorer 5.x-6.x:
   Other browsers (including Internet Explorer 7.x-9.x) do not redefine
   XMLHttpRequest if it already exists.
 
   This example is based on findings at:
   http://blogs.msdn.com/xmlteam/archive/2006/10/23/using-the-right-version-of-msxml-in-internet-explorer.aspx
*/
if (typeof XMLHttpRequest == "undefined")
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

var parseXml;

if (typeof window.DOMParser != "undefined") {
    parseXml = function(xmlStr) {
        return ( new window.DOMParser() ).parseFromString(xmlStr, "text/xml");
    };
} else if (typeof window.ActiveXObject != "undefined" &&
       new window.ActiveXObject("Microsoft.XMLDOM")) {
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

function refreshPage() {
  if(patiente != null)
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
  if(patiente != null)
    patiente();

  var sitename = "";
  if (arguments.length > 1)
    sitename = arguments[1];

  var formSite = document.getElementById('formSite');
  if(formSite) {
    if (sitename != "")
      formSite.site.value = sitename;
    formSite.category.value = categoryName;
    formSite.submit();
  }
}

function goSite(sitepath) {
  goCategory('', sitepath);
}

function getCategoryWebS(categoryName) {
  var rqt = new XMLHttpRequest();
  if(!rqt) return false;

  // asynchronous answer
  rqt.onreadystatechange = function () {
    if(rqt.readyState == 4 && rqt.status == 200)
      document.getElementById('idPage').innerHTML = rqt.responseText;
    else
      document.getElementById('idPage').innerHTML = rqt.readyState;
    cachePatienter();
  }

  // GET request
  //rqt.open('GET', 'http://localhost/sitmus/?getFunction=' + categoryName, true);
  //rqt.send(null);

  // POST request
  rqt.open('POST', 'http://localhost/sitmus', true);
  rqt.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  rqt.send("getFunction=" + categoryName);

  // synchronous answer
  //document.getElementById('idPage').innerHTML = rqt.responseText;
}

function show_div(divParent) {
  var obj = document.getElementById(divParent);
  if(!obj) return false;

  obj.style.visibility = 'visible';
  obj.style.display = 'block';

  return true;
}

function hide_div(divParent) {
  var obj = document.getElementById(divParent);
  if(!obj) return false;

  obj.style.visibility = 'hidden';
  obj.style.display = 'none';

  return true;
}

function postMail() {
  var frmSite = (document.getElementById('formSite'))?document.getElementById('formSite'):document.formSite;
  if(!frmSite) return false;

  frmSite.action = "src/controls/sendmail.php";
  frmSite.target = "sendmail";
  frmSite.submit();
  frmSite.action = "index.php";
  frmSite.target = "_self";
  goPage("sitmus");

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
