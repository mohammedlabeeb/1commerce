<?php

include_once("mailer/_configuration.php");
//$email_subject = "New Membership Application";
$email_timestamp = date($dateformat,mktime(date("H")+($hour_offset),date("i"),date("s"),date("m"),date("d"),date("y")));

// SOME MAIL SYSTEMS LIKE TO SEE AN EXTRA PARAMETER IN THE EMAIL HEADER
// THIS IS USED IN THE EMAIL HEADER TO HELP THE EMAIL BE DELIVERED
// SOME SERVERS MAY BLOCK EMAILS IF THIS IS NOT FOUND
// ENTER A USER WHICH IS KNOWN BY YOUR SERVER
// FOR EXAMPLE THE EMAIL ADDRESS ATTACHED TO THE HOSTING ACCOUNT
// USUALLY THIS EMAIL WILL BE AT THE SAME DOMAIN NAME
// FOR EXAMPLE IF YOUR WEBSITE IS: mydomain.com, then try info@mydomain.com
// THIS IS SWITCHED-OFF BY DEFAULT - SWITCH ON IF YOU HAVE PROBLEMS
// SET $use_additional_param to true
$email_it_from_trusted_user = "info@somedomain.com";
$use_the_f_flag_option = true;
$use_additional_param = false; // set this to true if you have problems


if($use_additional_param) {
	if($use_the_f_flag_option) {
		$additional_param = "-f $email_it_from_trusted_user";
	} else {
		$additional_param = "$email_it_from_trusted_user";
	}
} else {
	$additional_param = "";
}

// set-up redirect page
$redirect_to = $success_page;

if(!preg_match("/@/",$email_it_from) && !is_array($email_it_from)) {
	$email_it_from = $_POST[$email_it_from];
}

// function to handle errors
function error_found($mes,$failure_accept_message,$failure_page) {
   if($failure_accept_message == "yes") {
       $qstring = "?prob=".urlencode(base64_encode($mes));
   } else {
        $qstring = "";
   }
   $error_page_url = $failure_page."".$qstring;
   header("Location: $error_page_url");
   die();
}

$email_message = "";
//$email_message .= "Senders IP Address: ".$_SERVER['REMOTE_ADDR']."\r\n";
//$email_message .= "New Membership Application at: ".$email_timestamp . "\r\n\r\n";
$email_message .= $email_subject . " at: ".$email_timestamp . "\r\n\r\n";


//$email_message .= "Referring Page: ".$_SERVER['HTTP_REFERER']."\r\n\r\n";
$email_message .= $email_confirmation;
  
if(phpversion() < 5) {
	include_once '_mailclass4.php';
} else {
	include_once '_mailclass5.php';
}


unset($_SERVER['PHP_SELF']);
unset($_SERVER['REMOTE_ADDR']);

if(is_array($email_it_to)) {
	foreach($email_it_to as $email_it_to_element) {

		$myemail = new createTheMail($additional_param);
		$myemail->iso = "iso-8859-1";
		$myemail->to = "$email_it_to_element";
		$myemail->from = "$email_it_from";
		$myemail->from_name = "$email_it_from";
		$myemail->reply_to = "$email_it_from";
        if(strlen(trim($email_it_to_cc)) > 7) {
            $myemail->setCc($email_it_to_cc,$email_it_to_cc);
        }
        if(strlen(trim($email_it_to_bcc)) > 7) {
            $myemail->setBcc($email_it_to_bcc,$email_it_to_bcc);
        }
		$myemail->subject = $email_subject;
		$myemail->message = $email_message;

        if(count($_FILES) > 0 ) {
		    for($i=0; $i < count($_FILES['upload']['name']); $i++) {

			    if(trim($_FILES['upload']['name'][$i]) == "") {
				    // nothing selected
			    } else {

				    $file_extension = explode(".",$_FILES['upload']['name'][$i]);
				    $original_filename = $_FILES['upload']['name'][$i];
				    $file_extension = ".".strtolower($file_extension[(count($file_extension)-1)]);
				    $source_name = $_FILES['upload']['tmp_name'][$i];
				    $source_type = $_FILES['upload']['type'][$i];
				    $source_size = $_FILES['upload']['size'][$i];
				    if($source_size == 0) {
					    die('The size of the file '.$original_filename.' is to big, please go back and try again with a smaller file.');
				    } else {
					    $myemail->addAttachment($source_name,$original_filename,$source_type);
				    }

			    }

		    }
        }
		$myemail->mail();

	}
} else {
	$myemail = new createTheMail($additional_param);
	$myemail->iso = "iso-8859-1";
	$myemail->to = "$email_it_to";
	$myemail->from = "$email_it_from";
	$myemail->from_name = "$email_it_from";
	$myemail->reply_to = "$email_it_from";
	if(strlen(trim($email_it_to_cc)) > 7) {
        $myemail->setCc($email_it_to_cc,$email_it_to_cc);
    }
    if(strlen(trim($email_it_to_bcc)) > 7) {
        $myemail->setBcc($email_it_to_bcc,$email_it_to_bcc);
    }
	$myemail->subject = $email_subject;
	$myemail->message = $email_message;

    if(count($_FILES) > 0 ) {
	    for($i=0; $i < count($_FILES['upload']['name']); $i++) {

		    if(trim($_FILES['upload']['name'][$i]) == "") {
			    // nothing selected
		    } else {

			    $file_extension = explode(".",$_FILES['upload']['name'][$i]);
			    $original_filename = $_FILES['upload']['name'][$i];
			    $file_extension = ".".strtolower($file_extension[(count($file_extension)-1)]);
			    $source_name = $_FILES['upload']['tmp_name'][$i];
			    $source_type = $_FILES['upload']['type'][$i];
			    $source_size = $_FILES['upload']['size'][$i];
			    if($source_size == 0) {
				    die('The size of the file '.$original_filename.' is to big, please go back and try again with a smaller file.');
			    } else {
				    $myemail->addAttachment($source_name,$original_filename,$source_type);
			    }

		    }

	    }
    }
	$myemail->mail();
}

//at this point the email is successful otherwise the program redirects to $failure_page - in this case "confirm_members.php?email=fail"
?>