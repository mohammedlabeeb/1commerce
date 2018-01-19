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
#er .rd{color:#F00}
</style>
<div id="er">
<h1>Form Error</h1>

The error message returned was:<br /><br />
<div class="rd">
<?php
  if(isset($_GET['prob'])) {
      echo base64_decode(urldecode($_GET['prob']));
  }
?>
</div>
<br /><br />
<b>Please go back and try again.....</b>
</div>
</body>
</html>