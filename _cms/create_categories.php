<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$message_upload = "";
$target_file = "";
$pathName = "/images/";
$scrolltobottom = "";

//initialise screen fields
$selected_category = "";
$categorycode = "";
$name = "";
$description = "";
$parent = ""; $tree_node = ""; $display = "Y"; $menu_posn = 999;
$attribute1 = ""; $attribute2 = ""; $attribute3 = ""; $attribute4 = ""; $attribute5 = ""; $attribute6 = ""; $attribute7 = ""; $attribute8 = "";
$date_added = ""; $tabular_listing = "N";
$meta_title = ""; $meta_desc = ""; $meta_keywords = ""; $custom_head = ""; $div_wrap = ""; $ca_class = "";
$top_content = ""; $bottom_content = "";
$image_name = "no-image.jpg"; $image_folder = ""; $image_alt = "no image";

if(!isset($_POST['CATEGORY_CREATED'])){$category_created = 0;}else{$category_created = $_POST['CATEGORY_CREATED'];}
if (isset($_POST['DELETE'])) {
	if($category_created == 1){
		//Delete the row just created - NOTE the preferences file will NOT be adjusted and the previous seed will be lost
		$rows = Delete_Category($_POST['CATEGORY'], "ALL");
		if ($rows == 1){
			$message = $rows . " Category record successfully DELETED";
			$warning = "green";
			$category_created = 0;
			//initialise all fields ready for new record
			$next_code = getNextCode("category");
			//initialise screen fields
			$selected_category = "";
			$categorycode = "";
			$name = "";
			$description = "";
			$parent = ""; $tree_node = ""; $display = "Y"; $menu_posn = 999;
			$date_added = ""; $TABULAR_LISTING = "N";
			$attribute1 = ""; $attribute2 = ""; $attribute3 = ""; $attribute4 = ""; $attribute5 = ""; $attribute6 = ""; $attribute7 = ""; $attribute8 = "";
			$meta_title = ""; $meta_desc = ""; $meta_keywords = ""; $custom_head = ""; $div_wrap = ""; $ca_class = "";
			$top_content = ""; $bottom_content = "";
			$image_name = "no-image.jpg"; $image_folder = ""; $image_alt = "no image";
			if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
		}else{
			$message .= "CATEGORY record NOT DELETED - PLEASE CONTACT SHOPFITTER!!!"; 
			$warning = "red";
		}
	}else{
		$message .= "Cannot delete a record that hasn't been created yet !!!"; 
		$warning = "red";
		//refresh page
		$image_name = $_POST['IMAGE_NAME'];
		$image_folder = $_POST['IMAGE_FOLDER'];
		$image_alt = $_POST['IMAGE_ALT'];
		$categorycode = $_POST['CATEGORY'];
		$name = $_POST['NAME'];
		$description = $_POST['DESCRIPTION'];
		$parent = $_POST['PARENT'];
		$tree_node = $_POST['TREE_NODE'];
		$display = $_POST['DISPLAY'];
		$attribute1 = $_POST['ATTRIBUTE1'];
		$attribute2 = $_POST['ATTRIBUTE2'];
		$attribute3 = $_POST['ATTRIBUTE3'];
		$attribute4 = $_POST['ATTRIBUTE4'];
		$attribute5 = $_POST['ATTRIBUTE5'];
		$attribute6 = $_POST['ATTRIBUTE6'];
		$attribute7 = $_POST['ATTRIBUTE7'];
		$attribute8 = $_POST['ATTRIBUTE8'];
		if (isset($_POST['TABULAR_LISTING']) and $_POST['TABULAR_LISTING'] == 1){$tabular_listing = "Y";}else{$tabular_listing = "N";}
		$top_content = $_POST['TOP_CONTENT'];
		$bottom_content = $_POST['BOTTOM_CONTENT'];
		$menu_posn = $_POST['MENU_POSN'];
		$meta_title = $_POST['META_TITLE'];
		$meta_desc = $_POST['META_DESC'];
		$meta_keywords = $_POST['META_KEYWORDS'];
		$custom_head = $_POST['CUSTOM_HEAD'];
		$div_wrap = $_POST['DIV_WRAP'];
		$ca_class = $_POST['CA_CLASS'];
	}
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
}

