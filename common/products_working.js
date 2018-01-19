//-- The symbol to use for the javascript trolley and checkout
MonetarySymbol	= "&pound;"
//-- List of Products; used in the SEARCH and the checkout/basket
//-- Array key
//-- name^id.htm^summary^keyword^price^shipping^tax^shipping^weight^id^defaulttaxrate
var s = new Array();
var x=0;

//-- Start Products Definitions
s[x++]="Paco123456^^^^1299.99^5.67^4^^1.23^01000101001";
s[x++]="Diablo^^^^199.99^0.00^0^^0.00^01000101002";
s[x++]="English Marble^^^^399.99^0.00^0^^0.00^01000101003";
s[x++]="French Marble^^^^499.99^0.00^0^^0.00^01000201001";
s[x++]="English Stone^^^^599.99^0.00^0^^0.00^01000201002";
s[x++]="Cast Iron Fireplaces^^^^699.99^0.00^0^^0.00^01000201003";
s[x++]="Bespoke Marble^^^^199.99^0.00^0^^0.00^01000301001";
s[x++]="Acquisitions X-Fire Electric^^^^999.99^0.00^0^^0.00^01000301002";
s[x++]="Saphire^^^^1099.99^0.00^0^^0.00^01000301003";
s[x++]="Trifire^^^^1199.99^0.00^0^^0.00^02000101001";
s[x++]="Emerald^^^^1299.99^0.00^0^^0.00^02000101002";
s[x++]="Sunrise^^^^1399.99^0.00^0^^0.00^01000201000";
s[x++]="Lagoon^^^^1599.99^0.00^0^^0.00^02000202002";
s[x++]="Living Art^^^^1799.99^0.00^0^^0.00^03000303003";
s[x++]="Bizet^^^^1999.99^0.00^0^^0.00^04000404004";
s[x++]="SP5^^^^2399.99^0.00^0^^0.00^05000505005";
s[x++]="SP8^^^^2599.99^0.00^0^^0.00^06000606006";
s[x++]="Saxo Slimline^^^^950.99^0.00^0^^0.00^11111111111";
s[x++]="Scenic^^^^1250.99^0.00^0^^0.00^22222222222";
s[x++]="Passeo^^^^1349.99^0.00^0^^0.00^33333333333";
s[x++]="Apollo8^^^^12.99^5.67^4^^1.23^11222233444";
s[x++]="Metro Tunnel^^^^1749.99^0.00^0^^0.00^22333344555";
s[x++]="Venteo^^^^2349.99^0.00^0^^0.00^33444455666";
s[x++]="Glazer^^^^1750.00^0.00^0^^0.00^44555566777";
s[x++]="Otello 4^^^^1850.00^0.00^0^^0.00^77888899001";
s[x++]="Senza^^^^123.99^0.00^0^^0.00^10100010100";
s[x++]="Tennyson^^^^1234.99^0.00^0^^0.00^09000909009";
s[x++]="Buckingham^^^^1659.99^0.00^0^^0.00^08000808008";
s[x++]="Moray^^^^1670.99^0.00^0^^0.00^12000101005";
s[x++]="Henley^^^^1450.00^0.00^0^^0.00^01000203004";
s[x++]="Mayfair^^^^0.00^0.00^0^^0.00^";
s[x++]="Earisdon^^^^0.00^0.00^0^^0.00^";
s[x++]="Blake^^^^0.00^0.00^0^^0.00^";
s[x++]="Stonehaven^^^^0.00^0.00^0^^0.00^";
s[x++]="Escape^^^^0.00^0.00^0^^0.00^";
s[x++]="Argon^^^^0.00^0.00^0^^0.00^";
s[x++]="Scandium^^^^0.00^0.00^0^^0.00^";
s[x++]="Radium^^^^0.00^0.00^0^^0.00^";
s[x++]="Neon^^^^0.00^0.00^0^^0.00^";
s[x++]="Athos^^^^0.00^0.00^0^^0.00^";
s[x++]="Mafra^^^^0.00^0.00^0^^0.00^";
s[x++]="York^^^^0.00^0.00^0^^0.00^";
s[x++]="Regent^^^^0.00^0.00^0^^0.00^";
s[x++]="Stone Henge^^^^0.00^0.00^0^^0.00^";
s[x++]="Salisbury^^^^0.00^0.00^0^^0.00^";
s[x++]="Ashbourne^^^^0.00^0.00^0^^0.00^";
s[x++]="Victorian Corbel^^^^0.00^0.00^0^^0.00^";
s[x++]="Glasgow^^^^0.00^0.00^0^^0.00^";
s[x++]="Capri^^^^0.00^0.00^0^^0.00^";
s[x++]="Ulster^^^^0.00^0.00^0^^0.00^";
s[x++]="Little Wenlock Classic^^^^0.00^0.00^0^^0.00^";
s[x++]="Fusion Basket^^^^0.00^0.00^0^^0.00^";
s[x++]="Fissure 1400^^^^0.00^0.00^0^^0.00^";
s[x++]="G.S.D. 890^^^^0.00^0.00^0^^0.00^";
s[x++]="Olympia^^^^0.00^0.00^0^^0.00^";
s[x++]="Rimini^^^^0.00^0.00^0^^0.00^";
s[x++]="Tahiche^^^^0.00^0.00^0^^0.00^";
s[x++]="The Regal^^^^0.00^0.00^0^^0.00^";
s[x++]="The Henley^^^^0.00^0.00^0^^0.00^";
s[x++]="The Crown^^^^0.00^0.00^0^^0.00^";
s[x++]="The Tradition^^^^0.00^0.00^0^^0.00^";
s[x++]="The Landsdown^^^^0.00^0.00^0^^0.00^";
s[x++]="The Montgomery^^^^0.00^0.00^0^^0.00^";
s[x++]="The Edinburgh^^^^0.00^0.00^0^^0.00^";
s[x++]="The Pimlico^^^^0.00^0.00^0^^0.00^";
s[x++]="The Pembroke^^^^0.00^0.00^0^^0.00^";
s[x++]="The Edwardian^^^^0.00^0.00^0^^0.00^";
s[x++]="The Prince^^^^0.00^0.00^0^^0.00^";
s[x++]="The Northmoor^^^^0.00^0.00^0^^0.00^";
s[x++]="The Monroe^^^^0.00^0.00^0^^0.00^";
s[x++]="The Shire Newton^^^^0.00^0.00^0^^0.00^";
s[x++]="Little Wenlock^^^^0.00^0.00^0^^0.00^";
s[x++]="Much Wenlock^^^^0.00^0.00^0^^0.00^";
s[x++]="Berrington^^^^0.00^0.00^0^^0.00^";
s[x++]="Minsterley^^^^0.00^0.00^0^^0.00^";
s[x++]="Rembrandt^^^^0.00^0.00^0^^0.00^";
s[x++]="100 multi-fuel^^^^0.00^0.00^0^^0.00^";
s[x++]="200 multi-fuel^^^^0.00^0.00^0^^0.00^";
s[x++]="300 and 350 multi-fuel^^^^0.00^0.00^0^^0.00^";
s[x++]="500 multi-fuel^^^^0.00^0.00^0^^0.00^";
s[x++]="505 multi-fuel and woodburning^^^^0.00^0.00^0^^0.00^";
s[x++]="700 multi-fuel and woodburning^^^^0.00^0.00^0^^0.00^";
s[x++]="Cooking stove Ironheart^^^^0.00^0.00^0^^0.00^";
s[x++]="The Firewall 39inch Original^^^^0.00^0.00^0^^0.00^";
s[x++]="The Firewall 41inch Original^^^^0.00^0.00^0^^0.00^";
s[x++]="The Firewall 48inch Widescreen^^^^0.00^0.00^0^^0.00^";
s[x++]="The Firewall 50in Optica and Optica Image GRC^^^^0.00^0.00^0^^0.00^";
s[x++]="The Firewall 60inch Fissure^^^^0.00^0.00^0^^0.00^";
s[x++]="Radium^^^^0.00^0.00^0^^0.00^";
//-- End Products Definitions

//--- ----------------------------------------------
//--- Define the Shipping Zones
var shippingZones   = new Array();
var zoneWeight      = new Array();   // weight^price
var taxItems      = new Array();   // taxcode^percent
var shippingPolicy  = "percartweight";

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
taxItems[x++] = new taxrate("VAT",15);
taxItems[x++] = new taxrate("TAX",10);
taxItems[x++] = new taxrate("inc vat",0);
//-- End Tax Definitions

//-- Start Shipping Zones Definitions
x=0;

shippingZones[x++] = new zone("Please Select","0",0," ",0,0,0,0,0,0,0,"none")
zoneWeight[0] = new Array();


shippingZones[x++] = new zone("UK mainland","0",0,"UK. Within the mainland of the UK",150,100,0,0,0,0,0,"percartweight")
zoneWeight[1] = new Array();


shippingZones[x++] = new zone("All European Countries","0",0,"All countries in Europe",150,100,0,0,5,0,0,"peritem")
zoneWeight[2] = new Array();


shippingZones[x++] = new zone("USA","0",0,"All states in United States of America",150,100,0,0,6,0,0,"peritem")
zoneWeight[3] = new Array();
//-- End Shipping Zone Definitions