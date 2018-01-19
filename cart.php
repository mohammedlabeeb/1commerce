<?php
require_once("includes/session.php");
include_once("includes/masterinclude.php");

$preferences = getPreferences();
$currency = getCurrency($preferences->PREF_CURRENCY);
$currency_symbol = $currency->CU_SYMBOL;
//$name = $_GET['page'];
$information = getInformationPage('Cart');
$pageTitle = html_entity_decode($information->IN_TITLE);
$pageMetaDescription = html_entity_decode($information->IN_META_DESC);
$pageMetaKeywords = html_entity_decode($information->IN_META_KEYWORDS);
$pageCustomHead = html_entity_decode($information->IN_CUSTOM_HEAD, ENT_QUOTES);
$infopagename=$information->IN_NAME;
$category = "";
$attribute1 = ""; $attribute2 = ""; $attribute3 = ""; $attribute4 = "";
$top_level = "0"; $infopagename="";

include_once("includes/header.php");
?>

<script type="text/javascript" src="/common/products_empty.js"></script>
<script type="text/javascript" src="/common/shopfitter_core_secure.js"></script>		

<div class="body-content-info">
	<div class="info-heading">
		<h1><?php echo $information->IN_NAME ?></h1>
	</div>
	<div class="cart-content">
		<p>The items listed below are currently in your <?php echo $information->IN_NAME ?>:</p>
        <form action="/cart" method="get" onsubmit="return ValidateCart(this);">	
			<div class="cart-pos">
				<script type="text/javascript">
				<!--
						ManageCart("<?php echo $preferences->PREF_SELL_EXVAT; ?>");
				//-->
				</script>
				<input type="button" value="Update" class="small-button" />
			</div>
		</form>
        <?php
		if(strpos($preferences->PREF_CUSTOM_HEAD, "google-analytics.com", 0)){
			echo "<form action=\"https://secure.shopfitter.com/checkout/sf4checkout.cfm\" name=\"cartform\"  method=\"post\" onsubmit=\"_gaq.push(['_linkByPost', this]); return submitbutton(this.form, 0);\">";
		}else{
			echo "<form action=\"https://secure.shopfitter.com/checkout/sf4checkout.cfm\" name=\"cartform\"  method=\"post\" return submitbutton(this.form, 0);\">";
		}
		?>
			<div class="cart-pos">
            	<?php
				//if logged in as a trade user then upon checkout send to trade shop id
            	if ($login == 1 and strlen($preferences->PREF_TRADE_ID) == 6){
						$shopid = $preferences->PREF_TRADE_ID;
				}else{
						$shopid = $preferences->PREF_SHOP_ID;
				}
				?>
				<input type="hidden" name="sfs_id" value="<?php echo $shopid ?>" />
				<input type="hidden" name="currency" value="<?php echo $preferences->PREF_CURRENCY ?>" />
          		<script type="text/javascript">
            	<!--
					SFCheckoutCart("<?php echo $preferences->PREF_SELL_EXVAT; ?>");
				//-->
          		</script>       
<!--           		<input type="button" value=" checkout " onclick="submitbutton(this.form);" class="btn"
				onmouseover="this.className='btn btnhov'" onmouseout="this.className='btn'" />
-->				
           		<input type="submit" value="Checkout" class="big-button" />
				
		    </div>
      	</form>
		<p>&nbsp;</p>
    	<!--Shopping Cart ManageCart End  -->
		<div class="cart-message">
		<?php
        
                
                //$name = $_GET['page'];
                $information = getInformationPage('Cart');
                echo html_entity_decode($information->IN_DATA, ENT_QUOTES);
        
        ?> 

		</div>
	</div>
	
	

	
<!--	<div class="breadcrumb-holder">  
    	<span class="breadcrumb"><a href="/">Home</a></span> &gt; <span class="breadcrumb-here">Cart</span>
    </div>
-->		
	<p class="spacer">&nbsp;</p>

	
<?php
  include_once("includes/footer.php");
?>