<?php
include_once("includes/masterinclude.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title><?php echo $pageTitle ?></title>
    <meta name="description" content="<?php echo $pageMetaDescription ?>" />
	<meta name="keywords" content="<?php echo $pageMetaKeywords ?>" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="author" content="<?php echo $preferences->PREF_SHOPNAME ?>">
<?php
if(isset($productdetail)): ?>
	<meta property="og:title" content="<?php echo $pageTitle ?>" />
	<meta property="og:type" content="product" />
    <meta property="og:description" content="<?php echo (html_entity_decode($product->PR_DESC_SHORT)) ?>" />
	<meta property="og:url" content="<?php echo $preferences->PREF_SHOPURL . "/" . urlencode(html_entity_decode($product->PR_NAME, ENT_QUOTES)) . "/-/" . $product->PR_PRODUCT . ".htm" ?>" />
<?php
	$image_path = $preferences->PREF_SHOPURL . "/images/";
	if(strlen($product->PR_IMAGE_FOLDER) > 0){$image_path .= $product->PR_IMAGE_FOLDER . "/";}
	$image_path .= $product->PR_IMAGE;
?>
	<meta property="og:image" content="<?php echo $image_path ?>" />
<?php endif;?>
	<meta name="generator" content="1-ecommerce">
<?php 
	if(isset($canonical_product) and $canonical_product != ""){
		echo "<link rel=\"canonical\" href=\"" . $canonical_product . "\" />" . PHP_EOL;
	}
?>
    <link href="/theme/sitestyle.css" rel="stylesheet" />
    <script src="/common/language-en.js" type="text/javascript"></script>
    <script src="/common/sfcart.js" type="text/javascript"></script>
    <script src="/common/sfcart_quantity_discounts.js" type="text/javascript"></script>
    <script src="/common/shopfitter_core.js" type="text/javascript"></script>
    <script src="/common/products.js" type="text/javascript"></script>
    <script src="/common/general.js" type="text/javascript"></script>
    <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <script src="/common/review_validation.js" type="text/javascript"></script>
	<?php
    //set minimum order value for use by the shopping cart
	echo "<script>";
	echo "MinimumOrder=" . ($login == 1 ? $preferences->PREF_MIN_ORDER_TRADE : $preferences->PREF_MIN_ORDER) . ";";
	//echo "MinimumOrderPrompt='freddie';";
	echo "MinimumOrderPrompt='Your cart is below our " . ($login == 1 ? "Trade" : ""). " minimum order value of " . $currency_symbol . ($login == 1 ? $preferences->PREF_MIN_ORDER_TRADE : $preferences->PREF_MIN_ORDER) . ", please add more items';";
	echo "</script>";
	?>
    
    <!--[if lte IE 7]>
            <style type="text/css">
    .search input.box {
        background: url(/theme/theme-images/bb-search-box-ie.png) no-repeat right bottom; /* Unique Input Box background image for IE, must be aligned to the right*/
    }
        </style>
        
    <![endif]-->
    <?php
	//write tracking code details from preferences table
	echo html_entity_decode($preferences->PREF_CUSTOM_HEAD,ENT_QUOTES);
	?>
	
    <?php
	if (isset($pageCustomHead)){
		//write Page Custom Head details from categories or product table as applicable
		echo $pageCustomHead;
	}
	?>
    
</head>
<body>
<div class="ribbon">
<div class="body-wrapper">
        	<?php 
			if (isset($justloggedout)){
				$login_message = "You are now logged out";
				$login_class = "header-logout";
			}else{
				if ($login == 1){$login_message = "You are now logged in as " . $_SESSION['username']; $login_class="header-login";}else{$login_message = ""; $login_class = "";}
			}					
            ?>
            <div class="<?php echo $login_class?>">
                <?php echo $login_message?>
            </div>
            <!--
        	<div class="menu_login">       
				<?php
                //Member login button
                if ($login == 0){
                    echo "<form name=\"login_button\" action=\"/login_member.php\" method=\"post\">";
                        echo "<input name=\"MENU_LOGIN\" type=\"submit\" value=\"Trade Login\"/>";
                    echo "</form>";
                }else{
                    echo "<form name=\"logout_button\" action=\"/index.php?logout=1\" method=\"post\">";
                        echo "<input name=\"MENU_LOGOUT\" type=\"submit\" value=\"Logout\"/>";
                    echo "</form>";
                }
                ?>
            </div>
            -->		
		
<div class="banner-style">
		
            <?php
            $areadata=getAreadataPage("header");
            echo html_entity_decode($areadata->AR_DATA);
            ?>
			<!-- dynamic menu call -->
			<nav>
			<div class="topnav">
				<ul>
				<?php
					$enabled = "Y"; $edit = "All";
					$info = getAllInformation($enabled, $edit);
					$cntr1 = 0;
					foreach($info as $i){
						$cntr1 ++;
						if($infopagename == $i->IN_NAME){$class = " class=\"selected\"";}else{$class = "";}
						if($cntr1 < count($info)){
							echo "<li" . $class . "><a href=\"#\" ><span>" . $i->IN_NAME . "</span></a></li>";
						}else{
							echo "<li" . $class .  "><a href=\"#\" ><span>" . $i->IN_NAME . "</span></a></li>";
						}
					}
	                ?>
				</ul>
			</div>
			</nav>
</div>	

<div id="container">

<!-- end of header.php -->

