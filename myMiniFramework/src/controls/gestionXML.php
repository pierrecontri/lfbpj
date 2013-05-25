<?php

//////////////////////////////////////////////
// Gestion XML

$depth = array();
$tag_tree = array();
$stack = array();

class tag {
   var $name;
   var $attrs;
   var $children;
   var $data;

   function tag($name, $attrs, $children, $data = null) {
       $this->name = $name;
       $this->attrs = $attrs;
       $this->children = $children;
       $this->data = $data;
   }

   function printTag($indent = "") {
      print("{$indent}&lt;{$this->name}&gt;");
      if($this->data != null) {
        print($this->data);
        print("&lt;/{$this->name}&gt;<br />\n");
      }
      else {
        print("<br />\n");
        foreach($this->children as $ttyTag) {
          $ttyTag->printTag($indent . "&nbsp;&nbsp;");
        }
        print("{$indent}&lt;/{$this->name}&gt;<br />\n");
      }
   }
}


function debutElement($parser, $name, $attrs){
    global $depth;
    global $stack;

    if(!isset($depth[$parser])) $depth[$parser] = 0;
    $depth[$parser]++;

    $tag = new tag($name,$attrs,'');
    array_push($stack,$tag);
}

function finElement($parser, $name){
    global $depth;
    global $stack;

    $depth[$parser]--;

    $stack[count($stack)-2]->children[] = $stack[count($stack)-1];
    array_pop($stack);
}

function tagData($parser, $value){
  global $stack;
  $txt = utf8_decode($value);
  $stack[count($stack) - 1]->data = trim($txt);
}

function toXML() {
  global $stack, $depth;
  $file = "./audmp3/morceaux.xml";

  $xml_parser = xml_parser_create();
  xml_set_element_handler($xml_parser, "debutElement", "finElement");
  xml_set_character_data_handler($xml_parser, "tagData");

  if (!($fp = fopen($file, "r"))) {
      die("Impossible d'ouvrir le fichier XML");
  }

  while ($data = fread($fp, 4096)) {
      if (!xml_parse($xml_parser, $data, feof($fp))) {
          die(sprintf("erreur XML : %s à la ligne %d",
                      xml_error_string(xml_get_error_code($xml_parser)),
                      xml_get_current_line_number($xml_parser)));
      }
  }
  fclose($fp);
  xml_parser_free($xml_parser);

$racineTag = $stack[0];
$racineTag->printTag();
}
?>
