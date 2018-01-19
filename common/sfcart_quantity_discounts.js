//---------------------------------------------------------------------||
// FUNCTIONS:    QUANTITY DISCOUNT HANDLING                            ||
// PURPOSE:      Handles Quantity Discount opertions within	the		   ||
//               Shopping Cart                                         ||
//---------------------------------------------------------------------||
function ApplyQuantityDiscount(form, sell_exVAT){
	//takes the current selling price and applies quantity discount to it
	var qd_flag = form.QD_FLAG.value;
	if(qd_flag == 1){
		var product = form.ID_NUM.value;
		var quantity = form.QUANTITY.value;
		var selling = form.BASE_PRICE.value;  //current VAT inclusive selling price
		if(form.TAX.value > 0){
			var vatrate = form.TAX.value;
		}else{
			var vatrate = form.PREF_VAT.value;
		}
		//read in table of quantity discounts - quantity_discount.xml
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.open("GET","/xml/quantity_discount.xml",false);
		xmlhttp.send();
		xmlDoc=xmlhttp.responseXML;
		
		//get adjustment against required quantity
		var product_xml = xmlDoc.getElementsByTagName(product)[0];
		var quantities = product_xml.getElementsByTagName("quantity");
		var adjustments = product_xml.getElementsByTagName("discount");
		var adjustment = 0;
		for(var i = 0; i < quantities.length; i++){
			//alert("QTY=" + quantities[i].firstChild.data + "   ");
			//alert("ADJUST=" + adjustments[i].firstChild.data + "   ");
			var qty = parseInt(quantities[i].firstChild.data);
			var adjust = parseFloat(adjustments[i].firstChild.data);
			
			if(quantity >= qty){
				adjustment = adjust;
			}else{
				break;
			}
		}
		//now apply the adjustment found above
		switch(form.QD_TYPE.value){
			case "PP": //Price Point
				//get VAT inclusive price = adjustment price * vat rate
				var exVAT = adjustment;
				var incVAT = addVAT(adjustment, vatrate);
				break;
			case "PC": //Percentage Reduction
				//now apply percentage reduction		
				var incVAT =((parseFloat((selling) * 100) * (100 - parseFloat(adjustment))) + 5)/10000;
				incVAT = precise_round(incVAT, 2);
				var exVAT = ((incVAT * 10000) / ((10000 + vatrate * 100)/100))/100;
				exVAT = precise_round(exVAT, 2); 
				break;
			case "V": //Value Reduction
				var incVAT = selling - adjustment;
				var exVAT = ((incVAT * 10000) / ((10000 + vatrate * 100)/100))/100;
				exVAT = precise_round(exVAT, 2); 
				break;
			default:
				break;
		}
		//get the form incVAT Price display element and rewrite it with the new price
		var element_Gprice = "";
		var levelarray = new Array();
		for ( var i=0; i < form.childNodes.length; i++ ){
			if(sell_exVAT == "N"){
				element_Gprice = SearchChildrenForPrice(form.childNodes[i], form, levelarray, 0, incVAT, "Gprice", -1, sell_exVAT);
			}else{
				element_Gprice = SearchChildrenForPrice(form.childNodes[i], form, levelarray, 0, exVAT, "Gprice", -1, sell_exVAT);
			}
			if (element_Gprice){
				//break;
			}
		}
		//get the form exVAT Price display element and rewrite it with the new price
		var element_price = "";
		var levelarray = new Array();
		for ( var i=0; i < form.childNodes.length; i++ ){
			element_price = SearchChildrenForPrice(form.childNodes[i], form, levelarray, 0, exVAT, "price", -1);
			if (element_price){
				break;
			}
		}
	}
	return;
}

