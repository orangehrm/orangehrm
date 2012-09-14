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

<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_stylesheet('../orangehrmPimPlugin/css/viewEmployeeListSuccess');
use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>

<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<?php if ($form->hasErrors() || $sf_user->hasFlash('success') || $sf_user->hasFlash('error')): ?>
    <div class="messagebar">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
        <?php include_partial('global/flash_messages', array('sf_user' => $sf_user)); ?>
    </div>
<?php endif; ?>

<div class="outerbox">

    <div class="mainHeading">
        <h2><?php echo __("Employee Information") ?></h2>
    </div>

    <div class="searchbox">
        <form id="search_form" name="frmEmployeeSearch" method="post" action="<?php echo url_for('@employee_list'); ?>">
            <div id="formcontent">
                <br class="clear"/>
                <?php echo $form->render(); ?>  

                <div class="actionbar">
                    <div class="actionbuttons">
                        <input
                            type="button" class="plainbtn" id="searchBtn"
                            onmouseover="this.className='plainbtn plainbtnhov'"
                            onmouseout="this.className='plainbtn'" value="<?php echo __("Search") ?>" name="_search" />
                        <input
                            type="button" class="plainbtn"
                            onmouseover="this.className='plainbtn plainbtnhov'" id="resetBtn"
                            onmouseout="this.className='plainbtn'" value="<?php echo __("Reset") ?>" name="_reset" />

                    </div>
                    <br class="clear" />
                </div>
                <br class="clear" />
            </div>
            <input type="hidden" name="pageNo" id="pageNo" value="" />
            <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
        </form>
    </div>
</div> <!-- outerbox -->

<?php include_component('core', 'ohrmList'); ?>

<!-- confirmation box -->
<div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">
    <?php echo __(CommonMessages::DELETE_CONFIRMATION); ?>
    <div class="dialogButtons">        
        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {

        var supervisors = <?php echo str_replace('&#039;', "'", $form->getSupervisorListAsJson()) ?>;
        
        $('#btnDelete').attr('disabled', 'disabled');
        
        $("#ohrmList_chkSelectAll").click(function() {
            if($(":checkbox").length == 1) {
                $('#btnDelete').attr('disabled','disabled');
            }
            else {
                if($("#ohrmList_chkSelectAll").is(':checked')) {
                    $('#btnDelete').removeAttr('disabled');
                } else {
                    $('#btnDelete').attr('disabled','disabled');
                }
            }
        });
        
        $(':checkbox[name*="chkSelectRow[]"]').click(function() {
            if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled','disabled');
            }
        });

        // Handle hints
        if ($("#empsearch_id").val() == '') {
            $("#empsearch_id").val('<?php echo __("Type Employee Id") . "..."; ?>')
            .addClass("inputFormatHint");
        }

        if ($("#empsearch_supervisor_name").val() == '') {
            $("#empsearch_supervisor_name").val('<?php echo __("Type for hints") . "..."; ?>')
            .addClass("inputFormatHint");
        }

        $("#empsearch_id, #empsearch_supervisor_name").one('focus', function() {

            if ($(this).hasClass("inputFormatHint")) {
                $(this).val("");
                $(this).removeClass("inputFormatHint");
            }
        });

        $("#empsearch_supervisor_name").autocomplete(supervisors, {
            formatItem: function(item) {
                return item.name;
            }
            ,matchContains:true
        }).result(function(event, item) {
        }
    );

        $('#searchBtn').click(function() {
            $("#empsearch_isSubmitted").val('yes');
            $('#search_form input.inputFormatHint').val('');
            $('#search_form').submit();
        });

        $('#resetBtn').click(function(){
            $("#empsearch_isSubmitted").val('yes');
            $("#empsearch_employee_name_empName").val('');
            $("#empsearch_supervisor_name").val('');
            $("#empsearch_id").val('');
            $("#empsearch_job_title").val('0');
            $("#empsearch_employee_status").val('0');
            $("#empsearch_sub_unit").val('0');
            $("#empsearch_termination").val('<?php echo EmployeeSearchForm::WITHOUT_TERMINATED; ?>');
            $('#search_form').submit();
        });

        $('#btnAdd').click(function() {
            location.href = "<?php echo url_for('pim/addEmployee') ?>";
        });
        $('#btnDelete').click(function(){
            $('#frmList_ohrmListComponent').submit(function(){
                $('#deleteConfirmation').dialog('open');
                return false;
            });
        });

        $("#deleteConfirmation").dialog({
            autoOpen: false,
            modal: true,
            width: 325,
            height: 50,
            position: 'middle',
            open: function() {
                $('#dialogCancelBtn').focus();
            }
        });

        $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
        $('#dialogDeleteBtn').click(function() {
            document.frmList_ohrmListComponent.submit();
        });
        $('#dialogCancelBtn').click(function() {
            $("#deleteConfirmation").dialog("close");
        });

    }); //ready
    
    function submitPage(pageNo) {
        document.frmEmployeeSearch.pageNo.value = pageNo;
        document.frmEmployeeSearch.hdnAction.value = 'paging';
        $('#search_form input.inputFormatHint').val('');
        $("#empsearch_isSubmitted").val('no');
        document.getElementById('search_form').submit();
    }   
</script>
