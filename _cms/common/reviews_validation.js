//REVIEW FORM INPUT VALIDATION ----------------------------------------------------------------------------------------------------------------------------------

function amend_review_validation(){
	//initialise message box
	document.getElementById("MESSAGE").innerHTML = "";
	document.getElementById("MESSAGE").className = "";
	var errors_array = new Array();
	var allTags = document.getElementsByClassName("review");
	for(var i = 0; i < allTags.length; i++){
		//initialise input box before validation as may previously classed as invalid
		//if(allTags[i].nodeName == "INPUT"){allTags[i].className = "review";}
		allTags[i].className = "review";
		//validate fields within maintain_reviews.php
		if(allTags[i].name.substr(0, 10) == "PUBLISHED_"){
			if(allTags[i].value != "Y" && allTags[i].value != "N"){
				var msg = "Published Flag must be set to 'Y' or 'N'";
				allTags[i].className += " invalid";
				allTags[i].focus();
				errors_array.push(msg + "<br/>");
			}
		}
		//validate fields within amend_reviews.php
		switch(allTags[i].name){
			case "RV_PUBLISHED":
				if(allTags[i].value != "Y" && allTags[i].value != "N"){
					var msg = "Published Flag must be set to 'Y' or 'N'";
					allTags[i].className += " invalid";
					allTags[i].focus();
					errors_array.push(msg + "<br/>");
				}
				break;
			case "RV_TEXT":
				check_for_empty_field(allTags[i], "Please enter your Review details", errors_array);
				errors_array = check_for_valid_chars(allTags[i], "Invalid characters found within the Review text", errors_array);
				break;
			case "RV_REPLY":
				errors_array = check_for_valid_chars(allTags[i], "Invalid characters found within the name of your Reply", errors_array);
				break;
			default:
				break;
		}
	}
	if(errors_array.length > 0){
		error_message(errors_array, "red");
		return false;
	}
	
	return true;
}

function check_for_empty_field(input_box, msg, errors_array){
	if(input_box.value == ""){
		input_box.className += " invalid";
		input_box.focus();
		errors_array.push(msg + "<br/>");	
	}
	return errors_array;
}

function check_for_basic_chars(input_box, msg, errors_array){
	if(input_box.value != ""){
		if (!(/^[a-z0-9, ]+$/i.test(input_box.value))) {
			input_box.className += " invalid";
			input_box.focus();
			errors_array.push(msg + "<br/>");
		}
	}
	return errors_array;
}

function check_for_valid_chars(input_box, msg, errors_array){
	if(input_box.value != ""){
		if (!(/^[a-z0-9,.'"-()!&@£$ \n\r/-]+$/i.test(input_box.value))) {
			input_box.className += " invalid";
			input_box.focus();
			errors_array.push(msg + "<br/>");
		}
	}
	return errors_array;
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

//General functions ------------------------------------------------------------------------------------------------------------------------------------------
