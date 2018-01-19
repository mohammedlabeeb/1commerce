<?php
require_once("includes/session.php");
include_once("includes/masterinclude.php");
$tree = "0";
$c = "9999999";
$top_level = "0"; $infopagename="";
$category = "CAAA000";
$product = getProductDetails($_GET['product']);
$pagePath = " " . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . " " . $tree . " " . $product->PR_PRODUCT . ".htm"; //pass page link to shopping cart via cookie
$preferences = getPreferences();
$currency = getCurrency($preferences->PREF_CURRENCY);
$currency_symbol = $currency->CU_SYMBOL;
//get email setup details for review submittal
$email_setup = getEmailSetup();
//get page titles and meta descriptions for the header
$pageTitle = html_entity_decode($product->PR_NAME) . " " . $product->PR_META_TITLE;
$pageMetaDescription = $product->PR_META_DESC;
$pageMetaKeywords = $product->PR_META_KEYWORDS;
$pageCustomHead = html_entity_decode($product->PR_CUSTOM_HEAD, ENT_QUOTES);

//read current category details for attribute based search boxes
$category = "CAAA000";
$attribute1 = ""; $attribute2 = ""; $attribute3 = ""; $attribute4 = "";
$productdetail = true;

//get local page URL for this product and add canonical link if this is not the first occurence of the product within the tree
$product_url= $preferences->PREF_SHOPURL . "/". $product->PR_NAME . "/" . $tree . "/" . $product->PR_PRODUCT . ".htm";
$all_prods_on_tree = getProductFromProdcat($product->PR_PRODUCT); $canonical_product = "";
if($all_prods_on_tree[0]->PC_TREE_NODE != $tree){
	//then this product is found elsewhere on the tree, it is NOT the first found on prodcat and therefore requires a canonical link to the first found.
	$canonical_product = $preferences->PREF_SHOPURL . "/". $product->PR_NAME . "/" . $all_prods_on_tree[0]->PC_TREE_NODE . "/" . $product->PR_PRODUCT . ".htm";
}
if(isset($_POST['CREATE_REVIEW'])){
	if($preferences->PREF_PUBLISH == "Y"){
		$rv_published = "Y";
	}else{
		$rv_published = "N";
	}
	$fields = array("rv_product"=>$product->PR_PRODUCT, "rv_order"=>$_POST['RV_ORDER'], "rv_author"=>$_POST['RV_AUTHOR'], 
					"rv_town"=>$_POST['RV_TOWN'], "rv_country"=>$_POST['RV_COUNTRY'],
					"rv_rating"=>$_POST['RV_RATING'], "rv_title"=>$_POST['RV_TITLE'], "rv_text"=>$_POST['RV_TEXT'],
					"rv_published"=>$rv_published);
					
	$rows = Create_Review($fields);

	if ($rows == 1){
		if($preferences->PREF_PUBLISH == "Y"){
			$message = "Thank you for submitting a review against this product. It will now appear in our listings below.";
		}else{
			$message = "Thank you for submitting a review against this product. Subject to approval it will shortly appear in our listings below.";
		}
		$warning = "green";
		//now send email to shop to make them aware of the new review submittal
		$email_it_to = $email_setup->EM_REV_TO;
		$email_it_to_cc = $email_setup->EM_REV_CC;
		$email_it_to_bcc = $email_setup->EM_REV_BCC;
		$email_it_from = $preferences->PREF_EMAIL;
		$email_subject = $email_setup->EM_REV_SUBJECT;
		$email_confirmation = $email_setup->EM_REV_HEADER;
		$email_confirmation .= $email_setup->EM_REV_CONTENT;
		$email_confirmation .= "Name             : " . $_POST['RV_AUTHOR'] . " of " . $_POST['RV_TOWN'] . ", " . $_POST['RV_COUNTRY'] . "\r\n";
		$email_confirmation .= "Previous Order No: " . $_POST['RV_ORDER'] . "\r\n";
		$email_confirmation .= "Title            : " . $_POST['RV_TITLE'] . "\r\n";
		$email_confirmation .= "Review Text      : " . $_POST['RV_TEXT'] . "\r\n\r\n";
		$email_confirmation .= $email_setup->EM_REV_FOOTER;
		
		include_once("mailer/_process_from_top_level.php");
	}else{
		
	}
	if ($rows == 0){
		$message .= "Review Not Created - please contact Shopfitter";
		$warning = "red";
	}
	$error = null;
	$error = mysql_error();
	if ($error != null) { 
		$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
	}
}

