//=====================================================================||
//               Based on NOP Design JavaScript Shopping Cart          ||
//                                                                     ||
// For more information on SmartSystems, or how NOPDesign can help you ||
// Please visit us on the WWW at http://www.nopdesign.com              ||
//                                                                     ||
// Javascript portions of this shopping cart software are available as ||
// freeware from NOP Design.  You must keep this comment unchanged in  ||
// your code.  For more information contact FreeCart@NopDesign.com.    ||
//                                                                     ||
// JavaScript Shop Module, V.4.4.0                                     ||
//=====================================================================||

//---------------------------------------------------------------------||
//                       Global Options                                ||
//                      ----------------                               ||
// Shopping Cart Options, you can modify these options to change the   ||
// the way the cart functions.                                         ||
//                                                                     ||
// Language Packs                                                      ||
// ==============                                                      ||
// You may include any language pack before sfcart.js in your HTML    ||
// pages to change the language.  Simply include a language pack with  ||
// a script src BEFORE the <SCRIPT SRC="sfcart.js">... line.          ||
//  For example: <SCRIPT SRC="language-en.js"></SCRIPT>                ||
//                                                                     ||
// Options For Everyone:                                               ||
// =====================                                               ||
// * MonetarySymbol: string, the symbol which represents dollars/euro, ||
//   in your locale.                                                   ||
// * DisplayNotice: true/false, controls whether the user is provided  ||
//   with a popup letting them know their product is added to the cart ||
// * DisplayShippingColumn: true/false, controls whether the managecart||
//   and checkout pages display shipping cost column.                  ||
// * DisplayShippingRow: true/false, controls whether the managecart   ||
//   and checkout pages display shipping cost total row.               ||
// * DisplayTaxRow: true/false, controls whether the managecart        ||
//   and checkout pages display tax cost total row.                    ||
// * TaxRate: number, your area's current tax rate, ie: if your tax    ||
//   rate was 7.5%, you would set TaxRate = 0.075                      ||
// * TaxByRegion: true/false, when set to true, the user is prompted   ||
//   with TaxablePrompt to determine if they should be charged tax.    ||
//   In the USA, this is useful to charge tax to those people who live ||
//   in a particular state, but no one else.                           ||
// * TaxPrompt: string, popup message if user has not selected either  ||
//   taxable or nontaxable when TaxByRegion is set to true.            ||
// * TaxablePrompt: string, the message the user is prompted with to   ||
//   select if they are taxable.  If TaxByRegion is set to false, this ||
//   has no effect. Example: 'Arizona Residents'                       ||
// * NonTaxablePrompt: string, same as above, but the choice for non-  ||
//   taxable people.  Example: 'Other States'                          ||
// * MinimumOrder: number, the minium dollar amount that must be       ||
//   purchased before a user is allowed to checkout.  Set to 0.00      ||
//   to disable.                                                       ||
// * MinimumOrderPrompt: string, Message to prompt users with when     ||
//   they have not met the minimum order amount.                       ||
//                                                                     ||
// Payment Processor Options:                                          ||
// ==========================                                          ||
// * PaymentProcessor: string, the two digit payment processor code    ||
//   for support payment processor gateways.  Setting this field to    ||
//   anything other than an empty string will override your OutputItem ||
//   settings -- so please be careful when receiving any form data.    ||
//   Support payment processor gateways are:                           ||
//    * Authorize.net (an)                                             ||
//    * Worldpay      (wp)                                             ||
//    * LinkPoint     (lp)
//                                                                     ||
// Options For Programmers:                                            ||
// ========================                                            ||
// * OutputItem<..>: string, the name of the pair value passed at      ||
//   checkouttime.  Change these only if you are connecting to a CGI   ||
//   script and need other field names, or are using a secure service  ||
//   that requires specific field names.                               ||
// * AppendItemNumToOutput: true/false, if set to true, the number of  ||
//   each ordered item will be appended to the output string.  For     ||
//   example if OutputItemId is 'ID_' and this is set to true, the     ||
//   output field name will be 'ID_1', 'ID_2' ... for each item.       ||
// * HiddenFieldsToCheckout: true/false, if set to true, hidden fields ||
//   for the cart items will be passed TO the checkout page, from the  ||
//   ManageCart page.  This is set to true for CGI/PHP/Script based    ||
//   checkout pages, but should be left false if you are using an      ||
//   HTML/Javascript Checkout Page. Hidden fields will ALWAYS be       ||
//   passed FROM the checkout page to the Checkout CGI/PHP/ASP/Script  ||
//---------------------------------------------------------------------||

// Last modified 08/06/10 by MWV: new taxexemption flag against each line - set within Products.js
// Last modified 12/12/10 by MWV: carry over product weight and product shipping costs to checkout
// Last modified 27/12/10 by MWV: Line Tax and Shipping data now to be written to cookie. Product object now to be written from cookie info rather than the product.js file.

// *** NOTE THAT THIS VERSION OF THE SHOPPING CART IS TO BE USED ONLY FOR PHP SITES WHICH DO NOT USE THE PRODUCTS.JS FILE ***

//Options for Everyone:
ShippingInCheckout    = true;    // if shipping is calculated within the checkout we still need to add the product tax () into the basket total
								 // NOTE if this flag is set then "DisplayShippingRow" and "DisplayTaxRow" MUST BOTH be set to "false"
DisplayNotice         = true;
DisplayShippingColumn = false;
DisplayShippingRow    = false;   //set to false if tax/shipping to be calculated within checkout
DisplayTaxRow         = false;	 //set to false if tax/shipping to be calculated within checkout
TaxByRegion           = false;
TaxPrompt             = 'For tax purposes, please select if you are an Arizona resident before continuing';
TaxablePrompt         = 'Arizona Residents';
NonTaxablePrompt      = 'Other States';
//MinimumOrder          = 0.00;  //now set to a value held on the preferences table
//MinimumOrderPrompt    = 'Your cart is below our minimum order value, please add more items';   //now set within the page header
gNumOrdered           = 0;
gTotalWeight          = 0;
EmptyCartPrompt       = 'Your cart is empty, please add items';
Taxexempt             = false;
JumpToCart	          = false;
ReloadPage	          = false;
DefaultTaxRate	      = 17.5;

//Payment Processor Options:
PaymentProcessor      = '';

//Options for Programmers:
OutputItemId          = 'ID_';
OutputItemQuantity    = 'QUANTITY_';
OutputItemPrice       = 'PRICE_';
OutputItemName        = 'NAME_';
OutputItemSKU         = 'SKU_';            // new SKU Code
OutputItemP_Shipping  = 'PSHIPPING_';      // new product shipping (p.shipping) field to carryover to checkout for shipping/tax calcs
OutputItemShipping    = 'SHIPPING_';
OutputItemAddtlInfo   = 'ADDTLINFO_';
OutputItemP_Tax       = 'PTAX_';           // new product tax (p.tax) field to carryover to checkout for shipping/tax calcs
OutputItemTaxRate     = 'TAX_';
OutputItemTaxExemption= 'TAXEXEMPTION_';   // new taxexemption flag
OutputItemP_Weight    = 'PWEIGHT_';         // new product weight (p.weight) field to carryover to checkout for shipping/tax calcs
OutputOrderShippingTax= 'SHIPPING_TAX'     // new Shipping Tax flag
OutputOrderSubtotal   = 'SUBTOTAL';
OutputOrderShipping   = 'SHIPPING';
OutputOrderTax        = 'TAX';
TaxRate               = 'TAXRATE';
OutputOrderTotal      = 'TOTAL';
AppendItemNumToOutput = true;
HiddenFieldsToCheckout= true;
holdjump 		      = false;
updatehiddenpricefield= false;


//=====================================================================||
//---------------------------------------------------------------------||
//    YOU DO NOT NEED TO MAKE ANY MODIFICATIONS BELOW THIS LINE        ||
//---------------------------------------------------------------------||
//=====================================================================||


//---------------------------------------------------------------------||
//                      Language Strings                               ||
//                     ------------------                              ||
// These strings will not be used unless you have not included a       ||
// language pack already.  You should NOT modify these, but instead    ||
// modify the strings in language-**.js where ** is the language pack  ||
// you are using.                                                      ||
//---------------------------------------------------------------------||
if ( !bLanguageDefined ) {
   strSorry  = "Im Sorry, your cart is full, please proceed to checkout.";
   strAdded  = " added to your shopping cart.";
   strRemove = "Click 'Ok' to remove this product from your shopping cart.";
   strILabel = "Product Id";
   strDLabel = "Product Name/Description";
   strQLabel = "Quantity";
   strPLabel = "Price";
   strSLabel = "Shipping";
   strRLabel = "Remove from Cart";
   strRButton= "Remove";
   strSUB    = "SUBTOTAL";
   strSHIP   = "SHIPPING";
   strTAX    = "TAX";
   strTOT    = "TOTAL";
   strErrQty = "Invalid Quantity.";
   strNewQty = 'Please enter new quantity:';
   bLanguageDefined = true;
}


//---------------------------------------------------------------------||
// FUNCTION:    CKquantity                                             ||
// PARAMETERS:  Quantity to                                            ||
// RETURNS:     Quantity as a number, and possible alert               ||
// PURPOSE:     Make sure quantity is represented as a number          ||
//---------------------------------------------------------------------||
function CKquantity(checkString) {
   var strNewQuantity = "";

   for ( i = 0; i < checkString.length; i++ ) {
      ch = checkString.substring(i, i+1);
      if ( (ch >= "0" && ch <= "9") || (ch == '.') )
         strNewQuantity += ch;
   }

   if ( strNewQuantity.length < 1 )
      strNewQuantity = "1";

   return(strNewQuantity);
}


