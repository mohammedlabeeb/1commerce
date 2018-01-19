function add_table_row(){
	var table = document.getElementById("quantity_discounts");
	var table_rows = getRowsNameStartingWith(table, "ROW_");
	var row_count = document.getElementById("ROW_COUNT").value;
	//get last row in table and check data has been entered into it
	var cells = table_rows[row_count - 1].getElementsByTagName("td");
	var found = 0;
	for(var i = 0; i < cells.length; i++){
		if(cells[i].childNodes[0].value){
			found = 1;
		}
	}
	if(found == 1){
		//first add a line delete to the previous last line
		cells[2].innerHTML = "<a href='#' ><span style='color: red;' onclick='delete_table_row(" + (row_count - 1) + ");' >delete line</span></a>";
		
		//data has been entered into the last row of the table - so add an extra row
		var row = table.insertRow(-1);
		row.setAttribute('name', "ROW_" + (row_count));
		cell = row.insertCell(0);
		cell.innerHTML = "<input name='QTY_" + row_count + "' class='' type='text' size='4' value='' />";
		cell = row.insertCell(1);
		cell.innerHTML = "<input name='ADJUST_" + row_count + "' class='' type='text' size='4' value='' onkeyup='add_table_row();' />";
		cell = row.insertCell(2);
		//cell.innerHTML = "<a href='#' ><span style='color: red;' onclick='delete_table_row(" + row_count + ");' >delete line</span></a>";
		cell.innerHTML = "&nbsp;";
		document.getElementById("ROW_COUNT").value = parseInt(row_count) + 1;
	}
	return;
}

function getRowsNameStartingWith(table, row_name) {
	var children = table.getElementsByTagName('tr');
	var elements = [], child;
	for (var i = 0; i < children.length; i++) {
		child = children[i];
		var child_name = child.getAttribute('name');
		if(child_name){
			if (child_name.substr(0, row_name.length) == row_name){
				elements.push(child);
			}
		}
	}
	return elements;
}

function delete_table_row(row){
	var table = document.getElementById("quantity_discounts");
	var table_rows = getRowsNameStartingWith(table, "ROW_");
	var row_count = document.getElementById("ROW_COUNT").value;
//	if(row == parseInt(row_count) - 1){
//		alert("You cannot delete the last row of the table");
//		return;	
//	}
	//delete the row in question
	//for some reason "var row_to_delete = table.getElementsByName("ROW_" + row)[0];" will not grab the row - beats me!
	var row_to_delete = getRowsNameStartingWith(table, "ROW_" + row)[0];
	row_to_delete.parentNode.removeChild(row_to_delete);
	document.getElementById("ROW_COUNT").value = parseInt(row_count) - 1;
	//now renumber the remaining rows
	var table_rows = getRowsNameStartingWith(table, "ROW_");
	for(var i = 0; i < table_rows.length; i++){
		//rename the table row
		table_rows[i].setAttribute('name', "ROW_" + (i));
		//now rename the input boxes
		cells = table_rows[i].getElementsByTagName("td");
		cells[0].childNodes[0].setAttribute('name', "QTY_" + i);   //quantity input box
		cells[1].childNodes[0].setAttribute('name', "ADJUST_" + i);   //adjustment input box
		cells[2].childNodes[0].childNodes[0].setAttribute('onclick', "delete_table_row('" +  i + "');");   //<a href="#"><span> tag
	}

	return;
}

