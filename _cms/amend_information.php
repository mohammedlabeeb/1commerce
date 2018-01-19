<?php
header('Content-Type: text/html; charset=utf-8');
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$warning = "";
$selection_current = "";
$selection_name = "";
$updateline = ""; $deleteline = "";
$position_to_add = 0;
$page_to_add = "";
$name_to_add = "";
$link_to_add = "";
$edit_to_add = "Y";
$enabled_to_add = "Y";
$scrolltobottom = "";
$spacer1 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer2 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer2 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer2 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer2 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer3 = "&nbsp;&nbsp;";
$spacer4 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer5 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer5 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer5 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$spacer5 .= "&nbsp;";
$spacer6 = "&nbsp;&nbsp;&nbsp;&nbsp;";

//determine if an option line Update or Delete button has been hit
if(isset($_POST['INFO_COUNTER'])){
	$updateline = 0; $deleteline = 0;
	for ($i=1; $i<=$_POST['INFO_COUNTER']; $i++){
		if(isset($_POST['UPDATE_INFO'.$i])){$updateline = $i; break;}
		if(isset($_POST['DELETE_INFO'.$i])){$deleteline = $i; break;}
	}
}

if ($deleteline > 0) {
	//delete button has been hit - send PAGE_ORIGINAL rather than NAME as NAME could well have been changed before the delete button was hit
	$rows = Delete_Information($_POST['PAGE_ORIGINAL'.$deleteline], "");	
	if ($rows == 1){
		$message .= "Line DELETED from Information Menu Bar" . "<br/>";
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
	$infopage = getInformationPage($_POST['PAGE_ORIGINAL'.$updateline]);
	if (count($infopage) != 1){
		$message = "ORIGINAL PAGE NOT FOUND!!!";
		$warning = "red";
	}
	$message = "";
	if(is_numeric($_POST['POSITION'.$updateline]) and $_POST['POSITION'.$updateline] > 0 and $_POST['POSITION'.$updateline] <= $_POST['POSITION_TO_ADD']){$ok = 1;}else{$ok = 1;}
	if ($ok == 0){
		$message .= "Please enter a valid Line Position - maximum allowed is {$_POST['POSITION_TO_ADD']}" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['NAME'.$updateline]) == 0){
		$message .= "Please enter a valid Line Display Name" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['PAGE'.$updateline]) == 0){
		$message .= "Please enter a valid Line Page Name" . "<br/>";
		$warning = "red";
	}
	if ($_POST['EDIT'.$updateline] != "Y" and $_POST['EDIT'.$updateline] != "N"){
		$message .= "Please enter a valid Line Edit Value (Y/N)" . "<br/>";
		$warning = "red";
	}
	if ($_POST['ENABLED'.$updateline] != "Y" and $_POST['ENABLED'.$updateline] != "N"){
		$message .= "Please enter a valid Line Enabled Value (Y/N)" . "<br/>";
		$warning = "red";
	}
	if (($_POST['ENABLED'.$updateline] == "Y" and $infopage->IN_ENABLED == "N") and ($_POST['NUMBER_CHARACTERS'] + strlen($_POST['NAME'.$updateline]) > 192)){
		//line is now to be enabled so check the extra display field is going to fit on the information  bar
		$total_characters = strlen($_POST['NAME'.$updateline]) + $_POST['NUMBER_CHARACTERS'];
		$message .= "The number of characters in the Title Name will take the ENABLED Menu Bar total to {$total_characters} which is > 192 - the information bar isn't long enough!!!" . "<br/>";
		$warning = "red";
	}
	
	if($message == ""){
		//rewrite information
		$fields = array("in_page"=>$_POST['PAGE'.$updateline], "page_original"=>$_POST['PAGE_ORIGINAL'.$updateline],
						 "in_name"=>$_POST['NAME'.$updateline], "in_position"=>$_POST['POSITION'.$updateline], "in_link"=>$_POST['LINK'.$updateline], 
						 "in_edit"=>$_POST['EDIT'.$updateline], "in_enabled"=>$_POST['ENABLED'.$updateline]);	
		$rows = Rewrite_Information($fields);
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

if (isset($_POST['ADD_INFO'])) {
	//validate fields
	$message = "";
	if(is_numeric($_POST['POSITION_TO_ADD']) and $_POST['POSITION_TO_ADD'] > 0 and $_POST['POSITION_TO_ADD'] <= $_POST['INFO_COUNTER']){$ok = 1;}else{$ok = 1;}
	if ($ok == 0){
		$message .= "Please enter a valid Line Position {$_POST['POSITION_TO_ADD']} / {$_POST['INFO_COUNTER']}" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['PAGE_TO_ADD']) == 0){
		$message .= "Please enter a valid Page Name" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['NAME_TO_ADD']) == 0){
		$message .= "Please enter a valid Display Name" . "<br/>";
		$warning = "red";
	}
	if ($_POST['EDIT_TO_ADD'] != "Y" and $_POST['EDIT_TO_ADD'] != "N"){
		$message .= "Please enter a valid Line Edit Value (Y/N)" . "<br/>";
		$warning = "red";
	}
	if ($_POST['ENABLED_TO_ADD'] != "Y" and $_POST['ENABLED_TO_ADD'] != "N"){
		$message .= "Please enter a valid Line Enabled Value (Y/N)" . "<br/>";
		$warning = "red";
	}
	if ($_POST['ENABLED_TO_ADD'] == "Y" and (strlen($_POST['NAME_TO_ADD']) + $_POST['NUMBER_CHARACTERS'] > 192)){
		$total_characters = strlen($_POST['NAME_TO_ADD']) + $_POST['NUMBER_CHARACTERS'];
		$message .= "The number of characters in the Title Name will take the active Menu Bar total to {$total_characters} which is > 192 - the information bar isn't long enough!!!" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		$fields = array("in_page"=>$_POST['PAGE_TO_ADD'], "in_name"=>$_POST['NAME_TO_ADD'], "in_position"=>$_POST['POSITION_TO_ADD'], "in_link"=>$_POST['LINK_TO_ADD'], 
						"in_edit"=>$_POST['EDIT_TO_ADD'], "in_enabled"=>$_POST['ENABLED_TO_ADD']);	
		$rows = Create_Information($fields);
		if ($rows == 1){
			$message = $rows . " Line ADDED to Information Menu Bar";
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
		/*$position_to_add = $_POST['POSITION_TO_ADD'];
		$name_to_add = $_POST['NAME_TO_ADD'];
		$link_to_add =$_POST['LINK_TO_ADD'];
		$edit_to_add = $_POST['EDIT_TO_ADD'];
		$enabled_to_add = $_POST['ENABLED_TO_ADD'];*/
	}
}
if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Information Bar";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;


