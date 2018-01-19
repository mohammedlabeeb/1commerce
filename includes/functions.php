
<?php
// start of Knit And Sew functions ---------------------------------------------------------------------------------------------------------------------

//---- PREFERENCES AND SETTINGS-------------------------------------------------------------------------------------------------------------------------------------

function getPreferences(){
	$sql = "SELECT * FROM preferences ";
	
	return FetchSqlAsObject($sql);
}

function Rewrite_Preferences($fields){
	// rewrites entire table row
	$shop_id_original = $fields['shop_id_original'];
	$pref_shop_id = $fields['pref_shop_id'];
	$pref_trade_id = $fields['pref_trade_id'];
	$pref_shopname = $fields['pref_shopname'];
	$pref_shopurl = $fields['pref_shopurl'];
	$pref_email = $fields['pref_email'];
	$pref_meta_title = $fields['pref_meta_title'];
	$pref_meta_desc = $fields['pref_meta_desc'];
	$pref_meta_keywords = $fields['pref_meta_keywords'];
	$pref_currency = $fields['pref_currency'];
	$pref_vat = $fields['pref_vat'];
	$pref_exvat = $fields['pref_exvat'];
	$pref_sell_exvat = $fields['pref_sell_exvat'];
	$pref_min_order = $fields['pref_min_order'];
	$pref_min_order_trade = $fields['pref_min_order_trade'];
	$pref_google_search = $fields['pref_google_search'];
	$pref_cat_seed = $fields['pref_cat_seed'];
	$pref_prod_seed = $fields['pref_prod_seed'];
	$pref_prom_seed = $fields['pref_prom_seed'];
	$pref_custom_head = $fields['pref_custom_head'];
	$pref_tracking_code = $fields['pref_tracking_code'];
	$pref_shop_pw = $fields['pref_shop_pw'];
	$pref_shop_notes = $fields['pref_shop_notes'];
	$pref_tool_tips = $fields['pref_tool_tips'];
	$pref_advanced_search = $fields['pref_advanced_search'];
	$pref_reviews = $fields['pref_reviews'];
	$pref_publish = $fields['pref_publish'];
	$pref_shop_access = $fields['pref_shop_access'];
	
	$sql = "UPDATE preferences ";
	$sql .= "SET pref_shop_id='" . mysql_real_escape_string($pref_shop_id) . "', pref_trade_id='" . mysql_real_escape_string($pref_trade_id) . "', pref_shopname='" . mysql_real_escape_string($pref_shopname) . "',";
	$sql .= " pref_shopurl='" . mysql_real_escape_string($pref_shopurl) . "', pref_email='" . mysql_real_escape_string($pref_email) . "', pref_meta_title='" . htmlentities($pref_meta_title, ENT_QUOTES) . "',";
	$sql .= " pref_meta_desc='" . htmlentities($pref_meta_desc, ENT_QUOTES) . "', pref_meta_keywords='" . htmlentities($pref_meta_keywords, ENT_QUOTES) . "', pref_currency='" . mysql_real_escape_string($pref_currency) . "',";
	$sql .= " pref_vat='" . mysql_real_escape_string($pref_vat) . "', pref_exvat='" . mysql_real_escape_string($pref_exvat) . "', pref_sell_exvat='" . mysql_real_escape_string($pref_sell_exvat) . "',";
	$sql .= " pref_min_order='" . mysql_real_escape_string($pref_min_order) . "', pref_min_order_trade='" . mysql_real_escape_string($pref_min_order_trade) . "', pref_cat_seed='" . mysql_real_escape_string($pref_cat_seed) . "',";
	$sql .= " pref_prod_seed='" . mysql_real_escape_string($pref_prod_seed) . "', pref_prom_seed='" . mysql_real_escape_string($pref_prom_seed) . "', pref_google_search='" . mysql_real_escape_string($pref_google_search) . "',";
	$sql .= " pref_custom_head='" . htmlentities($pref_custom_head, ENT_QUOTES). "', pref_tracking_code='" . htmlentities($pref_tracking_code, ENT_QUOTES) . "',";
	$sql .= " pref_shop_pw='" . htmlentities($pref_shop_pw, ENT_QUOTES) . "', pref_shop_notes='" . htmlentities($pref_shop_notes, ENT_QUOTES) . "', pref_tool_tips='" . mysql_real_escape_string($pref_tool_tips) . "', pref_advanced_search='" . mysql_real_escape_string($pref_advanced_search) . "',";
	$sql .= " pref_reviews='" . mysql_real_escape_string($pref_reviews) . "', pref_publish='" . mysql_real_escape_string($pref_publish) . "',";
	$sql .= " pref_shop_access='" . mysql_real_escape_string($pref_shop_access) . "' ";
	$sql .= "WHERE pref_shop_id='" . mysql_real_escape_string($shop_id_original) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Update_Preferences($shop, $field, $value){
	// rewrites a single field
	
	$sql = "UPDATE preferences ";
	$sql .= "SET " . $field . "='" . mysql_real_escape_string($value) . "' ";
	$sql .= "WHERE pref_shop_id='" . $shop . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function getCurrencies(){
	$sql = "SELECT * FROM currencies ";
	
	return FetchSqlAsObjectArray($sql);
}

function getCurrency($currency){
	$sql = "SELECT * FROM currencies ";
	$sql .= "WHERE CU_SF_CODE = '" . $currency . "'";
	
	return FetchSqlAsObject($sql);
}

function getEmailSetup(){
	$sql = "SELECT * FROM email_setup ";
	
	return FetchSqlAsObject($sql);
}

function Rewrite_emailSetup($fields){
	// rewrites entire table row
	$em_reg_to = $fields['em_reg_to'];
	$em_reg_cc = $fields['em_reg_cc'];
	$em_reg_bcc = $fields['em_reg_bcc'];
	$em_reg_subject = $fields['em_reg_subject'];
	$em_reg_header = $fields['em_reg_header'];
	$em_reg_content = $fields['em_reg_content'];
	$em_reg_footer = $fields['em_reg_footer'];
	$em_conf_cc = $fields['em_conf_cc'];
	$em_conf_bcc = $fields['em_conf_bcc'];
	$em_conf_subject = $fields['em_conf_subject'];
	$em_conf_header = $fields['em_conf_header'];
	$em_conf_content = $fields['em_conf_content'];
	$em_conf_footer = $fields['em_conf_footer'];
	$em_rev_to = $fields['em_rev_to'];
	$em_rev_cc = $fields['em_rev_cc'];
	$em_rev_bcc = $fields['em_rev_bcc'];
	$em_rev_subject = $fields['em_rev_subject'];
	$em_rev_header = $fields['em_rev_header'];
	$em_rev_content = $fields['em_rev_content'];
	$em_rev_footer = $fields['em_rev_footer'];
	
	$sql = "UPDATE email_setup ";
	$sql .= "SET em_reg_to='" . htmlentities($em_reg_to, ENT_QUOTES) . "', em_reg_cc='" . htmlentities($em_reg_cc, ENT_QUOTES) . "', em_reg_bcc='" . htmlentities($em_reg_bcc, ENT_QUOTES) . "',";
	$sql .= " em_reg_subject='" . htmlentities($em_reg_subject, ENT_QUOTES) . "', em_reg_header='" . htmlentities($em_reg_header, ENT_QUOTES) . "', em_reg_content='" . htmlentities($em_reg_content, ENT_QUOTES) . "',";
	$sql .= " em_reg_footer='" . htmlentities($em_reg_footer, ENT_QUOTES) . "',";
	$sql .= " em_conf_cc='" . htmlentities($em_conf_cc, ENT_QUOTES) . "', em_conf_bcc='" . htmlentities($em_conf_bcc, ENT_QUOTES) . "',";
	$sql .= " em_conf_subject='" . htmlentities($em_conf_subject, ENT_QUOTES) . "', em_conf_header='" . htmlentities($em_conf_header, ENT_QUOTES) . "',";
	$sql .= " em_conf_content='" . htmlentities($em_conf_content, ENT_QUOTES) . "', em_conf_footer='" . htmlentities($em_conf_footer, ENT_QUOTES) . "', ";
	$sql .= " em_rev_to='" . htmlentities($em_rev_to, ENT_QUOTES) . "', em_rev_cc='" . htmlentities($em_rev_cc, ENT_QUOTES) . "', em_rev_bcc='" . htmlentities($em_rev_bcc, ENT_QUOTES) . "',";
	$sql .= " em_rev_subject='" . htmlentities($em_rev_subject, ENT_QUOTES) . "', em_rev_header='" . htmlentities($em_rev_header, ENT_QUOTES) . "', em_rev_content='" . htmlentities($em_rev_content, ENT_QUOTES) . "', ";
	$sql .= " em_rev_footer='" . htmlentities($em_rev_footer, ENT_QUOTES) . "' ";
	$sql .= "WHERE em_id='1'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}
//---- CATEGORIES -------------------------------------------------------------------------------------------------------------------------------------
function List_Categories($category){
	//gets all categories
	$sql = "SELECT * FROM categories ";
	if($category != "ALL"){
		$sql .= "WHERE CA_CODE='" . $category . "' ";
		return FetchSqlAsObject($sql);
	}
	$sql .= "ORDER BY CA_CODE";
	return FetchSqlAsObjectArray($sql);
}

function getAllClones($category){
	//gets all clones of specified category ie. every occurence of the category within the menu structure
	$sql = "SELECT * FROM categories ";
	if($category != ""){
		$sql .= "WHERE CA_CODE = '" . $category . "'";
	}
	return FetchSqlAsObjectArray($sql);
}

function getCategories($tree){
	//gets all categories below the tree node ie. all subcategories WITHIN a category
	$sql = "SELECT * FROM categories ";
	$sql .= "WHERE CA_TREE_NODE = '" . $tree . "'";
	$sql .= " ORDER BY CA_MENU_POSN";
	
	return FetchSqlAsObjectArray($sql);
}

function checkCategoryExists($category){
	//check category exists and is not already a subcategory of the currently selected category/tree node
	$sql = "SELECT * FROM categories ";
	$sql .= "WHERE CA_CODE = '" . $category . "'";
	$results = mysql_query($sql);
	if (mysql_num_rows($results) > 0){
		//note that each category may have multiple entries where it has been added to a number of different parent categories
		return "true";
	}
	
	return "false";
}

function getCategory($code, $tree){
	$sql = "SELECT * FROM categories ";
	$sql .= "WHERE CA_CODE = '" . $code . "'";
	if($tree != ""){
		$sql .= " AND CA_TREE_NODE = '" . $tree . "'";
	}
	return FetchSqlAsObject($sql);
}

function getCategory_menu_posn($code, $posn){
	$sql = "SELECT * FROM categories ";
	$sql .= "WHERE CA_CODE = '" . $code . "'";
	if($posn != ""){
		$sql .= " AND CA_MENU_POSN = '" . $posn . "'";
	}
	return FetchSqlAsObject($sql);
}

function addCategoryToTree($fields){
	$ca_code = $fields['ca_code'];
	$ca_parent = $fields['ca_parent'];
	$ca_tree_node = $fields['ca_tree_node'];
	$ca_menu_posn = $fields['ca_menu_posn'];
	$ca_class = $fields['ca_class'];
	//get generic category details
	$category = getCategory($ca_code, "");
	$fields = array("ca_code"=>$category->CA_CODE, "ca_name"=>$category->CA_NAME, "ca_description"=>$category->CA_DESCRIPTION,
					"ca_parent"=>$ca_parent, "ca_tree_node"=>$ca_tree_node, "ca_display"=>$category->CA_DISPLAY, "ca_menu_posn"=>$ca_menu_posn,
					"ca_image"=>$category->CA_IMAGE, "ca_image_folder"=>$category->CA_IMAGE_FOLDER, "ca_image_alt"=>$category->CA_IMAGE_ALT, "ca_div_wrap"=>$category->CA_DIV_WRAP,
					"ca_meta_title"=>$category->CA_META_TITLE, "ca_meta_desc"=>$category->CA_META_DESC, "ca_meta_keywords"=>$category->CA_META_KEYWORDS,
					"ca_attribute1"=>$category->CA_ATTRIBUTE1, "ca_attribute2"=>$category->CA_ATTRIBUTE2,"ca_attribute3"=>$category->CA_ATTRIBUTE3,"ca_attribute4"=>$category->CA_ATTRIBUTE4,
					"ca_attribute5"=>$category->CA_ATTRIBUTE5, "ca_attribute6"=>$category->CA_ATTRIBUTE6,"ca_attribute7"=>$category->CA_ATTRIBUTE7,"ca_attribute8"=>$category->CA_ATTRIBUTE8,
		 			"ca_top_content"=>$category->CA_TOP_CONTENT, "ca_bottom_content"=>$category->CA_BOTTOM_CONTENT, "ca_custom_head"=>$category->CA_CUSTOM_HEAD,
					"ca_class"=>$ca_class, "ca_disable"=>$category->CA_DISABLE,
					"ca_tabular_listing"=>$category->CA_TABULAR_LISTING);
					
	if($category->CA_TREE_NODE == ""){
		//basic category has not yet been added to the tree so rewrite this generic category with the current parent and tree node
		$writetree = true;
		$rows = Rewrite_Category($fields, $writetree);
	}else{
		//basic category has already been added to tree at least once
		//so insert a new row using the generic category details as a base record but adding the current parent and tree code
		$rows = Create_Category($fields);
	}
	return $rows;
}

function Create_Category($fields){
	// inserts a new product row
	$ca_code = $fields['ca_code'];
	$ca_name = $fields['ca_name'];
	$ca_description = $fields['ca_description'];
	$ca_parent = $fields['ca_parent'];
	$ca_tree_node = $fields['ca_tree_node'];
	$ca_display = $fields['ca_display'];
	$ca_menu_posn = $fields['ca_menu_posn'];
	$ca_image = $fields['ca_image'];
	$ca_image_folder = $fields['ca_image_folder'];
	$ca_image_alt = $fields['ca_image_alt'];
	$ca_attribute1 = $fields['ca_attribute1'];
	$ca_attribute2 = $fields['ca_attribute2'];
	$ca_attribute3 = $fields['ca_attribute3'];
	$ca_attribute4 = $fields['ca_attribute4'];
	$ca_attribute5 = $fields['ca_attribute5'];
	$ca_attribute6 = $fields['ca_attribute6'];
	$ca_attribute7 = $fields['ca_attribute7'];
	$ca_attribute8 = $fields['ca_attribute8'];
	$ca_tabular_listing = $fields['ca_tabular_listing'];
	$ca_top_content = $fields['ca_top_content'];
	$ca_bottom_content = $fields['ca_bottom_content'];
	$ca_meta_title = $fields['ca_meta_title'];
	$ca_meta_desc = $fields['ca_meta_desc'];
	$ca_meta_keywords = $fields['ca_meta_keywords'];
	$ca_custom_head = $fields['ca_custom_head'];
	$ca_div_wrap = $fields['ca_div_wrap'];
	$ca_class = $fields['ca_class'];
	$ca_disable = $fields['ca_disable'];

	// write table row
	$sql = "INSERT INTO categories (CA_CODE, CA_NAME, CA_DESCRIPTION, ";
	$sql .= "CA_IMAGE, CA_IMAGE_FOLDER, CA_IMAGE_ALT, ";
	$sql .= "CA_PARENT, CA_TREE_NODE, ";
	$sql .= "CA_DISPLAY, CA_MENU_POSN, ";
	$sql .= "CA_ATTRIBUTE1, CA_ATTRIBUTE2, CA_ATTRIBUTE3, CA_ATTRIBUTE4, ";
	$sql .= "CA_ATTRIBUTE5, CA_ATTRIBUTE6, CA_ATTRIBUTE7, CA_ATTRIBUTE8, ";
	$sql .= "CA_TABULAR_LISTING, ";
	$sql .= "CA_TOP_CONTENT, CA_BOTTOM_CONTENT, ";
	$sql .= "CA_META_TITLE, CA_META_DESC, CA_META_KEYWORDS, CA_CUSTOM_HEAD, CA_DIV_WRAP, CA_CLASS, CA_DISABLE)";
	$sql .= " VALUES ('" . mysql_real_escape_string($ca_code) . "', '" . htmlentities($ca_name, ENT_QUOTES) . "', '" . htmlentities($ca_description) . "', '";
	$sql .= mysql_real_escape_string($ca_image) . "', '" . mysql_real_escape_string($ca_image_folder) . "', '";
	$sql .= mysql_real_escape_string($ca_image_alt) . "', '" . mysql_real_escape_string($ca_parent) . "', '" . mysql_real_escape_string($ca_tree_node) . "', '";
	$sql .= mysql_real_escape_string($ca_display) . "', '" . mysql_real_escape_string($ca_menu_posn) . "', '";
	$sql .= mysql_real_escape_string($ca_attribute1) . "', '" . mysql_real_escape_string($ca_attribute2) . "', '";
	$sql .= mysql_real_escape_string($ca_attribute3) . "', '" . mysql_real_escape_string($ca_attribute4) . "', '";
	$sql .= mysql_real_escape_string($ca_attribute5) . "', '" . mysql_real_escape_string($ca_attribute6) . "', '";
	$sql .= mysql_real_escape_string($ca_attribute7) . "', '" . mysql_real_escape_string($ca_attribute8) . "', '"; 
	$sql .= mysql_real_escape_string($ca_tabular_listing) . "', '";
	$sql .= htmlentities($ca_top_content) . "', '" . htmlentities($ca_bottom_content) . "', '";
	$sql .= htmlentities($ca_meta_title) . "', '" . htmlentities($ca_meta_desc) . "', '" . htmlentities($ca_meta_keywords) . "', '";
	$sql .= htmlentities($ca_custom_head) . "', '" . htmlentities($ca_div_wrap) . "', '" . htmlentities($ca_class) . "', '" . htmlentities($ca_disable) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Category($fields, $writetree){
	// rewrites entire table row except for the tree details if $notree is set
	$ca_code = $fields['ca_code'];
	$ca_name = $fields['ca_name'];
	$ca_description = $fields['ca_description'];
	$ca_parent = $fields['ca_parent'];
	$ca_tree_node = $fields['ca_tree_node'];
	$ca_display = $fields['ca_display'];
	$ca_menu_posn = $fields['ca_menu_posn'];
	$ca_image = $fields['ca_image'];
	$ca_image_folder = $fields['ca_image_folder'];
	$ca_image_alt = $fields['ca_image_alt'];
	$ca_meta_title = $fields['ca_meta_title'];
	$ca_meta_desc = $fields['ca_meta_desc'];
	$ca_meta_keywords = $fields['ca_meta_keywords'];
	$ca_custom_head = $fields['ca_custom_head'];
	$ca_div_wrap = $fields['ca_div_wrap'];
	$ca_class = $fields['ca_class'];
	$ca_attribute1 = $fields['ca_attribute1'];
	$ca_attribute2 = $fields['ca_attribute2'];
	$ca_attribute3 = $fields['ca_attribute3'];
	$ca_attribute4 = $fields['ca_attribute4'];
	$ca_attribute5 = $fields['ca_attribute5'];
	$ca_attribute6 = $fields['ca_attribute6'];
	$ca_attribute7 = $fields['ca_attribute7'];
	$ca_attribute8 = $fields['ca_attribute8'];
	$ca_tabular_listing = $fields['ca_tabular_listing'];
	$ca_disable = $fields['ca_disable'];
	$ca_top_content = $fields['ca_top_content'];
	$ca_bottom_content = $fields['ca_bottom_content'];
	$sql = "UPDATE categories ";
	$sql .= "SET CA_CODE='" . mysql_real_escape_string($ca_code) . "',";
	$sql .= " CA_NAME='" . htmlentities($ca_name, ENT_QUOTES) . "', CA_DESCRIPTION='" . htmlentities($ca_description, ENT_QUOTES) . "',";
	if($writetree){
		$sql .= " CA_PARENT='" . mysql_real_escape_string($ca_parent) . "', CA_TREE_NODE='" . mysql_real_escape_string($ca_tree_node) . "',";
	}
	$sql .= " CA_DISPLAY='" . mysql_real_escape_string($ca_display) . "', CA_MENU_POSN='" . mysql_real_escape_string($ca_menu_posn) . "',";
	$sql .= " CA_IMAGE='" . mysql_real_escape_string($ca_image) . "', CA_IMAGE_FOLDER='" . mysql_real_escape_string($ca_image_folder) . "',";
	$sql .= " CA_IMAGE_ALT='" . mysql_real_escape_string($ca_image_alt) .  "',";
	$sql .= " CA_META_TITLE='" . htmlentities($ca_meta_title, ENT_QUOTES) . "', CA_META_DESC='" . htmlentities($ca_meta_desc, ENT_QUOTES) . "',";
	$sql .= " CA_META_KEYWORDS='" . htmlentities($ca_meta_keywords, ENT_QUOTES) . "', CA_CUSTOM_HEAD='" . htmlentities($ca_custom_head, ENT_QUOTES) . "',";
	$sql .= " CA_DIV_WRAP='" . htmlentities($ca_div_wrap, ENT_QUOTES) . "', CA_CLASS='" . htmlentities($ca_class, ENT_QUOTES) . "',";
	$sql .= " CA_ATTRIBUTE1='" . mysql_real_escape_string($ca_attribute1) . "', CA_ATTRIBUTE2='" . mysql_real_escape_string($ca_attribute2) . "',";
	$sql .= " CA_ATTRIBUTE3='" . mysql_real_escape_string($ca_attribute3) . "', CA_ATTRIBUTE4='" . mysql_real_escape_string($ca_attribute4) . "',";
	$sql .= " CA_ATTRIBUTE5='" . mysql_real_escape_string($ca_attribute5) . "', CA_ATTRIBUTE6='" . mysql_real_escape_string($ca_attribute6) . "',";
	$sql .= " CA_ATTRIBUTE7='" . mysql_real_escape_string($ca_attribute7) . "', CA_ATTRIBUTE8='" . mysql_real_escape_string($ca_attribute8) . "',";
	$sql .= " CA_TABULAR_LISTING='" . mysql_real_escape_string($ca_tabular_listing) . "',";
	$sql .= " CA_DISABLE='" . mysql_real_escape_string($ca_disable) . "',";
	$sql .= " CA_TOP_CONTENT='" . htmlentities($ca_top_content, ENT_QUOTES) . "', CA_BOTTOM_CONTENT='" . htmlentities($ca_bottom_content, ENT_QUOTES) . "',";
	$sql .= " CA_LAST_UPDATED='" . strftime("%Y-%m-%d %H:%M:%S", time()) . "' ";
	$sql .= "WHERE CA_CODE='" . mysql_real_escape_string($ca_code) . "' ";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Update_CategoryPosition($fields){
	// rewrites entire table row
	$ca_code = $fields['ca_code'];
	$ca_tree_node = $fields['ca_tree_node'];
	$ca_menu_posn = $fields['ca_menu_posn'];
	$original_posn = $fields['original_posn'];
	$ca_class = $fields['ca_class'];

	$sql = "UPDATE categories ";
	$sql .= "SET CA_MENU_POSN='" . mysql_real_escape_string($ca_menu_posn) . "', ";
	$sql .= "CA_CLASS='" . mysql_real_escape_string($ca_class) . "' ";
	$sql .= "WHERE CA_CODE='" . mysql_real_escape_string($ca_code) . "' AND CA_TREE_NODE='" . mysql_real_escape_string($ca_tree_node) . "' ";
	$sql .= "AND CA_MENU_POSN='" . $original_posn . "' LIMIT 1";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Category($category, $tree){
	// deletes ALL occurences of this code from categories or alternsatively just that below a specified tree node
	$sql = "DELETE FROM categories WHERE CA_CODE='" . $category . "'";
	if($tree != "ALL"){
		$sql .= " AND CA_TREE_NODE = '" . $tree . "'";
	}
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Delete_Category_By_Name($category, $name, $pos){
	// deletes ALL occurences of this code from categories
	$sql = "DELETE FROM categories WHERE CA_CODE='" . $category . "' ";
	if($name != ""){
		$sql .= "AND CA_NAME='" . htmlentities($name,ENT_QUOTES) . "' ";
	}
	if($pos != ""){
		$sql .= "AND CA_MENU_POSN='" . $pos . "' ";
	}
	$sql .= "LIMIT 1";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function deleteCategoryFromTree($category, $tree){
	//deletes a category from the tree which has already been added to the tree ie. where there is a valid parent code
	$sql = "SELECT * FROM categories ";
	$sql .= "WHERE CA_CODE = '" . $category . "'";
	$results = mysql_query($sql);
	if (mysql_num_rows($results) > 1){
		//category exists more than once within the menu tree structure so simply delete the row
		$sql = "DELETE FROM categories WHERE CA_CODE = '" . $category . "'";
		if($tree != "ALL"){
			$sql .= " AND CA_TREE_NODE = '" . $tree . "'";
		}
		$sql .= " AND CA_TREE_NODE <> ''";
		$rows = mysql_query($sql);
	}else{
	//category only exists once within the menu tree structure so rewrite the parent and tree codes as blank 
	// - this then becomes the "generic" category which may then be added to the menu again at a later time
		$c =  mysql_fetch_object($results);
		$fields = array("ca_code"=>$c->CA_CODE, "ca_name"=>$c->CA_NAME, "ca_description"=>$c->CA_DESCRIPTION,
						"ca_parent"=>"", "ca_tree_node"=>"", "ca_display"=>"Y", "ca_menu_posn"=>999,
						"ca_image"=>$c->CA_IMAGE, "ca_image_folder"=>$c->CA_IMAGE_FOLDER, "ca_image_alt"=>$c->CA_IMAGE_ALT,
						"ca_meta_title"=>$c->CA_META_TITLE, "ca_meta_desc"=>$c->CA_META_DESC, "ca_meta_keywords"=>$c->CA_META_KEYWORDS, "ca_div_wrap"=>$c->CA_DIV_WRAP, "ca_class"=>$c->CA_CLASS,
						"ca_custom_head"=>$c->CA_CUSTOM_HEAD, "ca_attribute1"=>$c->CA_ATTRIBUTE1, "ca_attribute2"=>$c->CA_ATTRIBUTE2, "ca_attribute3"=>$c->CA_ATTRIBUTE3, "ca_attribute4"=>$c->CA_ATTRIBUTE4,
						"ca_attribute5"=>$c->CA_ATTRIBUTE5, "ca_attribute6"=>$c->CA_ATTRIBUTE6, "ca_attribute7"=>$c->CA_ATTRIBUTE7, "ca_attribute8"=>$c->CA_ATTRIBUTE8,
						"ca_tabular_listing"=>$c->CA_TABULAR_LISTING, "ca_top_content"=>$c->CA_TOP_CONTENT, "ca_bottom_content"=>$c->CA_BOTTOM_CONTENT, "ca_disable"=>$c->CA_DISABLE);
		
		$writetree = true;		
		$rows = Rewrite_Category($fields, $writetree);
	}
	
	return $rows;
}

function getParentFromTree($tree){
	//get last element of tree node code. This is the parent category code.
	$fstart = 0;
	$fend = 999;
	while ($fend > 0){
		$fend = strpos($tree, "_", $fstart);
		if($fend > 0){
			$parent = substr($tree, $fend + 1);
			$fstart = $fend + 1;
		}
	}
	$sql = "SELECT * FROM categories ";
	$sql .= "WHERE CA_CODE = '" . $parent . "'";
	
	return FetchSqlAsObject($sql);
}

//---- PRODCAT -------------------------------------------------------------------------------------------------------------------------------------

function getProducts($tree){
	//gets all products below the tree node ie. all products WITHIN a category
	$sql = "SELECT * FROM prodcat ";
	$sql .= "WHERE PC_TREE_NODE = '" . $tree . "'";
	$sql .= " ORDER BY PC_POSITION";
	
	return FetchSqlAsObjectArray($sql);
}

function getProductsInCategory($category){
	$sql = "SELECT * FROM prodcat ";
	$sql .= "WHERE PC_CATEGORY = '" . $category . "'";
	
	return FetchSqlAsObjectArray($sql);
}

function getProductsBelowTreeNode($tree){
	$sql = "SELECT DISTINCT PC_PRODUCT FROM prodcat LEFT JOIN product on PC_PRODUCT = PR_PRODUCT ";
	$sql .= "WHERE PC_TREE_NODE LIKE '" . $tree . "%'";
	$sql .= "AND PR_DISABLE = 'N'";
	
	return FetchSqlAsObjectArray($sql);	
}

function getProductTree($product) {
	$tree = "";
	$sql = "SELECT * FROM prodcat";
	$sql .= " WHERE PC_PRODUCT = '" . $product . "'";
	$row = FetchSqlAsObject($sql);
	if(!empty($row)){$tree = $row->PC_TREE_NODE;}
	
	return $tree;
}

function getProductFromProdcat($product) {
	$sql = "SELECT * FROM prodcat";
	$sql .= " WHERE PC_PRODUCT = '" . $product . "'";
	
	return FetchSqlAsObjectArray($sql);
}

function addProductToTree($fields){
	// create table row
	$pc_product = $fields['pc_product'];
	$pc_category = $fields['pc_category'];
	$pc_tree_node = $fields['pc_tree_node'];
	$pc_position = $fields['pc_position'];
	
	// write table row
	$sql = "INSERT INTO prodcat (PC_PRODUCT, PC_CATEGORY, PC_TREE_NODE, PC_POSITION)";
	$sql .= " VALUES ('" . mysql_real_escape_string($pc_product) . "', '" . mysql_real_escape_string($pc_category) . "', '" . mysql_real_escape_string($pc_tree_node) . "', '";
	$sql .= mysql_real_escape_string($pc_position) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Prodcat($fields){
	// rewrites entire table row
	$pc_product = $fields['pc_product'];
	$pc_category = $fields['pc_category'];
	$pc_tree_node = $fields['pc_tree_node'];
	$pc_position = $fields['pc_position'];

	$sql = "UPDATE prodcat ";
	$sql .= "SET PC_PRODUCT='" . mysql_real_escape_string($pc_product) . "',";
	$sql .= " PC_CATEGORY='" . mysql_real_escape_string($pc_category) . "', PC_TREE_NODE='" . mysql_real_escape_string($pc_tree_node) . "',";
	$sql .= " PC_POSITION='" . mysql_real_escape_string($pc_position) . "' ";
	$sql .= "WHERE PC_PRODUCT='" . mysql_real_escape_string($pc_product) . "' AND PC_TREE_NODE='" . mysql_real_escape_string($pc_tree_node) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

//---- PRODUCT -------------------------------------------------------------------------------------------------------------------------------------

function deleteProductFromTree($product, $treenode){
	$sql = "DELETE FROM prodcat WHERE PC_PRODUCT = '" . $product . "'";
	if($treenode != "ALL"){
		$sql .= " AND PC_TREE_NODE = '" . $treenode . "'";
	}
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();;
}

function getAllProducts(){
	$sql = "SELECT * FROM product";
	
	return FetchSqlAsObjectArray($sql);
}

function getProductDetails($product){
	$sql = "SELECT * FROM product ";
	$sql .= "WHERE PR_PRODUCT = '" . $product . "'";
	
	return FetchSqlAsObject($sql);
}

function checkProductExists($product){
	$sql = "SELECT * FROM product ";
	$sql .= "WHERE PR_PRODUCT = '" . $product . "'";
	$results = mysql_query($sql);
	if (mysql_num_rows($results) == 1){
		$checkproduct = mysql_fetch_object($results);
		if ($checkproduct->PR_PRODUCT == $product){return "true";}
	}
	
	return "false";
}

function getProductByName($productname){
	$sql = "SELECT * FROM product ";
	$sql .= "WHERE PR_NAME = '" . $productname . "'";
	
	return FetchSqlAsObject($sql);
}

function Create_Product($fields){
	// inserts a new product row
	$pr_product = $fields['pr_product'];
	$pr_name = $fields['pr_name'];
	$pr_sku = $fields['pr_sku'];
	$pr_desc_short = $fields['pr_desc_short'];
	$pr_desc_long = $fields['pr_desc_long'];
	$pr_image = $fields['pr_image'];
	$pr_image_folder = $fields['pr_image_folder'];
	$pr_image_alt = $fields['pr_image_alt'];
	$pr_desc_trade = $fields['pr_desc_trade'];
	$pr_weight = $fields['pr_weight'];
	$pr_quantity = $fields['pr_quantity'];
	$pr_selling = $fields['pr_selling'];
	$pr_trade = $fields['pr_trade'];
	$pr_tax = $fields['pr_tax'];
	$pr_shipping = $fields['pr_shipping'];
	$pr_taxexemption = $fields['pr_taxexemption'];
	$pr_shipping_apply = $fields['pr_shipping_apply'];
	$pr_option1 = $fields['pr_option1'];
	$pr_option2 = $fields['pr_option2'];
	$pr_option3 = $fields['pr_option3'];
	$pr_option4 = $fields['pr_option4'];
	$pr_user_string1 = $fields['pr_user_string1'];
	$pr_no_stock = $fields['pr_no_stock'];
	$pr_google_cat = $fields['pr_google_cat'];
	$pr_availability = $fields['pr_availability'];
	$pr_google_brand = $fields['pr_google_brand'];
	$pr_google_gtin = $fields['pr_google_gtin'];
	$pr_google_mpn = $fields['pr_google_mpn'];
	$pr_google_adwords_grouping = $fields['pr_google_adwords_grouping'];
	$pr_google_adwords_labels = $fields['pr_google_adwords_labels'];
	$pr_google_adwords_redirect = $fields['pr_google_adwords_redirect'];
	$pr_meta_title = $fields['pr_meta_title'];
	$pr_meta_desc = $fields['pr_meta_desc'];
	$pr_meta_keywords = $fields['pr_meta_keywords'];
	$pr_custom_head = $fields['pr_custom_head'];
	$pr_prod_wrap = $fields['pr_prod_wrap'];

	// write table row
	$sql = "INSERT INTO product (PR_PRODUCT, PR_NAME, PR_SKU, PR_DESC_SHORT, ";
	$sql .= "PR_DESC_LONG, PR_IMAGE, PR_IMAGE_ALT, ";
	$sql .= "PR_IMAGE_FOLDER,PR_DESC_TRADE, PR_WEIGHT, ";
	$sql .= "PR_QUANTITY, PR_SELLING, PR_TRADE, ";
	$sql .= "PR_TAX, PR_SHIPPING, PR_TAXEXEMPTION, ";
	$sql .= "PR_SHIPPING_APPLY, PR_OPTION1, PR_OPTION2, ";
	$sql .= "PR_OPTION3, PR_OPTION4, PR_USER_STRING1, PR_NO_STOCK, ";
	$sql .= "PR_GOOGLE_CAT,PR_AVAILABILITY, PR_GOOGLE_BRAND, ";
	$sql .= "PR_GOOGLE_GTIN,PR_GOOGLE_MPN, ";
	$sql .= "PR_GOOGLE_ADWORDS_GROUPING,PR_GOOGLE_ADWORDS_LABELS, PR_GOOGLE_ADWORDS_REDIRECT, ";
	$sql .= "PR_META_TITLE, PR_META_DESC, PR_META_KEYWORDS, PR_CUSTOM_HEAD, PR_PROD_WRAP)";
	$sql .= " VALUES ('" . mysql_real_escape_string($pr_product) . "', '" . htmlentities($pr_name, ENT_QUOTES) ."', '" . mysql_real_escape_string($pr_sku) . "', '" . htmlentities($pr_desc_short, ENT_QUOTES) . "', '";
	$sql .= htmlentities($pr_desc_long, ENT_QUOTES) . "', '" . mysql_real_escape_string($pr_image) . "', '" . htmlentities($pr_image_alt, ENT_QUOTES) . "', '";
	$sql .= mysql_real_escape_string($pr_image_folder) . "', '" . htmlentities($pr_desc_trade, ENT_QUOTES) . "', '" . mysql_real_escape_string($pr_weight) . "', '";
	$sql .= mysql_real_escape_string($pr_quantity) . "', '" . mysql_real_escape_string($pr_selling) . "', '" . mysql_real_escape_string($pr_trade) . "', '";
	$sql .= mysql_real_escape_string($pr_tax) . "', '" . mysql_real_escape_string($pr_shipping) . "', '" . mysql_real_escape_string($pr_taxexemption) . "', '";
	$sql .= mysql_real_escape_string($pr_shipping_apply) . "', '" . mysql_real_escape_string($pr_option1) . "', '" . mysql_real_escape_string($pr_option2) . "', '";
	$sql .= mysql_real_escape_string($pr_option3) . "', '" . mysql_real_escape_string($pr_option4) . "', '" . htmlentities($pr_user_string1) . "', '";
	$sql .= htmlentities($pr_no_stock, ENT_QUOTES) . "', '" . htmlentities($pr_google_cat, ENT_QUOTES) . "', '" . htmlentities($pr_availability, ENT_QUOTES) . "', '";
	$sql .= htmlentities($pr_google_brand, ENT_QUOTES) . "', '" . htmlentities($pr_google_gtin, ENT_QUOTES) . "', '" . htmlentities($pr_google_mpn, ENT_QUOTES) . "', '";
	$sql .= htmlentities($pr_google_adwords_grouping, ENT_QUOTES) . "', '" . htmlentities($pr_google_adwords_labels, ENT_QUOTES) . "', '" . htmlentities($pr_google_adwords_redirect, ENT_QUOTES) . "', '";
	$sql .= htmlentities($pr_meta_title, ENT_QUOTES) . "', '" . htmlentities($pr_meta_desc, ENT_QUOTES) . "', '" . htmlentities($pr_meta_keywords, ENT_QUOTES) . "', '";
	$sql .= htmlentities($pr_custom_head, ENT_QUOTES) . "', '" . htmlentities($pr_prod_wrap, ENT_QUOTES) . "')";
	$rows = mysql_query($sql);
	return mysql_affected_rows();
}

function Rewrite_Product($fields){
	// rewrites entire table row - NOTE amending the product code itself is disallowed!
	$pr_product = $fields['pr_product'];
	$pr_name = $fields['pr_name'];
	$pr_sku = $fields['pr_sku'];
	$pr_desc_short = $fields['pr_desc_short'];
	$pr_desc_long = $fields['pr_desc_long'];
	$pr_image = $fields['pr_image'];
	$pr_image_folder = $fields['pr_image_folder'];
	$pr_image_alt = $fields['pr_image_alt'];
	$pr_desc_trade = $fields['pr_desc_trade'];
	$pr_weight = $fields['pr_weight'];
	$pr_quantity = $fields['pr_quantity'];
	$pr_selling = $fields['pr_selling'];
	$pr_trade = $fields['pr_trade'];
	$pr_tax = $fields['pr_tax'];
	$pr_shipping = $fields['pr_shipping'];
	$pr_taxexemption = $fields['pr_taxexemption'];
	$pr_shipping_apply = $fields['pr_shipping_apply'];
	$pr_disable = $fields['pr_disable'];
	$pr_option1 = $fields['pr_option1'];
	$pr_option2 = $fields['pr_option2'];
	$pr_option3 = $fields['pr_option3'];
	$pr_option4 = $fields['pr_option4'];
	$pr_user_string1 = $fields['pr_user_string1'];
	$pr_no_stock = $fields['pr_no_stock'];
	$pr_google_cat = $fields['pr_google_cat'];
	$pr_availability = $fields['pr_availability'];
	$pr_google_brand = $fields['pr_google_brand'];
	$pr_google_gtin = $fields['pr_google_gtin'];
	$pr_google_mpn = $fields['pr_google_mpn'];
	$pr_google_adwords_grouping = $fields['pr_google_adwords_grouping'];
	$pr_google_adwords_labels = $fields['pr_google_adwords_labels'];
	$pr_google_adwords_redirect = $fields['pr_google_adwords_redirect'];
	$pr_google_condition = $fields['pr_google_condition'];
	$pr_meta_title = $fields['pr_meta_title'];
	$pr_meta_desc = $fields['pr_meta_desc'];
	$pr_meta_keywords = $fields['pr_meta_keywords'];
	$pr_custom_head = $fields['pr_custom_head'];
	$pr_prod_wrap = $fields['pr_prod_wrap'];

	$sql = "UPDATE product ";
	$sql .= "SET PR_NAME='" . htmlentities($pr_name, ENT_QUOTES) . "', PR_SKU='" . mysql_real_escape_string($pr_sku) . "',";
	$sql .= " PR_DESC_SHORT='" . htmlentities($pr_desc_short, ENT_QUOTES) . "', PR_DESC_LONG='" . htmlentities($pr_desc_long, ENT_QUOTES) . "',";
	$sql .= " PR_IMAGE='" . mysql_real_escape_string($pr_image) . "', PR_IMAGE_FOLDER='" . mysql_real_escape_string($pr_image_folder) . "',";
	$sql .= " PR_IMAGE_ALT='" . htmlentities($pr_image_alt, ENT_QUOTES) . "', PR_DESC_TRADE='" . htmlentities($pr_desc_trade, ENT_QUOTES) . "',";
	$sql .= " PR_WEIGHT='" . mysql_real_escape_string($pr_weight) . "', PR_QUANTITY='" . mysql_real_escape_string($pr_quantity) . "',";
	$sql .= " PR_SELLING='" . mysql_real_escape_string($pr_selling) . "', PR_TRADE='" . mysql_real_escape_string($pr_trade) . "',PR_TAX='" . mysql_real_escape_string($pr_tax) . "',";
	$sql .= " PR_SHIPPING='" . mysql_real_escape_string($pr_shipping) . "', PR_TAXEXEMPTION='" . mysql_real_escape_string($pr_taxexemption) . "',";
	$sql .= " PR_SHIPPING_APPLY='" . mysql_real_escape_string($pr_shipping_apply) . "', PR_DISABLE='" . mysql_real_escape_string($pr_disable) . "', PR_OPTION1='" . htmlentities($pr_option1, ENT_QUOTES) . "',";
	$sql .= " PR_OPTION2='" . htmlentities($pr_option2, ENT_QUOTES) . "', PR_OPTION3='" . htmlentities($pr_option3, ENT_QUOTES) . "',";
	$sql .= " PR_OPTION4='" . htmlentities($pr_option4, ENT_QUOTES) . "', PR_META_TITLE='" . htmlentities($pr_meta_title, ENT_QUOTES) . "',";
	$sql .= " PR_USER_STRING1='" . htmlentities($pr_user_string1, ENT_QUOTES) . "',";
	$sql .= " PR_NO_STOCK='" . htmlentities($pr_no_stock, ENT_QUOTES) . "', PR_GOOGLE_CAT='" . htmlentities($pr_google_cat, ENT_QUOTES) . "',";
	$sql .= " PR_AVAILABILITY='" . htmlentities($pr_availability, ENT_QUOTES) . "', PR_GOOGLE_BRAND='" . htmlentities($pr_google_brand, ENT_QUOTES) . "',";
	$sql .= " PR_GOOGLE_GTIN='" . htmlentities($pr_google_gtin, ENT_QUOTES) . "', PR_GOOGLE_MPN='" . htmlentities($pr_google_mpn, ENT_QUOTES) . "',";
	$sql .= " PR_GOOGLE_ADWORDS_GROUPING='" . htmlentities($pr_google_adwords_grouping, ENT_QUOTES) . "', PR_GOOGLE_ADWORDS_LABELS='" . htmlentities($pr_google_adwords_labels, ENT_QUOTES) . "',";
	$sql .= " PR_GOOGLE_ADWORDS_REDIRECT='" . htmlentities($pr_google_adwords_redirect, ENT_QUOTES) . "',";
	$sql .= " PR_GOOGLE_CONDITION='" . htmlentities($pr_google_condition, ENT_QUOTES) . "',";
	$sql .= " PR_META_DESC='" . htmlentities($pr_meta_desc, ENT_QUOTES) . "', PR_META_KEYWORDS='" . htmlentities($pr_meta_keywords, ENT_QUOTES) . "',";
	$sql .= " PR_CUSTOM_HEAD='" . htmlentities($pr_custom_head, ENT_QUOTES) . "', PR_PROD_WRAP='" . mysql_real_escape_string($pr_prod_wrap) . "',";
	$sql .= " PR_LAST_UPDATED='" . strftime("%Y-%m-%d %H:%M:%S", time()) . "' ";
	$sql .= "WHERE PR_PRODUCT='" . mysql_real_escape_string($pr_product) . "' ";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Rewrite_Product_Specific($product, $field, $data){
	// rewrites a specific field
	$sql = "UPDATE product ";
	$sql .= "SET " . $field . "='" . $data . "'";
	$sql .= " WHERE PR_PRODUCT='" . mysql_real_escape_string($product) . "' ";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Product($product){
	$sql = "DELETE FROM product WHERE PR_PRODUCT='" . $product . "' LIMIT 1";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}
// QUANTITY DISCOUNT -----------------------------------------------------------------------------------------------------------------------------
function Get_All_Qdiscounts(){
	$sql = "SELECT * FROM qtydisch";
	$sql .= " ORDER BY QDH_PRODUCT";
	
	return FetchSqlAsObjectArray($sql);
}

function Get_Qdiscount($product){
	$sql = "SELECT * FROM qtydisch";
	$sql .= " WHERE QDH_PRODUCT='" . $product . "'";
	
	return FetchSqlAsObject($sql);
}

function Get_Qdiscount_Lines($product){
	$sql = "SELECT * FROM qtydiscl LEFT JOIN qtydisch ON QDL_QDH_ID = QDH_ID";
	$sql .= " WHERE QDH_PRODUCT='" . $product . "' ORDER BY QDL_QTY";

	return FetchSqlAsObjectArray($sql);
}

function Create_Qdiscount_header($fields) {
	// create new quantity discount header
	$qdh_product = $fields['qdh_product'];
	$qdh_type = $fields['qdh_type'];
	$sql = "INSERT INTO qtydisch ";
	$sql .= "(qdh_product, qdh_type)";
	$sql .= " VALUES ('" . $qdh_product . "', '" . $qdh_type . "')";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Rewrite_Qdiscount_header($fields) {
	// rwrite quantity discount header
	$qdh_product = $fields['qdh_product'];
	$qdh_type = $fields['qdh_type'];
	$sql = "UPDATE qtydisch ";
	$sql .= "SET qdh_type='" . $qdh_type . "' ";
	$sql .= "WHERE qdh_product='" . $qdh_product . "' ";
	$sql .= "LIMIT 1";	
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Create_Qdiscount_line($fields) {
	//create quantity discount lines - delete all existing lines before recreating them
	$qdl_qdh_id = $fields['qdl_qdh_id'];
	$qdl_qty = $fields['qdl_qty'];
	$qdl_adjust = $fields['qdl_adjust'];
	
	$sql = "INSERT INTO qtydiscl ";
	$sql .= "(qdl_qdh_id, qdl_qty, qdl_adjust)";
	$sql .= " VALUES ('" . $qdl_qdh_id . "', '" . $qdl_qty . "', '" . $qdl_adjust . "')";
	mysql_query($sql);

	return mysql_affected_rows();
}

function Delete_Qdiscount_header($fields){
	$qdh_product = $fields['qdh_product'];
	$sql = "DELETE FROM qtydisch WHERE QDH_PRODUCT = '" . $qdh_product . "'";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Qdiscount_lines($fields){
	$qdl_qdh_id = $fields['qdl_qdh_id'];
	$sql = "DELETE FROM qtydiscl WHERE QDL_QDH_ID = '" . $qdl_qdh_id . "'";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();
}

function Get_QD_Matrix_Selling($preferences, $product, $type, $adjust){
	switch($type){
		case "PP": //Price Point
			//get VAT inclusive price = PR_SELLING * VAT rate
			if($product->PR_TAX > 0){
				$vatrate = $product->PR_TAX;
			}else{
				$vatrate = $preferences->PREF_VAT;
			}
			if($preferences->PREF_SELL_EXVAT == "N"){
				//Display Prices are to include VAT so add it on
				$selling = addVAT($adjust, $vatrate);
			}else{
				$selling = $adjust;
			}
			
			break;
		case "PC": //Percentage Reduction
			//first get current product selling price
			$selling = $product->PR_SELLING;
			//get VAT inclusive price = PR_SELLING * VAT rate
			if($product->PR_TAX > 0){
				$vatrate = $product->PR_TAX;
			}else{
				$vatrate = $preferences->PREF_VAT;
			}
			if($preferences->PREF_SELL_EXVAT == "N"){
				//Display Prices are to include VAT so add it on
				$vatinc = addVAT($selling, $vatrate);
			}else{
				$vatinc = $selling;
			}
			//now apply percentage reduction
			$selling =((($vatinc) * 100 * (100 - $adjust)) + 5)/10000;
			$selling = round($selling, 2);
						
			break;
		case "V":  //Value Reduction
			//first get current product selling price
			$selling = $product->PR_SELLING;
			//get VAT inclusive price = PR_SELLING * VAT rate
			if($product->PR_TAX > 0){
				$vatrate = $product->PR_TAX;
			}else{
				$vatrate = $preferences->PREF_VAT;
			}
			if($preferences->PREF_SELL_EXVAT == "N"){
				//Display Prices are to include VAT so add it on
				$vatinc = addVAT($selling, $vatrate);
			}else{
				$vatinc = $selling;
			}
			$selling = $vatinc - $adjust;
			
			break;
		default:
			break;
	}
	
	return $selling;
}

//---- STOCK -------------------------------------------------------------------------------------------------------------------------------------
function Delete_All_Stock(){
	//empty the TABLE stock
	$sql = "TRUNCATE TABLE stock";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();
}

function getStock($sku){
	$sql = "SELECT * FROM stock" ;
	$sql .= " WHERE STK_PR_SKU = '" . $sku . "'";
	//echo "getStock - " . $sql . "<br/>";
	return FetchSqlAsObjectArray($sql);
}

function Create_Stock($fields){
	// inserts a new stock row
	$stk_pr_sku = $fields['stk_pr_sku'];
	$stk_option1_sku = $fields['stk_option1_sku'];
	$stk_option2_sku = $fields['stk_option2_sku'];
	$stk_option3_sku = $fields['stk_option3_sku'];
	$stk_option4_sku = $fields['stk_option4_sku'];
	$stk_pr_product = $fields['stk_pr_product'];

	// write table row
	$sql = "INSERT INTO stock (STK_PR_SKU, ";
	$sql .= "STK_OPTION1_SKU, STK_OPTION2_SKU, STK_OPTION3_SKU, STK_OPTION4_SKU, STK_PR_PRODUCT)";
	$sql .= " VALUES ('" . mysql_real_escape_string($stk_pr_sku) . "', '";
	$sql .= mysql_real_escape_string($stk_option1_sku) . "', '" . mysql_real_escape_string($stk_option2_sku) . "', '";
	$sql .= mysql_real_escape_string($stk_option3_sku) . "', '" . mysql_real_escape_string($stk_option4_sku) . "', '";
	$sql .= mysql_real_escape_string($stk_pr_product) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Stock($fields, $level){
	// rewrites entire table row
	$stk_pr_sku = $fields['stk_pr_sku'];
	$stk_option1_sku = $fields['stk_option1_sku'];
	$stk_option2_sku = $fields['stk_option2_sku'];
	$stk_option3_sku = $fields['stk_option3_sku'];
	$stk_option4_sku = $fields['stk_option4_sku'];
	$stk_pr_product = $fields['stk_pr_product'];

	$sql = "UPDATE stock ";
	$sql .= "SET STK_PR_SKU='" . mysql_real_escape_string($stk_pr_sku) . "', STK_PR_PRODUCT='" . mysql_real_escape_string($stk_pr_product) . "',";
	$sql .= " STK_OPTION1_SKU='" . mysql_real_escape_string($stk_option1_sku) . "', STK_OPTION2_SKU='" . mysql_real_escape_string($stk_option2_sku) . "',";
	$sql .= " STK_OPTION3_SKU='" . mysql_real_escape_string($stk_option3_sku) . "', STK_OPTION4_SKU='" . mysql_real_escape_string($stk_option4_sku) . "' ";
	$sql .= "WHERE STK_PR_SKU='" . mysql_real_escape_string($stk_pr_sku) . "' ";
	if($level >= 2){
		$sql .= " AND STK_OPTION1_SKU='" . mysql_real_escape_string($stk_option1_sku) . "'";
	}
	if($level >=3){
		$sql .= " AND STK_OPTION2_SKU='" . mysql_real_escape_string($stk_option2_sku) . "'";
	}
	if($level >=4){
		$sql .= " AND STK_OPTION3_SKU='" . mysql_real_escape_string($stk_option3_sku) . "'";
	}
	mysql_query($sql);
	//echo "Rewrite_Stock - " . $sql . "<br/>";
	return mysql_affected_rows();
}

//---- PRODADD -------------------------------------------------------------------------------------------------------------------------------------

function Confirm_AdditionalImage($product, $position){
	$sql = "SELECT * FROM prodadd";
	$sql .= " WHERE PRA_PRODUCT = '" . $product . "' AND PRA_POSITION = '" . $position . "'";
	$results = mysql_query($sql);
	
	return mysql_num_rows($results);
}

function getAdditionalImages($product){
	$sql = "SELECT * FROM prodadd ";
	$sql .= "WHERE PRA_PRODUCT = '" . $product . "'";
	$sql .= " ORDER BY PRA_POSITION";
	
	return FetchSqlAsObjectArray($sql);
}

function Update_Prodadd($fields){
	//bugfix 14/11/11 - if additional image is deleted using amend product then the row is not deleted from prodadd so...
	//delete all existing prodadd entries against a product before adding the current requirements
	$rows = Delete_Prodadd($fields['pra_product']);
	
	//check whether Prodadd already has entries - if it does then update otherwise insert the new entry
	$rowswritten = 0;
	$message = "";
	//Additional Image 1
	//==================
	$pra_product = $fields['pra_product'];
	$position_add1 = $fields['position_add1'];
	$image_name_add1 = $fields['image_name_add1'];
	$image_folder_add1 = $fields['image_folder_add1'];
	$image_alt_add1 = $fields['image_alt_add1'];
	if($image_name_add1 != "" and $image_name_add1 != "no-image.jpg"){
		$rows = Confirm_AdditionalImage($pra_product, 2);
		if ($rows == 1){
			//additional image 1 already set so update it
			$sql = "UPDATE prodadd ";
			$sql .= "SET pra_image='" . mysql_real_escape_string($image_name_add1) . "',";
			$sql .= " pra_image_folder='" . mysql_real_escape_string($image_folder_add1) . "', pra_image_alt='" . mysql_real_escape_string($image_alt_add1) . "' ";
			$sql .= "WHERE pra_position='2' AND pra_product='" . mysql_real_escape_string($pra_product) . "'";
			mysql_query($sql);
			if(mysql_affected_rows() > 1){
				$message = "Error Updating Additional Image 1 - PLEASE CONTACT SHOPFITTER!!!";
			}
			$rowswritten = mysql_affected_rows();
		}else{
			if ($rows == 0){
				//no additional image 1 currently set so write a new row
				$sql = "INSERT INTO prodadd (PRA_POSITION, PRA_PRODUCT, PRA_IMAGE, PRA_IMAGE_FOLDER, PRA_IMAGE_ALT)";
				$sql .= " VALUES ('2', '" . mysql_real_escape_string($pra_product) . "', '" . mysql_real_escape_string($image_name_add1) . "', '";
				$sql .= mysql_real_escape_string($image_folder_add1) . "', '" . mysql_real_escape_string($image_alt_add1) . "')";
				mysql_query($sql);
				if(mysql_affected_rows() != 1){
					$message = "Error Inserting Additional Image 1 - PLEASE CONTACT SHOPFITTER!!!";
				}
				$rowswritten = mysql_affected_rows();
			}else{
				$message = "Error creating new Additional Image 1 record - PLEASE CONTACT SHOPFITTER!!!";
			}
		}
	}
	//Additional Image 2
	//==================
	$position_add2 = $fields['position_add2'];
	$image_name_add2 = $fields['image_name_add2'];
	$image_folder_add2 = $fields['image_folder_add2'];
	$image_alt_add2 = $fields['image_alt_add2'];
	if($image_name_add2 != "" and $image_name_add2 != "no-image.jpg"){
		$rows = Confirm_AdditionalImage($pra_product, 3);
		if ($rows == 1){
			//additional image 2 already set so update it
			$sql = "UPDATE prodadd ";
			$sql .= "SET pra_image='" . mysql_real_escape_string($image_name_add2) . "',";
			$sql .= " pra_image_folder='" . mysql_real_escape_string($image_folder_add2) . "', pra_image_alt='" . mysql_real_escape_string($image_alt_add2) . "' ";
			$sql .= "WHERE pra_position='3' AND pra_product='" . mysql_real_escape_string($pra_product) . "'";
			mysql_query($sql);
			if(mysql_affected_rows() > 1){
				$message = "Error Updating Additional Image 2 - PLEASE CONTACT SHOPFITTER!!!";
			}
			$rowswritten = $rowswritten + mysql_affected_rows();
		}else{
			if ($rows == 0){
				//no additional image 2 currently set so write a new row
				$sql = "INSERT INTO prodadd (PRA_POSITION, PRA_PRODUCT, PRA_IMAGE, PRA_IMAGE_FOLDER, PRA_IMAGE_ALT)";
				$sql .= " VALUES ('3', '" . mysql_real_escape_string($pra_product) . "', '" . mysql_real_escape_string($image_name_add2) . "', '";
				$sql .= mysql_real_escape_string($image_folder_add2) . "', '" . mysql_real_escape_string($image_alt_add2) . "')";
				mysql_query($sql);
				if(mysql_affected_rows() != 1){
					$message = "Error Inserting Additional Image 2 - PLEASE CONTACT SHOPFITTER!!!";
				}
				$rowswritten = $rowswritten + mysql_affected_rows();
			}else{
				$message = "Error creating new Additional Image 1 record - PLEASE CONTACT SHOPFITTER!!!";
			}
		}
	}
	$fields = array("rows"=>$rowswritten, "message"=>$message);
	return $fields;

}

function Update_Prodadd_multi($fields){
	//bugfix 14/11/11 - if additional image is deleted using amend product then the row is not deleted from prodadd so...
	$rowswritten = 0;
	$message = "";
	//Additional Images
	//=================
	$pra_product = $fields['pra_product'];
	$no_addl_images = $fields['no_addl_images'];
	for($i = 1; $i <= $no_addl_images; $i++){
		${"position_add" . $i} = $fields['position_add' . $i];
		${"image_name_add" . $i} = $fields['image_name_add' . $i];
		${"image_folder_add" . $i} = $fields['image_folder_add' . $i];
		${"image_alt_add" . $i} = $fields['image_alt_add' . $i];
	}
	//delete all existing prodadd entries against a product before adding the current requirements
	$rows = Delete_Prodadd_multi($fields['pra_product'], $no_addl_images);
	
	for($i = 1; $i <= $no_addl_images; $i++){
		if(${"image_name_add" . $i} != "" and ${"image_name_add" . $i} != "no-image.jpg"){
			$sql = "INSERT INTO prodadd (PRA_POSITION, PRA_PRODUCT, PRA_IMAGE, PRA_IMAGE_FOLDER, PRA_IMAGE_ALT)";
			$sql .= " VALUES ('" . ${"position_add" . $i} . "', '" . mysql_real_escape_string($pra_product) . "', '" . mysql_real_escape_string(${"image_name_add" . $i}) . "', '";
			$sql .= mysql_real_escape_string(${"image_folder_add" . $i}) . "', '" . mysql_real_escape_string(${"image_alt_add" . $i}) . "')";
			mysql_query($sql);
			if(mysql_affected_rows() != 1){
				$message = "Error Inserting Additional Image 1 - PLEASE CONTACT SHOPFITTER!!!";
			}
			$rowswritten += mysql_affected_rows();
		}
	}
	$fields = array("rows"=>$rowswritten, "message"=>$message);
	return $fields;
}

function Delete_Prodadd($product){
	//note at this point we are limiting the number of additional images to 2 ie. there should be no more than 2 entries on Prodadd
	$sql = "DELETE FROM prodadd WHERE PRA_PRODUCT='" . $product . "' LIMIT 2";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Delete_Prodadd_multi($product, $no_addl_images){
	$sql = "DELETE FROM prodadd WHERE PRA_PRODUCT='" . $product . "' LIMIT " . $no_addl_images;
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

//---- ADDL_PRODUCTS -------------------------------------------------------------------------------------------------------------------------------------
function getAdditionalProducts($product){
	$sql = "SELECT * FROM addl_products LEFT JOIN product ON AP_ADDITIONAL = PR_PRODUCT ";
	$sql .= "WHERE AP_PRODUCT = '" . $product . "' ";
	$sql .= "AND PR_DISABLE = 'N'";
	$sql .= "ORDER BY AP_POSITION ASC";
	
	return FetchSqlAsObjectArray($sql);
}

function Write_ADDL_PRODUCTS($fields){
	// inserts a new hotspot row
	$ap_product = $fields['ap_product'];
	$ap_additional = $fields['ap_additional'];
	$ap_position = $fields['ap_position'];

	// write table row
	$sql = "INSERT INTO addl_products (AP_PRODUCT, AP_ADDITIONAL, AP_POSITION)";
	$sql .= " VALUES ('" . mysql_real_escape_string($ap_product) . "', '" . mysql_real_escape_string($ap_additional) . "', '" . mysql_real_escape_string($ap_position) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_ADDL_PRODUCTS($fields){
	// rewrites entire table row
	$ap_product = $fields['ap_product'];
	$ap_additional = $fields['ap_additional'];
	$ap_position = $fields['ap_position'];

	$sql = "UPDATE addl_products ";
	$sql .= "SET AP_PRODUCT='" . mysql_real_escape_string($ap_product) . "',";
	$sql .= " AP_ADDITIONAL='" . mysql_real_escape_string($ap_additional) . "', AP_POSITION='" . mysql_real_escape_string($ap_position) . "' ";
	$sql .= "WHERE AP_PRODUCT='" . mysql_real_escape_string($ap_product) . "' AND AP_ADDITIONAL='" . mysql_real_escape_string($ap_additional) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_ADDL_PRODUCTS($ap_product, $ap_additional){
	$sql = "DELETE FROM addl_products WHERE AP_PRODUCT='" . $ap_product . "'";
	if($ap_additional == "ALL"){
		$sql .= " OR AP_ADDITIONAL='" . $ap_product . "' LIMIT 1";
	}else{
		$sql .= " AND AP_ADDITIONAL='" . $ap_additional . "' LIMIT 1";
	}
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

//---- HOTSPOTS -------------------------------------------------------------------------------------------------------------------------------------

function getHotspots($code){
	$sql = "SELECT * FROM hotspots ";
	$sql .= "WHERE HS_CODE='" . $code . "'";
	
	return FetchSqlAsObjectArray($sql);
	
}

function getHotspot($code,$hotnumber){
	$sql = "SELECT * FROM hotspots ";
	$sql .= "WHERE HS_CODE='" . $code . "' AND HS_NUMBER = " . $hotnumber;
	
	return FetchSqlAsObject($sql);
	
}

function checkHotspotExists($code, $number){
	//check category exists and is not already a subcategory of the currently selected category/tree node
	$sql = "SELECT * FROM hotspots ";
	$sql .= "WHERE HS_CODE = '" . $code . "' AND HS_NUMBER = '" . $number . "'";
	$results = mysql_query($sql);
	if (mysql_num_rows($results) == 1){
		return "true";
	}
	
	return "false";
}

function Create_Hotspot($fields){
	// inserts a new hotspot row
	$hs_code = $fields['hs_code'];
	$hs_number = $fields['hs_number'];
	$hs_data = $fields['hs_data'];

	// write table row
	$sql = "INSERT INTO hotspots (HS_CODE, HS_NUMBER, HS_DATA)";
	$sql .= " VALUES ('" . mysql_real_escape_string($hs_code) . "', '" . mysql_real_escape_string($hs_number) . "', '" . htmlentities($hs_data, ENT_QUOTES) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Hotspot($fields){
	// rewrites entire table row
	$hs_code = $fields['hs_code'];
	$hs_number = $fields['hs_number'];
	$hs_data = $fields['hs_data'];

	$sql = "UPDATE hotspots ";
	$sql .= "SET HS_CODE='" . mysql_real_escape_string($hs_code) . "',";
	$sql .= " HS_NUMBER='" . mysql_real_escape_string($hs_number) . "', HS_DATA='" . htmlentities($hs_data, ENT_QUOTES) . "' ";
	$sql .= "WHERE HS_CODE='" . mysql_real_escape_string($hs_code) . "' AND HS_NUMBER='" . $hs_number . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Hotspots($code){
	//note at this point we are limiting the number of Hotspots to 3 ie. there should be no more than 3 entries on hotspots
	$sql = "DELETE FROM hotspots WHERE HS_CODE='" . $code . "' LIMIT 3";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

//---- SELECTION -------------------------------------------------------------------------------------------------------------------------------------

function getAllSelections() {
	$sql = "SELECT * FROM selection";

	return FetchSqlAsObjectArray($sql);	
}

function getGeneralSelections() {
	// get general selections only
	$sql = "SELECT * FROM selection";
	$sql .= " WHERE SE_PRODUCT='GENERAL' AND SE_EXCLUDE = 'N'";

	return FetchSqlAsObjectArray($sql);	
}

function getAllSelectionsWithProduct($product) {
	//get both general and product specific selection boxes
	$sql = "SELECT * FROM selection";
	$sql .= " WHERE SE_PRODUCT='" . "GENERAL" . "' OR SE_PRODUCT='" . $product . "' AND SE_EXCLUDE='N'";
	$sql .= " ORDER BY SE_PRODUCT";

	return FetchSqlAsObjectArray($sql);	
}

function getAllProductSelections($product) {
	//get selection boxes only if they contain options against $product
	$sql = "SELECT * FROM selection";
	$sql .= " WHERE SE_PRODUCT='" . $product . "'";

	return FetchSqlAsObjectArray($sql);	
}

function getSelection($selection_id) {
	//get specific selection
	$sql = "SELECT * FROM selection";
	$sql .= " WHERE SE_ID = '" .  $selection_id . "'";
	return FetchSqlAsObject($sql);
}

function getSelectionId($name, $product) {
	//gets the selection box id
	$sql = "SELECT * FROM selection";
	$sql .= " WHERE SE_NAME = '" .  htmlentities($name, ENT_QUOTES) . "'";
	$sql .= " AND SE_PRODUCT = '" . $product . "'";
	return FetchSqlAsObject($sql);
}

function Create_Selection($fields){
	// inserts a new Selection row
	$se_name = $fields['se_name'];
	$se_label = $fields['se_label'];
	$se_exclude = $fields['se_exclude'];
	$se_product = $fields['se_product'];
	$se_attribute1 = $fields['se_attribute1'];
	$se_attribute2 = $fields['se_attribute2'];
	$se_attribute3 = $fields['se_attribute3'];

	// write table row
	$sql = "INSERT INTO selection (SE_NAME, SE_LABEL, SE_EXCLUDE, SE_PRODUCT, SE_ATTRIBUTE1, SE_ATTRIBUTE2, SE_ATTRIBUTE3)";
	$sql .= " VALUES ('" . htmlentities($se_name, ENT_QUOTES) . "', '" . mysql_real_escape_string($se_label) . "', '" . mysql_real_escape_string($se_exclude) . "', '" . htmlentities($se_product,ENT_QUOTES) . "', '";
	$sql .= mysql_real_escape_string($se_attribute1) . "', '" . mysql_real_escape_string($se_attribute2) . "', '" . mysql_real_escape_string($se_attribute3) .  "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Selection($fields){
	// rewrites entire table row
	$se_id = $fields['se_id'];
	$se_name = $fields['se_name'];
	$se_label = $fields['se_label'];
	$se_exclude = $fields['se_exclude'];
	$se_product = $fields['se_product'];
	$se_attribute1 = $fields['se_attribute1'];
	$se_attribute2 = $fields['se_attribute2'];
	$se_attribute3 = $fields['se_attribute3'];

	$sql = "UPDATE selection ";
	$sql .= "SET SE_NAME='" . htmlentities($se_name, ENT_QUOTES) . "', SE_LABEL='" . htmlentities($se_label, ENT_QUOTES) . "',";
	$sql .= " SE_EXCLUDE='" . mysql_real_escape_string($se_exclude) . "', SE_PRODUCT='" . htmlentities($se_product, ENT_QUOTES) . "',";
	$sql .= " SE_ATTRIBUTE1='" . mysql_real_escape_string($se_attribute1) . "', SE_ATTRIBUTE2='" . mysql_real_escape_string($se_attribute2) . "',";
	$sql .= " SE_ATTRIBUTE3='" . mysql_real_escape_string($se_attribute3) . "'";
	$sql .= " WHERE SE_ID='" . mysql_real_escape_string($se_id) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Selection($selection){
	//delete option from selection box
	$sql = "DELETE FROM selection WHERE SE_ID='" . mysql_real_escape_string($selection) . "' LIMIT 1";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function DeleteSelectionFromProduct($selection){
	//delete option from selection box
	$sql = "SELECT * FROM product";
	$sql .= " WHERE PR_OPTION1='" . $selection . "' OR PR_OPTION2='" . $selection . "' OR PR_OPTION3='" . $selection . "' OR PR_OPTION4='" . $selection . "'";
	$active =  FetchSqlAsObjectArray($sql);
	$cntr1 = 0; $message = "";
	foreach($active as $a){
		if($a->PR_OPTION1 == $selection){
			$rows = Rewrite_Product_Specific($a->PR_PRODUCT, "PR_OPTION1", 0);
		}
		if($a->PR_OPTION2 == $selection){
			$rows = Rewrite_Product_Specific($a->PR_PRODUCT, "PR_OPTION2", 0);
		}
		if($a->PR_OPTION3 == $selection){
			$rows = Rewrite_Product_Specific($a->PR_PRODUCT, "PR_OPTION3", 0);
		}
		if($a->PR_OPTION4 == $selection){
			$rows = Rewrite_Product_Specific($a->PR_PRODUCT, "PR_OPTION4", 0);
		}
		if($rows != 1){
			$message = "FAILED TO REMOVE DELETED OPTION FROM PRODUCT " . $active->PR_PRODUCT . " - Please contact Shopfitter!!!   ";
			break;
		}else{
			$cntr1 ++;
		}
	}
	$message .= "Option removed from " . $cntr1 . " products";
	return $message;
}

//---- OPTIONS -------------------------------------------------------------------------------------------------------------------------------------

function getOptions($selection, $exclude) {
	//gets all options against the selection OBJECT
	$sql = "SELECT * FROM options";
	$sql .= " WHERE OP_SE_ID = '" . $selection->SE_ID . "'";
	if($exclude != "All"){
		$sql .= " AND OP_EXCLUDE = '" . $exclude . "'";
	}
	$sql .= " ORDER BY OP_NUMBER";

	return FetchSqlAsObjectArray($sql);
}

function getOptions_With_Value($select_id){
	$sql = "SELECT * FROM options LEFT JOIN selection ON OP_SE_ID = SE_ID";
	$sql .= " WHERE ";
	$sql .= " SE_ID = '" . $select_id . "' AND SE_EXCLUDE = 'N' AND OP_VALUE <> '0.00'";
	
	return FetchSqlAsObjectArray($sql);
}

function Create_Option($fields){
	// inserts a new Option row
	$op_se_id = $fields['op_se_id'];
	$op_name = $fields['op_name'];
	$op_number = $fields['op_number'];
	$op_text = $fields['op_text'];
	$op_value = $fields['op_value'];
	$op_selected = $fields['op_selected'];
	$op_exclude = $fields['op_exclude'];
	$op_product = $fields['op_product'];
	$op_sku = $fields['op_sku'];
	$op_attribute_value1 = $fields['op_attribute_value1'];
	$op_attribute_value2 = $fields['op_attribute_value2'];
	$op_attribute_value3 = $fields['op_attribute_value3'];

	// write table row
	$sql = "INSERT INTO options (OP_SE_ID, OP_NAME, OP_NUMBER, OP_TEXT, OP_VALUE, OP_SELECTED, OP_EXCLUDE, OP_PRODUCT, OP_SKU, OP_ATTRIBUTE_VALUE1, OP_ATTRIBUTE_VALUE2, OP_ATTRIBUTE_VALUE3)";
	$sql .= " VALUES ('" . mysql_real_escape_string($op_se_id) . "', '" . htmlentities($op_name, ENT_QUOTES) . "', '" . mysql_real_escape_string($op_number) . "', '";
	$sql .= mysql_real_escape_string($op_text) . "', '" . mysql_real_escape_string($op_value) . "', '" . mysql_real_escape_string($op_selected) . "', '";
	$sql .= mysql_real_escape_string($op_exclude) . "', '" . mysql_real_escape_string($op_product) . "', '" . htmlentities($op_sku, ENT_QUOTES) . "', '";
	$sql .= mysql_real_escape_string($op_attribute_value1) . "', '" . mysql_real_escape_string($op_attribute_value2) . "', '" . mysql_real_escape_string($op_attribute_value3) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Option($fields){
	// rewrites entire table row
	$op_se_id = $fields['op_se_id'];
	$name_original = $fields['name_original'];
	$op_name = $fields['op_name'];
	$op_number = $fields['op_number'];
	$op_text = $fields['op_text'];
	$op_value = $fields['op_value'];
	$op_selected = $fields['op_selected'];
	$op_exclude = $fields['op_exclude'];
	$op_product = $fields['op_product'];
	$op_sku = $fields['op_sku'];
	$op_attribute_value1 = $fields['op_attribute_value1'];
	$op_attribute_value2 = $fields['op_attribute_value2'];
	$op_attribute_value3 = $fields['op_attribute_value3'];

	$sql = "UPDATE options ";
	$sql .= "SET OP_NAME='" . htmlentities($op_name, ENT_QUOTES) . "', OP_NUMBER='" . mysql_real_escape_string($op_number) . "',";
	$sql .= " OP_TEXT='" . mysql_real_escape_string($op_text) . "', OP_VALUE='" . mysql_real_escape_string($op_value) . "',";
	$sql .= " OP_SELECTED='" . mysql_real_escape_string($op_selected) . "', OP_EXCLUDE='" . mysql_real_escape_string($op_exclude) . "',";
	$sql .= " OP_PRODUCT='" . mysql_real_escape_string($op_product) . "', OP_SKU='" . htmlentities($op_sku, ENT_QUOTES) . "',";
	$sql .= " OP_ATTRIBUTE_VALUE1='" . mysql_real_escape_string($op_attribute_value1) . "', OP_ATTRIBUTE_VALUE2='" . mysql_real_escape_string($op_attribute_value2) . "',";
	$sql .= " OP_ATTRIBUTE_VALUE3='" . mysql_real_escape_string($op_attribute_value3) . "'";
	$sql .= " WHERE OP_SE_ID='" . mysql_real_escape_string($op_se_id) . "' AND OP_NAME='" . htmlentities($name_original, ENT_QUOTES) . "'";
	mysql_query($sql);
	return mysql_affected_rows();
}

function Delete_Option($selection, $option){
	//delete option from selection box
	$sql = "DELETE FROM options WHERE OP_SE_ID='" . mysql_real_escape_string($selection) . "' AND OP_NAME='" . htmlentities($option, ENT_QUOTES) . "'";
	$sql .= " LIMIT 1";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Delete_All_Options($selection){
	//delete option from selection box
	$sql = "DELETE FROM options WHERE OP_SE_ID='" . mysql_real_escape_string($selection) . "'";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Check_Option_Values($product){
	$p = getProductDetails($product);
	for($i = 1; $i <= 4; $i++){
		$field = "PR_OPTION" . $i;
		if($p->$field != ""){
			$options = getOptions_With_Value($p->$field);
			if(!empty($options)){
				return true;
			}
		}
	}
	return false;
}

function Clear_Option_Tag($selection_id, $attr_no){
	$sql = "UPDATE options ";
	$sql .= "SET OP_ATTRIBUTE_VALUE" . $attr_no . " = 0 ";
	$sql .= "WHERE OP_SE_ID = '" . $selection_id . "'";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();
}

//---- ATTRIBUTES -------------------------------------------------------------------------------------------------------------------------------------
function getAllAttributes() {
	$sql = "SELECT * FROM attribute ORDER BY AT_POSITION";

	return FetchSqlAsObjectArray($sql);	
}
function Create_Attribute($fields){
	// inserts a new Selection row
	$at_search_name = $fields['at_search_name'];
	$at_name = $fields['at_name'];
	$at_position = $fields['at_position'];

	// write table row
	$sql = "INSERT INTO attribute (AT_SEARCH_NAME, AT_NAME, AT_POSITION)";
	$sql .= " VALUES ('" . htmlentities($at_search_name, ENT_QUOTES) . "', '" . htmlentities($at_name, ENT_QUOTES) . "', '" . mysql_real_escape_string($at_position) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function getAttributeId($name) {
	//gets the selection box id
	$sql = "SELECT * FROM attribute";
	$sql .= " WHERE AT_NAME = '" .  htmlentities($name, ENT_QUOTES) . "'";

	return FetchSqlAsObject($sql);
}

function getAttribute($attribute_id) {
	//get specific attribute
	$sql = "SELECT * FROM attribute";
	$sql .= " WHERE AT_ID = '" .  $attribute_id . "'";
	return FetchSqlAsObject($sql);
}

function Rewrite_Attribute($fields){
	// rewrites entire table row
	$at_id = $fields['at_id'];
	$at_search_name = $fields['at_search_name'];
	$at_name = $fields['at_name'];
	$at_position = $fields['at_position'];

	$sql = "UPDATE attribute ";
	$sql .= "SET AT_SEARCH_NAME='" . htmlentities($at_search_name, ENT_QUOTES) . "', AT_NAME='" . htmlentities($at_name, ENT_QUOTES) . "', AT_POSITION='" . mysql_real_escape_string($at_position) . "' ";
	$sql .= "WHERE AT_ID='" . mysql_real_escape_string($at_id) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Attribute($attribute){
	//delete Atribite header
	$sql = "DELETE FROM attribute WHERE AT_ID='" . mysql_real_escape_string($attribute) . "' LIMIT 1";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

//---- ATTRIBUTE VALUE --------------------------------------------------------------------------------------------------------------------------------
function getAttributeValue($id) {
	//get specific attribute_value
	$sql = "SELECT * FROM attribute_value";
	$sql .= " WHERE AV_ID = '" .  $id . "'";
	return FetchSqlAsObject($sql);
}

function getAttributeValues($attribute) {
	//gets all values against the attribute OBJECT
	$sql = "SELECT * FROM attribute_value";
	$sql .= " WHERE AV_AT_ID = '" . $attribute->AT_ID . "'";

	$sql .= " ORDER BY AV_POSITION";

	return FetchSqlAsObjectArray($sql);
}

function Create_Attribute_Value($fields){
	// inserts a new value row
	$av_at_id = $fields['av_at_id'];
	$av_name = $fields['av_name'];
	$av_position = $fields['av_position'];

	// write table row
	$sql = "INSERT INTO attribute_value (AV_AT_ID, AV_NAME, AV_POSITION)";
	$sql .= " VALUES ('" . mysql_real_escape_string($av_at_id) . "', '" . htmlentities($av_name, ENT_QUOTES) . "', '" . mysql_real_escape_string($av_position) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Delete_Attribute_Value($attribute, $value){
	//delete value from attribute search box
	$sql = "DELETE FROM attribute_value WHERE AV_AT_ID='" . mysql_real_escape_string($attribute) . "' AND AV_NAME='" . htmlentities($value, ENT_QUOTES) . "'";
	$sql .= " LIMIT 1";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Attribute_Value($fields){
	// rewrites entire table row
	$av_at_id = $fields['av_at_id'];
	$name_original = $fields['name_original'];
	$av_name = $fields['av_name'];
	$av_position = $fields['av_position'];

	$sql = "UPDATE attribute_value ";
	$sql .= "SET AV_NAME='" . htmlentities($av_name, ENT_QUOTES) . "', AV_POSITION='" . mysql_real_escape_string($av_position) . "' ";
	$sql .= "WHERE AV_AT_ID='" . mysql_real_escape_string($av_at_id) . "' AND AV_NAME='" . htmlentities($name_original, ENT_QUOTES) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_All_Attribute_Values($attribute){
	//delete all values from attribute box
	$sql = "DELETE FROM attribute_value WHERE AV_AT_ID='" . mysql_real_escape_string($attribute) . "'";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

//---- AREADATA ---------------------------------------------------------------------------------------------------------------------------------------
function getAllAreadata(){
	//gets all enabled information entries (Top Menu Options)
	$sql = "SELECT * FROM areadata";
	$sql .= " ORDER BY AR_AREA";

	return FetchSqlAsObjectArray($sql);
}

function getAreadataPage($area){
	//gets areadata page
	$sql = "SELECT * FROM areadata";
	$sql .= " WHERE AR_AREA='" . $area . "'";
	
	return FetchSqlAsObject($sql);
}

function Rewrite_Areadata($fields){
	// rewrites entire table row
	$ar_area = $fields['ar_area'];
	$ar_data = $fields['ar_data'];
	
	$sql = "UPDATE areadata ";
	$sql .= "SET AR_DATA='" . htmlentities($ar_data, ENT_QUOTES) . "'";
	$sql .= " WHERE AR_AREA='" . htmlentities($ar_area, ENT_QUOTES) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

//---- INFORMATION -------------------------------------------------------------------------------------------------------------------------------------
function getAllInformation($enabled, $edit){
	//gets all enabled information entries (Top Menu Options)
	$sql = "SELECT * FROM information";
	if($enabled != "All"){
		$sql .= " WHERE IN_ENABLED = '" . $enabled . "'";
	}
	if($edit != "All"){
		if($enabled == "All"){$sql .= " WHERE";}
		if($enabled != "All"){$sql .= " AND";}
		$sql .= " IN_EDIT = '" . $edit . "'";
	}
	$sql .= " ORDER BY IN_POSITION";

	return FetchSqlAsObjectArray($sql);
}

function getInformationPage($name){
	//gets information page
	$sql = "SELECT * FROM information";
	$sql .= " WHERE IN_PAGE='" . $name . "'";
	
	return FetchSqlAsObject($sql);
}

function Create_Information($fields){
	// inserts a new Information row
	$in_page = $fields['in_page'];
	$in_name = $fields['in_name'];
	$in_position = $fields['in_position'];
	$in_link = $fields['in_link'];
	$in_edit = $fields['in_edit'];
	$in_enabled = $fields['in_enabled'];
	if(isset($fields['in_data'])){$in_data = $fields['in_data'];}

	// write table row
	$sql = "INSERT INTO information (IN_PAGE, IN_NAME, IN_POSITION, IN_LINK, IN_EDIT, IN_ENABLED, IN_CREATED)";
	$sql .= " VALUES ('" . htmlentities($in_page, ENT_QUOTES) . "', '" . htmlentities($in_name, ENT_QUOTES) . "', '" . mysql_real_escape_string($in_position) . "', '" . htmlentities($in_link, ENT_QUOTES) . "', '";
	$sql .= mysql_real_escape_string($in_edit) . "', '" . mysql_real_escape_string($in_enabled) . "', '" . strftime("%Y-%m-%d %H:%M:%S", time()) . "')";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Information($fields){
	// rewrites entire table row
	$in_page = $fields['in_page'];
	$page_original = $fields['page_original'];
	$in_name = $fields['in_name'];
	$in_position = $fields['in_position'];
	$in_link = $fields['in_link'];
	$in_edit = $fields['in_edit'];
	$in_enabled = $fields['in_enabled'];
	if(isset($fields['in_data'])){$in_data = $fields['in_data'];}
	if(isset($fields['in_title'])){$in_title = $fields['in_title'];}
	if(isset($fields['in_meta_desc'])){$in_meta_desc = $fields['in_meta_desc'];}
	if(isset($fields['in_meta_keywords'])){$in_meta_keywords = $fields['in_meta_keywords'];}
	if(isset($fields['in_custom_head'])){$in_custom_head = $fields['in_custom_head'];}
	
	$sql = "UPDATE information ";
	$sql .= "SET IN_PAGE='" . htmlentities($in_page, ENT_QUOTES) . "', IN_NAME='" . htmlentities($in_name, ENT_QUOTES) . "', IN_POSITION='" . mysql_real_escape_string($in_position) . "',";
	$sql .= " IN_LINK='" . htmlentities($in_link, ENT_QUOTES) . "', IN_EDIT='" . mysql_real_escape_string($in_edit) . "', IN_ENABLED='" . mysql_real_escape_string($in_enabled) . "'";
	if(isset($in_data)){
		$sql .= ", IN_DATA='" . htmlentities($in_data, ENT_QUOTES) . "'";
	}
	if(isset($in_title)){
		$sql .= ", IN_TITLE='" . htmlentities($in_title, ENT_QUOTES) . "'";
	}
	if(isset($in_meta_desc)){
		$sql .= ", IN_META_DESC='" . htmlentities($in_meta_desc, ENT_QUOTES) . "'";
	}
	if(isset($in_meta_keywords)){
		$sql .= ", IN_META_KEYWORDS='" . htmlentities($in_meta_keywords, ENT_QUOTES) . "'";
	}
	if(isset($in_custom_head)){
		$sql .= ", IN_CUSTOM_HEAD='" . htmlentities($in_custom_head, ENT_QUOTES) . "'";
	}
	$sql .= ", IN_LAST_UPDATED='" . strftime("%Y-%m-%d %H:%M:%S", time()) . "'";
	$sql .= " WHERE IN_PAGE='" . htmlentities($page_original, ENT_QUOTES) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Information($page){
	//delete title from Information Menu Bar
	$sql = "DELETE FROM information WHERE IN_PAGE='" . htmlentities($page, ENT_QUOTES) . "'";
	$sql .= " LIMIT 1";
	$rows = mysql_query($sql);

	return mysql_affected_rows();
}

//---- USERS -------------------------------------------------------------------------------------------------------------------------------------
function Confirm_User($username, $hashed_password){
	$sql = "SELECT user_id FROM users";
	$sql .= " WHERE user_name = '" . $username . "' AND user_password = '" . $hashed_password . "'";
	$results = mysql_query($sql);
	
	return mysql_num_rows($results);
}

function Get_User($username, $hashed_password){
	$sql = "SELECT * FROM users";
	$sql .= " WHERE user_name = '" . $username . "' AND user_password = '" . $hashed_password . "'";
	
	return FetchSqlAsObject($sql);
}

function Rewrite_Users($fields){
	// rewrites entire table row
	$user_name = $fields['user_name'];
	$user_password = $fields['user_password'];

	$sql = "UPDATE users";
	$sql .= " SET user_password = '" . $user_password . "', user_dateadded = now(), user_lastlogin = now()";
	$sql .= " WHERE user_name='" . mysql_real_escape_string($user_name) . "'";
	mysql_query($sql);

	return mysql_affected_rows();
}

// MEMBER LOGIN -------------------------------------------------------------------------------------------------
function Confirm_Member($username, $password){
	$sql = "SELECT MB_ID FROM member WHERE MB_USERNAME = '" . $username . "'" . ($password != "" ? " AND MB_PASSWORD = '" . $password . "'" : "");
	$results = mysql_query($sql);
	
	return mysql_num_rows($results);
}
function Get_Member($username){
	$sql = "SELECT * FROM member WHERE MB_USERNAME = '" . $username . "'";
	
	return FetchSqlAsObject($sql);
}
function Get_All_Members($confirmed){
	$sql = "SELECT * FROM member";
	if($confirmed != "ALL"){
		$sql .= " WHERE MB_CONFIRMED = '" . $confirmed . "'";
	}
	
	return FetchSqlAsObjectArray($sql);
}

function Create_Member($fields) {
	// create new member
	$username = $fields['username'];
	$password = $fields['password'];
	$datecreated = date("l F d, Y, h:i:s");
	$lastlogin = date("l F d, Y, h:i:s");
	$logincount = 1;
	$category = $fields['category'];
	$title = $fields['title'];
	$firstname = $fields['firstname'];
	$lastname = $fields['lastname'];
	$company = $fields['company'];
	$address1 = $fields['address1'];
	$address2 = $fields['address2'];
	$town = $fields['town'];
	$county = $fields['county'];
	$country = $fields['country'];
	$postcode = $fields['postcode'];
	$phone = $fields['phone'];
	$mobile = $fields['mobile'];
	$email = $fields['email'];
	$confirmed = $fields['confirmed'];
	$sql = "INSERT INTO member ";
	$sql .= "(mb_username, mb_password, mb_datecreated, mb_lastlogin, mb_logincount, mb_category, mb_title, mb_firstname, mb_lastname, mb_company, mb_address1, mb_address2, ";
	$sql .= "mb_town, mb_county, mb_country, mb_postcode, mb_phone, mb_mobile, mb_email, mb_confirmed)";
	$sql .= " VALUES ('" . mysql_real_escape_string($username) . "', '" . mysql_real_escape_string($password) . "', '" . "now()" . "', '";
	$sql .= "now()" . "', " . $logincount . ", '" . mysql_real_escape_string($category) . "', '" . mysql_real_escape_string($title) . "', '";
	$sql .= mysql_real_escape_string($firstname) . "', '" . mysql_real_escape_string($lastname) . "', '" . mysql_real_escape_string($company) . "', '";
	$sql .= mysql_real_escape_string($address1) . "', '" . mysql_real_escape_string($address2) . "', '" . mysql_real_escape_string($town) . "', '";
	$sql .= mysql_real_escape_string($county) . "', '" . mysql_real_escape_string($country) . "', '" . mysql_real_escape_string($postcode) . "', '";
	$sql .=  mysql_real_escape_string($phone) . "', '" . mysql_real_escape_string($mobile) . "', '" . mysql_real_escape_string($email) . "', '" . mysql_real_escape_string($confirmed) .  "')";
	
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Rewrite_Member($fields) {
	// rewrites entire table row
	$id = $fields['id'];
	$username = $fields['username'];
	$password = $fields['password'];
	$datecreated = $fields['datecreated'];
	$lastlogin = date("l F d, Y, h:i:s");
	$logincount = $fields['logincount'];
	$category = $fields['category'];
	$title = $fields['title'];
	$firstname = $fields['firstname'];
	$lastname = $fields['lastname'];
	$company = $fields['company'];
	$address1 = $fields['address1'];
	$address2 = $fields['address2'];
	$town = $fields['town'];
	$country = $fields['country'];
	$postcode = $fields['postcode'];
	$phone = $fields['phone'];
	$mobile = $fields['mobile'];
	$email = $fields['email'];
	$confirmed = $fields['confirmed'];
	$sql = "UPDATE member ";
	$sql .= "SET mb_password='" . mysql_real_escape_string($password) . "' ,mb_lastlogin='" . "now()" . "', mb_logincount='" . mysql_real_escape_string($logincount) . "',";
	$sql .= " mb_category='" . mysql_real_escape_string($category) . "', mb_title='" .mysql_real_escape_string($title) . "',";
	$sql .= " mb_firstname='" . mysql_real_escape_string($firstname) . "', mb_lastname='" . mysql_real_escape_string($lastname) . "', mb_company='" . mysql_real_escape_string($company) . "',";
	$sql .= " mb_address1='" . mysql_real_escape_string($address1) . "', mb_address2='" . mysql_real_escape_string($address2) . "', mb_town='" .mysql_real_escape_string($town) . "',";
	$sql .= " mb_country='" . mysql_real_escape_string($country) . "', mb_postcode='" . mysql_real_escape_string($postcode) . "', mb_phone='" .mysql_real_escape_string($phone) . "',";
	$sql .= " mb_mobile='" . mysql_real_escape_string($mobile) . "', mb_email='" . mysql_real_escape_string($email) . "', mb_confirmed='" . mysql_real_escape_string($confirmed) . "' ";
	$sql .= "WHERE mb_id='" . mysql_real_escape_string($id) . "' AND mb_username='" . mysql_real_escape_string($username) . "'";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Accept_Member($fields) {
	//confirm member as accepted
	$id = $fields['id'];
	$username = $fields['username'];
	$category = $fields['category'];
	$password = $fields['password'];
	$confirmed = $fields['confirmed'];
	$sql = "UPDATE member ";
	$sql .= "SET mb_category='" . mysql_real_escape_string($category) . "', mb_password='" . mysql_real_escape_string($password) . "', mb_confirmed='Y' ";
	$sql .= "WHERE mb_id='" . mysql_real_escape_string($id) . "' AND mb_username='" . mysql_real_escape_string($username) . "'";	
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Member($id, $user){
	$sql = "DELETE FROM member WHERE MB_ID = '" . $id . "' AND MB_USERNAME = '" . $user . "'";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();;
}

// MEMBER PRICE CATEGORIES -----------------------------------------------------------------------------------------------------------------------------
function Get_All_Pcats(){
	$sql = "SELECT * FROM qtydisch";
	$sql .= " ORDER BY QDH_PRODUCT";
	
	return FetchSqlAsObjectArray($sql);
}

function Get_Pcat($product){
	$sql = "SELECT * FROM pricecath";
	$sql .= " WHERE PCH_PRODUCT='" . $product . "'";
	
	return FetchSqlAsObject($sql);
}

function Get_Pcat_Lines($product){
	$sql = "SELECT * FROM pricecatl LEFT JOIN pricecath ON PCL_PCH_ID = PCH_ID";
	$sql .= " WHERE PCH_PRODUCT='" . $product . "' ORDER BY PCL_CAT";

	return FetchSqlAsObjectArray($sql);
}

function Get_Pcat_Adj($product, $pricecat = ""){
	$sql = "SELECT * FROM pricecatl LEFT JOIN pricecath ON PCL_PCH_ID = PCH_ID";
	$sql .= " WHERE PCH_PRODUCT='" . $product . "' AND PCL_CAT='" . $pricecat . "' LIMIT 1";

	$row = FetchSqlAsObject($sql);
	if($row){return $row->PCL_ADJUST;}else{return null;};
}

function Create_Pcat_header($fields) {
	// create new quantity discount header
	$pch_product = $fields['pch_product'];
	$pch_type = $fields['pch_type'];
	$sql = "INSERT INTO pricecath ";
	$sql .= "(pch_product, pch_type)";
	$sql .= " VALUES ('" . $pch_product . "', '" . $pch_type . "')";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Rewrite_Pcat_header($fields) {
	// rwrite quantity discount header
	$pch_product = $fields['pch_product'];
	$pch_type = $fields['pch_type'];
	$sql = "UPDATE pricecath ";
	$sql .= "SET pch_type='" . $pch_type . "' ";
	$sql .= "WHERE pch_product='" . $pch_product . "' ";
	$sql .= "LIMIT 1";	
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Create_Pcat_line($fields) {
	//create quantity discount lines - delete all existing lines before recreating them
	$pcl_pch_id = $fields['pcl_pch_id'];
	$pcl_cat = $fields['pcl_cat'];
	$pcl_adjust = $fields['pcl_adjust'];
	
	$sql = "INSERT INTO pricecatl ";
	$sql .= "(pcl_pch_id, pcl_cat, pcl_adjust)";
	$sql .= " VALUES ('" . $pcl_pch_id . "', '" . $pcl_cat . "', '" . $pcl_adjust . "')";
	mysql_query($sql);

	return mysql_affected_rows();
}

function Delete_Pcat_header($fields){
	$pch_product = $fields['pch_product'];
	$sql = "DELETE FROM pricecath WHERE PCH_PRODUCT = '" . $pch_product . "'";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Pcat_lines($fields){
	$pcl_pch_id = $fields['pcl_pch_id'];
	$sql = "DELETE FROM pricecatl WHERE PCL_PCH_ID = '" . $pcl_pch_id . "'";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();
}
// PROMOTIONS -----------------------------------------------------------------------------------------------------------

function Get_Promotion_Type($id){
	$sql = "SELECT * FROM promotions";
	if($id != "ALL"){
		$sql .= " WHERE PROM_ID = '" . $id . "'";
		return FetchSqlAsObject($sql);
	}
	return FetchSqlAsObjectArray($sql);
}

function Get_Promotion($prom_type, $prom_no){
	$sql = "SELECT * FROM promhead";
	if($prom_type != "" or $prom_no !=""){
		$sql .= " WHERE ";
		if($prom_type != ""){$sql .= "PROMH_PROM_ID = '" . $prom_type . "'";}
		if($prom_type != "" and $prom_no != ""){$sql .= " AND ";}
		if($prom_no != ""){ 
			$sql .= "PROMH_NO = '" . $prom_no . "'";
			return FetchSqlAsObject($sql);
		}
	}
	return FetchSqlAsObjectArray($sql);
}

function Get_Promolines($prom_no){
	$sql = "SELECT * FROM promline";
	$sql .= " WHERE PROML_NO='" . $prom_no . "'";
	$sql .= " ORDER BY PROML_POS";
	return FetchSqlAsObjectArray($sql);
}

function Create_Promotion($fields) {
	// create new promotion header
	$promh_no = $fields['promh_no'];
	$promh_prom_id = $fields['promh_prom_id'];
	$promh_adjust = $fields['promh_adjust'];
	$promh_start = $fields['promh_start'];
	$promh_expiry = $fields['promh_expiry'];
	$sql = "INSERT INTO promhead ";
	$sql .= "(promh_no, promh_prom_id, promh_adjust, promh_start, promh_expiry)";
	$sql .= " VALUES ('" . mysql_real_escape_string($promh_no) . "', '" . mysql_real_escape_string($promh_prom_id) . "', '";
	$sql .= mysql_real_escape_string($promh_adjust) . "', '" . $promh_start . "', '" . $promh_expiry . "')";
	
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Rewrite_Promotion($fields) {
	// rewrites entire table row
	$promh_no = $fields['promh_no'];
	$promh_adjust = $fields['promh_adjust'];
	$promh_start = $fields['promh_start'];
	$promh_expiry = $fields['promh_expiry'];

	$sql = "UPDATE promhead ";
	$sql .= "SET promh_adjust='" . $promh_adjust . "', promh_start='" . $promh_start . "', promh_expiry='" . $promh_expiry . "' ";
	$sql .= "WHERE promh_no='" . mysql_real_escape_string($promh_no) . "'";	
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Create_Promoline($fields) {
	// create new Promotion Line
	$proml_no = $fields['proml_no'];
	$proml_pos = $fields['proml_pos'];
	$proml_cat = $fields['proml_cat'];
	$proml_prod = $fields['proml_prod'];
	$sql = "INSERT INTO promline ";
	$sql .= "(proml_no, proml_pos, proml_cat, proml_prod)";
	$sql .= " VALUES ('" . mysql_real_escape_string($proml_no) . "', '" . $proml_pos . "', '";
	$sql .= mysql_real_escape_string($proml_cat) . "', '" . mysql_real_escape_string($proml_prod) .  "')";
	
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Rewrite_Promoline($fields) {
	// rewrites entire table row
	$proml_no = $fields['proml_no'];
	$proml_pos = $fields['proml_pos'];
	$proml_cat = $fields['proml_cat'];
	$proml_prod = $fields['proml_prod'];

	$sql = "UPDATE promline ";
	$sql .= "SET proml_cat='" . $proml_cat . "', proml_prod='" . $proml_prod . "' ";
	$sql .= "WHERE proml_no='" . mysql_real_escape_string($proml_no) . "' AND proml_pos='" . $proml_pos . "' ";
	$sql .= "LIMIT 1";	
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Promotion($promo_no){
	//first delete all lines
	$sql = "DELETE FROM promline WHERE PROML_NO = '" . $promo_no . "'";
	$rows = mysql_query($sql);
	$noLines = mysql_affected_rows();
	//now delete the header
	$sql = "DELETE FROM promhead WHERE PROMH_NO = '" . $promo_no . "' LIMIT 1";
	$rows = mysql_query($sql);
	$noLines = $noLines + mysql_affected_rows();
	
	return $noLines;
}

function Delete_Promo_Header($promo_no){
	//first promotion header
	$sql = "DELETE FROM promhead WHERE PROMH_NO = '" . $promo_no . "'";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();
}

function Delete_Promo_Lines($promo_no, $pos){
	//first delete all lines
	$sql = "DELETE FROM promline WHERE PROML_NO = '" . $promo_no . "'";
	if($pos != "ALL"){
		$sql .= " AND PROML_POS='" . $pos . "'";
	}
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();
}

function Renumber_Promolines($promo){
	//renumbers promotion lines (normally after a line has been deleted) such that there are no gaps
	$promoline = Get_Promolines($promo);
	$pos = 0; $rows_rewritten = 0;
	foreach($promoline as $pl){
		$pos++;
		$sql = "UPDATE promline ";
		$sql .= "SET PROML_POS='" . $pos . "' ";
		$sql .= "WHERE PROML_NO='" . $pl->PROML_NO . "' AND PROML_POS='" . $pl->PROML_POS . "' LIMIT 1";
		mysql_query($sql);
	}
	
return mysql_affected_rows();
}

function On_Promotion($tree, $product){
	//determines whether or not a product is on an active promotion
	//first check to see if the product is on an active promotion at Product level and if not then check Categories all the way back up the tree
	//the function returns an object array containing all promotions data found against the product or first category found to be on promotion
	$sql = "SELECT promline.PROML_NO, promhead.PROMH_PROM_ID, promhead.PROMH_ADJUST, promhead.PROMH_START, promhead.PROMH_EXPIRY ";
	$sql .= "FROM `promline` ";
	$sql .= "INNER JOIN `promhead` on promline.PROML_NO = promhead.PROMH_NO ";
	$sql .= "WHERE promline.PROML_PROD = '" . $product . "' ";
	$sql .= "AND CURDATE() BETWEEN promhead.PROMH_START AND promhead.PROMH_EXPIRY";
	$results_set = FetchSqlAsObjectArray($sql);
	
	if(count($results_set) == 0){
		//not on a promotion as product so check back up the tree to see if it's on an active Category level promotion
		$fstart = 0;
		$fend = 999; $cat_array = array();
		while ($fend > 0){
			$fend = strpos($tree, "_", $fstart);
			if($fend > 0){ 
				$parent = substr($tree, $fend + 1);
				$fstart = $fend + 1;
				$start_cat = $fstart;
				//build an array containing all categories found within the tree
				$end_cat = strpos($tree, "_", $start_cat);
				if($end_cat > 0){
					$cat_array[] = substr($tree, $start_cat, $end_cat - $start_cat);
				}else{
					$cat_array[] = substr($tree, $start_cat);
				}
			}
		}
		for($i = count($cat_array); $i > 0; $i--){
			//go back up the category array and see if any are on promotion - return data on the first category found to be on promotion
			$sql = "SELECT promline.PROML_NO, promhead.PROMH_PROM_ID, promhead.PROMH_ADJUST, promhead.PROMH_START, promhead.PROMH_EXPIRY ";
			$sql .= "FROM `promline` ";
			$sql .= "INNER JOIN `promhead` on promline.PROML_NO = promhead.PROMH_NO ";
			$sql .= "WHERE promline.PROML_CAT = '" . $cat_array[$i - 1] . "' ";
			$sql .= "AND CURDATE() BETWEEN promhead.PROMH_START AND promhead.PROMH_EXPIRY";
			$results_set = FetchSqlAsObjectArray($sql);
			if(count($results_set) != 0){
				return $results_set;
			}
		}
		if(count($results_set) == 0){
			return false;
		}
	}
	return $results_set;
	
//  SELECT customers.CS_ID, customers.CS_USERNAME, orders.OR_DATE_CREATED
//	FROM `customers`
//	INNER JOIN `orders` on customers.CS_ID = orders.OR_CUSTOMER_ID
//	INNER JOIN `orderline` on orders.OR_ORDER_NO = orderline.OL_ORDER_NO
//	WHERE orderline.OL_ORDER_NO = 'AB001'
//  AND orders.OR_DATE_CREATED BETWEEN '2013-02-13 00:00:00' AND '2013-02-14 23:59:59'
}

// PRODUCT REVIEWS ------------------------------------------------------------------------------------------------------
function Get_Review_By_Id($id){
	$sql = "SELECT * FROM reviews";
	$sql .= " WHERE RV_ID='" . $id . "'";

	return FetchSqlAsObject($sql);
}

function Get_Reviews($product){
	$sql = "SELECT * FROM reviews";
	$sql .= " WHERE RV_PRODUCT='" . $product . "'";
	
	return FetchSqlAsObjectArray($sql);
}

function Get_Published_Reviews($product){
	$sql = "SELECT * FROM reviews";
	$sql .= " WHERE RV_PRODUCT='" . $product . "' AND RV_PUBLISHED='Y'";
	
	return FetchSqlAsObjectArray($sql);
}

function Get_Stars($product){
	//returns the product review star rating
	//first get average of all reviews
	$reviews = Get_Published_Reviews($product); $rating = 0;
	if(count($reviews) != 0){
		foreach($reviews as $r){
			$rating += $r->RV_RATING;
		}
		$rating = $rating / count($reviews);
		//now get star rating which is the average rounded to the neares 0.5
		if($rating >= ($half = ($ceil = ceil($rating))- 0.5) + 0.25){ 
			return number_format($ceil, 1);
		}else if($rating < $half - 0.25){
			return number_format(floor($rating), 1);
		}else{
			return number_format($half, 1);
		}
	}else{
		return 0;
	}
}

function Get_All_Reviews_Range($published = "", $from_date, $to_date){
	$sql = "SELECT * FROM reviews";
	if($published != "ALL" or ($from_date != "" and $to_date != "")){
		$sql .= " WHERE ";
	}
	if($published != "ALL"){
		$sql .= "RV_PUBLISHED = '" . $published . "'";
		if($from_date != "" and $to_date != ""){
			$sql .= " AND ";
		}
	}
	if($from_date != "" and $to_date != ""){
		$sql .=" RV_DATE BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
	}
	return FetchSqlAsObjectArray($sql);
}

function Search_For_Reviews($from_date, $to_date, $search, $published = "") {
	$sql = "SELECT * FROM reviews LEFT JOIN product ON RV_PRODUCT = PR_PRODUCT";
	$sql .= " WHERE ";
	if($search != ""){
		$sql .= "(RV_AUTHOR LIKE '%" . $search . "%'";
		$sql .= " OR RV_ORDER LIKE '%" . $search . "%'";
		$sql .= " OR RV_RATING LIKE '%" . $search . "%'";
		$sql .= " OR RV_TITLE LIKE '%" . $search . "%'";
		$sql .= " OR RV_TEXT LIKE '%" . $search . "%'";
		$sql .= " OR RV_REPLY LIKE '%" . $search . "%'";
		$sql .= " OR PR_NAME LIKE '%" . $search . "%'";
		$sql .= " OR PR_DESC_SHORT LIKE '%" . $search . "%'";
		$sql .= " OR PR_PRODUCT LIKE '%" . $search . "%'";
		$sql .= " OR PR_SKU LIKE '%" . $search . "%')";
	}
	if($published != "ALL"){
		if($search != ""){
			$sql .= " AND ";
		}
		$sql .= "RV_PUBLISHED = '" . $published . "'";
	}
	if($from_date != "" and $to_date != ""){
		if($search != "" or $published != "ALL"){
			$sql .= " AND ";
		}
		$sql .="RV_DATE BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
	}

	return FetchSqlAsObjectArray($sql);
}

function Create_Review($fields) {
	// create new Review
	$rv_product = $fields['rv_product'];
	$rv_order = $fields['rv_order'];
	$rv_author = $fields['rv_author'];
	$rv_town = $fields['rv_town'];
	$rv_country = $fields['rv_country'];
	$rv_rating = $fields['rv_rating'];
	$rv_title = $fields['rv_title'];
	$rv_text = $fields['rv_text'];
	$rv_published = $fields['rv_published'];
	$sql = "INSERT INTO reviews ";
	$sql .= "(rv_product, rv_order, rv_author, rv_town, rv_country, rv_date, rv_rating, rv_title, rv_text, rv_published)";
	$sql .= " VALUES ('" . $rv_product . "', '" . mysql_real_escape_string($rv_order) . "', '" . mysql_real_escape_string($rv_author) . "', '";
	$sql .= mysql_real_escape_string($rv_town) . "', '" . mysql_real_escape_string($rv_country) . "', '" . strftime("%Y-%m-%d %H:%M:%S", time()) . "', '" . $rv_rating . "', '";
	$sql .= htmlentities($rv_title, ENT_QUOTES) . "', '" . htmlentities($rv_text, ENT_QUOTES) . "', '";
	$sql .= $rv_published . "')";
	mysql_query($sql);
	
	return mysql_affected_rows();
}

function Rewrite_Review($fields) {
	// rewrite entire Review line
	$rv_id = $fields['rv_id'];
	$rv_product = $fields['rv_product'];
	$rv_order = $fields['rv_order'];
	$rv_author = $fields['rv_author'];
	$rv_town = $fields['rv_town'];
	$rv_country = $fields['rv_country'];
	$rv_date = $fields['rv_date'];
	$rv_rating = $fields['rv_rating'];
	$rv_title = $fields['rv_title'];
	$rv_text = $fields['rv_text'];
	$rv_reply = $fields['rv_reply'];
	$rv_reply_original = $fields['rv_reply_original'];
	$rv_date_reply = $fields['rv_date_reply'];
	$rv_published = $fields['rv_published'];
	$rv_published_original = $fields['rv_published_original'];
	$rv_date_publish = $fields['rv_date_publish'];
	if($rv_reply != $rv_reply_original){$rv_date_reply = strftime("%Y-%m-%d %H:%M:%S", time());}
	if($rv_published != $rv_published_original){$rv_date_publish = strftime("%Y-%m-%d %H:%M:%S", time());}
	$sql = "UPDATE reviews ";
	$sql .= "SET rv_product='" . $rv_product . "', rv_order='" . mysql_real_escape_string($rv_order) . "', rv_author='" . mysql_real_escape_string($rv_author) . "', ";
	$sql .= "rv_town='" . mysql_real_escape_string($rv_town) . "', rv_country='" . mysql_real_escape_string($rv_country) . "', rv_date='" . $rv_date . "', ";
	$sql .= "rv_title='" . htmlentities($rv_title, ENT_QUOTES) . "', rv_text='" . htmlentities($rv_text, ENT_QUOTES) . "', rv_reply='" . htmlentities($rv_reply, ENT_QUOTES) . "', ";
	$sql .= "rv_date_reply='" . $rv_date_reply . "', rv_published='" . $rv_published . "', rv_date_publish='" . $rv_date_publish . "' ";
	$sql .= "WHERE rv_id='" . mysql_real_escape_string($rv_id) . "' ";
	$sql .= "LIMIT 1";
	mysql_query($sql);

	return mysql_affected_rows();
}

function Rewrite_Review_Status($fields){
	// rewrites publish status of a review
	$rv_id = $fields['rv_id'];
	$rv_published = $fields['rv_published'];
	$ol_first_printed = strftime("%Y-%m-%d %H:%M:%S", time());
	$ol_last_printed = strftime("%Y-%m-%d %H:%M:%S", time());

	$sql = "UPDATE reviews";
	$sql .= " SET RV_PUBLISHED = '" . $rv_published . "'";
	if($rv_published == "Y"){
		$sql .= ", RV_DATE_PUBLISH = '" . strftime("%Y-%m-%d %H:%M:%S", time()) . "'"; 
	}
	$sql .= " WHERE RV_ID='" . $rv_id . "'";
	mysql_query($sql);

	return mysql_affected_rows();
}

function Delete_Review($id){
	$sql = "DELETE FROM reviews WHERE RV_ID = '" . $id . "' LIMIT 1";
	$rows = mysql_query($sql);
	
	return mysql_affected_rows();;
}

// ERROR HANDLING -------------------------------------------------------------------------------------------------------
function checkForErrors($rows, $error){
	if ($rows == 1){		
		$message = $rows . " record successfully UPDATED";
		$warning = "green";
	}
	if ($rows == 0){
		$message = "WARNING ! ! ! - NO RECORDS UPDATED";
		$warning = "orange";
	}
	if ($rows > 1){
		$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
		$warning = "red";
	}
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
	}
	
	$fields[0] = $message; $fields[1] = $warning;

}
// MISCELLANEOUS USEFUL FUNCTIONS ---------------------------------------------------------------------------------------
function Search_For($search) {
	$sql = "SELECT * FROM product";
	$sql .= " WHERE PR_NAME LIKE '%" . $search . "%'";
	$sql .= " OR PR_DESC_SHORT LIKE '%" . $search . "%'";
	$sql .= " OR PR_PRODUCT LIKE '%" . $search . "%'";

	return FetchSqlAsObjectArray($sql);
}

function Search_For_Advanced($search) {
	$sql = "SELECT * FROM product LEFT JOIN options ON PR_PRODUCT = OP_PRODUCT";
	$sql .= " WHERE (PR_NAME LIKE '%" . $search . "%'";
	$sql .= " OR PR_DESC_SHORT LIKE '%" . $search . "%'";
	$sql .= " OR PR_PRODUCT LIKE '%" . $search . "%'";
	$sql .= " OR PR_META_KEYWORDS LIKE '%" . $search . "%'";
	$sql .= " OR PR_SKU LIKE '%" . $search . "%'";
	$sql .= " OR OP_NAME LIKE '%" . $search . "%'";
	$sql .= " OR OP_SKU LIKE '%" . $search . "%')";
	$sql .= " AND PR_DISABLE = 'N'";

	return FetchSqlAsObjectArray($sql);
}

function Search_Options($search) {
	$sql = "SELECT * FROM product LEFT JOIN options ON PR_PRODUCT = OP_PRODUCT";
	$sql .= " WHERE PR_NAME LIKE '%" . $search . "%'";
	$sql .= " OR PR_DESC_SHORT LIKE '%" . $search . "%'";
	$sql .= " OR PR_PRODUCT LIKE '%" . $search . "%'";
	$sql .= " OR PR_SKU LIKE '%" . $search . "%'";
	$sql .= " OR OP_NAME LIKE '%" . $search . "%'";
	$sql .= " OR OP_SKU LIKE '%" . $search . "%'";

	return FetchSqlAsObjectArray($sql);
}

function Search_product($search) {
	$sql = "SELECT * FROM product";
	$sql .= " WHERE PR_NAME LIKE '%" . $search . "%'";
	$sql .= " OR PR_DESC_SHORT LIKE '%" . $search . "%'";
	$sql .= " OR PR_PRODUCT LIKE '%" . $search . "%'";
	$sql .= " OR PR_SKU LIKE '%" . $search . "%'";
	$sql .= " ORDER BY PR_PRODUCT";

	return FetchSqlAsObjectArray($sql);
}

function Search_category($search) {
	$sql = "SELECT DISTINCT CA_NAME, CA_DESCRIPTION, CA_CODE FROM categories";
	$sql .= " WHERE CA_NAME LIKE '%" . $search . "%'";
	$sql .= " OR CA_DESCRIPTION LIKE '%" . $search . "%'";
	$sql .= " OR CA_CODE LIKE '%" . $search . "%'";
	$sql .= " ORDER BY CA_CODE";

	return FetchSqlAsObjectArray($sql);
}

function Search_member($search) {
	$sql = "SELECT * FROM member";
	$sql .= " WHERE MB_ID LIKE '%" . $search . "%'";
	$sql .= " OR MB_USERNAME LIKE '%" . $search . "%'";
	$sql .= " ORDER BY MB_USERNAME";
	echo $sql;
	return FetchSqlAsObjectArray($sql);
}

function Fix_Price($price) {
	// return price as 2dp string
	$formatted = $price;
	$lenPrice = strlen($formatted);
	$offset = strpos($price, ".");
	if (!$offset) {
		$noDecimalPlaces = 0;
	} else {
		$noDecimalPlaces =  $lenPrice - ($offset + 1);
	}
	if ($noDecimalPlaces == 0 ) {$formatted = $price . ".00";}
	if ($noDecimalPlaces == 1 ) {$formatted = $price . "0";}

	return $formatted;
}

function reducePrice($price, $percentReduction) {
	$reducedPrice = round($price *((100.00 - $percentReduction)/100));
	
	//if the last digit is a zero the round function will not include it so add zeroes to make up the required 2dp
	if (strpos($reducedPrice, ".")) {
		$noDigits = (strlen($reducedPrice)) - (strpos($reducedPrice, ".") + 1);
	} else {
		$noDigits = 0;
	}
	if ($noDigits == 0) { $reducedPrice = $reducedPrice . ".00" ;}
	if ($noDigits == 1) { $reducedPrice = $reducedPrice . "0" ;}
	
	return $reducedPrice;
}

function addVAT($ex_VAT, $vatRate) {
	$inc_VAT = round(($ex_VAT * ((100 + $vatRate)/100)), 2);
	//if the last digit is a zero the round function will not include it so add zeroes to make up the required 2dp
	if (strpos($inc_VAT, ".")) {
		$noDigits = (strlen($inc_VAT)) - (strpos($inc_VAT, ".") + 1);
	} else {
		$noDigits = 0;
	}
	if ($noDigits == 0) { $inc_VAT = $inc_VAT . ".00" ;}
	if ($noDigits == 1) { $inc_VAT = $inc_VAT . "0" ;}
	
	return $inc_VAT;
}

function stripVAT($inc_VAT, $vatRate){
	$ex_VAT = round(($inc_VAT * 100) / (100 + $vatRate), 2);
	//if the last digit is a zero the round function will not include it so add zeroes to make up the required 2dp
	if (strpos($ex_VAT, ".")) {
		$noDigits = (strlen($ex_VAT)) - (strpos($ex_VAT, ".") + 1);
	} else {
		$noDigits = 0;
	}
	if ($noDigits == 0) { $ex_VAT = $ex_VAT . ".00" ;}
	if ($noDigits == 1) { $ex_VAT = $ex_VAT . "0" ;}
	
	return $ex_VAT;
}

function validate2dp($number) {
	if(substr($number, 0, 1) == "-"){
		if (strlen($number) > 4){
			if(!is_numeric($number)){
				return "false";
			}
			if(substr($number, strlen($number) - 3, 1) == "."){
				return "true";
			}
			
			//at this point it must be a number but not to 2dp
			return "false";
		}
	}else{
		if (strlen($number) > 3){
			if(!is_numeric($number)){
				return "false";
			}
			if(substr($number, strlen($number) - 3, 1) == "."){
				return "true";
			}
			
			//at this point it must be a number but not to 2dp
			return "false";
		}
	}
	return "false";
}

function getNextCode($type) {
	$preferences = getPreferences();
	if($type == "product"){$seed = $preferences->PREF_PROD_SEED;}
	if($type == "category"){$seed = $preferences->PREF_CAT_SEED;}
	if($type == "promotion"){$seed = $preferences->PREF_PROM_SEED;}
	$letters = substr($seed, 2,2);
	$number = substr($seed, 4);
	//seed is in format PRAB999 or CAAB999 or PMAB999
	//first increment number characters
	$number = $number + 1;
	if($number > 999){
		//set number to "001" and increment 2nd and 3rd alpha characters//
		$number = "001";
		$thirdchar = substr($seed, 2, 1);
		$fourthchar = substr($seed, 3, 1);
		if(ord($fourthchar) + 1 > 90){
			$fourthchar = "A";
			$thirdchar = chr(ord($thirdchar) + 1);
			if(ord($thirdchar) > 90){
				//we are trying to get a code greater than przz999 or cazz999!!!
				$thirdchar = "A"; $fourthchar = "A";
				$numbers = "001";
			}
		}else{
			$fourthchar = chr(ord($fourthchar) + 1);
		}
		$letters = $thirdchar . $fourthchar;
	}
	//put new number in 3 digit format
	if(strlen($number) == 1){$number = "00" . $number;}
	if(strlen($number) == 2){$number = "0" . $number;}
	
	if($type == "product"){$nextcode = "PR" . $letters . $number;}
	if($type == "category"){$nextcode = "CA" . $letters . $number;}
	if($type == "promotion"){$nextcode = "PM" . $letters . $number;}
	
	return $nextcode;	
}

function incrementSeed($seed) {
	$sql = "UPDATE preferences ";
	if(substr($seed, 0 ,2) == "PR"){$sql .= "SET PREF_PROD_SEED='" . mysql_real_escape_string($seed) . "' ";}
	if(substr($seed, 0 ,2) == "CA"){$sql .= "SET PREF_CAT_SEED='" . mysql_real_escape_string($seed) . "' ";}
	if(substr($seed, 0 ,2) == "PM"){$sql .= "SET PREF_PROM_SEED='" . mysql_real_escape_string($seed) . "' ";}
	$sql .= "WHERE PREF_ID=1";
	mysql_query($sql);

	return mysql_affected_rows();
}

function validateSeed($seed) {
	if (strlen($seed) == 7){
		if((substr($seed, 0, 2) != "CA") and (substr($seed, 0, 2) != "PR")){
			return "false";
		}
		if(!ctype_alpha(substr($seed, 0, 4))){
			return "false";
		}
		if(!is_numeric(substr($seed, 4, 3))){
			return "false";
		}
		return "true";
	}else{
		return "false";
	}
}

function Upload_File($tmp_name, $upload_file) {
	//check file has uploaded to temp directory then move it to working directory
	$XOK = 1;
	if (is_uploaded_file($tmp_name)) {
	   $mess = "File ". $tmp_name ." uploaded successfully.\n";
	} else {
	   $mess = "UPLOAD FAILURE!!! ";
	   $mess .= "[filename '". $tmp_name . "'] <br> Please contact 1-ecommerce.com"; $XOK = 0;
	}
	if($XOK == 1){
		if (move_uploaded_file($tmp_name, $upload_file)) {
			$mess = "File Uploaded Successfully";
		} else {
			$mess = "FILE UPLOAD FAILURE!!! - unable to rename temporary file. Please contact 1-ecommerce.com";
		}
	}
	
return $mess;
}

function Create_Imagebig($upload_file) {
	//get image details and if width = height and width > 400px rename to picturebig.jpg and create low res picture.jpg
	$image_info = getimagesize($upload_file); $mess = "";
	$image_width = $image_info[0]; $image_height = $image_info[1]; $image_type = $image_info[2];
	switch($image_type){
		case IMAGETYPE_JPEG:
			if(strpos($upload_file, ".jpg", 0)){
				$image_big = str_replace(".jpg", "big.jpg", $upload_file);
			}
			if(strpos($upload_file, ".jpeg", 0)){
				$image_big = str_replace(".jpeg", "big.jpeg", $upload_file);
			}
			break;
		case IMAGE_TYPE_GIF:
			$image_big = str_replace(".gif", "big.gif", $upload_file);
			break;
		case IMAGETYPE_PNG:
			$image_big = str_replace(".png", "big.png", $upload_file);
			break;	
		default:
			break;
	}
	//if($image_width == $image_height and $image_width > 400){
	if($image_width == $image_height and $image_width > 400){
		//rename the uploaded high res picture.jpg to picturebig.jpg file
		rename($upload_file, $image_big);
		//now create a smaller resolution file which will be named the original target name (the user need never know)
		$image_small = new SimpleImage(); 
		$image_small->load($image_big); 
		$image_small->resize(400,400); 
		$image_small->save($upload_file);
	}else{
		$mess = "Your image has uploaded sucessfully however it is NOT <br/> of the correct dimensions and may NOT display as <br/>expected.<br/>";
		$mess .= "You MUST upload an image of equal height and width <br/>";
		$mess .= " and with MINIMUM dimensions of 400px x 400px so that <br/> the product image zoom feature may display correctly.";
	}
	
return $mess;
}

function delete_directory($dirname) {
   	//deletes the specified directory
   	if (is_dir($dirname))
     	 $dir_handle = opendir($dirname);
   	if (!$dir_handle)
      	return false;
   	while($file = readdir($dir_handle)) {
      	if ($file != "." && $file != "..") {
         	if (!is_dir($dirname."/".$file))
            	unlink($dirname."/".$file);
         	else
            	delete_directory($dirname.'/'.$file);    
      	}
  	 }
   	closedir($dir_handle);
   	rmdir($dirname);
	
return true;
}

function delete_contents($dirname, $contents) {
   	//deletes the specified directory
   	if (is_dir($dirname))
     	 $dir_handle = opendir($dirname);
   	if (!$dir_handle)
      	return false;
   	while($file = readdir($dir_handle)) {
      	if ($file != "." && $file != "..") {
         	if (!is_dir($dirname."/".$file))
            	unlink($dirname."/".$file);
         	else
				if($contents == "both")
            		delete_directory($dirname.'/'.$file, $contents);    
      	}
  	}
   	closedir($dir_handle);
	
return true;
}

function copy_directory($source,$destination) {
	//copies recursively the contents of the source folder to the destination folder - if a destination folder/subfolder doesn't already exist it creates it then copies the contents
	$dir = opendir($source);
	if(!is_dir($destination)){ 
		$oldumask = umask(0); 
		mkdir($destination, 0777);
		umask($oldumask);
	}
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($source . '/' . $file) ) { 
                copy_directory($source . '/' . $file, $destination . '/' . $file); 
            } 
            else { 
                copy($source . '/' . $file, $destination . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
return true;
}

function get_top_level($tree) {
	//gets the top level category (the leftcolumn menu category) rom the tree
	$fstart = 0; $fend = 999; $cntr1 = 0;
	while ($fend > 0){
		$fend = strpos($tree, "_", $fstart);
		if($cntr1 == 0){
			//initially assume the call is from a level 1 category page. This will be overwritten if the call is from a deeper page.
			$toplevel = substr($tree, $fend + 1);
		}
		if($cntr1 > 0 and $fend > 0){
			//the first occurence will be the home npage ie.0 - we want the next one which will be the top level category code
			$toplevel = substr($tree, $fstart, $fend - $fstart);
			break;
		}
		$fstart =$fend + 1; $cntr1++;
	}
	
return $toplevel;
}

function pack_date($date){
	//used to convert a dd/mm/yyy input date into mysql format
	$elements = explode("/", $date);
	$day = $elements[0]; $month = $elements[1]; $year = $elements[2];
	$timestamp = mktime(0 ,0, 0, $month, $day, $year);
	$formatted = strftime("%Y-%m-%d %H:%M:%S", $timestamp);
	return $formatted;
}

function unpack_date($formatted){
	//used to convert a mysql date to dd/mm/yyy format for display
	$timestamp = strtotime($formatted);
	if($timestamp != 0){
		$date = strftime("%d/%m/%Y", $timestamp);
	}else{
		$date = "";
	}
	return $date;
}

function pack_calendar_date($date, $fromto){
	//used to convert a yyyy-mm-dd returned calendar date into mysql format
	$elements = explode("-", $date);
	$day = $elements[2]; $month = $elements[1]; $year = $elements[0];
	$timestamp = mktime(0 ,0, 0, $month, $day, $year);
	if($fromto == "from"){
		$formatted = strftime("%Y-%m-%d 00:00:00", $timestamp);
	}else{
		$formatted = strftime("%Y-%m-%d 23:59:59", $timestamp);
	}
	return $formatted;
}

function unpack_calendar_date($formatted){
	//used to convert a mysql date to yyyy-mm-dd format for calendar display
	$timestamp = strtotime($formatted);
	if($timestamp != 0){
		$date = strftime("%Y-%m-%d", $timestamp);
	}else{
		$date = "";
	}
	return $date;
}

function unpack_review_date($formatted){
	//used to convert a mysql date to dd mmm yyyy format for review display
	$timestamp = strtotime($formatted);
	if($timestamp != 0){
		$date = strftime("%d %b %Y", $timestamp);
	}else{
		$date = "";
	}
	return $date;
}
?>

