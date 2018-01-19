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

if (isset($_GET['delproduct'])) {
	//delete product from tree
	$rows = deleteProductFromTree($_GET['delproduct'], $_GET['tree']);	
	if ($rows == 1){
		$message .= "Product DELETED from the menu structure" . "<br/>";
		$warning = "green";
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
}

if (isset($_GET['searchproduct'])) {
	//NEW PRODUCT SELECTED from search dropdown so get product deatils for display 
	$product = getProductDetails($_GET['searchproduct']);
	$selected_product = $product->PR_PRODUCT;
	$productcode = $product->PR_PRODUCT;
	$name = html_entity_decode($product->PR_NAME);
	
	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_PRODUCT'] = $_GET['searchproduct'];
}

if (isset($_POST['RE-POSITION'])) {
	$tree = $_POST['TREE_CODE'];
	$rowswritten = 0;
	for ($i=1; $i<=$_POST['PRODUCTS_COUNTER']; $i++){
		//loop through products and rewrite each with the latest position
		//echo $_POST['PRODUCT'.$i] . " / " . $_POST['POSITION'.$i] . "<br/>";
		$fields = array("pc_product"=>$_POST['PRODUCT'.$i], "pc_category"=>$_POST['CATEGORY_CURRENT'], "pc_tree_node"=>$_POST['TREE_CODE'], "pc_position"=>$_POST['POSITION'.$i]);
		$rows = Rewrite_PRODCAT($fields);
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
			$warning = "red";
		}
		$rowswritten = $rowswritten + $rows;
	}
	if (($rowswritten >= 1 and $rowswritten <= $_POST['PRODUCTS_COUNTER']) and $message == "" ){
		$message = "{$rowswritten} records successfully UPDATED";
		$warning = "green";
	}
	if (($rowswritten == 0) and $message == "" ){
		$message .= "WARNING ! ! ! - NO RECORDS UPDATED";
		$warning = "orange";
	}
	if ($rowswritten > $_POST['PRODUCTS_COUNTER']){
		$message .= "ERROR ! ! ! - MORE ROWS WRITTEN THAN PRODUCTS WITHIN CATEGORY ! ! ! - {$rowswritten} RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
		$warning = "red";
	}
	unset($_POST['SEARCH_DATA']);
}

if (isset($_POST['ADD_PRODUCT'])) {
	//check product is valid first
	$checkok = validateSeed($_POST['PRODUCT_TO_ADD']);
	if($checkok == "true"){
		if(checkProductExists($_POST['PRODUCT_TO_ADD']) == "true"){
			//add to tree
			$fields = array("pc_product"=>$_POST['PRODUCT_TO_ADD'], "pc_category"=>$_POST['CATEGORY_CURRENT'], "pc_tree_node"=>$_POST['TREE_CODE'], "pc_position"=>$_POST['POSITION_TO_ADD']);		
			$rows = addProductToTree($fields);
			if ($rows == 1){
				$message = $rows . "Product ADDED to selected category";
				$warning = "green";
			}
			if ($rows == 0){
				$message = "WARNING ! ! ! - NO RECORDS UPDATED!!!";
				$warning = "orange";
			}
			if ($rows > 1){
				$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") PRODUCT RECORD UPDATED - PLEASE CONTACT SHOPFITTER!!!";
				$warning = "red";
			}
			$error = null;
			$error = mysql_error();
			if ($error != null) { 
				$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
				$warning = "red";
			}
		}else{
			$message = "PRODUCT CODE DOES NOT EXIST - please enter a valid product code!";
			$warning = "red";
		}
	}else{
		$message = "INVALID FORMAT PRODUCT CODE - please enter a valid product code!";
		$warning = "red";
	}
}

$category_current = "";
if(isset($_GET['category'])){
	$category = getCategory($_GET['category']);
	$category_current = $category->CA_CODE;
}

if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Add Products To Memu";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;


