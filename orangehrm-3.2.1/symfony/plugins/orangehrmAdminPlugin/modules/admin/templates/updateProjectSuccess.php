   <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" id="btnBack"
              value="<?php echo __("Back")?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Project")?></h2></div>
            	<?php echo message()?>
            	<form name="frmSave" id="frmSave" method="post"  action="">
                
                 <label for="txtSkillName"><?php echo __("Customer Name")?> <span class="required">*</span></label>
                    <select tabindex="1" class="formSelect" id="cmbCustomerId" name="cmbCustomerId">
						<option value="">-- Select customer --</option>
						<?php foreach($listCustomer as $customer){?>
							<option value="<?php echo $customer->getCustomerId()?>" <?php if($project->getCustomerId()==$customer->getCustomerId()){ echo "selected";}?>><?php echo $customer->getName()?></option>  
						<?php } ?> 			
					</select>
             		 <br class="clear"/>
             	<label for="txtSkillName"><?php echo __("Name")?> <span class="required">*</span></label>
                     <input id="txtName"  name="txtName" type="text"  class="formInputText" value="<?php echo $project->getName()?>" tabindex="5" />
             		 <br class="clear"/>
             	<label for="txtDescription"><?php echo __("Description")?></label>
					<textarea id="txtDescription" class="formTextArea" tabindex="2" cols="30" rows="3" name="txtDescription"/><?php echo $project->getDescription()?></textarea>
					<br class="clear"/>
                <div class="formbuttons">
                    <input type="button" class="savebutton" id="editBtn"
                       
                        value="<?php echo __("Save")?>" tabindex="11" />
                    <input type="button" class="clearbutton" id="resetBtn"
                         value="<?php echo __("Reset")?>" tabindex="12" />
                </div>
            </form>
            <div class="subHeading">
		        <h3><?php echo __("Project Administrators")?></h3>
		     </div>
		    <?php if( count($projectAdmins) > 0){?>
		     <form action="<?php echo url_for("admin/deleteProjectAdmin")?>" method="post" name="frmRemoveProjectAdmin" id="frmRemoveProjectAdmin">
		     	<input id="projectId" type="hidden" value="<?php echo $project->getProjectId()?>" name="projectId" readonly="readonly"/>
			     <div style="float: left;">
			     <table width="250" class="simpleList">
					<thead>
						<tr>
						<th class="listViewThS1">
							<input type="checkbox"  value="" name="allCheck" class="checkbox" id="allCheck"/>
						</th>
						<th class="listViewThS1"><?php echo __("Employee Name")?></th>
						</tr>
		    		</thead>
					<tbody>
						<?php foreach( $projectAdmins as $projectAdmin){?>
						<tr>
			       			<td class="odd">
			       				<input type="checkbox" value="<?php echo $projectAdmin->getEmployee()->getEmpNumber()?>" name="chkLocID[]" class="checkbox innercheckbox"/></td>
					 		<td class="odd"><?php echo $projectAdmin->getEmployee()->getFirstName().' '.$projectAdmin->getEmployee()->getLastName()?></td>
						</tr>
						<?php }?>
			 	 	</tbody>
			 	 </table>
			 	 </div>	
		 	 </form>
		 	<?php }?>
		 	 <br class="clear"/>
		     <div class="formbuttons">
                <input type="button" value="Add"  tabindex="6"  id="addBtn" class="addbutton"/>
                <input type="button" value="Delete" tabindex="7"  id="deleteBtn" class="delbutton"/>
             </div>
             <form  action="<?php echo url_for("admin/saveProjectAdmin")?>" method="post" name="frmAddProjectAdmin" id="frmAddProjectAdmin" >
	             <div id="addAdminLayer" class="hide">
					<label for="projAdminName"><?php echo __("Employee")?><span class="required">*</span></label>
						<input type="text" name="txtEmployee" id="txtEmployee" value=""></input>
						<input type="hidden" name="txtEmpId" id="txtEmpId" value="">
					<input id="projectId" type="hidden" value="<?php echo $project->getProjectId()?>" name="projectId" readonly="readonly"/>
					<input id="addProjectAdmin" class="addbutton" type="button" value="Assign"   tabindex="7" />
				</div>
			</form>
				<br class="clear"/>

         </div>
         
   </div>
   
   <script type="text/javascript">

		$(document).ready(function() {

			var data	= <?php echo str_replace('&#039;',"'",$empJson);?> ;

			//Validate the form
			 $("#frmSave").validate({
				
				 rules: {
				 	cmbCustomerId: { required: true },
				 	txtName: { required: true }
				 	
			 	 },
			 	 messages: {
			 		cmbCustomerId: '<?php echo __(ValidationMessages::REQUIRED); ?>',
				 		txtName: '<?php echo __(ValidationMessages::REQUIRED); ?>'
			 		
			 	 }
			 });

			//Validate the form
			 $("#frmAddProjectAdmin").validate({
				
				 rules: {
					 txtEmpId: { required: true }
				 	
			 	 },
			 	 messages: {
			 		txtEmpId: '<?php echo __(ValidationMessages::REQUIRED); ?>'
			 		
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

			//When click Add Button
				$("#addBtn").click(function() {
					$("#addAdminLayer").show();
				});

				//When click Add Button
				$("#addProjectAdmin").click(function() {
					$("#frmAddProjectAdmin").submit();
				});

				//When click Delete Button
				$("#deleteBtn").click(function() {
					$("#frmRemoveProjectAdmin").submit();
				});
			

				//When click reset buton 
					$("#resetBtn").click(function() {
						document.forms[0].reset('');
					 });

					//Auto complete
					$("#txtEmployee").autocomplete(data, {
                                            formatItem: function(item) {
                                                return $('<div/>').text(item.name).html();
                                            },
                                            formatResult: function(item) {
                                                return item.name
                                            }                                            
					}).result(function(event, item) {
					  	$('#txtEmpId').val(item.id);
					});
				  
					// When Click Main Tick box
					$("#allCheck").change(function() {
						if ($('#allCheck').attr('checked')) {
							$('.innercheckbox').attr('checked','checked');
						}else{
							$('.innercheckbox').removeAttr('checked');
						}
						
					});  										
				
		 });
</script>
       
