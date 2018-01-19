
	<?php
	// connect to the database
	$db = '1ecom';
	$host = 'localhost';
	$user = 'root';
	$password = '';
	
	$dbConn = mysql_connect($host,$user,$password) or die('Failed to connect to database');
	$result = mysql_select_db($db, $dbConn) or die('Failure selecting database');
	?>
	