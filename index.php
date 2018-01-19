<?php
	//determine wherher a member has just logged in
	require_once("includes/session.php");
	//confirm_logged_in();
	if (isset($_GET['logout'])){
		logout();
		$login = 0;
		$justloggedout = 1;
	}
?>
<?php
include_once("includes/masterinclude.php");

$preferences = getPreferences();
$pageTitle = $preferences->PREF_META_TITLE;
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

//read Home Page Category details for the Attribute based Search Boxes - NOTE that the Home Page category code is hard coded to "CAAA000"
$tree = 0;
$c = getCategory("CAAA000", "");
$top_level="0"; $infopagename = "";
$category = $c->CA_CODE;
$attribute1 = $c->CA_ATTRIBUTE1; $attribute2 = $c->CA_ATTRIBUTE2; $attribute3 = $c->CA_ATTRIBUTE3; $attribute4 = $c->CA_ATTRIBUTE4;
$attribute5 = $c->CA_ATTRIBUTE5; $attribute6 = $c->CA_ATTRIBUTE6; $attribute7 = $c->CA_ATTRIBUTE7; $attribute8 = $c->CA_ATTRIBUTE8;

$information = getInformationPage("Home");
$pageCustomHead = html_entity_decode($information->IN_CUSTOM_HEAD, ENT_QUOTES);

include_once("includes/header.php");
?>
<div class="body-indexcontent">
	<?php		
		//$name = $_GET['page'];
        //$information = getInformationPage("Home");
		echo html_entity_decode($information->IN_DATA, ENT_QUOTES);
	?>

<?php
  include_once("includes/footer-hp.php");
?>
