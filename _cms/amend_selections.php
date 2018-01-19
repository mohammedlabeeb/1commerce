<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$warning = "";
$pathName = "/images/";
$selection_current = "";
$selection_name = "";
$updateline = ""; $deleteline = "";
$number_to_add = 0;
$name_to_add = "";
$value_to_add = "0.00";
$scrolltobottom = "";

if (isset($_POST['CREATE_SELECTION'])) {
	//create Selection
	$fields = array("se_name"=>$_POST['SELECTION_CURRENT'], "se_label"=>"", "se_exclude"=>"0", "se_custom"=>"0");
	$rows = Create_Selection($fields);	
	if ($rows == 1){
		$message .= "Selection Box CREATED" . "<br/>";
		$warning = "green";
	}else{
		$message .= "Selection Box NOT CREATED!!! - PLEASE CONTACT SHOPFITTER!"; 
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
		$warning = "red";
	}
}

if(isset($_GET['selection'])){$selection_current = html_entity_decode(urldecode($_GET['selection']), ENT_QUOTES);}
if(isset($_POST['SELECTION_CURRENT'])){$selection_current = $_POST['SELECTION_CURRENT'];}

//echo "SELECTION CURRENT=" . $selection_current . "<br/>";
if($selection_current != "" and $selection_current != "new"){
	$selection = getSelection(htmlentities($selection_current, ENT_QUOTES));
	$selection_current = $selection->SE_NAME;
}

//determine if an option line Update or Delete button has been hit
if(isset($_POST['OPTIONS_COUNTER'])){
	$updateline = 0; $deleteline = 0;
	for ($i=1; $i<=$_POST['OPTIONS_COUNTER']; $i++){
		if(isset($_POST['UPDATE_OPTION'.$i])){$updateline = $i; break;}
		if(isset($_POST['DELETE_OPTION'.$i])){$deleteline = $i; break;}
	}
}

if ($deleteline > 0) {
	//delete option
	$rows = Delete_Option($selection_current, "ALL", $_POST['NAME_ORIGINAL'.$deleteline], "");	
	if ($rows == 1){
		$message .= "Option DELETED from the Selection Box" . "<br/>";
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

if ($updateline > 0) {
	//validate all table entries
	$message = "";
	if(is_numeric($_POST['NUMBER'.$updateline]) and $_POST['NUMBER'.$updateline] > 0 and $_POST['NUMBER'.$updateline] <= $_POST['OPTIONS_COUNTER']){$ok = 1;}else{$ok = 0;}
	if ($ok == 0){
		$message .= "Please enter a valid Option Number" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['NAME'.$updateline]) == 0){
		$message .= "Please enter a valid Option Name" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['VALUE'.$updateline]) > 0 and validate2dp($_POST['VALUE'.$updateline]) == "false"){
		$message .= "Please enter a valid Option Value to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		//loop through options and rewrite each with the latest details	
		$fields = array("op_se_name"=>$selection_current, "name_original"=>$_POST['NAME_ORIGINAL'.$updateline], "op_name"=>$_POST['NAME'.$updateline], "op_number"=>$_POST['NUMBER'.$updateline], "op_text"=>"",
							"op_value"=>$_POST['VALUE'.$updateline], "op_selected"=>"N", "op_product"=>"");
			
		$rows = Rewrite_Option($fields);
		if ($rows == 1){
			$message .= "{$rows} Option successfully UPDATED" . "<br/>";
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
		$message .= "Please enter a valid Option Name" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['VALUE_TO_ADD']) > 0 and validate2dp($_POST['VALUE_TO_ADD']) == "false"){
		$message .= "Please enter a valid Option Value to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		$fields = array("op_se_name"=>$selection_current, "op_name"=>$_POST['NAME_TO_ADD'], "op_number"=>$_POST['NUMBER_TO_ADD'], "op_text"=>"",
						"op_value"=>$_POST['VALUE_TO_ADD'], "op_selected"=>"N", "op_product"=>"");		
		$rows = Create_Option($fields);
		if ($rows == 1){
			$message = $rows . " Option ADDED to Selection Box";
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
	$rows = Delete_All_Options($selection_current, "");
	if ($rows == $_POST['OPTIONS_COUNTER']){
		$message = $rows . " options DELETED from Selection Box<br>";
		$warning = "green";
	}else{
		$message = "ERROR ! ! ! - Only {$rows} options DELETED out of {$_POST['OPTIONS_COUNTER']} - PLEASE CONTACT SHOPFITTER!!!<br>";
		$warning = "red";
		
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
	}
	if($warning = "green"){
		//echo "SELECTION=" . $selection_current;
		$rows = Delete_Selection($_POST['SELECTION_CURRENT'], 0);
		if ($rows == 1){
			$message .= "Selection Box {$selection_current} successfully DELETED<br>";
			$warning = "green";
		}
		if ($rows == 0){
			$message .= "WARNING ! ! ! - Selection Box NOT DELETED<br>";
			$warning = "orange";
		}
		if ($rows > 1){
			$message .= "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") Selection RECORD DELETED - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!<br>";
			$warning = "red";
		}
	}
	//initialise screen
	$selection_current = "";
	unset($selection);
}
//if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Amend Selection Box";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;


