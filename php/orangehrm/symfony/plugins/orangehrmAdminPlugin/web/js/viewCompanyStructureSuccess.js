$(document).ready(function() {

    $("#unitDialog").dialog({
        autoOpen: false,
        modal: true,
        width: 470,
        height: 300,
        position: 'middle'
    });

    $("#dltDialog").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        height: 130,
        position: 'middle'
    });

    setViewMode()

    $('#btnEdit').click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnEdit").attr('value') == lang_edit) {
            _clearMessage()
            setEditMode()
        }
        else {
            _clearMessage()
            setViewMode()
        }
    });

    $('span[id^=\"span_\"]').tooltip({
        bodyHandler: function() {
            var descript = loadToolTip(parseInt($(this).attr('id').replace('span_', '')));
            return (descript != "") ? descript : lang_noDescriptionSpecified;
        }
    });

    $('#dialogYes').click(function(){
        nodeId = $('#dltNodeId').val()
        $.ajax({
            url: deleteSubunitUrl,
            type: 'post',
            data: {
                'subunitId': nodeId
            },
            dataType: 'json',
            success: function(obj) {
                _showMessage(obj.messageType, obj.message);
                clearForm();
                reloadTree();
                $('#dltConfirmationMsg').text("")
                $("#dltDialog").dialog("close")
            }
        });
    });

    $('#dialogNo').click(function(){
        $("#dltDialog").dialog("close")
    });

    $('#ohrmFormActionButton_Cancel').click(function() {
        $('#unitDialog').dialog('close');
    });

    $('#ohrmFormActionButton_Save').click(function() {
        if(saveNode()){
            closeDialog();
        }
    });
    $('#ohrmFormComponent_Form').validate({
        rules: {
            txtName: {
                required: true,
                maxlength: 100
            },
            txtDescription: {
                maxlength: 400
            },
            txtUnit_Id: {
                maxlength: 100
            }
        },
        messages: {
            txtName: {
                required: lang_nameRequired,
                maxlength: lang_max_100
            },
            txtDescription: {
                maxlength: lang_max_400
            },
            txtUnit_Id: {
                maxlength: lang_max_100
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});


function loadNode(nodeId) {
    clearForm();
    clearErrors();
    $.ajax({
        async: false,
        url: getSubunitUrl,
        type: 'post',
        data: {
            'subunitId': nodeId
        },
        dataType: 'json',
        success: function(obj) {
            $('#ohrmFormComponent_Form label.idValueLabel:first').html(obj.id);
            $('#hdnId').val(obj.id);
            $('#txtName').val(obj.name);
            $('#txtDescription').val(obj.description);
            $('#txtUnit_Id').val(obj.unitId);
            showForm();
            $("#ui-dialog-title-unitDialog").text(lang_editUnit)
            openDialog()
        }
    });
}

function loadToolTip(nodeId){
    $.ajax({
        async: false,
        url: getSubunitUrl,
        type: 'post',
        data: {
            'subunitId': nodeId
        },
        dataType: 'json',
        success: function(obj) {
            description = obj.description
        }
    });

    return description;
}


function setViewMode(){
    $('.addLink').hide()
    $('.deleteLink').hide()
    $('.editLink').hide()
    $('.labelNode').show()
    $("#btnEdit").attr('value', lang_edit)
}

function setEditMode(){
    $('.labelNode').hide()
    $('.addLink').show()
    $('.deleteLink').show()
    $('.editLink').show()
    $("#btnEdit").attr('value', lang_done)
}


function openDialog(){
    $("#unitDialog").dialog("open")
}

function closeDialog(){
    $("#unitDialog").dialog("close")
}

function addChildToNode(nodeId) {
    clearForm();
    _clearMessage();
    nodeName = $('#treeLink_edit_' + nodeId).html();
    $('#lblParentNotice').remove();
    $('<label id="lblParentNotice">'+lang_addNote +' <span class="boldText">' + nodeName + '</span></label>').insertAfter($('#txtDescription').next('br'));

    $('#hdnParent').val(nodeId);
    showForm();
    $("#ui-dialog-title-unitDialog").text(lang_addUnit)
    clearErrors()
    openDialog()
}

function deleteNode(nodeId) {
    $('#dltConfirmationMsg').text("")
    nodeName = $('#treeLink_edit_' + nodeId).html();
    $('#dltNodeId').attr('value', nodeId)
    $('#dltConfirmationMsg').append(lang_delete_warning+'<br /><br />'+lang_delete_confirmation)
    $("#dltDialog").dialog("open")
}

function saveNode() {

    if (!$('#ohrmFormComponent_Form').valid()) {
        return false;
    }

    $.ajax({
        async: false,
        url: saveSubunitUrl,
        type: 'post',
        data: $('#ohrmFormComponent_Form').serialize(),
        dataType: 'json',
        success: function(obj) {
            if (obj.messageType == 'success') {
                reloadTree();
                loadNode(obj.affectedId);
            }
            _showMessage(obj.messageType, obj.message);
            closeDialog()
        }
    });
}

function resetForm() {
    loadNode(parseInt($('#hdnId').val()));
    $('#divSubunitFormContainer div[generated="true"]').remove();
    $('#lblParentNotice').remove();
    clearErrors();
}

function clearErrors() {
    $("label.error[generated='true']").each(function() {
        $('#' + $(this).attr('for')).removeClass('error');
        $(this).remove();
    });
}

function reloadTree() {
    $.ajax({
        async: false,
        url: viewCompanyStructureHtmlUrl + Math.random(),
        success: function(response) {
            $('#divCompanyStructureContainer').html(response);
            $('.labelNode').hide()
        }
    });
}

function _showMessage(messageType, message) {
    _clearMessage();
    $('#messageDiv').append('<div class="messageBalloon_' + messageType + ' id="divMessageBar" generated="true" style="width: 40%;">'+ message + '</div>');
}

function _clearMessage() {
    $('#messageDiv div[generated="true"]').remove();
}