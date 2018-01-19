<?php
require_once("includes/session.php");
include_once("includes/masterinclude.php");

$tree = $_GET['tree'];
$c = getParentfromTree($tree);
$top_level = get_top_level($tree); $infopagename="";

$catHeader = $c->CA_NAME;
$pagePath = " " . urlencode($c->CA_NAME) . " " . $tree . ".htm"; //pass page link to shopping cart via cookie
$top_content = html_entity_decode($c->CA_TOP_CONTENT); $bottom_content = html_entity_decode($c->CA_BOTTOM_CONTENT);
$tabular_listing = $c->CA_TABULAR_LISTING;
$preferences = getPreferences();
$currency = getCurrency($preferences->PREF_CURRENCY);
$currency_symbol = $currency->CU_SYMBOL;
$pageTitle = $c->CA_NAME;
if($c->CA_META_TITLE != ""){$pageTitle .= " " . $c->CA_META_TITLE;}else{$pageTitle .= $preferences->PREF_META_TITLE;}
$pageMetaDescription = $c->CA_META_DESC;
$pageMetaKeywords = $c->CA_META_KEYWORDS;
$pageCustomHead = html_entity_decode($c->CA_CUSTOM_HEAD, ENT_QUOTES);
$catDivWrap = $c->CA_DIV_WRAP;

//read current category details for attribute based search boxes
$category = $c->CA_CODE;
$attribute1 = $c->CA_ATTRIBUTE1; $attribute2 = $c->CA_ATTRIBUTE2; $attribute3 = $c->CA_ATTRIBUTE3; $attribute4 = $c->CA_ATTRIBUTE4;

include_once("includes/header.php");

?>

