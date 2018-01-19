<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = ""; $errors_array = array();
$message_upload = "";
$message_upload_add1 = "";
$message_upload_add2 = "";
$target_file = "";
$target_file_add1 = "";
$target_file_add2 = "";
$pathName = "/images/";
$scrolltobottom = "";

//the form encryption enctype="multipart/form-data" is prefixing every "'" with a "\" every time a submit button is hit which is knackering the descriptions.
//The only way around this short of re-structuring the whole program is to declare the character "\" as invalid and strip it out of every description if found
if(isset($_POST['NAME'])){$_POST['NAME'] = str_replace("\\", "", $_POST['NAME']);}
if(isset($_POST['DESC_SHORT'])){$_POST['DESC_SHORT'] = str_replace("\\", "", $_POST['DESC_SHORT']);}
if(isset($_POST['DESC_LONG'])){$_POST['DESC_LONG'] = str_replace("\\", "", $_POST['DESC_LONG']);}
if(isset($_POST['DESC_TRADE'])){$_POST['DESC_TRADE'] = str_replace("\\", "", $_POST['DESC_TRADE']);}

//initialise screen fields
$productcode = "";
$name = ""; $sku = "";
$desc_short = ""; $desc_long = "";
$desc_trade = "";
$weight = "0";
$quantity = "1";
$selling = "0.00"; $trade = "0.00";
$preferences = getPreferences();
$tax = $preferences->PREF_VAT; $taxexemption = "N";
$shipping = "0.00"; $shipping_apply = "F";
$option1 = "#"; $option2 = "#"; $option3 = "#"; $option4 = "#";
$user_string1 = ""; $no_stock = "";
$google_cat = ""; $availability = ""; $google_brand = ""; $google_gtin = ""; $google_mpn = "";
$google_adwords_grouping = ""; $google_adwords_labels = ""; $google_adwords_redirect = "";
$meta_title = ""; $meta_desc = ""; $meta_keywords = ""; $custom_head = ""; $prod_wrap = "";
$image_name = ""; $image_folder = ""; $image_alt = "";
$image_name_add1 = ""; $image_folder_add1 = ""; $image_alt_add1 = "";
$image_name_add2 = ""; $image_folder_add2 = ""; $image_alt_add2 = "";
$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";

if(isset($_POST['OPTION1'])){$option1 = $_POST['OPTION1'];}
if(isset($_POST['OPTION2'])){$option2 = $_POST['OPTION2'];}
if(isset($_POST['OPTION3'])){$option3 = $_POST['OPTION3'];}
if(isset($_POST['OPTION4'])){$option4 = $_POST['OPTION4'];}

if($preferences->PREF_GOOGLE_SEARCH == "N"){
	//set google post fields to blank
	$_POST['GOOGLE_CAT'] = ""; $_POST['AVAILABILITY'] = ""; $_POST['GOOGLE_BRAND'] = "";
	$_POST['GOOGLE_GTIN'] = ""; $_POST['GOOGLE_MPN'] = "";
}

if(!isset($_POST['PRODUCT_CREATED'])){$product_created = 0;}else{$product_created = $_POST['PRODUCT_CREATED'];}
if (isset($_POST['DELETE'])) {
	if($product_created == 1){
		//Delete the row just created - NOTE the preferences file will NOT be adjusted and the previous seed will be lost
		$rows = Delete_Product($_POST['PRODUCT']);
		if ($rows == 1){
			$message = $rows . " PRODUCT record successfully DELETED";
			$warning = "green";
			$product_created = 0;
			//now delete all associated PRODADD records
			$rows_delete = Delete_Prodadd($_POST['PRODUCT']);
			//initialise all fields ready for new record
			$next_code = getNextCode("product");
			$productcode = "";
			$_POST['NEW_PRODUCT'] = $next_code;
			$name = ""; $sku = "";
			$desc_short = ""; $desc_long = "";
			$desc_trade = "";
			$weight = "0";
			$quantity = "1";
			$selling = "0.00"; $trade = "0.00";
			$tax = $preferences->PREF_VAT; $taxexemption = "N";
			$shipping = "0.00"; $shipping_apply = "F";
			$option1 = "#"; $option2 = "#"; $option3 = "#"; $option4 = "#";
			$user_string1 = ""; $no_stock = "";
			$google_cat = ""; $availability = ""; $google_brand = ""; $google_gtin = ""; $google_mpn = "";
			$google_adwords_grouping = ""; $google_adwords_labels = ""; $google_adwords_redirect = "";
			$meta_title = ""; $meta_desc = ""; $meta_keywords = ""; $custom_head = ""; $prod_wrap = "";
			$image_name = ""; $image_folder = ""; $image_alt = "";
			$image_name_add1 = ""; $image_folder_add1 = ""; $image_alt_add1 = "";
			$image_name_add2 = ""; $image_folder_add2 = ""; $image_alt_add2 = "";
			$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";
			if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,3000)\" ";}
		}else{
			$message .= "PRODUCT record NOT DELETED - PLEASE CONTACT SHOPFITTER!!!"; 
			$warning = "red";
		}
	}else{
		$message .= "Cannot delete a record that hasn't been created yet !!!"; 
		$warning = "red";
		//refresh page with new details
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
		$option1 = $_POST['OPTION1'];
		$option2 = $_POST['OPTION2'];
		$option3 = $_POST['OPTION3'];
		$option4 = $_POST['OPTION4'];
		$user_string1 = $_POST['USER_STRING1'];
		$no_stock = $_POST['NO_STOCK'];
		$google_cat = $_POST['GOOGLE_CAT'];
		$availability = $_POST['AVAILABILITY'];
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
		//additional images
		$image_name_add1 = $_POST['IMAGE_NAME_ADD1'];
		$image_folder_add1 = $_POST['IMAGE_FOLDER_ADD1'];
		$image_alt_add1 = $_POST['IMAGE_ALT_ADD1'];
		$image_name_add2 = $_POST['IMAGE_NAME_ADD2'];
		$image_folder_add2 = $_POST['IMAGE_FOLDER_ADD2'];
		//hotspots
		$hotspot1 = $_POST['HOTSPOT1'];
		$hotspot2 = $_POST['HOTSPOT2'];
		$hotspot3 = $_POST['HOTSPOT3'];
	}
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,3000)\" ";}
}

