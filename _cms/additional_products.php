<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$warning = "";
$pathName = "/images/";
$lastselected = "";
$lastselectedcategory = "";
$scrolltobottom = "";
$searchproduct_main = "";
if(isset($_POST['SELECTED_PRODUCT_MAIN'])){$selected_product_main = $_POST['SELECTED_PRODUCT_MAIN'];}else{$selected_product_main = "";}

//echo "selected_product_main = " . $selected_product_main;
if (isset($_GET['delproduct'])) {
	$_POST['SEARCH_DATA_MAIN'] = $_GET['searchdata_main'];
	$_POST['SELECTED_PRODUCT_MAIN'] = $_GET['searchproduct_main'];
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_PRODUCT'] = $_GET['searchproduct'];
	//delete additional product record
	$rows = $rows = Delete_ADDL_PRODUCTS($_POST['SELECTED_PRODUCT_MAIN'], $_GET['delproduct']);	
	if ($rows == 1){
		$message .= "RELATED Product DELETED from the menu structure" . "<br/>";
		$warning = "green";
	}else{
		$message .= "RELATED PRODUCT record NOT DELETED - PLEASE CONTACT SHOPFITTER!!!"; 
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
		$warning = "red";
	}
}

if (isset($_GET['searchproduct_main'])) {
	//NEW PRODUCT SELECTED from main search dropdown so get product details for display 
	$product_main = getProductDetails($_GET['searchproduct_main']);
	$selected_product_main = $product_main->PR_PRODUCT;
	//echo "selected_product_main = " . $selected_product_main;
	$productcode_main = $product_main->PR_PRODUCT;
	$name_main = html_entity_decode($product_main->PR_NAME);
	
	$_POST['SEARCH_MAIN'] = "search_main";
	$_POST['SEARCH_DATA_MAIN'] = $_GET['searchdata_main'];
	$_POST['SELECTED_PRODUCT_MAIN'] = $_GET['searchproduct_main'];
	
}

if (isset($_GET['searchproduct']) and !isset($_GET['delproduct'])) {
	//refresh details of main product
	$product_main = getProductDetails($_POST['SELECTED_PRODUCT_MAIN']);
	$selected_product_main = $product_main->PR_PRODUCT;
	$productcode_main = $product_main->PR_PRODUCT;
	$name_main = html_entity_decode($product_main->PR_NAME);

	//NEW PRODUCT SELECTED from search dropdown so get product details for display 
	$product = getProductDetails($_GET['searchproduct']);
	$additional = getAdditionalProducts($_GET['searchproduct']);
	$selected_product = $product->PR_PRODUCT;
	$productcode = $product->PR_PRODUCT;
	$name = html_entity_decode($product->PR_NAME);
	
	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_PRODUCT'] = $_GET['searchproduct'];
	
}

if (isset($_POST['RE-POSITION'])) {
	$rowswritten = 0;
	for ($i=1; $i<=$_POST['PRODUCTS_COUNTER']; $i++){
		//loop through products and rewrite each with the latest position
		//echo $_POST['PRODUCT'.$i] . " / " . $_POST['POSITION'.$i] . "<br/>";
		$fields = array("ap_product"=>$_POST['SELECTED_PRODUCT_MAIN'], "ap_additional"=>$_POST['PRODUCT' .$i], "ap_position"=>$_POST['POSITION'.$i]);
		$rows = Rewrite_ADDL_PRODUCTS($fields);
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
			$warning = "red";
		}
		$rowswritten = $rowswritten + $rows;
	}
	if (($rowswritten >= 1 and $rowswritten <= $_POST['PRODUCTS_COUNTER']) and $message == "" ){
		$message = "{$rowswritten} record(s) successfully UPDATED";
		$warning = "green";
	}
	if (($rowswritten == 0) and $message == "" ){
		$message .= "WARNING ! ! ! - NO RECORDS UPDATED";
		$warning = "orange";
	}
	if ($rowswritten > $_POST['PRODUCTS_COUNTER']){
		$message .= "ERROR ! ! ! - MORE ROWS WRITTEN THAN RELATED PRODUCTS ! ! ! - {$rowswritten} RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
		$warning = "red";
	}
}

if (isset($_POST['ADD_PRODUCT'])) {
	//check product is valid first
	$checkok = validateSeed($_POST['PRODUCT_TO_ADD']);
	if($checkok == "true"){
		if(checkProductExists($_POST['PRODUCT_TO_ADD']) == "true"){
			//add to tree
			$fields = array("ap_product"=>$_POST['SELECTED_PRODUCT_MAIN'], "ap_additional"=>$_POST['PRODUCT_TO_ADD'], "ap_position"=>$_POST['POSITION_TO_ADD']);		
			$rows = Write_ADDL_PRODUCTS($fields);
			if ($rows == 1){
				$message = $rows . " Related Product successfully ADDED";
				$warning = "green";
			}
			if ($rows == 0){
				$message = "WARNING ! ! ! - NO RECORDS UPDATED!!!";
				$warning = "orange";
			}
			if ($rows > 1){
				$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") RELATED PRODUCT RECORD UPDATED - PLEASE CONTACT SHOPFITTER!!!";
				$warning = "red";
			}
			$error = null;
			$error = mysql_error();
			if ($error != null) { 
				$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
				$warning = "red";
			}
		}else{
			$message = "related PRODUCT CODE DOES NOT EXIST - please enter a valid product code!";
			$warning = "red";
		}
	}else{
		$message = "INVALID FORMAT related PRODUCT CODE - please enter a valid product code!";
		$warning = "red";
	}
}

