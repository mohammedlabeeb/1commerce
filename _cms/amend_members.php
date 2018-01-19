<?php
if (isset($_POST['LIST'])) {
	header( 'Location: list_members.php' ) ;
}

include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$logincount = 1;
$new = 1;
$errors = 0;
$errors_array = array();
$scrolltobottom = "";

//initialise screen fields
$selected_member = "";
$id = "";
$username = ""; $username_original = "";
$password = ""; $password_original = "";
$password_test = "";
$title = "MR"; $first_name = ""; $last_name = ""; $company_name = "";
$address1 = ""; $address2 = ""; $town = ""; $county = ""; $country = ""; $postcode = ""; $phone = ""; $mobile = ""; $email = ""; $category = "";
$member_confirmed = "N";
$ast_first = 0; $ast_last = 0; $ast_company = 0; $ast_add1 = 0; $ast_add2 = 0; $ast_town = 0; $ast_county = 0; $ast_country = 0; $ast_post = 0; $ast_phone = 0;
$ast_mobile = 0; $ast_email = 0; $ast_category = 0;
$ast_user = 0; $ast_pass = 0; $ast_passconf = 0;

if (isset($_POST['CLEAR'])) {
	//initialise all fields
	$selected_member = "";
	$id = "";
	$username = ""; $username_original = "";
	$password = ""; $password_original = "";
	$password_test = "";
	$title = "MR"; $first_name = ""; $last_name = ""; $company_name = "";
	$address1 = ""; $address2 = ""; $town = ""; $county = ""; $country = ""; $postcode = ""; $phone = ""; $mobile = ""; $email = ""; $category = "";
	$member_confirmed = "N";
	$ast_first = 0; $ast_last = 0; $ast_company = 0; $ast_add1 = 0; $ast_add2 = 0; $ast_town = 0; $ast_county = 0; $ast_country = 0; $ast_post = 0; $ast_phone = 0;
	$ast_mobile = 0; $ast_email = 0; $ast_category = 0;
	$ast_user = 0; $ast_pass = 0; $ast_passconf = 0;
}

if (isset($_POST['DELETE'])) {
	$rows = Delete_Member($_POST['ID'], $_POST['USERNAME']);
	$message = "";
	if ($rows == 1){
		$message = "Member successfully DELETED";
		$warning = "green";
	}
	if ($rows == 0){
		$message = "WARNING ! ! ! - RECORD NOT DELETED";
		$warning = "orange";
	}
	if ($rows > 1){
		$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") RECORD DELETED - PLEASE CONTACT SHOPFITTER";
		$warning = "red";
	}
	//initialise all fields
	$selected_member = "";
	$id = "";
	$username = ""; $username_original = "";
	$password = ""; $password_original = "";
	$password_test = "";
	$title = "MR"; $first_name = ""; $last_name = ""; $company_name = "";
	$address1 = ""; $address2 = ""; $town = ""; $county = ""; $country = ""; $postcode = ""; $phone = ""; $mobile = ""; $email = ""; $category = "";
	$member_confirmed = "N";
	$ast_first = 0; $ast_last = 0; $ast_company = 0; $ast_add1 = 0; $ast_add2 = 0; $ast_town = 0; $ast_county = 0; $ast_country = 0; $ast_post = 0; $ast_phone = 0;
	$ast_mobile = 0; $ast_email = 0;
	$ast_user = 0; $ast_pass = 0; $ast_passconf = 0;
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
}

