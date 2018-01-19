<?php

function ExecuteSql($sql) {
	$results = mysql_query($sql);
	return mysql_affected_rows();
}

function FetchSqlAsScalar($sql) {
	$results = mysql_query($sql);
	$row = mysql_fetch_array($results);
	return $row[0];
}

function FetchSqlAsObject($sql) {
	$results = mysql_query($sql);
	return mysql_fetch_object($results);
}

function FetchSqlAsObjectArray($sql) {
	$rows = array();
	$results = mysql_query($sql);
	while($row = mysql_fetch_object($results))
		$rows[] = $row;
	return $rows;
}

?>