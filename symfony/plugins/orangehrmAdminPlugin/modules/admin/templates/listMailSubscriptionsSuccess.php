<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
   <div class="formpageNarrow">
        	<div class="outerbox">
        		<div class="top">
        			<div class="left"></div><div class="right"></div><div class="middle"></div>
        		</div>
            <div class="mainHeading"><h2><?php echo __("Subscribe to E-mail Notifications")?></h2></div>

        
	            <form action="" onsubmit="" method="post" id="frmSave" name="frmSave">

                <?php echo $form['_csrf_token']; ?>
                <input type="hidden" value="UpdateRecord" name="sqlState"/>
                
                <input type="checkbox"  class="formCheckbox" value="1" name="notificationMessageStatus[]" <?php if(isset($notficationList[1]) && $notficationList[1]==1){ echo "checked='checked'";}?>/>
                <label for="txtSkillName"><?php echo __("Leave Applications")?></label> 
                <input type="text" value="<?php echo $mailnot[1]; ?>" class="formInputText" id="txtMailAddress_1" name="txtMailAddress_1"/>
	            <br class="clear"/>
                
                <input type="checkbox"  class="formCheckbox" value="<?php echo MailNotification::LEAVE_ASSIGNMENT ?>" name="notificationMessageStatus[]" <?php if(isset($notficationList[MailNotification::LEAVE_ASSIGNMENT]) && $notficationList[MailNotification::LEAVE_ASSIGNMENT] == MailNotification::STATUS_SUBSCRIBED) { echo "checked='checked'"; } ?>/>
                <label for="txtSkillName"><?php echo __("Leave Assignments")?></label> 
                <input type="text" value="<?php echo $mailnot[MailNotification::LEAVE_ASSIGNMENT]; ?>" class="formInputText" id="txtMailAddress_<?php echo MailNotification::LEAVE_ASSIGNMENT ?>" name="txtMailAddress_<?php echo MailNotification::LEAVE_ASSIGNMENT ?>"/>
	            <br class="clear"/>
                
                <input type="checkbox"  class="formCheckbox" value="2" name="notificationMessageStatus[]" <?php if(isset($notficationList[2]) && $notficationList[2]==1){ echo "checked='checked'";}?>/>
	            <label for="txtSkillName"><?php echo __("Leave Approvals")?></label>	            
	            <input type="text" value="<?php echo $mailnot[2]; ?>" class="formInputText" id="txtMailAddress_2" name="txtMailAddress_2"/>
	            <br class="clear"/>
                
                <input type="checkbox"  class="formCheckbox" value="0" name="notificationMessageStatus[]" <?php if(isset($notficationList[0]) && $notficationList[0]==1){ echo "checked='checked'";}?>/>
	            <label for="txtSkillName"><?php echo __("Leave Cancellations")?></label>	           
	            <input type="text" value="<?php echo $mailnot[0]; ?>" class="formInputText" id="txtMailAddress_0" name="txtMailAddress_0"/>
	            <br class="clear"/>
                
                <input type="checkbox"  class="formCheckbox" value="-1" name="notificationMessageStatus[]" <?php if(isset($notficationList[-1]) && $notficationList[-1]==1){ echo "checked='checked'";}?>/>
	            <label for="txtSkillName"><?php echo __("Leave Rejections")?></label>
	            <input type="text" value="<?php echo $mailnot[-1]; ?>" class="formInputText" id="txtMailAddress_-1" name="txtMailAddress_-1"/>
	            
	            <br class="clear"/>
	            <input type="checkbox"  class="formCheckbox" value="3" name="notificationMessageStatus[]" <?php if(isset($notficationList[3]) && $notficationList[3]==1){ echo "checked='checked'";}?>/>
                <label for="txtSkillName"><?php echo __("HSP Notifications")?></label>
	            <input type="text" value="<?php echo $mailnot[3]; ?>" class="formInputText" id="txtMailAddress_3" name="txtMailAddress_3"/>
	            
	            <br class="clear"/>
	            <input type="checkbox"  class="formCheckbox" value="8" name="notificationMessageStatus[]" <?php if(isset($notficationList[8]) && $notficationList[8]==1){ echo "checked='checked'";}?>/>
                <label for="txtSkillName"><?php echo __("Performance Review Submissions")?></label>
                <input type="text" value="<?php echo $mailnot[8]; ?>" class="formInputText" id="txtMailAddress_8" name="txtMailAddress_8"/>    
	            
	            <br class="clear"/>
	
	                <div class="formbuttons">
	                    <input type="button" value="<?php echo __("Edit")?>"   id="editBtn" class="editbutton"/>
	                    <input type="button" value="<?php echo __("Reset")?>" id="resetBtn"  tabindex="3"  class="clearbutton"/>
	                </div>
	            </form>
            </div>
        	<div class="bottom">
        		<div class="left"></div><div class="right"></div><div class="middle"></div>
        	</div>
       
        <div class="requirednotice"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
    </div>
<script type="text/javascript">
    
    var invalid_email=false;
    
	$(document).ready(function() {

	var mode	=	'edit';
		
		//Disable all fields
		$('#frmSave :input').attr('disabled', true);
		$('#editBtn').removeAttr('disabled');
		
		$("#editBtn").click(function() {
			
			if( mode == 'edit')
			{
				$('#editBtn').attr('value', 'Save'); 
				$('#frmSave :input').removeAttr('disabled');
				mode = 'save';
			}else
			{
			
            checkValidity();
            
                if(!invalid_email) {
                    $('#frmSave').submit();
                }else {
                    alert('<?php echo __(ValidationMessages::EMAIL_INVALID); ?>');
                    invalid_email = false;
                }
				
			}
		});


		//When click reset buton 
			$("#resetBtn").click(function() {
				document.forms[0].reset('');
			 });

	 });
     
     function checkValidity() {
        
        $.each($("input[name='notificationMessageStatus[]']:checked"), function() {
            
            var num = $(this).val();
            if(!validateEmail($('#txtMailAddress_'+ num).val())){
                invalid_email = true;
                $('#txtMailAddress_'+ num).focus();
            } 
            
            
        });
     }
     
     function validateEmail(email) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        var address = email;
            if(reg.test(address) == false) {                
                return false;
            }else{
                return true;
            }
    }
    
</script>