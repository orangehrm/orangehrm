<?php
stylesheet_tag(theme_path('css/orangehrm.datepicker.css'));
use_javascript('orangehrm.datepicker.js');
?>

<div class="box searchForm toggableForm">     

    <div class="head">
        <h1><?php echo __('Search Performance Reviews')?></h1>
    </div>
  
    <div class="inner">
            <form action="#" id="frmSearch" name="frmSearch" method="post">
                <input type="hidden" name="mode" value="search" >
                <?php echo $form['_csrf_token']; ?>
                <fieldset>
                    <ol>                        
                        <li>
                            <label for="txtPeriodFromDate"><?php echo __('From') ?> </label>
                            <?php
                            $fromDate = new ohrmWidgetDatePicker(array(), array('id' => 'txtPeriodFromDate'));
                            echo $fromDate->render('txtPeriodFromDate', set_datepicker_date_format($clues['from']));
                            ?>
                        </li>

                        <li>
                            <label for="txtPeriodToDate"><?php echo __('To') ?> </label>
                            <?php
                            $toDate = new ohrmWidgetDatePicker(array(), array('id' => 'txtPeriodToDate'));
                            echo $toDate->render('txtPeriodToDate', set_datepicker_date_format($clues['to']));
                            ?>
                        </li>

                        <li>
                            <label for="txtJobTitleCode"><?php echo __('Job Title') ?></label>
                            <select id="txtJobTitleCode" name="txtJobTitleCode" class="formSelect" tabindex="3">
                                <option value="0"><?php echo __('All') ?></option>
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
                                    echo "<option value=\"" . $job->getId() . "\"" . $selected . ">" . $jobName . "</option>\n";
                                }
                                ?>
                            </select>
                        </li>
  
                        <li>
                            <label for="txtSubDivisionId"><?php echo __('Sub Division') ?></label>
                            <select id="txtSubDivisionId" name="txtSubDivisionId" class="formSelect" tabindex="4">
                                <option value="0"><?php echo __('All') ?></option>
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
                        </li>
                        
                        <?php if ($loggedAdmin || $loggedReviewer) { ?>
                        <li>
                            <label for="txtEmpName"><?php echo __('Employee') ?></label>
                            <input id="txtEmpName" name="txtEmpName" type="text" 
                                   value="<?php echo isset($clues['empName']) ? $clues['empName'] : __('Type for hints') . '...' ?>"
                                   tabindex="5" onblur="autoFill('txtEmpName', 'hdnEmpId');"/>
                            <input type="hidden" name="hdnEmpId" id="hdnEmpId" 
                                   value="<?php echo isset($clues['empId']) ? $clues['empId'] : '0' ?>">
                        </li>
                        <?php } // $loggedAdmin || $loggedReviewer:Ends    ?>
                            
                        <?php if ($loggedAdmin) { ?>
                        <li>
                            <label for="txtReviewerName"><?php echo __('Reviewer') ?></label>
                            <input id="txtReviewerName"  name="txtReviewerName" type="text" class="formInputText" 
                                   value="<?php echo isset($clues['reviewerName']) ? $clues['reviewerName'] : 
                                       __('Type for hints') . '...' ?>" tabindex="6" 
                                       onblur="autoFill('txtReviewerName', 'hdnReviewerId');"/>
                            <input type="hidden" name="hdnReviewerId" id="hdnReviewerId" 
                                   value="<?php echo isset($clues['reviewerId']) ? $clues['reviewerId'] : '0' ?>">
                        </li>
                        <?php } // $loggedAdmin:Ends    ?>
                        
                    </ol>
                    <p>
                        <input type="button" class="" id="searchButton" value="<?php echo __("Search") ?>" tabindex="7"/>
                        <input type="button" class="reset" id="clearBtn" value="<?php echo __('Clear') ?>" tabindex="8"/>
                    </p> 
                </fieldset>
            </form>	
    </div> <!-- Inner:Ends -->
    
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>

</div> <!-- box:Ends -->

