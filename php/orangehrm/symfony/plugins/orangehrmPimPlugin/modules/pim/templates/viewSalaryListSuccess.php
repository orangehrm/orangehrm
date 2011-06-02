<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js')?>"></script>
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
            <?php include_partial('leftmenu', array('empNumber' => $empNumber, 'form' => $form));?></td>
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
                            
    <div id="changeSalary" class="outerbox" style="width:500px;">
        <div class="mainHeading"><h2 id="headchangeSalary"><?php echo __('Add Salary Component'); ?></h2></div>
        <form id="frmSalary" action="<?php echo url_for('pim/viewSalaryList?empNumber=' . $empNumber); ?>" method="post">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['id']->render(); ?>
            <?php echo $form['emp_number']->render(); ?>

            <?php echo $form['sal_grd_code']->renderLabel(__('Pay Grade') . ' <span class="required">*</span>'); ?>
            <?php echo $form['sal_grd_code']->render(array("class" => "formSelect")); ?>
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
            <?php echo $form['basic_salary']->renderLabel(__('Amount'). ' <span class="required">*</span>'); ?>
            <?php echo $form['basic_salary']->render(array("class" => "formInputText", "maxlength" => 20)); ?>
            <label for="minSalary" id="minMaxSalaryLbl"></label>            
            <br class="clear"/>

            <?php echo $form['comments']->renderLabel(__('Comments')); ?>
            <?php echo $form['comments']->render(array("class" => "formInputText")); ?>
            <br class="clear"/>
            
            <?php echo $form['set_direct_debit']->renderLabel(__('Add Direct Deposit Details')); ?>
            <?php echo $form['set_direct_debit']->render(array("class" => "formCheckbox")); ?>
            
            <br class="clear"/>

            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnSalarySave" value="<?php echo __("Save"); ?>" />
                <input type="button" class="savebutton" id="btnSalaryCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
        </form>
    </div>                                
    <div class="smallText" id="salaryRequiredNote"><?php echo __('Fields marked with an asterisk')?>
        <span class="required">*</span> <?php echo __('are required.')?></div>

    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo __('Assigned Salary Components'); ?></h2></div>
    
    <div id="actionSalary" class="actionbuttons">
        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addSalary" />&nbsp;
        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delSalary" />
    </div>
    <br class="clear" id="actionClearBr"/>

    <form id="frmDelSalary" action="<?php echo url_for('pim/deleteSalary?empNumber=' . $empNumber); ?>" method="post">
        <div id="tblSalary">
            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" border="0">
                <thead>
                <tr>
                    <td class="check"><input type="checkbox" id="salaryCheckAll" /></td>
                    <td><?php echo __('Salary Component');?></td>
                    <td><?php echo __('Pay Frequency');?></td>
                    <td><?php echo __('Currency');?></td>
                    <td><?php echo __('Amount');?></td>
                    <td><?php echo __('Comments');?></td>
                    <td class="directDepositCheck"><?php echo __('Show Direct Deposit Details');?></td>
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
                        $currencyId =  empty($currency) ? '' : htmlspecialchars($currency->getCurrencyId());
                        $amount = $salary->getBasicSalary();
                        $comments = htmlspecialchars($salary->getComments());
                        $salaryGrade = $salary->getSalGrdCode();
                        ?>
                    <tr class="<?php echo $cssClass;?>">
                <td class="check"><input type="hidden" id="code_<?php echo $salary->id;?>" value="<?php echo $salary->id; ?>" />

                <input type="checkbox" class="chkbox" value="<?php echo $salary->id;?>" name="delSalary[]"/></td>
                <td class="component"><a href="#" class="edit"><?php echo $component;?></a></td>
                <td><?php echo $payPeriodName;?></td>
                <td class="currency"><?php echo $currencyName;?></td>
                <td class="amount"><?php echo $amount;?></td>
                <td class="comments"><?php echo $comments;?></td>
                <td><input type="checkbox" class="chkbox" value="<?php echo $salary->id; ?>"/>
                
                <input type="hidden" id="sal_grd_code_<?php echo $salary->id;?>" value="<?php echo htmlspecialchars($salaryGrade); ?>" />
                <input type="hidden" id="currency_id_<?php echo $salary->id;?>" value="<?php echo htmlspecialchars($currencyId); ?>" />                
                <input type="hidden" id="payperiod_code_<?php echo $salary->id;?>" value="<?php echo htmlspecialchars($payPeriodCode); ?>" />
                </td>
                </tr>
                    <?php $row++;
                }?>
                </tbody>
            </table>
        </div>
    </form>
                            </div>

                            </div>
                        <?php echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => 'salary'));?>
                        <?php echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => 'salary'));?>
                            
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
    var lang_addSalary = "<?php echo __('Add Salary Component');?>";
    var lang_editSalary = "<?php echo __('Edit Salary Component');?>";
    var lang_payPeriodRequired = "<?php echo __("Pay Grade is required");?>";
    var lang_currencyRequired = "<?php echo __("Currency is required");?>";
    var lang_componentRequired = "<?php echo __("Component is required");?>";
    var lang_amountRequired = "<?php echo __("Amount is required");?>";
    var lang_invalidAmount = "<?php echo __("Amount should be within Man/Max values");?>";
    var lang_amountShouldBeNumber = "<?php echo __("Amount should be a number");?>";
    var lang_commentsLength = "<?php echo __("Comments cannot exceed 255 characters in length") ?>";
    var lang_componentLength = "<?php echo __('Component cannot exceed 100 characters in length');?>";
    var lang_selectSalaryToDelete = "<?php echo __('Please Select At Least One Salary Component To Delete');?>";
    //]]>
