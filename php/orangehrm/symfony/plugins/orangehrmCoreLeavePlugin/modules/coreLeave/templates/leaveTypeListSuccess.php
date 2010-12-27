<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
?>

<?php if(!empty($messageType))  {?>
<div id="messagebar" class="messageBalloon_<?php echo $messageType;?>" style="margin-left: 16px;width: 470px;">
	<span style="font-weight: bold;"><?php echo $message; ?></span>
</div>
<?php } ?>
<div id="errorDiv"></div>

<div class="outerbox" style="width:500px;">

    <div class="mainHeading"><h2><?php echo __('Leave Types'); ?></h2></div> 
 
	<form method="post" name="frmLeaveTypeList" id="frmLeaveTypeList" action="<?php echo url_for('coreLeave/leaveTypeList'); ?>">
    
	<div class="actionbar"> 
	
		<div class="actionbuttons"> 

			<input type="button" class="addbutton" name="btnAdd" id="btnAdd" value="<?php echo __('Add'); ?>" /> 
			<input type="button" class="delbutton" name="btnDel" id="btnDel" value="<?php echo __('Delete'); ?>" />
			<input type="hidden" name="hdnEditId" id="hdnEditId" value="" />
            <!--<input type="reset" class="resetbutton" value="Reset" />--> 
        
        </div> <!-- End of actionbuttons -->
         
        <!--<div class="noresultsbar"></div>-->
        <!--<div class="pagingbar"></div>-->
         
	</div> <!-- End of actionbar -->
    
    <br class="clear" />
    
	<table border="0" cellpadding="0" cellspacing="0" class="data-table"> 

	<thead> 
    <tr> 
    	<td width="50"> 
        	<input type="checkbox" class="innercheckbox" name="allCheck" id="allCheck" value="" /> 
      	</td> 
      	<td><?php echo __('Leave Type'); ?></td> 
    </tr> 
	</thead> 
  
	<tbody> 
	
	<?php $rowClass = 'odd' ?>
	
	<?php foreach ($leaveTypeList as $leaveType) { ?>
	
	<tr class="<?php echo $rowClass; ?>"> 
    	<td>
			<input type="checkbox" class="innercheckbox" name="chkLeaveType[]" value="<?php echo $leaveType->getLeaveTypeId(); ?>" />
    		<!--<input type='checkbox' class='checkbox' name='chkLeaveTypeID[]' value='LTY001' />-->
    	</td> 
        <td>
            <a href="#" class="leaveTypeNames"><?php echo $leaveType->getLeaveTypeName(); ?></a>
        </td> 
        
        <?php $rowClass = $rowClass=='odd'?'even':'odd'; ?>
        
    </tr>
    
    <?php } // End of $leaveTypeList foreach ?>
     
	</tbody> 

	</table> 
	
	<!--<div><span class="error" id="messageLayer1"></span></div>--> 
	<!--<div><span class="error" id="messageLayer2"></span></div>--> 

	</form>
	
</div> <!-- End of outerbox -->

<script type="text/javascript"> 

	$(document).ready(function() {
		
		// Hiding year of service from,to when loading
		if ($('#leaveType_chkYearsOfService').attr('checked')) {
			$('#yearOfService').show();
		} else {
		    $('#yearOfService').hide();
		}
		
		// Showing year of service from,to
		$('#leaveType_chkYearsOfService').click(function(){
			if ($('#leaveType_chkYearsOfService').attr('checked')) {
				$('#yearOfService').show();
			} else {
			    $('#yearOfService').hide();
			}
		});
		
		// Add button
		$('#btnAdd').click(function(){
			window.location.href = '<?php echo url_for('coreLeave/defineLeaveType'); ?>';
		});
		
		/* Delete button */
		$('#btnDel').click(function(){
			$('#frmLeaveTypeList').attr('action', '<?php echo url_for('coreLeave/deleteLeaveType'); ?>');
			$('#frmLeaveTypeList').submit();
		});
		
		
		$('innercheckbox').each(function(){
		    if ($(this).attr('checked')) {
		        alert($(this).value());
		    }
		});
		

		/* Edit button */
		$('#btnEdit').click(function(){

			var leaveTypeId = '';
			var checkedCount = 0;
			var errorCount = 0;
			var errorMessage = '';
			
			$('.innercheckbox').each(function(){
				
			    if ($(this).attr('checked')) {
			        leaveTypeId = $(this).val();
			    	checkedCount++;
			    }
			    
			});
		
		    if (checkedCount == 0) {
		    	errorCount++;
		    	errorMessage = '<?php echo __('Please select at least one leave type to edit'); ?>';
		    } else if (checkedCount > 1) {
		    	errorCount++;
		    	errorMessage = '<?php echo __('Please select only one leave type to edit'); ?>';
		    }
		    
		    if (errorCount > 0) {
		        $('#errorDiv').attr('class', 'messageBalloon_warning');
		        $('#errorDiv').empty();
		        $('#errorDiv').append('<ul><li>'+errorMessage+'</li></ul>');
		    }
		    
		    if (checkedCount == 1) {
				$('#hdnEditId').val(leaveTypeId);
				$('#frmLeaveTypeList').attr('method', 'get');
				$('#frmLeaveTypeList').attr('action', '<?php echo url_for('coreLeave/defineLeaveType'); ?>');
				$('#frmLeaveTypeList').submit();
		    }

		});

        $('.leaveTypeNames').live('click', function(){

            $('#hdnEditId').val($(this).parent().siblings().children(':checkbox').val());
            $('#frmLeaveTypeList').attr('method', 'get');
            $('#frmLeaveTypeList').attr('action', '<?php echo url_for('coreLeave/defineLeaveType'); ?>');
            $('#frmLeaveTypeList').submit();

        });
		
		/* Checkbox behavior */
		$("#allCheck").click(function() {
			if ($('#allCheck').attr('checked')) {
				$('.innercheckbox').attr('checked', true);
                $('#btnDel').attr('disabled', false);
			} else {
				$('.innercheckbox').attr('checked', false);
			}
		});

		$(".innercheckbox").click(function() {
			if(!($(this).attr('checked'))) {
				$('#allCheck').attr('checked', false);
			}
            $('#btnDel').attr('disabled', false);
		});
		
	}); // ready():Ends

</script>
