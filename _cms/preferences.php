<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$message = "";
$scrolltobottom = "";

if (isset($_POST['UPDATE'])) {
	//validate all fields first
	if (strlen($_POST['TRADE_ID']) > 0){
		if (strlen($_POST['TRADE_ID']) != 6 | !is_numeric($_POST['TRADE_ID'])){
			$message .= "Please enter a valid 6 digit Trade ID" . "<br/>";
		}
		$warning = "red";
	}
	if (strlen($_POST['VAT']) > 0 and validate2dp($_POST['VAT']) == "false"){
		$message .= "Please enter a valid Default VAT rate to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['MIN_ORDER']) > 0 and validate2dp($_POST['MIN_ORDER']) == "false"){
		$message .= "Please enter a valid Minimum Order Value to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if (strlen($_POST['MIN_ORDER_TRADE']) > 0 and validate2dp($_POST['MIN_ORDER_TRADE']) == "false"){
		$message .= "Please enter a valid Minimum Order Value (Trade) to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if (validateSeed($_POST['CAT_SEED']) == "false"){
		$message .= "Please enter a valid Category Seed" . "<br/>";
		$warning = "red";
	}
	if (validateSeed($_POST['PROD_SEED']) == "false"){
		$message .= "Please enter a valid Product Seed" . "<br/>";
		$warning = "red";
	}
	if ($message == ""){
		//no error message so update database preferences table
		if(!isset($_POST['EXVAT'])){$_POST['EXVAT'] = "N";}
		if(!isset($_POST['SELL_EXVAT'])){$_POST['SELL_EXVAT'] = "N";}
		if(!isset($_POST['GOOGLE_SEARCH'])){$_POST['GOOGLE_SEARCH'] = "N";}
		if (isset($_POST['TOOL_TIPS']) and $_POST['TOOL_TIPS'] == 1){$tool_tips = "Y";}else{$tool_tips = "N";}
		if (isset($_POST['ADVANCED_SEARCH']) and $_POST['ADVANCED_SEARCH'] == 1){$advanced_search = "Y";}else{$advanced_search = "N";}
		if (isset($_POST['REVIEWS']) and $_POST['REVIEWS'] == 1){$reviews = "Y";}else{$reviews = "N";}
		if (isset($_POST['PUBLISH']) and $_POST['PUBLISH'] == 1){$publish = "Y";}else{$publish = "N";}
		if (isset($_POST['SHOP_ACCESS']) and $_POST['SHOP_ACCESS'] == 1){$shop_access = "Y";}else{$shop_access = "N";}
		
		$fields = array("shop_id_original"=>$_POST['SHOP_ID_ORIGINAL'], "pref_shop_id"=>$_POST['SHOP_ID'], "pref_trade_id"=>$_POST['TRADE_ID'], "pref_shopname"=>$_POST['SHOPNAME'], "pref_shopurl"=>$_POST['SHOPURL'],
						"pref_email"=>$_POST['EMAIL'], "pref_theme"=>$_POST['THEME_HIDDEN'], "pref_meta_title"=>$_POST['META_TITLE'], "pref_meta_desc"=>$_POST['META_DESC'], "pref_meta_keywords"=>$_POST['META_KEYWORDS'], "pref_currency"=>$_POST['CURRENCY'],																							
						"pref_vat"=>$_POST['VAT'], "pref_exvat"=>$_POST['EXVAT'], "pref_sell_exvat"=>$_POST['SELL_EXVAT'], "pref_min_order"=>$_POST['MIN_ORDER'], "pref_min_order_trade"=>$_POST['MIN_ORDER_TRADE'],
						"pref_google_search"=>$_POST['GOOGLE_SEARCH'], "pref_cat_seed"=>$_POST['CAT_SEED'], "pref_prod_seed"=>$_POST['PROD_SEED'], "pref_prom_seed"=>$_POST['PROM_SEED'],
						"pref_custom_head"=>$_POST['CUSTOM_HEAD'], "pref_tracking_code"=>$_POST['TRACKING_CODE'],
						"pref_shop_pw"=>$_POST['SHOP_PW'], "pref_shop_notes"=>$_POST['SHOP_NOTES'], "pref_tool_tips"=>$tool_tips, "pref_advanced_search"=>$advanced_search,
						"pref_reviews"=>$reviews, "pref_publish"=>$publish, "pref_shop_access"=>$shop_access);
		$rows = Rewrite_Preferences($fields);
		if ($rows == 1){
			$message = $rows . " record successfully UPDATED";
			$warning = "green";
		}
		if ($rows == 0){
			$message = "WARNING ! ! ! - NO RECORDS UPDATED";
			$warning = "orange";
			$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
		}
		if ($rows > 1){
			$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
			$warning = "red";
			$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
		}
		$error = null;
		$error = mysql_error();
		if ($error != null ) {$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";}
	}else{
		$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
	}
}

$preferences = getPreferences();
$currencies = getCurrencies();
$currency = $preferences->PREF_CURRENCY;

//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Preferences";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;
?>

<?php
include_once("includes/header_admin.php");
?>

<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Preferences and Settings</h1>
	<br/>
	<form name="enter_preferences" action="/preferences" method="post">
		<table align="left" border="0" cellpadding="2" cellspacing="5">
			<tr>
				<td>Shopfitter ID 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter your Shop ID here</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
			  <td>
                    <input type="text" name="SHOP_ID" SIZE="6" value="<?php echo $preferences->PREF_SHOP_ID ?>">
                    <input type="hidden" name="SHOP_ID_ORIGINAL" SIZE="6" value="<?php echo $preferences->PREF_SHOP_ID ?>">
                   Get your <a href="https://secure.shopfitter.com/createnewshop.cfm" target="_new">Shop ID here</a>
                </td>
			</tr>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
            <tr>
				<td>Trade/Member ID
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter your Trade/Member Shop ID; it can be the same as your Shopfitter ID or a different account if you prefer to manage orders separately</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td>
                    <input type="text" name="TRADE_ID" SIZE="6" value="<?php echo $preferences->PREF_TRADE_ID ?>">
                </td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
			
			<tr>
				<td>Shop Name 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the name of your website</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="SHOPNAME" SIZE="32" value="<?php echo $preferences->PREF_SHOPNAME ?>"></td>
			</tr>
			<tr>
				<td>Shop URL 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter your website URL: don't forget the http://</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="SHOPURL" SIZE="50" value="<?php echo $preferences->PREF_SHOPURL ?>"></td>
			</tr>
            <tr>
				<td>E-mail Address 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter your e-mail address. This is used for notification e-mails when someone applies for trade or member status</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="EMAIL" SIZE="50" value="<?php echo $preferences->PREF_EMAIL ?>"></td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>

            <tr>
				<td>Site Template 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This shows which template you've chosen for your website; change templates by visiting the Change Templates page from the left menu</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td>
                    <input type="text" name="THEME" SIZE="50" disabled value="<?php echo $preferences->PREF_THEME ?>">
                    <input type="hidden" name="THEME_HIDDEN" value="<?php echo $preferences->PREF_THEME ?>">
                </td>
			</tr>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
            <tr>
            	<td>Currency 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select your trading currency</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            	<td>
                    <select name="CURRENCY" onchange="">
                        <?php
                        foreach($currencies as $c){
                            if($currency == $c->CU_SF_CODE){
                                $selected = "selected ";
                            }else{
                                $selected = "";
                            }		
                            echo "<option value=\"" . $c->CU_SF_CODE . "\" " . $selected . ">" . $c->CU_ISO_CODE . " - " . $c->CU_NAME . "</option>";
                        }
                        ?>
                	</select>
                </td>
            </tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
			
            <tr>
				<td>
                	<!---
                	Default VAT/Tax Rate (%) 
                	<a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Set the default tax rate if all your items have the same rate; otherwise leave this at 0.00 and set each item individually. Note the format MUST include 2 decimal places; eg 20.00</span><span class=\"bottom\"></span></span>" : "") ?></a>
                	--->
                </td>
				<td><input type="hidden" name="VAT" SIZE="8" value="<?php echo $preferences->PREF_VAT ?>">
			</tr>
            <tr>
                <td>Show ex-VAT/Tax Prices 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box if you want the prices on your website to be displayed excluding VAT/Tax</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <?php if($preferences->PREF_EXVAT == "Y"){$checked = "checked";}else{$checked = "";}?>
                <td><input type="checkbox" name="EXVAT" value="Y" <?php echo $checked ?>></td>
            </tr>
            <tr>
                <td>Sell using ex-VAT/Tax Prices
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box if you want to sell using ex-VAT/Tax prices</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <?php if($preferences->PREF_SELL_EXVAT == "Y"){$checked = "checked";}else{$checked = "";}?>
                <td><input type="checkbox" name="SELL_EXVAT" value="Y" <?php echo $checked ?>></td>
            </tr> 
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
			
            <tr>
				<td>Minimum Order Value 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Set a minimum value that your site will accept; eg 15.00. This will prompt your customers to add more to their order if is below the value you've set</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="MIN_ORDER" SIZE="8" value="<?php echo $preferences->PREF_MIN_ORDER ?>">
			</tr>
            <tr>
				<td>Minimum Order Value (Trade) 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Similar to above but applied to Trade/Member orders only</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="MIN_ORDER_TRADE" SIZE="8" value="<?php echo $preferences->PREF_MIN_ORDER_TRADE ?>">
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
            <tr>
                <td>Google Product Search 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box if you want to generate a feed to the Google Merchants system</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <?php if($preferences->PREF_GOOGLE_SEARCH == "Y"){$checked = "checked";}else{$checked = "";}?>
                <td><input type="checkbox" name="GOOGLE_SEARCH" value="Y" <?php echo $checked ?>></td>
            </tr> 
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
			
            <tr>
				<td>Category Seed 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Advanced Feature: This is the next number when you create a new category; alter it if you want to advance the sequence. Caution: if you reduce the number you may overwrite existing categories and damage your webshop functionality</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="CAT_SEED" SIZE="8" value="<?php echo $preferences->PREF_CAT_SEED ?>">
			</tr>
            <tr>
				<td>Product Seed 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Advanced Feature: This is the next number when you create a new product; alter it if you want to advance the sequence. Caution: if you reduce the number you may overwrite existing items and damage your webshop functionality</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="PROD_SEED" SIZE="8" value="<?php echo $preferences->PREF_PROD_SEED ?>">
			</tr>
            <tr>
				<td>Promotions Seed 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Advanced Feature: This is the next number when you create a new promotion; alter it if you want to advance the sequence. Caution: if you reduce the number you may overwrite existing items and damage your webshop functionality</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="PROM_SEED" SIZE="8" value="<?php echo $preferences->PREF_PROM_SEED ?>">
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
			
            <tr>
                <td>Show Tool Tips:
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box to remove the Tool Tips from all pages</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <?php if($preferences->PREF_TOOL_TIPS == "Y"){$checked = "checked";}else{$checked = "";}
                ?>
                <td><input type="checkbox" name="TOOL_TIPS" value="1" <?php echo $checked ?> ></td>
            </tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
            <tr>
                <td>Enable Tag Search:
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box to show the tag based search system, these will display dropdown selectors</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <?php if($preferences->PREF_ADVANCED_SEARCH == "Y"){$checked = "checked";}else{$checked = "";}
                ?>
                <td><input type="checkbox" name="ADVANCED_SEARCH" value="1" <?php echo $checked ?> ></td>
            </tr>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
            <tr>
                <td>Reviews Interfaced:
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box to allow Product Reviews</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <?php if($preferences->PREF_REVIEWS == "Y"){$checked = "checked";}else{$checked = "";}
                ?>
                <td><input type="checkbox" name="REVIEWS" value="1" <?php echo $checked ?> ></td>
            </tr>
            <?php if($preferences->PREF_REVIEWS == "Y"): ?>
                <tr>
                    <td>Publish Immediately:
                    <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box to publish customer reviews immediately they are added by the reviewer. No approval required.</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                    <?php if($preferences->PREF_PUBLISH == "Y"){$checked = "checked";}else{$checked = "";}
                    ?>
                    <td><input type="checkbox" name="PUBLISH" value="1" <?php echo $checked ?> ></td>
                </tr>
            <?php endif; ?>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
            <tr>
                <td>Non-member shop access:
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Tick this box to allow access to the shop by non-members<br/>Normally set to YES</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <?php if($preferences->PREF_SHOP_ACCESS == "Y"){$checked = "checked";}else{$checked = "";} ?>
                <td><input type="checkbox" name="SHOP_ACCESS" value="1" <?php echo $checked ?> ></td>
            </tr>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>
			<tr>
				<td>Default Meta Title  
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the main search terms for your site; these will be used along with other items for automated SEO. <br /><br />It is best to also set specific terms for each category, product and info page. <br /><br />Note that the Title tag (as it's known) is the most important place to include key words and phrases so that search engines pick them up.<br /><br />Approx 60 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="META_TITLE" type="text" value="<?php echo html_entity_decode($preferences->PREF_META_TITLE) ?>" size="84" maxlength="70" />
              <td>
			</tr>
			<tr>
				<td>Default Meta Description  
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a description of the content of your website as a properly formed sentence; this will be used where you don't add page specific meta descriptions for automated SEO. <br /><br />It is best to also set specific meta descriptions for each category, product and info page. <br /><br />Note that this description is used by search engines in their listings to describe the content of the pages they are listing; remember to include key words and phrases so that search engines can match them to search requests by people<br /><br />Approx 150 to 200 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="META_DESC" class="p-amend-textarea"  ><?php echo html_entity_decode($preferences->PREF_META_DESC) ?></textarea><td>
			</tr>
			<tr>
				<td>Default Meta Keywords  
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter a list of key words; this will be used where you don't add page specific meta keywords for automated SEO. <br /><br />It is best to also set specific meta keywords for each category, product and info page. <br /><br />Include 3 or 4 words or phrases from your meta title and meta description first; it's also a good place to put mis-spellings and alternative names for your products<br /><br />256 characters max, including spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="META_KEYWORDS" class="p-amend-textarea"  ><?php echo html_entity_decode($preferences->PREF_META_KEYWORDS) ?></textarea><td>
			</tr>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
            <tr>
				<td>Custom Head Item  
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Put code and scripts that need be added into the \"head\" area of your website.<br /><br />This can be site verification meta items such as the one for the Google Webmaster system, javascript or a link to an external stylesheet<br /><br />Note that anything added to this area will appear on every page of your site</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="CUSTOM_HEAD" class="p-amend-textarea"  ><?php echo html_entity_decode($preferences->PREF_CUSTOM_HEAD) ?></textarea><td>
			</tr>
			<tr>
				<td>Tracking Code 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Add tracking code such as that required by Google Analytics <br /><br />Note that anything added to this area will appear on every page of your site just before the closing body tag</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="TRACKING_CODE" class="p-amend-textarea"  ><?php echo html_entity_decode($preferences->PREF_TRACKING_CODE) ?></textarea><td>
			</tr>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
            <tr>
				<td>Secure Admin Password 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Insert the password you set alongside your Shopfitter ID to give you instant access to the order administration account</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="SHOP_PW" type="text" value="<?php echo html_entity_decode($preferences->PREF_SHOP_PW) ?>" size="32" />
                <td>
			</tr>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
            <tr>
				<td>Shop Notes 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">You can put information that relates to your website and associated items such as login passwords, links to relevant websites and other details here.<br /><br />Information added here will display on the CMS home page</span><span class=\"bottom\"></span></span>" : "") ?></a>
                <br/><br/>
                    <div class="edit-button">
					<a href="javascript:void(0);" NAME="My Window Name" title=" My title here " onclick=window.open("/_cms/edit_textarea.php?form=enter_preferences&field=SHOP_NOTES","Ratting","width=1000,height=500,left=150,top=200,toolbar=1,status=1,");><span>Edit</span>
                    </a></div>
                 <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click the edit button to make it easier to enter text with formatting such as links, bold and different colours</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="SHOP_NOTES" class="p-amend-textarea"  ><?php echo html_entity_decode($preferences->PREF_SHOP_NOTES) ?></textarea><td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td><input class="update-button" name="UPDATE" type="submit" value="Save Changes &raquo;&raquo;"></td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <!---<tr>
				<td nowrap>Message</td>
				<td><input type="text" name="MESSAGE" value="<?php echo "FREDDIE" ?>">
			</tr>--->
            <tr>
				<td colspan="2"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
			</tr>
		</table>
	</form>
<?php
  include_once("includes/footer_admin.php");
?>
