<?php
/* Check if this is a valid include */
if (!defined('IN_SCRIPT')) {die('Invalid attempt');} 

#error_reporting(E_ALL);

// Set backslash options
if (get_magic_quotes_gpc()){
	define('SF_SLASH',false);
}else{
	define('SF_SLASH',true);
}
// Define some constants for backward-compatibility
if ( ! defined('ENT_SUBSTITUTE')){
	define('ENT_SUBSTITUTE', 0);
}
if ( ! defined('ENT_XHTML')){
	define('ENT_XHTML', 0);
}

/*** FUNCTIONS ***/
function sf_htmlspecialchars($in){
	return htmlspecialchars($in, ENT_COMPAT | ENT_SUBSTITUTE | ENT_XHTML, 'UTF-8');
    #return htmlspecialchars($in, ENT_COMPAT | ENT_SUBSTITUTE | ENT_XHTML, 'ISO-8859-1');
}

function sf_process_messages($message,$redirect_to,$type='ERROR'){
	global $sf_settings;

    switch ($type){
    	case 'SUCCESS':
        	$_SESSION['SF_SUCCESS'] = TRUE;
            break;
        case 'NOTICE':
        	$_SESSION['SF_NOTICE'] = TRUE;
            break;
        default:
        	$_SESSION['SF_ERROR'] = TRUE;
    }

	$_SESSION['SF_MESSAGE'] = $message;

    /* In some cases we don't want a redirect */
    if ($redirect_to == 'NOREDIRECT'){
    	return TRUE;
    }

	header('Location: '.$redirect_to);
	exit();
}

function sf_token_hash() {
	return sha1(time() . microtime() . uniqid(rand(), TRUE) );
}

function sf_input($in,$error=0,$redirect_to='',$force_slashes=0){
	if (is_array($in)){
    	$in = array_map('sf_input',$in);
        return $in;
    }

    $in = trim($in);

    if (strlen($in)) {
        $in = sf_htmlspecialchars($in);
        $in = preg_replace('/&amp;(\#[0-9]+;)/','&$1',$in);
    }elseif ($error){
    	if ($redirect_to == 'NOREDIRECT'){
        	sf_process_messages($error,'NOREDIRECT');
        }elseif ($redirect_to){
        	sf_process_messages($error,$redirect_to);
        }else{
        	sf_error($error);
        }
    }

    if (SF_SLASH || $force_slashes){
		$in = addslashes($in);
    }

    return $in;
}

function sf_session_start(){
    session_name('SF' . sha1(dirname(__FILE__) . '$r^k*Zkq|w1(G@!-D?3%') );

    if (session_start()){
    	if ( ! isset($_SESSION['token']) ){
        	$_SESSION['token'] = sf_token_hash();
        }
        header ('P3P: CP="CAO DSP COR CURa ADMa DEVa OUR IND PHY ONL UNI COM NAV INT DEM PRE"');
        return true;
    }else{
        global $sf_settings;
        sf_error("Unable to start Session - Please contact 1-Ecommerce");
    }
}

function sf_session_stop(){
    session_unset();
    session_destroy();
    return true;
}

function sf_error($error,$showback=1) {
	global $sf_settings;
	
	require_once(SF_PATH . 'inc/header.inc.php');
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="3"><img src="<?php echo SF_PATH; ?>img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
            <td class="headersm"><?php echo $sf_settings['sf_title']; ?></td>
        	<td width="3"><img src="<?php echo SF_PATH; ?>img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
        </tr>
    </table>
	
	</td>
	</tr>
	<tr>
	<td>
	<p>&nbsp;</p>
	
		<div class="error">
			<img src="<?php echo SF_PATH; ?>img/error.png" width="16" height="16" border="0" alt="" style="vertical-align:text-bottom" />
			<b>Error Code 2 - Please contact 1-Ecommerce:</b><br /><br />
			<?php
			echo $error;
			?>
		</div>
		<br />
	
	<p>&nbsp;</p>
	
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	
	<?php
	require_once(SF_PATH . 'inc/footer.inc.php');
	exit();
}
