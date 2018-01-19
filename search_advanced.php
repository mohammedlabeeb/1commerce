<?php
require_once("includes/session.php");
include_once("includes/masterinclude.php");

$information = getInformationPage("Search");
$preferences = getPreferences();
$pageTitle = "Search";
$top_level="0"; $infopagename="";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;
if(!isset($_POST['search'])){$_POST['search'] = "";}

include_once("includes/header.php");

?>
<!-- category.inc -->


<div class="body-content-info">


	<h1>Search Results</h1>

	<div class="search-message">
	<?php
                //$name = $_GET['page'];
                $information = getInformationPage("Search");
                echo html_entity_decode($information->IN_DATA, ENT_QUOTES);
        
        ?> 
	</div>	
    <?php
	//scan search string for multiple search strings each separated by "/"
	$search = $_POST['search'];
	$fstart = 0; $fend = 999; $products_found = array();
	while($fend > 0){
		$fend = strpos($search, "/", $fstart);
		if($fend > 0){
			$searchString = substr($search, $fstart, ($fend - $fstart));
		}else{
			$searchString = substr($search, $fstart, strlen($search) - $fstart);
		}
		$products = Search_For_Advanced($searchString);
		
		//scan database against the current searchString then add all products found to the products_found array
		if(count($products) > 0){
			$last_product = "";
			foreach($products as $p){
				//knock out any duplicate entries caused by mysql joining tables together
				if($p->PR_PRODUCT != $last_product){
					$products_found[] = $p->PR_PRODUCT;
				}
				$last_product = $p->PR_PRODUCT;		
			}
		}
		$fstart = $fend + 1;
	}
	//sort the products_found array
	asort($products_found);
	echo "<ol>";
	//now scan down the products_found array, read the product found and display it
	$last_product = "";
	foreach($products_found as $pf){
		if($pf != $last_product){
			//ignore duplicate entries
			$p = getProductDetails($pf);
			
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
				"		<li class=\"search-li\">
						<span><a class=\"thumbnail-search\" href=\"" . $link . "\"><img src=\"" . $imagePathProd . "\" alt=\"" . $p->PR_IMAGE_ALT . "\" height=\"100\" /></a></span>				
					<a class=\"thumbnail-search\" href=\"" . $link . "\">". html_entity_decode($p->PR_NAME, ENT_QUOTES) . "</a>
					<br />" . html_entity_decode($p->PR_DESC_SHORT, ENT_QUOTES) . "</li>" . PHP_EOL;
			$last_product = $p->PR_PRODUCT;
			
		}
		$last_product = $pf;
	}
	echo "</ol>";
    ?>

	


    

   



<?php
  include_once("includes/footer.php");
?>
