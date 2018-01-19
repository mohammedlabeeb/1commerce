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

include_once("includes/header_admin.php");
?>
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
<?php
  include_once("includes/footer_admin.php");
?>
