<?php
include_once("includes/session.php");
confirm_logged_in();
//include_once("includes/functions_admin.php");
include_once("../includes/masterinclude.php");

$message = "";
$scrolltobottom = "";


$preferences = getPreferences();

//note this will also refresh the page after amending it
$pageTitle = "Site Admin: Image Uploaded";
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
	<h1>Image Uploaded</h1>
	<br/>
	<div class="start-page-content">
      	<p>&nbsp;</p>
      	<?php
$allowedExts = array("jpg", "jpeg", "gif", "png");
$extension = end(explode(".", $_FILES["file"]["name"]));
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/png")
|| ($_FILES["file"]["type"] == "image/pjpeg"))
&& ($_FILES["file"]["size"] < 2000000)
&& in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "Stored in: " . $_FILES["file"]["tmp_name"];
    }
  }
else
  {
  echo "Invalid file";
  }
?> 

<?php
$allowedExts = array("jpg", "jpeg", "gif", "png");
$extension = end(explode(".", $_FILES["file"]["name"]));
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/png")
|| ($_FILES["file"]["type"] == "image/pjpeg"))
&& ($_FILES["file"]["size"] < 2000000)
&& in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

    if (file_exists("../images/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists: ";
	  echo "<p>Image URL: <strong>" . $preferences->PREF_SHOPURL . "/images/" . $_FILES["file"]["name"] . "</strong></p>";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "../images/" . $_FILES["file"]["name"]);
      echo "Stored in: " . "images/" . $_FILES["file"]["name"];
	  echo "<p>&nbsp;</p>";
	  echo "<p>Image URL: <strong>" . $preferences->PREF_SHOPURL . "/images/" . $_FILES["file"]["name"] . "</strong></p>";
      }
    }
  }
else
  {
  echo "Invalid file";
  }
?> 
        <p>&nbsp;</p>
    </div>
      <div class="start-page-content">
      	<p>Insert the Image URL above in the URL field on the image properties box when using the visual designer.</p>
        <p>&nbsp;</p>
      	<h3><a href="javascript: history.go(-1)">Upload another image</a></h3>

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
