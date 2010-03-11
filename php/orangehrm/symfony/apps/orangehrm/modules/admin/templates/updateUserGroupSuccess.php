<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
   <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Admin User Group")?></h2></div>
            	<form name="frmSave" id="frmSave" method="post"  action="">                
	                <label for="txtSkillName"><?php echo __("Code")?> </label>
	                     <span class="formValue"><?php echo $userGroup->getUsergId()?></span>
	             		 <br class="clear"/>
	                <label for="txtSkillName"><?php echo __("Name")?> <span class="required">*</span></label>
	                     <input id="txtUserGroupName"  name="txtUserGroupName" type="text"  class="formInputText" value="<?php echo $userGroup->getUsergName()?>" tabindex="5" />
	             		 <br class="clear"/>
	                <div class="formbuttons">
	                    <input type="button" class="savebutton" id="editBtn"
	                        value="<?php echo __("Edit")?>" tabindex="11" />
	                    <input type="button" class="clearbutton"  id="resetBtn"
	                         value="<?php echo __("Reset")?>" tabindex="12" />
	                    <a href="<?php echo url_for('admin/listUserGroupRight?id='.$userGroup->getUsergId())?>"><?php echo __("Assign User Rights")?></a>
	                </div>
            </form>
        </div>
         <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', __("Fields marked with an asterisk #star are required.") ); ?>.</div>
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
				 	txtUserGroupName: { required: true }
			 	 },
			 	 messages: {
			 		txtUserGroupName: "<?php echo __("Name is required")?>"
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
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/listUserGroup')) ?>";  
				});

			//When click reset buton 
				$("#resetBtn").click(function() {
					document.forms[0].reset('');
				 });
				
		 });
</script>
       