<?php
error_reporting(0);
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <meta http-equiv="content-language" content="en" />
  <title>Form Error</title>
</head>
<body>
<style>
#er {font-family:arial}
</style>
<div id="er">
<h1>Form Error</h1>

Sorry, there was a problem sending your form submission.
<br /><br />
<div style="padding:10px">
<?php
  if(isset($_SESSION['error_message'])) {
      echo $_SESSION['error_message'];
  }
?>
</div>
<br /><br />
<b>Please <a href="javascript:history.back()">go back and try again</a></b>
</div>
</body>
</html>