//---------------------------------------------------------------------||
// FUNCTION:    AddToCart                                              ||
// PARAMETERS:  Form Object                                            ||
// RETURNS:     Cookie to user's browser, with prompt                  ||
// PURPOSE:     Adds a product to the user's shopping cart             ||
//---------------------------------------------------------------------||
function AddToCart(thisForm) {

    if (isDropShowing()){
        return false;
    }
   var iNumberOrdered = 0;
   var bAlreadyInCart = false;
   var notice = "";
   iNumberOrdered = GetCookie("NumberOrdered");
   
   if ( iNumberOrdered == null )
      iNumberOrdered = 0;

   if ( thisForm.ID_NUM == null )
      strID_NUM    = "";
   else
      strID_NUM    = thisForm.ID_NUM.value;
	  
   if ( thisForm.SKU == null )
      strSKU    = "";
   else
      strSKU    = thisForm.SKU.value;
	  
   if ( thisForm.CURRENCY_SYMBOL == null )
      strCURRENCY_SYMBOL    = "";
   else
      strCURRENCY_SYMBOL    = thisForm.CURRENCY_SYMBOL.value;
	  
   if ( thisForm.QUANTITY == null )
      strQUANTITY  = "1";
   else
      strQUANTITY  = thisForm.QUANTITY.value;

   if ( thisForm.PRICE == null )
      strPRICE     = "0.00";
   else
      strPRICE     = thisForm.PRICE.value;

   if ( thisForm.NAME == null )
      strNAME      = "";
   else
      strNAME      = thisForm.NAME.value;

   if ( thisForm.SHIPPING == null )
      strSHIPPING  = "0.00";
   else
      strSHIPPING  = thisForm.SHIPPING.value;
	  
   if ( thisForm.TAX == null )
      strTAX  = "0.00";
   else
      strTAX  = thisForm.TAX.value;
	  
   if ( thisForm.WEIGHT == null )
      strWEIGHT  = "0.00";
   else
      strWEIGHT  = thisForm.WEIGHT.value;
	  
   if ( thisForm.TAXEXEMPTION == null )
      strTAXEXEMPTION  = "0";
   else
      strTAXEXEMPTION  = thisForm.TAXEXEMPTION.value;
	  
      
   if ( thisForm.ADDITIONALINFO == null ) {
      strADDTLINFO = "";
   } else {
            if (thisForm.ADDITIONALINFO.selectedIndex == 0) {
                notice = "Please " + thisForm.ADDITIONALINFO[thisForm.ADDITIONALINFO.selectedIndex].text + " from the drop down menu";
                ShowAlert(notice, "red");
	            //location.href=location.href;
                return false; }
			var xx = thisForm.ADDITIONALINFO[thisForm.ADDITIONALINFO.selectedIndex].value.split("^");
			strADDTLINFO = xx[0];
			strPRICE = parseFloat(strPRICE) + parseFloat( xx[1] );
			if (strSKU.length > 0 & xx[2].length > 0){
					strSKU = strSKU + "-" + xx[2];
			}
   }
   
   
	 
   if ( thisForm.ADDITIONALINFO2 != null ) {
            if (thisForm.ADDITIONALINFO2.selectedIndex == 0) {
                notice = "Please " + thisForm.ADDITIONALINFO2[thisForm.ADDITIONALINFO2.selectedIndex].text + " from the drop down menu";
                ShowAlert(notice, "red");
	            //location.href=location.href;
                return false; }
			var xx = thisForm.ADDITIONALINFO2[thisForm.ADDITIONALINFO2.selectedIndex].value.split("^");
			strADDTLINFO += "; " + xx[0];
			strPRICE = parseFloat(strPRICE) + parseFloat( xx[1] );
			if (strSKU.length > 0 & xx[2].length > 0){
				strSKU = strSKU + "-" + xx[2];
			}
   }
   if ( thisForm.ADDITIONALINFO3 != null ) {
                if (thisForm.ADDITIONALINFO3.selectedIndex == 0) {
                notice = "Please " + thisForm.ADDITIONALINFO3[thisForm.ADDITIONALINFO3.selectedIndex].text + " from the drop down menu";
                ShowAlert(notice, "red");
	            //location.href=location.href;
                return false; }
			var xx = thisForm.ADDITIONALINFO3[thisForm.ADDITIONALINFO3.selectedIndex].value.split("^");
			strADDTLINFO += "; " + xx[0];
			strPRICE = parseFloat(strPRICE) + parseFloat( xx[1] );
			if (strSKU.length > 0 & xx[2].length > 0){
				strSKU = strSKU + "-" + xx[2];
			}
   }
   if ( thisForm.ADDITIONALINFO4 != null ) {
                if (thisForm.ADDITIONALINFO4.selectedIndex == 0) {
                notice = "Please " + thisForm.ADDITIONALINFO4[thisForm.ADDITIONALINFO4.selectedIndex].text + " from the drop down menu";
                ShowAlert(notice, "red");
	            //location.href=location.href;
                return false; }
			var xx = thisForm.ADDITIONALINFO4[thisForm.ADDITIONALINFO4.selectedIndex].value.split("^");
			strADDTLINFO += " " + xx[0];
			strPRICE = parseFloat(strPRICE) + parseFloat( xx[1] );
			if (strSKU.length > 0 & xx[2].length > 0){
				strSKU = strSKU + "-" + xx[2];
			}
   }
   if ( thisForm.USERENTRY != null ) {
			strADDTLINFO += " " + thisForm.USERENTRY.value;
   } 
   if ( thisForm.USERENTRY2 != null ) {
			strADDTLINFO += "; Line 2: " + thisForm.USERENTRY2.value;
   }
   if ( thisForm.USERENTRY3 != null ) {
			strADDTLINFO += "; Line 3: " + thisForm.USERENTRY3.value;
   } 
   if ( thisForm.USERENTRY3 != null ) {
			strADDTLINFO += "; Line 4: " + thisForm.USERENTRY4.value;
   }
   if ( thisForm.USERENTRY3 != null ) {
			strADDTLINFO += "; Line 5: " + thisForm.USERENTRY5.value;
   }
   if ( thisForm.USERENTRY3 != null ) {
			strADDTLINFO += "; Line 6: " + thisForm.USERENTRY6.value;
   }
   if ( thisForm.USERENTRY3 != null ) {
			strADDTLINFO += "; Line 7: " + thisForm.USERENTRY7.value;
   }
   if ( thisForm.USERENTRY3 != null ) {
			strADDTLINFO += "; Line 8: " + thisForm.USERENTRY8.value;
   }
   if ( thisForm.USERENTRY3 != null ) {
			strADDTLINFO += "; Line 9: " + thisForm.USERENTRY9.value;
   }
   if ( thisForm.USERENTRY3 != null ) {
			strADDTLINFO += "; Line 10: " + thisForm.USERENTRY10.value;
   }
   if (strADDTLINFO != null) {
    strADDTLINFO = strADDTLINFO.replace( /\x2F/g,"#SF3SLASH#");
   }
   strSLINK = thisForm.PAGE_LINK.value;

   //Is this product already in the cart?  If so, increment quantity instead of adding another.
   for ( i = 1; i <= iNumberOrdered; i++ ) {
      NewOrder = "Order." + i;
      database = "";
      database = GetCookie(NewOrder);

      Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);
	  Token5 = database.indexOf("|", Token4+1);
	  Token6 = database.indexOf("|", Token5+1);
	  Token7 = database.indexOf("|", Token6+1);
	  Token8 = database.indexOf("|", Token7+1);
	  Token9 = database.indexOf("|", Token8+1);
	  Token10 = database.indexOf("|", Token9+1);

      fields = new Array;
      fields[0] = database.substring( 0, Token0 );         			// ID_Num
      fields[1] = database.substring( Token0+1, Token1 );			// Quantity
      fields[2] = database.substring( Token1+1, Token2 );			// Price
      fields[3] = database.substring( Token2+1, Token3 );			// Name
      fields[4] = database.substring( Token3+1, Token4 );			// Shipping
	  fields[5] = database.substring( Token4+1, Token5 );			// Tax
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
	  fields[7] = database.substring( Token6+1, Token7 );			// Tax Exemption Flag
      fields[8] = database.substring( Token7+1, Token8 );	// Additional Information
	  fields[9] = database.substring( Token8+1, Token9 );	// page link
	  fields[10] = database.substring( Token9+1, Token10 );	// SKU code
	  fields[11] = database.substring( Token10+1, database.length );	// SCurrency Symbol
	  MonetarySymbol = fields[11];

      if ( fields[0] == strID_NUM &&
           fields[2] == strPRICE  &&
           fields[3] == strNAME   &&
           fields[8] == strADDTLINFO
         ) {
         bAlreadyInCart = true;
         dbUpdatedOrder = strID_NUM    + "|" +
                          (parseInt(strQUANTITY)+parseInt(fields[1]))  + "|" +
                          strPRICE     + "|" +
                          strNAME      + "|" +
                          strSHIPPING  + "|" +
						  strTAX  + "|" +
						  strWEIGHT  + "|" +
						  strTAXEXEMPTION  + "|" +
                          strADDTLINFO + "|" +
						  strSLINK + "|" +
						  strSKU + "|" +
						  strCURRENCY_SYMBOL;
         strNewOrder = "Order." + i;
         DeleteCookie(strNewOrder, "/");
         SetCookie(strNewOrder, dbUpdatedOrder, null, "/");
         notice = strQUANTITY + " " + strNAME + strAdded;
         break;
      }
   }


   if ( !bAlreadyInCart ) {
      iNumberOrdered++;

      if ( iNumberOrdered > 75 )
      {
         ShowAlert( strSorry, "red" );
         return false;
      }
      else {
         dbUpdatedOrder = strID_NUM    + "|" + 
                          strQUANTITY  + "|" +
                          strPRICE     + "|" +
                          strNAME      + "|" +
                          strSHIPPING  + "|" +
						  strTAX  + "|" +
						  strWEIGHT  + "|" +
						  strTAXEXEMPTION  + "|" +
                          strADDTLINFO + "|" +
						  strSLINK + "|" +
						  strSKU + "|" +
						  strCURRENCY_SYMBOL;

         strNewOrder = "Order." + iNumberOrdered;
         SetCookie(strNewOrder, dbUpdatedOrder, null, "/");
         SetCookie("NumberOrdered", iNumberOrdered, null, "/");
         notice = strQUANTITY + " " + strNAME + strAdded;
      }
   }

   if ( JumpToCart ) 
        {
	holdjump = true;
	}

   if ( DisplayNotice ) 
 	{
      		ShowAlert(notice);
      	}
   else 
 	{
		if ( JumpToCart ) 
        	{
		    var webpage = location.href.lastIndexOf("/");
		    var cartpage = location.href.substring(0,webpage)+"/cart";
		    window.location.href= cartpage; 
		}
		else 
		{   
		        if ( ReloadPage )
		        {
            			var webpage = location.href;
			        window.location.href= webpage;
		        }
		}
	}
return false;
}


//---------------------------------------------------------------------||
// FUNCTION:    getCookieVal                                           ||
// PARAMETERS:  offset                                                 ||
// RETURNS:     URL unescaped Cookie Value                             ||
// PURPOSE:     Get a specific value from a cookie                     ||
//---------------------------------------------------------------------||
function getCookieVal (offset) {
   var endstr = document.cookie.indexOf (";", offset);

   if ( endstr == -1 )
      endstr = document.cookie.length;
   return(unescape(document.cookie.substring(offset, endstr)));
}


//---------------------------------------------------------------------||
// FUNCTION:    FixCookieDate                                          ||
// PARAMETERS:  date                                                   ||
// RETURNS:     date                                                   ||
// PURPOSE:     Fixes cookie date, stores back in date                 ||
//---------------------------------------------------------------------||
function FixCookieDate (date) {
   var base = new Date(0);
   var skew = base.getTime();

   date.setTime (date.getTime() - skew);
}

//---------------------------------------------------------------------||
// FUNCTION:    changeprice                                            ||
// PARAMETERS:  form                                                   ||
// RETURNS:     nothing                                                ||
// PURPOSE:     update form price with new value (display only)        ||
//---------------------------------------------------------------------||
function changeprice( form_to_use ) {
		if (typeof form_to_use != 'undefined')
		{
		    //Loop through all the child elements to get the one with classname of price
		    var levelarray = new Array()
		    for ( x=0; x < form_to_use.form.childNodes.length; x++ )
		    {
		    //ie
		        if (SearchChildForPrice(form_to_use.form.childNodes[x], form_to_use, levelarray, 0))
		        {
		                break;
		        }
		    }
		}
	}

//---------------------------------------------------------------------||
// FUNCTION:    SearchChildForPrice                                    ||
// PARAMETERS:  FormElement, form_to_use, levelarray, level            ||
// RETURNS:     boolean                                                ||
// PURPOSE:     Recursive Finds Price Element                          ||
//---------------------------------------------------------------------||
function SearchChildForPrice ( FormElement, form_to_use, levelarray, level ) {

    var responce = false; 
    //is it me
    if (typeof FormElement != 'undefined'){
	    if (FormElement.className == 'price'){
    	    UpdatePriceElement( FormElement, form_to_use );
    	}
    	if (FormElement.className == 'Gprice'){
    	    UpdateGrossPriceElement( FormElement, form_to_use );
    	    return true;
    	}
    	levelarray[level] = 9999
    	for ( y=0; y < FormElement.childNodes.length; y++ )
    	    {
    		   //Is it my Children
    		   //store y in the level
    		 levelarray[level] = y
    		 responce = SearchChildForPrice( FormElement.childNodes[y], form_to_use, levelarray, level + 1);
    		 //restore y
    		 y = levelarray[levelarray.length - 1];
    		 level = levelarray.length - 1;
             if ( responce )
             {
                 return true;
    		    }
         }
         //Drop a level from the levelarray as we are no longer using this level
         levelarray.splice(levelarray.length - 1,1);
         return false;
    }
    return false; 
}
	
//---------------------------------------------------------------------||
// FUNCTION:    UpdatePriceElement                                     ||
// PARAMETERS:  Element, form_to_use                                   ||
// RETURNS:     nothing                                                ||
// PURPOSE:     Updates the Price Element                              ||
//---------------------------------------------------------------------||	
function UpdatePriceElement ( Element, form_to_use ){
	var xx = new Array();
	
	if ( form_to_use.form.ADDITIONALINFO != null ) {
	    //if (form_to_use.form.ADDITIONALINFO.selectedIndex > 0) {
	        xx = form_to_use.form.ADDITIONALINFO[form_to_use.form.ADDITIONALINFO.selectedIndex].value.split("^");
			//note that for the php package form.PRICE is now the VAT inclusive price so the new ex VAT price = new adjusted price - VAT
	        var origPRICE = form_to_use.form.PRICE.value;
	        var newprice =  parseFloat(origPRICE) + parseFloat( xx[1] );
	    //}
	}
		
	if ( form_to_use.form.ADDITIONALINFO2 != null ) {	
	    //if (form_to_use.form.ADDITIONALINFO2.selectedIndex > 0) {	            
	        xx = form_to_use.form.ADDITIONALINFO2[form_to_use.form.ADDITIONALINFO2.selectedIndex].value.split("^");
	        newprice = parseFloat(newprice) + parseFloat( xx[1] ); 
	    //}
	}
	
	if ( form_to_use.form.ADDITIONALINFO3 != null ) {
	    //if (form_to_use.form.ADDITIONALINFO3.selectedIndex > 0) {		            
	        xx = form_to_use.form.ADDITIONALINFO3[form_to_use.form.ADDITIONALINFO3.selectedIndex].value.split("^");
	        newprice = parseFloat(newprice) + parseFloat( xx[1] );
	    //}
	}
	
	if ( form_to_use.form.ADDITIONALINFO4 != null ) {		            
	    //if (form_to_use.form.ADDITIONALINFO4.selectedIndex > 0) {
	        xx = form_to_use.form.ADDITIONALINFO4[form_to_use.form.ADDITIONALINFO4.selectedIndex].value.split("^");
	        newprice = parseFloat(newprice) + parseFloat( xx[1] );
	    //}
	}
			            
	if (newprice) {
		//now strip off the VAT to give an ex VAT price
		//newprice = Math.round((newprice * 10000) / ((10000 + form_to_use.form.TAX.value * 100)/100)) / 100; 
		newprice = ((newprice * 10000) / ((10000 + form_to_use.form.TAX.value * 100)/100))/100;      
    	Element.innerHTML = moneyFormat(newprice);
	}
	
	if (updatehiddenpricefield){
	    form_to_use.form.getElementById("PRICE").value = moneyFormat(newprice);
	}
}

