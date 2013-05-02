var originalSkillName = '';

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

$.validator.addMethod("uniqueName", function(value, element, params) {
    
    /* If in edit mode and original name (value at loading time), return true */
    if ($('#saveFormHeading').text() == lang_editFormHeading && $.trim($('#skill_name').val()) == originalSkillName) {
        return true;
    }
    
    var temp = true;
    var currentSkill;
    var id = parseInt(id,10);
    var vcCount = skillList.length;
    for (var j=0; j < vcCount; j++) {
        if(id == skillList[j].id){
        	currentSkill = j;
        }
    }
    var i;
    skillName = $.trim($('#skill_name').val()).toLowerCase();
    for (i=0; i < vcCount; i++) {

        arrayName = skillList[i].name.toLowerCase();
        if (skillName == arrayName) {
            temp = false
            break;
        }
    }
    if(currentSkill != null){
        if(skillName == skillList[currentSkill].name.toLowerCase()){
            temp = true;
        }
    }
	
    return temp;
});

function validateData() {
    
    $("#frmSave").validate({

        rules: {
            'skill[name]' : {
                required:true,
                maxlength: 120,
                uniqueName:true
            },
            'skill[description]' : {
                maxlength: 250
            }

        },
        messages: {
            'skill[name]' : {
                required: lang_nameIsRequired,
                uniqueName: lang_nameExists
            },
            'skill[description]' : {
                maxlength: lang_descLengthExceeded
            }

        }

    });
    
}

function executeLoadtimeActions() {
    
    $('#saveFormDiv').hide();
    
    $('table.data-table tbody tr:odd').addClass('odd');
    $('table.data-table tbody tr:even').addClass('even');
    
    if (recordsCount == 0) {
        $('#recordsListTable th.check').hide();
        $('#recordsListTable td.check').hide();
    }    
    
}

function loadCheckboxBehavior() {
    
    $("#checkAll").click(function(){
        if($("#checkAll:checked").attr('value') == 'on') {
            $(".checkboxAtch").attr('checked', 'checked');
        } else {
            $(".checkboxAtch").removeAttr('checked');
        }
    });

    $(".checkboxAtch").click(function() {
        
        $("#checkAll").removeAttr('checked');
        
        if(($(".checkboxAtch").length - 1) == $(".checkboxAtch:checked").length) {
            $("#checkAll").attr('checked', 'checked');
        }
        
        if ($(".checkboxAtch:checked").length > 0 && $(".checkboxAtch").length >1) {
            $('#btnDel').removeAttr('disabled');
        } else {
            $('#btnDel').attr('disabled', 'disabled');
        }
        
    });    
    
}

function loadAddForm() {
    
    $("#btnAdd").click(function(){
        
        $('#saveFormDiv').show();
        $('#saveFormHeading').text(lang_addFormHeading);
        
        $('#recordsListTable th.check').hide();
        $('#recordsListTable td.check').hide();
        
        for (i in saveFormFieldIds) {
            $('#'+saveFormFieldIds[i]).val('');
        }
        
        $('#'+recordKeyId).val('');
        
        _removeRecordLinks();
        
        _clearErrorMessages();

        $('#listActions').hide();
        
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
                if (i == 0) {
                    originalSkillName = $.trim($(this).text());
                }
            }
            
            i++;

        });
        
        $('#'+recordKeyId).val(row.find('input.checkboxAtch:first').val());
        
        _clearErrorMessages();

        $('#recordsListTable th.check').hide();
        $('#recordsListTable td.check').hide();
        $('#listActions').hide();

    });
    
} 

function loadCancelButtonBehavior() {
    
    $("#btnCancel").click(function(){
        
        $('#saveFormDiv').hide();
        
        $('#recordsListTable th.check').show();
        $('#recordsListTable td.check').show();
        
        _addRecordLinks();

        $('#listActions').show();
        
        if (recordsCount == 0) {
            $('#recordsListTable th.check').hide();
            $('#recordsListTable td.check').hide();
        }         
        
    });
    
} 

function loadDeleteButtonBehavior() {   
    
    if ($(".checkboxAtch:checked").length == 0) {
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


