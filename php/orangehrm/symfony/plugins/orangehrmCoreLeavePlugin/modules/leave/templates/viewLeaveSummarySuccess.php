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
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.js') ?>"></script>

<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css') ?>" rel="stylesheet" type="text/css"/>

<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/viewLeaveSummarySuccess'); ?>

 <!-- 9706 <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js') ?>"></script>-->

<!--[if IE]>
<style type="text/css">
    #leaveSummary_txtEmpName {
        width: 195px;
    }
</style>
<![endif]-->
<style type="text/css">
    label.error {
        padding: 0;
        text-align: center;
    }
</style>
<form id="frmLeaveSummarySearch" name="frmLeaveSummarySearch" method="post" action="<?php echo url_for('leave/viewLeaveSummary'); ?>">
    <div class="outerbox" style="width: 850px;">
        <div class="mainHeading"><h2><?php echo __('Leave Summary') ?></h2></div>
        <div class="searchbar">
            <?php echo $form->render(); ?>
            <br class="clear" />

            <div class="formbuttons paddingLeftBtn">
                <input type="hidden" name="leavePeriodId" id="leavePeriodId" value="<?php echo $leavePeriodId; ?>" />
                <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $form->pageNo; ?>" />
                <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
                <input type="button" name="btnSearch" id="btnSearch" value="<?php echo __('Search') ?>" class="savebutton" />
                <?php if ($form->userType == 'Admin' || $form->userType == 'Supervisor') { ?>
                    <input type="reset" id="btnReset" value="<?php echo __('Reset') ?>" class="savebutton" />
                <?php } ?>
                <?php include_component('core', 'ohrmPluginPannel', array('location' => 'listing_layout_navigation_bar_1')); ?>
            </div>
    
        </div>
    </div>
    <?php echo templateMessage($templateMessage); ?>

    <div id="validationMsg"></div>

    <?php include_component('core', 'ohrmList'); ?>

</form>
<script type="text/javascript">
    
    var lang_typeHint = "<?php echo __("Type for hints"); ?>" + "...";
    
    /* Define language strings here */
    var lang_not_numeric = '<?php echo __(ValidationMessages::INVALID); ?>';
    var userType = '<?php echo $form->userType; ?>';
    
    $(document).ready(function() {
        
        if ($("#leaveSummary_txtEmpName").val() == "" || $("#leaveSummary_txtEmpName").val() == lang_typeHint) {
            $("#leaveSummary_txtEmpName").addClass("inputFormatHint").val(lang_typeHint);
        }
        
        $("#leaveSummary_txtEmpName").one('focus', function() {
            if ($(this).hasClass("inputFormatHint")) {
                $(this).val("");
                $(this).removeClass("inputFormatHint");
            }
        });
        
        /* 9706
        $("#frmLeaveSummarySearch").validate({
            onsubmit : false,
            rules: {
                'txtLeaveEntitled[]':{validateAmount: true, max: 365 }
            },
            messages: {
                'txtLeaveEntitled[]':{
                    validateAmount: lang_not_numeric,
                    max: lang_not_numeric
                }
            }
        });
        */
        
        /* Valid amount */
        /* 9706
        $.validator.addMethod("validateAmount", function(value, element) {
            if(value != '') {
                return value.match(/^\d+(?:\.\d\d?)?$/);
            } else {
                return true;
            }
        });
        */
        
    });

    function submitPage(pageNo) {

        document.frmLeaveSummarySearch.pageNo.value = pageNo;
        document.frmLeaveSummarySearch.hdnAction.value = 'paging';
        if ($('#leaveSummary_txtEmpName_empName').val() == lang_typeHint) {
            $('#leaveSummary_txtEmpName_empName').val('');
        }
        document.getElementById('frmLeaveSummarySearch').submit();
    }


    var editButtonCaption = "<?php echo __('Edit'); ?>";
    var saveButtonCaption = "<?php echo __('Save'); ?>";
</script>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>
