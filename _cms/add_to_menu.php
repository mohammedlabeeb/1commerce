<?php
include_once("includes/session.php");
//confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$message_upload = "";
$target_file = "";
$pathName = "/images/";
$scrolltobottom = "";
$option1 = "#"; $option2 = "#"; $option3 = "#"; $option4 = "#";

$category_current = "";
if(isset($_GET['category'])){
	$category = getCategory($_GET['category']);
	$category_current = $category->CA_CODE;
}
if(isset($_POST['CATEGORY_CURRENT'])){
	$category = getCategory($_POST['CATEGORY_CURRENT']);
	$category_current = $category->CA_CODE;
}

if (isset($_POST['DELETE'])) {

}

if (isset($_POST['UPDATE'])) {

}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Add To Memu";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Add To Menu - add products to the menu structure</h1>
	<br/>
    <table align="left" border="0" cellpadding="2" cellspacing="5">
	<!--- TOP LEVEL CATEGORY SELECTION------------------------------------------------------------------------------------------------------>
    <form name="enter-thumb" action="/_cms/add_to_menu.php" enctype="multipart/form-data" method="post">
        <tr>
        	<td>Top Level Category:</td>
            <td>
            	<select name="category" id="jumpMenu" onchange="MM_jumpMenu('parent',this,1)">
                	<option value="#">Choose from...</option>
                    <?php
					$categories = getCategories("0");
					foreach($categories as $c){
						if(isset($_GET['category']) and $_GET['category'] == $c->CA_CODE){
							$selected = "selected";
						}else{
							$selected = "";
						}
						echo "<option value=\"/_cms/add_to_menu.php?category=" . $c->CA_CODE . "\" " . $selected . ">" . $c->CA_NAME . "</option>";
					}
					?>
           		</select>
                <input name="CATEGORY_CURRENT" type="hidden" value="<?php echo isset($_POST['CATEGORY_CURRENT']) ? $_POST['CATEGORY_CURRENT'] : $category_current ?>">
            </td>
        </tr>
    <!--- SUBCATEGORIES SELECTION ----------------------------------------------------------------------------------------------------------->
        <?php
		if (isset($category)){
			echo "<tr>";
				echo "<td>Subcategory:</td>";
				echo "<td>";
					echo "<select name=\"subcategory\" id=\"jumpMenu\" onchange=\"MM_jumpMenu('parent',this,1)\">";
						echo "<option value=\"#\">Choose from...</option>";
						$tree = $category->CA_PARENT . "_" . $category->CA_CODE;
						$subcategories = getCategories($tree);
						foreach($subcategories as $s){
							if(isset($_GET['category']) and $_GET['category'] == $c->CA_CODE){
								$selected = "selected";
							}else{
								$selected = "";
							}
							echo "<option value=\"/_cms/add_to_menu.php?subcategory=" . $s->CA_CODE . "&tree=" . $tree . "\" " . $selected . ">" . $s->CA_NAME . "</option>";
						}
					echo "</select>";
					echo "<input name=\"CATEGORY_CURRENT\" type=\"hidden\" value=\"" . (isset($_POST['CATEGORY_CURRENT']) ? $_POST['CATEGORY_CURRENT'] : $category_current) . "\">";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan=\"2\">&nbsp;</td>";
			echo "</tr>";
			//--- PRODUCTS LISTING ----------------------------------------------------------------------------------------------------------->
			$products = getProductsInCategory($category->CA_CODE);
			$cntr1 = 0;
			foreach($products as $p){
				$product = getProductDetails($p->PC_PRODUCT);
				$cntr1 ++;
				echo "<tr>";
					echo "<td>Product " . $cntr1 . "</td>";
					echo "<td>";
						echo "<input type=\"text\" name=\"PRODUCT" . $cntr1 . "\" SIZE=\"70\" value=\"" . $product->PR_PRODUCT . " - " . $product->PR_NAME . "\" disabled >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						echo "<input name=\"DELETE\" type=\"submit\" value=\"Delete\">";
					echo "</td>";
				echo "</tr>";
			}
			echo "<tr>";
				echo "<td colspan=\"2\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Add Product:</td>";
				echo "<td>";
					echo "<input type=\"text\" name=\"ADD_PRODUCT\" SIZE=\"10\" value=\"\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<input name=\"ADD_PRODUCT\" type=\"submit\" value=\"Add\" />";
				echo "</td>";
			echo "</tr>";             
			echo "<tr>";
			//--- END OF PRODUCTS LISTING ---------------------------------------------------------------------------------------------------->
		}
		?> 
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>
			<td colspan="2"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>        
        <!---
        <tr>
            <td>Programmer Message:</td>
            <td><input type="text" name="PROGRAMMER_MESSAGE" SIZE="87" value="<?php echo $message ?>"></td>
        </tr>
        --->
	</form>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>

