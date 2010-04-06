<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js')?>"></script>
<div id="content">

	<div id="contentContainer">

		<?php echo isset($templateMessage)?templateMessage($templateMessage):''; ?>
	
		<div class="outerbox">

            <div id="formHeading"><h2><?php echo isset($clues['id'])?__('Edit Performance Review'):__('Add Performance Review'); ?></h2></div>
			
			<form action="#" id="frmSave" name="frmSave" class="content_inner" method="post">

			<div id="formWrapper">
              
				<label for="txtEmpName-0">Employee Name <span class="required">*</span></label>
				<input id="txtEmpName-0" name="txtEmpName-0" type="text" class="formInputText" 
				value="<?php echo isset($clues['empName'])?$clues['empName']:'Type for hints...'?>" tabindex="1" />
				<input type="hidden" name="hdnEmpId-0" id="hdnEmpId-0" 
				value="<?php echo isset($clues['empId'])?$clues['empId']:'0'?>">
				<div class="errorDiv"></div>
             	<br class="clear"/>

              	<label for="txtReviewerName-0">Reviewer Name <span class="required">*</span></label>
				<input id="txtReviewerName-0" name="txtReviewerName-0" type="text" class="formInputText" 
				value="<?php echo isset($clues['reviewerName'])?$clues['reviewerName']:'Type for hints...'?>" tabindex="2" />
				<input type="hidden" name="hdnReviewerId-0" id="hdnReviewerId-0" 
				value="<?php echo isset($clues['reviewerId'])?$clues['reviewerId']:'0'?>">
				<div class="errorDiv"></div>
             	<br class="clear"/>
              
              	<label for="txtPeriodFromDate-0">Period From Date <span class="required">*</span></label>
				<input id="txtPeriodFromDate-0" name="txtPeriodFromDate-0" type="text" class="formInputText" 
				value="<?php echo isset($clues['from'])?$clues['from']:''; ?>" tabindex="3" />
				<input id="fromButton" name="fromButton" class="calendarBtn" type="button" value="   " />
				<div class="errorDiv"></div>
             	<br class="clear"/>

              	<label for="txtPeriodToDate-0">Period To Date <span class="required">*</span></label>
				<input id="txtPeriodToDate-0" name="txtPeriodToDate-0" type="text" class="formInputText" 
				value="<?php echo isset($clues['to'])?$clues['to']:''; ?>" tabindex="4" />
				<input id="toButton" name="toButton" class="calendarBtn" type="button" value="   " />
				<div class="errorDiv"></div>
             	<br class="clear"/>

              	<label for="txtDueDate-0">Due Date <span class="required">*</span></label>
				<input id="txtDueDate-0" name="txtDueDate-0" type="text" class="formInputText" 
				value="<?php echo isset($clues['due'])?$clues['due']:''; ?>" tabindex="5" />
				<input id="dueButton" name="dueButton" class="calendarBtn" type="button" value="   " />
				<div class="errorDiv"></div>
             	<br class="clear"/>

				<input type="hidden" name="hdnId-0" id="hdnId-0"
				value="<?php echo isset($clues['id'])?$clues['id']:''?>">

			</div>

			<div id="buttonWrapper">
				
				<input type="button" class="savebutton" id="saveBtn" value="Save" tabindex="6" />
                        
				<input type="button" class="savebutton" id="resetBtn" value="Reset" tabindex="7" />
                    
			</div>  
              
            </form>
            
		</div> <!-- outerbox: Ends -->
        
	</div> <!-- contentContainer: Ends -->

</div> <!-- content: Ends -->

