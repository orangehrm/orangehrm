
<?php echo javascript_include_tag(plugin_web_path('orangehrmPimPlugin', 'js/viewUsTaxExemptionsSuccess')); ?>

<div class="box pimPane">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
        
    <?php if ($taxExemptionPermission->canRead()) { ?>
    <div class="head">
        <h1><?php echo __('Tax Exemptions'); ?></h1>
    </div>

    <div class="inner">

        <?php include_partial('global/flash_messages'); ?>

        <form id="frmEmpTaxExemptions" method="post" action="<?php echo url_for('pim/viewUsTaxExemptions'); ?>">
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['empNumber']->render(); ?>
            <fieldset>
                <ol>
                    <li>
                        <h2><?php echo __("Federal Income Tax") ?></h2>
                    </li>
                    <li>
                        <?php echo $form['federalStatus']->renderLabel(__("Status")); ?>
                        <?php echo $form['federalStatus']->render(array("class" => "drpDown")); ?>
                    </li>
                    <li>
                        <?php echo $form['federalExemptions']->renderLabel(__("Exemptions")); ?>
                        <?php echo $form['federalExemptions']->render(array("class" => "txtBox", "maxlength" => 2, 'size' => 10)); ?>
                    </li>
                    <li>
                        <h2><?php echo __("State Income Tax") ?></h2>
                    </li>
                    <li>
                        <?php echo $form['state']->renderLabel(__("State")); ?>
                        <?php echo $form['state']->render(array("class" => "drpDown")); ?>
                    </li>
                    <li>
                        <?php echo $form['stateStatus']->renderLabel(__("Status")); ?>
                        <?php echo $form['stateStatus']->render(array("class" => "drpDown")); ?>
                    </li>
                    <li>
                        <?php echo $form['stateExemptions']->renderLabel(__("Exemptions")); ?>
                        <?php echo $form['stateExemptions']->render(array("class" => "txtBox", "maxlength" => 2, 'size' => 10)); ?>
                    </li>
                    <li>
                        
                    </li>
                    <li>
                        <?php echo $form['unempState']->renderLabel(__("Unemployment State")); ?>
                        <?php echo $form['unempState']->render(array("class" => "drpDown")); ?>
                    </li>
                    <li>
                        <?php echo $form['workState']->renderLabel(__("Work State")); ?>
                        <?php echo $form['workState']->render(array("class" => "drpDown")); ?>
                    </li>
                </ol>
                <p>
                    <?php if ($taxExemptionPermission->canUpdate()) { ?>
                    <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Edit"); ?>" tabindex="2" />
                    <?php } ?>
                </p>
            </fieldset>
        </form>
    </div> <!-- inner -->
    <?php } ?>
    
    <?php 
    echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => CustomField::SCREEN_TAX_EXEMPTIONS)); 
    echo include_component('pim', 'attachments', array('empNumber' => $empNumber, 'screen' => EmployeeAttachment::SCREEN_TAX_EXEMPTIONS));
    ?>
    
</div> <!-- Box -->

<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_save = "<?php echo __("Save"); ?>";
    var lang_negativeAmount = "<?php echo __("Should be a positive number"); ?>";
    var lang_tooLargeAmount = "<?php echo __("Should be less than %amount%", array("%amount%" => 99)); ?>";
    var enterANumber = "<?php echo __("Enter a number"); ?>";
    var fileModified = 0;
    //]]>
</script>