</script>

<script type="text/javascript">
//<![CDATA[

function clearMinMax() {
    $("#minSalary").val('--');
    $("#maxSalary").val('--');
    $('#minMaxSalaryLbl').text('<?php echo __("Min") . " : " . __("N/A") . " " . __("Max") . " : " . __("N/A");?>');
}

function getMinMax(salaryGrade, currency)
{
    var url = '<?php echo url_for('admin/getMinMaxSalaryJson') ;?>' + '/salaryGrade/' + salaryGrade + "/currency/" + currency;

    $.getJSON(url, function(data) {

        var notApplicable = '<strong>-<?php echo __("N/A");?>-</strong>';
        var minSalary = null;
        var maxSalary = null;
        var minVal = "";
        var maxVal = "";

        if (data) {
            minSalary = data.min;
            maxSalary = data.max;
            minVal = minSalary;
            maxVal = maxSalary;
        }
        if (minSalary == null) {
            minSalary = notApplicable;
        }
        if (maxSalary == null) {
            maxSalary = notApplicable;
        }
        $("#minSalary").val(minVal);
        $("#maxSalary").val(maxVal);
        $('#minMaxSalaryLbl').text('<?php echo __("Min");?>' + " : " + minVal + " " + '<?php echo __("Max");?>' + " : " + maxVal);
    });
}
    
function updateCurrencyList(payGrade, currencyId, currencyName) {

    // don't check if not selected
    if (payGrade == '') {
        // remove all options except first
        $("#salary_currency_id option:not(:first)").remove().val('');
        clearMinMax();
        return;
    }

    var url = '<?php echo url_for('pim/getUnassignedCurrenciesJson?empNumber=' . $empNumber . '&paygrade=') ;?>' + payGrade;

    $.getJSON(url, function(data) {

        var numOptions = data.length;
        var optionHtml = '<option value="">-- <?php echo __("Select")?> --</option>';

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
    
$(document).ready(function() {

    //hide add section
    $("#changeSalary").hide();
    $("#salaryRequiredNote").hide();

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

    $("#addSalary").click(function() {

        removeEditLinks();
        clearMessageBar();
        $('div#changeSalary label.error').hide();   
        $('#actionClearBr').hide();
        

        //changing the headings
        $("#headchangeSalary").text(lang_addSalary);
        $("div#tblSalary .chkbox").hide();
        $("#salaryCheckAll").hide();

        //hiding action button section
        $("#actionSalary").hide();

        $('#salary_id').val("");
        $('#salary_sal_grd_code').val("");
        $("#salary_currency_id option:not(:first)").remove();
        $('#salary_currency_id').val("");        
        clearMinMax();
        
        $("#salary_basic_salary").val("");
        $("#salary_payperiod_code").val("");
        $("#salary_component").val("");
        $("#salary_comments").val("");

        //show add form
        $("#changeSalary").show();
        $("#salaryRequiredNote").show();
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
        
        if (!isNaN(amount) && !isNaN(min) && !isNaN(max)) {
            if ((amount > max) || (amount < min)) {
                valid = false;
            }
        }
        return valid;
        
    });

    //form validation
    var salaryValidator =
        $("#frmSalary").validate({
        rules: {
            'salary[sal_grd_code]': {required: true},
            'salary[currency_id]': {required: true},
            'salary[salary_component]': {required: true, maxlength: 100},
            'salary[comments]': {required: false, maxlength: 255},
            'salary[basic_salary]': {number:true, validateAmount:true, required: true}
        },
        messages: {
            'salary[sal_grd_code]': {required: lang_payPeriodRequired},
            'salary[currency_id]': {required: lang_currencyRequired},
            'salary[salary_component]': {required: lang_componentRequired, maxlength: lang_componentLength},
            'salary[comments]': {maxlength: lang_commentsLength},
            'salary[basic_salary]': {number: lang_amountShouldBeNumber, validateAmount: lang_invalidAmount, required: lang_amountRequired}
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

        $("div#tblSalary .chkbox").removeAttr("checked").show();
        
        //hiding action button section
        $("#actionSalary").show();
        $("#changeSalary").hide();
        $("#salaryRequiredNote").hide();        
        $("#salaryCheckAll").show();
        
        $('#salary_id').val('');
        
        // remove any options already in use
        $("#salary_code option[class='added']").remove();
        $('#static_salary_code').hide().val("");

    });

    $('form#frmDelSalary a.edit').live('click', function(event) {
        event.preventDefault();
        clearMessageBar();

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
                
        updateCurrencyList(salGrdCode, currencyId, currencyName);      

        $("#salaryRequiredNote").show();

        $("div#tblSalary td.check .chkbox").hide();
        $("#salaryCheckAll").hide();        
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