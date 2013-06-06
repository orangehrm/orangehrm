   <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Customer")?></h2></div>
            	<form name="frmSave" id="frmSave" method="post"  action="">
                <label for="txtSkillName"><?php echo __("Code")?> </label>
                     <span class="formValue"><?php echo $customer->getCustomerId()?></span>
                      <input id="id" type="hidden" value="<?php echo $customer->getCustomerId()?>" name="id"/>
             		 <br class="clear"/>
                 <label for="txtSkillName"><?php echo __("Name")?> <span class="required">*</span></label>
                     <input id="txtName"  name="txtName" type="text"  class="formInputText" value="<?php echo $customer->getName()?>" tabindex="5" />
             		 <br class="clear"/>
             	<label for="txtDescription"><?php echo __("Description")?></label>
					<textarea id="txtDescription" class="formTextArea" tabindex="2" cols="30" rows="3" name="txtDescription"/><?php echo $customer->getDescription()?></textarea>
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
			 		txtName: '<?php echo __(ValidationMessages::REQUIRED); ?>'
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

			 //When Click back button 
			 $("#btnBack").click(function() {
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/listCustomer')) ?>";  
				});

			//When click reset buton 
				$("#resetBtn").click(function() {
					document.forms[0].reset('');
				 });
		 });
</script>
       