if (isset($_POST['NEXT'])) {
	//initialise all fields ready for new record
	$next_code = getNextCode("product");
	$productcode = "";
	$_POST['NEW_PRODUCT'] = $next_code;
	$name = ""; $sku = "";
	$desc_short = ""; $desc_long = "";
	$desc_trade = "";
	$weight = "0";
	$quantity = "1";
	$selling = "0.00"; $trade = "0.00";
	$tax = $preferences->PREF_VAT; $taxexemption = "N";
	$shipping = "0.00"; $shipping_apply = "F";
	$option1 = "#"; $option2 = "#"; $option3 = "#"; $option4 = "#";
	$user_string1 = ""; $no_stock = "";
	$google_cat = ""; $availability = ""; $google_brand = ""; $google_gtin = ""; $google_mpn = "";
	$google_adwords_grouping = ""; $google_adwords_labels = ""; $google_adwords_redirect = "";
	$meta_title = ""; $meta_desc = ""; $meta_keywords = ""; $custom_head = ""; $prod_wrap = "";
	$image_name = ""; $image_folder = ""; $image_alt = "";
	$image_name_add1 = ""; $image_folder_add1 = ""; $image_alt_add1 = "";
	$image_name_add2 = ""; $image_folder_add2 = ""; $image_alt_add2 = "";
	$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";
	$product_created = 0;
}

