<html>
<head>
	<title>Visual Designer</title>
	<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
    
    <script langauge="javascript">
	function post_value(){
	opener.document.<?php echo $_GET['form']?>.<?php echo $_GET['field']; ?>.value = CKEDITOR.instances.editor1.getData();
	self.close();
	}
	</script>
    
</head>
<body onLoad="document.frm.editor1.value=opener.document.<?php echo $_GET['form']?>.<?php echo $_GET['field']; ?>.value">
	<form name="frm" method=post action=''>
		<p>
			My Editor:<br />
			<textarea id="editor1" name="editor1"></textarea>
			<script type="text/javascript">
				CKEDITOR.replace( 'editor1',
				{
				filebrowserBrowseUrl: 'upload2.php',
				filebrowserImageBrowseUrl: 'upload2.php',
				filebrowserFlashBrowseUrl : 'upload2.php',
				filebrowserWindowWidth: '1025',
    			filebrowserWindowHeight: '640'
				//filebrowserUploadUrl: 'upload.php',
				//filebrowserImageUploadUrl: 'upload.php',
				//filebrowserFlashUploadUrl : 'upload.php'
				
				//ORIGINAL SETTINGS
				//filebrowserBrowseUrl : '../ckeditor/ckfinder/ckfinder.html',
				//filebrowserImageBrowseUrl : '../ckeditor/ckfinder/ckfinder.html?type=Images',
				//filebrowserFlashBrowseUrl : '../ckeditor/ckfinder/ckfinder.html?type=Flash',
				//filebrowserUploadUrl : '../ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
				//filebrowserImageUploadUrl : '../ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
				//filebrowserFlashUploadUrl : '../ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
				
				}
				);
			</script>
		</p>
		<p>
			<input type="submit" onClick="post_value();" />
		</p>
	</form>
</body>
</html>