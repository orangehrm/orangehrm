<link href="<?php echo public_path('../../themes/orange/css/leave.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<style type="text/css">
.error_list {
    color: #ff0000;
}
</style>

<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/applyLeaveSuccess'); ?>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>

<?php if(!empty($overlapLeaves)){?>
<div id="duplicateWarning" class="confirmBox" style="margin-left:18px;">
 	<div class="confirmInnerBox">
	<?php echo __('The following leave requests are on the same day as the current leave request<br>
	Please cancel the existing leave requests and submit again  or change the leave period if needed.')?>
		</div>
</div>

<table border="0" cellspacing="0" cellpadding="0" style="margin-left: 18px;" class="simpleList">
<thead>
	<tr>
		<th width="100px" class="tableMiddleMiddle"><?php echo __("Date")?></th>
		<th width="50px" class="tableMiddleMiddle"><?php echo __("No of Hours")?></th>
		<th width="100px" class="tableMiddleMiddle"><?php echo __("Leave Period")?></th>
		<th width="90px" class="tableMiddleMiddle"><?php echo __("Leave Type")?></th>
		<th width="100px" class="tableMiddleMiddle"><?php echo __("Status")?></th>
		<th width="150px" class="tableMiddleMiddle"><?php echo __("Comments")?></th>
	</tr>
</thead>
<tbody>
<?php foreach($overlapLeaves as $leave){?>

	<tr>
		<td class="odd"><?php echo $leave->getLeaveDate()?></td>
		<td class="odd"><?php echo $leave->getLeaveLengthHours()?></td>
		<td class="odd"><?php echo $leave->getLeaveRequest()->getLeavePeriod()->getStartDate()?></td>
		<td class="odd"><?php echo $leave->getLeaveRequest()->getLeaveTypeName()?></td>
		<td class="odd"><?php echo $leave->getTextLeaveStatus()?></td>
		<td class="odd"><?php echo $leave->getLeaveComments()?></td>
	</tr>
<?php }?>

</tbody>
</table>
<?php }?>
<div class="formpage">
	<?php echo isset($templateMessage)?templateMessage($templateMessage):''; ?>
	<?php if( count($form->leaveTypeList) > 1){ ?>
    <div class="outerbox">
        <div class="mainHeading"><h2 class="paddingLeft"><?php echo __('Apply Leave')?></h2></div>
        
			<?php if($form->hasErrors()){?>
				<?php echo $form['txtEmpID']->renderError(); ?>
				<?php echo $form['txtLeaveType']->renderError(); ?>
				<?php echo $form['txtFromDate']->renderError(); ?>
				<?php echo $form['txtToDate']->renderError(); ?>
				<?php echo $form['txtLeaveTotalTime']->renderError(); ?>
				<?php echo $form['txtComment']->renderError(); ?>
				<?php echo $form['txtFromTime']->renderError(); ?>
			<?php }?>
			
			<form id="frmLeaveApply" name="frmLeaveApply" method="post" action="">
            <?php echo $form['_csrf_token']; ?>
            <?php  echo $form['txtEmpID']->render();?>
            <?php  echo $form['txtEmpWorkShift']->render();?>
                <table border="0" cellspacing="0" cellpadding="2" class="tableArrange">
                <tr>      
                    <td width="100" valign="top"><?php  echo __('Leave Type').' <span class=required>*</span>';?></td>
                    <td><?php  echo $form['txtLeaveType']->render();?><br class="clear" /></td>
                </tr>
                <tr>
                    <td class="labelCell"><?php echo __('From Date').' <span class=required>*</span>';?></td>
                    <td><?php  echo $form['txtFromDate']->render(array('size'=>'10','class'=>'formDateInput'));?>
                        <input id="fromDateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
                        <br class="clear" />
                    </td>
                </tr>
                <tr>
                    <td class="labelCell"><?php echo __('To Date').' <span class=required>*</span>';?></td>
                    <td><?php  echo $form['txtToDate']->render(array('size'=>'10','class'=>'formDateInput'));?>
                        <input id="toDateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
                        <br class="clear" />
                    </td>
                </tr>

                <tr id="trTime1" class="hide">
                    <td height="25" class="labelCell"><?php  echo __('From Time');?></td>
                    <td><?php  echo $form['txtFromTime']->render();?></td>
                </tr>
                <tr id="trTime2" class="hide">
                    <td height="25"><?php  echo __('To Time');?></td>
                    <td><?php  echo $form['txtToTime']->render();?></td>
                </tr>
                <tr id="trTime3" class="hide">
                    <td height="25"><?php  echo __('Total Hours');?></td>
                    <td><?php  echo $form['txtLeaveTotalTime']->render(array('style'=>'width:3em;'));?>
                        <br class="clear" />
                    </td>
                </tr>
                <tr>
                    <td id="trTime4" class="hide" colspan="2"></td>
                </tr>
                <tr>
                    <td valign="top"><?php echo __('Comment')?></td>
                    <td><?php  echo $form['txtComment']->render(array('rows'=>'3','cols'=>'30'));?><br class="clear" /></td>
                </tr>
            </table>
        <!-- here we have the button -->
        <div class="formbuttons paddingLeft">
            <input type="button" class="applybutton" id="saveBtn" value="Apply" title="Apply"/>
        </div>
</form>
</div>
    <div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>
<?php }?>
</div>
 <script type="text/javascript">
 $(document).ready(function() {
	
	 
	$.datepicker.setDefaults({showOn: 'click'});
	 
	var dateFormat	=	'YYYY-MM-DD' ;
	//Load default Mask if empty
	 var fromDateValue 	= 	trim($("#applyleave_txtFromDate").val());
	 if(fromDateValue == ''){
		 $("#applyleave_txtFromDate").val(dateFormat);
	 }

	 var toDateValue	=	trim($("#applyleave_txtToDate").val());
	 if(toDateValue == ''){
		 $("#applyleave_txtToDate").val(dateFormat);
	 }

	 //Clear Date format when click 
	 $("#applyleave_txtFromDate").click(function(){
		 if(trim($("#applyleave_txtFromDate").val()) == dateFormat)
		 	$("#applyleave_txtFromDate").val('');
	 });

	 $("#applyleave_txtToDate").click(function(){
		 if(trim($("#applyleave_txtToDate").val()) == dateFormat)
		 	$("#applyleave_txtToDate").val('');
	 });

	 //Show From if same date 
	 if(trim($("#applyleave_txtFromDate").val()) != dateFormat && trim($("#applyleave_txtToDate").val()) != dateFormat){
		 if( trim($("#applyleave_txtFromDate").val()) == trim($("#applyleave_txtToDate").val())) {
			 $("#trTime1").show();
             $("#trTime2").show();
             $("#trTime3").show();
         }
	 }
		 
	 
	 //Bind blur event of From Date
	 $("#applyleave_txtFromDate").blur(function() {
		 var fromDateValue 	= 	trim($("#applyleave_txtFromDate").val());
		 if(fromDateValue == ''){
			 $("#applyleave_txtFromDate").val(dateFormat);
		 }else{
			 var toDateValue	=	trim($("#applyleave_txtToDate").val());
			 fromdate = (fromDateValue).split("-");
			 todate	  = (toDateValue).split("-");
	         if(validateDate(parseInt(fromdate[2],10), parseInt(fromdate[1],10), parseInt(fromdate[0],10))){
				 if(fromDateValue == toDateValue) {
					 $("#trTime1").show();
                     $("#trTime2").show();
                     $("#trTime3").show();
                 }
		         if(!validateDate(parseInt(todate[2],10), parseInt(todate[1],10), parseInt(todate[0],10))){
	        	 	$("#trTime1").show();
                    $("#trTime2").show();
                    $("#trTime3").show();
	        	 	$("#applyleave_txtToDate").val(fromDateValue);
	         	 }
	         }
	         else {
	        	 $("#trTime1").hide(); 
                 $("#trTime2").hide();
                 $("#trTime3").show();
             }
		 } 
	 });

	//Bind blur event of To Date
	 $("#applyleave_txtToDate").blur(function() {
		
		 var toDateValue	=	trim($("#applyleave_txtToDate").val());
		 if(toDateValue == ''){
			 $("#applyleave_txtToDate").val(dateFormat);
		 }else{
			 var fromDateValue 	= 	trim($("#applyleave_txtFromDate").val());
			 fromdate = (fromDateValue).split("-");
			 todate	  = (toDateValue).split("-");
			 
	         if(validateDate(parseInt(fromdate[2],10), parseInt(fromdate[1],10), parseInt(fromdate[0],10)) && validateDate(parseInt(todate[2],10), parseInt(todate[1],10), parseInt(todate[0],10))){
	        	 
	         	 if(fromDateValue == toDateValue) {
	         		$("#trTime1").show();
                    $("#trTime2").show();
                    $("#trTime3").show();
                 } else {
	         		$("#trTime1").hide();
                    $("#trTime2").hide();
                    $("#trTime3").hide();
                 }
	         }
		 }
	 });

	 // Bind On change event of From Time 
	 	$('#applyleave_txtFromTime').change(function() {
	 		fillTotalTime();
		});

	// Bind On change event of To Time 
	 	$('#applyleave_txtToTime').change(function() {
	 		fillTotalTime();
		});
	
		//Bind date picker
        daymarker.bindElement("#applyleave_txtFromDate", {onSelect: function(theDate){
        	showTimepaneFromDate(theDate,dateFormat);
        }});

        $('#fromDateBtn').click(function(){
           daymarker.show("#applyleave_txtFromDate");
        });

        daymarker.bindElement("#applyleave_txtToDate", {onSelect: function(theDate){
        	showTimepaneToDate(theDate);
        }});

        $('#toDateBtn').click(function(){
           daymarker.show("#applyleave_txtToDate");
        });
        
        
     /*   $("#applyleave_txtFromDate").datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, onSelect:function(theDate){showTimepaneFromDate(theDate,dateFormat)}});
		$('#fromDateBtn').click(function(){
			//$("#applyleave_txtFromDate").val('');
            $("#applyleave_txtFromDate").datepicker('show');
		});
        $("#applyleave_txtToDate").datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, onSelect:function(theDate){showTimepaneToDate(theDate)}});
		$('#toDateBtn').click(function(){
			//$("#applyleave_txtToDate").val('');
            $("#applyleave_txtToDate").datepicker('show');
		});
		*/
		
		//Validation 	
		 $("#frmLeaveApply").validate({
				 rules: {
			        'applyleave[txtLeaveType]':{required: true },
			 		'applyleave[txtFromDate]': { required: true , dateISO: true, validDateFormat: true, validFromDate: true},
			 		'applyleave[txtToDate]': { required: true ,dateISO: true,validDateFormat: true },
			 		'applyleave[txtComment]': {maxlength: 200},
			 		'applyleave[txtLeaveTotalTime]':{ required: false , number: true , min: 0.01, validWorkShift : true,validTotalTime : true},
			 		'applyleave[txtToTime]': {validToTime: true}
			 	 },
			 	 messages: {
			 		'applyleave[txtLeaveType]':{ 
			 			required:"Leave Type is required"
			 		},
				 	'applyleave[txtFromDate]':{ 
			 			required:"From Date is required",
			 			dateISO:"From Date should be filled in YYYY-MM-DD format",
                        validDateFormat:"Invalid From Date",
			 			validFromDate: "From date should be before to date"
                        
			 		},
			 		'applyleave[txtToDate]':{ 
			 			required:"To Date is required",
			 			dateISO:"To Date should be filled in YYYY-MM-DD format ",
                        validDateFormat:"Invalid To Date"
			 			
			 		},
			 		'applyleave[txtComment]':{ 
			 			maxlength:"Comment length should be less than 200 characters"	
			 		},
			 		'applyleave[txtLeaveTotalTime]':{ 
			 			number:"Total time is numeric value",
			 			min : "Total time should be greater than 0.01",	
			 			max : "Total time should be lesser than 24"	,
			 			validTotalTime : "Invalid total time",
			 			validWorkShift : "Total time is greater than the shift length"
			 		},
			 		'applyleave[txtToTime]':{ 
			 			validToTime:"From time should be lesser than To time"	
			 		}
			 	 },
                errorElement : 'div',
			 	errorPlacement: function(error, element) {

                        //this is for leave type
                        error.insertAfter(element.next(".clear"));

                        //these are specially for date boxes
                        error.insertAfter(element.next().next(".clear"));
		   			 }
			 });
		
	        /* Valid From Date */
	        $.validator.addMethod("validFromDate", function(value, element) {
	        	
	            var fromdate	=	$('#applyleave_txtFromDate').val();
	            fromdate = (fromdate).split("-");
	            if(!validateDate(parseInt(fromdate[2],10), parseInt(fromdate[1],10), parseInt(fromdate[0],10))) {
	               return false;
	            }
	            var fromdateObj = new Date(parseInt(fromdate[0],10), parseInt(fromdate[1],10) - 1, parseInt(fromdate[2],10));
	            var todate		=	$('#applyleave_txtToDate').val();
	            todate = (todate).split("-");
	            var todateObj	=	new Date(parseInt(todate[0],10), parseInt(todate[1],10) - 1, parseInt(todate[2],10));
				
				if(fromdateObj > todateObj){
	    			return false;
				}
	    		else{
	    			return true;
	    		}

	        });

	        $.validator.addMethod("validDateFormat", function(value, element) {

	            var fromdate	=	value;
	            fromdate = (fromdate).split("-");
	            if(!validateDate(parseInt(fromdate[2],10), parseInt(fromdate[1],10), parseInt(fromdate[0],10))) {
	               return false;
	            }
                return true;

	        });

	        $.validator.addMethod("validTotalTime", function(value, element) {
		        var totalTime	=	$('#applyleave_txtLeaveTotalTime').val();
		        var fromdate	=	$('#applyleave_txtFromDate').val();
		        var todate		=	$('#applyleave_txtToDate').val();

				if((fromdate==todate) && (totalTime==''))
					return false;
				else
					return true;
		        
	        });  

	        $.validator.addMethod("validWorkShift", function(value, element) {
		        var totalTime	=	$('#applyleave_txtLeaveTotalTime').val();
		        var fromdate	=	$('#applyleave_txtFromDate').val();
		        var todate		=	$('#applyleave_txtToDate').val();
				var workShift	=	$('#applyleave_txtEmpWorkShift').val();
				
				if((fromdate==todate) && (parseFloat(totalTime) > parseFloat(workShift)))
					return false;
				else
					return true;
		        
	        });

	        $.validator.addMethod("validToTime", function(value, element) {
		       
		        var fromdate	=	$('#applyleave_txtFromDate').val();
		        var todate		=	$('#applyleave_txtToDate').val();
		        var fromTime	=	$('#applyleave_txtFromTime').val();
		        var toTime		=	$('#applyleave_txtToTime').val();

		        var fromTimeArr = (fromTime).split(":");
		        var toTimeArr = (toTime).split(":");
		        var fromdateArr	=	(fromdate).split("-");

				var fromTimeobj	=	new Date(fromdateArr[0],fromdateArr[1],fromdateArr[2],fromTimeArr[0],fromTimeArr[1]);
				var toTimeobj	=	new Date(fromdateArr[0],fromdateArr[1],fromdateArr[2],toTimeArr[0],toTimeArr[1]);
		        
				if((fromdate==todate) && (fromTime !='') && (toTime != '') && (fromTimeobj>=toTimeobj))
					return false;
				else
					return true;
		        
	        }); 
			
	//Click Submit button 
		$('#saveBtn').click(function(){
			$('#frmLeaveApply').submit();
		});
 });

 function showTimepaneFromDate(theDate,dateFormat){
		var Todate	=	trim($("#applyleave_txtToDate").val());
		if(Todate == dateFormat ){
			$("#applyleave_txtFromDate").val(theDate);
		 	$("#trTime1").show();
            $("#trTime2").show();
            $("#trTime3").show();
		 	$("#applyleave_txtToDate").val(theDate);
		}else{
			if(Todate == theDate ) {
				$("#trTime1").show();
                $("#trTime2").show();
                $("#trTime3").show();
            } else {
				$("#trTime1").hide();
                $("#trTime2").hide();
                $("#trTime3").hide();
            }
		}
		$("#applyleave_txtFromDate").valid();
		$("#applyleave_txtToDate").valid();
 }

 function showTimepaneToDate(theDate){
		var fromDate	=	trim($("#applyleave_txtFromDate").val());
		
		if(fromDate == theDate ) {
			$("#trTime1").show();
            $("#trTime2").show();
            $("#trTime3").show();
        } else {
			$("#trTime1").hide();
            $("#trTime2").hide();
            $("#trTime3").hide();
        }
		$("#applyleave_txtFromDate").valid();
		$("#applyleave_txtToDate").valid();
}
 
 //Calculate Total time
 function fillTotalTime(){
	 var fromTime = ($('#applyleave_txtFromTime').val()).split(":");
	 var fromdate = new Date();
	 fromdate.setHours(fromTime[0],fromTime[1]);

	 var toTime = ($('#applyleave_txtToTime').val()).split(":");
	 var todate = new Date();
	 todate.setHours(toTime[0],toTime[1]);


		if (fromdate < todate) {
			var difference = todate - fromdate;
		    
			//var hoursDifference = Math.floor(difference/1000/60/60);
			//difference -= hoursDifference*1000*60*60
			 
		    //var minutesDifference = Math.floor(difference/1000/60);
		    //difference -= minutesDifference*1000*60
					
			
			//$('#applyleave_txtLeaveTotalTime').val(hoursDifference+':'+minutesDifference);
			//$('#applyleave_txtLeaveTotalTime').val(parseFloat(difference/3600000));
			var floatDeference	=	parseFloat(difference/3600000) ;
			$('#applyleave_txtLeaveTotalTime').val(Math.round(floatDeference*Math.pow(10,2))/Math.pow(10,2));
		}

		$("#applyleave_txtToTime").valid();
 }
 
		
</script>