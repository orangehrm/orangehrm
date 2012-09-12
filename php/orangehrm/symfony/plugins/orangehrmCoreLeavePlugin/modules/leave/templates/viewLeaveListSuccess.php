<?php
/*
 *
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
 *
 */

$messageType = empty($messageType) ? '' : "messageBalloon_{$messageType}";
$searchActionButtons = $form->getSearchActionButtons();

use_stylesheet('orangehrm.datepicker.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');

use_stylesheets_for_form($form);

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.datepicker.js');
use_javascript('../../../scripts/jquery/ui/ui.draggable.js');
use_javascript('../../../scripts/jquery/ui/ui.resizable.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
use_javascript('orangehrm.datepicker.js');

use_javascripts_for_form($form);
?>


<?php if ($messageType == "messageBalloon_notice") { ?>
    <div class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php } ?>
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __($form->getTitle()); ?></h2></div>

    <div class="formWrapper">
        <form id="frmFilterLeave" name="frmFilterLeave" method="post" action="<?php echo url_for($baseUrl); ?>">

            <?php echo $form->render(); ?>
            <br class="clear" />

            <div class="buttonWrapper">
                <?php
                foreach ($searchActionButtons as $id => $button) {
                    echo $button->render($id), "\n";
                }
                ?>
                <?php include_component('core', 'ohrmPluginPannel', array('location' => 'listing_layout_navigation_bar_1')); ?>
                <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $form->pageNo; ?>" />
                <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
            </div>
        </form>
    </div>

</div> <!-- End of outerbox -->

<?php if ($messageType == "messageBalloon_success") {
    ?>
    <div id="leaveListActionMsg" class="<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php } ?>
<div id="processing"></div>
<!--this is ajax message place -->
<div id="ajaxCommentSaveMsg"></div>
<!-- end of ajax message place -->

<?php include_component('core', 'ohrmList'); ?>

<!-- comment dialog -->
<div id="commentDialog" title="<?php echo __('Leave Comment'); ?>">
    <form action="updateComment" method="post" id="frmCommentSave">
        <input type="hidden" id="leaveId" />
        <input type="hidden" id="leaveOrRequest" />
        <textarea name="leaveComment" id="leaveComment" cols="40" rows="10" class="commentTextArea"></textarea>
        <br class="clear" />
        <div class="error" id="commentError"></div>
        <div><input type="button" id="commentSave" class="plainbtn" value="<?php echo __('Edit'); ?>" />
            <input type="button" id="commentCancel" class="plainbtn" value="<?php echo __('Cancel'); ?>" /></div>
    </form>
</div>
<!-- end of comment dialog-->

<script type="text/javascript">
    //<![CDATA[
    var lang_typeHint = "<?php echo __("Type for hints"); ?>" + "...";
    var resetUrl = '<?php echo url_for($baseUrl . '?reset=1'); ?>';
    var commentUpdateUrl = '<?php echo public_path('index.php/leave/updateComment'); ?>';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_comment_successfully_saved = '<?php echo __(TopLevelMessages::SAVE_SUCCESS); ?>';
    var lang_edit = '<?php echo __('Edit'); ?>';
    var lang_save = '<?php echo __('Save'); ?>';
    var lang_length_exceeded_error = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';    
    var lang_selectAction = '<?php echo __("Select Action"); ?>';
    var leave_status_pending = '<?php echo PluginLeave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL; ?>';
    var ess_mode = '<?php echo ($essMode) ? '1' : '0'; ?>';
    
    function submitPage(pageNo) {
        //    location.href = '<?php //echo url_for($baseUrl . '?pageNo=');  ?>' + pageNo;
        document.frmFilterLeave.pageNo.value = pageNo;
        document.frmFilterLeave.hdnAction.value = 'paging';
        if ($('#leaveList_txtEmployee_empName').val() == lang_typeHint) {
            $('#leaveList_txtEmployee_empName').val('');
        }
        document.getElementById('frmFilterLeave').submit();        
    }
    
    $('#processing').html('');

    function handleSaveButton() {
        $('#processing').html('');
        $('#messageBalloon_success').remove();
        $('#messageBalloon_warning').remove();
        $('#leaveListActionMsg').html('');
        $(this).attr('disabled', true);
        
        $('#noActionsSelectedWarning').remove();
        $('#leaveListActionMsg').remove();
        $('#ajaxCommentSaveMsg').removeAttr('class').html('');
              
        var selectedActions = 0;
        
        $('select[name^="select_leave_action_"]').each(function() {
            var id = $(this).attr('id').replace('select_leave_action_', '');
            if ($(this).val() == '') {
                $('#hdnLeaveRequest_' + id).attr('disabled', true);
            } else {
                selectedActions++;
                $('#hdnLeaveRequest_' + id).attr('disabled', false);                
                $('#hdnLeaveRequest_' + id).val($(this).val());
            }

            if ($(this).val() == '') {
                $('#hdnLeave_' + id).attr('disabled', true);
            } else {
                $('#hdnLeave_' + id).attr('disabled', false); 
                $('#hdnLeave_' + id).val($(this).val());
            }
        });  
    
        if (selectedActions > 0) {
            $('#processing').html('<div class="messageBalloon_success">'+"<?php echo __('Processing'); ?>"+'...</div>');
            $('#frmList_ohrmListComponent').submit();
        } else {
            $('div#ajaxCommentSaveMsg').before('<div id="noActionsSelectedWarning" class="messageBalloon_warning"></div>');
            $('#noActionsSelectedWarning').text(lang_selectAction);
            $(this).attr('disabled', false);      
            return false;
        }
    }

    function setPage() {}


    
    //]]>
</script>