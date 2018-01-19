<?php
require_once("includes/session.php");
include_once("includes/masterinclude.php");

$preferences = getPreferences();
$pageTitle = "Search";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;
if(!isset($_POST['search'])){$_POST['search'] = "";}

include_once("includes/header.php");

?>
<!-- category.inc -->


<div class="body-content-info">


	<h1>Search Results</h1>

	<div class="search-message">
	
	</div>	
    <?php
    $products = Search_For($_POST['search']);
	if(count($products) > 0){
		echo "<ol>";
		foreach($products as $p){
		
		$imagePathProd = "";
		if(strlen($p->PR_IMAGE_FOLDER) > 0){$imagePathProd = $p->PR_IMAGE_FOLDER . "/";}
		$imagePathProd .= $p->PR_IMAGE;
		if(strlen($imagePathProd) == 0){
			$imagePathProd = "/images/thumbnoimage.jpg";
		}else{
			$imagePathProd = "/images/" . $imagePathProd;
		}
			
		
		
			$tree = getProductTree($p->PR_PRODUCT);
			$link = "/" . urlencode(html_entity_decode($p->PR_NAME, ENT_QUOTES)) . "/" . $tree . "/" . $p->PR_PRODUCT . ".htm";
			echo
				"<li class=\"search-li\">
					<a class=\"thumbnail-search\" href=\"" . $link . "\">". $p->PR_PRODUCT . " - " . html_entity_decode($p->PR_NAME, ENT_QUOTES) . "</a><br />" .  $p->PR_DESC_SHORT .
				 		"</br>
						<span>
							<a class=\"thumbnail-search\" href=\"" . $link . "\"><img src=\"" . $imagePathProd . "\" alt=\"" . $p->PR_IMAGE_ALT . "\" height=\"100\" /></a>
				 		</span>
				</li>";
			
		}
		echo "</ol>";
	}else{
		// no matching products found
		
		
		//<li class="search-li"><a class="thumbnail-search" href="link-address.htm">PRODUCT NAME<span><img src="images/PRAA100.jpg" alt="PRODUCT NAME"><br>PRODUCT NAME</span></a><br>PRODUCT DESCRIPTION</li>
		
		
		
	}
    
    ?>
	<div class="line"></div>
	<div class="search-right"><?php echo count($products) ?> product(s) found</div>
	<p>&nbsp;</p>

	<p class="spacer">&nbsp;</p>
	


    

   



<?php
  include_once("includes/footer.php");
?>
