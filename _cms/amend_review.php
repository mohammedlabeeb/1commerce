<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$errors = 0;
$errors_array = array();
$rv_id = ""; $rv_product = ""; $rv_order = ""; $rv_author = ""; $rv_town = ""; $rv_country = ""; $rv_date = 0;
$rv_rating = ""; $rv_title = ""; $rv_text = ""; $rv_reply = ""; $rv_date_reply = ""; $rv_published = ""; $rv_date_publish = "";
$rv_published_original = ""; $rv_reply_original = "";
$scrolltobottom = "";

if(isset($_GET['id'])){
	//first time through - having been swapped to by maintain_reviews.php
	$from_date = $_GET['fdate']; $to_date = $_GET['tdate']; $published = $_GET['published']; $search_data = $_GET['search'];
	$review = Get_Review_By_Id($_GET['id']);
	$rv_id = $review->RV_ID; $rv_product = $review->RV_PRODUCT; $rv_order = $review->RV_ORDER;
	$rv_author = $review->RV_AUTHOR; $rv_town = $review->RV_TOWN; $rv_country = $review->RV_COUNTRY; $rv_date = $review->RV_DATE;
	$rv_rating = $review->RV_RATING; $rv_title = $review->RV_TITLE; $rv_text = $review->RV_TEXT; $rv_reply = $review->RV_REPLY;
	$rv_date_reply = $review->RV_DATE_REPLY; $rv_published = $review->RV_PUBLISHED; $rv_date_publish = $review->RV_DATE_PUBLISH;
	$rv_published_original = $review->RV_PUBLISHED; $rv_reply_original = $review->RV_REPLY;
}

if(isset($_POST['UPDATE_REVIEW'])){
	//field validation now carried out within _cms/common/reviews_validation.js
	$message = "";
	if ($_POST['RV_PUBLISHED'] != "N" and $_POST['RV_PUBLISHED'] != "Y"){
		$message .= "Printed status must be set to 'Y' or 'N'" . "<br/>";
		$warning = "red";
	}
	if($message == ""){
		$rows_written = 0;					
		$fields = array("rv_id"=>$_POST['RV_ID'], "rv_product"=>$_POST['RV_PRODUCT'], "rv_order"=>$_POST['RV_ORDER'], "rv_author"=>$_POST['RV_AUTHOR'], 
				"rv_town"=>$_POST['RV_TOWN'], "rv_country"=>$_POST['RV_COUNTRY'], "rv_date"=>$_POST['RV_DATE'],
				"rv_rating"=>$_POST['RV_RATING'], "rv_title"=>$_POST['RV_TITLE'], "rv_text"=>$_POST['RV_TEXT'],
				"rv_reply"=>$_POST['RV_REPLY'], "rv_reply_original"=>$_POST['RV_REPLY_ORIGINAL'], "rv_date_reply"=>$_POST['RV_DATE_REPLY'],
				"rv_published"=>$_POST['RV_PUBLISHED'], "rv_published_original"=>$_POST['RV_PUBLISHED_ORIGINAL'], "rv_date_publish"=>$_POST['RV_DATE_PUBLISH']);				
		$rows = Rewrite_Review($fields);
		if($rows == 1){
			$rows_written ++;
		}
		if ($message == "" and $rows_written == 0){
			$message = "No rows updated";
			$warning = "orange";
		}
		if ($message == "" and $rows_written > 0){
			$message = "{$rows_written} rows successfully updated";
			$warning = "green";	
		}
		//now refresh screen with the updated details
		$review = Get_Review_By_Id($_POST['RV_ID']);
		$rv_id = $review->RV_ID; $rv_product = $review->RV_PRODUCT; $rv_order = $review->RV_ORDER;
		$rv_author = $review->RV_AUTHOR; $rv_town = $review->RV_TOWN; $rv_country = $review->RV_COUNTRY; $rv_date = $review->RV_DATE;
		$rv_rating = $review->RV_RATING; $rv_title = $review->RV_TITLE; $rv_text = $review->RV_TEXT; $rv_reply = $review->RV_REPLY;
		$rv_date_reply = $review->RV_DATE_REPLY; $rv_published = $review->RV_PUBLISHED; $rv_date_publish = $review->RV_DATE_PUBLISH;
		$rv_published_original = $rv_published; $rv_reply_original = $rv_reply;
	}else{
		//refresh screen with original details
		$rv_id = $_POST['RV_ID']; $rv_product = $_POST['RV_PRODUCT']; $rv_order = $_POST['RV_ORDER'];
		$rv_author = $_POST['RV_AUTHOR']; $rv_town = $_POST['RV_TOWN']; $rv_country = $_POST['RV_COUNTRY']; $rv_date = $_POST['RV_DATE'];
		$rv_rating = $_POST['RV_RATING']; $rv_title = $_POST['RV_TITLE']; $rv_text = $_POST['RV_TEXT']; $rv_reply = $_POST['RV_REPLY'];
		$rv_date_reply = $_POST['RV_DATE_REPLY']; $rv_published = $_POST['RV_PUBLISHED']; $rv_date_publish = $_POST['RV_DATE_PUBLISH'];
		$rv_published_original = $rv_published; $rv_reply_original = $rv_reply;
	}
	$from_date = $_POST['FROM_DATE']; $to_date = $_POST['TO_DATE']; $published = $_POST['PUBLISHED']; $search_data = $_POST['SEARCH_DATA'];
}

