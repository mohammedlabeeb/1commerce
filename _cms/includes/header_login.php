
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $preferences->PREF_SHOPNAME ?> Site Administration: Login</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta name="author" content="1 e-commerce" />
	<meta name="copyright" content="Copyright (c) 1 e-commerce" />
    <link href="/_cms/common/adminstyle.css" rel="stylesheet" type="text/css">
    <script src="/common/language-en.js" type="text/javascript"></script>
    <script src="/common/sfcart.js" type="text/javascript"></script>
    <script src="/common/shopfitter_core.js" type="text/javascript"></script>
    <script src="/common/products.js" type="text/javascript"></script>
    <script src="/common/general.js" type="text/javascript"></script>
    
    <!--[if lte IE 7]>
            <style type="text/css">
    .search input.box {
        background: url(images/ks-searchbox-ie.jpg) no-repeat right bottom; /* Unique Input Box background image for IE, must be aligned to the right*/
    }
        </style>
        
    <![endif]-->
    <script type="text/javascript">
	function MM_jumpMenu(targ,selObj,restore){ //v3.0
		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
		if (restore) selObj.selectedIndex=0;
	}	
	</script>
    
    

</head>
<body>
<div class="body-wrapper_admin">
		<div class="banner-style">
			<div class="top-logo">
            	<h1 class="sitename"><?php echo $preferences->PREF_SHOPNAME ?>
				</h1>
            </div>
			<div class="top-cart_admin">
            	<h2>Site Administration - <span class="site_admin">Logged in as <span class="warning_green"><?php echo $_SESSION['username']?></span></span></h2>
            </div>
		</div>	
		<div id="container_admin">


