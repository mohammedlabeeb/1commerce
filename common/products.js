//-- The symbol to use for the javascript trolley and checkout - now pulled from the shopfitter 5 database
MonetarySymbol	= ""
//-- List of Products; used in the SEARCH and the checkout/basket
//-- Array key
//-- name^id.htm^summary^keyword^price^shipping^tax^shipping^weight^id^defaulttaxrate
var s = new Array();
var x=0;

//-- Start Products Definitions
s[x++]="id^^^^0.00^0.00^0.00^^0.00^00000000000";

//-- End Products Definitions

//--- ----------------------------------------------
//--- Define the Shipping Zones
var shippingZones   = new Array();
var zoneWeight      = new Array();   // weight^price
var taxItems      = new Array();   // taxcode^percent

function zone(title,taxrate,taxexempt,description,maxthres,maxprice,minthres,minprice,peritem,perbasket,perpercent,perpolicy){
	this.title        = title;
	this.taxrate      = taxrate;
	this.taxexempt    = taxexempt;
	this.description	= description;
	this.maxthres     = maxthres;
	this.maxprice     = maxprice;
	this.minthres     = minthres;
	this.minprice     = minprice;
	this.peritem      = peritem;
	this.perbasket    = perbasket;
	this.perpercent   = perpercent;
	this.policy   = perpolicy;
}

function taxrate(_key, _value){
	this.key        = _key;
	this.value        = _value;
}

//-- Start Tax Definitions
x=0;
taxItems[x++] = new taxrate("NONE",0);

//-- End Tax Definitions

//-- Start Shipping Zones Definitions
x=0;

shippingZones[x++] = new zone("Please Select","0",0," ",0,0,0,0,0,0,0,"none")
zoneWeight[0] = new Array();

//-- End Shipping Zone Definitions

