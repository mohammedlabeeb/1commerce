<?php
define('IN_SCRIPT',1);
define('INSTALL',1);
define('SITE_HOME', '../');
define('SF_NEW_VERSION','1.0.0');
define('SF_PATH','../installer/');
define('SF_INCLUDES','../includes/');
define('DEBUG_MODE', 1);

$sf_settings = array();
$sf_settings['sf_version']='1.0.0';
$sf_settings['db_host']='localhost';
$sf_settings['db_name']='your_db_name';
$sf_settings['db_user']='your_db_username';
$sf_settings['db_pass']='your_db_password';
$sf_settings['sf_title']='1-Ecommerce ' . SF_NEW_VERSION . ' installation script';
$sf_settings['site_title']='1-Ecommerce ' . SF_NEW_VERSION . ' installation script';

if (DEBUG_MODE == 1){
    error_reporting(E_ALL);
}else{
    error_reporting(0);
}

define('HIDE_ONLINE',1);

/* Debugging should be enabled in installation mode */
$sf_settings['debug_mode'] = 1;
error_reporting(E_ALL);

require(SF_PATH . 'inc/common.inc.php');
require(SF_PATH . 'inc/database.inc.php');
sf_session_start();

/* Check for license agreement */
if (empty($_SESSION['license_agree']))
{
    $agree = !empty($_GET['agree']) ? sf_input($_GET['agree']) : '';
    if ($agree == 'YES')
    {
        $_SESSION['license_agree']=1;
        $_SESSION['step']=1;
    }
    else
    {
        $_SESSION['step']=0;
    }
}

if (!isset($_SESSION['step']))
{
    $_SESSION['step']=0;
}

/* Test database connection */
if (isset($_POST['dbtest'])){
    $db_success = 1;
    $sf_settings['db_host']=sf_input($_POST['host']);
    $sf_settings['db_name']=sf_input($_POST['name']);
    $sf_settings['db_user']=sf_input($_POST['user']);
    $sf_settings['db_pass']=sf_input($_POST['pass']);

	/* Allow & in password */
    $sf_settings['db_pass']=str_replace('&amp;', '&', $sf_settings['db_pass']);

    /* Connect to database */
    $sf_db_link = @mysql_connect($sf_settings['db_host'],$sf_settings['db_user'], $sf_settings['db_pass']) or $db_success=0;

    /* Select database works ok? */
    if ($db_success == 1 && !mysql_select_db($sf_settings['db_name'], $sf_db_link))
    {
    	/* Try to create the database */
		if (mysql_query("CREATE DATABASE " . $sf_settings['db_name'], $sf_db_link))
        {
        	if (mysql_select_db($sf_settings['db_name'], $sf_db_link))
            {
				$db_success = 1;
            }
            else
            {
				$db_success = 2;
            }
        }
        else
        {
        	$db_success = 2;
        }
    }

    if ($db_success == 2)
    {
        sf_iDatabase(2);
        exit();
    }
    elseif ($db_success == 1)
    {
        /* Check if these MySQL tables already exist, stop if they do */
        $tables_exist=0;
        $sql='SHOW TABLES FROM `'.$sf_settings['db_name'].'`';
        $result = sf_dbQuery($sql);

		$sf_tables = array(
			'addl_products',
			'areadata',
			'attribute',
            'attribute_value',
			'categories',
			'currencies',
            'email_setup',
			'hotspots',
            'information',
            'links',
			'member',
			'options',
			'orderline',
			'orders',
			'preferences',
			'prodadd',
			'prodcat',
            'product',
			'promhead',
			'promline',
            'promotions',
			'qtydisch',
            'qtydiscl',
            'reviews',
			'selection',
			'users',
        );

        while ($row=mysql_fetch_array($result, MYSQL_NUM)){
            if (in_array($row[0],$sf_tables)){
                $tables_exist = 1;
                break;
            }
        }
        mysql_free_result($result);

        if ($tables_exist){
            $_SESSION['step']=0;
            $_SESSION['license_agree']=0;
            sf_iFinish(1);
        }

        /* All ok, save settings and install the tables */
        sf_iSaveSettings();
        sf_iTables();

        /* Close database conenction and move to the next step */
        mysql_close($sf_db_link);
        $_SESSION['step']=3;
    }
    else
    {
        sf_iDatabase(1);
        exit();
    }
}

switch ($_SESSION['step']){
	case 1:
	   sf_iCheckSetup();
	   break;
	case 2:
	   sf_iDatabase();
	   break;
	case 3:
	   sf_iFinish();
	   break;
	default:
	   sf_iStart();
}

function sf_iFinish($problem=0) {
    global $sf_settings;
    sf_iHeader();
	?>

    <table border="0" width="100%">
    <tr>
    <td>INSTALLATION STEPS:<br />
    <font color="#008000">1. License agreement</font> -&gt; <font color="#008000">2. Check setup</font> -&gt; <font color="#008000">3. Database settings</font> -&gt; <b>4. Setup database tables</b></td>
    </tr>
    </table>
    
        <br />
    
        <div align="center">
        <table class="red-table">
        <tr>
            <td>
    
    <h3>Setup database tables</h3>
    
    <table>
    <tr>
    <td>-&gt; Testing database connection...</td>
    <td><font color="#008000"><b>SUCCESS</b></td>
    </tr>
    <tr>
    <td>-&gt; Installing database tables...</td>
    
    <?php
    if ($problem==1)
    {
    ?>
    
        <td><font color="#FF0000"><b>ERROR: Database tables already exist!</b></td>
        </tr>
        </table>
    
        <p style="color:#FF0000;">1-Ecommerce database tables already exist in this database. <br/>Please ensure that the database does not contain any 1-Ecommerce tables and try again.</p>
    
        <p align="center"><a href="index.php">Click here to continue</a></p>
    
    <?php
    }
    else
    {
    ?>
    
        <td><font color="#008000"><b>SUCCESS</b></font></td>
        </tr>
        </table>
    
        <p>Congratulations, you have successfully completed 1-Ecommerce database setup!</p>
    
        <p style="color:#FF0000"><b>Next steps:</b></p>
    
        <ol>
        <li><font color="#FF0000"><b>IMPORTANT:</b></font> Before doing anything else <b>delete</b> the <b>install</b> folder from your server!
        You can leave this browser window open.<br />&nbsp;</li>
        <li>Setup your new 1-Ecommerce installation from the Content Management System. Login using the default
        username and password:<br /><br />
        Username: <b>admin</b><br />
        Password: <b>change-this</b><br /><br />
    
            <form action="<?php echo SITE_HOME; ?>_cms/login.php" method="post">
            <input type="hidden" name="user" value="admin" />
            <input type="hidden" name="pass" value="change-this" />
            <input type="submit" value="Click here to login" class="orangebutton" onmouseover="sf_btn(this,'orangebuttonover');" onmouseout="sf_btn(this,'orangebutton');" /></p>
            </form>
    
        </li>
        </ol>
    
        <p>&nbsp;</p>
    
        <p align="center">For further instructions please see the readme.html file!</p>
    
    <?php
    } // End else
    ?>
    
            </td>
        </tr>
        </table>
        </div>
    
    <?php
        sf_iFooter();
        exit();
} // End sf_iFinish()


