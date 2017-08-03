var TAILLE_HSCROLL = 40;
var TAILLE_VSCROLL = 40;
var MARGE_WITHOUT_SCROLL = 40;
var TAILLE_SYSEXPLOIT = 100;

var isIE = (document.all)?1:0;

if(!Array.prototype.indexOf) {

  Array.prototype.indexOf = function(elmt /*, idx*/) {
    var len = this.length;
    var idx = Number(arguments[1]) || 0;
    idx = (idx < 0)?Math.ceil(idx):Math.floor(idx);
    if(idx < 0) idx += len;

    for(; idx < len; idx++) {
      if(idx in this && this[idx] === elmt)
        return idx;
    }
    return -1;
  };
}

function getSizeScreen() {
  return {x: ((isIE)?document.body.offsetWidth:document.body.clientWidth),
          y: ((isIE)?document.body.offsetHeight:document.body.clientHeight)};
}

function getSizeScreenDoc(docVar) {
  return {x: ((isIE)?docVar.offsetWidth:docVar.clientWidth),
          y: ((isIE)?docVar.offsetHeight:docVar.clientHeight)};
}

function getSizeInnerScreen() {
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) === "number" ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
  return {x: myWidth, y: myHeight};
}

function getScrollXY() {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) === "number" ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return {x: scrOfX, y: scrOfY};
}

function switchDiaporama() {
  var arrayObj = document.getElementById('contentObjects');
  var divPict = document.getElementById('divPictureContent');
  var imgPict = document.getElementById('pictureImg');
  var isDiapo = document.getElementById('isDiaporama');
  var buttonDiapo = document.getElementById('isDiapo');

  if(!(arrayObj && divPict && imgPict)) {
    alert("No picture management control present !");
    return false;
  }

  if(!(isDiapo && buttonDiapo)) {
    // change value in dynamic
    isDiapo.value = (isDiapo.value == 1)?0:1;

    // calcul for viewing
    var valDiapo = (isDiapo.value == 1);
    buttonDiapo.value = "Switch to the pictures " + ((valDiapo)?"table":"slideshow");
  }


  // check the picture containt
  if(imgPict.alt === "empty") {
    load_picture('pictureImg', 'objElem', 'first');
  }
  divPict.style.visibility = (valDiapo)?'visible':'hidden';
  divPict.style.display = (valDiapo)?'block':'none';

  arrayObj.style.visibility = (valDiapo)?'hidden':'visible';
  arrayObj.style.display = (valDiapo)?'none':'block';

  return true;
}

function affiche_photo(nomPhoto) {
  print_picture(nomPhoto);
}

function print_picture(nomPhoto) {

  var img = new Image();
  img.onload = function() {
    param = "top=0px, left=0px, width=" + screen.width + "px, height=" + screen.height + "px, toolbar=0, menubar=0, scrollbars=3, resizable=0, status=0, location=0";
    docPhoto = document.open("about:blank", "photo", param);

    var ratio = 1.0;
    var sizeScreen = getSizeScreen(docPhoto);
    var ratioX = (sizeScreen.x - TAILLE_VSCROLL)  / this.width;
    var ratioY = sizeScreen.y / this.height;

    ratio = (ratioX < ratioY)?ratioX:ratioY;
    if(ratio > 1.0) ratio = 1.0;
    var pictureSizeX = Math.floor(ratio * this.width);
    var pictureSizeY = Math.floor(ratio * this.height);

    docPhoto.document.write('<html><head><title>Picture Viewer</title></head><body style="background-color: black;">');
    docPhoto.document.write('<div style="margin: auto; text-align: center; width: 100%; height: 100%;"><a href="javascript:window.close()">');
    docPhoto.document.write('<img style="border: 2px white solid; width: ' + pictureSizeX + 'px; height: ' + pictureSizeY + 'px;" src="' + nomPhoto + '" />');
    docPhoto.document.write('<br /></a><br/>\n');

    docPhoto.document.write('</div></body></html>');
    docPhoto.focus();
  };
  img.src = nomPhoto;
  img.alt = nomPhoto;
}

function load_picture(nameImgContenuPhoto, nameInputListePhotos, sens) {
  var imgContenuPhoto  = document.getElementById(nameImgContenuPhoto);
  var listPhotos = document.getElementsByName(nameInputListePhotos);
  var nbPictures = listPhotos.length;

  if(imgContenuPhoto == null || listPhotos == null)
    return false;

  affichePatienter();
  
  // verifier qu'il y a des photos dans la liste
  if(nbPictures === 0) {
    alert("Pictures not found on the server");
    return false;
  }

  // recuperer la photo actuelle
  var srcPicture = imgContenuPhoto.src;
  // recuperer le nom de la photo uniquement si elle existe
  var pictureName = srcPicture.split("/").pop();
  if(pictureName.toLowerCase().indexOf(".jpg") == -1) {
    pictureName = "";
    sens = "";
  }

  var pictureObj = null;

  switch(sens) {
    case 'next' :
      pictureObj = getNextPicture(listPhotos, pictureName);
      break;
    case 'previous' :
      pictureObj = getPreviousPicture(listPhotos, pictureName);
      break;
    case 'random' :
      pictureObj = getRandomPicture(listPhotos);
      break;
    case 'first' :
      pictureObj = getNextPicture(listPhotos, null);
      break;
    case 'last' :
      pictureObj = getPreviousPicture(listPhotos, null);
      break;
    default :
      pictureObj = getNextPicture(listPhotos, null);
      break;
  };

  // recuperer son positionnement dans le tableau
  var pictureInfo = pictureObj.value.split(';');
  imgContenuPhoto.onload = function() {
    cachePatienter();
  };
  imgContenuPhoto.src = pictureInfo[0];
  imgContenuPhoto.alt = (pictureInfo.length > 1) ? pictureInfo[1] : pictureInfo[0];

  updatePictureNumber(listPhotos, pictureInfo[0]);
  return true;
}

