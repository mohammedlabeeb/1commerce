<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$warning = "";
$pathName = "/images/";
$selected_product = "";
$selection_id_current = -1;
$selection_name = "";
$updateline = ""; $deleteline = "";
$number_to_add = 0;
$name_to_add = "";
$value_to_add = "0.00";
$exclude_to_add = "N";
$sku_to_add = "";
$scrolltobottom = "";
$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer2 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer2 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer3 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";


if (isset($_GET['searchproduct'])) {
	//NEW PRODUCT SELECTED from search dropdown so get product deatils for display 
	$product = getProductDetails($_GET['searchproduct']);
	$selected_product = $product->PR_PRODUCT;
	$productcode = $product->PR_PRODUCT;
	
	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_PRODUCT'] = $_GET['searchproduct'];
}

//get current category and product objects
if(isset($_GET['product'])){$selected_product = $_GET['product'];}
if(isset($_POST['SELECTED_PRODUCT'])){$selected_product = $_POST['SELECTED_PRODUCT'];}
if($selected_product != ""){
	$product = getProductDetails($selected_product);
}

if(isset($_GET['selection_id'])){$selection_id_current = $_GET['selection_id'];}
if(isset($_POST['SELECTION_ID_CURRENT'])){$selection_id_current = $_POST['SELECTION_ID_CURRENT'];}
if($selection_id_current != -1){
	$selection = getSelection($selection_id_current);
	$selection_id_current = $selection->SE_ID;
	$attribute1_values = ""; $attribute2_values = ""; $attribute3_values = "";
	$attribute1 = getAttribute($selection->SE_ATTRIBUTE1);
	if($selection->SE_ATTRIBUTE1 != 0){$attribute1 = getAttribute($selection->SE_ATTRIBUTE1); $attribute1_values = getAttributeValues($attribute1);}
	$attribute2 = getAttribute($selection->SE_ATTRIBUTE2);
	if($selection->SE_ATTRIBUTE2 != 0){$attribute2 = getAttribute($selection->SE_ATTRIBUTE2); $attribute2_values = getAttributeValues($attribute2);}
	$attribute3 = getAttribute($selection->SE_ATTRIBUTE3);
	if($selection->SE_ATTRIBUTE3 != 0){$attribute3 = getAttribute($selection->SE_ATTRIBUTE3); $attribute3_values = getAttributeValues($attribute3);}
}

if (isset($_POST['CREATE_SELECTION'])) {
	//create Selection
	$fields = array("se_name"=>$_POST['SELECTION_NAME'], "se_label"=>"", "se_exclude"=>"N", "se_product"=>$selected_product, "se_attribute1"=>$_POST['SELECTION_ATTRIBUTE1'],
					"se_attribute2"=>$_POST['SELECTION_ATTRIBUTE2'], "se_attribute3"=>$_POST['SELECTION_ATTRIBUTE3']);
	$rows = Create_Selection($fields);	
	if ($rows == 1){
		$message .= "Option CREATED" . "<br/>";
		$warning = "green";
		// now get autogenerated id of selection box just created to write to options row
		$attribute1_values = ""; $attribute2_values = ""; $attribute3_values = "";
		$selection = getSelectionId($_POST['SELECTION_NAME'], $selected_product);
		$selection_id_current = $selection->SE_ID;
		$attribute1 = getAttribute($selection->SE_ATTRIBUTE1);
		if($attribute1){$attribute1_values = getAttributeValues($attribute1);}
		$attribute2 = getAttribute($selection->SE_ATTRIBUTE2);
		if($attribute2){$attribute2_values = getAttributeValues($attribute2);}
		$attribute3 = getAttribute($selection->SE_ATTRIBUTE3);
		if($attribute3){$attribute3_values = getAttributeValues($attribute3);}
	}else{
		$message .= "Option NOT CREATED!!! - PLEASE CONTACT SHOPFITTER!"; 
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
		$warning = "red";
	}
}

