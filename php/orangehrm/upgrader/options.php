<script language="JavaScript">
function optionSubmit() {
	obj = document.getElementById('option1');
	
	if (!(obj && obj.checked)) {
		obj = document.getElementById('option2');
	}
	
	if (obj) {
		document.frmInstall.actionResponse.value  = obj.value;
		document.frmInstall.submit();
	} else {
		alert('Please select one of the options before proceeding');
	}
}
</script>
<div id="content">
	<h2>Step 3: Options </h2>
   
      
	<p>Select one of the options and click <b>[Next]</b> to continue.</p>
	<p>
		<label>
		  	<input name="option" id="option1" type="radio" value="LOCCONF" checked="checked" tabindex="1"/>
		 	Upgrade Exsisting Database
		 </label>
		 <br/>
		 <label>
		  	<input name="option" id="option2" type="radio" value="DBCONF" tabindex="2"/> 
		  	Create New Database
		  </label>
	</p>
	<input class="button" type="button" value="Back" onclick="back();" tabindex="4">
	<input type="button" name="next" value="Next" onclick="optionSubmit();" id="next" tabindex="3">
</div>