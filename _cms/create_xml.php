<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

//defaults
$message = ""; $xmlCreated = 0;
$scrolltobottom = "";
$filename = "google";
if(isset($_POST['VATORNOT'])){$vat_mode = $_POST['VATORNOT'];}else{$vat_mode = "incVAT";}
$preferences = getPreferences();

if (isset($_POST['CREATE'])) {
	//validate all fields first
	if (strlen($_POST['FILENAME']) == 0){
		$message .= "Please enter a valid File Name" . "<br/>";
		$warning = "red";
	}
	if ($message == ""){
		$filename = $_POST['FILENAME'];
		$message = createXML($filename, $preferences, $vat_mode);
		$warning = "green";
		$xmlCreated = 1;
	}
}

if (isset($_GET['file'])) {
	$filename = $_GET['file'];
}

if (isset($_GET['records'])) {	$message = $_GET['records'] . " products updated into " . $filename . ".xml file";
	$warning = "green";
}
if (isset($_GET['backup']) and $_GET['backup'] == 1) {
		$message .= " - PREVIOUS FILE OF SAME NAME NOW BACKED UP AS .bak";
}
if (isset($_GET['errors']) and $_GET['errors'] != 0) {
		$message = "ERRORS FOUND - PLEASE LOOK AT ERROR LOG!!!";
}

//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Create Google Merchants XML Feed";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="">
    <br/>
	<h1>Create Google Merchants XML feed</h1>
	<br/>
	<form action="/_cms/create_xml.php" method="post">
		<table style="float:left"  border="0" cellpadding="2" cellspacing="5" width="">
			<tr>
            	<td width = "200px"></td>
				<td>File Name (.xml) 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is the name of the file you submit to Google's Merchant Bulk Feed; we've set it to default to google.xml but you can use your preferred feed name: the extension .xml will be added automatically so please don't include it in the name you enter</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="FILENAME" SIZE="32" value="<?php echo $filename ?>"></td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
            	<td width = "200px"></td>
                <td>
                   <p>All Prices inc VAT/Tax<br/>(as per Google Guidelines)</p>
                </td>
                <td>
                <?php if($vat_mode == "incVAT"){$checked = "checked";}else{$checked = "";} ?>
                   <input name="VATORNOT" type="radio" value="incVAT" <?php echo $checked ?>>
                </td>
            </tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <tr>
            	<td width = "100px"></td>
                <td>
                    All Prices ex VAT/Tax<br/>(prices within google.xml<br/>will be net)
                </td>
                <td>
                	<?php if($vat_mode == "exVAT"){$checked = "checked";}else{$checked = "";} ?>
                 	<input name="VATORNOT" type="radio" value="exVAT" <?php echo $checked ?>>
                </td>
            </tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <tr>
            	<td width = "100px"></td>
                <td>
                    All Prices ex VAT/Tax<br/>(only prices of tax exempt<br/>products will be net)
                </td>
                <td>
                	<?php if($vat_mode == "exemptOnly"){$checked = "checked";}else{$checked = "";} ?>
                    <input name="VATORNOT" type="radio" value="exemptOnly" <?php echo $checked ?>>
                </td>
            </tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td><input name="CREATE" type="submit" value="Create Bulk Upload file &raquo;&raquo;"class="create-button" onclick="processingMsg()"></td>
			</tr>
			

            <tr>
				<td colspan="2">
                	<label id="message" class="<?php echo $warning ?>" ><?php echo $message ?></label>
                    <div id="logLink" style="clear:both" ><p>
                    	<div class="preview-button"><a href="/_cms/logs/create_xml.txt" target="_blank"><span>View log</span></a></div> 
                		<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The log will open in a new tab or window when you click this link<br /><br />All empty fields will be reported as an error, if they aren't required for your product types then you can ignore GTIN and MPN; however, do pay attention to any missing Brand and Category errors reported</span><span class=\"bottom\"></span></span>" : "") ?></a>
               		</div>
                </td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
        </table>
        <table style="float:left" border="0" cellpadding="2" cellspacing="5" width="100%">
            <tr>
				<td colspan="2">
                <div style="width:720px; padding-right:20px">
                <p>Google has a number of required attributes in it's specifications for feeds to the Google Merchants system. The minimum requirements are as follows:</p>
                <p>&nbsp;</p>
                <ul>
				  <li><strong>Basic Product Information: </strong>Attributes to describe the basic information about your products, such as title, description and type of product.</li>
				  </ul>
                  <ul>
                    <li><strong>Availability &amp; Price: </strong>Attributes to specify the availability and prices of your listings.</li>
                  </ul>
                  <ul>
                    <li><strong>Unique Product Identifiers: </strong>Attributes to ensure your products appear on the right product page such as brands, UPCs and EANs.</li>
                  </ul>
                  <p>&nbsp;</p>
