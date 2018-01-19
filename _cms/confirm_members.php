<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$preferences = getPreferences();
$email_setup = getEmailSetup();

//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Confirm Members";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

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
$member_confirmed = "N"; $justconfirmed = 0;
$ast_first = 0; $ast_last = 0; $ast_company = 0; $ast_add1 = 0; $ast_add2 = 0; $ast_town = 0; $ast_county = 0; $ast_country = 0; $ast_post = 0; $ast_phone = 0;
$ast_mobile = 0; $ast_email = 0; $ast_category = 0;
$ast_user = 0; $ast_pass = 0; $ast_passconf = 0;

if(isset($_GET['email']) and $_GET['email'] == "fail"){
	$message = "EMAIL CONFIRMATION FAILURE!!!";
	$warning = "red";
	$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";
}

if (isset($_POST['DELETE'])) {
	if($_POST['JUST_CONFIRMED'] == 0){
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
		$address1 = ""; $address2 = ""; $town = ""; $county = ""; $country = ""; $postcode = ""; $phone = ""; $mobile = ""; $email = "";
		$member_confirmed = "N";
		$ast_first = 0; $ast_last = 0; $ast_company = 0; $ast_add1 = 0; $ast_add2 = 0; $ast_town = 0; $ast_county = 0; $ast_country = 0; $ast_post = 0; $ast_phone = 0;
		$ast_mobile = 0; $ast_email = 0;
		$ast_user = 0; $ast_pass = 0; $ast_passconf = 0;
		if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
	}else{
		// refresh input details
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
		$member_confirmed = $_POST['MEMBER_CONFIRMED'];
		$username = $_POST['USERNAME_ORIGINAL']; $username_original = $username;
		$id = $_POST['ID'];
		$password = ""; $password_original = $_POST['PASSWORD_ORIGINAL'];
		$login_name = isset($_SESSION['username']) ? $_SESSION['username'] : "";
		$message = "Member now confirmed - Please choose another";
		$warning = "red";
		$just_confirmed = 1;
		$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";
	}
}

if (isset($_GET['searchmember'])) {
	//NEW MEMBER SELECTED from search dropdown so get member deatils for display 
	$u = Get_Member($_GET['searchmember']);
	$id = $u->MB_ID;
	$selected_member = $u->MB_USERNAME;
	$id = $u->MB_ID;
	$username = $u->MB_USERNAME; $username_original = $username;
	$password = ""; $password_original = $u->MB_PASSWORD; //encrypted
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
	$category = $u->MB_CATEGORY;
	$member_confirmed = $u->MB_CONFIRMED;
	
	$_POST['SEARCH'] = "search";
	$_POST['SEARCH_DATA'] = $_GET['searchdata'];
	$_POST['SELECTED_MEMBER'] = $_GET['searchmember'];
	$just_confirmed = 0;
}