//---------------------------------------------------------------------||
// FUNCTION:    UpdateGrossPriceElement                                     ||
// PARAMETERS:  Element, form_to_use                                   ||
// RETURNS:     nothing                                                ||
// PURPOSE:     Updates the Price Element                              ||
//---------------------------------------------------------------------||	
function UpdateGrossPriceElement ( Element, form_to_use ){
	var xx = new Array();
	
	if ( form_to_use.form.ADDITIONALINFO != null ) {
	    //if (form_to_use.form.ADDITIONALINFO.selectedIndex > 0) {
	        xx = form_to_use.form.ADDITIONALINFO[form_to_use.form.ADDITIONALINFO.selectedIndex].value.split("^");
	        var origPRICE = form_to_use.form.PRICE.value;
	        var newprice =  parseFloat(origPRICE) + parseFloat( xx[1] );
	    //}
	}
		
	if ( form_to_use.form.ADDITIONALINFO2 != null ) {	
	    //if (form_to_use.form.ADDITIONALINFO2.selectedIndex > 0) {	            
	        xx = form_to_use.form.ADDITIONALINFO2[form_to_use.form.ADDITIONALINFO2.selectedIndex].value.split("^");
	        newprice = parseFloat(newprice) + parseFloat( xx[1] ); 
	    //}
	}
	
	if ( form_to_use.form.ADDITIONALINFO3 != null ) {
	    //if (form_to_use.form.ADDITIONALINFO3.selectedIndex > 0) {		            
	        xx = form_to_use.form.ADDITIONALINFO3[form_to_use.form.ADDITIONALINFO3.selectedIndex].value.split("^");
	        newprice = parseFloat(newprice) + parseFloat( xx[1] );
	    //}
	}
	
	if ( form_to_use.form.ADDITIONALINFO4 != null ) {		            
	    //if (form_to_use.form.ADDITIONALINFO4.selectedIndex > 0) {
	        xx = form_to_use.form.ADDITIONALINFO4[form_to_use.form.ADDITIONALINFO4.selectedIndex].value.split("^");
	        newprice = parseFloat(newprice) + parseFloat( xx[1] );
	    //}
	}
	
	//within the php package form.PRICE = VAT inclusive price so GPrice is now simply original price + adjustment
	//newprice = newprice + ((newprice / 100) * GetProductTax( form_to_use.form.ID_NUM.value ));
			            
	if (newprice) {		            
    	Element.innerHTML = moneyFormat(newprice);
	}
	
}

function GetProductTax( id ){
	//-- Run through the array
	for ( x=0; x < Product.length; x++ ){
		var sp = Product[x].split("^");
		//if ( sp[1].substring(0,sp[1].length-4) == id ){
		if ( sp[0] == id ){
			return  sp[1];
		}
	}
}

//---------------------------------------------------------------------||
// FUNCTION:    GetCookie                                              ||
// PARAMETERS:  Name                                                   ||
// RETURNS:     Value in Cookie                                        ||
// PURPOSE:     Retrieves cookie from users browser                    ||
//---------------------------------------------------------------------||
function GetCookie (name) {
    if (name.substring(0,6) == "Order."){
        return SFGetCookies(name); 
   } else {
   var arg = name + "=";
   var alen = arg.length;
   var clen = document.cookie.length;
   var i = 0;

   while ( i < clen ) {
      var j = i + alen;
      if ( document.cookie.substring(i, j) == arg ) return(getCookieVal (j));
      i = document.cookie.indexOf(" ", i) + 1;
      if ( i == 0 ) break;
   }

   return(null);
   }
}

//---------------------------------------------------------------------||
// FUNCTION:    SFGetCookies                                           ||
// PARAMETERS:  Name                                                   ||
// RETURNS:     Value in Cookie                                        ||
// PURPOSE:     Retrieves cookie from users browser                    ||
//---------------------------------------------------------------------||

function SFGetCookies(name) {
    var basketvalue = "";
    var BasketItemNumber = name.substring(6);
    for ( SFx=2; SFx < 20; SFx++ )
    	 {
    	    var itemvalue  = GetCookie("SF4." + SFx);
    	    if (itemvalue != null){
    	        basketvalue+=unescape(itemvalue);
    	        }
         }
   //basketvalue contains all my items...
   if (basketvalue == ""){
        return (null);     
   } else {
        //Split the basketvalue by '/' to get the items   
        var SF4_Items = basketvalue.split('/');
      
        
        for ( SFy = 0; SFy < SF4_Items.length; SFy++)
        {
            if (SFy == BasketItemNumber -1){
                return SF4_Items[SFy];
            }        
        }
        
        //just incase 
        if (SFy < BasketItemNumber){
            return (null);
        }
        //All done
   }


}  


//---------------------------------------------------------------------||
// FUNCTION:    SetCookie                                              ||
// PARAMETERS:  name, value, expiration date, path, domain, security   ||
// RETURNS:     Null                                                   ||
// PURPOSE:     Stores a cookie in the users browser                   ||
//---------------------------------------------------------------------||
function SetCookie (name,value,expires,path,domain,secure) {


   //What sort of cookie are we....
   if (name.substring(0,6) == "Order."){
        SFCookies(name,value,expires,path,domain,secure); 
   } else {

   document.cookie = name + "=" + escape (value) +
                     ((expires) ? "; expires=" + expires.toGMTString() : "") +
                     ((path) ? "; path=" + path : "") +
                     ((domain) ? "; domain=" + domain : "") +
                     ((secure) ? "; secure" : "");
                     }
}

//---------------------------------------------------------------------||
// FUNCTION:    SFCookies                                              ||
// PARAMETERS:  name, value, expiration date, path, domain, security   ||
// RETURNS:     Null                                                   ||
// PURPOSE:     Stores a cookie in the users browser                   ||
//---------------------------------------------------------------------||
function SFCookies(name,value,expires,path,domain,secure) {
var basketvalue = "";
var BasketItemNumber = name.substring(6);
    for ( SFx=2; SFx < 20; SFx++ )
    	 {
    	    var itemvalue  = GetCookie("SF4." + SFx);
    	    if (itemvalue != null){
    	        basketvalue+=unescape(itemvalue);
    	        }
         }
   
   //basketvalue contains all my items...
   if (basketvalue == ""){
   //we are adding our first item.....
        SetCookie("SF4.2", value,expires,path,domain,secure);
        //Temp for debugging
//        document.cookie = name + "=" + escape (value) +
//                     ((expires) ? "; expires=" + expires.toGMTString() : "") +
//                     ((path) ? "; path=" + path : "") +
//                     ((domain) ? "; domain=" + domain : "") +
//                     ((secure) ? "; secure" : "");
        //End of temp        
   } else {
        //Split the basketvalue by '/' to get the items   
        var SF4_Items = basketvalue.split('/');
        //reset the basketvalue
        basketvalue = "";
        
        for ( SFy = 0; SFy < SF4_Items.length; SFy++)
        {
            if (SFy == BasketItemNumber -1){
                SF4_Items[SFy] = value;
                basketvalue += SF4_Items[SFy] + "/";
            } else {
                basketvalue += SF4_Items[SFy]+ "/";
            }
        
        }
        
        //New item?
        if (SFy < BasketItemNumber){
            basketvalue += value+ "/";
        }
        //basketvalue now contains all of my items..
        //Split them into strings of 4000 characters. and keep count.
        var cookienumber = 2; //1 is used by the other items..
        do {
            if (basketvalue.length > 4000) {
                SetCookie("SF4."+cookienumber, basketvalue.substring(0, 4000),expires,path,domain,secure);
                cookienumber ++;
                basketvalue = basketvalue.substring(4000);
            } else {
                SetCookie("SF4."+cookienumber, basketvalue.substring(0, 4000),expires,path,domain,secure);
                break;
            }
        } while (basketvalue.length > 0);
        
        //All done
   }
}


//---------------------------------------------------------------------||
// FUNCTION:    DeleteCookie                                           ||
// PARAMETERS:  Cookie name, path, domain                              ||
// RETURNS:     null                                                   ||
// PURPOSE:     Removes a cookie from users browser.                   ||
//---------------------------------------------------------------------||
function DeleteCookie (name,path,domain) {
   //What sort of cookie are we....
   if (name.substring(0,6) == "Order."){
        //only the remove from basket now needs to do anything..
        return; 
   } else {
        if ( GetCookie(name) ) {
            document.cookie = name + "=" +
                              ((path) ? "; path=" + path : "") +
                              ((domain) ? "; domain=" + domain : "") +
                              "; expires=Thu, 01-Jan-70 00:00:01 GMT";
          }
   }
}

//---------------------------------------------------------------------||
// FUNCTION:    DeleteSFCookie                                         ||
// PARAMETERS:  Cookie name, path, domain                              ||
// RETURNS:     null                                                   ||
// PURPOSE:     Removes a Item From the Cartbrowser.                   ||
//---------------------------------------------------------------------||
function DeleteSFCookie(name,path,domain) {
    var basketvalue = "";
    var BasketItemNumber = name.substring(6);
    for ( SFx=2; SFx < 20; SFx++ )
    	 {
    	    var itemvalue  = GetCookie("SF4." + SFx);
    	    if (itemvalue != null){
    	        basketvalue+=unescape(itemvalue);
    	        }
         }
   //basketvalue contains all my items...
   if (basketvalue == ""){
        return (null);     
   } else {
        //Split the basketvalue by '/' to get the items   
        var SF4_Items = basketvalue.split('/');
      
        
        for ( SFy = 0; SFy < SF4_Items.length; SFy++)
        {
            if (SFy == BasketItemNumber -1){
                //Do nothing
            } else {
                basketvalue += SF4_Items[SFy]+ "/";
            }
            
        }
        //All done
   }
}


//---------------------------------------------------------------------||
// FUNCTION:    MoneyFormat                                            ||
// PARAMETERS:  Number to be formatted                                 ||
// RETURNS:     Formatted Number                                       ||
// PURPOSE:     Reformats Dollar Amount to #.## format                 ||
//---------------------------------------------------------------------||
var decimalplace  = ".";
var currencyseperator = ",";
function moneyFormat(num) {
  
  num = num.toString();
  //num = num.replace(currencyseperator, '');
  num = num.replace(/\&pound;|/g,'');
  if(isNaN(num))
  num = "0";
  sign = (num == (num = Math.abs(num)));
  num = Math.floor(num*100+0.50000000001);
  cents = num%100;
  num = Math.floor(num/100).toString();
  if(cents<10)
  cents = "0" + cents;
  num = num.replace(currencyseperator, '');
  for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
  num = num.substring(0,num.length-(4*i+3))+currencyseperator+
  num.substring(num.length-(4*i+3));
  return (((sign)?'':'-') + num + decimalplace + cents);

/*
   var dollars = Math.floor(input);
   var tmp = new String(input);

   for ( var decimalAt = 0; decimalAt < tmp.length; decimalAt++ ) {
      if ( tmp.charAt(decimalAt)=="." )
         break;
   }

   var cents  = "" + Math.round(input * 100);
   
   // ShowAlert( "moneyFormat; cents=" + cents + "; dollars=" + dollars );


   cents = cents.substring(cents.length-2, cents.length);
   dollars += ((tmp.charAt(decimalAt+2)=="9")&&(cents=="00"))? 1 : 0;

   if ( cents == "0" )
      cents = "00";

   return (dollars + "." + cents);
   */
}


//---------------------------------------------------------------------||
// FUNCTION:    RemoveFromCart                                         ||
// PARAMETERS:  Order Number to Remove                                 ||
// RETURNS:     Null                                                   ||
// PURPOSE:     Removes an item from a users shopping cart             ||
//---------------------------------------------------------------------||
function RemoveFromCart(RemOrder) {
   if ( confirm( strRemove ) ) {
      NumberOrdered = GetCookie("NumberOrdered");
      for ( i=RemOrder; i < NumberOrdered; i++ ) {
         NewOrder1 = "Order." + (i+1);
         NewOrder2 = "Order." + (i);
         database = GetCookie(NewOrder1);
         SetCookie (NewOrder2, database, null, "/");
      }
      NewOrder = "Order." + NumberOrdered;
      SetCookie ("NumberOrdered", NumberOrdered-1, null, "/");
      DeleteCookie(NewOrder, "/");
      location.href=location.href;
   }
}


//---------------------------------------------------------------------||
// FUNCTION:    ChangeQuantity                                         ||
// PARAMETERS:  Order Number to Change Quantity                        ||
// RETURNS:     Null                                                   ||
// PURPOSE:     Changes quantity of an item in the shopping cart       ||
//---------------------------------------------------------------------||
function ChangeQuantity(OrderItem,NewQuantity) {
   if ( isNaN(NewQuantity) ) {
      ShowAlert( strErrQty, "red" );
   } else {
      if (NewQuantity == 0){
        RemoveFromCart(OrderItem);
        return;        
      }
      NewOrder = "Order." + OrderItem;
      database = "";
      database = GetCookie(NewOrder);

	  Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);
	  Token5 = database.indexOf("|", Token4+1);
	  Token6 = database.indexOf("|", Token5+1);
	  Token7 = database.indexOf("|", Token6+1);
	  Token8 = database.indexOf("|", Token7+1);
	  Token9 = database.indexOf("|", Token8+1);
	  Token10 = database.indexOf("|", Token9+1);

      fields = new Array;
      fields[0] = database.substring( 0, Token0 );         			// ID_Num
      fields[1] = database.substring( Token0+1, Token1 );			// Quantity
      fields[2] = database.substring( Token1+1, Token2 );			// Price
      fields[3] = database.substring( Token2+1, Token3 );			// Name
      fields[4] = database.substring( Token3+1, Token4 );			// Shipping
	  fields[5] = database.substring( Token4+1, Token5 );			// Tax
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
	  fields[7] = database.substring( Token6+1, Token7 );			// Tax Exemption Flag
      fields[8] = database.substring( Token7+1, Token8 );	// Additional Information
	  fields[9] = database.substring( Token8+1, Token9 );	// page link
	  fields[10] = database.substring( Token9+1, Token10 );	// SKU code
	  fields[11] = database.substring( Token10+1, database.length );	// Currency Symbol
	  MonetarySymbol = fields[11];
	  
      dbUpdatedOrder = fields[0] + "|" +
                       NewQuantity + "|" +
                       fields[2] + "|" +
                       fields[3] + "|" +
                       fields[4] + "|" +
					   fields[5] + "|" +
					   fields[6] + "|" +
					   fields[7] + "|" +
                       fields[8] + "|" +
					   fields[9] + "|" +
                       fields[10] + "|" +
					   fields[11];
      strNewOrder = "Order." + OrderItem;
      DeleteCookie(strNewOrder, "/");
      SetCookie(strNewOrder, dbUpdatedOrder, null, "/");
      location.href=location.href;      
   }
}


