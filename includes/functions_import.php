<?php
//---CONVERT ALL CATEGORY AND PRODUCT CODES TO UPPER CASE ------------------------------------------------------------------------------------------------
function Convert_Codes_To_Uppercase() {
	// write table row
	$sql = "UPDATE addl_products SET AP_PRODUCT=UPPER(AP_PRODUCT), AP_ADDITIONAL=UPPER(AP_ADDITIONAL); ";
	$rows = mysql_query($sql);
	$sql = "UPDATE categories SET CA_CODE=UPPER(CA_CODE), CA_PARENT=UPPER(CA_PARENT), CA_TREE_NODE=UPPER(CA_TREE_NODE);";
	$rows = mysql_query($sql);
	$sql = "UPDATE hotspots SET HS_CODE=UPPER(HS_CODE);";
	$rows = mysql_query($sql);
	$sql = "UPDATE options SET OP_PRODUCT=UPPER(OP_PRODUCT);";
	$rows = mysql_query($sql);
	$sql = "UPDATE prodadd SET PRA_PRODUCT=UPPER(PRA_PRODUCT);";
	$rows = mysql_query($sql);
	$sql = "UPDATE prodcat SET PC_PRODUCT=UPPER(PC_PRODUCT), PC_CATEGORY=UPPER(PC_CATEGORY), PC_TREE_NODE=UPPER(PC_TREE_NODE);";
	$rows = mysql_query($sql);
	$sql = "UPDATE product SET PR_PRODUCT=UPPER(PR_PRODUCT);";
	$rows = mysql_query($sql);
	$sql = "UPDATE selection SET SE_PRODUCT=UPPER(SE_PRODUCT);";
	$rows = mysql_query($sql);
	return $rows;
	
	//return mysql_affected_rows();
}
//---SELECTION BOXES -------------------------------------------------------------------------------------------------------------------------------------
function Import_Selections($fields) {
	// write table row
	$sql = "INSERT INTO selection (SE_NAME, SE_LABEL, SE_EXCLUDE, SE_PRODUCT)";
	$sql .= " VALUES ('" . $fields[0] . "', '" . $fields[1] . "', '" . $fields[2] . "', '" . ($fields[3]) . "')";
	$rows = mysql_query($sql);
	return $rows;
	
	//return mysql_affected_rows();
}

function Get_Selection($selection, $custom) {
	// write table row
	$sql = "SELECT * FROM selection";
	$sql .= " WHERE SE_NAME = '" . $selection . "'";
	if($custom != ""){
		$sql .= " AND SE_PRODUCT = '" . $custom . "'";
	}
	$rows = mysql_query($sql);
	return $rows;
	
	//return mysql_affected_rows();
}

function Add_Selection_To_Product($setNo, $selection, $product) {
	// write table row
	$sql = "UPDATE PRODUCT SET PR_OPTION" . $setNo . " = '" . $selection . "'";
	$sql .= " WHERE PR_PRODUCT = '" . $product . "'";
	$rows = mysql_query($sql);
	return $rows;
	
	//return mysql_affected_rows();
}

