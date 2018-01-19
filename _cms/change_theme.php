<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$preferences = getPreferences();
$theme = $preferences->PREF_THEME;
if(isset($_GET['theme'])){$theme = $_GET['theme'];}
$message_select = ""; $message_install = ""; $rows_written = 0; $rows_rewritten = 0;
$pathName = "/images/";
$scrolltobottom = "";
//print('<pre>');
//print_r($_POST);
//print('</pre>');

if (isset($_POST['MAKE_CURRENT'])) {
	$theme_selected = $_POST['theme_selected'];
	//delete existing current theme folder
	$dirname = "../theme/";
	$delete = delete_contents($dirname, "both");
	//copy chosen theme from theme library to new current theme folder
	$source = "../theme_library/" . $theme_selected;
	$destination = "../theme/";
	$copy = copy_directory($source, $destination);
	//update preferences table
	$rows = Update_Preferences($preferences->PREF_SHOP_ID, "PREF_THEME", $theme_selected);
	if ($rows == 1){
		$message_select = "Theme successfully UPDATED";
		$warning = "green";
		$theme = $theme_selected;
	}
	if ($rows == 0){
		$message_select = "WARNING ! ! ! - NO RECORDS UPDATED";
		$warning = "orange";
		$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
	}
	if ($rows > 1){
		$message_select = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
		$warning = "red";
		$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null ) {$message_select .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";}
}

if (isset($_GET['deltheme'])) {
	//delete theme
	$dirname = "../theme_library/" . $_GET['deltheme'];
	$delete = delete_directory($dirname);
		
}

if (isset($_POST['INSTALL_THEME'])) {
	//check file is a zip file
	$pos = strpos(basename($_FILES['FILE_ZIP']['name']), ".");
	if (substr(basename($_FILES['FILE_ZIP']['name']), $pos, 4) != ".zip"){
		$message_install = "Incorrect File Extension - Theme File is not a .zip file";
		$warning = "red";
		$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
	}else{
		//clear out any existing content in the zip folder
		$clear = delete_contents("zip/unzipped/", "both");
		if($clear == 1){
			$clear = delete_contents("zip/", "files only");
			if($clear == 1){
				$upload_errors = array(
					UPLOAD_ERR_OK => "No Errors",
					UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize",
					UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE",
					UPLOAD_ERR_PARTIAL => "Partial Upload",
					UPLOAD_ERR_NO_FILE => "No File",
					UPLOAD_ERR_NO_TMP_DIR => "No temporary directory",
					UPLOAD_ERR_CANT_WRITE => "Can't write to disk",
					UPLOAD_ERR_EXTENSION => "File upload stopped by extension");
					
				$error = $_FILES['FILE_ZIP']['error'];
				$message_install = $upload_errors[$error] . " - ";
				$warning = "green";
				
				if($message_install == "No Errors - "){
					//echo "checking folders.....";
					$zip_folder = "/_cms/zip/";
					if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $zip_folder)){
						$message_install = "Upload folder (" . $_SERVER['DOCUMENT_ROOT'] . $zip_folder . ") does NOT exist!!!" . "<br/>";
						$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
					}else{
						//echo "uploading file.....";
						$tmp_name_zip = $_FILES['FILE_ZIP']['tmp_name'];
						$target_file_zip = basename($_FILES['FILE_ZIP']['name']);
						$upload_file_t_zip = "zip/" . $target_file_zip;
						$message_install = Upload_File($tmp_name_zip, $upload_file_t_zip);
						$message_install .= " - " . $upload_errors[$error];
						
						//echo "unzipping file.....";
						$fileName = $_FILES['FILE_ZIP']['name'];
						//echo "FILE TO UNZIP = " . $fileName;
						$zip = new ZipArchive;
						$zip_fullPath = "zip/" . $fileName;
						$res = $zip->open($zip_fullPath);
						if ($res === TRUE) {
							$zip->extractTo('zip/unzipped/');
							$zip->close();
							//echo "installing theme.....";
							//copy unzipped theme from unzipped to theme library folder
							$source = "zip/unzipped/";
							$destination = "../theme_library/";
							$copy = copy_directory($source, $destination);
							
							//installation successful so tidy-up and delete the zip files
							$clear = delete_contents("zip/unzipped/", "both");
							if($clear == 1){
								$clear = delete_contents("zip/", "files only");
								if($clear == 1){
									$message_install .= " - Installation Successful";
									$warning = "green";
								}else{
									$message_install .= "FAILURE to cleardown contents of zip folder - please contact Shopfitter";
									$warning = "red";
									$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
								}
							}else{
								$message_install .= "FAILURE to cleardown contents of unzipped folder - please contact Shopfitter";
								$warning = "red";
								$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
							}	
						} else {
							$message_install .= "EXTRACTION FAILURE - unable to open .zip file (" . $zip_fullPath . ")";
							$warning = "red";
							$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
							//echo 'failed';
						}
					}
				}
			}else{
				$message_install .= "FAILURE to delete contents of zip folder - please contact Shopfitter";
				$warning = "red";
				$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
			}
		}else{
			$message_install .= "FAILURE to delete contents of unzipped folder - please contact Shopfitter";
			$warning = "red";
			$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
		}
	}
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Change Theme";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Change Template - Change current Template</h1>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="amend_text" action="/_cms/change_theme.php" method="post">
    	<tr>
        	<td colspan="4">
            	<span class="right-link"><a href="http://www.1-ecommerce.com/templates/" target="_blank">Download new theme templates</a></span>
                <span class="q-right-theme"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Selecting a different template will change the design of your website; you can revert to the original or choose another template whenever you wish<br /><br />Uninstall a template from the gallery by clicking the Delete button corresponding to the unwanted template<br /><br />You can get more theme templates from www.1-ecommerce.com/templates</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                <h2>Select Template</h2>
          		</td>
        </tr>
    	<tr>
        	<td colspan="4">&nbsp;</td>
        </tr>
    	<tr>
            <td><span style="float: right; position: relative; right: 28px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the template you want by clicking one of the buttons below; then click the Update button at the bottom of the list</span><span class=\"bottom\"></span></span>" : "") ?></a></span><h3>Selected</h3></td>
            <td><span style="float: right; position: relative; right: 290px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The title of each template</span><span class=\"bottom\"></span></span>" : "") ?></a></span><h3>Name</h3></td>
            <td colspan="2"><span style="float: right; position: relative; right: 230px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Sample images of templates installed</span><span class=\"bottom\"></span></span>" : "") ?></a></span><h3>Screenshot</h3></td>
        </tr>
    	<?php
		//scan down theme_library and display one line against each theme
		$cntr1 = 0;
		$dir = opendir('../theme_library/');
		while($read = readdir($dir)){
			if ($read !="." && $read != ".."){
				$cntr1 ++;
				$checked = "checked";
				echo "<tr>";
					echo "<td>";
						echo "<input type=\"radio\" name=\"theme_selected\" value=\"" . $read ."\" " . ($theme == $read ? "checked" : "") . " \">";
					echo "</td>\r\n";
					echo "<td>";
						echo "<input type=\"text\" name=\"THEME_NAME" . $cntr1 . "\" value=\"" . $read . "\" class=\"theme_name_input\" disabled />";
					echo "</td>\r\n";
					echo "<td>";
						echo "<div class=\"p-display-picture\">";
							echo "<img name=\"IMAGE" . $cntr1 . "\" src=\"../theme_library/" . $read . "/theme-images/screenshot.jpg\" width=\"200\" alt=\"" . $read .  " Screenshot\" />";	
						echo "</div>";
					echo "</td>\r\n";
					echo "<td>";
							echo "<div class=\"delete-button\"><a href=\"/_cms/change_theme.php?deltheme=" . $read . "\">";
							echo "<span>Delete</span></a></div>";
														
					echo "</td>\r\n";
				echo "</tr>";	
			}
		}
		
		?>           
		<!--- UPDATE BUTTON ---------->
		<tr>
			<td>
            	<input name="MAKE_CURRENT" type="submit" value="Update" class="update-button"> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">When you've selected the template you want click this Update button</span><span class=\"bottom\"></span></span>" : "") ?></a>            </td>
		</tr>
        <tr>
			<td colspan="2">
            	<label class="<?php echo $warning ?>" ><?php echo $message_select ?></label>
                <input type="hidden" name="COUNTER" value="<?php echo $cntr1 ?>"  />
            </td>
		</tr>
        <tr>
        	<td colspan="2">&nbsp;</td>
        </tr>
	</form>
    <form name="enter-ZIP" action="/_cms/change_theme.php" method="post" enctype="multipart/form-data">
    	<tr>
        	<td class="td-sep" colspan="4">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="4">
            	<span style="float: right; position: relative; right: 424px;"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Install a new template by first downloading one from the 1-ecommerce website<br /><br />Next, click the Browse button to the right and navigate to the template file you've downloaded<br /><br />Finally, click the Install button below<br /><br />The newly installed template will be available for selection from the gallery</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
            	<h2>Install New Template</h2>
            </td>
        </tr>
    	<tr>
        	<td colspan="4">
            	<label for="FILE_ZIP">Template File name (.zip):&nbsp;&nbsp; </label>
                <Input name="FILE_ZIP" type="file" size="60" />
            </td>
        </tr>
        <tr>
           	<td colspan="4">
                <Input name="INSTALL_THEME" type="submit" value="Install" class="install-button" /><br/><br/>
        	</td>
        </tr>
        <tr>
   			<td colspan="4">
        		<Label class="<?php echo $warning ?>" ><?php echo $message_install ?></Label>
            </td>
        </tr>
	</form>
    	<tr>
        	<td colspan="4"><span class="right-link"><a href="http://www.1-ecommerce.com/templates/" target="_blank">Download new theme templates</a></span></td>
        </tr>
        <tr>
        	<td class="td-spacer150" colspan="4">&nbsp;</td>
        </tr>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>