function sf_iTables(){
	// This function setups all required MySQL tables
	
	// -> addl_products
	$sql="
	CREATE TABLE IF NOT EXISTS `addl_products` (
	  `AP_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `AP_POSITION` int(2) NOT NULL DEFAULT '10',
	  `AP_PRODUCT` varchar(11) NOT NULL,
	  `AP_ADDITIONAL` varchar(11) NOT NULL,
	  PRIMARY KEY (`AP_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");

	// -> areadata
	$sql="
	CREATE TABLE IF NOT EXISTS `areadata` (
	  `AR_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `AR_AREA` varchar(20) NOT NULL,
	  `AR_DATA` text,
	  PRIMARY KEY (`AR_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `areadata` (`AR_ID`, `AR_AREA`, `AR_DATA`) VALUES
	(1, 'Header', '&lt;!--Header Data Here--&gt;\r\n&lt;div class=&quot;top-logo&quot;&gt;\r\n&lt;a href=&quot;/&quot;&gt;&lt;img alt=&quot;Store logo&quot; height=&quot;151&quot; src=&quot;/theme/theme-images/bb-bodyboard-logo.png&quot; width=&quot;210&quot; /&gt;&lt;/a&gt;\r\n&lt;/div&gt;\r\n&lt;div class=&quot;mini-basket&quot; id=&quot;simplecart&quot;&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;\r\n					  &lt;!--\r\n										SimpleCart();\r\n								//--&gt;\r\n					&lt;/script&gt;\r\n&lt;/div&gt;\r\n\r\n'),
	(3, 'Sidebar', '&lt;p&gt;\r\n	&lt;strong&gt;Sidebar Data Here&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Input anything you like within reason!!!&lt;/p&gt;\r\n'),
	(4, 'Background', ''),
	(2, 'Footer', '&lt;p&gt;\r\n	&lt;strong&gt;Footer Data Here&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Input anything you like within reason!!!&lt;/p&gt;\r\n')
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> attribute
	$sql="
	CREATE TABLE IF NOT EXISTS `attribute` (
	  `AT_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `AT_SEARCH_NAME` varchar(100) DEFAULT NULL,
	  `AT_NAME` varchar(100) DEFAULT NULL,
	  `AT_POSITION` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`AT_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> attribute_value
	$sql="
	CREATE TABLE IF NOT EXISTS `attribute_value` (
	  `AV_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `AV_AT_ID` int(11) NOT NULL,
	  `AV_NAME` varchar(100) DEFAULT NULL,
	  `AV_POSITION` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`AV_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> categories	
	$sql="
	CREATE TABLE IF NOT EXISTS `categories` (
	  `CA_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `CA_NAME` varchar(100) NOT NULL DEFAULT '',
	  `CA_DESCRIPTION` text NOT NULL,
	  `CA_CODE` varchar(11) DEFAULT NULL,
	  `CA_PARENT` varchar(11) NOT NULL DEFAULT '0',
	  `CA_TREE_NODE` varchar(60) NOT NULL DEFAULT '0',
	  `CA_IMAGE` varchar(255) DEFAULT 'no-image.jpg',
	  `CA_IMAGE_FOLDER` varchar(60) DEFAULT NULL,
	  `CA_IMAGE_ALT` varchar(255) DEFAULT NULL,
	  `CA_DATE_ADDED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  `CA_DISPLAY` char(1) NOT NULL DEFAULT 'Y',
	  `CA_MENU_POSN` int(11) NOT NULL DEFAULT '0',
	  `CA_PROMOTION` char(1) NOT NULL DEFAULT 'N',
	  `CA_PROMOTION_POSN` int(11) NOT NULL DEFAULT '0',
	  `CA_META_TITLE` varchar(255) DEFAULT NULL,
	  `CA_META_DESC` text,
	  `CA_META_KEYWORDS` text,
	  `CA_CUSTOM_HEAD` text,
	  `CA_TOP_CONTENT` text,
	  `CA_BOTTOM_CONTENT` text,
	  `CA_ATTRIBUTE1` int(11) NOT NULL DEFAULT '0',
	  `CA_ATTRIBUTE2` int(11) NOT NULL DEFAULT '0',
	  `CA_ATTRIBUTE3` int(11) NOT NULL DEFAULT '0',
	  `CA_ATTRIBUTE4` int(11) NOT NULL DEFAULT '0',
	  `CA_ATTRIBUTE5` int(11) NOT NULL DEFAULT '0',
	  `CA_ATTRIBUTE6` int(11) NOT NULL DEFAULT '0',
	  `CA_ATTRIBUTE7` int(11) NOT NULL DEFAULT '0',
	  `CA_ATTRIBUTE8` int(11) NOT NULL DEFAULT '0',
	  `CA_TABULAR_LISTING` char(1) NOT NULL DEFAULT 'N',
	  `CA_DIV_WRAP` varchar(30) NOT NULL DEFAULT 'products-holder-firefox',
	  `CA_CLASS` varchar(30) NOT NULL DEFAULT 'Standard',
	  `CA_DISABLE` char(1) NOT NULL DEFAULT 'N',
	  `CA_LAST_UPDATED` datetime NOT NULL,
	  PRIMARY KEY (`CA_ID`),
	  KEY `CA_ID` (`CA_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	
	
	
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `categories` (`CA_ID`, `CA_NAME`, `CA_DESCRIPTION`, `CA_CODE`, `CA_PARENT`, `CA_TREE_NODE`, `CA_IMAGE`, `CA_IMAGE_FOLDER`, `CA_IMAGE_ALT`, `CA_DATE_ADDED`, `CA_DISPLAY`, `CA_MENU_POSN`, `CA_PROMOTION`,
							  `CA_PROMOTION_POSN`, `CA_META_TITLE`, `CA_META_DESC`, `CA_META_KEYWORDS`, `CA_CUSTOM_HEAD`, `CA_TOP_CONTENT`, `CA_BOTTOM_CONTENT`,
							  `CA_ATTRIBUTE1`, `CA_ATTRIBUTE2`, `CA_ATTRIBUTE3`, `CA_ATTRIBUTE4`, `CA_ATTRIBUTE5`, `CA_ATTRIBUTE6`, `CA_ATTRIBUTE7`, `CA_ATTRIBUTE8`,
							  `CA_TABULAR_LISTING`, `CA_DIV_WRAP`, `CA_CLASS`, `CA_DISABLE`, `CA_LAST_UPDATED`) VALUES
	(11, 'Home Page', 'Home Page', 'CAAA000', '9999999', '9999999', 'no-image.jpg', '', 'no image', '2011-12-01 16:47:17', 'Y', 0, 'N', 0, '', '', '', NULL, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 'N', 'firefox', 'Standard', 'N', '0000-00-00 00:00:00')
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");

	// -> currencies
	$sql="
	CREATE TABLE IF NOT EXISTS `currencies` (
	  `CU_SF_CODE` int(3) NOT NULL,
	  `CU_ISO_CODE` varchar(10) NOT NULL,
	  `CU_NAME` varchar(30) NOT NULL,
	  `CU_SYMBOL` varchar(10) NOT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=latin1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `currencies` (`CU_SF_CODE`, `CU_ISO_CODE`, `CU_NAME`, `CU_SYMBOL`) VALUES
	(1, 'GBP', 'Pounds Sterling', '&pound;'),
	(2, 'EUR', 'Euro', '&euro;'),
	(3, 'USD', 'US Dollar', '$'),
	(4, 'AUD', 'Australian Dollar', 'AUD $'),
	(5, 'CAD', 'Canadian Dollar', 'CAD $'),
	(6, 'JPY', 'Japanese Yen', '&yen;'),
	(7, 'NZD', 'New Zealand Dollar', 'NZ $'),
	(8, 'CHF', 'Swiss Franc', '&curren;'),
	(9, 'HKD', 'Hong Kong Dollar', 'HK $'),
	(10, 'SGD', 'Singapore Dollar', 'SG $'),
	(11, 'SEK', 'Swedish Krona', '&curren;'),
	(12, 'DKK', 'Danish Krone', '&curren;'),
	(13, 'PLN', 'Polish Zloty', '&curren;'),
	(14, 'NOK', 'Norwegian Krone', '&curren;'),
	(15, 'HUF', 'Hungarian Forint', '&curren;'),
	(16, 'CZK', 'Czech Koruna', '&curren;'),
	(17, 'XTS', 'No Currency', '&curren;')
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> email_setup
	$sql="
	CREATE TABLE IF NOT EXISTS `email_setup` (
	  `EM_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `EM_REG_TO` varchar(100) DEFAULT NULL,
	  `EM_REG_CC` varchar(100) DEFAULT NULL,
	  `EM_REG_BCC` varchar(100) DEFAULT NULL,
	  `EM_REG_SUBJECT` varchar(200) DEFAULT NULL,
	  `EM_REG_HEADER` varchar(1000) DEFAULT NULL,
	  `EM_REG_CONTENT` varchar(2000) DEFAULT NULL,
	  `EM_REG_FOOTER` varchar(1000) DEFAULT NULL,
	  `EM_CONF_CC` varchar(100) DEFAULT NULL,
	  `EM_CONF_BCC` varchar(100) DEFAULT NULL,
	  `EM_CONF_SUBJECT` varchar(200) DEFAULT NULL,
	  `EM_CONF_HEADER` varchar(1000) DEFAULT NULL,
	  `EM_CONF_CONTENT` varchar(2000) DEFAULT NULL,
	  `EM_CONF_FOOTER` varchar(1000) DEFAULT NULL,
	  `EM_REV_TO` varchar(100) DEFAULT NULL,
	  `EM_REV_CC` varchar(100) DEFAULT NULL,
	  `EM_REV_BCC` varchar(100) DEFAULT NULL,
	  `EM_REV_SUBJECT` varchar(200) DEFAULT NULL,
	  `EM_REV_HEADER` varchar(1000) DEFAULT NULL,
	  `EM_REV_CONTENT` varchar(2000) DEFAULT NULL,
	  `EM_REV_FOOTER` varchar(1000) DEFAULT NULL,
	  PRIMARY KEY (`EM_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `email_setup` (`EM_ID`, `EM_REG_TO`, `EM_REG_CC`, `EM_REG_BCC`, `EM_REG_SUBJECT`, `EM_REG_HEADER`, `EM_REG_CONTENT`, `EM_REG_FOOTER`, `EM_CONF_CC`, `EM_CONF_BCC`, `EM_CONF_SUBJECT`, `EM_CONF_HEADER`, `EM_CONF_CONTENT`, `EM_CONF_FOOTER`, `EM_REV_TO`, `EM_REV_CC`, `EM_REV_BCC`, `EM_REV_SUBJECT`, `EM_REV_HEADER`, `EM_REV_CONTENT`, `EM_REV_FOOTER`) VALUES
	(1, 'support@shopfitter.com', '', '', 'A New Member Application', 'New Member Application\r\n----------------------\\r\\n', 'The following applicant has applied for registration...\\r\\n\\r\\n', 'Please assess and confirm as soon as possible', 'simon@shopfitter.com', '', 'New Member Confirmation of Registration', 'New Member Confirmation of Registration\r\n---------------------------------------\\r\\n', 'Dear new member,\\r\\n\\r\\n\r\nThank you for your completed Trade Registration form which has now been accepted\\r\\n\\r\\n\r\nPlease login to our website at http://1-ecommerce.co.uk/index.php using the Trade Login button at the bottom of the page\\r\\n\\r\\n\r\nYour login details are as follows...\\r\\n\\r\\n', 'Best Regards\\r\\n\\r\\n\r\nThe Controller\\r\\n\r\n1-Ecommerce.com', 'mark@shopfitter.com', '', '', 'A New Review Submittal', 'New Review Submittal\r\n----------------------\\r\\n', 'The following applicant has submitted a new review...\\r\\n\\r\\n', 'Please assess and confirm as soon as possible')
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> hotspots
	$sql="
	CREATE TABLE IF NOT EXISTS `hotspots` (
	  `HS_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `HS_CODE` varchar(11) NOT NULL DEFAULT '',
	  `HS_NUMBER` int(3) NOT NULL DEFAULT '0',
	  `HS_DATA` text NOT NULL,
	  PRIMARY KEY (`HS_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> information
	$sql="
	CREATE TABLE IF NOT EXISTS `information` (
	  `IN_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `IN_PAGE` varchar(20) NOT NULL DEFAULT '',
	  `IN_NAME` varchar(100) NOT NULL DEFAULT '',
	  `IN_POSITION` int(11) NOT NULL DEFAULT '0',
	  `IN_LINK` varchar(200) DEFAULT NULL,
	  `IN_EDIT` char(1) NOT NULL DEFAULT 'Y',
	  `IN_ENABLED` char(1) NOT NULL DEFAULT 'Y',
	  `IN_DATA` text NOT NULL,
	  `IN_TITLE` varchar(65) DEFAULT NULL,
	  `IN_META_DESC` varchar(156) DEFAULT NULL,
	  `IN_META_KEYWORDS` text,
	  `IN_CUSTOM_HEAD` text,
	  `IN_CREATED` datetime DEFAULT NULL,
	  `IN_LAST_UPDATED` datetime DEFAULT NULL,
	  PRIMARY KEY (`IN_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `information` (`IN_ID`, `IN_PAGE`, `IN_NAME`, `IN_POSITION`, `IN_LINK`, `IN_EDIT`, `IN_ENABLED`, `IN_DATA`, `IN_TITLE`, `IN_META_DESC`, `IN_META_KEYWORDS`, `IN_CUSTOM_HEAD`, `IN_CREATED`, `IN_LAST_UPDATED`) VALUES";
	$sql .= "(1, 'Home', 'Home', 0, '/', 'Y', 'Y', '&lt;div class=&quot;home-top-slide-wrapper&quot;&gt;\r\n\r\n&lt;!-- Top Descriptive Text START --&gt;		\r\n&lt;div class=&quot;index-top-text-box-holder&quot;&gt;\r\n&lt;div class=&quot;index-top-text-box&quot;&gt;\r\n&lt;h1&gt;H1 Page Title Text - Important SEO Information&lt;/h1&gt;\r\n&lt;p&gt;Now you&#039;re here, you need&#039;t go anywhere else. Let us explain ...&lt;/p&gt;\r\n&lt;p&gt;A load of text describing the site, business and products on offer. Include important search terms and phrases as well as reasons why people should buy from this site.&lt;/p&gt;\r\n&lt;p&gt;Remember to include text that describes benefits to customers of buying from this site.&lt;/p&gt;\r\n&lt;/div&gt;\r\n&lt;/div&gt;\r\n&lt;!-- Top Descriptive Text END --&gt;	\r\n\r\n\r\n\r\n\r\n&lt;div class=&quot;home-banner-slide-holder&quot;&gt;\r\n&lt;div id=&quot;bannerfade&quot;&gt;\r\n&lt;!-- To add a New Ribbon Banner over top left of slideshow - remove following htm comments --&gt;\r\n&lt;!--			&lt;img src=&quot;images/new-ribbon.png&quot; width=&quot;112&quot; height=&quot;112&quot; alt=&quot;New Ribbon&quot; id=&quot;ribbon&quot;&gt;\r\n--&gt;\r\n&lt;!-- END of a New Ribbon Banner over top left of slideshow --&gt;\r\n\r\n			&lt;div id=&quot;banner&quot;&gt;\r\n				&lt;div class=&quot;slides_container&quot;&gt;\r\n&lt;!-- Animated Image Slideshow(Images must be 500 x 250 in size) Code START --&gt;\r\n					&lt;div class=&quot;slide&quot;&gt;\r\n						&lt;a href=&quot;Womens+Fashion/0_caaa001_CAAA006.htm&quot; title=&quot;Women&#039;s Fashion Sunglasses&quot;&gt;&lt;img src=&quot;images/animated-banner-500x250-1.jpg&quot; width=&quot;500&quot; height=&quot;250&quot; alt=&quot;Women&#039;s Fashion Sunglasses&quot;&gt;&lt;/a&gt;\r\n						&lt;div class=&quot;caption&quot; style=&quot;bottom:0&quot;&gt;\r\n							&lt;p&gt;Women&#039;s Fashion Sunglasses, image must be 500px wide x 250px high - please add more description here for SEO (upto 2 lines will fit)...&lt;/p&gt;\r\n						&lt;/div&gt;\r\n					&lt;/div&gt;\r\n\r\n&lt;!-- 1 x Animated Image Slideshow Code START --&gt;\r\n					&lt;div class=&quot;slide&quot;&gt;\r\n						&lt;a href=&quot;Kids+Fashion/0_caaa001_CAAA008.htm&quot; title=&quot;Kid&#039;s Fashion Sunglasses&quot;&gt;&lt;img src=&quot;images/animated-banner-500x250-2.jpg&quot; width=&quot;500&quot; height=&quot;250&quot; alt=&quot;Kid&#039;s Fashion Sunglasses&quot;&gt;&lt;/a&gt;\r\n						&lt;div class=&quot;caption&quot;&gt;\r\n							&lt;p&gt;Kid&#039;s Fashion Sunglasses, image must be 500px wide x 250px high - please add more description here for SEO(upto 2 lines will fit)...&lt;/p&gt;\r\n						&lt;/div&gt;\r\n					&lt;/div&gt;\r\n&lt;!-- 1 x Animated Image Slideshow Code END --&gt;\r\n\r\n					&lt;div class=&quot;slide&quot;&gt;\r\n						&lt;a href=&quot;Motor+and+Sports+Eyewear/0_CAAA004.htm&quot; title=&quot;Motor and Sports Eyewear Sunglasses&quot;&gt;&lt;img src=&quot;images/animated-banner-500x250-3.jpg&quot; width=&quot;500&quot; height=&quot;250&quot; alt=&quot;Motor and Sports Eyewear Sunglasses&quot;&gt;&lt;/a&gt;\r\n						&lt;div class=&quot;caption&quot;&gt;\r\n							&lt;p&gt;Motor and Sports Eyewear Sunglasses, image must be 500px wide x 250px high - please add more description here for SEO (upto 2 lines will fit)...&lt;/p&gt;\r\n						&lt;/div&gt;\r\n					&lt;/div&gt;\r\n					&lt;div class=&quot;slide&quot;&gt;\r\n						&lt;a href=&quot;Ski+and+Snowboard+Goggles/0_CAAA003.htm&quot; title=&quot;Ski and Snowboard Goggles&quot;&gt;&lt;img src=&quot;images/animated-banner-500x250-4.jpg&quot; width=&quot;500&quot; height=&quot;250&quot; alt=&quot;Ski and Snowboard Goggles&quot;&gt;&lt;/a&gt;\r\n						&lt;div class=&quot;caption&quot;&gt;\r\n							&lt;p&gt;Ski and Snowboard Goggles, image must be 500px wide x 250px high - please add more description here for SEO (upto 2 lines will fit)...&lt;/p&gt;\r\n						&lt;/div&gt;\r\n					&lt;/div&gt;\r\n					&lt;div class=&quot;slide&quot;&gt;\r\n						&lt;a href=&quot;Mens+Fashion/0_caaa001_CAAA007.htm&quot; title=&quot;Men&#039;s Fashion Sunglasses&quot;&gt;&lt;img src=&quot;images/animated-banner-500x250-5.jpg&quot; width=&quot;500&quot; height=&quot;250&quot; alt=&quot;Men&#039;s Fashion Sunglasses&quot;&gt;&lt;/a&gt;\r\n						&lt;div class=&quot;caption&quot;&gt;\r\n							&lt;p&gt;Men&#039;s Fashion Sunglasses, image must be 500px wide x 250px high - please add more description here for SEO (upto 2 lines will fit)...&lt;/p&gt;\r\n						&lt;/div&gt;\r\n					&lt;/div&gt;\r\n&lt;!-- Animated Image Slideshow(Images must be 500 x 250 in size) Code END --&gt;\r\n\r\n				&lt;/div&gt;\r\n				&lt;/div&gt;\r\n		&lt;/div&gt;\r\n		\r\n\r\n\r\n		&lt;div class=&quot;home-slide-title&quot;&gt;Our Bestsellers&lt;/div&gt;\r\n\r\n		&lt;div class=&quot;slide-wrap&quot;&gt;\r\n		\r\n		    &lt;div class=&quot;slides&quot;&gt;\r\n					&lt;div class=&quot;slides_container&quot;&gt;\r\n&lt;!-- Product Slider screen1 1-3 items (Images must be 130 x 115 in size) Code START --&gt;					\r\n					&lt;div class=&quot;slide&quot;&gt;\r\n\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;1 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;1 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;2 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;2 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;3 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;3 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n							\r\n					&lt;/div&gt;			\r\n&lt;!-- Product Slider screen1 1-3 items (Images must be 130 x 115 in size) Code END --&gt;\r\n\r\n&lt;!-- Product Slider screen2 4-6 items (Images must be 130 x 115 in size) Code START --&gt;				\r\n					&lt;div class=&quot;slide&quot;&gt;\r\n							\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;4 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;4 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;5 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;5 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;6 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;6 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n							\r\n					&lt;/div&gt;	\r\n		\r\n&lt;!-- Product Slider screen2 4-6 items (Images must be 130 x 115 in size) Code END --&gt;\r\n\r\n&lt;!-- Product Slider screen3 7-9 items (Images must be 130 x 115 in size) Code START --&gt;			\r\n					&lt;div class=&quot;slide&quot;&gt;\r\n							\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;7 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;7 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;8 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;8 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n					&lt;div class=&quot;home-prodx3&quot;&gt;\r\n					&lt;h2&gt;&lt;a href=&quot;/Catbird/0_caaa001_CAAA006/PRAA002.htm&quot;&gt;&lt;img src=&quot;images/home-catbird130x115.jpg&quot; alt=&quot;9 Catbird&quot; width=&quot;130&quot; height=&quot;115&quot; /&gt;9 Catbird Women&#039;s Sunglasses leopard or tortoiseshell pattern.&lt;/a&gt;&lt;/h2&gt;\r\n					&lt;/div&gt;\r\n\r\n\r\n					  &lt;/div&gt;\r\n\r\n&lt;!-- Product Slider screen3 7-9 items (Images must be 130 x 115 in size) Code END --&gt;\r\n\r\n			  &lt;/div&gt;\r\n			     &lt;a href=&quot;#&quot; class=&quot;prev&quot;&gt;Previous item&lt;/a&gt;&lt;a href=&quot;#&quot; class=&quot;next&quot;&gt;Next item&lt;/a&gt;&lt;/div&gt;\r\n\r\n		&lt;/div&gt;\r\n\r\n&lt;/div&gt;\r\n\r\n		\r\n&lt;!-- END OF Bannerfade Holder --&gt;		\r\n&lt;/div&gt;\r\n\r\n\r\n\r\n\r\n\r\n\r\n&lt;div class=&quot;home-prod-holder&quot;&gt;\r\n	\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n	\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n	\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n	\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n	\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n	\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n	\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n	\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n\r\n	&lt;div class=&quot;home-prod&quot;&gt;\r\n		&lt;h2&gt;\r\n			&lt;a href=&quot;/Dingo/0_caaa001_CAAA007/PRAA010.htm&quot;&gt;&lt;img src=&quot;images/home-dingo115x115.jpg&quot; alt=&quot;1 Dingo&quot; width=&quot;115&quot; height=&quot;115&quot; /&gt;1 Dingo &Acirc;&pound;17.99&lt;span&gt;Lightweight, shatterproof and flexible Mens Sunglasses.&lt;/span&gt;&lt;/a&gt;&lt;/h2&gt;\r\n	&lt;/div&gt;\r\n\r\n&lt;/div&gt;\r\n\r\n&lt;div class=&quot;home-text-holder&quot;&gt;\r\n	&lt;p&gt;You can add more products above by duplicating the individual product code, it will wrap to take up more space&lt;/p&gt;\r\n	&lt;h3&gt;HEADING 3 - You can add more information here \r\n		&Acirc;&nbsp;&lt;/h3&gt;\r\n	&lt;p&gt;\r\n		Please add more text to describe your site and products, the home page is the most important page for SEO so its well worth adding more descriptive text.&lt;/p&gt;\r\n\r\n&lt;/div&gt;\r\n\r\n\r\n\r\n\r\n\r\n\r\n&lt;script type=&quot;text/javascript&quot; src=&quot;https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js&quot;&gt;&lt;/script&gt;\r\n	&lt;script type=&quot;text/javascript&quot; src=&quot;js/slides.min.jquery.js&quot;&gt;&lt;/script&gt;\r\n	&lt;script&gt;\r\n		$(function(){\r\n			$(&#039;.slides&#039;).slides({\r\n				preload: true,\r\n				preloadImage: &#039;theme/theme-images/loading.gif&#039;,\r\n				play: 5000,\r\n				pause: 2500,\r\n				hoverPause: true\r\n			});\r\n\r\n\r\n  $(&quot;#banner&quot;).slides({\r\n				hoverPause: true,\r\n				pause: 10,\r\n				effect: &#039;fade&#039;,\r\n				preloadImage: &#039;theme/theme-images/loading.gif&#039;,\r\n				generatePagination: true,\r\n				crossfade: true,\r\n				fadeSpeed: 1000,\r\n				play: 4000,\r\n				animationStart: function(current){\r\n					$(&#039;.caption&#039;).animate({\r\n						bottom:-48\r\n					},100);\r\n					if (window.console &amp;&amp; console.log) {\r\n						// example return of current slide number\r\n						console.log(&#039;animationStart on slide: &#039;, current);\r\n					};\r\n				},\r\n				animationComplete: function(current){\r\n					$(&#039;.caption&#039;).animate({\r\n						bottom:0\r\n					},200);\r\n					if (window.console &amp;&amp; console.log) {\r\n						// example return of current slide number\r\n						console.log(&#039;animationComplete on slide: &#039;, current);\r\n					};\r\n				},\r\n				slidesLoaded: function() {\r\n					$(&#039;.caption&#039;).animate({\r\n						bottom:0\r\n					},200);\r\n				}\r\n			});\r\n		});\r\n		\r\n\r\n	&lt;/script&gt;\r\n\r\n\r\n\r\n\r\n\r\n\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n', 'Title Tag 1', 'meta tag 1', 'keywords tag 1', '', '0000-00-00 00:00:00', '2013-04-23 23:27:12'),";
	$sql .= "(26, 'Contact', 'Contact', 5, '/contact/', 'Y', 'Y', '&lt;p&gt;This is where an address, phone number and other stuff can go.&lt;/p&gt;\r\n&lt;p&gt;You could even put a map here to help customers find you.&lt;/p&gt;', '', '', '', '', '0000-00-00 00:00:00', '2013-03-15 13:15:11'),
(9, 'Links', 'Links', 4, '/Links.htm', 'Y', 'Y', '', '', '', '', '', '0000-00-00 00:00:00', '2013-04-02 15:11:22'),";
	$sql .= "(11, 'Terms-Page', 'Terms', 1, '/Terms-Page.htm', 'Y', 'Y', '&lt;p&gt;\r\n	&lt;span style=&quot;color:#ff0000;&quot;&gt;(Information in this style needs to be customised and inserted as appropriate instead of the note) Text in this style can be edited as required&lt;/span&gt;.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;1. Definitions.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	In these terms and conditions the following meanings will apply:&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	The Company means (Legal name of business)., whose registered office is at (registered office or main trading address of business).&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Customer means the person or company whose details are entered on any order or enquiry form on this website.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Browser means any person accessing and using this website by means of software products enabling Internet connection.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	The Website means the website at (insert website address).&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;2. Copyright.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	The website is owned and operated by The Company and all contents and designs are copyright of The Company and its suppliers or agents. Browsers using the site are permitted limited rights to view and print the contents for personal use only and are prohibited from copying or reproducing or reusing any of the contents or designs in any medium for any other purpose, in particular but not exclusively for any commercial gain.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;3. Products and Services.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Products offered by The Company, including for sale through The Website, include (Insert description of nature of products or services sold). The company undertakes that all products are of suitable quality for purpose (however Customers are asked to ensure that size, style and colour details are carefully checked before ordering as mistakes may not be rectifiable. Precise colour or specification details may vary from illustrations.)&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;4. Conditions of Contract.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	No contract will subsist between you The Customer and The Company for the sale of product(s) or service(s) to you, unless and until The Company accepts and confirms your order in writing or by email. The contract when formed will be deemed to have been concluded in (insert country where business is based) and will be interpreted, construed and enforced in all respects in accordance with the laws of (name of country), and will be subject to the jurisdiction of the (name of country) Courts.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	In the event that any clause within these terms is deemed at law to be unreasonable or unenforceable such clause will be deleted and such deletion will have no bearing on the validity or interpretation of the remaining clauses.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;5. Delivery.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Delivery of products ordered from stock will normally be made within (for example 7) working days within (description of delivery area. For multiple delivery zones describe arrangements). (Specify arrangements for non-stock or custom manufactured items). Carriage costs will be charged at cost as indicated on the order and confirmed to the Customer with the order acknowledgement.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	The Company will use its best endeavours to ensure timely delivery of all orders, but time of delivery will not be capable of being made of the essence of the contract as actual delivery to the Customer will be by independent carrier and outside the direct control of the Company.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;6. Price and Payment.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Prices shown on the website are (inclusive / exclusive delete as appropriate) of VAT (or local tax as appropriate), which is applicable on all consumer sales throughout the European Union (or country or trading area as appropriate). The Company reserves the right to vary prices to reflect changes in price from its suppliers without notice and the contract price will be the price quoted in the contract confirmation.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	The price of the contract will require to be paid in full prior to dispatch of the products or fulfilment of the services unless otherwise agreed. Payment is of the essence of the contract and the Company will be relieved of any obligation under the contract if payment is not made in accordance with the contract terms.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;7. Payment and Personal Data Security.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	To facilitate effective processing of orders the Company offers secure payment facilities online, via the website and can accept payment by major credit cards or debit cards by this method. To this end cookies are used by the website server to track order details only and Customers are asked to accept these files from the website server.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Personal details are encrypted during transmission and stored and used strictly in accordance with the Companys Data Protection Policy and will not be passed to any third party without your explicit permission. (Data protection should include registration with local responsible authority.)&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Alternatively payment may be made in person at the Companys premises or by cheque or bank transfer by arrangement at time of contract.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;8. Warranty.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	The Company warrants that all products and services supplied will be of suitable quality and fit for their designed purpose, (and offers an unequivocal guarantee that they will be free from manufacturing defect or fault).&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Exceptionally however, any discounted or sale products will be sold as is and are specifically excluded from this guarantee.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	No guarantee is offered where products are used for purposes other than that for which they were designed.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	This warranty is in addition to, and does not affect, your statutory rights.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;9. Cancellation.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Save as required by the (UK Consumer Protection (Distance Selling) Regulations 2000, or equivalent in the businesss home country), or pursuant to clause 6 above, the contract will be non-cancellable by the Customer, once confirmed by the Company. Ordering mistakes by Customers with regard to (size, colour or specification for example) will not constitute grounds for cancellation. Notwithstanding, acceptance of cancellation by the Company in exceptional circumstances and any refund or part refund will be solely at the discretion of the Company.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;10. Returns.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	In the unlikely event of products being faulty or of sub-standard quality, please report such problem immediately to the Company using the enquiry form or contact details on the website. Arrangements for the return of such product will be made on your behalf and the cost of return and replacement will be met fully by the Company. Goods should be returned together with original packaging wherever possible.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	In the event of cancellation of the contract, the Customer will be responsible for the safe return, and all costs of return, of the products in an undamaged, unworn state, together with all original packaging.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;11. Liability.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	To the maximum extent permissible in law, the Company excludes all liability for any loss or consequential loss however incurred by the Customer, arising from any action or omission or failure by the Company in connection with the contract.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	In any event the maximum liability of the Company will be not more than the purchase price of the products or services under the contract, should the contract be cancelled for any reason.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;12. Title and Risk.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Title in the goods will pass to the Customer on payment of the full purchase price. Risk however, will be carried by the Company until such time as the products are delivered to the Customer.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;13. Force Majeure.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	In the event of circumstances outside the Companys control affecting the performance of the contract, the Company will be entitled to notify the Customer and revise or cancel the contract to reflect the changed circumstances and the Customer will accept such changes.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;14. Notices.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	All communications in connection with the contract will be deemed to have been served if sent by ordinary mail to the Customers postal address or by email to the Customers email address, as notified to the Company by the Customer.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Communications to the Company should be addressed to the registered office address at (insert registered office or main business address of business) or by email to (insert contact email address). Or contact by telephone at (insert international telephone number).&lt;/p&gt;\r\n', 'Terms Page', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),";
	$sql .= "(12, 'Delivery', 'Delivery', 2, '/Delivery.htm', 'Y', 'Y', '&lt;h1&gt;\r\n	Delivery&lt;/h1&gt;\r\n&lt;p&gt;\r\n	Here you can add information about your shipping / delivery charges - different rates and zones can be set in the shipping screen.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Our delivery costs are displayed at the shopping cart page, please select the relevant shipping from the drop down menu.&lt;/p&gt;\r\n', '', '', '', '', '0000-00-00 00:00:00', '2013-04-05 11:03:34'),";
	$sql .= "(18, 'Privacy', 'Privacy', 3, '/Privacy.htm', 'Y', 'Y', '&lt;h1&gt;&lt;a name=&quot;_top&quot; id=&quot;_top&quot;&gt;&lt;/a&gt;privacy policy&lt;/h1&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;&lt;a href=&quot;#_Statement_of_intent&quot;&gt;Statement of intent&lt;/a&gt;&lt;br /&gt;\r\n  &lt;a href=&quot;#_Collection_of_personal&quot;&gt;Collection of personal information&lt;/a&gt;&lt;br /&gt;\r\n  &lt;a href=&quot;#_Storage_of_personal&quot;&gt;Storage of personal information&lt;/a&gt;&lt;br /&gt;\r\n  &lt;a href=&quot;#_Sharing_information&quot;&gt;Sharing information&lt;/a&gt;&lt;br /&gt;\r\n  &lt;a href=&quot;#_Cookies&quot;&gt;Cookies&lt;/a&gt;&lt;br /&gt;\r\n  &lt;a href=&quot;#_I.P._Address_1&quot;&gt;I.P. Address&lt;/a&gt;&lt;br /&gt;\r\n  &lt;a href=&quot;#_Access&quot;&gt;Access&lt;/a&gt;&lt;br /&gt;\r\n  &lt;a href=&quot;#_Links&quot;&gt;Links&lt;/a&gt;&lt;br /&gt;\r\n&lt;/p&gt;\r\n&lt;h2&gt;&lt;a name=&quot;_Statement_of_intent&quot; id=&quot;_Statement_of_intent&quot;&gt;&lt;/a&gt;&lt;a href=&quot;#_top&quot;&gt;Statement of intent&lt;/a&gt;&lt;/h2&gt;\r\n&lt;p&gt;Our Privacy Policy explains how and why we collect personal information about you.  We are committed to responsible management of personal information in accordance with the Data Protection Act 1998 and the Privacy and Electronic Communications (EC  Directive) Regulations 2003. &lt;/p&gt;\r\n&lt;h2&gt;&lt;a name=&quot;_Collection_of_personal&quot; id=&quot;_Collection_of_personal&quot;&gt;&lt;/a&gt;&lt;a href=&quot;#_top&quot;&gt;Collection of personal  information&lt;/a&gt;&lt;/h2&gt;\r\n&lt;p&gt;In order to receive certain information and to use certain services on this site you will be asked to provide personal information about yourself such as your name, address and email address.  When you provide this information to us you consent to our use of that  information in accordance with the terms of this Privacy Policy. This information will be used to provide you with the services that you have requested and to provide you with news and information about our products and services. If you no longer want to receive such information just let us know by &lt;a href=&quot;support/index.php?a=add&quot;&gt;email&lt;/a&gt; and we will stop sending it.&lt;/p&gt;\r\n&lt;h2&gt;&lt;a name=&quot;_Storage_of_personal&quot; id=&quot;_Storage_of_personal&quot;&gt;&lt;/a&gt;&lt;a href=&quot;#_top&quot;&gt;Storage of personal  information&lt;/a&gt;&lt;/h2&gt;\r\n&lt;p&gt;We will keep your personal information for as long as we need it to provide our products and/or services to you. We will try to keep personal information which we hold about you up to date, but if you think that we are holding information which is inaccurate or you are otherwise unhappy about our use of your personal information then please send us an &lt;a href=&quot;support/index.php?a=add&quot;&gt;email&lt;/a&gt; to let us know.&lt;/p&gt;\r\n&lt;h2&gt;&lt;a name=&quot;_Sharing_information&quot; id=&quot;_Sharing_information&quot;&gt;&lt;/a&gt;&lt;a href=&quot;#_top&quot;&gt;Sharing information&lt;/a&gt;&lt;/h2&gt;\r\n&lt;p&gt;We will not sell the personal information that we collect from you. We  use the personal information which we collect from you to help us provide our products and/or services to you. Some data processing in relation to this site may be carried out for us by a third party and in some cases that may mean that the third party will receive your personal information. &lt;em&gt;Such third  provide guarantees to us that they will provide an adequate level of security. Any date processing carried out for us by a third party will be  governed by a written contract between us and that third party, which contract  will provide that the third party will only act on our instructions&lt;/em&gt;.  Apart from this we will not share or transfer any personal information with any other third party unless we are  required to do so by law.&lt;/p&gt;\r\n&lt;h2&gt;&lt;a name=&quot;_Cookies&quot; id=&quot;_Cookies&quot;&gt;&lt;/a&gt;&lt;a href=&quot;#_top&quot;&gt;Cookies&lt;/a&gt;&lt;/h2&gt;\r\n&lt;p&gt;Some websites use &#039;cookies&#039;&Acirc; which are text only strings of information that the website you are visiting transfers to the cookie file of the browser on your computer. Their purpose is to identify users and to enhance the user&#039;s experience by customising web pages. A cookie will usually contain the name of  the domain from which the cookie has come, an expiry date for the cookie, and a value, which is usually a random generated unique number.&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;&lt;strong&gt;Types of cookies&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;Cookies may be either &lt;strong&gt;first party cookies&lt;/strong&gt; or &lt;strong&gt;third party cookies&lt;/strong&gt;. &lt;strong&gt;First party cookies&lt;/strong&gt; are used by the website owner and &lt;strong&gt;third party cookies&lt;/strong&gt; are used by a third party such as an advertiser or a web designer or a website host.&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;Cookies may be &lt;strong&gt;session cookies&lt;/strong&gt;, which are temporary cookies that remain in the cookie file of your browser until you leave the site or &lt;strong&gt;persistent cookies&lt;/strong&gt;, which remain in the cookie file for much longer. How long persistent cookies remain in your browser depends on the duration given to the cookie.&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;Cookies may record personal data such as name, and email address and/or  non-personal data such as IP address and unique but anonymous customer identity.&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;&lt;em&gt;This site uses first party cookies to record on an anonymous basis  &lt;/em&gt;&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;ul&gt;\r\n  &lt;li&gt;&lt;em&gt;the precise time that you visit this site;&lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt; the precise web  pages that you view; &lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt; the websites visited by you before and after you visit this site; &lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt;to compile anonymous, aggregated statistics that allow  us to understand how users use this site to help us to improve the operation of this site and&lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt;items placed in the shopping basket&lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt; Please note that our cookies will not store your personal details. This site does not use third party cookies.&lt;/em&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;&lt;em&gt;This site uses first party cookies to record &lt;/em&gt;&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;ul&gt;\r\n  &lt;li&gt;&lt;em&gt;the precise time that you visit this site; &lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt;the precise web pages that you view and the websites visited by you before and after you visit this site; &lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt;to help us recognise you as a unique visitor when you return to this site and to allow us to tailor content for you&lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt;to compile anonymous, aggregated statistics that allow us to understand how users use this site to help us to improve the operation of this site;&lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt;to allow you to carry information across pages of our site an avoid having to re-enter information;&lt;/em&gt;&lt;/li&gt;\r\n  &lt;li&gt;&lt;em&gt;to allow you to maintain a shopping basket;&lt;/em&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;&lt;em&gt;This site does not use third party cookies.&lt;/em&gt;&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;&lt;a name=&quot;_I.P._Address&quot; id=&quot;_I.P._Address&quot;&gt;&lt;/a&gt;&lt;strong&gt;Refusing cookies&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;You are entitled to refuse the use of cookies on this site. However, search and shopping basket functionality will not work.&lt;/p&gt;\r\n&lt;p&gt;You can refuse cookies by making modifying the settings in your browser.&lt;/p&gt;\r\n&lt;p&gt;&Acirc;&nbsp;&lt;/p&gt;\r\n&lt;p&gt;For more information about how to manage cookies visit &lt;a href=&quot;http://www.allaboutcookies.org/&quot;&gt;http://www.allaboutcookies.org/&lt;/a&gt;&lt;/p&gt;\r\n&lt;h2&gt;&lt;a name=&quot;_I.P._Address_1&quot; id=&quot;_I.P._Address_1&quot;&gt;&lt;/a&gt;&lt;a href=&quot;#_top&quot;&gt;I.P. Address&lt;/a&gt;&lt;/h2&gt;\r\n&lt;p&gt; &lt;/p&gt;\r\n&lt;p&gt;This site logs your Internet Protocol (I.P.) address. All computers that  are linked to the Internet have an I.P. number. An I.P. address does not  provide identifiable personal information.&lt;/p&gt;\r\n&lt;h2&gt;&lt;a name=&quot;_Access&quot; id=&quot;_Access&quot;&gt;&lt;/a&gt;&lt;a href=&quot;#_top&quot;&gt;Access&lt;/a&gt;&lt;/h2&gt;\r\n&lt;p&gt; &lt;/p&gt;\r\n&lt;p&gt;You are entitled under the Data Protection Act 1998 to a copy of the  personal information that we hold about you if you apply to us in writing.  We may make a small charge for dealing with  any such request.&lt;/p&gt;\r\n&lt;h2&gt;&lt;a name=&quot;_Links&quot; id=&quot;_Links&quot;&gt;&lt;/a&gt;&lt;a href=&quot;#_top&quot;&gt;Links&lt;/a&gt;&lt;/h2&gt;\r\n&lt;p&gt;This site provides links to other websites. Our Privacy Policy applies only to this site and we are not responsible for the privacy practices of third party sites.&lt;/p&gt;', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),";
	$sql .= "(27, 'Cart', 'Cart', 6, 'Cart', 'Y', 'N', '&lt;p&gt;This is the cart editable section&lt;/p&gt;', 'Cart', 'meta desc test', 'meta key test', 'custom head test', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),";
	$sql .= "(28, 'Thank-You', 'Thanks', 7, '/Thank-You.htm', 'Y', 'N', '&lt;h1&gt;Thank you for your message&lt;/h1&gt;\r\n&lt;p&gt;&amp;nbsp&lt;/p&gt;\r\n&lt;p&gt;We&#039;ve received your message and will get back to you as soon as possible&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp&lt;/p&gt;', 'Thank You for your Message', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),";
	$sql .= "(29, 'member', 'Member', 8, '/login_member.php', 'Y', 'Y', '&lt;h2&gt;Member Login Page Info&lt;/h2&gt;\r\n&lt;p&gt;This is where you can add content on the member login page&lt;/p&gt;', 'Member Login Page', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),";
	$sql .= "(32, '404', '404', 9, '/404.htm', 'Y', 'N', '&lt;h1&gt;\r\n	Page Not Found : 404&lt;/h1&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	The page you&amp;#39;re trying to find has moved or there&amp;#39;s been an error&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Please use the menu on the left to find what you&amp;#39;re looking for&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Or go the the &lt;a href=&quot;/&quot;&gt;Home Page&lt;/a&gt;&lt;/p&gt;', 'Page Not Found: 404 Error', '', '', '', '0000-00-00 00:00:00', '2013-06-03 20:14:01'),";
	$sql .= "(33, 'Search', 'Search', 10, '/search_advanced.htm', 'Y', 'Y', '', NULL, NULL, NULL, NULL, '2013-06-03 20:14:34', NULL)";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");

	
	// -> links
	$sql="
	CREATE TABLE IF NOT EXISTS `links` (
	  `LK_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `LK_PRODUCT` varchar(11) NOT NULL DEFAULT '',
	  `LK_PRODUCT_LINK` varchar(11) NOT NULL DEFAULT '',
	  PRIMARY KEY (`LK_ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> member
	$sql="
	CREATE TABLE IF NOT EXISTS `member` (
	  `MB_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `MB_USERNAME` varchar(20) NOT NULL,
	  `MB_PASSWORD` varchar(41) NOT NULL,
	  `MB_DATECREATED` datetime DEFAULT NULL,
	  `MB_LASTLOGIN` datetime DEFAULT NULL,
	  `MB_LOGINCOUNT` int(11) DEFAULT NULL,
	  `MB_CATEGORY` varchar(2) DEFAULT NULL,
	  `MB_TITLE` varchar(4) DEFAULT NULL,
	  `MB_FIRSTNAME` varchar(20) DEFAULT NULL,
	  `MB_LASTNAME` varchar(30) DEFAULT NULL,
	  `MB_COMPANY` varchar(50) DEFAULT NULL,
	  `MB_ADDRESS1` varchar(50) DEFAULT NULL,
	  `MB_ADDRESS2` varchar(50) DEFAULT NULL,
	  `MB_TOWN` varchar(50) DEFAULT NULL,
	  `MB_COUNTY` varchar(30) DEFAULT NULL,
	  `MB_COUNTRY` varchar(50) DEFAULT NULL,
	  `MB_POSTCODE` varchar(10) DEFAULT NULL,
	  `MB_PHONE` varchar(15) DEFAULT NULL,
	  `MB_MOBILE` varchar(15) DEFAULT NULL,
	  `MB_EMAIL` varchar(50) DEFAULT NULL,
	  `MB_CONFIRMED` char(1) NOT NULL DEFAULT 'N',
	  PRIMARY KEY (`MB_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> options	
	$sql="
	CREATE TABLE IF NOT EXISTS `options` (
	  `OP_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `OP_SE_ID` int(11) NOT NULL,
	  `OP_NAME` varchar(100) NOT NULL DEFAULT '',
	  `OP_NUMBER` int(11) NOT NULL DEFAULT '0',
	  `OP_TEXT` varchar(100) NOT NULL DEFAULT '',
	  `OP_VALUE` varchar(60) NOT NULL DEFAULT '',
	  `OP_SELECTED` char(1) NOT NULL DEFAULT 'N',
	  `OP_EXCLUDE` char(1) NOT NULL DEFAULT 'N',
	  `OP_PRODUCT` varchar(11) DEFAULT NULL,
	  `OP_SKU` varchar(20) DEFAULT NULL,
	  `OP_ATTRIBUTE_VALUE` int(11) NOT NULL DEFAULT '0',
	  `OP_ATTRIBUTE_VALUE1` int(11) NOT NULL DEFAULT '0',
	  `OP_ATTRIBUTE_VALUE2` int(11) NOT NULL DEFAULT '0',
	  `OP_ATTRIBUTE_VALUE3` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`OP_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> orderline
//	$sql="
//	CREATE TABLE IF NOT EXISTS `orderline` (
//	  `OL_ID` int(11) NOT NULL AUTO_INCREMENT,
//	  `OL_ORDER_NO` varchar(10) NOT NULL,
//	  `OL_ITEM` varchar(11) NOT NULL,
//	  `OL_DESC` varchar(100) NOT NULL,
//	  `OL_PRICE` varchar(9) NOT NULL,
//	  `OL_QTY` int(11) NOT NULL,
//	  PRIMARY KEY (`OL_ID`)
//	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
//	";
//	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> orders
//	$sql="
//	CREATE TABLE IF NOT EXISTS `orders` (
//	  `OR_ID` int(11) NOT NULL AUTO_INCREMENT,
//	  `OR_ORDER_NO` varchar(10) NOT NULL,
//	  `OR_DATE_CREATED` datetime NOT NULL,
//	  `OR_FIRST_NAME` varchar(32) NOT NULL,
//	  `OR_LAST_NAME` varchar(64) NOT NULL,
//	  `OR_EMAIL` varchar(100) NOT NULL,
//	  `OR_TEL` varchar(20) NOT NULL,
//	  `OR_PRICE_TOTAL` varchar(9) NOT NULL,
//	  `OR_CUSTOMER_NAME` varchar(20) NOT NULL,
//	  `OR_CUSTOMER_ID` int(11) NOT NULL,
//	  PRIMARY KEY (`OR_ID`)
//	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 
//	";
//	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> preferences
	$sql="
	CREATE TABLE IF NOT EXISTS `preferences` (
	  `PREF_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PREF_SHOP_ID` int(11) DEFAULT NULL,
	  `PREF_TRADE_ID` int(11) DEFAULT NULL,
	  `PREF_SHOPNAME` varchar(64) DEFAULT NULL,
	  `PREF_SHOPURL` varchar(150) DEFAULT NULL,
	  `PREF_EMAIL` varchar(100) DEFAULT NULL,
	  `PREF_THEME` varchar(100) DEFAULT NULL,
	  `PREF_META_TITLE` varchar(255) DEFAULT NULL,
	  `PREF_META_DESC` text,
	  `PREF_META_KEYWORDS` text,
	  `PREF_CURRENCY` int(3) NOT NULL DEFAULT '1',
	  `PREF_VAT` varchar(9) DEFAULT '0.00',
	  `PREF_EXVAT` char(1) NOT NULL DEFAULT 'N',
	  `PREF_SELL_EXVAT` char(1) NOT NULL DEFAULT 'N',
	  `PREF_MIN_ORDER` varchar(9) NOT NULL DEFAULT '0.00',
	  `PREF_MIN_ORDER_TRADE` varchar(9) NOT NULL DEFAULT '0.00',
	  `PREF_GOOGLE_SEARCH` char(1) NOT NULL DEFAULT 'N',
	  `PREF_CAT_SEED` varchar(7) NOT NULL DEFAULT '',
	  `PREF_PROD_SEED` varchar(7) NOT NULL DEFAULT '',
	  `PREF_PROM_SEED` varchar(7) NOT NULL,
	  `PREF_CUSTOM_HEAD` text,
	  `PREF_TRACKING_CODE` text,
	  `PREF_SHOP_PW` varchar(100) DEFAULT NULL,
	  `PREF_SHOP_NOTES` varchar(2000) DEFAULT NULL,
	  `PREF_TOOL_TIPS` char(1) NOT NULL DEFAULT 'Y',
	  `PREF_ADVANCED_SEARCH` char(1) NOT NULL DEFAULT 'N',
	  `PREF_REVIEWS` char(1) NOT NULL DEFAULT 'Y',
	  `PREF_PUBLISH` char(1) NOT NULL DEFAULT 'N',
	  `PREF_SHOP_ACCESS` char(1) NOT NULL DEFAULT 'Y',
	  PRIMARY KEY (`PREF_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `preferences` (`PREF_ID`, `PREF_SHOP_ID`, `PREF_TRADE_ID`, `PREF_SHOPNAME`, `PREF_SHOPURL`, `PREF_EMAIL`, `PREF_THEME`, `PREF_META_TITLE`, `PREF_META_DESC`, `PREF_META_KEYWORDS`, `PREF_CURRENCY`, `PREF_VAT`, `PREF_EXVAT`, `PREF_SELL_EXVAT`, `PREF_MIN_ORDER`, `PREF_MIN_ORDER_TRADE`, `PREF_GOOGLE_SEARCH`, `PREF_CAT_SEED`, `PREF_PROD_SEED`, `PREF_PROM_SEED`, `PREF_CUSTOM_HEAD`, `PREF_TRACKING_CODE`, `PREF_SHOP_PW`, `PREF_SHOP_NOTES`, `PREF_TOOL_TIPS`, `PREF_ADVANCED_SEARCH`, `PREF_REVIEWS`, `PREF_PUBLISH`, `PREF_SHOP_ACCESS`) VALUES
	(1, 100000, 999999, 'New 1-ecommerce website', 'http://www.your-domain.com', 'email@your-domain.com', '0111_Typomorph - 4opt - 2up - menu left', 'most important search terms for site', 'Meta description stuff', 'less important key words and miss-spellings', 1, '0.00', 'Y', 'N', '0.00', '9.95', 'Y', 'CAAA001', 'PRAA001', 'PMAA001', '', '', 'Order Admin Account Password', '&lt;p&gt;\r\n	A place to put useful information relevant to the webshop&lt;/p&gt;\r\n&lt;p&gt;\r\n	For example; a link to &lt;a href=&quot;http://google.com/analytics&quot;&gt;Google Analytics&lt;/a&gt;&lt;/p&gt;\r\n&lt;p&gt;\r\n	Password reminders and other stuff&lt;/p&gt;\r\n', 'Y', 'Y', 'Y', 'Y', 'Y')
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	
	// -> pricecath
	$sql="
	CREATE TABLE IF NOT EXISTS `pricecath` (
	  `PCH_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PCH_PRODUCT` varchar(11) NOT NULL,
	  `PCH_TYPE` char(2) NOT NULL DEFAULT 'PC',
	  PRIMARY KEY (`PCH_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `pricecath` (`PCH_ID`, `PCH_PRODUCT`, `PCH_TYPE`) VALUES
	(1, 'MASTER', 'PC')
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");

	// -> pricecatl
	$sql="
	CREATE TABLE IF NOT EXISTS `pricecatl` (
	  `PCL_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PCL_PCH_ID` int(11) NOT NULL,
	  `PCL_CAT` char(2) NOT NULL,
	  `PCL_ADJUST` varchar(9) NOT NULL,
	  PRIMARY KEY (`PCL_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> prodadd
	$sql="
	CREATE TABLE IF NOT EXISTS `prodadd` (
	  `PRA_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PRA_POSITION` int(2) NOT NULL DEFAULT '0',
	  `PRA_PRODUCT` varchar(11) NOT NULL DEFAULT '',
	  `PRA_IMAGE` varchar(255) DEFAULT NULL,
	  `PRA_IMAGE_ALT` varchar(100) DEFAULT NULL,
	  `PRA_IMAGE_FOLDER` varchar(100) DEFAULT NULL,
	  PRIMARY KEY (`PRA_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> prodcat
	$sql="
	CREATE TABLE IF NOT EXISTS `prodcat` (
	  `PC_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PC_PRODUCT` varchar(11) NOT NULL DEFAULT '',
	  `PC_CATEGORY` varchar(11) NOT NULL DEFAULT '',
	  `PC_TREE_NODE` varchar(60) NOT NULL DEFAULT '0',
	  `PC_POSITION` int(11) NOT NULL DEFAULT '999',
	  PRIMARY KEY (`PC_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> product
	$sql="
	CREATE TABLE IF NOT EXISTS `product` (
	  `PR_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PR_PRODUCT` varchar(11) NOT NULL DEFAULT '',
	  `PR_NAME` varchar(100) NOT NULL DEFAULT '',
	  `PR_CAT_TREE` varchar(60) NOT NULL DEFAULT '0',
	  `PR_SKU` varchar(20) DEFAULT NULL,
	  `PR_DESC_SHORT` varchar(256) NOT NULL DEFAULT '',
	  `PR_DESC_LONG` text,
	  `PR_DESC_TRADE` varchar(100) DEFAULT NULL,
	  `PR_IMAGE` varchar(255) DEFAULT NULL,
	  `PR_IMAGE_ALT` varchar(255) DEFAULT NULL,
	  `PR_IMAGE_FOLDER` varchar(100) DEFAULT NULL,
	  `PR_DIMENSIONS` varchar(60) DEFAULT NULL,
	  `PR_WEIGHT` int(11) NOT NULL DEFAULT '0',
	  `PR_QUANTITY` int(11) NOT NULL DEFAULT '0',
	  `PR_SELLING` varchar(9) NOT NULL DEFAULT '0.00',
	  `PR_TRADE` varchar(9) NOT NULL DEFAULT '0.00',
	  `PR_TAX` varchar(9) NOT NULL DEFAULT '0.00',
	  `PR_SHIPPING` varchar(9) NOT NULL DEFAULT '0.00',
	  `PR_TAXEXEMPTION` char(1) NOT NULL DEFAULT 'N',
	  `PR_SHIPPING_APPLY` char(1) NOT NULL DEFAULT 'Y',
	  `PR_SCREEN_POSN` int(11) NOT NULL DEFAULT '0',
	  `PR_PROMOTION` char(1) NOT NULL DEFAULT 'N',
	  `PR_PROMOTION_POSN` int(11) NOT NULL DEFAULT '0',
	  `PR_OPTION1` int(11) DEFAULT NULL,
	  `PR_OPTION2` int(11) DEFAULT NULL,
	  `PR_OPTION3` int(11) DEFAULT NULL,
	  `PR_OPTION4` int(11) DEFAULT NULL,
	  `PR_USER_STRING1` varchar(50) DEFAULT NULL,
	  `PR_GOOGLE_CAT` varchar(200) DEFAULT NULL,
	  `PR_GOOGLE_BRAND` varchar(100) DEFAULT NULL,
	  `PR_GOOGLE_GTIN` varchar(20) DEFAULT NULL,
	  `PR_GOOGLE_MPN` varchar(20) DEFAULT NULL,
	  `PR_GOOGLE_CONDITION` varchar(20) NOT NULL DEFAULT 'New',
	  `PR_GOOGLE_ADWORDS_GROUPING` varchar(50) DEFAULT NULL,
	  `PR_GOOGLE_ADWORDS_LABELS` text,
	  `PR_GOOGLE_ADWORDS_REDIRECT` varchar(200) DEFAULT NULL,
	  `PR_AVAILABILITY` varchar(30) DEFAULT NULL,
	  `PR_NO_STOCK` varchar(200) DEFAULT NULL,
	  `PR_DISABLE` char(1) NOT NULL DEFAULT 'N',
	  `PR_META_TITLE` varchar(255) DEFAULT NULL,
	  `PR_META_DESC` text,
	  `PR_META_KEYWORDS` text,
	  `PR_CUSTOM_HEAD` text,
	  `PR_PROD_WRAP` text NOT NULL,
	  `PR_DATE_ADDED` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	  `PR_LAST_UPDATED` datetime NOT NULL,
	  PRIMARY KEY (`PR_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> promhead
	$sql="
	CREATE TABLE IF NOT EXISTS `promhead` (
	  `PROMH_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PROMH_NO` varchar(11) NOT NULL,
	  `PROMH_PROM_ID` int(11) NOT NULL,
	  `PROMH_ADJUST` varchar(9) NOT NULL,
	  `PROMH_START` datetime NOT NULL,
	  `PROMH_EXPIRY` datetime NOT NULL,
	  PRIMARY KEY (`PROMH_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> promline
	$sql="
	CREATE TABLE IF NOT EXISTS `promline` (
	  `PROML_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PROML_NO` varchar(11) NOT NULL,
	  `PROML_POS` int(3) NOT NULL,
	  `PROML_CAT` varchar(11) NOT NULL,
	  `PROML_PROD` varchar(11) NOT NULL,
	  PRIMARY KEY (`PROML_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> promotions
	$sql="
	CREATE TABLE IF NOT EXISTS `promotions` (
	  `PROM_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `PROM_TYPE` varchar(30) NOT NULL,
	  `PROM_LEVEL` char(10) NOT NULL DEFAULT 'Both',
	  PRIMARY KEY (`PROM_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `promotions` (`PROM_ID`, `PROM_TYPE`, `PROM_LEVEL`) VALUES
	(1, 'Percentage', 'Both'),
	(2, 'Adjustment', 'Both')
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> qtydisch
	$sql="
	CREATE TABLE IF NOT EXISTS `qtydisch` (
	  `QDH_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `QDH_PRODUCT` varchar(11) NOT NULL,
	  `QDH_TYPE` char(2) NOT NULL DEFAULT 'P',
	  PRIMARY KEY (`QDH_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> qtydiscl
	$sql="
	CREATE TABLE IF NOT EXISTS `qtydiscl` (
	  `QDL_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `QDL_QDH_ID` int(11) NOT NULL,
	  `QDL_QTY` int(11) NOT NULL,
	  `QDL_ADJUST` varchar(9) NOT NULL,
	  PRIMARY KEY (`QDL_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> reviews
	$sql="
	CREATE TABLE IF NOT EXISTS `reviews` (
	  `RV_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `RV_PRODUCT` varchar(11) NOT NULL,
	  `RV_ORDER` varchar(10) NOT NULL,
	  `RV_AUTHOR` varchar(60) NOT NULL,
	  `RV_TOWN` varchar(50) NOT NULL,
	  `RV_COUNTRY` varchar(50) NOT NULL,
	  `RV_DATE` datetime NOT NULL,
	  `RV_RATING` decimal(2,1) NOT NULL,
	  `RV_TITLE` varchar(100) NOT NULL,
	  `RV_TEXT` varchar(1000) NOT NULL,
	  `RV_REPLY` varchar(1000) DEFAULT NULL,
	  `RV_DATE_REPLY` datetime DEFAULT NULL,
	  `RV_PUBLISHED` char(1) NOT NULL DEFAULT 'N',
	  `RV_DATE_PUBLISH` datetime DEFAULT NULL,
	  PRIMARY KEY (`RV_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> selection	
	$sql="
	CREATE TABLE IF NOT EXISTS `selection` (
	  `SE_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `SE_NAME` varchar(100) NOT NULL DEFAULT '',
	  `SE_LABEL` varchar(100) NOT NULL DEFAULT '',
	  `SE_EXCLUDE` char(1) NOT NULL DEFAULT 'N',
	  `SE_PRODUCT` varchar(11) NOT NULL DEFAULT 'GENERAL',
	  `SE_ATTRIBUTE` int(11) NOT NULL DEFAULT '0',
	  `SE_ATTRIBUTE1` int(11) NOT NULL DEFAULT '0',
	  `SE_ATTRIBUTE2` int(11) NOT NULL DEFAULT '0',
	  `SE_ATTRIBUTE3` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`SE_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
	";
	
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	// -> users
	$sql="
	CREATE TABLE IF NOT EXISTS `users` (
	  `user_id` int(11) NOT NULL AUTO_INCREMENT,
	  `user_name` varchar(20) NOT NULL DEFAULT '',
	  `user_password` varchar(41) NOT NULL DEFAULT '',
	  `user_dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  `user_lastlogin` datetime DEFAULT NULL,
	  PRIMARY KEY (`user_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	$sql="
	INSERT INTO `users` (`user_id`, `user_name`, `user_password`, `user_dateadded`, `user_lastlogin`) VALUES
	(1, 'admin', '7bb34866d45c3a48b519340e4beac657426d46db', '2013-06-21 20:22:02', '2013-06-21 20:22:02'),
	(2, 'shopfitter', '633daa959306b7afdf045094e8ef2f636f2a6dc9', '2012-01-03 20:15:43', '2012-01-03 20:15:43')
	";
	$result = sf_dbQuery($sql) or sf_error("Couldn't execute SQL: $sql. MySQL said: ".mysql_error()."<br />&nbsp;<br /> Please make sure you delete any old installations of 1-Ecommerce before installing this version!");
	
	return true;
} // End sf_iTables()


function sf_iSaveSettings() {
    global $sf_settings;

    /* Encode and escape characters */
    $set = $sf_settings;
    foreach ($sf_settings as $k=> $v){
    	if (is_array($v)){
        	continue;
        }
    	$set[$k] = addslashes($v);
    }
    //Rewrite the whole of dbopen.php
	$settings_file_content='
	<?php
	// connect to the database
	$db = \'' . $set['db_name'] . '\';
	$host = \'' . $set['db_host'] . '\';
	$user = \'' . $set['db_user'] . '\';
	$password = \'' . $set['db_pass'] . '\';
	
	$dbConn = mysql_connect($host,$user,$password) or die(\'Failed to connect to database\');
	$result = mysql_select_db($db, $dbConn) or die(\'Failure selecting database\');
	?>
	';
	
	$fp=fopen(SF_INCLUDES . 'dbopen.php','w') or sf_error("Unable to open database login file");
	fputs($fp,$settings_file_content);
	fclose($fp);
	
	return true;
} // End sf_iSaveSettings()


function sf_iDatabase($problem=0) {
    global $sf_settings, $sf_db_link;
    sf_iHeader();
	?>

    <table border="0" width="100%">
    <tr>
    <td>INSTALLATION STEPS:<br />
    <font color="#008000">1. License agreement</font> -&gt; <font color="#008000">2. Check setup</font> -&gt; <b>3. Database settings</b> -&gt; 4. Setup database tables</td>
    </tr>
    </table>
    
        <br />
    
        <div align="center">
        <table class="red-table">
        <tr>
            <td>
    
    <h3>Database settings</h3>
    
    <div align="center">
    <table border="0" width="750" cellspacing="1" cellpadding="5" class="white">
    <tr>
    <td>
    <p><b>1-Ecommerce will not work unless the information below is correct and a database connection
    test is successful. <br/>For correct database information contact your hosting company.</b></p>
    
    <?php
    if ($problem==1)
    {
        echo '<p style="color:#FF0000;"><b>Database connection failed!</b><br />Double-check all the information below. If not sure contact your hosting company for the correct information!<br /><br />MySQL said: '.mysql_error().'</p>';
    }
    elseif ($problem==2)
    {
        echo '<p style="color:#FF0000;"><b>Database connection failed!</b><br />Double-check <b>database name</b> and make sure the user has access to the database. If not sure contact your hosting company for the correct information!<br /><br />MySQL said: '.mysql_error().'</p>';
    }
    ?>
    
    <form action="install.php" method="post">
    <table>
    <tr>
    <td>Database Host:</td>
    <td><input type="text" name="host" value="<?php echo $sf_settings['db_host']; ?>" size="40" /></td>
    </tr>
    <tr>
    <td>Database Name:</td>
    <td><input type="text" name="name" value="<?php echo $sf_settings['db_name']; ?>" size="40" /></td>
    </tr>
    <tr>
    <td>Database User (login):</td>
    <td><input type="text" name="user" value="<?php echo $sf_settings['db_user']; ?>" size="40" /></td>
    </tr>
    <tr>
    <td>User Password:</td>
    <td><input type="text" name="pass" value="<?php echo $sf_settings['db_pass']; ?>" size="40" /></td>
    </tr>
    </table>
    
    <p align="center"><input type="hidden" name="dbtest" value="1" /><input type="submit" value="Continue to Step 4" class="orangebutton" onmouseover="sf_btn(this,'orangebuttonover');" onmouseout="sf_btn(this,'orangebutton');" /></p>
    </form>
    
    </td>
    </tr>
    </table>
    </div>
    
                </td>
            </tr>
            </table>
            </div>
    <?php
        sf_iFooter();
} // End sf_iDatabase()




function sf_iCheckSetup() {
    global $sf_settings;
    sf_iHeader();
    $_SESSION['all_passed']=1;
    $correct_this=array();
	?>

    <table border="0" width="100%">
    <tr>
    <td>INSTALLATION STEPS:<br />
    <font color="#008000">1. License agreement</font> -&gt; <b>2. Check setup</b> -&gt; 3. Database settings -&gt; 4. Setup database tables</td>
    </tr>
    </table>
    
    <br />
    
        <div align="center">
        <table class="red-table">
       <tr>
       <td>
    
    <h3>Check setup</h3>
    
    <p>Checking whether your server meets all requirements and that files are setup correctly</p>
    
    <div align="center">
    <table border="0" width="750" cellspacing="1" cellpadding="3" class="white">
    <tr>
    <th class="admin_white"><b>Required</b></th>
    <th class="admin_white"><b>Your setting</b></th>
    <th class="admin_white"><b>Status</b></th>
    </tr>
    
    <tr>
    <td class="admin_gray"><b>PHP version</b><br />1-Ecommerce requires PHP version 5.x</td>
    <td class="admin_gray" valign="middle" nowrap="nowrap"><b><?php echo PHP_VERSION; ?></b></td>
    <td class="admin_gray" valign="middle">
    <?php
    if (function_exists('version_compare') && version_compare(PHP_VERSION,'5.0.0','>=')){
        echo '<font color="#008000"><b>Passed</b></font>';
    }else{
        $_SESSION['all_passed']=0;
        echo '<font color="#FF0000"><b>Failed</b></font>';
        $correct_this[]='You are using an old and non-secure version of PHP, ask your host to update your PHP version!';
    }
    ?>
    </td>
    </tr>
    <!-- CHECK dbopen.php file can be written to --->
    <tr>
    <td class="admin_white"><b>includes/dbopen.php file</b><br />Must be uploaded and writable by the script</td>
    <td class="admin_white" valign="middle" nowrap="nowrap">
    <?php
    $mypassed=1;
    if (file_exists('../includes/dbopen.php')){
		@chmod('../includes/dbopen.php', 0666);
        echo '<b><font color="#008000">Exists</font>, ';
        if (is__writable('../includes/dbopen.php')){
            echo '<font color="#008000">Writable</font></b>';
        }else{
            echo '<font color="#FF0000">Not writable</font></b>';
            $mypassed=2;
        }
    }else{
        $mypassed=0;
        echo '<b><font color="#FF0000">Not uploaded</font>, <font color="#FF0000">Not writable</font></b>';
    }
    ?>
    </td>
    <td class="admin_white" valign="middle">
    <?php
    if ($mypassed==1){
        echo '<font color="#008000"><b>Passed</b></font>';
    }elseif ($mypassed==2){
        $_SESSION['all_passed']=0;
        echo '<font color="#FF0000"><b>Failed</b></font>';
        $correct_this[]='Make sure the <b>includes/dbopen.php</b> file is writable: on Linux chmod it to 666 or rw-rw-rw-, on Windows (IIS) make sure IUSR account has modify/read/write permissions';
    }else{
        $_SESSION['all_passed']=0;
        echo '<font color="#FF0000"><b>Failed</b></font>';
        $correct_this[]='The <b>dbopen.php</b> file does not exist. Please remove all installed folders/files, download 1-commerce software again and try to re-install. If this fails again then please contact 1-ecommerce.';
    }
    ?>
    </td>
    </tr>
    <!--- END OF Check dbopen.php file can be written to --->
    <!--- CHECK THAT ANY UPLOADS FOLDERS MAY BE WRITTEN TO --->
    <?php
	$folder = "../images";$correct_this = check_folders($folder, $correct_this);
	$folder = "../theme";$correct_this = check_folders($folder, $correct_this);
	$folder = "../theme_library";$correct_this = check_folders($folder, $correct_this);
	$folder = "../theme/theme-images";$correct_this = check_folders($folder, $correct_this);
	$folder = "../_cms/xml";$correct_this = check_folders($folder, $correct_this);
	$folder = "../_cms/zip";$correct_this = check_folders($folder, $correct_this);
	$folder = "../_cms/zip/unzipped";$correct_this = check_folders($folder, $correct_this);
	$folder = "../_cms/logs";$correct_this = check_folders($folder, $correct_this);
	$folder = "../xml";$correct_this = check_folders($folder, $correct_this);
	?>
    <!--- END OF... CHECK THAT ANY UPLOADS FOLDERS MAY BE WRITTEN TO --->
    <tr>
    <td class="admin_gray"><b>MySQL Enabled</b><br />MySQL must be enabled in PHP</td>
    <td class="admin_gray" valign="middle" nowrap="nowrap">
    <?php
    $mypassed=1;
    if (function_exists('mysql_connect')){
        echo '<b><font color="#008000">Enabled</font></b>';
    }else{
        $mypassed=0;
        $_SESSION['all_passed']=0;
        echo '<font color="#FF0000"><b>Disabled</b></font>';
        $correct_this[]='MySQL extension is not compiled/enabled in your PHP installation.';
    }
    ?>
    </td>
    <td class="admin_gray" valign="middle">
    <?php
    if ($mypassed==1){
        echo '<font color="#008000"><b>Passed</b></font>';
    }else{
        echo '<font color="#FF0000"><b>Failed</b></font>';
    }
    ?>
    </td>
    </tr>
    
    </table>
    </div>
    
    <p>&nbsp;</p>
    
    <?php
    if (!empty($correct_this)){
        ?>
        <div align="center">
        <table border="0" width="750" cellspacing="1" cellpadding="3">
        <tr>
        <td>
        <p><font color="#FF0000"><b>You will not be able to continue installation until the required tests are passed. Things you need to correct before continuing installation:</b></font></p>
        <ol>
        <?php
        foreach($correct_this as $mythis)
        {
            echo "<li>$mythis<br />&nbsp;</li>";
        }
        ?>
        </ol>
        <form method="post" action="install.php">
        <p align="center">&nbsp;<br /><input type="submit" value="Test again" class="orangebutton" onmouseover="sf_btn(this,'orangebuttonover');" onmouseout="sf_btn(this,'orangebutton');" /></p>
        </form>
        </td>
        </tr>
        </table>
        </div>
        <?php
    }else{
        $_SESSION['step']=2;
        ?>
        <form method="POST" action="install.php">
        <div align="center">
        <table border="0">
        <tr>
        <td>
        <p align="center"><font color="#008000"><b>All required tests passed, you may now continue to database setup</b></font></p>
        <p align="center"><input type="submit" value="Continue to Step 3" class="orangebutton" onmouseover="sf_btn(this,'orangebuttonover');" onmouseout="sf_btn(this,'orangebutton');" /></p>
        </td>
        </tr>
        </table>
        </div>
        </form>
    
        <?php
    }
    ?>
                </td>
            </tr>
            </table>
            </div>
    <?php
        sf_iFooter();
} // End sf_iCheckSetup()



function sf_iStart() {
    global $sf_settings;
    sf_iHeader();
?>

<table border="0" width="100%">
<tr>
<td>INSTALLATION STEPS:<br />
<b>1. License agreement</b> -&gt; 2. Check setup -&gt; 3. Database settings -&gt; 4. Setup database tables</td>
</tr>
</table>

<br />

    <div align="center">
	<table class="red-table">
	<tr>
		<td>

<h3>License agreement</h3>
<p>&nbsp;</p>
<p><strong>Summary:</strong></p>
<p>&nbsp;</p>
<ul>
<li>The script is provided &quot;as is&quot;, without any warranty. Use at your own risk.<br /></li>
<li>1-Ecommerce is a registered trademark, except in some special cases using the term !-Ecommerce requires permission.<br /></li>
<li>You are not allowed to redistribute this script or any software based on this script without written permission.<br /></li>
<li>Using this code, in part or full, to create new applications or products is expressly forbidden.<br /></li>
<li>You must not edit or remove any &quot;Powered by&quot; links without permission.</li>
</ul>
<p>&nbsp;</p>
<p><b>The entire License agreement:</b></p>
<p>&nbsp;</p>
<p align="center">
<textarea name="textarea" cols="65" rows="16">NOTICE: THIS SOFTWARE IS LICENSED TO YOU SUBJECT TO THE TERMS AND CONDITIONS OF THE FOLLOWING LEGALLY BINDING LICENCE AGREEMENT.  PLEASE READ THE AGREEMENT CAREFULLY BEFORE DOWNLOADING THE SOFTWARE.  BY EXERCISING THE DOWNLOAD OPTION YOU AGREE TO BE BOUND BY THE TERMS AND CONDITIONS OF THIS AGREEMENT. IF YOU DO NOT SO AGREE YOU SHOULD DISCONTINUE DOWNLOADING OF THE SOFTWARE AND DELETE ANY COPIES OF THE SOFTWARE IN YOUR POSSESSION.


1-ECOMMERCE LIMITED
SOFTWARE LICENCE AGREEMENT

1	OWNERSHIP AND LICENCE GRANT
1.1	1-Ecommerce Limited (&quot;Licensor&quot;) retains ownership of the copy of the software which you download from the Licensor's website and all other copies that you are authorised by this Agreement to make (&quot;the Software&quot;) including without limitation all copyright and other intellectual property rights, anywhere in the world, in the Software.
1.2	Licensor grants you the non-exclusive licence to install, store, access, display, run and use the Software to establish your own internet based e-commerce site only and not to  provide a bureau or other service for third parties.
1.3	You may install and use the Software only on a single computer at any one time, or on one internet server hosting your website provided that the Software is used only by you or your employees as provided in Clause 1.2 above.  The Licensor may be willing to act as Internet service provider for you, the terms and conditions of which shall be subject to separate agreement.
1.4	You may copy the Software as reasonably necessary for back-up and archival purposes, provided that each is kept in your possession, and that you reproduce the Licensor's copyright notice on each copy.  You may not otherwise copy, modify, merge, disassemble or decompile the Software for any reason except and to the extent expressly permitted by applicable law.
1.5	This licence is personal to you, and you may not sub-licence, rent, lend or lease the Software to anyone else.
1.6	You acknowledge that in order for the Software to operate and perform in accordance with the user guide found on the Licensor's website (the &quot;User Guide&quot;) the Licensor's order server has to be able to track all orders placed using the Software and an order tracking device is comprised within the Software.  The Licensor's order server will record each order placed upon you by each of your customers using the Software.  The Licensor shall advise you by e-mail of each such order recorded. You acknowledge that your collection and use of customer data is governed by the Data Protection Act 1998, and that you are therefore responsible for compliance with terms of this Act at all times.
1.7	You agree that, subject to providing evidence to the contrary, the records of the Licensor's order server shall be deemed to be an accurate record of orders placed upon you by your customers using the Software.  Notwithstanding the foregoing if an order is not recorded by the Licensor order server  for any reason, save as otherwise expressly set out herein, THE LICENSOR SHALL HAVE NO LIABILITY TO YOU IN RESPECT OF ANY ORDERS NOT RECEIVED BY YOU.
1.8	The Licensor shall not have access to any data regarding the orders placed by your customers or data concerning your customers themselves, save as you may authorise by separate agreement.
1.9	The Licensor shall provide support services in relation to the use and operation of the Software as advertised and on the terms and conditions set out on the Licensor's website from time to time.
2	FEES
2.1	In consideration of the grant of this Licence you agree to pay to the Licensor &pound;1, US $1, &euro;1 per order over &pound;/$/&euro;10 in value (or 25p, 25&cent; 25c if under &pound;10/$10/&euro;10) for goods or services placed upon you by your customers using the Software within 30 days of invoice therefor from Licensor, notwithstanding that any such order is subsequently withdrawn, not completed or the customer fails to pay you for the goods or services supplied.

2.2	The Licensor shall be entitled to increase the price payable under Clause 2.1 above at any time during the period of this Licence Agreement provided that no more than one increase shall be made in any period of 12 months and the Licensor shall give you not less than one months' notice of any such increase taking effect.
2.3	The payments due hereunder are stated exclusive of any Value Added Tax that may be payable thereon by you in addition to the sums stated herein.
2.4	You agree that the Licensor may suspend the operation of the Software and the processing of orders for goods or services if you fail to pay any sum due on its due date for payment, provided that the Licensor shall first give you not less than 7 days' notice in writing of its intention to suspend the operation of the Software IF YOU FAIL TO PAY THE FEES AS SET OUT ABOVE THE LICENSOR SHALL BE ENTITLED TO TERMINATE THIS AGREEMENT IN ACCORDANCE WITH CLAUSE 4 BELOW.
3	WARRANTY AND LIMITATION
3.1	If you discover a material error in the Software which substantially affects your use of the Software and you notify the Licensor of such error within 90 days from the date on which you download the Software, your sole and exclusive remedies will be to de-install the Software and either cease to use it or to download a further copy of the Software which shall be subject to the terms and conditions set out herein.
3.2	THE LICENSOR WILL USE REASONABLE ENDEAVOURS TO REMEDY ANY ERRORS IN THE  SOFTWARE WHICH ARE NOTIFIED TO IT. THE LICENSOR DOES NOT WARRANT THAT THE SOFTWARE WILL MEET YOUR REQUIREMENTS OR THAT ITS OPERATION WILL BE UNINTERRUPTED OR ERROR-FREE. LICENSOR WILL HAVE NO OBLIGATION TO YOU FOR DEFECTS WHICH RESULT, IN WHOLE OR IN PART, FROM YOUR FAULT OR NEGLIGENCE OR THAT OF ANYONE CLAIMING THROUGH YOU OR ON YOUR BEHALF, OR FROM MODIFICATIONS MADE TO THE SOFTWARE OTHER THAN BY LICENSOR OR WITH LICENSOR'S APPROVAL, OR IMPROPER OR UNAUTHORISED USE OF THE SOFTWARE (INCLUDING THE COMBINATION, OPERATION OR USE OF THE SOFTWARE WITH EQUIPMENT, DATA, SOFTWARE OR OTHER PRODUCTS NOT SUPPLIED BY LICENSOR OR APPROVED BY LICENSOR FOR OPERATION OR USE WITH THE SOFTWARE), OR USE OF THE SOFTWARE IN A MANNER FOR WHICH IT WAS NOT DESIGNED, OR BY CAUSES EXTERNAL TO THE SOFTWARE.
3.3	TO THE EXTENT PERMITTED BY LAW, LICENSOR EXCLUDES ALL REPRESENTATIONS, WARRANTIES, CONDITIONS AND OTHER TERMS NOT EXPRESSLY STATED IN THIS AGREEMENT INCLUDING THE CONDITIONS IMPLIED BY LAW OF SATISFACTORY QUALITY AND FITNESS FOR PURPOSES.
3.4	Subject to paragraph 3.5, Licensor's liability to you or any third party, whether in contract, delict, tort (including negligence) or otherwise, shall not exceed the aggregate fees paid by you in accordance with Clause 2.1 above in the calendar year to which the relevant claim relates.  Subject to paragraph 3.5, in no event will Licensor be liable to you for any indirect or consequential damages or losses, or any loss of profit or loss of data.
3.5	Nothing in this Agreement shall have the effect of excluding or limiting Licensor's liability for fraud or for death or personal injury caused by Licensor's negligence, or any other liability if and to the extent that the same may not be excluded or limited as a matter of law.
3.6	You agree that you shall comply and shall procure that each of your employees shall in using the Software comply in all respects and at all times with the Data Protection Act 1998 and any successor thereto.
4	TERMINATION
4.1	This licence and your right to use the Software automatically terminates if you:
4.1.1	commit a material breach of this Agreement and fail to remedy the same after receiving not less than 14 days' notice so to do from the Licensor;
4.1.2	become bankrupt or insolvent or have a liquidator or receiver appointed over any of your assets; or
4.1.3	transfer or permit access to the Software to another party other than in accordance with this Agreement.
4.2	On termination of this Agreement, you shall destroy all copies of the Software and accompanying documentation in your possession or control and cease immediately to use the Software.
5	CONFIDENTIAL INFORMATION
5.1	All information, data, drawings, specifications, documentation, software listings, source or object code which the Licensor may have imparted and may from time to time impart to you relating to the Software (other than the ideas and principles which underlie the Software) is proprietary and confidential.  The Licensee hereby agrees that it shall use the same solely in accordance with the provisions of this Agreement and that it shall not at any time during or after expiry or termination of this Agreement, disclose the same, whether directly or indirectly, to any third party without the Licensor's prior written consent.
5.2	Subject only to the specific, limited provisions of Clause 5.1 above, you further agree that you shall not yourself or through any subsidiary, agent or third party use such confidential information to copy, reproduce, translate, adapt, vary, modify, decompile, disassemble or reverse engineer the Software nor shall you sell, lease, license, sub-license or otherwise deal with the Software or any part or parts or variations, modifications, copies, releases, versions or enhancements thereof or have any software or other program written or developed for yourself based on any confidential information supplied to you by the Licensor.
5.3	The foregoing provisions shall not prevent the disclosure or use by you of any information, which is or hereafter becomes, through no fault of the Licensor, public knowledge or, to the extent permitted by law.
6	FORCE MAJEURE
The Licensor shall be under no liability to you in respect of anything which, apart from this provision, may constitute breach of this Agreement arising by reason of any matter outside the reasonable control of the Licensor, including any power interruptions or failures of or interruptions to any communications equipment, software or hardware.
7	ASSIGNMENT
You shall not assign or otherwise transfer all or any part of the Software or this Agreement without the prior written consent of the Licensor.
8	WAIVER
Failure or neglect by either party to enforce at any time any of the provisions hereof shall not be construed nor shall be deemed to be a waiver of that party's rights hereunder nor in any way affect the validity of the whole or any part of this Agreement nor prejudice that party's rights to take subsequent action.
9	HEADINGS
The headings of the terms and conditions herein contained are inserted for convenience of reference only and are not intended to be part of or to affect the meaning or interpretation of any of the terms and conditions of this Agreement.
10	COMMUNICATIONS
1-Ecommerce Ltd contacts users to advise of system status and developments, you may opt out of our mailing list using the links provided in our communications, or, if these have been omitted for any reason, send an e-mail to removes@1-ecommerce.com. 1-Ecommerce Ltd does it's utmost to ensure that unsolicited e-mail is not sent by us, however we rely on you to inform us of your wish to stop receiving our updates. In agreeing to this license you accept that you will not request any 1-ecommerce.com e-mail address or IP address be added to a spam blacklist held by your, or any other, ISP.
11	GENERAL
11.1	This Agreement will be governed by and construed in accordance with the laws of England and Wales whose courts shall have non-exclusive jurisdiction over all disputes which may arise between us.
11.2	The unenforceability or invalidity of any party of this Agreement will not affect the enforceability or validity of any remaining party.</textarea>
</p>

<hr />

<form method="get" action="install.php" name="license" onsubmit="return sf_checkAgree()">
<div align="center">
<table border="0">
<tr>
<td>

<p><b>Do you agree to the License agreement and all the terms incorporated therein?</b> <font color="#FF0000"><i>(required)</i></font></b></p>

<p align="center">
<input type="hidden" name="agree" value="YES" />
<input type="button" onclick="javascript:parent.location='index.php'" value="NO, I DO NOT AGREE (Cancel setup)" class="orangebuttonsec" onmouseover="sf_btn(this,'orangebuttonsecover');" onmouseout="sf_btn(this,'orangebuttonsec');" />
&nbsp;
<input type="submit" value="YES, I AGREE (Click to continue)" class="orangebutton" onmouseover="sf_btn(this,'orangebuttonover');" onmouseout="sf_btn(this,'orangebutton');" />
</p>

</td>
</tr>
</table>
</div>
</form>

		</td>
	</tr>
	</table>
    </div>

<?php
    sf_iFooter();
} // End sf_iStart()


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
<h1 class="bannername">1-Ecommerce</h1>
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
    <p>&nbsp;</p></td>
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


/*
This function is from http://www.php.net/is_writable
and is a work-around for IIS bug which returns files as
writable by PHP when in fact they are not.
*/
function is__writable($path) {
//will work in despite of Windows ACLs bug
//NOTE: use a trailing slash for folders!!!
//see http://bugs.php.net/bug.php?id=27609
//see http://bugs.php.net/bug.php?id=30931

    if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
        return is__writable($path.uniqid(mt_rand()).'.tmp');
    else if (is_dir($path))
        return is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = @fopen($path, 'a');
    if ($f===false)
        return false;
    fclose($f);
    if (!$rm)
        unlink($path);
    return true;
}

function check_folders($folder, $correct_this){
	//strip off the '../' for error messaging
	$folder_name = str_replace("../", "", $folder);
	?>
	<tr>
    <td class="admin_gray"><b><?php echo $folder_name; ?> directory</b><br />Must exist and be writable by the script</td>
    <td class="admin_gray" valign="middle" nowrap="nowrap">
    <?php
    $mypassed=1;
    
    if (is_dir($folder)) {
		@chmod($folder, 0777);
		echo '<b><font color="#008000">Exists</font>, ';
		if (is__writable($folder)){
			echo '<font color="#008000">Writable</font></b>';
			$mypassed=1;
		} else{
			echo '<font color="#FF0000">Not writable</font></b>';
			$mypassed=2;
		}
    }else{
        $mypassed=0;
        echo '<b><font color="#FF0000">Not uploaded</font>, <font color="#FF0000">Not writable</font></b>';
    }
    ?>
    </td>
    <td class="admin_gray" valign="middle">
    <?php
    if ($mypassed==1){
        echo '<font color="#008000"><b>Passed</b></font>';
    }elseif ($mypassed==2){
        $_SESSION['all_passed']=0;
        echo '<font color="#FF0000"><b>Failed</b></font>';
        $correct_this[]='Make sure the <b>' . $folder_name . '</b> directory is writable: on Linux chmod it to 777 or rwxrwxrwx, on Windows (IIS) make sure IUSR account has modify/read/write permissions';
    }else{
        $_SESSION['all_passed']=0;
        echo '<font color="#FF0000"><b>Failed</b></font>';
        $correct_this[]='The <b>' . $folder_name . '</b> directory does not exist. Please remove all installed folders/files, download 1-Ecommerce software again and try to re-install. If this fails again then please contact 1-Ecommerce.';
    }
    ?>
    </td>
    </tr>
    <?php
	return $correct_this;
}
?>