function show_picture_fullScreen(pictureName, pictureAlt) {
  var divPicture     = "pictureImg";
  var divContentPict = "divPictureContent";
  var divTelecommand = "divPictureTelecommand";

  affichePatienter();
  
  var divPict = document.getElementById(divPicture);
  var divCont = document.getElementById(divContentPict);
  var divTel  = document.getElementById(divTelecommand);

  // make div and imgage if it does not exists
  if(divCont == null) {
    divCont = document.createElement('div');
    divCont.id = divContentPict;
  }
  
  if(divPict == null) {
    divPict = new Image();
	divPict.id = "pictureImg";
	divCont.appendChild(divPict);
  }

  if(isIE)
    divPict.onclick = function () { hide_div2(divContentPict); /* bug chrome */ cachePatienter(); };
  else
    divPict.addEventListener('click', function (e) { hide_div2(divContentPict); /* bug chrome */ cachePatienter(); }, false);

  // recuperation des dimensions du cadre
  var sizeScreen = getSizeInnerScreen();
  // remove scrollBar
  sizeScreen.x -= 32;
  // remove pb firefox
  sizeScreen.y -= 34;
  
  divCont.className = "fullScreenPicture";
  document.body.appendChild(divCont);

  show_div(divContentPict);
  divPict.onload = function() {
    divPict.style.height = "auto";
    divPict.style.width = "auto";
    cachePatienter();
  };
  divPict.src = pictureName;
  divPict.alt = pictureAlt;
  
  // recalcul the page number of telecommand
  if (divTel) {
    updatePictureNumber(document.getElementsByName('objElem'), pictureName);
  }
}

function hide_div2(divParent) {
  var idPage = document.getElementById('idPage');
  var objParent = document.getElementById(divParent);
  var divPict = document.getElementById('pictureImg');
  
  if(!idPage || !objParent || !divPict) return false;

  if(isIE)
    divPict.onclick = function () { show_picture_fullScreen(divPict.src, divPict.alt); };
  else
    divPict.addEventListener('click', function (e) { show_picture_fullScreen(divPict.src, divPict.alt); }, false);

  objParent.className = "Picture";
  hide_div(divParent);

  // if is diaporama don't hide it, just move it
  var isDiapo = document.getElementById('isDiaporama');
  if(isDiapo) {
    idPage.appendChild(objParent);
    if(isDiapo.value == 1) show_div(divParent);
  }

  return true;
}

var defilementPhoto = false;
function defilStartStop(nameButtonDefil) {
  defilementPhoto = !defilementPhoto;
  var btnStartStop = document.getElementById(nameButtonDefil);
  if(btnStartStop != null)
    btnStartStop.className = (defilementPhoto)?"buttonTelAutoStop":"buttonTelAutoStart";
}

function defilement_photo(nameImgContenuPhoto, nameInputListePhotos, sens, nameInputTempo) {
  if(!defilementPhoto) return false;
  var inputTempo = document.getElementById(nameInputTempo);
  if(inputTempo != null) {
    var tempo = inputTempo.value * 1000;
    load_picture(nameImgContenuPhoto, nameInputListePhotos, sens);
    setTimeout("defilement_photo('" + nameImgContenuPhoto + "', '" + nameInputListePhotos + "', '" + sens + "', '" + nameInputTempo + "')", tempo);
  }
  return true;
}

function updatePictureNumber(listByName, pictureName) {
  var pictureIdx = getObjectIndexInList(listByName, pictureName);
  if(pictureIdx == -1) return false;
  
  // afficher le repere de la photo photo xx / nbTotal
  var inputPageNb = document.getElementById('inputPageNb');
  if(inputPageNb)
    inputPageNb.value = (pictureIdx + 1) + " / " + listByName.length;

  return true;
}

function getObjectIndexInList(listByName, searchName) {
  // get only the file name, without folders
  searchName = searchName.split('/').pop().toLowerCase();
  var idx = 0;
  // search
  while((idx < listByName.length) && (listByName[idx].value.toLowerCase().indexOf(searchName) == -1))
    idx++;
  // if not found, return -1
  return (idx >= listByName.length) ? -1 : idx;
}

function getNextPicture(pictureList, pictureName) {
  var idx = 0;
  // search the picture
  while((idx < pictureList.length) && (pictureList[idx++].value.indexOf(pictureName) == -1));
  // not found
  if(idx >= pictureList.length) idx = 0;
  return pictureList[idx];
}

function getPreviousPicture(pictureList, pictureName) {
  var idx = pictureList.length - 1;
  // search the picture
  while((idx >= 0) && (pictureList[idx--].value.indexOf(pictureName) == -1));
  // not found
  if(idx < 0) idx = pictureList.length - 1;
  return pictureList[idx];
}

function getRandomPicture(pictureList) {
  return pictureList[Math.round(Math.random() * (pictureList.length - 1))];
}
