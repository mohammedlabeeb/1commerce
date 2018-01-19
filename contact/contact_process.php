<?php
ini_set('max_execution_time', 300); // 5 minutes to allow time for file upload
session_start();
error_reporting(0);

include "contact_configuration.php";


$email_timestamp = date($dateformat,mktime(date("H")+($hour_offset),date("i"),date("s"),date("m"),date("d"),date("y")));



// reCAPTCHA code
if(isset($reCAPTCHA_publickey)) {
	if(strlen(trim($reCAPTCHA_publickey))>0 && strlen(trim($reCAPTCHA_privatekey))>0 && !isset($_POST['custom_antispam_field_index'])) {
	   require_once('requiredformfiles/recaptcha/recaptchalib.php');
	   $reCaptchaResponse = recaptcha_check_answer($reCAPTCHA_privatekey,
	                                $_SERVER["REMOTE_ADDR"],
	                                $_POST["recaptcha_challenge_field"],
	                                $_POST["recaptcha_response_field"]);
	   if (!$reCaptchaResponse->is_valid) {
	   	$antispamfail = true;
	    // error_found("The security challenge (reCAPTCHA) was entered incorrectly.",$failure_accept_message,$failure_page);
	   }
	   unset($_POST["recaptcha_challenge_field"]);
	   unset($_POST["recaptcha_response_field"]);
	}
}
// end of reCAPTCHA code


// CUSTOM ANTI-SPAM
if(isset($_POST['custom_antispam_field'])) {
	$ind = $_POST['custom_antispam_field_index'];
	$answer = $custom_captcha_challenges[$ind][1];
	if(strtolower($_POST['custom_antispam_field']) == strtolower($answer)) {
	  // good
	} else {
		$antispamfail = true;
	// error_found("The security challenge (anti-spam) field was entered incorrectly.",$failure_accept_message,$failure_page);
	}
}


// VALIDATION
require_once('requiredformfiles/_validation.php');

foreach($fieldlist as $field_name => $field_value) {
	if($field_value == "OPTIONAL" || 
		$field_name == "recaptcha_challenge_field" ||
		$field_name == "recaptcha_response_field") {
		continue;
	}
	$fieldlist_to_validate[$field_name] = $field_value;
}

$val = new FCFvalidate;
$val->validate($fieldlist_to_validate, $_POST);
if($val->error) {
  $er = $val->error_string;
  if(isset($antispamfail)) {
  	$er .= "The security challenge (anti-spam) field was entered incorrectly.<br />";
  }
  error_found($er,$failure_accept_message,$failure_page);
  exit();
}

if(isset($antispamfail)) {
	$er = "The security challenge (anti-spam) field was entered incorrectly.<br />";
	error_found($er,$failure_accept_message,$failure_page);
	exit();
}
// VALIDATION










// set-up redirect page
$redirect_to = $success_page;



if(!preg_match("/@/",$email_it_from) && !is_array($email_it_from)) {
	$email_it_from = $_POST[$email_it_from];
}


// function to handle errors
function error_found($mes,$failure_accept_message,$failure_page) {
   $_SESSION['error_message'] = $mes;
   header("Location: requiredformfiles/$failure_page");
   die();
}

$email_message = "";

  // loop through all form fields submitted
  foreach($_POST as $field_name => $field_value) {

		if($field_name == "recaptcha_challenge_field" || 
			$field_name == "custom_antispam_field_index" || 
			$field_name == "custom_antispam_field") {
			continue;
		}
  	
      if(is_array($field_value)) {
      	$this_val = '';
      	$fvac = count($field_value);
      	$fvaci=0;
      	foreach($field_value as $fva) {
      		$fvaci++;
      		$this_val .= $fva;
      		if($fvaci < $fvac) {
      			$this_val .= ", ";
      		}
      	}
      } else {
	      $this_val = $field_value;
      }
      if(get_magic_quotes_gpc()) {
      	$email_message .= str_replace("\n","\r\n",$field_name).": ".stripslashes($this_val)."\r\n\r\n";
      } else {
      	$email_message .= str_replace("\n","\r\n",$field_name).": ".$this_val."\r\n\r\n";
      }
  }


  $email_message .= "Senders IP Address: ".$_SERVER['REMOTE_ADDR']."\r\n";
  $email_message .= "Form submitted at: ".$email_timestamp."\r\n";
  $email_message .= "Referring Page: ".$_SERVER['HTTP_REFERER']."\r\n\r\n";



