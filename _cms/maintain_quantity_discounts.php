<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = ""; $errors_array = array();
$discount_type = "";
$scrolltobottom = "";

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Amend Products";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

//initialise screen fields
$selected_product = "";
$productcode = "";
$name = ""; $sku = "";
$desc_short = ""; $desc_long = ""; $new = "Y";

if (isset($_POST['DELETE'])) {
	//delete quantity discount header and all discount lines against the product
	$qdh = Get_Qdiscount($_POST['PRODUCT']);
	if($qdh){
		$qdl_qdh_id = $qdh->QDH_ID; $error_write = 0;
		$fields = array("qdl_qdh_id"=>$qdl_qdh_id);
		$rows_qdl = Delete_Qdiscount_lines($fields);
		if($rows_qdl == 0){$error_write = 1;}
		
		$fields = array("qdh_product"=>$_POST['PRODUCT']);
		$rows_qdh = Delete_Qdiscount_header($fields);
		if($rows_qdh != 1){$error_write = 1;}
		if ($error_write == 0){
			$errors_array[] = "Product record successfully DELETED";
			$warning = "green";
			//now update quantity_discount.xml
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->load( '../xml/quantity_discount.xml' );
			$root = $doc->getElementsByTagName("qdset")->item(0);
			//find and delete node
			$xpath = new DOMXPath($doc);
			$product = $xpath->query($_POST['PRODUCT'])->item(0);
			$product->parentNode->removeChild($product);
			//now save the xml file
			 $doc->save('../xml/quantity_discount.xml');
			 
			 //initialise screen fields
			$selected_product = "";
			$productcode = "";
			$name = ""; $sku = "";
			$desc_short = ""; $desc_long = ""; $new = "Y";
		}
		if ($rows_qdh == 0 and $rows_qdl == 0){
			$errors_array[] = "WARNING ! ! ! - NO RECORDS DELETED";
			$warning = "orange";
		}
		if ($error_write == 1){
			$errors_array[] = "DELETION FAILURE - PLEASE CONTACT SHOPFITTER!!!";
			$warning = "red";
		}
		
	}else{
		//must have hit the delete button when no product has been selected
	}
	$_GET['searchdata'] = $_POST['SEARCH_DATA']; $_GET['searchproduct'] = $_POST['PRODUCT'];
}

if (isset($_GET['searchproduct'])) {
	//NEW PRODUCT SELECTED from search dropdown so get product deatils for display 
	$product = getProductDetails($_GET['searchproduct']);
	$selected_product = $product->PR_PRODUCT;
	$productcode = $product->PR_PRODUCT;
	$name = html_entity_decode($product->PR_NAME, ENT_QUOTES);

	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_PRODUCT'] = $_GET['searchproduct'];
	$discount = Get_Qdiscount($productcode);
	if($discount){
		$discount_type = $discount->QDH_TYPE;
		$discounts = Get_Qdiscount_Lines($productcode);
		$new = "N";
	}else{
		//set defaults
		$discount_type = "PP"; $discounts = array(); $new = "Y";
	}
}

if (isset($_GET['del'])) {
	//delete line from table 
	Delete_Table_Row($_GET['del']);
}

