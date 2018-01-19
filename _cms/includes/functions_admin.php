<?php 

//---- USERS -------------------------------------------------------------------------------------------------------------------------------------
function Confirm_User($username, $hashed_password){
	$sql = "SELECT user_id FROM users";
	$sql .= " WHERE user_name = '" . $username . "' AND user_password = '" . $hashed_password . "'";
	$results = mysql_query($sql);
	
	return mysql_num_rows($results);
}

function Get_User($username, $hashed_password){
	$sql = "SELECT * FROM users";
	$sql .= " WHERE user_name = '" . $username . "' AND user_password = '" . $hashed_password . "'";
	
	return FetchSqlAsObject($sql);
}

//---- USEFUL FUNCTIONS --------------------------------------------------------------------------------------------------------------
function stripVAT($incVAT) {
	
	$exVAT = round($incVAT / 1.175, 2);
	//if the last digit is a zero the round function will not include it so add zeroes to make up the required 2dp
	if (strpos($exVAT, ".")) {
		$noDigits = (strlen($exVAT)) - (strpos($exVAT, ".") + 1);
	} else {
		$noDigits = 0;
	}
	if ($noDigits == 0) { $exVAT = $exVAT . ".00" ;}
	if ($noDigits == 1) { $exVAT = $exVAT . "0" ;}
	
	return $exVAT;
}

?>

