<?php
// $base_url = "http://210.212.91.105:8080/xmlui";
$base_url = "http://210.212.228.207";
$base_folder = "crawl-output";
$harvester = new harvester ( $base_url, $base_folder );
$harvester->harvest ();

// ///////////////////////
class harvester {
	private $base_url = null;
	private $base_folder = null;
	private $structure = null;
	const ITEM_LIMIT = 1000;
	public function __construct($base_url, $base_folder) {
		$this->base_url = $base_url;
		$this->base_folder = $base_folder;
		if (file_exists ( $this->base_folder )) {
			system ( "rm -r " . $this->base_folder );
		}
		mkdir ( $this->base_folder );
	}
	public function harvest() {
		// $curl_options = array (
		// CURLOPT_FOLLOWLOCATION => true,
		// CURLOPT_RETURNTRANSFER => true,
		// CURLOPT_SSL_VERIFYHOST => false,
		// CURLOPT_TIMEOUT => 60,
		// CURLOPT_HTTPHEADER => array (
		// "User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36"
		// ),
		// CURLOPT_PROXY => "http://172.16.2.30:8080"
		// );
		// $curl = curl_init ( $this->base_url . "/community-list" );
		// curl_setopt_array ( $curl, $curl_options );
		// $response = curl_exec ( $curl );
		// curl_close ( $curl );
		// file_put_contents ( "a.html", $response );
		// file_put_contents ( "b.html", $response );
		// exit ( 0 );
		$response = file_get_contents ( "a.html" );
		// $response = file_get_contents ( "b.html" );
		
		libxml_use_internal_errors ( true );
		$doc = new DOMDocument ();
		$doc->loadHTML ( $response, LIBXML_BIGLINES | LIBXML_NOWARNING | LIBXML_ERR_NONE );
		libxml_clear_errors ();
		$xpath = new DOMXPath ( $doc );
		
		$nodes = $xpath->query ( "//div[@id='aspect_artifactbrowser_CommunityBrowser_div_comunity-browser']" );
		$this->structure = $this->get_structure ( $nodes [0], $xpath );
		
		file_put_contents ( $this->base_folder . "/structure.json", json_encode ( $this->structure, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
		
		$this->crawl_items ( $this->structure );
	}
	private function crawl_items($nodes) {
		$doc = new DOMDocument ();
		
		foreach ( $nodes as $node ) {
			if ($node->type == "collection") {
				$curl_options = array (
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_POST => true,
						CURLOPT_POSTFIELDS => array (
								"type" => "title",
								"sort_by" => 1,
								"order" => "ASC",
								"rpp" => 500,
								"update" => "Update" 
						),
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_SSL_VERIFYHOST => false,
						CURLOPT_TIMEOUT => 60,
						CURLOPT_HTTPHEADER => array (
								"User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36" 
						),
						CURLOPT_PROXY => "http://172.16.2.30:8080" 
				);
				echo "Processing Collection: " . $node->id . "\t[$node->name]" . PHP_EOL;
				$curl = curl_init ( $this->base_url . "/handle/" . $node->id . "/browse" );
				curl_setopt_array ( $curl, $curl_options );
				$response = curl_exec ( $curl );
				curl_close ( $curl );
				mkdir ( $folder = $this->base_folder . "/" . str_replace ( "/", "_", $node->id ) );
				
				$doc->loadHTML ( $response, LIBXML_BIGLINES | LIBXML_NOWARNING | LIBXML_ERR_NONE );
				$xpath = new DOMXPath ( $doc );
				
				$items = $xpath->query ( "//div[@id='aspect_artifactbrowser_ConfigurableBrowse_div_browse-by-title-results']/ul/li/div/div/a" );
				foreach ( $items as $item ) {
					$handle = preg_replace ( "/(.*?handle)\/(.*)/", "$2", $item->getAttribute ( "href" ) );
					try {
						$this->store_item_details ( $handle, $folder );
					} catch ( Exception $e ) {
						echo "ERROR: error while processing Item: " . $handle . PHP_EOL;
						exit ( 0 );
					}
				}
			} else {
				$this->crawl_items ( $node->child );
			}
		}
	}
	private function store_item_details($handle, $file_path) {
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
		$curl = curl_init ( $this->base_url . "/handle/" . $handle . "?show=full" );
		curl_setopt_array ( $curl, $curl_options );
		$response = curl_exec ( $curl );
		curl_close ( $curl );
		
		$doc = new DOMDocument ();
		$doc->loadHTML ( $response, LIBXML_BIGLINES | LIBXML_NOWARNING | LIBXML_ERR_NONE );
		$xpath = new DOMXPath ( $doc );
		$details = $xpath->query ( "//div[@id='aspect_artifactbrowser_ItemViewer_div_item-view']" );
		if ($details->length) {
			$details = $details [0];
			file_put_contents ( $file_path . "/" . str_replace ( "/", "_", $handle ) . ".xml", $details->ownerDocument->saveXML ( $details ) );
		} else {
			echo "WARNING: Restricted item: " . $handle . PHP_EOL;
		}
	}
	private function get_structure($node, $xpath) {
		$list = array ();
		
		// get collections
		$collections = $xpath->query ( "./ul/li[contains(@class,'collection')]", $node );
		foreach ( $collections as $collection ) {
			$self_node = $xpath->query ( "./div/div/a", $collection ) [0];
			$obj = new stdClass ();
			$obj->name = trim ( $self_node->textContent );
			$obj->id = preg_replace ( "/(.*?handle)\/(.*)/", "$2", $self_node->getAttribute ( "href" ) );
			$obj->type = "collection";
			$obj->child = array ();
			array_push ( $list, $obj );
		}
		
		// get communities
		$communities = $xpath->query ( "./ul/li[contains(@class,'community')]", $node );
		foreach ( $communities as $community ) {
			$self_node = $xpath->query ( "./div/div/a", $community ) [0];
			$obj = new stdClass ();
			$obj->name = trim ( $self_node->textContent );
			$obj->id = preg_replace ( "/(.*?handle)\/(.*)/", "$2", $self_node->getAttribute ( "href" ) );
			$obj->type = "community";
			$obj->child = $this->get_structure ( $community, $xpath );
			array_push ( $list, $obj );
		}
		return $list;
	}
}
?>
