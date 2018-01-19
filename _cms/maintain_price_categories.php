<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = ""; $errors_array = array(); $warning = "";
$discount_type = "";
$scrolltobottom = "";

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Manage Price Categories";

//initialise screen fields
$selected_product = "MASTER";
$productcode = "MASTER";
$name = "All Products"; $sku = "";
$desc_short = ""; $desc_long = ""; $new = "Y";

//get MASTER (default) details
$discount = Get_Pcat($productcode);
if($discount){
	$discount_type = $discount->PCH_TYPE;
	$discounts = Get_Pcat_Lines($productcode);
	$new = "N";
}else{
	//set defaults
	$discount_type = "PC"; $discounts = array(); $new = "N";
}

if (isset($_POST['DELETE'])) {
	if($_POST['PRODUCT'] != "MASTER"){
		//delete quantity discount header and all discount lines against the product
		$pch = Get_Pcat($_POST['PRODUCT']);
		if($pch){
			$pcl_pch_id = $pch->PCH_ID; $error_write = 0;
			$fields = array("pcl_pch_id"=>$pcl_pch_id);
			$rows_pcl = Delete_Pcat_lines($fields);
			if($rows_pcl == 0){$error_write = 1;}
			
			$fields = array("pch_product"=>$_POST['PRODUCT']);
			$rows_pch = Delete_Pcat_header($fields);
			if($rows_pch != 1){$error_write = 1;}
			if ($error_write == 0){
				$errors_array[] = "Product record successfully DELETED";
				$warning = "green";
				 
				 //initialise screen fields
				$selected_product = "";
				$productcode = "";
				$name = ""; $sku = "";
				$desc_short = ""; $desc_long = ""; $new = "Y";
			}
			if ($rows_pch == 0 and $rows_pcl == 0){
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
	}else{
		$errors_array[] = "UNABLE TO DELETE MASTER PRODUCT!!!";
		$warning = "red";
	}
}

if (isset($_GET['searchproduct'])) {
	//NEW PRODUCT SELECTED from search dropdown so get product deatils for display 
	if($_GET['searchproduct'] != "MASTER"){
		$product = getProductDetails($_GET['searchproduct']);
		$selected_product = $product->PR_PRODUCT;
		$productcode = $product->PR_PRODUCT;
		$name = html_entity_decode($product->PR_NAME, ENT_QUOTES);
	}else{
		$selected_product = "MASTER";
		$productcode = "MASTER";
		$name = "All Products";
	}

	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_PRODUCT'] = $_GET['searchproduct'];
	$discount = Get_Pcat($productcode);
	if($discount){
		$discount_type = $discount->PCH_TYPE;
		$discounts = Get_Pcat_Lines($productcode);
		$new = "N";
	}else{
		//set defaults
		$discount_type = "PC"; $discounts = array(); $new = "Y";
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
			$fields = array("pch_product"=>$_POST['PRODUCT'], "pch_type"=>$_POST['TYPE']);
			$rows_pch = Create_Pcat_header($fields);
			if($rows_pch != 1){$error_write = 1;}
			$pch = Get_Pcat($_POST['PRODUCT']);
			$pcl_pch_id = $pch->PCH_ID;
		}else{
			$fields = array("pch_product"=>$_POST['PRODUCT'], "pch_type"=>$_POST['TYPE']);
			$rows_pch = Rewrite_Pcat_header($fields);
			$pch = Get_Pcat($_POST['PRODUCT']);
			$pcl_pch_id = $pch->PCH_ID;
			//delete existing discount lines before writing the new table row values to file
			$fields = array("pcl_pch_id"=>$pcl_pch_id);
			$rows_delete = Delete_Pcat_lines($fields);
			if($rows_delete == 0 and $_POST['PRODUCT'] != "MASTER"){$error_write = 1;}
		}
		$rows_qdl = 0;
		for($i = 0; $i < $_POST['ROW_COUNT'] - 1; $i++){
			$pcat = $_POST['PCAT_' . $i]; $adjust = $_POST['ADJUST_' . $i];
			$fields = array("pcl_pch_id"=>$pcl_pch_id, "pcl_cat"=>$pcat, "pcl_adjust"=>$adjust);
			$rows = Create_Pcat_line($fields);
			$rows_qdl += $rows;
			//note that you can't delete the pricecath MASTER record so...
			//if you want to remove all the MASTER line entries then you have to delete each line separately, which means you end up with a header and no lines.
			//This is OK. The product specific price categories however should NEVER have a header and no lines.
			//Just bear this in mind when triggering any error messages
			if($rows_qdl == 0 and $_POST['PRODUCT'] != "MASTER"){$error_write = 1;}
		}
		if ($error_write == 0){
			$errors_array[] = "Product record successfully UPDATED";
			$warning = "green";
		}
		if (($rows_qdh == 0 and $rows_qdl == 0) and $_POST['PRODUCT'] != "MASTER"){
			$errors_array[] = "WARNING ! ! ! - NO RECORDS UPDATED";
			$warning = "orange";
		}
		if ($error_write == 1){
			$errors_array[] = "ERROR ! ! ! - MORE THAN ONE (" . $rows_qdh . ") RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
			$warning = "red";
		}
	}
	//refresh page with new details
	if($_POST['PRODUCT'] != "MASTER"){
		$product = getProductDetails($_POST['PRODUCT'], "");
		$selected_product = $product->PR_PRODUCT;
		$productcode = $product->PR_PRODUCT;
		$name = html_entity_decode($product->PR_NAME, ENT_QUOTES);
	}else{
		$selected_product = "MASTER";
		$productcode = "MASTER";
		$name = "All Products";
	}
	$discount = Get_Pcat($productcode);
	if($discount){
		$discount_type = $discount->PCH_TYPE;
		$discounts = Get_Pcat_Lines($productcode);
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
	<h1>Create/Amend Price Categories - Update Price Categories Table</h1>
	<br/>
    <form name="enter_thumb" action="/_cms/maintain_price_categories.php" enctype="multipart/form-data" method="post" onsubmit="return validate_discount_table();">
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
                        <?php ($_POST['SELECTED_PRODUCT'] and $_POST['SELECTED_PRODUCT'] == "MASTER") ? $selected = "selected" : $selected = ""; ?>
                        <option value="/_cms/maintain_price_categories.php?searchdata=&searchproduct=MASTER" <?php echo $selected ?> >MASTER - All Products</option>

                        <?php
                        if (isset($_POST['SEARCH_DATA'])){
                            $products = Search_product($_POST['SEARCH_DATA']);
                            foreach($products as $p){
                                if(isset($_POST['SELECTED_PRODUCT']) and $_POST['SELECTED_PRODUCT'] == $p->PR_PRODUCT){
                                    $selected = "selected";
                                }else{
                                    $selected = "";
                                }
                                echo "<option value=\"/_cms/maintain_price_categories.php?searchdata=" . $_POST['SEARCH_DATA'] . "&searchproduct=" . $p->PR_PRODUCT . "\"" . $selected . ">" . $p->PR_PRODUCT . " - " . html_entity_decode($p->PR_NAME) . "</option>";
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
                    <td>Product Code: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The code of the selected product<br>MASTER affects price of all products</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
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
                    <td>Discount Type: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Adjustment by percentage of the trade/member price.<br><br>Example: to mark up the price by 50% enter 150 in the adjustment field</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                    <td>
                        <select name="TYPE" onchange="check_header(this);">
                        	<!-- Only Percentage Discounts allowed as of 04/12/13 -->
                            <option value="PC" <?php echo ($discount_type == "PC") ? "selected" : "" ?> >Percentage Adjustment</option>
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
            <table id="price_cat_discounts" align="left" border="" cellpadding="2" cellspacing="5">
                <?php 
				$cntr1 = 0; //the actual number of rows
				if(isset($discounts) and count($discounts) > 0){
					if($discount_type == "PP"){$char = "£"; $adjust_type = "Price";}
					if($discount_type == "PC"){$char = "%"; $adjust_type = "Adjustment";}
					if($discount_type == "V"){$char = "-£"; $adjust_type = "Adjustment";}
					echo "<tr>";
						echo "<td class='pc_pcat'><b>Price Category</b></td><td id=\"td-type\" class='pc_adjust'><b>" . $adjust_type . "(" . $char . ")</b></td><td class='pc_delete'>&nbsp;</td>";
					echo "</tr>";
					foreach($discounts as $d){
						echo "<tr name=\"ROW_" . $cntr1 . "\">";
							echo "<td class='pc_pcat'>";
								echo "<input name=\"PCAT_" . $cntr1 . "\" class=\"\" type=\"text\" size=\"4\" value=\"" . $d->PCL_CAT . "\" onKeyUp=\"this.value=this.value.toUpperCase()\"/>";
							echo "</td>";
							echo "<td class='pc_adjust'>";
							echo "<input name=\"ADJUST_" . $cntr1 . "\" class=\"\" type=\"text\" size=\"4\" value=\"" . $d->PCL_ADJUST . "\" />";
							echo "</td>";
							echo "<td class='pc_delete'>";
							echo "<a href=\"#\" ><span style=\"color: red;\" onclick=\"delete_table_row(" .$cntr1 . ");\">delete line</span></a>";
							echo "</td>";
						echo "</tr>";
						$cntr1++;
					}
				}else{
					//new set of price categories
					if($discount_type == "PP"){$char = "£"; $adjust_type = "Price";}
					if($discount_type == "PC"){$char = "%"; $adjust_type = "Adjustment";}
					if($discount_type == "V"){$char = "-£"; $adjust_type = "Adjustment";}
					echo "<tr>";
						echo "<td><b>Price Category</b></td><td id=\"td-type\"><b>Adjustment</b></td>";
					echo "</tr>";
					
				}
				//add a further blank row for additional input
				echo "<tr name=\"ROW_" . $cntr1 . "\">";
					echo "<td class='pc_pcat'>";
						echo "<input name=\"PCAT_" . $cntr1 . "\" class=\"\" type=\"text\" size=\"4\" value=\"\" onKeyUp=\"this.value=this.value.toUpperCase()\" />";
					echo "</td>";
					echo "<td class='pc_adjust'>";
					echo "<input name=\"ADJUST_" . $cntr1 . "\" class=\"\" type=\"text\" size=\"4\" value=\"\" onkeyup=\"add_table_row();\" />";
					echo "</td>";
					echo "<td class='pc_delete'>";
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
                        &nbsp;&nbsp;&nbsp;
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
							echo "<label id=\"MESSAGE\"class=\"" . $warning ."\"></label><br/>";
                            foreach($errors_array as $e){
                                echo "<label id=\"MESSAGE_ARRAY\"class=\"" . $warning ."\">" . $e . "</label><br/>";	
                            }
                            ?>
                        </div>
                    </td>
                </tr>
			<tr>
				<td>
				<h2>User Guide</h2>
<p>Adjust MASTER for all products or adjust each one independently using the search <br>and select functionality.</p>
<p>Enter a two character Price Category identifier using letters and numbers; eg A1, A2 etc.</p>
<p>Enter a percentage price Adjustment for the corresponding Price Category; eg 150 will <br>create a 50% margin on the product trade/member price</p>
				</td>
			</tr>
            </table>
        <?php endif; ?>
    </form>
<?php
  include_once("includes/footer_admin.php");
?>
<script src="/_cms/scripts/maintain_price_categories.js" type="text/javascript"></script>

<script type="text/javascript">
function check_header(element){
	//sets the quantity discount table header to match the selected discount type
	var cell = document.getElementById("td-type");
	switch (element.value){
		case "PP":
			cell.innerHTML = "<b>Price(£)</b>";
			break;
		case "PC":
			cell.innerHTML = "<b>Adjustment(%)</b>";
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