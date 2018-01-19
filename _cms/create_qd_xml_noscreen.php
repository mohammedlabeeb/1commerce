<?php
include_once("../includes/masterinclude.php");
$preferences = getPreferences();
$dir = "../xml/";
$filename = $_GET['file'] . ".xml";
$fullpath = $dir . $filename;
$backup = 0;

//open error log
$file1 = "logs/create_quantity_discount.txt";
$handle1 = fopen($file1,'w');

if (!$handle1){
	die("Log file open failure:" . mysql_error());
}else{
	$today = getdate();
	fwrite($handle1, "======================================" . "\r\n");
	fwrite($handle1, "CREATE QUANTITY DISCOUNT XML ERROR LOG - " . date("d/m/y : H:i:s", time()) . "\r\n");
	fwrite($handle1, "======================================" . "\r\n");
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
$root = $doc->createElement("qdset");
$root = $doc->appendChild($root);


//CATEGORY AND PRODUCT PAGES
//***************************
//starting at the top level drill down through the menu structure (HARDCODE TO LEVEL 5 ONLY AT THIS STAGE)
//LEVEL1 - find categories hung on tree node                      ****************************************
//======
$discounts = Get_All_Qdiscounts();
$cntr1 = 0; $cntr2 = 0;
foreach($discounts as $d){
/*	$product = $doc->createElement("product");
	$attribute = $doc->createAttribute('code');
	$attribute->value = $d->QDH_PRODUCT;
	$product->appendChild($attribute);*/
/*		$code = $doc->createElement("code");
			$code->appendChild($doc->createTextNode($d->QDH_PRODUCT));
			$product->appendChild($code);*/
	$product = $doc->createElement($d->QDH_PRODUCT);
		$type = $doc->createElement("type");
			$type->appendChild($doc->createTextNode($d->QDH_TYPE));
			$product->appendChild($type);
			
	$discount_lines = Get_Qdiscount_Lines($d->QDH_PRODUCT);
	foreach($discount_lines as $dl){
		$qty = $doc->createElement("quantity");
			$qty->appendChild($doc->createTextNode($dl->QDL_QTY));
			$product->appendChild($qty);
		$discount = $doc->createElement("discount");
			$discount->appendChild($doc->createTextNode($dl->QDL_ADJUST));
			$product->appendChild($discount);
		$cntr2++;
	}
	$root->appendChild($product);
	$cntr1++;
}


/*foreach($categories_level1 as $cat1){
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
	}
	//NOTE that there are no Level 1 products since this is the index page
}*/

echo "Total Products = " . $cntr1 . "   Total Discount Lines = " . $cntr2 . "<br/>";

//echo $doc->saveXML();
$doc->save($fullpath);
fclose($handle1);
echo "<script type=\"text/javascript\">document.location.href=\"/_cms/create_qd_xml.php?file=" . $_GET['file'] . "&products=" . $cntr1 . "&lines=" . $cntr2 . "&backup=" . $backup . "&errors=0\";</script>";
?> 