//---------------------------------------------------------------------||
// FUNCTION:    GetFromCart                                            ||
// PARAMETERS:  Null                                                   ||
// RETURNS:     Product Table Written to Document                      ||
// PURPOSE:     Draws current cart product table on HTML page          ||
//              **DEPRECATED FUNCTION, USE ManageCart or Checkout**    ||
//---------------------------------------------------------------------||
function GetFromCart( fShipping ) {
   ManageCart( );
}


//---------------------------------------------------------------------||
// FUNCTION:    RadioChecked                                           ||
// PARAMETERS:  Radio button to check                                  ||
// RETURNS:     True if a radio has been checked                       ||
// PURPOSE:     Form fillin validation                                 ||
//---------------------------------------------------------------------||
function RadioChecked( radiobutton ) {
   var bChecked = false;
   var rlen = radiobutton.length;
   for ( i=0; i < rlen; i++ ) {
      if ( radiobutton[i].checked )
         bChecked = true;
   }    
   return bChecked;
} 


//---------------------------------------------------------------------||
// FUNCTION:    QueryString                                            ||
// PARAMETERS:  Key to read                                            ||
// RETURNS:     value of key                                           ||
// PURPOSE:     Read data passed in via GET mode                       ||
//---------------------------------------------------------------------||
QueryString.keys = new Array();
QueryString.values = new Array();
function QueryString(key) {
   var value = null;
   for (var i=0;i<QueryString.keys.length;i++) {
      if (QueryString.keys[i]==key) {
         value = QueryString.values[i];
         break;
      }
   }
   return value;
} 

//---------------------------------------------------------------------||
// FUNCTION:    QueryString_Parse                                      ||
// PARAMETERS:  (URL string)                                           ||
// RETURNS:     null                                                   ||
// PURPOSE:     Parses query string data, must be called before Q.S.   ||
//---------------------------------------------------------------------||
function QueryString_Parse() {
   var query = window.location.search.substring(1);
   var pairs = query.split("&"); for (var i=0;i<pairs.length;i++) {
      var pos = pairs[i].indexOf('=');
      if (pos >= 0) {
         var argname = pairs[i].substring(0,pos);
         var value = pairs[i].substring(pos+1);
         QueryString.keys[QueryString.keys.length] = argname;
         QueryString.values[QueryString.values.length] = value;
      }
   }
}


//---------------------------------------------------------------------||
// FUNCTION:    ManageCart                                             ||
// PARAMETERS:  Null                                                   ||
// RETURNS:     Product Table Written to Document                      ||
// PURPOSE:     Draws current cart product table on HTML page          ||
//---------------------------------------------------------------------||
function ManageCart( ) {
   var iNumberOrdered = 0;    //Number of products ordered
   var fTotal         = 0;    //Total cost of order
   var fTax           = 0;    //Tax amount
   var fShipping      = 0;    //Shipping amount (nett)
   var fShipTax       = 0;    //Shipping Tax
   var strTotal       = "";   //Total cost formatted as money
   var strTax         = "";   //Total tax formatted as money
   var strShipping    = "";   //Total shipping formatted as money
   var strOutput      = "";   //String to be written to page
   var bDisplay       = true; //Whether to write string to the page (here for programmers)
   var skuCode        = "";   //string used to display SKU Code
	 
	 var userZone		= GetCookie("zone");    //Zone
	 if ( userZone == null ) userZone = 0;

	 var userShipping		= GetCookie("shipping");    //Shipping type
	 if ( userShipping == null ) userShipping = 0;

   iNumberOrdered = GetCookie("NumberOrdered");
   if ( iNumberOrdered == null )
      iNumberOrdered = 0;
       
   if ( bDisplay )
      strOutput = "<table class=\"nopcart\"><tr>" +
                  //Remove cart ID title		"<td class=\"nopheader\"><strong>"+strILabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strDLabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strQLabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strPLabel+"</strong></td>" +
                  (DisplayShippingColumn?"<td class=\"nopheader\"><strong>"+strSLabel+"</strong></td>":"") +
                  "<td class=\"nopheader\"><strong>"+strRLabel+" </strong></td></tr>";

   if ( iNumberOrdered == 0 ) {
      strOutput += "<tr><td colspan=4 class=\"nopentry\" align=center><br /><strong>" + EmptyCartPrompt + "</strong><br /><br /></td></tr>";
   }

   //Get the Total number of Products Ordered and Basket weight
   for ( i = 1; i <= iNumberOrdered; i++ ) {
      NewOrder = "Order." + i;
      database = "";
      database = GetCookie(NewOrder);

	  Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);
	  Token5 = database.indexOf("|", Token4+1);
	  Token6 = database.indexOf("|", Token5+1);
	  Token7 = database.indexOf("|", Token6+1);
	  Token8 = database.indexOf("|", Token7+1);
	  Token9 = database.indexOf("|", Token8+1);
	  Token10 = database.indexOf("|", Token9+1);

      fields = new Array;
      fields[0] = database.substring( 0, Token0 );                 // Product ID
      fields[1] = database.substring( Token0+1, Token1 );          // Quantity
      fields[2] = database.substring( Token1+1, Token2 );          // Price
      fields[3] = database.substring( Token2+1, Token3 );          // Product Name/Description
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
      gNumOrdered   +=  Number(fields[1]);
      //gTotalWeight += Number( fields[1] * GetProductWeight( fields[0] ));
	  gTotalWeight += Number( fields[1] * fields[6]);
      }	
	   
   for ( i = 1; i <= iNumberOrdered; i++ ) {
      NewOrder = "Order." + i;
      database = "";
      database = GetCookie(NewOrder);
	  
	  productObject = CreateProductObject(database);

	  Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);
	  Token5 = database.indexOf("|", Token4+1);
	  Token6 = database.indexOf("|", Token5+1);
	  Token7 = database.indexOf("|", Token6+1);
	  Token8 = database.indexOf("|", Token7+1);
	  Token9 = database.indexOf("|", Token8+1);
	  Token10 = database.indexOf("|", Token9+1);
	  
      fields = new Array;
      fields[0] = database.substring( 0, Token0 );                 	// Product ID
      fields[1] = database.substring( Token0+1, Token1 );          	// Quantity
      fields[2] = database.substring( Token1+1, Token2 );          	// Price
      fields[3] = database.substring( Token2+1, Token3 );          	// Product Name/Description
      //fields[4] = CalculateShipping( GetProduct( fields[0] ), userZone );   //fields[4] = database.substring( Token3+1, Token4 );          // Shipping Cost
	  fields[4] = CalculateShipping( productObject, userZone );   //fields[4] = database.substring( Token3+1, Token4 );          // Shipping Cost
	  
	  fields[5] = database.substring( Token4+1, Token5 );          	// Tax
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
	  fields[7] = database.substring( Token6+1, Token7 );			// Tax Exemption Flag
	  
      fields[8] = database.substring( Token7+1, Token8 ); //Additional Information
      
      fields[8] = fields[8].replace( /#SF3SLASH#/g,"/");
      fields[8] = fields[8].replace(/\xA3/gi,"&pound;");
	  
	  fields[9] = database.substring( Token8+1, Token9 ); //page link
	  fields[10] = database.substring( Token9+1, Token10 ); //SKU code
	  fields[11] = database.substring( Token10+1, database.length ); //Currency Symbol
	  MonetarySymbol = fields[11];
	  
	  //see if taxexemption flag is set in product object
	  // productobject = GetProduct( fields[0] );
	  //fields[6] = productobject.taxexemption;
	
      fTotal     += (parseInt(fields[1]) * parseFloat(fields[2]) );
      fShipping  += (parseInt(fields[1]) * parseFloat(fields[4]) );
      //fTax			 += parseInt(fields[1]) * CalculateTax( GetProduct( fields[0] ), userZone ); //fTax        = (fTotal * TaxRate);
      //fTax			 += Math.round(parseInt(fields[1]) * CalculateTax( GetProduct( fields[0] ), userZone, fields[2] )*100)/100; //fTax        = (fTotal * TaxRate);
	  fTax			 += Math.round(parseInt(fields[1]) * CalculateTax( productObject, userZone, fields[2] )*100)/100; //fTax        = (fTotal * TaxRate);
      strTotal    = moneyFormat(fTotal);
      strTax      = moneyFormat(fTax);
      strShipping = moneyFormat(fShipping);

//ADS
      //slink = new String(fields[3]);
      //slink = slink.replace( / /g, "_" );
      //slink = slink.replace( /'/g, "&#39;" );
	  slink = new String(fields[9]);
	  slink = slink.replace( / /g, "/" );
	  if (fields[10].length){
		  skuCode = " (" + fields[10] + ")";
	  }else{
		  skuCode = "";
	  }
      
      if ( bDisplay ) {
  //remove link       strOutput += "<tr><td class=\"nopentry\"><a href='" + fields[0] + ".htm'>"  + fields[0] + "</a></td>";

         if ( fields[8] == "" )
            //strOutput += "<td class=\"nopentry\">"  + fields[3] + "</td>";
	    	//strOutput += "<td class=\"nopentry nopprod\"><a href='" + slink + ".htm'>"  + fields[3] + "</td>";
			strOutput += "<td class=\"nopentry nopprod\"><a href='" + slink + "'>"  + fields[3] + skuCode + "</td>";
         else
            //strOutput += "<td class=\"nopentry nopprod\"><a href='" + slink + ".htm'>"  + fields[3] + " - <i>"+ fields[8] + "</i></td>";
			//add SKU code to the end of the description
			strOutput += "<td class=\"nopentry nopprod\"><a href='" + slink + "'>"  + fields[3] + " - <i>"+ fields[8] + skuCode + "</i></td>";
         strOutput += "<td class=\"nopentry\"><input type=text name=Q size=2 value=\"" + fields[1] + "\" onchange=\"ChangeQuantity("+i+", this.value);\"></td>";
         strOutput += "<td class=\"nopentry\">"+ MonetarySymbol + moneyFormat(fields[2]) + "/ea</td>";

         if ( DisplayShippingColumn ) {
            if ( parseFloat(fields[4]) > 0 ){
               strOutput += "<td class=\"nopentry\">"+ MonetarySymbol + moneyFormat(fields[4]) + "/ea</td>";
            }else
               strOutput += "<td class=\"nopentry\">N/A</td>";
         }

         strOutput += "<td class=\"nopentry\"><input type=button value=\" "+strRButton+" \" onclick=\"RemoveFromCart("+i+")\" class=\"small-button\"></td></tr>";
      }

      if ( AppendItemNumToOutput ) {
         strFooter = i;
      } else {
         strFooter = "";
      }
      if ( HiddenFieldsToCheckout ) {
         strOutput += "<input type=hidden name=\"" + OutputItemId        + strFooter + "\" value=\"" + fields[0] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemQuantity  + strFooter + "\" value=\"" + fields[1] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemPrice     + strFooter + "\" value=\"" + fields[2] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemName      + strFooter + "\" value=\"" + fields[3] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemShipping  + strFooter + "\" value=\"" + fields[4] + "\">";
		 strOutput += "<input type=hidden name=\"" + OutputItemTaxExemption + strFooter + "\" value=\"" + fields[7] + "\">";   // new taxexemption flag
		 strOutput += "<input type=hidden name=\"" + OutputItemAddtlInfo + strFooter + "\" value=\"" + fields[8] + "\">";
		 strOutput += "<input type=hidden name=\"" + OutputItemSKU        + strFooter + "\" value=\"" + fields[10] + "\">"; //SKU Code
      }

   }

   if ( bDisplay ) {
      strOutput += "<tr><td class=\"noptotal nopsub\" colspan=2><strong>"+strSUB+"</strong></td>";	// Subtotal title position was colspan=4 
      strOutput += "<td class=\"noptotal nopsub2\"><strong>" + MonetarySymbol + strTotal + "</strong></td>";	//Subtotal price
      strOutput += "<td class=\"noptotal nopsub\">&nbsp;</td>";	//blank subtotal box
      strOutput += "</tr>";

      //--- Calculate the actual shipping
      //ShowAlert( fTotal + " : " + fShipping + " : " + userZone );
      fShipping     = calculateTotalShipping(fTotal,fShipping,userZone);
      strShipping   = moneyFormat(fShipping);
      //Calculate the Tax on the shipping
      fTax += CalculateShippingTax(fShipping, userZone);
      strTax      = moneyFormat(fTax);
      
      if ( DisplayShippingRow ) {
         strOutput += "<tr><td class=\"noptotal nopship\" colspan=2><strong>"+strSHIP+"</strong></td>";	// shipping title position was colspan=2
         strOutput += "<td class=\"noptotal nopship2\"><strong>" + MonetarySymbol + strShipping + "</strong></td>"; //shipping total price
		 strOutput += "<td class=\"noptotal nopship\">" + displayZone(userZone) + "</td>";	//shipping zone box
         strOutput += "</tr>";
      }

      if ( DisplayTaxRow || TaxByRegion ) {
         if ( TaxByRegion ) {
            strOutput += "<tr><td class=\"noptotal noptax\" colspan=2><strong>"+strTAX+"</strong></td>"; // display tax position was 4
            strOutput += "<td class=\"noptotal noptax\"><strong>";
            strOutput += "<input type=radio name=\""+OutputOrderTax+"\" value=\"" + strTax + "\">";
            strOutput += TaxablePrompt + ": " + MonetarySymbol + strTax;
            strOutput += "<br /><input type=radio name=\""+OutputOrderTax+"\" value=\"0.00\">";
            strOutput += NonTaxablePrompt + ": " + MonetarySymbol + "0.00";
            strOutput += "</strong></td>";
			strOutput += "<td class=\"noptotal noptax\">&nbsp;</td>";	//blank box
            strOutput += "</tr>";
         } else {
            if (Taxexempt);
            {
                var exemptcookie = GetCookie("exempt");
                if (exemptcookie != null) 
                {
                    if (exemptcookie == "true")
                    {
                        fTax        = 0;
                        strTax      = moneyFormat(fTax);
                    }
                }
            }
            
            if (DisplayTaxRow == false)
            {
                fTax        = 0;
                strTax      = moneyFormat(fTax);
            }
            strOutput += "<tr><td class=\"noptotal noptax\" colspan=2><strong>"+strTAX+"</strong></td>"; // display tax position was 4
            strOutput += "<td class=\"noptotal noptax2\"><strong>" + MonetarySymbol + strTax + "</strong></td>";
            if (Taxexempt)
            {
                if (exemptcookie == "true")
                {
                    strOutput +="<td class=\"noptotal noptax\"><input type=\"checkbox\" id=\"sf-no-\" onclick='ChangeExemptFlag( this.checked );' checked/>" + strTAX + " exempt</td>";    // display tax exempt
                }
                else
                {
                    strOutput +="<td class=\"noptotal noptax\"><input type=\"checkbox\" id=\"sf-no-\" onclick='ChangeExemptFlag( this.checked );' />" + strTAX + " exempt</td>";
                }
            
            }
			  strOutput += "<td class=\"noptotal noptax\">&nbsp;</td>";	//blank box
            strOutput += "</tr>";
         }
      }
      if (isNaN(fTotal))
        {
        fTotal = 0;
        }
      if (isNaN(fShipping))
        {
        fShipping = 0;
        }
      if (isNaN(fTax))
        {
        fTax = 0;
        }
	  var basketTotal = fTotal;
	  if (DisplayShippingRow == true){basketTotal = basketTotal + fShipping}
	  if (DisplayTaxRow == true){basketTotal = basketTotal + fTax;}
      if ( !TaxByRegion ) {
         strOutput += "<tr><td class=\"noptotal total\" colspan=2><strong>"+strTOT+"</strong></td>";	//total - position was colspan=4
         //strOutput += "<td class=\"noptotal total\"><strong>" + MonetarySymbol + moneyFormat((fTotal + fShipping + fTax)) + "</strong></td>";
		 strOutput += "<td class=\"noptotal total\"><strong>" + MonetarySymbol + moneyFormat(basketTotal) + "</strong></td>";
		 strOutput += "<td class=\"noptotal total\">&nbsp;</td>";	//blank total box box - remove and make previous colspan=2
         strOutput += "</tr>";
      }
      strOutput += "</table>";

      if ( HiddenFieldsToCheckout ) {
         strOutput += "<input type=hidden name=\""+OutputOrderSubtotal+"\" value=\""+ MonetarySymbol + strTotal + "\">";
         strOutput += "<input type=hidden name=\""+OutputOrderShipping+"\" value=\""+ MonetarySymbol + strShipping + "\">";
         strOutput += "<input type=hidden name=\""+OutputOrderTax+"\"      value=\""+ MonetarySymbol + strTax + "\">";
         //strOutput += "<input type=hidden name=\""+OutputOrderTotal+"\"    value=\""+ MonetarySymbol + moneyFormat((fTotal + fShipping + fTax)) + "\">";
		 strOutput += "<input type=hidden name=\""+OutputOrderTotal+"\"    value=\""+ MonetarySymbol + moneyFormat(basketTotal) + "\">";
		 
      }
   }
   g_TotalCost = (fTotal + fShipping + fTax);
   g_TotalNett = (fTotal);

   document.write(strOutput);
   document.close();
}

