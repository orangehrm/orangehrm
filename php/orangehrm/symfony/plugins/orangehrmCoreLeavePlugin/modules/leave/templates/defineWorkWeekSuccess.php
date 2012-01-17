<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
?>
<div id="messageBalloonContainer" style="width:380px;">
    <?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
</div>
<div class="formpageNarrow">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo __('Work Week'); ?></h2></div>

        <div id="errorDiv"> </div>
        <?php if ($workWeekForm->hasErrors()) { ?>
            <?php echo $workWeekForm['day_length_Monday']->renderError() ?>
            <?php echo $workWeekForm['day_length_Tuesday']->renderError() ?>
            <?php echo $workWeekForm['day_length_Wednesday']->renderError() ?>
            <?php echo $workWeekForm['day_length_Thursday']->renderError() ?>
            <?php echo $workWeekForm['day_length_Friday']->renderError() ?>
            <?php echo $workWeekForm['day_length_Saturday']->renderError() ?>
            <?php echo $workWeekForm['day_length_Sunday']->renderError() ?>
        <?php } ?>

        <form id="frmWorkWeek" name="frmWorkWeek" method="post" action="<?php echo url_for('leave/defineWorkWeek') ?>" >
            <?php echo $workWeekForm['_csrf_token']->render() ?>
            <br class="clear"/>
            <?php if ($workWeekForm->isCountryEnabled()) { ?>
                <?php echo $workWeekForm['operational_country']->renderLabel(__("Country ") . "<span class=\"required\">*</span>"); ?>
                <?php echo $workWeekForm['operational_country']->render(); ?>
                <br class="clear"/>
            <?php } ?>

            <?php echo $workWeekForm['day_length_Monday']->renderLabel(); ?>
            <?php echo $workWeekForm['day_length_Monday']->render(); ?>
            <br class="clear"/>

            <?php echo $workWeekForm['day_length_Tuesday']->renderLabel(); ?>
            <?php echo $workWeekForm['day_length_Tuesday']->render(); ?>
            <br class="clear"/>

            <?php echo $workWeekForm['day_length_Wednesday']->renderLabel(); ?>
            <?php echo $workWeekForm['day_length_Wednesday']->render(); ?>
            <br class="clear"/>

            <?php echo $workWeekForm['day_length_Thursday']->renderLabel(); ?>
            <?php echo $workWeekForm['day_length_Thursday']->render(); ?>
            <br class="clear"/>

            <?php echo $workWeekForm['day_length_Friday']->renderLabel(); ?>
            <?php echo $workWeekForm['day_length_Friday']->render(); ?>
            <br class="clear"/>

            <?php echo $workWeekForm['day_length_Saturday']->renderLabel(); ?>
            <?php echo $workWeekForm['day_length_Saturday']->render(); ?>
            <br class="clear"/>

            <?php echo $workWeekForm['day_length_Sunday']->renderLabel(); ?>
            <?php echo $workWeekForm['day_length_Sunday']->render(); ?>
            <br class="clear"/>


            <div class="formbuttons">
                <input type="button" class="savebutton" id="saveBtn" value="<?php echo __('Edit'); ?>" />
                <input type="button" class="clearbutton" onclick="reset();" value="<?php echo __('Reset'); ?>" />
            </div>
        </form>
    </div>
    <script type="text/javascript">
        //<![CDATA[

        $(document).ready(function() {
            $(".formSelect").attr("disabled", "disabled");
            
            $("#saveBtn").click(function() {                
                if($("#saveBtn").attr("value") == "<?php echo __("Edit") ?>") {
                    $(".formSelect").removeAttr("disabled");
                    $("#saveBtn").attr("value", "<?php echo __("Save") ?>");
                    return;
                }

                if($("#saveBtn").attr("value") == "<?php echo __("Save") ?>") {
                    
                    var count = 0   // Number of not working days
                    $('.formSelect').each(function(){
                        if($(this).find('option:selected').val() == 8) {
                            count = count + 1;
                        }
                    });
                    
                    if(count == 7) {
                        $('#messageBalloonContainer').empty();
                        $('#messageBalloonContainer').append("<div class=\"messageBalloon_warning\"><?php echo __("At Least One Day Should Be a Working Day") ?></div>");
                        $('.messageBalloon_warning').css('padding-left', '10px');
                    } else {                        
                         <?php if ($workWeekForm->isCountryEnabled()) { ?>
                                if($("#frmWorkWeek").validate()) {
                                   $("#frmWorkWeek").submit();  
                                   return; 
                                }
                         <?php }else {?>                        
                                  $("#frmWorkWeek").submit();
                                  $(".formSelect").attr("disabled", "disabled");
                                  return; 
                           <?php } ?>                             
                    }
                    
                }
            });
            
          <?php if ($workWeekForm->isCountryEnabled()) { ?>
            $.validator.addMethod('selectCountry', function(value, element) { 
               
                return (element.value != '0');
            }
        );

            $("#frmWorkWeek").validate({
                rules: {
                    'WorkWeek[operational_country]': { selectCountry: true }
                },
                messages: {
                    'WorkWeek[operational_country]': { selectCountry:'<?php echo __('Select a country') ?> '}
                }
            });
         <?php }?>
     
        });
        //]]>
    </script>
</div>
