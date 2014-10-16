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

<div class="box searchForm toggableForm" id="employee-information">
    <div class="head">
        <h1><?php echo __("Employee Information") ?></h1>
    </div>
    <div class="inner">
        <form id="search_form" name="frmEmployeeSearch" method="post" action="<?php echo url_for('@employee_list'); ?>">

            <fieldset>

                <ol>
                    <?php echo $form->render(); ?>
                </ol>

                <input type="hidden" name="pageNo" id="pageNo" value="" />
                <input type="hidden" name="hdnAction" id="hdnAction" value="search" />                 

                <p>
                    <input type="button" id="searchBtn" value="<?php echo __("Search") ?>" name="_search" />
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset" />                    
                </p>

            </fieldset>

        </form>

    </div> <!-- inner -->

    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>

</div> <!-- employee-information -->

<?php include_component('core', 'ohrmList'); ?>

<?php include_partial('global/delete_confirmation'); ?>

<script type="text/javascript">

    $(document).ready(function() {
        
        var supervisors = <?php echo str_replace('&#039;', "'", $form->getSupervisorListAsJson()) ?>;

        $('#btnDelete').attr('disabled', 'disabled');

        $("#ohrmList_chkSelectAll").click(function() {
            if ($(":checkbox").length == 1) {
                $('#btnDelete').attr('disabled', 'disabled');
            }
            else {
                if ($("#ohrmList_chkSelectAll").is(':checked')) {
                    $('#btnDelete').removeAttr('disabled');
                } else {
                    $('#btnDelete').attr('disabled', 'disabled');
                }
            }
        });

        $(':checkbox[name*="chkSelectRow[]"]').click(function() {
            if ($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
            } else {
                $('#btnDelete').attr('disabled', 'disabled');
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
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }
            , matchContains: true
        }).result(function(event, item) {
        }
        );

        $('#searchBtn').click(function() {
            $("#empsearch_isSubmitted").val('yes');
            $('#search_form input.inputFormatHint').val('');
            $('#search_form input.ac_loading').val('');
            $('#search_form').submit();
        });

        $('#resetBtn').click(function() {
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
        $('#btnDelete').click(function() {
            $('#frmList_ohrmListComponent').submit(function() {
                $('#deleteConfirmation').dialog('open');
                return false;
            });
        });

        /* Delete confirmation controls: Begin */
        $('#dialogDeleteBtn').click(function() {
            document.frmList_ohrmListComponent.submit();
        });
        /* Delete confirmation controls: End */

    }); //ready

    function submitPage(pageNo) {
        document.frmEmployeeSearch.pageNo.value = pageNo;
        document.frmEmployeeSearch.hdnAction.value = 'paging';
        $('#search_form input.inputFormatHint').val('');
        $('#search_form input.ac_loading').val('');
        $("#empsearch_isSubmitted").val('no');
        document.getElementById('search_form').submit();
    }
</script>
