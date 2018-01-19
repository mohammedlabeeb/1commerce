<?php

include_once("../includes/masterinclude.php");

/* get 1-ecommerce preferences */
$preferences = getPreferences();

/* Licensed software, from www.freecontactform.com */

$fieldlist = array();
$fieldlist["Name"] = "NOT_EMPTY";
$fieldlist["Email"] = "EMAIL";
$fieldlist["Comments"] = "NOT_EMPTY";
$form_page_name = "index.php";
$email_it_to = "$preferences->PREF_EMAIL";
$email_it_to_cc = "";
$email_it_to_bcc = "";
$email_it_from = "$preferences->PREF_EMAIL";
$email_subject = "Contact from $preferences->PREF_SHOPNAME website";
$email_suspected_spam = "*SUSPECT Contact Form";
$accept_suspected_hack = "yes"; // change to "no" to NOT accept
$success_page = "../Thank-You.htm";
$failure_page = "_formerror.php";
$failure_accept_message = "yes";



// CAPTCHA OPTION 2: 
$custom_captcha = "yes"; // set to "yes" to use

// ENTER YOUR QUESTION AND ANSWER PAIRS - add as many as you like
$custom_captcha_challenges[] = array("If you add 5 to 8 what is the result?", "13");
$custom_captcha_challenges[] = array("If you add 5 to 5 what is the result?", "10");
$custom_captcha_challenges[] = array("Using only numbers, how many days are in one week?", "7");
$custom_captcha_challenges[] = array("Using only numbers, how many hours are in one day?", "24");


$rnd = rand(0,count($custom_captcha_challenges)-1);
$custom_antispam_field_index = $rnd;
$custom_antispam_field_question = $custom_captcha_challenges[$rnd][0];
$customer_antispam_field_HTML = $custom_captcha_challenges[$rnd][0].
	' &nbsp; <input type="hidden" name="custom_antispam_field_index" value="'.$rnd.'" /> 
	  <input size="8" type="text" name="custom_antispam_field" id="custom_antispam_field" />';

	
if(isset($reCAPTCHA_publickey)) {	
	if(strlen(trim($reCAPTCHA_publickey)) > 0 
	  && strlen(trim($reCAPTCHA_privatekey)) > 0 
	  && $custom_captcha == "no") {
	  if(isset($fieldlist)) {
		// $fieldlist[] = "recaptcha_challenge_field";
		// $fieldlist[] = "recaptcha_response_field";
		$fieldlist["recaptcha_challenge_field"] = "NOT_EMPTY";
		$fieldlist["recaptcha_response_field"] = "NOT_EMPTY";
	  }
	}
}

// SMTP EMAILS
$smtp_use = "no"; // set to no if you do not want to use SMTP
$smtp_host = "your.serverhost.com";
$smtp_auth = true;
$smtp_secure = "tls";
$smtp_user = "account-username";
$smtp_pass = "account-password";
$smtp_ssl = "yes"; // set to no if you do not need to use ssl
$smtp_port = 587; // or try 465;


// 	AUTO-RESPONDER EMAIL MESSAGE
$email_autoresponder = "yes";
$email_autoresponder_from = "$preferences->PREF_EMAIL";
$email_autoresponder_to = "Email"; // enter email field name from the form
$email_autoresponder_subject = "Your message has been received";
$email_autoresponder_message = 
"Hi,

We have received your message and will to get back to you as soon as possible.

Regards,
$preferences->PREF_SHOPNAME
";

// TIMEZONE - used to mark email datetime in the email
if(phpversion() > "5.0") {
	date_default_timezone_set('Europe/London'); // for List see: http://www.php.net/manual/en/timezones.php
}
$hour_offset = "+0";
$dateformat = "Y-m-d H:i:s"; // for List see: http://www.php.net/manual/en/function.date.php
?>