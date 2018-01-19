<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$message = "";
$scrolltobottom = "";


$preferences = getPreferences();

//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Getting Started";

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
  <div class="admin">
    <br/>
	<h1>Getting Started</h1>
	<br/>

    

        <div class="login-box">
        	<h2>Help and hints to help you to success</h2>
        </div>		

        <div class="start-page-content">
        	<p>The 1ECOMMERCE website software is easy to use when you become familiar with it; however, it can seem impenetratably complicated when you first see it. This section is to help you take your first few steps towards creating your own successful ecommerce website. If, after reading through this and our <a href="http://1-ecommerce.com/support/knowledgebase.php?category=2">FAQs</a> or <a href="http://1-ecommerce.com/support/knowledgebase.php">knowledgebase</a>, you still need to contact us then go to our <a href="http://www.1-ecommerce.com/support/index.php?a=add">support page</a> and send a message. We also have <a href="http://www.1-ecommerce.com/service-contracts.php">support packages</a> that you may wish to consider for further assistance.</p>
        	<p>&nbsp;</p>
        	<h2>Before you start</h2>
        	<p>Find guidance throughout this Content Management System (CMS) by rolling your mouse over the red and white &quot;<img src="/_cms/csstooltips/q-icon.png" />&quot; icons. This will provide popup 'tool-tips' relevant to the task you need to perform - it also explains the function of the control you're looking at. When you're happy that you know what you're doing you'll find the tool-tips really annoying! So we've included a function to switch them off in the <a href="/preferences">Preferences &amp; Settings</a> section - look for 'Show Tool Tips' and deselect the tick box.</p>
       	  <p>&nbsp;</p>
        	<p>You should also enter all the required details in the <a href="/preferences">Preferences &amp; Settings</a> section before adding your website content so <a href="/preferences">go there now</a>; once done come back to this page and carry on reading.</p>
        	<p>&nbsp;</p>
        	<h2>First things first</h2>
        	<p>This software works by creating something and then using a search system to add it to menus, categories and other items. You can search by entering the name you gave  when setting the item up, by the PR or CA number automatically assigned by the software or see all items by placing your cursor in the search field but leaving it empty then clicking search. You can then select the item you want using the &quot;select from&quot; dropdown.</p>
       	  <p>&nbsp;</p>
   	    <p>Most people want to build their home page first. But if you enter all your categories, products and information pages it's much easier to know what to add to your home page; all the items you'll want to link to exist and you'll have thought about special offers and helpful content to assist your prospective customers: so leave the home page until you've got all your other content entered. </p>
        	<p>&nbsp;</p>
        	<p>In general, it's better to list with only basic content and get the items live with only rudimentary detail than to try and get it all perfect; which delays things. The sooner your site is live and products listed the sooner Google, Yahoo, Bing et al can index your web shop. So&hellip;</p>
        	<p>&nbsp;</p>
        	<h3>Enter all your categories in the Create Categories section</h3>
        	<p>Enter each of your  category names usnig the <a href="/create_categories">Create Categories</a> section; keep it simple to begin with, you can go back and add more to each one later using the <a href="/amend_categories">Amend Categories</a> section. Once you've created your categories you'll need to add them to the menu using the <a href="/ad_categories">Add Categories to Menu</a> section; if you need to subdivide your products further, then add the category to a parent category - this is now a subcategory.</p>
        	<p>&nbsp;</p>
        	<h3>List your products or services</h3>
        	<p>Now use the <a href="/create_products">Create Products</a> to enter your product details. Give the product a name, something that would work as a search phrase that your prospective customers may use in a search engine. Remember to add a few more important search terms into the 'Meta Title' field for improved search engine performance. Just like adding your categories, you can go back and add more detail using the <a href="/amend_products">Amend Products</a> section. Then you need to go to <a href="/add_products">Add Products to Category</a> so that they can be seen in the menu structure.</p>
        	<p>&nbsp;</p>
        	<p>Product options such as colour, size or anything else you need are set up using the <a href="/options_general">General Options</a> or <a href="/options_product">Product Options</a> sections. General options can be applied to any product whereas Product options are set up for one specific product. You can add related items to products of the same style; for example, bras and briefs sold separately as on the <a href="http://www.bravellous.com/1e/Fantasie+Elodie+Bra/0_caaa001_CAAA010/PRAA018" target="_blank">Bravellous website</a>. <a href="/amend_hotspots">Product Hotspots</a> provide areas to add extra content in predetermined parts of the page that you want to include but doesn't get submitted as part of the Google Product feed. These are ideal for extra images and text specifically added for SEO purposes.</p>
        	<p>&nbsp;</p>
        	<h3>Information pages</h3>
        	<p>There are a number of information pages already set up for you; these include a contact page with a ready made form and thank you page; delivery, terms, privacy and conditions pages for you to amend to suit your own business; links page for you to add links to other sites; cart and member pages allow you to enter your own further information to specified areas within these functional pages. </p>
        	<p>&nbsp;</p>
        	<p>Info pages need to be set up in the <a href="/amend_information">Info Pages Setup</a> section before you can add any content. In that section you'll also see the Y/N settings in the &quot;Enable&quot; column; Y is the default setting that includes the info page in the main menu. Once they're set up you can add or amend the content by opening the <a href="/amend_infopage">Info Pages Content</a></p>
        	<p>&nbsp;</p>
        	<p>You can create as many info pages as you like but remember that only a limited number should be added to the main menu; any others can be linked to by using the controls in the edit regions throughout the system.</p>
        	<p>&nbsp;</p>
        	<p>Your home page is also an info page and you can edit and add your content in the same way as any other info page; but the advice is as mentioned earlier - leave it to last.</p>
        	<p>&nbsp;</p>
        	<h3>Banner, bottom and sidebar content</h3>
        	<p>Next, open the <a href="/amend_areadata">Header / Footer / Sidebar</a> section and add your logo and other items you want to include in the header; it's well worth including a contact phone number in this prominent position because it provides reassurance for customers that they can call you if you they need to.</p>
        	<p>&nbsp;</p>
        	<p>The footer can have links to pages you need to include on the site but don't want to add to your main menu; this could apply to your privacy policy, terms of use and links, for example.</p>
        	<p>&nbsp;</p>
          <h3>Templates</h3>
          <p>Open the templates section to choose the theme design you wish to use for your website; other theme templates are available for download/upload from the <a href="http://www.1-ecommerce.com/templates/">1ECOMMERCE website</a> and you can even order your own bespoke/custom design there.</p>
          <p>&nbsp;</p>
          <h3>Other Features</h3>
          <p>You can set up differential pricing for discount or trade members using the appropriate sections; before you start this you'll need to enter your <a href="/setup_emails">e-mail settings</a> for automated confirmations.</p>
          <p>&nbsp;</p>
          <p>An advanced, tag based search feature is included as standard in this system; this provides a filtering facility so that site visitors can select products based on parameters you set up such as size, colour, style for example. You can add up to four tag sets in each area.</p>
          <p>&nbsp;</p>
          <p>Google Merchants and Sitemaps should be generated each time you make a change to your website using the items in the top menu.</p>
          <p>&nbsp;</p>
          <h3>Going Live</h3>
          <p>To set your ecommerce website live and able to make sales you need to create an admin account on the <a href="https://secure.shopfitter.com/createnewshop.cfm" target="_blank">Shopfitter Secure Server</a>; once you've done this add the Shop ID number and password to the <a href="/preferences">Preferences and Settings</a> section. You add your delivery charging details and VAT/Tax settings here too as well as carrying out your order administration.</p>
    </div>
      
<?php
  include_once("includes/footer_admin.php");
?>
