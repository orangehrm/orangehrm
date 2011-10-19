  <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
	  <style type="text/css">
	.style1 {color: #FF0000}
	</style>
<link href="../../themes/orange/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/orange/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/orange/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
    <div class="formpage2col">
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading" id="mainHeading"><h2><?php echo __("Company Info : General")?></h2></div>
            <form name="frmGenInfo" id="frmGenInfo" method="post" onsubmit="" action="">
                <input type="hidden" name="txtCode" value="<?php echo $company->getComCode()?>" />

                <label for="txtCompanyName"><?php echo __("Company Name")?><span class="required">*</span></label>
                <input id="txtCompanyName" name="txtCompanyName" type="text" 
                    class="formInputText required"
                    value="<?php echo $company->getComapanyName()?>" maxlength="250"/>

                <span class="formLabel"><?php echo __("Number of Employees")?></span>
                <span class="formValue"><?php echo $company->getEmpCount()?></span>
                <br class="clear"/>

                <label for="txtTaxID"><?php echo __("Tax ID")?></label>
                <input id='txtTaxID' name='txtTaxID' type="text"  class="formInputText"
                    value="<?php echo $company->getTaxId()?>" maxlength="25"/>

                <label for="txtNAICS"><?php echo __("NAICS")?></label>
                <input id='txtNAICS' name='txtNAICS' type="text"  class="formInputText"
                    value="<?php echo $company->getNaics()?>" maxlength="15"/>
                <br class="clear"/>
                <label for="txtPhone"><?php echo __("Phone")?></label>
                <input id='txtPhone' name='txtPhone' type="text"  class="formInputText"
                    value="<?php echo $company->getPhone()?>" maxlength="20"/>

                <label for="txtFax"><?php echo __("Fax")?></label>
                <input id="txtFax" name="txtFax" type="text"   class="formInputText"
                    value="<?php echo $company->getFax()?>" maxlength="20"/>
                <br class="clear"/>
                <label for="cmbCountry"><?php echo __("Country")?></label>
                <select id='cmbCountry' name='cmbCountry'  class="formSelect countrySelect">
                    <option value="0">--- <?php echo __("--Select--")?> ---</option>
					<?php foreach( $countryList as $country){?>
						<option value="<?php echo $country->cou_code?>" <?php if($company->getCountry() == $country->cou_code){?>selected<?php }?>><?php echo $country->name?></option>
					<?php }?>
                </select>
                <br class="clear"/>
                <label for="txtStreet1"><?php echo __("Address1")?></label>
                <input id='txtStreet1' name='txtStreet1' type="text"  class="formInputText"
                    value="<?php echo $company->getStreet1()?>" maxlength="40"/>

                <label for="txtStreet2"><?php echo __("Address2")?></label>
                <input id='txtStreet2' name='txtStreet2' type="text"  class="formInputText"
                    value="<?php echo $company->getStreet2()?>" maxlength="40"/>
                <br class="clear"/>
                <label for="cmbCity"><?php echo __("City")?></label>
                <input id="txtCity"  name="txtCity" type="text"  class="formInputText"  value="<?php echo $company->getCity()?>"
                    value="" maxlength="30"/>

                <label for="cmbState"><?php echo __("State / Province")?></label>
                <div id="lrState">
                    
                      <?php if($company->getCountry() == 'US'){?>
						 <select name="txtState" id="txtState"  class="formSelect">
						 	<option value="0">--- <?php echo __("--Select--")?> ---</option>
						 	<?php foreach( $provinceList as $province){?>
								<option value="<?php echo $province->province_code?>" <?php if($company->getState() == $province->province_code){?>selected<?php }?>><?php echo $province->province_name?></option>
							<?php }?>
						 </select>
						<?php } else{?>
								<input id="txtState" name="txtState" type="text" class="formInputText"  maxlength="30"  value="<?php echo $company->getState()?>"/>
								<select name="" id="txtState1"  class="formSelect" style="display:none">
							 	<option value="0">--- <?php echo __("--Select--")?> ---</option>
							 	<?php foreach( $provinceList as $province){?>
									<option value="<?php echo $province->province_code?>"><?php echo $province->province_name?></option>
								<?php }?>
							 </select>
						<?php }?>
                </div>
                <br class="clear"/>
                <label for="txtZIP"><?php echo __("ZIP Code")?></label>
                <input id='txtZIP' name='txtZIP' type="text"  class="formInputText"
                    value="<?php echo $company->getZipCode()?>" maxlength="20"/>
                <br class="clear"/>

                <label for="txtComments"><?php echo __("Comments")?></label>
                 <span id="commentLengthWarningLabel" style="display: none" class="style1"><?php echo __(" Length of comments exceeds the limit. Text at the end of the comment will be lost.")?></span>
                <textarea id='txtComments' name='txtComments'  class="formTextArea"
                    rows="3" cols="20"  onkeyup="showCommentLengthExceedWarning()"
                   
                    ><?php echo $company->getComments()?></textarea>
                <br class="clear"/>
                <div class="formbuttons" align="center">

                    <input type="button" class="editbutton" id="editBtn"
                         tabindex="2" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo __("Edit")?>" />
                    <input type="button" class="clearbutton"  tabindex="3" id="resetBtn"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                         value="<?php echo __("Reset")?>" />

                </div>
            </form>
        </div>

        <div class="requirednotice"></div>
    </div>
<script type="text/javascript">

$(document).ready(function() 
{
	var mode	=	'edit';
	
	//Diable all attributes when loading
	$('#frmGenInfo :input').attr('disabled', true);
	$('#editBtn').removeAttr('disabled');

	//Change State drop down when select US
	$("#cmbCountry").change(function() {
			if($("#cmbCountry").val() == 'US')
			{
				$("#txtState").hide();
				$("#txtState1").show();
				$('#txtState1').attr('name', 'txtState'); 
			}else
			{
				$("#txtState").show();
				$("#txtState1").hide();
			}

			
	});
	
	//When click Edit button
	$("#editBtn").click(function() {
		if( mode == 'edit')
		{
			$('#editBtn').attr('value', 'Save'); 
			$('#frmGenInfo :input').removeAttr('disabled');
			mode	=	'save';
		}else
		{
			$('#frmGenInfo').submit();
		}
		
   });

	//When click reset buton 
	$("#resetBtn").click(function() {
		document.forms[0].reset('');
	 });

	//Validate the form
	$.validator.addMethod("NumbersOnly", function(value, element) {
	        return this.optional(element) || /^[0-9\-\+]+$/i.test(value);
	 });
	
	 $("#frmGenInfo").validate({
		 rules: {
		 	txtCompanyName: { required: true },
		 	txtPhone: { NumbersOnly: true },
		 	txtFax : { NumbersOnly: true }
	 	 },
	 	 messages: {
	 		txtCompanyName: "<?php echo __("Company Name is required")?>",
	 		txtPhone: "<?php echo __("Invalid Phone number")?>",
	 		txtFax: "<?php echo __("Invalid Fax number")?>"
	 	 }
	 	 /*
	 	  errorPlacement: function(error, element) {
	 	    error.wrap("<li></li>").appendTo($("#mainHeading")); 
	 	    $('<div class="errorIcon"></div>').insertAfter(element);
	 	  },
	 	 success: function(label) {
	 		// set   as text for IE
	 		label.html(" Ok!").fadeIn("slow");
	 		}
	 		 	 
	 	 */
	 });

	
 });

function showCommentLengthExceedWarning() {
    totalFieldLength = 800;
    marginOffset = 35;
    usedLength = 0;

    with (document.forms['frmGenInfo']) {
        usedLength += txtCompanyName.value.length;
        usedLength += txtTaxID.value.length;
        usedLength += txtNAICS.value.length;
        usedLength += txtPhone.value.length;
        usedLength += txtFax.value.length;
        usedLength += cmbCountry.options[cmbCountry.selectedIndex].value.length;
        usedLength += txtStreet1.value.length;
        usedLength += txtStreet2.value.length;
        usedLength += cmbCity.value.length;
        usedLength += (txtState.type == 'text') ? txtState.value.length : txtState1.options[txtState1.selectedIndex].value.length;
        usedLength += txtZIP.value.length;

        availableLength = totalFieldLength - (usedLength + marginOffset);
        commentLengthWarning = document.getElementById('commentLengthWarningLabel');

        if (txtComments.value.length > availableLength) {
            commentLengthWarning.style.display = 'block';
        } else {
            commentLengthWarning.style.display = 'none';
        }
    }
}

</script>