function Import_Options($fields) {
	// write table row
	$fixstring = str_replace("quot", "&amp;quot;", $fields[1]);
	$sql = "INSERT INTO options (OP_SE_ID, OP_NAME, OP_NUMBER, OP_TEXT, OP_VALUE, OP_SELECTED, OP_PRODUCT)";
	$sql .= " VALUES ('" . $fields[0] . "', '" . $fixstring . "', '" . $fields[2] . "', '" . $fields[3] . "', '" . $fields[4] . "', '" . $fields[5] . "', '" . $fields[6] . "')";
	$rows = mysql_query($sql);
	return $rows;
	
	//return mysql_affected_rows();
}
//---HOTSPOTS---------------------------------------------------------------------------------------------------------------------------------------------
function Import_Hotspots($fields) {
	//category hotspots now to write to category->CA_TOP_CONTENT(hotspot1) and CA_BOTTOM_CONTENT(hotspot2)
	//Note that the str_replace is to replace the code "&#8220;" with a double quotes which is then html encoded and decoded at the other end
	// write table row
	$hotError = 0;
	if(substr($fields[0], 0, 2) == "ca"){
		if($fields[1] == 1){
				$sql = "UPDATE categories set CA_TOP_CONTENT='" . htmlentities(str_replace("&#8220;", "\"", mysql_real_escape_string($fields[2])), ENT_QUOTES) . "' WHERE CA_CODE='" . $fields[0] . "'";
		}
		if($fields[1] == 2){
				//echo "FIELD[2]=" . $fields[2] . "<br/>";
				$sql = "UPDATE categories set CA_BOTTOM_CONTENT='" . htmlentities(str_replace("&#8220;", "\"", mysql_real_escape_string($fields[2])), ENT_QUOTES) . "' WHERE CA_CODE='" . $fields[0] . "'";
		}
		if($fields[1] != 1 and $fields[1] != 2){
				echo "INVALID HOTSPOT NUMBER(" . $fields[1] . "( FOUND WITHIN - " . $fields[0] . " / " . $fields[1];
				$hotError = 1;
		}
		if(!$hotError){
			$rows = mysql_query($sql);
		}
	}else{
		//this is product hotspot data
		$sql = "INSERT INTO hotspots (HS_CODE, HS_NUMBER, HS_DATA)";
		$sql .= " VALUES ('" . mysql_real_escape_string($fields[0]) . "', '" . mysql_real_escape_string($fields[1]) . "', '" . htmlentities(str_replace("&#8220;", "\"", mysql_real_escape_string($fields[2])), ENT_QUOTES) . "')";
		$rows = mysql_query($sql);
	}

	return $rows;
	
	//return mysql_affected_rows();
}

function getHotspots($handle){
	$catlist = array();
	while(!feof($handle)) {
		$line = preg_replace("/[^a-zA-Z0-9\s\x2D]/", "", fgets($handle)) . "<br/>";
		//echo $line;
		$tstart = strpos($line, "category id", 0);
		if ($tstart > 0){
			$fstart = $tstart + 11;
			$fend = strpos($line, "\r\n", 0);
			$flength = $fend - $fstart;
			$cat = trim(substr($line, $fstart, $flength));
			$catlist[] = $cat;
		}
	}
	return $catlist;
}

//---PRODUCTS---------------------------------------------------------------------------------------------------------------------------------------------
function Import_Products($fields) {
	// write table row
	$sql = "INSERT INTO product (PR_PRODUCT, PR_NAME, PR_DESC_SHORT, PR_DESC_LONG, PR_IMAGE, PR_IMAGE_ALT, ";
	$sql .= "PR_WEIGHT, PR_SELLING, PR_TAX, PR_TAXEXEMPTION, PR_SHIPPING_APPLY, PR_USER_STRING1, ";
	$sql .= "PR_META_TITLE, PR_META_DESC, PR_META_KEYWORDS)";
	$sql .= " VALUES ('" . $fields[0] . "', '" . mysql_real_escape_string($fields[1]) . "', '" . mysql_real_escape_string($fields[5]) . "', '";
	$sql .= mysql_real_escape_string($fields[6]) . "', '" . mysql_real_escape_string($fields[17]) . "', '" . mysql_real_escape_string($fields[21]) . "', '";
	//$sql .= mysql_real_escape_string($fields[117]) . "', '";
	$sql .= $fields[14] . "', '" . $fields[3] . "', '" . $fields[11] . "', '" . $fields[10] . "', '" . $fields[12] . "', '" . htmlentities(mysql_real_escape_string($fields[22])) . "', '";
	$sql .= mysql_real_escape_string($fields[7]) . "', '" . mysql_real_escape_string($fields[8]) . "', '" . mysql_real_escape_string($fields[9]) . "')";
	$rows = mysql_query($sql);

	return $rows;
	
	//return mysql_affected_rows();
}

function Import_Descriptions($product,$short, $long) {
	$long = formatDescription($long);
	// write table row
	$sql = "UPDATE product";
	$sql .= " SET PR_DESC_SHORT='" . mysql_real_escape_string($short) . "', PR_DESC_LONG='" . htmlentities(mysql_real_escape_string($long)) . "' ";
	$sql .= " WHERE PR_PRODUCT = '" . $product . "'";
	$rows = mysql_query($sql);

	return $rows;
	
	//return mysql_affected_rows();
}

function formatDescription($description){
	//check whether image is held in a subfolder
	$formatted = str_replace("\n", "<br/>", $description);
	echo "FORMATTED = " . $formatted . "<br/>";
	
	return $formatted;
}

