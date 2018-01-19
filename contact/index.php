<?php
require_once("../includes/session.php");
include_once("../includes/masterinclude.php");

$information = getInformationPage("Contact");
$category = "";
$attribute1 = ""; $attribute2 = ""; $attribute3 = ""; $attribute4 = "";
$top_level="0"; $infopagename=$information->IN_NAME;

$preferences = getPreferences();
$pageTitle = $information->IN_NAME . html_entity_decode($information->IN_TITLE);
//$pageTitle = html_entity_decode($information->IN_TITLE);
$pageMetaDescription = html_entity_decode($information->IN_META_DESC);
$pageMetaKeywords = html_entity_decode($information->IN_META_KEYWORDS);
$pageCustomHead = html_entity_decode($information->IN_CUSTOM_HEAD, ENT_QUOTES);

require_once('../includes/header-cf.php');

?>

<!-- category.inc -->
<div class="body-content-info">


	<h1>Contact Us</h1>

	<div class="search-message">
	<?php
        
                
                //$name = $_GET['page'];
                $information = getInformationPage("Contact");
                echo html_entity_decode($information->IN_DATA, ENT_QUOTES);
        
        ?> 
	</div>

<div class="contactformpro">
    
    
    <script type="text/javascript" src="requiredformfiles/_validation.js"></script>
    <script type="text/javascript">
    FCFrequired.add('Name','NOT_EMPTY','Name');
    FCFrequired.add('Email','EMAIL','Email');
    FCFrequired.add('Comments','NOT_EMPTY','Comments');
    </script>
    <form accept-charset="utf-8" method="post" action="contact_process.php" onsubmit="return FCFvalidate.check(this)">
    <table border="0">
    <tr>
     <td colspan="2">
      <p>Fields marked with <span class="required_star"> * </span> are required.</p><br />
     </td>
    </tr>
    <tr>
     <td valign="top">
      <label for="Name">Name<span class="required_star"> * </span></label>
     </td>
     <td valign="top">
      <input size="40" type="text" name="Name" id="Name" maxlength="50" value="" />
     </td>
    </tr>
    <tr>
     <td valign="top">
      <label for="Email">Email<span class="required_star"> * </span></label>
     </td>
     <td valign="top">
      <input size="40" type="text" name="Email" id="Email" maxlength="50" value="" />
     </td>
    </tr>
    <tr>
     <td valign="top">
      <label for="Comments" class="required">Message<span class="required_star"> * </span></label>
     </td>
     <td valign="top">
      <textarea cols="40" rows="6" name="Comments" id="Comments" maxlength="1000"></textarea>
     </td>
    </tr>
    <!--
    <?php 
    echo " -" . "->"; 
    require_once('contact_configuration.php'); 
    if(isset($reCAPTCHA_privatekey) && strlen(trim($reCAPTCHA_privatekey)) > 8 && $custom_captcha == "no") { 
    echo "
    <tr>
     <td>&nbsp;</td>
     <td>";
      require_once('requiredformfiles/recaptcha/recaptchalib.php');
      echo recaptcha_get_html($reCAPTCHA_publickey);
     echo "<script>
      FCFrequired.add('recaptcha_response_field','NOT_EMPTY','Security Challenge (reCAPTCHA)');
     </script>";
     echo  "</td>
     </tr>";
    } elseif(isset($custom_captcha) && $custom_captcha == "yes") {
    echo '
    <tr>
     <td valign="top">
     <label for="custom_antispam_field">Challenge<span class="required_star"> * </span></label>
    <script>
     FCFrequired.add(\'custom_antispam_field\',\'NOT_EMPTY\',\'Security Challenge\');
     </script>
     <td>';
     echo 'To help prevent automated spam, please answer the following question.<br /><br />';
      echo $customer_antispam_field_HTML;
      echo '
      </td>
     </tr>';
    } 
    echo "<!-"."-"; 
    ?>
    -->
    <tr>
     <td colspan="2" align="center"><br /><br /><input class="big-button" type="submit" value="Submit Form" id="form_submit_button" /><br />
     </td>
    </tr>
    </table>
    </form>
</div>

<?php
  include_once("../includes/footer-cf.php");
?>