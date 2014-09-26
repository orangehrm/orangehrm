
<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/localizationSuccess')); ?>

<div id="localization" class="box">
    
    <div class="head">
        <h1 id="localizationHeading"><?php echo __('Localization'); ?></h1>
    </div>
    
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>
        
        <form name="frmLocalization" id="frmLocalization" method="post" action="<?php echo url_for("admin/localization")?>">
            
            <?php echo $form['_csrf_token']; ?>

            <fieldset>
                
                <ol>
                    
                    <li>
                        <?php echo $form['dafault_language']->renderLabel(__('Language')); ?>
                        <?php echo $form['dafault_language']->render(); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['use_browser_language']->render(array("class" => "")); ?>
                        <?php echo $form['use_browser_language']->renderLabel(__('Use browser language if set') . " ( " . 
                                "<a class='btn1' data-toggle='modal' href='#languageDialog'>" . __('Supported languages') . "</a>" . " )"); ?>
                    </li>
                    
                    <li>
                        <?php echo $form['default_date_format']->renderLabel(__('Date Format')); ?>
                        <?php echo $form['default_date_format']->render(); ?>
                    </li>
                    
                    <li class="helpText">
                        <a href="http://www.orangehrm.com/localization-help.shtml" target="_blank"><?php echo __('Language and font help'); ?></a>
                    </li>
                    
                </ol>
                
                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Edit"); ?>"/>
                </p>
                
            </fieldset>
            
        </form>
        
    </div>
    
</div>

<!-- Message for supported languages -->
<div class="modal hide" id="languageDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Supported Languages')?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __("Following languages are supported")?></p>
        <?php $languages = $form->getLanguages()?>
        <ul>
            <?php foreach($languages as $lang) {
                echo  "<li>".$lang."</li>";
            } ?>
        </ul>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" value="<?php echo __('Ok'); ?>" />
    </div>
</div>
<!-- End-of-msg -->

<script type="text/javascript">
    //<![CDATA[    
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_save = "<?php echo __("Save"); ?>";
    //]]>
</script>