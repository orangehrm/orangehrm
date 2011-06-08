<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js') ?>"></script>
<?php echo stylesheet_tag('../orangehrmPimPlugin/css/viewSalaryListSuccess'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top">
            <?php include_partial('leftmenu', array('empNumber' => $empNumber, 'form' => $form)); ?></td>
        <td valign="top">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td valign="top">
                        <!-- this space is for contents -->
                        <div id="mainDiv">
                            <?php
                            if (isset($message) && isset($messageType)) {
                                $tmpMsgClass = "messageBalloon_{$messageType}";
                                $tmpMsg = $message;
                            } else {
                                $tmpMsgClass = '';
                                $tmpMsg = '';
                            }
                            ?>
                            <div id="messagebar" class="<?php echo $tmpMsgClass; ?>">
                                <span style="font-weight: bold;"><?php echo $tmpMsg; ?></span>
                            </div>


                            <?php if (!$essUserMode): ?>
                                <div id="changeSalary" class="outerbox" >
                                    <div class="mainHeading"><h2 id="headchangeSalary"><?php echo __('Add Salary Component'); ?></h2></div>
                                    <form id="frmSalary" action="<?php echo url_for('pim/viewSalaryList?empNumber=' . $empNumber); ?>" method="post">

                                    <?php echo $form['_csrf_token']; ?>
                                    <?php echo $form['id']->render(); ?>
                                    <?php echo $form['emp_number']->render(); ?>


                                    <?php
                                    echo $form['sal_grd_code']->renderLabel(__('Pay Grade'));
                                    if ($form->havePayGrades) {

                                        echo $form['sal_grd_code']->render(array("class" => "formSelect"));
                                    } else {
                                        echo $form['sal_grd_code']->render();
                                    ?>
                                        <label id="noSalaryGrade" for="sal_grd_code"><?php echo __("Not Defined"); ?></label>
                                    <?php
                                    }
                                    ?>
                                    <br class="clear"/>

                                    <?php echo $form['salary_component']->renderLabel(__('Salary Component') . ' <span class="required">*</span>'); ?>
                                    <?php echo $form['salary_component']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                                    <br class="clear"/>

                                    <?php echo $form['payperiod_code']->renderLabel(__('Pay Frequency')); ?>
                                    <?php echo $form['payperiod_code']->render(array("class" => "formSelect")); ?>
                                    <br class="clear"/>

                                    <?php echo $form['currency_id']->renderLabel(__('Currency') . ' <span class="required">*</span>'); ?>
                                    <?php echo $form['currency_id']->render(array("class" => "formSelect")); ?>
                                    <br class="clear"/>

                                    <input name="" disabled="disabled" id="minSalary" type="hidden" value=""/>
                                    <input name="" disabled="disabled" id="maxSalary" type="hidden" value=""/>
                                    <?php echo $form['basic_salary']->renderLabel(__('Amount') . ' <span class="required">*</span>'); ?>
                                    <?php echo $form['basic_salary']->render(array("class" => "formInputText", "maxlength" => 12)); ?>
                                    <label for="minSalary" id="minMaxSalaryLbl"></label>
                                    <br class="clear"/>

                                    <?php echo $form['comments']->renderLabel(__('Comments')); ?>
                                    <?php echo $form['comments']->render(array("class" => "formInputText")); ?>
                                    <br class="clear"/>

                                    <?php echo $form['set_direct_debit']->render(); ?>
                                    <?php echo $form['set_direct_debit']->renderLabel(__('Add Direct Deposit Details'),
                                            array('id' => 'set_direct_debit_label')); ?>

                                    <br class="clear"/>

                                    <div id="directDebitSection" style="display:none">
                                        <?php echo $directDepositForm['_csrf_token']; ?>
                                        <?php echo $directDepositForm['id']->render(); ?>

                                        <?php echo $directDepositForm['account']->renderLabel(__('Account Number') . ' <span class="required">*</span>'); ?>
                                        <?php echo $directDepositForm['account']->render(array("class" => "formInputText", "maxlength" => 100)); ?>

                                        <br class="clear"/>

                                        <?php echo $directDepositForm['account_type']->renderLabel(__('Account Type') . ' <span class="required">*</span>'); ?>
                                        <?php echo $directDepositForm['account_type']->render(array("class" => "formSelect")); ?>

                                        <br class="clear"/>

                                        <div id="accountTypeOther">
                                            <?php echo $directDepositForm['account_type_other']->renderLabel(__('Please Specify') . ' <span class="required">*</span>'); ?>
                                            <?php echo $directDepositForm['account_type_other']->render(array("class" => "formInputText", "maxlength" => 20)); ?>

                                            <br class="clear"/>
                                        </div>

                                        <?php echo $directDepositForm['routing_num']->renderLabel(__('Routing Number') . ' <span class="required">*</span>'); ?>
                                        <?php echo $directDepositForm['routing_num']->render(array("class" => "formInputText", "maxlength" => 20)); ?>

                                            <br class="clear"/>

                                        <?php echo $directDepositForm['amount']->renderLabel(__('Amount') . ' <span class="required">*</span>'); ?>
                                        <?php echo $directDepositForm['amount']->render(array("class" => "formInputText", "maxlength" => 12)); ?>
                                            <br class="clear"/>
                                        </div>

                                        <br class="clear"/>

                                        <div class="formbuttons">
                                            <input type="button" class="savebutton" id="btnSalarySave" value="<?php echo __("Save"); ?>" />
                                        <?php if (count($salaryList) > 0) {
                                        ?>
                                                <input type="button" class="savebutton" id="btnSalaryCancel" value="<?php echo __("Cancel"); ?>" />
                                        <?php } ?>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                                            <div class="smallText" id="salaryRequiredNote"><?php echo __('Fields marked with an asterisk') ?>
                                                <span class="required">*</span> <?php echo __('are required.') ?></div>
                            <?php if (count($salaryList) > 0) {
 ?>
                                                <div class="outerbox">
                                                    <div class="mainHeading"><h2><?php echo __('Assigned Salary Components'); ?></h2></div>

                                                    <div id="actionSalary" class="actionbuttons">
                                                        <input type="button" value="<?php echo __("Add"); ?>" class="savebutton" id="addSalary" />&nbsp;
                                                        <input type="button" value="<?php echo __("Delete"); ?>" class="savebutton" id="delSalary" />
                                                    </div>
                                                    <br class="clear" id="actionClearBr"/>

                                                    <form id="frmDelSalary" action="<?php echo url_for('pim/deleteSalary?empNumber=' . $empNumber); ?>" method="post">
                                                        <div id="tblSalary">
                                                            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" border="0">
                                                                <thead>
                                                                    <tr>
                                                                        <td class="check"><input type="checkbox" id="salaryCheckAll" /></td>
                                                                        <td class="component"><?php echo __('Salary Component'); ?></td>
                                                                        <td class="payperiod"><?php echo __('Pay Frequency'); ?></td>
                                                                        <td class="currency"><?php echo __('Currency'); ?></td>
                                                                        <td class="amount"><?php echo __('Amount'); ?></td>
                                                                        <td class="comments"><?php echo __('Comments'); ?></td>
                                                                        <td class="directDepositCheck"><?php echo __('Show Direct Deposit Details'); ?></td>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                <?php
                                                $row = 0;

                                                foreach ($salaryList as $salary) {
                                                    $cssClass = ($row % 2) ? 'even' : 'odd';
                                                    //empty($salary->from_date)
                                                    $component = htmlspecialchars($salary->getSalaryComponent());
                                                    $period = $salary->getPayperiod();
                                                    $payPeriodName = empty($period) ? '' : htmlspecialchars($period->getName());
                                                    $payPeriodCode = empty($period) ? '' : htmlspecialchars($period->getCode());

                                                    $currency = $salary->getCurrencyType();
                                                    $currencyName = empty($currency) ? '' : htmlspecialchars($currency->getCurrencyName());
                                                    $currencyId = empty($currency) ? '' : htmlspecialchars($currency->getCurrencyId());
                                                    $amount = $salary->getBasicSalary();
                                                    $comments = htmlspecialchars($salary->getComments());
                                                    $salaryGrade = $salary->getSalGrdCode();

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
                                                        <td class="check"><input type="hidden" id="code_<?php echo $salary->id; ?>" value="<?php echo $salary->id; ?>" />

                                                            <input type="checkbox" class="chkbox" value="<?php echo $salary->id; ?>" name="delSalary[]"/></td>
                                                        <td class="component"><a href="#" class="edit"><?php echo $component; ?></a></td>
                                                        <td><?php echo $payPeriodName; ?></td>
                                                        <td class="currency"><?php echo $currencyName; ?></td>
                                                        <td class="amount"><?php echo $amount; ?></td>
                                                        <td class="comments"><?php echo $comments; ?></td>
                                                        <td>
                                                        <?php if ($hasDirectDeposit) { ?>
                                                            <input type="checkbox" class="chkbox displayDirectDeposit" value="<?php echo $salary->id; ?>"/>
                                                            <input type="hidden" id="dd_id_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($directDeposit->id); ?>" />
                                                            <input type="hidden" id="dd_account_type_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($accountType); ?>" />
                                                            <input type="hidden" id="dd_other_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($otherType); ?>" />
                                                            <input type="hidden" id="dd_account_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($directDeposit->account); ?>" />
                                                            <input type="hidden" id="dd_routing_num_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($directDeposit->routing_num); ?>" />
                                                            <input type="hidden" id="dd_amount_<?php echo $salary->id; ?>" value="<?php echo htmlspecialchars($directDeposit->amount); ?>" />

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
                                                            <tr class="directDepositRow" style="display:none;">
                                                                <td colspan="7" class="<?php echo $cssClass; ?>" >
                                                                    <span class="directDepositHeading"><?php echo __("Direct Deposit Details"); ?></span>

                                                                    <table cellspacing="0" cellpadding="0" border="0" class="directDepositTable" width="80%">
                                                                        <thead>
                                                                            <tr>
                                                                                <td><?php echo __("Account Number"); ?></td>
                                                                                <td><?php echo __("Account Type"); ?></td>
                                                                                <td><?php echo __("Routing Number"); ?></td>
                                                                                <td><?php echo __("Amount"); ?></td>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td><?php echo htmlspecialchars($directDeposit->account); ?></td>
                                                                                <td><?php echo htmlspecialchars($accountTypeStr); ?></td>
                                                                                <td><?php echo htmlspecialchars($directDeposit->routing_num); ?></td>
                                                                                <td><?php echo htmlspecialchars($directDeposit->amount); ?></td>
                                                                            </tr>
                                                                        </tbody>
                                                            <?php
                                                            //;
                                                            //if (isset($x)) {var_dump($x->toArray());}
                                                            //    echo $salary->EmpDirectdebit->account;
                                                            //}?>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                <?php $row++;
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                </div>
                            <?php } ?>

                            <?php echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => 'salary')); ?>
                            <?php echo include_component('pim', 'attachments', array('empNumber' => $empNumber, 'screen' => 'salary')); ?>

                                            </div>
                                        </td>
                                        <td valign="top" align="left">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <script type="text/javascript">
                        //<![CDATA[

                        var fileModified = 0;
                        var lang_addSalary = "<?php echo __('Add Salary Component'); ?>";
                        var lang_editSalary = "<?php echo __('Edit Salary Component'); ?>";
                        var lang_payPeriodRequired = "<?php echo __("Pay Grade is required"); ?>";
                        var lang_currencyRequired = "<?php echo __("Currency is required"); ?>";
                        var lang_componentRequired = "<?php echo __("Component is required"); ?>";
                        var lang_amountRequired = "<?php echo __("Amount is required"); ?>";
                        var lang_invalidAmount = "<?php echo __("Amount should be within Man/Max values"); ?>";
                        var lang_negativeAmount = "<?php echo __("Amount should be a positive number"); ?>";
                        var lang_tooLargeAmount = "<?php echo __("Amount should be less than 999999999.99"); ?>";
                        var lang_amountShouldBeNumber = "<?php echo __("Amount should be a number"); ?>";
                        var lang_commentsLength = "<?php echo __("Comments cannot exceed 255 characters in length") ?>";
                        var lang_componentLength = "<?php echo __('Component cannot exceed 100 characters in length'); ?>";
                        var lang_selectSalaryToDelete = "<?php echo __('Please Select At Least One Salary Component To Delete'); ?>";
                        var lang_accountRequired = "<?php echo __('Account Number is required'); ?>";
                        var lang_accountMaxLength = "<?php echo __('Account cannot exceed 100 characters in length'); ?>";
                        var lang_accountTypeRequired = "<?php echo __('Account Type is required'); ?>";
                        var lang_routingNumRequired = "<?php echo __('Routing Number is required'); ?>";
                        var lang_routingNumInteger = "<?php echo __('Routing Number should only contain digits'); ?>";
                        var lang_depositAmountRequired=  "<?php echo __('Amount is required'); ?>";
                        var lang_depositAmountShouldBeNumber = "<?php echo __('Amount should be a number'); ?>";
                        var lang_otherRequired = "<?php echo __('Please specify other account type'); ?>";
                        var lang_otherMaxLength = "<?php echo __('Other account cannot exceed 20 characters in length'); ?>";
                        var lang_otherMaxLength = "<?php echo __('Other account cannot exceed 20 characters in length'); ?>";
                        var essMode = '<?php echo $essUserMode; ?>';



                        //]]>
                    </script>

                    <script type="text/javascript">
                        //<![CDATA[

                        function clearMinMax() {
                            $("#minSalary").val('--');
                            $("#maxSalary").val('--');
                            $('#minMaxSalaryLbl').text('');
                        }

                        function getMinMax(salaryGrade, currency)
                        {
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
                                    $('#salary_currency_id').append($("<option></option>").
                                        attr("value", currencyId).
                                        text(currencyName));
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


                            if(essMode){
                                $('.data-table td.check').hide();
                                $('#actionSalary').hide();
                                 $('#actionClearBr').hide();
                                removeEditLinks();
                            }


                            //hide add section
<?php if (count($salaryList) > 0) { ?>
                        $("#changeSalary").hide();
                        $("#salaryRequiredNote").hide();
<?php
                                                } else {
                                                    // Force
?>
                        clearDirectDepositFields();
                        $('#directDebitSection').hide();
<?php } ?>

                    //hiding the data table if records are not available
                    if($("div#tblSalary table.data-table .chkbox").length == 0) {
                    $("#tblSalary").hide();
                    $("#editSalary").hide();
                    $("#delSalary").hide();
                    }

                    //if check all button clicked
                    $("#salaryCheckAll").click(function() {
                    $("div#tblSalary td.check .chkbox").removeAttr("checked");
                    if($("#salaryCheckAll").attr("checked")) {
                    $("div#tblSalary td.check .chkbox").attr("checked", "checked");
                    }
                    });

                    //remove tick from the all button if any checkbox unchecked
                    $("div#tblSalary td.check .chkbox").click(function() {
                    $("#salaryCheckAll").removeAttr('checked');
                    if($("div#tblSalary td.check .chkbox").length == $("div#tblSalary td.check .chkbox:checked").length) {
                    $("#salaryCheckAll").attr('checked', 'checked');
                    }
                    });

                    $("#salary_set_direct_debit").click(function() {

                    if ($(this).attr('checked')) {
                    $('#directDebitSection').show();
                    } else {
                    $('#directDebitSection').hide();
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
$('div#tblSalary td.check').hide();
$('div#tblSalary td.component').attr('colspan', 2);
         
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
$("#salaryRequiredNote").show();
        
// hide direct deposit section
$('#directDebitSection').hide();
clearDirectDepositFields();
$("#salary_set_direct_debit").removeAttr('checked');
        
});

//clicking of delete button
$("#delSalary").click(function(){

clearMessageBar();

if ($("div#tblSalary td.check .chkbox:checked").length > 0) {
$("#frmDelSalary").submit();
} else {
$("#messagebar").attr('class', 'messageBalloon_notice').text(lang_selectSalaryToDelete);
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
        
if (!isNaN(amount)) {
            
if (!isNaN(min) && (amount < min)) {
valid = false;
}
            
if (!isNaN(max) && (amount > max)) {
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
'salary[comments]': {required: false, maxlength: 255},
'salary[basic_salary]': {number:true, validateAmount:true, required: true, min: 0, max:999999999.99},
'directdeposit[account]': {required: "#salary_set_direct_debit:checked", maxlength:100},
'directdeposit[account_type]': {required: "#salary_set_direct_debit:checked"},
'directdeposit[account_type_other]': {required: function(element) {
    if ( $('#salary_set_direct_debit:checked').length &&
        $('#directdeposit_account_type').val() == "OTHER" ) {
        return true;
    } else {
        return false;
    }
},
maxlength:20},
'directdeposit[routing_num]': {required: "#salary_set_direct_debit:checked", digits:true},
'directdeposit[amount]': {required: "#salary_set_direct_debit:checked", number:true, min: 0, max:999999999.99}
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
'directdeposit[amount]': {required: lang_otherRequired, number: lang_depositAmountShouldBeNumber, min: lang_negativeAmount, max:lang_tooLargeAmount}
            
},

errorElement : 'div',
errorPlacement: function(error, element) {
error.insertAfter(element.next(".clear"));
error.insertAfter(element.next().next(".clear"));

}
});
    
function addEditLinks() {
// called here to avoid double adding links - When in edit mode and cancel is pressed.
removeEditLinks();
$('div#tblSalary table tbody td.component').wrapInner('<a class="edit" href="#"/>');
}
$('#accountTypeOther').hide();
function removeEditLinks() {
$('div#tblSalary table tbody td.component a').each(function(index) {
$(this).parent().text($(this).text());
});
}

$("#btnSalaryCancel").click(function() {
clearMessageBar();

addEditLinks();
salaryValidator.resetForm();
$('#actionClearBr').show();
        
$('div#changeSalary label.error').hide();

$("div#tblSalary .chkbox").removeAttr("checked");
$('div#tblSalary td.check').show();
$('div#tblSalary td.component').attr('colspan', 1);
        
//hiding action button section
$("#actionSalary").show();
$("#changeSalary").hide();
$("#salaryRequiredNote").hide();
        
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
var id = $(this).closest("tr").find('td.check input.chkbox:first').val();
        
$('#salary_id').val(id);
        
var salGrdCode = $("#sal_grd_code_" + id).val();
$("#salary_sal_grd_code").val(salGrdCode);
        
var currencyId = $("#currency_id_" + id).val();
$("#salary_currency_id").val(currencyId);
var currencyName = $(this).closest("tr").find('td.currency').text();
        
var basicSalary =  $(this).closest("tr").find('td.amount').text();
$("#salary_basic_salary").val(basicSalary);
        
$("#salary_payperiod_code").val($("#payperiod_code_" + id).val());

var component =  $(this).closest("tr").find('td.component').text();
        
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
            
            
if ($("#directdeposit_account_type_other").val() == '') {
$('#accountTypeOther').hide();
} else {
$('#accountTypeOther').show();
}
            
} else {
$("#salary_set_direct_debit").removeAttr('checked');
$('#directDebitSection').hide();
clearDirectDepositFields();
}
        
$("#salary_payperiod_code").val($("#payperiod_code_" + id).val());
        
updateCurrencyList(salGrdCode, currencyId, currencyName);

$("#salaryRequiredNote").show();

$("div#tblSalary td.check").hide();
$('div#tblSalary td.component').attr('colspan', 2);

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

function clearMessageBar() {
$("#messagebar").text("").attr('class', "");
}
//]]>
</script>