include_once("includes/header_preview.php");
?>

<div itemscope itemtype="http://schema.org/Product" class="body-content <?php echo $product->PR_PROD_WRAP?>">
	
	<h1 itemprop="name"><?php echo html_entity_decode($product->PR_NAME) ?></h1>
	<?php 
    //HOTSPOT 1
    $hotspot = getHotspot($product->PR_PRODUCT, 1);
    if($hotspot){
        echo "<div class=\"prod-hotspot-1\">";
            echo html_entity_decode($hotspot->HS_DATA, ENT_QUOTES);
        echo "</div>";
    }
    ?>
	<div class="prod-holder">
			<div class="image-holder">
                <div class="main-product-image">
                    <?php
                    $imagePath = "/images/";
                    if(strlen($product->PR_IMAGE_FOLDER) > 0){$imagePath .= $product->PR_IMAGE_FOLDER . "/";}
                    $imagePath .= $product->PR_IMAGE;
                    //now get the name of the large image for fancybox
                    $posn = strpos($imagePath, ".");
                    $ext = substr($imagePath, $posn);
                    $imagePath_big = substr($imagePath, 0, $posn) . "big" . $ext;
					if(!file_exists(substr($imagePath_big, 1))){$imagePath_big = "";}
                    echo "<img itemprop=\"image\" id=\"zoom_03\" src=\"" . $imagePath . "\" alt=\"" . $product->PR_IMAGE_ALT . "\" data-zoom-image=\"" . $imagePath_big . "\" />";
                    ?>		
                </div>
                <div id="gallery_01">
                    <?php
                    $imagePath = "/images/";
                    if(strlen($product->PR_IMAGE_FOLDER) > 0){$imagePath .= $product->PR_IMAGE_FOLDER . "/";}
                    $imagePath .= $product->PR_IMAGE;
                    //now get the name of the large image for fancybox
                    $posn = strpos($imagePath, ".");
                    $ext = substr($imagePath, $posn);
                    $imagePath_big = substr($imagePath, 0, $posn) . "big" . $ext;
					if(!file_exists(substr($imagePath_big, 1))){$imagePath_big = "";}
                    echo "<a href=\"#\" class=\"elevatezoom-gallery active\" data-image=\"" . $imagePath . "\" data-zoom-image=\"" . $imagePath_big . "\">";
                    echo "<img src=\"" . $imagePath . "\" alt=\"" . $product->PR_IMAGE_ALT . "\" title=\"" . $product->PR_IMAGE_ALT . "\" /></a>";			
                    ?>		
                    <?php
                    $additional = getAdditionalImages($product->PR_PRODUCT);
                    foreach($additional as $a){
                        $imagePath = "/images/";
                        if(strlen($a->PRA_IMAGE_FOLDER) > 0){$imagePath .= $a->PRA_IMAGE_FOLDER . "/";}
                        $imagePath .= $a->PRA_IMAGE;
                        //now get the name of the large image for fancybox
                        $posn = strpos($imagePath, ".");
                        $ext = substr($imagePath, $posn);
                        $imagePath_big = substr($imagePath, 0, $posn) . "big" . $ext;
						if(!file_exists(substr($imagePath_big, 1))){$imagePath_big = "";}
                        echo "<a href=\"#\" class=\"elevatezoom-gallery\" data-image=\"" . $imagePath . "\" data-zoom-image=\"" . $imagePath_big . "\">";
                        echo "<img src=\"" . $imagePath . "\" alt=\"" . $a->PRA_IMAGE_ALT . "\" title=\"" . $a->PRA_IMAGE_ALT . "\" /></a>\n";
                    }
                    ?>
                    
                </div>
                <script type="text/javascript">
                    $(document).ready(function () {
                    $("#zoom_03").elevateZoom({gallery:'gallery_01', zoomType : 'inner', cursor: 'crosshair', galleryActiveClass: "active"}); 
                    
                    $("#zoom_03").bind("click", function(e) {  
                      var ez =   $('#zoom_03').data('elevateZoom');
                      ez.closeAll(); //NEW: This function force hides the lens, tint and window	
                        $.fancybox(ez.getGalleryList());
                      return false;
                    }); 
                    
                    }); 
        
                </script>

            </div>
			
			<div itemprop="description" class="prod-description"><?php echo html_entity_decode($product->PR_DESC_SHORT, ENT_QUOTES) ?></div>
            <?php
                if ($login == 1 and strlen($product->PR_DESC_TRADE) > 0){
                    echo "<div class=\"prod-summary-trade\">";
                        echo html_entity_decode($product->PR_DESC_TRADE, ENT_QUOTES);
                    echo "</div>";
                }
            ?>
            <?php
            //PRODUCT SALE
			//first check for quantity discounts
			$qd_flag = 0; $qdh_type = ""; $pref_vat = "";
			$value_options = Check_Option_Values($product->PR_PRODUCT);
			$promotions = On_Promotion($tree, $product->PR_PRODUCT); 
			$discount = Get_Qdiscount($product->PR_PRODUCT);
			if($value_options == false and empty($promotions) and $discount and $login == 0){
				//at this point the rules are Quantity Discount will only become available if...
				//a.) a product has NO selection dropdowns where at least one option line has a value adjustment setup ie. no option values allowed
				//b.) the product is not currently on promotion
				$qd_flag = 1; $qdh_type = $discount->QDH_TYPE; $pref_vat = $preferences->PREF_VAT;
			}
            if($product->PR_AVAILABILITY != "out of stock"){
                include("includes/productsale_preview.php");
            }else{
            //	echo "<form action=\"sale\">";
                    echo "<div class=\"cat-prod-nostock\">";
                        echo html_entity_decode($product->PR_NO_STOCK, ENT_QUOTES);
                    echo "</div>";
            //	echo "</form>";	
            }
            ?>
            <!-- QUANTITY DISCOUNT TABLE -------------------------------------------------------------------->
            <?php
			if($qd_flag == 1){
				//qd_flag is set so print matrix
				echo "<div id=\"quantity_discount_matrix\">";
					$discounts = Get_Qdiscount_Lines($product->PR_PRODUCT);
					if($discounts){
						$qd_flag = 1; $pref_vat = $preferences->PREF_VAT;
						echo "<span>Volume Pricing:</span>";
						echo "<table>";
							echo "<tr>";
								echo "<th>Quantity</th>";
								echo "<th>Price</th>";
							echo "</tr>";
							foreach($discounts as $d){
								$selling_qd = Get_QD_Matrix_Selling($preferences, $product, $d->QDH_TYPE, $d->QDL_ADJUST);
								$qdh_type = $d->QDH_TYPE;
								echo "<tr>";
									echo "<td>";
										echo $d->QDL_QTY;
									echo "</td>";
									echo "<td>";
										echo $currency_symbol . $selling_qd;
									echo "</td>";
								
								echo "<tr>";
							}
						echo "</table>";
					}
				echo "</div>";
			}
            ?>
			<!-- END OF QUANTITY DISCOUNT TABLE ------------------------------------------------------------->
            
            <div class="prod-summary">
                <?php echo html_entity_decode($product->PR_DESC_LONG, ENT_QUOTES) ?>
            </div>
            
            <div>
			<?php 
			//HOTSPOT 2
			$hotspot = getHotspot($product->PR_PRODUCT, 2);
			if($hotspot){
				echo "<div class=\"prod-hotspot-2\">";
					echo html_entity_decode($hotspot->HS_DATA, ENT_QUOTES);
				echo "</div>";
			}
			?> 
            </div>
