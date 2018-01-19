<?php
require_once("includes/session.php");
include_once("includes/masterinclude.php");

//$name = $_GET['page'];
$information = getInformationPage("Member");

$pageTitle = html_entity_decode($information->IN_TITLE);
$pageMetaDescription = html_entity_decode($information->IN_META_DESC);
$pageMetaKeywords = html_entity_decode($information->IN_META_KEYWORDS);
$pageCustomHead = html_entity_decode($information->IN_CUSTOM_HEAD, ENT_QUOTES);
$index = true;
$new = 1;
$message = "";
$message_login = "";
$logincount = 1;
$errors = 0;
$errors_array = array();
$title = "MR"; $first_name = ""; $last_name = ""; $company_name = ""; $address1 = ""; $address2 = ""; $town = ""; $county = ""; $country= ""; $postcode = ""; $phone = ""; $mobile = "";
$email = ""; $username = ""; $username_original = ""; $password = ""; $password_original = ""; $password_test = ""; $login_name = ""; $login_password = "";
$ast_first = 0; $ast_last = 0; $ast_company = 0; $ast_add1 = 0; $ast_add2 = 0; $ast_town = 0; $ast_county = 0; $ast_country = 0; $ast_post = 0; $ast_phone = 0;
$ast_mobile = 0; $ast_email = 0;
$ast_user = 0; $ast_pass = 0; $ast_passconf = 0;
$top_level="0"; $infopagename=$information->IN_NAME;

$preferences = getPreferences();
$email_setup = getEmailSetup();
$ip=$_SERVER['REMOTE_ADDR'];
$message_login = "";

if (isset($_POST['GO_HOME'])){
	echo "<script type=\"text/javascript\">document.location.href=\"index.php?login=1\";</script>";
}

if (isset($_POST['LOGIN_NAME']) and isset($_POST['LOGIN_PASSWORD'])){
	$username = $_POST['LOGIN_NAME']; $username_original = $username;
	$password = $_POST['LOGIN_PASSWORD'];
	$hashed_password = sha1($password);
	$u = Confirm_Member($username, $hashed_password);
	if ($u == 1){
		$u = Get_Member($username);
		$_SESSION['user_id'] = $u->MB_ID;
		$_SESSION['username'] = $username;
		//refresh screen with member details
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
		$password_original = $password;
		$message_login = "Login Successful";
		$warning = "green";
		$login = 1;
	}else{
		$login_name = $_POST['LOGIN_NAME']; $login_password = $_POST['LOGIN_PASSWORD'];
		$warning = "red";
		$message_login = "Login failed - Please try again";
	}
}