if (isset($_POST['NEXT'])) {
	//initialise screen fields
	$selected_category = "";
	$categorycode = "";
	$name = "";
	$description = "";
	$parent = ""; $tree_node = ""; $display = "Y"; $menu_posn = 999;
	$date_added = ""; $tabular_listing = "N";
	$attribute1 = ""; $attribute2 = ""; $attribute3 = ""; $attribute4 = ""; $attribute5 = ""; $attribute6 = ""; $attribute7 = ""; $attribute8 = "";
	$top_content = ""; $bottom_content = "";
	$meta_title = ""; $meta_desc = ""; $meta_keywords = ""; $custom_head = ""; $div_wrap = ""; $ca_class = "";
	$image_name = "no-image.jpg"; $image_folder = ""; $image_alt = "no image";
	$category_created = 0;
}

if (isset($_POST['RESTORE_IMAGE'])) {

	$image_name = "no-image.jpg";
	$image_folder = "";
	$image_alt = "no image";
	//now refresh the category settings
	$categorycode = $_POST['CATEGORY'];
	$name = $_POST['NAME'];
	$description = $_POST['DESCRIPTION'];
	$parent = $_POST['PARENT'];
	$tree_node = $_POST['TREE_NODE'];
	$display = $_POST['DISPLAY'];
	$menu_posn = $_POST['MENU_POSN'];
	$attribute1 = $_POST['ATTRIBUTE1'];
	$attribute2 = $_POST['ATTRIBUTE2'];
	$attribute3 = $_POST['ATTRIBUTE3'];
	$attribute4 = $_POST['ATTRIBUTE4'];
	$attribute5 = $_POST['ATTRIBUTE5'];
	$attribute6 = $_POST['ATTRIBUTE6'];
	$attribute7 = $_POST['ATTRIBUTE7'];
	$attribute8 = $_POST['ATTRIBUTE8'];
	if (isset($_POST['TABULAR_LISTING']) and $_POST['TABULAR_LISTING'] == 1){$tabular_listing = "Y";}else{$tabular_listing = "N";}
	$top_content = $_POST['TOP_CONTENT'];
	$bottom_content = $_POST['BOTTOM_CONTENT'];
	$meta_title = $_POST['META_TITLE'];
	$meta_desc = $_POST['META_DESC'];
	$meta_keywords = $_POST['META_KEYWORDS'];
	$custom_head = $_POST['CUSTOM_HEAD'];
	$div_wrap = $_POST['DIV_WRAP'];
	$ca_class = $_POST['CA_CLASS'];
}

if (isset($_POST['UPLOAD_IMAGE'])) {
	//first thing to do is to re-extract the image_folder which may well have been changed via the input field FULL_PATH.
	//NOTE this is the only way the user may amend the IMAGE_FOLDER since by definition he must be changing the image at
	//the same time otherwise the link won't work anymore.
	//check image folder exists before proceeding
	$image_folder = "";
	$OK = 1;
	$image_folder = substr($_POST['FULL_PATH'], strlen($pathName));
	if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['FULL_PATH'])){
		$message_upload = "Upload folder does NOT exist!!!" . "<br/>";
		$warning = "red";
		$OK = 0;	
	}
	//echo "<pre>";
	//print_r($_FILES['FILE_UPLOAD']);
	//echo "</pre>";
	//echo "<hr/>";
	if($OK == 1){
		$upload_errors = array(
			UPLOAD_ERR_OK => "No Errors",
			UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize",
			UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE",
			UPLOAD_ERR_PARTIAL => "Partial Upload",
			UPLOAD_ERR_NO_FILE => "No File",
			UPLOAD_ERR_NO_TMP_DIR => "No temporary directory",
			UPLOAD_ERR_CANT_WRITE => "Can't write to disk",
			UPLOAD_ERR_EXTENSION => "File upload stopped by extension");
	
		$error = $_FILES['FILE_UPLOAD']['error'];
		$message_upload = $upload_errors[$error] . " - ";
		if ($error == 0){$warning = "green";} else {$warning = "red";}
		//Upload file
		$tmp_name = $_FILES['FILE_UPLOAD']['tmp_name'];
		$target_file = basename($_FILES['FILE_UPLOAD']['name']);
		$upload_file_t = "../images/" . (strlen($image_folder) > 0 ? $image_folder . "/" : "")  . $target_file;
		$message_upload = Upload_File($tmp_name, $upload_file_t);
		$message_upload .= " - " . $upload_errors[$error];
	}
	//refresh page
	$image_name = $_POST['IMAGE_NAME'];
	$image_folder = $_POST['IMAGE_FOLDER'];
	$image_alt = $_POST['IMAGE_ALT'];
	$categorycode = $_POST['CATEGORY'];
	$name = $_POST['NAME'];
	$description = $_POST['DESCRIPTION'];
	$parent = $_POST['PARENT'];
	$tree_node = $_POST['TREE_NODE'];
	$display = $_POST['DISPLAY'];
	$menu_posn = $_POST['MENU_POSN'];
	$attribute1 = $_POST['ATTRIBUTE1'];
	$attribute2 = $_POST['ATTRIBUTE2'];
	$attribute3 = $_POST['ATTRIBUTE3'];
	$attribute4 = $_POST['ATTRIBUTE4'];
	$attribute5 = $_POST['ATTRIBUTE5'];
	$attribute6 = $_POST['ATTRIBUTE6'];
	$attribute7 = $_POST['ATTRIBUTE7'];
	$attribute8 = $_POST['ATTRIBUTE8'];
	if (isset($_POST['TABULAR_LISTING']) and $_POST['TABULAR_LISTING'] == 1){$tabular_listing = "Y";}else{$tabular_listing = "N";}
	$top_content = $_POST['TOP_CONTENT'];
	$bottom_content = $_POST['BOTTOM_CONTENT'];
	$meta_title = $_POST['META_TITLE'];
	$meta_desc = $_POST['META_DESC'];
	$meta_keywords = $_POST['META_KEYWORDS'];
	$custom_head = $_POST['CUSTOM_HEAD'];
	$div_wrap = $_POST['DIV_WRAP'];
	$ca_class = $_POST['CA_CLASS'];
}

