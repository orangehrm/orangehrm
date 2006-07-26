<!--
	
	function addNewLocation () {
	
		document.getElementById("cmbProvince").value = document.getElementById("txtState").value;
		
		if ( validateLoc() ) {
			swStatus();
			xajax_addLocation(xajax.getFormValues('frmAddLoc'));	
		};	
		
	}	
	
	function validateLoc () {
		
		var frm = document.frmAddLoc;
			
		if (frm.txtLocDescription.value == '') {
			alert ("Location Name empty!");
			frm.txtLocDescription.focus();
			return;
		}
		
		if (frm.cmbCountry.value == '0') {		
			alert ("Country not selected!");
			frm.cmbCountry.focus();
			return;
		}
		
		if ( frm.cmbProvince.value == '0') {		
			alert ("State not selected!");
			frm.cmbProvince.focus();
			return;
		}

		if ( frm.cmbDistrict.value == '0') {
			alert ("City Cannot be empty!");
			frm.cmbDistrict.focus();
			return;
		}
		
		if ( frm.txtAddress.value == '') {		
			alert ("Address empty!");
			frm.txtAddress.focus();
			return;
		}
		
		if ( frm.txtZIP.value == '' ){		
			alert ("Zip - Code Cannot be empty!");
			frm.txtZIP.focus();
			return;
		}
		
		if ( (frm.txtZIP.value != '') && (!numbers(frm.txtZIP)) ){		
			if ( ! confirm ("Zip - Code Contains non-numeric characters! Here they are"+nonNumbers(frm.txtZIP)+". Do you want to continue?") ) {
				frm.txtZIP.focus();
			return;
			}		
		}		
		

		if (frm.txtPhone.value != '' && !numeric(frm.txtPhone)) {
			alert("Should be Numeric!");
			frm.txtPhone.focus();
			return;
		}
		
		 if(frm.txtFax.value != '' && !numeric(frm.txtFax)) {		

			alert("Should be Numeric!");
			frm.txtFax.focus();
			return;
		}
		return true;
	}
	
	function validate() {
		var flag = true;
		var errs = 'Following errors were found:\n\n';
				
		if (document.getElementById("txtTitle").value == '') {
			errs+="- Sub-division Name cannot be empty.\n";
			flag = false;
		};
		
		if (document.getElementById("cmbType").value == 'null') {
			errs+="- Please select a Type or define a custom type.\n";
			flag = false;
		};
		
		if (document.getElementById("cmbLocation").value == '') {
			errs+="- Please select a Location or define a new Location and select.\n";
			flag = false;
		};
		
		if(!flag) {
			alert(errs);
			errs="return false;";
		} else {
			document.getElementById("frmAddNode").submit();
			errs="return true;";
		}
		
	return errs;
	}
	
	function addChild(rgtz, txt, parnt) {		
		document.frmAddNode.reset();
		document.getElementById("cmbLocation").selectedIndex = 0;
		document.getElementById("cmbType").selectedIndex = 0;
		document.frmAddNode.rgt.value=rgtz;			
		document.frmAddNode.sqlState.value='NewRecord';			
		document.getElementById("parnt").innerHTML="<?=$frmSub_divisionHeadingAdd?>"+txt;
		document.getElementById("txtParnt").value=parnt;
		document.getElementById("layerForm").style.visibility="visible";
	}
	
	function edit(id, txt, desc, loc){
		var words = txt.split(" ");
		var found =false;
		
		document.frmAddNode.reset();	
					
		document.frmAddNode.sqlState.value='UpdateRecord';
		document.frmAddNode.rgt.value=id;
		document.getElementById("parnt").innerHTML="<?=$frmSub_divisionHeadingEdit?> "+txt;
		
		for(i=0; i < document.getElementById("cmbType").options.length; i++) {
			
			if (document.getElementById("cmbType").options[i].value == words[words.length-1]) {
				found = true;
				break;
			};
			
		}
		
		if (found) {			
			document.getElementById("cmbType").selectedIndex=i;
		} else {
			document.getElementById("cmbType").selectedIndex=document.getElementById("cmbType").options.length-1;		
		}
		
		for(i=0; i < document.getElementById("cmbLocation").options.length; i++) {
			
			if (document.getElementById("cmbLocation").options[i].value == loc) {
				break;
			};
			
		}
		
		document.getElementById("cmbLocation").selectedIndex = i;
			
		document.getElementById("cmbLocation").value=loc;
		words.splice(words.length-1,1);
		document.getElementById("txtTitle").value=words.join(" ");	
		document.getElementById("txtDesc").value=desc;
		document.getElementById("layerForm").style.visibility="visible";
	}
	
	function frmAddHide () {
		document.getElementById("txtType").style.visibility="hidden";
		document.getElementById("layerForm").style.visibility="hidden";
		document.getElementById("layerFormLoc").style.visibility = "hidden";
		document.getElementById("tblCompStruct").focus();
	}
	
	function frmEditHide () {
		document.getElementById("txtType").style.visibility="hidden";
		document.getElementById("layerEditForm").style.visibility="hidden";
		document.getElementById("layerFormLoc").style.visibility = "hidden";
	}
	
	function deleteChild(lftz, rgtz, txt) {
		
		var message='Are you sure you want to delete '+txt;
		var dependants = (((rgtz - lftz + 1)/2)-1);
		
		if (dependants > 0) {
			
			message = message+". Also "+dependants+" unit(s) under "+txt+" will be deteted";
		
		};
		message = message+'. It could cause the company structure to change.';
		
		if (confirm(message)) {
			
			document.frmDeleteNode.rgt.value=rgtz;		
			document.frmDeleteNode.lft.value=lftz;		
			document.frmDeleteNode.sqlState.value='delete';
			document.getElementById("frmDeleteNode").submit();
		
		};
	}
	
	function cmbType_Change() {
		
		if (document.getElementById("cmbType").value == '') {			
			document.getElementById("txtType").style.visibility = "visible";			
		} else {			
			document.getElementById("txtType").style.visibility = "hidden";
			document.getElementById("txtType").value=document.getElementById("cmbType").value;
			
		}
	}
	
	function locChange ($who) {
		if ($who.value == 'Other') {
			document.getElementById('status').innerHTML = '';
			document.getElementById("layerFormLoc").reset;
			document.getElementById("layerFormLoc").style.visibility = "visible";
		} else { 
			document.getElementById("layerFormLoc").style.visibility = "hidden";
		}
	}
	
	function swStatus() {
	
		document.getElementById('status').innerHTML = "<image src='/themes/beyondT/icons/loading.gif' width='20' height='20' style='vertical-align: bottom;'>Please Wait...."; 
		
	}
	
	function resetx() {
		document.getElementById('lrState').innerHTML = '<input type="text" name="txtState" id="txtState" value="">';
	}
-->