<div class="body-content">

	<h1><?php echo $catHeader ?></h1>
	
	<div class="cat-hotspot-1">
		<?php
        //Now to use Categories Top Content column rather than hotspots data
		echo html_entity_decode($top_content, ENT_QUOTES, "UTF-8");
        ?>
    </div>
    

    <!-- Products Holder -->
	<div class="products-holder <?php echo $catDivWrap ?>">
    <?php
	//list all products within parent category
	$products = getProducts($_GET['tree']);
	$adjustPrice = array();
	if(count($products) > 0){
		if($tabular_listing == "N"){
			foreach ($products as $p){
				$adjustPrice[] = $p->PC_PRODUCT; //add to price adjustments array
				$product = getProductDetails($p->PC_PRODUCT);
				if($product->PR_DISABLE == "N"){
					$imagePathProd = "";
					if(strlen($product->PR_IMAGE_FOLDER) > 0){$imagePathProd = $product->PR_IMAGE_FOLDER . "/";}
					$imagePathProd .= $product->PR_IMAGE;
					if(strlen($imagePathProd) == 0){
						$imagePathProd = "/images/thumbnoimage.jpg";
					}else{
						$imagePathProd = "/images/" . $imagePathProd;
					}	
					echo "<div class=\"product-list " .  $product->PR_PROD_WRAP . "\">";
						echo "<div class=\"product-list-image-wrap\">";
							echo "<img src=\"" . $imagePathProd . "\" alt=\"" . $product->PR_IMAGE_ALT . "\" />
							<div class=\"product-list-image\"><a href=\"/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $p->PC_TREE_NODE . "/" . $p->PC_PRODUCT . ".htm" . "\"><span>" . $product->PR_IMAGE_ALT . "</span></a></div>";
						echo "</div>";
						
						
						echo "<h2><a href=\"/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $p->PC_TREE_NODE . "/" . $p->PC_PRODUCT . ".htm" . "\">" . html_entity_decode($product->PR_NAME) . "<span class=\"more-details\">more...</span></a></h2>";
						
						if ($login == 1 and strlen($product->PR_DESC_TRADE) > 0){
							echo "<div class=\"cat-prod-description\">";
								echo html_entity_decode($product->PR_DESC_TRADE); 
							echo "</div>";
						}
						
						elseif ($login == 0 and strlen($product->PR_DESC_SHORT) > 0){
							echo "<div class=\"cat-prod-description\">";
								echo html_entity_decode($product->PR_DESC_SHORT); 
							echo "</div>";
						}
							
						//PRODUCT SALE
						if($product->PR_AVAILABILITY != "out of stock"){
							include("includes/productsale.php");
						}else{
						//	echo "<form action=\"sale\">";
								echo "<div class=\"cat-prod-nostock\">";
									echo html_entity_decode($product->PR_NO_STOCK, ENT_QUOTES);
								echo "</div>";
						//	echo "</form>";	
						}
					echo "</div>";
				}
			}
		}else{
			//display products in tabular listing format
			echo "<div id=\"product-list-tabular\">" . "\n";
				echo "<form action=\"sale-table\">" . "\n";
					echo "<table id=\"product-table\" cellpadding=\"2\" cellspacing=\"5\">" . "\n";
						echo "<tr>" . "\n";
						echo "<th>Name</th><th>Description</th>";
						if($preferences->PREF_EXVAT == "Y"){echo "<th>Ex Vat</th>";}		
						echo "<th>Price</th><th>Quantity</th>" . "\n";
						echo "</tr>" . "\n";
						$prodCntr = 0;
						foreach ($products as $p){
							$adjustPrice[] = $p->PC_PRODUCT; //add to price adjustments array
							$product = getProductDetails($p->PC_PRODUCT);
							if($product->PR_DISABLE == "N"){
								//If a member is logged in then Selling Price =Ttrade Price
								$selling = $product->PR_SELLING;
								if ($login == 1 and $product->PR_TRADE > 0){$selling = $product->PR_TRADE;}
								//get VAT inclusive price = PR_SELLING * VAT rate
								if($product->PR_TAX > 0){
									$vatrate = $product->PR_TAX;
								}else{
									$vatrate = $preferences->PREF_VAT;
								}
								$vatinc = addVAT($selling, $vatrate);	
								echo "<tr id=\"product-row_" . $prodCntr . "\">" . "\n";
									echo "<td id=\"prod-name_" . $prodCntr . "\" >";
										echo "<h2><a href=\"/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $p->PC_TREE_NODE . "/" . $p->PC_PRODUCT . ".htm" . "\">" . $product->PR_NAME . "</a></h2>";
									echo "</td>" . "\n";
									echo "<td id=\"prod-desc-short_" . $prodCntr . "\">" . "\n";
										if ($login == 1 and strlen($product->PR_DESC_TRADE) > 0){
											echo html_entity_decode($product->PR_DESC_TRADE); 
										}
										
										elseif ($login == 0 and strlen($product->PR_DESC_SHORT) > 0){
											echo html_entity_decode($product->PR_DESC_SHORT); 
										}
										$disabled = "";
										if($product->PR_AVAILABILITY == "out of stock"){
											echo "<br/><span class=\"out-of-stock\"><strong>" . html_entity_decode($product->PR_NO_STOCK, ENT_QUOTES) . "</strong></span>";
											$disabled = "disabled";
										}
									echo "</td>" . "\n";
									if ($preferences->PREF_EXVAT =="Y"){
										echo "<td class=\"prod-exvat\">";
											  echo "(" . $currency_symbol . "<span class=\"price\">" . $selling . "</span> ex VAT)";
										echo "</td>" . "\n";
									}
									echo "<td id=\"prod-selling_" . $prodCntr . "\">";
										if ($login == 0){
											echo "<span itemprop=\"price\" class=\"cat-prod-price\">" . $currency_symbol . "<span class=\"Gprice\">" . $vatinc . "</span></span>"; //standard price
										}else{
											echo $currency_symbol . "<span class=\"Gprice\">" . $vatinc . "</span>"; //trade/member price
										}
										//echo $currency_symbol . $product->PR_SELLING;
									if(strlen($product->PR_USER_STRING1) > 0){
        								echo "<div class=\"userstring1\">";
        									echo html_entity_decode($product->PR_USER_STRING1);
       									echo "</div>";
										}
									echo "</td>" . "\n";
									echo "<td id=\"prod-qty_" . $prodCntr . "\">";
										echo "<input name=\"QUANTITY_" . $prodCntr . "\" id=\"prod-qty\" size=\"3\" onchange=\"this.value=CKquantity(this.value)\" value=\"0\" " . $disabled . " />";
									//echo "</td>" . "\n";
									//echo "<td id=\"row-data_" . $prodCntr . "\">";
										//$vatinc = $product->PR_SELLING; $selling = 0;
										echo "<input type=\"hidden\" name=\"CURRENCY_SYMBOL_" . $prodCntr . "\" value=\"" . $currency_symbol . "\" />";
										echo "<input type=\"hidden\" name=\"PRICE_" . $prodCntr . "\" value=\"" . $vatinc . "\" />";
										echo "<input type=\"hidden\" name=\"PRICE_EXVAT_" . $prodCntr . "\" value=\"" . $selling . "\" />";
										echo "<input type=\"hidden\" name=\"NAME_" . $prodCntr . "\" value=\"" . html_entity_decode($product->PR_NAME) . "\" />";
										echo "<input type=\"hidden\" name=\"ID_NUM_" . $prodCntr . "\" value=\"" . $product->PR_PRODUCT . "\" />";
										echo "<input type=\"hidden\" name=\"SKU_" . $prodCntr . "\" value=\"" . $product->PR_SKU . "\" />";
										echo "<input type=\"hidden\" name=\"SHIPPING_" . $prodCntr . "\" value=\"" . $product->PR_SHIPPING . "\" />";
										echo "<input type=\"hidden\" name=\"TAX_" . $prodCntr . "\" value=\"" . $product->PR_TAX . "\" />";
										echo "<input type=\"hidden\" name=\"WEIGHT_" . $prodCntr . "\" value=\"" . $product->PR_WEIGHT . "\" />";
										echo "<input type=\"hidden\" name=\"TAXEXEMPTION_" . $prodCntr . "\" value=\"" . $product->PR_TAXEXEMPTION . "\" />";
										echo "<input type=\"hidden\" name=\"TAXRATE_" . $prodCntr . "\" value=\"0.00\" />";
										echo "<input type=\"hidden\" name=\"PAGE_LINK_" . $prodCntr . "\" value=\"" . $pagePath . "\" />";
									echo "</td>" . "\n";
								echo "</tr>" . "\n";
							}
							$prodCntr++;
						}
					echo "</table>" . "\n";
					echo "<p><input type=\"hidden\" name=\"ROW_COUNTER\" value=\"" . $prodCntr. "\" />";
					echo "<input type=\"button\" value=\" Add to Cart \" style=\"float: clear;\" onclick=\"return AddTableToCart(this.form);\" class=\"add-button\"/></p>";
				echo "</form>";
			echo "</div>" . "\n";
		}
	}
	?>
        
		
    <!-- CATEGORIES LISTING -->
	<?php
    //list all subcategories within parent category
    $categories = getCategories($_GET['tree']);
	if(count($categories) > 0){
		echo "<div class=\"catsub-prod-holder\">";
	}
        foreach($categories as $c){
			if($c->CA_DISABLE == "N"){
				$imagePath = "";
				if(strlen($c->CA_IMAGE_FOLDER) > 0){$imagePath = $c->CA_IMAGE_FOLDER . "/";}
				$imagePath .= $c->CA_IMAGE;
				echo "<div class=\"latest-prod\">";
				echo "<div class=\"latest-prod-image\" style=\"background-image: url(/images/" . str_replace(" ", "%20", $imagePath) . "); background-repeat:none;\"></div>";
				echo "<h2><a href=\"/". urlencode(html_entity_decode($c->CA_NAME, ENT_QUOTES)) . "/" . $c->CA_TREE_NODE . "_" . $c->CA_CODE . ".htm" . "\">" . $c->CA_NAME . "<span>" . $c->CA_DESCRIPTION . "</span></a></h2>";
				echo "</div>";
			}
        }
	if(count($categories) > 0){
		echo "</div>";
	}
    ?>
    </div>		
		
        <!-- end of products-holder -->
		<div class="cat-hotspot-2">
		    <?php
			//Now to use Categories Top Content column rather than hotspots data
			echo $bottom_content;
			?>
		</div>
	
	<div class="breadcrumb-holder">  
    	<?php
		//QUICK MENU - scan down the tree code generating entries against each category node
		$fstart = 0;
		$fend = 999;
		while ($fend > 0){
			//echo $tree . "<br/>";
			$fend = strpos($tree, "_", $fstart);
			//echo substr($tree, 0, $fend);
			if($fend > 0){
				$node = substr($tree, 0, $fend);
				if ($node == "0"){
					//home
					echo "<span class=\"breadcrumb\"><a href=\"/\">Home</a></span> &gt; ";
				}else{
					//category
					$menucat = getParentFromTree($node);
					echo "<span class=\"breadcrumb\"><a href=\"/" . urlencode(html_entity_decode($menucat->CA_NAME, ENT_QUOTES)) . "/" . $node . ".htm" . "\">" . html_entity_decode($menucat->CA_NAME, ENT_QUOTES) . "</a></span> &gt; ";
				}
			}else{
				//must be the last category within the tree node ie. the current parent so no point in generating a link to the same page
				$node = $tree;
				$menucat = getParentFromTree($node);
				echo "<span class=\"breadcrumb-here\">" . html_entity_decode($menucat->CA_NAME, ENT_QUOTES) . "</span>";
			}
			$fstart =$fend + 1;
		}
		?>
    </div>

	
	<!-- end of products-holder-firefox -->

<?php
//need to generate this code for the javascript price adjustment coding to work
echo "<script type=\"text/javascript\">";
echo "var Product = new Array();";
echo "var px=0;";
foreach($adjustPrice as $p){
	echo "Product[px++] = \"" . $p . "^0\";";
}
echo "</script>";

/*
echo "<script type=\"text/javascript\">";
echo "var Product = new Array();";
echo "var px=0;";
echo "Product[px++] = \"PRAD104^0\";";
echo "Product[px++] = \"PRAD107^0\";";
echo "Product[px++] = \"PRAD108^0\";";

echo "</script>";
*/
?>

<?php
  include_once("includes/footer.php");
?>
    
