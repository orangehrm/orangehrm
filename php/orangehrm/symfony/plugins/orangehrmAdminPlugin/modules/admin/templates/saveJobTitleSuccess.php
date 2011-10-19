
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
   <div class="formpage2col">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Job : Job Title")?></h2></div>
            	<form name="frmSave" id="frmSave" method="post"  action="">
                
                 <label class="controlLabel" for="txtLocationCode"><?php echo __("Job Title Name")?> <span class="required">*</span></label>
                     <input id="txtName"  name="txtName" type="text"  class="formInputText" value="" tabindex="5" />
             		 <br class="clear"/>
				<label class="controlLabel" for="txtDesc"><?php echo __("Job Description")?> <span class="required">*</span></label>
					<textarea id="txtJobTitleDesc" class="formTextArea" tabindex="2" name="txtJobTitleDesc" type="text"/></textarea>
				<br class="clear"/>
				<label class="controlLabel" for="txtDuties"><?php echo __("Job Title Comments")?></label>
					<textarea id="txtJobTitleComments" class="formTextArea" tabindex="3" name="txtJobTitleComments" type="text"/></textarea>
				<br class="clear"/>
				<label for="txtSpec"><?php echo __("Job Specification")?></label>
					<select name="txtSpec" class="formSelect" style="width: 150px;">
							<option value="-1">--Select--</option>
						<?php foreach($listJobSpecifications as $jobSpecifications){?>
							<option value="<?php echo $jobSpecifications->getJobspecId()?>"><?php echo $jobSpecifications->getJobspecName()?></option>
						<?php }?>
					</select>
				<br class="clear"/>
                <label for="txtPayGrade"><?php echo __("Pay Grade")?></label>
					<select name="txtPayGrade" class="formSelect" style="width: 150px;">
							<option value="-1">--Select--</option>
						<?php foreach($saleryGradeList as $saleryGrade){?>
							<option value="<?php echo $saleryGrade->getSalGrdCode()?>"><?php echo $saleryGrade->getSalGrdName()?></option>
						<?php }?>
					</select>
					<div style="padding: 10px 0pt 2px 10px;">
					&nbsp;
						<input class="longbtn" type="button" id="addPayGrade" value="<?php echo __("Add Pay Grade")?>" />
						<input class="longbtn" type="button" id="editPayGrade" value="<?php echo __("Edit Pay Grade")?>" />
					</div>
					

				<br class="clear"/>        		 	  
                <div class="formbuttons">
                    <input type="button" class="savebutton" id="editBtn"
                       
                        value="<?php echo __("Save")?>" tabindex="11" />
                    <input type="button" class="clearbutton"  id="resetBtn"
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
				 	txtName: { required: true },
				 	txtJobTitleDesc: { required: true }
			 	 },
			 	 messages: {
			 		txtName: "<?php echo __("Job Title is required")?>",
			 		txtJobTitleDesc: "<?php echo __("Job Description is required")?>"
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
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/listJobTitle')) ?>";  
				});

			//When click Add Pay Grade
			 $("#addPayGrade").click(function() {
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveSaleryGrade')) ?>";  
				}); 

			//When click Edit Pay Grade
			 $("#editPayGrade").click(function() {
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveSaleryGrade')) ?>";  
				}); 
		 });
</script>
       