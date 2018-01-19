<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");
include_once('../class/SimpleImage.php'); 

$message = ""; $errors_array = array();
$message_upload = "";
$target_file = "";
$pathName = "/images/";
$scrolltobottom = ""; $scroll_to = "";
$no_addl_images = 7;

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Amend Products";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

//the form encryption enctype="multipart/form-data" is prefixing every "'" with a "\" every time a submit button is hit which is knackering the descriptions.
//The only way around this short of re-structuring the whole program is to declare the character "\" as invalid and strip it out of every description if found
if(isset($_POST['NAME'])){$_POST['NAME'] = str_replace("\\", "", $_POST['NAME']);}
if(isset($_POST['DESC_SHORT'])){$_POST['DESC_SHORT'] = str_replace("\\", "", $_POST['DESC_SHORT']);}
if(isset($_POST['DESC_LONG'])){$_POST['DESC_LONG'] = str_replace("\\", "", $_POST['DESC_LONG']);}
if(isset($_POST['DESC_TRADE'])){$_POST['DESC_TRADE'] = str_replace("\\", "", $_POST['DESC_TRADE']);}
if(isset($_POST['NO_STOCK'])){$_POST['NO_STOCK'] = str_replace("\\", "", $_POST['NO_STOCK']);}
if(isset($_POST['IMAGE_ALT'])){$_POST['IMAGE_ALT'] = str_replace("\\", "", $_POST['IMAGE_ALT']);}
if(isset($_POST['GOOGLE_BRAND'])){$_POST['GOOGLE_BRAND'] = str_replace("\\", "", $_POST['GOOGLE_BRAND']);}
if(isset($_POST['GOOGLE_GTIN'])){$_POST['GOOGLE_GTIN'] = str_replace("\\", "", $_POST['GOOGLE_GTIN']);}
if(isset($_POST['GOOGLE_MPN'])){$_POST['GOOGLE_MPN'] = str_replace("\\", "", $_POST['GOOGLE_MPN']);}
if(isset($_POST['GOOGLE_ADWORDS_GROUPING'])){$_POST['GOOGLE_ADWORDS_GROUPING'] = str_replace("\\", "", $_POST['GOOGLE_ADWORDS_GROUPING']);}
if(isset($_POST['GOOGLE_ADWORDS_LABELS'])){$_POST['GOOGLE_ADWORDS_LABELS'] = str_replace("\\", "", $_POST['GOOGLE_ADWORDS_LABELS']);}
if(isset($_POST['GOOGLE_ADWORDS_REDIRECT'])){$_POST['GOOGLE_ADWORDS_REDIRECT'] = str_replace("\\", "", $_POST['GOOGLE_ADWORDS_REDIRECT']);}

if(isset($_POST['META_TITLE'])){$_POST['META_TITLE'] = str_replace("\\", "", $_POST['META_TITLE']);}
if(isset($_POST['META_DESC'])){$_POST['META_DESC'] = str_replace("\\", "", $_POST['META_DESC']);}
if(isset($_POST['META_KEYWORDS'])){$_POST['META_KEYWORDS'] = str_replace("\\", "", $_POST['META_KEYWORDS']);}
if(isset($_POST['CUSTOM_HEAD'])){$_POST['CUSTOM_HEAD'] = str_replace("\\", "", $_POST['CUSTOM_HEAD']);}
if(isset($_POST['PROD_WRAP'])){$_POST['PROD_WRAP'] = str_replace("\\", "", $_POST['PROD_WRAP']);}
for($i = 1; $i <= $no_addl_images; $i++){
	if(isset($_POST['IMAGE_ALT_ADD' . $i])){$_POST['IMAGE_ALT_ADD' . $i] = str_replace("\\", "", $_POST['IMAGE_ALT_ADD' . $i]);}
}
if(isset($_POST['HOTSPOT1'])){$_POST['HOTSPOT1'] = str_replace("\\", "", $_POST['HOTSPOT1']);}
if(isset($_POST['HOTSPOT2'])){$_POST['HOTSPOT2'] = str_replace("\\", "", $_POST['HOTSPOT2']);}
if(isset($_POST['HOTSPOT3'])){$_POST['HOTSPOT3'] = str_replace("\\", "", $_POST['HOTSPOT3']);}

//initialise screen fields
$selected_product = "";
$productcode = "";
$name = ""; $sku = "";
$desc_short = ""; $desc_long = "";
$desc_trade = "";
$weight = "0";
$quantity = "1";
$selling = "0.00"; $trade = "0.00";
$tax = "0.00"; $taxexemption = "N";
$shipping = "0.00"; $shipping_apply = "F"; $disable_product = "N";
$option1 = "#"; $option2 = "#"; $option3 = "#"; $option4 = "#";
$user_string1; $availability = ""; $condition = "";
$no_stock = ""; $disable_product = "N";
$google_cat = ""; $google_brand = ""; $google_gtin = ""; $google_mpn = "";
$google_adwords_grouping = ""; $google_adwords_labels = ""; $google_adwords_redirect = "";
$meta_title = ""; $meta_desc = ""; $meta_keywords = ""; $custom_head = "";
$prod_wrap = "";
$date_amended = "";
$date_added = "";
$image_name = ""; $image_folder = ""; $image_alt = "";
for($i = 1; $i <= $no_addl_images; $i++){
	${"image_name_add" . $i} = ""; ${"image_folder_add" . $i} = ""; ${"image_alt_add" . $i} = "";
	${"target_file_add" . $i} = "";
	${"message_upload_add" . $i} = "";
}
$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";

if(isset($_POST['OPTION1'])){$option1 = $_POST['OPTION1'];}
if(isset($_POST['OPTION2'])){$option2 = $_POST['OPTION2'];}
if(isset($_POST['OPTION3'])){$option3 = $_POST['OPTION3'];}
if(isset($_POST['OPTION4'])){$option4 = $_POST['OPTION4'];}

if($preferences->PREF_GOOGLE_SEARCH == "N"){
	//set google post fields to blank
	$_POST['GOOGLE_CAT'] = ""; $_POST['AVAILABILTY'] = ""; $_POST['GOOGLE_BRAND'] = "";
	$_POST['GOOGLE_GTIN'] = ""; $_POST['GOOGLE_MPN'] = "";
	$_POST['GOOGLE_ADWORDS_GROUPING'] = ""; $_POST['GOOGLE_ADWORDS_LABELS'] = ""; $_POST['GOOGLE_ADWORDS_REDIRECT'] = "";
}

if (isset($_POST['DELETE']) and $_POST['SELECTED_PRODUCT'] != "") {
	//first delete all entries from the menu structure
	$rows = deleteProductFromTree($_POST['PRODUCT'], "ALL");	
	$message .= "(" . $rows . ") Product entries removed from the menu structure" . "<br/>";
	$warning = "green";
	//Delete the row just created - NOTE the preferences file will NOT be adjusted and the previous seed will be lost
	$rows = Delete_Product($_POST['PRODUCT']);
	if ($rows == 1){
		$message .= $rows . " PRODUCT record successfully DELETED" .  "<br/>";
		$warning = "green";
		//now delete all associated PRODADD records
		$rows = Delete_Prodadd_multi($_POST['PRODUCT'], $no_addl_images);
		if($rows > 0){$message .= $rows . " associated ADDITIONAL IMAGES record(s) DELETED" . "<br/>";}
		//now delete all associated ADDL_PRODUCTS records
		$rows = Delete_ADDL_PRODUCTS($_POST['PRODUCT'], "ALL");
		if($rows > 0){$message .= $rows . " associated ADDITIONAL PRODUCTS record(s) DELETED" . "<br/>";}
		//now delete all associated HOTSPOTS records
		$rows = Delete_Hotspots($_POST['PRODUCT']);
		if($rows > 0){$message .= $rows . " associated HOTSPOTS record(s) DELETED" . "<br/>";}
		//initialise all fields
		$_POST['PRODUCT_CREATED'] = 0;
		$product = getProductDetails($_POST['PRODUCT'], "");
		$option1 = ""; $option2 = ""; $option3 = ""; $option4 = "";
	}else{
		$message .= "PRODUCT record NOT DELETED - PLEASE CONTACT SHOPFITTER!!!"; 
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
		$warning = "red";
	}
	//initialise screen fields
	$selected_product = "";
	$productcode = "";
	$name = ""; $sku = "";
	$desc_short = ""; $desc_long = "";
	$desc_trade = "";
	$weight = "0";
	$quantity = "1";
	$selling = "0.00"; $trade = "0.00";
	$tax = "0.00"; $taxexemption = "N";
	$shipping = "0.00"; $shipping_apply = "F"; $disable_product = "N";
	$option1 = "#"; $option2 = "#"; $option3 = "#"; $option4 = "#";
	$user_string1; $availability = ""; $condition = "";
	$no_stock = "";
	$google_cat = ""; $google_brand = ""; $google_gtin = ""; $google_mpn = "";
	$google_adwords_grouping = ""; $google_adwords_labels = ""; $google_adwords_redirect;
	$meta_title = ""; $meta_desc = ""; $meta_keywords = ""; $custom_head = "";
	$prod_wrap = "";
	$date_amended = "";
	$date_added = "";
	$image_name = ""; $image_folder = ""; $image_alt = "";
	for($i = 1; $i < $no_addl_images; $i++){
		${"image_name_add" . $i} = ""; ${"image_folder_add" . $i} = ""; ${"image_alt_add" . $i} = "";
	}
	$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,9000)\" ";}
}

