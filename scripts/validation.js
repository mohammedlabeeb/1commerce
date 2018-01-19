function validateGeneralEnquiry(form) {
	
	var errors = "";
	if(form.name.value == "")
		errors += " - Name is required\n";
	if(form.phone.value == "")
		errors += " - Telephone number is required\n";
	if((form.email.value == "") || ((form.email.value).indexOf('@') >= (form.email.value).lastIndexOf('.')))
		errors += " - A valid email address is required\n";
	if(form.enquiry.value == "")
		errors += " - Enquiry is required\n";
	if(errors != "")
		alert("The following error(s) occured when submitting your enquiry:\n\n"+errors+"\nPlease correct these errors and resubmit your enquiry.");
	return (errors == "");
	
}
