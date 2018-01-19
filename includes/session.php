<?php
session_start();
if (isset($_SESSION['user_id'])){
	$login = 1;
}else{
	$login = 0;
}

function confirm_logged_in() {
	if (!isset($_SESSION['user_id'])) {
		//redirect
		header("Location: /_cms/login.php?login=0");
	}
}
function logout(){
		$_SESSION = array();
		if(isset($_COOKIE[session_name()])){
			setcookie(session_name(), '', time()-42000, '/');
		}	
		session_destroy();
}

?>