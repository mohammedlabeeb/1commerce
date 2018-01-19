<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

//defaults
$message = "";
$scrolltobottom = "";
$filename = "quantity_discount";
if(isset($_GET['file'])){
	//then the page has been swapped back from create_qd_xml_noscreen.php
	$message = "Total Products written = " . $_GET['products'] . "   Total Discount lines written = " . $_GET['lines'];
}

if (isset($_POST['CREATE'])) {
	//validate all fields first
	if (strlen($_POST['FILENAME']) == 0){
		$message .= "Please enter a valid File Name" . "<br/>";
		$warning = "red";
	}
	if ($message == ""){
		$filename = $_POST['FILENAME'];
		echo "<script type=\"text/javascript\">document.location.href=\"/_cms/create_qd_xml_noscreen.php?file=" . $filename . "\";</script>";
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
	<h1>Create Quantity Discount XML file</h1>
	<br/>
	<form action="/_cms/create_qd_xml.php" method="post">
		<table align="left" border="0" cellpadding="2" cellspacing="5">
			<tr>
				<td>File Name (.xml) 
                <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">This is the name of the file you submit to Google's Webmaster Tools system; we've set it to default to sitemap.xml but you can use your preferred file name: the extension .xml will be added automatically so please don't include it in the name you enter</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
				<td>
                	<input type="text" name="FILENAME_DISABLED" SIZE="32" value="<?php echo $filename ?>" disabled />
                    <input type="hidden" name="FILENAME" SIZE="32" value="<?php echo $filename ?>" />
                </td>
			</tr>
			
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td><input name="CREATE" type="submit" value="Create XML &raquo;&raquo;" class="create-button"></td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <tr>
				<td colspan="2">
                	<!---<p style="font-size:110%;">Quantity Discount XML file URL: <?php echo $preferences->PREF_SHOPURL ?>/xml/quantity_discount.xml</p>-->
                </td>
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
                    <!-- Any screen explanations etc. then whack it in here --->
                    <label id="MESSAGE"class="green"><?php echo $message ?></label>	
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
<?php
  include_once("includes/footer_admin.php");
?>