if (isset($_GET['searchmember'])) {
	//NEW MEMBER SELECTED from search dropdown so get member deatils for display 
	$u = Get_Member($_GET['searchmember']);
	$id = $u->MB_ID;
	$selected_member = $u->MB_USERNAME;
	$id = $u->MB_ID;
	$username = $u->MB_USERNAME; $username_original = $username;
	$password = ""; $password_original = $u->MB_PASSWORD; //encrypted
	$category = $u->MB_CATEGORY;
	$title = $u->MB_TITLE;
	$first_name = $u->MB_FIRSTNAME;
	$last_name = $u->MB_LASTNAME;
	$company_name = $u->MB_COMPANY;
	$address1 = $u->MB_ADDRESS1;
	$address2 = $u->MB_ADDRESS2;
	$town = $u->MB_TOWN;
	$county = $u->MB_COUNTY;
	$country = $u->MB_COUNTRY;
	$postcode = $u->MB_POSTCODE;
	$phone = $u->MB_PHONE;
	$mobile = $u->MB_MOBILE;
	$email = $u->MB_EMAIL;
	$member_confirmed = $u->MB_CONFIRMED;
	
	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_MEMBER'] = $_GET['searchmember'];
}


if (isset($_POST['CREATE']) or isset($_POST['UPDATE'])) {
	if(isset($_POST['CREATE'])){$new = 1;}else{$new = 0;}
	if ($_POST['FIRST_NAME'] == ""){
		$errors_array[] = "Please enter a first name";
		$warning = "red";
		$ast_first = 1;
		$errors = 1;
	}
	if ($_POST['LAST_NAME'] == ""){
		$errors_array[] = "Please enter a last name";
		$warning = "red";
		$ast_last = 1;
		$errors = 1;
	}
	if ($_POST['ADDRESS1'] == ""){
		$errors_array[] = "Please enter an address";
		$warning = "red";
		$ast_add1 = 1;
		$errors = 1;
	}
	if ($_POST['TOWN'] == ""){
		$errors_array[] = "Please enter a Town";
		$warning = "red";
		$ast_town = 1;
		$errors = 1;;
	}
	if ($_POST['COUNTRY'] == ""){
		$errors_array[] = "Please enter a Country";
		$warning = "red";
		$ast_country = 1;
		$errors = 1;
	}
	if ($_POST['POSTCODE'] == ""){
		$errors_array[] = "Please enter a Postcode";
		$warning = "red";
		$ast_post = 1;
		$errors = 1;
	}
	if ($_POST['EMAIL'] == ""){
		$errors_array[] = "Please enter a valid email address";
		$warning = "red";
		$ast_email = 1;
		$errors = 1;
	}
	if ($_POST['CATEGORY'] == ""){
		$errors_array[] = "Please enter a valid  Price Category Code";
		$warning = "red";
		$ast_email = 1;
		$errors = 1;
	}
	if ($_POST['USERNAME'] == ""){
		$errors_array[] = "Please enter a User Name";
		$warning = "red";
		$ast_user = 1;
		$errors = 1;
	}else{
		$m = Confirm_Member($_POST['USERNAME'],"");
		if ($new == 1 and $m != 0){
			//trying to create an existing member!
			$errors_array[] = "User already exists!";
			$warning = "red";
			$ast_user = 1;
			$errors = 1;
		}
		if ($new == 0){
			if ($_POST['USERNAME'] !== $_POST['USERNAME_ORIGINAL']){
				$errors_array[] = "Cannot amend User Name";
				$warning = "red";
				$errors = 1;
				$ast_user = 1;
			}
		}
	}	
	if ($new == 1 and $_POST['PASSWORD'] == ""){
		$errors_array[] = "Please enter a Password";
		$warning = "red";
		$ast_pass = 1;
		$errors = 1;
	}
	if ($new == 1 and $_POST['PASSWORD'] != $_POST['PASSWORD_TEST']){
		$errors_array[] = "Password and confirmation do not match";
		$warning = "red";
		$errors = 1;
		$ast_pass = 1; $ast_passconf = 1;
	}
	if ($new == 0 and $_POST['PASSWORD'] != ""){
		//trying to change password
		if($_POST['PASSWORD'] != $_POST['PASSWORD_TEST']){
			$errors_array[] = "Password and confirmation do not match";
			$warning = "red";
			$errors = 1;
			$ast_pass = 1; $ast_passconf = 1;
		}
	}

	if($errors == 0){
		//create / amend  table member
		if($new == 1){$hashed_password = sha1($_POST['PASSWORD']);}
		if($new == 0){
			if($_POST['PASSWORD'] != $_POST['PASSWORD_ORIGINAL']){
				//password has been changed
				$hashed_password = sha1($_POST['PASSWORD']);
			}else{
				//password is unchanged so it's already in encrypted format
				$hashed_password = $_POST['PASSWORD'];
			}
		}
		if(isset($_POST['MEMBER_CONFIRMED']) and $_POST['MEMBER_CONFIRMED'] == "1"){$member_confirmed = "Y";}else{$member_confirmed = "N";}
		$fields = array("id"=>$_POST['ID'], "username"=>$_POST['USERNAME'], "password"=>sha1($_POST['PASSWORD']), "datecreated"=>date("d F l, Y, h:i:s"),
					"lastlogin"=>date("l F d, Y, h:i:s"), "logincount"=>$logincount, "category"=>$_POST['CATEGORY'],
					"title"=>$_POST['TITLE'], "firstname"=>$_POST['FIRST_NAME'], "lastname"=>$_POST['LAST_NAME'],																							
					"company"=>$_POST['COMPANY_NAME'], "address1"=>$_POST['ADDRESS1'], "address2"=>$_POST['ADDRESS2'], "town"=>$_POST['TOWN'], "county"=>$_POST['COUNTY'],
					"country"=>$_POST['COUNTRY'], "postcode"=>$_POST['POSTCODE'], "phone"=>$_POST['PHONE'], "mobile"=>$_POST['MOBILE'],
					"email"=>$_POST['EMAIL'], "confirmed"=>$member_confirmed);
		if ($new == 1){
			$rows = Create_Member($fields);
		}else{
			$rows = Rewrite_Member($fields);
		}
		$message = "";	
		if ($rows == 1){
			if ($new == 1){
				$message = "New Member successfully CREATED";
			}else{
				$message = $rows . " record successfully UPDATED";
			}
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
		//refresh details
		$username = $_POST['USERNAME']; $username_original = $username;
		$id = $_POST['ID'];
		$password = $_POST['PASSWORD']; $password_original = $password;
		$u = Get_Member($username);
		$category = $u->MB_CATEGORY;
		$title = $u->MB_TITLE;
		$first_name = $u->MB_FIRSTNAME;
		$last_name = $u->MB_LASTNAME;
		$company_name = $u->MB_COMPANY;
		$address1 = $u->MB_ADDRESS1;
		$address2 = $u->MB_ADDRESS2;
		$town = $u->MB_TOWN;
		$county = $u->MB_COUNTY;
		$country = $u->MB_COUNTRY;
		$postcode = $u->MB_POSTCODE;
		$phone = $u->MB_PHONE;
		$mobile = $u->MB_MOBILE;
		$email = $u->MB_EMAIL;
		$login_name = $username;
		$login_password = $password;
		if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
	}else{
		// refresh input details
		$category = $_POST['CATEGORY'];
		$title = $_POST['TITLE'];
		$first_name = $_POST['FIRST_NAME'];
		$last_name = $_POST['LAST_NAME'];
		$company_name = $_POST['COMPANY_NAME'];
		$address1 = $_POST['ADDRESS1'];
		$address2 = $_POST['ADDRESS2'];
		$town = $_POST['TOWN'];
		$county = $_POST['COUNTY'];
		$country = $_POST['COUNTRY'];
		$postcode = $_POST['POSTCODE'];
		$phone = $_POST['PHONE'];
		$mobile = $_POST['MOBILE'];
		$email = $_POST['EMAIL'];
		if (isset($_POST['MEMBER_CONFIRMED']) and $_POST['MEMBER_CONFIRMED'] == 1){$member_confirmed = "Y";}else{$member_confirmed = "N";}
		$username = $_POST['USERNAME_ORIGINAL']; $username_original = $username;
		$id = $_POST['ID'];
		$password = ""; $password_original = $_POST['PASSWORD_ORIGINAL'];
		$login_name = isset($_SESSION['username']) ? $_SESSION['username'] : "";
		
		/*
		$message = "";
		foreach($errors_array as $e){
			$message .= $e . "<br/>";	
		}
		$warning = "red";
		*/
	}
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Amend Categories";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Amend Members - Update Member Details</h1>
	<br/>

	<!--- SEARCHBOXES ------------------------------------------------------------------------------------------------------>
    <form name="enter-thumb" action="/_cms/amend_members.php" enctype="multipart/form-data" method="post">
    <div class="member_searchbox">
    <table align="left" border="0" cellpadding="2" cellspacing="5">
    	<tr>
          <td>Search for: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name to search for the member whose details you want to amend<br /><br />To select from all members place the mouse cursor in the search field with no other text and click search<br /><br />Then select the member from the Choose from... dropdown box</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
            <td><Input name="SEARCH_DATA" type="text" size="72" value="<?php echo isset($_POST['SEARCH_DATA']) ? $_POST['SEARCH_DATA'] : "" ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          	    <Input name="SEARCH" type="submit" value="search" class="search-button" /></td>
    	</tr>
        <tr>
        	<td></td>
            <td>
            	<select name="search_results" id="jumpMenu" onchange="MM_jumpMenu('parent',this,1)">
                	<option value="#">Choose from...</option>
                    <?php
					if (isset($_POST['SEARCH_DATA'])){
						$members = Search_member($_POST['SEARCH_DATA']);
						foreach($members as $m){
							if(isset($_POST['SELECTED_MEMBER']) and $_POST['SELECTED_MEMBER'] == $m->MB_USERNAME){
								$selected = "selected";
							}else{
								$selected = "";
							}
							echo "<option value=\"/_cms/amend_members.php?searchdata=" . $_POST['SEARCH_DATA'] . "&searchmember=" . $m->MB_USERNAME . "\"" . $selected . ">" . $m->MB_USERNAME . " - " . $m->MB_ID . "</option>";
						}
					}
					?>
           		</select> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select a member from the dropdown selector<br /><br />You need to have searched for the member first</span><span class=\"bottom\"></span></span>" : "") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="hidden" name="SELECTED_MEMBER" value="<?php echo (isset($selected_member) ? $selected_member : "") ?>">
                <input type="submit" name="LIST" value="List All Members" class="member-list-button" /> <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Click here to go to the List All Members page</span><span class=\"bottom\"></span></span>" : "") ?></a>
            </td>
        </tr>
        <!--<tr>
			<td colspan="2">&nbsp;</td>
		</tr>-->
    <!--- END OF SEARCHBOXES ----------------------------------------------------------------------------------------------->
    </table>
    </div>
    <div class="member_errors">
		<?php
        foreach($errors_array as $e){
            echo "<label class=\"" . $warning ."\">" . $e . "</label><br/>";	
        }
        ?>
    </div>
    <div class="member_fields">
    <table align="left" border="0" cellpadding="2" cellspacing="5">
        <tr>
            <td>
               <label><span class="orange">Personal Details:</span></label><br/>
            </td>
        </tr>
    	<tr>
        	<td>Title:</td>
            <td>
            	<Select name="TITLE">
                	<option value="MR" <?php echo $title == "MR" ? "selected" : ""  ?>>Mr.</option>
                    <option value="MRS" <?php echo $title == "MRS" ? "selected" : ""  ?>>Mrs.</option>
                    <option value="MISS" <?php echo $title == "MISS" ? "selected" : ""  ?>>Miss</option>
                    <option value="MS" <?php echo $title == "MS" ? "selected" : ""  ?>>Ms.</option>
                </Select>
            </td>
        </tr>
        <tr>
            <td>First Name:</td>
            <td>
                <input type="text" name="FIRST_NAME" value="<?php echo $first_name ?>" size="22" />
            </td>
            <td>
            	<?php echo $ast_first == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
        </tr>
		<tr>
			<td>Last Name</td>
			<td>
				<input type="text" name="LAST_NAME" value="<?php echo $last_name ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_last == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Company Name:</td>
			<td>
				<input type="text" name="COMPANY_NAME" value="<?php echo $company_name ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_company == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Address 1:</td>
			<td>
				<input type="text" name="ADDRESS1" value="<?php echo $address1 ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_add1 == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Address 2:</td>
			<td>
				<input type="text" name="ADDRESS2" value="<?php echo $address2 ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_add2 == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Town:</td>
			<td>
				<input type="text" name="TOWN" value="<?php echo $town ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_town == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>County:</td>
			<td>
				<input type="text" name="COUNTY" value="<?php echo $county ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_county == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Country:</td>
			<td>
				<input type="text" name="COUNTRY" value="<?php echo $country ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_country == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Postcode:</td>
			<td>
				<input type="text" name="POSTCODE" value="<?php echo $postcode ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_post == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Telephone:</td>
			<td>
				<input type="text" name="PHONE" value="<?php echo $phone ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_phone == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Mobile:</td>
			<td>
				<input type="text" name="MOBILE" value="<?php echo $mobile ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_mobile == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
	    </tr>
		<tr>
			<td>email:</td>
			<td>
				<input type="text" name="EMAIL" value="<?php echo $email ?>" size="22" /><br/><br/>
			</td>
            <td>
            	<?php echo $ast_email == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
        <tr>
			<td>
            	<label><span class="orange">Price Category:</span></label>
            </td>
			<td>
				<input type="text" name="CATEGORY" value="<?php echo $category ?>" size="22" maxlength="2" onKeyUp="this.value = this.value.toUpperCase();" />
			</td>
            <td>
            	<?php echo $ast_category == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
        <tr>
			<td>&nbsp;</td>
		</tr>
        <tr>
			<td>Confirmed:</td>
            <?php if($member_confirmed == "Y"){$checked = "checked";}else{$checked = "";}?>
            <td><input type="checkbox" name="MEMBER_CONFIRMED" value="1" <?php echo $checked ?> ></td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>
				<label><span class="orange">Login Details:</span></label><br/>
			</td>
		</tr>
		<tr>
			<td>User Name:</td>
			<td>
				<input type="text" name="USERNAME" value="<?php echo $username ?>" size="22" />
                <input type="hidden" name="USERNAME_ORIGINAL" value="<?php echo $username_original ?>" size="" />
                <input type="hidden" name="ID" value="<?php echo $id ?>" size="" />
			</td>
            <td>
            	<?php echo $ast_user == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>
		<tr>
			<td>Password:</td>
			<td>
				<input type="text" name="PASSWORD" value="<?php echo $password ?>" size="22" />
                <input type="hidden" name="PASSWORD_ORIGINAL" value="<?php echo $password_original ?>" size="22" />
			</td>
            <td>
            	<?php echo $ast_pass == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>   
     	<tr>
        	<td>Confirm Password:</td>
        	<td>
        		<input type="text" name="PASSWORD_TEST" value="<?php echo $password_test ?>" size="22" /><br/><br/>
        	</td>
            <td>
            	<?php echo $ast_passconf == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
        </tr>
        
		<!--- UPDATE BUTTON ---------->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
            	<input name="CREATE" type="submit" value="Create" class="member-create-button">
            	<input name="UPDATE" type="submit" value="Update" class="member-update-button">
            	<input name="DELETE" type="submit" value="Delete" class="member-delete-button">
<!--                <input name="CLEAR" type="submit" value="Clear">
-->            </td>
		</tr>
        <tr>
			<td colspan="2">&nbsp;</td>
		</tr>
        <tr>
			<td colspan="2"><label class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
		</tr>
        <!---
        <tr>
            <td>Programmer Message:</td>
            <td><input type="text" name="PROGRAMMER_MESSAGE" SIZE="87" value="<?php echo $message ?>"></td>
        </tr>
        --->
   	</table> 
    </div>   
	</form>

<?php
  include_once("includes/footer_admin.php");
?>

