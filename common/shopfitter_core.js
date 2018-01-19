// 
// This is the main shopping basket routines
// that utilise client-side javascript to store
// contents
//
// Copyright 2003,2004,2005,2006,2007 ShopFitter Ltd
//
// Last modified 08/06/10 by MWV: new taxexemption flag against each line - set within Products.js

//------------------------------------------------
//  FUNCTION:	GetProduct
//  PARAMETERS:	ID of product
//  RETURNS: 	Object of the product
//  PURPOSE:    Looks through the s[] array and gets the product. Returns an empty object if not there
//------------------------------------------------

function GetProduct( id ){
	var p 						= new Object();
	p.name 						= "";
	p.id 						= null;
	p.price 					= 0;
	p.shipping	   				= 0;
	p.tax						= 0;
	p.search                    = "";
	p.weight                    = 0;
	p.taxexemption                 = 0;
	
	//-- Run through the array
	for ( x=0; x < s.length; x++ ){
		var sp = s[x].split("^");
		//if ( sp[1].substring(0,sp[1].length-4) == id ){
		if ( sp[9] == id ){
			p.id 						= id;
			p.name						= sp[0];
			p.price 					= sp[4];
			p.shipping	    			= sp[5];
			p.tax						= sp[6] / 100;
			p.search                    = sp[7];
			p.weight                    = sp[8];
			if ( typeof(sp[10]) == "undefined" ) {
				p.taxexemption = 0;
			} else {
				p.taxexemption = sp[10];
			}
			
			break;
		}
	}
	return p;
}

//------------------------------------------------
//  FUNCTION:	CreateProductObject
//  PARAMETERS:	ID of product
//  RETURNS: 	Object of the product
//  PURPOSE:    Looks through the cookie for a specific product, unpacks, gets the values and puts into an object. 
//              This is to remove the need for the products.js file for a php based site
//------------------------------------------------

function CreateProductObject( database ){
	
	Token0 = database.indexOf("|", 0);
    Token1 = database.indexOf("|", Token0+1);
    Token2 = database.indexOf("|", Token1+1);
    Token3 = database.indexOf("|", Token2+1);
    Token4 = database.indexOf("|", Token3+1);
	Token5 = database.indexOf("|", Token4+1);
	Token6 = database.indexOf("|", Token5+1);
	Token7 = database.indexOf("|", Token6+1);

	var p 						= new Object();
	p.id 						= database.substring( 0, Token0 );                 	// Product ID
	p.price 					= database.substring( Token1+1, Token2 );          	// Price
	p.name 						= database.substring( Token2+1, Token3 );          	// Product Name/Description
	p.shipping	   				= database.substring( Token3+1, Token4 );           // Shipping Cost
	p.tax						= database.substring( Token4+1, Token5 );          	// Tax
	p.search                    = "";
	p.weight                    = database.substring( Token5+1, Token6 );			// Weight
	p.taxexemption              = database.substring( Token6+1, Token7 );			// Tax Exemption Flag
	
	return p;
}

//------------------------------------------------
//  FUNCTION:	GetProductWeight
//  PARAMETERS:	ID of product
//  RETURNS: 	Object of the product
//  PURPOSE:    Looks through the s[] array and gets the weight of the product.
//------------------------------------------------

function GetProductWeight( id ){
	var p 						= new Object();
	p.name 						= "";
	p.id 						= null;
	p.price 					= 0;
	p.shipping	   				= 0;
	p.tax						= 0;
	p.search                    = "";
	p.weight                    = 0;
	
	//-- Run through the array
	for ( x=0; x < s.length; x++ ){
		var sp = s[x].split("^");
		//if ( sp[1].substring(0,sp[1].length-4) == id ){
		if ( sp[9] == id ){
			p.id 						= id;
			p.name						= sp[0];
			p.price 					= sp[4];
			p.shipping	    			= sp[5];
			p.tax						= sp[6] / 100;
			p.search                    = sp[7];
			p.weight                    = sp[8];
			break;
		}
	}
	return p.weight;
}

//------------------------------------------------
//  FUNCTION:	CalculateShipping
//  PARAMETERS:	ProductObject, Zone number, shipping
//  RETURNS: 	Price
//  PURPOSE:    Looks up the shipping table to calculate price
//------------------------------------------------