if (isset($_POST['DELETE_REVIEW'])) {
	$rows = Delete_Review($_POST['RV_ID']);
	$message = "";
	if ($rows == 1){
		$message = "Review successfully DELETED";
		$warning = "green";
	}
	if ($rows == 0){
		$message = "WARNING ! ! ! - Review NOT DELETED";
		$warning = "orange";
	}
	if ($rows > 1){
		$message = "ERROR ! ! ! - MORE THAN ONE (" . $rows . ") RECORD DELETED - PLEASE CONTACT SHOPFITTER";
		$warning = "red";
	}
	//initialise all fields
	$rv_id = ""; $rv_product = ""; $rv_order = "";
	$rv_author = ""; $rv_town = ""; $rv_country = ""; $rv_date = "";
	$rv_rating = ""; $rv_title = ""; $rv_text = ""; $rv_reply = "";
	$rv_date_reply = ""; $rv_published = ""; $rv_date_publish = "";
	$rv_published_original = $rv_published; $rv_reply_original = $rv_reply;
	$from_date = $_POST['FROM_DATE']; $to_date = $_POST['TO_DATE']; $published = $_POST['PUBLISHED'];
	if($message != ""){$scrolltobottom = "onLoad=\"scrollTo(0,2000)\" ";}
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Amend Categories";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;
$return_to = "/_cms/maintain_reviews.php?fdate=" . $from_date . "&tdate=" . $to_date . "&search=" . $search_data . "&published=" . $published;

include_once("includes/header_admin.php");

?>
<div class="body-indexcontent_admin" >
	<div class="admin">
        <br/>
        <h1>List Reviews - List All Reviews</h1>
        <p><br /><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"../images/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">All Customer Reviews are listed here<br /><br />Details include Author, Location, Previous Order No. Rating and the Review itself/span><span class=\"bottom\"></span></span>" : "") ?></a></p>
    </div>
    <div class="review_errors">
		<?php
        foreach($errors_array as $e){
            echo "<label class=\"" . $warning ."\">" . $e . "</label><br/>";	
        }
        ?>
    </div>
    <div class="amend_review" >
    	<form name="amend_review" action="/_cms/amend_review.php" method="post" onsubmit="return amend_review_validation();">