if (isset($_POST['UPDATE']) or isset($_POST['CONFIRM'])) {
	if($_POST['JUST_CONFIRMED'] == 0){
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
			if ($_POST['USERNAME'] !== $_POST['USERNAME_ORIGINAL']){
				$errors_array[] = "Cannot amend User Name";
				$warning = "red";
				$errors = 1;
				$ast_user = 1;
			}
		}	
		if (isset($_POST['CONFIRM']) and $_POST['PASSWORD'] == ""){
			$errors_array[] = "Please enter a Password";
			$warning = "red";
			$ast_pass = 1;
			$errors = 1;
		}
		if (isset($_POST['CONFIRM']) and $_POST['PASSWORD'] != $_POST['PASSWORD_TEST']){
			$errors_array[] = "Password and confirmation do not match";
			$warning = "red";
			$errors = 1;
			$ast_pass = 1; $ast_passconf = 1;
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
	
			if (isset($_POST['UPDATE'])){
				$fields = array("id"=>$_POST['ID'], "username"=>$_POST['USERNAME'], "password"=>sha1($_POST['PASSWORD']), "datecreated"=>date("d F l, Y, h:i:s"),
				"lastlogin"=>date("l F d, Y, h:i:s"), "logincount"=>$logincount, "category"=>$_POST['CATEGORY'], "title"=>$_POST['TITLE'], "firstname"=>$_POST['FIRST_NAME'], "lastname"=>$_POST['LAST_NAME'],																							
				"company"=>$_POST['COMPANY_NAME'], "address1"=>$_POST['ADDRESS1'], "address2"=>$_POST['ADDRESS2'], "town"=>$_POST['TOWN'], "county"=>$_POST['COUNTY'],
				"country"=>$_POST['COUNTRY'], "postcode"=>$_POST['POSTCODE'], "phone"=>$_POST['PHONE'], "mobile"=>$_POST['MOBILE'],
				"email"=>$_POST['EMAIL'], "confirmed"=>$member_confirmed);
				$rows = Rewrite_Member($fields);
			}else{
				$fields = array("id"=>$_POST['ID'], "category"=>$_POST['CATEGORY'], "username"=>$_POST['USERNAME'], "password"=>sha1($_POST['PASSWORD']), "confirmed"=>"Y");
				$rows = Accept_Member($fields);
			}
			$message = "";	
			if ($rows == 1){
				if (isset($_POST['UPDATE'])){
					$message = "Member successfully UPDATED";
				}else{
					//send confirmational email
					$email_it_to = $_POST['EMAIL'];
					$email_it_to_cc = $email_setup->EM_CONF_CC;
					$email_it_to_bcc = $email_setup->EM_CONF_BCC;
					$email_it_from = $preferences->PREF_EMAIL;
					$email_subject = $email_setup->EM_CONF_SUBJECT;
					$email_confirmation = $email_setup->EM_CONF_HEADER;
					$email_confirmation .= $email_setup->EM_CONF_CONTENT;
					$email_confirmation .= "Name: " . $_POST['USERNAME'] . "    Password: " . $_POST['PASSWORD'] . "\r\n\r\n";
					$email_confirmation .= $email_setup->EM_CONF_FOOTER;
					$failure_page = "confirm_members.php?email=fail";
					
					include_once("../mailer/_process_from_cms.php");
					$message = "User " . $_POST['USERNAME'] . " now CONFIRMED as a member";
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
			if(isset($_POST['UPDATE'])){
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
				$member_confirmed = $u->MB_CONFIRMED;
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
				$member_confirmed = $_POST['MEMBER_CONFIRMED'];
				$username = $_POST['USERNAME_ORIGINAL']; $username_original = $username;
				$id = $_POST['ID'];
				$password = ""; $password_original = $_POST['PASSWORD_ORIGINAL'];
				$login_name = isset($_SESSION['username']) ? $_SESSION['username'] : "";
				if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
				$just_confirmed = 1;
			}
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
			$member_confirmed = $_POST['MEMBER_CONFIRMED'];
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
	}else{
		$message = "Member now confirmed - Please choose another";
		$warning = "red";
		$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";
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
		$member_confirmed = $_POST['MEMBER_CONFIRMED'];
		$username = $_POST['USERNAME_ORIGINAL']; $username_original = $username;
		$id = $_POST['ID'];
		$password = ""; $password_original = $_POST['PASSWORD_ORIGINAL'];
		$login_name = isset($_SESSION['username']) ? $_SESSION['username'] : "";
		$just_confirmed = 1;
	}
}

include_once("includes/header_admin.php");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
    <br/>
	<h1>Confirm New Members</h1>
    <p><br /><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">When a trade or discount club member applies you receive an e-mail from the system to prompt you to visit this page and confirm the membership<br /><br />Until you confirm the new member they cannot log in and buy goods at the discounted prices you've set up<br /><br />
	To confirm a member select the name from the Members awaiting confirmation dropdown selector, this will fill all the details that the applicant has entered. Next, give the applicant a password then click the Confirm New Member button<br /><br />An automated e-mail will be sent to the applicant to let them know they've been accepted (set these up in the Setup Emails page from the top menu)<br /><br />If the applicant's details need to be corrected you can amend them and then click the Update Member Details button before you confirm acceptance<br /><br />Delete an unconfirmed applicant using the Delete Member button</span><span class=\"bottom\"></span></span>" : "") ?></a></p>
	<br/>

	<!--- SEARCHBOXES ------------------------------------------------------------------------------------------------------>
    <form name="enter-thumb" action="/_cms/confirm_members.php" enctype="multipart/form-data" method="post">
    <div class="member_searchbox">
    <table align="left" border="0" cellpadding="2" cellspacing="5">
        <tr>
        	<td>Members awaiting confirmation:</td>
            <td>
            	<select name="search_results" id="jumpMenu" onchange="MM_jumpMenu('parent',this,1)">
                	<option value="#">Choose from...</option>
                    <?php
						//list members who are NOT yet confirmed
						$members = Get_All_Members("N");
						foreach($members as $m){
							if(isset($_POST['SELECTED_MEMBER']) and $_POST['SELECTED_MEMBER'] == $m->MB_USERNAME){
								$selected = "selected";
							}else{
								$selected = "";
							}
							echo "<option value=\"/_cms/confirm_members.php?searchdata=" . $_POST['SEARCH_DATA'] . "&searchmember=" . $m->MB_USERNAME . "\"" . $selected . ">" . $m->MB_USERNAME . " - " . $m->MB_ID . "</option>";
						}
					?>
           		</select>
				<input type="hidden" name="SELECTED_MEMBER" value="<?php echo (isset($selected_member) ? $selected_member : "") ?>">
            <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? " <img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Select an applicant from the dropdown selector</span><span class=\"bottom\"></span></span>" : "") ?></a>
            </td>
        </tr>
        <!---<tr>
			<td colspan="2">&nbsp;</td>
		</tr>--->
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
                <input type="hidden" name="MEMBER_CONFIRMED" value="<?php echo $member_confirmed ?>" size="" />
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
			<td>Password: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Create a password for the trade/club member<br /><br />This can be anything you choose so that you retain control over access to the reduced prices<br /><br />A good way of inventing passwords is to use the member's name or a word from a related address or business and use numbers and special characters (slashes, stars etc) instead of some of the letters; eg Simon Allen = 51M0n-4//3n, Macdonald = M4cd0n4/d*<br /><br />To ensure the security of the system use longer passwords that contain capital letters, lower case letters and keyboard characters. Don't use punctuation characters such as &quot;, &amp; or spaces</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
			<td>
				<input type="text" name="PASSWORD" value="<?php echo $password ?>" size="22" />
                <input type="hidden" name="PASSWORD_ORIGINAL" value="<?php echo $password_original ?>" size="" />
			</td>
            <td>
            	<?php echo $ast_pass == 1 ? "<span class=\"red\">*</span>" : "" ?>
            </td>
		</tr>   
     	<tr>
        	<td>Confirm Password: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Enter the password again to confirm it</span><span class=\"bottom\"></span></span>" : "") ?></a></td>
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
            	<input name="UPDATE" type="submit" value="Update Member Details" class="member-update-button">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            	<input name="DELETE" type="submit" value="Delete Member" class="member-delete-button">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="CONFIRM" type="submit" value="Confirm New Member" class="member-confirm-button">
                <input type="hidden" name="JUST_CONFIRMED" value="<?php echo $just_confirmed ?>" size="" />
            </td>
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
        	<tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
   	</table> 
    </div>   
	</form>

<?php
  include_once("includes/footer_admin.php");
?>