include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Add Products To Category - products in categories</h1>
    <p><br /><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is where you add products to categories<br /><br />Once added products are visible in a menu form allowing site visitors to select more details or buy from this page<br /><br />Help and guidance can be found by clicking on either blue titles on the page or help links like this</span><span class=\"bottom\"></span></span>" : "") ?></a></p>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="enter-thumb" action="/_cms/add_products.php" enctype="multipart/form-data" method="post">
		<?php
		//--- CATEGORY SELECTION ----------------------------------------------------------------------------------------------------------->
        $tree = "0";
        //$message = "";
        if(isset($_GET['tree'])){$tree = $_GET['tree'];}
		if(isset($_POST['TREE_CODE'])){$tree = $_POST['TREE_CODE'];}
		if (isset($_POST['PREVIOUS_CATEGORY']) and $tree != "0"){
			//set tree to one level back up the current tree
			$fstart = 0;
			$fend = 999;
			while ($fend > 0){
				$fend = strpos($tree, "_", $fstart);
				if($fend > 0){
					$previous = substr($tree, 0, $fend);
					$fstart = $fend + 1;
				}
			}
			$tree = $previous;
		}
		$fstart = 0;
        $fend = 999;
        while($fend > 0){
            //loop through tree and display a selection box against each level found
            $fend = strpos($tree, "_", $fstart);	
            if($fend>0){	
                $treenode = substr($tree, 0, $fend);
                $fstart = $fend + 1;
            }else{
                $treenode = $tree;
            }
            $categories = getCategories($treenode);
            if(count($categories)){
                echo "<tr>";
                    //echo "<td>Category:" . $treenode . "</td>";
					echo "<td>Category: ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the category you want to add products to</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
					echo "</td>";
                    echo "<td colspan=\"2\">";
                        echo "<select name=\"category\" id=\"jumpMenu\" onchange=\"MM_jumpMenu('parent',this,1)\">";
                            echo "<option value=\"#\">Choose from...</option>";
                            $categories = getCategories($treenode);
                            foreach($categories as $c){
								if($c->CA_CODE != "HEADER" and $c->CA_CODE != "SPACER"){
									//get next treenode and compare with category tree node to test option selection
									$nextfend = strpos($tree, "_", $fstart);
									if($nextfend>0){
										$nextnode = substr($tree, 0, $nextfend);
									}else{
										$nextnode = $tree;
									}
									if($nextnode == $c->CA_TREE_NODE . "_" . $c->CA_CODE){
										$selected = "selected";
										$lastselected = $c->CA_NAME;
										$lastselectedcategory = $c->CA_CODE;
									}else{
										$selected = "";
									}
									echo "<option value=\"/_cms/add_products.php?tree=" . $c->CA_TREE_NODE . "_" . $c->CA_CODE . "\" " . $selected . ">" . $c->CA_NAME . "</option>";
								}
							}
                        echo "</select>";
                        //echo "<input name=\"CATEGORY_CURRENT\" type=\"hidden\" value=\"" . (isset($_POST['CATEGORY_CURRENT']) ? $_POST['CATEGORY_CURRENT'] : $category_current) . "\">";
                    echo "</td>";
                echo "</tr>";
            }
            
        }
        echo "<tr>";
        	echo "<td></td>";
            echo "<td colspan=\"2\">";
            	echo "<input type=\"submit\" name=\"PREVIOUS_CATEGORY\" value=\"Previous Category\" class=\"previous-button\">";
            echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to return to the previous selection<br /><br />Click until you've stepped back as far as you wish</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
			echo "</td>";
        echo "</tr>"; 
        echo "<tr>";
			echo "<td colspan=\"3\" class=\"td-sep\">&nbsp;</td>";
		echo "</tr>";   
        //--- PRODUCTS LISTING ----------------------------------------------------------------------------------------------------------->
		echo "<tr>";
			echo "<td>Position ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the menu items appear from left to right or top to bottom<br /><br />Lower numbers display nearer the left or top</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "<input name=\"TREE_CODE\" type=\"hidden\" value=\"" . $tree . "\" >";
				echo "<input name=\"CATEGORY_CURRENT\" type=\"hidden\" value=\"" . $lastselectedcategory . "\" >";
			echo "</td>";
			echo "<td>Products listed within <b>" . $lastselected . "</b> category</td>";
			echo "<td>";
			echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Remove the product from the menu by clicking the appropriate Delete button<br /><br />This does not delete the product itself</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
			echo "Remove from list</td>";
		echo "</tr>";

        if($tree != "0"){
            //at this point we do not add products to the home page!
            $products = getProducts($tree);
			//$products_array = array();
            $cntr1 = 0;
            foreach($products as $p){
                $product = getProductDetails($p->PC_PRODUCT);
                $cntr1 ++;
                echo "<tr>";
                    echo "<td class=\"position-td\">";
						echo "<input type=\"text\" name=\"POSITION" . $cntr1 . "\" SIZE=\"6\" value=\"" . $p->PC_POSITION . "\" >";
					echo "</td>";
                    echo "<td class=\"product-td\" valign=\"middle\">";
                        echo "<input type=\"text\" name=\"PRODUCT_DISABLED" . $cntr1 . "\" SIZE=\"70\" value=\"" . $product->PR_PRODUCT . " - " . $product->PR_NAME . "\" disabled ></td>";
						echo "<td>";
						echo "<a href=\"/_cms/add_products.php?tree=" . $tree . "&delproduct=" . $product->PR_PRODUCT . "\" class=\"delete-button\">";
							echo "Delete";
                			echo "</a>";
						echo "<input type=\"hidden\" name=\"PRODUCT" . $cntr1 . "\" SIZE=\"70\" value=\"" . $product->PR_PRODUCT . "\" >";
                    echo "</td>";
                echo "</tr>";
            }
			echo "<tr>";
                echo "<td colspan=\"3\">";
					echo "<input name=\"PRODUCTS_COUNTER\" type=\"hidden\" value=\"" . $cntr1 . "\" />";
					echo "<input name=\"RE-POSITION\" type=\"submit\" value=\"Update\" class=\"update-button\" />";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Save the changes you've made to the order position</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td colspan=\"3\" class=\"td-sep\"></td>";
            echo "</tr>";
			//--- SEARCHBOX ------------------------------------------------------------------------------------------------------>
			echo "<tr>";
				echo "<td></td>";
				echo "<td colspan=\"2\">";
					echo "Add Product to Category";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the product you want to add to the category<br /><br />To select from all products place the mouse cursor in the search field with no other text and click search<br /><br />Then select the desired products from the Choose from... dropdown box<br /><br />Finally, when the product code appears in the Add Product field, set the position number you desire and click the Add button<br /><br />The listing above should have appeared in the list above</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
			echo "<tr>";
			echo "<tr>";
				echo "<td>Search for: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the product you want to add to the category<br /><br />To select from all products place the mouse cursor in the search field with no other text and click search</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td colspan=\"2\">";
					echo "<Input name=\"SEARCH_DATA\" type=\"text\" size=\"57\" value=\"" . (isset($_POST['SEARCH_DATA']) ? $_POST['SEARCH_DATA'] : "") . "\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp";
					echo "<Input name=\"SEARCH\" type=\"submit\" value=\"search\" class=\"search-button\" />";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td colspan=\"2\">";
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
								echo "<option value=\"/_cms/add_products.php?tree=" . $tree . "&searchdata=" . $_POST['SEARCH_DATA'] . "&searchproduct=" . $p->PR_PRODUCT . "\"" . $selected . ">" . $p->PR_PRODUCT . " - " . $p->PR_NAME . "</option>";
							}
						}
					echo "</select>";
					echo "<input type=\"hidden\" name=\"SELECTED_PRODUCT\" value=\"" . (isset($selected_product) ? $selected_product : "") . "\">";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the desired product from this dropdown menu</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan=\"3\" class=\"td-sep\">&nbsp;</td>";
			echo "</tr>";
			//--- END OF SEARCHBOX ----------------------------------------------------------------------------------------------->
				
            echo "<tr>";
                echo "<td>Add product: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The product you've chosen to add to the category</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
                echo "<td colspan=\"2\">";
                    echo "<input type=\"text\" name=\"PRODUCT_TO_ADD\" SIZE=\"10\" value=\"" . (isset($selected_product) ? $selected_product : "") . "\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                   	echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the products appear from top to bottom or left to right<br /><br />Lower numbers display nearer the left or top<br /><br />The default is 999 which will add the product to the end of the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a> " : "";
				    echo "<input type=\"text\" name=\"POSITION_TO_ADD\" SIZE=\"5\" value=\"999\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<input name=\"ADD_PRODUCT\" type=\"submit\" value=\"Add\" class=\"add-button\" />";
                echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save the new product in the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
            echo "</tr>";             
            echo "<tr>";
        }
       //--- MESSAGES ---------------------------------------------------------------------------------------------------->
	   ?>
        <tr>
			<td colspan="3"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>        

	</form>
    	<tr>
        	<td colspan="3">&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="3">&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="3">&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="3">&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="3">&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="3">&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="3">&nbsp;</td>
        </tr>
        <tr>
        	<td colspan="3">&nbsp;</td>
        </tr>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>

