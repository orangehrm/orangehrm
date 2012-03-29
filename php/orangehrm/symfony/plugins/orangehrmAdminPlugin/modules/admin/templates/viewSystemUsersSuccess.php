<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */
?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.8.13.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>
<?php use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css'); ?>
<?php use_javascript('../../../scripts/jquery/jquery.autocomplete.js'); ?>
<?php use_stylesheet('../orangehrmAdminPlugin/css/viewSystemUserSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/viewSystemUserSuccess'); ?>


<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="searchProject">
    <div class="outerbox">

        <div class="mainHeading">
            <h2><?php echo __("System Users") ?></h2>
        </div>

        <div class="searchbox">
            <form id="search_form" method="post" action="<?php echo url_for('admin/viewSystemUsers'); ?>">
                <div id="formcontent">

                    <?php
                    echo $form->render();
                    ?>
                    <div class="errorHolder"></div>                    
                </div>
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
            </form>
        </div>
    </div>
</div>

<div id="customerList">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>

<form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('admin/viewSystemUsers'); ?>">
    <input type="hidden" name="pageNo" id="pageNo" value="" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>

<!-- confirmation box -->
<div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">

    <br class="clear"/>

    <?php echo __(CommonMessages::DELETE_CONFIRMATION); ?>

    <div class="dialogButtons">
        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>

<script type="text/javascript">
    function submitPage(pageNo) {

        document.frmHiddenParam.pageNo.value = pageNo;
        document.frmHiddenParam.hdnAction.value = 'paging';
        document.getElementById('frmHiddenParam').submit();

    }
                
    var addUserUrl          =   '<?php echo url_for('admin/saveSystemUser'); ?>';
    var viewUserUrl          =   '<?php echo url_for('admin/viewSystemUsers'); ?>';
    var lang_typeforhint    =   '<?php echo __("Type for hints") . "..."; ?>';
    var user_ValidEmployee  =   '<?php echo __(ValidationMessages::INVALID); ?>';

</script>