<p>The first two items in the list are managed by ensuring you complete all the fields in the product details pages when you add or update items for sale in your website. The third item in the list is handled in 1-ecommerce by ticking the &quot;Google Product Search&quot; box in the Preferences &amp; Settings page and then adding appropriate data in at least two of the Google fields on the Product Details pages; preferably three: in the case of clothing three is the minimum.</p>
<p>&nbsp;</p>
                <p>The Google Category and Google Brand fields are mandatory and MUST have data entered. Brand is self explanatory; however, if you make your own goods then you can enter your own business name, otherwise put the manufacturer's name. For guidance on what to enter in the Google Category fied and to access Google's category help tool please visit the <a href="http://support.google.com/merchants/bin/answer.py?hl=en-GB&amp;answer=160081" target="_blank">Categorising Your Products</a> Merchant Centre Help page(scroll down a little to find the category tool.</p>
                <p>&nbsp;</p>
              <p>For an explanation of what Google expects in the two Unique Product Identifiers fields (Google GTIN and Google MPN) see the <a href="http://support.google.com/merchants/bin/answer.py?hl=en-GB&amp;answer=160161" target="_blank">Unique Product Identifiers help page</a>.</p>
              <p>&nbsp;</p></div></td>
			</tr>
            <!---<tr>
				<td nowrap>Message</td>
				<td><input type="text" name="MESSAGE" value="<?php echo "FREDDIE" ?>">
			</tr>--->
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
	</form>
	
<?php
  include_once("includes/footer_admin.php");
?>

<?php
	function createXML($basename, $preferences, $vat_mode){
	//open error log
	$file1 = "logs/create_xml.txt";
	$handle1 = fopen($file1,'w');
	if (!$handle1){
		die("Log file open failure:" . mysql_error());
	}else{
		$today = getdate();
		fwrite($handle1, "=============================" . "\r\n");
		fwrite($handle1, "PRODUCT BULK UPLOAD ERROR LOG - " . date("d/m/y : H:i:s", time()) . "\r\n");
		fwrite($handle1, "=============================" . "\r\n");
	}
	
	$newXML = 1; $offset = 0; $fileNo = 1; $message = ""; $cntr1 = 0; $error_count = 0;
	//get first block of products and loop throught them
	$sql = "SELECT * FROM product ORDER BY PR_PRODUCT LIMIT 5000 OFFSET " . $offset;
	$products = FetchSqlAsObjectArray($sql);
	while(count($products) > 0){
		if($newXML == 1){
			$dir = "xml/"; $filename = $basename;
			$filename .= "_" . $fileNo . ".xml";
			$fullpath = $dir . $filename;
			$backup = 0;
			
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
			$root = $doc->createElement("rss");
			$root->setAttribute('version', '2.0');
			$root->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
			$root = $doc->appendChild($root);
			  
			//create element (channenl)
			$channel = $doc->createElement("channel");
			
			//create header "title", "description" and "link" tags
			$title = $doc->createElement("title");
			$title->appendChild($doc->createTextNode("Products"));
			$channel->appendChild($title);
			$desc = $doc->createElement("description");
			$desc->appendChild($doc->createTextNode("Product Bulk Upload"));
			$channel->appendChild($desc);
			$link = $doc->createElement("link");
			$link->appendChild($doc->createTextNode($preferences->PREF_SHOPURL));
			$channel->appendChild($link);
			$newXML = 0;
		}	 
		foreach($products as $p){
			$cntr1++;
			//item - note that this next read on prodcat will get the first prodcat record only ie. a product may exist in > 1 position on the tree but this get will simply get the first occurence
			$t = getProductTree($p->PR_PRODUCT);
			if($t != ""){
				//item exists within the tree structure
				$item = $doc->createElement("item");
					//BASIC PRODUCT INFORMATION
					//=========================
					//1. ID
						$id = $doc->createElement("g:id");
						$id->appendChild($doc->createTextNode($p->PR_PRODUCT));
						$item->appendChild($id);
						
					//2. Title
						$title = $doc->createElement("title");
						$name = html_entity_decode($p->PR_NAME, ENT_QUOTES);
						if(strlen($name > 70)){$name = substr($name, 0, 70);}
						$title->appendChild($doc->createTextNode($name));
						$item->appendChild($title);
						if(strlen($name) == 0){
							$error_message = "Title field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
						
					//3. Description
						$desc = $doc->createElement("description");
						$product_desc = html_entity_decode($p->PR_DESC_LONG,ENT_QUOTES);
						$desc->appendChild($doc->createTextNode($product_desc));
						$item->appendChild($desc);
						if(strlen($product_desc) == 0){
							$error_message = "Description field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
						
					//4. Google Product Category
						$google_cat = $doc->createElement("g:google_product_category");
						$category = html_entity_decode($p->PR_GOOGLE_CAT, ENT_QUOTES);
						$google_cat->appendChild($doc->createTextNode($category));
						$item->appendChild($google_cat);
						if(strlen($category) == 0){
							$error_message = "Google Product Category field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
						
					//5. Product Type - taken from our own menu structure (ie the local equivalent of the Google Product Category
						$type = "";
						$prodcat = GetProductFromProdcat($p->PR_PRODUCT);
						foreach($prodcat as $pc){
							//product may appear in > 1 category so just get the first occurence
							break;
						}
						// eg tree_node = 0_CAAA008_CAAA009. Starting at third character strip out each category code,find it's name and then add the name to the Product Type
						$tree_node = $pc->PC_TREE_NODE; $length = strlen($tree_node);
						for($start = 2; $start != 0; $start ++){
							$pos = strpos($tree_node, "_", $start);
							$cat = substr($tree_node, $start, 7);
							//get category name
							$category = getCategory($cat, "");
							if($type != ""){$type .= " > ";}
							$type .= html_entity_decode($category->CA_NAME, ENT_QUOTES);
							$start = $pos;
						}
						$product_type = $doc->createElement("g:product_type");
						$product_type->appendChild($doc->createTextNode($type));
						$item->appendChild($product_type);
						if(strlen($type) == 0){
							$error_message = "Product Type field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
					
					//6. Link (Item)
						//create link using the prodcat tree node found within 5. above
						$link = $doc->createElement("link");
						$product_link = $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($p->PR_NAME, ENT_QUOTES)) . "/" . $pc->PC_TREE_NODE . "/" . $p->PR_PRODUCT . ".htm";
						$link->appendChild($doc->createTextNode($product_link ));
						$item->appendChild($link);
						if(strlen($product_link) == 0){
							$error_message = "Product Link field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
		
					//7. Image Link
						$image = $doc->createElement("g:image_link");
						$imagePath = "/images/";
						if(strlen($p->PR_IMAGE_FOLDER) > 0){$imagePath .= html_entity_decode($p->PR_IMAGE_FOLDER, ENT_QUOTES) . "/";}
						$imagePath .= html_entity_decode($p->PR_IMAGE, ENT_QUOTES);
						$image_link = $preferences->PREF_SHOPURL . $imagePath;
						$image->appendChild($doc->createTextNode($image_link));
						$item->appendChild($image);
						if(strlen($image_link) == 0){
							$error_message = "Product Image Link field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
						
					//8. Additional Image Link
						//include an attribute for each additional image
						$additional = getAdditionalImages($p->PR_PRODUCT);
						foreach($additional as $ai){
							$image = $doc->createElement("g:additional_image_link");
							$imagePath = "/images/";
							if(strlen($ai->PRA_IMAGE_FOLDER) > 0){$imagePath .= html_entity_decode($ai->PRA_IMAGE_FOLDER,ENT_QUOTES) . "/";}
							$imagePath .= html_entity_decode($ai->PRA_IMAGE, ENT_QUOTES);
							$image->appendChild($doc->createTextNode($preferences->PREF_SHOPURL . $imagePath));
							$item->appendChild($image);
						}	
					//9. Condition
						$condition = $doc->createElement("g:condition");
						$condition->appendChild($doc->createTextNode($p->PR_GOOGLE_CONDITION));
						$item->appendChild($condition);
						
					//AVAILABILTY AND PRICE
					//=====================
					//1. Availability
						$google_avail = $doc->createElement("g:availability");
						$availability = html_entity_decode($p->PR_AVAILABILITY);
						$google_avail->appendChild($doc->createTextNode($availability));
						$item->appendChild($google_avail);
						if(strlen($availability) == 0){
							$error_message = "Availability field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
						
					//2. Price (full price including VAT)
						$price = $doc->createElement("g:price");
						if($p->PR_TAX > 0){
							$vatrate = $p->PR_TAX;
						}else{
							$vatrate = $preferences->PREF_VAT;
						}
						switch ($vat_mode){
							case "incVAT":
								$vatinc = addVAT($p->PR_SELLING, $vatrate);
								$price->appendChild($doc->createTextNode($vatinc));
								break;
							case "exVAT":
								$vatinc = $p->PR_SELLING;
								$price->appendChild($doc->createTextNode($vatinc));
								break;
							case "exemptOnly":
								if($p->PR_TAXEXEMPTION == "Y"){
									$vatinc = $p->PR_SELLING;
									$price->appendChild($doc->createTextNode($vatinc));
								}else{
									$vatinc = addVAT($p->PR_SELLING, $vatrate);
									$price->appendChild($doc->createTextNode($vatinc));
								}
								break;
							default:
						}
						$item->appendChild($price);
						if(strlen($vatinc) == 0){
							$error_message = "Price field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
						
					//3. Sale Price - Not mandatory
					//4. Sale Price Effective Date - not mandatory
			
					//UNIQUE PRODUCT IDENTIFIERS
					//==========================
					//1. Brand
						$google_brand = $doc->createElement("g:brand");
						$brand = html_entity_decode($p->PR_GOOGLE_BRAND, ENT_QUOTES);
						$google_brand->appendChild($doc->createTextNode($brand));
						$item->appendChild($google_brand);
						if(strlen($brand) == 0){
							$error_message = "Brand field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
						
					//2. Global Trade Item Number (GTIN)
						$google_gtin = $doc->createElement("g:gtin");
						$gtin = html_entity_decode($p->PR_GOOGLE_GTIN);
						$google_gtin->appendChild($doc->createTextNode($gtin));
						$item->appendChild($google_gtin);
						if(strlen($gtin) == 0){
							$error_message = "Global Trade Item Number (GTIN) field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
						
					//3. Manufacturer Product Number (MPN)
						$google_mpn = $doc->createElement("g:mpn");
						$mpn = html_entity_decode($p->PR_GOOGLE_MPN);
						$google_mpn->appendChild($doc->createTextNode($mpn));
						$item->appendChild($google_mpn);
						if(strlen($mpn) == 0){
							$error_message = "Manufacturer Product Number (MPN) field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
					
					//PRODUCT VARIANTS - REQUIRED FOR US TARGET MARKET ONLY
					//================
					//1. Item Group ID
					//2. Color
					//3. Material
					//4. Pattern
					//5. Size
					//APPAREL PRODUCTS - REQUIRED FOR US TARGET MARKET ONLY
					//================
					//1. Gender
					//2. Age Group
					//3. Color
					//4. Size
					//TAX AND SHIPPING - NOT MANDATORY
					//================
					//1. Tax
					//2. Shipping
					//3. Shipping Weight
						if($p->PR_WEIGHT != 0){
						  $google_weight = $doc->createElement("g:shipping_weight");
						  $weight = html_entity_decode($p->PR_WEIGHT) . " kg";
						  $google_weight->appendChild($doc->createTextNode($weight));
						  $item->appendChild($google_weight);
						}else{
							//non-mandatory field so do not write line if weight is zero - just issue a warning
							$error_message = "Delivery (Shipping) Weight field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
					//NEARBY STORES - NOT MANDATORY
					//=============
					//1. Online Only
					//LOYALTY POINTS - JAPAN ONLY
					//==============
					//1. Loyalty Points
					//ADDITIONAL ATTRIBUTES - NOT MANDATORY
					//=====================
					//1. Excluded Destination
					//2. Expiration Date
					//ADWORDS ATTRIBUTES - MANDATORY AS OF MARCH 2013
					//=====================
					//1. Adwords Grouping
						$google_adwords_grouping = $doc->createElement("g:adwords_grouping");
						$grouping = html_entity_decode($p->PR_GOOGLE_ADWORDS_GROUPING);
						$google_adwords_grouping->appendChild($doc->createTextNode($grouping));
						$item->appendChild($google_adwords_grouping);
						if(strlen($grouping) == 0){
							$error_message = "Google Adwords Grouping field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
					//2. Adwords Labels
						if($p->PR_GOOGLE_ADWORDS_LABELS != ""){
							$labels_array = explode(",", $p->PR_GOOGLE_ADWORDS_LABELS);
							for($i = 0; $i < count($labels_array); $i++){
								$google_adwords_labels = $doc->createElement("g:adwords_labels");
								$label = trim($labels_array[$i]);
								$google_adwords_labels->appendChild($doc->createTextNode($label));
								$item->appendChild($google_adwords_labels);
							}
						}else{
							$google_adwords_labels = $doc->createElement("g:adwords_labels");
							$google_adwords_labels->appendChild($doc->createTextNode(""));
							$item->appendChild($google_adwords_labels);
							$error_message = "Google Adwords Labels field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
					//3. Adwords Redirect
						$google_adwords_redirect = $doc->createElement("g:adwords_redirect");
						$redirect = ($p->PR_GOOGLE_ADWORDS_REDIRECT != "") ? $p->PR_GOOGLE_ADWORDS_REDIRECT : $product_link;
						$google_adwords_redirect->appendChild($doc->createTextNode($redirect));
						$item->appendChild($google_adwords_redirect);
						if(strlen($redirect) == 0){
							//this should never be the case now
							$error_message = "Google Adwords Redirect field is Blank";
							fwrite($handle1, "Product " . $p->PR_PRODUCT . " - " . $error_message . "\r\n");
							$error_count ++;	
						}
				//write item to structure
				$channel->appendChild($item);
			}
		}
		$root->appendChild($channel);
		//echo $doc->saveXML();
		$doc->save($fullpath);
		$message .= "File Created - {$fullpath}<br/>";
		$offset += 5000; $fileNo ++; $newXML = 1;
		//get next block of products
		$sql = "SELECT * FROM product ORDER BY PR_PRODUCT LIMIT 5000 OFFSET " . $offset;
		$products = FetchSqlAsObjectArray($sql);
	}
	fclose($handle1);
	$message .= "<br/>Total Records processed = {$cntr1}<br/><br/>";

	return $message;
}
?> 
<script type="text/javascript">
	function processingMsg(){
		document.getElementById("message").innerHTML = "<b>Processing Data - this may take a few minutes ......</b>";
	}
</script>