<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js')?>"></script>

<?php use_stylesheet('../orangehrmAdminPlugin/css/localizationSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/localizationSuccess'); ?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>

<div id="localization">
    <div class="outerbox">
        <div class="mainHeading"><h2 id="localizationHeading"><?php echo __('Localization'); ?></h2></div>
        <form name="frmLocalization" id="frmLocalization" method="post" action="<?php echo url_for("admin/localization")?>">
            <?php echo $form['_csrf_token']; ?>

            <br class="clear"/>

            <?php echo $form['dafault_language']->renderLabel(__('Language')); ?>
            <?php echo $form['dafault_language']->render(array("class" => "drpDown")); ?>
            <br class="clear"/>
            <div id="chkBrowserLang">
                <?php echo $form['use_browser_language']->render(array("class" => "formSelect")); ?>
                <?php echo $form['use_browser_language']->renderLabel(__('Use browser language if set')." ( "."<a href=\"javascript:openDialogue()\">".__('Supported languages')."</a> )"); ?>
                <br class="clear"/>
            </div>

            <?php echo $form['default_date_format']->renderLabel(__('Date Format')); ?>
            <?php echo $form['default_date_format']->render(array("class" => "drpDown")); ?>
                <br class="clear"/>

                <div class="formbuttons">
                    <input type="button" class="savebutton" name="btnSave" id="btnSave"
                           value="<?php echo __("Edit"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                </div>

            </form>
        </div>
    </div>

<div id="languageDialog" title="<?php echo __('OrangeHRM - Supported Languages')?>"  style="display:none;">
<?php echo __("Following languages are supported")?>
    <br class="clear"/>
    <br class="clear"/>
<?php $languages = $form->getLanguages()?>
    <ul>
<?php foreach($languages as $lang){
    echo  "<li>".$lang."</li>";
}
        ?>
        </ul>
<div class="dialogButtons">
    <input type="button" id="dialogOk" class="savebutton" value="<?php echo __('Ok');?>" />
</div>
</div>

<div class="paddingLeftRequired"><a href="http://www.orangehrm.com/localization-help.shtml" target="_blank"><?php echo __('Language and font help');?></a></div>

    <script type="text/javascript">
        //<![CDATA[    
        var lang_edit = "<?php echo __("Edit"); ?>";
        var lang_save = "<?php echo __("Save"); ?>";
        var browserLanguage = "<?php echo $browserLanguage ?>";
        var reloadParent = <?php echo isset($templateMessage)?'true':'false'; ?>;
    //]]>
</script>