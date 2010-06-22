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
                   value="<?php echo isset($clues['empName'])?$clues['empName']:'Type for hints...'?>" tabindex="1" <?php if(isset($clues['id'])) {?>style="display:none;"<?php }?> />
            <?php if(isset($clues['id'])) {?>
            <label style="width:auto;"><?php echo $clues['empName'];?></label>
            <?php }?>
				<input type="text" name="hdnEmpId-0" id="hdnEmpId-0"
                   value="<?php echo isset($clues['empId'])?$clues['empId']:'0'?>" style="display:none; "/>
				<div class="errorDiv"></div>
             	<br class="clear"/>

            <label for="txtReviewerName-0">Reviewer Name <span class="required">*</span></label>
				<input id="txtReviewerName-0" name="txtReviewerName-0" type="text" class="formInputText"
				value="<?php echo isset($clues['reviewerName'])?$clues['reviewerName']:'Type for hints...'?>" tabindex="2" />
				<input type="text" name="hdnReviewerId-0" id="hdnReviewerId-0"
				value="<?php echo isset($clues['reviewerId'])?$clues['reviewerId']:'0'?>" style="display:none;" />
				<div class="errorDiv"></div>
             	<br class="clear"/>

              	<label for="txtPeriodFromDate-0">From <span class="required">*</span></label>
				<input id="txtPeriodFromDate-0" name="txtPeriodFromDate-0" type="text" class="formInputText"
				value="<?php echo isset($clues['from'])?$clues['from']:''; ?>" tabindex="3" />
				<input id="fromButton" name="fromButton" class="calendarBtn" type="button" value="   " />
				<div class="errorDiv"></div>
             	<br class="clear"/>

              	<label for="txtPeriodToDate-0">To <span class="required">*</span></label>
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

				<input type="button" class="savebutton" id="resetBtn" value="<?php if(isset($clues['id'])){ echo 'Reset';}else{echo 'Clear';}?>" tabindex="7" />

			</div>

            </form>

		</div> <!-- outerbox: Ends -->

	</div> <!-- contentContainer: Ends -->

</div> <!-- content: Ends -->

<script type="text/javascript">
   function autoFill(selector, filler, data) {
      jQuery.each(data, function(index, item){
         if(item.name == $("#" + selector).val()) {
            $("#" + filler).val(item.id);
            return true;
         }
      });
   }

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

      $("#txtEmpName-0").change(function(){
         autoFill('txtEmpName-0', 'hdnEmpId-0', <?php echo str_replace('&#039;',"'",$empJson)?>);
      });

      $("#txtReviewerName-0").change(function(){
         autoFill('txtReviewerName-0', 'hdnReviewerId-0', <?php echo str_replace('&#039;',"'",$empJson)?>);
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
         var autoFields = new Array("txtEmpName-0", "txtReviewerName-0");
         var autoHidden = new Array("hdnEmpId-0", "hdnReviewerId-0");

         for(x=0; x < autoFields.length; x++) {
            $("#" + autoHidden[x]).val(0);
            for(i=0; i < empdata.length; i++) {
               var data = empdata[i];
               if($("#" + autoFields[x]).val() == data.name) {
                  $("#" + autoHidden[x]).val(data.id);
                  break;
               }
            }
         }

			$('#frmSave').submit();
		});

		// Clear button
		$('#resetBtn').click(function(){
         $("label.error").each(function(i){
            $(this).remove();
         });
			document.forms[0].reset('');
         autoFill('txtEmpName-0', 'hdnEmpId-0', empdata);
         autoFill('txtReviewerName-0', 'hdnReviewerId-0', empdata);
		});

		/* Validation */
		$("#frmSave").validate({

			 rules: {
			 	'txtEmpName-0': { required: true, empIdSet: true },
			 	'txtReviewerName-0': { required: true, reviewerIdSet: true },
			 	'txtPeriodFromDate-0': { required: true, dateISO: true , validFromDate: true },
			 	'txtPeriodToDate-0': { required: true, dateISO: true ,validToDate: true },
			 	'txtDueDate-0': { required: true, dateISO: true ,validDueDate: true }
		 	 },
		 	 messages: {
		 		'txtEmpName-0':{
		 			required:"Employee Name is required",
                    empIdSet:"Please select an employee"
		 		},
		 		'txtReviewerName-0':{
		 			required:"Reviewer Name is required",
                    reviewerIdSet:"Please select a reviewer"
		 		},
		 		'txtPeriodFromDate-0':{
		 			required:"From field is required",
		 			dateISO:"Invalid date. From field should be filled in YYYY-MM-DD format with correct values",
		 			validFromDate: " From field should be lesser than To field or Invalid date"

		 		},
		 		'txtPeriodToDate-0':{
			 		required:"To field is required",
			 		dateISO:"Invalid date. To field should be filled in YYYY-MM-DD format with correct values",
			 		validToDate: " To field should be higher than From field or Invalid date"
		 		},
		 		'txtDueDate-0':{
			 		required:"Due Date is required",
			 		dateISO:"Invalid date. Due Date field should be filled in YYYY-MM-DD format with correct values",
			 		validDueDate:"Due Date field should be higher than From field or Invalid date"
		 		}
		 	 },
		 	 errorPlacement: function(error, element) {
     		 	error.appendTo(element.next().next());
     		 	//error.appendTo(element.next(".errorDiv"));
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
            fromdate = (fromdate).split("-");
            if(!validateDate(parseInt(fromdate[2], 10), parseInt(fromdate[1], 10), parseInt(fromdate[0], 10))) {
               return false;
            }

            var todate		=	$('#txtPeriodToDate-0').val();
            todate = (todate).split("-");
            var todateObj	=	new Date(parseInt(todate[0], 10), parseInt(todate[1], 10) - 1, parseInt(todate[2], 10));

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
            todate = (todate).split("-");
            if(!validateDate(parseInt(todate[2], 10), parseInt(todate[1], 10), parseInt(todate[0], 10))) {
               return false;
            }
            var todateObj	=	new Date(parseInt(todate[0], 10), parseInt(todate[1], 10) - 1, parseInt(todate[2], 10));

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
            var fromdateObj   = new Date(fromdate.replace(/-/g,'/'));
            var duedate		=	$('#txtDueDate-0').val();
            duedate = (duedate).split("-");
            if(!validateDate(parseInt(duedate[2], 10), parseInt(duedate[1], 10), parseInt(duedate[0], 10))) {
               return false;
            }
            var duedateObj	= new Date(parseInt(duedate[0], 10), parseInt(duedate[1], 10) - 1, parseInt(duedate[2], 10));


			if( ($('#txtPeriodFromDate-0').val() != '') && (fromdateObj >= duedateObj)){
    			return false;
			}
    		else{
        		return true;
    		}

        });

	}); // ready():Ends

	/* Applying rounding box style */
	if (document.getElementById && document.createElement) {
 		roundBorder('outerbox');
	}

   function validateDate(day, month, year) {
      var days31 = new Array(1,3,5,7,8,10,12);

      if(month > 12 || month < 1) {
         return false;
      }

      if(day == 29 && month == 2) {
         if(year % 4 == 0) {
            return true;
         }
      }

      if(month == 2 && day < 29) {
         return true;
      }
      if(day < 32 && month != 2) {
         if(day == 31) {
            flag = false;
            for(i=0; i < days31.length; i++) {
               if(days31[i] == month) {
                  flag = true;
                  break;
               }
            }
            return flag;
         }
         return true;
      }
      return false;
   }
  
</script>