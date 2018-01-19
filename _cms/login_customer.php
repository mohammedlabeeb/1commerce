<?php
require_once("includes/session.php");
if (isset($_GET['logout']) and $_GET['logout'] == 1){
	logout();
}
//doublecheck status
if (isset($_SESSION['user_id'])){
	$login = 1; $login_message = "Logged In";
}else{
	$login = 0; $login_message = "Logged Out";
}
	
include_once("../includes/masterinclude.php");
//require_once("includes/functions_admin.php");

$pageTitle = "";
$keywords = "";
$description = "";
$index = true;
$new = 1;
$message = "";
$message_login = "";
$logincount = 1;
$errors = 0;
$errors_array = array();
$ast_first = 0; $ast_last = 0; $ast_company = 0; $ast_add1 = 0; $ast_add2 = 0; $ast_town = 0; $ast_county = 0; $ast_country = 0; $ast_post = 0; $ast_phone = 0;
$ast_mobile = 0; $ast_email = 0;
$ast_user = 0; $ast_pass = 0; $ast_passconf = 0;



$preferences = getPreferences();
$ip=$_SERVER['REMOTE_ADDR'];
$message_login = "";

if (isset($_POST['username']) and isset($_POST['password'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$hashed_password = sha1($password);
	$u = Confirm_User($username, $hashed_password);
	if ($u == 1){
		$u = Get_User($username, $hashed_password);
		$_SESSION['user_id'] = $u->user_id;
		$_SESSION['username'] = $u->user_name;
		echo "<script type=\"text/javascript\">document.location.href=\"/preferences\";</script>";
	}else{
		$warning = "red";
		$message_login = "Login failed - Please try again";
	}
}

include_once("includes/header_login.php");
?>
<div class="body-indexcontent_login">

		<h1><span class="">Member Registration / Login Procedure</span></h1>
       	<div name="enter-details" class="member_registration">
        	<h2><span class="orange">New Member Registration</span></h2>
        	<table align="left" border="0" cellpadding="2" cellspacing="5">
            	<form name="enter-details" action="/_cms/login_customer.php" method="post">
                	<tr>
                      	<td>
                         	<label><span class="orange">Personal Details:</span></label><br/>
                        </td>
                    </tr>
                	<tr>
                    	<td>Title:</td>
                        <td>
                        	<Select name="TITLE">
                            	<option value="MR">Mr.</option>
                                <option value="MISS">Mrs.</option>
                                <option value="MRS">Miss</option>
                                <option value="DR">Ms.</option>
                            </Select>
                        </td>
                    </tr>
                 	<tr>
                        <td>First Name:</td>
                        <td>
                            <input type="text" name="FIRST_NAME" value="<?php echo isset($_POST['FIRST_NAME']) ? $_POST['FIRST_NAME'] : "" ?>" />
                        </td>
                        <td>
                        	<?php echo $ast_first == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Last Name</td>
                        <td>
                            <input type="text" name="LAST_NAME" value="<?php echo isset($_POST['LAST_NAME']) ? $_POST['LAST_NAME'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_last == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Company Name:</td>
                        <td>
                            <input type="text" name="COMPANY_NAME" value="<?php echo isset($_POST['COMPANY_NAME']) ? $_POST['COMPANY_NAME'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_company == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Address 1:</td>
                        <td>
                           <input type="text" name="ADDRESS1" value="<?php echo isset($_POST['ADDRESS1']) ? $_POST['ADDRESS1'] : "" ?>" size="" />
                        </td>
                        	<?php echo $ast_add1 == 1 ? "<span>*</span>" : "" ?>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>Address 2:</td>
                        <td>
                            <input type="text" name="ADDRESS2" value="<?php echo isset($_POST['ADDRESS2']) ? $_POST['ADDRESS2'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_add2 == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Town:</td>
                        <td>
                            <input type="text" name="TOWN" value="<?php echo isset($_POST['TOWN']) ? $_POST['TOWN'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_town == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>County:</td>
                        <td>
                            <input type="text" name="COUNTY" value="<?php echo isset($_POST['COUNTY']) ? $_POST['COUNTY'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_county == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                       	<td>Country:</td>
                        <td>
                            <input type="text" name="COUNTRY" value="<?php echo isset($_POST['COUNTRY']) ? $_POST['COUNTRY'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_country == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Postcode</td>
                        <td>
                            <input type="text" name="POSTCODE" value="<?php echo isset($_POST['POSTCODE']) ? $_POST['POSTCODE'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_post == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Telephone:</td>
                        <td>
                            <input type="text" name="PHONE" value="<?php echo isset($_POST['PHONE']) ? $_POST['PHONE'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_phone == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Mobile:</td>
                        <td>
                            <input type="text" name="MOBILE" value="<?php echo isset($_POST['MOBILE']) ? $_POST['MOBILE'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_mobile == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>email:</td>
                        <td>
                            <input type="text" name="EMAIL" value="<?php echo isset($_POST['EMAIL']) ? $_POST['EMAIL'] : "" ?>" size="" /><br/><br/>
                        </td>
                        <td>
                        	<?php echo $ast_email == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                      	<td>
                         	<label><span class="orange">Login Details:</span></label><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>User Name:</td>
                        <td>
                            <input type="text" name="USERNAME" value="<?php echo isset($_POST['USERNAME']) ? $_POST['USERNAME'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_user == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
					<tr>
                        <td>Password:</td>
                        <td>
                            <input type="PASSWORD" name="PASSWORD" value="<?php echo isset($_POST['PASSWORD']) ? $_POST['PASSWORD'] : "" ?>" size="" />
                        </td>
                        <td>
                        	<?php echo $ast_pass == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td>
                            <input type="PASSWORD" name="PASSWORD_TEST" value="<?php echo isset($_POST['PASSWORD_TEST']) ? $_POST['PASSWORD_TEST'] : "" ?>" size="" /><br/><br/>
                        </td>
                        <td>
                        	<?php echo $ast_passconf == 1 ? "<span>*</span>" : "" ?>
                        </td>
                    </tr>
    				<tr>
                    	<td></td>
                        <td>
                           	<input class="register-submit" name="SUBMIT_REGISTER" type="submit" value="Register"/><br/><br/>
                        </td>
                    </tr>
                    <tr>
                    	<td></td>
                       <td>
                           	<label class="<?php echo $warning ?>"><?php echo $message ?></label><br/><br/>
                       </td>
                    </tr>
                </form>
            </table>
        </div>
        <div class="member_login">
			<img src="art/bar-narrow.jpg" class="bar-narrow" />
			<div>
				<h2><span class="orange">Existing Members Login</span></h2>
                <b>Already a member?</b><br/>
                <p>If you are already registered with us, please log-in here </p>
                <div name="enter-login" id="login">
                	<form name="enter-login" action="/TLR/Fires/Login/" method="post">
                    	<label for="LOGIN_NAME">Name:&nbsp;&nbsp;</label><input name="LOGIN_NAME" type="text" size="17" value=""<?php echo isset($_POST['LOGIN_NAME']) ? $_POST['LOGIN_NAME'] : "" ?>""/><br/><br/>
                        <label for="LOGIN_PASSWORD">Password:&nbsp;&nbsp;</label><input name="LOGIN_PASSWORD" type="password" size="17" value="<?php echo isset($_POST['LOGIN_PASSWORD']) ? $_POST['LOGIN_PASSWORD'] : "" ?>"/>
                        <br/><br/>
                            <input class="login-submit" name="SUBMIT_LOGIN" type="submit" value="Login"/><br/><br/>
                  	</form>
                </div>
                <span class="login-message"><label class="<?php echo $warning ?>"><?php echo $message_login ?></label></span>
			</div>
        </div>

<?php
  include_once("includes/footer_admin.php");
?>

