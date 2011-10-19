<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
   <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Project")?></h2></div>
            	<form name="frmSave" id="frmSave" method="post"  action="">
	                
	                 <label for="txtSkillName"><?php echo __("Customer Name")?> <span class="required">*</span></label>
	                    <select tabindex="1" class="formSelect" id="cmbCustomerId" name="cmbCustomerId">
							<option value="">-- Select customer --</option>
							<?php foreach($listCustomer as $customer){?>
								<option value="<?php echo $customer->getCustomerId()?>"><?php echo $customer->getName()?></option>  
							<?php } ?> 			
						</select>
	             		 <br class="clear"/>
	             	<label for="txtSkillName"><?php echo __("Name")?> <span class="required">*</span></label>
	                     <input id="txtName"  name="txtName" type="text"  class="formInputText" value="" tabindex="5" />
	             		 <br class="clear"/>
	             	<label for="txtDescription"><?php echo __("Description")?></label>
						<textarea id="txtDescription" class="formTextArea" tabindex="2" cols="30" rows="3" name="txtDescription"/></textarea>
						<br class="clear"/>
	                <div class="formbuttons">
	                    <input type="button" class="savebutton" id="editBtn"
	                       
	                        value="<?php echo __("Save")?>" tabindex="11" />
	                    <input type="button" class="clearbutton" id="resetBtn"
	                         value="<?php echo __("Reset")?>" tabindex="12" />
	                </div>
            	</form>
           
         </div>
         
   </div>   
   <script type="text/javascript">

		$(document).ready(function() {

			

			//Validate the form
			 $("#frmSave").validate({
				
				 rules: {
				 	cmbCustomerId: { required: true },
				 	txtName: { required: true }
				 	
			 	 },
			 	 messages: {
			 		cmbCustomerId: "<?php echo __("Customer Name is required")?>",
				 	txtName: "<?php echo __("Name is required")?>"
			 		
			 	 }
			 });

			// When click edit button
				$("#editBtn").click(function() {
					$('#frmSave').submit();
				});

			 //When Click back button 
			 $("#btnBack").click(function() {
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/listProject')) ?>";  
				});

			//When click reset buton 
				$("#resetBtn").click(function() {
					document.forms[0].reset('');
				 });
				
		 });
</script>
       