<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = ""; $rows_written = 0; $rows_rewritten = 0;
$pathName = "/images/";
$scrolltobottom = "";

//initialise screen fields
$selected_product = "";
$code = "";
$name = "";
$image_name = ""; $image_folder = ""; $image_alt = "";
$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";

if (isset($_GET['searchproduct'])) {
	//NEW PRODUCT SELECTED from search dropdown so get product deatils for display 
	$product = getProductDetails($_GET['searchproduct']);
	$selected_product = $product->PR_PRODUCT;
	$code = $product->PR_PRODUCT;
	$name = html_entity_decode($product->PR_NAME);
	$image_name = $product->PR_IMAGE;
	$image_folder = $product->PR_IMAGE_FOLDER;
	$image_alt = html_entity_decode($product->PR_IMAGE_ALT);
	//now get HOTSPOTS
	$hotspots = getHotspots($product->PR_PRODUCT);
	$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";
	foreach($hotspots as $h){
		$hotspot = "hotspot" . $h->HS_NUMBER;
		$$hotspot = $h->HS_DATA;
	}
	
	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_PRODUCT'] = $_GET['searchproduct'];
}

if (isset($_POST['UPDATE'])) {
	//update HOTSPOT 1
	$message = "";
	$fields = array("hs_code"=>$_POST['CODE'], "hs_number"=>1, "hs_data"=>$_POST['HOTSPOT1']);
	//echo "CODE = " . $fields['hs_code'];
	$ok = checkHotspotExists($_POST['CODE'], "1");
	if($ok == "false"){
		if(strlen($_POST['HOTSPOT1']) > 0){
			$rows = Create_Hotspot($fields);
			$rows_written = $rows;
		}
	}else{
		$rows = Rewrite_Hotspot($fields);
		$rows_rewritten = $rows;
	}
	//update HOTSPOT 2
	$fields = array("hs_code"=>$_POST['CODE'], "hs_number"=>2, "hs_data"=>$_POST['HOTSPOT2']);
	$ok = checkHotspotExists($_POST['CODE'], 2);
	if($ok == "false"){
		if(strlen($_POST['HOTSPOT2']) > 0){
			$rows = Create_Hotspot($fields);
			$rows_written = $rows_written + $rows;
		}
	}else{
		$rows = Rewrite_Hotspot($fields);
		$rows_rewritten = $rows_rewritten + $rows;
	}
	//update HOTSPOT 3
	$fields = array("hs_code"=>$_POST['CODE'], "hs_number"=>3, "hs_data"=>$_POST['HOTSPOT3']);
	$ok = checkHotspotExists($_POST['CODE'], 3);
	if($ok == "false"){
		if(strlen($_POST['HOTSPOT3']) > 0){
			$rows = Create_Hotspot($fields);
			$rows_written = $rows_written + $rows;
		}
	}else{
		$rows = Rewrite_Hotspot($fields);
		$rows_rewritten = $rows_rewritten + $rows;
	}
	if ($rows_written + $rows_rewritten > 0){
		$message = "{$rows_written} new HOTSPOT row(s) CREATED and {$rows_rewritten} row(s) UPDATED";
		$warning = "green";	
	}
	if ($rows_written + $rows_rewritten < 1){
		$message .= "WARNING ! ! ! - NO RECORDS UPDATED";
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
		$warning = "red";
	}
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
	//refresh page with new details
	$product = getProductDetails($_POST['CODE']);
	$selected_product = $product->PR_PRODUCT;
	$code = $product->PR_PRODUCT;
	$name = html_entity_decode($product->PR_NAME);
	$image_name = $product->PR_IMAGE;
	$image_folder = $product->PR_IMAGE_FOLDER;
	$image_alt = html_entity_decode($product->PR_IMAGE_ALT);

	$hotspots = getHotspots($_POST['CODE']);
	$hotspot1 = ""; $hotspot2 = ""; $hotspot3 = "";
	foreach($hotspots as $h){
		$hotspot = "hotspot" . $h->HS_NUMBER;
		$$hotspot = $h->HS_DATA;
	}
	
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Amend Hotspots";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Product Hotspots - Create and Amend</h1>
    <p><br /><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is where you create and manage extra content that can be applied to specific products<br /><br />This can be in the form of text, images video or whatever you want<br /><br />Each product page has three hotspot areas;<br /><br />&bull; Hotspot 1 is at the top of the product content area directly after the Title and breadcrumb<br />&bull; Hotspot 2 appears beneath the Main Description area<br />&bull; Hotspot 3 is right at the bottom of the product content page below where related items display</span><span class=\"bottom\"></span></span>" : "") ?></a>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="amend_text" action="/_cms/amend_hotspots.php" method="post">

        <!--- SEARCHBOXES ------------------------------------------------------------------------------------------------------>
    	<tr>
          <td>Search for:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the product you want to work on<br /><br />To select from all products place the mouse cursor in the search field with no other text and click search<br /><br />Then select the desired product from the Choose from... dropdown box</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><Input name="SEARCH_DATA" type="text" size="72" value="<?php echo isset($_POST['SEARCH_DATA']) ? $_POST['SEARCH_DATA'] : "" ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          	    <Input name="SEARCH" type="submit" value="search" class="search-button" /></td>
    	</tr>
        <tr>
        	<td></td>
            <td>
            	<select name="search_results" id="jumpMenu" onchange="MM_jumpMenu('parent',this,1)" class="search-product-box">
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
							echo "<option value=\"/_cms/amend_hotspots.php?searchdata=" . $_POST['SEARCH_DATA'] . "&searchproduct=" . $p->PR_PRODUCT . "\"" . $selected . ">" . $p->PR_PRODUCT . " - " . $p->PR_NAME . "</option>";
						}
					}
					?>
           		</select>
                <?php
				echo "<input type=\"hidden\" name=\"SELECTED_PRODUCT\" value=\"" . (isset($selected_product) ? $selected_product : "") ."\">";
				?>
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the product you wish to add hotspot content to; you need to have searched for it first</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
        </tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
    <!--- END OF SEARCHBOXES ------------------------------------------------------------------------------------------>
		<tr>
        	<td>
            </td>
            <td>
            	<div class="p-display-picture">
					<img name="IMAGE" src="<?php echo $pathName . (strlen($image_folder) > 0 ? $image_folder . "/" : "") . (strlen($image_name) > 0 ? $image_name : "no-image.jpg") ?>" width="200" alt="<?php echo (strlen($image_name) > 0 ? $image_name : "no-image.jpg") ?>" /><br/>	
				</div>

            </td> 
		</tr>   
        <tr>
           	<td>Product Code:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The product code assigned by the software</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><label><strong><?php echo strlen($code ) > 0 ? $code : "" ?></strong></label>
                 	<input type="hidden" name="CODE" SIZE="50" value="<?php echo $code ?>">
        </td>
    	</tr>
        <tr>
            <td>Name:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The name of the product (or service) you're adding hotspot content to</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="NAME" SIZE="50" value="<?php echo $name ?>"></td>
        </tr>
        <tr>
        	<td valign="top">Hotspot 1:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter text or other content that you want to appear below the title and breadcrumb but ABOVE the other product details (including the price and add to cart/buy now button<br /><br />Content can be text, images, scripts or other html<br /><br />Text added for SEO purposes is best entered in hotspot 3</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><textarea type="text" id="HOTSPOT1" name="HOTSPOT1" class="p-amendhot-textarea"><?php echo $hotspot1 ?></textarea></td>
        </tr> 
        <tr>
			<td></td>
			<td>
                <div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/edit_textarea.php?form=amend_text&field=HOTSPOT1","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");>
				<span>Edit</span>
                </a></div>
            	<span style="position: relative; right:18px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
            </td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>
        	<td valign="top">Hotspot 2:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter text or other content that you want to appear below the main long description area but ABOVE the other product elements such as the related items<br /><br />Content can be text, images, scripts or other html<br /><br />This is a good place to put video clips and scripts with active content</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><textarea type="text" id="HOTSPOT2" name="HOTSPOT2" class="p-amendhot-textarea"><?php echo $hotspot2 ?></textarea></td>
        </tr> 
        <tr>
			<td></td>
			<td>
            	<div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/edit_textarea.php?form=amend_text&field=HOTSPOT2","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");>
                    <span>Edit</span>
                </a></div>
				<span style="position: relative; right:18px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
            </td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>
        	<td valign="top">Hotspot 3:<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter text or other content that you want to appear below everything else in the product details, including related items<br /><br />Content can be text, images, scripts or other html<br /><br />A gallery of images and text added for SEO purposes can work well here</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><textarea type="text" id="HOTSPOT3" name="HOTSPOT3" class="p-amendhot-textarea"><?php echo $hotspot3 ?></textarea></td>
        </tr> 
        <tr>
			<td></td>
			<td>
            	<div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("edit_textarea.php?form=amend_text&field=HOTSPOT3","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");>
                    <span>Edit</span>
                </a></div>
            	<span style="position: relative; right:18px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
            </td>
		</tr>         
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>      
		<!--- UPDATE BUTTON ---------->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td>
            	<input name="UPDATE" type="submit" value="Update All Hotspots" class="update-button"> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save your changes in all hotspots</span><span class=\"bottom\"></span></span>" : "") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
<?php
  include_once("includes/footer_admin.php");
?>

