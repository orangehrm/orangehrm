<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css')?>" rel="stylesheet" type="text/css"/>
   <div class="formpage2col" style="width: 650px;">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
	            <div class="mainHeading">
					<h2>
						<?php if ($userType=='Yes'){?>
							<?php echo __("Users : HR Admin Userss")?>
						<?php }else{?>
							<?php echo __("Users : ESS Users")?>
						<?php }?>
					</h2>
				</div>
				
				<form name="frmSave" id="frmSave" method="post"  action="">
					<input type="hidden" name="isAdmin" value="<?php echo $userType?>">
	                <label for="txtUserName"><?php echo __("User Name")?><span class="required">*</span></label>
	                	<input type="text" value="" class="formInputText" tabindex="1" name="txtUserName" id="txtUserName"/>
	                <br class="clear"/>
	
	                <label for="txtUserPassword"><?php echo __("Password")?><span class="required">*</span></label>
	                    <input type="password" tabindex="2" class="formInputText" name="txtUserPassword" id="txtUserPassword"/>
					<br class="clear"/>
	                 <label for="txtUserConfirmPassword"><?php echo __("Confirm Password")?><span class="required">*</span></label>
	                    <input type="password" tabindex="3" class="formInputText" name="txtUserConfirmPassword" id="txtUserConfirmPassword"/>
	                 <br class="clear"/>
	               
	                <label for="cmbUserStatus"><?php echo __("Status")?></label>
		                <select tabindex="4" class="formSelect" name="cmbUserStatus" id="cmbUserStatus">
		                    <option value="Enabled"><?php echo __("Enabled")?></option>
		                    <option value="Disabled"><?php echo __("Disabled")?></option>
		                </select>
						<input type="hidden" value="" id="cmbUserEmpID" name="cmbUserEmpID"/>
					<div>
						<label for="txtUserEmpID"><?php echo __("Employee")?><span class="required"/></label>
						<input type="text" value="" class="formInputText" tabindex="4" name="txtEmployee" id="txtEmployee" />
						<input type="hidden" name="txtEmpId" id="txtEmpId" value="">
	    			</div>
	
	
	                <br class="clear"/>
	
	                    
	                    <label for="cmbUserStatus"><?php echo __("Admin User Group")?><span class="required">*</span></label>
	                    <select tabindex="6" class="formSelect" name="cmbUserGroupID" id="cmbUserGroupID">
	                        <option value=""><?php echo __("--Select User Group--")?></option>
	                        <?php foreach( $listUserGroup as $userGroup){?>
	                        	<option value="<?php echo $userGroup->getUsergId()?>"><?php echo $userGroup->getUsergName()?></option>        
	                        <?php } ?>
	                       </select>
	                    <br class="clear"/>
	                
	                 <div class="formbuttons">
	                    <input type="button" class="savebutton" id="editBtn"
	                       
	                        value="<?php echo __("Save")?>" tabindex="11" />
	                    <input type="button" class="clearbutton"  id="resetBtn"
	                         value="<?php echo __("Reset")?>" tabindex="12" />
	                </div>
	            </form>
				
            	
        </div>
         <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', __("Fields marked with an asterisk #star are required.") ); ?>.</div>
   </div>
   
   <script type="text/javascript">

		$(document).ready(function() {

			var data	= <?php echo str_replace('&#039;',"'",$empJson)?> ;

			//Validate the form
			 $("#frmSave").validate({
				
				 rules: {
					 txtUserName: { required: true, minlength: 5},
				 	txtUserPassword: { required: true, minlength: 4 },
			 		txtUserConfirmPassword: { required: true },
			 		cmbUserGroupID: { required: true }
			 	 },
			 	 messages: {
			 		txtUserName:
                    {
			 			required: "<?php echo __("User Name is required")?>",
                        minlength: "<?php echo __("User Name should be at least five characters long")?>"
                    },
                    txtUserName:
                    {
			 			required: "<?php echo __("Password is required")?>",
                        minlength: "<?php echo __("Password should be at least four characters long")?>"
                    },
			 		txtUserPassword: "<?php echo __("Password is required")?>",
			 		txtUserConfirmPassword: "<?php echo __("Confirm Password is required")?>",
			 		cmbUserGroupID: "<?php echo __("Admin User Group is required")?>"
			 	 }
			 });

			// When click edit button
				$("#editBtn").click(function() {
					$('#frmSave').submit();
				});

			//When click reset buton 
				$("#resetBtn").click(function() {
					document.forms[0].reset('');
				 });
				 
			 //When Click back button 
			 $("#btnBack").click(function() {
				 location.href = "<?php echo url_for("admin/listUser?isAdmin=".$userType)?>";  
				});

			//Auto complete
				$("#txtEmployee").autocomplete(data, {
				  formatItem: function(item) {
				    return item.name;
				  }
				}).result(function(event, item) {
				  	$('#txtEmpId').val(item.id);
				});
		 });
</script>
       