if (isset($_GET['searchproduct'])) {
	//NEW PRODUCT SELECTED from search dropdown so get product deatils for display 
	$product = getProductDetails($_GET['searchproduct']);
	$selected_product = $product->PR_PRODUCT;
	$productcode = $product->PR_PRODUCT;
	$name = html_entity_decode($product->PR_NAME, ENT_QUOTES);
	$sku = $product->PR_SKU;
	$desc_short = html_entity_decode($product->PR_DESC_SHORT, ENT_QUOTES);
	$desc_long = html_entity_decode($product->PR_DESC_LONG, ENT_QUOTES);
	$desc_trade = html_entity_decode($product->PR_DESC_TRADE, ENT_QUOTES);
	$weight = $product->PR_WEIGHT;
	$quantity = $product->PR_QUANTITY;
	$selling = $product->PR_SELLING;
	$trade = $product->PR_TRADE;
	$tax = $product->PR_TAX;
	$taxexemption = $product->PR_TAXEXEMPTION;
	$shipping = $product->PR_SHIPPING;
	$shipping_apply = $product->PR_SHIPPING_APPLY;
	$disable_product = $product->PR_DISABLE;
	$option1 = $product->PR_OPTION1;
	$option2 = $product->PR_OPTION2;
	$option3 = $product->PR_OPTION3;
	$option4 = $product->PR_OPTION4;
	$user_string1 = html_entity_decode($product->PR_USER_STRING1, ENT_QUOTES);
	$no_stock = html_entity_decode($product->PR_NO_STOCK, ENT_QUOTES);
	$google_cat = $product->PR_GOOGLE_CAT;
	$availability = $product->PR_AVAILABILITY;
	$condition = $product->PR_GOOGLE_CONDITION;
	$google_brand = $product->PR_GOOGLE_BRAND;
	$google_gtin = $product->PR_GOOGLE_GTIN;
	$google_mpn = $product->PR_GOOGLE_MPN;
	$google_adwords_grouping = $product->PR_GOOGLE_ADWORDS_GROUPING;
	$google_adwords_labels = $product->PR_GOOGLE_ADWORDS_LABELS;
	$google_adwords_redirect = $product->PR_GOOGLE_ADWORDS_REDIRECT;
	$meta_title = html_entity_decode($product->PR_META_TITLE, ENT_QUOTES);
	$meta_desc = html_entity_decode($product->PR_META_DESC, ENT_QUOTES);
	$meta_keywords = html_entity_decode($product->PR_META_KEYWORDS, ENT_QUOTES);
	$custom_head = html_entity_decode($product->PR_CUSTOM_HEAD, ENT_QUOTES);
	$prod_wrap = $product->PR_PROD_WRAP;
	$date_amended = $product->PR_LAST_UPDATED;
	$date_added = $product->PR_DATE_ADDED;
	$image_name = $product->PR_IMAGE;
	$image_folder = $product->PR_IMAGE_FOLDER;
	$image_alt = html_entity_decode($product->PR_IMAGE_ALT, ENT_QUOTES);
	//get additional images
	$additional = getAdditionalImages($_GET['searchproduct']);
	foreach($additional as $a){
		//Note that Additional Image 1 is actually filed as PRA_POSITION = 2 and Additional Image 2 as PRA_POSITION = 3
		for($i = 1; $i <= $no_addl_images; $i++){
			if ($a->PRA_POSITION == $i + 1){
				${"image_name_add" . $i} = $a->PRA_IMAGE;
				${"image_folder_add" . $i} = $a->PRA_IMAGE_FOLDER;
				${"image_alt_add" . $i} = $a->PRA_IMAGE_ALT;
			}
		}
	}
	//get hotspots
	$hotspots = getHotspots($_GET['searchproduct']);
	$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";
	foreach($hotspots as $h){
		$hotspot = "hotspot" . $h->HS_NUMBER;
		$$hotspot = html_entity_decode($h->HS_DATA, ENT_QUOTES);
	}
	
	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_PRODUCT'] = $_GET['searchproduct'];
}

//check if an additional image upload button has been hit
$addl = 0; $rmvl = 0; $rest = 0;
for($i = 1; $i <= $no_addl_images; $i++){
	if(isset($_POST['UPLOAD_IMAGE_ADD' . $i])){
		$addl = $i;
	}
	if(isset($_POST['REMOVE_IMAGE_ADD' . $i])){
		$rmvl = $i;
	}
	if(isset($_POST['RESTORE_IMAGE_ADD' . $i])){
		$rest = $i;
	}
}
if (isset($_POST['REMOVE_IMAGE']) or $rmvl > 0) {
	if (isset($_POST['REMOVE_IMAGE'])){
		$image_name = "no-image.jpg";
		$image_folder = "";
		$image_alt = "";
		$image_name_add1 = $_POST['IMAGE_NAME_ADD1'];
		$image_folder_add1 = $_POST['IMAGE_FOLDER_ADD1'];
		$image_alt_add1 = $_POST['IMAGE_ALT_ADD1'];
		$image_name_add2 = $_POST['IMAGE_NAME_ADD2'];
		$image_folder_add2 = $_POST['IMAGE_FOLDER_ADD2'];
		$image_alt_add2 = $_POST['IMAGE_ALT_ADD2'];
	}
	
	if(isset($_POST['REMOVE_IMAGE_ADD' . $rmvl])){
		$image_name = $_POST['IMAGE_NAME'];
		$image_folder = $_POST['IMAGE_FOLDER'];
		$image_alt = $_POST['IMAGE_ALT'];
		//get additional images
		${"image_name_add" . $rmvl} = "no-image.jpg";
		${"image_folder_add" . $rmvl} = "";
		${"image_alt_add" . $rmvl} = "";
		for($i = 1; $i <= $no_addl_images; $i++){
			if($i != $rmvl){
				${"image_name_add" . $i} = $_POST['IMAGE_NAME_ADD' . $i];
				${"image_folder_add" . $i} = $_POST['IMAGE_FOLDER_ADD' . $i];
				${"image_alt_add" . $i} = $_POST['IMAGE_ALT_ADD' . $i];
			}
		}
		$scroll_to = "additional_image_" . $rmvl;
	}
	
	//now refresh the product settings made so far
	$selected_product = $_POST['SELECTED_PRODUCT'];;
	$productcode = $_POST['PRODUCT'];
	$name = html_entity_decode($_POST['NAME']);
	$sku = $_POST['SKU'];
	$desc_short = $_POST['DESC_SHORT'];
	$desc_long = $_POST['DESC_LONG'];
	$desc_trade = $_POST['DESC_TRADE'];
	$weight = $_POST['WEIGHT'];
	$quantity = $_POST['QUANTITY'];
	$selling = $_POST['SELLING'];
	$trade = $_POST['TRADE'];
	$tax = $_POST['TAX'];
	if (isset($_POST['TAXEXEMPTION']) and $_POST['TAXEXEMPTION'] == 1){$taxexemption = "Y";}else{$taxexemption = "N";}
	$shipping = $_POST['SHIPPING'];
	if (isset($_POST['SHIPPING_APPLY']) and $_POST['SHIPPING_APPLY'] == 1){$shipping_apply = "T";}else{$shipping_apply = "F";}
	if (isset($_POST['DISABLE_PRODUCT']) and $_POST['DISABLE_PRODUCT'] == 1){$disable_product = "Y";}else{$disable_product = "N";}
	$option1 = $_POST['OPTION1'];
	$option2 = $_POST['OPTION2'];
	$option3 = $_POST['OPTION3'];
	$option4 = $_POST['OPTION4'];
	$user_string1 = $_POST['USER_STRING1'];
	$no_stock = $_POST['NO_STOCK'];
	$google_cat = $_POST['GOOGLE_CAT'];
	$availability = $_POST['AVAILABILITY'];
	$condition = $_POST['CONDITION'];
	$google_brand = $_POST['GOOGLE_BRAND'];
	$google_gtin = $_POST['GOOGLE_GTIN'];
	$google_mpn = $_POST['GOOGLE_MPN'];
	$google_adwords_grouping = $_POST['GOOGLE_ADWORDS_GROUPING'];
	$google_adwords_labels = $_POST['GOOGLE_ADWORDS_LABELS'];
	$google_adwords_redirect = $_POST['GOOGLE_ADWORDS_REDIRECT'];
	$meta_title = $_POST['META_TITLE'];
	$meta_desc = $_POST['META_DESC'];
	$meta_keywords = $_POST['META_KEYWORDS'];
	$custom_head = $_POST['CUSTOM_HEAD'];
	$prod_wrap = $_POST['PROD_WRAP'];
	$date_amended = $_POST['DATE_LAST_UPDATED'];
	$date_added = $_POST['DATE_ADDED'];
	$hotspot1 = $_POST['HOTSPOT1'];
	$hotspot2 = $_POST['HOTSPOT2'];
	$hotspot3 = $_POST['HOTSPOT3'];
	if (isset($_POST['RESTORE_IMAGE_ADD1']) or isset($_POST['RESTORE_IMAGE_ADD2'])){$scrolltobottom = "onLoad=\"scrollTo(0,9000)\" ";}
}

