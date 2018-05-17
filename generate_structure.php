<?php
//$url = 'http://210.212.228.207/community-list';
$url = "http://shodhganga.inflibnet.ac.in/community-list";
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

libxml_use_internal_errors ( true );
$doc = new DOMDocument ();
$doc->loadHTML ( $response, LIBXML_BIGLINES | LIBXML_NOWARNING | LIBXML_ERR_NONE );
libxml_clear_errors ();
$xpath = new DOMXPath ( $doc );
// $path="//div[@id='aspect_artifactbrowser_CommunityBrowser_div_comunity-browser']/ul[@xmlns='http://di.tamu.edu/DRI/1.0/']/li";
$path = "//main[@id='content']/div[@class='container']/ul/li";
$block = $xpath->query ($path , $doc );

//$structure=( get_structure_xmlui ( $block, $xpath ) );
$structure=get_structure_jspui ( $block, $xpath );
file_put_contents("structure.json", json_encode($structure,JSON_PRETTY_PRINT));

function get_structure_xmlui($itemlist, $xpath) {
	$list = array ();
	foreach ( $itemlist as $item ) {
		// print_r($item);
		$nodes = $xpath->query ( "./div/div/a", $item );
		$name = trim ( $nodes->item ( 0 )->nodeValue );
		$handle = $nodes->item ( 0 )->getAttribute ( "href" );
		$obj = new stdClass ();
		
		$obj->name = $name;
		$obj->handle = $handle;
		
		$children = $xpath->query ( "./ul/li", $item );
		
		if ($children->length) {
			$obj->type = "community";
			
			$obj->children = get_structure_xmlui ( $children, $xpath);
			
		} else
			$obj->type = "collection";
		array_push ( $list, $obj );
		
	}
	
	return $list;
}
function get_structure_jspui($itemlist, $xpath){
	$list = array ();
	foreach ( $itemlist as $item ) {
		// print_r($item);
		$nodes = $xpath->query ( "./div[@class='media-body']/h4/a", $item );
		$name = trim ( $nodes->item ( 0 )->nodeValue );
		$handle = $nodes->item ( 0 )->getAttribute ( "href" );
		$obj = new stdClass ();
		
		$obj->name = $name;
		$obj->handle = $handle;
		
		$children = $xpath->query ( "./div[@class='media-body']/ul[@class='media-list']/li", $item );
		
		if ($children->length) {
			$obj->type = "community";
			
			$obj->children = get_structure_jspui( $children, $xpath);
			
		} else
			$obj->type = "collection";
			array_push ( $list, $obj );
			
	}
	
	return $list;
}
?>
