<?php //use_stylesheets_for_form($form);  ?>
<style>

</style>
<div id="messagebar" class="messageBalloon_<?php echo $messageType; ?>" >
    <span><?php echo (!empty($messageType)) ? $message : ""; ?></span>
</div>

<div id="location" class="box">
    <div class="head">
        <h1 id="PerformanceHeading"><?php echo __("Key Performance Indicator"); ?></h1>
    </div>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>

        <form name="searchKpi" id="searchKpi" method="post" action="" >

            <fieldset>

                <ol>
                    <?php echo $form->render(); ?>

                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>

                <p>
                    <input type="button" class="addbutton" name="saveBtn" id="saveBtn" value="<?php echo __("Save"); ?>"/>
                    <input id="btnCancel" class="reset" type="button" value="<?php echo __("Cancel"); ?>" name="btnCancel">
                </p>

            </fieldset>

        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        
        $('#saveBtn').click(function(){
            $('#searchKpi').submit();
        });
        
        $("#searchKpi").validate({
            rules: {
                'defineKpi360[jobTitleCode]':{required: true },
                'defineKpi360[keyPerformanceIndicators]':{required: true, maxlength:100 },
                'defineKpi360[minRating]':{ required: true, min: 0, max:100, number:true,  positiveNumber: true, maxMinValidation: true },
                'defineKpi360[maxRating]':{ required: true, min: 0, max:100, number:true,  positiveNumber: true, maxMinValidation: true } 
            },
            messages: {
                'defineKpi360[jobTitleCode]':{
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'defineKpi360[keyPerformanceIndicators]':{
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                    maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>'
                },
                'defineKpi360[minRating]':{
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                    number:'<?php echo __(ValidationMessages::VALID_NUMBER); ?>',
                    min:'<?php echo __(ValidationMessages::GREATER_THAN, array('%number%' => 0)); ?>',
                    max:'<?php echo __(ValidationMessages::LESS_THAN, array('%number%' => 100)); ?>',
                    maxMinValidation:'<?php echo __(PerformanceValidationMessages::MAX_SHOULD_BE_GREATER_THAN_MIN); ?>'
                    
                },
                'defineKpi360[maxRating]':{
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                    number:'<?php echo __(ValidationMessages::VALID_NUMBER); ?>',
                    min:'<?php echo __(ValidationMessages::GREATER_THAN, array('%number%' => 0)); ?>',
                    max:'<?php echo __(ValidationMessages::LESS_THAN, array('%number%' => 100)); ?>',
                    maxMinValidation:'<?php echo __(PerformanceValidationMessages::MAX_SHOULD_BE_GREATER_THAN_MIN); ?>'
                    
                }
            }
        });
        
        $.validator.addMethod('positiveNumber',
        function (value) { 
            if(value>=0 && parseInt(value) >= 0){
                return /^[0-9][0-9]*$/.test(value);    
            } else {
                return false;
            } 
        }, '<?php echo __(PerformanceValidationMessages::ONLY_INTEGER_ALLOWED); ?>');
        
        $.validator.addMethod('jobOrDepartmentValidation',
        function (value) { 
                
            if($('#defineKpi360_jobTitleCode').val() >0 || $('#defineKpi360_department').val() > 0){
                return true
            } else {
                return false;
            }
        });

        $.validator.addMethod('maxMinValidation',
        function (value) { 
            if($('#defineKpi360_maxRating').val() !='' && value>0){
                if( parseInt($('#defineKpi360_maxRating').val()) >  parseInt($('#defineKpi360_minRating').val()) ){                       
                    return true;
                } else {                       
                    return false;
                }
            } else {                  
                return true;
            } 
        });
       
        
        $('#saveBtn').click(function(){           
            $('#kpiGroup').submit();
        });
        
        $('#btnCancel').click(function(){
            window.location.replace('<?php echo public_path('index.php/performance/searchKpi'); ?>');
        });
    });
</script>