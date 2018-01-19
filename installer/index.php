<?php
define('IN_SCRIPT',1);
define('INSTALL',1);
define('SITE_HOME', '../');
define('SF_NEW_VERSION','1.0.0');
define('SF_PATH','../installer/');
define('SF_INCLUDES','../includes/');
define('DEBUG_MODE', 1);

$sf_settings = array();
$sf_settings['sf_version']='2.4.2';

if (DEBUG_MODE == 1){
    error_reporting(E_ALL);
}else{
    error_reporting(0);
}

/* Debugging should be enabled in installation mode */
$sf_settings['debug_mode'] = 1;
error_reporting(E_ALL);

require(SF_PATH . 'inc/common.inc.php');
require(SF_PATH . 'inc/database.inc.php');
sf_session_start();

sf_iHeader();
?>
	<br />

    <div align="center">
	<table class="red-table">
	<tr>
		<td>
        <h3>Thank you for downloading 1-Ecommerce!</h3>
        <p>&nbsp;</p>
        <p>This tool will help you install and configure 1-Ecommerce on your server.</p>
        <p>&nbsp;</p>
        <p><font color="#FF0000">PLEASE <a href="http://www.1-ecommerce.com/help/knowledgebase.php?article=7" target="_blank" >READ INSTALLATION GUIDE</a> BEFORE RUNNING THIS INSTALLATION SCRIPT!</font></p>       
        <hr />     
        <form method="get" action="install.php">
        <p align="center"><input type="submit" value="New install" class="orangebutton" onmouseover="sf_btn(this,'orangebuttonover');" onmouseout="sf_btn(this,'orangebutton');" /></p>
        <p align="center">Installs a new copy of 1-Ecommerce on your server</p>
        </form>
        <p>&nbsp;</p>
		</td>
	</tr>
	</table>
    </div>

<?php
sf_iFooter();
exit();


function sf_iHeader() {
    global $sf_settings;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>Install 1-Ecommerce <?php echo SF_NEW_VERSION; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
	<link href="css/sf_style.css" type="text/css" rel="stylesheet" />
    </head>
<body>
<div class="body-wrapper">
<h1 class="bannername">1-ecommerce</h1>
<div id="container">
<div class="body-content">
<h1>1-Ecommerce <?php echo SF_NEW_VERSION; ?> installation script</h1>
<div align="center">
<table border="0" cellspacing="0" cellpadding="5" class="enclosing">
	<tr>
	<td>

<?php
} // End sf_iHeader()


function sf_iFooter() {
    global $sf_settings;
?>
	<p>&nbsp;</p>
    </td>
    </tr>
    </table>
</div>	
<!-- body-indexcontent end -->
</div>	
<!-- container end -->
</div>
<!-- body-wrapper end -->
</div>

</body>
</html>
<?php
} // End sf_iFooter()
?>

