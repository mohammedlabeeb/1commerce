<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");
$preferences = getPreferences();
$next_code = getNextCode("promotion");

$message = "";
$warning = "";
$pathName = "/images/";
$selected_product = "";
$selection_id_current = -1;
$selection_name = "";
$updateline = ""; $deleteline = "";
$number_to_add = 0;
$category_to_add = "";
$product_to_add = "";
$sku_to_add = "";
$scrolltobottom = "";
$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer2 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer2 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer3 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if(isset($_REQUEST['start_date']) and isset($_REQUEST['expiry_date'])){
	//date is returned as yyyy-mm-dd
	$start_date = $_REQUEST['start_date'];
	$strDate = explode("-", $start_date);
	$fday = $strDate[2]; $fmonth = $strDate[1]; $fyear = $strDate[0];
	$expiry_date = $_REQUEST['expiry_date'];
	$strDate = explode("-", $expiry_date);
	$tday = $strDate[2]; $tmonth = $strDate[1]; $tyear = $strDate[0];
}else{
	//default date
	$today = strftime("%Y-%m-%d", time());
	$strDate = explode("-", $today);
	$fday = $strDate[2]; $fmonth = $strDate[1]; $fyear = $strDate[0];
	$tday = $strDate[2]; $tmonth = $strDate[1]; $tyear = $strDate[0];
	$from_date = $today; $to_date = $today;
}

$selected_promo_id = isset($_GET['promo_id']) ? $_GET['promo_id'] : "";
$selected_promo_no = isset($_GET['promo_no']) ? $_GET['promo_no'] : "";
if (isset($_POST['SELECTED_PROMO_ID'])){$selected_promo_id = $_POST['SELECTED_PROMO_ID'];}
if (isset($_POST['SELECTED_PROMO_NO'])){$selected_promo_no = $_POST['SELECTED_PROMO_NO'];}