function ApplyQuantityDiscountToTable(form, row, sell_exVAT){
	//takes the current selling price of the product on the current table row and applies quantity discount to it
	var qd_flag = eval("form.QD_FLAG_" + row + ".value");
	if(qd_flag == 1){
		var product = eval("form.ID_NUM_" + row + ".value");
		var quantity = eval("form.QUANTITY_" + row + ".value");
		var selling = eval("form.BASE_PRICE_" + row + ".value");  //current VAT inclusive selling price
		if(eval("form.TAX_" + row + ".value") > 0){
			var vatrate = eval("form.TAX_" + row + ".value");
		}else{
			var vatrate = eval("form.PREF_VAT_" + row + ".value");
		}
		//read in table of quantity discounts - quantity_discount.xml
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.open("GET","/xml/quantity_discount.xml",false);
		xmlhttp.send();
		xmlDoc=xmlhttp.responseXML;
		
		//get adjustment against required quantity
		var product_xml = xmlDoc.getElementsByTagName(product)[0];
		var quantities = product_xml.getElementsByTagName("quantity");
		var adjustments = product_xml.getElementsByTagName("discount");
		var adjustment = 0;
		for(var i = 0; i < quantities.length; i++){
			//alert("QTY=" + quantities[i].firstChild.data + "   ");
			//alert("ADJUST=" + adjustments[i].firstChild.data + "   ");
			var qty = parseInt(quantities[i].firstChild.data);
			var adjust = parseFloat(adjustments[i].firstChild.data);
			
			if(quantity >= qty){
				adjustment = adjust;
			}else{
				break;
			}
		}
		//now apply the adjustment found above
		switch(eval("form.QD_TYPE_" + row + ".value")){
			case "PP": //Price Point
				//get VAT inclusive price = adjustment price * vat rate
				var exVAT = adjustment;
				var incVAT = addVAT(adjustment, vatrate);
				break;
			case "PC": //Percentage Reduction
				//now apply percentage reduction		
				var incVAT =((parseFloat((selling) * 100) * (100 - parseFloat(adjustment))) + 5)/10000;
				incVAT = precise_round(incVAT, 2);
				var exVAT = ((incVAT * 10000) / ((10000 + vatrate * 100)/100))/100;
				exVAT = precise_round(exVAT, 2); 
				break;
			case "V": //Value Reduction
				var incVAT = selling - adjustment;
				var exVAT = ((incVAT * 10000) / ((10000 + vatrate * 100)/100))/100;
				exVAT = precise_round(exVAT, 2); 
				break;
			default:
				break;
		}
		//get the form incVAT Price display element and rewrite it with the new price
		var element_Gprice = "";
		var levelarray = new Array();
		for ( var i=0; i < form.childNodes.length; i++ ){
			if(sell_exVAT == "N"){
				element_Gprice = SearchChildrenForPrice(form.childNodes[i], form, levelarray, 0, incVAT, "Gprice", row, sell_exVAT);
			}else{
				element_Gprice = SearchChildrenForPrice(form.childNodes[i], form, levelarray, 0, exVAT, "Gprice", row, sell_exVAT);
			}
			
			if (element_Gprice){
				break;
			}
		}
		//get the form exVAT Price display element and rewrite it with the new price
		var element_price = "";
		var levelarray = new Array();
		for ( var i=0; i < form.childNodes.length; i++ ){
			element_price = SearchChildrenForPrice(form.childNodes[i], form, levelarray, 0, exVAT, "price", row);
			if (element_price){
				break;
			}
		}
	}
	return;
}

function SearchChildrenForPrice ( FormElement, form, levelarray, level, selling, element, row, sell_exVAT ) {
	//finds the price element and updates it with the new selling price
    var response = false; 
    //is it me
    if (typeof FormElement != 'undefined'){
		if(row == -1){
			//we are updating a product detail page or a category page in non tabular format
			if (element == "price" && FormElement.className == 'price'){
				//update the exVAT price element with the new VAT exclusive price
				FormElement.innerHTML = fixPrice(selling);
				form.PRICE_EXVAT.value = fixPrice(selling);
				return true;
			}
			if (element == "Gprice" && FormElement.className == 'Gprice'){
				//update the greoss price element with the new VAT inclusive price
				FormElement.innerHTML = fixPrice(selling);
				if(sell_exVAT == "N"){
					//only update the Price field if we displaying VAT inclusive prices
					form.PRICE.value = fixPrice(selling);
				}
				return true;
			}
		}else{
			//we are updating a category page product table row
			if (element == "price" && FormElement.className == "price_" + row){
				//update the exVAT price element with the new VAT exclusive price
				FormElement.innerHTML = fixPrice(selling);
				var priceElement = eval("form.PRICE_EXVAT_" + row);
				priceElement.value = fixPrice(selling);
				return true;
			}
			if (element == "Gprice" && FormElement.className == "Gprice_" + row){
				//update the greoss price element with the new VAT inclusive price
				FormElement.innerHTML = fixPrice(selling);
				var priceElement = eval("form.PRICE_" + row);
				priceElement.value = fixPrice(selling);
				return true;
			}
		}
    	levelarray[level] = 9999
    	for (var  j=0; j < FormElement.childNodes.length; j++ ){
    		   //does the current form element have children - if so drill down to the next level and check them out as well
    		 levelarray[level] = j
    		 response = SearchChildrenForPrice( FormElement.childNodes[j], form, levelarray, level + 1, selling, element, row);
    		 //restore j
    		 j = levelarray[levelarray.length - 1];
    		 level = levelarray.length - 1;
             if( response ){
                 return true;
    		 }
         }
         //Drop a level from the levelarray as we are no longer using this level
         levelarray.splice(levelarray.length - 1,1);
         return false;
    }
    return false; 
}

