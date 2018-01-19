<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$message = "";
$scrolltobottom = "";


$preferences = getPreferences();

//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Image Upload";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $pageTitle ?></title>
    <meta name="description" content="<?php echo $pageMetaDescription ?>" />
	<meta name="keywords" content="<?php echo $pageMetaKeywords ?>" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta name="author" content="1 e-commerce and Shopfitter" />
	<meta name="design" content="1 e-commerce" />
    <link href="/_cms/common/adminstyle.css" rel="stylesheet" type="text/css">
    <link href="/_cms/common/bubble.css" rel="stylesheet" type="text/css">
    <link href="/_cms/calendar/calendar.css" rel="stylesheet" type="text/css" />
	<script src="/common/language-en.js" type="text/javascript"></script>
    <script src="/common/sfcart.js" type="text/javascript"></script>
    <script src="/common/shopfitter_core.js" type="text/javascript"></script>
    <script src="/common/products.js" type="text/javascript"></script>
    <script src="/common/general.js" type="text/javascript"></script>
    <script src="/_cms/calendar/calendar.js" type="text/javascript"></script>
        
    <![endif]-->
    <script type="text/javascript">
	function MM_jumpMenu(targ,selObj,restore){ //v3.0
		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
		if (restore) selObj.selectedIndex=0;
	}
	
	function MM_refreshScreen(targ,selObj){ //v3.0
		eval(targ+".location='"+selObj.value+"'");
	}
	</script>
    <script type="text/javascript">
	<!--
	function scrollToBottom(){
	var scrollH=document.body.scrollHeight;
	var offsetH=document.body.offsetHeight;
	if(scrollH>offsetH) window.scrollTo(0,scrollH);
	else window.scrollTo(0,offsetH);
	}
	//-->
	</script>
    
    <script type="text/javascript" src="/ckeditor/ckeditor.js">
    </script>
</head>

<?php
echo "<body " . $scrolltobottom . ">";
?>

<div class="body-wrapper_admin">
		<div class="banner-style">
			<div class="top-logo">
            	<h1 class="sitename"><?php echo $preferences->PREF_SHOPNAME ?></h1>
            </div>
			<div class="top-cart_admin">
            	<h2>Site Administration - <span class="site_admin">Logged in as <span class="warning_green"><?php echo $_SESSION['username']?></span></span></h2>
            </div>
		</div>
        <div class="topmenu_admin">

		</div>	
<div class="body-indexcontent_admin">
  <div class="admin">
    <br/>
	<h1>Image Upload</h1>
	<br/>
    
    
      <div class="login-box">
        <h2>Image uploader for info pages, category content areas and hotspots using the &quot;edit&quot; button</h2>
        <p>&nbsp;</p>
        <p>Use this image uploader to add pictures into the main images folder so that you can insert them into HTML areas such as the home page, other info pages, hotspots and category top and bottom content areas. These are inserted using the &quot;edit&quot; buttons.</p>
        <p>&nbsp;</p>
        <p>Do not use this uploader for adding pictures to the products (except hotspots) or category images (except top/bottom content).
        <p>&nbsp;</p>       
        <p>&nbsp;</p>
    <form action="../_cms/upload_file2.php" method="post" enctype="multipart/form-data">
        <label for="file">Filename:</label>
        <input name="file" type="file" id="file" size="50">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <input type="submit" name="submit" value=" -- Upload Image -- ">
    </form>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
      </div>		
      <div class="start-page-content">
      	<p>&nbsp;</p>
      	<p>Images uploaded will be placed in the main images folder (<?php echo $preferences->PREF_SHOPURL ?>/images/).</p>
        <p>&nbsp;</p>
        <p>When you add an image in the visual designer you'll need to either enter the file path or browse to the image location.</p>
        <p>&nbsp;</p>
        <p>The file path will be similar to this: <span style="font-weight:bold;"><?php echo $preferences->PREF_SHOPURL ?>/images/&quot;filename.jpg&quot;</span></p>
      </div>


        </div>

	</div>
    <!--- end of container --->
    <div class="footerline_admin">
        <div class="txt-footer_admin">

        </div>

	</div>
	<a name="bottom" id="bottom"></a>
</div>
<!--- end of body-wrapper --->


</body>
</html>