<div class="box noHeader" id="search-results">


    <div class="inner">
        <form method="post" action="#" id="frmList" name="frmList">
            
            <?php echo $form['_csrf_token']; ?>
                
            <div id="tableWrapper">
                <div class="top">
                    <?php
                    if ($pager->haveToPaginate()) {
                        include_partial('global/paging_links', array('pager' => $pager, 'url' => url_for('performance/viewReview'), 'location' => 'top'));
                    }
                    ?>
                    <?php if ($loggedAdmin) {
                        ?>                       
                        <input type="button" class="" id="addReview" value="<?php echo __('Add') ?>" tabindex="9"  />
                        <input type="button" class=""  name="editReview" id="editReview" 
                               value="<?php echo __('Edit') ?>" tabindex="10" disabled />
                        <input type="button" class="delete" id="deleteReview" value="<?php echo __('Delete') ?>" 
                               tabindex="11" disabled />
                    <?php } ?>     
                </div>
                
                <?php include_partial('global/flash_messages'); ?>
                
                <table class="table hover">
                    <thead>
                        <tr>
                            <th style="width:2%" class="tdcheckbox">
                                <input type="checkbox"  name="allCheck" value="" id="allCheck" 
                                    <?php echo ($loggedAdmin) ? '' : 'disabled'; ?> />
                            </th>
                            <th>
                                <?php echo __('Employee') ?>
                            </th>
                            <th> 
                                <?php echo __('Job Title') ?>
                            </th>
                            <th>
                                <?php echo __('Review Period') ?>
                            </th>
                            <th>
                                <?php echo __('Due Date') ?>
                            </th>
                            <th>
                                <?php echo __('Status') ?>
                            </th>
                            <th>
                                <?php echo __('Reviewer') ?>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php 
                        $validReviews = 0;
                        if (count($reviews) > 0) {   

                            $i = 0;
                            foreach ($reviews as $review) {
                                if ($review->getEmployee()) {
                                    $validReviews++;
                                    $rowClass = ($i % 2) ? 'even' : 'odd';
                                    $empName = $review->getEmployee()->getFirstName() . ' ' . $review->getEmployee()->getLastName();
                                    ?>
                                    <tr class="<?php echo $rowClass; ?>">
                                        <td class="tdcheckbox">
                                            <input type="checkbox" class="innercheckbox" name="chkReview[]"
                                                   id="chkReview-<?php echo $i; ?>" value="<?php echo $review->getId(); ?>"
                                                   <?php echo (($review->getState() == 
                                                           PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) && $loggedAdmin && trim($empName) != "") ? '' : 'disabled'; ?> />
                                        </td>
                                        <td class="">
                                            <?php
                                            $link = false;
                                            if ($loggedEmpId == $review->getEmployeeId()) {
                                                if ($review->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED) {
                                                    $link = true;
                                                } elseif ($loggedEmpId == $review->getReviewerId() && $review->getState() != 
                                                        PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) {
                                                    $link = true;
                                                } else {
                                                    $link = false;
                                                }
                                            } elseif ($loggedReviewer && $review->getState() != 
                                                    PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) {
                                                $link = true;
                                            } elseif ($loggedAdmin) {
                                                $link = true;
                                            }
                                            ?>
                                                
                                            <?php if ($link) {
                                                ?>
                                                <a href="<?php echo url_for('performance/performanceReview?id=' . 
                                                        $review->getId()) ?>"><?php echo $empName; ?></a>
                                                <?php
                                            } else {
                                                echo $empName;
                                            }
                                            if (trim($empName) == "") {
                                                echo "<font color='red'>" . __('Not Available') . "</font>";
                                            }
                                            ?>
                                        </td>
                                        <td class="">
                                            <?php echo $review->getJobTitle()->getJobTitleName(); ?>
                                        </td>
                                        <td class="">
                                            <?php echo set_datepicker_date_format($review->getperiodFrom()) . ' - ' . 
                                                    set_datepicker_date_format($review->getperiodTo()); ?>
                                        </td>
                                        <td class="">
                                            <?php echo set_datepicker_date_format($review->getDueDate()); ?>
                                        </td>
                                        <td class="">
                                            <?php echo __($review->getTextStatus()); ?>
                                        </td>
                                        <td class="">
                                            <?php
                                            $reviewer = $review->getReviewer()->getFirstName() . ' ' . 
                                                    $review->getReviewer()->getLastName();
                                            if (trim($reviewer) == "") {
                                                $reviewer = "<font color='red'>" . __('Not Available') . "</font>";
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
                        <?php } ?> 
                                    
                        <?php 
                        // Checking $validReviews this instead of count($reviews) to handle cases where there are reviews but some
                        // of them do not have a linked employee because that employee is deleted, but review is not deleted
                        // due to earlier bug in system.
                            if ($validReviews == 0) { ?>
                            <tr>
                                <td></td>
                                <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                if ($pager->haveToPaginate()) {
                    include_partial('global/paging_links', array('pager' => $pager, 
                        'url' => url_for('performance/viewReview'), 'location' => 'bottom'));
                }
                ?> 
            </div> <!-- tableWrapper:Ends -->
                
            <!-- Preserving search clues -->
            <input name="txtPeriodFromDate" type="hidden" value="<?php echo isset($clues['from']) ? $clues['from'] : ''; ?>" />
            <input name="txtPeriodToDate" type="hidden" value="<?php echo isset($clues['to']) ? $clues['to'] : ''; ?>" />
            <input name="txtJobTitleCode" type="hidden" value="<?php echo isset($clues['jobCode']) ? $clues['jobCode'] : ''; ?>" />
            <input name="txtSubDivisionId" type="hidden" 
                   value="<?php echo isset($clues['divisionId']) ? $clues['divisionId'] : ''; ?>" />
            <input name="txtEmpName" type="hidden" value="<?php echo isset($clues['empName']) ? $clues['empName'] : '' ?>" />
            <input name="hdnEmpId" type="hidden" value="<?php echo isset($clues['empId']) ? $clues['empId'] : '' ?>">
            <input name="txtReviewerName" type="hidden" 
                   value="<?php echo isset($clues['reviewerName']) ? $clues['reviewerName'] : '' ?>" />
            <input name="hdnReviewerId" type="hidden" value="<?php echo isset($clues['reviewerId']) ? $clues['reviewerId'] : '' ?>">
            <input name="hdnPageNo" type="hidden" value="<?php echo isset($clues['pageNo']) ? $clues['pageNo'] : '' ?>">

        </form> <!-- #frmList:Ends -->
            
    </div> <!-- inner:Ends -->
    
</div> <!-- box noHeader Ends -->

<script type="text/javascript">
    
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, 
            array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    
    var empdata = <?php echo $form->getEmployeeListAsJson(); ?>;
    
    function autoFill(selector, filler) {
        jQuery.each(empdata, function(index, item){
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
            $('#txtPeriodToDate').val(displayDateFormat);
        }
        
        <?php if ($loggedAdmin || $loggedReviewer) { ?>            
            
            /* Auto completion of employees */
            $("#txtEmpName").autocomplete(empdata, {
                formatItem: function(item) {
                    return $('<div/>').text(item.name).html();
                },
                formatResult: function(item) {
                    return item.name
                }, matchContains:"word"
            }).result(function(event, item) {
                $('#hdnEmpId').val(item.id);
            });
            
            /* Auto completion of reviewers */
            $("#txtReviewerName").autocomplete(empdata, {
                formatItem: function(item) {
                    return $('<div/>').text(item.name).html();
                },
                formatResult: function(item) {
                    return item.name
                }, matchContains:"word"
            }).result(function(event, item) {
                $('#hdnReviewerId').val(item.id);
            });
            
        <?php } // $loggedAdmin || $loggedReviewer:Ends          ?>
        
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
                if ($('#txtEmpName').val() == '<?php echo __('Type for hints') . '...' ?>') {
                    $('#txtEmpName').val('');
                }
            <?php } // $loggedAdmin || $loggedReviewer:Ends          ?>
            
            <?php if ($loggedAdmin) { ?>
                autoFields = autoFields + "|txtReviewerName";
                autoHidden = autoHidden + "|hdnReviewerId";
                if ($('#txtReviewerName').val() == '<?php echo __('Type for hints') . '...' ?>') {
                    $('#txtReviewerName').val('');
                }
            <?php } // $loggedAdmin:Ends          ?>
            
            <?php if ($loggedAdmin || $loggedReviewer) { ?>
                fillAutoFields(autoFields.split("|"), autoHidden.split("|"));
            <?php } ?>
                
            if($('#txtPeriodFromDate').val() == displayDateFormat){
                $('#txtPeriodFromDate').val('');
            }
            if($('#txtPeriodToDate').val() == displayDateFormat) {
                $('#txtPeriodToDate').val('');
            }    
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
            <?php } // $loggedAdmin || $loggedReviewer:Ends          ?>
            <?php if ($loggedAdmin) { ?>
                $('#txtReviewerName').val('');
                $('#hdnReviewerId').val('0');
            <?php } // $loggedAdmin:Ends          ?>
            
        });
        
        /* Add button */
        $('#addReview').click(function(){
            window.location.href = '<?php echo url_for('performance/saveReview'); ?>';
        });
        
        /* Edit button */
        $('#editReview').click(function(){
            var reviews = $(".innercheckbox:checked").size();
            if (reviews < 1) {
                // message to show click at least one
            } else if (reviews == 1) {
                var url = '<?php echo url_for('performance/saveReview?reviewId='); ?>' + $('.innercheckbox:checked').val();
                window.location.href = url;
            } else {
                // message to show click one review
            }
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
            }
        });
		
    }); // ready():Ends
    
</script>

