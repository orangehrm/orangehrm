<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
   <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Job : Pay Grade")?></h2></div>
            <?php echo message()?>
            	<form name="frmSave" id="frmSave" method="post"  action="" >
                
                <label ><?php echo __("Code")?></label>
                     <span class="formValue"><?php echo $saleryGrade->getSalGrdCode()?></span>
             		 <br class="clear"/>
             		 
                 <label for="txtLocationCode"><?php echo __("Name")?><span class="required">*</span></label>
                     <input id="txtName"  name="txtName" type="text"  class="formInputText" value="<?php echo $saleryGrade->getSalGrdName()?>" tabindex="5" />
             		 <br class="clear"/>        		 	  
                <div class="formbuttons">
                    <input type="button" class="savebutton" id="editBtn"
                       
                        value="<?php echo __("Edit")?>" tabindex="11" />
                    <input type="button" class="clearbutton" id="resetBtn"
                         value="<?php echo __("Reset")?>" tabindex="12" />
                </div>
            </form>
            
            <div class="subHeading"><h3><?php echo __("Assign new currency")?></h3></div>
            
            <br class="clear"/>
            <form name="frmAssignCurrency" id="frmAssignCurrency" method="post"  action="<?php echo url_for('admin/saveSaleryGradeCurrency')?>" >
            	<input type="hidden" name="id" value="<?php echo $saleryGrade->getSalGrdCode()?>"></input>
            		<label ><?php echo __("Currency")?><span class="required">*</span></label>
                     <select class="formSelect" id="cmbUnAssCurrency" name="cmbUnAssCurrency">
    						<option value=""><?php echo __("---Select Currency---")?></option>
    						<?php foreach($currencyList as $currency){?>
								<option value="<?php echo $currency->getCurrencyId()?>"><?php echo $currency->getCurrencyName()?></option>
							<?php }?>
					</select>
             		 <br class="clear"/>
             		 <label for="txtMinSal"><?php echo __("Minimum Salary")?></label>
					<input id="txtMinSal" class="formInputText" type="text" name="txtMinSal" id="txtMinSal"/>
					<br class="clear"/>
					<label for="txtMaxSal"><?php echo __("Maximum Salary")?></label>
					<input id="txtMaxSal" class="formInputText" type="text" name="txtMaxSal" id="txtMaxSal"/>
					<br class="clear"/>
					<label for="txtStepSal"><?php echo __("Step Increase")?></label>
					<input id="txtStepSal" class="formInputText" type="text" name="txtStepSal" id="txtStepSal"/>
					<br class="clear"/>
					
					<div class="formbuttons">
					<input id="addCurrencyBtn" name="addCurrencyBtn" class="savebutton" type="button" value="Save" title="Save" />
					</div>
            </form>
            <form name="frmRemoveCurrency" id="frmRemoveCurrency" method="post"  action="<?php echo url_for('admin/deleteSaleryGradeCurrency')?>" >
            <input type="hidden" name="id" value="<?php echo $saleryGrade->getSalGrdCode()?>"></input>
            <div class="subHeading">
            
				<h3><?php echo __("Assigned currencies")?></h3>
				</div>
				
				<div class="actionbar">
			        <div class="actionbuttons">
			            <input type="button" value="<?php echo __("Delete")?>"  id="delBtn" class="delbutton"/>
			                </div>
			        <div class="noresultsbar" ></div>
			        <div class="pagingbar"></div>
			    </div>
					<table border="0">
						<tbody>
						<tr>
				          	<td/>
							 <td><strong><?php echo __("Currency")?></strong></td>
							 <td><strong><?php echo __("Minimum Salary")?></strong></td>
							 <td><strong><?php echo __("Maximum Salary")?></strong></td>
							 <td><strong><?php echo __("Step Increase")?></strong></td>
						</tr>
						<?php foreach( $sallerGradeCurrencyList as $sallerGradeCurrency){?>
				   			<tr>
				   				<td><input type="checkbox" value="<?php echo $sallerGradeCurrency->getCurrencyId()?>" name="chkdel[]" /></td>
				   				<td><?php echo $sallerGradeCurrency->getCurrencyType()->getCurrencyName()?></td>
				   				<td><?php echo $sallerGradeCurrency->getMinSalary()?></td>
				   				<td><?php echo $sallerGradeCurrency->getMaxSalary()?></td>
				   				<td><?php echo $sallerGradeCurrency->getSalaryStep()?></td>
				   			</tr>	
				   		<?php }?>
				   		</tbody>
		   		</table>
        </div>
        </form>
         <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', __("Fields marked with an asterisk #star are required.") ); ?>.</div>
   </div>
   
   <script type="text/javascript">
	
		$(document).ready(function() {

		var mode	=	'edit';
			
			//Disable all fields
			$('#frmSave :input').attr('disabled', true);
			$('#editBtn').removeAttr('disabled');
			
			$("#editBtn").click(function() {
				
				if( mode == 'edit')
				{
					$('#editBtn').attr('value', 'Save'); 
					$('#frmSave :input').removeAttr('disabled');
					mode = 'save';
				}else
				{
					
					$('#frmSave').submit();
				}
			});

			//Validate the form
			 $("#frmSave").validate({
				
				 rules: {
				 	txtName: { required: true }
			 	 },
			 	 messages: {
			 		txtName: "<?php echo __("Name is required")?>"
			 	 }
			 });

			//Validate the form
			 $("#frmAssignCurrency").validate({
				
				 rules: {
				 	cmbUnAssCurrency: { required: true },
				 	txtMinSal:{
					 	required: true,
					 	number: true
					 },
				 	txtMaxSal: {
						 	required: true,
						 	number: true
					},
				 	txtStepSal: {
					 	required: true,
					 	number: true
					 }
			 	 },
			 	 messages: {
			 		cmbUnAssCurrency: "<?php echo __("Currency is required")?>",
			 		txtMinSal :{ 
				 		required:"<?php echo __("Minimum salary is required")?>",
				 		number:"<?php echo __("Minimum salary should be numeric")?>"
				 	},
			 		txtMaxSal : { 
				 		required:"<?php echo __("Maximum salary is required")?>",
				 		number:"<?php echo __("Minimum salary should be numeric")?>"
				 	},
			 		txtStepSal :{ 
				 		required: "<?php echo __("Step salary is required")?>"
				 		,number:"<?php echo __("Minimum salary should be numeric")?>"
				 	}
			 	 }
			 });
			 
			//When click save button 
			$("#addCurrencyBtn").click(function() {
				$('#frmAssignCurrency').submit();
			});


			//When click remove button
			$("#delBtn").click(function() {
				$('#frmRemoveCurrency').submit();
			});
			
			
			//When click reset buton 
				$("#resetBtn").click(function() {
					document.forms[0].reset('');
				 });
			 
			 
		 });
</script>
       