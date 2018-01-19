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

$preferences = getPreferences();
//note this will also refresh the page after amending it
$pageTitle = "Site Administration: Amend Categories";
$pageMetaDescription = $preferences->PREF_META_DESC;
$pageMetaKeywords = $preferences->PREF_META_KEYWORDS;

include_once("includes/header_admin.php");
$members = Get_All_Members("ALL");
?>
<div class="body-indexcontent_admin">
	<div class="admin">
        <br/>
        <h1>List Members - List All Members</h1>
        <p><br /><a href="#" class="tt"><?php echo ($preferences->PREF_TOOL_TIPS == "Y" ? "<img src=\"/_cms/csstooltips/q-icon.png\"><span class=\"tooltip\"><span class=\"top\"></span><span class=\"middle\">All trade/discount club members are listed here<br /><br />Details include name, user name, e-mail address and status</span><span class=\"bottom\"></span></span>" : "") ?></a></p>
    </div>
    <div class="member_errors">
		<?php
        foreach($errors_array as $e){
            echo "<label class=\"" . $warning ."\">" . $e . "</label><br/>";	
        }
        ?>
    </div>
    <div class="list_members">
    <table align="left" border="0" cellpadding="2" cellspacing="5" width="90%">
        <tr>
            <td width="30%">
               <label>Name</label><br/>
            </td>
            <td width="20%">
               <label>User Name</label><br/>
            </td>
            <td width="20%">
               <label>email address</label><br/>
            </td>
            <td width="15%">
            	<label>Price Category</label><br/>
            </td>
            <td width="10%">
               <label>Confirmed</label><br/>
            </td>
        </tr>
        <?php
        foreach($members as $m){
        	echo "<tr>";
				echo "<td>";
					echo "<a href=\"/_cms/amend_members.php?searchdata=&searchmember=" . $m->MB_USERNAME . "\"><span class=\"member_line\"><b>" . $m->MB_FIRSTNAME . " " . $m->MB_LASTNAME . "</b></span></a>";
				echo "</td>";
				echo "<td>";
					echo "<span class=\"member_line\">" . $m->MB_USERNAME . "</span>";
				echo "</td>";
				echo "<td>";
					echo "<a href=\"mailto:" . $m->MB_EMAIL . "\"><span style=\"color:#669999;\" class=\"member_line\">" . $m->MB_EMAIL . "</span></a>";
				echo "</td>";
				echo "<td align='center'>";
					echo "<span class=\"member_line\">" . $m->MB_CATEGORY . "</span>";
				echo "</td>";
				echo "<td align='center'>";
					echo "<span class=\"member_line\">" . $m->MB_CONFIRMED . "</span>";
				echo "</td>";
			echo "</tr>";
        }
    	?>
   	</table> 
    </div>   
	</form>

<?php
  include_once("includes/footer_admin.php");
?>