if (isset($_POST['CREATE'])) {
	if(isset($_POST['CATEGORY_CREATED']) and $_POST['CATEGORY_CREATED'] == 1){
		$message = "Cannot amend a newly created category - Please use Amend Category page";
		$warning = "red";
		//refresh page
		$image_name = $_POST['IMAGE_NAME'];
		$image_folder = $_POST['IMAGE_FOLDER'];
		$image_alt = $_POST['IMAGE_ALT'];
		$categorycode = $_POST['CATEGORY'];
		$name = $_POST['NAME'];
		$description = $_POST['DESCRIPTION'];
		$parent = $_POST['PARENT'];
		$tree_node = $_POST['TREE_NODE'];
		$display = $_POST['DISPLAY'];
		$menu_posn = $_POST['MENU_POSN'];
		$attribute1 = $_POST['ATTRIBUTE1'];
		$attribute2 = $_POST['ATTRIBUTE2'];
		$attribute3 = $_POST['ATTRIBUTE3'];
		$attribute4 = $_POST['ATTRIBUTE4'];
		$attribute5 = $_POST['ATTRIBUTE5'];
		$attribute6 = $_POST['ATTRIBUTE6'];
		$attribute7 = $_POST['ATTRIBUTE7'];
		$attribute8 = $_POST['ATTRIBUTE8'];
		if (isset($_POST['TABULAR_LISTING']) and $_POST['TABULAR_LISTING'] == 1){$tabular_listing = "Y";}else{$tabular_listing = "N";}
		$top_content = $_POST['TOP_CONTENT'];
		$bottom_content = $_POST['BOTTOM_CONTENT'];
		$meta_title = $_POST['META_TITLE'];
		$meta_desc = $_POST['META_DESC'];
		$meta_keywords = $_POST['META_KEYWORDS'];
		$custom_head = $_POST['CUSTOM_HEAD'];
		$div_wrap = $_POST['DIV_WRAP'];
		$ca_class = $_POST['CA_CLASS'];
	}else{
		$message = "";
		//validate all fields first
		if (strlen($_POST['IMAGE_NAME']) == 0 ){
			$message .= "Please enter the Category Name" . "<br/>";
			$warning = "red";
		}
	
		if ($message == ""){
			//no error message so update database categories table
			if(isset($_POST['TABULAR_LISTING']) and $_POST['TABULAR_LISTING'] == 1){$tabular_listing = "Y";}else{$tabular_listing = "N";}	
			$fields = array("ca_code"=>$_POST['CATEGORY'], "ca_name"=>$_POST['NAME'], "ca_description"=>$_POST['DESCRIPTION'],
							"ca_parent"=>$_POST['PARENT'], "ca_tree_node"=>$_POST['TREE_NODE'], "ca_display"=>$_POST['DISPLAY'], "ca_menu_posn"=>$_POST['MENU_POSN'],
							"ca_image"=>$_POST['IMAGE_NAME'], "ca_image_folder"=>$_POST['IMAGE_FOLDER'], "ca_image_alt"=>$_POST['IMAGE_ALT'],
							"ca_attribute1"=>$_POST['ATTRIBUTE1'], "ca_attribute2"=>$_POST['ATTRIBUTE2'],
							"ca_attribute3"=>$_POST['ATTRIBUTE3'], "ca_attribute4"=>$_POST['ATTRIBUTE4'],
							"ca_attribute5"=>$_POST['ATTRIBUTE5'], "ca_attribute6"=>$_POST['ATTRIBUTE6'],
							"ca_attribute7"=>$_POST['ATTRIBUTE7'], "ca_attribute8"=>$_POST['ATTRIBUTE8'], 
							"ca_tabular_listing"=>$tabular_listing,
							"ca_top_content"=>$_POST['TOP_CONTENT'], "ca_bottom_content"=>$_POST['BOTTOM_CONTENT'],
							"ca_meta_title"=>$_POST['META_TITLE'], "ca_meta_desc"=>$_POST['META_DESC'], "ca_meta_keywords"=>$_POST['META_KEYWORDS'],
							"ca_custom_head"=>$_POST['CUSTOM_HEAD'], "ca_div_wrap"=>$_POST['DIV_WRAP'], "ca_class"=>"Standard", "ca_disable"=>"N");
				
			$rows = Create_Category($fields);
			if ($rows == 1){
				$message = $rows . " CATEGORY record successfully UPDATED";
				$warning = "green";
				$category_created = 1;
				//increment seed on preferences
				$rows = incrementSeed($_POST['CATEGORY']);	
				if($rows !=1){$message .= "error writing PREFERENCE Record ({$_POST['CATEGORY']}) - seed NOT Updated - PLEASE CONTACT SHOPFITTER!!!"; $warning = "red";}
			}
			if ($rows == 0){
				$message = "WARNING ! ! ! - NO RECORDS UPDATED!!!";
				$warning = "orange";
			}
			if ($rows > 1){
				$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") CATEGORY RECORD UPDATED - PLEASE CONTACT SHOPFITTER!!!";
				$warning = "red";
			}
			$error = null;
			$error = mysql_error();
			if ($error != null) { 
				$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
				$warning = "red";
			}
		}
		//refresh page with new details
		$category = getCategory($_POST['CATEGORY'], "");
		$selected_category = $category->CA_CODE;
		$categorycode = $category->CA_CODE;
		$name = html_entity_decode($category->CA_NAME);
		$description = html_entity_decode($category->CA_DESCRIPTION);
		$parent = $category->CA_PARENT;
		$tree_node = $category->CA_TREE_NODE;
		$display = $category->CA_DISPLAY;
		$menu_posn = $category->CA_MENU_POSN;
		$attribute1 = $category->CA_ATTRIBUTE1;
		$attribute2 = $category->CA_ATTRIBUTE2;
		$attribute3 = $category->CA_ATTRIBUTE3;
		$attribute4 = $category->CA_ATTRIBUTE4;
		$attribute5 = $category->CA_ATTRIBUTE5;
		$attribute6 = $category->CA_ATTRIBUTE6;
		$attribute7 = $category->CA_ATTRIBUTE7;
		$attribute8 = $category->CA_ATTRIBUTE8;
		$tabular_listing = $category->CA_TABULAR_LISTING;
		$top_content = html_entity_decode($category->CA_TOP_CONTENT);
		$bottom_content = html_entity_decode($category->CA_BOTTOM_CONTENT);
		$meta_title = html_entity_decode($category->CA_META_TITLE);
		$meta_desc = html_entity_decode($category->CA_META_DESC);
		$meta_keywords = html_entity_decode($category->CA_META_KEYWORDS);
		$custom_head = html_entity_decode($category->CA_CUSTOM_HEAD);
		$div_wrap = html_entity_decode($category->CA_DIV_WRAP);
		$ca_class = html_entity_decode($category->CA_CLASS);
		$date_added = $category->CA_DATE_ADDED;
		$image_name = $category->CA_IMAGE;
		$image_folder = $category->CA_IMAGE_FOLDER;
		$image_alt = $category->CA_IMAGE_ALT;
	}
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Create Categories";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;
if($category_created == 0){$next_code = getNextCode("category");}

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
      	<div class="admin">
    <br/>
	<h1>Create Categories</h1>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="enter_thumb" action="/_cms/create_categories.php" enctype="multipart/form-data" method="post">
    	<tr>
          <td class="category-td">New Category Code:</td>
            <td><Input  type="text" name="NEW_CATEGORY_DISABLED" size="15" value="<?php echo isset($_POST['NEW_CATEGORY']) ? $_POST['NEW_CATEGORY'] : $next_code ?>" disabled />
            	<Input type="hidden" name="NEW_CATEGORY" value="<?php echo strlen($categorycode) > 0 ? $categorycode : $next_code ?>" />
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is the ID code automatically assigned by the software to the new category<br /><br />The next code is assigned according to the Category Seed on the Preferences and Settings page</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            
    	</tr>
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
    <!--- MAIN IMAGE HANDLING ---------------------------------------------------------------------------------------------->
		<tr>
        	<td>
            </td>
            <td>
            	<div class="p-display-picture">
				<?php        
                if (isset($_POST['UPLOAD_IMAGE'])) {
					$image_folder = substr($_POST['FULL_PATH'], strlen($pathName));
					$image_name = $target_file;
					$image_alt = $target_file;
                    echo "<img name=\"IMAGE\" src=\"" . $pathName . (strlen($image_folder) > 0 ? $image_folder . "/" : "") . $target_file . "\" width=\"200\" alt=\"" . $target_file. "\" /><br/>";
                    echo "<label>" . $target_file . "</label>";
                }else{
					echo "<img name=\"IMAGE\" src=\"" . $pathName . (strlen($image_folder) > 0 ? $image_folder . "/" : "") . (strlen($image_name) > 0 ? $image_name : "no-image.jpg") . "\" width=\"200\" alt=\"" . (strlen($image_name) > 0 ? $image_name : "no-image.jpg") . "\" /><br/>";	
					echo "<label>" . (strlen($image_name) > 0 ? $image_name : "no image") . "</label>";	
                }
                ?>
				</div>
				<div class="p-change-picture">
                    <label>Main Image - Name:</label>
                    <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Category images are only used for sub-categories where they appear on a main category page<br /><br />Once you've uploaded an image its filename will appear here</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_NAME_DISABLED" SIZE="49" disabled value="<?php echo $image_name ?>"><br/><br/>
                    <input type="hidden" name="IMAGE_NAME" value="<?php echo $image_name ?>">
                    <label>Main Image - Alternative Name:</label>
                    <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a short text description of the image; if you don't then filename will be used<br /><br />Note that this is a legal requirement and is also indexed by search engines so is good for search engine optimisation</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_ALT" SIZE="49" value="<?php echo $image_alt ?>"><br/><br/>                    
                    
                    <label>Main Image - Subfolder:</label>
                    <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">If you've uploaded the image to a sub-folder this will show where the image is located</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="IMAGE_FOLDER_DISABLED" SIZE="49" disabled value="<?php echo $image_folder ?>"><br/><br/><br/>
                    <input type="hidden" name="IMAGE_FOLDER" value="<?php echo $image_folder ?>">
                    <Input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                    <label>Add Picture:</label>
                    <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">To add an image click the Browse button and navigate to the image you want to use; then click the Upload button</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <Input name="FILE_UPLOAD" type="file" size="37" value="<?php echo $pathName . strlen($image_name) > 0 ? $image_name : "" ?>" onchange="Set_Picture(this.form.IMAGE_NAME, this.form.FILE_UPLOAD, this.form.IMAGE);"/><br/><br/>
                    <label>New Image will be uploaded to the following folder:</label>
                    <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">If you want to upload images to a sub-folder within the images directory you can specify it here; format as shown: /sub-folder-name</span><span class=\"bottom\"></span></span>" : "") ?></a><br/>
                    <input type="text" name="FULL_PATH" SIZE="49" value="<?php echo $pathName . $image_folder ?>"><br/><br/>
                    <Input name="UPLOAD_IMAGE" type="submit" value="Upload Image" class="upload-button" /><br/><br/>

                    <label class="<?php echo $warning ?>"><?php echo $message_upload ?></label>
                </div>

            </td> 
		</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
		
    	<!--- END OF IMAGE HANDLING --------------------------------------------------------------------------------------->     
        <tr>
           	<td>Category Code:
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The category code assigned by the software</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><label><strong><?php echo strlen($categorycode) > 0 ? $categorycode : $next_code ?></strong></label>
                 	<input type="hidden" name="CATEGORY" SIZE="50" value="<?php echo strlen($categorycode) > 0 ? $categorycode : $next_code ?>">
        </td>
    	</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>          
		
        <tr>
            <td>Name:
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a name for the category (or sub-category)<br /><br />This will be displayed in menus and URLs<br /><br />It's best to use sentence case or all lower case for names; all capitals is less preferred due to an internet convention that words written in capitals are too forceful in the same way as shouting is in normal conversation</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="NAME" SIZE="50" value="<?php echo $name ?>"></td>
        </tr>
        <tr>
            <td>Description:
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a brief description of the sub-category to display on the parent category page<br /><br />Not required for top level categories or if you only want to display an image in your sub-category links</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td>
            	<input type="text" name="DESCRIPTION" SIZE="87" value="<?php echo $description ?>">
                <input type="hidden" name="PARENT" SIZE="87" value="<?php echo $parent ?>">
                <input type="hidden" name="TREE_NODE" SIZE="87" value="<?php echo $tree_node ?>">
                <input type="hidden" name="DISPLAY" SIZE="87" value="<?php echo $display ?>">
                <input type="hidden" name="MENU_POSN" SIZE="87" value="<?php echo $menu_posn ?>">
            </td>
        </tr> 
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
        <tr>
        	<td colspan="2">
                <label>Select Tag 1:</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="ATTRIBUTE1" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $attributes = getAllAttributes();
                    foreach($attributes as $a){
                        if($attribute1 == $a->AT_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select a search tag to apply to this category; you can choose from up to 8<br /><br />These need to be set up and entered in Product Options first</span><span class=\"bottom\"></span></span>" : "") ?></a>
           		&nbsp;&nbsp;&nbsp;
                <label>Select Tag 5:</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="ATTRIBUTE5" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $attributes = getAllAttributes();
                    foreach($attributes as $a){
                        if($attribute5 == $a->AT_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
		<tr>
        	<td colspan="2">
                <label>Select Tag 2:</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="ATTRIBUTE2" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $attributes = getAllAttributes();
                    foreach($attributes as $a){
                        if($attribute2 == $a->AT_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
           		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label>Select Tag 6:</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="ATTRIBUTE6" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $attributes = getAllAttributes();
                    foreach($attributes as $a){
                        if($attribute6 == $a->AT_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
		<tr>
        	<td colspan="2">
                <label>Select Tag 3:</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="ATTRIBUTE3" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $attributes = getAllAttributes();
                    foreach($attributes as $a){
                        if($attribute3 == $a->AT_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
           		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label>Select Tag 7:</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="ATTRIBUTE7" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $attributes = getAllAttributes();
                    foreach($attributes as $a){
                        if($attribute7 == $a->AT_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
		<tr>
        	<td colspan="2">
                <label>Select Tag 4:</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="ATTRIBUTE4" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $attributes = getAllAttributes();
                    foreach($attributes as $a){
                        if($attribute4 == $a->AT_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
           		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label>Select Tag 8:</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <select name="ATTRIBUTE8" onchange="">
                    <option value="#">Choose from...</option>
                    <?php
                    $attributes = getAllAttributes();
                    foreach($attributes as $a){
                        if($attribute8 == $a->AT_ID){
                            $selected = "selected ";
                        }else{
                            $selected = "";
                        }		
                        echo "<option value=\"" . $a->AT_ID . "\" " . $selected . ">" . html_entity_decode($a->AT_NAME, ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
		
        <tr>
			<td>Tabular Listing: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box if this category is to list all products in a table format</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <?php if($tabular_listing == "Y"){$checked = "checked";}else{$checked = "";}?>
            <td><input type="checkbox" name="TABULAR_LISTING" value="1" <?php echo $checked ?>></td>
		</tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
		 
        <tr>
        	<td>Top Content: 
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter text or other content that you want to appear ABOVE the product listings<br /><br />Remember that content here will push your product listings down the page<br /><br />Text added for SEO purposes is best entered in the Bottom Content area</span><span class=\"bottom\"></span></span>" : "") ?></a><br /><br />
            	
                <div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/edit_textarea.php?form=enter_thumb&field=TOP_CONTENT","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");>
                	<span>Edit</span>
                </a></div>
                <span style="float: right; position: relative; right:18px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                </td>
            <td><textarea type="text" name="TOP_CONTENT" class="p-amend-textarea" ><?php echo html_entity_decode($top_content, ENT_QUOTES) ?></textarea></td>
        </tr> 
        <tr>
			<td></td>
			<td>
                
            </td>
		</tr>
        <tr>
        	<td>Bottom Content: 
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter text or other content that you want to appear BELOW the product listings<br /><br />This is the ideal place to add text for SEO purposes</span><span class=\"bottom\"></span></span>" : "") ?></a><br /><br />
            	<div class="edit-button"><a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onClick=window.open("/_cms/edit_textarea.php?form=enter_thumb&field=BOTTOM_CONTENT","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");>
				<span>Edit</span>
                </a></div>
                <span style="float: right; position: relative; right:18px; bottom: 16px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></span></td>
            <td><textarea id="BOTTOM_CONTENT" name="BOTTOM_CONTENT" class="p-amend-textarea" ><?php echo $bottom_content ?></textarea></td>
        </tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
		
       	<tr>
        	<td>META Title: 
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the main search terms for this page; these should be specific to the page and are critical for SEO. <br /><br />Note that the Title tag (as it's known) is the most important place to include key words and phrases so that search engines pick them up.<br /><br />Approx 60 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="META_TITLE" SIZE="90" value="<?php echo $meta_title ?>"></td>
        </tr>                   
        <tr>
        	<td>META Description: 
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a description of the content of this page as a properly formed sentence; each page should have a unique description to ensure the best search engine rankings <br /><br />Note that this description is used by search engines in their listings to describe the content of the pages they are listing so think of it as a way to convert searchers into visitors; remember to include key words and phrases so that search engines can match them to search requests by people<br /><br />Approx 150 to 200 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><textarea type="text" name="META_DESC" class="p-amend-textarea"><?php echo $meta_desc ?></textarea></td>
        </tr> 
        <tr>
        	<td>META Keywords: 
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Include 3 or 4 words or phrases from your meta title and meta description first; it's also a good place to put mis-spellings relevant to the page content<br /><br />256 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><textarea type="text" name="META_KEYWORDS" class="p-amend-textarea"><?php echo $meta_keywords ?></textarea></td>
        </tr>
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
				
        <tr>
        	<td>Custom Head: 
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Put code, scripts and other items that need be added into the \"head\" area of this page.<br /><br />Note that anything added to this area will only appear on this page</span><span class=\"bottom\"></span></span>" : "") ?></a>
            </td>
            <td><textarea type="text" name="CUSTOM_HEAD" class="p-amendhead-textarea"><?php echo $custom_head ?></textarea></td>
        </tr> 
		
        <tr>
			<td colspan="2" class="td-sep">&nbsp;</td>
		</tr>
		
        <tr>
            <td>Prod Wrapper Div: 
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">An advanced control that enables different styling on individual category pages: leave as originally set up</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><input type="text" name="DIV_WRAP" SIZE="90" value="<?php echo $div_wrap ?>"></td>
            <td><input type="hidden" name="CA_CLASS" value="<?php echo $ca_class ?>"></td>
        </tr>       
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>          
		<!--- CREATE BUTTON ---------->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td>
            	<input type="hidden" name="CATEGORY_CREATED" value="<?php echo $category_created ?>" />
            	<input name="CREATE" type="submit" value="Create Category &raquo;&raquo;" class="create-button">
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to create a category with the details entered above</span><span class=\"bottom\"></span></span>" : "") ?></a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="NEXT" type="submit" value="Next Category &raquo;&raquo;" class="next-button">
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click here to create another new category - ensure you've clicked the Create Category button to save the current new category first</span><span class=\"bottom\"></span></span>" : "") ?></a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="DELETE" type="submit" value="Delete Category &raquo;&raquo;" class="delete-button-left">
            	
            </td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>
			<td colspan="2"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <!---
        <tr>
            <td>Programmer Message:</td>
            <td><input type="text" name="PROGRAMMER_MESSAGE" SIZE="87" value="<?php echo $message ?>"></td>
        </tr>
        --->
	</form>
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

