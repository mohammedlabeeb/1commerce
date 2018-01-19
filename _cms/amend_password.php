<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$message = "";
$scrolltobottom = "";
$username = "admin";
if (isset($_POST['PASSWORD_NEW'])){$password_new = $_POST['PASSWORD_NEW'];}else{$password_new = "";}

if (isset($_POST['UPDATE'])) {
	//validate all fields first
	if (strlen($_POST['PASSWORD_NEW']) < 5){
		$message .= "Please enter a password greater than 5 characters" . "<br/>";
		$warning = "red";
	}
	if ($_POST['PASSWORD_NEW'] != $_POST['PASSWORD_CHECK']){
		$message .= "Password check failure - please re-enter password details" . "<br/>";
		$warning = "red";
	}
	if ($message == ""){
		//no error message so update database preferences table 
		$fields = array("user_name"=>$_SESSION['username'], "user_password"=>sha1($_POST['PASSWORD_NEW']));
		$rows = Rewrite_Users($fields);
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
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Change Password";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Change Password</h1>
	<br/>
	<form action="/_cms/amend_password.php" method="post">
		<table class="password-table" align="left" border="0" cellpadding="2" cellspacing="5">
        	<tr>
				<td class="password-td">Username: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">The website administrator's user name; this can't be altered</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td>
                    <!---<input type="text" name="PASSWORD_NEW" SIZE="10" value="<?php echo $username ?>" disabled>--->
                    <label><?php echo $username ?></label>
                </td>
			</tr>
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			<tr>
				<td>New Password: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter your new password. It's advisable to use a mix of capital letters, lower case, numbers and other characters; for example something like this: D1ff1cu/t</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td>
                    <input type="text" name="PASSWORD_NEW" SIZE="20" value="<?php echo $password_new ?>">
                </td>
			</tr>
            <tr>
				<td>Re-enter Password: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter your new password again to confirm it</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td>
                    <input type="text" name="PASSWORD_CHECK" SIZE="20" value="">
                </td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td><input name="UPDATE" type="submit" value="Change Password" class="update-button"> 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save your new password</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
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
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
	</form>
<?php
  include_once("includes/footer_admin.php");
?>

