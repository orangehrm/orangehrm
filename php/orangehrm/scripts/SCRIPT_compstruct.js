

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
			alert ("<?php echo $lang_Error_LocationNameEmpty; ?>!");
			frm.txtLocDescription.focus();
			return;
		}

		if (frm.cmbCountry.value == '0') {
			alert ("<?php echo $lang_Error_CountryNotSelected; ?>!");
			frm.cmbCountry.focus();
			return;
		}

		if ( frm.cmbProvince.value == '0') {
			alert ("<?php echo $lang_Error_StateNotSelected; ?>!");
			frm.cmbProvince.focus();
			return;
		}

		if ( frm.cmbDistrict.value == '0') {
			alert ("<?php echo $lang_Error_CityCannotBeEmpty; ?>!");
			frm.cmbDistrict.focus();
			return;
		}

		if ( frm.txtAddress.value == '') {
			alert ("<?php echo $lang_Error_AddressEmpty; ?>!");
			frm.txtAddress.focus();
			return;
		}

		if ( frm.txtZIP.value == '' ){
			alert ("<?php echo $lang_Error_ZipEmpty; ?>!");
			frm.txtZIP.focus();
			return;
		}

		if ( (frm.txtZIP.value != '') && (!numbers(frm.txtZIP)) ){
			if ( ! confirm ("<?php echo $lang_Error_CompStruct_ZipInvalid; ?>".replace(/#characterList/, nonNumbers(frm.txtZIP))+". <?php echo $lang_Error_DoYouWantToContinue; ?>") ) {
				frm.txtZIP.focus();
			return;
			}
		}


		if (frm.txtPhone.value != '' && !numeric(frm.txtPhone)) {
			alert("<?php echo $lang_Error_ShouldBeNumeric; ?>!");
			frm.txtPhone.focus();
			return;
		}

		 if(frm.txtFax.value != '' && !numeric(frm.txtFax)) {

			alert("<?php echo $lang_Error_ShouldBeNumeric; ?>!");
			frm.txtFax.focus();
			return;
		}
		return true;
	}

	function validate() {
		var flag = true;
		var errs = '<?php echo $lang_Error_FollowingErrorsWereFound; ?>:\n\n';

		if (document.getElementById("txtTitle").value == '') {
			errs+="- <?php echo $lang_Error_SubDivisionNameCannotBeEmpty; ?>.\n";
			flag = false;
		};

		if (document.getElementById("cmbType").value == 'null') {
			errs+="- <?php echo $lang_Error_PleaseSelectATypeOrDefineACustomType; ?>.\n";
			flag = false;
		};

		if (document.getElementById("cmbLocation").value == 'Other') {
			errs+="- <?php echo $lang_Error_CompStruct_LocEmpty; ?>.\n";
			flag = false;
		};

		if(document.getElementById("txtDeptId").value!='')
		{
			exist=0;
			for(i=0;i<allChildDepIds.length;i++)
			{
				/* Find if department id exist, excluding department id of element being edited */
				if ((allChildDepIds[i]==document.getElementById("txtDeptId").value) &&
						(allChildIds[i] != document.getElementById("add_rgt").value)) {
					exist=1;
				}
			}

			if (exist==1) {
				errs+="- <?php echo $lang_Error_CompStruct_Dept_Id_Invalid; ?>.\n";
				flag = false;
			}
		}

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
		currentEditNodeValues = ['', '', '', '', ''];
		document.frmAddNode.reset();
		document.getElementById("cmbLocation").selectedIndex = 0;
		document.getElementById("cmbType").selectedIndex = 0;
		document.frmAddNode.rgt.value=rgtz;
		document.frmAddNode.sqlState.value='NewRecord';
		document.getElementById("parnt").innerHTML="<?php echo $lang_compstruct_frmSub_divisionHeadingAdd; ?> "+txt;
		document.getElementById("txtParnt").value=parnt;
		document.getElementById("layerForm").style.visibility="visible";
	}

	function edit(deptid, id, txt, desc, loc){
		currentEditNodeValues = [deptid, id, txt, desc, loc];
	<?php if (!(isset($_GET['esp']) && ($_GET['esp'] == 1))) { ?>
		var words = txt.split(" ");
		var found =false;

		document.frmAddNode.reset();

		document.frmAddNode.sqlState.value='UpdateRecord';
		document.frmAddNode.rgt.value=id;
		document.getElementById("parnt").innerHTML="<?php echo $lang_compstruct_frmSub_divisionHeadingEdit; ?> "+txt;

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
		document.getElementById("txtDeptId").value=deptid
		document.getElementById("layerForm").style.visibility="visible";
	<?php } else { ?>

		<?php 
		    if (isset($_GET['locForm']) && isset($_GET['locInput'])) { ?>
                opener.document.<?php echo $_GET['locForm'];?>.txt<?php echo $_GET['locInput'];?>.value=txt;
                opener.document.<?php echo $_GET['locForm'];?>.cmb<?php echo $_GET['locInput'];?>.value=id;	    
        <?php
		    } else if (isset($_GET['locInput'])) { ?>
			opener.document.frmEmp.txt<?php echo $_GET['locInput'];?>.value=txt;
			opener.document.frmEmp.cmb<?php echo $_GET['locInput'];?>.value=id;
		<?php } else { ?>
			opener.document.frmEmp.txtLocation.value=txt;
			opener.document.frmEmp.cmbLocation.value=id;
		<?php } ?>
		window.close(0);
	<?php } ?>
	}

	function frmAddHide () {
		//document.getElementById("txtType").style.visibility="hidden";
		document.getElementById("layerForm").style.visibility="hidden";
		document.getElementById("layerFormLoc").style.visibility = "hidden";
		document.getElementById("tblCompStruct").focus();
	}

	function frmEditHide () {
		//document.getElementById("txtType").style.visibility="hidden";
		document.getElementById("layerEditForm").style.visibility="hidden";
		document.getElementById("layerFormLoc").style.visibility = "hidden";
	}

	function deleteChild(lftz, rgtz, txt) {

		var message='<?php echo $lang_Error_AreYouSureYouWantToDelete; ?> '+txt;
		var dependants = (((rgtz - lftz + 1)/2)-1);

		if (dependants > 0) {
			msgTxt = "<?php echo $lang_Error_CompStruct_UnitCount; ?>".replace(/#children/, dependants);
			msgTxt = msgTxt.replace(/#parent/, txt);

			message = message+"? "+msgTxt;

		};
		message = message+'. <?php echo $lang_Error_ItCouldCauseTheCompanyStructureToChange; ?>.';

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
			document.getElementById("frmAddLoc").reset();
			document.getElementById("layerFormLoc").style.visibility = "visible";
		} else {
			document.getElementById("layerFormLoc").style.visibility = "hidden";
		}
	}

	function swStatus() {

		document.getElementById('status').innerHTML = "<image src='../../themes/beyondT/icons/loading.gif' width='20' height='20' style='vertical-align: bottom;'><?php echo $lang_Commn_PleaseWait;?>....";

	}

	function resetx() {
		document.getElementById('lrState').innerHTML = '<input type="text" name="txtState" id="txtState" value="">';
	}
