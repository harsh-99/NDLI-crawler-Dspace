<?php
$url = 'http://210.212.228.207/community-list';
$curl_options = array (
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTPHEADER => array (
                "User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36" 
        ),
        CURLOPT_PROXY => "http://172.16.2.30:8080"
);
$curl = curl_init ( $url );
curl_setopt_array ( $curl, $curl_options );
$response = curl_exec ( $curl );
curl_close ( $curl );
// echo $response;
libxml_use_internal_errors ( true );
$doc = new DOMDocument ();
$doc->loadHTML ( $response, LIBXML_BIGLINES | LIBXML_NOWARNING | LIBXML_ERR_NONE );
libxml_clear_errors ();
$xpath = new DOMXPath ( $doc );
$structure = array();

function traverser($items1,$xpath,$c,$flag){
    if (!is_null($items1)) {
      foreach ($items1 as $item1) {
        $items2 = $xpath->query ( "./li", $item1 );
        if (!is_null($items2)) {
            foreach ($items2 as $item2) {
              $items3 = $xpath->query("./div/div/a", $item2);
              // $items3 = $xpath->query("./div/h4/a", $item2);
              if (!is_null($items3)) {
              foreach ($items3 as $item3) {
                $items4 = $xpath->query("../../../ul", $item3);
                // $items4 = $xpath->query("../../ul", $item3);
                $nodes3 = $item3->childNodes;
                $obj = new stdClass ();
                $obj->flag = $flag;
                echo"\nThe flag value is ". $flag. "\n";
                $c+=1;                
                print_r($c);
                $s =0;
                print_r ($item3->getAttribute("href"));
                $obj->href = $item3->getAttribute("href");
                foreach ($nodes3 as $node3) {
                $s += 1;
                // print_r($i);
                // echo $node3->nodeValue. "\n";
                if ($s==2){ 
                  $a = $node3->nodeValue;
                  echo $node3->nodeValue. "\n";
                  // echo "Helloin";
                  $obj->name= $a; 
                  array_push($GLOBALS["structure"],$obj);
                }
                
                
                  // array_push($structure,$node3->nodeValue);
                }
              
                traverser($items4,$xpath,0,$flag+1);
              }
            }

        $nodes2 = $item2->childNodes;
            }
        }
    }    
}
  else {
    echo"\nreturned";
	  return;
  }
}
// function hello(){
//   $GLOBALS["i"] +=1;
//   print_r($GLOBALS["i"]);
// }
$i =0;

function getarray($structure){
  $list = array();
  while ($GLOBALS["i"] < $GLOBALS["t"]){ 
    $lag = new stdClass ();
    if (($structure[$GLOBALS["i"]]->flag) == ($structure[$GLOBALS["i"]+1]->flag)){
      $lag->child = array();
      $lag->name = $structure[$GLOBALS["i"]]->name;
      $lag->href = $structure[$GLOBALS["i"]]->href;
      // echo "\n";
      // print_r($structure[$GLOBALS["i"]]->name);
      array_push($list, $lag);
      // print_r($list);
      // echo "Pushed";
      // print_r($GLOBALS["i"]);
      $GLOBALS["i"]+=1;
    }
   else if((($structure[$GLOBALS["i"]]->flag)+1) == ($structure[$GLOBALS["i"]+1]->flag) ){
    $lag->name = $structure[$GLOBALS["i"]]->name;
    $lag->href = $structure[$GLOBALS["i"]]->href;
    // print_r($structure[$GLOBALS["i"]]->name);
    // echo "\n";
    $GLOBALS["i"] += 1;
    $lag->child = getarray($structure);
    array_push($list, $lag);
    // // print_r($list);
    // echo "Pushed";
    // print_r($GLOBALS["i"]);
    // echo "\n\n";
    // // exit();
   }
   else if (($structure[$GLOBALS["i"]]->flag) == (($structure[$GLOBALS["i"]+1]->flag)+1)){
    $lag->name = $structure[$GLOBALS["i"]]->name;
    $lag->href = $structure[$GLOBALS["i"]]->href;
    // echo "\n";
    // print_r($structure[$GLOBALS["i"]]->name);
    $lag->child = array();
    array_push($list, $lag);
    $GLOBALS["i"] += 1;
    // // print_r($list);
    // echo "Pushed";
    // print_r($GLOBALS["i"]);
    // echo "\n";
    return $list;
   } 
  }
  return $list;
  exit();
}
// i should be updated, it should not get affected by recursion  
$c =0;
// $items1 = $xpath->query ( "//div[@class='container']/ul[@class='media-list']" );
$items1 = $xpath->query ( "//div[@id='ds-body']/div/ul" );
traverser($items1,$xpath,0,0);
print_r($structure);
$t = count($structure);
// echo("The value of count is iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii");
// print_r($t);
$new = array();
$new = getarray($structure);
// // echo "Hell\n";
print_r($new);
// echo "hjvhvjvhjvhjhvj \n";
?>