if (isset($_POST['RESTORE_IMAGE']) or $rest > 0) {
	if (isset($_POST['RESTORE_IMAGE'])){
		$product = getProductDetails($_POST['PRODUCT'], "");
		$image_name = $product->PR_IMAGE;
		$image_folder = $product->PR_IMAGE_FOLDER;
		$image_alt = $product->PR_IMAGE_ALT;
		for($i = 1; $i <= $no_addl_images; $i++){
			${"image_name_add" . $i} = $_POST['IMAGE_NAME_ADD' . $i];
			${"image_folder_add" . $i} = $_POST['IMAGE_FOLDER_ADD' . $i];
			${"image_alt_add" . $i} = $_POST['IMAGE_ALT_ADD' . $i];
		}
	}
	
	if(isset($_POST['RESTORE_IMAGE_ADD' . $rest])){
		$image_name = $_POST['IMAGE_NAME'];
		$image_folder = $_POST['IMAGE_FOLDER'];
		$image_alt = $_POST['IMAGE_ALT'];
		//get additional images
		$additional = getAdditionalImages($_POST['PRODUCT']);
		foreach($additional as $a){
			// at this point we are "hardcoded" to allow only 2 additional images
			//Note that within prodadd PRA_POSITION is actially the screen sdditional image number + 1 ie. additional image no. 1 has a value of PRA_POSITION = 2
			//Think of the main image as position 1 and additional images follow on to that
			if ($a->PRA_POSITION == ($rest + 1)){
				${"image_name_add" . $rest} = $a->PRA_IMAGE;
				${"image_folder_add" . $rest} = $a->PRA_IMAGE_FOLDER;
				${"image_alt_add" . $rest} = $a->PRA_IMAGE_ALT;
			}
		}
		//"hardcode" to 7 additional images for now
		for($i = 1; $i <= $no_addl_images; $i++){
			if($i != $rest){
				${"image_name_add" . $i} = $_POST['IMAGE_NAME_ADD' . $i];
				${"image_folder_add" . $i} = $_POST['IMAGE_FOLDER_ADD' . $i];
				${"image_alt_add" . $i} = $_POST['IMAGE_ALT_ADD' . $i];
			}
		}
		$scroll_to = "additional_image_" . $rest;
	}
	//now refresh the product settings made so far
	$selected_product = $_POST['SELECTED_PRODUCT'];;
	$productcode = $_POST['PRODUCT'];
	$name = $_POST['NAME'];
	$sku = $_POST['SKU'];
	$desc_short = $_POST['DESC_SHORT'];
	$desc_long = $_POST['DESC_LONG'];
	$desc_trade = $_POST['DESC_TRADE'];
	$weight = $_POST['WEIGHT'];
	$quantity = $_POST['QUANTITY'];
	$selling = $_POST['SELLING'];
	$trade = $_POST['TRADE'];
	$tax = $_POST['TAX'];
	if (isset($_POST['TAXEXEMPTION']) and $_POST['TAXEXEMPTION'] == 1){$taxexemption = "Y";}else{$taxexemption = "N";}
	$shipping = $_POST['SHIPPING'];
	if (isset($_POST['SHIPPING_APPLY']) and $_POST['SHIPPING_APPLY'] == 1){$shipping_apply = "T";}else{$shipping_apply = "F";}
	if (isset($_POST['DISABLE_PRODUCT']) and $_POST['DISABLE_PRODUCT'] == 1){$disable_product = "Y";}else{$disable_product = "N";}
	$option1 = $_POST['OPTION1'];
	$option2 = $_POST['OPTION2'];
	$option3 = $_POST['OPTION3'];
	$option4 = $_POST['OPTION4'];
	$user_string1 = $_POST['USER_STRING1'];
	$no_stock = $_POST['NO_STOCK'];
	$google_cat = $_POST['GOOGLE_CAT'];
	$availability = $_POST['AVAILABILITY'];
	$condition = $_POST['CONDITION'];
	$google_brand = $_POST['GOOGLE_BRAND'];
	$google_gtin = $_POST['GOOGLE_GTIN'];
	$google_mpn = $_POST['GOOGLE_MPN'];
	$google_adwords_grouping = $_POST['GOOGLE_ADWORDS_GROUPING'];
	$google_adwords_labels = $_POST['GOOGLE_ADWORDS_LABELS'];
	$google_adwords_redirect = $_POST['GOOGLE_ADWORDS_REDIRECT'];
	$meta_title = $_POST['META_TITLE'];
	$meta_desc = $_POST['META_DESC'];
	$meta_keywords = $_POST['META_KEYWORDS'];
	$custom_head = $_POST['CUSTOM_HEAD'];
	$prod_wrap = $_POST['PROD_WRAP'];
	$date_amended = $_POST['DATE_LAST_UPDATED'];
	$date_added = $_POST['DATE_ADDED'];
	$hotspot1 = $_POST['HOTSPOT1'];
	$hotspot2 = $_POST['HOTSPOT2'];
	$hotspot3 = $_POST['HOTSPOT3'];
}

//check if an additional image upload button has been hit
$addl = 0;
for($i = 1; $i <= 7; $i++){
	if(isset($_POST['UPLOAD_IMAGE_ADD' . $i])){
		$addl = $i;
		break;
	}
}
if (isset($_POST['UPLOAD_IMAGE']) or $addl > 0) {
	//first thing to do is to re-extract the image_folder which may well have been changed via the input field FULL_PATH.
	//NOTE this is the only way the user may amend the IMAGE_FOLDER since by definition he must be changing the image at
	//the same time otherwise the link won't work anymore.
	//check image folder exists before proceeding
	$image_folder = ""; 
	$image_folder_add1 = ""; $image_folder_add2 = ""; $image_folder_add3 = ""; $image_folder_add4 = ""; $image_folder_add5 = "";
	$image_folder_add6 = ""; $image_folder_add7 = "";
	$OK = 1;
	if (isset($_POST['UPLOAD_IMAGE'])){
		$image_folder = substr($_POST['FULL_PATH'], strlen($pathName));
		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['FULL_PATH'])){
			$message_upload = "Upload folder does NOT exist!!!" . "<br/>";
			$warning = "red";
			$OK = 0;	
		}
	}
	
	if (isset($_POST['UPLOAD_IMAGE_ADD' . $addl])){
		${"image_folder_add" . $addl} = substr($_POST['FULL_PATH_ADD' . $addl], strlen($pathName));
		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['FULL_PATH_ADD' . $addl])){
			${"message_upload_add" . $addl} = "Upload folder does NOT exist!!!" . "<br/>";
			$warning = "red";
			$OK = 0;	
		}
	}
	//echo "<pre>";
	//print_r($_FILES['FILE_UPLOAD']);
	//echo "</pre>";
	//echo "<hr/>";
	if($OK == 1){
		$upload_errors = array(
			UPLOAD_ERR_OK => "No Errors",
			UPLOAD_ERR_INI_SIZE => "UPLOAD FAILURE!!! - Image larger than upload maximum file size",
			UPLOAD_ERR_FORM_SIZE => "UPLOAD FAILURE!!! - Image larger than form maximum file size",
			UPLOAD_ERR_PARTIAL => "UPLOAD FAILURE!!! - Partial Upload Only",
			UPLOAD_ERR_NO_FILE => "UPLOAD FAILURE!!! - No File specified to upload",
			UPLOAD_ERR_NO_TMP_DIR => "UPLOAD FAILURE!!! - No temporary directory",
			UPLOAD_ERR_CANT_WRITE => "UPLOAD FAILURE!!! - Can't write to disk",
			UPLOAD_ERR_EXTENSION => "UPLOAD FAILURE!!! - File upload stopped by extension");
	
		if (isset($_POST['UPLOAD_IMAGE'])){
			$error = $_FILES['FILE_UPLOAD']['error'];
			$message_upload = $upload_errors[$error];
			if ($error == 0){$warning = "green";} else {$warning = "red"; $OK = 0;}
		}
		if (isset($_POST['UPLOAD_IMAGE_ADD' . $addl])){
			$error = $_FILES['FILE_UPLOAD_ADD' . $addl]['error'];
			${"message_upload_add" . $addl} = $upload_errors[$error];
			if ($error == 0){$warning = "green";} else {$warning = "red"; $OK = 0;}
			$scroll_to = "additional_image_" . $addl;
		}

		if($OK == 1){
			//Upload file
			//The user will have been asked to upload an image of equal width/height ratio and of at least 400 x 400px.
			//For the product zoom display to work for a product we require 2 images 
			//1.) a lower res 400 x 400px picture.jpg to be used for main image + thumbnails and 
			//2.) a larger res <400 x 400px picturebig.jpg to be used actual size within the image zoom feature
			//The user simply uploads a single image.
			//If this image > 400 x 400px then
			//The uploaded file is renamed picturebig.jpg and a low res 400 x 400px picture.jpg will be generated from it giving us the 2 files.
			//If the uploaded image <= 400 x 400px then no renaming/resizing will occur and only the uploaded file picture.jpg will exist.
			//If no picturebig.jpg exists then the image zoom plugin simply uses the picture.jpg file and no zooming will take place.
			
			if (isset($_POST['UPLOAD_IMAGE'])){
				$tmp_name = $_FILES['FILE_UPLOAD']['tmp_name'];
				$target_file = basename($_FILES['FILE_UPLOAD']['name']);
				$upload_file_t = "../images/" . (strlen($image_folder) > 0 ? $image_folder . "/" : "")  . $target_file;
				$message_upload = Upload_File($tmp_name, $upload_file_t);
				if($message_upload == "File Uploaded Successfully"){
					//get image details and if width = height and width > 400px rename to picturebig.jpg and create low res picture.jpg
					$message_big = Create_imagebig($upload_file_t);
					if($message_big != ""){$message_upload = $message_big; $warning = "red";}
				}else{
					$warning = "red";
				}
			}
			if (isset($_POST['UPLOAD_IMAGE_ADD' . $addl])){
				${"tmp_name_add" . $addl} = $_FILES['FILE_UPLOAD_ADD' . $addl]['tmp_name'];
				${"target_file_add" . $addl} = basename($_FILES['FILE_UPLOAD_ADD' . $addl]['name']);
				${"upload_file_t_add" . $addl} = "../images/" . (strlen(${"image_folder_add" . $addl}) > 0 ? ${"image_folder_add" . $addl} . "/" : "")  . ${"target_file_add" . $addl};
				//echo "TEMP = " . $tmp_name . " / TARGET = " . $upload_file_t;
				${"message_upload_add" . $addl} = Upload_File(${"tmp_name_add" . $addl}, ${"upload_file_t_add" . $addl});
				if(${"message_upload_add" . $addl} == "File Uploaded Successfully"){
					//get image details and if width = height and width > 400px rename to picturebig.jpg and create low res picture.jpg
					$warning = "green";
					$message_big = Create_imagebig(${"upload_file_t_add" . $addl});
					if($message_big != ""){${"message_upload_add" . $addl} = $message_big; $warning = "red";}
				}else{
					$warning = "red";
				}
				$scroll_to = "additional_image_" . $addl;
			}
		}
	}
	//refresh page with new details
	$selected_product = $_POST['SELECTED_PRODUCT'];
	$image_name = $_POST['IMAGE_NAME'];
	$image_folder = $_POST['IMAGE_FOLDER'];
	$image_alt = $_POST['IMAGE_ALT'];
	$productcode = $_POST['PRODUCT'];
	$name = $_POST['NAME'];
	$sku = $_POST['SKU'];
	$desc_short = $_POST['DESC_SHORT'];
	$desc_long = $_POST['DESC_LONG'];
	$desc_trade = $_POST['DESC_TRADE'];
	$weight = $_POST['WEIGHT'];
	$quantity = $_POST['QUANTITY'];
	$selling = $_POST['SELLING'];
	$trade = $_POST['TRADE'];
	$tax = $_POST['TAX'];
	if (isset($_POST['TAXEXEMPTION']) and $_POST['TAXEXEMPTION'] == 1){$taxexemption = "Y";}else{$taxexemption = "N";}
	$shipping = $_POST['SHIPPING'];
	if (isset($_POST['SHIPPING_APPLY']) and $_POST['SHIPPING_APPLY'] == 1){$shipping_apply = "T";}else{$shipping_apply = "F";}
	if (isset($_POST['DISABLE_PRODUCT']) and $_POST['DISABLE_PRODUCT'] == 1){$disable_product = "Y";}else{$disable_product = "N";}
	$option1 = $_POST['OPTION1'];
	$option2 = $_POST['OPTION2'];
	$option3 = $_POST['OPTION3'];
	$option4 = $_POST['OPTION4'];
	$user_string1 = $_POST['USER_STRING1'];
	$no_stock = $_POST['NO_STOCK'];
	$google_cat = $_POST['GOOGLE_CAT'];
	$availability = $_POST['AVAILABILITY'];
	$condition = $_POST['CONDITION'];
	$google_brand = $_POST['GOOGLE_BRAND'];
	$google_gtin = $_POST['GOOGLE_GTIN'];
	$google_mpn = $_POST['GOOGLE_MPN'];
	$google_adwords_grouping = $_POST['GOOGLE_ADWORDS_GROUPING'];
	$google_adwords_labels = $_POST['GOOGLE_ADWORDS_LABELS'];
	$google_adwords_redirect = $_POST['GOOGLE_ADWORDS_REDIRECT'];
	$meta_title = $_POST['META_TITLE'];
	$meta_desc = $_POST['META_DESC'];
	$meta_keywords = $_POST['META_KEYWORDS'];
	$custom_head = $_POST['CUSTOM_HEAD'];
	$prod_wrap = $_POST['PROD_WRAP'];
	$date_amended = $_POST['DATE_LAST_UPDATED'];	
	$date_added = $_POST['DATE_ADDED'];	
	//additional images
	for($i = 1; $i <= $no_addl_images; $i++){
		${"image_name_add" . $i} = $_POST['IMAGE_NAME_ADD' . $i];
		${"image_folder_add" . $i} = $_POST['IMAGE_FOLDER_ADD' . $i];
		${"image_alt_add" . $i} = $_POST['IMAGE_ALT_ADD' . $i];
	}
	//get hotspots
	$hotspot1 = $_POST['HOTSPOT1'];
	$hotspot2 = $_POST['HOTSPOT2'];
	$hotspot3 = $_POST['HOTSPOT3'];
	if (isset($_POST['UPLOAD_IMAGE_ADD1']) or isset($_POST['UPLOAD_IMAGE_ADD2'])){$scrolltobottom = "onLoad=\"scrollTo(0,9000)\" ";}
}