//---------------------------------------------------------------------||
// FUNCTION:    SmallCart                                             ||
// PARAMETERS:  Null                                                   ||
// RETURNS:     Product Table Written to Document                      ||
// PURPOSE:     Draws current cart product table on HTML page          ||
//---------------------------------------------------------------------||
function SmallCart( ) {
   var iNumberOrdered = 0;    //Number of products ordered
   var fTotal         = 0;    //Total cost of order
   var fTax           = 0;    //Tax amount
   var fShipping      = 0;    //Shipping amount
   var strTotal       = "";   //Total cost formatted as money
   var strTax         = "";   //Total tax formatted as money
   var strShipping    = "";   //Total shipping formatted as money
   var strOutput      = "";   //String to be written to page
   var bDisplay       = true; //Whether to write string to the page (here for programmers)
	 
	 var userZone		= GetCookie("zone");    //Zone
	 if ( userZone == null ) userZone = 0;

	 var userShipping		= GetCookie("shipping");    //Shipping type
	 if ( userShipping == null ) userShipping = 0;

   iNumberOrdered = GetCookie("NumberOrdered");
   if ( iNumberOrdered == null )
      iNumberOrdered = 0;

   if ( bDisplay )
      strOutput = "<div class=\"mini-basket-head\">Your shopping Basket &nbsp;<a href=\"/cart\">View basket</a></div>" +
                  "<div class=\"mini-basket-content\">";

   if ( iNumberOrdered == 0 ) {
      strOutput += "<p>" + EmptyCartPrompt + "</p>";
   }

   for ( i = 1; i <= iNumberOrdered; i++ ) {
      NewOrder = "Order." + i;
      database = "";
      database = GetCookie(NewOrder);

	  Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);
	  Token5 = database.indexOf("|", Token4+1);
	  Token6 = database.indexOf("|", Token5+1);
	  Token7 = database.indexOf("|", Token6+1);
	  Token8 = database.indexOf("|", Token7+1);
	  Token9 = database.indexOf("|", Token8+1);
	  Token10 = database.indexOf("|", Token9+1);

      fields = new Array;
      fields[0] = database.substring( 0, Token0 );                 // Product ID
      fields[1] = database.substring( Token0+1, Token1 );          // Quantity
      fields[2] = database.substring( Token1+1, Token2 );          // Price
      fields[3] = database.substring( Token2+1, Token3 );          // Product Name/Description
	  fields[4] = database.substring( Token3+1, Token4 );			// Shipping
	  fields[5] = database.substring( Token4+1, Token5 );			// Tax
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
	  fields[7] = database.substring( Token6+1, Token7 );			// Tax Exemption Flag
      fields[8] = database.substring( Token7+1, Token8 );           // Additional Information
      
      fields[8] = fields[8].replace( /#SF3SLASH#/g,"/");
	  fields[9] = database.substring( Token8+1, Token9 ); // Page Link
	  fields[10] = database.substring( Token9+1, Token10 ); // SKU Code
	  fields[11] = database.substring( Token10+1, database.length ); // Currency Code
	  MonetarySymbol = fields[11];

//ADS
      //slink = new String(fields[3]);
      //slink = slink.replace( / /g, "_" );
      //slink = slink.replace( /'/g, "&#39;" );
	  slink = new String(fields[9]);
	  slink = slink.replace( / /g, "/" );
      
      if ( fields[8] == "" ){
        //strOutput += "<p>" + "<input type=button value=\" X \" onclick=\"RemoveFromCart("+i+")\" class=\"small-button\" />" + "<input type=text name=Q size=1 value=\"" + fields[1] + "\" onchange=\"ChangeQuantity("+i+", this.value);\" class=\"nopquantity\" />"+"<a href='" + slink + ".htm'>"  + fields[3]  + "</p>";
		strOutput += "<p>" + "<input type=button value=\" X \" onclick=\"RemoveFromCart("+i+")\" class=\"small-button\" />" + "<input type=text name=Q size=1 value=\"" + fields[1] + "\" onchange=\"ChangeQuantity("+i+", this.value);\" class=\"nopquantity\" />"+"<a href='" + slink + "'>"  + fields[3]  + "</p>";
      }
      else{
        //strOutput += "<p>" + "<input type=button value=\" X \" onclick=\"RemoveFromCart("+i+")\" class=\"small-button\" />" + "<input type=text name=Q size=1 value=\"" + fields[1] + "\" onchange=\"ChangeQuantity("+i+", this.value);\" class=\"nopquantity\" />"+"<a href='" + slink + ".htm'>"  + fields[3] + " - <i>"+ fields[8] +"</i>"+ "</p>";
		strOutput += "<p>" + "<input type=button value=\" X \" onclick=\"RemoveFromCart("+i+")\" class=\"small-button\" />" + "<input type=text name=Q size=1 value=\"" + fields[1] + "\" onchange=\"ChangeQuantity("+i+", this.value);\" class=\"nopquantity\" />"+"<a href='" + slink + "'>"  + fields[3] + " - <i>"+ fields[8] + " (" + fields[10] + ")" +"</i>"+ "</p>";
      }
      

      
    }
      strOutput += "</div>";
      
      var minicart = document.getElementById("minicart");
      
      if (minicart != null){
      minicart.innerHTML = strOutput;
      }
      //var oText = document.createTextElement(strOutput);
      //minicart.appendChild(oText);
      //document.write(strOutput);
      //document.close();
}


//---------------------------------------------------------------------||
// FUNCTION:    SimpleCart                                             ||
// PARAMETERS:  Null                                                   ||
// RETURNS:     Product Table Written to Document                      ||
// PURPOSE:     Draws current cart product table on HTML page          ||
//---------------------------------------------------------------------||
function SimpleCart( ) {
   var iNumberOrdered = 0;    //Number of products ordered
   var strOutput      = "";   //String to be written to page
	 
	 var userZone		= GetCookie("zone");    //Zone
	 if ( userZone == null ) userZone = 0;

	 var userShipping		= GetCookie("shipping");    //Shipping type
	 if ( userShipping == null ) userShipping = 0;

   iNumberOrdered = GetCookie("NumberOrdered");
   if ( iNumberOrdered == null )
      iNumberOrdered = 0;

      

      strOutput += "<a href=\"/cart\">"+ iNumberOrdered +" items in basket</a>"
      var minicart = document.getElementById("simplecart");
      if (minicart != null){
      minicart.innerHTML = strOutput;
      }
      //var oText = document.createTextElement(strOutput);
      //minicart.appendChild(oText);
      //document.write(strOutput);
      //document.close();
}

//---------------------------------------------------------------------||
// FUNCTION:    ValidateCart                                           ||
// PARAMETERS:  Form to validate                                       ||
// RETURNS:     true/false                                             ||
// PURPOSE:     Validates the managecart form                          ||
//---------------------------------------------------------------------||
var g_TotalCost = 0;
var g_TotalNett = 0;
function ValidateCart( theForm ) {
   if ( TaxByRegion ) {
      if ( !RadioChecked(eval("theForm."+OutputOrderTax)) ) {
         ShowAlert( TaxPrompt , "red");
         return false;
      }
   }
   
   if ( g_TotalCost == 0) {
        ShowAlert( EmptyCartPrompt , "red");
        return false;
       }
   iNumberOrdered = GetCookie("NumberOrdered");
   if ( iNumberOrdered == null )
      iNumberOrdered = 0;
   if ( iNumberOrdered == 0) {
	ShowAlert( EmptyCartPrompt , "red");
        return false;
       }

   if ( MinimumOrder >= 0.01 ) {
      if ( g_TotalNett < MinimumOrder ) {
         ShowAlert( MinimumOrderPrompt , "red");
         return false;
      }
   }

   return true;
}

