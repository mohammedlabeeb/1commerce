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

if (isset($_GET['delcategory'])) {
	//check that category to be deleted contains no subcategories or products
	if($_GET['delcategory'] != "HEADER" and $_GET['delcategory'] != "SPACER"){
		$node = $_GET['tree'] . "_" . $_GET['delcategory'];
		$rows_category = count(getCategories($node));
		$rows_product = count(getProducts($node));
		if($rows_category > 0 or $rows_product > 0){
			$message = "Unable to delete category {$_GET['delcategory']} - {$rows_category} subcategories and {$rows_product} products found within it  - Please delete these individually and re-try";
			$warning = "red";
		}else{
			//delete category from tree
			$rows = deleteCategoryFromTree($_GET['delcategory'], $_GET['tree']);	
			if ($rows == 1){
				$message .= "Category DELETED from the menu structure" . "<br/>";
				$warning = "green";
			}else{
				$message .= "CATEGORY record NOT DELETED - PLEASE CONTACT SHOPFITTER!!!"; 
				$warning = "red";
			}
			$error = null;
			$error = mysql_error();
			if ($error != null) { 
				$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " - PLEASE CONTACT SHOPFITTER!!!";
				$warning = "red";
			}
		}
	}else{
		//custom header so delete the actual category record since there are no dependancies
		$rows = Delete_Category_By_Name($_GET['delcategory'], urldecode($_GET['name']), $_GET['pos']);
		if ($rows == 1){
			$message .= "Category DELETED" . "<br/>";
			$warning = "green";
		}else{
			$message .= "CATEGORY record NOT DELETED - PLEASE CONTACT SHOPFITTER!!!"; 
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

if (isset($_GET['searchcategory'])) {
	//NEW PRODUCT SELECTED from search dropdown so get product deatils for display 
	$category = getCategory($_GET['searchcategory'], "");
	$selected_category = $category->CA_CODE;
	$categorycode = $category->CA_CODE;
	$name = html_entity_decode($category->CA_NAME, ENT_QUOTES);
	
	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_CATEGORY'] = $_GET['searchcategory'];
}

if (isset($_POST['RE-POSITION'])) {
	$tree = $_POST['TREE_CODE'];
	$rowswritten = 0;
	for ($i=1; $i<=$_POST['CATEGORIES_COUNTER']; $i++){
		//loop through categories and rewrite each with the latest position
		//echo $_POST['CATEGORY'.$i] . " / " . $_POST['POSITION'.$i] . "<br/>";
		$fields = array("ca_code"=>$_POST['CATEGORY'.$i], "ca_tree_node"=>$_POST['TREE_CODE'], 
						"ca_menu_posn"=>$_POST['POSITION'.$i], "original_posn"=>$_POST['ORIGINAL_POSITION'.$i],
						"ca_class"=>$_POST['CLASS'.$i]);
		$rows = Update_CategoryPosition($fields);
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
			$warning = "red";
		}
		$rowswritten = $rowswritten + $rows;
	}
	if (($rowswritten >= 1 and $rowswritten <= $_POST['CATEGORIES_COUNTER']) and $message == "" ){
		$message = "{$rowswritten} records successfully UPDATED";
		$warning = "green";
	}
	if (($rowswritten == 0) and $message == "" ){
		$message .= "WARNING ! ! ! - NO RECORDS UPDATED";
		$warning = "orange";
	}
	if ($rowswritten > $_POST['CATEGORIES_COUNTER']){
		$message .= "ERROR ! ! ! - MORE ROWS WRITTEN THAN SUBCATEGORIES WITHIN CATEGORY ! ! ! - {$rowswritten} RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
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

if (isset($_POST['ADD_CATEGORY'])) {
	//check category is valid first
	$checkok = validateSeed($_POST['CATEGORY_TO_ADD']);
	if($checkok == "true"){
		if(checkCategoryExists($_POST['CATEGORY_TO_ADD']) == "true"){
			//add to tree
			$fields = array("ca_code"=>$_POST['CATEGORY_TO_ADD'], "ca_parent"=>$_POST['CATEGORY_CURRENT'], "ca_tree_node"=>$_POST['TREE_CODE'],
			 				"ca_menu_posn"=>$_POST['POSITION_TO_ADD'], "ca_class"=>$_POST['CLASS_TO_ADD']);		
			$rows = addCategoryToTree($fields);
			if ($rows == 1){
				$message = $rows . "New SubCategory ADDED to selected category";
				$warning = "green";
			}
			if ($rows < 1){
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
			$message = "CATEGORY CODE DOES NOT EXIST - please enter a valid category code!";
			$warning = "red";
		}
	}else{
		$message = "INVALID FORMAT CATEGORY CODE - please enter a valid category code!";
		$warning = "red";
	}
}

if (isset($_POST['ADD_HEADER']) or isset($_POST['ADD_SPACER'])) {
	//create a header or a spacer category record to be used within the left menu structure ONLY
	if(isset($_POST['ADD_HEADER'])){$ca_code = "HEADER";}else{$ca_code = "SPACER";}
	if(isset($_POST['ADD_HEADER'])){$ca_name = $_POST['HEADER_TO_ADD'];}else{$ca_name = "";}
	$fields = array("ca_code"=>$ca_code, "ca_name"=>$ca_name, "ca_description"=>"",
					"ca_parent"=>"0", "ca_tree_node"=>"0", "ca_display"=>"Y", "ca_menu_posn"=>$_POST['POSITION_TO_ADD'],
					"ca_image"=>"", "ca_image_folder"=>"", "ca_image_alt"=>"",
					"ca_attribute1"=>"", "ca_attribute2"=>"",
					"ca_attribute3"=>"", "ca_attribute4"=>"", "ca_tabular_listing"=>"N",
					"ca_top_content"=>"", "ca_bottom_content"=>"",
					"ca_meta_title"=>"", "ca_meta_desc"=>"", "ca_meta_keywords"=>"",
					"ca_custom_head"=>"", "ca_div_wrap"=>"", "ca_class"=>$_POST['CLASS_TO_ADD'], "ca_disable"=>"N");
		
	$rows = Create_Category($fields);
	//now add the custom category to the tree
	if ($rows == 1){
		$message = $rows . "New SubCategory ADDED to selected category";
		$warning = "green";
	}
	if ($rows < 1){
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

$category_current = "";
if(isset($_GET['category'])){
	$category = getCategory($_GET['category'], "");
	$category_current = $category->CA_CODE;
}

if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Add Categories To Memu";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Add Categories To Menu - add categories to the menu structure</h1>
    <p><br />
    <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is where you add categories to the menu as well as sub-categories into parent categories<br /><br />A parent category is included in the menu that appears on all pages in the site<br /><br />A sub-category is only seen on its parent category page: sub-categories can be added to more than one parent category<br /><br />Help and guidance can be found by clicking on help icons like this</span><span class=\"bottom\"></span></span>" : "") ?></a></p>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    <form name="enter-thumb" action="/_cms/add_categories.php" enctype="multipart/form-data" method="post">
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
					echo "<td>";
					echo "Category: ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the category you want to add items to<br /><br />First the parent category and then any subsequent sub-categories<br /><br />The category selector will appear in a hierachy with the parent at the top; once you've reached the category you require search for the category you want to add to the menu</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
					echo "</td>";
                    echo "<td>";
                        echo "<select name=\"category\" id=\"jumpMenu\" onchange=\"MM_jumpMenu('parent',this,1)\">";
                            echo "<option value=\"#\">Choose from...</option>";
                            $categories = getCategories($treenode);
                            foreach($categories as $c){
                                //get next treenode and compare with category tree node tto test option selection
								if($c->CA_CODE != "HEADER" and $c->CA_CODE != "SPACER"){
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
									echo "<option value=\"/_cms/add_categories.php?tree=" . $c->CA_TREE_NODE . "_" . $c->CA_CODE . "\" " . $selected . ">" . $c->CA_NAME . "</option>";
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
            echo "<td>";
            	echo "<input type=\"submit\" name=\"PREVIOUS_CATEGORY\" value=\"Previous Category\" class=\"previous-button\">";
            echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to return to the previous selection<br /><br />Click until you've stepped back as far as you wish</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
			echo "</td>";
        echo "</tr>"; 
        echo "<tr>";
			echo "<td colspan=\"2\" class=\"td-sep\">&nbsp;</td>";
		echo "</tr>";   
        //--- CATEGORIES LISTING ----------------------------------------------------------------------------------------------------------->
		echo "<tr>";
			echo "<td>";
				echo "Position ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the menu items appear from left to right or top to bottom<br /><br />Lower numbers display nearer the left or top</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "<input name=\"TREE_CODE\" type=\"hidden\" value=\"" . $tree . "\" >";
				echo "<input name=\"CATEGORY_CURRENT\" type=\"hidden\" value=\"" . $lastselectedcategory . "\" >";
			echo "</td>";
			if($tree == "0"){$lastselected = "Main Menu";}
			echo "<td><div class=\"category-heading\">Categories added to <strong>" . $lastselected;
			echo "</strong> category</div>";
			echo "<div class=\"category-menu\">Menu Class</div>";
			echo "<div class=\"category-remove\">";
			echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Remove the category from the menu by clicking the appropriate Delete button<br /><br />This does not delete the category itself</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
			echo "Remove from list</div></td>";
		echo "</tr>";

        //if($tree != "0"){
            $categories = getCategories($tree);
            $cntr1 = 0;
            foreach($categories as $c){
                $category = getCategory_menu_posn($c->CA_CODE, $c->CA_MENU_POSN);
                $cntr1 ++;
                echo "<tr>";
                    echo "<td>";
						echo "<div class=\"form-input\"><label>Position</label><input type=\"text\" name=\"POSITION" . $cntr1 . "\" SIZE=\"6\" value=\"" . $c->CA_MENU_POSN . "\" class=\"box-position\"></div>";
						echo "<input type=\"hidden\" name=\"ORIGINAL_POSITION" . $cntr1 . "\" SIZE=\"6\" value=\"" . $c->CA_MENU_POSN . "\" >";
					echo "</td>";
                    echo "<td valign=\"middle\">";
                        echo "<div class=\"form-input\"><label>Category</label><input type=\"text\" name=\"CATEGORY_DISABLED" . $cntr1 . "\" SIZE=\"70\" value=\"" . $category->CA_CODE . " - " . $category->CA_NAME . "\" disabled ></div>";
						echo "<div class=\"form-input\"><label>Menu Class</label><input type=\"text\" name=\"CLASS" . $cntr1 . "\" SIZE=\"15\" value=\"" . $category->CA_CLASS . "\" ></div>";
						
						echo "<div class=\"delete-position\"><a href=\"/_cms/add_categories.php?tree=" . $tree . "&delcategory=" . $category->CA_CODE . "&name=" . urlencode(html_entity_decode($category->CA_NAME, ENT_QUOTES)) . "&pos=" . $c->CA_MENU_POSN . "\" class=\"delete-button\">";
							echo "<span>Delete</span></a></div>";
														
							
						echo "<input type=\"hidden\" name=\"CATEGORY" . $cntr1 . "\" SIZE=\"70\" value=\"" . $category->CA_CODE . "\" >";
                    echo "</td>";
                echo "</tr>";
            }
			echo "<tr>";
			echo "</tr>";
				echo "<td>";
					echo "&nbsp";
				echo "</td>";
			echo "<tr>";
				echo "<td>";
				echo "</td>";
                echo "<td>";
					echo "<input name=\"CATEGORIES_COUNTER\" type=\"hidden\" value=\"" . $cntr1 . "\" />";
					echo "<input name=\"RE-POSITION\" type=\"submit\" value=\"Update\" class=\"update-button\"/><a href=\"#\" class=\"tt\"> ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Save all changes you've made to the above table</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
            echo "</tr>";
            echo "<tr>";
				echo "<td colspan=\"2\" class=\"td-sep\">&nbsp;</td>";
            echo "</tr>";
			//--- ADD CATEGORIES ------------------------------------------------------------------------------------------------------>
			echo "<tr>";
				echo "<td></td>";
				echo "<td>";
					echo "Add Category to menu or Subcategory to a parent Category";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the category you want to add to the menu or parent category<br /><br />To select from all categories place the mouse cursor in the search field with no other text and click search<br /><br />Then select the desired category from the Choose from... dropdown box<br /><br />Finally, when the category code appears in the Add Category field, set the position number you desire and click the Add button<br /><br />The listing above should have appeared in the list above</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo "Search for: ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the category you want to add to the menu or parent category<br /><br />To select from all categories place the mouse cursor in the search field with no other text and click search</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
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
						if($lastselectedcategory == ""){
							//only allow user to select HEADER or Spacer at the top level ie. the left menu
							if(isset($_GET['header'])){$selected = "selected";}else{$selected = "";}
							echo "<option value=\"/_cms/add_categories.php?header=1\" " . $selected . ">HEADER</option>";
							if(isset($_GET['spacer'])){$selected = "selected";}else{$selected = "";}
							echo "<option value=\"/_cms/add_categories.php?spacer=1\" " . $selected . ">SPACER</option>";
						}
						if (isset($_POST['SEARCH_DATA'])){
							$categories = Search_category($_POST['SEARCH_DATA']);
							foreach($categories as $c){
								if($c->CA_CODE != "HEADER" and $c->CA_CODE != "SPACER"){
									if(isset($_POST['SELECTED_CATEGORY']) and $_POST['SELECTED_CATEGORY'] == $c->CA_CODE){
										$selected = "selected";
									}else{
										$selected = "";
									}
									echo "<option value=\"/_cms/add_categories.php?tree=" . $tree . "&searchdata=" . $_POST['SEARCH_DATA'] . "&searchcategory=" . $c->CA_CODE . "\"" . $selected . ">" . $c->CA_CODE . " - " . $c->CA_NAME . "</option>";
								}
							}
						}
					echo "</select>";
					echo "<input type=\"hidden\" name=\"SELECTED_CATEGORY\" value=\"" . (isset($selected_category) ? $selected_category : "") . "\">";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select the desired category from this dropdown menu</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan=\"2\" class=\"td-sep\">&nbsp;</td>";
			echo "</tr>";
			//--- END OF SEARCHBOX ----------------------------------------------------------------------------------------------->
            echo "<tr>";
			if(!isset($_GET['header']) and !isset($_GET['spacer'])){
                echo "<td>Add category: ";
				echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The category you've chosen to add to the menu or sub-category</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
				echo "</td>";
                echo "<td>";
                    echo "<input type=\"text\" name=\"CATEGORY_TO_ADD\" SIZE=\"10\" value=\"" . (isset($selected_category) ? $selected_category : "") . "\">&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "Menu Class: ";
					echo "<input type=\"text\" name=\"CLASS_TO_ADD\" SIZE=\"15\" value=\"\">&nbsp;&nbsp;";
					echo "To position: ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the menu or subcategories appear from top to bottom or left to right<br /><br />Lower numbers display nearer the left or top<br /><br />The default is 999 which will add the new category to the end of the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a> " : "";
					echo "<input type=\"text\" name=\"POSITION_TO_ADD\" SIZE=\"5\" value=\"999\">&nbsp;&nbsp;&nbsp;";
                echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save the new category in the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
                    echo " <input name=\"ADD_CATEGORY\" type=\"submit\" value=\"Add\" class=\"add-button\" />";
				echo "</td>";
			}else{
				if(isset($_GET['header'])){
					echo "<td>Add Header Text: ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">You have chosen to add a new Header to the menu. Please add the text for the header here.</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
					echo "</td>";
					echo "<td>";
						echo "<input type=\"text\" name=\"HEADER_TO_ADD\" SIZE=\"27\" value=\"\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "Menu Class: ";
						echo "<input type=\"text\" name=\"CLASS_TO_ADD\" SIZE=\"15\" value=\"\">&nbsp;&nbsp;";
						echo "To position: ";
						echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the menu or subcategories appear from top to bottom or left to right<br /><br />Lower numbers display nearer the left or top<br /><br />The default is 999 which will add the new category to the end of the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a> " : "";
						echo "<input type=\"text\" name=\"POSITION_TO_ADD\" SIZE=\"5\" value=\"999\">&nbsp;&nbsp;&nbsp;";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save the new Header Text in the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
						echo "<input name=\"ADD_HEADER\" type=\"submit\" value=\"Add\" class=\"add-button\" />";
					echo "</td>";
				}
				if(isset($_GET['spacer'])){
					echo "<td>Add Spacer ";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">You have chosen to add a new Spacer to the menu. Please add the text for the header here.</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
					echo "</td>";
					echo "<td>";
						echo "<input type=\"hidden\" name=\"CLASS_TO_ADD\" SIZE=\"15\" value=\"\">&nbsp;&nbsp;";
						echo "to position: ";
						echo ($preferences->PREF_TOOL_TIPS == "Y") ? "<a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This sets the order in which the menu or subcategories appear from top to bottom or left to right<br /><br />Lower numbers display nearer the left or top<br /><br />The default is 999 which will add the new category to the end of the list</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a> " : "";
						echo "<input type=\"text\" name=\"POSITION_TO_ADD\" SIZE=\"5\" value=\"999\">&nbsp;&nbsp;&nbsp;";
					echo ($preferences->PREF_TOOL_TIPS == "Y") ? " <a href=\"#\" class=\"tt\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to add a Spacer to the desired menu position</span><span class=\"bottom\"></span></span><img src=\"/_cms/csstooltips/q-icon.png\"></a>" : "";
						echo "<input name=\"ADD_SPACER\" type=\"submit\" value=\"Add\" class=\"add-button\" />";
					echo "</td>";
				}
			}
            echo "</tr>";             
        //}
       //--- MESSAGES ---------------------------------------------------------------------------------------------------->
	   ?>
        <tr>
			<td colspan="2"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>        

	</form>
    <tr>
    	<td colspan="2">
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<h2>MENU CLASS</h2>
		<p>this changes the style of the menu:<br/>
		Leave blank for default menu<br/><br/>
		Add:  'category-contrast'   to contrast with the default style<br/>
		Add:  'category-hilight'   to standout from the default style - ideal for a Special Offers category<br/>
		Add:  'category-title'   if you add a 'Header' to the menu <br/>
		Add:  'category-spacer'   if you add a 'Spacer' to the menu </p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		</td>
    </tr>

    </table>
<?php
  include_once("includes/footer_admin.php");
?>

