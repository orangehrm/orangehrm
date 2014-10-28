<?php use_stylesheets_for_form($form); ?>

<script>
    var addedReviewers = new Array();
</script>

<div id="performance" class="box single">
    <div class="head">
        <h1 id="addPerformanceHeading"><?php echo __('Performance Review'); ?></h1>
    </div>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>

        <?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
        <!-- Custom modal-->
        <div id="alertModal" class="modal hide" style="display: none;">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3> <?php echo __('OrangeHRM - Information') ?></h3>
            </div>
            <div class="modal-body">
                <p id="messageNorecord"></p>  
            </div>
            <div class="modal-footer">
                <input id="dialogDeleteBtn" class="btn" type="button" value="<?php echo __("Ok"); ?>" data-dismiss="modal">
            </div>
        </div>
        <form id="saveReview" name="saveReview" method="post" action="">
            <fieldset>

                <ol>
                    <?php
                    $tableVisisbility = "";
                    echo $form['_csrf_token'];
                    ?>
                    <?php echo $form['reviewId']->render(); ?>
                    <?php echo $form['formAction']->render(); ?>
                    <?php echo $form['employeeId']->render(); ?>
                    <?php echo $form['supervisorReviewerId']->render(); ?>
                    <?php if ($form['reviewId']->getValue() == '') { ?>
                        <li>
                            <?php echo $form['employee']->renderLabel(); ?>
                            <?php echo $form['employee']->render(array('class' => 'longTextBoxAutoComplete')); ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li>
                            <?php echo $form['employee']->renderLabel($form['employee']->getValue()); ?>
                            <?php echo $form['employee']->render(array("style" => "display:none")); ?>
                            <?php echo $form['employeeId']->render(); ?>
                        </li>
                    <?php }
                    ?>
                </ol>

                <div id="reviewCreationBody">
                <h4><?php echo __('Supervisor Reviewers'); ?></h4>
                    <ol>  
                        <?php if ($form['reviewId']->getValue() == '') { ?>
                        <li>
                            <?php echo $form['supervisorReviewer']->renderLabel(); ?>               
                            <?php echo $form['supervisorReviewer']->render(array('class' => 'longTextBoxAutoComplete')); ?>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li>
                            <?php $reviewers = $form->getReviewers('supervisors'); ?>
                            <?php foreach ($reviewers as $reviewer) {
                            echo $form['supervisorReviewer']->renderLabel($reviewer->getEmployee()->getFullName());}?>
                            <?php echo $form['supervisorReviewerId']->render(); ?>
                        </li>
                        <?php }
                    ?>
                    </ol>

                    <ol>
                        <li>
                            <?php echo $form['workPeriodStartDate']->renderLabel(null, array('class' => 'lableValue')); ?>
                            <?php echo $form['workPeriodStartDate']->render(); ?>
                        </li>
                        <li>
                            <?php echo $form['workPeriodEndDate']->renderLabel(null, array('class' => 'lableValue')); ?>
                            <?php echo $form['workPeriodEndDate']->render(); ?>
                        </li>
                        <li>
                            <?php echo $form['dueDate']->renderLabel(null, array('class' => 'lableValue')); ?>
                            <?php echo $form['dueDate']->render(); ?>
                        </li>
                    </ol>

                    <ol id="withoutBorder">
                        <li class="required">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                    </ol>

                    <p>
                        <?php if ($form->isSaveEnabled()) { ?>
                            <input type="button" class="applybutton" id="saveBtn" value="<?php echo __('Save'); ?>" title="<?php echo __('Add'); ?>"/>
                        <?php } ?>                            
                        <?php if ($form->isActivateEnabled()) { ?>
                            <input type="button" class="applybutton" id="activateBtn" value="<?php echo __('Activate'); ?>" title="<?php echo __('Activate'); ?>"/>      
                        <?php } ?>
                        <input type="button" class="reset" id="backBtn" value="<?php echo __('Back'); ?>" title="<?php echo __('Back'); ?>"/> 
                    </p>

                </div>
            </fieldset>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {

<?php if (!$form->isSaveEnabled()) { ?>
            $('input:text,textarea,select,').attr('disabled', 'disabled');
            $('input:text,input:button,textarea,select').addClass('disableElements');
            $('.deleteRow').html('');
<?php } ?>

<?php if ($form['reviewId']->getValue() == 0) { ?>
            $('#reviewCreationBody').toggle();
<?php } ?>

<?php if (isset($templateMessage)) { ?>
            $('#reviewCreationBody').show();
<?php } ?>

        var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
        var leaveBalanceUrl = '<?php echo url_for('leave/getLeaveBalanceAjax'); ?>';
        var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>';
        var lang_dateError = '<?php echo __("To date should be after from date") ?>';
        var reviewId = '<?php echo $form['reviewId']->getValue(); ?>';

        $.datepicker.setDefaults({showOn: 'click'});

        daymarker.bindElement(".ohrm_datepicker", {
            dateFormat: datepickerDateFormat,
            onClose: function () {
                $(this).valid();
            }
        });

        $('.ohrm_datepicker').click(function () {
            daymarker.show(this);
        });

        $('.calendarBtn').click(function () {
            var elementId = ($(this).prev().attr('id'));
            daymarker.show("#" + elementId);
        });

        $('#saveBtn').click(function () {
            $('#saveReview360Form_formAction').val('save');
            $('#saveReview').submit();
        });

        $('#backBtn').click(function () {
            $(location).attr('href', '<?php echo url_for("performance/searchPerformancReview") ?>');
        });

        $('#activateBtn').click(function () {
            $('#saveReview360Form_formAction').val('activate');
            $('#saveReview').submit();
        });

        var employeeListAll = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>;
        var employeeList = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson($form['employeeId']->getValue())) ?>;
        var supervisorList = null;


        popluateAutoCompleate1('saveReview360Form_employee', 'saveReview360Form_employeeId', employeeListAll);


        $('#saveReview360Form_employee').keyup(function () {
            popluateAutocompleateOnType(employeeList, $('#saveReview360Form_employee').val(), 'saveReview360Form_employeeId');
        });


        var urlToGeSupervisorList = '<?php echo url_for('performance/getSupervisorListAjax'); ?>';
        var urlToGetSubordinateList = '<?php echo url_for('performance/getSubordinateListAjax'); ?>';

        function popluateAutoCompleate1(visibleElementId, hiddenElementId, employeeList) {


<?php if ($form['reviewId']->getValue() == '') { ?>

                $("#" + visibleElementId).autocomplete(employeeList, {
                    formatItem: function (item) {
                        return item.name;
                    }
                    , matchContains: true
                }).result(function (event, item) {

                    clearFormAndDisplay();

                    addedReviewers = new Array();
                    $.ajax({
                        type: 'GET',
                        url: urlToGeSupervisorList,
                        data: '&id=' + item.id,
                        dataType: 'json',
                        success: function (data) {
                            $.each(data, function (i, item) {
                                addReviewerRow("supervisors", 'supervisor_table', item.name, item.id);
                            });
                            supervisorList = data;
                            popluateAutoCompleate('saveReview360Form_supervisorReviewer', 'saveReview360Form_supervisorReviewerId', supervisorList);
                        }
                    });

                    $.ajax({
                        type: 'GET',
                        url: urlToGetSubordinateList,
                        data: '&id=' + item.id,
                        dataType: 'json',
                        success: function (data) {
                            $.each(data, function (i, item) {
                                addReviewerRow("subordinates", 'subordinate_table', item.name, item.id);
                            });
                        }
                    });

                    $('#' + hiddenElementId).val(item.id);
                });
<?php } ?>
        }

        function clearFormAndDisplay() {
            $('#reviewCreationBody').toggle(500);

            $("#supervisor_table").find("tr:gt(0)").remove();
            $('#supervisor_table tbody>tr:first').hide();

            $("#subordinate_table").find("tr:gt(0)").remove();
            $('#subordinate_table tbody>tr:first').hide();

            $("#peer_reviewer_table").find("tr:gt(0)").remove();
            $('#peer_reviewer_table tbody>tr:first').hide();
        }

        function popluateAutoCompleate(visibleElementId, hiddenElementId, employeeList) {
            $("#" + visibleElementId).autocomplete(employeeList, {
                formatItem: function (item) {
                    return item.name;
                }
                , matchContains: true
            }).result(function (event, item) {
                $('#' + hiddenElementId).val(item.id);
            });
            
        }

        $('#add_supervisor_Btn').live("click", function () {
            if ($('#saveReview360Form_supervisorReviewerId').val() > 0) {
                addReviewerRow("supervisors", 'supervisor_table', $('#saveReview360Form_supervisorReviewer').val(), $('#saveReview360Form_supervisorReviewerId').val());
                $("#saveReview360Form_supervisorReviewer").val('');
            }
        });

        $('#deleteRow').live("click", function () {
            $(this).parent().parent().remove();
            employeeIdToBeDeleted = ($(this).parent().parent().find('input').val());
            addedReviewers = ($.removeFromArray(employeeIdToBeDeleted, addedReviewers));
        });

        $.removeFromArray = function (value, arr) {
            return $.grep(arr, function (elem, index) {
                return elem !== value;
            });
        };


        function addReviewerRow(type, tableId, employeeName, employeeNumber) {

            if ($("#saveReview360Form_employeeId").val() == employeeNumber) {
                $("#messageNorecord").text('<?php echo __("Employee cannot be a reviewer in another category") ?>');
                $("#alertModal").modal();
                return false;
            }

            if (isAlreadyAddedReviewer(employeeNumber)) {
                tableRow = '<tr>';
                tableRow += '<td><input type="hidden" name="reviewers[' + type + '][]" value="' + employeeNumber + '"/>' + employeeName + '</td>';
                tableRow += '<td><a id="deleteRow" class="deleteRow" href="#">X</a></td>';
                tableRow += '</tr>';

                var row = $(tableRow);
                row.insertAfter('#' + tableId + ' tbody>tr:last');

                $('#' + tableId + ' tbody>tr:first').show();

            } else {
                $("#messageNorecord").text('<?php echo __("Cannot add same person as a reviewer multiple times") ?>');
                $("#alertModal").modal();
            }

        }

        $("#saveReview").validate({
            rules: {
                'saveReview360Form[employeeId]': {required: true},
                'saveReview360Form[employee]': {required: true},
                'saveReview360Form[supervisorReviewer]': {required: true,isSupervisor: true},
                'saveReview360Form[supervisorReviewerId]': {required: true},
                'saveReview360Form[workPeriodStartDate]': {required: true},
                'saveReview360Form[workPeriodEndDate]': {required: true},
                'saveReview360Form[dueDate]': {
                    required: true,
                    valid_date: function () {
                        return {
                            format: datepickerDateFormat
                        }                                
                    },
                    date_range: function () {
                        return {
                            format: datepickerDateFormat,
                            fromDate: $("#saveReview360Form_workPeriodStartDate").val()
                        }
                    }
                },
                'saveReview360Form[workPeriodStartDate]': {
                    required: true,
                    valid_date: function () {
                        return {
                            format: datepickerDateFormat
                        }
                    },
                    date_range: function () {
                        return {
                            format: datepickerDateFormat,
                            fromDate: $("#saveReview360Form_workPeriodStartDate").val()
                        }
                    }
                },
                'saveReview360Form[workPeriodEndDate]': {
                    required: true,
                    valid_date: function () {
                        return {
                            format: datepickerDateFormat
                        }
                    },
                    date_range: function () {
                        return {
                            format: datepickerDateFormat,
                            fromDate: $("#saveReview360Form_workPeriodStartDate").val()
                        }
                    }
                }
            },
            messages: {
                'saveReview360Form[dueDate]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                    valid_date: lang_invalidDate,
                    date_range: '<?php echo __("End date should be after Start date") ?>'
                },
                'saveReview360Form[employeeId]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'saveReview360Form[employee]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'saveReview360Form[supervisorReviewer]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'saveReview360Form[supervisorReviewerId]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'saveReview360Form[workPeriodStartDate]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                    valid_date: lang_invalidDate
                },
                'saveReview360Form[workPeriodEndDate]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                    valid_date: lang_invalidDate,
                    date_range: '<?php echo __("End date should be after Start date") ?>'
                }
            }
        });
        $.validator.addMethod('isSupervisor',
        function (value) { 
            if(reviewId > 0){
                return true;   
            } else{
               var supervisorId = $('#saveReview360Form_supervisorReviewerId').val();
               for(var i=0; i < supervisorList.length; i++){
                   if(supervisorList[i].id == supervisorId){
                       return true;
                   }
               }               
               return false;
            } 
        }, '<?php echo __(PerformanceValidationMessages::INVALID_SUPERVIOSR); ?>');

        /**
         * @param employeeData Json Array
         * @param employeeName String
         * @param elmentToChange elementName
         */
        function popluateAutocompleateOnType(employeeData, employeeName, elmentToChange) {

            employeeJason = getEmployeesInAutoCompleteByName(employeeData, employeeName);
            if (employeeJason != null) {
                $('#' + elmentToChange).val(employeeJason.id);
                return true;
            } else {
                $('#' + elmentToChange).val('');
                return false;
            }
        }

        /**
         *@param 
         *@return Jason object 
         */
        function getEmployeesInAutoCompleteByName(employeeData, employeeName) {
            jsonobject = null;
            $.each(employeeData, function (i, item) {
                if ((item.name.toLowerCase()) == (employeeName.toLowerCase())) {
                    jsonobject = item;
                }
            });
            return jsonobject;
        }

        function isAlreadyAddedReviewer(employeeId) {
            if ($.inArray(employeeId, addedReviewers) < 0) {
                addedReviewers.push(employeeId);
                return true;
            } else {
                return false;
            }
        }
    });
</script>