<!-- PRODUCT REVIEWS - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
            <?php if($preferences->PREF_REVIEWS == "Y"): ?>
            <div class="prod-review">
                <h3>Customer Reviews</h3>
                <?php
                echo "<div id=\"reviews\">";
                    $star_rating = Get_Stars($product->PR_PRODUCT);
                    $stars = "/theme/theme-images/star_" . ($star_rating * 10) . ".png";
					$reviews = Get_Published_Reviews($product->PR_PRODUCT);
					if(count($reviews) == 1){$reviews_text = "review";}else{$reviews_text = "reviews";}
					
					echo "<div itemprop=\"aggregateRating\" itemscope=\"\" itemtype=\"http://schema.org/AggregateRating\">";
						echo "<img alt=\"Rated Average " . $star_rating . "/5.0\" src=\"". $stars . "\" style=\"vertical-align: text-bottom\"
							title=\"Rated Average " . $star_rating . "/5.0\" height=\"16\" width=\"85\" />";
						echo "<span itemprop=\"ratingValue\"> " . $star_rating . "</span>/5.0 stars - <span itemprop=\"reviewCount\">" . count($reviews) . "</span> ";
						echo $reviews_text . "&nbsp;&nbsp;&nbsp;&nbsp;";
						if($star_rating > 0){
							echo "<input name=\"CREATE_REVIEW\" type=\"button\" value=\"Create your own review\" onclick=\"show_review_form();\" class=\"small-button\" />";
						}else{
							echo "<input name=\"CREATE_REVIEW\" type=\"button\" value=\"Be the first to review this product\" onclick=\"show_review_form();\" class=\"small-button\" disabled />";
						}
					echo "</div>";
                    ?>
                    <form id="review_form" action="<?php $pagePath ?>" METHOD="post" onsubmit="return validate_review();">