function getProduct($product){
	$sql = "SELECT * FROM product ";
	$sql .= "WHERE PR_PRODUCT='" . $product . "'";
	$results = mysql_query($sql);
	$row = mysql_fetch_object($results);
	
	return $row;
}

function Import_AdditionalImage($position, $fields) {
	// write table row
	$imageName = $fields[1];
	//check whether image is held in a subfolder
	$fstart = 0; $folderstart = 0;
	$fend = 999;
	$imageFolder = "";
	echo "|" . $imageName . "|" . "<br/>";
	while ($fend > 0){
		$fend = strpos($imageName, "\\", $folderstart);
		if($fend > 0){
			$imageFolder = substr($imageName, 0, ($fend - $fstart));
			$olderstart = $fend + 1;
			$imageName = substr($imageName, $fend + 1);
		}
	}
	//Note that the image folder is a local folder only. At time of publishing all local files are moved into the "images" folder.
	//Thus there will never be any subfolders within the on-site "images" folder.
	
	$sql = "INSERT INTO prodadd (PRA_POSITION, PRA_PRODUCT, PRA_IMAGE, PRA_IMAGE_ALT)";
	$sql .= " VALUES ('" . $position . "', '" . $fields[0] . "', '" . mysql_real_escape_string($imageName) . "', '" . mysql_real_escape_string($fields[2]) . "')";
	//$sql .= mysql_real_escape_string($imageFolder) . "')";
	echo $sql . "<br/>";
	$rows = mysql_query($sql);
	echo "ROWS WRITTEN = " . $rows . "<br/>";
	return $rows;
	
	//return mysql_affected_rows();
}

function Import_Additional_Product($position, $fields) {
	// write table row
	$sql = "INSERT INTO addl_products (AP_POSITION, AP_PRODUCT, AP_ADDITIONAL)";
	$sql .= " VALUES ('" . $position . "', '" . $fields[0] . "', '" . $fields[1] . "')";
	$rows = mysql_query($sql);

	return $rows;
	//return mysql_affected_rows();
}

function Fix_Price($price) {
	// return price as 2dp string
	$formatted = $price;
	$lenPrice = strlen($formatted);
	$offset = strpos($price, ".");
	if (!$offset) {
		$noDecimalPlaces = 0;
	} else {
		$noDecimalPlaces =  $lenPrice - ($offset + 1);
	}
	if ($noDecimalPlaces == 0 ) {$formatted = $price . ".00";}
	if ($noDecimalPlaces == 1 ) {$formatted = $price . "0";}

	return $formatted;
}

function Pence_To_Pounds($price) {
	// convert from pence to pounds
	if(substr($price, 0, 1) != "-"){
		$formatted = $price;
		$lenPrice = strlen($formatted);
		$offset = strpos($formatted, ".");
		if($offset > 0){
			$formatted = Fix_Price($price);
		}else{
			if($lenPrice == 1){$formatted = "00" . $formatted;}
			if($lenPrice == 2){$formatted = "0" . $formatted;}
			$lenPrice = strlen($formatted);
			$formatted = substr($formatted, 0, $lenPrice - 2) . "." . substr($formatted, $lenPrice - 2);
		}
	}else{
		//strip off the "-", convert and then add it back on again
		$formatted = substr($price, 1);
		$lenPrice = strlen($formatted);
		$offset = strpos($formatted, ".");
		if($offset > 0){
			$formatted = Fix_Price($formatted);
		}else{
			if($lenPrice == 1){$formatted = "00" . $formatted;}
			if($lenPrice == 2){$formatted = "0" . $formatted;}
			$lenPrice = strlen($formatted);
			$formatted = substr($formatted, 0, $lenPrice - 2) . "." . substr($formatted, $lenPrice - 2);
			$formatted = "-" . $formatted;
		}
	}

	return $formatted;
}

