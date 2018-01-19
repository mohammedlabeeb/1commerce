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
		echo "<script type=\"text/javascript\">document.location.href=\"/home\";</script>";
	}else{
		$warning = "red";
		$message_login = "Login failed - Please try again";
	}
}

include_once("includes/header_login.php");
?>
<div class="body-indexcontent_login">
	<div class="login">
	<h1>ADMINISTRATION LOGIN</h1>
  	<div id="owner_reauth" class="container_admin">
    	<div class="inner smallpanel">
      		<form id="login" name="login" action="/_cms/login.php" method="post" >
        		<fieldset class="upper">
          			<img src="/_cms/_assets/images/user.png" class="account_login_icon">
          			<div id="reauthorizeInner">
            			<h2>CMS - Authorisation required</h2>
						<?php 
                        if($message_login != ""){
                            echo "<p><span class=\"message_error\">" . $message_login . "</span></p>";
                        }else{
                            echo "<p class=\"message\">Please enter your username &amp; password</p>";
                        }
                        ?>
                        <div class="row password public">
                            <input id="reauthuser" name="username" type="text" value="username" onFocus="this.value=''" required="yes" message="You must enter a username">
                        </div>
                        <div class="row password public">
                            <input id="reauthPassword" name="password" type="password" value="password" onFocus="this.value=''" required="yes" message="You must enter a password">
                        </div>
                        <p id="error" class="error" style="display: none;">Incorrect password.</p>
          			</div>
					<div id="footerButtons" class="row buttons continue public">
            			<a href="#" id="submit" class="login-button" onclick="document['login'].submit();" role="button"><strong>Login</strong></a><img src="/_cms/_assets/images/blank.gif" id="reauthSpinner" class="spinner-16-fff" style="display: none;"> 
    	    		</div>
    	  		</fieldset>

      		</form>
      		<div class="bottom">
            	<div class="security_message">
                	<?php echo "For security reasons, your IP address (<span style=\"color:##FFF\">" . $ip . "</span>) has been logged." ?>
            	</div>
    		</div>
    	</div>
	</div>
    </div>
<?php
  include_once("includes/footer_admin.php");
?>