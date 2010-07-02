<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js');?>"></script>
<div id="content">
	<div id="contentContainer">
	<?php if(count($listJobTitle) == 0){?>
			<div id="messageBalloon_notice" class="messageBalloon_notice">
				<ul><li><?php echo __("Job titles haven't been defined")?> <a href="<?php echo '../../../.././lib/controllers/CentralController.php?uniqcode=JOB&amp;VIEW=MAIN' ?>"><?php echo __("Define Now")?></a></li></ul>		
			</div>
		<?php }?>
		<?php if($confirm){?>
					<div id="messageBalloon_notice" class="messageBalloon_notice">
						<ul><li><?php echo __("KPI already exists, This opertaion deletes exsting KPI")?> &nbsp;&nbsp;<a href="javascript:confirmOverwrite();"><?php echo __("Ok")?></a> &nbsp;&nbsp;<a href="javascript:cancelOverwrite();"><?php echo __("Cancel")?></a></li></ul>
					</div>
				<?php }?>
				
        <div class="outerbox">
            <div id="formHeading"><h2><?php echo __("Copy Key Performance Indicators")?></h2></div>
			<form action="#" id="frmSave" class="content_inner" method="post">

                        <?php echo $form['_csrf_token']; ?>
                            
				<input type="hidden" id="txtConfirm" name="txtConfirm" value="0">
			
               <div id="formWrapper">
                       <label for="txtLocationCode"><?php echo __("Copy From")?><span class="required">*</span></label>
                     <select name="txtJobTitle" id="txtJobTitle" class="formSelect" tabindex="1">
                        <option value=""><?php echo __("--Select Job Title--")?></option>
                     	 <?php foreach($listAllJobTitle as $jobTitle){?>
	                     	<option value="<?php echo $jobTitle->getId()?>" <?php if($fromJobTitle ==  $jobTitle->getId()){ print("selected");}?>><?php echo htmlspecialchars_decode($jobTitle->getName())?><?php echo ($jobTitle->getIsActive()==0)?' (Deleted)':''?></option>
	                     <?php }?>
                     </select>
                   <br class="clear"/>
                     <label for="txtLocationCode"><?php echo __("Copy To")?><span class="required">*</span></label>
                     <select name="txtCopyJobTitle" id="txtCopyJobTitle" class="formSelect" tabindex="1">
                        <option value=""><?php echo __("--Select Job Title--")?></option>
                     	 <?php foreach($listJobTitle as $jobTitle){?>
	                     	<option value="<?php echo $jobTitle->getId()?>" <?php if($toJobTitle ==  $jobTitle->getId()){ print("selected");}?>><?php echo htmlspecialchars_decode($jobTitle->getName())?></option>
	                     <?php }?>
                     </select>
                   <br class="clear"/>
             		 <br class="clear"/>
             </div>
			<div id="buttonWrapper">
                    <input type="button" class="savebutton" id="saveBtn"
                        value="Save" tabindex="6" />
                        
                     <input type="button" class="savebutton" id="resetBtn"
                        value="Reset" tabindex="7" />
                    
            </div>  
              
            </form>
        </div>
 	</div>


   <script type="text/javascript">
	    

    	if (document.getElementById && document.createElement) {
	 			roundBorder('outerbox');
				//roundBorder('outerboxList');
			}
	
	</script> 
	
	 <script type="text/javascript">

		$(document).ready(function() {

			//Validate the form
			 $("#frmSave").validate({
				
				 rules: {
				 	txtJobTitle: { required: true },
				 	txtCopyJobTitle: { required: true }
			 	 },
			 	 messages: {
			 		txtJobTitle: "<?php echo __("Job Title is required")?>", 
			 		txtDescription: "<?php echo __("Copy Job Title is required")?>"
			 		
			 	 }
			 });

			// when click Save button 
				$('#saveBtn').click(function(){
					$('#frmSave').submit();
				});

				// when click reset button 
				$('#resetBtn').click(function(){
               $("label.error").each(function(i){
                  $(this).remove();
               });
					document.forms[0].reset('');
				});

		 });
		
		function confirmOverwrite(){
			$('#txtConfirm').val('1');
			$('#frmSave').submit();
		}		

		function cancelOverwrite(){
			location.href = "<?php echo url_for('performance/listDefineKpi') ?>";
		}
	</script>