//---------------------------------------------------------------------||
// FUNCTION:    CheckoutCart                                           ||
// PARAMETERS:  Null                                                   ||
// RETURNS:     Product Table Written to Document                      ||
// PURPOSE:     Draws current cart product table on HTML page for      ||
//              checkout.                                              ||
//---------------------------------------------------------------------||
function CheckoutCart( ) {
   var iNumberOrdered = 0;    //Number of products ordered
   var fTotal         = 0;    //Total cost of order
   var fTax           = 0;    //Tax amount
   var fShipping      = 0;    //Shipping amount
   var strTotal       = "";   //Total cost formatted as money
   var strTax         = "";   //Total tax formatted as money
   var strShipping    = "";   //Total shipping formatted as money
   var strOutput      = "";   //String to be written to page
   var bDisplay       = false; //Whether to write string to the page (here for programmers)
   var strPP          = "";   //Payment Processor Description Field
   var taxexempt      = GetCookie("exempt");    //TaxExempt
   if ( taxexempt == null ) taxexempt = "false";
   

	 var userZone		= GetCookie("zone");    //Zone
	 if ( userZone == null ) userZone = 0;

	 var userShipping		= GetCookie("shipping");    //Shipping type
	 if ( userShipping == null ) userShipping = 0;

   iNumberOrdered = GetCookie("NumberOrdered");
   if ( iNumberOrdered == null )
      iNumberOrdered = 0;

   if ( TaxByRegion ) {
      QueryString_Parse();
      fTax = parseFloat( QueryString( OutputOrderTax ) );
      strTax = moneyFormat(fTax);
   }

   if ( bDisplay )
      strOutput = "<table class=\"nopcart\"><tr>" +
                  //remove checkout ID			"<td class=\"nopheader\"><strong>"+strILabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strDLabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strQLabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strPLabel+"</strong></td>" +
                  (DisplayShippingColumn?"<td class=\"nopheader\"><strong>"+strSLabel+"</strong></td>":"") +
                  "</tr>";
                  
   //Get the Total number of Products Ordered and Basket weight
   for ( i = 1; i <= iNumberOrdered; i++ ) {
      NewOrder = "Order." + i;
      database = "";
      database = GetCookie(NewOrder);

      Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);

      fields = new Array;
      fields[0] = database.substring( 0, Token0 );                 // Product ID
      fields[1] = database.substring( Token0+1, Token1 );          // Quantity
      fields[2] = database.substring( Token1+1, Token2 );          // Price
      fields[3] = database.substring( Token2+1, Token3 );          // Product Name/Description
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
      gNumOrdered   +=  Number(fields[1]);
      //gTotalWeight += Number( fields[1] * GetProductWeight( fields[0] ));
	  gTotalWeight += Number( fields[1] * fields[6]);
      }

   tVAT = 0;
   for ( i = 1; i <= iNumberOrdered; i++ ) {
      NewOrder = "Order." + i;
      database = "";
      database = GetCookie(NewOrder);
	  fVAT = 0;
	  
	  productObject = CreateProductObject(database);

	  Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);
	  Token5 = database.indexOf("|", Token4+1);
	  Token6 = database.indexOf("|", Token5+1);
	  Token7 = database.indexOf("|", Token6+1);
	  Token8 = database.indexOf("|", Token7+1);
	  Token9 = database.indexOf("|", Token8+1);
	  Token10 = database.indexOf("|", Token9+1);
	  

      fields = new Array;
      fields[0] = database.substring( 0, Token0 );                 // Product ID
      fields[1] = database.substring( Token0+1, Token1 );          // Quantity
      fields[2] = database.substring( Token1+1, Token2 );          // Price
      fields[3] = database.substring( Token2+1, Token3 );          // Product Name/Description
	  //fields[4] = CalculateShipping( GetProduct( fields[0] ), userZone );   //fields[4] = database.substring( Token3+1, Token4 );          // Shipping Cost
	  fields[4] = CalculateShipping( productObject, userZone );   //fields[4] = database.substring( Token3+1, Token4 );          // Shipping Cost
	  fields[5] = database.substring( Token4+1, Token5 );			// Tax
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
	  fields[7] = database.substring( Token6+1, Token7 );			// Tax Exemption Flag
      fields[8] = database.substring( Token7+1, Token8 ); //Additional Information
      
      fields[8] = fields[8].replace( /#SF3SLASH#/g,"/");
	  	  //see if taxexemption flag is set in product object
	  	  //productobject = GetProduct( fields[0] );
	      //fields[6] = productobject.taxexemption;
		  
	  fields[9] = database.substring( Token8+1, Token9 ); //page link
	  fields[10] = database.substring( Token9+1, Token10 ); //SKU code
	  fields[11] = database.substring( Token10+1, database.length ); //Currency Code
	  MonetarySymbol = fields[11];

      fTotal     += (parseInt(fields[1]) * parseFloat(fields[2]) );
      fShipping  += (parseInt(fields[1]) * parseFloat(fields[4]) );
      if (taxexempt != "true")
      {
      //fTax		 += parseInt(fields[1]) * CalculateTax( GetProduct( fields[0] ), userZone );
      //fTax		 += Math.round(parseInt(fields[1]) * CalculateTax( GetProduct( fields[0] ), userZone, fields[2] )*100)/100;
	  fTax		 += Math.round(parseInt(fields[1]) * CalculateTax( productObject, userZone, fields[2] )*100)/100;
      }
	  fVAT = parseFloat(fields[2]) - (parseFloat(fields[2]) * 100)/(10000 + (productObject.tax * 100)) * 100;
	  fVAT = Math.round(fVAT * 100)/100;
	  tVAT = tVAT + (parseInt(fields[1]) * fVAT);
      strTotal    = moneyFormat(fTotal);
      strShipping = moneyFormat(fShipping);
	  strTax    = moneyFormat(fTax);

      if ( bDisplay ) {
         //remove checkout ID		strOutput += "<tr><td class=\"nopentry\">"  + fields[0] + "</td>";

         if ( fields[8] == "" )
            strOutput += "<td class=\"nopentry\">"  + fields[3] + "</td>";
         else
            strOutput += "<td class=\"nopentry\">"  + fields[3] + " - <i>"+ fields[8] + "</i></td>";

         strOutput += "<td class=\"nopentry\">" + fields[1] + "</td>";
         strOutput += "<td class=\"nopentry\">"+ MonetarySymbol + moneyFormat(fields[2]) + "/ea</td>";

         if ( DisplayShippingColumn ) {
            if ( parseFloat(fields[4]) > 0 ){
               strOutput += "<td class=\"nopentry\">"+ MonetarySymbol + moneyFormat(fields[4]) + "/ea</td>";
            }else
               strOutput += "<td class=\"nopentry\">N/A</td>";
         }

         strOutput += "</tr>";
      }

      if ( AppendItemNumToOutput ) {
         strFooter = i;
      } else {
         strFooter = "";
      }
      if ( PaymentProcessor != '' ) {
         //Process description field for payment processors instead of hidden values.
         //Format Description of product as:
         // ID, Name, Qty X
         strPP += fields[0] + ", " + fields[3];
         if ( fields[8] != "" )
            strPP += " - " + fields[8];
         strPP += ", Qty. " + fields[1] + "\n";
      } else {
		 priceExVAT = fields[2];
		 if (ShippingInCheckout == true) {priceExVAT = parseFloat(fields[2]) - fVAT;}
         strOutput += "<input type=hidden name=\"" + OutputItemId        + strFooter + "\" value=\"" + fields[0] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemQuantity  + strFooter + "\" value=\"" + fields[1] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemPrice     + strFooter + "\" value=\"" + priceExVat + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemName      + strFooter + "\" value=\"" + fields[3] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemShipping  + strFooter + "\" value=\"" + fields[4] + "\">";
		 strOutput += "<input type=hidden name=\"" + OutputItemTaxExemption + strFooter + "\" value=\"" + fields[7] + "\">";   // new taxexemption flag
         strOutput += "<input type=hidden name=\"" + OutputItemAddtlInfo + strFooter + "\" value=\"" + fields[8] + "\">";      // Additional Info
		 strOutput += "<input type=hidden name=\"" + OutputItemSKU        + strFooter + "\" value=\"" + fields[10] + "\">";    // SKU Code
		 
         if (taxexempt != "true")
            {
            //strOutput += "<input type=hidden name=\"" + OutputItemTaxRate + strFooter + "\" value=\"" + (GetTaxRate( GetProduct( fields[0] ), userZone)) + "\">";
			strOutput += "<input type=hidden name=\"" + OutputItemTaxRate + strFooter + "\" value=\"" + (GetTaxRate( productObject, userZone)) + "\">";
            } else {
            strOutput += "<input type=hidden name=\"" + OutputItemTaxRate + strFooter + "\" value=\"0\">";
            }
         
         
      } 

   }

   if ( bDisplay ) {
   
      //--- Calculate the actual shipping and display checkout page
      fShipping     = calculateTotalShipping(fTotal,fShipping,userZone);
      strShipping   = moneyFormat(fShipping);
      
      fTax += CalculateShippingTax(fShipping, userZone);
      strTax    = moneyFormat(fTax);
   
      strOutput += "<tr><td class=\"noptotal\" colspan=2><strong>"+strSUB+"</strong></td>";  //Subtotal title edit colspan for position was 3
      strOutput += "<td class=\"noptotal\" colspan=2 align=right><strong>" + MonetarySymbol + strTotal + "</strong></td>";	//Subtotal price
      strOutput += "</tr>";

      strOutput += "<tr><td class=\"noptotal\" colspan=2><strong>"+strSHIP+"</strong></td>";  //Shipping title edit colspan for position was 3
      strOutput += "<td class=\"noptotal\" colspan=2 align=right><strong>" + MonetarySymbol + strShipping + "</strong></td>";		//Shipping price
      strOutput += "</tr>";
      //displays tax in checkout screen
      if ( DisplayTaxRow )
      {
        strOutput += "<tr><td class=\"noptotal\" colspan=2><strong>"+strTAX+"</strong></td>";  //Tax Title edit colspan for position was 3
        strOutput += "<td class=\"noptotal\" colspan=2 align=right><strong>" + MonetarySymbol + strTax + "</strong></td>";	//Tax price
	    strOutput += "</tr>";
      }
      strOutput += "<tr><td class=\"noptotal\" colspan=2><strong>"+strTOT+"</strong></td>";  //Total Title edit colspan for position was 3
      strOutput += "<td class=\"noptotal\" colspan=2 align=right><strong>" + MonetarySymbol + moneyFormat((fTotal + fShipping + fTax)) + "</strong></td>";		//Total price
      strOutput += "</tr>";

      strOutput += "</table>";

      strOutput += "<input type=hidden name=\"x_zone\" value=\"" + userZone + "\">";
      strOutput += "<input type=hidden name=\"x_shippingmethod\" value=\"" + userShipping + "\">";
			
			
      if ( PaymentProcessor == 'an') {
         //Process this for Authorize.net WebConnect
         strOutput += "<input type=hidden name=\"x_Version\" value=\"3.0\">";
         strOutput += "<input type=hidden name=\"x_Show_Form\" value=\"PAYMENT_FORM\">";
         strOutput += "<input type=hidden name=\"x_Description\" value=\""+ strPP + "\">";
         strOutput += "<input type=hidden name=\"x_Amount\" value=\""+ moneyFormat((fTotal + fShipping + fTax)) + "\">";
      } else if ( PaymentProcessor == 'wp') {
         //Process this for WorldPay
         strOutput += "<input type=hidden name=\"desc\" value=\""+ strPP + "\">";
         strOutput += "<input type=hidden name=\"amount\" value=\""+ moneyFormat((fTotal + fShipping + fTax)) + "\">";
      } else if ( PaymentProcessor == 'lp') {
         //Process this for LinkPoint         
         strOutput += "<input type=hidden name=\"mode\" value=\"fullpay\">";
         strOutput += "<input type=hidden name=\"chargetotal\" value=\""+ moneyFormat((fTotal + fShipping + fTax)) + "\">";
         strOutput += "<input type=hidden name=\"tax\" value=\""+ MonetarySymbol + strTax + "\">";
         strOutput += "<input type=hidden name=\"subtotal\" value=\""+ MonetarySymbol + strTotal + "\">";
         strOutput += "<input type=hidden name=\"shipping\" value=\""+ MonetarySymbol + strShipping + "\">";
         strOutput += "<input type=hidden name=\"desc\" value=\""+ strPP + "\">";
      } else {
         strOutput += "<input type=hidden name=\""+OutputOrderSubtotal+"\" value=\""+ strTotal + "\">";
         strOutput += "<input type=hidden name=\""+OutputOrderShipping+"\" value=\""+ strShipping + "\">";
         strOutput += "<input type=hidden name=\""+OutputOrderTax+"\"      value=\""+ strTax + "\">";
         strOutput += "<input type=hidden name=\""+OutputOrderTotal+"\"    value=\""+ moneyFormat((fTotal + fShipping + fTax)) + "\">";
      }
   }
   document.write(strOutput);
   document.close();
}


//---------------------------------------------------------------------||
// FUNCTION:    ChangeShipping                                         ||
// PARAMETERS:  shipping                                               ||
// RETURNS:     nothing                                                ||
// PURPOSE:     Updates the cookie                                     ||
//---------------------------------------------------------------------||
function ChangeShipping( newShipping ) {
	DeleteCookie( "shipping", "/" );
	SetCookie( "shipping", newShipping, null, "/" );
	location.href=location.href;
}

//---------------------------------------------------------------------||
// FUNCTION:    ChangeZone                                             ||
// PARAMETERS:  zone                                                   ||
// RETURNS:     nothing                                                ||
// PURPOSE:     Updates the cookie                                     ||
//---------------------------------------------------------------------||
function ChangeZone( newZone ) {
	DeleteCookie( "zone", "/" );
	SetCookie( "zone", newZone, null, "/" );
	location.href=location.href;
}

//---------------------------------------------------------------------||
// FUNCTION:    ChangeExempt                                           ||
// PARAMETERS:  zone                                                   ||
// RETURNS:     nothing                                                ||
// PURPOSE:     Updates the cookie                                     ||
//---------------------------------------------------------------------||
function ChangeExemptFlag( Exempt ) {
	DeleteCookie( "exempt", "/" );
	SetCookie( "exempt", Exempt, null, "/" );
	location.href=location.href;
}

//---------------------------------------------------------------------|| 
// FUNCTION: Print_total                                               || 
// PARAMETERS: true/false if you want MonetarySymbol added to string   || 
// RETURNS: Total cost currently racked up by shopper                  || 
// PURPOSE: Aesthetics                                                 || 
//---------------------------------------------------------------------|| 
function Print_total(bSymbol) { 
var strOutput = ""; //String to be written to page 
var strTotal = ""; //Total cost formatted as money 
var fTotal = 0; 
var iNumberOrdered = 0; //Number of products ordered 


iNumberOrdered = GetCookie("NumberOrdered"); 
if ( iNumberOrdered == null ) 
iNumberOrdered = 0; 


for ( i = 1; i <= iNumberOrdered; i++ ) { 

NewOrder = "Order." + i; 
database = ""; 
database = GetCookie(NewOrder); 

Token0 = database.indexOf("|", 0);
Token1 = database.indexOf("|", Token0+1);
Token2 = database.indexOf("|", Token1+1);
Token3 = database.indexOf("|", Token2+1);
Token4 = database.indexOf("|", Token3+1);
Token5 = database.indexOf("|", Token4+1);
Token6 = database.indexOf("|", Token5+1);
Token7 = database.indexOf("|", Token6+1);
Token8 = database.indexOf("|", Token7+1);
Token9 = database.indexOf("|", Token8+1);
Token10 = database.indexOf("|", Token9+1);


fields = new Array;
fields[0] = database.substring( 0, Token0 );         			// ID_Num
fields[1] = database.substring( Token0+1, Token1 );				// Quantity
fields[2] = database.substring( Token1+1, Token2 );				// Price
fields[3] = database.substring( Token2+1, Token3 );				// Name
fields[4] = database.substring( Token3+1, Token4 );				// Shipping
fields[5] = database.substring( Token4+1, Token5 );				// Tax
fields[6] = database.substring( Token5+1, Token6 );				// Weight
fields[7] = database.substring( Token6+1, Token7 );				// Tax Exemption Flag
fields[8] = database.substring( Token7+1, Token8 );				// Additional Information 
fields[9] = database.substring( Token8+1, Token9 );				//page link
fields[10] = database.substring( Token9+1, Token10 ); 	//SKU code
fields[11] = database.substring( Token10+1, database.length ); 	//Currency Code
MonetarySymbol = fields[11];

fTotal += (parseInt(fields[1]) * parseFloat(fields[2]) ); 

} 

strTotal = moneyFormat(fTotal); 
strOutput+=strTotal; 
if ( bSymbol ) 
   strOutput = MonetarySymbol + strOutput 
document.write(strOutput); 

} 

