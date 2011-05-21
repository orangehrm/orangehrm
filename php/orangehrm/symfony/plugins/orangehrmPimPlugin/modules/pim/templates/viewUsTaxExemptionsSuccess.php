<?php echo stylesheet_tag('../orangehrmPimPlugin/css/viewUsTaxExemptionsSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmPimPlugin/js/viewUsTaxExemptionsSuccess'); ?>

<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    //]]>
</script>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 550px;">
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div class="outerbox">
    <div class="mainHeading"><h2><?php echo __('Tax Exemptions'); ?></h2></div>
    <div>
        <form id="frmEmpTaxExemptions" method="post" action="<?php echo url_for('pim/viewUsTaxExemptions'); ?>">
            <?php echo $taxExemptionForm['_csrf_token']; ?>
            <?php echo $taxExemptionForm['empNumber']->render(); ?>
            <br />
            <span class="label"><?php echo __("Federal Income Tax") ?></span>
            <div>
                <?php echo $taxExemptionForm['federalStatus']->renderLabel(__("Marital Status")); ?>
                <?php echo $taxExemptionForm['federalStatus']->render(array("class" => "drpDown")); ?>
                <br class="clear" />
            </div>
            <div>
                <?php echo $taxExemptionForm['federalExemptions']->renderLabel(__("Exemptions")); ?>
                <?php echo $taxExemptionForm['federalExemptions']->render(array("class" => "txtBox", "maxlength" => 70, 'size' => 10)); ?>
                <br class="clear" />
                <br class="clear" />
                <br class="clear" />
            </div>
            <span class="label"><?php echo __("State Income Tax") ?></span>
            <div>
                <?php echo $taxExemptionForm['state']->renderLabel(__("State")); ?>
                <?php echo $taxExemptionForm['state']->render(array("class" => "drpDown")); ?>
                <br class="clear" />
            </div>
            <div>
                <?php echo $taxExemptionForm['stateStatus']->renderLabel(__("Marital Status")); ?>
                <?php echo $taxExemptionForm['stateStatus']->render(array("class" => "drpDown")); ?>
                <br class="clear" />
            </div>
            <div>
                <?php echo $taxExemptionForm['stateExemptions']->renderLabel(__("Exemptions")); ?>
                <?php echo $taxExemptionForm['stateExemptions']->render(array("class" => "txtBox", "maxlength" => 70, 'size' => 10)); ?>
                <br class="clear" />
                <br class="clear" />
                <br class="clear" />
            </div>
            <div>
                <?php echo $taxExemptionForm['unempState']->renderLabel(__("Unemployment State")); ?>
                <?php echo $taxExemptionForm['unempState']->render(array("class" => "drpDown")); ?>
                <br class="clear" />
            </div>
            <div>
                <?php echo $taxExemptionForm['workState']->renderLabel(__("Work State")); ?>
                <?php echo $taxExemptionForm['workState']->render(array("class" => "drpDown")); ?>
                <br class="clear" />
            </div>
            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Edit"); ?>" tabindex="2" />
            </div>
        </form>
    </div>
</div>
