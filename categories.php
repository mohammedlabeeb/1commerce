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
//check visitor access to the shop
if($preferences->PREF_SHOP_ACCESS == "N" and $login == 0): ?>
	<script>
		window.location = "/Restricted-Access.htm";
	</script>
<?php endif;
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
$attribute5 = $c->CA_ATTRIBUTE5; $attribute6 = $c->CA_ATTRIBUTE6; $attribute7 = $c->CA_ATTRIBUTE7; $attribute8 = $c->CA_ATTRIBUTE8;

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
    
	<div class="products-holder <?php echo $catDivWrap ?>">
    <?php
	//list all products within parent category
	$products = getProducts($_GET['tree']);
	$adjustPrice = array();
	if(count($products) > 0){
		if($tabular_listing == "N"){
			$prodCntr = 0;
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
					// echo "<div class=\"product-holder\">";
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
							}elseif ($login == 0 and strlen($product->PR_DESC_SHORT) > 0){
								echo "<div class=\"cat-prod-description\">";
									echo html_entity_decode($product->PR_DESC_SHORT); 
								echo "</div>";
							}
							//PRODUCT SALE
							$value_options = Check_Option_Values($product->PR_PRODUCT);
							$promotions = On_Promotion($tree, $product->PR_PRODUCT); $qd_flag = 0; $qdh_type = ""; $pref_vat = "";
							$discount = Get_Qdiscount($product->PR_PRODUCT);
							if($discount and empty($promotions) and $value_options == false and $login == 0){
								//at this point the rules are Quantity Discount will only become available if...
								//a.) a product has NO selection dropdowns where at least one option line has a value adjustment setup ie. no option values allowed
								//b.) the product is not currently on promotion
								$qd_flag = 1; $qdh_type = $discount->QDH_TYPE; $pref_vat = $preferences->PREF_VAT;
							}
							if($product->PR_AVAILABILITY != "out of stock"){
								include("includes/productsale.php");
							}else{
							//	echo "<form action=\"sale\">";
									echo "<div class=\"cat-prod-nostock\">";
										echo html_entity_decode($product->PR_NO_STOCK, ENT_QUOTES);
									echo "</div>";
							//	echo "</form>";	
							}
							//Product Review Rating--------------------------------------------------
	/*						if($preferences->PREF_REVIEWS == "Y"){
								echo "<div id=\"cat_reviews\">";
									$star_rating = Get_Stars($product->PR_PRODUCT);
									$stars = "/theme/theme-images/star_" . ($star_rating * 10) . ".png";
									echo "<img src=\"" . $stars . "\" />&nbsp;";
									echo $star_rating . "/5.0 ";
									$reviews = Get_Published_Reviews($product->PR_PRODUCT);
									echo "(" . count($reviews) , ") &nbsp;&nbsp;";
								echo "</div>";
							}*/
							//End of Product Review Rating
					//	echo "</div>";
						//QUANTITY DISCOUNTS "DROPDOWN" --------------------------------------------------------------------------------------------------------
						if($qd_flag and $product->PR_AVAILABILITY != "out of stock"){
							echo "<div class=\"show-hide-normal\">";
								echo "<input id=\"faq_" . $prodCntr . "\" type=\"checkbox\">";
								echo "<h3><label for=\"faq_" . $prodCntr . "\">Volume Pricing</label></h3>";
								$qd_flag = 0;
								$discounts = Get_Qdiscount_Lines($product->PR_PRODUCT);
								if($discounts){
									$qd_flag = 1;
									foreach($discounts as $d){
										$selling_qd = Get_QD_Matrix_Selling($preferences, $product, $d->QDH_TYPE, $d->QDL_ADJUST);
										echo "<p>" . $currency_symbol . $selling_qd . "<span>" . $d->QDL_QTY . "+</span></p>";		
									}
								}
							echo "</div>";
						}
						//END OF QUANTITY DISCOUNT "DROPDOWN" -------------------------------------------------------------------------------------------------	

					echo "</div>";
				}
				$prodCntr++;
			}
		}else{
			//display products in tabular listing format
			echo "<div id=\"product-list-tabular\">" . "\n";
				echo "<div class=\"prod-heading\">Product Information</div>";
				echo "<form action=\"sale-table\">" . "\n";

						//need to run through each of the products and find if any are flagged as tax Exempt in which case we MUST always show exVAT
						$tax_exempt = 0;
						foreach($products as $p){
							$product = getProductDetails($p->PC_PRODUCT);
							if($product->PR_TAXEXEMPTION == "Y"){$tax_exempt = 1; break;}
						}		

						$prodCntr = 0;
						foreach ($products as $p){
							echo "<div class=\"product-list-table\">";
								$adjustPrice[] = $p->PC_PRODUCT; //add to price adjustments array
								$product = getProductDetails($p->PC_PRODUCT);
								if($product->PR_DISABLE == "N"){
									//CHECK FOR PROMOTION CODE INPUT ------------------------>
									//determine whether or not product is on a current promotion and if so allow entry of code
									$promotions = On_Promotion($tree, $product->PR_PRODUCT);
									$promo_code_valid = ""; $promo_code_user = ""; $promo_type = ""; $promo_adjust = "";
									if(!empty($promotions)){
										//product is on at least 1 promotion
										//At this point each product may only be found against a single promotion so if it's inadvertantly on more than 1 then 
										//it's tough luck because we're only taking the first found code as valid
										$promo_code_valid = $promotions[0]->PROML_NO;
										$promo_type = $promotions[0]->PROMH_PROM_ID;
										$promo_adjust = $promotions[0]->PROMH_ADJUST;
									}
									//CHECK FOR QUANTITY DISCOUNT --------------------------->
									//first check that no option lines have value adjustments set against them
									$value_options = Check_Option_Values($product->PR_PRODUCT);
									$qd_flag = 0; $qdh_type = ""; $pref_vat = "";
									$discount = Get_Qdiscount($product->PR_PRODUCT);
									if($discount and empty($promotions) and $value_options == false and $login == 0){
										//at this point the rules are Quantity Discount will only become available if...
										//a.) a product has NO option lines with value adjustments setup - may change later but over complex for now
										//b.) the product is NOT currently on promotion
										$qd_flag = 1; $qdh_type = $discount->QDH_TYPE; $pref_vat = $preferences->PREF_VAT;
									}
									//TRADE DISCOUNT --------------------------------------------------------------------------------------------------------------->
									$selling = $product->PR_SELLING;
									if ($login == 1 and $product->PR_TRADE > 0){
										$selling = $product->PR_TRADE;
										$member = Get_Member($_SESSION['username']);
										$pricecat = $member->MB_CATEGORY;
										if($pricecat){
											//a member has a price category assigned to him so find any matching price category adjustment set up against the product
											$adjust = Get_Pcat_Adj($product->PR_PRODUCT, $pricecat);
											if($adjust){
												$pch = Get_Pcat($product->PR_PRODUCT); $ptype = $pch->PCH_TYPE;
												switch($ptype){
													case "PC":
														//as of 09/12/13 the only type of adjustments are percentage multipliers
														//If a member is logged in then Member Selling Price = Current Trade Price * any matching Price category multiplier set up against that product
														//ie. if a member is assigned to price category A and the adjustment against category A for the procuct in question = 95.25% then the
														//new trade price for that member will be Current Trade Price * 0.9525 rounded to 2dp.
														$selling = round($product->PR_TRADE * ($adjust/100), 2);
														//note that if a selling price is calculated for example as 4.995 then rounding to 2dp using the above expression will give us 5 
														//and not 5.00 - to ensure that doesn't happen we force the zero's to display using Fix_Price()
														$selling = Fix_Price($selling);
														break;
													default:
														break;
												}
											}else{
												//no product price category data set up against the product itself so use price categories set up against the default MASTER product 
												//ie. if the corresponding price category has been set up.
												$adjust = Get_Pcat_Adj("MASTER", $pricecat);
												if($adjust){
													$pch = Get_Pcat("MASTER"); $ptype = $pch->PCH_TYPE;
													switch($ptype){
														case "PC":
															//as of 09/12/13 the only type of adjustments are percentage multipliers - see explanation above
															$selling = round($product->PR_TRADE * ($adjust/100), 2);
															$selling = Fix_Price($selling);
															break;
														default:
															break;
													}
												}
											}
										}
									}
									//END OF TRADE DISCOUNT -------------------------------------------------------------------------------------------------------->
									//get VAT inclusive price = PR_SELLING * VAT rate
									if($product->PR_TAX > 0){
										$vatrate = $product->PR_TAX;
									}else{
										$vatrate = $preferences->PREF_VAT;
									}
									$vatinc = addVAT($selling, $vatrate);	

									echo "<div class=\"form-left\">";
										echo "<h2><a href=\"/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/" . $p->PC_TREE_NODE . "/" . $p->PC_PRODUCT . ".htm" . "\">" . html_entity_decode($product->PR_NAME,ENT_QUOTES) . "</a></h2>";
										echo "<div class=\"cat-prod-description\">";
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
										echo "</div>";
										if(strlen($product->PR_USER_STRING1) > 0){
											echo "<div class=\"userstring1\">Was: <span>";
												echo html_entity_decode($product->PR_USER_STRING1);
											echo "</span></div>";
										}
									echo "</div>";
									echo "<div class=\"form-right\">";
										echo "<div class=\"prod-exvat\">";
											if ($preferences->PREF_EXVAT =="Y" or $product->PR_TAXEXEMPTION == "Y"){
		
													echo "(" . $currency_symbol . "<span class=\"price_" . $prodCntr . "\">" . $selling . "</span> ex VAT)";
		
											}else{
		
											}
										echo "</div>";
/*										echo "<div class=\"prod-selling_" . $prodCntr . "\">";
											if ($login == 0){
												echo "<span itemprop=\"price\" class=\"cat-prod-price\">" . $currency_symbol . "<span class=\"Gprice_" . $prodCntr . "\">" . $vatinc . "</span></span>"; //standard price
											}else{
												echo $currency_symbol . "<span class=\"Gprice_" . $prodCntr . "\">" . $vatinc . "</span>"; //trade/member price
											}
											//echo $currency_symbol . $product->PR_SELLING;
										echo "</div>";*/
										echo "<div class=\"cat-prod-price\">";
											if($preferences->PREF_SELL_EXVAT == "N"){
												echo "<span itemprop=\"price\">" . $currency_symbol . "<span class=\"Gprice_" . $prodCntr . "\">" . $vatinc . "</span></span>"; //standard price
											}else{
												//Sell using ex-VAT/Tax Prices flag is set so show only exVAT prices
												echo "<span itemprop=\"price\">" . $currency_symbol . "<span class=\"Gprice_" . $prodCntr . "\">" . $selling . "</span></span>"; //standard price
											}
										echo "</div>";
										echo "<div class=\"quantity\">";
											echo "<input name=\"QUANTITY_" . $prodCntr . "\" id=\"prod-qty\" size=\"3\" onkeyup=\"this.value=CKquantity(this.value, 1); ApplyQuantityDiscountToTable(this.form, " . $prodCntr . ", '" . $preferences->PREF_SELL_EXVAT . "')\" onchange=\"this.value=CKquantity(this.value, 0)\" value=\"0\" " . $disabled . " />";
										echo "</div>";

										//$vatinc = $product->PR_SELLING; $selling = 0;
										echo "<input type=\"hidden\" name=\"CURRENCY_SYMBOL_" . $prodCntr . "\" value=\"" . $currency_symbol . "\" />";
										echo "<input type=\"hidden\" name=\"PRICE_" . $prodCntr . "\" value=\"" . $vatinc . "\" />";
										echo "<input type=\"hidden\" name=\"PRICE_EXVAT_" . $prodCntr . "\" value=\"" . $selling . "\" />";
										echo "<input type=\"hidden\" name=\"BASE_PRICE_" . $prodCntr . "\" value=\"" . $vatinc . "\" />";
										echo "<input type=\"hidden\" name=\"NAME_" . $prodCntr . "\" value=\"" . html_entity_decode($product->PR_NAME) . "\" />";
										echo "<input type=\"hidden\" name=\"ID_NUM_" . $prodCntr . "\" value=\"" . $product->PR_PRODUCT . "\" />";
										echo "<input type=\"hidden\" name=\"SKU_" . $prodCntr . "\" value=\"" . $product->PR_SKU . "\" />";
										echo "<input type=\"hidden\" name=\"SHIPPING_" . $prodCntr . "\" value=\"" . $product->PR_SHIPPING . "\" />";
										echo "<input type=\"hidden\" name=\"TAX_" . $prodCntr . "\" value=\"" . $product->PR_TAX . "\" />";
										echo "<input type=\"hidden\" name=\"WEIGHT_" . $prodCntr . "\" value=\"" . $product->PR_WEIGHT . "\" />";
										echo "<input type=\"hidden\" name=\"TAXEXEMPTION_" . $prodCntr . "\" value=\"" . $product->PR_TAXEXEMPTION . "\" />";
										echo "<input type=\"hidden\" name=\"TAXRATE_" . $prodCntr . "\" value=\"0.00\" />";
										echo "<input type=\"hidden\" name=\"PAGE_LINK_" . $prodCntr . "\" value=\"" . $pagePath . "\" />";
										//required for Promotions only
										echo "<input type=\"hidden\" name=\"PROMO_CODE_VALID_" . $prodCntr . "\" value=\"" . $promo_code_valid . "\" />";
										echo "<input type=\"hidden\" name=\"PROMO_CODE_USER_" . $prodCntr . "\" value=\"" . $promo_code_user . "\" />";
										echo "<input type=\"hidden\" name=\"PROMO_TYPE_" . $prodCntr . "\" value=\"" . $promo_type . "\" />";
										echo "<input type=\"hidden\" name=\"PROMO_ADJUST_" . $prodCntr . "\" value=\"" . $promo_adjust . "\" />";
										//required for Quantity Discounts only -->
										echo "<input type=\"hidden\" name=\"QD_FLAG_" . $prodCntr . "\" value=\"" . $qd_flag . "\" />";
										echo "<input type=\"hidden\" name=\"QD_TYPE_" . $prodCntr . "\" value=\"" . $qdh_type . "\" />";
										echo "<input type=\"hidden\" name=\"PREF_VAT_" . $prodCntr . "\" value=\"" . $pref_vat . "\" />";
										
										//-- QUANTITY DISCOUNT TABLE --------------------------------------------------------------------
										if($qd_flag == 1){	
											echo "<div class=\"show-hide\">";
												echo "<input id=\"faq_" . $prodCntr . "\" type=\"checkbox\">";
												echo "<h3><label for=\"faq_" . $prodCntr . "\">Volume Pricing</label></h3>";
												$qd_flag = 0;
												$discounts = Get_Qdiscount_Lines($product->PR_PRODUCT);
												if($discounts){
													$qd_flag = 1;
													foreach($discounts as $d){
														$selling_qd = Get_QD_Matrix_Selling($preferences, $product, $d->QDH_TYPE, $d->QDL_ADJUST);
														echo "<p>" . $currency_symbol . $selling_qd . "<span>" . $d->QDL_QTY . "+</span></p>";
													}
												}
											echo "</div>";
										}
										//-- END OF QUANTITY DISCOUNT TABLE -------------------------------------------------------------

										
										//get Option Sets - Limited to 4 only as of 17/01/11 since the sfcart.js can currently only cope with 4
										if($product->PR_OPTION1 > 0){
											$selection = getSelection($product->PR_OPTION1);
											if($selection->SE_EXCLUDE == "N"){
												$options = getOptions($selection, "N");
												echo "<br>";
												echo "<select name=\"ADDITIONALINFO_" . $prodCntr . "\" onchange=\"changeprice(this, " . $prodCntr . ", '" . $preferences->PREF_SELL_EXVAT . "', " . $qd_flag . ");\" >";
												echo "<option value=\"0^0.00^0\">Select " . html_entity_decode($selection->SE_NAME, ENT_QUOTES) . "</option>";
												foreach($options as $o){
													//value from the options table will be in line with the selling price from the product record which is now to be enforced as VAT exclusive but...
													//the PRICE is passed to the cart as VAT inclusive so... we need to add on the VAT to the options adjustment price for the price changes to work correctly.
													if($preferences->PREF_SELL_EXVAT == "N"){$option_value = addVAT($o->OP_VALUE, $vatrate);}else{$option_value = $o->OP_VALUE;}
													echo "<option value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "^" . $option_value . "^" . $o->OP_SKU . "\">" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "</option>";
												}
												echo "</select><br>";
											}
										}
								
										if($product->PR_OPTION2 > 0){
											$selection = getSelection($product->PR_OPTION2);
											if($selection->SE_EXCLUDE == "N"){
												$options = getOptions($selection, "N");
												echo "<select name=\"ADDITIONALINFO2_" . $prodCntr . "\" onchange=\"changeprice(this, " . $prodCntr . ");\" >";
												echo "<option value=\"0^0.00^0\">Select " . html_entity_decode($selection->SE_NAME, ENT_QUOTES) . "</option>";
												foreach($options as $o){
													echo "<option value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "^" . addVAT($o->OP_VALUE, $vatrate) . "^" . $o->OP_SKU . "\">" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "</option>";
												}
												echo "</select><br>";
											}
										}
										if($product->PR_OPTION3 > 0){
											$selection = getSelection($product->PR_OPTION3);
											if($selection->SE_EXCLUDE == "N"){
												$options = getOptions($selection, "N");
												echo "<select name=\"ADDITIONALINFO3_" . $prodCntr . "\" onchange=\"changeprice(this, " . $prodCntr . ");\" >";
												echo "<option value=\"0^0.00^0\">Select " . html_entity_decode($selection->SE_NAME, ENT_QUOTES) . "</option>";
												foreach($options as $o){
													echo "<option value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "^" . addVAT($o->OP_VALUE, $vatrate) . "^" . $o->OP_SKU . "\">" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "</option>";
												}
												echo "</select><br>";
											}
										}
										if($product->PR_OPTION4 > 0){
											$selection = getSelection($product->PR_OPTION4);
											if($selection->SE_EXCLUDE == "N"){
												$options = getOptions($selection, "N");
												echo "<select name=\"ADDITIONALINFO4_" . $prodCntr . "\" onchange=\"changeprice(this, " . $prodCntr . ");\" >";
												echo "<option value=\"0^0.00^0\">Select " . html_entity_decode($selection->SE_NAME, ENT_QUOTES) . "</option>";
												foreach($options as $o){
													echo "<option value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "^" . addVAT($o->OP_VALUE, $vatrate) . "^" . $o->OP_SKU . "\">" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "</option>";
												}
												echo "</select><br>";
											}
										}
										echo "<br>";
									echo "</div>";
								}
								$prodCntr++;
							echo "</div>";
						}

					echo "<p><input type=\"hidden\" name=\"ROW_COUNTER\" value=\"" . $prodCntr. "\" />";
					echo "<input type=\"button\" value=\" Add to Cart \" style=\"float: clear;\" onclick=\"return AddTableToCart(this.form, '" . $preferences->PREF_SELL_EXVAT . "');\" class=\"add-button\"/></p>";
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
				echo "<div class=\"latest-prod-image\" style=\"background-image: url(/images/" . str_replace(" ", "%20", $imagePath) . ");\"></div>";
				echo "<h2><a href=\"/". urlencode(html_entity_decode($c->CA_NAME, ENT_QUOTES)) . "/" . $c->CA_TREE_NODE . "_" . $c->CA_CODE . ".htm" . "\">" . $c->CA_NAME . "<span>" . html_entity_decode($c->CA_DESCRIPTION, ENT_QUOTES) . "</span></a></h2>";
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
    