if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Related Products";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;


include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Related Products - add associated products</h1>
    <p><br /><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Add products that are related to each other here; start by selecting the product you want to add related products to from the dropdown menu below<br /><br />
	Products need to have been created before you can add them as a related item<br />	<br />Put your mouse on any of the blue text for an explanation of what an item is and how to use it</span><span class=\"bottom\"></span></span>" : "") ?></a></p>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    	<tr>
        	<td colspan="2"><a href="amend_products.php?searchdata=&searchproduct=<?php echo $selected_product_main ?>">Click here to go to <?php echo $name_main ?> product details </a></td>
        </tr>
        <tr>
        	<td colspan="2" class="td-sep">&nbsp;</td>
        </tr>
    <form name="enter-thumb" action="/_cms/additional_products.php" enctype="multipart/form-data" method="post">
		<?php
		//--- MAIN PRODUCT SELECTION ----------------------------------------------------------------------------------------------------------->
		/*if(isset($_POST['SELECTED_PRODUCT_MAIN'])){
		echo "SELECTED_PRODUCT_MAIN = " . $_POST['SELECTED_PRODUCT_MAIN'] . "</br>";
		echo "selected_product_main = " . $selected_product_main;
		}else{
			echo "SELECTED_PRODUCT_MAIN NOT DEFINED";
		}*/
		echo "<tr>";
			echo "<td>Search for:  ";
			echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the product you want to add related product to<br /><br />To select from all products simply place your mouse cursor in the field and click Search</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
			echo "</td>";
			echo "<td>";
				echo "<Input name=\"SEARCH_DATA_MAIN\" type=\"text\" size=\"57\" value=\"" . (isset($_POST['SEARCH_DATA_MAIN']) ? $_POST['SEARCH_DATA_MAIN'] : "") . "\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp";
				echo "<Input name=\"SEARCH_MAIN\" type=\"submit\" value=\"search\" class=\"search-button\" />";
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td></td>";
			echo "<td>";
				echo "<select name=\"search_results\" id=\"jumpMenu\" onchange=\"MM_jumpMenu('parent',this,1)\" class=\"search-product-box\" >";
					echo "<option value=\"#\">Choose from...</option>";
					if (isset($_POST['SEARCH_DATA_MAIN'])){
						$products = Search_product($_POST['SEARCH_DATA_MAIN']);
						foreach($products as $p){
							if(isset($_POST['SELECTED_PRODUCT_MAIN']) and $_POST['SELECTED_PRODUCT_MAIN'] == $p->PR_PRODUCT){
								$selected = "selected";
							}else{
								$selected = "";
							}
							echo "<option value=\"/_cms/additional_products.php?searchdata_main=" . $_POST['SEARCH_DATA_MAIN'] . "&searchproduct_main=" . $p->PR_PRODUCT . "\"" . $selected . ">" . $p->PR_PRODUCT . " - " . $p->PR_NAME . "</option>";
						}
					}
				echo "</select>";
				echo "<input type=\"hidden\" name=\"SELECTED_PRODUCT_MAIN\" value=\"" . (isset($selected_product_main) ? $selected_product_main : "") . "\">";
			echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the page you want to edit</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td colspan=\"2\" class=\"td-sep\">&nbsp;</td>";
		echo "</tr>"; 
        //--- ADDITIONAL PRODUCTS LISTING ----------------------------------------------------------------------------------------------------------->
		if(isset($_POST['SELECTED_PRODUCT_MAIN']) and $_POST['SELECTED_PRODUCT_MAIN'] != ""){
		echo "<tr>";
			echo "<td>Position ";
			echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the menu items appear from left to right or top to bottom<br /><br />Lower numbers display nearer the left or top<br /><br />Remember to click Update at the bottom of the list when you've made your changes</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
			echo "</td>";
			echo "<td>Products related to <b>" . $selected_product_main . "</b> product</td>";
		echo "</tr>";
            $additional = getAdditionalProducts($_POST['SELECTED_PRODUCT_MAIN']);
            $cntr1 = 0;
            foreach($additional as $a){
                $product = getProductDetails($a->AP_ADDITIONAL);
                $cntr1 ++;
                echo "<tr>";
                    echo "<td>";
						echo "<input type=\"text\" name=\"POSITION" . $cntr1 . "\" SIZE=\"6\" value=\"" . $a->AP_POSITION . "\" >";
					echo "</td>";
                    echo "<td valign=\"middle\">";
                        echo "<input type=\"text\" name=\"PRODUCT_DISABLED" . $cntr1 . "\" SIZE=\"70\" value=\"" . $product->PR_PRODUCT . " - " . $product->PR_NAME . "\" disabled >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<a href=\"/_cms/additional_products.php?searchdata_main=" . $_POST['SEARCH_DATA_MAIN'] . "&searchproduct_main=" . $_POST['SELECTED_PRODUCT_MAIN'] . "&searchdata=" . (isset($_POST['SEARCH_DATA']) ? $_POST['SEARCH_DATA'] : "") . "&searchproduct=" . (isset($selected_product) ? $selected_product : "") .  "&delproduct=" . $product->PR_PRODUCT . "\" class=\"delete-button\">";
							echo "Delete";
                			echo "</a>";
						echo "<input type=\"hidden\" name=\"PRODUCT" . $cntr1 . "\" SIZE=\"70\" value=\"" . $product->PR_PRODUCT . "\" >";
                    echo "</td>";
                echo "</tr>";
            }
			echo "<tr>";
                echo "<td>";
					echo "<input name=\"PRODUCTS_COUNTER\" type=\"hidden\" value=\"" . $cntr1 . "\" />";
					echo "<input name=\"RE-POSITION\" type=\"submit\" value=\"Update\"  class=\"update-button\" />";
				echo "</td>";
            echo "</tr>";
            echo "<tr>";
			echo "<td colspan=\"2\" class=\"td-sep\">&nbsp;</td>";
            echo "</tr>";
			//--- ADD ADDITIONAL PRODUCT SEARCHBOX ------------------------------------------------------------------------------------------------------>
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "Add Related Items to this Product";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Search for: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Search for the item you want to add as a related product to<br /><br />To select from all products simply place your mouse cursor in the field and click Search<br /><br />Next select the product you want from the Choose from... box below</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td>";
					echo "<Input name=\"SEARCH_DATA\" type=\"text\" size=\"57\" value=\"" . (isset($_POST['SEARCH_DATA']) ? $_POST['SEARCH_DATA'] : "") . "\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp";
					echo "<Input name=\"SEARCH\" type=\"submit\" value=\"search\" class=\"search-button\" />";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<select name=\"search_results\" id=\"jumpMenu\" onchange=\"MM_jumpMenu('parent',this,1)\">";
						echo "<option value=\"#\">Choose from...</option>";
						if (isset($_POST['SEARCH_DATA'])){
							$products = Search_product($_POST['SEARCH_DATA']);
							foreach($products as $p){
								if(isset($_POST['SELECTED_PRODUCT']) and $_POST['SELECTED_PRODUCT'] == $p->PR_PRODUCT){
									$selected = "selected";
								}else{
									$selected = "";
								}
								echo "<option value=\"/_cms/additional_products.php?searchdata_main=" . $_POST['SEARCH_DATA_MAIN'] . "&searchproduct_main=" . $_POST['SELECTED_PRODUCT_MAIN'] . "&searchdata=" . $_POST['SEARCH_DATA'] . "&searchproduct=" . $p->PR_PRODUCT . "\"" . $selected . ">" . $p->PR_PRODUCT . " - " . $p->PR_NAME . "</option>";
							}
						}
					echo "</select>";
					echo "<input type=\"hidden\" name=\"SELECTED_PRODUCT\" value=\"" . (isset($selected_product) ? $selected_product : "") . "\">";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the product you want to add as related; you need to have searched for it first</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan=\"2\" class=\"td-sep\">&nbsp;</td>";
			echo "</tr>";
			//--- END OF SEARCHBOX ----------------------------------------------------------------------------------------------->
				
            echo "<tr>";
                echo "<td>Add Product: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The product you've chosen to add as related</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
                echo "<td>";
                    echo "<input type=\"text\" name=\"PRODUCT_TO_ADD\" SIZE=\"10\" value=\"" . (isset($selected_product) ? $selected_product : "") . "\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                   	echo "To Position: ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the related products appear from top to bottom or left to right<br /><br />Lower numbers display nearer the left or top<br /><br />The default is 999 which will add this related product to the end of the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a> " : "";
				    echo "<input type=\"text\" name=\"POSITION_TO_ADD\" SIZE=\"5\" value=\"999\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<input name=\"ADD_PRODUCT\" type=\"submit\" value=\"Add\"  class=\"add-button\" />";
                echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save the related product in the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
            echo "</tr>";             
            echo "<tr>";
		}
       //--- MESSAGES ---------------------------------------------------------------------------------------------------->
	   ?>
        <tr>
			<td colspan="2"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>        

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
        <tr>
        	<td colspan="2">&nbsp;</td>
        </tr>
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
<?php
  include_once("includes/footer_admin.php");
?>

