<style type="text/css">
    table.table td {
        vertical-align: top;
    }
    table.data {
        width: 85%;
    }
</style>


<div class="box miniList" id="performanceReviewcontentContainer">

    <div class="head" id="formHeading" >
        <h1><?php echo __("Performance Review") ?></h1>
    </div>

    <div class="inner">

        <?php include_partial('global/flash_messages'); ?>

        <form action="#" id="frmSave" class="content_inner" method="post">

            <?php echo $form['_csrf_token']; ?>
            <input type="hidden" name="id" id="id" value="<?php echo $performanceReview->getId() ?>"/>
            <input type="hidden" name="saveMode" id="saveMode" value="" />

            <fieldset>
                <ol>
                    <li>
                        <label><?php echo __("Employee") ?></label>
                        <label class="line"><?php echo $performanceReview->getEmployee()->getFirstName() ?> 
                            <?php echo $performanceReview->getEmployee()->getLastName() ?></label>
                    </li>
                    <li>
                        <label><?php echo __("Job Title") ?></label>
                        <label class="line"><?php echo $performanceReview->getJobTitle()->getJobTitleName(); ?> </label>
                    </li>
                    <li>
                        <label><?php echo __("Reviewer") ?></label>
                        <label class="line"><?php echo $performanceReview->getReviewer()->getFirstName() ?> <?php echo $performanceReview->getReviewer()->getLastName() ?></label>
                    </li>
                    <li>
                        <label><?php echo __("Review Period") ?></label>
                        <label class="line"><?php echo set_datepicker_date_format($performanceReview->getPeriodFrom()) ?>-<?php echo set_datepicker_date_format($performanceReview->getPeriodTo()) ?></label>
                    </li>
                    <li>
                        <label><?php echo __("Status") ?></label>
                        <label class="line"><?php echo __($performanceReview->getTextStatus()) ?> </label>
                    </li>
                    <?php if (count($performanceReview->getPerformanceReviewComment()) > 0) { ?>
                        <li>
                            <label><?php echo __("Notes") ?></label>
                            <table class="table data">
                                <tr>
                                    <th style="width:20%"><?php echo __("Date") ?></th>
                                    <th style="width:30%"><?php echo __("Employee") ?></th>
                                    <th style="width:50%"><?php echo __("Comment") ?></th>
                                </tr>
                                <?php
                                $i = 1;
                                foreach ($performanceReview->getPerformanceReviewComment() as $comment) {
                                    ?>
                                    <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
                                        <td ><?php echo set_datepicker_date_format($comment->getCreateDate()) ?></td>
                                        <td ><?php echo ($comment->getEmployee()->getFullName() != '') ? $comment->getEmployee()->getFullName() : __('Admin') ?></td>
                                        <td ><?php echo $comment->getComment() ?></td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </table>
                        </li>
                    <?php } ?>
                </ol>
            </fieldset>
            <input type="hidden" name="validRate" id="validRate" value="1" />

            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40%" scope="col"><?php echo __("Key Performance Indicator") ?></th>
                        <th scope="col" style="width:10%"><?php echo __("Min Rate") ?></th>
                        <th scope="col" style="width:10%"><?php echo __("Max Rate") ?></th>
                        <th scope="col" style="width:10%"><?php echo __("Rating") ?></th>
                        <th scope="col" style="width:30%"><?php echo __("Reviewer Comments") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($kpiList as $kpi) {
                        ?>
                        <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
                            <td >
                                <?php echo $kpi->getKpi() ?>
                            </td>
                            <td >
                                <?php echo ($kpi->getMinRate() != '') ? $kpi->getMinRate() : '-' ?>
                            </td>
                            <td >
                                <?php echo ($kpi->getMaxRate() != '') ? $kpi->getMaxRate() : '-' ?>
                            </td>
                            <td  >
                                <input type="hidden" name="max<?php echo $kpi->getId() ?>" id="max<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMaxRate() ?>" />
                                <input type="hidden" name="min<?php echo $kpi->getId() ?>" id="min<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMinRate() ?>" />
                                <input id="txtRate<?php echo $kpi->getId() ?>"  name="txtRate[<?php echo $kpi->getId() ?>]" type="text"  class="smallInput" value="<?php echo trim($kpi->getRate()) ?>"  maxscale="<?php echo $kpi->getMaxRate() ?>" minscale="<?php echo $kpi->getMinRate() ?>" valiadate="1" />
                                <span class="validation-error"></span>
                            </td>
                            <td class="">
                                <textarea id='txtComments' class="reviwerComment" name='txtComments[<?php echo $kpi->getId() ?>]'
                                          rows="2" cols="40"><?php echo trim($kpi->getComment()); ?></textarea>
                                <span class="validation-error"></span>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                    <?php
                    if (($isHrAdmin || $isReviwer) &&
                            ($performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED)) :
                        ?>                        
                        <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
                            <td colspan="4" style="text-align:right"><?php echo __("Note") ?></td>
                            <td>
                                <textarea id='txtMainComment' name='txtMainComment' class="formTextArea" rows="2" cols="40" ></textarea>
                                <span class="validation-error"></span>
                            </td>
                        </tr>
<?php endif; ?>                        
                </tbody>
            </table>

            <p style="margin-top:10px">
                <?php if (($isReviwer && ($performanceReview->getState() <= PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED)) || ( $isHrAdmin && $performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED)) { ?>
                    <input type="button" class="" id="saveBtn" value="<?php echo __("Edit") ?>"  />
                <?php } ?>

                <?php if ($isReviwer && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED)) { ?>
                    <input type="button" class="" id="submitBtn" value="<?php echo __("Submit") ?>"  />
                <?php } ?>

                <?php if ($isHrAdmin && $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) { ?>
                    <input type="button" class="delete" id="rejectBtn" value="<?php echo __("Reject") ?>"  />
                <?php } ?>

                <?php if ($isHrAdmin && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED )) { ?>
                    <input type="button" class="" id="approveBtn" value="<?php echo __("Approve") ?>"  />
                <?php } ?>

                <input type="button" class="reset" id="backBtn" value="<?php echo __("Back"); ?>" />
            </p>
        </form>

    </div> <!-- inner -->