<script type="text/javascript">

	$(document).ready(function() {

		var empdata = <?php echo str_replace('&#039;',"'",$empJson)?>;		
		
		/* Auto completion of employees */
		$("#txtEmpName-0").autocomplete(empdata, {
			formatItem: function(item) {
		    	return item.name;
			}, matchContains:"word"
		}).result(function(event, item) {
		  	$('#hdnEmpId-0').val(item.id);
		});
		
		/* Auto completion of reviewers */
		$("#txtReviewerName-0").autocomplete(empdata, {
			formatItem: function(item) {
		    	return item.name;
			}, matchContains:"word"
		}).result(function(event, item) {
		  	$('#hdnReviewerId-0').val(item.id);
		});
		
		/* Clearing auto-fill fields */
		$("#txtEmpName-0").click(function(){ $(this).attr({ value: '' }); $("#hdnEmpId-0").attr({ value: '0' }); });
		$("#txtReviewerName-0").click(function(){ $(this).attr({ value: '' }); $("#hdnReviewerId-0").attr({ value: '0' }); });
		
		/* Date picker */
        $.datepicker.setDefaults({showOn: 'click'});

		$("#txtPeriodFromDate-0").datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
		$('#fromButton').click(function(){
			$("#txtPeriodFromDate-0").datepicker('show');
		});
		
		$("#txtPeriodToDate-0").datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
		$('#toButton').click(function(){
			$("#txtPeriodToDate-0").datepicker('show');
		});
		
		$("#txtDueDate-0").datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
		$('#dueButton').click(function(){
			$("#txtDueDate-0").datepicker('show');
		});

		// Save button
		$('#saveBtn').click(function(){
			$('#frmSave').submit();
		});

		// Clear button
		$('#resetBtn').click(function(){
			document.forms[0].reset('');
		});
		
		/* Validation */		
		$("#frmSave").validate({
			
			 rules: {
			 	'txtEmpName-0': { required: true, empIdSet: true, sameAsEmp: true },
			 	'txtReviewerName-0': { required: true, reviewerIdSet: true, sameAsEmp: true },
			 	'txtPeriodFromDate-0': { required: true, dateISO: true , validFromDate: true },
			 	'txtPeriodToDate-0': { required: true, dateISO: true ,validToDate: true },
			 	'txtDueDate-0': { required: true, dateISO: true ,validDueDate: true }
		 	 },
		 	 messages: {
		 		'txtEmpName-0':{ 
		 			required:"Employee Name is required",
                    empIdSet:"Please select an employee",
                    sameAsEmp:"Employee can not also be the reviewer"
		 		},
		 		'txtReviewerName-0':{ 
		 			required:"Reviewer Name is required",
                    reviewerIdSet:"Please select a reviewer",
                    sameAsEmp:"Employee can not also be the reviewer"
		 		},
		 		'txtPeriodFromDate-0':{ 
		 			required:"Period From Date is required",
		 			dateISO:"Period From Date should be YYYY-MM-DD format",
		 			validFromDate: " Period From Date should be lesser than Period To Date"
		 			
		 		},
		 		'txtPeriodToDate-0':{ 
			 		required:"Period To Date is required",
			 		dateISO:"Period To Date should be YYYY-MM-DD format",
			 		validToDate: " Period To Date should be higher than Period From Date"
		 		},
		 		'txtDueDate-0':{ 
			 		required:"Due Date is required",
			 		dateISO:"To Date should be YYYY-MM-DD format",
			 		validDueDate:"Due Date should be higher than Period From Date"
		 		}
		 	 },
		 	 errorPlacement: function(error, element) {
     		 	error.appendTo(element.next().next());
     		 	//error.appendTo(element.next(".errorDiv"));
   			 }
		 	 
		});

        /* Checks whether Employee and Reviewer are same */
        $.validator.addMethod("sameAsEmp", function(value, element) {
            if ($('#hdnEmpId-0').val() != 0 && $('#hdnReviewerId-0').val() != 0) {
                return ($('#hdnEmpId-0').val() != $('#hdnReviewerId-0').val());
            } else {
                return true;
            }

        });

        /* Checks whether Employee is set */
        $.validator.addMethod("empIdSet", function(value, element) {
            if ($('#hdnEmpId-0').val() == 0) {
                return false;
            } else {
                return true;
            }
        });

        /* Checks whether Reviewer is set */
        $.validator.addMethod("reviewerIdSet", function(value, element) {
            if ($('#hdnReviewerId-0').val() == 0) {
                return false;
            } else {
                return true;
            }
        });

        /* Valid From Date */
        $.validator.addMethod("validFromDate", function(value, element) {
        	
            var fromdate	=	$('#txtPeriodFromDate-0').val();
            var	fromdateObj		=	new Date(fromdate.replace(/-/g,'/'));
            var todate		=	$('#txtPeriodToDate-0').val();
            var	todateObj	=	new Date(todate.replace(/-/g,'/')); 
           
			
			if( ($('#txtPeriodToDate-0').val() != '') && (fromdateObj >= todateObj)){
				
    			return false;
			}
    		else{
    			return true;
    		}

        });

        /* Valid To Date */
        $.validator.addMethod("validToDate", function(value, element) {
        	
            var fromdate	=	$('#txtPeriodFromDate-0').val();
            var	fromdateObj		=	new Date(fromdate.replace(/-/g,'/'));
            var todate		=	$('#txtPeriodToDate-0').val();
            var	todateObj	=	new Date(todate.replace(/-/g,'/')); 
           
			
			if( ($('#txtPeriodFromDate-0').val() != '') && (fromdateObj >= todateObj)){
				
    			return false;
			}
    		else{
        			return true;
    		}

        });
        
        /* Valid Due Date */
        $.validator.addMethod("validDueDate", function(value, element) {
        	
            var fromdate	=	$('#txtPeriodFromDate-0').val();
            var	fromdateObj		=	new Date(fromdate.replace(/-/g,'/'));
            var duedate		=	$('#txtDueDate-0').val();
            var	duedateObj	=	new Date(duedate.replace(/-/g,'/')); 
           
			
			if( ($('#txtPeriodFromDate-0').val() != '') && (fromdateObj > duedateObj)){
				
    			return false;
			}
    		else{
    				//$('#txtDueDate-0').val(getFormatDate(duedateObj));
        			return true;
    		}

        });


 		       

	}); // ready():Ends
	
	/* Applying rounding box style */ 
	if (document.getElementById && document.createElement) {
 		roundBorder('outerbox');
	}


</script>