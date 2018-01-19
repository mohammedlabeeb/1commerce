<?php
include_once("includes/session.php");
//confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$warning = "";
$pathName = "/images/";
$lastselected = "";
$lastselectedcategory = "";
$infopage_current = "";
$scrolltobottom = "";

if(isset($_GET['page'])){
	$infopage = getInformationPage(urldecode($_GET['page']));
	$infopage_current = $infopage->IN_PAGE;
}
if(isset($_POST['INFOPAGE_CURRENT'])){
	$infopage = getInformationPage($_POST['INFOPAGE_CURRENT']);
	$infopage_current = $infopage->IN_PAGE;
}

if (isset($_POST['UPDATE'])) {
	//$message = "INFOPAGE_CURRENT=" . $_POST['INFOPAGE_CURRENT'];
	$infopage = getInformationPage($_POST['INFOPAGE_CURRENT']);
	//$message .= "IN_NAME=" . $infopage->IN_NAME;
	if (count($infopage) != 1){
		$message = "PAGE NOT FOUND!!!";
		$warning = "red";
	}
	$fields = array("in_page"=>html_entity_decode($infopage->IN_PAGE, ENT_QUOTES), "page_original"=>html_entity_decode($infopage->IN_PAGE, ENT_QUOTES), "in_name"=>html_entity_decode($infopage->IN_NAME, ENT_QUOTES), "in_position"=>$infopage->IN_POSITION,"in_link"=>$infopage->IN_LINK, 
					"in_edit"=>$infopage->IN_EDIT, "in_title"=>$_POST['TITLE'], "in_meta_desc"=>$_POST['META_DESC'], "in_meta_keywords"=>$_POST['META_KEYWORDS'],
					"in_custom_head"=>$_POST['CUSTOM_HEAD'], "in_enabled"=>$infopage->IN_ENABLED, "in_data"=>$_POST['INFODATA']);	
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
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
	//refresh screen with latest details
	$infopage = getInformationPage($_POST['INFOPAGE_CURRENT']);
	$infopage_current = $infopage->IN_PAGE;
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Information Pages";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Information Pages - Amend Info Page Content</h1>
	<p><br />
    <?php
    if($preferences->PREF_TOOL_TIPS == "Y"){
    	echo "<a href=\"#\" class=\"tt\"><img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Edit your information pages here; start by selecting the page you want to work on from the dropdown menu below<br /><br />
			  Info pages need to be set up on the Info Pages Setup before you can enter any content<br />	<br />Remember to save your changes by clicking the Update button at the bottom when you're finished</span><span class=\"bottom\"></span></span></a></p>";
	}
	?>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="amend_text" action="/_cms/amend_infopage.php" enctype="multipart/form-data" method="post">
		<?php
		//--- INFORMATION PAGE SELECTION ----------------------------------------------------------------------------------------------------------->
		$enabled = "All"; $edit = "Y";
        $information = getAllInformation($enabled, $edit);
        if(count($information)){
        	echo "<tr>";
				echo "<td>";
					echo "Info Page: ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the page you want to edit</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td>";
                    echo "<select name=\"information\" id=\"jumpMenu\" onchange=\"MM_jumpMenu('parent',this,1)\" class=\"box-select\">";
                        echo "<option value=\"#\">Choose from...</option>";
                        foreach($information as $i){
							if($infopage_current == $i->IN_PAGE){$selected = "selected";}else{$selected = "";}
                            echo "<option value=\"/_cms/amend_infopage.php?page=" . urlencode($i->IN_PAGE) . "\" " . $selected . ">" . $i->IN_NAME . "</option>";
                        }
                    echo "</select>";
                 echo "</td>";
            echo "</tr>";
        }
		?>
        <?php
        if($infopage_current != ""){
			echo "<tr>";
				echo "<td>";
					echo "Title Tag: "; 
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the main search terms for this page; these should be specific to the page and are critical for SEO. <br /><br />Note that the Title tag (as it's known) is the most important place to include key words and phrases so that search engines pick them up.<br /><br />Approx 60 characters max, including spaces</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td>";
					echo "<input type=\"text\" name=\"TITLE\" SIZE=\"50\" value=\"" . $infopage->IN_TITLE . "\" class=\"box-select\">";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
				echo "Meta Description: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a description of the content of this page as a properly formed sentence; each page should have a unique description to ensure the best search engine rankings <br /><br />Note that this description is used by search engines in their listings to describe the content of the pages they are listing so think of it as a way to convert searchers into visitors; remember to include key words and phrases so that search engines can match them to search requests by people<br /><br />Approx 150 to 200 characters max, including spaces</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td>";
					echo "<input type=\"text\" name=\"META_DESC\" SIZE=\"99\" value=\"" . $infopage->IN_META_DESC . "\" class=\"box-select\">";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
				echo "Meta Keywords: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a list of key words specific to this page <br /><br />Include 3 or 4 words or phrases from your meta title and meta description first; it's also a good place to put mis-spellings relevant to thie page content<br /><br />256 characters max, including spaces</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
				echo "<td>";
					echo "<input type=\"text\" name=\"META_KEYWORDS\" SIZE=\"99\" value=\"" . $infopage->IN_META_KEYWORDS . "\" class=\"box-select\">";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
				echo "Custom Head: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Put code, scripts and microdata that needs be added into the \"head\" area of this page.<br /><br />Note that anything added to this area will only appear on this page</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "<td>";
					echo "<textarea type=\"text\" name=\"CUSTOM_HEAD\" class=\"p-amendhead-textarea\">" . $infopage->IN_CUSTOM_HEAD . "</textarea>";
				echo "</td>";
        	echo "</tr>";
			echo "<tr>";
			echo "</tr>";
			echo "<tr>";
			echo "</tr>";
			echo "<tr>";
			echo "</tr>";
			echo "<tr>";
				echo "<td valign=\"top\">";
					
				echo "<div class=\"edit-button\">";
				echo "<a href=\"javascript:void(0);\" NAME=\"My Window Name\" title=\" Edit Page Content \" onClick=window.open(\"/_cms/edit_textarea.php?form=amend_text&field=INFODATA\",\"Ratting\",\"width=1000,height=800,left=150,top=200,toolbar=1,status=1,\");>";
				echo "<span>Edit</span></a></div>";
					
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours<br /><br />When you've submitted the changes in the pop-up editor remember to save your work by clicking the Update button at the bottom</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "<td><textarea type=\"text\" id=\"INFODATA\" name=\"INFODATA\" class=\"p-amendinfo-textarea\">" . html_entity_decode($infopage->IN_DATA, ENT_QUOTES) . "</textarea></td>";
				echo "<td>";
					echo "<input type=\"hidden\" name=\"INFOPAGE_CURRENT\" value=\"" . htmlentities($infopage_current, ENT_QUOTES) . "\" >";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					/*echo "<a href=\"javascript:void(0);\" NAME=\"My Window Name\" title=\" My title here \" onClick=window.open(\"/_cms/edit_textarea.php?form=amend_text&field=INFODATA\",\"Ratting\",\"width=1000,height=800,left=150,top=200,toolbar=1,status=1,\");>";
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
					echo "<input name=\"UPDATE\" type=\"submit\" value=\"Update Information Page\" class=\"update-button\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					
					
					
					
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
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>
