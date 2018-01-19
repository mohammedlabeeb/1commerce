<?php
include_once("includes/session.php");
confirm_logged_in();
include_once("../includes/masterinclude.php");

$message = "";
$logincount = 1;
$new = 1;
$errors = 0;
$errors_array = array();
$scrolltobottom = "";

//initialise screen fields
$selected_member = "";
$id = "";
$username = ""; $username_original = "";
$password = ""; $password_original = "";
$password_test = "";
$title = "MR"; $first_name = ""; $last_name = ""; $company_name = "";
$address1 = ""; $address2 = ""; $town = ""; $county = ""; $country = ""; $postcode = ""; $phone = ""; $mobile = ""; $email = "";
$member_confirmed = "N";
$ast_first = 0; $ast_last = 0; $ast_company = 0; $ast_add1 = 0; $ast_add2 = 0; $ast_town = 0; $ast_county = 0; $ast_country = 0; $ast_post = 0; $ast_phone = 0;
$ast_mobile = 0; $ast_email = 0;
$ast_user = 0; $ast_pass = 0; $ast_passconf = 0;
if(isset($_GET['fdate']) and isset($_GET['tdate'])){
	//this page is now being returned to from the amend_review.php page and as such is being passed back it's original search criteria which it should now default to
	$_REQUEST['from_date'] = $_GET['fdate']; $_REQUEST['to_date'] = $_GET['tdate'];
}

if(isset($_REQUEST['from_date']) and isset($_REQUEST['to_date'])){
	//date is returned as yyyy-mm-dd
	$from_date = $_REQUEST['from_date'];
	$strDate = explode("-", $from_date);
	$fday = $strDate[2]; $fmonth = $strDate[1]; $fyear = $strDate[0];
	$to_date = $_REQUEST['to_date'];
	$strDate = explode("-", $to_date);
	$tday = $strDate[2]; $tmonth = $strDate[1]; $tyear = $strDate[0];
}else{
	//default date
	$today = strftime("%Y-%m-%d", time()); $amonthago = strftime("%Y-%m-%d", strtotime('-30 days'));
	$strDate = explode("-", $amonthago);
	$fday = $strDate[2]; $fmonth = $strDate[1]; $fyear = $strDate[0];
	$strDate = explode("-", $today);
	$tday = $strDate[2]; $tmonth = $strDate[1]; $tyear = $strDate[0];
	$from_date = $amonthago; $to_date = $today;
}

//if(isset($_POST['SEARCH_DATA']) and $_POST['SEARCH_DATA'] != ""){$search_data = $_POST['SEARCH_DATA'];}else{$search_data = "";}
if(isset($_POST['SEARCH_DATA']) or isset($_GET['search'])){
	if(isset($_POST['SEARCH_DATA'])){
		$search_data = $_POST['SEARCH_DATA'];
	}else{
		$search_data = $_GET['search'];
	}	
}else{
	$search_data = "";
}

if(isset($_POST['PUBLISHED_YN']) or isset($_GET['published'])){
	//show either all orderlines or just those not yet published
	if(isset($_POST['PUBLISHED_YN'])){$published = $_POST['PUBLISHED_YN'];}else{$published = $_GET['published'];}
}else{
	//must be first entry to page so default to "ALL"
	$published = "ALL";
}

if(isset($_POST['UPDATE_STATUS'])){
	//validate all fields first
	$message = "";
	for($i = 1; $i <= $_POST['ROW_COUNT']; $i++){
		if ($_POST['PUBLISHED_' . $i] != "N" and $_POST['PUBLISHED_' . $i] != "Y"){
			$message .= "Printed status must be set to 'Y' or 'N'" . "<br/>";
			$warning = "red";
		}
	}
	if($message == ""){
		$rows_written = 0;
		for($i = 1; $i <= $_POST['ROW_COUNT']; $i++){
			//for each line of the table if the published status has changed then amend it
			if($_POST['PUBLISHED_' . $i] != $_POST['PUBLISHED_ORIGINAL_' . $i]){
				$fields = array("rv_id"=>$_POST['ID_' . $i], "rv_published"=>$_POST['PUBLISHED_' . $i]);
				$rows = Rewrite_Review_Status($fields);
				if($rows == 1){
					$rows_written ++;	
				}else{
					$message .= "WARNING ! ! ! - Published status amendment failure (Line Number" . $i. " above) - Please contact Shopfitter!";
					$warning = "red";
				}
			}
		}
		if ($message == "" and $rows_written == 0){
			$message = "No rows updated";
			$warning = "red";
		}
		if ($message == "" and $rows_written > 0){
			$message = "{$rows_written} rows successfully updated";
			$warning = "green";	
		}
	}else{
		//refresh screen with original details
	}
}

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Amend Categories";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
//convert 16-02-2013 to 2013-02-16 00:00:00
//echo "DATES=" . $from_date . " to " . $to_date;
$reviews = Search_For_Reviews(pack_calendar_date($from_date, "from"), pack_calendar_date($to_date, "to"), $search_data, $published);
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
    <div class="list_reviews" >
    	<form id="print_status" name="print_status" action="/_cms/maintain_reviews.php" method="post" onsubmit="return amend_review_validation();">
        	<?php require_once('calendar/classes/tc_calendar.php'); ?>
            <table id="approve_reviews_search" align="left" border="0" cellpadding="2" cellspacing="5">
                <tr>
                    <td class="approve_reviews_search-td">From Date:</td>
                    <td>
                    	<?php
                        //FROM DATE ---------------------------------instantiate class and set properties 
                        $myCalendar = new tc_calendar("from_date", true);
                        $myCalendar->setIcon("calendar/images/iconCalendar.gif");
                        $myCalendar->setDate($fday, $fmonth, $fyear);
                        $myCalendar->setPath('calendar'); //set path to calendar_form.php
						
                        //output the calendar
                        $myCalendar->writeScript();
						?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td>To Date:</td>
                    <td>
						<?php
                        //TO DATE -----------------------------------instantiate class and set properties 
                        $myCalendar = new tc_calendar("to_date", true);
                        $myCalendar->setIcon("calendar/images/iconCalendar.gif");
                        $myCalendar->setDate($tday, $tmonth, $tyear);
                        $myCalendar->setPath('calendar'); //set path to calendar_form.php
						
                        //output the calendar
                        $myCalendar->writeScript();  
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                
                <!--- SEARCHBOXES ------------------------------------------------------------------------------------------------------>
                <tr>
                  <td>Search for: <a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">Type the name or a key word to search for the review you want to work on<br /><br />To select from all reviews place the mouse cursor in the search field with no other text and click search<br /><br /></span><span class=\"bottom\"></span></span>" : "") ?></a></td>
                    <td><Input name="SEARCH_DATA" type="text" size="64" value="<?php echo $search_data ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <!--- END OF SEARCHBOXES ------------------------------------------------------------------------------------------>
                 
                <tr>
                	<td colspan="2">
                        <label>All Reviews:<input type="radio" name="PUBLISHED_YN" value="ALL" onchange="submit();" <?php echo ($published == "ALL" ? "checked" : "")?> ></label>
                        <label>Not Yet Published<input type="radio" name="PUBLISHED_YN" value="N" onchange="submit();" <?php echo ($published == "N" ? "checked" : "")?>></label>
            		</td>
            	</tr>
                <tr>
                	<td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" align="left" >
                        <input class="search-button" name="SEARCH" type="submit" value="Search" />
                    </td>
                </tr>
                <tr>
                	<td colspan="2" class="td-sep">&nbsp;</td>
                </tr>
            </table>
