<?php
/* Check if this is a valid include */
if (!defined('IN_SCRIPT')) {die('Invalid attempt');} 

function sf_dbSetNames(){
	global $sf_settings, $sf_db_link;

    if ($sf_settings['db_vrsn']){
		mysql_set_charset('utf8', $sf_db_link);
    }else{
    	sf_dbQuery("SET NAMES 'utf8'");
    }

}

function sf_dbConnect(){
	global $sf_settings;
	global $sf_db_link;

    if ( ! function_exists('mysql_connect') ){
    	die("Function mysql_connect does not exist - Please contact 1-Ecommerce");
    }

    $sf_db_link = @mysql_connect($sf_settings['db_host'], $sf_settings['db_user'], $sf_settings['db_pass']);

    if ( ! $sf_db_link){
    	if ($sf_settings['debug_mode']){
			sf_error("Cannot connect to Database</p><p>mysql said:<br />".mysql_error()."</p>");
        }else{
			sf_error("Cannot connect to Database</p><p>Please contact 1-Ecommerce <a href=\"mailto:simon@shopfitter.com\">simon@shopfitter.com</a></p>");
        }
    }

    if ( ! @mysql_select_db($sf_settings['db_name'], $sf_db_link)){
    	if ($sf_settings['debug_mode']){
			sf_error("Cannot select Database</p><p>mysql_said:<br />".mysql_error()."</p>");
        }else{
			sf_error("Cannot select Database</p><p>Please contact 1-Ecommerce <a href=\"mailto:simon@shopfitter.com\">simon@shopfitter.com</a></p>");
        }
    }
    /* Check MySQL/PHP version and set encoding to utf8 */
    sf_dbSetNames();

    return $sf_db_link;
}


function sf_dbClose(){
	global $sf_db_link;
	
    return @mysql_close($sf_db_link);
}


function sf_dbQuery($query){
    global $sf_last_query;
    global $sf_db_link;
    global $sf_settings;

    if (!$sf_db_link && !sf_dbConnect()){
        return false;
    }

    $sf_last_query = $query;


    if ($res = @mysql_query($query, $sf_db_link)){
    	return $res;
    }elseif ($sf_settings['debug_mode']){
	    sf_error("Error in sql statement: $query</p><p>mysql_said:<br />".mysql_error()."</p>");
    }else{
	    sf_error("Error in sql statement</p><p>Please contact 1-Ecommerce <a href=\"mailto:simon@shopfitter.com\">simon@shopfitter.com</a></p>");
    }
}

