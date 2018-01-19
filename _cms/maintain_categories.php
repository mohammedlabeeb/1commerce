<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$message = "";
if (isset($_POST['UPDATE'])) {
	//validate all fields first
	if (strlen($_POST['VAT']) > 0 and validate2dp($_POST['VAT']) == "false"){
		$message .= "Please enter a valid Default VAT rate to 2 decimal places" . "<br/>";
		$warning = "red";
	}
	if (validateSeed($_POST['CAT_SEED']) == "false"){
		$message .= "Please enter a valid Category Seed" . "<br/>";
		$warning = "red";
	}
	if (validateSeed($_POST['PROD_SEED']) == "false"){
		$message .= "Please enter a valid Product Seed" . "<br/>";
		$warning = "red";
	}
	if ($message == ""){
		//no error message so update database preferences table 
		$fields = array("pref_shop_id"=>$_POST['SHOP_ID'], "pref_shopname"=>$_POST['SHOPNAME'], "pref_shopurl"=>$_POST['SHOPURL'],
						"pref_meta_title"=>$_POST['META_TITLE'], "pref_meta_desc"=>$_POST['META_DESC'], "pref_meta_keywords"=>$_POST['META_KEYWORDS'],																							
						"pref_vat"=>$_POST['VAT'], "pref_cat_seed"=>$_POST['CAT_SEED'], "pref_prod_seed"=>$_POST['PROD_SEED']);
		$rows = Rewrite_Preferences($fields);
		if ($rows == 1){
			$message = $rows . " record successfully UPDATED";
			$warning = "green";
		}
		if ($rows == 0){
			$message = "WARNING ! ! ! - NO RECORDS UPDATED";
			$warning = "orange";
		}
		if ($rows > 1){
			$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") RECORDS UPDATED - PLEASE CONTACT SHOPFITTER";
			$warning = "red";
		}
		$error = null;
		$error = mysql_error();
		if ($error != null) { 
			$message .= " - ERRORS FOUND ! ! ! - " . mysql_error() . " ";
		}
	}
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Preferences";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Maintain Categories - Create and Amend Category Details</h1>
	<br/>
	<form action="/preferences" method="post">
		<table align="left" border="0" cellpadding="2" cellspacing="5">
			<tr>
				<td nowrap>Shopfitter ID</td>
				<td>
                    <strong><?php echo $preferences->PREF_SHOP_ID ?></strong>
                    <input type="hidden" name="SHOP_ID" value="<?php echo $preferences->PREF_SHOP_ID ?>">
                </td>
			</tr>
			<tr>
				<td nowrap>Shop Name</td>
				<td><input type="text" name="SHOPNAME" SIZE="32" value="<?php echo $preferences->PREF_SHOPNAME ?>"></td>
			</tr>
			<tr>
				<td nowrap>Shop URL</td>
				<td><input type="text" name="SHOPURL" SIZE="32" value="<?php echo $preferences->PREF_SHOPURL ?>"></td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td nowrap>Default Meta Title</td>
                <td><textarea type="text" name="META_TITLE" class="p-amend-textarea"  ><?php echo html_entity_decode($preferences->PREF_META_TITLE) ?></textarea><td>
			</tr>
			<tr>
				<td nowrap>Default Meta Description</td>
                <td><textarea type="text" name="META_DESC" class="p-amend-textarea"  ><?php echo html_entity_decode($preferences->PREF_META_DESC) ?></textarea><td>
			</tr>
			<tr>
				<td nowrap>Default Meta Keywords</td>
                <td><textarea type="text" name="META_KEYWORDS" class="p-amend-textarea"  ><?php echo html_entity_decode($preferences->PREF_META_KEYWORDS) ?></textarea><td>
			</tr>
            <tr>
				<td nowrap>Default VAT Rate</td>
				<td><input type="text" name="VAT" SIZE="8" value="<?php echo $preferences->PREF_VAT ?>">
			</tr>
                        <tr>
				<td nowrap>Category Seed</td>
				<td><input type="text" name="CAT_SEED" SIZE="8" value="<?php echo $preferences->PREF_CAT_SEED ?>">
			</tr>
                        <tr>
				<td nowrap>Product Seed</td>
				<td><input type="text" name="PROD_SEED" SIZE="8" value="<?php echo $preferences->PREF_PROD_SEED ?>">
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td></td>
				<td><input name="UPDATE" type="submit" value="update &raquo;&raquo;"></td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <!---<tr>
				<td nowrap>Message</td>
				<td><input type="text" name="MESSAGE" value="<?php echo "FREDDIE" ?>">
			</tr>--->
            <tr>
				<td colspan="2"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
			</tr>
		</table>
	</form>
<?php
  include_once("includes/footer_admin.php");
?>

