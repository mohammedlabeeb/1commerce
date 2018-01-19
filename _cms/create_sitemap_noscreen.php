<?php
include_once("../includes/masterinclude.php");
$preferences = getPreferences();
$dir = "xml/";
$filename = $_GET['file'] . ".xml";
$fullpath = $dir . $filename;
$backup = 0;

//open error log
$file1 = "logs/create_sitemap.txt";
$handle1 = fopen($file1,'w');

if (!$handle1){
	die("Log file open failure:" . mysql_error());
}else{
	$today = getdate();
	fwrite($handle1, "=============================" . "\r\n");
	fwrite($handle1, "CREATE SITEMAP ERROR LOG - " . date("d/m/y : H:i:s", time()) . "\r\n");
	fwrite($handle1, "=============================" . "\r\n");
}

echo "Processing Data - this may take a few minutes ......";

//check that file doesn't already exist. If ir does then back it up
if (is_dir($dir)) {
	if ($dhandle = opendir($dir)) {
		while (($file = readdir($dhandle)) !== false) {
			if ($file == $filename){
				//backup existing file
				$backup = $fullpath . "_bak";
   				rename($fullpath, $backup);
				$backup = 1;
			}
		}
		closedir($dhandle);
	}
}

//create header
$doc = new DomDocument('1.0', 'UTF-8');
$doc->formatOutput = true;
  
// create root node (rss) with attributes
$root = $doc->createElement("urlset");
$root->setAttribute('xmlns', 'http://www.google.com/schemas/sitemap/0.84');
$root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$root->setAttribute('xsi:schemaLocation', 'http://www.google.com/schemas/sitemap/0.84');
$root = $doc->appendChild($root);

//INDEX, CART AND SEARCH PAGES
//============================
$cntr1 = 0;
$url = $doc->CreateElement("url");
$loc = $doc->createElement("loc");
$link = $preferences->PREF_SHOPURL;
$loc->appendChild($doc->createTextNode($link));
$url->appendChild($loc);
$root->appendChild($url);
$cntr1 ++;

$url = $doc->CreateElement("url");
$loc = $doc->createElement("loc");
$link = $preferences->PREF_SHOPURL . "/cart";
$loc->appendChild($doc->createTextNode($link));
$url->appendChild($loc);
$root->appendChild($url);
$cntr1 ++;

$url = $doc->CreateElement("url");
$loc = $doc->createElement("loc");
$link = $preferences->PREF_SHOPURL . "/search";
$loc->appendChild($doc->createTextNode($link));
$url->appendChild($loc);
$root->appendChild($url);
$cntr1 ++;

