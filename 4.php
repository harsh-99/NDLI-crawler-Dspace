<?php
function hello(){
  $GLOBALS["i"] +=10;
  print_r($GLOBALS["i"]);
}

$i =0;
hello();
?>