if (isset($_POST['CREATE_PROMOTION'])) {
	//create Selection
	if(!is_numeric($_POST['PROMO_ADJUST'])){
		$message .= "Adjustment field must be numeric" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['PROMO_ADJUST']) > 0 and validate2dp($_POST['PROMO_ADJUST']) == "false"){
		$message .= "Please enter a valid Adjustment value to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		$fields = array("promh_no"=>$_POST['PROMO_NO'], "promh_prom_id"=>$selected_promo_id, "promh_adjust"=>$_POST['PROMO_ADJUST'],
						"promh_start"=>pack_calendar_date($start_date, "from"), "promh_expiry"=>pack_calendar_date($expiry_date, "to"));
		$rows = Create_Promotion($fields);	
		if ($rows == 1){
			$message .= "Promotion CREATED" . "<br/>";
			$warning = "green";
			$selected_promo_no = $_POST['PROMO_NO'];
			if(substr($selected_promo_no, 0, 2) == "PM"){
				//only increment the seed if the user has taken out a "PM" number and is not using a bespoke numbering system of his own
				$rows = incrementSeed($selected_promo_no);
			}
		}else{
			$message .= "Promotion NOT CREATED!!! - PLEASE CONTACT SHOPFITTER!"; 
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

if (isset($_POST['UPDATE_PROMHEAD'])) {
	if(!is_numeric($_POST['PROMO_ADJUST'])){
		$message .= "Adjustment field must be numeric" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['PROMO_ADJUST']) > 0 and validate2dp($_POST['PROMO_ADJUST']) == "false"){
		$message .= "Please enter a valid Adjustment value to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		$fields = array("promh_no"=>$selected_promo_no, "promh_adjust"=>$_POST['PROMO_ADJUST'],
						"promh_start"=>pack_calendar_date($start_date, "from"), "promh_expiry"=>pack_calendar_date($expiry_date, "to"));
		$rows = Rewrite_Promotion($fields);	
		if ($rows == 1){
			$message .= "Promotion Header amended successfully" . "<br/>";
			$warning = "green";
		}elseif ($rows == 0){
			$message .= "No Changes made to Promotion Header"; 
			$warning = "orange";
		}else{
			$message .= "Promotion Header NOT updated!!! - PLEASE CONTACT SHOPFITTER!"; 
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

//determine if an option line Update or Delete button has been hit
if(isset($_POST['LINE_COUNTER'])){
	$updateline = 0; $deleteline = 0;
	for ($i=1; $i<=$_POST['LINE_COUNTER']; $i++){
		if(isset($_POST['UPDATE_LINE_'.$i])){$updateline = $i; break;}
		if(isset($_POST['DELETE_LINE_'.$i])){$deleteline = $i; break;}
	}
}
if ($updateline > 0) {
	//update option button has been hot so... validate all table entries
	$message = "";
//	if($_POST['CATEGORY_'.$updateline] != "" and $_POST['PROMO_LEVEL'] == "Product"){
//			$message .= "Cannot enter a Category code against a Product level Promotion type" . "<br/>";
//			$warning = "red";
//	}
//	if($_POST['PRODUCT_'.$updateline] != "" and $_POST['PROMO_LEVEL'] == "Category"){
//			$message .= "Cannot enter a Product code against a Category level Promotion type" . "<br/>";
//			$warning = "red";
//	}
	if($_POST['CATEGORY_'.$updateline] != ""){
		if(CheckCategoryExists($_POST['CATEGORY_'.$updateline]) == "false"){
			$message .= "Please enter a valid Category" . "<br/>";
			$warning = "red";
		}
	}
	if($_POST['PRODUCT_'.$updateline] != ""){
		if(CheckProductExists($_POST['PRODUCT_'.$updateline]) == "false"){
			$message .= "Please enter a valid Product" . "<br/>";
			$warning = "red";
		}
	}
	if($_POST['CATEGORY_'.$updateline] != "" and $_POST['PRODUCT_'.$updateline] != ""){
		$message .= "Cannot enter both a Category and Product against a single promotion line" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		//loop through options and rewrite each with the latest details
		$fields = array("proml_no"=>$selected_promo_no, "proml_pos"=>$updateline, "proml_cat"=>$_POST['CATEGORY_'.$updateline], 
							"proml_prod"=>$_POST['PRODUCT_'.$updateline]);
		$rows = Rewrite_Promoline($fields);
		if ($rows == 1){
			$message .= "{$rows} Line successfully UPDATED" . "<br/>";
			$warning = "green";
		}
		if (($rows == 0) and $message == "" ){
			$message .= "WARNING ! ! ! - NO CHANGES MADE";
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

if ($deleteline > 0) {
	//delete option button has been hit
	$rows = Delete_Promo_Lines($selected_promo_no, $deleteline);	
	if ($rows == 1){
		$rows = Renumber_Promolines($selected_promo_no);
		$message .= "Line successfully deleted " . "<br/>";
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

if (isset($_POST['ADD_LINE'])) {
	//validate fields
	$message = "";
//	if($_POST['CATEGORY_TO_ADD'] != "" and $_POST['PROMO_LEVEL'] == "Product"){
//			$message .= "Cannot enter a Category code against a Product level Promotion type" . "<br/>";
//			$warning = "red";
//	}
//	if($_POST['PRODUCT_TO_ADD'] != "" and $_POST['PROMO_LEVEL'] == "Category"){
//			$message .= "Cannot enter a Product code against a Category level Promotion type" . "<br/>";
//			$warning = "red";
//	}
	if ($_POST['CATEGORY_TO_ADD'] != ""){
		echo "CATEGORY=" . $_POST['CATEGORY_TO_ADD'];
		if(CheckCategoryExists($_POST['CATEGORY_TO_ADD']) == "false"){
			$message .= "Please enter a valid Category" . "<br/>";
			$warning = "red";
		}
	}
	if ($_POST['PRODUCT_TO_ADD'] != ""){
		if(CheckProductExists($_POST['PRODUCT_TO_ADD']) == "false"){
			$message .= "Please enter a valid Product" . "<br/>";
			$warning = "red";
		}
	}
	if($_POST['CATEGORY_TO_ADD'] != "" and $_POST['PRODUCT_TO_ADD'] != ""){
		$message .= "Cannot enter both a Category and Product against a single promotion line" . "<br/>";
		$warning = "red";
	}
	if($_POST['CATEGORY_TO_ADD'] == "" and $_POST['PRODUCT_TO_ADD'] == ""){
		$message .= "Must enter either a Category code OR a Product Code" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		$fields = array("proml_no"=>$selected_promo_no, "proml_pos"=>$_POST['LINE_COUNTER'] + 1,
						"proml_cat"=>$_POST['CATEGORY_TO_ADD'], "proml_prod"=>$_POST['PRODUCT_TO_ADD']);		
		$rows = Create_Promoline($fields);
		if ($rows == 1){
			$message = $rows . " Line ADDED to Promotion";
			$warning = "green";
		}
		if ($rows == 0){
			$message = "WARNING ! ! ! - NO RECORDS UPDATED!!!";
			$warning = "orange";
		}
		if ($rows > 1){
			$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") PROMLINE RECORD UPDATED - PLEASE CONTACT SHOPFITTER!!!";
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

if (isset($_POST['DELETE_PROMOTION'])) {
	//delete promotion + all lines
	$rows = Delete_Promo_Lines($selected_promo_no, "ALL");
	if ($rows != $_POST['LINE_COUNTER']){
		$message = "ERROR ! ! ! - Only {$rows} promotion lines DELETED out of {$_POST['OPTIONS_COUNTER']} - PLEASE CONTACT SHOPFITTER!!!<br>";
		$warning = "red";
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
	}else{
		//echo "SELECTION=" . $selection_id_current;
		$rows = Delete_Promo_Header($selected_promo_no);
		if ($rows == 1){
			$message .= "Promotion successfully DELETED<br>";
			$warning = "green";
			$selected_promo_no = "";
		}
		if ($rows == 0){
			$message .= "WARNING ! ! ! - Promotion Header NOT DELETED<br>";
			$warning = "orange";
			$selected_promo_no = "";
		}
		if ($rows > 1){
			$message .= "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") Promotion Header DELETED - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
	}
}

if($selected_promo_id != ""){
	//we have a promotion type selected
	$promotion_type = Get_Promotion_Type($selected_promo_id);
	$level = $promotion_type->PROM_LEVEL;
}else{
	$level = "";
}
if($selected_promo_no != "" and $selected_promo_no != -1){
	//we have a selected promotion number
	$promotion = Get_Promotion("", $selected_promo_no);
	$adjust = $promotion->PROMH_ADJUST;
	//date is returned as yyyy-mm-dd
	$start_date = unpack_calendar_date($promotion->PROMH_START);
	$strDate = explode("-", $start_date);
	$fday = $strDate[2]; $fmonth = $strDate[1]; $fyear = $strDate[0];
	$expiry_date = unpack_calendar_date($promotion->PROMH_EXPIRY);
	$strDate = explode("-", $expiry_date);
	$tday = $strDate[2]; $tmonth = $strDate[1]; $tyear = $strDate[0];
	$promolines = Get_Promolines($selected_promo_no);
}else{
	//initialise screen fields
	$adjust = "";
}
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Product Options";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Maintain Promotions - Create and Amend Promotions</h1>
    <p><br /><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is where you create and manage promotions that can then be applied to specific categories or products</span><span class=\"bottom\"></span></span>" : "") ?></a>
    <table id="maintain_promotion" align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="enter-thumb" action="/_cms/maintain_promotions.php" method="post">
    	<?php require_once('calendar/classes/tc_calendar.php'); ?>
    	<!-- ENTER PROMOTION TYPE -->
        <tr>
        	<td class="promotion-td">Select Promotion Type:</td>
            <td colspan="3">
            	<select name="promotion_types" id="jumpMenu" onchange="MM_jumpMenu('parent',this,1)" class="search-product-box2">
                	<option value="/_cms/maintain_promotions.php?promo_id=">Choose from...</option>
                    <?php
					$promotion_type = Get_Promotion_Type("ALL");
					foreach($promotion_type as $p){
						if(isset($_GET['promo_id']) and $_GET['promo_id'] == $p->PROM_ID){
							$selected = "selected";
						}elseif(isset($_POST['SELECTED_PROMO_ID']) and $_POST['SELECTED_PROMO_ID'] == $p->PROM_ID){
							$selected = "selected";
						}else{
							$selected = "";
						}
						echo "<option value=\"/_cms/maintain_promotions.php?promo_id=" . $p->PROM_ID . "\"" . $selected . ">" . $p->PROM_TYPE . "</option>";
					}
					?>
           		</select>
				<input type="hidden" name="SELECTED_PROMO_ID" value="<?php echo (isset($selected_promo_id) ? $selected_promo_id : "") ?>">
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the type of promotion you wish to work on</span><span class=\"bottom\"></span></span>" : "") ?></a>
            </td>
        </tr>
        <?php 
//        if($selected_promo_id != ""){
//			echo "<tr>";
//				echo "<td>Level:";
//	            echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Promotion level may be 'Category', 'Product' or 'Both'</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
//				echo "</td>";
//				echo "<td>";
//					echo "<input type=\"text\" name=\"PROMO_LEVEL\" Size=\"10\" value=\"" . $level . "\" />";
//				echo "</td>";
//			echo "</tr>";
//		}
        ?>
        <tr>
			<td colspan="4">&nbsp;</td>
		</tr>
   		<!-- END OF ENTER PROMOTION TYPE -->
        <!-- LIST ALL PROMOTIONS----------->
        <?php 
        if($selected_promo_id != ""){
            echo "<tr>";
                echo "<td>Select Promotion:</td>";
                echo "<td colspan=\"3\">";
                    echo "<select name=\"PROMOTIONS\" onchange=\"MM_jumpMenu('parent',this,1)\">";
                        echo "<option value=\"/_cms/maintain_promotions.php?promo_id=" . $selected_promo_id . "&promo_no=#\">Choose from...</option>";
                        if(isset($selected_promo_id)){
                            if($selected_promo_no == -1){$selected = "selected";}else{$selected = "";}
                            echo "<option value=\"/_cms/maintain_promotions.php?promo_id=" . $selected_promo_id . "&promo_no=-1\"" . $selected . ">&lt;New Promotion&gt;</option>";
                            $promotion = Get_Promotion($selected_promo_id, "");
                            foreach($promotion as $p){
                                //customised product selections only
                                if($selected_promo_no == $p->PROMH_NO){
                                    $selected = "selected ";
                                }else{
                                    $selected = "";
                                }		
                                echo "<option value=\"/_cms/maintain_promotions.php?promo_id=" . $selected_promo_id . "&promo_no=" . $p->PROMH_NO . "\" " . $selected . ">" . $p->PROMH_NO . "</option>";
                            }
                        }
                    echo "</select>";	
					echo "<input type=\"hidden\" name=\"SELECTED_PROMO_NO\" value=\"" . (isset($selected_promo_no) ? $selected_promo_no : "") . "\">";
                echo "</td>";
           echo "</tr>";
        }
        ?>
        <?php
        if($selected_promo_no != ""){
			if($selected_promo_no == -1){
				echo "<tr>";
					echo "<td>Promotion No:";
						echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter new Promotion Number<br /><br />This may be taken from seed or may be overrided by a number of your own choice</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
					echo "</td>";
					echo "<td colspan=\"3\">";
						echo "<input type=\"text\" name=\"PROMO_NO\" Size=\"10\" value=\"" . $next_code . "\" />";
					echo "</td>";
				echo "</tr>";
			}
			echo "<tr>";
				echo "<td>Start Date:</td>";
				echo "<td colspan=\"3\">";
					//FROM DATE ---------------------------------instantiate class and set properties 
					$myCalendar = new tc_calendar("start_date", true);
					$myCalendar->setIcon("calendar/images/iconCalendar.gif");
					$myCalendar->setDate($fday, $fmonth, $fyear);
					$myCalendar->setPath('calendar'); //set path to calendar_form.php
					
					//output the calendar
					$myCalendar->writeScript();
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Expiry Date:</td>";
				echo "<td colspan=\"3\">";
					//FROM DATE ---------------------------------instantiate class and set properties 
					$myCalendar = new tc_calendar("expiry_date", true);
					$myCalendar->setIcon("calendar/images/iconCalendar.gif");
					$myCalendar->setDate($tday, $tmonth, $tyear);
					$myCalendar->setPath('calendar'); //set path to calendar_form.php
					
					//output the calendar
					$myCalendar->writeScript();
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Adjustment:";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the adjustment to be applied against the Promotion Number<br /><br />For example; For Promotion Type 'Percentage', an Adjustment of -10 will discount all products on promotion by 10%</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td>";
					echo "<input type=\"text\" name=\"PROMO_ADJUST\" Size=\"10\" value=\"" . $adjust . "\" />";
				echo "</td>";
				echo "<td>";
					if($selected_promo_no != -1){
						echo "<input type=\"submit\" name=\"UPDATE_PROMHEAD\" value=\"Update\" class=\"update-button\" />";
					}
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";
        }
        ?>
         <?php
		if($selected_promo_no == -1){
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<input type=\"submit\" name=\"CREATE_PROMOTION\" value=\"Create Option\" class=\"create-button\">";
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";
		}
		?>
        <tr>
			<td colspan="4">&nbsp;</td>
		</tr>
    	<!-- END OF LIST ALL PROMOTIONS--------------------------------------------------------------------------------------------------------->
        <!-- DISPLAY ALL PROMOTION LINES ------------------------------------------------------------------------------------------------------->
        <?php
		if($selected_promo_no != "" and $selected_promo_no != -1){
			echo "<tr>";
				echo "<td>Number ";
				echo "</td>";
				echo "<td>Category ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a category to which the promotion will apply<br /></span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td>Product ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a product to which the promotion will apply</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td></td>";
			 echo "</tr>";
			$promolines = Get_Promolines($selected_promo_no);
			$cntr1 = 0; $last_number = 0;
			foreach($promolines as $p){
				$cntr1 ++; $cat_name = "";
				if($p->PROML_CAT !=""){
					$cat = List_Categories($p->PROML_CAT);
					$cat_name = " - " . $cat->CA_NAME;
				}
				echo "<tr>";
					echo "<td>";
						echo "<input type=\"text\" name=\"NUMBER_" . $cntr1 . "\" SIZE=\"2\" value=\"" . $p->PROML_POS . "\" >";
					echo "</td>";
					echo "<td>";
						echo "<input type=\"text\" name=\"CATEGORY_" . $cntr1 . "\" SIZE=\"40\" value=\"" . html_entity_decode($p->PROML_CAT, ENT_QUOTES) . $cat_name . "\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<input type=\"hidden\" name=\"CATEGORY_ORIGINAL" . $cntr1 . "\" value=\"" . html_entity_decode($p->PROML_CAT, ENT_QUOTES) . "\" >";
					echo "</td>";
					echo "<td>";	
						echo "<input type=\"text\" name=\"PRODUCT_" . $cntr1 . "\" SIZE=\"20\" value=\"" . $p->PROML_PROD . "\" >&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<input type=\"hidden\" name=\"PRODUCT_ORIGINAL" . $cntr1 . "\" SIZE=\"1\" value=\"" . $p->PROML_PROD . "\" >&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "</td>";
					echo "<td>";
						
						echo "<input type=\"submit\" name=\"UPDATE_LINE_" . $cntr1 . "\" value=\"Update\" class=\"update-button\" >&nbsp;&nbsp;";
						echo "<input type=\"submit\" name=\"DELETE_LINE_" . $cntr1 . "\" value=\"Delete\" class=\"delete-button\">&nbsp;&nbsp;";
					echo "</td>";
				echo "</tr>";
				$last_number = $cntr1;
			}
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<input name=\"LINE_COUNTER\" type=\"hidden\" value=\"" . $cntr1 . "\" />";
					//echo "<input type=\"submit\" name=\"UPDATE_OPTIONS\" value=\"Update\">";
				echo "</td>";
				echo "<td></td>";
				echo "<td></td>";
			echo "</tr>";		
			echo "<tr>";
				echo "<td colspan=\"4\">&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan=\"4\" class=\"td-sep\">&nbsp;</td>";
			echo "</tr>";
			//-- END OF DISPLAY ALL PROMOTION LINES ------------------------------------------------------------------------------------------------>

			//-- ADD PROMOTION LINE ---------------------------------------------------------------------------------------------------------------->    
        	$number_to_add = $last_number + 1;
            echo "<tr>";
				echo "<td colspan=\"4\">Add Line: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Add the next line<br /><br />Click the Add button to create an additional promotion line</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
            echo "</tr>";
			echo "<tr>";
                echo "<td>";
                    echo "<input type=\"text\" name=\"NUMBER_TO_ADD\" SIZE=\"2\" value=\"" . $number_to_add . "\">";
				echo "</td>";
				echo "<td>";
					echo "<select name=\"CATEGORY_TO_ADD\" class=\"search-product-box2\">";
						echo "<option value=\"\">Choose from...</option>";
						$categories = List_Categories("ALL");
						foreach($categories as $c){
							echo "<option value=\"" . $c->CA_CODE . "\">" . $c->CA_CODE . " - " . $c->CA_NAME . "</option>";
						}
					echo "</select>";
				echo "</td>";
//				echo "<td>";
//					echo "<input type=\"text\" name=\"CATEGORY_TO_ADD\" SIZE=\"20\" value=\"" . $category_to_add . "\">&nbsp;&nbsp;&nbsp;&nbsp;";
//				echo "</td>";
				echo "<td>";
					echo "<input type=\"text\" name=\"PRODUCT_TO_ADD\" SIZE=\"20\" value=\"" . $product_to_add . "\">&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "</td>";
				echo "<td>";	
					echo "<input name=\"ADD_LINE\" type=\"submit\" value=\"Add\" class=\"add-button\" />";
                echo "</td>";
            echo "</tr>";
			//-- END OF ADD PROMOTION LINE --------------------------------------------------------------------------------------------------------->
			//-- DELETE PROMOTION ------------------------------------------------------------------------------------------------------------------>
			echo "<tr>";
				echo "<td colspan=\"4\" class=\"td-sep\">&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<input type=\"submit\" name=\"DELETE_PROMOTION\" value=\"Delete Option\" class=\"delete-button\" >";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to delete the entire Promotion</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td></td>";
				echo "<td></td>";
			echo "</tr>";
			//-- END OF DELETE PROMOTION ----------------------------------------------------------------------------------------------------------->
		}
		echo "<tr>";
			echo "<td colspan=\"4\"><label class=\"" . $warning . "\" >" . $message . "</label></td>";
		echo "</tr>";
		?>

	</form>
    	<tr>
        	<td colspan="4">&nbsp;</td>
        </tr>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>