//CATEGORY AND PRODUCT PAGES
//***************************
//starting at the top level drill down through the menu structure (HARDCODE TO LEVEL 5 ONLY AT THIS STAGE)
//LEVEL1 - find categories hung on tree node                      ****************************************
//======
$tree_node1 = "0"; $error_count = 0;
$categories_level1 = getCategories($tree_node1);
foreach($categories_level1 as $cat1){
	if($cat1->CA_DISPLAY == "Y"){
		//create element (url)
		$url = $doc->createElement("url");
		$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($cat1->CA_NAME, ENT_QUOTES)) . "/" . $cat1->CA_TREE_NODE . "_" . $cat1->CA_CODE . ".htm";
		//now add url to xml structure
		$loc = $doc->createElement("loc");
		$loc->appendChild($doc->createTextNode($link));
		$url->appendChild($loc);
		$root->appendChild($url);
		$cntr1 ++;
		//LEVEL 2 - find categories hung on tree node
		//=======
		$tree_node2 = "0_" . $cat1->CA_CODE;
		$categories_level2 = getCategories($tree_node2);
		foreach($categories_level2 as $cat2){
			if($cat2->CA_DISPLAY == "Y"){
				//create element (url)
				$url = $doc->createElement("url");
				$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($cat2->CA_NAME, ENT_QUOTES)) . "/" . $cat2->CA_TREE_NODE . "_" . $cat2->CA_CODE . ".htm";
				//now add url to xml structure
				$loc = $doc->createElement("loc");
				$loc->appendChild($doc->createTextNode($link));
				$url->appendChild($loc);
				$root->appendChild($url);
				$cntr1 ++;
				//LEVEL 3 - find categories hung on tree node
				//=======
				$tree_node3 = "0_" . $cat1->CA_CODE . "_" . $cat2->CA_CODE;
				$categories_level3 = getCategories($tree_node3);
				foreach($categories_level3 as $cat3){
					if($cat3->CA_DISPAY == "Y"){
						//create element (url)
						$url = $doc->createElement("url");
						$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($cat3->CA_NAME, ENT_QUOTES)) . "/" . $cat3->CA_TREE_NODE . "_" . $cat3->CA_CODE . ".htm";
						//now add url to xml structure
						$loc = $doc->createElement("loc");
						$loc->appendChild($doc->createTextNode($link));
						$url->appendChild($loc);
						$root->appendChild($url);
						$cntr1 ++;
						//LEVEL 4 - find categories hung on tree node
						//=======
						$tree_node4 = "0_" . $cat4->CA_CODE . "_" . $cat4->CA_CODE;
						$categories_level4 = getCategories($tree_node4);
						foreach($categories_level4 as $cat4){
							if($cat4->CA_DISPLAY == "Y"){
								//create element (url)
								$url = $doc->createElement("url");
								$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($cat4->CA_NAME, ENT_QUOTES)) . "/" . $cat4->CA_TREE_NODE . "_" . $cat4->CA_CODE . ".htm";
								//now add url to xml structure
								$loc = $doc->createElement("loc");
								$loc->appendChild($doc->createTextNode($link));
								$url->appendChild($loc);
								$root->appendChild($url);
								$cntr1 ++;
								//LEVEL 5 - find categories hung on tree node
								//=======
								$tree_node5 = "0_" . $cat5->CA_CODE . "_" . $cat5->CA_CODE;
								$categories_level5 = getCategories($tree_node5);
								foreach($categories_level5 as $cat5){
									if($cat5->CA_DISPLAY == "Y"){
										//create element (url)
										$url = $doc->createElement("url");
										$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($cat5->CA_NAME, ENT_QUOTES)) . "/" . $cat5->CA_TREE_NODE . "_" . $cat5->CA_CODE . ".htm";
										//now add url to xml structure
										$loc = $doc->createElement("loc");
										$loc->appendChild($doc->createTextNode($link));
										$url->appendChild($loc);
										$root->appendChild($url);
										$cntr1 ++;
									}
								}
								//LEVEL 5 - find products hung on tree node from prodcat
								//=======
								$products_level5 = getProducts($tree_node5);
								foreach($products_level5 as $prod5){
									$product = getProductDetails($prod5->PC_PRODUCT);
									//create element (url)
									$url = $doc->createElement("url");
									$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $prod5->PC_TREE_NODE . "/" . $prod5->PC_PRODUCT . ".htm";
									//now add url to xml structure
									$loc = $doc->createElement("loc");
									$loc->appendChild($doc->createTextNode($link));
									$url->appendChild($loc);
									$root->appendChild($url);
									$cntr1 ++;
								}
							}
						}
						//LEVEL 4 - find products hung on tree node from prodcat
						//=======
						$products_level4 = getProducts($tree_node4);
						foreach($products_level4 as $prod4){
							$product = getProductDetails($prod4->PC_PRODUCT);
							//create element (url)
							$url = $doc->createElement("url");
							$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $prod4->PC_TREE_NODE . "/" . $prod4->PC_PRODUCT . "htm";
							//now add url to xml structure
							$loc = $doc->createElement("loc");
							$loc->appendChild($doc->createTextNode($link));
							$url->appendChild($loc);
							$root->appendChild($url);
							$cntr1 ++;
						}
					}
				}
				//LEVEL 3 - find products hung on tree node from prodcat
				//=======
				$products_level3 = getProducts($tree_node3);
				foreach($products_level3 as $prod3){
					$product = getProductDetails($prod3->PC_PRODUCT);
					//create element (url)
					$url = $doc->createElement("url");
					$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $prod3->PC_TREE_NODE . "/" . $prod3->PC_PRODUCT . ".htm";
					//now add url to xml structure
					$loc = $doc->createElement("loc");
					$loc->appendChild($doc->createTextNode($link));
					$url->appendChild($loc);
					$root->appendChild($url);
					$cntr1 ++;
				}
			}
		}
		//LEVEL 2 - find products hung on tree node from prodcat
		//=======
		$products_level2 = getProducts($tree_node2);
		foreach($products_level2 as $prod2){
			$product = getProductDetails($prod2->PC_PRODUCT);
			//create element (url)
			$url = $doc->createElement("url");
			$link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $prod2->PC_TREE_NODE . "/" . $prod2->PC_PRODUCT . ".htm";
			//now add url to xml structure
			$loc = $doc->createElement("loc");
			$loc->appendChild($doc->createTextNode($link));
			$url->appendChild($loc);
			$root->appendChild($url);
			$cntr1 ++;
		}
	}
	//NOTE that there are no Level 1 products since this is the index page
}
//INFORMATION PAGES
//******************
$info = getAllInformation("Y", "All");
foreach($info as $i){
	$url = $doc->createElement("url");
	$link = $preferences->PREF_SHOPURL . $i->IN_LINK;
	//now add url to xml structure
	$loc = $doc->createElement("loc");
	$loc->appendChild($doc->createTextNode($link));
	$url->appendChild($loc);
	$root->appendChild($url);
}

echo "Total URLs Created = " . $cntr1 . "<br/>";

//echo $doc->saveXML();
$doc->save($fullpath);
fclose($handle1);
echo "<script type=\"text/javascript\">document.location.href=\"/_cms/create_sitemap.php?file=" . $_GET['file'] . "&records=" . $cntr1 . "&backup=" . $backup . "&errors=" . $error_count . "\";</script>";
?> 