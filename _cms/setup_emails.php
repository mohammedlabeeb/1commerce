<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$message = "";
$scrolltobottom = "";

if (isset($_POST['UPDATE'])) {
	//validate all fields first

	if ($message == ""){
		//no error message so update database email_setup table
		$fields = array("em_reg_to"=>$_POST['REG_TO'], "em_reg_cc"=>$_POST['REG_CC'], "em_reg_bcc"=>$_POST['REG_BCC'],
						"em_reg_subject"=>$_POST['REG_SUBJECT'], "em_reg_header"=>$_POST['REG_HEADER'], "em_reg_content"=>$_POST['REG_CONTENT'], "em_reg_footer"=>$_POST['REG_FOOTER'],
						"em_conf_cc"=>$_POST['CONF_CC'], "em_conf_bcc"=>$_POST['CONF_BCC'],
						"em_conf_subject"=>$_POST['CONF_SUBJECT'], "em_conf_header"=>$_POST['CONF_HEADER'], "em_conf_content"=>$_POST['CONF_CONTENT'], "em_conf_footer"=>$_POST['CONF_FOOTER'],
						"em_rev_to"=>$_POST['REV_TO'], "em_rev_cc"=>$_POST['REV_CC'], "em_rev_bcc"=>$_POST['REV_BCC'],
						"em_rev_subject"=>$_POST['REV_SUBJECT'], "em_rev_header"=>$_POST['REV_HEADER'], "em_rev_content"=>$_POST['REV_CONTENT'], "em_rev_footer"=>$_POST['REV_FOOTER']);
		$rows = Rewrite_emailSetup($fields);
		if ($rows == 1){
			$message = $rows . " record successfully UPDATED";
			$warning = "green";
			$scrolltobottom = "onLoad=\"scrollToBottom()\" ";
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
$email_setup = getEmailSetup();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Setup e-mails";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<link href="common/adminstyle.css" rel="stylesheet" type="text/css" />
<link href="common/bubble.css" rel="stylesheet" type="text/css" />

<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Setup emails</h1>
	<br/>
	<form action="/setup_emails" method="post">
        <div class="setup_emails">
        <h2>New Member Application - sent to website administrators</h2>
		<table align="left" border="0" cellpadding="2" cellspacing="5">
        	<tr>
				<td class="email-td">To: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter your e-mail address; the place you want trade/member application notifications to be sent to</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="REG_TO" type="text" size="60"  value="<?php echo html_entity_decode($email_setup->EM_REG_TO) ?>" maxlength="70" />
              <td>
			</tr>
            <tr>
				<td>CC: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Add a secondary e-mail address if you want trade/member application notifications to be copied to another mailbox: this can be left blank</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="REG_CC" type="text" size="60"  value="<?php echo html_entity_decode($email_setup->EM_REG_CC) ?>" maxlength="70" />
              <td>
			</tr>
            <tr>
				<td>BCC: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Notifications e-mails sent to this address will be invisible to the other two recipients; use it as a secret copy: this can be left blank</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="REG_BCC" type="text" size="60"  value="<?php echo html_entity_decode($email_setup->EM_REG_BCC) ?>" maxlength="70" />
              <td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
			<tr>
				<td>Subject: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the title of the e-mail; this will show in the Subject column of the recipients' e-mail application</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="REG_SUBJECT" type="text" size="88"  value="<?php echo html_entity_decode($email_setup->EM_REG_SUBJECT) ?>" maxlength="70" />
              <td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
			<tr>
				<td>Header: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want at the start of the application e-mail; this is for website administrators and is not received by the applicant</span><span class=\"bottom\"></span></span>" : "") ?></a><br /><br />To put text on the next line use \r\n and for a double line return use \r\n\r\n</td>
                <td><textarea type="text" name="REG_HEADER" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_REG_HEADER) ?></textarea><td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			

			<tr>
				<td>Content: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want in the body of the application e-mail; this is for website administrators and is not received by the applicant</span><span class=\"bottom\"></span></span>" : "") ?></a><br /><br />To put text on the next line use \r\n and for a double line return use \r\n\r\n</td>
                <td><textarea type="text" name="REG_CONTENT" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_REG_CONTENT) ?></textarea><td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			

            <tr>
				<td>Footer: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want in the footer of the application e-mail; this is for website administrators and is not received by the applicant</span><span class=\"bottom\"></span></span>" : "") ?></a><br /><br />To put text on the next line use \r\n and for a double line return use \r\n\r\n</td>
                <td><textarea type="text" name="REG_FOOTER" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_REG_FOOTER) ?></textarea><td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>		
            </tr>			

            <tr>
				<td colspan="2">&nbsp;</td>		
            </tr>			
		</table>
        </div>
        
        <div class="setup_emails">
        <h2>New Member Confirmation - sent to applicants</h2>
        <table align="left" border="0" cellpadding="2" cellspacing="5">
            <tr>
				<td class="email-td">CC: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter an e-mail address you want to copy the acceptance confirmation e-mail to</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="CONF_CC" type="text" size="60"  value="<?php echo html_entity_decode($email_setup->EM_CONF_CC) ?>" maxlength="70" />
              <td>
			</tr>
            <tr>
				<td>BCC: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter an e-mail address to receive a blind copy of the acceptance confirmation e-mail</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="CONF_BCC" type="text" size="60"  value="<?php echo html_entity_decode($email_setup->EM_CONF_BCC) ?>" maxlength="70" />
              <td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
			<tr>
				<td>Subject: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the title of the e-mail; this will show in the Subject column of the recipients' e-mail application; this is sent when the application has been successful</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="CONF_SUBJECT" type="text" size="88" value="<?php echo html_entity_decode($email_setup->EM_CONF_SUBJECT) ?>" maxlength="70" />
              <td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
			<tr>
				<td>Header: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want at the start of the acceptance confirmation e-mail</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="CONF_HEADER" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_CONF_HEADER) ?></textarea><td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
			<tr>
				<td>Content: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want in the body of the acceptance confirmation e-mail</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="CONF_CONTENT" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_CONF_CONTENT) ?></textarea><td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
            <tr>
				<td>Footer: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want at the end of the acceptance confirmation e-mail</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="CONF_FOOTER" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_CONF_FOOTER) ?></textarea><td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>		
            </tr>			
            <tr>
				<td colspan="2">&nbsp;</td>		
            </tr>			
		</table>
        </div>
        
        <div class="setup_emails">
        <h2>New Review Confirmation - sent to website administrators</h2>
        <table align="left" border="0" cellpadding="2" cellspacing="5">
        	<tr>
				<td class="email-td">To: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter an e-mail address you want to send the review confirmation e-mail to</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="REV_TO" type="text" size="60"  value="<?php echo html_entity_decode($email_setup->EM_REV_TO) ?>" maxlength="70" />
              <td>
			</tr>
            <tr>
				<td>CC: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter an e-mail address you want to copy the review confirmation e-mail to</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="REV_CC" type="text" size="60"  value="<?php echo html_entity_decode($email_setup->EM_REV_CC) ?>" maxlength="70" />
              <td>
			</tr>
            <tr>
				<td>BCC: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter an e-mail address to receive a blind copy of the review confirmation e-mail</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="REV_BCC" type="text" size="60"  value="<?php echo html_entity_decode($email_setup->EM_REV_BCC) ?>" maxlength="70" />
              <td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
			<tr>
				<td>Subject: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the title of the e-mail; this will show in the Subject column of the recipients' e-mail application</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><input name="REV_SUBJECT" type="text" size="88" value="<?php echo html_entity_decode($email_setup->EM_REV_SUBJECT) ?>" maxlength="70" />
              <td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
			<tr>
				<td>Header: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want at the start of the review confirmation e-mail</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="REV_HEADER" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_REV_HEADER) ?></textarea><td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
			<tr>
				<td>Content: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want in the body of the review confirmation e-mail</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="REV_CONTENT" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_REV_CONTENT) ?></textarea><td>
			</tr>
			
            <tr>
				<td colspan="2" class="td-sep">&nbsp;</td>		
            </tr>			
			
            <tr>
				<td>Footer: 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the text you want at the end of the review confirmation e-mail</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                <td><textarea type="text" name="REV_FOOTER" class="p-amend-textarea"  ><?php echo html_entity_decode($email_setup->EM_REV_FOOTER) ?></textarea><td>
			</tr>
			<tr>
				<td></td>
				<td><input name="UPDATE" type="submit" value="Update &raquo;&raquo;"class="update-button"> 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click this button to save your updates</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
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
        </div>
	</form>
<?php
  include_once("includes/footer_admin.php");
?>