//---CATEGORIES---------------------------------------------------------------------------------------------------------------------------------------------
function Import_Categories($fields, $tree) {
	// write table row
	//Note that the image folder is a local folder only. At time of publishing all local files are moved into the "images" folder.
	//Thus there will never be any subfolders within the on-site "images" folder.
	$sql = "INSERT INTO categories (CA_NAME, CA_DESCRIPTION, CA_CODE, CA_PARENT, CA_IMAGE, CA_IMAGE_ALT, CA_TREE_NODE)";
	$sql .= " VALUES ('" . mysql_escape_string($fields[0]) . "', '" . mysql_real_escape_string($fields[1]) . "', '" . mysql_real_escape_string($fields[2]) . "', '";
	$sql .= $fields[3] . "', '" . mysql_real_escape_string($fields[4]) . "', '";
	//$sql .= mysql_real_escape_string($fields[6]) . "', '";
	$sql .= mysql_real_escape_string($fields[4]) . "', '";
	$sql .= mysql_real_escape_string($tree) . "')";
	$rows = mysql_query($sql);

	return $rows;
	
	//return mysql_affected_rows();
}

function getCatFields($handle, $catCode, $parent){
	$catName = getCatName($handle);
	$fields[0] = $catName;
	//echo $catName . "<br/>";
	$catDesc = getCatDesc($handle);
	$fields[1] = $catDesc;
	$fields[2] = $catCode;
	$fields[3] = $parent;
	$imageFields = getImageName($handle);
	$fields[4] = $imageFields[0];
	//echo $imageName . "<br/>";
	$imageAlt = getImageAlt($handle);
	$fields[5] = $imageAlt;
	//echo $imageAlt . "<br/>";
	$fields[6] = $imageFields[1];
	
	return $fields;
}

function attachProduct($product, $category, $tree, $posCtr){
	// write table row
	$sql = "INSERT INTO prodcat (PC_PRODUCT, PC_CATEGORY, PC_TREE_NODE, PC_POSITION)";
	$sql .= " VALUES ('" . mysql_real_escape_string(strtoupper($product)) . "', '" . mysql_real_escape_string($category) . "', '";
	$sql .= mysql_real_escape_string($tree) . "', '" . $posCtr . "')";
	$rows = mysql_query($sql);

	return $rows;
}

function getCategories($handle){
	$catlist = array();
	while(!feof($handle)) {
		$line = preg_replace("/[^a-zA-Z0-9\s\x2D]/", "", fgets($handle)) . "<br/>";
		//echo $line;
		$tstart = strpos($line, "category id", 0);
		if ($tstart > 0){
			$fstart = $tstart + 11;
			$fend = strpos($line, "\r\n", 0);
			$flength = $fend - $fstart;
			$cat = trim(substr($line, $fstart, $flength));
			$catlist[] = $cat;
		}
	}
	return $catlist;
}

function Import_MetaData($code, $description, $keywords) {
	// write table row
	$sql = "UPDATE Categories SET CA_META_DESC = '" . htmlentities(mysql_real_escape_string($description)) . "', CA_META_KEYWORDS = '" . htmlentities(mysql_real_escape_string($keywords)) . "'";
	$sql .= " WHERE CA_CODE = '" . $code . "'";
	$rows = mysql_query($sql);
	return $rows;
	
	//return mysql_affected_rows();
}

function getProducts($handle){
	$prodlist = array();
	while(!feof($handle)) {
		$line = preg_replace("/[^a-zA-Z0-9\s\x2D]/", "", fgets($handle)) . "<br/>";
		//echo $line;
		$tstart = strpos($line, "product id", 0);
		if ($tstart > 0){
			$fstart = $tstart + 10;
			$fend = strpos($line, "\r\n", 0);
			$flength = $fend - $fstart;
			$prod = trim(substr($line, $fstart, $flength));
			$prodlist[] = $prod;
		}
	}
	return $prodlist;
}

function getCatName($handle){
	$catName = "";
	while(!feof($handle)) {
		$line = preg_replace("/[^a-zA-Z0-9\s\x2D]/", "", fgets($handle)) . "<br/>";
		//echo $line;
		$tstart = strpos($line, "name", 0);
		if ($tstart > 0){
			$fstart = $tstart + 4;
			$fend = strpos($line, " buynow", 0);
			$flength = $fend - $fstart;
			$catName = trim(substr($line, $fstart, $flength));
			//replace all " amp " with "&";
			$catName = str_replace(" amp ", " & ", $catName);
			break;
		}
	}
	rewind($handle);
	
	return $catName;
}

function getCatDesc($handle){
	$catDesc = "";
	while(!feof($handle)) {
		$line = preg_replace("/[^a-zA-Z_0-9\s\x2D\x2E\x5C\]/", "", fgets($handle)) . "<br/>";
		//echo $line;
		$tstart = strpos($line, "Desciption value", 0);
		if ($tstart > 0){
			$fstart = $tstart + 16;
			$fend = strpos($line, "\r\n", 0);
			$flength = $fend - $fstart;
			$catDesc = trim(substr($line, $fstart, $flength));
			break;
		}
	}
	rewind($handle);
	
	return $catDesc;
}