if (isset($_POST['UPDATE']) and $_POST['SELECTED_PRODUCT'] != "") {
	$message = "";
	//validate all fields first
	if (strlen($_POST['NAME']) == 0){
		$message = "Please enter a valid Product Name" . "<br/>";
		$warning = "red";
	}
	
	$pos1 = strpos($_POST['NAME'], "/");
	$pos2 = strpos($_POST['NAME'], "\"");
	$pos3 = strpos($_POST['NAME'], "'");
	if ($pos1 != 0 | $pos2 != 0){
		if ($pos1 != 0){
			$message .= "Invalid character(" . "/" . ") found within Product Name" . "<br/>";
			$warning = "red";
		}
		if ($pos2 != 0){
			$message .= "Invalid character(Double Quotes) found within Product Name" . "<br/>";
			$warning = "red";
		}
		if ($pos3 != 0){
			$message .= "Invalid character(Single Quote) found within Product Name" . "<br/>";
			$warning = "red";
		}
	}
	$pos1 = strpos($_POST['SKU'], "/");
	$pos2 = strpos($_POST['SKU'], "\"");
	$pos3 = strpos($_POST['SKU'], "'");
	if ($pos1 != 0 | $pos2 != 0){
		if ($pos1 != 0){
			$message .= "Invalid character(" . "/" . ") found within SKU Code" . "<br/>";
			$warning = "red";
		}
		if ($pos2 != 0){
			$message .= "Invalid character(Double Quotes) found within SKU Code" . "<br/>";
			$warning = "red";
		}
		if ($pos3 != 0){
			$message .= "Invalid character(Single Quote) found within SKU Code" . "<br/>";
			$warning = "red";
		}
	}
	if ($_POST['WEIGHT'] != intval($_POST['WEIGHT']) or strlen($_POST['WEIGHT']) != strlen(intval($_POST['WEIGHT']))){
		$message .= "Please enter Weight as an integer in kg" . "<br/>";
		$warning = "red";
	}
		if ($_POST['QUANTITY'] != intval($_POST['QUANTITY']) or strlen($_POST['QUANTITY']) != strlen(intval($_POST['QUANTITY']))){
		$message .= "Please enter Quantity as an integer" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['SELLING']) > 0 and validate2dp($_POST['SELLING']) == "false"){
		$message .= "Please enter a valid Selling Price to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['TRADE']) > 0 and validate2dp($_POST['TRADE']) == "false"){
		$message .= "Please enter a valid Trade Price to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['TAX']) > 0 and validate2dp($_POST['TAX']) == "false"){
		$message .= "Please enter a valid Tax Rate to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['SHIPPING']) > 0 and validate2dp($_POST['SHIPPING']) == "false"){
		$message .= "Please enter a valid Shipping Price to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['GOOGLE_GTIN']) > 0 and (strlen($_POST['GOOGLE_GTIN']) != 8 and strlen($_POST['GOOGLE_GTIN']) != 12 and strlen($_POST['GOOGLE_GTIN']) != 13)){
		$message .= "Please enter a valid length Google GTIN number - 8, 12 or 13 digits" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['GOOGLE_MPN']) > 0 and !preg_match("~^[-a-z-A-Z0-9]+$~", $_POST['GOOGLE_MPN'])){
		$message .= "Please enter an alphanumeric Google MPN number" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['GOOGLE_ADWORDS_GROUPING']) > 0 and !preg_match("~^[-a-z-A-Z0-9]+$~", $_POST['GOOGLE_ADWORDS_GROUPING'])){
		$message .= "Please enter an alphanumeric Google Adwords Grouping filter" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['GOOGLE_ADWORDS_LABELS']) > 0 and !preg_match("~^[-a-z-A-Z0-9,\ ]+$~", $_POST['GOOGLE_ADWORDS_LABELS'])){
		$message .= "Please enter a valid Google Adwords Labels filter - only alphanumeric characters + commas allowed" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['GOOGLE_ADWORDS_REDIRECT']) > 0){
        if(!preg_match("~^[-a-z-A-Z0-9,.%+_:?=&/\\\ ]+$~", $_POST['GOOGLE_ADWORDS_REDIRECT'])){
			$message .= "Please enter a valid Google Adwords Redirect link - invalid characters entered" . "<br/>";
			$warning = "red";
		}
		$shop_url = $preferences->PREF_SHOPURL; $len_shop_url = strlen($shop_url);
		if(substr($_POST['GOOGLE_ADWORDS_REDIRECT'], 0, $len_shop_url) != $shop_url){
			$message .= "Please enter a valid Google Adwords Redirect link - the URL must redirect to the current shop website" . "<br/>";
			$warning = "red";
		}
	}
	if ($message == ""){
		//no error message so update database product table
		if($option1 == "#"){$option1 = "";}
		if($option2 == "#"){$option2 = "";}
		if($option3 == "#"){$option3 = "";}
		if($option4 == "#"){$option4 = "";}	
			
		if(isset($_POST['TAXEXEMPTION']) and $_POST['TAXEXEMPTION'] == 1){$taxexemption = "Y";}else{$taxexemption = "N";}
		if(isset($_POST['SHIPPING_APPLY']) and $_POST['SHIPPING_APPLY'] == 1){$shipping_apply = "T";}else{$shipping_apply = "N";}
		if(isset($_POST['DISABLE_PRODUCT']) and $_POST['DISABLE_PRODUCT'] == 1){$disable_product = "Y";}else{$disable_product = "N";}
		if(isset($_POST['GOOGLE_ADWORDS_REDIRECT'])){$product_link = $_POST['GOOGLE_ADWORDS_REDIRECT'];}else{$product_link = "";}
		if($product_link == ""){
			//this field should always contain a link unless it's just been created and not yet on the tree
			$product = getProductDetails($_POST['PRODUCT'], "");
			$prodcat = GetProductFromProdcat($product->PR_PRODUCT);
			if(!empty($prodcat)){
				foreach($prodcat as $pc){
					//product may appear in > 1 category so just get the first occurence
					break;
				}
				$product_link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $pc->PC_TREE_NODE . "/" . $product->PR_PRODUCT . ".htm";
			}
		}
		$fields = array("pr_product"=>$_POST['PRODUCT'], "pr_name"=>$_POST['NAME'], "pr_sku"=>$_POST['SKU'], "pr_desc_short"=>$_POST['DESC_SHORT'], "pr_desc_long"=>$_POST['DESC_LONG'],
						"pr_image"=>$_POST['IMAGE_NAME'], "pr_image_folder"=>$_POST['IMAGE_FOLDER'], "pr_image_alt"=>$_POST['IMAGE_ALT'],
						"pr_desc_trade"=>$_POST['DESC_TRADE'], "pr_weight"=>$_POST['WEIGHT'], "pr_quantity"=>$_POST['QUANTITY'],
						"pr_selling"=>$_POST['SELLING'], "pr_trade"=>$_POST['TRADE'], "pr_tax"=>$_POST['TAX'],
						"pr_shipping"=>$_POST['SHIPPING'], "pr_taxexemption"=>$taxexemption, "pr_shipping_apply"=>$shipping_apply, "pr_disable"=>$disable_product,
						"pr_option1"=>$option1, "pr_option2"=>$option2, "pr_option3"=>$option3,
						"pr_option4"=>$option4, "pr_meta_title"=>$_POST['META_TITLE'], "pr_meta_desc"=>$_POST['META_DESC'],
						"pr_no_stock"=>$_POST['NO_STOCK'], "pr_google_cat"=>$_POST['GOOGLE_CAT'], "pr_availability"=>$_POST['AVAILABILITY'], "pr_user_string1"=>$_POST['USER_STRING1'],
						"pr_google_brand"=>$_POST['GOOGLE_BRAND'], "pr_google_gtin"=>$_POST['GOOGLE_GTIN'], "pr_google_mpn"=>$_POST['GOOGLE_MPN'], "pr_google_condition"=>$_POST['CONDITION'],
						"pr_google_adwords_grouping"=>$_POST['GOOGLE_ADWORDS_GROUPING'], "pr_google_adwords_labels"=>$_POST['GOOGLE_ADWORDS_LABELS'], "pr_google_adwords_redirect"=>$product_link,
						"pr_meta_keywords"=>$_POST['META_KEYWORDS'], "pr_custom_head"=>$_POST['CUSTOM_HEAD'], "pr_prod_wrap"=>$_POST['PROD_WRAP']);
		$rows = Rewrite_Product($fields);
		$rows_prodadd = 0;
		if ($rows == 1){
			$errors_array[] = "Product record successfully UPDATED";
			$warning = "green";
		}
		if ($rows == 0){
			$errors_array[] = "WARNING ! ! ! - NO RECORDS UPDATED";
			$warning = "orange";
		}
		if ($rows > 1){
			$errors_array[] = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
			$warning = "red";
		}
		
		
		//update PRODADD file with additional images
		//always update prodadd regardless of whether there is currently an image in any one field - an existing image may well have been removed
		$message_prodadd = ""; $rows_prodadd = 0;
		$fields = array("pra_product"=>$_POST['PRODUCT'], "no_addl_images"=>$no_addl_images);
		for($i = 1; $i <= $no_addl_images; $i ++){
			$fields["position_add" . $i] = $i + 1;
			$fields["image_name_add" . $i] = $_POST['IMAGE_NAME_ADD' . $i];
			$fields["image_folder_add" . $i] = $_POST['IMAGE_FOLDER_ADD' . $i];
			$fields["image_alt_add" . $i] = $_POST['IMAGE_ALT_ADD' . $i];
		}
		$fields = Update_Prodadd_multi($fields);
		$rows_prodadd = $fields['rows'];
		$message_prodadd = $fields['message'];
		if($message_prodadd != ""){
			$errors_array[] = $message_prodadd;
			$warning = "red";
		}
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$error_array[] = " - ADDITIONAL IMAGE UPDATE ERRORS FOUND ! ! ! - " . mysql_error() . " ";
			$warning = "red";
		}

		//update HOTSPOTS file with additional images
		//update HOTSPOT 1
		if($_POST['HOTSPOT1'] != "" or $_POST['HOTSPOT2'] != "" or $_POST['HOTSPOT3'] != ""){
			$rows_hotspots = 0; $message_hotspots = "";
			$fields = array("hs_code"=>$_POST['PRODUCT'], "hs_number"=>1, "hs_data"=>$_POST['HOTSPOT1']);
			//echo "CODE = " . $fields['hs_code'];
			$ok = checkHotspotExists($_POST['PRODUCT'], "1");
			if($ok == "false"){
				if(strlen($_POST['HOTSPOT1']) > 0){
					$rows_hotspots = Create_Hotspot($fields);
					$rows_written = $rows_hotspots;
				}
			}else{
				$rows_hotspots = Rewrite_Hotspot($fields);
				$rows_rewritten = $rows_hotspots;
			}
			//update HOTSPOT 2
			$fields = array("hs_code"=>$_POST['PRODUCT'], "hs_number"=>2, "hs_data"=>$_POST['HOTSPOT2']);
			$ok = checkHotspotExists($_POST['PRODUCT'], 2);
			if($ok == "false"){
				if(strlen($_POST['HOTSPOT2']) > 0){
					$rows_hotspots = Create_Hotspot($fields);
					$rows_written = $rows_written + $rows_hotspots;
				}
			}else{
				$rows_hotspots = Rewrite_Hotspot($fields);
				$rows_rewritten = $rows_rewritten + $rows_hotspots;
			}
			//update HOTSPOT 3
			$fields = array("hs_code"=>$_POST['PRODUCT'], "hs_number"=>3, "hs_data"=>$_POST['HOTSPOT3']);
			$ok = checkHotspotExists($_POST['PRODUCT'], 3);
			if($ok == "false"){
				if(strlen($_POST['HOTSPOT3']) > 0){
					$rows_hotspots = Create_Hotspot($fields);
					$rows_written = $rows_written + $rows_hotspots;
				}
			}else{
				$rows_hotspots = Rewrite_Hotspot($fields);
				$rows_rewritten = $rows_rewritten + $rows_hotspots;
			}
			$error = null;
			$error = mysql_error();
			if ($error != null) { 
				$errors_array[] = " - HOTSPOTS UPDATE ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
				$warning = "red";
			}
		}
	}
	//refresh page with new details
	$product = getProductDetails($_POST['PRODUCT'], "");
	$selected_product = $product->PR_PRODUCT;
	$productcode = $product->PR_PRODUCT;
	$name = html_entity_decode($product->PR_NAME, ENT_QUOTES);
	$sku = $product->PR_SKU;
	$desc_short = html_entity_decode($product->PR_DESC_SHORT, ENT_QUOTES);
	$desc_long = html_entity_decode($product->PR_DESC_LONG, ENT_QUOTES);
	$desc_trade = html_entity_decode($product->PR_DESC_TRADE, ENT_QUOTES);
	$weight = $product->PR_WEIGHT;
	$quantity = $product->PR_QUANTITY;
	$selling = $product->PR_SELLING;
	$trade = $product->PR_TRADE;
	$tax = $product->PR_TAX;
	$taxexemption = $product->PR_TAXEXEMPTION;
	$shipping = $product->PR_SHIPPING;
	$shipping_apply = $product->PR_SHIPPING_APPLY;
	$disable_product = $product->PR_DISABLE;
	$option1 = $product->PR_OPTION1;
	$option2 = $product->PR_OPTION2;
	$option3 = $product->PR_OPTION3;
	$option4 = $product->PR_OPTION4;
	$user_string1 = html_entity_decode($product->PR_USER_STRING1, ENT_QUOTES);
	$no_stock = html_entity_decode($product->PR_NO_STOCK, ENT_QUOTES);
	$google_cat = $product->PR_GOOGLE_CAT;
	$availability = $product->PR_AVAILABILITY;
	$condition = $product->PR_GOOGLE_CONDITION;
	$google_brand = $product->PR_GOOGLE_BRAND;
	$google_gtin = $product->PR_GOOGLE_GTIN;
	$google_mpn = $product->PR_GOOGLE_MPN;
	$google_adwords_grouping = $product->PR_GOOGLE_ADWORDS_GROUPING;
	$google_adwords_labels = $product->PR_GOOGLE_ADWORDS_LABELS;
	$google_adwords_redirect = $product->PR_GOOGLE_ADWORDS_REDIRECT;
	$meta_title = html_entity_decode($product->PR_META_TITLE, ENT_QUOTES);
	$meta_desc = html_entity_decode($product->PR_META_DESC, ENT_QUOTES);
	$meta_keywords = html_entity_decode($product->PR_META_KEYWORDS, ENT_QUOTES);
	$custom_head = html_entity_decode($product->PR_CUSTOM_HEAD, ENT_QUOTES);
	$prod_wrap = $product->PR_PROD_WRAP;
	$date_amended = $product->PR_LAST_UPDATED;
	$date_added = $product->PR_DATE_ADDED;
	$image_name = $product->PR_IMAGE;
	$image_folder = $product->PR_IMAGE_FOLDER;
	$image_alt = html_entity_decode($product->PR_IMAGE_ALT, ENT_QUOTES);
	
	//get additional images
	$additional = getAdditionalImages($_POST['PRODUCT']);
	foreach($additional as $a){
		${"image_name_add" . ($a->PRA_POSITION - 1)} = $a->PRA_IMAGE;
		${"image_folder_add" . ($a->PRA_POSITION - 1)} = $a->PRA_IMAGE_FOLDER;
		${"image_alt_add" . ($a->PRA_POSITION - 1)} = $a->PRA_IMAGE_ALT;
	}
	//get hotspots
	$hotspots = getHotspots($_POST['PRODUCT']);
	$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";
	foreach($hotspots as $h){
		$hotspot = "hotspot" . $h->HS_NUMBER;
		$$hotspot = html_entity_decode($h->HS_DATA);
	}
	if($message != "" or count($errors_array) != 0){$scrolltobottom = "onLoad=\"scrollTo(0,9000)\" ";}
}

