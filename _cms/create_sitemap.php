<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

//defaults
$message = "";
$scrolltobottom = "";
$filename = "sitemap";


if (isset($_POST['CREATE'])) {
	//validate all fields first
	if (strlen($_POST['FILENAME']) == 0){
		$message .= "Please enter a valid File Name" . "<br/>";
		$warning = "red";
	}
	if ($message == ""){
		$filename = $_POST['FILENAME'];
		echo "<script type=\"text/javascript\">document.location.href=\"/_cms/create_sitemap_noscreen.php?file=" . $filename . "\";</script>";
	}
}

if (isset($_GET['file'])) {
	$filename = $_GET['file'];
}

if (isset($_GET['records'])) {	$message = $_GET['records'] . " products updated into " . $filename . ".xml file";
	$warning = "green";
}
if (isset($_GET['backup']) and $_GET['backup'] == 1) {
		$message .= " - PREVIOUS FILE OF SAME NAME NOW BACKED UP AS .bak";
}
if (isset($_GET['errors']) and $_GET['errors'] != 0) {
		$message = "ERRORS FOUND - PLEASE LOOK AT ERROR LOG!!!";
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Create XML Sitemap";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Create Sitemap XML file</h1>
	<br/>
	<form action="/_cms/create_sitemap.php" method="post">
		<table align="left" border="0" cellpadding="2" cellspacing="5">
			<tr>
				<td class="sitemap-td">File Name (.xml) 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is the name of the file you submit to Google's Webmaster Tools system; we've set it to default to sitemap.xml but you can use your preferred file name: the extension .xml will be added automatically so please don't include it in the name you enter</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td><input type="text" name="FILENAME" SIZE="32" value="<?php echo $filename ?>"></td>
			</tr>
			
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td><input name="CREATE" type="submit" value="Create Sitemap &raquo;&raquo;" class="create-button"></td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <tr>
				<td colspan="2"><p style="font-size:110%;">XML Sitemap URL: <?php echo $preferences->PREF_SHOPURL ?>_cms/xml/sitemap.xml</p></td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			</tr>
		</table>
	</form>
    	<table style="float:left" border="0" cellpadding="2" cellspacing="5" width="100%">
            <tr>
				<td colspan="2">
                <div style="width:720px; padding-right:20px">
                  <p>Set up a Google Webmaster Tools account at <a href="http://www.google.com/webmasters/" title="http://www.google.com/webmasters/" target="_blank" rel="nofollow">www.google.com/webmasters/</a> or directly to <a href="http://www.google.com/webmasters/sitemaps/" title="http://www.google.com/webmasters/sitemaps/" target="_blank" rel="nofollow">the sitemaps login page</a> and click the Sign in to Webmaster Tools link. </p>
                  <p>&nbsp;</p>
                  <p>Once you're in, add a new site in the Dashboard and follow the instructions. </p>
                  <p>&nbsp;</p>
                  <p>You'll need to verify your site, do this by choosing Add Meta Tag and then copy the resulting text into the Custom Head Items box in the Preferences &amp; Settings page.                  </p>
<p>&nbsp;</p>
                  <p>Next, save your updates then go back to the Google Sitemaps system and click Verify. </p>
                  <p>&nbsp;</p>
                  <p>For more information go to <a href="http://support.google.com/webmasters/bin/answer.py?hl=en&answer=35769" title="http://support.google.com/webmasters/bin/answer.py?hl=en&answer=35769" target="_blank" rel="nofollow">Google Webmaster Guidelines</a>. </p>
                </div></td>
			</tr>
            <!---<tr>
				<td nowrap>Message</td>
				<td><input type="text" name="MESSAGE" value="<?php echo "FREDDIE" ?>">
			</tr>--->
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
<?php
  include_once("includes/footer_admin.php");
?>

