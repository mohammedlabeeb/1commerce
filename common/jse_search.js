// ---------- script properties ----------

var pricechk = 0;

//alert ("pricechk = " + pricechk); 

var include_num = 1;
var bold = 0;

// ---------- sites ----------

var cookies = document.cookie;
var p = cookies.indexOf("sf4=");

if (p != -1) {
	var st = p + 4;
	var en = cookies.indexOf(";", st);
	if (en == -1) {
		en = cookies.length;
	}
	var d = cookies.substring(st, en);
	var darr = unescape(d).split("^");
	if (darr.length == 2) pricechk = 1; 
	d = darr[0];
}
var od = d;
var m = 0;
if (d.charAt(0) == '"' && d.charAt(d.length - 1) == '"') {
	m = 1;
}

// alert("d = " + d);

var r = new Array();
var co = 0;

if (m == 0) {
	var woin = new Array();
	var w = d.split(" ");
	for (var a = 0; a < w.length; a++) {
		woin[a] = 0;
		if (w[a].charAt(0) == '-') {
			woin[a] = 1;
		}
	}
	for (var a = 0; a < w.length; a++) {
		w[a] = w[a].replace(/^\-|^\+/gi, "");
	}
	a = 0;

//	alert("s.length = " + s.length);


	for (var c = 0; c < s.length; c++) {
		pa = 0;
		nh = 0;
		
		//alert("woin.length = " + woin.length + "   w[0] = " + w[0]);
		for (var i = 0; i < woin.length; i++) {
			if (woin[i] == 0) {
				nh++;
				var pat = new RegExp(w[i], "i");
				
				
				// ---  PRICE CHK --- //
				if (pricechk == 1) {
					var s_array = s[c].split("^");
					// s_array [4] = PRICE
					
					var upper = parseInt(w[0]) + 10;								
					if (s_array[4] >= w[0] && s_array[4] < upper) {
						var rn = 0;
					} else {
						var rn = -1;	
					}
				// ---  END PRICE CHK --- //	
									
				} else {
					var rn = s[c].search(pat);
				}
				
				if (rn >= 0) {
					pa++;
				} else {
					pa = 0;
				}
			}
			if (woin[i] == 1) {
				var pat = new RegExp(w[i], "i");
				
				if (pricechk == 1) {
					var s_array = s[c].split("^");
					// s_array [4] = PRICE
					
					var upper = parseInt(w[0]) + 10;								
					if (s_array[4] >= w[0] && s_array[4] < upper) {
						var rn = 0;
					} else {
						var rn = -1;	
					}
										
				} else {
					var rn = s[c].search(pat);
				}

				//alert("rn2 = s[c].search(pat) = " + rn + "      s[c] = " + s[c]);

				if (rn >= 0) {
					pa = 0;
				}
			}
		}
		if (pa == nh) {
			r[a] = s[c];
			a++;
		}
	}
	co = a;
}// end if  (m)




if (m == 1) {
	//d = d.replace(/"/gi, "");
	var a = 0;
	var pat = new RegExp(d, "i");
	for (var c = 0; c < s.length; c++) {
		var rn = s[c].search(pat);
		if (rn >= 0) {
			r[a] = s[c];
			a++;
		}
	}
	co = a;

}


function return_query() {
	document.jse_Form.d.value = od;
}

function num_jse() {
	document.write(co);
}

function out_jse() {
	if (co == 0) {
		document.write('<p>Your search did not match any products. Make sure all keywords are spelt correctly.</p><p>Try different or more general keywords.</p>');
		return;
	}
	
	document.write( '<ol>' );
	
	//alert ("r.length = " + r.length);
	
	for (var a = 0; a < r.length; a++) {
		var os = r[a].split("^");
		
		// os[4] = price
		//alert ("\n" + "os[0] = " + os[0] + "\n" + "os[1] = " + os[1] + "\nos[2] = " + os[2] + "\nos[3] = " + os[3] + "\nos[4] = " + os[4] + "\nos[5] = " + os[5] + "\nos[6] = " + os[6] + "\nos[7] = " + os[7] + "\nos[8] = " + os[8] + "\nos[9] = " + os[9] + "\nos[10] = " + os[10]);
		
		if (bold == 1 && m == 1) {
			var br = "<strong>" + d + "</strong>";
			os[2] = os[2].replace(pat, br);
		}
		
		document.write('<li class="search-li"><a href="', os[1], '">', os[0], '</a>, '</li>');
		//document.write('<li class="search-li"><a href="', os[1], '">', os[0], '</a><br />', os[2], '</li>');
	}
	document.write( '</ol>' );

}

