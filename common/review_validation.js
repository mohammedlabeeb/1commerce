//REVIEW FORM INPUT VALIDATION ----------------------------------------------------------------------------------------------------------------------------------

function validate_review(){
	//initialise message box
	document.getElementById("MESSAGE").innerHTML = "";
	document.getElementById("MESSAGE").className = "";
	var errors_array = new Array();
	var allTags = document.getElementsByClassName("review");
	for(var i = 0; i < allTags.length; i++){
		//initialise input box before validation as may previously classed as invalid
		//if(allTags[i].nodeName == "INPUT"){allTags[i].className = "review";}
		allTags[i].className = "review";
		switch(allTags[i].name){
			case "RV_AUTHOR":
				check_for_empty_field(allTags[i], "Please enter your name");
				check_for_valid_chars(allTags[i], "Invalid characters found within your name");
				break;
			case "RV_TOWN":
				check_for_empty_field(allTags[i], "Please enter your home town");
				check_for_valid_chars(allTags[i], "Invalid characters found within the name of your home town");
				break;
			case "RV_COUNTRY":
				check_for_empty_field(allTags[i], "Please enter your home country");
				check_for_valid_chars(allTags[i], "Invalid characters found the name of your country");
				break;
			case "RV_ORDER":
				check_for_empty_field(allTags[i], "Please enter your previous Order No. against this product");
				check_for_valid_chars(allTags[i], "Please enter a valid order number");
				break;
			case "RV_TITLE":
				check_for_empty_field(allTags[i], "Please enter a title for your review");
				check_for_valid_chars(allTags[i], "Invalid characters found within your review title text");
				break;
			case "RV_TEXT":
				check_for_empty_field(allTags[i], "Please enter your review details");
				check_for_valid_chars(allTags[i], "Invalid characters found within your review text");
				break;
			case "custom_antispam_field":
				var input_box = allTags[i];
				var reply = input_box.value;
				var answer = document.getElementsByName("custom_antispam_field_answer")[0].value;
				if(reply !== answer){
					input_box.className += " invalid";
					input_box.focus();
					errors_array.push("The security challenge (anti-spam) field was entered incorrectly" + "<br/>");
				}
				break;
			case "HONEYPOT":
				var input_box = allTags[i];
				if(input_box.value === ""){
					//OK - this field should always be empty and if it's not then it must have been filled by a spam bot
				}else{
					var msg = "Honeypot trap has been triggered by SPAM bot";
					errors_array.push(msg + "<br/>");	
				}
				break;
			default:
				break;
		}
	}
	if(errors_array.length > 0){
		error_message(errors_array, "red");
		return false;
	}
	var msg = "";
	if(document.getElementsByName("RV_PUBLISHED")[0].value == "Y"){
		msg = "Thank you for submitting a review against this product. It will now appear in our listings below.";
	}else{
		msg = "Thank you for submitting a review against this product. Subject to approval it will shortly appear in our listings below.";
	}
	alert(msg);
	
	return true;

	function check_for_empty_field(input_box, msg){
		if(input_box.value === ""){
			input_box.className += " invalid";
			input_box.focus();
			errors_array.push(msg + "<br/>");	
		}
		return;
	}
	
	function check_for_basic_chars(input_box, msg){
		if(input_box.value != ""){
			if (!(/^[a-z0-9, ]+$/i.test(input_box.value))) {
				input_box.className += " invalid";
				input_box.focus();
				errors_array.push(msg + "<br/>");
			}
		}
		return;
	}
	
	function check_for_valid_chars(input_box, msg){
		if(input_box.value != ""){
			if (!(/^[a-z0-9,.'"-()!&@£$ \n\r/-]+$/i.test(input_box.value))) {
				input_box.className += " invalid";
				input_box.focus();
				errors_array.push(msg + "<br/>");
			}else{
				//Anti-Spam check for characters [<, >, http:, //] and their special character converted equivalents. There should be no reason why a user
				//would want to key these characters for review purposes.
				//If any of these characters are found in whatever form then the input shall be deemed to be spam for obvious reasons
				var pattern = /<|>|http:|www.|www&#46;|.co|&#46;co|\/\/|&lt;|&gt;|http&#58;|&#47;&#47;|&amp;lt;|&amp;gt;|http&amp;#58;|&amp;#47;&amp;#47;/;
				if(pattern.test(input_box.value)){
					input_box.className += " invalid";
					input_box.focus();
					errors_array.push(msg + "<br/>");
				}
			}
		}
		return;
	}
	
	function error_message(errors_array, warning){
		var message = ""; var no_changes = 0;
		for( var i = 0; i < errors_array.length; i++){
			message += errors_array[i];
			if(errors_array[i] == "No changes have been made - please continue to Proof your Storyboard"){no_changes = 1;}
		}
		//document.getElementById("MESSAGE").innerHTML = message;
		//document.getElementById("MESSAGE").className = warning;
		if (errors_array.length == 1 && no_changes ==1){
			alert("No changes have been made - please continue to Proof your Storyboard");
		}else{
			document.getElementById("MESSAGE").innerHTML = message;
			document.getElementById("MESSAGE").className = warning;
			//alert("Errors Found - Please correct errors to continue or Logout");
		}
		return;
	}
}

//General functions ------------------------------------------------------------------------------------------------------------------------------------------
function show_review_form(){
	document.getElementById("review_form").style.display = "block";
	
	return;
}