if (isset($_POST['UPDATE_NAME'])) {
	if ($_POST['SELECTION_EXCLUDE'] != "N" and $_POST['SELECTION_EXCLUDE'] != "Y"){
		$message .= "Please enter N or Y against the Exclude field" . "<br/>";
		$warning = "red";
	}
	if(strpos($_POST['SELECTION_NAME'], "\"")){
		$message .= "Please do not use double quotes within Name field - use single quotes instead" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		$fields = array("se_id"=>$selection_id_current, "se_name"=>$_POST['SELECTION_NAME'], "se_label"=>"", "se_exclude"=>$_POST['SELECTION_EXCLUDE'], 
						"se_product"=>$selected_product, "se_attribute1"=>$_POST['SELECTION_ATTRIBUTE1'], "se_attribute2"=>$_POST['SELECTION_ATTRIBUTE2'],
						"se_attribute3"=>$_POST['SELECTION_ATTRIBUTE3'] );
		$rows = Rewrite_Selection($fields);	
		if ($rows == 1){
			$message .= "Option Name amended successfully" . "<br/>";
			$warning = "green";	
			// now get autogenerated id of selection box just created to write to options row
			$attribute1_values = ""; $attribute2_values = ""; $attribute3_values = "";
			$selection = getSelectionId($_POST['SELECTION_NAME'], $selected_product);
			$selection_id_current = $selection->SE_ID;
			$attribute1 = getAttribute($selection->SE_ATTRIBUTE1);
			if($attribute1){
				$attribute1_values = getAttributeValues($attribute1);
			}else{
				//if a selection box attribute is set to zero then ensure that no options are tagged against it
				//eg. an existing selection box tag may have just been removed in which case that tag needs removing from any options previously assigned it.
				//Clear_Option_Tag($selection_id_current, 1);
			}
			$attribute2 = getAttribute($selection->SE_ATTRIBUTE2);
			if($attribute2){$attribute2_values = getAttributeValues($attribute2);}
			$attribute3 = getAttribute($selection->SE_ATTRIBUTE3);
			if($attribute3){$attribute3_values = getAttributeValues($attribute3);}
			//if a selection box attribute has changed then ensure that no options are tagged with any of the attributes of the original
			//eg. an existing selection box tag may have just been removed in which case that tag needs removing from any options previously assigned it.
			if($selection->SE_ATTRIBUTE1 != $_POST['SELECTION_ATTRIBUTE1_ORIG']){
				Clear_Option_Tag($selection_id_current, 1);
			}
			if($selection->SE_ATTRIBUTE2 != $_POST['SELECTION_ATTRIBUTE2_ORIG']){
				Clear_Option_Tag($selection_id_current, 2);
			}
			if($selection->SE_ATTRIBUTE3 != $_POST['SELECTION_ATTRIBUTE3_ORIG']){
				Clear_Option_Tag($selection_id_current, 3);
			}
		}else{
			$message .= "Option name NOT amended!!! - PLEASE CONTACT SHOPFITTER!"; 
			$warning = "red";
		}
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
			$warning = "red";
		}
	}
}

if(isset($_GET['search'])){$_POST['SEARCH_DATA'] = $_GET['search']; $_POST['SELECTED_PRODUCT'] = $_GET['product'];}

//determine if an option line Update or Delete button has been hit
if(isset($_POST['OPTIONS_COUNTER'])){
	$updateline = 0; $deleteline = 0;
	for ($i=1; $i<=$_POST['OPTIONS_COUNTER']; $i++){
		if(isset($_POST['UPDATE_OPTION'.$i])){$updateline = $i; break;}
		if(isset($_POST['DELETE_OPTION'.$i])){$deleteline = $i; break;}
	}
}

if ($deleteline > 0) {
	//delete option button has been hit
	$rows = Delete_Option(html_entity_decode($selection_id_current, ENT_QUOTES), $_POST['NAME_ORIGINAL'.$deleteline], "");	
	if ($rows == 1){
		$message .= "Line DELETED from Option" . "<br/>";
		$warning = "green";
	}else{
		$message .= "Line NOT DELETED - PLEASE CONTACT SHOPFITTER!!!"; 
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
		$warning = "red";
	}
}

