<!-- footer.inc -->
    </div>
	<div id="leftcolumn">			
		<div class="searchbar-wrap">
            <div class="searchbar">
               <form action="/search_advanced" id="searchform" method="POST">
                  <fieldset class="search">
                  <input type="text" value="" name="search" class="box" />
                  <input name="action2" type="submit" class="btn" value="" />
                  </fieldset>
               </form>
            </div>
        <?php
		//Advanced Searchbar
		if ($preferences->PREF_ADVANCED_SEARCH == "Y"){
			echo "<div class=\"searchbar_Advanced\" onclick=\"this.className='searchbar_Advanced-show'\"  ondblclick=\"this.className='searchbar_Advanced'\">";
			   echo "<form action=\"/search_for_attributes\" id=\"searchform_advanced\" method=\"POST\">";
				  echo "<fieldset class=\"search_advanced\">";
	
					  //get attributes against the current category page only
					  //$attributes = getAllAttributes();
					  $attributes = array();
					  $cntr_array = 0;
					  for($i=1; $i<=8; $i++){
						$fieldname = "attribute" . $i;
						if (isset($$fieldname) and $$fieldname > 0){
							$attribute = getAttribute($$fieldname);
							$attributes[] = $attribute;
							$cntr_array ++;
						}  
					  }
					  if($cntr_array > 0){
						  echo "<label>Advanced Search:</label>";
						  $cntr_attributes = 0;
						  foreach($attributes as $a){ 
							  $cntr_attributes ++;
							  $attribute = getAttribute($a->AT_ID);
							  echo "<div class=\"styled-select\"><select name=\"ATTRIBUTE_VALUE_ID" . $cntr_attributes . "\" onchange=\"\">";
								echo "<option value=\"-1\"" . ">Select " . $a->AT_SEARCH_NAME . "</option>";
								$values = getAttributeValues($attribute);
								foreach($values as $v){
									if(isset($_POST['ATTRIBUTE_VALUE_ID'.$cntr_attributes]) and $_POST['ATTRIBUTE_VALUE_ID'.$cntr_attributes] == $v->AV_ID){$selected = "selected";}else{$selected = "";} 
									echo "<option value=\"" . $v->AV_ID . "\" " . $selected . ">" . html_entity_decode($v->AV_NAME, ENT_QUOTES) . "</option>";
								}
							  echo "</select></div>";
							  echo "<input name=\"ATTRIBUTE_ID" .$cntr_attributes . "\" type=\"hidden\" value=\"" . $a->AT_ID . "\" />";
						  }
						  echo "<input name=\"ATTRIBUTES_COUNT\" type=\"hidden\" value=\"" . $cntr_attributes . "\" />";
						  echo "<input name=\"CATEGORY_CURRENT\" type=\"hidden\" value=\"" . $category . "\" />";
						  echo "<input name=\"TREE_CURRENT\" type=\"hidden\" value=\"" . $tree . "\" />";
						  echo "<input name=\"TOP_LEVEL\" type=\"hidden\" value=\"" . $top_level . "\" />";
						  if($cntr_attributes > 0){
							echo "<input name=\"action3\" type=\"submit\" class=\"btn_advanced\" value=\"Search\" />";
						  }
					  }
	
				  echo "</fieldset>";
			   echo "</form>";
			   echo "<p>(Double click background to hide)</p>";
			echo "</div>";
		}
        ?>
			</div>
        <!-- LEFT MENU LISTING -->
		<nav>
        <ul class="category">
                <?php
                $parent = 0;
                $categories = getCategories($parent);
                foreach($categories as $c){
                    if($c->CA_DISABLE == "N"){
                        //echo "<li><a href=\"categories.php?tree=0_" . $c->CA_CODE . "\">" . $c->CA_NAME . "</a></li>";
						$class = $c->CA_CLASS;
						if($top_level == $c->CA_CODE){$class .= " selected";}
						if($class != ""){$class = " class=\"" . $class . "\"";}
						if($c->CA_CODE != "HEADER" and $c->CA_CODE != "SPACER"){
							if($preferences->PREF_SHOP_ACCESS == "Y" or($preferences->PREF_SHOP_ACCESS == "N" and $login == 1 )){
                       			echo "<li" . $class . "><a href=\"/" . urlencode(html_entity_decode($c->CA_NAME, ENT_QUOTES)) . "/0_" . $c->CA_CODE . ".htm" . "\">" . $c->CA_NAME . "</a></li>";
							}else{
								//shop access is only allowed to members
								echo "<li" . $class . "><a href=\"/Restricted-Access" . ".htm" . "\">" . $c->CA_NAME . "</a></li>";
							}
						}else{
							$spacer = ($c->CA_CODE == "SPACER") ? "&nbsp;" : "";
							echo "<li" . $class . ">" . $c->CA_NAME . $spacer . "</li>";

						}
					}
                }
                ?>
                <li class="l-bottom"><a href="/cart"><strong>View Basket</strong></a></li>
            </ul>
            </nav>
            <div class="sidebar">
            <?php
            $areadata=getAreadataPage("sidebar");
            echo html_entity_decode($areadata->AR_DATA);
            ?>				
            </div>
            
        <div id="siteseal-horiz">
            <div class="sf1"><?php $_GET['type'] = 0; include 'ranlink.php'; ?></div>
            <div class="sf2"><a href="https://secure.shopfitter.com/cert.cfm" title="This website uses the shopfitter.com secure server"><span>.</span></a></div>
        </div>
        
        <div class="leftshadow"></div>
            
        </div>