<!--                        <table align="left" border="0" cellpadding="2" cellspacing="5">
-->                        <table>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Name:</td>
                                <td>
                                    <input name="RV_AUTHOR" class="review" type="text" size="22" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Town:</td>
                                <td>
                                    <input name="RV_TOWN" class="review" type="text" size="22" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Country:</td>
                                <td>
                                    <input name="RV_COUNTRY" class="review" type="text" size="22" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Previous<br/>Order:</td>
                                <td>
                                    <input name="RV_ORDER" class="review" type="text" size="22" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Rating:</td>
                                <td>
                                    <select name="RV_RATING" class="review" style="width: 50px; background-color: #fff9c2;">
                                        <option value="1.0" >1.0</option>
                                        <option value="1.5" >1.5</option>
                                        <option value="2.0" >2.0</option>
                                        <option value="2.5" >2.5</option>
                                        <option value="3.0" >3.0</option>
                                        <option value="3.5" >3.5</option>
                                        <option value="4.0" >4.0</option>
                                        <option value="4.5" >4.5</option>
                                        <option value="5.0" selected >5.0</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Title:</td>
                                <td>
                                    <input name="RV_TITLE" class="review" type="text" style="width: 580px;" maxlength="60" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Text:</td>
                                <td>
                                    <textarea name="RV_TEXT" class="review" type="text" style="width: 580px; height:100px;" value=""></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input name="CREATE_REVIEW" class="small-button" type="submit" size="22" value="Add Review" />
                                    <input name="RV_PUBLISHED" type="hidden" value="<?php echo $preferences->PREF_PUBLISH ?>" />
                                    <br/>
                                </td>
                            </tr>
                            <tr>
                               <td></td>
                               <td>
                                    <label id="MESSAGE" class="review"></label>
                               </td>
                            </tr>
                        </table>
                    </form>
                    <?php
                    //List Products
                    foreach($reviews as $r){
						$stars = "/theme/theme-images/star_" . ($r->RV_RATING * 10) . ".png";
						/*This is Simon's requested format
						echo "<div itemprop=\"review\" itemscope=\"\" itemtype=\"http://schema.org/Review\">";
							echo "<p>";
								echo "<span itemprop=\"name\" style=\"font-weight: bold;\">" . $product->PR_NAME . "</span> - by ";
								echo "<span itemprop=\"author\" style=\"font-weight: bold;\">" . $r->RV_AUTHOR . "</span>, &nbsp; " . unpack_review_date($r->RV_DATE);
							echo "</p>";
							echo "&nbsp;<p itemprop=\"description\">" . $r->RV_TEXT . "</p>";
							echo "<div itemprop=\"reviewRating\" itemscope=\"\" itemtype=\"http://schema.org/Rating\">";
								echo "<img alt=\"Rated " . $r->RV_RATING . "/5.0\" src=\"" . $stars . "\" style=\"vertical-align: text-bottom; margin-top: 3px;\"
									title=\"Rated " . $r->RV_RATING . "/5.0\" border=\"0px\" height=\"16px\" width=\"85px\">&nbsp";
								echo "<span itemprop=\"ratingValue\">" . $r->RV_RATING . "</span>/<span itemprop=\"bestRating\">5.0</span> stars";
							echo "</div>";
						echo "</div>";*/
						
						//but I've gone with this layout because it's what I designed for based on Amazon
						echo "<div class=\"review_wrap\" itemprop=\"review\" itemscope=\"\" itemtype=\"http://schema.org/Review\">";
							echo "<div class=\"review_stars\" itemprop=\"reviewRating\" itemscope=\"\" itemtype=\"http://schema.org/Rating\">";
								echo "<img alt=\"Rated " . $r->RV_RATING . "/5.0\" src=\"" . $stars . "\" 
									title=\"Rated " . $r->RV_RATING . "/5.0\" />";
								echo "<span itemprop=\"ratingValue\">" . $r->RV_RATING . "</span>/<span itemprop=\"bestRating\">5.0</span> stars";
							echo "</div>";
							echo "<div class=\"review_subheader\"><p><strong>" . $r->RV_TITLE . "</strong>, " . unpack_review_date($r->RV_DATE) . "</p>";
							echo "<p>By <span class=\"author\" itemprop=\"author\">" . $r->RV_AUTHOR . "</span> (" . $r->RV_TOWN . ", " . $r->RV_COUNTRY . ")</p></div>";
							//echo "<p>" . $r->RV_TEXT . "</p>";
							echo "<div class=\"review_text-wrap\">";
								echo "<p itemprop=\"description\">" . $r->RV_TEXT . "</p>";
							echo "</div>";
							if($r->RV_REPLY != ""){
								echo "<label style=\"margin-left: 10px;\">Reply:<br/></label>";
								echo "<div class=\"reply_text-wrap\">";
									echo "<p>" . $r->RV_REPLY . "</p>";
								echo "</div>";
							}
						echo "</div>";
						
                    }
                    echo "<br/>";
                ?>	
                </div>
            </div>
            <?php endif; ?>
