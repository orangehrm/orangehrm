<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<div class="formpage">
	<div class="navigation">
		<input class="savebutton" type="button"  value="Back" id="btnBack" name="btnBack"/>
	</div>
	
		<div class="outerbox">
			<div class="top">
				<div class="left"></div>
		     	<div class="right"></div>
		     	<div class="middle"></div>
			</div>
			<div class="maincontent">
		        <div class="mainHeading"><h2><?php echo __("Rights Assigned to User Groups")?></h2></div>
		
		                <input type="hidden" value="" id="STAT" name="STAT"/>
		        <span class="formLabel"><?php echo __("User Group ID")?></span>
		        <span class="formValue"><?php echo $userGroup->getUsergId()?></span>
		        <input type="hidden" value="USG003" name="txtUserGroupID" id="txtUserGroupID"/>
		        <br class="clear"/>
		        <span class="formLabel"><?php echo __("Admin User Group")?></span>
		        <span class="formValue"><?php echo $userGroup->getUsergName()?></span>
		        <br class="clear"/>
		    </div>
		     <div class="bottom">
		     	<div class="left"></div>
		     	<div class="right"></div>
		     	<div class="middle"></div>
		     </div>
		</div>
	   
	   
	   <div class="outerbox">
	   		<div class="top">
				<div class="left"></div>
		     	<div class="right"></div>
		     	<div class="middle"></div>
			</div>
		   <div class="maincontent">
		   <form action="<?php echo url_for('admin/SaveUserGroupRight')?>" method="post" id="frmSave" name="frmSave">
		   <input type="hidden" name="id" value="<?php echo $userGroup->getUsergId()?>">
		        <label for="cmbModuleID"><?php echo __("Module")?></label>
				<select class="formSelect" id="cmbModuleID" name="cmbModuleID">
		            <option value=""><?php echo __("--Select Module--")?></option>
		            <?php foreach($moduleList as $module){?>
		            	<option value="<?php echo $module->getModId()?>"><?php echo $module->getName()?></option>
		            <?php }?>
				</select>
		
		        <br class="clear"/>
		        <label for="chkAdd"><?php echo __("Add")?></label>
		        <input type="checkbox" class="formCheckboxWide" value="1" id="chkAdd" name="chkAdd"/>
		
		        <label for="chkEdit"><?php echo __("Edit")?></label>
		        <input type="checkbox" class="formCheckboxWide" value="1" id="chkEdit" name="chkEdit"/>
		        <br class="clear"/>
		
		        <label for="chkDelete"><?php echo __("Delete")?></label>
		        <input type="checkbox" class="formCheckboxWide" value="1" id="chkDelete" name="chkDelete"/>
		
		        <label for="chkView"><?php echo __("View")?></label>
		        <input type="checkbox" class="formCheckboxWide" value="1" id="chkView" name="chkView"/>
		        <br class="clear"/>
		
		        <div class="formbuttons">
		        	<input type="button" title="Save" value="Save" tabindex="5" id="saveBtn" class="savebutton"/>
		        </div>
	        </form>
	    </div>
    	<div class="bottom">
	     	<div class="left"></div>
	     	<div class="right"></div>
	     	<div class="middle"></div>
	     </div>
    
    </div>
    
    	<div class="outerbox">
    	<div class="top">
				<div class="left"></div>
		     	<div class="right"></div>
		     	<div class="middle"></div>
			</div>
    	<div class="maincontent">
    	 <form action="<?php echo url_for('admin/deleteUserGroupRight')?>" method="post" id="frmDelete" name="frmDelete">
    	  <input type="hidden" name="id" value="<?php echo $userGroup->getUsergId()?>">
        <div class="subHeading"><h3><?php echo __("Assigned Rights")?></h3></div>

			<table cellspacing="0" cellpadding="5" border="0" width="100%" class="">
			        <tbody><tr>
						 <td><strong><?php echo __("Module")?></strong></td>
						 <td><strong><?php echo __("Add")?></strong></td>
						 <td><strong><?php echo __("Edit")?></strong></td>
						 <td><strong><?php echo __("Delete")?></strong></td>
						 <td><strong><?php echo __("View")?></strong></td>
					</tr>
					<?php foreach($moduleRights as $right){?>
						<tr>
						 <td><?php echo $right->getModule()->getName()?></td>
						 <td><?php  if($right->getAddition()==1){echo "Yes";}else{echo "No"; }?></td>
						 <td><?php  if($right->getEditing()==1){echo "Yes";}else{echo "No"; }?></td>
						 <td><?php  if($right->getDeletion()==1){echo "Yes";}else{echo "No"; }?></td>
						 <td><?php  if($right->getViewing()==1){echo "Yes";}else{echo "No"; }?></td>
						</tr>
					<?php }?>
					
			</tbody></table>
        <div class="formbuttons">
        <input type="button" title="Reset" value="Reset"  id="delBtn" class="delbutton"/>

        </div>
        </form>
    	</div>
    	<div class="bottom">
	     	<div class="left"></div>
	     	<div class="right"></div>
	     	<div class="middle"></div>
	     </div>
    
    </div>
</div>
 <script type="text/javascript">

		$(document).ready(function() {

			//Validate the form
			 $("#frmSave").validate({
				
				 rules: {
				 	cmbModuleID: { required: true},
				 	chkView: { required: true }
			 	 },
			 	 messages: {
			 		cmbModuleID: "<?php echo __("Module is required")?>",
			 		chkView: "<?php echo __("View is required")?>"
			 	 }
			 });

			//When click reset buton 
			$("#saveBtn").click(function() {
				$('#frmSave').submit();
			 });

			//When click reset buton 
			$("#delBtn").click(function() {
				$('#frmDelete').submit();
			 });
			 
			 //When Click back button 
			 $("#btnBack").click(function() {
				 location.href = "<?php echo url_for("admin/listUser?isAdmin=".$userType)?>";  
				});
				
		 });
		</script>