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

include_once("includes/header_admin.php");
?>
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
    <form action="../_cms/upload_file.php" method="post" enctype="multipart/form-data">
        <label for="file">Filename:</label>
        <input name="file" type="file" id="file" size="50">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <input type="submit" name="submit" value=" -- Upload Image -- " class="upload-button">
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

<?php
  include_once("includes/footer_admin.php");
?>