if (isset($_POST['UPDATE']) and $_POST['SELECTED_PRODUCT'] != "") {
	$message = "";
	//validate all fields first
	if ($message == ""){
		//no error message so update database product table
		$rows_qdh = 0; $error_write = 0;
		if($_POST['NEW_DISCOUNT'] == "Y"){
			$fields = array("qdh_product"=>$_POST['PRODUCT'], "qdh_type"=>$_POST['TYPE']);
			$rows_qdh = Create_Qdiscount_header($fields);
			if($rows_qdh != 1){$error_write = 1;}
			$qdh = Get_Qdiscount($_POST['PRODUCT']);
			$qdl_qdh_id = $qdh->QDH_ID;
		}else{
			$fields = array("qdh_product"=>$_POST['PRODUCT'], "qdh_type"=>$_POST['TYPE']);
			$rows_qdh = Rewrite_Qdiscount_header($fields);
			$qdh = Get_Qdiscount($_POST['PRODUCT']);
			$qdl_qdh_id = $qdh->QDH_ID;
			//delete existing discount lines before writing the new table row values to file
			$fields = array("qdl_qdh_id"=>$qdl_qdh_id);
			$rows_delete = Delete_Qdiscount_lines($fields);
			if($rows_delete == 0){$error_write = 1;}
		}
		$rows_qdl = 0;
		for($i = 0; $i < $_POST['ROW_COUNT'] - 1; $i++){
			$qty = $_POST['QTY_' . $i]; $adjust = $_POST['ADJUST_' . $i];
			$fields = array("qdl_qdh_id"=>$qdl_qdh_id, "qdl_qty"=>$qty, "qdl_adjust"=>$adjust);
			$rows = Create_Qdiscount_line($fields);
			$rows_qdl += $rows;
			if($rows_qdl == 0){$error_write = 1;}
		}
		if ($error_write == 0){
			$errors_array[] = "Product record successfully UPDATED";
			$warning = "green";
			//now update quantity_discount.xml
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->load( '../xml/quantity_discount.xml' );
			$root = $doc->getElementsByTagName("qdset")->item(0);
			if($_POST['NEW_DISCOUNT'] == "Y"){
				$product = $doc->createElement($_POST['PRODUCT']);
				$type = $doc->createElement("type");
					$type->appendChild($doc->createTextNode($_POST['TYPE']));
					$product->appendChild($type);
				//now loop through the matrix and add rows
				for($i = 0; $i < $_POST['ROW_COUNT'] - 1; $i++){
					$qty = $_POST['QTY_' . $i]; $adjust = $_POST['ADJUST_' . $i];
					$quantity = $doc->createElement("quantity");
						$quantity->appendChild($doc->createTextNode($qty));
						$product->appendChild($quantity);
					$discount = $doc->createElement("discount");
						$discount->appendChild($doc->createTextNode($adjust));
						$product->appendChild($discount);
					$root->appendChild($product);
				}
			}else{
				//find the product node and delete all children
				$xpath = new DOMXPath($doc);
				$product = $xpath->query($_POST['PRODUCT'])->item(0);
				$nodestoremove = array();
				foreach ($product->childNodes as $node) {
					//if ($node->nodeName != "type") {
						$nodestoremove[] = $node;
					//}
				}
				foreach ($nodestoremove as $node) {
					$product->removeChild($node);
				}
				unset($nodestoremove); // so nodes can be garbage-collected
				//rewrite the discount type just in case it's been changed
				$type = $doc->createElement("type");
					$type->appendChild($doc->createTextNode($_POST['TYPE']));
					$product->appendChild($type);
					
				//now loop through the matrix and add new rows
				for($i = 0; $i < $_POST['ROW_COUNT'] - 1; $i++){
					$qty = $_POST['QTY_' . $i]; $adjust = $_POST['ADJUST_' . $i];
					$quantity = $doc->createElement("quantity");
						$quantity->appendChild($doc->createTextNode($qty));
						$product->appendChild($quantity);
					$discount = $doc->createElement("discount");
						$discount->appendChild($doc->createTextNode($adjust));
						$product->appendChild($discount);
				}
			}
			//now save the xml file
			 $doc->save('../xml/quantity_discount.xml');
		}
		if ($rows_qdh == 0 and $rows_qdl == 0){
			$errors_array[] = "WARNING ! ! ! - NO RECORDS UPDATED";
			$warning = "orange";
		}
		if ($error_write == 1){
			$errors_array[] = "ERROR ! ! ! - MORE THAN ONE (" . $rows_qdh . ") RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
			$warning = "red";
		}
	}
	//refresh page with new details
	$product = getProductDetails($_POST['PRODUCT'], "");
	$selected_product = $product->PR_PRODUCT;
	$productcode = $product->PR_PRODUCT;
	$name = html_entity_decode($product->PR_NAME, ENT_QUOTES);
	$discount = Get_Qdiscount($productcode);
	if($discount){
		$discount_type = $discount->QDH_TYPE;
		$discounts = Get_Qdiscount_Lines($productcode);
		$new = "N";
	}else{
		//error
	}

	//if($message != "" or count($errors_array) != 0){$scrolltobottom = "onLoad=\"scrollTo(0,3000)\" ";}
}

