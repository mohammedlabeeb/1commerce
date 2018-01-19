//		Author: Stuart Cochrane
//		URL: www.freecontactform.com
//		Date: 2012
//		
//		License:
//		
//		Copyright (c) 2012 Stuart Cochrane
//		
//		Permission is hereby granted, to any person legally purchasing a copy
//		of this software and associated documentation files (the "Software"), to deal
//		in the Software with little restriction, including the rights to use, copy, 
//		modify, merge and publish the Software, subject to the following conditions:
//		
//		A. The copyright, permission and conditional notices shall be included in
//		   all copies or substantial portions of the Software.
//		
//		B. Single license holder can use this Software on a single Licensed Domain only.
//			 This includes sub-domains (of Licensed Domain).
//		
//		C. You may not convey/distribute this software to any third party without
//		   express permission from the Copyright Holder/Author.
//		
//		D. You can surrender you license to a third party providing you also surrender
//		   the licensed Domain name to the same party.
//		
//		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
//		IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//		FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//		AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
//		LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
//		OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
//		THE SOFTWARE.


var FCFrequired = {

 field : [],

 add : function(name, type, mess) {
   this.field[this.field.length] = [name,type,mess];
 },

 out : function() {
   return this.field;
 },

 clear: function() {
   this.field = [];
 }

}

var FCFvalidate = {

 check : function(cform) {
	
 	
  // update the submit button
  try {
  	var button_value = document.getElementById('form_submit_button').value;
  	document.getElementById('form_submit_button').value = "Please Wait.....";
  	// document.getElementById('form_submit_button').disabled = true;
  } catch(e) { }
 	
   var error_message = 'Please fix the following errors:\n\n';
   var mess_part = '';
   var to_focus = '';

   var tmp = true;

   // loop all FCFrequired fields
   for(var i=0; i<FCFrequired.field.length; i++) {

       if(this.checkit(FCFrequired.field[i][0],FCFrequired.field[i][1],cform)) {
          // field okay
       } else {


         if( FCFrequired.field[i][1] == "RADIO_GROUP" ||
             FCFrequired.field[i][1] == "CHECKBOX_GROUP" ||
             FCFrequired.field[i][1] == "RADIO_SINGLE" ||
             FCFrequired.field[i][1] == "CHECKBOX_SINGLE" ||
             FCFrequired.field[i][1] == "SELECT_MULTIPLE" ||
             FCFrequired.field[i][1] == "SELECT") {
           mess_part = " Nothing was selected";
         } else {
           mess_part = "Entered data is not valid";
         }

         error_message = error_message + FCFrequired.field[i][2] + ': ' + mess_part + '\n';

         // only focus on fields with valid ID's
         if(has_id(FCFrequired.field[i][0]) && to_focus.length == 0) {
           to_focus = FCFrequired.field[i][0];
         }
         tmp = false;

       }

   // compare two fields?
   if(FCFrequired.field[i][1] == "COMPARE") {
   	
   	try {
   		var tmp_z = FCFrequired.field[i][0].split(":");
   		var tmp_a = $$(tmp_z[0]);
   		var tmp_b = $$(tmp_z[1]);
   		
   		if(tmp_a == tmp_b) {
   			// matched
   		} else {
   			mess_part = 'Do not match'
   			error_message = error_message + FCFrequired.field[i][2] + ': ' + mess_part + '\n';
   			tmp = false;
   		}
   	} catch(e) {}
   	
   }
       
       
   } // for

   if(!tmp) {
     alert(error_message);
   }

   if(to_focus.length > 0) {
     document.getElementById(to_focus).focus();
   }

   if(tmp==false) {
   	try {
   		document.getElementById('form_submit_button').value = button_value;
   		// document.getElementById('form_submit_button').disabled = false;
   	} catch(e) {}
   }
   
   return tmp;

 },

 checkit : function(cvalue,ctype,cform) {
   exp : '';
   ischecked : false;

   // make sure we have some cvalue
   if( ctype == 'RADIO_GROUP' ||
       ctype == 'RADIO_SINGLE' ||
       ctype == 'CHECKBOX_GROUP' ||
       ctype == 'CHECKBOX_SINGLE' ||
       ctype == 'SELECT_MULTIPLE' ||
       ctype == 'SELECT') {
     // continue
   } else {
   	
     if(ctype == "COMPARE") {
     	// continue
     } else if(this.trim($$(cvalue)).length < 1) {
       return false;
     }
     
   }


   switch(ctype) {


     case "RADIO_GROUP":
     case "CHECKBOX_GROUP":

       var radio_count = cform[cvalue].length;
       ischecked = false;

       for(var g=0; g < radio_count; g++) {
         if(cform[cvalue][g].checked) {
           ischecked = true;
         }
       }

       return ischecked;

     break;

     case "COMPARE":
        return true;
     break;
     
     
     case "RADIO_SINGLE":
     case "CHECKBOX_SINGLE":
       if(cform[cvalue].checked==true) { return true; } else { return false; }
     break;

     case "SELECT":
     case "SELECT_MULTIPLE":
     case "NOT_EMPTY":
       if(this.trim($$(cvalue)).length < 1) { return false; } else { return true; }
     break;

     case "ALPHA":
       exp = /^[A-Za-z]+$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true; }
     break;

     case "ALPHASPACE":
       exp = /^[A-Za-z ]+$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true; }
     break;

     case "NUMERIC":
       exp = /^[0-9]+$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true; }
     break;
     
     case "NUMERICPLUS":
       exp = /^[0-9 +-.]+$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true; }
     break;

     case "ALPHANUM":
       exp = /^[a-zA-Z0-9]+$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true; }
     break;

     case "ALPHANUMSPACE":
       exp = /^[a-zA-Z0-9 ]+$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true; }
     break;

     case "EMAIL":
       exp = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true; }
     break;

     case "YYYYMMDD":
       exp = /^(19|20)[0-9][0-9][- /.](0[1-9]|1[012])[-/.](0[1-9]|[12][0-9]|3[01])$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true;}
     break;

     case "DDMMYYYY":
       exp = /^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[-/.](19|20)[0-9][0-9]$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true;}
     break;

     case "MMDDYYYY":
       exp = /^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[-/.](19|20)[0-9][0-9]$/;
       if($$(cvalue).match(exp)==null) { return false; } else { return true;}
     break;

     default:
       exp = new RegExp(ctype);
       if($$(cvalue).match(exp)) { return true } else { return false; }
   } // switch
 },

 trim : function(s) {
   if(s.length > 0) {
     return s.replace(/^\s+/, '').replace(/\s+$/, '');
   } else {
     return s;
   }
 }

} // var

function $$(id) {
 if( !has_id(id) && !has_name(id)) {
   alert("Field " + id + " does not exist!\n Form validation configuration error.");
   return false;
 }
 if(has_id(id)) {
   return document.getElementById(id).value;
 } else {
   return;
 }
}

function has_id(id) {
 try {
   var tmp = document.getElementById(id).value;
 } catch(e) {
   return false;
 }
 return true;
}

function has_name(nm) {
 try {
   var tmp = cfrm.nm.type;
 } catch(e) {
   return false;
 }
 return true;
}

function $val(id) {
	return document.getElementById(id);
}
function trim(id) {
	$val(id).value = $val(id).value.replace(/^\s+/, '').replace(/\s+$/, '');
}