function CalculateShipping( productObject, currentZone ){
  if ( productObject.id == null ) return 0;
  if ( currentZone > shippingZones.length ) return 0;
//This is where we change the shipping policy
  shippingPolicy = shippingZones[currentZone].policy;
  if ( shippingPolicy == 'peritem' ){
  
    return (1 * shippingZones[currentZone].peritem) + (1 * productObject.shipping);
    
  }else if ( shippingPolicy == 'perweight' ){
  
  	var priceSoFar 	= 0;
  	for ( x=zoneWeight[currentZone].length-1; x >= 0; --x ){
  		var sp = zoneWeight[currentZone][x].split("^");
      var weightRow = (1*sp[0]);
  		if ( weightRow <= (1*productObject.weight) ){
  			priceSoFar = (1*sp[1]);
  			break;
      	}
  	}
    
  	return priceSoFar;
  }else if ( shippingPolicy == 'itemsplice' ){
  
  	var priceSoFar 	= 0;
  	for ( x=zoneWeight[currentZone].length-1; x >= 0; --x ){
  		var sp = zoneWeight[currentZone][x].split("^");
      var weightRow = (1*sp[0]);
  		if ( weightRow <= gNumOrdered ){
  			priceSoFar = (1*sp[1])+ (1 * productObject.shipping) ;
  			break;
      	}
  	}
    
  	return priceSoFar;
    
  }else if ( shippingPolicy == 'perpercent' ){
    return  productObject.price * (shippingZones[currentZone].perpercent / 100);
  }else if ( shippingPolicy == 'perbasket' ){
    return  0;  //-- To be Calculated later
  }else if ( shippingPolicy == 'percartweight' ){
    return  0;  //-- To be Calculated later
  }else if ( shippingPolicy == 'basketsplice' ){
    return 0;   //--To be Calculated later
  }
}

//------------------------------------------------
//  FUNCTION:	CalculateTax
//  PARAMETERS:	ProductObject
//  RETURNS: 	Price
//  PURPOSE:    Calculates the tax on a product
//------------------------------------------------

function CalculateTax( productObject, currentZone, manualprice ){
  if ( productObject.id == null ) 
	return 0;

  if (DisplayTaxRow == false)
  {
    return 0;
  }

  //alert( 'CalculateTax.exempt: ' + (shippingZones[currentZone].taxrate/100) + " $ " + productObject.price );
  
  //-- This zone is not to be taxed
  if ( shippingZones[currentZone].taxexempt == 1 ) 
	return 0;
  
  //-- See if this product has a tax
  if ( productObject.tax != 0 ) 
    return (manualprice/100) * (productObject.tax*100); 
	//-- return (productObject.price/100) * (productObject.tax*100); 
	
  
  //-- return (productObject.price/100) * (shippingZones[currentZone].taxrate/100);
  return (manualprice/100) * (shippingZones[currentZone].taxrate/100);
}

function GetTaxRate( productObject, currentZone ) {
 if ( productObject.id == null ) 
	return 0;

  if (DisplayTaxRow == false)
  {
    return 0;
  }
  
  //-- This zone is not to be taxed
  if ( shippingZones[currentZone].taxexempt == 1 ) 
	return 0;
  
  //-- See if this product has a tax
  if ( productObject.tax != 0 ) 
    return (productObject.tax*100); 
	//-- return (productObject.price/100) * (productObject.tax*100); 
	
  
  //-- return (productObject.price/100) * (shippingZones[currentZone].taxrate/100);
  return (shippingZones[currentZone].taxrate/100);

}

//------------------------------------------------
//  FUNCTION:   CalculateShippingTax
//  PARAMETERS: TotalShipping, currentZone
//  Returns:    Vat On Shipping
//------------------------------------------------

function CalculateShippingTax( TotalShipping, currentZone ){
  if ( currentZone > shippingZones.length ) 
  return 0;
  
   var taxexempt      = GetCookie("exempt");    //TaxExempt
   if ( taxexempt == null ) taxexempt = "false";
  
  if (taxexempt == "true")
  return 0;
  
  if (DisplayTaxRow == false)
  {
    return 0;
  }
  
  for (var i = 0; i < taxItems.length; i++)
  {
  if (taxItems[i].key == shippingZones[currentZone].taxrate)
    return (TotalShipping * (taxItems[i].value / 100));
  }
  
  return 0;
}