function getImageName($handle){
	$imageName = "";
	$imageFolder = "";
	$imageFields[0] = "";
	$imageFields[1] = "";
	while(!feof($handle)) {
		$line = preg_replace("/[^a-zA-Z_0-9\s\x2D\x2E\x5C\]/", "", fgets($handle)) . "<br/>";
		//$tstart = strpos($line, "North Hants Bikes\dat\images\\", 0);
		$tstart = strpos($line, "MPM Engineering Worktops\dat\images\\", 0);
		if ($tstart > 0){
			$fstart = $tstart + 36;
			$fend = strpos($line, "\r\n", 0);
			$flength = $fend - $fstart;
			$imageName = trim(substr($line, $fstart, $flength));
			$originalImageName = $imageName;
			$imageFolder = "";
			//check whether image is held in a subfolder
			$fstart = 0; $folderstart = 0;
			$fend = 999;
			//echo "Original Image Name=" . $originalImageName . " - ";
			while ($fend > 0){
				$fend = strpos($originalImageName, "\\", $folderstart);
				if($fend > 0){
					$imageFolder = substr($originalImageName, 0, ($fend - $fstart));
					$imageName = substr($originalImageName, $fend + 1);
					$folderstart = $fend + 1;
				}
			}
			$imageFields[0] = $imageName;
			$imageFields[1] = $imageFolder;
			//echo "imageName=" . $imageName . "   imageFolder=" . $imageFolder . "<br/>";	
		}
	}
	rewind($handle);
	
	return $imageFields;
}

function getImageAlt($handle){
	$imageAlt = "";
	while(!feof($handle)) {
		$line = preg_replace("/[^a-zA-Z0-9\s\x2D]/", "", fgets($handle)) . "<br/>";
		//echo $line;
		$tstart = strpos($line, "AltImageText value", 0);
		if ($tstart > 0){
			$fstart = $tstart + 18;
			$fend = strpos($line, "\r\n", 0);
			$flength = $fend - $fstart;
			$imageAlt = trim(substr($line, $fstart, $flength));
			//now replace "jpg" with ".jpg"
			$imageAlt = str_replace("jpg", ".jpg", $imageAlt);
			break;
		}
	}
	rewind($handle);
	
	return $imageAlt;
}

//----ERROR HANDLING --------------------------------------------------------------------------------------------------------------
function errorImportCat($handlelog, $catCode){
	$error = "ERROR!!! - (" . mysql_error() . ") importing category code " . $catCode . " into CATEGORY table - ABORTING!!! ";
	echo $error;
	fwrite($handlelog, "====================================================================================" . "\r\n");
	fwrite($handlelog, $error . "\r\n");
	fwrite($handlelog, "====================================================================================" . "\r\n\r\n");

	return;
}

function errorImportProdcat($handlelog, $product){
	$error = "ERROR!!! - (" . mysql_error() . ") importing product code " . $product . " into PRODCAT table - ABORTING!!! ";
	echo $error;
	fwrite($handlelog, "====================================================================================" . "\r\n");
	fwrite($handlelog, $error . "\r\n");
	fwrite($handlelog, "====================================================================================" . "\r\n\r\n");

	return;
}

function logit($message, $handle, $sSpacing , $fSpacing){
	echo $sSpacing . $message . "<br/>";
	fwrite($handle, $fSpacing . $message . "\r\n");
	
	return;
}

//---- USEFUL FUNCTIONS --------------------------------------------------------------------------------------------------------------
function stripVAT($incVAT) {
	
	$exVAT = round($incVAT / 1.200, 2);
	//if the last digit is a zero the round function will not include it so add zeroes to make up the required 2dp
	if (strpos($exVAT, ".")) {
		$noDigits = (strlen($exVAT)) - (strpos($exVAT, ".") + 1);
	} else {
		$noDigits = 0;
	}
	if ($noDigits == 0) { $exVAT = $exVAT . ".00" ;}
	if ($noDigits == 1) { $exVAT = $exVAT . "0" ;}
	
	return $exVAT;
}

?>

