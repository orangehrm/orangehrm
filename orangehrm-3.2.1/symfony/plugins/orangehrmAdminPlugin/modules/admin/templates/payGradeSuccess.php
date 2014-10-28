
<?php 
use_javascript('jquery/jquery.form');
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/payGradeSuccess')); 
?>

<?php $hasCurrencies = !empty($currencyList) && count($currencyList) > 0; ?>

<?php if($payGradePermissions->canRead()){ ?>
<div id="payGrade" class="box">
    
    <div class="head">
        <h1 id="payGradeHeading"><?php echo $title; ?></h1>
    </div>
    
    <div class="inner">
        
        <?php include_partial('global/flash_messages', array('prefix' => 'paygrade')); ?>

        <form name="frmPayGrade" id="frmPayGrade" method="post" action="<?php echo url_for('admin/payGrade'); ?>" >
            
            <fieldset>

                <ol>
                    
                    <?php echo $form->render(); ?>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    
                </ol>

                <p>
                    <?php if(($payGradePermissions->canCreate() && empty($payGradeId)) || ($payGradePermissions->canUpdate() && ($payGradeId > 0))){?>
                    <input type="button" class="addbutton" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <?php }?>
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>

        </form>
        
    </div>

</div>

<?php if ($payGradeId > 0) {
 ?>
<div id="addEditCurrency" class="box">
    
    <div class="head">
        <h1 id="currencyHeading"><?php echo __("Assigned Currencies"); ?></h1>
    </div>
    
    <div class="inner">
        
        <form name="frmCurrency" id="frmCurrency" method="post" action="<?php echo url_for('admin/savePayGradeCurrency?payGradeId=' . $payGradeId); ?>">
            <?php echo $currencyForm['_csrf_token']; ?>
            <?php echo $currencyForm->renderHiddenFields(); ?>

            <fieldset>
                
                <ol id="addPaneCurrency" style="display: none">
                    
                    <li>
                        <?php echo $currencyForm['currencyName']->renderLabel(__('Currency') . ' <em>*</em>'); ?>
                        <?php echo $currencyForm['currencyName']->render(array("class" => "formInput", "maxlength" => 52)); ?>
                        <span class="errorHolder curName"></span>
                    </li>
                    
                    <li>
                        <?php echo $currencyForm['minSalary']->renderLabel(__('Minimum Salary')); ?>
                        <?php echo $currencyForm['minSalary']->render(array("class" => "formInput", "maxlength" => 52)); ?>
                    </li>
                    
                    <li>
                        <?php echo $currencyForm['maxSalary']->renderLabel(__('Maximum Salary')); ?>
                        <?php echo $currencyForm['maxSalary']->render(array("class" => "formInput", "maxlength" => 52)); ?>
                    </li>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    
                </ol>    
                
                <p id="actionButtons" style="display: none">
                    <?php if($payGradePermissions->canUpdate()){?>
                    <input type="button" class="addbutton" name="btnSaveCurrency" id="btnSaveCurrency" value="<?php echo __("Save"); ?>"/>
                    <?php }?>
                    <input type="button" class="reset" id="cancelButton" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>

        </form>
        
    </div>
    
</div>

<a id="Currencies"></a>
<div id="currency" class="box miniList">
    
    <div class="head">
        <h1 id="currencyListHeading"><?php echo __("Assigned Currencies"); ?></h1>
    </div>
    
    <div class="inner"> 
        
        <?php include_partial('global/flash_messages'); ?>
        
        <form name="frmDelCurrencies" id="frmDelCurrencies" method="post" action="<?php echo url_for('admin/deletePayGradeCurrency?payGradeId=' . $payGradeId); ?>">
            
            <?php echo $deleteForm['_csrf_token']; ?>
            
            <p id="addDeleteBtnDiv">
                <?php if ($payGradePermissions->canUpdate()) {?>
                <input type="button" class="addbutton" id="btnAddCurrency" value="<?php echo __("Add"); ?>"/>
                <?php if ($hasCurrencies) { ?>
                
                <input type="button" class="delete" id="btnDeleteCurrency" value="<?php echo __("Delete"); ?>"/>
                    
                <?php } }?>
            </p>
            
            <table class="table hover" id="tblCurrencies">
                <thead>
                    <tr>
                        <?php if ($payGradePermissions->canUpdate()) {?>
                        <th class="check" style="width:2%"><input type="checkbox" id="currencyCheckAll" class="checkboxCurr"/></th>
                        <?php }?>
                        <th style="width:40%"><?php echo __("Currency") ?></th>
                        <th style="width:34%"><?php echo __("Minimum Salary") ?></th>
                        <th style="width:34%"><?php echo __("Maximum Salary") ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($hasCurrencies) {
                    $row = 0;
                    $cssClass = ($row % 2) ? 'even' : 'odd';
                    foreach ($currencyList as $currency) {
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                ?>
                        <tr class="<?php echo $cssClass; ?>">
                            <?php if ($payGradePermissions->canUpdate()){?>
                            <td class="check"><input type='checkbox' class='checkboxCurr' name='delCurrencies[]' value="<?php echo $currency->currency_id; ?>"/></td>
                            <td><a href="#" class="editLink"><?php echo __($currency->getCurrencyType()->getCurrencyName()); ?></a></td>
                            <?php }else{?>
                                <td><?php echo __($currency->getCurrencyType()->getCurrencyName()); ?></td>
                            <?php }?>
                            <td class=""><?php echo number_format($currency->minSalary, 2, '.', ','); ?></td>
                            <td class=""><?php echo number_format($currency->maxSalary, 2, '.', ','); ?></td>
                        </tr>
                <?php
                        $row++;
                    }
                } else {
                ?>
                    <tr class="<?php echo $cssClass; ?>">
                        <?php if ($payGradePermissions->canUpdate()) {?>
                        <td class="check"></td>
                        <?php } ?>
                        <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            
        </form>
        
    </div>
    
</div>

<?php } 
}
?>

<script type="text/javascript">
    var currencies = <?php echo str_replace('&#039;', "'", $form->getCurrencyListAsJson()); ?> ;
    var currencyList = eval(currencies);
    var payGrades = <?php echo str_replace('&#039;', "'", $form->getPayGradeListAsJson()); ?> ;
    var payGradeList = eval(payGrades);
    var assignedCurrencies = <?php echo str_replace('&#039;', "'", $form->getAssignedCurrencyListAsJson($payGradeId)); ?>;
    var assignedCurrencyList = eval(assignedCurrencies);
    var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
    var lang_exceed12Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 10)); ?>';
    var payGradeId = "<?php echo $payGradeId; ?>";
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_save = "<?php echo __("Save"); ?>";
    var lang_currencyRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_salaryShouldBeNumeric = '<?php echo __("Should be a positive number"); ?>';
    var lang_validCurrency = '<?php echo __(ValidationMessages::INVALID); ?>';
    var lang_currencyAlreadyExist = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_validSalaryRange  = '<?php echo __("Should be higher than Minimum Salary"); ?>';
    var lang_addCurrency  = "<?php echo __("Add Currency"); ?>";
    var lang_editCurrency  = "<?php echo __("Edit Currency"); ?>";
    var lang_assignedCurrency  = "<?php echo __("Assigned Currencies"); ?>";
    var lang_uniquePayGradeName  = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var viewPayGradesUrl = "<?php echo url_for("admin/viewPayGrades"); ?>";
    var getCurrencyDetailsUrl = "<?php echo url_for("admin/getCurrencyDetailsJson"); ?>";
    var lang_typeHint = "<?php echo __("Type for hints") . "..."; ?>";
    var lang_negativeAmount = "<?php echo __("Should be a positive number"); ?>";
    var lang_tooLargeAmount = '<?php echo __("Should be less than %amount%", array("%amount%" => '1000,000,000')); ?>';
</script>