//---------------------------------------------------------------------|| 
// FUNCTION: Print_total_products                                      || 
// PARAMETERS: true/false if you want "item(s)" added to string        || 
// RETURNS: Total cost currently racked up by shopper                  || 
// PURPOSE: Aesthetics                                                 || 
//---------------------------------------------------------------------|| 
function Print_total_products(bVerbose) { 
var strOutput = ""; //String to be written to page 
var fTotal = 0; 
var iNumberOrdered = 0; //Number of products ordered 


iNumberOrdered = GetCookie("NumberOrdered"); 
if ( iNumberOrdered == null ) 
iNumberOrdered = 0; 


for ( i = 1; i <= iNumberOrdered; i++ ) { 

NewOrder = "Order." + i; 
database = ""; 
database = GetCookie(NewOrder); 

Token0 = database.indexOf("|", 0);
Token1 = database.indexOf("|", Token0+1);
Token2 = database.indexOf("|", Token1+1);
Token3 = database.indexOf("|", Token2+1);
Token4 = database.indexOf("|", Token3+1);
Token5 = database.indexOf("|", Token4+1);
Token6 = database.indexOf("|", Token5+1);
Token7 = database.indexOf("|", Token6+1);
Token8 = database.indexOf("|", Token7+1);
Token9 = database.indexOf("|", Token8+1);
Token10 = database.indexOf("|", Token9+1);

fields = new Array;
fields[0] = database.substring( 0, Token0 );         			// ID_Num
fields[1] = database.substring( Token0+1, Token1 );				// Quantity
fields[2] = database.substring( Token1+1, Token2 );				// Price
fields[3] = database.substring( Token2+1, Token3 );				// Name
fields[4] = database.substring( Token3+1, Token4 );				// Shipping
fields[5] = database.substring( Token4+1, Token5 );				// Tax
fields[6] = database.substring( Token5+1, Token6 );				// Weight
fields[7] = database.substring( Token6+1, Token7 );				// Tax Exemption Flag
fields[8] = database.substring( Token7+1, Token8 );				// Additional Information
fields[9] = database.substring( Token8+1, Token9 ); 			//page link
fields[10] = database.substring( Token9+1, Token10 ); 			//SKU code
fields[11] = database.substring( Token10+1, database.length ); 	//Currency Code
MonetarySymbol = fields[11];

fTotal += (parseInt(fields[1])); 

} 

strOutput+=fTotal; 
if ( bVerbose ) { 
   if (fTotal == 1) { 
      strOutput+=" item" 
   } 
   else { 
      strOutput+=" items" 
   } 
} 
document.write(strOutput); 

} 

//---------------------------------------------------------------------|| 
// FUNCTION: Cart_is_empty                                             || 
// PARAMETERS: none                                                    || 
// RETURNS: Total true if cart is empty, false otherwise               || 
// PURPOSE: Aesthetics                                                 || 
//---------------------------------------------------------------------|| 
function Cart_is_empty( ) { 
   iNumInCart = GetCookie("NumberOrdered"); 
                
   if ( iNumInCart == null ) iNumInCart = 0; 

   if ( iNumInCart == 0 ) return true; 
    
   return false; 

}

//------------------------------------------------
//  FUNCTION:	ShowAlert
//  PARAMETERS:	None
//  RETURNS: 	Nothing
//  PURPOSE:    Show SF4-Alert DIV
//------------------------------------------------
//Use
//<INPUT TYPE="button" VALUE="Show" 
//onClick=ShowDiv();>
//Theme must contain the class sf4-alert

function ShowAlert(sMessage, lShowRed ) {
	var AlertMessage = document.getElementById("basketmessage");
	if (lShowRed == "red") {
	    AlertMessage = document.getElementById("basketmessage-error");
	}
	
	if (AlertMessage != null)
	{
	AlertMessage.innerHTML = sMessage;
	
	if (typeof lShowRed == 'undefined')
	{
	AlertMessage.style.color = RGBtoHex ( 255 , 255 , 255 )
	}
	else
	{
	AlertMessage.style.color = RGBtoHex ( 255 , 245 , 32 )
	}
	}
	//Verticle
	var ScrollPosition = getScrollingPosition();
	var location = ScrollPosition[1] + 230;
	var position = String(location) + "px";
	//Horizontal
	var WindowSize = getViewportSize();
	var horizontal = WindowSize[0];
	var hposition = (horizontal / 2) + ScrollPosition[0];
	var hlocation = String(hposition) + "px";
	var popup = document.getElementById("message-container");
	if (lShowRed == "red") {
	    popup = document.getElementById("message-container-error");
	}
	
	
	if (popup == null)
	{
	alert(sMessage);
	}
	else
	{
	var dropSheet = document.createElement("div");
	var dropsize = GetWindowSize();
	
	dropSheet.setAttribute("id", "dropSheet");
//	dropSheet.style.position = "absolute";
//	dropSheet.style.left = "0"
//	dropSheet.style.top = "0"
	dropSheet.style.width = dropsize[0] + "px"
	dropSheet.style.height = dropsize[1] + "px"
	document.body.appendChild(dropSheet);

	popup.style.top=position;
	popup.style.left=hlocation;
	}
	SmallCart();
	SimpleCart();
}

//------------------------------------------------
//  FUNCTION:	isPopupShowing
//  PARAMETERS:	None
//  RETURNS: 	Boolean
//  PURPOSE:    to see if Alert is showing..
//------------------------------------------------

function isDropShowing() {
    var dropSheet = document.getElementById("dropSheet");
    return (dropSheet != null);
}

//------------------------------------------------
//  FUNCTION:	HideAlert
//  PARAMETERS:	None
//  RETURNS: 	Nothing
//  PURPOSE:    Hides SF4-Alert DIV
//------------------------------------------------

function HideAlert() {
    var messcont = document.getElementById("message-container");
    if (messcont != null){
        messcont.style.top="-230px";
    }
    
    var messerr = document.getElementById("message-container-error");   
    if (messerr != null){
        messerr.style.top="-230px";
    }
//	document.getElementById("message-container").style.top="-230px";
//	document.getElementById("message-container-error").style.top="-230px";
	var dropSheet = document.getElementById("dropSheet");
	
	if (dropSheet != null){
	    dropSheet.parentNode.removeChild(dropSheet);
	    }

	if ( holdjump ) 
        {
	    var webpage = location.href.lastIndexOf("/");
	    var cartpage = location.href.substring(0,webpage)+"/cart";
	    window.location.href= cartpage; 
	}
	else 
	{   
	        if ( ReloadPage )
	        {
        		var webpage = location.href;
		        window.location.href= webpage;
	        }
	}
	holdjump = false;
	return false;		
}

//------------------------------------------------
//  FUNCTION:	getScrollingPosition
//  PARAMETERS:	None
//  RETURNS: 	Array
//  PURPOSE:    Get the scoll position of the page
//------------------------------------------------

function getScrollingPosition()
{
  var position = [0, 0];

  if (typeof window.pageYOffset != 'undefined')
  {
    position = [
        window.pageXOffset,
        window.pageYOffset
    ];
  }

  else if (typeof document.documentElement.scrollTop != 'undefined'
      && (document.documentElement.scrollTop > 0 ||
      document.documentElement.scrollLeft > 0))
  {
    position = [
        document.documentElement.scrollLeft,
        document.documentElement.scrollTop
    ];
  }

  else if (typeof document.body.scrollTop != 'undefined')
  {
    position = [
        document.body.scrollLeft,
        document.body.scrollTop
    ];
  }

  return position;
}
 
//------------------------------------------------
//  FUNCTION:	getViewportSize
//  PARAMETERS:	None
//  RETURNS: 	Array
//  PURPOSE:    Get the window size position of the page
//------------------------------------------------   
    
function getViewportSize()
{
  var size = [0, 0];

  if (typeof window.innerWidth != 'undefined')
  {
    size = [
        window.innerWidth,
        window.innerHeight
    ];
  }
  else if (typeof document.documentElement != 'undefined'
      && typeof document.documentElement.clientWidth != 'undefined'
      && document.documentElement.clientWidth != 0)
  {
    size = [
        document.documentElement.clientWidth,
        document.documentElement.clientHeight
    ];
  }
  else
  {
    size = [
        document.getElementsByTagName('body')[0].clientWidth,
        document.getElementsByTagName('body')[0].clientHeight
    ];
  }

  return size;
}

function GetWindowSize()
{
    var explorerSize = getViewportSize()
    
    if (explorerSize[0] < document.documentElement.scrollWidth){
        explorerSize[0] = document.documentElement.scrollWidth;
        }
        
    if (explorerSize[1] < document.documentElement.scrollHeight) {
        explorerSize[1] = document.documentElement.scrollHeight;
    }
    
   return explorerSize;
}

function ChangeExempt()
{
    var exemptbox = document.getElementById("sf-no-");
    if (exemptbox != null)
    {
    Taxexempt =  exemptbox.checked;
    }
    
}

function submitbutton( theForm ){
	var userZone		= GetCookie("zone");
	if (ValidateCart(theForm) == false){
	return false;
	}
	if (DisplayShippingRow == false){
	theForm.action='https://secure.shopfitter.com/checkout/sf4checkout.cfm';
	theForm.submit();
	}else{
	    if ( userZone == null){
	            notice = "From the drop down menu please select Delivery"
                ShowAlert( notice, "red");
			return false;
		    }else if ( userZone == 0){
	            notice = "From the drop down menu please select Delivery"
                ShowAlert( notice, "red");
			return false;
		    }else{
			theForm.action='https://secure.shopfitter.com/checkout/sf4checkout.cfm';theForm.submit();
		    }	
	    }
	}

//------------------------------------------------
//  FUNCTION:	RGBtoHex
//  PARAMETERS:	red, green, blue
//  RETURNS: 	HTML Value
//  PURPOSE:    converts RGB onto HTML
//------------------------------------------------ 
function RGBtoHex(R,G,B) {return "#"+toHex(R)+toHex(G)+toHex(B)}
function toHex(N) {
 if (N==null) return "00";
 N=parseInt(N); if (N==0 || isNaN(N)) return "00";
 N=Math.max(0,N); N=Math.min(N,255); N=Math.round(N);
 return "0123456789ABCDEF".charAt((N-N%16)/16)
      + "0123456789ABCDEF".charAt(N%16);
}

function searchBox( theForm ){
		if ( theForm.search.value.length == 0 ){
			return false;
		}else{
			document.cookie = "sf4=" + escape(theForm.search.value)
			return true;
		}
	}
	
//------------------------------------------------
//  FUNCTION:	Go to URL
//  PARAMETERS:	None
//  RETURNS: 	Nothing
//  PURPOSE:    Go to cart.php from Alert popup
//------------------------------------------------

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
////---------
////Add the enter key for the OK box
//document.onkeydown = function(e){ 	
//				if (e == null) { // ie
//					keycode = event.keyCode;
//				} else { // mozilla
//					keycode = e.which;
//				}
//				if(keycode == 27){ // escape
//					HideAlert();
//				} 	
//			};