<!-- END OF PRODUCT REVIEWS - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
	</div>
<!-- ADDITIONAL PRODUCTS -->
    <?php
	$product_save = $product->PR_PRODUCT;
	//list all additional products
    $additional = getAdditionalProducts($product->PR_PRODUCT);
	if(count($additional) > 0){
		echo "<div class=\"rel-box\">";
			echo "<h2>Related Items</h2>";
					echo "<div class=\"products-holder\">";
					
			//list all additional products
			$additional = getAdditionalProducts($product->PR_PRODUCT);
			
			$adjustPrice = array(); $prodCntr = 0;
			foreach ($additional as $a){
				//get tree node
				$product_tree = getProductTree($a->AP_ADDITIONAL);
				//add to price adjustments array
				$adjustPrice[] = $a->AP_ADDITIONAL;
				$product = getProductDetails($a->AP_ADDITIONAL);
				if($product->PR_DISABLE == "N"){
					$imagePathProd = "";
					if(strlen($product->PR_IMAGE_FOLDER) > 0){$imagePathProd = $product->PR_IMAGE_FOLDER . "/";}
					$imagePathProd .= $product->PR_IMAGE;
					if(strlen($imagePathProd) == 0){
						$imagePathProd = "/images/thumbnoimage.jpg";
					}else{
						$imagePathProd = "/images/" . $imagePathProd;
					}
					
					echo "<div class=\"product-list\">";
		
						echo "<div class=\"product-list-image-wrap\">";
							echo "<img src=\"" . $imagePathProd . "\" alt=\"" . $product->PR_IMAGE_ALT . "\" />
							<div class=\"product-list-image\"><a href=\"/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES, "UTF-8")) . "/" . $product_tree . "/" . $a->AP_ADDITIONAL . ".htm" . "\"><span>" . $product->PR_IMAGE_ALT . "</span></a></div>";
						echo "</div>";
						
						
						echo "<h2><a href=\"/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES, "UTF-8")) . "/" . $product_tree . "/" . $a->AP_ADDITIONAL . ".htm" . "\">" . html_entity_decode($product->PR_NAME, ENT_QUOTES, "UTF-8") . "<span class=\"more-details\">more...</span></a></h2>";
						
						if ($login == 1 and strlen($product->PR_DESC_TRADE) > 0){
							echo "<div class=\"cat-prod-description\">";
								echo html_entity_decode($product->PR_DESC_TRADE, ENT_QUOTES, "UTF-8"); 
							echo "</div>";
						}
						
						elseif ($login == 0 and strlen($product->PR_DESC_SHORT) > 0){
							echo "<div class=\"cat-prod-description\">";
								echo html_entity_decode($product->PR_DESC_SHORT, ENT_QUOTES, "UTF-8"); 
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
							include("includes/productsale_preview.php");
						}else{
						//	echo "<form action=\"sale\">";
								echo "<div class=\"cat-prod-nostock\">";
									echo html_entity_decode($product->PR_NO_STOCK, ENT_QUOTES, "UTF-8");
								echo "</div>";
						//	echo "</form>";	
						}
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
		echo "</div>";
		echo "</div>";
	}	
    ?>
<!-- END OF ADDITIONAL PRODUCTS -->
	<?php 
	$product = getProductDetails($product_save);
	
	//HOTSPOT 3
	$hotspot = getHotspot($product->PR_PRODUCT, 3);
	if($hotspot){
		echo "<div class=\"prod-hotspot-3\">";
			echo html_entity_decode($hotspot->HS_DATA, ENT_QUOTES);
			echo "<p class=\"spacer\">&nbsp;</p>";
		echo "</div>";
	}
	?>
	<div class="breadcrumb-holder">
    
    </div>	
	
<?php
//script neccessary for the price adjustment javascript functions to work
echo "<script type=\"text/javascript\">";
echo "var Product = new Array();";
echo "var px=0;";
echo "Product[px++] = \"" . $product->PR_PRODUCT . "^0\";";
echo "</script>";
?>
<!-- link to fancybox code -->
<script type="text/javascript" src="/js/jquery.elevateZoom-2.5.5.min.js"></script>
<script type="text/javascript" src="/js/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" href="/js/jquery.fancybox.css" type="text/css" media="all" />

<?php
  include_once("includes/footer_preview.php");
?>