//------------------------------------------------
//  FUNCTION:	calculateTotalShipping
//  PARAMETERS:	total, shiptotal, currentzone
//  RETURNS: 	Price
//  PURPOSE:    Calculates the total price for shipping
//------------------------------------------------

function calculateTotalShipping( subTotal, shipTotal, currentZone ){
  if ( currentZone > shippingZones.length ) 
	return 0;
  
  //alert(subTotal + " : " + shipTotal + " : " + currentZone);
  
  //-- Calculates the total shipping applying special business rules if necessary
  if ( shippingPolicy == 'perbasket' ){
    shippingCost  = shippingZones[currentZone].perbasket;
  }else shippingCost = shipTotal;
  
  if ( shippingPolicy == 'basketsplice' )
  {
    var priceSoFar 	= 0;
  	for ( x=zoneWeight[currentZone].length-1; x >= 0; --x )
  	{
      var sp = zoneWeight[currentZone][x].split("^");
      var weightRow = (1*sp[0]);
  		if ( weightRow <= subTotal )
  		{
  			priceSoFar = (1*sp[1]) ;
  			break;
  		}
     }
      	shippingCost = priceSoFar;
  }
  
  if ( shippingPolicy == 'percartweight' )
  {
    var priceSoFar 	= 0;
  	for ( x=zoneWeight[currentZone].length-1; x >= 0; --x )
  	{
      var sp = zoneWeight[currentZone][x].split("^");
      var weightRow = (1*sp[0]);
  		if ( weightRow <= gTotalWeight )
  		{
  			priceSoFar = (1*sp[1]) ;
  			break;
  		}
     }
      	shippingCost = priceSoFar;
  }
  
  //PerPercentage Total Calc
  if ( shippingPolicy == 'perpercent' ){
    shippingCost = subTotal * (shippingZones[currentZone].perpercent / 100);
  }

  if ( (shippingCost) >= shippingZones[currentZone].maxprice ){
    //-- If the total is over a given price, then the shipping is capped
    shippingCost = shippingZones[currentZone].maxprice;
  }
    if ( (subTotal) >= shippingZones[currentZone].maxthres ){
    //-- If the total basket is over a given price, then the shipping is zero
    return 0;
  }
	
  if ( (shippingCost) <= shippingZones[currentZone].minprice ){
    //-- if the total is below a certain value, then the shipping must be at least
    shippingCost = shippingZones[currentZone].minprice;
  }
  
  //if ( (subTotal) <= shippingZones[currentZone].minthres ){
  //  //-- if the total is below a certain value, then the shipping is zero
  //  return 0;
  //}
  
  //-- Apply shipping tax
  if ( shippingZones[currentZone].taxrate > 0 ){
    shippingCost = shippingCost + (shippingCost * (shippingZones[currentZone].taxrate / 100));
  }
  
  return shippingCost;
}


//------------------------------------------------
//  FUNCTION:	displayShipping
//  PARAMETERS:	CurrentShipping
//  RETURNS: 	string to display
//  PURPOSE:    Displays the Shipping drop down
//------------------------------------------------

function displayZone( currentZone ){
  var alertBox  = 'alert(unescape("Shipping Zones __________%0A%0D';
  var buffer = "<select name='z' onChange='ChangeZone(this.selectedIndex);'>";
  for ( var x=0; x < shippingZones.length; x++ ){
  	buffer += "<option value='"+ x +"'";
	if ( currentZone == x ) 
	  buffer += " selected";
    buffer += ">";
    buffer += shippingZones[x].title;
    buffer += "</option>";
    alertBox += shippingZones[x].title;
    alertBox += "%0A%0D";
    alertBox += shippingZones[x].description;
    alertBox += "%0A%0D%0A%0D";
  }

  alertBox += '"))';
  //buffer += "</select> <a href='javascript:return false;' onclick='"+ alertBox +"; return false;'>Details</a><br>";
  buffer += "</select> <a href=\"delivery.htm\">Details</a><br>";
  return buffer;
}