if ($updateline > 0) {
	//update option button has been hot so... validate all table entries
	$message = "";
	if(is_numeric($_POST['NUMBER'.$updateline]) and $_POST['NUMBER'.$updateline] > 0){$ok = 1;}else{$ok = 0;}
	if ($ok == 0){
		$message .= "Please enter a valid Line Number" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['NAME'.$updateline]) == 0){
		$message .= "Please enter a valid Line Name" . "<br/>";
		$warning = "red";
	}
	if(strpos($_POST['NAME'.$updateline], "\"")){
		$message .= "Please do not use double quotes within Option Text field - use single quotes instead" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['VALUE'.$updateline]) > 0 and validate2dp($_POST['VALUE'.$updateline]) == "false"){
		$message .= "Please enter a valid Line Value to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if ($_POST['EXCLUDE'.$updateline] != "Y" and $_POST['EXCLUDE'.$updateline] != "N"){
		$message .= "Please enter a valid line Exclude value (Y/N)" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		//loop through options and rewrite each with the latest details
		$fields = array("op_se_id"=>$selection_id_current, "name_original"=>$_POST['NAME_ORIGINAL'.$updateline], "op_name"=>$_POST['NAME'.$updateline], "op_number"=>$_POST['NUMBER'.$updateline], "op_text"=>"",
							"op_value"=>$_POST['VALUE'.$updateline], "op_selected"=>"N", "op_exclude"=>$_POST['EXCLUDE'.$updateline], "op_product"=>$selected_product, "op_sku"=>$_POST['SKU'.$updateline],
							"op_attribute_value1"=>$_POST['ATTRIBUTE_VALUE1'.$updateline], "op_attribute_value2"=>$_POST['ATTRIBUTE_VALUE2'.$updateline],
							"op_attribute_value3"=>$_POST['ATTRIBUTE_VALUE3'.$updateline]);
			
		$rows = Rewrite_Option($fields);
		if ($rows == 1){
			$message .= "{$rows} Line successfully UPDATED" . "<br/>";
			$warning = "green";
		}
		if (($rows == 0) and $message == "" ){
			$message .= "WARNING ! ! ! - NO OPTIONS UPDATED";
			$warning = "orange";
		}
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
			$warning = "red";
		}
	}
}

if (isset($_POST['ADD_OPTION'])) {
	//validate fields
	$message = "";
	if (strlen($_POST['NAME_TO_ADD']) == 0){
		$message .= "Please enter a valid Line Name" . "<br/>";
		$warning = "red";
	}
	if(strpos($_POST['NAME_TO_ADD'], "\"")){
		$message .= "Please do not use double quotes within Option Text field - use single quotes instead" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['VALUE_TO_ADD']) > 0 and validate2dp($_POST['VALUE_TO_ADD']) == "false"){
		$message .= "Please enter a valid Line Value to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if ($_POST['EXCLUDE_TO_ADD'] != "Y" and $_POST['EXCLUDE_TO_ADD'] != "N"){
		$message .= "Please enter a valid line Exclude value (Y/N)" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		$fields = array("op_se_id"=>$selection->SE_ID, "op_name"=>$_POST['NAME_TO_ADD'], "op_number"=>$_POST['NUMBER_TO_ADD'], "op_text"=>"",
						"op_value"=>$_POST['VALUE_TO_ADD'], "op_selected"=>"N", "op_exclude"=>$_POST['EXCLUDE_TO_ADD'], "op_product"=>$selected_product, "op_sku"=>$_POST['SKU_TO_ADD'],
						"op_attribute_value1"=>$_POST['ATTRIBUTE_VALUE_TO_ADD1'], "op_attribute_value2"=>$_POST['ATTRIBUTE_VALUE_TO_ADD2'],
						"op_attribute_value3"=>$_POST['ATTRIBUTE_VALUE_TO_ADD3']);		
		$rows = Create_Option($fields);
		if ($rows == 1){
			$message = $rows . " Line ADDED to Option";
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
	}
}

