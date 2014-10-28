<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<div class="formpage">
        <div class="outerbox">
        	<div class="top">
        		<div class="left"></div>
        		<div class="right"></div>
        		<div class="middle"></div>
        	</div>
        	<div class="maincontent">
            	<div class="mainHeading"><h2><?php echo __("Project Activities")?></h2></div>
					
			        	<form action="" method="post" name="frmActivity" id="frmActivity">
			            <label for="cmbProjectId"><?php echo __("Project")?></label>
			            <select class="formSelect"  name="id" id="id">
			            	<option value=""><?php echo __("Select Project")?></option>
							<?php foreach( $listProject as $project){?>
								<option value="<?php echo $project->getProjectId()?>" <?php if($currentProject==$project->getProjectId()){ echo "selected";}?>><?php echo $project->getName().'-'.$project->getCustomer()->getName()?></option>
							<?php }?>
						</select>
						</form>
			            <br class="clear"/>
			            <hr style="margin: 15px 0px; width: 420px; float: left;"/>
			            <?php if($hasProjectActivity){?>
				           <form action="<?php echo url_for("admin/deleteProjectActivity")?>" method="post" name="frmRemoveProjectAdmin" id="frmRemoveProjectAdmin">
		     					<input type="hidden" name="id" id="id" value="<?php echo  $currentProject?>"></input>
				            <div style="float: left;">
							<table width="250" class="simpleList">
								<thead>
									<tr>
									<th class="listViewThS1">
										<input type="checkbox"  value="" name="allCheck" class="checkbox" id="allCheck"/>
									</th>
									<th class="listViewThS1"><?php echo __("Activity")?></th>
									</tr>
					    		</thead>
									    		<tbody>
									    		<?php foreach( $projectActivityList as $projectActivity){?>
									    		<tr>
									       			<td class="odd">
									       				<input type="checkbox"  value="<?php echo $projectActivity->getActivityId()?>" name="chkLocID[]" class="checkbox innercheckbox"/>
									       			</td>
											 			<td class="odd">
											 			<?php echo $projectActivity->getName()?>			 		
											 		</td>
												</tr>
							 		    		<?php }?>
							 		 		</tbody></table>
							</div>
							</form>
							 <br class="clear"/>
						<?php }else{?>
						 <br class="clear"/>
			           
			      			<div class="notice"><?php echo __("No Activities defined.")?></div>
				  
			           
			            <?php } ?>
			             <div class="formbuttons">
			                
			                <input type="button" value="<?php echo __("Add")?>"  tabindex="4"  id="addBtn" class="savebutton"/>
			                 <?php if($hasProjectActivity){?>
			                 <input type="button" value="Delete" tabindex="7"  id="deleteBtn" class="delbutton"/>
			                 <?php }?>                 
			            </div>
			            <br class="clear"/>
			                        
						<div style="display: none;" id="addActivityLayer">
							<form action="<?php echo url_for('admin/saveProjectActivity')?>" method="post" name="frmAddActivity" id="frmAddActivity">
						    	<input type="hidden" name="id" id="id" value="<?php echo  $currentProject?>"></input>
						    	<label for="activityName"><?php echo __("Activity")?><span class="required">*</span></label>
					            <input type="text" class="formInputText" value="" id="activityName" name="activityName"/>
				                <br class="clear"/>
				                                
				                 <div class="formbuttons">
				                    
				                    <input type="button" value="<?php echo __("Save")?>"  tabindex="7"  id="addProjectActivity" class="savebutton"/>
				                    <input type="button" value="<?php echo __("Cancel")?>"  id="adminCancelBtn" tabindex="8"  class="clearbutton"/>
				                             
				                </div>                
			                  </form>   
						</div>
			            <br class="clear"/>            
			     
    		</div>
    		<div class="bottom">
    			<div class="left"></div>
        		<div class="right"></div>
        		<div class="middle"></div>
    		</div>
</div>
 <script type="text/javascript">

	$(document).ready(function() {

		//When click Add Button
		$("#addBtn").click(function() {
			$("#addActivityLayer").show();
		});

		//When click Add Button
		$("#adminCancelBtn").click(function() {
			$("#addActivityLayer").hide();
		});

		//When Change the project
		$("#id").change(function() {
			$("#frmActivity").submit();
			
		});

		
		//When Adding project activity
		$("#addProjectActivity").click(function() {
			$("#frmAddActivity").submit();
			
		});

		//When delete project activity
		$("#deleteBtn").click(function() {
			$("#frmRemoveProjectAdmin").submit();
			
		});

		//Validate the form
		 $("#frmAddActivity").validate({
			
			 rules: {
			 	activityName: { required: true }
			 	
		 	 },
		 	 messages: {
		 		activityName: '<?php echo __(ValidationMessages::REQUIRED); ?>'
		 		
		 	 }
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