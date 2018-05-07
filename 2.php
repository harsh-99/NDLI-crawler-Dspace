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

// ibxml_use_internal_errors ( true );
$doc = new DOMDocument ();
$doc->loadHTML ( $response, LIBXML_BIGLINES | LIBXML_NOWARNING | LIBXML_ERR_NONE );
libxml_clear_errors ();
$xpath = new DOMXPath ( $doc );
// $items = $xpath->query ( "//li[@class = 'ds-artifact-item community even']/div/div/a" );
// // print_r ($items);
// if (!is_null($items)) {
//   foreach ($items as $item) {
//     // echo "[". $item->nodeName. "]\n";
//     print_r ($item->getAttribute("href"));

//     $nodes = $item->childNodes;
//     foreach ($nodes as $node) {
//       echo $node->nodeValue. "\n";
//     }
//   }
// }
// $items1 = $xpath->query ( "//li[@class = 'ds-artifact-item community odd']/div/div/a" );

// print_r ($items1);
// if (!is_null($items1)) {
//   foreach ($items1 as $item1) {
//     print_r ($item1->getAttribute("href"));
    
//     echo "[". $item1->nodeName. "]\n";

//     $nodes1 = $item1->childNodes;
//     foreach ($nodes1 as $node1) {
//       echo $node1->nodeValue. "\n";

//     }
//   }
// }

$items2 = $xpath->query ( "//li[@class = 'ds-artifact-item community odd']" );
//print_r ($items2);
if (!is_null($items2)) {
  foreach ($items2 as $item2) {
    // echo "[". $item2->nodeName. "]\n";
    $items3 = $xpath->query ( "./div/div/a", $item2 );
    if (!is_null($items3)) {
        // echo "Hello";
        // print_r($items3);
        foreach ($items3 as $item3) {
            $items4 = $xpath->query ( "./ul/li/div/div/a", $item2 );
            if (!is_null($items4)) {
            // echo "Hello";
            // print_r($items3);
            foreach ($items4 as $item4) {
             // print_r($item3);
              // echo "Hello1";
              $nodes4 = $item4->childNodes;
              foreach ($nodes4 as $node4) {
                echo $node4->nodeValue. "\n";
            }
        }
    }
            // print_r($item3);
            // echo "Hello1";
            $nodes3 = $item3->childNodes;
            foreach ($nodes3 as $node3) {
                echo $node3->nodeValue. "\n";
            }
        }
    }
    
    $nodes2 = $item2->childNodes;
    foreach ($nodes2 as $node2) {
       // echo $node2->nodeValue. "\n";
        // print_r($node2);
        // echo "Hello";

    }
        // exit(0);
  }
}
// $items3 = $xpath->query ( "//li[@class = 'ds-artifact-item community odd']/div/div/a" );
// // print_r ($items2);
// if (!is_null($items3)) {
//   foreach ($items3 as $item3) {
//     echo "[". $item3->nodeName. "]\n";

//     $nodes3 = $item3->childNodes;
//     foreach ($nodes3 as $node3) {
//       echo $node3->nodeValue. "\n";
//       // print_r($node3);
//     }
//   }
// }
// $items4 = $xpath->query ( "/html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[2]/div/div/a/span" );
// // print_r ($items2);
// if (!is_null($items4)) {
//   foreach ($items4 as $item4) {
//     echo "[". $item4->nodeName. "]\n";

//     $nodes4 = $item4->childNodes;
//     foreach ($nodes4 as $node4) {
//       echo $node4->nodeValue. "\n";
//     }
//   }
// }
// $items5 = $xpath->query ( "/html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[2]/ul/li/div/div/a/span" );
// // print_r ($items2);
// if (!is_null($items5)) {
//   foreach ($items5 as $item5) {
//     echo "[". $item5->nodeName. "]\n";

//     $nodes5 = $item5->childNodes;
//     foreach ($nodes5 as $node5) {
//       echo $node5->nodeValue. "\n";
//     }
//   }
// }
// $items6 = $xpath->query ( "/html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[3]/div/div[1]/a/span" );
// // print_r ($items2);
// if (!is_null($items6)) {
//   foreach ($items6 as $item6) {
//     echo "[". $item6->nodeName. "]\n";

//     $nodes6 = $item6->childNodes;
//     foreach ($nodes6 as $node6) {
//       echo $node6->nodeValue. "\n";
//     }
//   }
// }
// $harsh = "/html/body/div[1]/div[4]/div/div[1]/div/ul/li/div/div[1]/a/span"
// if($harsh !=  )
// $items7 = $xpath->query ( "/html/body/div[1]/div[4]/div/div[1]/div/ul/li/div/div[1]/a/span" );
// // print_r ($items2);
// if (!is_null($items7)) {
//   foreach ($items7 as $item7) {
//     echo "[". $item7->nodeName. "]\n";

//     $nodes7 = $item7->childNodes;
//     foreach ($nodes7 as $node7) {
//       echo $node7->nodeValue. "\n";
//     }
//   }
// }

// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[1]/div/div/a/span 
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[2]/div/div/a/span
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[3]/div/div/a/span

// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[1]/ul/li[1]/div/div/a/span
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[2]/ul/li[1]/div/div/a/span
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/div/div[1]/a/span
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[1]/li[1]/div/div/a/span
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[1]/div/div/a/span
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[1]/ul/li[1]/div/div/a/span
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[2]/div/div/a/span
// /html/body/div[1]/div[4]/div/div[1]/div/ul/li/ul[2]/li[3]/div/div[1]/a/span


?>