//------------------------------------------------
//-- End of shopfitter_core.js
//------------------------------------------------// 
// This is the main shopping basket routines
// that utilise client-side javascript to store
// contents
//
// Copyright 2003,2004,2005,2006,2007 ShopFitter Ltd
//
// Last modified 08/06/10 by MWV: new taxexemption flag against each line - set within Products.js

//------------------------------------------------
//  FUNCTION:	GetProduct
//  PARAMETERS:	ID of product
//  RETURNS: 	Object of the product
//  PURPOSE:    Looks through the s[] array and gets the product. Returns an empty object if not there
//------------------------------------------------

function GetProduct( id ){
	var p 						= new Object();
	p.name 						= "";
	p.id 						= null;
	p.price 					= 0;
	p.shipping	   				= 0;
	p.tax						= 0;
	p.search                    = "";
	p.weight                    = 0;
	p.taxexemption                 = 0;
	
	//-- Run through the array
	for ( x=0; x < s.length; x++ ){
		var sp = s[x].split("^");
		//if ( sp[1].substring(0,sp[1].length-4) == id ){
		if ( sp[9] == id ){
			p.id 						= id;
			p.name						= sp[0];
			p.price 					= sp[4];
			p.shipping	    			= sp[5];
			p.tax						= sp[6] / 100;
			p.search                    = sp[7];
			p.weight                    = sp[8];
			if ( typeof(sp[10]) == "undefined" ) {
				p.taxexemption = 0;
			} else {
				p.taxexemption = sp[10];
			}
			
			break;
		}
	}
	return p;
}

//------------------------------------------------
//  FUNCTION:	CreateProductObject
//  PARAMETERS:	ID of product
//  RETURNS: 	Object of the product
//  PURPOSE:    Looks through the cookie for a specific product, unpacks, gets the values and puts into an object. 
//              This is to remove the need for the products.js file for a php based site
//------------------------------------------------

function CreateProductObject( database ){
	
	Token0 = database.indexOf("|", 0);
    Token1 = database.indexOf("|", Token0+1);
    Token2 = database.indexOf("|", Token1+1);
    Token3 = database.indexOf("|", Token2+1);
    Token4 = database.indexOf("|", Token3+1);
	Token5 = database.indexOf("|", Token4+1);
	Token6 = database.indexOf("|", Token5+1);
	Token7 = database.indexOf("|", Token6+1);

	var p 						= new Object();
	p.id 						= database.substring( 0, Token0 );                 	// Product ID
	p.price 					= database.substring( Token1+1, Token2 );          	// Price
	p.name 						= database.substring( Token2+1, Token3 );          	// Product Name/Description
	p.shipping	   				= database.substring( Token3+1, Token4 );           // Shipping Cost
	p.tax						= database.substring( Token4+1, Token5 );          	// Tax
	p.search                    = "";
	p.weight                    = database.substring( Token5+1, Token6 );			// Weight
	p.taxexemption              = database.substring( Token6+1, Token7 );			// Tax Exemption Flag
	
	return p;
}

//------------------------------------------------
//  FUNCTION:	GetProductWeight
//  PARAMETERS:	ID of product
//  RETURNS: 	Object of the product
//  PURPOSE:    Looks through the s[] array and gets the weight of the product.
//------------------------------------------------

function GetProductWeight( id ){
	var p 						= new Object();
	p.name 						= "";
	p.id 						= null;
	p.price 					= 0;
	p.shipping	   				= 0;
	p.tax						= 0;
	p.search                    = "";
	p.weight                    = 0;
	
	//-- Run through the array
	for ( x=0; x < s.length; x++ ){
		var sp = s[x].split("^");
		//if ( sp[1].substring(0,sp[1].length-4) == id ){
		if ( sp[9] == id ){
			p.id 						= id;
			p.name						= sp[0];
			p.price 					= sp[4];
			p.shipping	    			= sp[5];
			p.tax						= sp[6] / 100;
			p.search                    = sp[7];
			p.weight                    = sp[8];
			break;
		}
	}
	return p.weight;
}

//------------------------------------------------
//  FUNCTION:	CalculateShipping
//  PARAMETERS:	ProductObject, Zone number, shipping
//  RETURNS: 	Price
//  PURPOSE:    Looks up the shipping table to calculate price
//------------------------------------------------

