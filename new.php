<?php
$family_subject= array();
$subject = array();
$url = "http://cgcri.csircentral.net";
$years = get_years($url);
print_r($years);
mkdir('output');
foreach ($years as $obj) {
	crawl_pages($obj->handle, $url);
}
$family_subject = get_subject($url);
foreach ($subject as $s){
	$handle = $s->handle;
	$ht = '.html';
	$s->handle = str_replace($ht,'',$handle);
	$handle = $s->handle;
	$ht = 'SUB';
	$s->handle = str_replace($ht,'',$handle);
	$s->handle = (int)($s->handle);
}
usort($subject, function($a, $b) {
    return $a->handle <=> $b->handle;
});
// print_r($subject);	
foreach ($years as $s) {
	change_subject($s->handle,$s->index);
}


function get_years($base_url){
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
	$curl = curl_init ( $base_url."/view/year/" );
	curl_setopt_array ( $curl, $curl_options );
	$response = curl_exec ( $curl );
	curl_close ( $curl );

	libxml_use_internal_errors ( true );
	$doc = new DOMDocument ();
	$doc->loadHTML ( $response, LIBXML_BIGLINES | LIBXML_NOWARNING | LIBXML_ERR_NONE );
	libxml_clear_errors ();
	$xpath = new DOMXPath ( $doc );
	$structure = array();
	$i = 0;
	$items = $xpath->query ( "//td[@valign = 'top']/ul/li");
	if (!is_null($items)) {
		foreach ( $items as $item ) {
			$index = trim($items->item($i)->nodeValue );
			$nodes = $xpath->query ( "./a", $item );
	  		$name = trim ( $nodes->item ( 0 )->nodeValue );
			$handle = $nodes->item ( 0 )->getAttribute ( "href" );
			$obj = new stdClass ();
			$obj->name = $name;
			$obj->handle = $handle;
			$ht =$name;	
			$index = str_replace($ht,'',$index);
			$ht ='(';	
			$index = str_replace($ht,'',$index);
			$ht =')';	
			$index = str_replace($ht,'',$index);
			$i+=1;
			$obj->index = $index;
			// print_r($obj);
			// echo("\n");
			array_push($structure, $obj);
		}
	}
	return $structure;
}
function get_subject($base_url){
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
	$curl = curl_init ( $base_url."/view/subjects/" );
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
	$items = $xpath->query ( "//div[@class= 'ep_view_menu' ]/ul/li");
	// print_r($items);
	$structure = get_structure($items, $xpath);
	// print_r($structure);
	return $structure;
}

function get_structure($itemlist, $xpath) {
	$list = array ();
	foreach ( $itemlist as $item ) {
		// print_r($item);
		$nodes = $xpath->query ( "./a", $item );
		$name = trim ( $nodes->item ( 0 )->nodeValue );
		$handle = $nodes->item ( 0 )->getAttribute ( "href" );
		$obj = new stdClass ();
		
		$obj->name = $name;
		$obj->handle = $handle;
		
		$children = $xpath->query ( "./ul/li", $item );
		
		if ($children->length) {
			$obj->type = "community";
			
			$obj->children = get_structure( $children, $xpath);
			
		} else
			$obj->type = "collection";
		array_push ( $list, $obj );
		
	}
	
	foreach ($list as $l){
		// echo $l->name." ->   ". $l->handle."\n";
		$obj = new stdClass();
		$obj->name = $l->name;
		$obj->handle = $l->handle;
		array_push($GLOBALS['subject'], $obj);
	}
	return $list;
}

function crawl_pages($handle, $base_url){
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
	$ht = '.html';
	$handle = str_replace($ht,'',$handle);
	$curl = curl_init($base_url."/cgi/exportview/year/".$handle."/JSON/".$handle.".js");
	curl_setopt_array ( $curl, $curl_options );
	$response = curl_exec ( $curl );
	curl_close ( $curl );
	// echo $response;
	$oldpath = getcwd();
	// file_put_contents ($handle .".xml", $response->ownerDocument->saveXML ( $response ) );
	file_put_contents('output'."/".$handle.".json", $response);
}

function change_subject($handle,$index){
	$str = file_get_contents('output'."/".$handle.".json");
	// print_r($str);
	$total_subject = count($GLOBALS['subject']);
	// print_r($total_subject);
	$json = json_decode($str, true);
	$sub = array();
	for($i=0;$i<$index;$i++){
		// print_r($i);
		$total = count($json[$i]['subjects']);
		for($j=0;$j<$total;$j++){
			// print_r($j);
			$sub = $json[$i]['subjects'][$j];
			// echo "  ->    ";
			$ht ='SUB';	
			$sub = str_replace($ht,'',$sub);
			// print_r($sub);
			// echo "\n";
			$sub = (int)$sub;
			for($k=0;$k<$total_subject;$k++){
				if($k == $sub) break;
			}
			// print_r($GLOBALS['subject'][$k]->name);
			// echo "\n";
			$json[$i]['subjects'][$j] = $GLOBALS['subject'][$k]->name;
		}
	}
	file_put_contents('output'."/".$handle.".json", json_encode($json,JSON_PRETTY_PRINT));

}

?>