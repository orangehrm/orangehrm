<script type="text/javascript"
	src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>

<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>

<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/defineLeavePeriodSuccess'); ?>

<style type="text/css">
</style>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>

<div class="formpage">
<div class="navigation"></div>
<div id="status"></div>
<?php echo message()?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;">
	<span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div class="outerbox">
<div class="mainHeading">
    <h2 class="paddingLeft"><?php echo __("Leave Period")?></h2>
</div>

<form id="frmLeavePeriod" name="frmLeavePeriod" action="" method="post">
    <?php echo $form['_csrf_token']; ?>
    <table border="0" cellpadding="0" cellspacing="0" class="tableArrange">
        <tr class="datesForNonLeapYears">
            <td colspan="2"><strong><?php echo __("For Leap Years"); ?></strong></td>
        </tr>
        <tr class="tableArrangetr">
            <td align="left" class="labelTd"><?php echo __("Start Date"); ?> <span class=required>*</span></td>
            <td><span id="comboHolder">
                <?php echo $form['cmbStartMonth']->render(); ?>
                <?php echo $form['cmbStartDate']->render(); ?>
                </span>
            </td>
        </tr>
        <tr><td></td><td id="inlineErrorHolder" class="errorHolder"></td></tr>
        <tr id="endDateDiv" class="tableArrangetr">
            <td align="left"><?php echo __("End Date"); ?></td>
            <td><span id="lblEndDate" class="valueLabel"><?php echo $endDate; ?></span>&nbsp;<span id="lblEndDateFollowingYear" class="valueLabel"></span></td>
        </tr>
        <tr class="datesForNonLeapYears">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr class="datesForNonLeapYears">
            <td align="left" colspan="2"><strong><?php echo __("For Non-Leap Years"); ?></strong></td>
        </tr>
        <tr class="datesForNonLeapYears tableArrangetr">
            <td align="left"><?php echo __("Start Date"); ?> <span class=required>*</span></td>
            <td>
                <span id="comboHolder">
                <?php echo $form['cmbStartMonthForNonLeapYears']->render(); ?>
                <?php echo $form['cmbStartDateForNonLeapYears']->render(); ?>
                </span>
            </td>
        </tr>
        <tr class="datesForNonLeapYears tableArrangetr" >
            <td align="left"><?php echo __("End Date"); ?></td>
            <td><span id="lblEndDateForNonLeapYears" class="valueLabel">-</span>&nbsp;<span id="lblNonLeapYearFollowingYear" class="valueLabel"></span></td>
        </tr>
        <?php if ($isLeavePeriodDefined) { ?>
        <tr class="tableArrangetr">
            <td align="left"><?php echo __("Current Leave Period"); ?></td>
            <td class="valueLabel"><?php echo $currentLeavePeriod->getDescription();?></td>
        </tr>
        <?php } ?>
    </table>

<div class="formbuttons paddingLeftBtn"><input type="button" class="savebutton" id="btnEdit"
                    value="<?php echo ($isLeavePeriodDefined) ? __("Edit") : __("Save"); ?>" tabindex="2" />
                    <input type="button" class="clearbutton" id="btnReset"
                    value="<?php echo __("Reset")?>" tabindex="3" <?php if($isLeavePeriodDefined) {?>  style="display: none;"<?php }?> /></div>

</form>

</div>
</div>

<div class="requirednotice"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>

