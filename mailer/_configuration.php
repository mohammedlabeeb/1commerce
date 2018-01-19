<?php


/* Licensed software, from www.freecontactform.com */

$form_page_name = "index.html";
//$email_it_to_cc = "";
//$email_it_to_bcc = "";
//$email_it_from = "mark@shopfitter.com";
//$email_subject = "Shopfitter Registration confirmation";
$email_suspected_spam = "*SUSPECT Contact Us Form";
$accept_suspected_hack = "yes"; // change to "no" to NOT accept
$success_page = "";
//$failure_page = "confirm_members.php?email=fail";
$failure_accept_message = "yes";

// TIMEZONE - used to mark email datetime in the email
if(phpversion() > "5.0") {
	date_default_timezone_set('Europe/London'); // for List see: http://www.php.net/manual/en/timezones.php
}
$hour_offset = "+0";
$dateformat = "Y-m-d H:i:s"; // for List see: http://www.php.net/manual/en/function.date.php

?>