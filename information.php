<?php
require_once("includes/session.php");
include_once("includes/masterinclude.php");

$preferences = getPreferences();
//$pageTitle = $preferences->IN_NAME;
//$pageMetaDescription = $preferences->PREF_META_DESC;
//$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;
$category = "";
$attribute1 = ""; $attribute2 = ""; $attribute3 = ""; $attribute4 = "";
$top_level="0";

$name = $_GET['page'];
$information = getInformationPage($name);
$infopagename=$information->IN_NAME;
//meta data for information pages now taken from the information table
$pageTitle = $information->IN_NAME . html_entity_decode($information->IN_TITLE);
$pageMetaDescription = html_entity_decode($information->IN_META_DESC);
$pageMetaKeywords = html_entity_decode($information->IN_META_KEYWORDS);
$pageCustomHead = html_entity_decode($information->IN_CUSTOM_HEAD, ENT_QUOTES);

include_once("includes/header.php");
?>

<div class="body-content-info">
    <div class="info-heading">
        <h1><?php echo $information->IN_NAME ?></h1>
    </div>
	<?php
    	//DATA
		//$name = $_GET['page'];
        //$information = getInformationPage($name);
		echo html_entity_decode($information->IN_DATA, ENT_QUOTES);
    ?>
	<p class="spacer">&nbsp;</p>

<?php
  include_once("includes/footer.php");
?>