//---------------------------------------------------------------------||
// FUNCTION:    CheckoutCart                                           ||
// PARAMETERS:  Null                                                   ||
// RETURNS:     Product Table Written to Document                      ||
// PURPOSE:     Draws current cart product table on HTML page for      ||
//              checkout.                                              ||
//---------------------------------------------------------------------||
function SFCheckoutCart( ) {
   var iNumberOrdered = 0;    //Number of products ordered
   var fTotal         = 0;    //Total cost of order
   var fTax           = 0;    //Tax amount
   var fShipping      = 0;    //Shipping amount
   var strTotal       = "";   //Total cost formatted as money
   var strTax         = "";   //Total tax formatted as money
   var strShipping    = "";   //Total shipping formatted as money
   var strOutput      = "";   //String to be written to page
   var bDisplay       = false; //Whether to write string to the page (here for programmers)
   var strPP          = "";   //Payment Processor Description Field
   gNumOrdered           = 0;
   gTotalWeight          = 0;
   var taxexempt      = GetCookie("exempt");    //TaxExempt
   if ( taxexempt == null ) taxexempt = "false";
   

	 var userZone		= GetCookie("zone");    //Zone
	 if ( userZone == null ) userZone = 0;

	 var userShipping		= GetCookie("shipping");    //Shipping type
	 if ( userShipping == null ) userShipping = 0;

   iNumberOrdered = GetCookie("NumberOrdered");
   if ( iNumberOrdered == null )
      iNumberOrdered = 0;

   if ( TaxByRegion ) {
      QueryString_Parse();
      fTax = parseFloat( QueryString( OutputOrderTax ) );
      strTax = moneyFormat(fTax);
   }

   if ( bDisplay )
      strOutput = "<table class=\"nopcart\"><tr>" +
                  //remove checkout ID			"<td class=\"nopheader\"><strong>"+strILabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strDLabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strQLabel+"</strong></td>" +
                  "<td class=\"nopheader\"><strong>"+strPLabel+"</strong></td>" +
                  (DisplayShippingColumn?"<td class=\"nopheader\"><strong>"+strSLabel+"</strong></td>":"") +
                  "</tr>";
                  
   //Get the Total number of Products Ordered and Basket weight
   for ( i = 1; i <= iNumberOrdered; i++ ) {
      NewOrder = "Order." + i;
      database = "";
      database = GetCookie(NewOrder);

      Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);

      fields = new Array;
      fields[0] = database.substring( 0, Token0 );                 // Product ID
      fields[1] = database.substring( Token0+1, Token1 );          // Quantity
      fields[2] = database.substring( Token1+1, Token2 );          // Price
      fields[3] = database.substring( Token2+1, Token3 );          // Product Name/Description
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
      gNumOrdered   +=  Number(fields[1]);
      //gTotalWeight += Number( fields[1] * GetProductWeight( fields[0] ));
	  gTotalWeight += Number( fields[1] * fields[6]);
      }

   tVAT = 0;
   for ( i = 1; i <= iNumberOrdered; i++ ) {
      NewOrder = "Order." + i;
      database = "";
      database = GetCookie(NewOrder);
	  fVAT = 0;
	  
	  productObject = CreateProductObject(database);

	  Token0 = database.indexOf("|", 0);
      Token1 = database.indexOf("|", Token0+1);
      Token2 = database.indexOf("|", Token1+1);
      Token3 = database.indexOf("|", Token2+1);
      Token4 = database.indexOf("|", Token3+1);
	  Token5 = database.indexOf("|", Token4+1);
	  Token6 = database.indexOf("|", Token5+1);
	  Token7 = database.indexOf("|", Token6+1);
	  Token8 = database.indexOf("|", Token7+1);
	  Token9 = database.indexOf("|", Token8+1);
	  Token10 = database.indexOf("|", Token9+1);

      fields = new Array;
      fields[0] = database.substring( 0, Token0 );                  // Product ID
      fields[1] = database.substring( Token0+1, Token1 );           // Quantity
      fields[2] = database.substring( Token1+1, Token2 );           // Price
      fields[3] = database.substring( Token2+1, Token3 );           // Product Name/Description
	  //fields[4] = CalculateShipping( GetProduct( fields[0] ), userZone );   //fields[4] = database.substring( Token3+1, Token4 );          // Shipping Cost
	  fields[4] = CalculateShipping( productObject, userZone );   //fields[4] = database.substring( Token3+1, Token4 );          // Shipping Cost
	  fields[5] = database.substring( Token4+1, Token5 );			// Tax
	  fields[6] = database.substring( Token5+1, Token6 );			// Weight
	  fields[7] = database.substring( Token6+1, Token7 );			// Tax Exemption Flag
      fields[8] = database.substring( Token7+1, Token8 );  			// Additional Information
      
      fields[8] = fields[8].replace( /#SF3SLASH#/g,"/");
	  //see if taxexemption flag is set in product object
	  //productobject = GetProduct( fields[0] );
	  //fields[6] = productobject.taxexemption;

	  fields[9] = database.substring( Token8+1, Token9 ); 			//page link
	  fields[10] = database.substring( Token9+1, Token10 ); //SKU code
	  fields[11] = database.substring( Token10+1, database.length ); //CurrencySymbol
	  MonetarySymbol = fields[11];

      fTotal     += (parseInt(fields[1]) * parseFloat(fields[2]) );
      fShipping  += (parseInt(fields[1]) * parseFloat(fields[4]) );
      if (taxexempt != "true")
      {
      //fTax		 += parseInt(fields[1]) * CalculateTax( GetProduct( fields[0] ), userZone );
      //fTax		 += Math.round(parseInt(fields[1]) * CalculateTax( GetProduct( fields[0] ), userZone, fields[2] )*100)/100;
	  fTax		 += Math.round(parseInt(fields[1]) * CalculateTax( productObject, userZone, fields[2] )*100)/100;
      }
	  fVAT = parseFloat(fields[2]) - (parseFloat(fields[2]) * 100)/(10000 + (productObject.tax * 100)) * 100;
	  fVAT = Math.round(fVAT * 100)/100;
	  tVAT = tVAT + (parseInt(fields[1]) * fVAT);
      strTotal    = moneyFormat(fTotal);
      strShipping = moneyFormat(fShipping);
	  strTax    = moneyFormat(fTax);

      if ( bDisplay ) {
         //remove checkout ID		strOutput += "<tr><td class=\"nopentry\">"  + fields[0] + "</td>";

         if ( fields[8] == "" )
            strOutput += "<td class=\"nopentry\">"  + fields[3] + "</td>";
         else
            strOutput += "<td class=\"nopentry\">"  + fields[3] + " - <i>"+ fields[8] + " (" + fields[10] + ")" + "</i></td>";

         strOutput += "<td class=\"nopentry\">" + fields[1] + "</td>";
         strOutput += "<td class=\"nopentry\">"+ MonetarySymbol + moneyFormat(fields[2]) + "/ea</td>";

         if ( DisplayShippingColumn ) {
            if ( parseFloat(fields[4]) > 0 ){
               strOutput += "<td class=\"nopentry\">"+ MonetarySymbol + moneyFormat(fields[4]) + "/ea</td>";
            }else
               strOutput += "<td class=\"nopentry\">N/A</td>";
         }

         strOutput += "</tr>";
      }

      if ( AppendItemNumToOutput ) {
         strFooter = i;
      } else {
         strFooter = "";
      }
      if ( PaymentProcessor != '' ) {
         //Process description field for payment processors instead of hidden values.
         //Format Description of product as:
         // ID, Name, Qty X
         strPP += fields[0] + ", " + fields[3];
         if ( fields[8] != "" )
            strPP += " - " + fields[8];
         strPP += ", Qty. " + fields[1] + "\n";
      } else {
		 priceExVAT = fields[2];
		 if (ShippingInCheckout == true) {priceExVAT = parseFloat(fields[2]) - fVAT;}
         strOutput += "<input type=hidden name=\"" + OutputItemId        + strFooter + "\" value=\"" + fields[0] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemQuantity  + strFooter + "\" value=\"" + fields[1] + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemPrice     + strFooter + "\" value=\"" + priceExVAT + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemName      + strFooter + "\" value=\"" + fields[3] + "\">";
		 strOutput += "<input type=hidden name=\"" + OutputItemP_Weight    + strFooter + "\" value=\"" + productObject.weight + "\">";
		 strOutput += "<input type=hidden name=\"" + OutputItemP_Shipping  + strFooter + "\" value=\"" + productObject.shipping + "\">";
         strOutput += "<input type=hidden name=\"" + OutputItemShipping  + strFooter + "\" value=\"" + fields[4] + "\">";
		 strOutput += "<input type=hidden name=\"" + OutputItemTaxExemption + strFooter + "\" value=\"" + fields[7] + "\">";   // new taxexemption flag
         strOutput += "<input type=hidden name=\"" + OutputItemAddtlInfo + strFooter + "\" value=\"" + fields[8] + "\">";
		 strOutput += "<input type=hidden name=\"" + OutputItemP_Tax     + strFooter + "\" value=\"" + productObject.tax + "\">";
		 strOutput += "<input type=hidden name=\"" + OutputItemSKU        + strFooter + "\" value=\"" + fields[10] + "\">"; //SKU Code
         if (taxexempt != "true")
            {
            //strOutput += "<input type=hidden name=\"" + OutputItemTaxRate + strFooter + "\" value=\"" + (GetTaxRate( GetProduct( fields[0] ), userZone)) + "\">";
			strOutput += "<input type=hidden name=\"" + OutputItemTaxRate + strFooter + "\" value=\"" + (GetTaxRate( productObject, userZone)) + "\">";
            } else {
            strOutput += "<input type=hidden name=\"" + OutputItemTaxRate + strFooter + "\" value=\"0\">";
            }
         
         
      } 

   }
   
   //--- Calculate the actual shipping and display checkout page
      fShipping     = calculateTotalShipping(fTotal,fShipping,userZone);
      strShipping   = moneyFormat(fShipping);
      
      fTax += CalculateShippingTax(fShipping, userZone);
      strTax    = moneyFormat(fTax);
	  mkvar = 0;
	  er_percent = -1;
	  for (var i = 0; i < taxItems.length; i++)
  	  {
	 	 if (taxItems[i].key == shippingZones[userZone].taxrate){
			mkvar = (fShipping * (taxItems[i].value / 100));
			er_percent = i;
		 }
	  }
	  
      strOutput += "<input type=hidden name=\"" + OutputOrderShippingTax + "\" value=\"" + moneyFormat(CalculateShippingTax(fShipping, userZone)) + "\">";   // new Shipping Tax flag
   if ( bDisplay ) {
   
      
   
      strOutput += "<tr><td class=\"noptotal\" colspan=2><strong>"+strSUB+"</strong></td>";  //Subtotal title edit colspan for position was 3
      strOutput += "<td class=\"noptotal\" colspan=2 ALIGN=RIGHT><strong>" + MonetarySymbol + strTotal + "</strong></td>";	//Subtotal price
      strOutput += "</tr>";

      strOutput += "<tr><td class=\"noptotal\" colspan=2><strong>"+strSHIP+"</strong></td>";  //Shipping title edit colspan for position was 3
      strOutput += "<td class=\"noptotal\" colspan=2 ALIGN=RIGHT><strong>" + MonetarySymbol + strShipping + "</strong></td>";		//Shipping price
      strOutput += "</tr>";
      //displays tax in checkout screen
      if ( DisplayTaxRow )
      {
        strOutput += "<tr><td class=\"noptotal\" colspan=2><strong>"+strTAX+"</strong></td>";  //Tax Title edit colspan for position was 3
        strOutput += "<td class=\"noptotal\" colspan=2 ALIGN=RIGHT><strong>" + MonetarySymbol + strTax + "</strong></td>";	//Tax price
	    strOutput += "</tr>";
      }
	  var basketTotal = fTotal;
	  if (DisplayShippingRow == true){basketTotal = basketTotal + fShipping}
	  if (DisplayTaxRow == true){basketTotal = basketTotal + fTax;}
	  if (ShippingInCheckout == true){basketTotal = basketTotal + fTax;}
      strOutput += "<tr><td class=\"noptotal\" colspan=2><strong>"+strTOT+"</strong></td>";  //Total Title edit colspan for position was 3
      //strOutput += "<td class=\"noptotal\" colspan=2 ALIGN=RIGHT><strong>" + MonetarySymbol + moneyFormat((fTotal + fShipping + fTax)) + "</strong></td>";		//Total price
	  strOutput += "<td class=\"noptotal\" colspan=2 ALIGN=RIGHT><strong>" + MonetarySymbol + moneyFormat(basketTotal) + "</strong></td>";		//Total price
      strOutput += "</tr>";

      strOutput += "</TABLE>";

      strOutput += "<input type=hidden name=\"x_zone\" value=\"" + userZone + "\">";
      strOutput += "<input type=hidden name=\"x_shippingmethod\" value=\"" + userShipping + "\">";
   }			
			
      if ( PaymentProcessor == 'an') {
         //Process this for Authorize.net WebConnect
         strOutput += "<input type=hidden name=\"x_Version\" value=\"3.0\">";
         strOutput += "<input type=hidden name=\"x_Show_Form\" value=\"PAYMENT_FORM\">";
         strOutput += "<input type=hidden name=\"x_Description\" value=\""+ strPP + "\">";
         strOutput += "<input type=hidden name=\"x_Amount\" value=\""+ moneyFormat((fTotal + fShipping + fTax)) + "\">";
      } else if ( PaymentProcessor == 'wp') {
         //Process this for WorldPay
         strOutput += "<input type=hidden name=\"desc\" value=\""+ strPP + "\">";
         strOutput += "<input type=hidden name=\"amount\" value=\""+ moneyFormat((fTotal + fShipping + fTax)) + "\">";
      } else if ( PaymentProcessor == 'lp') {
         //Process this for LinkPoint         
         strOutput += "<input type=hidden name=\"mode\" value=\"fullpay\">";
         strOutput += "<input type=hidden name=\"chargetotal\" value=\""+ moneyFormat((fTotal + fShipping + fTax)) + "\">";
         strOutput += "<input type=hidden name=\"tax\" value=\""+ MonetarySymbol + strTax + "\">";
         strOutput += "<input type=hidden name=\"subtotal\" value=\""+ MonetarySymbol + strTotal + "\">";
         strOutput += "<input type=hidden name=\"shipping\" value=\""+ MonetarySymbol + strShipping + "\">";
         strOutput += "<input type=hidden name=\"desc\" value=\""+ strPP + "\">";
      } else {
         strOutput += "<input type=hidden name=\""+OutputOrderSubtotal+"\" value=\""+ strTotal + "\">";
         strOutput += "<input type=hidden name=\""+OutputOrderShipping+"\" value=\""+ strShipping + "\">";
         strOutput += "<input type=hidden name=\""+OutputOrderTax+"\"      value=\""+ strTax + "\">";
		 var basketTotal = fTotal;
	  	 if (DisplayShippingRow == true){basketTotal = basketTotal + fShipping}
	  	 if (DisplayTaxRow == true){basketTotal = basketTotal + fTax;}
		 //strOutput += "<input type=hidden name=\""+OutputOrderTotal+"\"    value=\""+ moneyFormat((fTotal + fShipping + fTax)) + "\">";
		 strOutput += "<input type=hidden name=\""+OutputOrderTotal+"\"    value=\""+ moneyFormat(basketTotal) + "\">";

		 
      }
      


   document.write(strOutput);
   document.close();
}


