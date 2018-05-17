<?php
$url = "http://cgcri.csircentral.net/view/year/";
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
$structure = array();

$items = $xpath->query ( "//td[@valign = 'top']/ul/li");
if (!is_null($items)) {
	foreach ( $items as $item ) {
		$nodes = $xpath->query ( "./a", $item );
  		$name = trim ( $nodes->item ( 0 )->nodeValue );
		$handle = $nodes->item ( 0 )->getAttribute ( "href" );
		$obj = new stdClass ();
		
		$obj->name = $name;
		$obj->handle = $handle;
		// print_r($obj);
		array_push($structure, $obj);
	}
}
print_r($structure);

?>