if (isset($_POST['SUBMIT_REGISTER']) or isset($_POST['UPDATE_DETAILS'])){
	if(isset($_POST['SUBMIT_REGISTER'])){$new = 1;}else{$new = 0;}
	//validation routines
	$errors_array = array();
	if(isset($_POST['SUBMIT_REGISTER']) and isset($_SESSION['user_id'])){
		$errors_array[] = "Only new members may register";
		$errors = 1;
		$warning = "red";
	}
	if(isset($_POST['UPDATE_DETAILS']) and !isset($_SESSION['user_id'])){
		$errors_array[] = "Only existing members may update details";
		$errors = 1;
		$warning = "red";
	}
	if ($_POST['FIRST_NAME'] == ""){
		$errors_array[] = "Please enter your first name";
		$warning = "red";
		$errors = 1;
		$ast_first = 1;
	}
	if ($_POST['LAST_NAME'] == ""){
		$errors_array[] = "Please enter your last name";
		$warning = "red";
		$errors = 1;
		$ast_last = 1;
	}
	if ($_POST['ADDRESS1'] == ""){
		$errors_array[] = "Please enter an address";
		$warning = "red";
		$errors = 1;
		$ast_add1 = 1;
	}
	if ($_POST['TOWN'] == ""){
		$errors_array[] = "Please enter a Town";
		$warning = "red";
		$errors = 1;
		$ast_town = 1;
	}
	if ($_POST['COUNTRY'] == ""){
		$errors_array[] = "Please enter a Country";
		$warning = "red";
		$errors = 1;
		$ast_country = 1;
	}
	if ($_POST['POSTCODE'] == ""){
		$errors_array[] = "Please enter a Postcode";
		$warning = "red";
		$errors = 1;
		$ast_post = 1;
	}
	if ($_POST['EMAIL'] == ""){
		$errors_array[] = "Please enter a valid email address";
		$warning = "red";
		$errors = 1;
		$ast_email = 1;
	}
	if (!isset($_SESSION['username'])){
		//registration
		if ($_POST['USERNAME'] == ""){
			$errors_array[] = "Please enter a User Name";
			$warning = "red";
			$errors = 1;
			$ast_user = 1;
		}
		$m = Confirm_Member($_POST['USERNAME'],"");
		if ($new == 1 and $m != 0){
			//trying to create new member who already exists!
			$errors_array[] = "User already exists!";
			$warning = "red";
			$errors = 1;
			$ast_user = 1;
		}
	}else{
		//amending existing details
		if ($_POST['USERNAME'] !== $_POST['USERNAME_ORIGINAL']){
			$errors_array[] = "Cannot amend User Name";
			$warning = "red";
			$errors = 1;
			$ast_user = 1;
		}
	}
	if(isset($_SESSION['username'])){
		if ($_POST['PASSWORD'] == ""){
			$errors_array[] = "Please enter a Password";
			$warning = "red";
			$errors = 1;
			$ast_pass = 1;
		}
		if (!isset($_SESSION['username'])){
			//registration
			if ($_POST['PASSWORD'] != $_POST['PASSWORD_TEST']){
				$errors_array[] = "Password and confirmation do not match";
				$warning = "red";
				$errors = 1;
				$ast_pass = 1; $ast_passconf = 1;
			}
		}else{
			//amending existing details
			if ($_POST['PASSWORD'] !== $_POST['PASSWORD_ORIGINAL'] and $_POST['PASSWORD'] != $_POST['PASSWORD_TEST']){
				$errors_array[] = "Password and confirmation do not match";
				$warning = "red";
				$errors = 1;
				$ast_pass = 1; $ast_passconf = 1;
			}
		}
	}

	if($errors == 0){
		//create / amend  table member
		if (isset($_SESSION['user_id'])){
			$id = $_SESSION['user_id'];
			$password = sha1($_POST['PASSWORD']);
		}else{
			$id = "";
			$password = "";
		}
		$fields = array("id"=>$id, "username"=>$_POST['USERNAME'], "password"=>$password, "datecreated"=>date("d F l, Y, h:i:s"),
					"lastlogin"=>date("l F d, Y, h:i:s"), "logincount"=>$logincount, "title"=>$_POST['TITLE'], "firstname"=>$_POST['FIRST_NAME'], "lastname"=>$_POST['LAST_NAME'],																							
					"company"=>$_POST['COMPANY_NAME'], "address1"=>$_POST['ADDRESS1'], "address2"=>$_POST['ADDRESS2'], "town"=>$_POST['TOWN'], "county"=>$_POST['COUNTY'],
					"country"=>$_POST['COUNTRY'], "postcode"=>$_POST['POSTCODE'], "phone"=>$_POST['PHONE'], "mobile"=>$_POST['MOBILE'],
					"email"=>$_POST['EMAIL'], "confirmed"=>"N");
		if ($new == 1){
			$rows = Create_Member($fields);
		}else{
			$rows = Rewrite_Member($fields);
		}
			
		if ($rows == 1){
			if ($new == 1){
				$message = "Registration complete - Please await email confirmation";
				//now send email to shop to make them aware they need to confirm the new registration
				$email_it_to = $email_setup->EM_REG_TO;
				$email_it_to_cc = $email_setup->EM_REG_CC;
				$email_it_to_bcc = $email_setup->EM_REG_BCC;
				$email_it_from = $preferences->PREF_EMAIL;
				$email_subject = $email_setup->EM_REG_SUBJECT;
			    $email_confirmation = $email_setup->EM_REG_HEADER;
				$email_confirmation .= $email_setup->EM_REG_CONTENT;
				$email_confirmation .= "Name: " . $_POST['USERNAME'] . "\r\n\r\n";
				$email_confirmation .= $email_setup->EM_REG_FOOTER;
				$email_it_to = $preferences->PREF_EMAIL;
				
				include_once("mailer/_process_from_top_level.php");
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
		if(isset($_SESSION['username'])){$password = $_POST['PASSWORD'];}else{$password = "";} 
		$password_original = $password;
		$hashed_password = sha1($password);
		$u = Get_Member($username);
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
		$login_name = isset($_SESSION['username']) ? $_SESSION['username'] : "";
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
		if ($new == 1){$username = $_POST['USERNAME'];}else{$username = $_POST['USERNAME_ORIGINAL'];}
		$username_original = $username;
		if(isset($_SESSION['username'])){
			$password = $_POST['PASSWORD_ORIGINAL'];
			$password_original = $password;
		}
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
include_once("includes/header.php");

?>

<div class="body-content-info">

	<h1><span class="">Member Registration / Login Procedure</span></h1>
       	<div name="enter-details" class="member_registration">
        	<h2>New Member Registration</h2>
            <?php
				foreach($errors_array as $e){
					echo "<label class=\"" . $warning ."\">" . $e . "</label><br/>";	
				}
			?>
        	<table align="left" border="0" cellpadding="2" cellspacing="5">
            	<form name="enter-details" action="/login_member.php" method="post">
                	<tr>
                      	<td colspan="3">
                         	<label>Personal Details:</label><br/>
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
						<td></td>
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
                        <td>Last Name:</td>
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
                            <input type="text" name="EMAIL" value="<?php echo $email ?>" size="22" />
                        </td>
                        <td>
                        	<?php echo $ast_email == 1 ? "<span class=\"red\">*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                      	<td colspan="3">
                         	<label>Login Details:</label><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>User Name:</td>
                        <td>
                            <input type="text" name="USERNAME" value="<?php echo $username ?>" size="22" />
                            <input type="hidden" name="USERNAME_ORIGINAL" value="<?php echo $username_original ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_user == 1 ? "<span class=\"red\">*</span>" : "" ?>
                        </td>
                    </tr>
                    <?php
					if(isset($_SESSION['username'])){
						echo "<tr>";
							echo "<td>Password:</td>";
							echo "<td>";
								echo "<input type=\"PASSWORD\" name=\"PASSWORD\" value=\"" . $password . "\" size=\"22\" />";
								echo "<input type=\"hidden\" name=\"PASSWORD_ORIGINAL\" value=\"" . $password_original . "\" size=\"\" />";
							echo "</td>";
							echo "<td>";
								echo $ast_pass == 1 ? "<span class=\"red\">*</span>" : "";
							echo "</td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td>Confirm Password:</td>";
							echo "<td>";
								echo "<input type=\"PASSWORD\" name=\"PASSWORD_TEST\" value=\"" . $password_test . "\" size=\"22\" /><br/><br/>";
							echo "</td>";
							echo "<td>";
								echo $ast_passconf == 1 ? "<span class=\"red\">*</span>" : "";
							echo "</td>";
						echo "</tr>";
                    }
                    ?>
    				<tr>
                    	<td></td>
                        <td>
                           	<input class="small-button" name="SUBMIT_REGISTER" type="submit" value="Register"/>&nbsp;&nbsp;
                            <input class="small-button" name="UPDATE_DETAILS" type="submit" value="Update"/><br/><br/>
                        </td>
                    	<td></td>
                    </tr>
                    <tr>
                       <td colspan = "3">
                           	<label class="<?php echo $warning ?>"><?php echo $message ?></label><br/><br/>
                       </td>
                    </tr>
                </form>
            </table>
        </div>
        <div class="member_login">
			<div>
				<h2>Existing Members Login</h2>
                <p><strong>Already a member?</strong><br/>
                If you are already registered with us, please log-in here </p>
                <div name="enter-login" id="login">
                	<table align="left" border="0" cellpadding="2" cellspacing="5">
                		<form name="enter-login" action="/login_member.php" method="post">
                        	<tr>
                        		<td>Name:</td>
                            	<td>
                                	<input name="LOGIN_NAME" type="text" size="17" value="<?php echo $login_name ?>"/>
                            	</td>
                            </tr>
                            <tr>
                            	<td>Password:</td>
                                <td>
                                	<input name="LOGIN_PASSWORD" type="password" size="17" value="<?php echo $login_password ?>"/>
                                </td>
                            </tr>
                            <tr>
                            	<td></td>
                                <td>
                            		<input class="small-button" name="SUBMIT_LOGIN" type="submit" value="Login"/>&nbsp;&nbsp;
                                    <input class="small-button" name="GO_HOME" type="submit" value="Home"/>
                                </td>
                            </tr>
							<tr>
                            	<td></td>
                                <td>
                                	<label class="<?php echo $warning ?>"><?php echo $message_login ?></label>
                                </td>
                            </tr>
                  		</form>
                	</table>
                </div>
			</div>
            <div class="member-info">
            <?php              
                //$name = $_GET['page'];
                $information = getInformationPage("Member");
                echo html_entity_decode($information->IN_DATA, ENT_QUOTES);       
        	?> 
            </div>
        </div>

<?php
  include_once("includes/footer.php");
?>
    
