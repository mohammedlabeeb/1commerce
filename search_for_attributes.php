<?php
require_once("includes/session.php");
include_once("includes/masterinclude.php");

$preferences = getPreferences();
$pageTitle = "Search";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;
$products_found = array();
if(!isset($_POST['search'])){$_POST['search'] = "";}

//Current category details for the Attribute based Search Boxes
$top_level = $_POST['TOP_LEVEL']; $infopagename="";
$tree_current = $_POST['TREE_CURRENT'];
$c = getCategory($_POST['CATEGORY_CURRENT'], "");
$category = $c->CA_CODE;
$attribute1 = $c->CA_ATTRIBUTE1; $attribute2 = $c->CA_ATTRIBUTE2; $attribute3 = $c->CA_ATTRIBUTE3; $attribute4 = $c->CA_ATTRIBUTE4;
$attribute5 = $c->CA_ATTRIBUTE5; $attribute6 = $c->CA_ATTRIBUTE6; $attribute7 = $c->CA_ATTRIBUTE7; $attribute8 = $c->CA_ATTRIBUTE8;

include_once("includes/header.php");
?>
<!-- category.inc -->


<div class="body-content-info">


	<h1>Search Results</h1>

	<div class="search-message">
	
	</div>	
    <?php
	//$products = getAllProducts();
	//only search for products within the current category
	//echo "TREE NODE = " . $tree_current . "<br/>";
	$products_below_tree = getProductsBelowTreeNode($tree_current);
	foreach($products_below_tree as $pbt){
	//foreach($products as $p){
		$p = getProductDetails($pbt->PC_PRODUCT);
		//echo $p->PR_PRODUCT . "<br/>";
		$success = array(); $cntr_active = 0;
		for ($i=1; $i<=$_POST['ATTRIBUTES_COUNT']; $i++){
			if($_POST['ATTRIBUTE_VALUE_ID'.$i] != -1){
				$cntr_active ++;
				//For each searchbox with a selection value against it. Run through each product option box.
				//If the option box associated attribute = that of the current searchbox then search all option box rows
				$success[$cntr_active] = 0;
				$attribute_id = $_POST['ATTRIBUTE_ID'.$i]; 
				$attribute_value_id = $_POST['ATTRIBUTE_VALUE_ID'.$i];
				$attribute_value = getAttributeValue($attribute_value_id);
				$searchstring = $attribute_value->AV_NAME; //eg. Bamboo - This is the tag value we are searching for
				for ($x=1; $x<=4; $x++){
					//for each product option box
					$option = "PR_OPTION" . $x;
					if($p->$option >0){
						$selection = getSelection($p->$option);
						//if($selection->SE_ATTRIBUTE == $attribute_id){
							//the product option box has the same associated attribute as the current searchbox so search associated option lines for the search value
							//echo "Product " . $p->PR_PRODUCT . "  Option" . $x . "=" . $p->$option . " has same id as searchbox" . $i . " = " . $_POST['ATTRIBUTE_ID'.$i] . "<BR/>";
							$options = getOptions($selection, "N");
							foreach($options as $o){
								//if($o->OP_ATTRIBUTE_VALUE == $_POST['ATTRIBUTE_VALUE_ID'.$i]){
								//IMPORTANT!!! This change is required for the Global Search to work - we need to search on the actual search word and not simply the attribute id
								//so read the attribute record for the searchbox search word and compare this with the option attribute linked search word
								//echo $p->PR_PRODUCT . "/" . $o->OP_ATTRIBUTE_VALUE . "<BR/>";
								$searchstring_option1 = ""; $searchstring_option2 = ""; $searchstring_option3 = "";
								if($o->OP_ATTRIBUTE_VALUE1 != 0){
									//echo "OPTION=" . $o->OP_ID . " / " . $o->OP_ATTRIBUTE_VALUE1 . "<br/>";
									$attribute_value_option1 = getAttributeValue($o->OP_ATTRIBUTE_VALUE1);
									$searchstring_option1 = $attribute_value_option1->AV_NAME;
								}
								if($o->OP_ATTRIBUTE_VALUE2 != 0){
									$attribute_value_option2 = getAttributeValue($o->OP_ATTRIBUTE_VALUE2);
									$searchstring_option2 = $attribute_value_option2->AV_NAME;
								}
								if($o->OP_ATTRIBUTE_VALUE3 != 0){
									$attribute_value_option3 = getAttributeValue($o->OP_ATTRIBUTE_VALUE3);
									$searchstring_option3 = $attribute_value_option3->AV_NAME;
								}
								//echo "Searchstring from box = " . $searchstring . " OPTION =" . $o->OP_NAME . " Value = " . $searchstring_option . "<br/>";
								if($searchstring == $searchstring_option1 or $searchstring == $searchstring_option2 or $searchstring == $searchstring_option3){
									//match found so set "found" flag in array
									//echo " MATCH FOUND!!! " . "<br/>";
									$success[$cntr_active] = 1;
								}
							}
						//}
					}
				}
			}
		}
		//now scan the "success" array - if a product matches the search criteria it will have a "found" flag set in each slot of the array
		//echo "Success Array...";
		$ok = 1;
		for ($i=1; $i<=$cntr_active; $i++){
			//echo "Slot" . $i . "=\"" . $success[$i] . "\" ";
			if($success[$i] == 0){
				$ok = 0;
				//echo "FAILURE";
			}
		}
		//echo "OK=" . $ok . "</br></br>";
		if($ok == 1){
			$products_found[] = $p->PR_PRODUCT;
		}
	}
	
	//echo "<pre>";
		//print_r ($products_found);
	//echo "</pre>";
	
	//now display all products contained within the products found array
	$cntr_products = 0;
	asort($products_found);
	foreach($products_found as $pf){
		$cntr_products ++;
		$p = getProductDetails($pf);
		echo "<ol>";
		$imagePathProd = "";
		if(strlen($p->PR_IMAGE_FOLDER) > 0){$imagePathProd = $p->PR_IMAGE_FOLDER . "/";}
		$imagePathProd .= $p->PR_IMAGE;
		if(strlen($imagePathProd) == 0){
			$imagePathProd = "/images/thumbnoimage.jpg";
		}else{
			$imagePathProd = "/images/" . $imagePathProd;
		}
									
			$tree = getProductTree($p->PR_PRODUCT);
			$link = "/" . urlencode(html_entity_decode($p->PR_NAME, ENT_QUOTES)) . "/" . $tree . "/" . $p->PR_PRODUCT . ".htm";
			echo
				"		<li class=\"search-li\">
						<span><a class=\"thumbnail-search\" href=\"" . $link . "\"><img src=\"" . $imagePathProd . "\" alt=\"" . $p->PR_IMAGE_ALT . "\" height=\"100\" /></a></span>				
					<a class=\"thumbnail-search\" href=\"" . $link . "\">". html_entity_decode($p->PR_NAME, ENT_QUOTES) . "</a>
					<br />" . html_entity_decode($p->PR_DESC_SHORT, ENT_QUOTES) . "</li>" . PHP_EOL;
			$last_product = $p->PR_PRODUCT;
		echo "</ol>";
	}
    ?>
	<div class="line"></div>
	<div class="search-right"><?php echo $cntr_products ?> product(s) found</div>
	<p>&nbsp;</p>
	
<?php
  $tree = $tree_current;
  include_once("includes/footer.php");
?>