if (version_compare(PHP_VERSION, '5.0.0', '<') ) {
	require("requiredformfiles/phpv4/class.phpmailer.php");
} else {
	require("requiredformfiles/class.phpmailer.php");
}


$mail = new PHPMailer();

if($smtp_use == "yes") {
	$mail->IsSMTP();
	if($smtp_ssl == "yes") {
		$mail->SMTPSecure = "ssl"; 
	}
	$mail->Port = $smtp_port; 
	$mail->Host = $smtp_host;
	$mail->SMTPAuth = $smtp_auth;
	$mail->SMTPSecure = $smtp_secure;
	$mail->Username = $smtp_user;
	$mail->Password = $smtp_pass;
}

$mail->From = $email_it_from;
$mail->FromName = $email_it_from;

if(is_array($email_it_to)) {
	foreach($email_it_to as $email_it_to_element) {
		$mail->AddAddress($email_it_to_element,$email_it_to_element);
	}
} else {
	$mail->AddAddress($email_it_to,$email_it_to);
}

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
			    $mail->AddAttachment($source_name,$original_filename);
		    }
	    }
	}
}

// $mail->WordWrap = 50;
// $mail->IsHTML(false);

$mail->Subject = $email_subject;
$mail->Body    = $email_message;
// $mail->AltBody = $email_message;


if(isset($email_it_to_cc) && trim($email_it_to_cc) <> "") {
 $mail->AddCC(trim($email_it_to_cc), trim($email_it_to_cc));
}

if(isset($email_it_to_bcc) && trim($email_it_to_bcc) <> "") {
 $mail->AddBCC(trim($email_it_to_bcc), trim($email_it_to_bcc));
}