function CalculateShipping( productObject, currentZone ){
  if ( productObject.id == null ) return 0;
  if ( currentZone > shippingZones.length ) return 0;
//This is where we change the shipping policy
  shippingPolicy = shippingZones[currentZone].policy;
  if ( shippingPolicy == 'peritem' ){
  
    return (1 * shippingZones[currentZone].peritem) + (1 * productObject.shipping);
    
  }else if ( shippingPolicy == 'perweight' ){
  
  	var priceSoFar 	= 0;
  	for ( x=zoneWeight[currentZone].length-1; x >= 0; --x ){
  		var sp = zoneWeight[currentZone][x].split("^");
      var weightRow = (1*sp[0]);
  		if ( weightRow <= (1*productObject.weight) ){
  			priceSoFar = (1*sp[1]);
  			break;
      	}
  	}
    
  	return priceSoFar;
  }else if ( shippingPolicy == 'itemsplice' ){
  
  	var priceSoFar 	= 0;
  	for ( x=zoneWeight[currentZone].length-1; x >= 0; --x ){
  		var sp = zoneWeight[currentZone][x].split("^");
      var weightRow = (1*sp[0]);
  		if ( weightRow <= gNumOrdered ){
  			priceSoFar = (1*sp[1])+ (1 * productObject.shipping) ;
  			break;
      	}
  	}
    
  	return priceSoFar;
    
  }else if ( shippingPolicy == 'perpercent' ){
    return  productObject.price * (shippingZones[currentZone].perpercent / 100);
  }else if ( shippingPolicy == 'perbasket' ){
    return  0;  //-- To be Calculated later
  }else if ( shippingPolicy == 'percartweight' ){
    return  0;  //-- To be Calculated later
  }else if ( shippingPolicy == 'basketsplice' ){
    return 0;   //--To be Calculated later
  }
}

//------------------------------------------------
//  FUNCTION:	CalculateTax
//  PARAMETERS:	ProductObject
//  RETURNS: 	Price
//  PURPOSE:    Calculates the tax on a product
//------------------------------------------------

function CalculateTax( productObject, currentZone, manualprice ){
  if ( productObject.id == null ) 
	return 0;

  if (DisplayTaxRow == false)
  {
    return 0;
  }

  //alert( 'CalculateTax.exempt: ' + (shippingZones[currentZone].taxrate/100) + " $ " + productObject.price );
  
  //-- This zone is not to be taxed
  if ( shippingZones[currentZone].taxexempt == 1 ) 
	return 0;
  
  //-- See if this product has a tax
  if ( productObject.tax != 0 ) 
    return (manualprice/100) * (productObject.tax*100); 
	//-- return (productObject.price/100) * (productObject.tax*100); 
	
  
  //-- return (productObject.price/100) * (shippingZones[currentZone].taxrate/100);
  return (manualprice/100) * (shippingZones[currentZone].taxrate/100);
}

function GetTaxRate( productObject, currentZone ) {
 if ( productObject.id == null ) 
	return 0;

  if (DisplayTaxRow == false)
  {
    return 0;
  }
  
  //-- This zone is not to be taxed
  if ( shippingZones[currentZone].taxexempt == 1 ) 
	return 0;
  
  //-- See if this product has a tax
  if ( productObject.tax != 0 ) 
    return (productObject.tax*100); 
	//-- return (productObject.price/100) * (productObject.tax*100); 
	
  
  //-- return (productObject.price/100) * (shippingZones[currentZone].taxrate/100);
  return (shippingZones[currentZone].taxrate/100);

}

//------------------------------------------------
//  FUNCTION:   CalculateShippingTax
//  PARAMETERS: TotalShipping, currentZone
//  Returns:    Vat On Shipping
//------------------------------------------------

function CalculateShippingTax( TotalShipping, currentZone ){
  if ( currentZone > shippingZones.length ) 
  return 0;
  
   var taxexempt      = GetCookie("exempt");    //TaxExempt
   if ( taxexempt == null ) taxexempt = "false";
  
  if (taxexempt == "true")
  return 0;
  
  if (DisplayTaxRow == false)
  {
    return 0;
  }
  
  for (var i = 0; i < taxItems.length; i++)
  {
  if (taxItems[i].key == shippingZones[currentZone].taxrate)
    return (TotalShipping * (taxItems[i].value / 100));
  }
  
  return 0;
}

