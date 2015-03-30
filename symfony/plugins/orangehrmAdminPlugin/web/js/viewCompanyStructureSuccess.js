$(document).ready(function() {
    
    if(!isOrganizationNameSet){
        _showMessage('warning', organizationNameNotSetValidationMessage);
    }

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
    
    $('#btnSetOrganizationName').click(function() {
        window.location.replace(organizationGeneralInformationURL);
    });

    $('#dialogYes').click(function(){
        nodeId = $('#dltNodeId').val()
        $.ajax({
            url: deleteSubunitUrl,
            type: 'post',
            data: {
                'subunitId': nodeId,
                'defaultList[_csrf_token]': $('#defaultList__csrf_token').val()
            },
            dataType: 'json',
            success: function(obj) {
                _showMessage(obj.messageType, obj.message);
                clearForm();
                _clearMessageBaloon();
                reloadTree();
                $('#dltConfirmationMsg').text("")
                $("#dltDialog").modal('hide');
            }
        });
    });

    $('#dialogNo').click(function(){
        $("#dltDialog").modal('hide');
    });

    $('#btnCancel').click(function() {
        $("#unitDialog").modal('hide');
    });

    $('#btnSave').click(function() {
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
            $("#title").text(lang_editUnit);
            openDialog();
        }
    });
}

function setViewMode(){
    $('.addButton').hide()
    $('.deleteButton').hide()
    $('.editLink').hide()
    $('.labelNode').show()
    $("#btnEdit").attr('value', lang_edit)
}

function setEditMode(){
    $('.labelNode').hide()
    $('.addButton').show()
    $('.deleteButton').show()
    $('.editLink').show()
    $("#btnEdit").attr('value', lang_done)
}


function openDialog(){
    $("#unitDialog").modal('show');
}

function closeDialog(){
    $("#unitDialog").modal('hide');
}

function addChildToNode(nodeId) {
    clearForm();
    _clearMessage();
    nodeName = $('#treeLink_edit_' + nodeId).html();
    $('#lblParentNotice').remove();
    $('<li class="line" id="lblParentNotice">'+lang_addNote +' <span class="boldText">' + nodeName + '</span></li>').
        insertBefore('#lastElement');

    $('#hdnParent').val(nodeId);
    showForm();
    $("#title").text(lang_addUnit);
    clearErrors();
    openDialog();
}

function deleteNode(nodeId) {
    $('#dltConfirmationMsg').text("")
    nodeName = $('#treeLink_edit_' + nodeId).html();
    $('#dltNodeId').attr('value', nodeId)
    $('#dltConfirmationMsg').append(lang_delete_warning+'<br /><br />'+lang_delete_confirmation)
    $("#dltDialog").modal('show');
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
            _clearMessageBaloon();
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
    $("span.validation-error").each(function() {
        $('#' + $(this).attr('for')).removeClass('validation-error');
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
    $('#messageDiv').append('<div class="message ' + messageType + '" id="divMessageBar" generated="true">'+ message + 
        "<a class='messageCloseButton' href='#'>"+closeText+"</a>" +  '</div>');
}

function _clearMessage() {
    $('#messageDiv div[generated="true"]').remove();
}

function _clearMessageBaloon (){
    $('#divMessageBar').delay(2000)
        .fadeOut("slow", function () {
            $('#divMessageBar').remove();
        }); 
}