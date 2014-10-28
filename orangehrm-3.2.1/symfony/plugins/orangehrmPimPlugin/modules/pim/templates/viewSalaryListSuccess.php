
<div class="box pimPane">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
    
    <?php if ($salaryPermissions->canCreate() || (count($salaryList) > 0 && $salaryPermissions->canUpdate())) { ?>
    <div id="changeSalary">
        <div class="head">
            <h1 id="headchangeSalary"><?php echo __('Add Salary Component'); ?></h1>
        </div>
        
        <div class="inner">
            <form id="frmSalary" action="<?php echo url_for('pim/viewSalaryList?empNumber=' . $empNumber); ?>" method="post" class="longLabels">
                <fieldset>
                    <?php echo $form['_csrf_token']; ?>
                    <?php echo $form['id']->render(); ?>
                    <?php echo $form['emp_number']->render(); ?>
                    <ol>
                        <li>
                            <?php echo $form['sal_grd_code']->renderLabel(__('Pay Grade')); ?>
                            <?php
                            if ($form->havePayGrades) {
                                echo $form['sal_grd_code']->render(array("class" => "formSelect"));
                            } else {
                                echo $form['sal_grd_code']->render();
                            ?>
                                <label id="noSalaryGrade" for="sal_grd_code"><?php echo __("Not Defined"); ?></label>
                            <?php } ?>
                        </li>
                        <li>
                            <?php echo $form['salary_component']->renderLabel(__('Salary Component') . ' <em>*</em>'); ?>
                            <?php echo $form['salary_component']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                        </li>
                        <li>
                            <?php echo $form['payperiod_code']->renderLabel(__('Pay Frequency')); ?>
                            <?php echo $form['payperiod_code']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li>
                            <?php echo $form['currency_id']->renderLabel(__('Currency') . ' <em>*</em>'); ?>
                            <?php echo $form['currency_id']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li>
                            <input name="" disabled="disabled" id="minSalary" type="hidden" value=""/>
                            <input name="" disabled="disabled" id="maxSalary" type="hidden" value=""/>
                            <?php echo $form['basic_salary']->renderLabel(__('Amount') . ' <em>*</em>'); ?>
                            <?php echo $form['basic_salary']->render(array("class" => "formInputText", "maxlength" => 12)); ?>
                            <label for="minSalary" id="minMaxSalaryLbl" class="fieldHelpRight"></label>
                        </li>
                        <li class="largeTextBox">
                            <?php echo $form['comments']->renderLabel(__('Comments')); ?>
                            <?php echo $form['comments']->render(array("class" => "formInputText")); ?>
                        </li>
                        <li>
                            <?php echo $form['set_direct_debit']->renderLabel(__('Add Direct Deposit Details'), array('id' => 'set_direct_debit_label')); ?>
                            <?php echo $form['set_direct_debit']->render(); ?>
                        </li>
                        <li class="required" id="notDirectDebitSection">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                    </ol>
                    <ol id="directDebitSection">
                        <?php echo $directDepositForm['_csrf_token']; ?>
                        <?php echo $directDepositForm['id']->render(); ?>
                        <li>
                            <?php echo $directDepositForm['account']->renderLabel(__('Account Number') . ' <em>*</em>'); ?>
                            <?php echo $directDepositForm['account']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                        </li>
                        <li>
                            <?php echo $directDepositForm['account_type']->renderLabel(__('Account Type') . ' <em>*</em>'); ?>
                            <?php echo $directDepositForm['account_type']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li id="accountTypeOther">
                            <?php echo $directDepositForm['account_type_other']->renderLabel(__('Please Specify') . ' <em>*</em>');?>
                            <?php echo $directDepositForm['account_type_other']->render(array("class" => "formInputText", "maxlength" => 20)); ?>
                        </li>
                        <li> 
                            <?php echo $directDepositForm['routing_num']->renderLabel(__('Routing Number') . ' <em>*</em>'); ?>
                            <?php echo $directDepositForm['routing_num']->render(array("class" => "formInputText", "maxlength" => 9)); ?>
                        </li>
                        <li>
                            <?php echo $directDepositForm['amount']->renderLabel(__('Amount') . ' <em>*</em>'); ?>
                            <?php echo $directDepositForm['amount']->render(array("class" => "formInputText", "maxlength" => 12)); ?>
                        </li>
                        <li class="required">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                    </ol>
                    <p>
                        <input type="button" class="" id="btnSalarySave" value="<?php echo __("Save"); ?>" />
                        <?php if (
                                (count($salaryList) > 0) || 
                                (count($salaryList) > 0 && $salaryPermissions->canCreate()) || 
                                (count($salaryList) > 0 && $salaryPermissions->canUpdate())
                                ) { ?>
                        <input type="button" class="reset" id="btnSalaryCancel" value="<?php echo __("Cancel"); ?>" />
                        <?php } ?>
                    </p>
                </fieldset>
            </form>
        </div> <!-- inner -->
    </div> <!-- changeSalary-Add-or-Edit-salary -->
    <?php } ?>
    
    <div class="miniList" id="salaryMiniList">
        <div class="head">
            <h1><?php echo __("Assigned Salary Components"); ?></h1>
        </div>
        
        <div class="inner">
            <?php if ($salaryPermissions->canRead()) : ?>
            
            <?php include_partial('global/flash_messages', array('prefix' => 'salary')); ?>
            
            <form id="frmDelSalary" action="<?php echo url_for('pim/deleteSalary?empNumber=' . $empNumber); ?>" method="post" class="longLabels">
                <?php echo $listForm ?>
                <p id="actionSalary">
                    <?php if ($salaryPermissions->canCreate()) { ?>
                    <input type="button" value="<?php echo __("Add"); ?>" class="" id="addSalary" />
                    <?php } ?>
                    <?php if ($salaryPermissions->canDelete() && count($salaryList) > 0) { ?>
                    <input type="button" value="<?php echo __("Delete"); ?>" class="delete" id="delSalary" />
                    <?php } ?>
                </p>
                <table id="tblSalary" class="table hover">
                    <thead>
                        <tr>
                            <?php if ($salaryPermissions->canDelete() && count($salaryList) > 0) { ?>
                            <th class="check" style="width:2%"><input type="checkbox" id="salaryCheckAll" /></th>
                            <?php } ?>
                            <th class="component"><?php echo __('Salary Component'); ?></th>
                            <th class="payperiod"><?php echo __('Pay Frequency'); ?></th>
                            <th class="currency"><?php echo __('Currency'); ?></th>
                            <th class="amount"><?php echo __('Amount'); ?></th>
                            <th class="comments"><?php echo __('Comments'); ?></th>
                            <th class="directDepositCheck"><?php echo __('Show Direct Deposit Details'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!(count($salaryList) > 0)) { ?>
                        <tr>
                            <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                            <td colspan="5"></td>
                        </tr>
                        <?php } else { ?>                        
                        <?php
                        $row = 0;
                        foreach ($salaryList as $salary) :
                            $cssClass = ($row % 2) ? 'even' : 'odd';
                            //empty($salary->from_date)
                            $component = $salary->getSalaryName();
                            $period = $salary->getPayperiod();
                            $payPeriodName = empty($period) ? '' : htmlspecialchars($period->getName());
                            $payPeriodCode = empty($period) ? '' : htmlspecialchars($period->getCode());
                            $currency = $salary->getCurrencyType();
                            $currencyName = empty($currency) ? '' : __(htmlspecialchars($currency->getCurrencyName()));
                            $currencyId = empty($currency) ? '' : htmlspecialchars($currency->getCurrencyId());
                            $amount = $salary->getAmount();
                            $comments = $salary->getNotes();
                            $salaryGrade = $salary->getPayGradeId();
                            $directDeposit = $salary->getDirectDebit();
                            $hasDirectDeposit = !empty($directDeposit->id);
                            $accountType = $directDeposit->account_type;
                            $otherType = "";
                            if ($hasDirectDeposit) {
                                if (($directDeposit->account_type != EmployeeDirectDepositForm::ACCOUNT_TYPE_SAVINGS) && 
                                        ($directDeposit->account_type != EmployeeDirectDepositForm::ACCOUNT_TYPE_CHECKING)) {
                                    $accountType = EmployeeDirectDepositForm::ACCOUNT_TYPE_OTHER;
                                    $otherType = $directDeposit->account_type;
                                }
                            }
                            ?>
                            <tr class="<?php echo $cssClass; ?>">
                                <?php if (!$essUserMode && $salaryPermissions->canDelete()) { ?>

                                <td class="check">
                                    <input type="checkbox" class="chkbox" value="<?php echo $salary->id; ?>" name="delSalary[]"/>
                                </td>
                                <?php } ?>
                                <td class="component">
                                    <input type="hidden" id="code_<?php echo $salary->id; ?>" value="<?php echo $salary->id; ?>" />                                      <?php if($salaryPermissions->canUpdate()) {?>
                                    <a href="#" class="edit"><?php echo $component;?></a>
                                    <?php }else{ 
                                    echo $component;
                                    }?>
                                </td>
                                <td><?php echo __($payPeriodName); ?></td>
                                <td class="currency"><?php echo $currencyName; ?></td>
                                <td class="amount"><?php echo $amount; ?></td>
                                <td class="comments"><?php echo $comments; ?></td>
                                <td>
                                    <?php if ($hasDirectDeposit) { ?>
                                    <input type="checkbox" class="chkbox displayDirectDeposit" value="<?php echo $salary->id; ?>"/>
                                    <input type="hidden" id="dd_id_<?php echo $salary->id; ?>" value="<?php echo $directDeposit->id; ?>" />
                                    <input type="hidden" id="dd_account_type_<?php echo $salary->id; ?>" value="<?php echo $accountType; ?>" />
                                    <input type="hidden" id="dd_other_<?php echo $salary->id; ?>" value="<?php echo $otherType; ?>" />
                                    <input type="hidden" id="dd_account_<?php echo $salary->id; ?>" value="<?php echo $directDeposit->account; ?>" />
                                    <input type="hidden" id="dd_routing_num_<?php echo $salary->id; ?>" value="<?php echo $directDeposit->routing_num; ?>" />
                                    <input type="hidden" id="dd_amount_<?php echo $salary->id; ?>" value="<?php echo $directDeposit->amount; ?>" />
                                    <?php } ?>
                                    <input type="hidden" id="sal_grd_code_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($salaryGrade); ?>" />
                                    <input type="hidden" id="currency_id_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($currencyId); ?>" />
                                    <input type="hidden" id="payperiod_code_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($payPeriodCode); ?>" />
                                    <input type="hidden" id="have_dd_<?php echo $salary->id; ?>" value="<?php echo $hasDirectDeposit ? "1" : "0" ?>" />
                                </td>
                            </tr>
                            <?php
                            if ($hasDirectDeposit) {
                                $accountTypeStr = "";
                                if ($accountType == EmployeeDirectDepositForm::ACCOUNT_TYPE_OTHER) {
                                    $accountTypeStr = $otherType;
                                } else {
                                    $accountTypeStr = $directDepositForm->getAccountTypeDescription($accountType);
                                }
                                ?>
                                <tr class="directDepositRow <?php echo $cssClass; ?>" style="display:none;">
                                    <td colspan="<?php echo $essUserMode || !$salaryPermissions->canDelete() ? '6' : '7'?>" class="<?php echo $cssClass; ?>" >
                                        <span class="directDepositHeading"><h3><?php echo __("Direct Deposit Details"); ?></h3></span>
                                        <table class="table hover" style="width:60%">
                                            <thead>
                                                <tr>
                                                    <th><?php echo __("Account Number"); ?></th>
                                                    <th><?php echo __("Account Type"); ?></th>
                                                    <th><?php echo __("Routing Number"); ?></th>
                                                    <th><?php echo __("Amount"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?php echo $directDeposit->account; ?></td>
                                                    <td><?php echo $accountTypeStr; ?></td>
                                                    <td><?php echo $directDeposit->routing_num; ?></td>
                                                    <td><?php echo $directDeposit->amount; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php
                            }
                            $row++;
                        endforeach;
                        } 
                        ?>
                    </tbody>
                </table>
            </form>
            <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
            <?php endif; ?>
        </div>
    </div> <!-- miniList-salaryMiniList -->
    
    <?php 
    echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => CustomField::SCREEN_SALARY));
    echo include_component('pim', 'attachments', array('empNumber' => $empNumber, 'screen' => EmployeeAttachment::SCREEN_SALARY)); 
    ?>
