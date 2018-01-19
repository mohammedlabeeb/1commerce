
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $pageTitle ?></title>
    <meta name="description" content="<?php echo $pageMetaDescription ?>" />
	<meta name="keywords" content="<?php echo $pageMetaKeywords ?>" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta name="author" content="1 e-commerce and Shopfitter" />
	<meta name="design" content="1 e-commerce" />
    <link href="/_cms/common/adminstyle.css" rel="stylesheet" type="text/css">
    <link href="/_cms/common/bubble.css" rel="stylesheet" type="text/css">
    <link href="/_cms/calendar/calendar.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<script src="/common/language-en.js" type="text/javascript"></script>
    <script src="/common/sfcart.js" type="text/javascript"></script>
    <script src="/common/shopfitter_core.js" type="text/javascript"></script>
    <script src="/common/products.js" type="text/javascript"></script>
    <script src="/common/general.js" type="text/javascript"></script>
    <script src="/_cms/calendar/calendar.js" type="text/javascript"></script>
    <script src="/_cms/common/reviews_validation.js" type="text/javascript"></script>
    <?php
	if(isset($pageTitle) and $pageTitle == "Site Administration: Add Products To Memu"){
		//improved scroll handling - better scroll handling but limited to add_products.php for now 18/11/13 
		echo "<script src=\"/_cms/common/header_admin.js\" type=\"text/javascript\"></script>";
	}
	?>
    
        
    <![endif]-->
    <script type="text/javascript">
	function MM_jumpMenu(targ,selObj,restore){ //v3.0
		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
		if (restore) selObj.selectedIndex=0;
	}
	
	function MM_refreshScreen(targ,selObj){ //v3.0
		eval(targ+".location='"+selObj.value+"'");
	}
	</script>
    <script type="text/javascript">
	<!--
	function scrollToBottom(){
	var scrollH=document.body.scrollHeight;
	var offsetH=document.body.offsetHeight;
	if(scrollH>offsetH) window.scrollTo(0,scrollH);
	else window.scrollTo(0,offsetH);
	}
	//-->
	</script>
    
    <script type="text/javascript" src="/ckeditor/ckeditor.js">
    </script>
</head>
<?php
echo "<body " . $scrolltobottom . ">";

?>


<div class="body-wrapper_admin">
		<div class="banner-style">
			<div class="top-logo">
            	<h1 class="sitename"><?php echo $preferences->PREF_SHOPNAME ?></h1>
            </div>
			<div class="top-cart_admin">
            	<h2>Site Administration - <span class="site_admin">Logged in as <span class="warning_green"><?php echo $_SESSION['username']?></span></span></h2>
            </div>
		</div>		
		<div class="topmenu_admin">
			<ul>
                <li><a href="/home" ><span>&nbsp;CMS Home</span></a></li>
                <li><a href="/start" ><span>Getting Started</span></a></li>
				<li><a href="/preferences" ><span>Preferences &amp; Settings</span></a></li>
                <li><a href="/setup_emails" ><span>Setup emails</span></a></li>
                <li><a href="/amend_password">Change Password</a></li>
                <li><a href="/upload_file">Upload Image</a></li>
                <li><a href="/create_xml">Google Merchants</a></li>
                <li><a href="/create_sitemap">XML Sitemap</a></li>
                <li class="end"><a href="/login?logout=1" ><span>&nbsp;Logout&nbsp;</span></a></li>
            </ul>
		</div>

		<div id="container_admin">
			<div id="leftcolumn">
            	<ul class="category">
                	<li class="l-top">
                    	 Templates                    </li>
                	<li><a href="/change_theme"><strong>Change Template</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Templates handle the layout, colours and design of your website<br /><br />Change site templates here</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                </ul>		
          <ul class="category">
                	<li class="l-top">
                    	Manage Search Tags                    </li>
                	<li><a href="/amend_attributes"><strong>Search Tags</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Search tags are part of an advanced search feature that allows you to tag your products for hierachical filtered searching</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                </ul>			
		  <ul class="category">
                	<li class="l-top">
                    	Manage Info Pages                    </li>
               	  <li><a href="/amend_areadata"><strong>Header / Footer / Sidebar</strong></a></li>
                    <li><a href="/amend_information"><strong>Info Pages Setup</strong></a></li>
                	<li><a href="/amend_infopage"><strong>Info Pages Content</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Manage the top menu, content of your information pages (home, terms, about etc) and header, footer and sidebar</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                </ul>
          <ul class="category">
                	<li class="l-top">
                    	Manage Categories                    </li>             
                  <li><a href="/create_categories"><strong>Create Categories</strong></a></li>
                    <li><a href="/amend_categories"><strong>Amend Categories</strong></a></li>
                    <li><a href="/add_categories"><strong>Add Categories To Menu</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Create and work on the categories within your webshop, and add them to menus and parent categories</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                </ul>
          <ul class="category">
                	<li class="l-top">
                    	Manage Products                    </li>   
                  <li><a href="/create_products"><strong>Create Products</strong></a></li>
                    <li><a href="/amend_products"><strong>Amend Products</strong></a></li>
                    <li><a href="/maintain_quantity_discounts"><strong>Maintain Quantity Discounts</strong></a></li>
                    <li><a href="/additional_products"><strong>Related Products</strong></a></li>
                    <!---
                    <li><a href="/amend_hotspots"><strong>Product Hotspots</strong></a></li>
                    --->
                    <li><a href="/add_products"><strong>Add Products To Category</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Create and work on the products within your webshop; add them to categories and relate products to each other. Add extra content to product pages outside the usual detail areas in hotspots</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                 </ul>   
          <ul class="category">
                 	<li class="l-top">
                    	Manage Options                    </li>    
                   <li><a href="/options_general"><strong>General Options</strong></a></li>
                    <li><a href="/options_product"><strong>Product Options</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Create product specific options or general options that can be added to any product</span><span class=\"bottom\"></span></span>" : "") ?></a></span>       
                 </ul>
          <ul class="category">
                 	<li class="l-top">
                    	Manage Reviews                    </li>    
                   <li><a href="/maintain_reviews"><strong>Maintain Reviews</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Publish, Approve, Reply to, Edit or Delete Reviews</span><span class=\"bottom\"></span></span>" : "") ?></a></span>       
                 </ul>
          <ul class="category">
                 	<li class="l-top">
                    	Manage Promotions                    </li>    
                   <li><a href="/maintain_promotions"><strong>Maintain Promotions</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Create product specific options or general options that can be added to any product</span><span class=\"bottom\"></span></span>" : "") ?></a></span>       
                 </ul>
          <ul class="category">
                 	<li class="l-top">
                    	Trade/Discount Club                    </li>  
                   	<li><a href="/confirm_members"><strong>Confirm New Members</strong></a></li>
                	<li><a href="/amend_members"><strong>Amend Members</strong></a></li>
                    <li><a href="/list_members"><strong>List Members</strong></a></li>
                    <li><a href="/maintain_price_categories"><strong>Maintain Price Categories</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Manage your trade or discount club members and pricing here</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                 </ul>
          <ul class="category">
                 	<li class="l-top">
                    	General Housekeeping                    </li>  
                   <li><a href="/create_qd_xml"><strong>Create Quantity Discount XML</strong></a></li>
                    <span style="position: absolute; top: 2px; left:2px"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Manual update of volume pricing discounts (only needed if values have not updated automatically)</span><span class=\"bottom\"></span></span>" : "") ?></a></span>
                 </ul>                   
</div>
            <!--- end of leftcolumn -->