function getCartQDPrice(qdiscount_type, product, quantity, price, price_exvat, price_base, vatrate){
	//takes the current selling price and applies quantity discount to it
	var selling = price_base;  //current VAT inclusive selling price of the base price item ie. before any quantity discount is applied
	//read in table of quantity discounts - quantity_discount.xml
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.open("GET","/xml/quantity_discount.xml",false);
	xmlhttp.send();
	xmlDoc=xmlhttp.responseXML;
	
	//get adjustment against required quantity
	var product_xml = xmlDoc.getElementsByTagName(product)[0];
	var quantities = product_xml.getElementsByTagName("quantity");
	var adjustments = product_xml.getElementsByTagName("discount");
	var adjustment = 0;
	for(var i = 0; i < quantities.length; i++){
		//alert("QTY=" + quantities[i].firstChild.data + "   ");
		//alert("ADJUST=" + adjustments[i].firstChild.data + "   ");
		var qty = parseInt(quantities[i].firstChild.data);
		var adjust = parseFloat(adjustments[i].firstChild.data);
		
		if(quantity >= qty){
			adjustment = adjust;
		}else{
			break;
		}
	}
	//now apply the adjustment found above
	switch(qdiscount_type){
		case "PP": //Price Point
			//get VAT inclusive price = adjustment price * vat rate
			adjustment = addVAT(adjustment, vatrate);
			selling = adjustment;
			break;
		case "PC": //Percentage Reduction
			//now apply percentage reduction		
			selling =((parseFloat((selling) * 100) * (100 - parseFloat(adjustment))) + 5)/10000;
			selling = precise_round(selling, 2);
			break;
		case "V": //Value Reduction
			selling = selling - adjustment;
			break;
		default:
			break;
	}

	return selling;
}

function show_quantity_discounts(lineNo){
	if(document.getElementsByName("QD_FLAG_" + lineNo)[0].value == 1){
		var tableRow_product = document.getElementById("product-row_" + lineNo);
		var tableRow_tab = document.getElementById("quantity_discount_row_cat_tab_" + lineNo);
		if(tableRow_tab.style.display == "" || tableRow_tab.style.display == "none" ){
			tableRow_tab.style.display = "table-row";
			/*tableRow_product.style.backgroundColor = "#58baff";*/
		}else{
			tableRow_tab.style.display = "none";
			/*tableRow_product.style.backgroundColor = "#d6edfc";*/
		}
	}
	return;
}

//GENERAL HANDY FUNCTIONS ----------------------------------------------------------------------------------------------------------------------------

function addVAT(ex_VAT, vatRate) {
	//inc_VAT = (((parseFloat(ex_VAT) * (100 + parseFloat(vatRate))) * 10) + 5)/1000;
	inc_VAT = (parseFloat(ex_VAT) * (100 + parseFloat(vatRate)))/100;
	inc_VAT = precise_round(inc_VAT, 2);
	return inc_VAT;
}

function precise_round(num,decimals){
	var rounded = Math.round(num*Math.pow(10,decimals))/Math.pow(10,decimals);
	return rounded;
}

function fixPrice(number){
	//convert number to a string and format to 2dp with trailing zero's
	var price = number.toString(); var formatted = ""; var noDecimalPlaces = 0;
	var offset = price.indexOf(".");
	if (offset == -1) {
		noDecimalPlaces = 0;
	} else {
		noDecimalPlaces =  price.length - (offset + 1);
	}
	if (noDecimalPlaces == 0 ) {formatted = price + ".00";}
	if (noDecimalPlaces == 1 ) {formatted = price + "0";}
	if (noDecimalPlaces == 2 ) {formatted = price;}
	
	return formatted;
}
	