<!-- end of leftcolumn -->	
	</div>	

			<?php			        
			$areadata=getAreadataPage("Background");
			if($areadata->AR_DATA != ""){
			echo "<div class=\"dropmenu\">";
				echo "<div id=\"slideshow\">";
					echo html_entity_decode($areadata->AR_DATA);
				echo "</div>";
			echo "</div>";
			}
			?>

    <div class="footerline">
        <div class="txt-footer">
            <?php
            $areadata=getAreadataPage("footer");
            echo html_entity_decode($areadata->AR_DATA, ENT_QUOTES);
            ?>
        </div>

        <?php 
            if (isset($justloggedout)){
                $login_message = "You are now logged out";
                $login_class = "header-logout";
            }else{
                if ($login == 1){$login_message = "You are logged in as " . $_SESSION['username']; $login_class="header-login";}else{$login_message = "";}
            }
                            
        ?>
        <!--
        <div class="<?php echo $login_class?>">
            <?php echo $login_message?>
        </div>
        -->
    </div>
	<a id="bottom">&nbsp;</a>
	
<!-- end of body-wrapper -->
</div>

<!-- MODAL MESSAGE BOX -->
<!--<div id="dialog-confirm" title="View Shopping Cart?">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        These items will be permanently deleted and cannot be recovered. Are you sure?
    </p>
</div>-->

<nav>
	<div class="fixedbanner">
		<div class="fixedpos">
		<div class="fixed-up"><a href="#" title="Go to the Top of page"><span>Go to the Top of page</span></a></div>
		<div class="fixed-home"><a href="/index.php" title="Go to the Home page"><span>Go to Home page</span></a></div>
		<div class="fixed-basket"><a href="/cart" title="Go to Shopping Cart page"><span>Go to Shopping Cart page</span></a></div>
		<div class="fixed-mail"><a href="/contact/index.php" title="Go to Contact page"><span>Go to Contact page</span></a></div>
		<div class="fixed-back"><a href="javascript: history.go(-1)" title="Go Back one page"><span>Go Back one page</span></a></div>
		<div class="fixed-down"><a href="#bottom" title="Go to the Bottom of page"><span>Go to the Bottom of page</span></a></div>
		</div>
	</div>
</nav>
<!-- end of ribbon -->
</div>

<div id="message-container" class="message-container">
	<p id="basketmessage">This is the basket Message</p>
    <form action="bm" >
        <p>
        <input type="button" onclick="HideAlert();MM_goToURL('parent','/cart');return document.MM_returnValue" value=" View basket " class="message-button2" />
        <input type="button" value="Continue shopping" onclick='HideAlert();' class="message-button" />
        </p>
    </form>
</div>
<div id="message-container-error" class="message-container-error">
	<p id="basketmessage-error">This is the basket Message</p>
	<form action="bm" >
       <p>
       <input type="button" value="Continue" onclick='HideAlert();' class="message-button-error" />
       </p>
    </form>
</div>

<?php
//write tracking code details from preferences table
echo html_entity_decode($preferences->PREF_TRACKING_CODE,ENT_QUOTES);
?>
    
</body>
</html>
