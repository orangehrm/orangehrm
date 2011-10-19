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
                <label for="txtLocationCode"><?php echo __("Job Title ID")?> </label>
                     <span class="formValue"><?php echo $jobTitle->getId()?></span>
             		 <br class="clear"/>
                 <label for="txtLocationCode"><?php echo __("Job Title Name")?> <span class="required">*</span></label>
                     <input id="txtName"  name="txtName" type="text"  class="formInputText" value="<?php echo $jobTitle->getName()?>" tabindex="5" />
             		 <br class="clear"/>
				<label for="txtDesc"><?php echo __("Job Description")?> </label>
					<textarea id="txtJobTitleDesc" class="formTextArea" tabindex="2" name="txtJobTitleDesc" type="text"/><?php echo $jobTitle->getDescription()?></textarea>
				<br class="clear"/>
				<label for="txtDuties"><?php echo __("Job Title Comments")?></label>
					<textarea id="txtJobTitleComments" class="formTextArea" tabindex="3" name="txtJobTitleComments" type="text"/><?php echo $jobTitle->getComments()?></textarea>
				<br class="clear"/>
				<label for="txtSpec"><?php echo __("Job Title Comments")?></label>
					<select name="txtSpec" class="formSelect" style="width: 150px;">
							<option value="-1">--Select--</option>
						<?php foreach($listJobSpecifications as $jobSpecifications){?>
							<option value="<?=$jobSpecifications->getJobspecId()?>"  <?php if($jobTitle->getJobspecId() == $jobSpecifications->getJobspecId()){ echo "selected";}?>><?=$jobSpecifications->getJobspecName()?></option>
						<?php }?>
					</select>
				<br class="clear"/>
                <label for="txtPayGrade"><?php echo __("Pay Grade")?></label>
					<select name="txtPayGrade" class="formSelect" style="width: 150px;">
							<option value="-1">--Select--</option>
						<?php foreach($saleryGradeList as $saleryGrade){?>
							<option value="<?=$saleryGrade->getSalGrdCode()?>" <?php if($saleryGrade->getSalGrdCode() == $jobTitle->getSalaryGradeId()){ echo "selected";}?> ><?=$saleryGrade->getSalGrdName()?></option>
						<?php }?>
					</select>
					<div style="padding: 10px 0pt 2px 10px;">
					&nbsp;
						<input class="longbtn" type="button" id="addPayGrade" value="<?php echo __("Add Pay Grade")?>" />
						<input class="longbtn" type="button" id="editPayGrade" value="<?php echo __("Edit Pay Grade")?>" />
					</div>
				<br class="clear"/>
				<label for="cmbAssEmploymentStatus">
					<?php echo __("Employment Status")?>
				<span class="success">#</span>
				</label> 
				<select id="assignEmploymentStatus" class="formSelect" style="width: 150px; height: 50px;" name="assignEmploymentStatus" size="3">
					<?php foreach($jobTitle->getJobTitleEmployeeStatus() as $empStatus){?>
						<option value="<?php echo $empStatus->getEmployeeStatus()->getId()?>"><?php echo $empStatus->getEmployeeStatus()->getEstatName()?></option>
					<?php }?>
				</select>
       		 	<div style="margin: 10px 10px 0pt; float: left;">
				<input class="plainbtn" type="button"  style="width: 100px;" value="< Add" id="addEmployeeStatus" />
				<br/>
				<input class="plainbtn" type="button"  style="width: 100px; margin-top: 10px;" id="removeEmployeeStatus" value="Remove >" />
				</div>
				<select  class="formSelect" style="width: 150px; height: 50px;" name="employmentStatus" id="employmentStatus" size="3">
					<?php foreach($listEmploymentStatus as $empStatus){?>
						<option value="<?php echo $empStatus->getId()?>"><?php echo $empStatus->getEstatName()?></option>
					<?php }?>
					
					</select>
					<br class="clear"/>
					
					<div style="padding-top: 20px; padding-left: 10px;" class="controlContainer">
			            <input type="button"  class="extralongbtn"  value="<?php echo __("Add Employment Status")?>" id="addNewEmployeeStatus" />
						<br/><br/>
			            <input type="button"  class="extralongbtn"  value="<?php echo __("Edit Employment Status")?>" id="updateExistEmployeeStatus" />
					</div>
		
					<div id="layerEmpStat" style="display: none;">
								<input value="" name="txtEmpStatID" type="hidden">
								<label for="txtEmpStatDesc">Employment Status</label>
								<input name="txtEmpStatDesc" id="txtEmpStatDesc" class="formInputText" style="width: 200px;" type="text">
					            <input class="savebutton" id="btnEmpStat" onmouseover="moverButton(this);" onmouseout="moutButton(this);" style="margin: 10px 0pt 0pt 5px;" value="Save" onclick="addFormData();" type="button">
					</div>		

                <div class="formbuttons">
                    <input type="button" class="savebutton" id="editBtn"
                       
                        value="<?php echo __("Edit")?>" tabindex="11" />
                    <input type="button" class="clearbutton"  id="resetBtn"
                         value="<?php echo __("Reset")?>" tabindex="12" />
                </div>
                <input type="hidden" name="selEmpStatus" id="selEmpStatus" value=""></input>
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
			 		txtName: "<?php echo __("Job Title Name is required")?>"
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
						var selectStr	=	'';
						 $("#assignEmploymentStatus option").each(function(){
							 selectStr += $(this).val()+',';
					        });
					    $('#selEmpStatus').val(selectStr);
						$('#frmSave').submit();
					}
				});

			 	//When Click back button 
			 	$("#btnBack").click(function() {
				 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/listJobTitle')) ?>";  
				});

				//When click reset buton 
				$("#resetBtn").click(function() {
					document.forms[0].reset('');
				 });

				//When click Add Pay Grade
				 $("#addPayGrade").click(function() {
					 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveSaleryGrade')) ?>";  
					}); 

				//When click Edit Pay Grade
				 $("#editPayGrade").click(function() {
					 location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/admin/saveSaleryGrade')) ?>";  
					}); 

				//When click Add Employee Status
				 $("#addEmployeeStatus").click(function() {
					
					 var value 	= 	$('#employmentStatus').val();
					 var 	text=	$('#employmentStatus :selected').text() ;
					 $('#assignEmploymentStatus').append($("<option></option>").attr("value",value).text(text)); 
					 $('#employmentStatus :selected').remove() ;

				}); 

				//When click Remove Employee Status
				 $("#removeEmployeeStatus").click(function() {
					
					 var value 	= 	$('#assignEmploymentStatus').val();
					 var 	text=	$('#assignEmploymentStatus :selected').text() ;
					 $('#employmentStatus').append($("<option></option>").attr("value",value).text(text)); 
					 $('#assignEmploymentStatus :selected').remove() ;

				}); 

				//When click Add Employee Status
					$("#addNewEmployeeStatus").click(function() {
						$("#layerEmpStat").show();
					}); 

				//When click Add Employee Status
					$("#updateExistEmployeeStatus").click(function() {
						var 	text=	$('#employmentStatus :selected').text();
						$("#txtEmpStatDesc").val(text);
						$("#layerEmpStat").show();
					}); 
				
		 });
</script>
       