if (isset($_POST['RESTORE_IMAGE']) or isset($_POST['RESTORE_IMAGE_ADD1']) or isset($_POST['RESTORE_IMAGE_ADD2'])) {
	if (isset($_POST['RESTORE_IMAGE'])){
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
	if(isset($_POST['RESTORE_IMAGE_ADD1'])){
		$image_name = $_POST['IMAGE_NAME'];
		$image_folder = $_POST['IMAGE_FOLDER'];
		$image_alt = $_POST['IMAGE_ALT'];
		$image_name_add1 = "no-image.jpg";
		$image_folder_add1 = "";
		$image_alt_add1 = "";
		$image_name_add2 = $_POST['IMAGE_NAME_ADD2'];
		$image_folder_add2 = $_POST['IMAGE_FOLDER_ADD2'];
		$image_alt_add2 = $_POST['IMAGE_ALT_ADD2'];
	}
	if(isset($_POST['RESTORE_IMAGE_ADD2'])){
		$image_name = $_POST['IMAGE_NAME'];
		$image_folder = $_POST['IMAGE_FOLDER'];
		$image_alt = $_POST['IMAGE_ALT'];
		$image_name_add1 = $_POST['IMAGE_NAME_ADD1'];
		$image_folder_add1 = $_POST['IMAGE_FOLDER_ADD1'];
		$image_alt_add1 = $_POST['IMAGE_ALT_ADD1'];
		$image_name_add2 = "no-image.jpg";
		$image_folder_add2 = "";
		$image_alt_add2 = "";
	}
	//now refresh the product settings made so far
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
	$option1 = $_POST['OPTION1'];
	$option2 = $_POST['OPTION2'];
	$option3 = $_POST['OPTION3'];
	$option4 = $_POST['OPTION4'];
	$user_string1 = $_POST['USER_STRING1'];
	$no_stock = $_POST['NO_STOCK'];
	$google_cat = $_POST['GOOGLE_CAT'];
	$availability = $_POST['AVAILABILITY'];
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
	$hotspot1 = $_POST['HOTSPOT1'];
	$hotspot2 = $_POST['HOTSPOT2'];
	$hotspot3 = $_POST['HOTSPOT3'];
	if (isset($_POST['RESTORE_IMAGE_ADD1']) or isset($_POST['RESTORE_IMAGE_ADD2'])){$scrolltobottom = "onLoad=\"scrollTo(0,3000)\" ";}
}

if (isset($_POST['UPLOAD_IMAGE']) or isset($_POST['UPLOAD_IMAGE_ADD1']) or isset($_POST['UPLOAD_IMAGE_ADD2'])) {
	//first thing to do is to re-extract the image_folder which may well have been changed via the input field FULL_PATH.
	//NOTE this is the only way the user may amend the IMAGE_FOLDER since by definition he must be changing the image at
	//the same time otherwise the link won't work anymore.
	//check image folder exists before proceeding
	$image_folder = ""; $image_folder_add1 = ""; $image_folder_add2 = "";
	$OK = 1;
	if (isset($_POST['UPLOAD_IMAGE'])){
		$image_folder = substr($_POST['FULL_PATH'], strlen($pathName));
		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['FULL_PATH'])){
			$message_upload = "Upload folder does NOT exist!!!" . "<br/>";
			$warning = "red";
			$OK = 0;	
		}
	}
	if (isset($_POST['UPLOAD_IMAGE_ADD1'])){
		$image_folder_add1 = substr($_POST['FULL_PATH_ADD1'], strlen($pathName));
		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['FULL_PATH_ADD1'])){
			$message_upload_add1 = "Upload folder does NOT exist!!!" . "<br/>";
			$warning = "red";
			$OK = 0;	
		}
	}
	if (isset($_POST['UPLOAD_IMAGE_ADD2'])){
		$image_folder_add2 = substr($_POST['FULL_PATH_ADD2'], strlen($pathName));
		if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['FULL_PATH_ADD2'])){
			$message_upload_add2 = "Upload folder does NOT exist!!!" . "<br/>";
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
			UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize",
			UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE",
			UPLOAD_ERR_PARTIAL => "Partial Upload",
			UPLOAD_ERR_NO_FILE => "No File",
			UPLOAD_ERR_NO_TMP_DIR => "No temporary directory",
			UPLOAD_ERR_CANT_WRITE => "Can't write to disk",
			UPLOAD_ERR_EXTENSION => "File upload stopped by extension");
	
		if (isset($_POST['UPLOAD_IMAGE'])){
			$error = $_FILES['FILE_UPLOAD']['error'];
			$message_upload = $upload_errors[$error] . " - ";
			if ($error == 0){$warning = "green";} else {$warning = "red";}
		}
	
		if (isset($_POST['UPLOAD_IMAGE_ADD1'])){
			$error = $_FILES['FILE_UPLOAD_ADD1']['error'];
			$message_upload_add1 = $upload_errors[$error] . " - ";
			if ($error == 0){$warning = "green";} else {$warning = "red";}
		}
		if (isset($_POST['UPLOAD_IMAGE_ADD2'])){
			$error = $_FILES['FILE_UPLOAD_ADD2']['error'];
			$message_upload_add2 = $upload_errors[$error] . " - ";
			if ($error == 0){$warning = "green";} else {$warning = "red";}
		}
		//Upload file
		if (isset($_POST['UPLOAD_IMAGE'])){
			$tmp_name = $_FILES['FILE_UPLOAD']['tmp_name'];
			$target_file = basename($_FILES['FILE_UPLOAD']['name']);
			$upload_file_t = "../images/" . (strlen($image_folder) > 0 ? $image_folder . "/" : "")  . $target_file;
			//echo "TEMP = " . $tmp_name . " / TARGET = " . $upload_file_t;
			$message_upload = Upload_File($tmp_name, $upload_file_t);
			$message_upload .= " - " . $upload_errors[$error];
		}
	
		if (isset($_POST['UPLOAD_IMAGE_ADD1'])){
			$tmp_name_add1 = $_FILES['FILE_UPLOAD_ADD1']['tmp_name'];
			$target_file_add1 = basename($_FILES['FILE_UPLOAD_ADD1']['name']);
			$upload_file_t_add1 = "../images/" . (strlen($image_folder_add1) > 0 ? $image_folder_add1 . "/" : "")  . $target_file_add1;
			//echo "TEMP = " . $tmp_name . " / TARGET = " . $upload_file_t;
			$message_upload_add1 = Upload_File($tmp_name_add1, $upload_file_t_add1);
			$message_upload_add1 .= " - " . $upload_errors[$error];
		}
		
		if (isset($_POST['UPLOAD_IMAGE_ADD2'])){
			$tmp_name_add2 = $_FILES['FILE_UPLOAD_ADD2']['tmp_name'];
			$target_file_add2 = basename($_FILES['FILE_UPLOAD_ADD2']['name']);
			$upload_file_t_add2 = "../images/" . (strlen($image_folder_add2) > 0 ? $image_folder_add2 . "/" : "")  . $target_file_add2;
			$message_upload_add2 = Upload_File($tmp_name_add2, $upload_file_t_add2);
			$message_upload_add2 .= " - " . $upload_errors[$error];
		}
	}
	//refresh page with new details
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
	$option1 = $_POST['OPTION1'];
	$option2 = $_POST['OPTION2'];
	$option3 = $_POST['OPTION3'];
	$option4 = $_POST['OPTION4'];
	$user_string1 = $_POST['USER_STRING1'];
	$no_stock = $_POST['NO_STOCK'];
	$google_cat = $_POST['GOOGLE_CAT'];
	$availability = $_POST['AVAILABILITY'];
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
	//additional images
	$image_name_add1 = $_POST['IMAGE_NAME_ADD1'];
	$image_folder_add1 = $_POST['IMAGE_FOLDER_ADD1'];
	$image_alt_add1 = $_POST['IMAGE_ALT_ADD1'];
	$image_name_add2 = $_POST['IMAGE_NAME_ADD2'];
	$image_folder_add2 = $_POST['IMAGE_FOLDER_ADD2'];
	$image_alt_add2 = $_POST['IMAGE_ALT_ADD2'];
	//hotspots
	$hotspot1 = $_POST['HOTSPOT1'];
	$hotspot2 = $_POST['HOTSPOT2'];
	$hotspot3 = $_POST['HOTSPOT3'];
	if (isset($_POST['UPLOAD_IMAGE_ADD1']) or isset($_POST['UPLOAD_IMAGE_ADD2'])){$scrolltobottom = "onLoad=\"scrollTo(0,3000)\" ";}
}