</div> <!-- Box -->

<script type="text/javascript">
//<![CDATA[

    var canUpdate = '<?php echo $salaryPermissions->canUpdate(); ?>';
    var fileModified = 0;
    var lang_addSalary = "<?php echo __('Add Salary Component'); ?>";
    var lang_editSalary = "<?php echo __('Edit Salary Component'); ?>";
    var lang_payPeriodRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_currencyRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_componentRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_amountRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_invalidAmount = "<?php echo __("Should be within Min/Max values"); ?>";
    var lang_negativeAmount = "<?php echo __("Should be a positive number"); ?>";
    var lang_tooLargeAmount = "<?php echo __("Should be less than %amount%", array("%amount%" => '1000,000,000')); ?>";
    var lang_amountShouldBeNumber = "<?php echo __("Should be a number"); ?>";
    var lang_commentsLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)) ?>";
    var lang_componentLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_selectSalaryToDelete = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";
    var lang_accountRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_accountMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_accountTypeRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_routingNumRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_routingNumInteger = "<?php echo __('Should be a number'); ?>";
    var lang_depositAmountRequired=  "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_depositAmountShouldBeNumber = "<?php echo __('Should be a number'); ?>";
    var lang_otherRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_otherMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 20)); ?>";
    var essMode = '<?php echo $essUserMode; ?>';
