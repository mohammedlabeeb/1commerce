<?php
include_once("includes/session.php");
//confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$warning = "";
$pathName = "/images/";
$lastselected = "";
$lastselectedcategory = "";
$area_current = "";
$scrolltobottom = "";

if(isset($_GET['area'])){
	$areadata = getAreadataPage(urldecode($_GET['area']));
	$area_current = $areadata->AR_AREA;
}
if(isset($_POST['AREA_CURRENT'])){
	$areadata = getAreadataPage($_POST['AREA_CURRENT']);
	$area_current = $areadata->AR_AREA;
}

if (isset($_POST['UPDATE'])) {
	$areadata = getAreadataPage($_POST['AREA_CURRENT']);
	if (count($areadata) != 1){
		$message = "PAGE AREA NOT FOUND!!!";
		$warning = "red";
	}
	$fields = array("ar_area"=>htmlentities($areadata->AR_AREA, ENT_QUOTES), "ar_data"=>$_POST['AREADATA']);	
	$rows = Rewrite_Areadata($fields);
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
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
	//refresh screen with latest details
	$areadata = getAreadataPage($_POST['AREA_CURRENT']);
	$area_current = $areadata->AR_AREA;
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Edit Header / Footer";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Edit Header / Footer / Sidebar Content</h1>
	<p><br />
    <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the area you wish to alter from the drop down menu<br /><br />Add your logo and telephone number, for example, to the header<br /><br />Footer content can include your full address, copyright and disclaimers; and links to privacy and other pages you need to include but wish to de-emphasise<br /><br />The sidebar adds content underneath the vertical menu (left or right; template dependent)<br /><br />To add formatting such as bold, text colour or bullet lists click the edit button: remember to save your changes by clicking the Update button at the bottom</span><span class=\"bottom\"></span></span>" : "") ?></a></p>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="amend_text" action="/_cms/amend_areadata.php" method="post">
		<?php
		//--- AREA SELECTION ----------------------------------------------------------------------------------------------------------->
		$enabled = "All"; $edit = "Y";
        $area = getAllAreadata();
        if(count($area)){
        	echo "<tr>";
				echo "<td>Page Area:</td>";
                echo "<td>";
                    echo "<select name=\"area\" id=\"jumpMenu\" onchange=\"MM_jumpMenu('parent',this,1)\" class=\"box-name\">";
                        echo "<option value=\"#\">Choose from...</option>";
                        foreach($area as $a){
							if($area_current == $a->AR_AREA){$selected = "selected";}else{$selected = "";}
                            echo "<option value=\"/_cms/amend_areadata.php?area=" . urlencode($a->AR_AREA) . "\" " . $selected . ">" . $a->AR_AREA . "</option>";
                        }
                    echo "</select>";
                 echo "</td>";
            echo "</tr>";
        }
		?>
        <?php
        if($area_current != ""){
			echo "<tr>";
			echo "</tr>";
			echo "<tr>";
				echo "<td valign=\"top\">";
									
				echo "<div class=\"edit-button\">";
				echo "<a href=\"javascript:void(0);\" NAME=\"My Window Name\" title=\" Click this button to open the page editor \" onClick=window.open(\"/_cms/edit_textarea.php?form=amend_text&field=AREADATA\",\"Ratting\",\"width=1000,height=800,left=150,top=200,toolbar=1,status=1,\");>";
				echo "<span>Edit</span></a></div>";
									
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours<br /><br />When you've submitted the changes in the pop-up editor remember to save your work by clicking the Update button at the bottom</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td><textarea type=\"text\" id=\"AREADATA\" name=\"AREADATA\" class=\"p-amendarea-textarea\">" . html_entity_decode($areadata->AR_DATA, ENT_QUOTES) . "</textarea></td>";
				echo "<td>";
					echo "<input type=\"hidden\" name=\"AREA_CURRENT\" value=\"" . htmlentities($area_current, ENT_QUOTES) . "\" >";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					/*echo "<a href=\"javascript:void(0);\" NAME=\"My Window Name\" title=\" Click this button to open the page editor here \" onClick=window.open(\"/_cms/edit_textarea.php?form=amend_text&field=INFODATA\",\"Ratting\",\"width=1000,height=800,left=150,top=200,toolbar=1,status=1,\");>";
						echo "<img src=\"/_cms/_assets/images/edit_button.jpg\" alt=\"Edit Page Text\"";
						echo "onmouseover=\"this.src='/_cms/_assets/images/edit_button_hover.jpg'\"";
						echo "onmouseout=\"this.src='/_cms/_assets/images/edit_button.jpg'\" align=\"absmiddle\" />";
					echo "</a>";*/
				echo "</td>";
			echo "</tr>";
			//-- UPDATE BUTTON ----------------------------------------
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "<input name=\"UPDATE\" type=\"submit\" class=\"update-button\" value=\"Update " . $area_current  . " content\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "</td>";
			echo "</tr>";
			//-- MESSAGES ---------------------------------------------
			echo "<tr>";
				echo "<td></td>";
				echo "<td colspan=\"2\"><label class=\"" . $warning . "\" >" . $message . "</label></td>";
			echo "</tr>";
        }       
		?>
	</form>
    <tr>
    	<td>&nbsp;</td>
    <tr>
    <tr>
    	<td>&nbsp;</td>
    <tr>
    <tr>
    	<td>&nbsp;</td>
    <tr>
    <tr>
    	<td>&nbsp;</td>
    <tr>
    <tr>
    	<td>&nbsp;</td>
    <tr>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>