//------------------------------------------------
//  FUNCTION:	calculateTotalShipping
//  PARAMETERS:	total, shiptotal, currentzone
//  RETURNS: 	Price
//  PURPOSE:    Calculates the total price for shipping
//------------------------------------------------

function calculateTotalShipping( subTotal, shipTotal, currentZone ){
  if ( currentZone > shippingZones.length ) 
	return 0;
  
  //alert(subTotal + " : " + shipTotal + " : " + currentZone);
  
  //-- Calculates the total shipping applying special business rules if necessary
  if ( shippingPolicy == 'perbasket' ){
    shippingCost  = shippingZones[currentZone].perbasket;
  }else shippingCost = shipTotal;
  
  if ( shippingPolicy == 'basketsplice' )
  {
    var priceSoFar 	= 0;
  	for ( x=zoneWeight[currentZone].length-1; x >= 0; --x )
  	{
      var sp = zoneWeight[currentZone][x].split("^");
      var weightRow = (1*sp[0]);
  		if ( weightRow <= subTotal )
  		{
  			priceSoFar = (1*sp[1]) ;
  			break;
  		}
     }
      	shippingCost = priceSoFar;
  }
  
  if ( shippingPolicy == 'percartweight' )
  {
    var priceSoFar 	= 0;
  	for ( x=zoneWeight[currentZone].length-1; x >= 0; --x )
  	{
      var sp = zoneWeight[currentZone][x].split("^");
      var weightRow = (1*sp[0]);
  		if ( weightRow <= gTotalWeight )
  		{
  			priceSoFar = (1*sp[1]) ;
  			break;
  		}
     }
      	shippingCost = priceSoFar;
  }
  
  //PerPercentage Total Calc
  if ( shippingPolicy == 'perpercent' ){
    shippingCost = subTotal * (shippingZones[currentZone].perpercent / 100);
  }

  if ( (shippingCost) >= shippingZones[currentZone].maxprice ){
    //-- If the total is over a given price, then the shipping is capped
    shippingCost = shippingZones[currentZone].maxprice;
  }
    if ( (subTotal) >= shippingZones[currentZone].maxthres ){
    //-- If the total basket is over a given price, then the shipping is zero
    return 0;
  }
	
  if ( (shippingCost) <= shippingZones[currentZone].minprice ){
    //-- if the total is below a certain value, then the shipping must be at least
    shippingCost = shippingZones[currentZone].minprice;
  }
  
  //if ( (subTotal) <= shippingZones[currentZone].minthres ){
  //  //-- if the total is below a certain value, then the shipping is zero
  //  return 0;
  //}
  
  //-- Apply shipping tax
  if ( shippingZones[currentZone].taxrate > 0 ){
    shippingCost = shippingCost + (shippingCost * (shippingZones[currentZone].taxrate / 100));
  }
  
  return shippingCost;
}


//------------------------------------------------
//  FUNCTION:	displayShipping
//  PARAMETERS:	CurrentShipping
//  RETURNS: 	string to display
//  PURPOSE:    Displays the Shipping drop down
//------------------------------------------------

function displayZone( currentZone ){
  var alertBox  = 'alert(unescape("Shipping Zones __________%0A%0D';
  var buffer = "<select name='z' onChange='ChangeZone(this.selectedIndex);'>";
  for ( var x=0; x < shippingZones.length; x++ ){
  	buffer += "<option value='"+ x +"'";
	if ( currentZone == x ) 
	  buffer += " selected";
    buffer += ">";
    buffer += shippingZones[x].title;
    buffer += "</option>";
    alertBox += shippingZones[x].title;
    alertBox += "%0A%0D";
    alertBox += shippingZones[x].description;
    alertBox += "%0A%0D%0A%0D";
  }

  alertBox += '"))';
  //buffer += "</select> <a href='javascript:return false;' onclick='"+ alertBox +"; return false;'>Details</a><br>";
  buffer += "</select> <a href=\"delivery.htm\">Details</a><br>";
  return buffer;
}


//------------------------------------------------
//-- End of shopfitter_core.js
//------------------------------------------------