if($scroll_to != ""){
	$scrolltobottom = "onLoad=\"load('" . $scroll_to . "')\"";
}
include_once("includes/header_admin.php");

?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Amend Products - Update Product Details</h1>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
	<!--- SEARCHBOXES ------------------------------------------------------------------------------------------------------>
    <form name="enter_thumb" action="/_cms/amend_products.php" enctype="multipart/form-data" method="post">
    	<tr>
          <td class="product-td">Search for: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the product you want to work on<br /><br />To select from all products place the mouse cursor in the search field with no other text and click search<br /><br />Then select the desired product from the Choose from... dropdown box</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><Input name="SEARCH_DATA" type="text" size="72" value="<?php echo isset($_POST['SEARCH_DATA']) ? $_POST['SEARCH_DATA'] : "" ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          	    <Input name="SEARCH" type="submit" value="search" class="search-button" /></td>
    	</tr>
        <tr>
        	<td></td>
            <td>
            	<select name="search_results" id="jumpMenu" onchange="MM_jumpMenu('parent',this,1)" class="search-product-box" >
                	<option value="#">Choose from...</option>
                    <?php
					if (isset($_POST['SEARCH_DATA'])){
						$products = Search_product($_POST['SEARCH_DATA']);
						foreach($products as $p){
							if(isset($_POST['SELECTED_PRODUCT']) and $_POST['SELECTED_PRODUCT'] == $p->PR_PRODUCT){
								$selected = "selected";
							}else{
								$selected = "";
							}
							echo "<option value=\"/_cms/amend_products.php?searchdata=" . $_POST['SEARCH_DATA'] . "&searchproduct=" . $p->PR_PRODUCT . "\"" . $selected . ">" . $p->PR_PRODUCT . " - " . html_entity_decode($p->PR_NAME) . "</option>";
						}
					}
					?>
           		</select>
				<input type="hidden" name="SELECTED_PRODUCT" value="<?php echo (isset($selected_product) ? $selected_product : "") ?>">
                 <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the product you wish to work on; you need to have searched for it first</span><span class=\"bottom\"></span></span>" : "") ?></a>
            </td>
        </tr>
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
    <!--- END OF SEARCHBOXES ------------------------------------------------------------------------------------------>
    <!--- MAIN IMAGE HANDLING ---------------------------------------------------------------------------------------------->
		<tr>
        	<td>
            </td>
            <td>
            	<div class="p-display-picture">
				<?php
                //$pathName = "/images/";
                //if (isset($product->PR_IMAGE) and strlen($product->PR_IMAGE_FOLDER) > 0){ $pathName .= $product->PR_IMAGE_FOLDER . "/";}
                             
                if (isset($_POST['UPLOAD_IMAGE'])) {
					$image_folder = substr($_POST['FULL_PATH'], strlen($pathName));
					$image_name = $target_file;
					$image_alt = $target_file;
                    echo "<img name=\"IMAGE\" src=\"" . $pathName . (strlen($image_folder) > 0 ? $image_folder . "/" : "") . $target_file . "\" width=\"200\" alt=\"" . $target_file. "\" /><br/>";
                    //echo "<input name=\"IMAGE_NAME\" type=\"hidden\" size=\"28\" value=\"" . $target_file . "\" />";
                    echo "<label>" . $target_file . "</label>";
                }else{
					//$image_folder = (isset($product->PR_IMAGE_FOLDER) ? $product->PR_IMAGE_FOLDER : "");
					//$image_name = (isset($product->PR_IMAGE) ? $product->PR_IMAGE : "no-image.jpg");
					//$image_alt = (isset($product->PR_IMAGE_ALT) ? $product->PR_IMAGE_ALT : "");
					echo "<img name=\"IMAGE\" src=\"" . $pathName . (strlen($image_folder) > 0 ? $image_folder . "/" : "") . (strlen($image_name) > 0 ? $image_name : "no-image.jpg") . "\" width=\"200\" alt=\"" . (strlen($image_name) > 0 ? $image_name : "no-image.jpg") . "\" /><br/>";	
					//echo "<input name=\"IMAGE_NAME\" type=\"hidden\" size=\"28\" value=\"" . (isset($product->PR_IMAGE) ? $product->PR_IMAGE : "no-image.jpg") . "\" />";
					echo "<label>" . (strlen($image_name) > 0 ? $image_name : "no image") . "</label>";	
                }
                ?>
				</div>
				<div class="p-change-picture">
                    <label>Main Image - Name:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">When you've uploaded an image its filename will appear here</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_NAME_DISABLED" SIZE="49" disabled value="<?php echo htmlentities($image_name,ENT_QUOTES) ?>"><br/><br/>            
					<input type="hidden" name="IMAGE_NAME" value="<?php echo htmlentities($image_name,ENT_QUOTES) ?>">
                    <label>Main Image - Alternative Name:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a short text description of the image; if you don't then filename will be used<br /><br />Note that this is a legal requirement and is also indexed by search engines</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_ALT" SIZE="49" value="<?php echo $image_alt ?>"><br/><br/>                    
                    
                    <label>Main Image - Subfolder:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">If you've uploaded the image to a sub-folder this will show where the image is located</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_FOLDER_DISABLED" SIZE="49" disabled value="<?php echo $image_folder ?>"><br/><br/><br/>
                    <input type="hidden" name="IMAGE_FOLDER" value="<?php echo $image_folder ?>">
                    <Input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                    <label>Add Picture:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">To add an image click the Browse button and navigate to the image you want to use; then click the Upload button</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <Input name="FILE_UPLOAD" type="file" size="37" value="<?php echo $pathName . strlen($image_name) > 0 ? $image_name : "" ?>" onchange="Set_Picture(this.form.IMAGE_NAME, this.form.FILE_UPLOAD, this.form.IMAGE);"/><br/><br/>
                    <label>New Image will be uploaded to the following folder:</label><br /> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">If you want to upload images to a sub-folder within the images directory you can specify it here; format as shown: /sub-folder-name/</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="FULL_PATH" SIZE="49" value="<?php echo $pathName . $image_folder ?>"><br/><br/>
                    <Input name="UPLOAD_IMAGE" type="submit" value="Upload Image" class="upload-button" /> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Upload the image you've selected above</span><span class=\"bottom\"></span></span>" : "") ?></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <Input name="RESTORE_IMAGE" type="submit" value="Restore" class="restore-button" />&nbsp;&nbsp; <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Restore a previously added image if you've altered it but changed your mind<br /><br />Note that this will only work if you haven't clicked the Update button<br /><br />Remove the current image with the Remove button</span><span class=\"bottom\"></span></span>" : "") ?></a>
                    &nbsp;&nbsp;
                    <Input name="REMOVE_IMAGE" type="submit" value="Remove" class="remove-button" /><br/><br/>

                    <label class="<?php echo $warning ?>"><?php echo $message_upload ?></label>
                </div>

            </td> 
		</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
    	<!--- END OF IMAGE HANDLING --------------------------------------------------------------------------------------->    
        <tr>
           	<td>Product Code: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The product code assigned by the software</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><label><strong><?php echo strlen($productcode ) > 0 ? $productcode : "" ?></strong></label>
                 	<input type="hidden" name="PRODUCT" SIZE="50" value="<?php echo $productcode ?>">
        </td>
    	</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
				
        <tr>
            <td>Name: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Edit the name for the product (or service)<br /><br />This will be displayed in menus and URLs<br /><br />Warning: If you change the name the page URL will also change and any links in search engines will be incorrect; consider setting this item as Out of Stock with a message that points to the updated product page - remember to remove this from the menu<br /><br />It's best to use sentence case or all lower case for names; all capitals is less preferred due to an internet convention that words written in capitals are too forceful in the same way as a shout is in normal conversation</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="NAME" SIZE="50" value="<?php echo $name ?>"></td>
        </tr>
        <tr>
            <td>SKU Code: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Stock Keeping Unit: Enter a SKU code<br /><br />For items that have options the SKU code can be either an amalgamation of top level SKU (set here) and then option specific SKU elements<br /><br />Alternatively, leave this blank and add the specific SKU in the Options section</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="SKU" SIZE="15" value="<?php echo $sku ?>"></td>
        </tr>
        <tr>
            <td>Intro Description: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a brief introduction of the product to display on the category page<br /><br />
            This will also appear as the introduction on the product details page (except where it's been disabled in the template)</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="DESC_SHORT" SIZE="87" value="<?php echo $desc_short ?>"></td>
        </tr>
        <tr>
            <td>Member/Trade Intro: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a brief introduction that will only be seen by logged in members, this replaces the publicly seen intro</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="DESC_TRADE" SIZE="87" value="<?php echo $desc_trade ?>"></td>
        </tr>
        <!---
        <tr>
            <td>Long Description:</td>
                <td><textarea type="text" name="DESC_LONG" class="p-amend-textarea"><?php echo $desc_long ?></textarea></td>
        </tr>
        --->
        <tr>
			<td valign="top">
            	Long Description: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a detailed description of the product or service<br />
            	<br />This is also used in the Google Merchants/Shopping system so should be unique and not use text that is too sales like<br /><br />Harder selling text can be added in hotspots 1, 2 or 3</span><span class=\"bottom\"></span></span>" : "") ?></a></br></br>
				<div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/edit_textarea.php?form=enter_thumb&field=DESC_LONG","Ratting","width=1000,height=800,left=150,top=200,toolbar=1,status=1,");>
					<span>Edit</span>
				</a></div>
                <span style="float: right; position: relative; right:40px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
            </td>
			<td>
            	<textarea type="text" name="DESC_LONG" class="p-amendproduct-textarea"><?php echo $desc_long ?></textarea>
            </td>
		</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
            <td>Weight (kg): <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the weight of your item<br /><br />This is used for calculating delivery charges</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="WEIGHT" SIZE="10" value="<?php echo $weight ?>"></td>
        </tr>            
        <tr>
            <td>Default Quantity: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the quantity that shows in the Quantity box on the product description<br /><br />
            It's set to 1 by default but if you want 2 or more to be added when the customer adds the item to their cart enter the number you want here</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="QUANTITY" SIZE="10" value="<?php echo $quantity ?>"></td>
        </tr>             
        <tr>
        	<td>Selling Price: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the price you want to sell the item at excluding VAT/Tax<br /><br />
       	    VAT/Tax is calculated by the cart based on the percentage entered in either the Tax Rate box below</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="SELLING" SIZE="20" value="<?php echo $selling ?>"></td>
        </tr>
        <tr>
            <td>Member/Trade Price: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the discounted member or trade price you want to sell the item at excluding VAT/Tax</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="TRADE" SIZE="10" value="<?php echo $trade ?>"></td>
        </tr> 
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
				           
        <tr>
            <td>Tax Rate (%): <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the VAT/Tax rate applied to this product/service</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="TAX" SIZE="10" value="<?php echo $tax ?>"></td>
        </tr> 
        <tr>
			<td>Tax Exempt: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box if this product is eligible for VAT/Tax exemption</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <?php if($taxexemption == "Y"){$checked = "checked";}else{$checked = "";}?>
            <td><input type="checkbox" name="TAXEXEMPTION" value="1" <?php echo $checked ?>></td>
		</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
				        
        <tr>
            <td>Shipping Cost: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">If you want to specify a cost per item for delivery enter the price here<br /><br />Remember the format should be to 2 decimal places; eg if it costs &pound;/&euro;/$/&curren;3 to send this item enter 3.00</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="SHIPPING" SIZE="10" value="<?php echo $shipping ?>"></td>
        </tr>   
        <tr>
			<td>Apply Shipping: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box to enable per item and by weight delivery methods<br /><br />You'll also need to apply settings in the secure admin account</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <?php if($shipping_apply == "T"){$checked = "checked";}else{$checked = "";}?>
            <td><input type="checkbox" name="SHIPPING_APPLY" value="1" <?php echo $checked ?> ></td>
		</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>		
		
        <tr>
			<td>Set up Options: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"../images/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the link to set up options for this item</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><a href="options_product.php?searchdata=&searchproduct=<? echo $productcode ?>" >Click here to create or edit options for <?php echo $name ?></a></td>
		</tr>
		
        <tr>
        	<td>Option 1: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the options you want to apply to this product<br /><br />You'll need to set the options up first in the Options section from the menu on the left<br /><br />You can create general options that can be applied to all products or options available for only a specific product<br /><br />Up to 4 option sets can be added to each product</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td>
                <select name="OPTION1" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $selections = getAllSelectionsWithProduct($selected_product);
                    foreach($selections as $s){
                        if($option1 == $s->SE_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $s->SE_ID . "\" " . $selected . ">" . ($s->SE_PRODUCT != "GENERAL" ? "(" : "") . html_entity_decode($s->SE_NAME, ENT_QUOTES) . ($s->SE_PRODUCT != "GENERAL" ? ")" : "") . "</option>";
                    }
                    ?>
                </select>
                <!---
                starting point coding for edit button to swap out to options create pages
                &nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/options_general.php?form=amend_text&field=OPTION1","Ratting","width=1000,height=800,left=150,top=200,toolbar=1,status=1,");>
					<img src="/_cms/_assets/images/edit_button.jpg" alt="Edit Page Text"
						onmouseover="this.src='/_cms/_assets/images/edit_button_hover.jpg'"
						onmouseout="this.src='/_cms/_assets/images/edit_button.jpg'" align="absmiddle" />
				</a>
                --->
			</td>    
        </tr>
        <tr>
        	<td>Option 2:</td>
            <td>
                <select name="OPTION2" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $selections = getAllSelectionsWithProduct($selected_product);
                    foreach($selections as $s){
                        if($option2 == $s->SE_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $s->SE_ID . "\" " . $selected . ">" . ($s->SE_PRODUCT != "GENERAL" ? "(" : "") . html_entity_decode($s->SE_NAME, ENT_QUOTES) . ($s->SE_PRODUCT != "GENERAL" ? ")" : "") . "</option>";
                    }
                    ?>
                </select>
			</td>
        </tr>
        <tr>
        	<td>Option 3:</td>
            <td>
                <select name="OPTION3" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $selections = getAllSelectionsWithProduct($selected_product);
                    foreach($selections as $s){
                        if($option3 == $s->SE_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $s->SE_ID . "\" " . $selected . ">" . ($s->SE_PRODUCT != "GENERAL" ? "(" : "") . html_entity_decode($s->SE_NAME, ENT_QUOTES) . ($s->SE_PRODUCT != "GENERAL" ? ")" : "") . "</option>";
                    }
                    ?>
                </select>
			</td>
        </tr>
        <tr>
        	<td>Option 4:</td>
            <td>
                <select name="OPTION4" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $selections = getAllSelectionsWithProduct($selected_product);
                    foreach($selections as $s){
                        if($option4 == $s->SE_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $s->SE_ID . "\" " . $selected . ">" . ($s->SE_PRODUCT != "GENERAL" ? "(" : "") . html_entity_decode($s->SE_NAME, ENT_QUOTES) . ($s->SE_PRODUCT != "GENERAL" ? ")" : "") . "</option>";
                    }
                    ?>
                </select>
			</td>
        </tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
			<td>Related Products: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"../images/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the link to set up options for this item</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><a href="additional_products.php?searchdata_main=<? echo $productcode ?>&searchproduct_main=<? echo $productcode ?>" >Click here to create or edit related products for <?php echo $name ?></a></td>
		</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
            <td>User String 1: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">User String<br />
                    <br />
                    User string<br />
                    <br />
                    </span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="USER_STRING1" SIZE="15" value="<?php echo $user_string1 ?>"></td>
        </tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
			<td>Availability: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Set the availability of the product<br /><br />This attribute is also included in the feed to Google Merchants/Shopping<br /><br />When Out of Stock is selected the Out of Stock Message (below) is displayed and the buy now/add to cart function is disabled(</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
			<td>
				<select name="AVAILABILITY" onchange="">
					<option value="in stock" <?php echo($availability == "in stock" ? "selected" : "") ?>>In Stock</option>
					<option value="available for order" <?php echo ($availability == "available for order" ? "selected" : "") ?>>Available for Order</option>
					<option value="out of stock" <?php echo($availability == "out of stock" ? "selected" : "") ?>>Out of Stock</option>
					<option value="preorder" <?php echo($availability == "preorder" ? "selected" : "") ?>>Pre-Order</option>
				</select>
			</td>
		</tr>
        <tr>
        	<td>Out of Stock Message: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a message you'd like potential customers to see when this item is set to Out of Stock<br /><br />eg; \"New stock due in 5 days\" or \"No longer available, have a look at this alternative\"</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="NO_STOCK" SIZE="87" value="<?php echo $no_stock ?>"></td>
        </tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
			<td>Disable Product: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box to remove the product from all menu listings</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <?php if($disable_product == "Y"){$checked = "checked";}else{$checked = "";}
			?>
            <td><input type="checkbox" name="DISABLE_PRODUCT" value="1" <?php echo $checked ?> ></td>
		</tr>
        <?php
		if($preferences->PREF_GOOGLE_SEARCH == "Y"){
			echo "<tr>";
				echo "<td>Google Category: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert the Google Category data<br /><br />See Google's help page for guidance; find the link on the Google Merchants Setup in the top menu under Categorising Your Products</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td><input type=\"text\" name=\"GOOGLE_CAT\" SIZE=\"87\" value=\"" . $google_cat . "\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Google Brand: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert the product Brand name</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td><input type=\"text\" name=\"GOOGLE_BRAND\" SIZE=\"25\" value=\"" . $google_brand . "\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Google GTIN: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert the Global Trade Item Number (GTIN) data<br /><br />See Google's help page for guidance; find the link on the Google Merchants Setup in the top menu under Unique Product Identifiers help page.</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td><input type=\"text\" name=\"GOOGLE_GTIN\" SIZE=\"14\" value=\"" . $google_gtin . "\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Google MPN: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert the Manufacturers Part Number (MPN) data<br /><br />You should be able to get these from your suppliers, or if you produce your own items create it yourself<br /><br />See Google's help page for guidance; find the link on the Google Merchants Setup in the top menu under Unique Product Identifiers help page.</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td><input type=\"text\" name=\"GOOGLE_MPN\" SIZE=\"14\" value=\"" . $google_mpn . "\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Google Adwords Grouping: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert the Google Adwords Grouping filter</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td><input type=\"text\" name=\"GOOGLE_ADWORDS_GROUPING\" SIZE=\"25\" value=\"" . $google_adwords_grouping . "\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Google Adwords Labels: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert one or more Google Adwords Labels<br /><br />Any number of product filters may be added each separated by a comma<br /><br />See Google's help page for guidance; find the link on the Google Merchants Setup in the top menu under Unique Product Identifiers help page.</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td><textarea type=\"text\" name=\"GOOGLE_ADWORDS_LABELS\" class=\"p-amend-textarea\">{$google_adwords_labels}</textarea></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Google Adwords Redirect: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert the Google Adwords Redirect link<br /><br />By default this is set up as the page link to the product however this may be ameded as desired<br /><br />See Google's help page for guidance; find the link on the Google Merchants Setup in the top menu under Unique Product Identifiers help page.</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td><input type=\"text\" name=\"GOOGLE_ADWORDS_REDIRECT\" SIZE=\"105\" value=\"" . $google_adwords_redirect . "\"></td>";
			echo "</tr>";
			echo "<tr>";
        }
        ?>
        <tr>
			<td>Google Condition: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert the Google Product Condition<br/><br />See Google's help page for guidance; find the link on the Google Merchants Setup in the top menu under Categorising Your Products</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
			<td>
				<select name="CONDITION" onchange="">
					<option value="New" <?php echo($condition == "New" ? "selected" : "") ?>>New</option>
					<option value="Used" <?php echo ($condition == "Used" ? "selected" : "") ?>>Used</option>
					<option value="Refurbished" <?php echo($condition == "Refurbished" ? "selected" : "") ?>>Refurbished</option>
				</select>
			</td>
		</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
        	<td>META Title: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the main search terms for this product page; these should be specific to the page and are critical for SEO. <br /><br />Note that the Title tag (as it's known) is the most important place to include key words and phrases so that search engines pick them up.<br /><br />Approx 60 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="META_TITLE" SIZE="87" value="<?php echo $meta_title ?>"></td>
        </tr>       
        <tr>
        	<td>META Description: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a description of the content of this page as a properly formed sentence; each page should have a unique description to ensure the best search engine rankings <br /><br />Note that this description is used by search engines in their listings to describe the content of the pages they are listing so think of it as a way to convert searchers into visitors; remember to include key words and phrases so that search engines can match them to search requests by people<br /><br />Approx 150 to 200 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><textarea type="text" name="META_DESC" class="p-amend-textarea"><?php echo $meta_desc ?></textarea></td>
        </tr> 
        <tr>
        	<td>META Keywords: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Include 3 or 4 words or phrases from your meta title and meta description first; it's also a good place to put mis-spellings relevant to thie page content<br /><br />256 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><textarea type="text" name="META_KEYWORDS" class="p-amend-textarea"><?php echo $meta_keywords ?></textarea></td>
        </tr>    
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		 
        <tr>
        	<td>Custom Head: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Put code, scripts and microdata that needs be added into the \"head\" area of this page.<br /><br />Note that anything added to this area will only appear on this page</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><textarea type="text" name="CUSTOM_HEAD" class="p-amend-textarea"><?php echo $custom_head ?></textarea></td>
        </tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
            <td>Prod Wrapper: 
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">An advanced control that enables different styling on individual products: leave as originally set up</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="PROD_WRAP" SIZE="90" value="<?php echo $prod_wrap ?>"></td>
        </tr>
        <tr>
            <td>Last Updated: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The time stamp this product was last altered</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td>
            	<input type="text" name="DATE_LAST_UPDATED_DISABLED" SIZE="20" value="<?php echo $date_amended ?>" disabled>
                <input type="hidden" name="DATE_LAST_UPDATED" value="<?php echo $date_amended ?>" >
            </td>
        </tr>  
        <tr>
            <td>Date Created: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The time stamp this product was originally created</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td>
            	<input type="text" name="DATE_ADDED_DISABLED" SIZE="20" value="<?php echo $date_added ?>" disabled>
                <input type="hidden" name="DATE_ADDED" value="<?php echo $date_added ?>" >
            </td>
        </tr>        
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
        
        <!--- ADDITIONAL IMAGE HANDLING  ---------------------------------------------------------------------------------------------->
        <?php
		for($i = 1; $i <= $no_addl_images; $i++): ?>
            <tr>
                <td>
                </td>
                <td>
                    <div class="p-display-picture">
                    <a name="additional_image_<?php echo $i ?>"></a>
                    <?php
                                 
                    if (isset($_POST['UPLOAD_IMAGE_ADD' . $i])) {
                        ${"image_folder_add" . $i} = substr($_POST['FULL_PATH_ADD' . $i], strlen($pathName));
                        ${"image_name_add" . $i} = ${"target_file_add" . $i};
                        ${"image_alt_add" . $i} = ${"target_file_add" . $i};
                        echo "<img name=\"IMAGE_ADD" . $i . "\" src=\"" . $pathName . (strlen(${"image_folder_add" . $i}) > 0 ? ${"image_folder_add" . $i} . "/" : "") . ${"target_file_add" . $i} . "\" width=\"200\" alt=\"" . ${"target_file_add" . $i} . "\" /><br/>";
                        echo "<label>" . ${"target_file_add" . $i} . "</label>";
                    }else{
                        //$image_folder_add1 = (isset($imageFolderAdd1) ? $imageFolderAdd1 : "");
                        //$image_name_add1 = (isset($imageNameAdd1) ? $imageNameAdd1 : "no-image.jpg");
                        //$image_alt_add1 = (isset($imageAltAdd1) ? $imageAltAdd1 : "");
                        echo "<img name=\"IMAGE_ADD" . $i . "\" src=\"" . $pathName . (strlen(${"image_folder_add" . $i}) > 0 ? ${"image_folder_add" . $i} . "/" : "") . (strlen(${"image_name_add" . $i}) > 0 ? ${"image_name_add" . $i} : "no-image.jpg") . "\" width=\"200\" alt=\"" . (strlen(${"image_name_add" . $i}) > 0 ? ${"image_name_add" . $i} : "no-image.jpg") . "\" /><br/>";	
                        echo "<label>" . (strlen(${"image_name_add" . $i}) > 0 ? ${"image_name_add" . $i} : "no image") . "</label>";	
                    }
                    ?>
                    </div>
                    <div class="p-change-picture">
                        <label>Additional Image <?php echo $i ?> - Name:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Add a second image here using the same method as the main image</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                        <input type="text" name="IMAGE_NAME_ADD<?php echo $i ?>_DISABLED" SIZE="49" disabled value="<?php echo ${"image_name_add" . $i} ?>"><br/><br/>
                        <input type="hidden" name="IMAGE_NAME_ADD<?php echo $i ?>" value="<?php echo ${"image_name_add" . $i} ?>">
                        <label>Additional Image <?php echo $i ?> - Alternative Name:</label><br/>
                        <input type="text" name="IMAGE_ALT_ADD<?php echo $i ?>" SIZE="49" value="<?php echo ${"image_alt_add" . $i} ?>"><br/><br/>                    
                        
                        <label>Additional Image <?php echo $i ?> - Subfolder:</label><br/>
                        <input type="text" name="IMAGE_FOLDER_ADD<?php echo $i ?>_DISABLED" SIZE="49" disabled value="<?php echo ${"image_folder_add" . $i} ?>"><br/><br/><br/>
                        <input type="hidden" name="IMAGE_FOLDER_ADD<?php echo $i ?>" value="<?php echo ${"image_folder_add" . $i} ?>">
                        <Input type="hidden" name="MAX_FILE_SIZE_ADD<?php echo $i ?>" value="1000000" />
                        <label>Change Picture to:</label><br/>
                        <Input name="FILE_UPLOAD_ADD<?php echo $i ?>" type="file" size="37" value="<?php echo $pathName . ${"image_name_add" . $i} ?>" onchange="Set_Picture(this.form.IMAGE_NAME_ADD<?php echo $i ?>, this.form.FILE_UPLOAD_ADD<?php echo $i ?>, this.form.IMAGE_ADD<?php echo $i ?>);"/><br/><br/>
                        <label>New Image will be uploaded to the following folder:</label><br/>
                        <input type="text" name="FULL_PATH_ADD<?php echo $i ?>" SIZE="49" value="<?php echo $pathName . $image_folder_add1 ?>"><br/><br/>
                        <Input name="UPLOAD_IMAGE_ADD<?php echo $i ?>" type="submit" value="Upload Image" class="upload-button" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <Input name="RESTORE_IMAGE_ADD<?php echo $i ?>" type="submit" value="Restore" class="restore-button" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <Input name="REMOVE_IMAGE_ADD<?php echo $i ?>" type="submit" value="Remove" class="remove-button" /><br/><br/>
    
                        <label class="<?php echo $warning ?>"><?php echo ${"message_upload_add" . $i} ?></label>
                    </div>
    
                </td> 
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>   
        <?php endfor; ?>
    	<!--- END OF ADDITIONAL IMAGE HANDLING ---------------------------------------------------------------------------------------> 
        
        <!--- HOTSPOTS HANDLING -------------------------------------------------------------------------------------------------------->         
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
        	<td valign="top">Hotspot 1:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter text or other content that you want to appear below the title and breadcrumb but ABOVE the other product details (including the price and add to cart/buy now button<br /><br />Content can be text, images, scripts or other html<br /><br />Text added for SEO purposes is best entered in hotspot 3</span><span class=\"bottom\"></span></span>" : "") ?></a>
			
                <div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/edit_textarea.php?form=enter_thumb&field=HOTSPOT1","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");>
				<span>Edit</span>
                </a></div>
            	<span style="position: relative; left:70px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
			
			</td>
            <td><textarea type="text" id="HOTSPOT1" name="HOTSPOT1" class="p-amendhot-textarea"><?php echo $hotspot1 ?></textarea></td>
        </tr> 
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
        <tr>
        	<td valign="top">Hotspot 2:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter text or other content that you want to appear below the main long description area but ABOVE the other product elements such as the related items<br /><br />Content can be text, images, scripts or other html<br /><br />This is a good place to put video clips and scripts with active content</span><span class=\"bottom\"></span></span>" : "") ?></a>
			
            	<div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/edit_textarea.php?form=enter_thumb&field=HOTSPOT2","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");>
                    <span>Edit</span>
                </a></div>
				<span style="position: relative; left:70px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
			
			</td>
            <td><textarea type="text" id="HOTSPOT2" name="HOTSPOT2" class="p-amendhot-textarea"><?php echo $hotspot2 ?></textarea></td>
        </tr> 
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
        <tr>
        	<td valign="top">Hotspot 3:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter text or other content that you want to appear below everything else in the product details, including related items<br /><br />Content can be text, images, scripts or other html<br /><br />A gallery of images and text added for SEO purposes can work well here</span><span class=\"bottom\"></span></span>" : "") ?></a>
			
            	<div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/edit_textarea.php?form=enter_thumb&field=HOTSPOT3","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");>
                    <span>Edit</span>
                </a></div>
            	<span style="position: relative; left:70px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
			
			</td>
            <td><textarea type="text" id="HOTSPOT3" name="HOTSPOT3" class="p-amendhot-textarea"><?php echo $hotspot3 ?></textarea></td>
        </tr> 
        <!--- END OF HOTSPOTS HANDLING --------------------------------------------------------------------------------------->         
		<!--- UPDATE BUTTON ---------->
		<tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		<tr>
			<td></td>
			<td>
            	<input name="UPDATE" type="submit" value="Update Product&raquo;&raquo;" class="update-button"> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save your changes</span><span class=\"bottom\"></span></span>" : "") ?></a>
            	<input name="DELETE" type="submit" value="Delete Product &raquo;&raquo;" class="delete-button-left"> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to delete this product</span><span class=\"bottom\"></span></span>" : "") ?></a>
				<?php
				if($productcode != "") {$alink = "/preview/product/" . $productcode;}else{$alink = "";}
				?>
                <div class="preview-button"><a href="<?php echo $alink ?>" target="_blank"><span>preview</span></a></div>	
                <span style="float: right; position: relative; right:538px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to see what the product page will look like.<br /><br />Remember that you may still need to add it to the menu for site visitors to view it</span><span class=\"bottom\"></span></span>" : "") ?></a></span>				
            </td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>
			<td colspan="2"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>
        <!---
        <tr>
            <td>Programmer Message:</td>
            <td><input type="text" name="PROGRAMMER_MESSAGE" SIZE="87" value="<?php echo $message ?>"></td>
        </tr>
        --->
	</form>
    	<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
    </table>
    <div class="member_errors">
		<?php
        foreach($errors_array as $e){
            echo "<label class=\"" . $warning ."\">" . $e . "</label><br/>";	
        }
        ?>
    </div>
<?php
  include_once("includes/footer_admin.php");
?>
<script type="text/javascript">
	function load(anchor) {
		window.location.hash = anchor; 
	}
</script>

