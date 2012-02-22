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

<?php use_stylesheet('../orangehrmCoreLeavePlugin/css/defineWorkWeekSuccess'); ?>
<?php use_javascripts_for_form($workWeekForm); ?>
<?php use_stylesheets_for_form($workWeekForm); ?>

<div id="messageBalloonContainer" style="width:380px;">
    <?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
</div>
<div class="formpageNarrow">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo __('Work Week'); ?></h2></div>

        <div id="errorDiv"></div>
        
        <form id="frmWorkWeek" name="frmWorkWeek" method="post" action="<?php echo url_for('leave/defineWorkWeek') ?>" >            
            <?php echo $workWeekForm->render() ?>
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
            $('.formSelect[name^="WorkWeek[day_length"]').attr('disabled', true);
            
            $('#saveBtn').click(function() {                
                if($(this).val() == "<?php echo __('Edit') ?>") {
                    $('.formSelect').attr('disabled', false);
                    $(this).val("<?php echo __('Save') ?>");
                    return;
                }

                if($(this).val() == "<?php echo __('Save') ?>") {
                    
                    var noOfWorkingDays = 0;
                    $('.formSelect').each(function(){
                        if($(this).find('option:selected').val() == 8) {
                            noOfWorkingDays = noOfWorkingDays + 1;
                        }
                    });
                    
                    if(noOfWorkingDays == 7) {
                        $('#messageBalloonContainer').empty();
                        $('#messageBalloonContainer').append("<div class=\"messageBalloon_warning\"><?php echo __('At Least One Day Should Be a Working Day') ?></div>");
                        $('.messageBalloon_warning').css('padding-left', '10px');
                    } else {
                        $('#frmWorkWeek').submit();
                        $(".formSelect").attr('disabled', true);
                        return;                    
                    }
                    
                }
            });            
          
        });
        //]]>
    </script>
</div>