<script type="text/javascript">
	var isLeavePeriodDefined = <?php echo ($isLeavePeriodDefined) ? 'true' : 'false' ?>;
	var isEditMode = <?php echo ($isLeavePeriodDefined) ? 'false' : 'true' ?>;
	var initValues = null;

	$(document).ready(function() {

		$('#leaveperiod_cmbStartMonth').change(function(){

            $("#endDateDiv").show();

            if($(this).val() == 0) {
                $("#endDateDiv").hide();
            }
            
            if($(this).val() != 0) {
                eraseErrorMessages();
            }

			url = '<?php echo url_for('coreLeave/loadDatesforMonth'); ?>/month/' + $(this).val();
			$.getJSON(url, function(dates) {
		    	populateDateSelector($('#leaveperiod_cmbStartDate'), dates);
	            loadEndDate_Regular();
	            checkCurrentStartDate();
			});
		});
		
		url = '<?php echo url_for('coreLeave/loadDatesforMonth'); ?>/month/2/isLeapYear/false';
		$.getJSON(url, function(dates) {
	    	populateDateSelector($('#leaveperiod_cmbStartDateForNonLeapYears'), dates);
            loadEndDate_NonLeapYears();
		});

        $("#leaveperiod_cmbStartMonthForNonLeapYears").change(function() {
            url = '<?php echo url_for('coreLeave/loadDatesforMonth'); ?>/month/' + $("#leaveperiod_cmbStartMonthForNonLeapYears").val() + '/isLeapYear/false';
            $.getJSON(url, function(dates) {
                populateDateSelector($('#leaveperiod_cmbStartDateForNonLeapYears'), dates);
                loadEndDate_NonLeapYears();
            });
            loadEndDate_NonLeapYears();
        });
        
		$('#leaveperiod_cmbStartDate').change(function() {
			loadEndDate_Regular();
			checkCurrentStartDate();
		});

		$('#leaveperiod_cmbStartDateForNonLeapYears').change(loadEndDate_NonLeapYears);

		$('.datesForNonLeapYears').hide();

		$('#btnReset').click(function() {
		    $('#leaveperiod_cmbStartMonth').val(initValues.startMonth);
		    $('#leaveperiod_cmbStartDate').html(initValues.startDateHTML);
		    $('#leaveperiod_cmbStartDate').val(initValues.startDate);
		    $('#lblEndDate').html(initValues.endDate);
            var followingYearText = "";
            if(initValues.startMonth > 1 || initValues.startDate > 1) {
                followingYearText = "(Following Year)";
            }
            $('#lblEndDateFollowingYear').html(followingYearText);
		    $('.datesForNonLeapYears').hide();
		    //$('#messagebar').html('');
		    $('#messagebar').hide();
		});

		$('#btnEdit').click(function() {
			if (isEditMode) {
				if (validateForm()) {
					$('#frmLeavePeriod').submit();
                    $("#btnReset").hide();
				}
			} else {
				isEditMode = true;
                $("#btnReset").show();
				$('#frmLeavePeriod select').attr('disabled', false);
				$(this).val('<?php echo __("Save"); ?>');
			}
		});

		if (isLeavePeriodDefined) {
			$('#frmLeavePeriod select').attr('disabled', true);
		}

		$('#leaveperiod_cmbStartMonth').val(<?php echo ($isLeavePeriodDefined) ? $startMonthValue : 0 ?>);
		$('#leaveperiod_cmbStartDate').val(<?php echo ($isLeavePeriodDefined) ? $startDateValue : 0 ?>);

		initStartMonth = $('#leaveperiod_cmbStartMonth').val();
		initStartDateHTML = $('#leaveperiod_cmbStartDate').html();
		initStartDate = $('#leaveperiod_cmbStartDate').val();
		initEndDate = $('#lblEndDate').html();

		initValues = {
			startMonth : initStartMonth,
			startDateHTML : initStartDateHTML,
			startDate: initStartDate,
			endDate: initEndDate
		 };

		daymarker.bindElement("#leaveperiod_calCurrentPeriodStartDate", {
            onSelect: function(date) {
            	// TODO: Make this part format independant
            	dateValues = date.split('-');
            	var selectedDate = new Date(dateValues[0], dateValues[1] - 1, dateValues[2]);
            	

            	url = '<?php echo url_for('coreLeave/loadLeavePeriodEndDate'); ?>/month/' + $('#leaveperiod_cmbStartMonth').val() + '/date/' + $('#leaveperiod_cmbStartDate').val() + '/format/Y-m-d';
        	    $.ajax({
        	        type: 'get',
        	        url: url,
        	        success: function(endDateString) {

        	    		dateValues = endDateString.split('-');
	        	    	maximumAllowedDate = new Date(dateValues[0], dateValues[1] - 1, dateValues[2]);
	        	    	maximumAllowedDate.setTime(maximumAllowedDate.getTime() - 86400000); // Deducting one day

	        	    	if (selectedDate.getTime() > maximumAllowedDate.getTime()) {
	    					maximumAllowedDateString = maximumAllowedDate.getFullYear() + '-' + (maximumAllowedDate.getMonth() + 1) + '-' + maximumAllowedDate.getDate();
	    					$('#leaveperiod_calCurrentPeriodStartDate').val(maximumAllowedDateString);
	                    	
	    					$('#messagebar').addClass('messageBalloon_warning'); // Extract error display to a new function
	    					errorMessage = '<?php echo __("Selected date is greater than the leave period end date. Maximum allowed start date is selected") ?>';
	    					$('#messagebar').css('height', 30);
	    					$('#messagebar').html(errorMessage);
	    					$('#messagebar').show();
	    				} else {
	    					$('#messagebar').html('');
	    					$('#messagebar').hide();
	    				}

        	        }
        	    });
        	    
            	
            },
            dateFormat : 'yy-mm-dd'
        });

		$('#calCurrentPeriodStartDate_Button').click(function(){
            daymarker.show("#leaveperiod_calCurrentPeriodStartDate");
        });

		$('#divChooseCurrentStartDate').hide();
        $("#endDateDiv").show();
        if($("#leaveperiod_cmbStartMonth").val() == 0) {
            $("#endDateDiv").hide();
        }
	});
	
	function loadEndDate_Regular() {
		if ($('#leaveperiod_cmbStartMonth').val() == '2' && $('#leaveperiod_cmbStartDate').val() == '29') {
			$('.datesForNonLeapYears').show();
		} else {
		    $('.datesForNonLeapYears').hide();
		}
		
		loadEndDate($('#leaveperiod_cmbStartMonth').val(), $('#leaveperiod_cmbStartDate').val(), $('#lblEndDate'));
	}
	
	function loadEndDate_NonLeapYears() {
        var startMonth = 2;
        if($("#leaveperiod_cmbStartMonthForNonLeapYears").val() > 0) {
            startMonth = $("#leaveperiod_cmbStartMonthForNonLeapYears").val();
        }
		loadEndDate(startMonth, $('#leaveperiod_cmbStartDateForNonLeapYears').val(), $('#lblEndDateForNonLeapYears'));
	}
	
	function loadEndDate(startMonth, startDate, displayLabel) {
	    url = '<?php echo url_for('coreLeave/loadLeavePeriodEndDate'); ?>/month/' + startMonth + '/date/' + startDate;
	    $.ajax({
	        type: 'get',
	        url: url,
	        success: function(html) {
                //this is for end date on leap year
                var followingYearText = "";
                if($("#leaveperiod_cmbStartMonth").val() > 1 || $('#leaveperiod_cmbStartDate').val() > 1) {
                    followingYearText = "(Following Year)";
                }
                $("#lblEndDateFollowingYear").html(followingYearText);

                //this is for end date on non leap year
                followingYearText = "";
                if($("#leaveperiod_cmbStartMonthForNonLeapYears").val() > 1 || $('#leaveperiod_cmbStartDateForNonLeapYears').val() > 1) {
                    followingYearText = "(Following Year)";
                }
                $("#lblNonLeapYearFollowingYear").html(followingYearText);
	            displayLabel.html(html);
	        }
	    });	    	    	    
	}
	
	function populateDateSelector(dateSelector, dates) {
	    var html = '';
        $.each (dates, function (index, date) {
            var option = '<option value="' + date + '">' + date + '</option>';
            if(dateSelector.attr("id") == "leaveperiod_cmbStartDateForNonLeapYears" && date == 28) {
                option = '<option value="' + date + '" selected="selected">' + date + '</option>';
            }
            html += option;
        });
        dateSelector.html(html);
	}

	function checkCurrentStartDate() {
		if (!isLeavePeriodDefined) {
			startMonth = $('#leaveperiod_cmbStartMonth').val();
			startDate = $('#leaveperiod_cmbStartDate').val();
		    url = '<?php echo url_for('coreLeave/getCurrentStartDate'); ?>/month/' + startMonth + '/date/' + startDate;
		    $.ajax({
		        type: 'get',
		        url: url,
		        success: function(html) {
		            $('#spanDateHolder').html(html);
		            $('#leaveperiod_calCurrentPeriodStartDate').val(html);
		            $('#divChooseCurrentStartDate').show();
		        }
		    });  
		}
	}

    function validateForm() {

        eraseErrorMessages();

		if ($('#leaveperiod_cmbStartMonth').val() == '0') {
			placeError('<?php echo __('Start month is required'); ?>');
            return false;
		}

		if ($('#leaveperiod_cmbStartDate').val() == '0') {
			placeError('<?php echo __('Start date is required'); ?>');
            return false;
		}

        return true;

    }

    function placeError(message) {
        $('#inlineErrorHolder').append(message);
    }

    function eraseErrorMessages() {
        $('#inlineErrorHolder').empty();
    }


</script>