include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Amend Selection Box - Create and Amend Selection Box Details</h1>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="enter-thumb" action="/_cms/amend_selections.php" method="post">
		<!--- SELECTION BOX SELECTION ----------------------------------------------------------------------------------------------------------->
        <tr>
        	<td>Selection Box:</td>
            <td>
                <select name="SELECTION" onchange="MM_jumpMenu('parent',this,1)">
                    <option value="#">Choose from...</option>
                    <?php
					if($selection_current == "new"){$selected = "selected";}
					echo "<option value=\"/_cms/amend_selections.php?selection=new\"" . $selected . ">&lt;New Selection Box&gt;</option>";
                    $selections = getAllSelections();
                    foreach($selections as $s){
						//no custom selections
						if($s->SE_CUSTOM == 0){
							if($selection_current == html_entity_decode($s->SE_NAME, ENT_QUOTES)){
								$selected = "selected ";
							}else{
								$selected = "";
							}		
							echo "<option value=\"/_cms/amend_selections.php?selection=" . urlencode(html_entity_decode($s->SE_NAME, ENT_QUOTES)) . "\" " . $selected . ">" . html_entity_decode($s->SE_NAME, ENT_QUOTES) . "</option>";
						}
                    }
                    ?>
                </select>
			</td>
            <td></td>
        </tr>
        <tr>
        	<td>Name:</td>
            <td>
            	<input type="text" name="SELECTION_CURRENT" Size="50" value="<?php echo ($selection_current == "new" ? "" : $selection_current) ?>" />
                <input type="hidden" name="SELECTION_CURRENT_CODED" Size="30" value="<?php echo htmlentities($selection_current, ENT_QUOTES) ?>" />
            </td>
            <td></td>
        </tr>
        <?php
		if($selection_current == "new"){
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<input type=\"submit\" name=\"CREATE_SELECTION\" value=\"Create Selection Box\">";
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";
		}
		?>
        <tr>
			<td colspan="3">&nbsp;</td>
		</tr>
        <?php
		if(isset($selection)){
			//--- DISPLAY OPTION LINES ---------------------------------------------------------------------------------------------------------------------------->
			echo "<tr>";
        		echo "<td>Number</td>";
           		echo "<td>Option Name</td>";
            	echo "<td>Value</td>";
       		 echo "</tr>";
            $options = getOptions("", $selection);
			$cntr1 = 0; $last_number = 0;
            foreach($options as $o){
				$cntr1 ++;
                echo "<tr>";
                    echo "<td>";
						echo "<input type=\"text\" name=\"NUMBER" . $cntr1 . "\" SIZE=\"2\" value=\"" . $o->OP_NUMBER . "\" >";
					echo "</td>";
                    echo "<td>";
                        echo "<input type=\"text\" name=\"NAME" . $cntr1 . "\" SIZE=\"50\" value=\"" . $o->OP_NAME . "\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<input type=\"hidden\" name=\"NAME_ORIGINAL" . $cntr1 . "\" value=\"" . $o->OP_NAME . "\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "</td>";
					echo "<td>";
						echo "<input type=\"text\" name=\"VALUE" . $cntr1 . "\" SIZE=\"10\" value=\"" . $o->OP_VALUE . "\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<input type=\"submit\" name=\"UPDATE_OPTION" . $cntr1 . "\" value=\"Update\" >&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<input type=\"submit\" name=\"DELETE_OPTION" . $cntr1 . "\" value=\"Delete\" >&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo "</td>";
                echo "</tr>";
				$last_number = $o->OP_NUMBER;
            }
			echo "<tr>";
				echo "<td</td>";
				echo "<td>";
					echo "<input name=\"OPTIONS_COUNTER\" type=\"hidden\" value=\"" . $cntr1 . "\" />";
					//echo "<input type=\"submit\" name=\"UPDATE_OPTIONS\" value=\"Update\">";
				echo "</td>";
				echo "<td</td>";
			echo "</tr>";		
	        echo "<tr>";
                echo "<td colspan=\"3\"></td>";
            echo "</tr>";
			//---ADD OPTION --------------------------------------------------------------------------------------------------------------------------------------->	
			$number_to_add = $last_number + 1;
            echo "<tr>";
                echo "<td></td>";
				echo "<td>Add Option:</td>";
				echo "<td></td>";
            echo "</tr>";
			echo "<tr>";
                echo "<td>";
                    echo "<input type=\"text\" name=\"NUMBER_TO_ADD\" SIZE=\"2\" value=\"" . $number_to_add . "\">";
				echo "</td>";
				echo "<td>";
				    echo "<input type=\"text\" name=\"NAME_TO_ADD\" SIZE=\"50\" value=\"" . $name_to_add . "\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "</td>";
				echo "<td>";	
					echo "<input type=\"text\" name=\"VALUE_TO_ADD\" SIZE=\"10\" value=\"" . $value_to_add . "\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

					echo "<input name=\"ADD_OPTION\" type=\"submit\" value=\"Add\" />";
                echo "</td>";
            echo "</tr>";             
		}
        ?>
        
        <tr>
			<td colspan="3">&nbsp;</td>
		</tr>
        <?php
		if(isset($selection)){
			echo "<tr>";
                echo "<td></td>";
				echo "<td>";
				    echo "<input type=\"submit\" name=\"DELETE_SELECTION\" value=\"Delete Selection Box\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "</td>";
				echo "<td></td>";
            echo "</tr>";
		}
		?>
        <tr>
			<td colspan="3"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>        

	</form>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>