<!--            <table id="approve_reviews" align="left" border="0" cellpadding="2" cellspacing="10" width="650px" style="text-align: left;">
--> 
 
            <table id="approve_reviews" align="left" border="0" cellpadding="2" cellspacing="10">
				<tr>
                    <th>
                       <label>Product</label><br/>
                    </th>
                    <th>
                       <label>Description</label><br/>
                    </th>
                    <th>
                       <label>Previous Order No.</label><br/>
                    </th>
                    <th>
                       <label>Publish</label><br/>
                    </th>
                </tr>
                <tr>
                    <td class="review_line">
                        <span><?php echo $rv_product; ?></span>
                    </td>
                    <td class="review_line">
                    	<?php $product = getProductDetails($rv_product);?>
                        <span><?php if($product){echo html_entity_decode($product->PR_NAME, ENT_QUOTES);} ?></span>
                    </td>
                    <td class="review_line">
                        <span><?php echo $rv_order; ?></span>
                    </td>
                    <td class="review_line">
                        <input name="RV_PUBLISHED" type="text" class="review" SIZE="1" onblur="this.value=this.value.toUpperCase()" value="<?php echo $rv_published;  ?>" />
                        <input name="RV_PUBLISHED_ORIGINAL" type="hidden" value="<?php echo $rv_published_original ?>" />
                        <input name="RV_ID" type="hidden" value="<?php echo $rv_id ?>" />
                        <input name="RV_PRODUCT" type="hidden" value="<?php echo $rv_product ?>" />
                        <input name="RV_ORDER" type="hidden" value="<?php echo $rv_order ?>" />
                        <input name="RV_AUTHOR" type="hidden" value="<?php echo $rv_author ?>" />
                        <input name="RV_TOWN" type="hidden" value="<?php echo $rv_town ?>" />
                        <input name="RV_COUNTRY" type="hidden" value="<?php echo $rv_country ?>" />
                        <input name="RV_DATE" type="hidden" value="<?php echo $rv_date ?>" />
                        <input name="RV_RATING" type="hidden" value="<?php echo $rv_rating ?>" />
                        <input name="RV_TITLE" type="hidden" value="<?php echo $rv_title ?>" />
                        <input name="RV_TEXT" type="hidden" value="<?php echo $rv_text ?>" />
                        <input name="RV_REPLY" type="hidden" value="<?php echo $rv_reply ?>" />
                        <input name="RV_DATE_REPLY" type="hidden" value="<?php echo $rv_date_reply ?>" />
                        <input name="RV_DATE_PUBLISH" type="hidden" value="<?php echo $rv_date_publish ?>" />
                        <input name="FROM_DATE" type="hidden" value="<?php echo $from_date ?>" />
                        <input name="TO_DATE" type="hidden" value="<?php echo $to_date ?>" />
                        <input name="SEARCH_DATA" type="hidden" value="<?php echo $search_data ?>" />
                        <input name="PUBLISHED" type="hidden" value="<?php echo $published ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <?php
                        $title = $rv_title;
                        //if(strlen($title) > 40){$title = substr($title, 0, 40) . ".....";}
                        echo "<p class=\"review_header\"><img src=\"/theme/theme-images/star_" . ($rv_rating * 10) . ".png\" />&nbsp;";
                        echo "<b>" . $title . "</b>&nbsp;&nbsp;&nbsp;&nbsp;" . unpack_review_date($rv_date) . "</p>";
                        echo "<p class=\"review_subheader\">By <strong>" . $rv_author . "</strong> (" . $rv_town . ", " . $rv_country . ")</p>";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <label>Review Title:</label><br/>
                        <input name="RV_TITLE" class="review" size="56" maxlength="60" value="<?php echo $rv_title ?>" />
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <label>Review Text:</label><br/>
                        <textarea name="RV_TEXT" class="review"><?php echo $rv_text ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <label>Reply Text:</label><br/>
                        <textarea name="RV_REPLY" class="review"><?php echo $rv_reply ?></textarea>
                        <input name="RV_REPLY_ORIGINAL" type="hidden" value="<?php echo $rv_reply_original ?>" />
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4">
                    	<input class="update-button" name="UPDATE_REVIEW" type="submit" value="Update Review Details" />&nbsp;&nbsp;
                        <input class="delete-button" name="DELETE_REVIEW" type="submit" value="Delete Review" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input class="previous-button" name="BACK_TO_MAINTAIN" type="button" value="Return to Listing" onclick="document.location.href='<?php echo $return_to ?>';" />
                    </td>
                <tr>
                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="8" align="left" ><label id="MESSAGE" class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
                </tr>
            </table> 
        </form>   
	</div>
<?php
  include_once("includes/footer_admin.php");
?>