include_once("includes/header_admin.php");

?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Create/Amend Quantity Discounts - Update Quantity Discount Table</h1>
	<br/>
    <form name="enter_thumb" action="/_cms/maintain_quantity_discounts.php" enctype="multipart/form-data" method="post" onsubmit="return validate_discount_table();">
    	<table align="left" border="0" cellpadding="2" cellspacing="5">
        	<!--- SEARCHBOXES ------------------------------------------------------------------------------------------------------>
            <tr>
              <td class="quantity-td">Search for: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the product you want to work on<br /><br />To select from all products place the mouse cursor in the search field with no other text and click search<br /><br />Then select the desired product from the Choose from... dropdown box</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
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
                                echo "<option value=\"/_cms/maintain_quantity_discounts.php?searchdata=" . $_POST['SEARCH_DATA'] . "&searchproduct=" . $p->PR_PRODUCT . "\"" . $selected . ">" . $p->PR_PRODUCT . " - " . html_entity_decode($p->PR_NAME) . "</option>";
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
            <?php if($productcode != ""): ?>
                <!-- only display fields if a product has been chosen from the dropdown -->
                <tr>
                    <td>Product Code: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The code of the selected product</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                    <td><label><strong><?php echo strlen($productcode ) > 0 ? $productcode : "" ?></strong></label>
                            <input type="hidden" name="PRODUCT" SIZE="50" value="<?php echo $productcode ?>">
                </td>
                </tr>
				
				<tr>
					<td colspan="2" class="td-sep">&nbsp;</td>		
				</tr>
						
                <tr>
                    <td>Name: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The name of the selected product<br /><br /></span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                    <td><label><strong><?php echo strlen($name ) > 0 ? html_entity_decode($name, ENT_QUOTES) : "" ?></strong></label></td>
                </tr>
				
				<tr>
					<td colspan="2" class="td-sep">&nbsp;</td>		
				</tr>
				
                <tr>
                    <td>Discount Type: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the options you want to apply to this product<br /><br />You'll need to set the options up first in the Options section from the menu on the left<br /><br />You can create general options that can be applied to all products or options available for only a specific product<br /><br />Up to 4 option sets can be added to each product</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                    <td>
                        <select name="TYPE" onchange="check_header(this);">
                         	<option value="PP" <?php echo ($discount_type == "PP") ? "selected" : "" ?> >Price Point</option>
                            <option value="PC" <?php echo ($discount_type == "PC") ? "selected" : "" ?> >Percentage Discount</option>
                            <option value="V" <?php echo ($discount_type == "V") ? "selected" : "" ?> >Value Discount</option>
                        </select>
                    </td> 
                </tr>   
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>		
		</tr>
            <?php endif; ?>
        </table>
    	<!--- END OF SEARCHBOXES ------------------------------------------------------------------------------------------>
        <?php if($productcode != ""): ?>
        	<!-- only display table if a product has been chosen from the dropdown -->
            <table id="quantity_discounts" align="left" border="" cellpadding="2" cellspacing="5">
                <?php 
				$cntr1 = 0; //the actual number of rows
				if(isset($discounts) and count($discounts) > 0){
					if($discount_type == "PP"){$char = "£"; $adjust_type = "Price";}
					if($discount_type == "PC"){$char = "-%"; $adjust_type = "Adjustment";}
					if($discount_type == "V"){$char = "-£"; $adjust_type = "Adjustment";}
					echo "<tr>";
						echo "<td><b>Quantity</b></td><td id=\"td-type\"><b>" . $adjust_type . "(" . $char . ")</b></td>";
					echo "</tr>";
					foreach($discounts as $d){
						echo "<tr name=\"ROW_" . $cntr1 . "\">";
							echo "<td>";
								echo "<input name=\"QTY_" . $cntr1 . "\" class=\"\" type=\"text\" size=\"4\" value=\"" . $d->QDL_QTY . "\" />";
							echo "</td>";
							echo "<td>";
							echo "<input name=\"ADJUST_" . $cntr1 . "\" class=\"\" type=\"text\" size=\"4\" value=\"" . $d->QDL_ADJUST . "\" />";
							echo "</td>";
							echo "<td>";
							echo "<a href=\"#\" ><span style=\"color: red;\" onclick=\"delete_table_row(" .$cntr1 . ");\">delete line</span></a>";
							echo "</td>";
						echo "</tr>";
						$cntr1++;
					}
				}else{
					//new set of quantity discounts
					if($discount_type == "PP"){$char = "£"; $adjust_type = "Price";}
					if($discount_type == "PC"){$char = "-%"; $adjust_type = "Adjustment";}
					if($discount_type == "V"){$char = "-£"; $adjust_type = "Adjustment";}
					echo "<tr>";
						echo "<td><b>Quantity</b></td><td id=\"td-type\"><b>Price(£)</b></td>";
					echo "</tr>";
					
				}
				//add a further blank row for additional input
				echo "<tr name=\"ROW_" . $cntr1 . "\">";
					echo "<td>";
						echo "<input name=\"QTY_" . $cntr1 . "\" class=\"\" type=\"text\" size=\"4\" value=\"\" />";
					echo "</td>";
					echo "<td>";
					echo "<input name=\"ADJUST_" . $cntr1 . "\" class=\"\" type=\"text\" size=\"4\" value=\"\" onkeyup=\"add_table_row();\" />";
					echo "</td>";
					echo "<td>";
					//echo "<a href=\"#\" ><span style=\"color: red;\" onclick=\"delete_table_row(" .$cntr1 . ");\">delete line</span></a>";
						echo "&nbsp;";
					echo "</td>";
				echo "</tr>";
				$cntr1++;
				?> 				
				
            </table>
            <table id="update_buttons" align="left" cellpadding="2" cellspacing="5">    
                <!--- UPDATE BUTTON ---------->
                <tr>
                    <td>
                        <input name="UPDATE" type="submit" value="Update Product&raquo;&raquo;" class="update-button"> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save your changes</span><span class=\"bottom\"></span></span>" : "") ?></a>
                        <input name="DELETE" type="submit" value="Delete Product &raquo;&raquo;" class="delete-button"> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to delete this product</span><span class=\"bottom\"></span></span>" : "") ?></a>
                 		<input id="NEW_DISCOUNT" name="NEW_DISCOUNT" type="hidden" value="<?php echo $new ?>" />
                        <input id="ROW_COUNT" name="ROW_COUNT" type="hidden" value="<?php echo $cntr1 ?>" />
                 	</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                    	<div class="member_errors">
						
							<?php
                            foreach($errors_array as $e){
                                echo "<label id=\"MESSAGE_ARRAY\"class=\"" . $warning ."\">" . $e . "</label><br/>";	
                            }
                            ?>
                        </div>
                    </td>
                </tr>
            </table>
        <?php endif; ?>
    </form>
<?php
  include_once("includes/footer_admin.php");
?>
<script src="/_cms/scripts/maintain_quantity_discounts.js" type="text/javascript"></script>

<script type="text/javascript">
function check_header(element){
	//sets the quantity discount table header to match the selected discount type
	var cell = document.getElementById("td-type");
	switch (element.value){
		case "PP":
			cell.innerHTML = "<b>Price(£)</b>";
			break;
		case "PC":
			cell.innerHTML = "<b>Adjustment(-%)</b>";
			break;
		case "V":
			cell.innerHTML = "<b>Adjustment(-£)</b>";
			break;
		default:
			break;
	}
	return;
}
</script>