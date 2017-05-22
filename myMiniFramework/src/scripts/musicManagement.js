
function listen(fileSoundName,soundName,soundComment) {
  paramSafari = "width=300, height=130, toolbar=0, menubar=0, scrollbars=3, resizable=1, status=0, location=0";
  paramNetscape = "width=400, height=180, toolbar=0, menubar=0, scrollbars=3, resizable=1, status=0, location=0";

  if(navigator.appVersion.indexOf("Safari")>=0)
  {
    docMorceau = window.open(fileSoundName, "morceau", paramSafari);
    docMorceau.focus();
  }
  else
  {
    docMorceau = window.open("about:blank", "morceau", paramNetscape);
    docMorceau.document.writeln("<center><a href=\"javascript:window.close();\">Fermer la fen&ecirc;tre</a></center><br/>");
    docMorceau.document.write("<table width=\"100%\"><tr><td width=\"100%\"><font color=\"black\"><u>" + soundName + "</u></font></a><br>\n");
    docMorceau.document.write("</td></tr>\n");
    if(soundComment!=null && soundComment!="NULL")
    {
      docMorceau.document.write("<tr><td width=\"100%\"><font color=\"black\">" + soundComment + "</font></td></tr>\n");
    }
    docMorceau.document.write("<tr><td width=\"100%\"><embed name=\"PageMorceau\" src=\"" + fileSoundName + "\" width=\"100%\" height=\"30\" LOOP=FALSE AUTOSTART=TRUE CONTROLS=TRUE MASTERSOUND /></td></tr>");
    docMorceau.document.write("<tr><td><a href=\"" + fileSoundName + "\">T&eacute;l&eacute;charger le morceau</a></td></tr>\n");
    docMorceau.document.write("</table>\n");
    docMorceau.focus();
  }
}
