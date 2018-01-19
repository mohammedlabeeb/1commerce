<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$message = "";
$scrolltobottom = "";


$preferences = getPreferences();

//note this will also refresh the page after amending it
$pageTitle = "Site Administration: CMS Home";

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
  <div class="admin">
    <br/>
	<h1>CMS Home</h1>
	<br/>
    
			<form action="https://secure.shopfitter.com/admin/index.cfm" method="post">
				<div class="login-box">
                <h2>Secure Admin Site Login</h2>
				<table align="center" width="100%" border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td width="180">Shopfitter Shop ID</td>
						<td width="159"><input type="text" name="LOGIN_SHOPFITTERID" SIZE="12" value="<?php echo $preferences->PREF_SHOP_ID ?>"> 
                        <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Your Shop ID will display here; you can also enter a different ID, such as your Trade/Member Shop ID instead<br /><br />To change your saved Shop ID please click Preferences &amp; Settings in the top menu</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                        <td width="367">Log in to the secure order administration site by clicking the login button</td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type="text" name="LOGIN_PASSWORD" SIZE="12" value="<?php echo $preferences->PREF_SHOP_PW ?>"> 
                        <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Your password for the Order Admin account will display here; you can also enter a different password, such as your Trade/Member Shop password instead<br /><br />To change your saved password please click Preferences &amp; Settings in the top menu</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                        <td>You need to visit the Shop Settings page in the secure site and enter all the relevant details</td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="login &raquo;&raquo;" class="login-button"> 
                        <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click here to log in to your Order Admin account</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                        <td>If you haven't registered to get your Shop ID go to the <a href="https://secure.shopfitter.com/createnewshop.cfm" target="_blank">Create New Shop</a> page</td>
					</tr>
				</table>
      </div>		
		</form>
<!--	  <div class="login-box">
      <table width="100%" border="0" align="left" cellpadding="2" cellspacing="5">
		  <tr>
			  <td width="26%" nowrap>Shopfitter ID <?php echo $preferences->PREF_SHOP_ID ?></td>
			  <td width="74%">Trade ID <?php echo $preferences->PREF_TRADE_ID ?></td>
		  </tr>
      </table>
      </div>-->
      <div class="login-box">
      <table width="100%" border="0" align="left" cellpadding="2" cellspacing="5">
		  <tr>
			  <td width="11%"><h2>Shop Notes</h2></td>
              <td width="89%"><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Information you want to save that relates to your website such as Google login details etc. To add data to this area click Preferences &amp; Settings in the top menu and go to \"Shop Notes\"</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
          </tr>
		  <tr>
			  <td colspan="2"><?php echo html_entity_decode($preferences->PREF_SHOP_NOTES) ?></td>
		  </tr>
          <tr>
			  <td colspan="2">&nbsp;</td>
		  </tr>
          <tr>
			  <td colspan="2">&nbsp;</td>
		  </tr>
          <tr>
			  <td colspan="2">&nbsp;</td>
		  </tr>
      </table>
      </div>
      <div class="login-box">
      <p>
          <!--<tr>
				<td nowrap>Message</td>
				<td><input type="text" name="MESSAGE" value="<?php echo "FREDDIE" ?>">
			</tr>-->
        <label class="<?php echo $warning ?>" ><?php echo $message ?></label></p>
    </div>
<?php
  include_once("includes/footer_admin.php");
?>