if(!$mail->Send()) {
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

// reset variable
$mail = "";

// send the auto-responder
if(isset($email_autoresponder) && $email_autoresponder == "yes") {

	$mail = new PHPMailer();

	if($smtp_use == "yes") {
		$mail->IsSMTP();
		if($smtp_ssl == "yes") {
			$mail->SMTPSecure = "ssl"; 
		}
		$mail->Port = $smtp_port; 
		$mail->Host = $smtp_host;
		$mail->SMTPAuth = $smtp_auth;
		$mail->SMTPSecure = $smtp_secure;
		$mail->Username = $smtp_user;
		$mail->Password = $smtp_pass;
	}

	$mail->From = $email_autoresponder_from;
	$mail->FromName = $email_autoresponder_from;
	
	if(!preg_match("/@/",$email_autoresponder_to)) {
		$email_autoresponder_to = $_POST[$email_autoresponder_to];
	}
	
	$mail->AddAddress($email_autoresponder_to,$email_autoresponder_to);
	$mail->Subject = $email_autoresponder_subject;
	$mail->Body    = $email_autoresponder_message;
	if(!$mail->Send()) {
	   echo "Message could not be sent. <p>";
	   echo "Mailer Error: " . $mail->ErrorInfo;
	   exit;
	}

}











// PHP 4 compatible class - change 'var' to 'public' if using PHP 5
class required {
    var $fields; // public
    function required() // __construct()
    {
        $this->fields = array();
    } 
    function add($name, $type) // public
    {
        $this->fields[$name] = $type;
    } 
    function out() // public
    {
        return $this->fields;
    } 
} 
class validate {
    var $error = false; // public
    var $error_string; // publc
    var $error_tmp = "data not valid";

    function validate($ar, $post) // __construct()
    {
        if (!is_array($ar)) {
            /* no validation required */
        } else {
            foreach($ar as $a_name => $a_type) {
            	
            	$a_name = str_replace("[]","",$a_name);
            	
                /* if no validation specified, make not_empty */
                if (trim($a_type) == "") {
                    $a_type = "NOT_EMPTY";
                } 
                /* make sure $name is in $post */
                $found = false;
                foreach($post as $p_name => $p_value) {
                	
                	$p_name = str_replace("[]","",$p_name);
                	                	
                    if ($p_name == $a_name) {
                        $found = true;
                        break;
                    } 
                } 
                if (!$found) {
                    $this->error("FIELD: " . $a_name . " -> ERROR: no data submitted.<br />");
                } else {

                    if (is_array($p_value)) {
                        foreach($p_value as $pv) {
                            if (!$this->checkit($pv, $a_type)) {
                                echo "<i>$p_value,$tp</i><br />";
                                $this->error("FIELD: " . $a_name . " -> ERROR: " . $this->error_tmp . "<br />");
                            } 
                        } 
                    } else {
                        if (!$this->checkit($p_value, $a_type)) {
                            $this->error("FIELD: " . $a_name . " -> ERROR.: " . $this->error_tmp . "<br />");
                        } 
                    } 
                } 
            } 
        } 
    } 
    /* ERRORS */
    function error($str) // private
    {
        $this->error = true;
        $this->error_string .= $str;
    } 
    /* VALIDATE FIELD AGAINST TYPE */
    function checkit($value, $type) // private
    {
        $length = "";
        if (preg_match('/^MIN[0-9]+$/', $type)) {
            $tmp = explode(":", $type);
            $length = $tmp[1];
            $type = "MINLENGTH";
        } 
        if (preg_match('/^MAX[0-9]+$/', $type)) {
            $tmp = explode(":", $type);
            $length = $tmp[1];
            $type = "MAXLENGTH";
        } 

        switch ($type) {
        	
        	case "RADIO_GROUP":
		    case "CHECKBOX_GROUP":
            case "NOT_EMPTY":
                $this->error_tmp = "string cannot be empty";
                return $this->not_empty($value);
                break;

            case "MINLENGTH":
                if (strlen($value) < $length) {
                    $this->error_tmp = "string to short";
                    return false;
                } else {
                    return true;
                } 
                break;

            case "MAXLENGTH":
                if (strlen($value) > $length) {
                    $this->error_tmp = "string to long";
                    return false;
                } else {
                    return true;
                } 
                break;

            case "ALPHA":
                $exp = '/^[a-z]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not alpha";
                    return false;
                } 
                break;

            case "ALPHASPACE":
                $exp = '/^[a-z ]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not alphaspace";
                    return false;
                } 
                break;

            case "ALPHANUM":
                $exp = '/^[a-z0-9]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not alphanum";
                    return false;
                } 
                break;

            case "ALPHANUMSPACE":
                $exp = '/^[a-z0-9 ]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not alphanumspace";
                    return false;
                } 
                break;

            case "NUMERIC":
                $exp = '/^[0-9]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not numeric";
                    return false;
                } 
                break;

            case "NUMERICPLUS":
                $exp = '/^[0-9+-.]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not numericplus";
                    return false;
                } 
                break;

            case "EMAIL":
                $exp = '/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "not a valid email";
                    return false;
                } 
                break;

            case "YYYYMMDD":
                $exp = '/^(19|20)[0-9][0-9][- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not YYYYMMDD";
                    return false;
                } 
                break;

            case "DDMMYYYY":
                $exp = '/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9][0-9]$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not DDMMYYYY";
                    return false;
                } 
                break;

            case "MMDDYYYY":
                $exp = '/^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)[0-9][0-9]$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not MMDDYYYY";
                    return false;
                } 
                break;

            default:
                if ($this->not_empty($value) && $this->regex($type, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "string not valid";
                    return false;
                } 
        } 
    } 
    /* NOT_EMPTY */
    function not_empty($value) // private
    {
        if (trim($value) == "") {
            return false;
        } else {
            return true;
        } 
    } 

    /* REGULAR EXPRESSION */
    function regex($regex, $value) // private
    {
        $the_regex = 'preg_match("/' . $regex . '/i", "' . $value . '")';
        $the_code = '<?php if(' . $the_regex . ') { return true; } else { return false; } ?>';
        if (!eval('?>' . $the_code . '<?php ')) {
            return false;
        } else {
            return true;
        } 
    } 
}

  // redirect
header("Location: $redirect_to");
die("<script>location.replace('$redirect_to')</script>");
?>