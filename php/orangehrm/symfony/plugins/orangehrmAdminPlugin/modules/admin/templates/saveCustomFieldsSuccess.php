   <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Custom Fields")?></h2></div>
            	<form name="frmSave" id="frmSave" method="post"  action="">
                
                 <label for="cmbCustomerId"><?php echo __("Field Number")?> </label>
                    <select tabindex="1" class="formSelect" id="txtId" name="txtId">
						<?php foreach($avaliableIds as $avaliableId){?>
							<option value="<?php echo $avaliableId?>"><?php echo $avaliableId?></option>  
						<?php } ?> 			
					</select>
             		 <br class="clear"/>
             	<label for="txtName"><?php echo __("Field Name")?> <span class="required">*</span></label>
                     <input id="txtName"  name="txtName" type="text"  class="formInputText" value="" tabindex="5" />
             		 <br class="clear"/>
             	<label for="cmbCustomerId"><?php echo __("Type")?><span class="required">*</span></label>
					<select tabindex="1" class="formSelect" id="cmbType" name="cmbType">
						<option value="0"><?php echo __("String")?></option>  
						<option value="1"><?php echo __("Drop Down")?></option>  
					</select>
					<br class="clear"/>
				<div class="hide" id="selectOptions">
	                <label for="txtExtra">Select Options <span class="required">*</span>
	                </label>
	                <input type="text" value="" class="formInputText" tabindex="4" name="txtExtra" id="txtExtra"/>
	                <div class="fieldHint">Enter allowed options separated by commas</div>
                </div>
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
				 	txtName: { required: true }
				 	
			 	 },
			 	 messages: {
			 		txtMembershipInfoDesc: '<?php echo __(ValidationMessages::REQUIRED); ?>'
			 		
			 	 }
			 });
			 
			//When Changing drop down
			$("#cmbType").change(function() {
				if( $("#cmbType").val() == 0)
				{
					$("#selectOptions").hide();
				}else
				{
					$("#selectOptions").show();
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
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/listCustomFields')) ?>";  
				});
				
		 });
</script>
       