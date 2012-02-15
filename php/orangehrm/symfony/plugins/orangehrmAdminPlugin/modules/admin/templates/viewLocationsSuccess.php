<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.8.13.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>
<?php use_stylesheet('../orangehrmAdminPlugin/css/viewLocationsSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/viewLocationsSuccess'); ?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div id="searchLocation">
    <div class="outerbox">
        <div class="mainHeading"><h2 id="searchLocationHeading"><?php echo __("Locations"); ?></h2></div>
        <form name="frmSearchLocation" id="frmSearchLocation" method="post" action="<?php echo url_for('admin/viewLocations'); ?>" >
            <?php echo $form['_csrf_token']; ?>

            <br class="clear"/>
            <div id="name" class="contentDiv">
                <?php echo $form['name']->renderLabel(__('Location Name')); ?>
                <?php echo $form['name']->render(array("class" => "txtBox")); ?>
                <br class="clear"/>
            </div>

            <div id="city" class="contentDiv">
                <?php echo $form['city']->renderLabel(__('City')); ?>
                <?php echo $form['city']->render(array("class" => "txtBox")); ?>
                <br class="clear"/>
            </div>

            <div id="country" class="contentDiv">
                <?php echo $form['country']->renderLabel(__('Country')); ?>
                <?php echo $form['country']->render(array("class" => "txtBox")); ?>
                <br class="clear"/>
            </div>
            <br class="clear"/>
            <br class="clear"/>
            <div class="actionbar" style="border-top: 1px solid #FAD163; margin-top: 3px">
                <div class="actionbuttons">
                    <input type="button" class="searchbutton" name="btnSave" id="btnSearch"
                           value="<?php echo __("Search"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                    <input type="button" class="resetbutton" name="btnReset" id="btnReset"
                           value="<?php echo __("Reset"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                </div>
                <br class="clear"/>
            </div>
            <br class="clear"/>
        </form>
    </div>
</div>
<div id="customerList">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>

<form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('admin/viewLocations'); ?>">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php //echo $form->pageNo; ?>" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>

<!-- confirmation box -->
<div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">

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
    var addLocationUrl = '<?php echo url_for('admin/location'); ?>';
    var viewLocationUrl = '<?php echo url_for('admin/viewLocations'); ?>';
</script>