function $($id) {
	return document.getElementById($id);
}

function ESBOption(value, label, description) {
	this.value = value;
	this.label = label;
	this.description = description;
	
	this.equals = function(obj) {
		if(this.value == obj.value && this.label == obj.label && this.description == obj.description) {
			return true;
		} else {
			return false;
		}
	}
}

var list = new Array();
var pointer = null;
var length = list.length;

var url = null;
var table = null;
var valueField = null;
var labelField = null;
var descField = null;
var joinTable = null;
var joinConditions = null;
var focusNext = null;

var txtEnhancedSearchBox = null;

function focusNextControl() {
	if (focusNext != null) {
		focusNext.focus();
	}
}

function refreshList(obj, evt) {

	if(obj.value.trim() == "") {
		_hideAll();
		_deSelect();
		return;
	}

	txtEnhancedSearchBox = obj;
	key = evt.keyCode;

	switch(key) {
		case 13: // Enter
			_hideAll();
			_select();
			break;

		case 38: // Up Arrow
			_move(-1);
			break;

		case 40: // Down Arrow
			_move(1);
			break;

		case 27: // ESC
			_hideAll();
			_deSelect();
			break;
	
		default:
			if ((key >= 65 && key <= 90) || (key >= 96 && key <= 105) || (key >= 48 && key <= 57) || (key == 32 || key == 8)) {
				_populateList(obj.value.trim());
				_unmarkAll();
				_hideAll();
				_match(obj.value.trim());
			} else {
			}
			break;
	}

}

function _select() {
	txtEnhancedSearchBox.value = list[pointer].label;
	$('hidEnhancedSearchBox').value = list[pointer].value;
	pointer = null;
}

function _deSelect() {
	txtEnhancedSearchBox.value = '';
	$('hidEnhancedSearchBox').value = '-1';
	pointer = null;
}


function _move(inc) {
	if (isNaN(pointer) || pointer == null) {
		pointer = 0;
	} else {
		if ((inc == 1 && pointer >= (length - 1)) || (inc == -1 && pointer == 0))
			return;
		pointer += inc;
	}

	while ($('item' + pointer).style.display != 'block') {
		pointer += inc;

		if(pointer < 0) {
			pointer = 0;
			break;
		} else if (pointer >= length) {
			pointer = length - 1;
			break;
		}
	}

	_unmarkAll();
	_mark($('item' + pointer));
}

function _unmarkAll() {
	for(i = 0; i < length; i++) {
		$('item' + i).style.backgroundColor = "#FFFFFF";
	}
}

function _mark(obj) {
	obj.style.backgroundColor = "#FFCC00";
}

function _hideAll() {
		for(i = 0; i < length; i++) {
			$("item" + i). style.display = "none";
		}
}

function _match(str) {

	for(i = 0; i < length; i++) {
		if(list[i].label.toLowerCase().indexOf(str.toLowerCase()) == 0) {
			$("item" + i). style.display = "block";
			$("item" + i).innerHTML = list[i].label.replace(str, str.bold()) + '<br />' + list[i].description.fontcolor('#999999').fontsize(-1) 
		}
	}

}

function _inList(obj) {
	for (i in list) {
		if (list[i].equals(obj)) {
			return true;
		}
	}
	
	return false;
}

function _handleResponse(response)  { 
	rows = response.split("\n");

	for(i in rows) { 
		params = rows[i].split(',');
		obj = new ESBOption(params[0], params[1], params[2]);
		
		list[i] = obj;
		$('dropdownPane').innerHTML += '<span class="items" id="item' + i + '" onmouseover="pointer = ' + i + '; _mark(this)" onmouseout="_unmarkAll()" onclick="_hideAll(); _select();"></span>\n';
	}
	
	length = list.length;
}

function _handleWait() {

}

function _populateList(filterKey) {
    	xmlHTTPObject = null;

		try {
  			xmlHTTPObject = new XMLHttpRequest();
		} catch (e) {
			try {
			    xmlHTTPObject = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				xmlHTTPObject = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}

		if (xmlHTTPObject == null)
			alert("Your browser does not support AJAX!");

        xmlHTTPObject.onreadystatechange = function() {
			if (xmlHTTPObject.readyState == 4) {  
				response = xmlHTTPObject.responseText;
				_handleResponse(response.trim());
			} else {
				_handleWait();
			}
		}

		queryString = "&table=" + escape(table) +
							"&valueField=" + escape(valueField) +
							"&labelFields=" + escape(labelField) +
							"&descFields=" + escape(descField) +
							"&filterKey=" + escape(filterKey) +
							"&joinTable=" + escape(joinTable) +
							"&joinCondition=" + escape(joinConditions);

        xmlHTTPObject.open('GET', url + queryString, true);
        xmlHTTPObject.send(null);
}