if (isset($_POST['CREATE'])) {
	if(isset($_POST['PRODUCT_CREATED']) and $_POST['PRODUCT_CREATED'] == 1){
		$message = "Cannot amend a newly created product - Please use Amend Product page";
		$warning = "red";
		//refresh page with new details
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
		$option1 = $_POST['OPTION1'];
		$option2 = $_POST['OPTION2'];
		$option3 = $_POST['OPTION3'];
		$option4 = $_POST['OPTION4'];
		$user_string1 = $_POST['USER_STRING1'];
		$no_stock = $_POST['NO_STOCK'];
		$google_cat = $_POST['GOOGLE_CAT'];
		$availability = $_POST['AVAILABILITY'];
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
		//additional images
		$image_name_add1 = $_POST['IMAGE_NAME_ADD1'];
		$image_folder_add1 = $_POST['IMAGE_FOLDER_ADD1'];
		$image_alt_add1 = $_POST['IMAGE_ALT_ADD1'];
		$image_name_add2 = $_POST['IMAGE_NAME_ADD2'];
		$image_folder_add2 = $_POST['IMAGE_FOLDER_ADD2'];
		//hotspots
		$hotspot1 = $_POST['HOTSPOT1'];
		$hotspot2 = $_POST['HOTSPOT2'];
		$hotspot3 = $_POST['HOTSPOT3'];
	}else{
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
		if (strlen($_POST['GOOGLE_ADWORDS_REDIRECT']) > 0 ){
			//will always be blank since this field is not valid until the product has been added to the tree structure
		}
		if ($message == ""){
			//no error message so update database product table
			if($option1 == "#"){$option1 = "";}
			if($option2 == "#"){$option2 = "";}
			if($option3 == "#"){$option3 = "";}
			if($option4 == "#"){$option4 = "";}		
			if(isset($_POST['TAXEXEMPTION']) and $_POST['TAXEXEMPTION'] == 1){$taxexemption = "Y";}else{$taxexemption = "N";}
			if(isset($_POST['SHIPPING_APPLY']) and $_POST['SHIPPING_APPLY'] == 1){$shipping_apply = "T";}else{$shipping_apply = "N";}
			$fields = array("pr_product"=>$_POST['PRODUCT'], "pr_name"=>$_POST['NAME'], "pr_sku"=>$_POST['SKU'], "pr_desc_short"=>$_POST['DESC_SHORT'], "pr_desc_long"=>$_POST['DESC_LONG'],
							"pr_image"=>$_POST['IMAGE_NAME'], "pr_image_folder"=>$_POST['IMAGE_FOLDER'], "pr_image_alt"=>$_POST['IMAGE_ALT'],
							"pr_desc_trade"=>$_POST['DESC_TRADE'], "pr_weight"=>$_POST['WEIGHT'], "pr_quantity"=>$_POST['QUANTITY'],
							"pr_selling"=>$_POST['SELLING'], "pr_trade"=>$_POST['TRADE'], "pr_tax"=>$_POST['TAX'],
							"pr_shipping"=>$_POST['SHIPPING'], "pr_taxexemption"=>$taxexemption, "pr_shipping_apply"=>$shipping_apply,
							"pr_option1"=>$option1, "pr_option2"=>$option2, "pr_option3"=>$option3,
							"pr_option4"=>$option4, "pr_meta_title"=>$_POST['META_TITLE'], "pr_meta_desc"=>$_POST['META_DESC'], "pr_user_string1"=>$_POST['USER_STRING1'],
							"pr_no_stock"=>$_POST['NO_STOCK'], "pr_google_cat"=>$_POST['GOOGLE_CAT'], "pr_availability"=>$_POST['AVAILABILITY'],
							"pr_google_brand"=>$_POST['GOOGLE_BRAND'], "pr_google_gtin"=>$_POST['GOOGLE_GTIN'], "pr_google_mpn"=>$_POST['GOOGLE_MPN'],
							"pr_google_adwords_grouping"=>$_POST['GOOGLE_ADWORDS_GROUPING'], "pr_google_adwords_labels"=>$_POST['GOOGLE_ADWORDS_LABELS'], "pr_google_adwords_redirect"=>$_POST['GOOGLE_ADWORDS_REDIRECT'],
							"pr_meta_keywords"=>$_POST['META_KEYWORDS'], "pr_custom_head"=>$_POST['CUSTOM_HEAD'], "pr_prod_wrap"=>$_POST['PROD_WRAP']);
				
			$rows = Create_Product($fields);
			if ($rows == 1){
				$errors_array[] = "Product record successfully CREATED";
				$warning = "green";
				$product_created = 1;
				//increment seed on preferences
				$rows = incrementSeed($_POST['PRODUCT']);	
				if($rows !=1){$errors_array[] = "error writing PREFERENCE Record ({$_POST['PRODUCT']}) - seed NOT Updated - PLEASE CONTACT SHOPFITTER!!!"; $warning = "red";}

			}
			if ($rows == 0){
				$errors_array[] = "WARNING ! ! ! - NO RECORDS CREATED";
				$warning = "orange";
			}
			if ($rows > 1){
				$errors_array[] = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") PRODUCT RECORD CREATED - PLEASE CONTACT SHOPFITTER";
				$warning = "red";
			}
			//update PRODADD file with additional images
			if($_POST['IMAGE_NAME_ADD1'] != "" or $_POST['IMAGE_NAME_ADD2'] != ""){
				$message_prodadd = ""; $rows_prodadd = 0;
				$fields = array("pra_product"=>$_POST['PRODUCT'], "position_add1"=>2, "image_name_add1"=>$_POST['IMAGE_NAME_ADD1'], "image_folder_add1"=>$_POST['IMAGE_FOLDER_ADD1'],
								"image_alt_add1"=>$_POST['IMAGE_ALT_ADD1'], "position_add2"=>3, "image_name_add2"=>$_POST['IMAGE_NAME_ADD2'], "image_folder_add2"=>$_POST['IMAGE_FOLDER_ADD2'],
								"image_alt_add2"=>$_POST['IMAGE_ALT_ADD2']);
				$fields = Update_Prodadd($fields);
				$rows_prodadd = $fields['rows'];
				$message_prodadd = $fields['message'];
				if($message_prodadd != ""){
					$message = $message_prodadd;
					$warning = "red";
				}
				$error = null;
				$error = mysql_error();
				if ($error != null) { 
					$error_array[] = " - ADDITIONAL IMAGE UPDATE ERRORS FOUND ! ! ! - " . mysql_error() . " ";
					$warning = "red";
				}
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
			//refresh page with newly created product details
			$product = getProductDetails($_POST['PRODUCT']);
			$productcode = $product->PR_PRODUCT;
			$name = $product->PR_NAME;
			$sku = $product->PR_SKU;
			$desc_short = $product->PR_DESC_SHORT;
			$desc_long = $product->PR_DESC_LONG;
			$desc_trade = $product->PR_DESC_TRADE;
			$weight = $product->PR_WEIGHT;
			$quantity = $product->PR_QUANTITY;
			$selling = $product->PR_SELLING;
			$trade = $product->PR_TRADE;
			$tax = $product->PR_TAX;
			$taxexemption = $product->PR_TAXEXEMPTION;
			$shipping = $product->PR_SHIPPING;
			$shipping_apply = $product->PR_SHIPPING_APPLY;
			$option1 = $product->PR_OPTION1;
			$option2 = $product->PR_OPTION2;
			$option3 = $product->PR_OPTION3;
			$option4 = $product->PR_OPTION4;
			$user_string1 = $product->PR_USER_STRING1;
			$no_stock = $product->PR_NO_STOCK;
			$google_cat = $product->PR_GOOGLE_CAT;
			$availability = $product->PR_AVAILABILITY;
			$google_brand = $product->PR_GOOGLE_BRAND;
			$google_gtin = $product->PR_GOOGLE_GTIN;
			$google_mpn = $product->PR_GOOGLE_MPN;
			$google_adwords_grouping = $product->PR_GOOGLE_ADWORDS_GROUPING;
			$google_adwords_labels = $product->PR_GOOGLE_ADWORDS_LABELS;
			$google_adwords_redirect = $product->PR_GOOGLE_ADWORDS_REDIRECT;
			$meta_title = $product->PR_META_TITLE;
			$meta_desc = $product->PR_META_DESC;
			$meta_keywords = $product->PR_META_KEYWORDS;
			$custom_head = $product->PR_CUSTOM_HEAD;
			$prod_wrap = $product->PR_PROD_WRAP;
			$image_name = $product->PR_IMAGE;
			$image_folder = $product->PR_IMAGE_FOLDER;
			$image_alt = html_entity_decode($product->PR_IMAGE_ALT);
			//get additional images
			$additional = getAdditionalImages($_POST['PRODUCT']);
			foreach($additional as $a){
				// at this point we are "hardcoding" to allow only 2 additional images
				if ($a->PRA_POSITION == 2){
					$image_name_add1 = $a->PRA_IMAGE;
					$image_folder_add1 = $a->PRA_IMAGE_FOLDER;
					$image_alt_add1 = $a->PRA_IMAGE_ALT;
				}
				if ($a->PRA_POSITION == 3){
					$image_name_add2 = $a->PRA_IMAGE;
					$image_folder_add2 = $a->PRA_IMAGE_FOLDER;
					$image_alt_add2 = $a->PRA_IMAGE_ALT;
				}
			}
			//get hotspots
			$hotspots = getHotspots($_POST['PRODUCT']);
			$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";
			foreach($hotspots as $h){
				$hotspot = "hotspot" . $h->HS_NUMBER;
				$$hotspot = $h->HS_DATA;
			}
		}else{
			//refresh page with new details
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
			$option1 = $_POST['OPTION1'];
			$option2 = $_POST['OPTION2'];
			$option3 = $_POST['OPTION3'];
			$option4 = $_POST['OPTION4'];
			$user_string1 = $_POST['USER_STRING1'];
			$no_stock = $_POST['NO_STOCK'];
			$google_cat = $_POST['GOOGLE_CAT'];
			$availability = $_POST['AVAILABILITY'];
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
			//additional images
			$image_name_add1 = $_POST['IMAGE_NAME_ADD1'];
			$image_folder_add1 = $_POST['IMAGE_FOLDER_ADD1'];
			$image_alt_add1 = $_POST['IMAGE_ALT_ADD1'];
			$image_name_add2 = $_POST['IMAGE_NAME_ADD2'];
			$image_folder_add2 = $_POST['IMAGE_FOLDER_ADD2'];
			//hotspots
			$hotspot1 = $_POST['HOTSPOT1'];
			$hotspot2 = $_POST['HOTSPOT2'];
			$hotspot3 = $_POST['HOTSPOT3'];
		}
	}
	if($message != "" or count($errors_array) != 0){$scrolltobottom = "onLoad=\"scrollTo(0,3000)\" ";}
}

//$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Create Products";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;
if($product_created == 0){$next_code = getNextCode("product");}

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
      	<div class="admin">
    <br/>
	<h1>Create Products</h1>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="enter_thumb" action="/_cms/create_products.php" enctype="multipart/form-data" method="post">
    	<tr>
          <td class="product-td">New Product Code:</td>
            <td><Input  type="text" name="NEW_PRODUCT_DISABLED" size="15" value="<?php echo isset($_POST['NEW_PRODUCT']) ? $_POST['NEW_PRODUCT'] : $next_code ?>" disabled />
            	<Input type="hidden" name="NEW_PRODUCT" value="<?php echo strlen($productcode) > 0 ? $productcode : $next_code ?>" />
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is the ID code automatically assigned by the software to the new product<br /><br />The next code is assigned according to the Product Seed on the Preferences and Settings page</span><span class=\"bottom\"></span></span>" : "") ?></a>
            </td>
            
    	</tr>
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
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
                    <input type="text" name="IMAGE_NAME_DISABLED" SIZE="49" disabled value="<?php echo $image_name ?>"><br/><br/>
                    <input type="hidden" name="IMAGE_NAME" value="<?php echo $image_name ?>">
                    <label>Main Image - Alternative Name:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a short text description of the image; if you don't then filename will be used<br /><br />Note that this is a legal requirement and is also indexed by search engines</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_ALT" SIZE="49" value="<?php echo $image_alt ?>"><br/><br/>                    
                    
                    <label>Main Image - Subfolder:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">If you've uploaded the image to a sub-folder this will show where the image is located</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_FOLDER_DISABLED" SIZE="49" disabled value="<?php echo $image_folder ?>"><br/><br/><br/>
                    <input type="hidden" name="IMAGE_FOLDER" value="<?php echo $image_folder ?>">
                    <Input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                    <label>Add Picture:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">To add an image click the Browse button and navigate to the image you want to use; then click the Upload button</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <Input name="FILE_UPLOAD" type="file" size="37" value="<?php echo $pathName . strlen($image_name) > 0 ? $image_name : "" ?>" onchange="Set_Picture(this.form.IMAGE_NAME, this.form.FILE_UPLOAD, this.form.IMAGE);"/><br/><br/>
                    <label>New Image will be uploaded to this folder:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">If you want to upload images to a sub-folder within the images directory you can specify it here; format as shown: /sub-folder-name/</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="FULL_PATH" SIZE="49" value="<?php echo $pathName . $image_folder ?>"><br/><br/>
                    <Input name="UPLOAD_IMAGE" type="submit" value="Upload Image" class="upload-button" />&nbsp;&nbsp;&nbsp;<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Upload an image with the button to the left or remove one by using the button on the right</span><span class=\"bottom\"></span></span>" : "") ?></a>&nbsp;&nbsp;&nbsp;
                    <Input name="RESTORE_IMAGE" type="submit" value="Remove Image" class="remove-button" /><br/><br/>

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
            <td><label><strong><?php echo strlen($productcode) > 0 ? $productcode : $next_code ?></strong></label>
                 	<input type="hidden" name="PRODUCT" SIZE="50" value="<?php echo strlen($productcode) > 0 ? $productcode : $next_code ?>">
        </td>
    	</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
		
        <tr>
            <td>Name: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a name for the product (or service)<br /><br />This will be displayed in menus and URLs<br /><br />It's best to use sentence case or all lower case for names; all capitals is less preferred due to an internet convention that words written in capitals are too forceful in the same way as a shout is in normal conversation</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="NAME" SIZE="50" value="<?php echo $name ?>"></td>
        </tr>
        <tr>
            <td>SKU Code: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Stock Keeping Unit: Enter a SKU code<br />
                    <br />
                    For items that have options the SKU code can be either an amalgamation of top level SKU (set here) and then option specific SKU elements<br />
                    <br />
                    Alternatively, leave this blank and add the specific SKU in the Options section</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
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
                <span style="float: right; position: relative; left:-45px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
            </td>
			<td>
            	<textarea type="text" name="DESC_LONG" class="p-amendproduct-textarea"><?php echo $desc_long ?></textarea>
            </td>
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
        	<td>Option 1: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the options you want to apply to this product<br /><br />You'll need to set the options up first in the Options section from the menu on the left<br /><br />You can create general options that can be applied to all products or options available for only a specific product<br /><br />Up to 4 option sets can be added to each product</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td>
                <select name="OPTION1" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $selections = getGeneralSelections();
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
			</td>
        </tr>
        <tr>
        	<td>Option 2:</td>
            <td>
                <select name="OPTION2" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $selections = getGeneralSelections();
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
                    $selections = getGeneralSelections();
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
                    $selections = getGeneralSelections();
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
        <!--- ADDITIONAL IMAGE 1 HANDLING  ---------------------------------------------------------------------------------------------->
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>		
		
		<tr>
        	<td>
            </td>
            <td>
            	<div class="p-display-picture">
				<?php
                             
                if (isset($_POST['UPLOAD_IMAGE_ADD1'])) {
					$image_folder_add1 = substr($_POST['FULL_PATH_ADD1'], strlen($pathName));
					$image_name_add1 = $target_file_add1;
					$image_alt_add1 = $target_file_add1;
                    echo "<img name=\"IMAGE_ADD1\" src=\"" . $pathName . (strlen($image_folder_add1) > 0 ? $image_folder_add1 . "/" : "") . $target_file_add1 . "\" width=\"200\" alt=\"" . $target_file_add1 . "\" /><br/>";
                    echo "<label>" . $target_file_add1 . "</label>";
                }else{
					//$image_folder_add1 = (isset($imageFolderAdd1) ? $imageFolderAdd1 : "");
					//$image_name_add1 = (isset($imageNameAdd1) ? $imageNameAdd1 : "no-image.jpg");
					//$image_alt_add1 = (isset($imageAltAdd1) ? $imageAltAdd1 : "");
					echo "<img name=\"IMAGE_ADD1\" src=\"" . $pathName . (strlen($image_folder_add1) > 0 ? $image_folder_add1 . "/" : "") . (strlen($image_name_add1) > 0 ? $image_name_add1 : "no-image.jpg") . "\" width=\"200\" alt=\"" . (strlen($image_name_add1) > 0 ? $image_name_add1 : "no-image.jpg") . "\" /><br/>";	
					echo "<label>" . (strlen($image_name_add1) > 0 ? $image_name_add1 : "no image") . "</label>";	
                }
                ?>
				</div>
				<div class="p-change-picture">
                    <label>Additional Image 1 - Name:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Add a second image here using the same method as the main image</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_NAME_ADD1_DISABLED" SIZE="49" disabled value="<?php echo $image_name_add1 ?>"><br/><br/>
                    <input type="hidden" name="IMAGE_NAME_ADD1" value="<?php echo $image_name_add1 ?>">
                    <label>Additional Image 1 - Alternative Name:</label><br/>
                    <input type="text" name="IMAGE_ALT_ADD1" SIZE="49" value="<?php echo $image_alt_add1 ?>"><br/><br/>                    
                    
                    <label>Additional Image 1 - Subfolder:</label><br/>
                    <input type="text" name="IMAGE_FOLDER_ADD1_DISABLED" SIZE="49" disabled value="<?php echo $image_folder_add1 ?>"><br/><br/><br/>
                    <input type="hidden" name="IMAGE_FOLDER_ADD1" value="<?php echo $image_folder_add1 ?>">
                    <Input type="hidden" name="MAX_FILE_SIZE_ADD1" value="1000000" />
                    <label>Add Picture:</label><br/>
                    <Input name="FILE_UPLOAD_ADD1" type="file" size="37" value="<?php echo $pathName . $image_name_add1 ?>" onchange="Set_Picture(this.form.IMAGE_NAME_ADD1, this.form.FILE_UPLOAD_ADD1, this.form.IMAGE_ADD1);"/><br/><br/>
                    <label>New Image will be uploaded to the following folder:</label><br/>
                    <input type="text" name="FULL_PATH_ADD1" SIZE="49" value="<?php echo $pathName . $image_folder_add1 ?>"><br/><br/>
                    <Input name="UPLOAD_IMAGE_ADD1" type="submit" value="Upload Image" class="upload-button" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <Input name="RESTORE_IMAGE_ADD1" type="submit" value="Remove Image" class="remove-button" /><br/><br/>

                    <label class="<?php echo $warning ?>"><?php echo $message_upload_add1 ?></label>
                </div>

            </td> 
		</tr>
    	<!--- END OF ADDITIONAL IMAGE 1 HANDLING ---------------------------------------------------------------------------------------> 
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>    
        <!--- ADDITIONAL IMAGE 2 HANDLING  ---------------------------------------------------------------------------------------------->
		<tr>
        	<td>
            </td>
            <td>
            	<div class="p-display-picture">
				<?php
                             
                if (isset($_POST['UPLOAD_IMAGE_ADD2'])) {
					$image_folder_add2 = substr($_POST['FULL_PATH_ADD2'], strlen($pathName));
					$image_name_add2 = $target_file_add2;
					$image_alt_add2 = $target_file_add2;
                    echo "<img name=\"IMAGE_ADD2\" src=\"" . $pathName . (strlen($image_folder_add2) > 0 ? $image_folder_add2 . "/" : "") . $target_file_add2 . "\" width=\"200\" alt=\"" . $target_file_add2 . "\" /><br/>";
                    echo "<label>" . $target_file_add2 . "</label>";
                }else{
					echo "<img name=\"IMAGE_ADD2\" src=\"" . $pathName . (strlen($image_folder_add2) > 0 ? $image_folder_add2 . "/" : "") . (strlen($image_name_add2) > 0 ? $image_name_add2 : "no-image.jpg") . "\" width=\"200\" alt=\"" . (strlen($image_name_add2) > 0 ? $image_name_add2 : "no-image.jpg") . "\" /><br/>";	
					echo "<label>" . (strlen($image_name_add2) > 0 ? $image_name_add2 : "no image") . "</label>";	
                }
                ?>
				</div>
				<div class="p-change-picture">
                    <label>Additional Image 2 - Name:</label> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Add a third image here using the same method as the main image</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_NAME_ADD2_DISABLED" SIZE="49" disabled value="<?php echo $image_name_add2 ?>"><br/><br/>
                    <input type="hidden" name="IMAGE_NAME_ADD2" value="<?php echo $image_name_add2 ?>">
                    <label>Additional Image 2 - Alternative Name:</label><br/>
                    <input type="text" name="IMAGE_ALT_ADD2" SIZE="49" value="<?php echo $image_alt_add2 ?>"><br/><br/>                    
                    
                    <label>Additional Image 2 - Subfolder:</label><br/>
                    <input type="text" name="IMAGE_FOLDER_ADD2_DISABLED" SIZE="49" disabled value="<?php echo $image_folder_add2 ?>"><br/><br/><br/>
                    <input type="hidden" name="IMAGE_FOLDER_ADD2" value="<?php echo $image_folder_add2 ?>">
                    <Input type="hidden" name="MAX_FILE_SIZE_ADD2" value="1000000" />
                    <label>Add Picture:</label><br/>
                    <Input name="FILE_UPLOAD_ADD2" type="file" size="37" value="<?php echo $pathName . $image_name_add2 ?>" onchange="Set_Picture(this.form.IMAGE_NAME_ADD2, this.form.FILE_UPLOAD_ADD2, this.form.IMAGE_ADD2);"/><br/><br/>
                    <label>New Image will be uploaded to the following folder:</label><br/>
                    <input type="text" name="FULL_PATH_ADD2" SIZE="49" value="<?php echo $pathName . $image_folder_add2 ?>"><br/><br/>
                    <Input name="UPLOAD_IMAGE_ADD2" type="submit" value="Upload Image" class="upload-button" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <Input name="RESTORE_IMAGE_ADD2" type="submit" value="Remove Image" class="remove-button" /><br/><br/>

                    <label class="<?php echo $warning ?>"><?php echo $message_upload_add2 ?></label>
                </div>

            </td> 
		</tr>
    	<!--- END OF ADDITIONAL IMAGE 2 HANDLING --------------------------------------------------------------------------------------->   
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
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>          
		<!--- CREATE BUTTON ---------->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td>
            	<input type="hidden" name="PRODUCT_CREATED" value="<?php echo $product_created ?>" />
            	<input name="CREATE" type="submit" value="Create Product &raquo;&raquo;" class="create-button"> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to create a product with the details entered above</span><span class=\"bottom\"></span></span>" : "") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="NEXT" type="submit" value="Next Product &raquo;&raquo;" class="next-button"> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click here to create another new product - ensure you've clicked the Create Product button to save the current new product first</span><span class=\"bottom\"></span></span>" : "") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="DELETE" type="submit" value="Delete Product &raquo;&raquo;" class="delete-button-left">
            	
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