//]]>
</script>

<script type="text/javascript">
//<![CDATA[

    $('#delSalary').attr('disabled', 'disabled');

    function clearMessageBar() {
        $("#messagebar").text("").attr('class', "");
    }
    
    function clearMinMax() {
        $("#minSalary").val('--');
        $("#maxSalary").val('--');
        $('#minMaxSalaryLbl').text('');
    }

    function getMinMax(salaryGrade, currency) {
        var notApplicable = '<?php echo __("N/A"); ?>';

        if (salaryGrade == '') {
            $("#minSalary").val('');
            $("#maxSalary").val('');
            $('#minMaxSalaryLbl').text('');
        }
        else {
            var url = '<?php echo url_for('admin/getMinMaxSalaryJson'); ?>' + '/salaryGrade/' + salaryGrade + "/currency/" + currency;

            $.getJSON(url, function(data) {

                var minSalary = false;
                var maxSalary = false;
                var minVal = "";
                var maxVal = "";
                var minMaxLbl = "";

                if (data) {
                    if (data.min) {
                        minSalary = data.min;
                        minVal = minSalary;
                        minMaxLbl = '<?php echo __("Min"); ?>' + " : " + minSalary + " ";
                    }

                    if (data.max) {
                        maxSalary = data.max;
                        maxVal = maxSalary;
                        minMaxLbl = minMaxLbl + '<?php echo __("Max"); ?>' + " : " + maxSalary;
                    }
                }

                $("#minSalary").val(minVal);
                $("#maxSalary").val(maxVal);
                $('#minMaxSalaryLbl').text(minMaxLbl);
            });

        }
    }

    function updateCurrencyList(payGrade, currencyId, currencyName) {

        var url = '<?php echo url_for('pim/getAvailableCurrenciesJson?empNumber=' . $empNumber . '&paygrade='); ?>' + payGrade;

        $.getJSON(url, function(data) {

            var numOptions = data.length;
            var optionHtml = '<option value="">-- <?php echo __("Select") ?> --</option>';

            for (var i = 0; i < numOptions; i++) {
                optionHtml += '<option value="' + data[i].currency_id + '">' + data[i].currency_name + '</option>';
            }

            $("#salary_currency_id").html(optionHtml);

            // If editing a currency, add that currency to list
            if (currencyId && currencyName) {
                $('#salary_currency_id').val(currencyId);
                getMinMax(payGrade, currencyId);
            } else {
                $('#salary_currency_id').val('');
                clearMinMax();
            }

        })
    }

    function clearDirectDepositFields() {
        $("#salary_set_direct_debit").removeAttr('checked');
        $("#directdeposit_id").val('');
        $("#directdeposit_account").val('');
        $("#directdeposit_account_type").val('');
        $("#directdeposit_routing_num").val('');
        $("#directdeposit_amount").val('');
    }

    $(document).ready(function() {

    $("#changeSalary").hide();

    //if check all button clicked
    $("#salaryCheckAll").click(function() {
        $(".check .chkbox").removeAttr("checked");
        if ($("#salaryCheckAll").attr("checked")) {
            $(".check .chkbox").attr("checked", "checked");
        }
        
        if($('.check .chkbox:checkbox:checked').length > 0) {
            $('#delSalary').removeAttr('disabled');
        } else {
            $('#delSalary').attr('disabled', 'disabled');
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $(".check .chkbox").click(function() {
        $("#salaryCheckAll").removeAttr('checked');
        if ($(".check .chkbox").length == $(".check .chkbox:checked").length) {
            $("#salaryCheckAll").attr('checked', 'checked');
        }
        
        if($('.check .chkbox:checkbox:checked').length > 0) {
            $('#delSalary').removeAttr('disabled');
        } else {
            $('#delSalary').attr('disabled', 'disabled');
        }
    });

    $("#salary_set_direct_debit").click(function() {
        
        if ($(this).attr('checked')) {
            $('#directDebitSection').show();
            $('#notDirectDebitSection').hide();
        } else {
            $('#directDebitSection').hide();
            $('#notDirectDebitSection').show();
        }
        
    });

    $("input.displayDirectDeposit").click(function() {
        
        // find row with direct deposit details
        var directDepositRow = $(this).closest("tr").next();
        
        if ($(this).attr('checked')) {
            directDepositRow.show();
        } else {
            directDepositRow.hide();
        }
    });

    $("#directdeposit_account_type").change(function() {
        if ($(this).val() == '<?php echo EmployeeDirectDepositForm::ACCOUNT_TYPE_OTHER; ?>') {
            $('#accountTypeOther').show();
        } else {
            $('#accountTypeOther').hide();
        }
    });
    
    $("#addSalary").click(function() {

        removeEditLinks();
        clearMessageBar();
        $('div#changeSalary label.error').hide();
        $('#actionClearBr').hide();


        //changing the headings
        $("#headchangeSalary").text(lang_addSalary);
        $('.check').hide();

        //hiding action button section
        $("#actionSalary").hide();

        $('#salary_id').val("");
        $('#salary_sal_grd_code').val("");
        updateCurrencyList('', false, false);
        $('#salary_currency_id').val("");

        clearMinMax();

        $("#salary_basic_salary").val("");
        $("#salary_payperiod_code").val("");
        $("#salary_component").val("");
        $("#salary_comments").val("");

        //show add form
        $("#changeSalary").show();

        // hide direct deposit section
        $('#directDebitSection').hide();
        $('#notDirectDebitSection').show();
        clearDirectDepositFields();
        $("#salary_set_direct_debit").removeAttr('checked');

    });

    //clicking of delete button
    $("#delSalary").click(function(){
        if ($(".check .chkbox:checked").length > 0) {
            $("#frmDelSalary").submit();
        }
    });

    $("#btnSalarySave").click(function() {
        clearMessageBar();
        
        $("#frmSalary").submit();
    });

    /* Valid From Date */
    $.validator.addMethod("validateAmount", function(value, element) {
        
        var valid = true;
        
        var min	= parseFloat($('#minSalary').val());
        var max = parseFloat($('#maxSalary').val());
        var amount = parseFloat($('#salary_basic_salary').val().trim());
        
        if (!isNaN(amount) && (min != 0 || max != 0)) {
            
            if (!isNaN(min) && (amount < min)) {
                valid = false;
            }
            
            if (!isNaN(max) && max != 0 && (amount > max)) {
                valid = false;
            }
        }
        return valid;
        
    });

    //form validation
    var salaryValidator =
        $("#frmSalary").validate({
        rules: {
            'salary[sal_grd_code]': {required: false},
            'salary[currency_id]': {required: true},
            'salary[salary_component]': {required: true, maxlength: 100},
            'salary[comments]': {required: false, maxlength: 250},
            'salary[basic_salary]': {number:true, validateAmount:true, required: true, min: 0, max:999999999.99},
            'directdeposit[account]': {required: "#salary_set_direct_debit:checked", maxlength:100},
            'directdeposit[account_type]': {required: "#salary_set_direct_debit:checked"},
            'directdeposit[account_type_other]': {
                required: function(element) {
                    if ( $('#salary_set_direct_debit:checked').length &&
                        $('#directdeposit_account_type').val() == "OTHER" ) {
                        return true;
                    } else {
                        return false;
                    }
                },
                maxlength:20},
            'directdeposit[routing_num]': {required: "#salary_set_direct_debit:checked", digits:true},
            'directdeposit[amount]': {required: "#salary_set_direct_debit:checked", number:true, min: 0, max:1000000000.00}
        },
        messages: {
            'salary[currency_id]': {required: lang_currencyRequired},
            'salary[salary_component]': {required: lang_componentRequired, maxlength: lang_componentLength},
            'salary[comments]': {maxlength: lang_commentsLength},
            'salary[basic_salary]': {number: lang_amountShouldBeNumber, validateAmount: lang_invalidAmount, required: lang_amountRequired, min: lang_negativeAmount, max:lang_tooLargeAmount},
            'directdeposit[account]': {required: lang_accountRequired, maxlength: lang_accountMaxLength},
            'directdeposit[account_type]': {required: lang_accountTypeRequired},
            'directdeposit[account_type_other]': {required: lang_otherRequired, maxlength: lang_otherMaxLength},
            'directdeposit[routing_num]': {required: lang_routingNumRequired, digits: lang_routingNumInteger},
            'directdeposit[amount]': {required: lang_amountRequired, number: lang_depositAmountShouldBeNumber, min: lang_negativeAmount, max:lang_tooLargeAmount}
            
        }
    });
    
    function addEditLinks() {
        if (canUpdate) {
            // called here to avoid double adding links - When in edit mode and cancel is pressed.
            removeEditLinks();
            $('td.component').wrapInner('<a class="edit" href="#"/>');
        }
    }
    $('#accountTypeOther').hide();
    function removeEditLinks() {
        $('td.component a').each(function(index) {
            $(this).parent().text($(this).text());
        });
    }

    $("#btnSalaryCancel").click(function() {
        clearMessageBar();

        addEditLinks();
        salaryValidator.resetForm();
        $('#actionClearBr').show();

        $('div#changeSalary label.error').hide();

        $(".chkbox").removeAttr("checked");
        $('.check').show();
        $('td.component').attr('colspan', 1);

        //hiding action button section
        $("#actionSalary").show();
        $("#changeSalary").hide();

        $('#salary_id').val('');

        // remove any options already in use
        $("#salary_code option[class='added']").remove();
        clearDirectDepositFields();
        $('#static_salary_code').hide().val("");

    });

    $('form#frmDelSalary a.edit').live('click', function(event) {
        event.preventDefault();
        clearMessageBar();
        $('#actionClearBr').hide();
        
        //changing the headings
        $("#headchangeSalary").text(lang_editSalary);
        
        salaryValidator.resetForm();
        
        $('div#changeSalary label.error').hide();
        
        //hiding action button section
        $("#actionSalary").hide();
        
        //show add form
        $("#changeSalary").show();
        
        var id = $(this).closest("tr").find('input.chkbox:first').val();
        
        $('#salary_id').val(id);
        
        var salGrdCode = $("#sal_grd_code_" + id).val();
        $("#salary_sal_grd_code").val(salGrdCode);
        
        var currencyId = $("#currency_id_" + id).val();
        $("#salary_currency_id").val(currencyId);
        var currencyName = $(this).closest("tr").find('td.currency').text();
        
        var basicSalary =  $(this).closest("tr").find('td.amount').text();
        $("#salary_basic_salary").val(basicSalary);
        
        $("#salary_payperiod_code").val($("#payperiod_code_" + id).val());
        
        var component =  $(this).closest("tr").find('td.component').text().trim();
        
        $("#salary_salary_component").val(component);
        
        var comments =  $(this).closest("tr").find('td.comments').text();
        $("#salary_comments").val(comments);
        
        // Direct Deposit
        
        var haveDirectDeposit = $("#have_dd_" + id).val() == "1";
        
        if (haveDirectDeposit) {
            $("#salary_set_direct_debit").attr('checked', 'checked');
            $("#directdeposit_id").val($("#dd_id_" + id).val());
            $("#directdeposit_account").val($("#dd_account_" + id).val());
            $("#directdeposit_account_type").val($("#dd_account_type_" + id).val());
            $("#directdeposit_account_type_other").val($("#dd_other_" + id).val());
            $("#directdeposit_routing_num").val($("#dd_routing_num_" + id).val());
            $("#directdeposit_amount").val($("#dd_amount_" + id).val());
            $('#directDebitSection').show();
            $('#notDirectDebitSection').hide();
            
            
            if ($("#directdeposit_account_type_other").val() == '') {
                $('#accountTypeOther').hide();
            } else {
                $('#accountTypeOther').show();
            }
            
        } else {
            $("#salary_set_direct_debit").removeAttr('checked');
            $('#directDebitSection').hide();
            $('#notDirectDebitSection').show();
            clearDirectDepositFields();
        }
        
        $("#salary_payperiod_code").val($("#payperiod_code_" + id).val());
        
        updateCurrencyList(salGrdCode, currencyId, currencyName);
        
        $(".check").hide();
        
    });
    
    /*
     * Ajax call to fetch unassigned currencies for selected pay grade
     */
    $("#salary_sal_grd_code").change(function() {
        
        var payGrade = this.options[this.selectedIndex].value;
        updateCurrencyList(payGrade, false, false);
    });
    

    /*
     * Ajax call to fetch min/max salary
     */
    $("#salary_currency_id").change(function() {
        
        var currencyCode = this.options[this.selectedIndex].value;
        var salaryGrade = $("#salary_sal_grd_code").val();
        
        $('#salary_currency_id').val(currencyCode);
        
        // don't check if not selected
        if (currencyCode == '0') {
            clearMinMax();
            return;
        }
        getMinMax(salaryGrade, currencyCode);
    });
});
//]]>
</script>