if (isset($_POST['DELETE_SELECTION'])) {
	//delete selection box + all associated options
	$rows = Delete_All_Options($selection_id_current);
	if ($rows == $_POST['OPTIONS_COUNTER']){
		$message = $rows . " Line DELETED from Option<br>";
		$warning = "green";
	}else{
		$message = "ERROR ! ! ! - Only {$rows} lines DELETED out of {$_POST['OPTIONS_COUNTER']} - PLEASE CONTACT SHOPFITTER!!!<br>";
		$warning = "red";
		
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
	}
	if($warning = "green"){
		//echo "SELECTION=" . $selection_id_current;
		$rows = Delete_Selection($selection_id_current);
		if ($rows == 1){
			$message .= "Option {$selection_id_current} successfully DELETED<br>";
			$warning = "green";
		}
		if ($rows == 0){
			$message .= "WARNING ! ! ! - Option NOT DELETED<br>";
			$warning = "orange";
		}
		if ($rows > 1){
			$message .= "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") Option RECORD DELETED - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
	}
	if($warning = "green"){
		//now delete the option from all product records where it was active
		$msg= DeleteSelectionFromProduct($selection_id_current);
		if (substr($msg, 0, 6) == "FAILED"){
			$message = $msg;
			$warning = "red";
		}else{
			$message = $msg;
			$warning = "green";
		}
	}
	//initialise screen
	$selection_id_current = -1;
	unset($selection);
}
//if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Product Options";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Product Options - Create and Amend Product Options</h1>
	<p>&nbsp;</p>
	<table align="left" border="0" cellpadding="2" cellspacing="5">
		<tr>
			<td class="options-td"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is where you create and manage options that can be applied to specific products<br /><br />For example; if one of your items has unusual variations or specific price differential<br /><br />Once you've set an option up here it will be available to be added to the Options 1, 2, 3 and 4 on the Amend Products page<br /><br />Search for the product you want to create options sets for first</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
			<td>&nbsp;</td>
		</tr>
	</table>
    
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    	<tr>
        	<td colspan="2">
            	<?php if(!empty($product)): ?>
            		<a href="amend_products.php?searchdata=&searchproduct=<?php echo $product->PR_PRODUCT; ?>">Click here to go to <?php echo $product->PR_NAME ?> product details </a>
            	<?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    <form name="enter-thumb" action="/_cms/options_product.php" method="post">
    	<!-- SEARCHBOX -->
    	<tr>
          <td class="options-td">Search for: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the product you want to work on<br /><br />To select from all products place the mouse cursor in the search field with no other text and click search<br /><br />Then select the desired product from the Choose from... dropdown box</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td>
            	<Input name="SEARCH_DATA" type="text" size="50" value="<?php echo isset($_POST['SEARCH_DATA']) ? $_POST['SEARCH_DATA'] : "" ?>" />
            </td>
            <td class="update-delete-td">
          	    <Input name="SEARCH" type="submit" value="search" class="search-button" />
            </td>
    	</tr>
        <tr>
        	<td>&nbsp;</td>
            <td colspan="2">
            	<select name="search_results" id="jumpMenu" onchange="MM_jumpMenu('parent',this,1)" class="search-product-box2">
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
							echo "<option value=\"/_cms/options_product.php?searchdata=" . $_POST['SEARCH_DATA'] . "&searchproduct=" . $p->PR_PRODUCT . "\"" . $selected . ">" . $p->PR_PRODUCT . " - " . $p->PR_NAME . "</option>";
						}
					}
					?>
           		</select>
				<input type="hidden" name="SELECTED_PRODUCT" value="<?php echo (isset($selected_product) ? $selected_product : "") ?>">
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the product you wish to work on; you need to have searched for it first</span><span class=\"bottom\"></span></span>" : "") ?></a>
            </td>
        </tr>
        <tr>
			<td class="td-sep" colspan="3">&nbsp;</td>
		</tr>
   		<!-- END OF SEARCHBOX -->
    	<?php
		//-- SELECTION BOX SELECTION -->
		if(isset($selected_product) and $selected_product != ""){
			echo "<tr>";
				echo "<td>Select Option:</td>";
				echo "<td>";
					echo "<select name=\"SELECTION\" onchange=\"MM_jumpMenu('parent',this,1)\">";
						echo "<option value=\"#\">Choose from...</option>";
	
						if(isset($selected_product)){
							if($selection_id_current == -1){$selected = "selected";}else{$selected = "";}
							echo "<option value=\"/_cms/options_product.php?search=" . $_POST['SEARCH_DATA'] . "&product=" . $selected_product . "&selection_id=-1\"" . $selected . ">&lt;New Option&gt;</option>";
							$selections = getAllProductSelections($selected_product);
							foreach($selections as $s){
								//customised product selections only
								if($selection_id_current == $s->SE_ID){
									$selected = "selected ";
								}else{
									$selected = "";
								}		
								echo "<option value=\"/_cms/options_product.php?search=" . $_POST['SEARCH_DATA'] . "&product=" . $selected_product . "&selection_id=" . $s->SE_ID . "\" " . $selected . ">" . html_entity_decode($s->SE_NAME, ENT_QUOTES) . "</option>";
							}
						}
	
					echo "</select>";
					if($selection_id_current != -1){
              			echo "$spacer ";
						echo "Exclude ";
						echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">N or Y: N is the default state and means that the option set is not excluded from the option list; change this to Y and the option set will not be available for selection on the product page<br /><br />Use this when the entire option set needs to be disabled for temporary removal</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
                	}	
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Name: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a name for the option that will be displayed on the product page<br /><br />For example; if you're creating an option set for different colours enter Colour</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td>";
					echo "<input type=\"text\" name=\"SELECTION_NAME\" Size=\"50\" value=\"" . ($selection_id_current == -1 ? "" : html_entity_decode($selection->SE_NAME, ENT_QUOTES)) . "\" />";
					echo "<input type=\"hidden\" name=\"SELECTION_ID_CURRENT\" Size=\"30\" value=\"" . htmlentities($selection_id_current, ENT_QUOTES) . "\" />";
					if($selection_id_current != -1){
						echo "&nbsp;";
						echo "<input type=\"text\" name=\"SELECTION_EXCLUDE\" Size=\"1\" value=\"" . $selection->SE_EXCLUDE . "\" />";
					}
					
				echo "</td>";
				echo "<td>";
					if($selection_id_current != -1){
						echo "<input type=\"submit\" name=\"UPDATE_NAME\" value=\"Update\" class=\"update-button\" />";
					}
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Tags: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select up to 3 option search tags<br /><br />You need to have set this up in the Search Attributes page first<br /><br />This is used for the contextual search system and filters products by their option attributes <br /><br />For example; different colours and sizes as separate attributes will allow filtering a search to all Colour Red items; or further filtering to all Colour Red + Size Medium items<br /><br />You need to be specific in naming option attributes because the Colour could apply to a variety of products that you may need to have separate searches for; eg Colour - T shirts, Colour - Shorts and Colour - Socks</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td colspan=\"2\">";
					echo "<div class=\"tag_wrapper\">";				 
						echo "<select name=\"SELECTION_ATTRIBUTE1\" class=\"select_attribute1\" onchange=\"\">";
							echo "<option value=\"#\">Choose from...</option>";
							$attributes = getAllAttributes();
							foreach($attributes as $a){
								if(isset($selection->SE_ATTRIBUTE1) and $selection->SE_ATTRIBUTE1 == $a->AT_ID){
									$selected = "selected ";
								}else{
									$selected = "";
								}		
								echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
							}
						echo "</select>&nbsp;&nbsp;&nbsp;";
						if(isset($selection)){
							echo "<input name=\"SELECTION_ATTRIBUTE1_ORIG\" type=\"hidden\" value=\"" . $selection->SE_ATTRIBUTE1 . "\" />";
						}
						
						echo "<select name=\"SELECTION_ATTRIBUTE2\" class=\"select_attribute2\" onchange=\"\">";
							echo "<option value=\"#\">Choose from...</option>";
							$attributes = getAllAttributes();
							foreach($attributes as $a){
								if(isset($selection->SE_ATTRIBUTE2) and $selection->SE_ATTRIBUTE2 == $a->AT_ID){
									$selected = "selected ";
								}else{
									$selected = "";
								}		
								echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
							}
						echo "</select>&nbsp;&nbsp;&nbsp;";
						if(isset($selection)){
							echo "<input name=\"SELECTION_ATTRIBUTE2_ORIG\" type=\"hidden\" value=\"" . $selection->SE_ATTRIBUTE2 . "\" />";
						}
						
						echo "<select name=\"SELECTION_ATTRIBUTE3\" class=\"select_attribute3\" onchange=\"\">";
							echo "<option value=\"#\">Choose from...</option>";
							$attributes = getAllAttributes();
							foreach($attributes as $a){
								if(isset($selection->SE_ATTRIBUTE3) and $selection->SE_ATTRIBUTE3 == $a->AT_ID){
									$selected = "selected ";
								}else{
									$selected = "";
								}		
								echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
							}
						echo "</select></br></br>";
						if(isset($selection)){
							echo "<input name=\"SELECTION_ATTRIBUTE3_ORIG\" type=\"hidden\" value=\"" . $selection->SE_ATTRIBUTE3 . "\" />";
						}
					echo "</div>";
				echo "</td>";
			echo "</tr>";
		}
        ?>
        <?php
		if($selection_id_current == -1){
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<input type=\"submit\" name=\"CREATE_SELECTION\" value=\"Create Option\" class=\"create-button\">";
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";
		}
		?>
        <tr>
			<td class="td-sep" colspan="3">&nbsp;</td>
		</tr>
        <?php
		if($selection_id_current != -1){
			//--- DISPLAY OPTION LINES ---------------------------------------------------------------------------------------------------------------------------->
			echo "<tr>";
        		echo "<td>Position ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the option items appear from top to bottom in the dropdown selector<br /><br />Lower numbers display nearer the top</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
           		echo "<td>Option Text ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a unique title for the search attribute; eg T-Shirts Size<br /><br />This is seen by visitors to your website; it allows them to identify what the attribute applies to<br /><br />This should be in the upper field below</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo " / "; 
				echo "Tag Value ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The first three of the four lower boxes<br /><br />Select up to 3 option search tags<br /><br />This is what your site visitors see when asked to make a selection for searching</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo " / ";
				echo "SKU Code ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The last of the four lower boxes<br /><br />Stock Keeping Unit: Enter a SKU code<br /><br />For items that have options the SKU code can be either an amalgamation of top level SKU (set in the prioduct details) and then option specific SKU elements set here<br /><br />Alternatively, complete this field with the SKU code and leave the SKU code field in the product page blank</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "&nbsp;";
				echo "Value ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a price variation for the option<br /><br />eg; if size Large is &pound;/&euro;/$/&curren;2 more than the main price you entered for size medium type in 2.00<br /><br />Conversely, if size small is &pound;/&euro;/$/&curren;2 less then enter -2.00</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "&nbsp;";
				echo "Exclude ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">N or Y: N is the default state and means that the option is not excluded from the option list; change this to Y and the option will not be available for selection on the product page<br /><br />Use this when a product option needs to be disabled due to no stock or other temporary removal</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
            	echo "<td></td>";
       		 echo "</tr>";
            $options = getOptions($selection, "All");
			$cntr1 = 0; $last_number = 0;
            foreach($options as $o){
				$cntr1 ++;
                echo "<tr>";
                    echo "<td>";
						echo "<div class=\"form-input\"><label>Position</label><input type=\"text\" name=\"NUMBER" . $cntr1 . "\" SIZE=\"2\" value=\"" . $o->OP_NUMBER . "\" class=\"box-position\"></div>";
					echo "</td>";
                    echo "<td>";
                        echo "<div class=\"form-input\"><label>Name</label><input type=\"text\" name=\"NAME" . $cntr1 . "\" SIZE=\"50\" value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "\" ></div>";
						echo "<input type=\"hidden\" name=\"NAME_ORIGINAL" . $cntr1 . "\" value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "\" >";
						echo "<div class=\"form-input\"><label>Value</label><input type=\"text\" name=\"VALUE" . $cntr1 . "\" SIZE=\"5\" value=\"" . $o->OP_VALUE . "\" ></div>";
						echo "<div class=\"form-input\"><label>Exclude</label><input type=\"text\" name=\"EXCLUDE" . $cntr1 . "\" SIZE=\"1\" value=\"" . $o->OP_EXCLUDE . "\" class=\"box-live\"></div>";
					echo "</td>";
					echo "<td>";
						
						echo "<div class=\"delete-position\"><input type=\"submit\" name=\"UPDATE_OPTION" . $cntr1 . "\" value=\"Update\" class=\"update-button\" >&nbsp;&nbsp;";
						echo "<input type=\"submit\" name=\"DELETE_OPTION" . $cntr1 . "\" value=\"Delete\" class=\"delete-button\"></div>";
                    echo "</td>";
                echo "</tr>";
				echo "<tr>";
                    echo "<td>";
					echo "</td>";
                    echo "<td>";
						echo "<div class=\"tag_wrapper\">";
							echo "<div class=\"form-input\"><label>Tag1 (Adv Search)</label><select name=\"ATTRIBUTE_VALUE1" . $cntr1 . "\" class=\"select_attribute1 tag-box\" onchange=\"\" >";
								echo "<option value=\"#\">Choose from...</option>";
								foreach($attribute1_values as $v){
									if($o->OP_ATTRIBUTE_VALUE1 == $v->AV_ID){
										$selected = "selected ";
									}else{
										$selected = "";
									}		
									echo "<option value=\"" . $v->AV_ID . "\" " . $selected . ">" . html_entity_decode($v->AV_NAME, ENT_QUOTES) . "</option>";
								}
								echo "</select>";
							echo "</div>";
							echo "<div class=\"form-input\"><label>Tag2 (Adv Search)</label><select name=\"ATTRIBUTE_VALUE2" . $cntr1 . "\" class=\"select_attribute2 tag-box\" onchange=\"\" >";
								echo "<option value=\"#\">Choose from...</option>";
								foreach($attribute2_values as $v){
									if($o->OP_ATTRIBUTE_VALUE2 == $v->AV_ID){
										$selected = "selected ";
									}else{
										$selected = "";
									}		
									echo "<option value=\"" . $v->AV_ID . "\" " . $selected . ">" . html_entity_decode($v->AV_NAME, ENT_QUOTES) . "</option>";
								}
								echo "</select>";
							echo "</div>";
							echo "<div class=\"form-input\"><label>Tag3 (Adv Search)</label><select name=\"ATTRIBUTE_VALUE3" . $cntr1 . "\" class=\"select_attribute3 tag-box\" onchange=\"\" >";
								echo "<option value=\"#\">Choose from...</option>";
								foreach($attribute3_values as $v){
									if($o->OP_ATTRIBUTE_VALUE3 == $v->AV_ID){
										$selected = "selected ";
									}else{
										$selected = "";
									}		
									echo "<option value=\"" . $v->AV_ID . "\" " . $selected . ">" . html_entity_decode($v->AV_NAME, ENT_QUOTES) . "</option>";
								}
								echo "</select>";
							echo "</div>";	
						echo "</div>";
						echo "<div class=\"form-input\"><label>SKU Code</label>";
							echo "<input type=\"text\" name=\"SKU" . $cntr1 . "\" SIZE=\"15\" value=\"" . html_entity_decode($o->OP_SKU, ENT_QUOTES) . "\" >";
						echo "</div>";
					echo "</td>";
					echo "<td>";
                    echo "</td>";
                echo "</tr>";
				echo "<tr><td colspan=\"3\" class=\"td-sep\"></td></tr>";
				$last_number = $o->OP_NUMBER;
            }
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<input name=\"OPTIONS_COUNTER\" type=\"hidden\" value=\"" . $cntr1 . "\" />";
					//echo "<input type=\"submit\" name=\"UPDATE_OPTIONS\" value=\"Update\">";
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";		
	        echo "<tr>";
                echo "<td class=\"td-spacer50\" colspan=\"3\"></td>";

            echo "</tr>";
			//---ADD OPTION --------------------------------------------------------------------------------------------------------------------------------------->	
			$number_to_add = $last_number + 1;
            echo "<tr>";
                echo "<td></td>";
				echo "<td>Add Option: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Add the next option<br /><br />Select the search attribute from the dropdown selector below<br /><br />Add a SKU code in the lower right box if required<br /><br />Click the Add button to create the new option in the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td></td>";
            echo "</tr>";
			echo "<tr>";
                echo "<td>";
                    echo "<div class=\"form-input\"><label>Position</label><input type=\"text\" name=\"NUMBER_TO_ADD\" SIZE=\"2\" value=\"" . $number_to_add . "\" class=\"box-position\"></div>";
				echo "</td>";
				echo "<td>";
				    echo "<div class=\"form-input\"><label>Name</label><input type=\"text\" name=\"NAME_TO_ADD\" SIZE=\"50\" value=\"" . $name_to_add . "\"></div>";
					echo "<div class=\"form-input\"><label>Value</label><input type=\"text\" name=\"VALUE_TO_ADD\" SIZE=\"5\" value=\"" . $value_to_add . "\"></div>";
					echo "<div class=\"form-input\"><label>Exclude</label><input type=\"text\" name=\"EXCLUDE_TO_ADD\" SIZE=\"1\" value=\"" . $exclude_to_add . "\" class=\"box-live\"></div>";
				echo "</td>";
				echo "<td>";	
					echo "<div class=\"delete-position\"><input name=\"ADD_OPTION\" type=\"submit\" value=\"Add\" class=\"add-button\" /></div>";
                echo "</td>";
            echo "</tr>";
			echo "<tr>";
                echo "<td>";
				echo "</td>";
				echo "<td>";
					echo "<div class=\"tag_wrapper\">";
						echo "<div class=\"form-input\"><label>Tag (Adv Search)</label><select name=\"ATTRIBUTE_VALUE_TO_ADD1\" class=\"select_attribute1\" onchange=\"\" class=\"tag-box\">";
							echo "<option value=\"#\">Choose from...</option>";
							foreach($attribute1_values as $v){		
								echo "<option value=\"" . $v->AV_ID . "\" >" . html_entity_decode($v->AV_NAME, ENT_QUOTES) . "</option>";
							}
						echo "</select></div>";
						echo "<div class=\"form-input\"><label>Tag (Adv Search)</label><select name=\"ATTRIBUTE_VALUE_TO_ADD2\" class=\"select_attribute2\" onchange=\"\" class=\"tag-box\">";
							echo "<option value=\"#\">Choose from...</option>";
							foreach($attribute2_values as $v){		
								echo "<option value=\"" . $v->AV_ID . "\" >" . html_entity_decode($v->AV_NAME, ENT_QUOTES) . "</option>";
							}
						echo "</select></div>";
						echo "<div class=\"form-input\"><label>Tag (Adv Search)</label><select name=\"ATTRIBUTE_VALUE_TO_ADD3\" class=\"select_attribute3\" onchange=\"\" class=\"tag-box\">";
							echo "<option value=\"#\">Choose from...</option>";
							foreach($attribute3_values as $v){		
								echo "<option value=\"" . $v->AV_ID . "\" >" . html_entity_decode($v->AV_NAME, ENT_QUOTES) . "</option>";
							}
						echo "</select></div>";
					echo "</div>";
					echo "<div class=\"form-input\"><label>SKU Code</label>";
				    	echo "<input type=\"text\" name=\"SKU_TO_ADD\" SIZE=\"15\" value=\"" . $sku_to_add . "\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "</div>";
				echo "</td>";
				echo "<td>";	
                echo "</td>";
            echo "</tr>";      
		}
        ?>
        
        <tr>
			<td colspan="3">&nbsp;</td>
		</tr>
        <?php
		if($selection_id_current != -1){
			echo "<tr>";
                echo "<td></td>";
				echo "<td>";
				    echo "<input type=\"submit\" name=\"DELETE_SELECTION\" value=\"Delete Option\" class=\"delete-button\" >";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to delete the entire option set</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td></td>";
            echo "</tr>";
		}
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
