<?php
/*******************************************************************************
*  Title: Random text script (RanTex)
*  Version: 1.0 @ January 4, 2009
*  Author: Klemen Stirn
*  Website: http://www.phpjunkyard.com
********************************************************************************
*  COPYRIGHT NOTICE
*  Copyright 2009 Klemen Stirn. All Rights Reserved.
*
*  This script may be used and modified free of charge by anyone
*  AS LONG AS COPYRIGHT NOTICES AND ALL THE COMMENTS REMAIN INTACT.
*  By using this code you agree to indemnify Klemen Stirn from any
*  liability that might arise from it's use.
*
*  If you are using this script you are required to place a link
*  to PHPJunkyard on your website. You will find some link suggestions here:
*  http://www.phpjunkyard.com/link2us.php
*
*  Selling the code for this program, in part or full, without prior
*  written consent is expressly forbidden.
*
*  Obtain permission before redistributing this software over the Internet
*  or in any other medium. In all cases copyright and header must remain
*  intact. This Copyright is in full effect in any country that has
*  International Trade Agreements with the United States of America or
*  with the European Union.
*******************************************************************************/

/*******************************************************************************
*  SETTINGS
*
*  See readme.htm file for further instructions!
*******************************************************************************/

/* File, where the random text/quotes are stored one per line */
$settings['text_from_file'] = '';

/*
   If you prefer you can list quotes that RanTex will choose from here.
   In this case set above variable to $settings['text_from_file'] = '';
*/
$settings['quotes'] = array(
'<a href="http://www.1-ecommerce.com/Downloads/0_CAAA016.htm"><span>ecommerce software</span></a>',
'<a href="http://www.1-ecommerce.com/Website+Plans/0_CAAA001.htm"><span>ecommerce websites</span></a>',
'<a href="http://www.1-ecommerce.com/Web+Marketing/0_CAAA035.htm"><span>SEO ecommerce</span></a>',
'<a href="http://www.1-ecommerce.com/Gold+Standard+Website/0_CAAA001/PRAA006.htm"><span>search engine friendly sites</span></a>',
'<a href="http://www.1-ecommerce.com/Download-1E.htm"><span>shopping cart software</span></a>',
'<a href="http://www.1-ecommerce.com/PayPal/0_CAAA008_CAAA009.htm"><span>paypal shopping cart</span></a>',
'<a href="http://www.1-ecommerce.com"><span>shopping cart system</span></a>',
);

/*
   How to display the text?
   0 = raw mode: print the text as it is, when using RanTex as an include
   1 = Javascript mode: when using Javascript to display the quote
*/
$settings['display_type'] = 0;

/* Allow on-the-fly settings override? 0 = NO, 1 = YES */
$settings['allow_otf'] = 1;

/*******************************************************************************
*  DO NOT EDIT BELOW...
*
*  ...or at least make a backup before you do!
*******************************************************************************/

/* Override type? */
if ($settings['allow_otf'] && isset($_GET['type']))
{
	$type = intval($_GET['type']);
}
else
{
	$type = $settings['display_type'];
}

/* Get a list of all text options */
if ($settings['text_from_file'])
{
	$settings['quotes'] = file($settings['text_from_file']);
}

/* If we have any text choose a random one, otherwise show 'No text to choose from' */
if (count($settings['quotes']))
{
	$txt = $settings['quotes'][array_rand($settings['quotes'])];
}
else
{
	$txr = 'No text to choose from';
}

/* Output the image according to the selected type */
if ($type)
{
    /* New lines will break Javascript, remove any and replace them with <br /> */
    $txt = nl2br(trim($txt));
    $txt = str_replace(array("\n","\r"),'',$txt);
	echo 'document.write(\''.addslashes($txt).'\')';
}
else
{
	echo $txt;
}
?>