<!--            <table id="approve_reviews" align="left" border="0" cellpadding="2" cellspacing="10" width="550px" style="text-align: left;">
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
                    <th style="text-align: right; padding-right: 36px;">
                       <label>Publish</label><br/>
                    </th>
                </tr>
                <?php $cntr1 = 0; ?>
                <?php foreach($reviews as $r): ?>
                    <?php 
                        $cntr1 ++;
                        $original = $r->RV_PUBLISHED;
						$product = getProductDetails($r->RV_PRODUCT);
                    ?>
                    <tr>
                        <td class="review_line">
                        	<span><?php echo $r->RV_PRODUCT; ?></span>
                        </td>
                        <td class="review_line">
                        	<span><?php echo html_entity_decode($product->PR_NAME, ENT_QUOTES); ?></span>
                        </td>
                        <td class="review_line">
                        	<span><?php echo $r->RV_ORDER; ?></span>
                        </td>
                        <td class="review_line" style="text-align: right; padding-right: 36px;" >
                            <input id="PUBLISHED_<?php echo $cntr1 ?>" name="PUBLISHED_<?php echo $cntr1 ?>" type="text" class="review" SIZE="1" onblur="this.value=this.value.toUpperCase()" value="<?php echo $r->RV_PUBLISHED;  ?>" />
                            <input name="PUBLISHED_ORIGINAL_<?php echo $cntr1 ?>" type="hidden" value="<?php echo $original ?>" />
                            <input name="ID_<?php echo $cntr1 ?>" type="hidden" value="<?php echo $r->RV_ID ?>" />
                            <input name="ROW_COUNT" type="hidden" value="<?php echo $cntr1; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                        	<?php
							echo "<a href=\"/_cms/amend_review.php?id=" . $r->RV_ID . "&fdate=" . $from_date . "&tdate=" . $to_date . "&search=" . $search_data . "&published=" . $published .  "\">";
								$title = $r->RV_TITLE;
								//if(strlen($title) > 40){$title = substr($title, 0, 40) . ".....";}
								echo "<p class=\"review_header\"><img src=\"/theme/theme-images/star_" . ($r->RV_RATING * 10) . ".png\" />&nbsp;";
								echo "<b>" . $title . "</b>&nbsp;&nbsp;&nbsp;&nbsp;" . unpack_review_date($r->RV_DATE) . "</p>";
								echo "<p class=\"review_subheader\">By <strong>" . $r->RV_AUTHOR . "</strong> (" . $r->RV_TOWN . ", " . $r->RV_COUNTRY . ")</p>";
								echo "<div id=\"review_text\">";
									echo "<p class=\"review_text\">" . $r->RV_TEXT . "</p>";
								echo "</div>";
								if($r->RV_REPLY != ""){
									echo "<label style=\"margin-left: 10px;\">Reply:<br/></label>";
									echo "<div id=\"reply_text\">";
										echo "<p class=\"reply_text\">" . $r->RV_REPLY . "</p>";
									echo "</div>";
								}
							echo "</a>";
							?>
                        </td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="4" class="td-sep">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4">
                    	<input class="update-button" name="UPDATE_STATUS" type="submit" value="Update Publish Status" />
                    </td>
                <tr>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" align="left" ><label id="MESSAGE" class="<?php echo $warning ?>" ><?php echo $message ?></label></td>
                </tr>
            </table> 
        </form>   
	</div>
<?php
  include_once("includes/footer_admin.php");
?>

