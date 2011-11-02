<?php
$tree->addScriptContent("
    $('#ohrmFormComponent_Form').validate({
        rules: {
            txtName: { required: true, maxlength: 100 },
            txtDescription: { maxlength: 400 },
            txtUnitId: { maxlength: 100 }
        },
        messages: {
            txtName: {
                required: 'Department name is required',
                maxlength: 'Maximum character limit exceeded for unit name'
            },
            txtDescription: {
                maxlength: 'Maximum character limit exceeded for description'
            },
            txtUnitId: {
                maxlength: 'Maximum character limit exceeded for unit id'
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
");
?>

<style type="text/css">
    #divDepartmentTreeContainer {
        width: 49%;
        display: block;
        float: left;
    }

    #divDepartmentFormContainer {
        width: 49%;
        display: block;
        float: left;
    }

    #divEmployeeListContainer {
        width: 80%;
        height: 70%;
    }
    div#divDepartmentFormContainer form#ohrmFormComponent_Form label{
        padding-left: 5px;
        width: 100px;
    }

    div#divDepartmentFormContainer form#ohrmFormComponent_Form input#txtName.formInputText{
        width: 300px;
        padding-left: 5px;
    }

    div#divDepartmentFormContainer form#ohrmFormComponent_Form input#txtUnit_Id.formInputText{
        width: 300px;
        padding-left: 5px;
    }
    div#divDepartmentFormContainer form#ohrmFormComponent_Form textarea#txtDescription.formTextArea{
        height: 100px;
        width: 300px;
        margin-left: 0px;
    }

    div#divDepartmentFormContainer div.requirednotice{
        margin-left: 8px;
        font-size: 11px;
    }
    div#divDepartmentFormContainer form#ohrmFormComponent_Form label#lblParentNotice{
        width: 400px;
    }

    #tooltip {
        position: absolute;
        z-index: 3000;
        border: 1px solid #111;
        background-color: #eee;
        padding: 5px;
        opacity: 0.85;
    }
    #tooltip h3, #tooltip div { margin: 0; }

    html body label{
        width: 200px;
        padding-left: 20px;
        font-size: 15px;
        margin-top: 0px;
}

</style>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>
<?php echo javascript_include_tag('jquery.tooltip.js') ?>
<?php use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css'); ?>
<?php use_javascript('../../../scripts/jquery/jquery.autocomplete.js'); ?>
<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>

<br class="clear"/>
<div id="messageDiv"></div>
<br class="clear"/>
<!--<div id="editButton" style="text-align: left; padding-left: 10px">-->
        <label><?php echo __("Organization Structure") ?></label>
    <input type="button" class="editbutton" name="btnEdit" id="btnEdit"
           value="<?php echo __("Edit"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
<!--</div>-->
<br class="clear"/>
<br class="clear"/>
<div id="divDepartmentTreeContainer"><?php $tree->render(); ?></div>

<div id="unitDialog" title="" style="display:none;">
    <div id="divDepartmentFormContainer" style="width: 450px"><?php $form->render();
$form->printRequiredFieldsNotice(); ?></div>
</div>

<div id="dltDialog" title="<?php echo __("Confirmation required"); ?>"  style="display:none;">
    <div id="dltConfirmationMsg"></div>
    <input type="hidden" id="dltNodeId" value=""/>
    <div class="dialogButtons">
        <input type="button" id="dialogYes" class="savebutton" value="<?php echo __('Yes'); ?>" />
        <input type="button" id="dialogNo" class="savebutton" value="<?php echo __('No'); ?>" />
    </div>
</div>


<script type="text/javascript">
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_done = "<?php echo __("Done"); ?>";
    var lang_addUnit = "<?php echo __("Add Unit"); ?>";
    var lang_editUnit = "<?php echo __("Edit Unit"); ?>";
    var lang_noDescriptionSpecified = "<?php echo __("No description specified"); ?>";
    var lang_confirmationPart1 = "<?php echo __("You are going to delete"); ?>";
    var lang_confirmationPart2 = "<?php echo __("and all elements under it. This action cannot be undone. Are you sure you want to continue ?"); ?>";

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
            height: 150,
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
                url: '<?php echo public_path('index.php/admin/deleteDepartment'); ?>',
                type: 'post',
                data: {
                    'departmentId': nodeId
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
            saveNode();
            $('#unitDialog').dialog('close');
        });
    });


    function loadNode(nodeId) {
        clearForm();
        clearErrors();
        $.ajax({
            async: false,
            url: '<?php echo public_path('index.php/admin/loadDepartment'); ?>',
            type: 'post',
            data: {
                'departmentId': nodeId
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
            url: '<?php echo public_path('index.php/admin/loadDepartment'); ?>',
            type: 'post',
            data: {
                'departmentId': nodeId
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
        $('<label id="lblParentNotice">This department will be added under <span class="boldText">' + nodeName + '</span></label>').insertAfter($('#txtDescription').next('br'));

        $('#hdnParent').val(nodeId);
        showForm();
        $("#ui-dialog-title-unitDialog").text(lang_addUnit)
        openDialog()
    }

    function deleteNode(nodeId) {
        $('#dltConfirmationMsg').text("")
        nodeName = $('#treeLink_edit_' + nodeId).html();
        $('#dltNodeId').attr('value', nodeId)
        $('#dltConfirmationMsg').append(lang_confirmationPart1+" "+nodeName+" "+lang_confirmationPart2)
        $("#dltDialog").dialog("open")
    }

    function saveNode() {
        if (!$('#ohrmFormComponent_Form').valid()) {
            return;
        }

        $.ajax({
            async: false,
            url: '<?php echo public_path('index.php/admin/saveDepartment'); ?>',
            type: 'post',
            data: $('#ohrmFormComponent_Form').serialize(),
            dataType: 'json',
            success: function(obj) {
                if (obj.messageType == 'success') {
                    reloadTree();                 
                    loadNode(obj.affectedId);
                }
                _showMessage(obj.messageType, obj.message);
            }
        });
    }

    function resetForm() {
        loadNode(parseInt($('#hdnId').val()));
        $('#divDepartmentFormContainer div[generated="true"]').remove();
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
            url: '<?php echo public_path('index.php/admin/viewDepartmentTreeHtml'); ?>/seed/' + Math.random(),
            success: function(response) {
                $('#divDepartmentTreeContainer').html(response);
                $('.labelNode').hide()
            }
        });
    }

    function _showMessage(messageType, message) {
        _clearMessage();
        $('#messageDiv').append('<div class="messageBalloon_' + messageType + ' id="divMessageBar" generated="true" style="width: 95%; margin: 0px 0px 8px 0px;">'+ message + '</div>');
    }

    function _clearMessage() {
        $('#messageDiv div[generated="true"]').remove();
    }

</script>

<?php $tree->printJavascript(); ?>