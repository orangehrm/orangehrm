$(document).ready(function() {
    
    executeLoadtimeActions();
    loadCheckboxBehavior();
    loadAddForm();
    loadEditForm();
    loadCancelButtonBehavior();
    loadDeleteButtonBehavior();
    
    $('#btnSave').click(function() {
        validateData();
        $('#frmSave').submit();
    });

});

function validateData() {
    
    $("#frmSave").validate({
        
        rules: {
            'terminationReason[name]' : {
                required:true,
                maxlength: 120
                /*remote: {
                   url: urlForExistingNameCheck
                }*/
            }

        },
        messages: {
            'terminationReason[name]' : {
                required: lang_nameIsRequired
                /*remote: lang_nameExists*/
            }

        }

    });
    
}

function executeLoadtimeActions() {
    
    $('#saveFormDiv').hide();
    
    $('table.data-table tbody tr:odd').addClass('odd');
    $('table.data-table tbody tr:even').addClass('even');
    
    if (recordsCount == 0) {
        $('#recordsListTable .check').hide();
    }    
    
}

function loadCheckboxBehavior() {
    
    $("#checkAll").click(function(){
        if($("#checkAll:checked").attr('value') == 'on') {
            $(".checkbox").attr('checked', 'checked');
        } else {
            $(".checkbox").removeAttr('checked');
        }
    });

    $(".checkbox").click(function() {
        
        $("#checkAll").removeAttr('checked');
        
        if(($(".checkbox").length - 1) == $(".checkbox:checked").length) {
            $("#checkAll").attr('checked', 'checked');
        }
        
        if ($(".checkbox:checked").length > 0 && $(".checkbox").length >1) {
            $('#btnDel').removeAttr('disabled');
        } else {
            $('#btnDel').attr('disabled', 'disabled');
        }
        
    });    
    
}

function loadAddForm() {
    
    $("#btnAdd").click(function(){
        
        $('#listActions').hide();
        $('#saveFormDiv').show();
        $('#saveFormHeading').text(lang_addFormHeading);
        
        $('#recordsListTable .check').hide();
        
        for (i in saveFormFieldIds) {
            $('#'+saveFormFieldIds[i]).val('');
        }
        
        $('#'+recordKeyId).val('');
        
        _removeRecordLinks();
        
        _clearErrorMessages();
        
    });
    
}

function loadEditForm() {
    
    $('#recordsListTable a').live('click', function() {
        
        $('#saveFormDiv').show();
        $('#saveFormHeading').text(lang_editFormHeading);
        
        var row = $(this).closest("tr");
        
        var i=0;
        row.children("td.tdValue").each(function(){
            
            if (saveFormFieldIds[i] !== undefined) {
                $('#'+saveFormFieldIds[i]).val($.trim($(this).text()));
            }
            
            i++;

        });
        
        $('#'+recordKeyId).val(row.find('input.checkbox:first').val());
        
        _clearErrorMessages();

        $('#recordsListTable .check').hide();
        $('#listActions').hide();

    });
    
} 

function loadCancelButtonBehavior() {
    
    $("#btnCancel").click(function(){
        
        $('#saveFormDiv').hide();
        
        $('#recordsListTable .check').show();
        
        _addRecordLinks();

        $('#listActions').show();
        
        if (recordsCount == 0) {
            $('#recordsListTable .check').hide();
        }         
        
    });
    
} 

function loadDeleteButtonBehavior() {   
    
    if ($(".checkbox:checked").length == 0) {
        $('#btnDel').attr('disabled', 'disabled');
    } 
    
    $('#btnDel').click(function(){
        $('#frmList').submit();
    });
    
}

function _removeRecordLinks() {
    $('#recordsListTable tbody td.tdName a').each(function(index) {
        $(this).parent().text($(this).text());
    });
}

function _addRecordLinks() {
    $('#recordsListTable tbody td.tdName').wrapInner('<a href="#"/>');
}

function _clearErrorMessages() {    
    $('.errorHolder').each(function(){
        $(this).empty();
    });    
}


