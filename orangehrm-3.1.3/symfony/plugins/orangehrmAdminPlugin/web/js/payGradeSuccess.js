$(document).ready(function() {
    $('#addEditCurrency').hide();
   
    $('#btnSave').click(function() {  
        
        if($('#btnSave').val() == lang_edit){
            $('#payGrade_name').removeAttr('disabled');
            $('#btnSave').val(lang_save);
            
        } else if ($('#btnSave').val() == lang_save){
            $('#payGrade_payGradeId').val(payGradeId);
            $('#frmPayGrade').submit();
        }        
    });
    
    $('#btnDeleteCurrency').attr('disabled', 'disabled');
    $("#currencyCheckAll").click(function() {
    	if($(":checkbox").length == 1) {
            $('#btnDeleteCurrency').attr('disabled','disabled');
        }
        else {
            if($("#currencyCheckAll").is(':checked')) {
                $('#btnDeleteCurrency').removeAttr('disabled');
            } else {
                $('#btnDeleteCurrency').attr('disabled','disabled');
            }
        }
    });
    
    $(':checkbox[name*="delCurrencies[]"]').click(function() {
    	if($(':checkbox[name*="delCurrencies[]"]').is(':checked')) {
            $('#btnDeleteCurrency').removeAttr('disabled');
        } else {
            $('#btnDeleteCurrency').attr('disabled','disabled');
        }
    });
    
    //auto complete
    $("#payGradeCurrency_currencyName").autocomplete(currencies, {
        formatItem: function(item) {
            return item.name;
        },
        matchContains:true
    }).result(function(event, item) {
        $('.curName').html("");
        $('#payGradeCurrency_currencyName').removeClass("error");
        $('#payGradeCurrency_currencyName').valid();
    });
    
    $('#btnCancel').click(function() {
        window.location.replace(viewPayGradesUrl);
    });
                
    if(payGradeId > 0){
        $('#payGrade_name').attr('disabled','disabled');
        $('#btnSave').val(lang_edit);
        $('#payGradeCurrency_payGradeId').val(payGradeId);
    }
    
    
    ///// JQuery for currency list
    
    $('#btnAddCurrency').click(function() {
        $('#addEditCurrency').show();
        
        $('#addPaneCurrency').show();
        $('#actionButtons').show();
        $('#addDeleteBtnDiv').hide();
        $('.check').hide();
        validatorCurr.resetForm();
        $('#currencyHeading').text(lang_addCurrency);
        $('#payGradeCurrency_currencyName').each(function(){
            if($(this).parent().css('display') == 'block') {
                if ($(this).val() == '' || $(this).val() == lang_typeHint) {
                    $(this).addClass("inputFormatHint").val(lang_typeHint);
                }
            }
        });
   
        $('#payGradeCurrency_currencyName').one('focus', function() {
        
            if ($(this).hasClass("inputFormatHint")) {
                $(this).val("");
                $(this).removeClass("inputFormatHint");
            }

        });
    });
    
    $('#cancelButton').click(function(){
        $('#addEditCurrency').hide();
        
        $('#addPaneCurrency').hide();
        $('#actionButtons').hide();
        $('#addDeleteBtnDiv').show();
        $('#currencyHeading').text(lang_assignedCurrency);
        $('.check').show();
        validatorCurr.resetForm();
    });
    
    $('#btnSaveCurrency').click(function(){
        if ($('#payGradeCurrency_currencyName').val() == lang_typeHint) {
            $('#payGradeCurrency_currencyName').val("");
        }
        $('#frmCurrency').submit();
    });
    
    $('.editLink').click(function(event) {
        $('#addEditCurrency').show();
		
        event.preventDefault();
        
        validatorCurr.resetForm();
        var row = $(this).closest("tr");
        var curId = row.find('input.checkboxCurr:first').val();
        var url = getCurrencyDetailsUrl+"?curId="+curId+"&payGradeId="+payGradeId;
        var curName = row.find('a.editLink').text();
        getCurrencyDetails(url, curName);
        $('#currencyHeading').text(lang_editCurrency);
        
        $('#addPaneCurrency').show();
        $('#actionButtons').show();
        $('.check').hide();
        $('#addDeleteBtnDiv').hide();
       
    });
    
    //if check all button clicked
    $("#currencyCheckAll").click(function() {
        $("table#tblCurrencies tbody input.checkboxCurr").removeAttr("checked");
        if($("#currencyCheckAll").attr("checked")) {
            $("table#tblCurrencies tbody input.checkboxCurr").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $("table#tblCurrencies tbody input.checkboxCurr").click(function() {
        $("#currencyCheckAll").removeAttr('checked');
        if($("table#tblCurrencies tbody input.checkboxCurr").length == $("table#tblCurrencies tbody input.checkboxCurr:checked").length) {
            $("#currencyCheckAll").attr('checked', 'checked');
        }
    });
    
    $('#btnDeleteCurrency').click(function() {

        var checked = $('#frmDelCurrencies input:checked').length;

        if (checked > 0) {
            $('#frmDelCurrencies').submit();
        }
    });

    $.validator.addMethod("uniquePayGradeName", function(value, element, params) {
        var temp = true;
        var currentName;
        var id = $('#payGrade_payGradeId').val();
        var vcCount = payGradeList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == payGradeList[j].id){
                currentName = j;
            }
        }
        var i;
        vcName = $.trim($('#payGrade_name').val()).toLowerCase();

        for (i=0; i < vcCount; i++) {
            arrayName = payGradeList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentName != null){
            if(vcName == payGradeList[currentName].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });
    
    var validator = $("#frmPayGrade").validate({

        rules: {
            'payGrade[name]' : {
                required:true,
                maxlength: 50,
                uniquePayGradeName: true
            }
        },
        messages: {
            'payGrade[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors,
                uniquePayGradeName: lang_uniquePayGradeName
            }
        }

    });
    
    $.validator.addMethod("currencyValidation", function(value, element, params) {
        
        var curCount = currencyList.length;
        var isValid = false;
        var curName = value;
        var inputName = $.trim(curName).toLowerCase();
        if(inputName != ""){
            var i;
            for (i=0; i < curCount; i++) {
                var arrayName = currencyList[i].name.toLowerCase();
                if (inputName == arrayName) {
                    isValid =  true;
                    break;
                }
            }
        }
        return isValid;
    });
    
    $.validator.addMethod("validSalaryRange", function(value, element, params) {
        
        var isValid = true;
        var minSal = $('#payGradeCurrency_minSalary').val();
        var maxSal = $('#payGradeCurrency_maxSalary').val();
        
        if(minSal != ""){
            minSal = parseFloat(minSal);
        }
        if(maxSal != ""){
            maxSal = parseFloat(maxSal);
        }

        if(minSal > maxSal && maxSal != "") {
            isValid = false;
        }
        return isValid;
    });
    
    $.validator.addMethod("twoDecimalsMin", function(value, element, params) {
        
        var isValid = false;
        var minSal = $('#payGradeCurrency_minSalary').val();
        var match = minSal.match(/^\$?([0-9]{1,3},([0-9]{3},)*[0-9]{3}|[0-9]+)(.[0-9][0-9])?$/);
        if(match) {
            isValid = true;
        }
        if (minSal == ""){
            isValid = true;
        }
        return isValid;
    });
    
    $.validator.addMethod("twoDecimalsMax", function(value, element, params) {
        
        var isValid = false;
        var maxSal = $('#payGradeCurrency_maxSalary').val();
        var match = maxSal.match(/^\$?([0-9]{1,3},([0-9]{3},)*[0-9]{3}|[0-9]+)(.[0-9][0-9])?$/);
        if(match) {
            isValid = true;
        }
        if (maxSal == ""){
            isValid = true;
        }
        return isValid;
    });
       
    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentName;
        var id = $('#payGradeCurrency_currencyId').val();
        var vcCount = assignedCurrencyList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == assignedCurrencyList[j].id){
                currentName = j;
            }
        }
        var i;
        vcName = $.trim($('#payGradeCurrency_currencyName').val()).toLowerCase();

        for (i=0; i < vcCount; i++) {
            arrayName = assignedCurrencyList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentName != null){
            if(vcName == assignedCurrencyList[currentName].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });
    
    var validatorCurr = $("#frmCurrency").validate({

        rules: {
            'payGradeCurrency[currencyName]' : {
                required:true,
                maxlength: 50,
                currencyValidation: true,
                uniqueName: true,
                onkeyup: false
            },
            'payGradeCurrency[minSalary]' : {
                twoDecimalsMin: true,
                min: 0,
                max:999999999.99
            },
            'payGradeCurrency[maxSalary]' : {
                twoDecimalsMax:true,
                min: 0,
                max:999999999.99,
                validSalaryRange: true
            }
        },
        messages: {
            'payGradeCurrency[currencyName]' : {
                required: lang_currencyRequired,
                maxlength: lang_exceed50Charactors,
                currencyValidation: lang_validCurrency,
                uniqueName: lang_currencyAlreadyExist
            },
            'payGradeCurrency[minSalary]' : {
                twoDecimalsMin: lang_salaryShouldBeNumeric,
                min: lang_negativeAmount,
                max:lang_tooLargeAmount
            },
            'payGradeCurrency[maxSalary]' : {
                twoDecimalsMax: lang_salaryShouldBeNumeric,
                min: lang_negativeAmount,
                max:lang_tooLargeAmount,
                validSalaryRange: lang_validSalaryRange
            }
        }

    });
});

function getCurrencyDetails(url, curName){

    $.getJSON(url, function(data) {

        $('#payGradeCurrency_currencyId').val(data.currency_id);
        $('#payGradeCurrency_currencyName').val(data.currency_id+" - "+curName);
        $('#payGradeCurrency_minSalary').val(data.minSalary);
        $('#payGradeCurrency_maxSalary').val(data.maxSalary);

    });
}