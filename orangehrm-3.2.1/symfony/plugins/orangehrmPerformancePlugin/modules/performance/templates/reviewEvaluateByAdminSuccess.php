<?php use_stylesheets_for_form($form); ?>

<?php
use_stylesheets_for_form($form);
Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, false);
?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>

<div class="box" id="divFormContainer">
    <div class="head"><h1><?php echo __('Administrator Evaluation Form'); ?></h1></div>
    <?php include_partial('global/form_errors', array('form' => $form)); ?>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <div id="reviewData" >
            <div style="" class="reviewInfo" id="review-details">
                <div class="smallerHeader"><center><h1><b><?php echo __('Review Details') ?></b></h1></center></div>
                <table >
                    <tr style="height: 6px"><td></td></tr>
                    <tr class="trReviewHeight"><td class="tableColoumnWidth" ><label class="labelName"><?php echo __("Employee Name"); ?></label></td><td class="tableColoumnWidthname" ></td> <td><label class="labelValue"><?php echo $form->getReview()->getEmployee()->getFullName() ?></label></td></tr>
                    <tr class="trReviewHeight"><td class="tableColoumnWidth" ><label class="labelName"><?php echo __("Job Title"); ?></label></td> <td class="tableColoumnWidthname" > </td> <td><label class="labelValue"><?php echo $form->getReview()->getJobTitle()->getJobTitleName() ?></label></td></tr>
                    <tr class="trReviewHeight"><td class="tableColoumnWidth" ><label class="labelName"><?php echo __("Review Period"); ?></label></td> <td></td> <td id="reviewColoumnId" ><label class="labelValue"><?php echo set_datepicker_date_format($form->getReview()->getWorkPeriodStart()) . " To " . set_datepicker_date_format($form->getReview()->getWorkPeriodEnd()); ?></label></td></tr>
                    <tr class="trReviewHeight"><td class="tableColoumnWidth" ><label class="labelName"><?php echo __("Review Due Date"); ?></label></td><td></td>  <td><label class="labelValue"><?php echo set_datepicker_date_format($form->getReview()->getDueDate()) ?></label></td></tr>
                    <tr class="trReviewHeight"><td class="tableColoumnWidth" ><label class="labelName"><?php echo __("Status"); ?></label></td> <td></td> <td><label class="labelValue"><?php echo __(ReviewStatusFactory::getInstance()->getStatus($form->getReview()->getStatusId())->getName()) ?></label></td></tr>
                </table>


            </div>

            <div class="reviewersreviewInfo" >
                <div class="smallerHeader"><center><h1><?php echo __("Individual Evaluation Status") ?></h1></center></div>

                <div class="evaluation">
                    <table id="induvidualEvaluate" >
                        <thead>
                            <tr>
                                <th><?php echo __("Reviewer Type"); ?></th>
                                <th><?php echo __("Reviewer Name"); ?></th>
                                <th><?php echo __("Review Status"); ?></th>                       
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($form->getReviewers() as $reviewer) { ?>
                                <tr>
                                    <td class="leftAlign"><?php echo __($reviewer->getGroup()->getName()) ?></td>
                                    <td class="leftAlign"><?php echo $reviewer->getEmployee()->getFullName() ?></td>
                                    <td class="leftAlign"><?php echo ReviewerReviewStatusFactory::getInstance()->getStatus($reviewer->getStatus())->getName() ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <br/><br/>
        <div>
            <form id="reviewEvaluate" name="reviewEvaluate" method="post" action="">
                <?php
                echo $form['_csrf_token'];
                echo $form['id']->render();
                echo $form['action']->render();
                echo $form['evaluationsAction']->render();
                ?>
                <br class="clear"/>
                <?php
                if (sizeof($form->getReviewers()) > 0) {
                    $reviewerGroupId = $form->getReviewers()->getFirst()->getReviewerGroupId();
                }

                $columNumber = 0;
                foreach ($form->getReviewers() as $reviewer) {

                    if (($reviewer->getGroup()->getId() == 2 && $reviewer->getStatus() == 3) || $reviewer->getGroup()->getId() != 2) {
                        if ($reviewer->getReviewerGroupId() != 2) {
                            $reviewerGroupId = $reviewer->getReviewerGroupId();
                            $columNumber = 1;
                            ?>
                            </tr>
                            </table>
                            <br class="clear"/>
                            <div class="smallerHeader"><h1><?php echo __('Evaluation by ' . $reviewer->getGroup()->getName()); ?></h1></div>
                            <table class="expandTable">
                                <br class="clear"/>
                                <tr>
                                <?php } else {
                                    ?>                               

                                <div class="smallerHeader"><h1><?php echo __('Evaluation by Employee'); ?></h1></div>
                                <table class="expandTable">
                                    <br class="clear"/>
                                    <tr>
                                        <?php
                                        $columNumber++;
                                    }
                                    ?>
                                </tr>
                                <td>   
                                    <div>
                                        <div class="evaluationexpand">
                                            <table  class="evaluateBy" >
                                                <thead>
                                                    <tr>
                                                        <?php if ($columNumber == 1) { ?>
                                                            <th colspan="6" class="evaluationEmployee"><?php echo __("Evaluation by") . ' ' . $reviewer->getEmployee()->getFullName(); ?></th>
                                                        <?php } else { ?>
                                                            <th colspan="2" class="evaluationEmployee"><?php echo __("Evaluation by") . ' ' . $reviewer->getEmployee()->getFullName(); ?></th>
                                                        <?php } ?>
                                                    </tr>
                                                    <tr>
                                                        <?php if ($columNumber == 1) { ?>
                                                            <th><?php echo __("KPI"); ?></th>
                                                            <th><?php echo __("Min"); ?></th>
                                                            <th><?php echo __("Max"); ?></th>
                                                        <?php } ?>
                                                        <th><?php echo __("Rating"); ?></th>
                                                        <th><?php echo __("Comment"); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $valuesForCalcuation = array();
                                                    $ratings = $form->getSortedRatings($reviewer->getRating());

                                                    foreach ($ratings as $rating) {
                                                        ?>
                                                        <tr>
                                                    <input type="hidden" value="<?php echo $rating->getId(); ?>" id="rating_id_<?php echo $rating->getId(); ?>" name="rating_id[<?php echo $rating->getId(); ?>]" />
                                                    <?php if ($columNumber == 1) { ?>
                                                        <td class="rightAlign"><center><?php echo $rating->getKpi()->getKpiIndicators() ?></center></td>
                                                        <td class="rightAlign"><center><?php echo $rating->getKpi()->getMinRating() ?></center></td>
                                                        <td class="rightAlign"></center><?php echo $rating->getKpi()->getMaxRating() ?></center></td>
                                                    <?php } ?>
                                                    <td><center><input class="rightAlign" min="<?php echo $rating->getKpi()->getMinRating() ?>" max="<?php echo $rating->getKpi()->getMaxRating() ?>"  type="text" value="<?php echo $rating->getRating(); ?>" id="rating_<?php echo $rating->getId(); ?>"  name="rating[<?php echo $rating->getId(); ?>]" /></center></td>
                                                    <td><textarea class="comment" type="text" id="comment_<?php echo $rating->getId(); ?>" name="comment[<?php echo $rating->getId(); ?>]" ><?php echo $rating->getComment(); ?></textarea> </td>                   
                                                    </tr>            
                                                    <?php
                                                }

                                                if ($columNumber == 1) {
                                                    ?>
                                                    <tr>
                                                        <td colspan="3"><td colspan="1"><label class="lableName"><?php echo __("General Comment : "); ?></label></td>
                                                        <td colspan="1"><textarea class="comment" type="text" id="general_comment_<?php echo $reviewer->getGroup()->getId(); ?>" name="general_comment[<?php echo $reviewer->getGroup()->getId(); ?>]" ><?php echo $reviewer->getComment(); ?></textarea></td>
                                                    </tr>
                                                    
                                                    <?php
                                                } else {
                                                    ?>
                                                    <tr>
                                                        <td colspan="1"><label class="lableName"><?php echo __("General Comment : "); ?></label></td>
                                                        <td colspan="1"><textarea class="comment" type="text" id="general_comment_<?php echo $reviewer->getGroup()->getId(); ?>" name="general_comment[<?php echo $reviewer->getGroup()->getId(); ?>]" ><?php echo $reviewer->getComment(); ?></textarea></td>
                                                    </tr>
                                                    
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </td>
                                <?php
                            }
                        }
                        ?>
                        </tr>
                    </table>
                    <br class="clear"/>
                    <div class="smallerHeader"><h1><?php echo __('Finalization'); ?></h1></div>
                    <br class="clear"/>
                    <ol>
                        <li class="largeTextBox">
                            <?php echo $form['hrAdminComments']->renderLabel(null, array('class' => 'labelValue')); ?>
                            <?php echo $form['hrAdminComments']->render() ?>
                        </li>

                        <li>
                            <?php echo $form['finalRating']->renderLabel(null, array('class' => 'labelValue')); ?>
                            <?php echo $form['finalRating']->render() ?>
                        </li>
                        <li>  
                            <?php echo $form['completedDate']->renderLabel(null, array('class' => 'labelValue')); ?>
                            <?php echo $form['completedDate']->render() ?>
                        </li>

                        <?php if ($form->isEvaluationsEditable()) { ?>
                            <li class="required">
                                <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                            </li>
                        <?php } ?>
                    </ol>
                    <p>
                        <?php if ($form->isEvaluationsEditable()) { ?>
                            <input type="button" class="applybutton" id="saveBtn" value="<?php echo __('Save'); ?>" title="<?php echo __('Add'); ?>"/>                
                        <?php } ?>
                        <?php if ($form->isEvaluationsCompleateEnabled()) { ?>
                            <input type="button" class="applybutton" id="completeBtn" value="<?php echo __('Complete'); ?>" title="<?php echo __('Complete'); ?>"/>
                        <?php } ?>
                        <input type="button" class="reset" id="backBtn" value="<?php echo __('Back'); ?>" title="<?php echo __('Back'); ?>"/> 
                    </p>
            </form>
            <!-- Confirmation box HTML: Begins -->
            <div class="modal hide" id="deleteConfModal">
                <div class="modal-header">
                    <a class="close" data-dismiss="modal">×</a>
                    <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
                </div>
                <div class="modal-body">
                    <p><?php echo __("The review will be made read-only after completion.") . __("This action cannot be undone.") . __("Are you sure you want to continue?"); ?></p>
                    <br/>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
                    <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
                </div>
            </div>
            <!-- Confirmation box HTML: Ends -->
        </div>

    </div>
</div>

<script>

    var backUrl = '<?php echo url_for($backUrl); ?>';

    $(document).ready(function () {
<?php if (!$form->isEvaluationsEditable()) { ?>
            $('input,textarea').attr("disabled", "disabled");
            $('#backBtn').removeAttr("disabled");
            $(".calendar").datepicker('disable');
<?php } ?>
        $('#saveBtn').click(function () {
            var settings = $('#reviewEvaluate').validate().settings;
            delete settings.rules["reviewEvaluation[hrAdminComments]"];
            delete settings.messages["reviewEvaluation[hrAdminComments]"];
            delete settings.rules["reviewEvaluation[finalRating]"];
            delete settings.messages["reviewEvaluation[finalRating]"];
            delete settings.rules["reviewEvaluation[completedDate]"];
            delete settings.messages["reviewEvaluation[completedDate]"];
            $('#reviewEvaluation_action').attr('value', 'save');
            $('#reviewEvaluate').submit();
        });

        $('#completeBtn').click(function () {
            if ($('#reviewEvaluate').valid()) {
                $("#deleteConfModal").modal();
            }

        });

        $('#dialogDeleteBtn').bind('click', function () {
            $('#reviewEvaluation_action').attr('value', 'complete');
            $('#reviewEvaluation_evaluationsAction').attr('value', 'complete');
            $('#reviewEvaluate').submit();
        });

        $('#backBtn').click(function () {
            console.log(backUrl);
            window.location.replace(backUrl);
        });

        var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
        var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>';

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



        $("#reviewEvaluate").validate({
            rules: {
                'reviewEvaluation[hrAdminComments]': {required: true, maxlength: 255},
                'reviewEvaluation[finalRating]': {required: true, min: 0, max: 100, number: true, positiveNumber: true},
                'reviewEvaluation[completedDate]': {
                    required: true,
                    valid_date: function () {
                        return {
                            format: datepickerDateFormat
                        }
                    }

                }
            },
            messages: {
                'reviewEvaluation[hrAdminComments]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                    maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 255)); ?>'
                },
                'reviewEvaluation[finalRating]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                    number: '<?php echo __(ValidationMessages::VALID_NUMBER); ?>'

                },
                'reviewEvaluation[completedDate]': {
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                    valid_date: lang_invalidDate
                }
            }
        });

        $.validator.addMethod('positiveNumber',
                function (value) {
                    if (!parseFloat(value) > 0) {
                        return /^[0-9][0-9]*$/.test(value);
                    } else {
                        return true;
                    }
                }, '<?php echo __(PerformanceValidationMessages::ONLY_INTEGER_ALLOWED); ?>');


    });
    var minMsg = "<?php echo __('Rating should be less than or equal to ') ?>";
    var maxMsg = "<?php echo __('Rating should be greater than or equal to ') ?>";
    jQuery.extend(jQuery.validator.messages, {
        max: jQuery.validator.format(minMsg + "{0}."),
        min: jQuery.validator.format(maxMsg + "{0}.")
    });
</script>

<?php
use_stylesheets_for_form($form);
Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
?>