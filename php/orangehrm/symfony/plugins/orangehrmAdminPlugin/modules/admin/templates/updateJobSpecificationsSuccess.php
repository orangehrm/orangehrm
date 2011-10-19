<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
   <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Job : Job Specifications")?></h2></div>
            	<form name="frmSave" id="frmSave" method="post"  action="">
                
                 <label for="txtLocationCode"><?php echo __("Name")?> <span class="required">*</span></label>
                     <input id="txtName"  name="txtName" type="text"  class="formInputText" value="<?php echo $jobSpecification->getJobspecName()?>" tabindex="5" />
             		 <br class="clear"/>
				<label for="txtDesc"><?php echo __("Description")?> </label>
					<textarea id="txtDesc" class="formTextArea" tabindex="2" name="txtDesc" type="text"/><?php echo $jobSpecification->getJobspecDesc()?></textarea>
				<br class="clear"/>
				<label for="txtDuties"><?php echo __("Duties")?></label>
					<textarea id="txtDuties" class="formTextArea" tabindex="3" name="txtDuties" type="text"/><?php echo $jobSpecification->getJobspecDuties()?></textarea>
				<br class="clear"/>
                          		 	  
                <div class="formbuttons">
                    <input type="button" class="savebutton" id="editBtn"
                       
                        value="<?php echo __("Edit")?>" tabindex="11" />
                    <input type="button" class="clearbutton"  id="resetBtn"
                         value="<?php echo __("Reset")?>" tabindex="12" />
                </div>
            </form>
        </div>
         
   </div>
   
   <script type="text/javascript">

		$(document).ready(function() {
			var mode	=	'edit';
			
			//Disable all fields
			$('#frmSave :input').attr('disabled', true);
			$('#editBtn').removeAttr('disabled');
			

			//Validate the form
			 $("#frmSave").validate({
				
				 rules: {
				 	txtName: { required: true }
			 	 },
			 	 messages: {
			 		txtName: "<?php echo __("Name is required")?>"
			 	 }
			 });

			// When click edit button
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


				//When click reset buton 
				$("#resetBtn").click(function() {
					document.forms[0].reset('');
				 });

			 //When Click back button 
			 $("#btnBack").click(function() {
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/listJobSpecifications')) ?>";  
				});
				
		 });
</script>
       