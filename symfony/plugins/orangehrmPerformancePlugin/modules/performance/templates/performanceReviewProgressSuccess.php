<?php use_stylesheet(plugin_web_path('orangehrmPerformancePlugin', 'css/performanceReviewProgressSuccess.css')); ?>


<div class="box" id="divFormContainer">
    <div class="head"><h1><?php echo __('Performance Review Progress'); ?></h1></div>

    <div class="inner">
        <div class="reviewers"  id="review-details">
            <div class="smallerHeader" id ="review_detail"><center><h1><?php echo __('Review Details') ?></h1></center></div>
            <table>

                <tr class='trReviewHeight'><td class="tableColoumnWidth" ><label class="lableName"><?php echo __("Employee Name"); ?></label></td><td class="tableColumnwithvalue" ></td><td><label class="labelValue"><?php echo $review->getEmployee()->getFullName() ?></label></td></tr>
                <tr class='trReviewHeight'><td class="tableColoumnWidth" ><label class="lableName"><?php echo __("Job Title"); ?></label></td><td class="tableColumnwithvalue" ></td><td><label class="labelValue"><?php echo $review->getJobTitle()->getJobTitleName() ?></label></td></tr>
                <tr class='trReviewHeight'><td class="tableColoumnWidth" ><label class="lableName"><?php echo __("Department"); ?></label></td> <td class="tableColumnwithvalue" ></td><td><label class="labelValue"><?php echo $review->getDepartment()->getName() ?></label></td></tr>
                <tr class='trReviewHeight'><td class="tableColoumnWidth" ><label class="lableName"><?php echo __("Review Period"); ?></label></td> <td class="tableColumnwithvalue" ></td><td><label class="labelValue"><?php echo set_datepicker_date_format($review->getWorkPeriodStart()) . " To " . set_datepicker_date_format($review->getWorkPeriodEnd()); ?></label></td></tr>
                <tr class='trReviewHeight'><td class="tableColoumnWidth" ><label class="lableName"><?php echo __("Review Due Date"); ?></label></td><td class="tableColumnwithvalue" ></td> <td><label class="labelValue"><?php echo set_datepicker_date_format($review->getDueDate()) ?></label></td></tr>
                <tr class='trReviewHeight'><td class="tableColoumnWidth" ><label class="lableName"><?php echo __("Status"); ?></label></td> <td class="tableColumnwithvalue" ></td><td><label class="labelValue"><?php echo __(ReviewStatusFactory::getInstance()->getStatus($review->getStatusId())->getName()) ?></label></td></tr>

            </table>
        </div>
        <br/><br/>
        <div class="reviewers">
            <?php
            $newGroup = false;
            $firstOne = 1;
            foreach ($review->getReviewers() as $reviewer) {
                if ($tempValue != $reviewer->getGroup()->getName()) {
                    $newGroup = true;
                } else {
                    $newGroup = false;
                }

                $tempValue = $reviewer->getGroup()->getName();

                if ($newGroup) {
                    $headingName = 'Reviewer';
                    if (!$firstOne) {
                        echo '</tbody></table></div>';
                    }
                    ?>

                    <br/><br/>
                    <div class="smallerHeader" ><center><h1><?php echo __($reviewer->getGroup()->getName() . ' ' . $headingName) ?></h1></center></div>   
                    <div class="evaluation"  >
                        <table border="1">

                            <tr   id="employeeReviewer">
                                <td id="empId" ><?php echo __("Employee Id"); ?></td>
                                <td id="empname" ><b><?php echo __('Employee Name'); ?></b></td>
                                <td id="reviewSatus" ><b><?php echo __("Review Status"); ?></b></td>   
                                <td id="completeDate" ><b><?php echo __("Completed Date"); ?></b></td> 

                            </tr>


                            <?php $firstOne = 0;
                        } ?>
                        <tr>
                            <td ><?php echo $reviewer->getEmployee()->getEmployeeId() ?></td>
                            <td ><?php echo $reviewer->getEmployee()->getFullName() ?></td>
                            <td ><?php echo __(ReviewerReviewStatusFactory::getInstance()->getStatus($reviewer->getStatus())->getName()) ?></td>
                            <td ><?php echo set_datepicker_date_format($reviewer->getCompletedDate()) ?></td>
                        </tr>

<?php } ?>
                </table>
            </div>
        </div>
        <div class="reviewers">
            <p>
<?php echo'<br>'; ?>
                <input type="button" class="reset" id="backBtn" value="<?php echo __('Back'); ?>" title="<?php echo __('Back'); ?>"/>
            </p> 
        </div>
    </div>
</div>

<script >
    var backUrl = '<?php echo $backUrl; ?>';
    $('#backBtn').click(function () {
        window.location.replace(backUrl);
    });
</script>