</div> <!-- performanceReviewcontentContainer -->

<script type="text/javascript">
    
    function clearErrors() {
        
        $("span.validation-error").each(function(){
            $(this).empty();
        });
        
        $("input.smallInput").each(function(){
            $(this).removeClass('validation-error');
        });        
        
    }

    //Check submit
    function checkSubmit(){
        
        clearErrors();
        
        var valid = true ;
        
        $("input.smallInput").each(function() {
            
            max	=	parseFloat($(this).attr('maxscale'));
            min =   parseFloat($(this).attr('minscale'));
            rate =  parseFloat(this.value) ;
            
            if (this.value != '' && isNaN(rate)) {
                valid = false;                
                $(this).addClass('validation-error');
                $(this).next('span.validation-error').text('<?php echo __('Should be a number'); ?>');
            }

            if( !isNaN(max) || !isNaN(min)){
                
                if( isNaN(rate)){
                    
                    valid = false;
                    $(this).addClass('validation-error');
                    $(this).next('span.validation-error').text('<?php echo __('Should be a number'); ?>');
                    
                } else {
                    
                    if( (rate > max) || (rate <min) ){
                        
                        valid = false;                        
                        $(this).addClass('validation-error');
                        $(this).next('span.validation-error').text('<?php echo __('Should be within Min and Max'); ?>');

                    }
                    
                }

            }
        });

        return valid ;
        
    }

    $(document).ready(function(){
        var mode	=	'edit';

        //Disable all fields
        $('#frmSave :input').attr("disabled", "disabled");
        
        //enable buttons
        $('#backBtn').removeAttr("disabled");
        <?php if (($isReviwer && ($performanceReview->getState() <= PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED)) || ( $isHrAdmin && $performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED)) { ?>
            $('#saveBtn').removeAttr("disabled");
        <?php } ?>

        <?php if ($isReviwer && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED)) { ?>
            $('#submitBtn').removeAttr("disabled");
        <?php } ?>

        <?php if ($isHrAdmin && $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) { ?>
            $('#rejectBtn').removeAttr("disabled");
        <?php } ?>

        <?php if ($isHrAdmin && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED )) { ?>
            $('#approveBtn').removeAttr("disabled");
        <?php } ?>          

        //When click edit button
        $("#saveBtn").click(function() {
            if( mode == 'edit')
            {
                $('#saveBtn').attr('value', "<?php echo __("Save") ?>");
                $('#frmSave :input').removeAttr("disabled");
                mode = 'save';
            } else {
                if(checkSubmit()){
                    $('#saveMode').val('save');
                    $('#frmSave').submit();
                }
            }
        });

        //When Submit button click
        $("#submitBtn").click(function() {
            $('#frmSave :input').removeAttr("disabled");
            if(checkSubmit()){
                $('#saveMode').val('submit');
                $('#frmSave').submit();
            }
        });

        //When Submit button click
        $("#rejectBtn").click(function() {
            $('#frmSave :input').removeAttr("disabled");                    
            $('#saveMode').val('reject');
            $('#frmSave').submit();
        });

        //When Submit button click
        $("#approveBtn").click(function() {
            $('#frmSave :input').removeAttr("disabled");
            $('#saveMode').val('approve');
            $('#frmSave').submit();
        });

        // Back button
        $("#backBtn").click(function() {
            location.href = "<?php echo url_for('performance/viewReview'); ?>";
        });

        $.validator.addMethod("minmax", function(value, element) {

            if($('#validRate').val() == '1' )
                return true;
            else
                return false;
        });

        //Check Reviwer comment
        $("#frmSave").delegate("keyup", "textarea.reviwerComment", function(event) {
            validateReviewerComment();
        });

        function validateReviewerComment() {

            var flag = true;

            $("textarea.reviwerComment").each(function() {
                if(this.value.length >= 2000 ){
                    flag = false;
                    $(this).addClass('validation-error');
                    $(this).next('span.validation-error').text('<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 2000)) ?>');                    
                }
            });
            
            var mainComment = $('#txtMainComment');
            
            if (mainComment.val().length > 250) {
                flag = false;
                mainComment.addClass('validation-error');
                mainComment.next('span.validation-error').text('<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)) ?>');
            }

            return flag;
            
        }

        //make sure all validations are performed before submit
        $("#frmSave").submit(function() {
            flag = validateReviewerComment();
            return flag;
        });
        
    });
</script>