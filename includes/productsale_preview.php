		<?php 
		if(strlen($product->PR_USER_STRING1) > 0){
			echo "<div class=\"userstring1\">";
				echo "Was: ";
				echo "<span>";
        		echo html_entity_decode($product->PR_USER_STRING1,ENT_QUOTES);
				echo "</span>";
       		echo "</div>";
		}
		?>	
        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="buy-box">
		<form action="sale">
        	<?php
			//PROMOTION CODE INPUT --------------------------------------------------------------------------------------------------------->
			//determine whether or not product is on a current promotion and if so allow entry of code
			$promotions = On_Promotion($tree, $product->PR_PRODUCT);
			$promo_code_valid = ""; $promo_code_user = ""; $promo_type = ""; $promo_adjust = "";
			if(!empty($promotions)){
				//product is on at least 1 promotion
				//At this point each product may only be found against a single promotion so if it's inadvertantly on more than 1 then 
				//it's tough luck because we're only taking the first found code as valid
				$promo_code_valid = $promotions[0]->PROML_NO;
				$promo_type = $promotions[0]->PROMH_PROM_ID;
				$promo_adjust = $promotions[0]->PROMH_ADJUST;
			}
//            if($promo_code_valid != ""){
//				echo "<div class=\"cat-prod-promo\">";
//					echo "Promotion Code:<br/>";
//					echo "<input name=\"PROMO_CODE\" type=\"text\" size=\"10\" value=\"{$promo_code_valid}\" />";
//				echo "</div>";
//			}
			//END OF PROMOTION CODE INPUT -------------------------------------------------------------------------------------------------->
			
			//If a member is logged in then Selling Price =Ttrade Price
			$selling = $product->PR_SELLING;
			if ($login == 1 and $product->PR_TRADE > 0){$selling = $product->PR_TRADE;}
			
			//get VAT inclusive price = PR_SELLING * VAT rate
			if($product->PR_TAX > 0){
				$vatrate = $product->PR_TAX;
			}else{
				$vatrate = $preferences->PREF_VAT;
			}
			$vatinc = addVAT($selling, $vatrate);
			?>
            <?php
			if ($preferences->PREF_EXVAT =="Y" or $product->PR_TAXEXEMPTION == "Y"){
				echo "<div class=\"cat-prod-exvat\">";
				   echo "(" . $currency_symbol . "<span class=\"price\">" . $selling . "</span> ex VAT)";
				echo "</div>";
			}
            ?>
			<div class="cat-prod-price">
            	<?php
            	if ($login == 0){
					echo "Price: <span itemprop=\"price\">" . $currency_symbol . "<span class=\"Gprice\">" . $vatinc . "</span></span>"; //standard price
                }else{
					echo "Price: " . $currency_symbol . "<span class=\"Gprice\">" . $vatinc . "</span>"; //trade/member price
				}
                ?>
                <link itemprop="availability" href="http://schema.org/InStock" /><span class="availability"><?php echo $product->PR_AVAILABILITY ?></span>
				<span class="quantity">Quantity: <input type="text" size="3" maxlength="3" name="QUANTITY" onkeyup="this.value=CKquantity(this.value, 1); ApplyQuantityDiscount(this.form)" onchange="this.value=CKquantity(this.value, 0)" value="1" /></span>
			</div>
			<p>
            <input type="hidden" name="CURRENCY_SYMBOL" value="<?php echo $currency_symbol ?>" />
			<input type="hidden" name="PRICE" 	 		value="<?php echo $vatinc ?>" />
            <input type="hidden" name="PRICE_EXVAT" 	value="<?php echo $selling ?>" />
            <input type="hidden" name="BASE_PRICE" 	 		value="<?php echo $vatinc ?>" />
			<input type="hidden" name="NAME" 		 	value="<?php echo html_entity_decode($product->PR_NAME) ?>" />
			<input type="hidden" name="ID_NUM" 		 	value="<?php echo $product->PR_PRODUCT ?>" />
            <input type="hidden" name="SKU" 		 	value="<?php echo $product->PR_SKU ?>" />
			<input type="hidden" name="SHIPPING" 		value="<?php echo $product->PR_SHIPPING ?>" />
			<input type="hidden" name="TAX" 			value="<?php echo $product->PR_TAX ?>" />
			<input type="hidden" name="WEIGHT" 		 	value="<?php echo $product->PR_WEIGHT ?>" />
			<input type="hidden" name="TAXEXEMPTION" 	value="<?php echo $product->PR_TAXEXEMPTION ?>" />
			<input type="hidden" name="TAXRATE" 	 	value="0.00" />
            <input type="hidden" name="PAGE_LINK" 	 	value="<?php echo $pagePath ?>" />
            
            <!-- required for Promotions only -->
            <input type="hidden" name="PROMO_CODE_VALID" 	 	value="<?php echo $promo_code_valid ?>" />
            <input type="hidden" name="PROMO_CODE_USER" 	 	value="<?php echo $promo_code_user ?>" />
            <input type="hidden" name="PROMO_TYPE" 	 	value="<?php echo $promo_type ?>" />
            <input type="hidden" name="PROMO_ADJUST" 	 	value="<?php echo $promo_adjust ?>" />
            
            <!-- required for Quantity Discounts only -->
            <input type="hidden" name="QD_FLAG" 	 	value="<?php echo $qd_flag ?>" />
            <input type="hidden" name="QD_TYPE" 	 	value="<?php echo $qdh_type ?>" />
            <input type="hidden" name="PREF_VAT" 	 	value="<?php echo $pref_vat ?>" />
                
            <?php
			//get Option Sets - Limited to 4 only as of 17/01/11 since the sfcart.js can currently only cope with 4
			if($product->PR_OPTION1 > 0){
				$selection = getSelection($product->PR_OPTION1);
				if($selection->SE_EXCLUDE == "N"){
					$options = getOptions($selection, "N");
					echo "<select name=\"ADDITIONALINFO\" onchange=\"changeprice(this);\" >";
					echo "<option value=\"0^0.00^0\">Select " . html_entity_decode($selection->SE_NAME, ENT_QUOTES) . "</option>";
					foreach($options as $o){
						//value from the options table will be in line with the selling price from the product record which is now to be enforced as VAT exclusive but...
						//the PRICE is passed to the cart as VAT inclusive so... we need to add on the VAT to the options adjustment price for the price changes to work correctly.
						echo "<option value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "^" . addVAT($o->OP_VALUE, $vatrate) . "^" . $o->OP_SKU . "\">" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "</option>";
					}
					echo "</select><br>";
				}
			}
	
			if($product->PR_OPTION2 > 0){
				$selection = getSelection($product->PR_OPTION2);
				if($selection->SE_EXCLUDE == "N"){
					$options = getOptions($selection, "N");
					echo "<select name=\"ADDITIONALINFO2\" onchange=\"changeprice(this);\" >";
					echo "<option value=\"0^0.00^0\">Select " . html_entity_decode($selection->SE_NAME, ENT_QUOTES) . "</option>";
					foreach($options as $o){
						echo "<option value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "^" . addVAT($o->OP_VALUE, $vatrate) . "^" . $o->OP_SKU . "\">" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "</option>";
					}
					echo "</select><br>";
				}
			}
			if($product->PR_OPTION3 > 0){
				$selection = getSelection($product->PR_OPTION3);
				if($selection->SE_EXCLUDE == "N"){
					$options = getOptions($selection, "N");
					echo "<select name=\"ADDITIONALINFO3\" onchange=\"changeprice(this);\" >";
					echo "<option value=\"0^0.00^0\">Select " . html_entity_decode($selection->SE_NAME, ENT_QUOTES) . "</option>";
					foreach($options as $o){
						echo "<option value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "^" . addVAT($o->OP_VALUE, $vatrate) . "^" . $o->OP_SKU . "\">" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "</option>";
					}
					echo "</select><br>";
				}
			}
			if($product->PR_OPTION4 > 0){
				$selection = getSelection($product->PR_OPTION4);
				if($selection->SE_EXCLUDE == "N"){
					$options = getOptions($selection, "N");
					echo "<select name=\"ADDITIONALINFO4\" onchange=\"changeprice(this);\" >";
					echo "<option value=\"0^0.00^0\">Select " . html_entity_decode($selection->SE_NAME, ENT_QUOTES) . "</option>";
					foreach($options as $o){
						echo "<option value=\"" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "^" . addVAT($o->OP_VALUE, $vatrate) . "^" . $o->OP_SKU . "\">" . html_entity_decode($o->OP_NAME, ENT_QUOTES) . "</option>";
					}
					echo "</select><br>";
				}
			}
			echo "<br>";
			echo "<br>";
			?>
			
			<input type="button" value=" Add to Cart " onclick="" class="add-button"/></p>


		</form>	
        </div>