function validate_discount_table(){
	//initialise message box
	if(document.getElementById("MESSAGE") != null){
		document.getElementById("MESSAGE").innerHTML = "";
		document.getElementById("MESSAGE").className = "";
	}
	if(document.getElementById("MESSAGE_ARRAY") != null){
		document.getElementById("MESSAGE_ARRAY").innerHTML = "";
		document.getElementById("MESSAGE_ARRAY").className = "";
	}
	var errors_array = new Array();
	var table = document.getElementById("quantity_discounts");
	if(table != null){
		var table_rows = getRowsNameStartingWith(table, "ROW_");
		for(var i = 0; i < table_rows.length; i++){
			cells = table_rows[i].getElementsByTagName("td");
			if(i < table_rows.length - 1){
				//initialise class of input boxes before validation as may previously have been classed as invalid
				cells[0].childNodes[0].setAttribute('class', "");
				var valid = false;
				valid = check_for_empty_field(cells[0].childNodes[0], "Please enter a Quantity against row " + (i + 1));
				if(valid == true){
					valid = check_isNumeric(cells[0].childNodes[0], "Please enter an Integer Quantity greater than zero against row " + (i + 1));
				}
				cells[1].childNodes[0].setAttribute('class', "");
				valid = false;
				valid = check_for_empty_field(cells[1].childNodes[0], "Please enter an Adjustment against row " + (i + 1));
				if(valid == true){
					valid = check2dp(cells[1].childNodes[0], "Please enter an Integer Adjustment value to 2dp against row " + (i + 1));
				}
			}else{
				//the last row in the table should be empty
				//initialise class of input boxes before validation as may previously have been classed as invalid
				cells[0].childNodes[0].setAttribute('class', "");
				var valid = false;
				valid = check_for_completed_field(cells[0].childNodes[0]);
				if(valid == true){
					cells[1].childNodes[0].setAttribute('class', "");
					var valid = false;
					valid = check_for_empty_field(cells[1].childNodes[0], "Please complete the last line" + (i + 1));
				}else{
					cells[1].childNodes[0].setAttribute('class', "");
					var valid = false;
					valid = check_for_completed_field(cells[1].childNodes[0]);
					if(valid == true){
						cells[0].childNodes[0].setAttribute('class', "");
						var valid = false;
						valid = check_for_empty_field(cells[0].childNodes[0], "Please complete the last line" + (i + 1));
					}
				}
			}
		}
		if(errors_array.length > 0){
			error_message(errors_array, "red");
			return false;
		}
	}
	
	return true;

	function check_for_completed_field(input_box){
		if(input_box.value != ""){
			return true;	
		}
		return false;
	}
	
	function check_for_empty_field(input_box, msg){
		if(input_box.value == "" || input_box.value == 0){
			input_box.className += " invalid";
			input_box.focus();
			errors_array.push(msg + "<br/>");
			return false;	
		}
		
		return true;
	}
	
	function check2dp(input_box, msg) {
		//validates a number to 2dp
		if(!isNaN(parseFloat(input_box.value)) && isFinite(input_box.value)){
			var len = input_box.value.length;
			var posn = input_box.value.indexOf(".");
			if(posn > 0 && input_box.value.substr(len - 3, 1) == "."){
				return true;
			}
		}
		input_box.className += " invalid";
		input_box.focus();
		errors_array.push(msg + "<br/>");
		
		return false;
	}
	
	function check_isNumeric(input_box, msg){
		var numericExpression = /^[0-9]+$/;
		if(input_box.value.match(numericExpression) && input_box.value > 0) {
			return true;
		}
		input_box.className += " invalid";
		input_box.focus();
		errors_array.push(msg + "<br/>");
		
		return false;
	}
	
	function error_message(errors_array, warning){
		var message = ""; var no_changes = 0;
		for( var i = 0; i < errors_array.length; i++){
			message += errors_array[i];
			if(errors_array[i] == "No changes have been made"){no_changes = 1;}
		}
		//document.getElementById("MESSAGE").innerHTML = message;
		//document.getElementById("MESSAGE").className = warning;
		if (errors_array.length == 1 && no_changes ==1){
			alert("No changes have been made");
		}else{
			document.getElementById("MESSAGE").innerHTML = message;
			document.getElementById("MESSAGE").className = warning;
			//alert("Errors Found - Please correct errors to continue or Logout");
		}
		return;
	}
}

