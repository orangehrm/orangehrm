<?php
/* For formatting date */
$formatData['currentFormat'] = 'yyyy-mm-dd';
$formatData['newFormat'] = 'dd/mm/yyyy';
$formatData['currentSeparater'] = '-';
$formatData['newSeparater'] = '/';
?>

<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>

<div id="content">

    <div id="contentContainer">

        <?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>

        <div class="outerbox">

            <div id="formHeading" class="mainHeading"><h2><?php echo __('Search Performance Reviews')?></h2></div>

            <form method="post" action="#" id="frmSearch" name="frmSearch" class="content_inner">

                <div id="formWrapper">

                    <label for="txtPeriodFromDate"><?php echo __('From')?> </label>
                    <input id="txtPeriodFromDate" name="txtPeriodFromDate" type="text" class="date-pick formInputText"
                           value="<?php echo isset($clues['from']) ? set_datepicker_date_format($clues['from']) : ''; ?>" tabindex="1" />
                    <input id="fromButton" name="fromButton" class="calendarBtn" type="button" value="   " />
                    <div class="errorDiv"></div>
                    <br class="clear"/>

                    <label for="txtPeriodToDate"><?php echo __('To')?> </label>
                    <input id="txtPeriodToDate" name="txtPeriodToDate" type="text" class="date-pick formInputText"
                           value="<?php echo isset($clues['to']) ? set_datepicker_date_format($clues['to']) : ''; ?>" tabindex="2" />
                    <input id="toButton" name="toButton" class="calendarBtn" type="button" value="   " />
                    <div class="errorDiv"></div>
                    <br class="clear"/>

                    <label for="txtJobTitleCode"><?php echo __('Job Title')?></label>
                    <select id="txtJobTitleCode" name="txtJobTitleCode" class="formSelect" tabindex="3">
                        <option value="0"><?php echo __('All')?></option>
                        <?php
                        foreach ($jobList as $job) {

                            if ($job->getId() == $clues['jobCode']) {
                                $selected = ' selected';
                            } else {
                                $selected = '';
                            }

                            $jobName = $job->getJobTitleName();

                            if ($job->getIsDeleted() == JobTitle::DELETED) {
                                $jobName = $jobName . ' (' . __('Deleted') . ')';
                            }

                            echo "<option value=\"" . $job->getId() . "\"" . $selected . ">" . htmlspecialchars_decode($jobName) . "</option>\n";
                        }
                        ?>
                    </select>
                    <br class="clear"/>

                    <label for="txtSubDivisionId"><?php echo __('Sub Division')?></label>
                    <select id="txtSubDivisionId" name="txtSubDivisionId" class="formSelect" tabindex="4">
                        <option value="0"><?php echo __('All')?></option>
                        <?php
                        foreach ($tree as $node) {
                            if ($node->getId() != 1) {
                                if ($node->getId() == $clues['divisionId']) {
                                    $selected = ' selected';
                                } else {
                                    $selected = '';
                                }
                                echo "<option value=\"" . $node->getId() . "\"" . $selected . ">" . str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'] . "</option>\n";
                            }
                        }
                        ?>
                    </select>
                    <br class="clear"/>

                    <?php if ($loggedAdmin || $loggedReviewer) {
 ?>
                            <label for="txtEmpName"><?php echo __('Employee')?></label>
                            <input id="txtEmpName" name="txtEmpName" type="text" class="formInputText"
                                   value="<?php echo isset($clues['empName']) ? $clues['empName'] : __('Type for hints').'...' ?>" tabindex="5" onblur="autoFill('txtEmpName', 'hdnEmpId', <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>);"/>
                            <input type="text" name="hdnEmpId" id="hdnEmpId"
                                   value="<?php echo isset($clues['empId']) ? $clues['empId'] : '0' ?>" style="visibility:hidden;">
                            <div class="errorDiv"></div>
                            <br class="clear"/>
<?php } // $loggedAdmin || $loggedReviewer:Ends   ?>

                    <?php if ($loggedAdmin) { ?>
                            <label for="txtReviewerName"><?php echo __('Reviewer')?></label>
                            <input id="txtReviewerName"  name="txtReviewerName" type="text" class="formInputText"
                                   value="<?php echo isset($clues['reviewerName']) ? $clues['reviewerName'] : __('Type for hints').'...' ?>" tabindex="6" onblur="autoFill('txtReviewerName', 'hdnReviewerId', <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>);"/>
                            <input type="text" name="hdnReviewerId" id="hdnReviewerId"
                                   value="<?php echo isset($clues['reviewerId']) ? $clues['reviewerId'] : '0' ?>" style="visibility:hidden;">
                            <div class="errorDiv"></div>
                            <br class="clear"/>
                    <?php } // $loggedAdmin:Ends   ?>

                    </div> <!-- formWrapper:Ends -->

                    <div id="buttonWrapper" class="formbuttons">
                        <input type="button" class="savebutton" id="searchButton" value="<?php echo __('Search')?>" tabindex="7" />
                        <input type="button" class="savebutton" id="clearBtn" value="<?php echo __('Clear')?>" tabindex="8" />
                    </div>

                </form>

            </div> <!-- outerbox:Ends -->

        </div> <!-- contentContainer:Ends -->
        <br class="clear"/>

        <div id="errorContainer" class="hide">

        </div>

    <?php
                        if (count($reviews) > 0) {
    ?>

                            <div class="outerbox">

                                <form method="post" action="#" id="frmList" name="frmList" class="content_inner">

            <?php echo $form['_csrf_token']; ?>

                            <div class="navigationHearder">
                <?php if ($loggedAdmin) {
                ?>
                                <input type="button" class="savebutton" id="addReview" value="<?php echo __('Add')?>" tabindex="9" />
                                <input type="submit" class="savebutton" name="editReview" id="editReview" value="<?php echo __('Edit')?>" tabindex="10" disabled />
                                <input type="button" class="clearbutton" id="deleteReview" value="<?php echo __('Delete')?>" tabindex="11" disabled />
                <?php } ?>

                <?php if ($pager->haveToPaginate()) {
                ?>
                                <div class="pagingbar">
                    <?php include_partial('global/paging_links', array('pager' => $pager, 'url' => '@performance_reviews')); ?>
                            </div>
                <?php } ?>

                        </div>

                        <div id="tableWrapper">

                            <table cellpadding="0" cellspacing="0" class="data-table" align="center">

                                <thead>
                                    <tr>
                                        <td width="50" class="tdcheckbox">
                                            <input type="checkbox" name="allCheck" value="" id="allCheck" <?php echo ($loggedAdmin) ? '' : 'disabled'; ?> />
                                        </td>

                                        <td scope="col">
                                                                                                							<?php echo __('Employee')?>
                                        </td>

                                        <td scope="col">
                                                                                                							<?php echo __('Job Title')?>
                                        </td>

                                        <td scope="col">
                                                                                                							<?php echo __('Review Period')?>
                                        </td>

                                        <td scope="col">
                                                                                                							<?php echo __('Due Date')?>
                                        </td>

                                        <td scope="col">
                                                                                                							<?php echo __('Status')?>
                                        </td>

                                        <td scope="col">
                                                                                                							<?php echo __('Reviewer')?>
                                        </td>

                                    </tr>
                                </thead>

                                <tbody>

                        <?php
                            $i = 0;
                            foreach ($reviews as $review) {
                                if($review->getEmployee()) {
                                $rowClass = ($i % 2) ? 'even' : 'odd';
                                $empName = $review->getEmployee()->getFirstName() . ' ' . $review->getEmployee()->getLastName();
                        ?>

                                <tr class="<?php echo $rowClass; ?>">

                                    <td class="tdcheckbox">
                                        <input type="checkbox" class="innercheckbox" name="chkReview[]"
                                               id="chkReview-<?php echo $i; ?>" value="<?php echo $review->getId(); ?>"
                                       <?php echo (($review->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) && $loggedAdmin && trim($empName) != "") ? '' : 'disabled'; ?> />
                            </td>

                            <td class="">
                                <?php
                                       $link = false;
                                       if ($loggedEmpId == $review->getEmployeeId()) {
                                           if ($review->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED) {
                                               $link = true;
                                           } elseif ($loggedEmpId == $review->getReviewerId() && $review->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) {
                                               $link = true;
                                           } else {
                                               $link = false;
                                           }
                                       } elseif ($loggedReviewer && $review->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) {
                                           $link = true;
                                       } elseif ($loggedAdmin) {
                                           $link = true;
                                       }
                                ?>

                                <?php if ($link) {
                                ?>
                                           <a href="<?php echo url_for('performance/performanceReview?id=' . $review->getId()) ?>"><?php echo $empName; ?></a>
                                <?php
                                       } else {
                                           echo $empName;
                                       }
                                       if (trim($empName) == "") {
                                           echo "<font color='red'>".__('Not Available')."</font>";
                                       }
                                ?>
                                   </td>

                                   <td class="">
                                <?php echo htmlspecialchars_decode($review->getJobTitle()->getJobTitleName()); ?>
                                   </td>

                                   <td class="">
                                <?php echo set_datepicker_date_format($review->getperiodFrom()) . ' - ' . set_datepicker_date_format($review->getperiodTo()); ?>
                                   </td>

                                   <td class="">
                                <?php echo set_datepicker_date_format($review->getDueDate()); ?>
                                   </td>

                                   <td class="">
                                <?php echo __($review->getTextStatus()); ?>
                                   </td>

                                   <td class="">
                                <?php
                                       $reviewer = $review->getReviewer()->getFirstName() . ' ' . $review->getReviewer()->getLastName();
                                       if (trim($reviewer) == "") {
                                           $reviewer = "<font color='red'>".__('Not Available')."</font>";
                                       }
                                       echo $reviewer;
                                ?>
                                   </td>

                               </tr>

                        <?php
                                       $i++;
                                } // End of if condition
                             } // End of foreach
                        ?>

                               </tbody>

                           </table>

                       </div> <!-- tableWrapper:Ends -->

                       <!-- Preserving search clues -->

                       <input name="txtPeriodFromDate" type="hidden" value="<?php echo isset($clues['from']) ? $clues['from'] : ''; ?>" />
                       <input name="txtPeriodToDate" type="hidden" value="<?php echo isset($clues['to']) ? $clues['to'] : ''; ?>" />
                       <input name="txtJobTitleCode" type="hidden" value="<?php echo isset($clues['jobCode']) ? $clues['jobCode'] : ''; ?>" />
                       <input name="txtSubDivisionId" type="hidden" value="<?php echo isset($clues['divisionId']) ? $clues['divisionId'] : ''; ?>" />
                       <input name="txtEmpName" type="hidden" value="<?php echo isset($clues['empName']) ? $clues['empName'] : '' ?>" />
                       <input name="hdnEmpId" type="hidden" value="<?php echo isset($clues['empId']) ? $clues['empId'] : '' ?>">
                       <input name="txtReviewerName" type="hidden" value="<?php echo isset($clues['reviewerName']) ? $clues['reviewerName'] : '' ?>" />
                       <input name="hdnReviewerId" type="hidden" value="<?php echo isset($clues['reviewerId']) ? $clues['reviewerId'] : '' ?>">
                       <input name="hdnPageNo" type="hidden" value="<?php echo isset($clues['pageNo']) ? $clues['pageNo'] : '' ?>">

                   </form> <!-- #frmList:Ends -->

               </div> <!-- outerbox:Ends -->

    <?php
                               } // End of checking $reviews count
    ?>

                           </div> <!-- content: Ends -->

                           <style type="text/css">
                               form#frmSearch.content_inner div#formWrapper .formSelect{
                                   width: 210px;
                               }
                               form#frmSearch.content_inner div#formWrapper .formInputText{
                                   width: 200px;
                               }
                           </style>

                           <script type="text/javascript">

                               var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
                               var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
                               var lang_dateError = '<?php echo __("To date should be after from date") ?>';
                               var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';

                               function autoFill(selector, filler, data) {
                                   jQuery.each(data, function(index, item){
                                       if(item.name == $("#" + selector).val()) {
                                           $("#" + filler).val(item.id);
                                           return true;
                                       }
                                   });
                               }

                               $(document).ready(function() {
                                   if($('#txtPeriodFromDate').val() == ""){
                                       $('#txtPeriodFromDate').val(displayDateFormat);
                                   }
                                   if($('#txtPeriodToDate').val() == ""){
                                       $('#txtPeriodToDate').val(displayDateFormat);}

<?php if ($loggedAdmin || $loggedReviewer) { ?>

                               var empdata = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>;

                               /* Auto completion of employees */
                               $("#txtEmpName").autocomplete(empdata, {
                               formatItem: function(item) {
                               return item.name;
                               }, matchContains:"word"
                               }).result(function(event, item) {
                               $('#hdnEmpId').val(item.id);
                               });

                               /* Auto completion of reviewers */
                               $("#txtReviewerName").autocomplete(empdata, {
                               formatItem: function(item) {
                               return item.name;
                               }, matchContains:"word"
                               }).result(function(event, item) {
                               $('#hdnReviewerId').val(item.id);
                               });

<?php } // $loggedAdmin || $loggedReviewer:Ends         ?>

                           /* Clearing auto-fill fields */
                           $("#txtEmpName").click(function(){ $(this).attr({ value: '' }); $("#hdnEmpId").attr({ value: '0' }); });
                           $("#txtReviewerName").click(function(){ $(this).attr({ value: '' }); $("#hdnReviewerId").attr({ value: '0' }); });

                           /* Date picker */
                           $.datepicker.setDefaults({showOn: 'click'});

                           $("#txtPeriodFromDate").datepicker({ dateFormat: datepickerDateFormat, changeMonth: true, changeYear: true});
                           $("#txtPeriodFromDate").click(function(){
                           $("#txtPeriodFromDate").datepicker('show');
                           });
                           $('#fromButton').click(function(){
                           $("#txtPeriodFromDate").datepicker('show');
                           });

                           $("#txtPeriodToDate").datepicker({ dateFormat: datepickerDateFormat, changeMonth: true, changeYear: true});
                           $("#txtPeriodToDate").click(function(){
                           $("#txtPeriodToDate").datepicker('show');
                           });
                           $('#toButton').click(function(){
                           $("#txtPeriodToDate").datepicker('show');
                           });

                           /* Search button */
                           $('#searchButton').click(function(){
                           var autoFields = "txtEmpName";
                           var autoHidden = "hdnEmpId";

<?php if ($loggedAdmin || $loggedReviewer) { ?>
                               if ($('#txtEmpName').val() == '<?php echo __('Type for hints').'...'?>') {
                               $('#txtEmpName').val('');
                               }
<?php } // $loggedAdmin || $loggedReviewer:Ends         ?>

<?php if ($loggedAdmin) { ?>
                               autoFields = autoFields + "|txtReviewerName";
                               autoHidden = autoHidden + "|hdnReviewerId";
                               if ($('#txtReviewerName').val() == '<?php echo __('Type for hints').'...'?>') {
                               $('#txtReviewerName').val('');
                               }
<?php } // $loggedAdmin:Ends         ?>

<?php if ($loggedAdmin || $loggedReviewer) { ?>
                               fillAutoFields(autoFields.split("|"), autoHidden.split("|"));
<?php } ?>
                           $('#frmSearch').submit();

                           });

                           function fillAutoFields(autoFields, autoHidden) {
                           //this is to make case insensitive
                           for(x=0; x < autoFields.length; x++) {
                           $("#" + autoHidden[x]).val(0);
                           for(i=0; i < empdata.length; i++) {
                           var data = empdata[i];
                           var fieldValue = $("#" + autoFields[x]).val();
                           fieldValue = fieldValue.toLowerCase();
                           if((data.name).toLowerCase() == fieldValue) {
                           $("#" + autoHidden[x]).val(data.id);
                           break;
                           }
                           }
                           }
                           }
                           // Clear button
                           $('#clearBtn').click(function(){
                           $('#txtPeriodFromDate').val(displayDateFormat);
                           $('#txtPeriodToDate').val(displayDateFormat);
                           $('#txtJobTitleCode').val('0');
                           $('#txtSubDivisionId').val('0');
<?php if ($loggedAdmin || $loggedReviewer) { ?>
                               $('#txtEmpName').val('');
                               $('#hdnEmpId').val('0');
<?php } // $loggedAdmin || $loggedReviewer:Ends         ?>
<?php if ($loggedAdmin) { ?>
                               $('#txtReviewerName').val('');
                               $('#hdnReviewerId').val('0');
<?php } // $loggedAdmin:Ends         ?>

                           });

                           /* Add button */
                           $('#addReview').click(function(){
                           window.location.href = '<?php echo url_for('performance/saveReview'); ?>';
                           });

                           /* Edit button */
                           $('#editReview').click(function(){
                           $('#frmList').attr('action', '<?php echo url_for('performance/saveReview'); ?>');
                           $('#frmList').submit();
                           });

                           /* Delete button */
                           $('#deleteReview').click(function(){
                           $('#frmList').attr('action', '<?php echo url_for('performance/deleteReview'); ?>');
$('#frmList').submit();
});

/* Checkbox behavior */
$("#allCheck").click(function() {
if ($('#allCheck').attr('checked')) {
$('.innercheckbox').attr('checked', true);
$('#deleteReview').attr('disabled', false);
} else {
$('.innercheckbox').attr('checked', false);
}
});

$(".innercheckbox").click(function() {
if(!($(this).attr('checked'))) {
$('#allCheck').attr('checked', false);
}
$('#editReview').attr('disabled', false);
$('#deleteReview').attr('disabled', false);
});

//Validate search form 
var validator = $("#frmSearch").validate({
rules: {
'txtPeriodFromDate': {
valid_date: function() {
return {
 format:datepickerDateFormat,
 required:false,
 displayFormat:displayDateFormat
}
}
},
'txtPeriodToDate': {
valid_date: function() {
return {
 format:datepickerDateFormat,
 required:false,
 displayFormat:displayDateFormat
}
},
date_range: function() {
return {
 format:datepickerDateFormat,
 displayFormat:displayDateFormat,
 fromDate:$('#txtPeriodFromDate').val()
}
}
}
},
messages: {
'txtPeriodFromDate' : {
valid_date: lang_invalidDate
},
'txtPeriodToDate' : {
valid_date: lang_invalidDate ,
date_range: lang_dateError
}
},
errorPlacement: function(error, element) {
error.appendTo(element.next().next());
}
});
		
}); // ready():Ends

/* Applying rounding box style */ 
if (document.getElementById && document.createElement) {
roundBorder('outerbox');
}

</script>

