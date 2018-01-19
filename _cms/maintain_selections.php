<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$preferences = getPreferences();
$pageTitle = $preferences->PREF_META_TITLE;
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Maintain Selections - Create and Amend Selection Boxes and their Options</h1>

    </div>
<?php
  include_once("includes/footer_admin.php");
?>