include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Info Pages Set Up and Menu Bar</h1>
    <?php
    if($preferences->PREF_TOOL_TIPS == "Y"){
		echo "<div class=\"pos-tool-info1\"><a href=\"#\" class=\"tt\"><img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is where you set up all your information pages; the content is entered and edited in the Info Pages Content area in the left menu<br /><br />
		Info pages  need to be set up here but they don't need to be added to the menu bar; you can create an info page and then add links from other pages as required<br />	<br />Put your mouse on any of the blue text for an explanation of what an item is and how to use it</span><span class=\"bottom\"></span></span></a></div>";

		echo "<div class=\"pos-tool-link-position\"><a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the menu items appear from left to right or top to bottom<br /><br />Lower numbers display nearer the left or top</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a></div>";
		
		echo "<div class=\"pos-tool-link-display\"><a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the name that will appear in the menu</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a></div>";

		echo "<div class=\"pos-tool-link-name\"><a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the name that will appear in the URL (website address for the page) don't use any spaces in the name</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a></div>";

		echo "<div class=\"pos-tool-link-title\"><a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the link to the page; format as follows:<br /><ul> <li>info pages: /\"Page-Name\".htm</li><li>default pages, eg cart: \"cart\"</li><li>URLs: http://www.\"URL.com\" </li></ul><br />There should be no spaces in the link and for info pages the Page Name and final part of the link need to match exactly - it is case sensitive<br /><br />Links to external URLs must include the http:// at the beginning <br />eg http://www.1-ecommerce.com</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a></div>";

		echo "<div class=\"pos-tool-link-edit\"><a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Format Y or N <br /><br />Y makes the page content editable from Info Pages Content in the left menu; N disables editing<br /><br />Note that Links to external URLs should be set to N</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a></div>";
						
		echo "<div class=\"pos-tool-link-enable\"><a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Format Y or N <br /><br />Y includes the page in the menu; N creates the page but it doesn't display in the menu</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a></div>";
						
				}else{
						echo "";
				}		
    ?>

    <table align="left" border="0" cellpadding="2" cellspacing="5" >
    <form name="enter-thumb" action="/_cms/amend_information.php" method="post">
    	
        <?php
            $information = getAllInformation("All", "All");
			$cntr1 = 0; $last_number = 0; $number_enabled = 0; $number_characters = 0;
            foreach($information as $i){
				$cntr1 ++;
                echo "<tr>";
                    echo "<td>";
						echo "<div class=\"form-input\"><label>Position</label><input type=\"text\" name=\"POSITION" . $cntr1 . "\" SIZE=\"1\" value=\"" . $i->IN_POSITION . "\" class=\"box-position\"></div>";
					echo "</td>";
                    echo "<td>";
                        echo "<div class=\"form-input\"><label>Display Name</label><input type=\"text\" name=\"NAME" . $cntr1 . "\" SIZE=\"25\" value=\"" . $i->IN_NAME . "\" class=\"box-name\"></div>";
						echo "<div class=\"form-input\"><label>Link</label><input type=\"text\" name=\"LINK" . $cntr1 . "\" SIZE=\"40\" value=\"" . $i->IN_LINK . "\" class=\"box-link\"></div>";
						echo "<div class=\"form-input\"><label>Edit</label><input type=\"text\" name=\"EDIT" . $cntr1 . "\" SIZE=\"1\" value=\"" . $i->IN_EDIT . "\" class=\"box-edit\"></div>";
						echo "<div class=\"form-input\"><label>Menu</label><input type=\"text\" name=\"ENABLED" . $cntr1 . "\" SIZE=\"1\" value=\"" . $i->IN_ENABLED . "\" class=\"box-enabled\"></div>";
					echo "</td>";
					echo "<td>";
						
						echo "<div class=\"delete-position\"><input type=\"submit\" name=\"UPDATE_INFO" . $cntr1 . "\" value=\"Update\" class=\"update-button\" >";
						echo "<input type=\"submit\" name=\"DELETE_INFO" . $cntr1 . "\" value=\"Delete\" class=\"delete-button\" ></div>";
                    echo "</td>";
                echo "</tr>";
				echo "<tr>";
					echo "<td></td>";
					echo "<td>";
                        echo "<div class=\"form-input\"><label>Page Name</label><input type=\"text\" name=\"PAGE" . $cntr1 . "\" SIZE=\"25\" value=\"" . $i->IN_PAGE . "\" class=\"box-name\"></div>";
						echo "<input type=\"hidden\" name=\"PAGE_ORIGINAL" . $cntr1 . "\" value=\"" . $i->IN_PAGE . "\" >&nbsp;&nbsp;";
					echo "</td>";
					echo "<td></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td class=\"td-sep\" colspan=\"3\"></td>";
				echo "</tr>";
				if($i->IN_ENABLED == "Y"){
					$number_enabled = $number_enabled + 1;
					$number_characters = $number_characters + strlen($i->IN_NAME);
				}
				$last_number = $i->IN_POSITION;
            }
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<input name=\"INFO_COUNTER\" type=\"hidden\" value=\"" . $cntr1 . "\" />";
					echo "<input name=\"NUMBER_ENABLED\" type=\"hidden\" value=\"" . $number_enabled . "\" />";
					echo "<input name=\"NUMBER_CHARACTERS\" type=\"hidden\" value=\"" . $number_characters . "\" />";
					//echo "<input type=\"submit\" name=\"UPDATE_OPTIONS\" value=\"Update\">";
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";		
	        echo "<tr>";
                echo "<td class=\"td-spacer50\" colspan=\"3\"></td>";
            echo "</tr>";
			//---ADD OPTION --------------------------------------------------------------------------------------------------------------------------------------->	
			$position_to_add = $last_number + 1;
            echo "<tr>";
                echo "<td></td>";
				echo "<td><b>Add to Menu</b>:</td>";
				echo "<td></td>";
            echo "</tr>";
			echo "<tr>";
                echo "<td>";
                    echo "<div class=\"form-input\"><label>Position</label><input type=\"text\" name=\"POSITION_TO_ADD\" SIZE=\"1\" value=\"" . $position_to_add . "\" class=\"box-position\"></div>";
				echo "</td>";
				echo "<td>";
				    echo "<div class=\"form-input\"><label>Display Name</label><input type=\"text\" name=\"NAME_TO_ADD\" SIZE=\"25\" value=\"" . $name_to_add . "\" class=\"box-name\"></div>";
					echo "<div class=\"form-input\"><label>Link</label><input type=\"text\" name=\"LINK_TO_ADD\" SIZE=\"40\" value=\"" . $link_to_add . "\" class=\"box-link\"></div>";
					echo "<div class=\"form-input\"><label>Edit</label><input type=\"text\" name=\"EDIT_TO_ADD\" SIZE=\"1\" value=\"" . $edit_to_add . "\" class=\"box-edit\"></div>";
					echo "<div class=\"form-input\"><label>Menu</label><input type=\"text\" name=\"ENABLED_TO_ADD\" SIZE=\"1\" value=\"" . $enabled_to_add . "\" class=\"box-enabled\"></div>";
				echo "</td>";
				echo "<td>";	
					echo "<div class=\"delete-position\"><input name=\"ADD_INFO\" type=\"submit\" value=\"Add\" class=\"add-button\" /></div>";
                echo "</td>";
            echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
                      echo "<div class=\"form-input\"><label>Page Name</label><input type=\"text\" name=\"PAGE_TO_ADD\" SIZE=\"25\" value=\"" . $page_to_add . "\" class=\"box-name\"></div>";
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";		             
        ?>
        
        <tr>
			<td colspan="3">&nbsp;</td>
		</tr>
        <tr>
			<td colspan="3"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>        

	</form>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>

