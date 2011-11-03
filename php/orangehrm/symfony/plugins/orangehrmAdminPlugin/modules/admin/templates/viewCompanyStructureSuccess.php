<style type="text/css">
    #divCompanyStructureContainer {
        width: 49%;
        display: block;
        float: left;
        padding-left: 20px;
    }

    #divSubunitFormContainer {
        width: 49%;
        display: block;
        float: left;
    }

    #divEmployeeListContainer {
        width: 80%;
        height: 70%;
    }

    div#divSubunitFormContainer form#ohrmFormComponent_Form input#txtName.formInputText{
        width: 300px;
        padding-left: 5px;
    }

    div#divSubunitFormContainer form#ohrmFormComponent_Form input#txtUnit_Id.formInputText{
        width: 300px;
        padding-left: 5px;
    }
    div#divSubunitFormContainer form#ohrmFormComponent_Form textarea#txtDescription.formTextArea{
        height: 100px;
        width: 300px;
        margin-left: 0px;
    }

    div#divSubunitFormContainer div.requirednotice{
        margin-left: 8px;
        font-size: 11px;
    }
    div#divSubunitFormContainer form#ohrmFormComponent_Form label#lblParentNotice{
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

    #heading{
        width: 200px;
        padding-left: 20px;
        font-size: 18px;
        font-weight: bold;
        margin-top: 0px;
    }

    div#divSubunitFormContainer form#ohrmFormComponent_Form label{
        padding-left: 5px;
        width: 100px;
    }

    div#divSubunitFormContainer form#ohrmFormComponent_Form label.error{
        padding-left: 105px;
        width: 250px !important;
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

<div id="messageDiv"></div>
<br class="clear"/>
<label id="heading"><?php echo __("Company Structure") ?></label>
<input type="button" class="editbutton" name="btnEdit" id="btnEdit"
       value="<?php echo __("Edit"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
<br class="clear"/>
<br class="clear"/>
<div id="divCompanyStructureContainer"><?php $tree->render(); ?></div>

<div id="unitDialog" title="" style="display:none;">
    <div id="divSubunitFormContainer" style="width: 450px"><?php $form->render();
$form->printRequiredFieldsNotice(); ?></div>
</div>

<div id="dltDialog" title="<?php echo __("OrangeHRM - Confirmation Required"); ?>"  style="display:none;">
    <br class="clear"/>
    <div id="dltConfirmationMsg"></div>
    <input type="hidden" id="dltNodeId" value=""/>
    <div class="dialogButtons">
        <input type="button" id="dialogYes" class="savebutton" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogNo" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>


<script type="text/javascript">
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_done = "<?php echo __("Done"); ?>";
    var lang_addUnit = "<?php echo "OrangeHRM - ".__("Add Unit"); ?>";
    var lang_editUnit = "<?php echo "OrangeHRM - ".__("Edit Unit"); ?>";
    var lang_confirmationPart2 = "<?php echo __("and all the sub units under it will be permanantly deleted"); ?>";
    var lang_addNote = "<?php echo __("This department will be added under"); ?>";
    var lang_nameRequired = "<?php echo __("Name required"); ?>";
    var lang_max = "<?php echo __("Maximum allowed character limit is") . " "; ?>";
    var lang_noDescriptionSpecified = "<?php echo __("Description is not specified"); ?>";

    $(document).ready(function() {

        $("#unitDialog").dialog({
            autoOpen: false,
            modal: true,
            width: 470,
            height: 280,
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
                url: '<?php echo public_path('index.php/admin/deleteSubunit'); ?>',
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
                $('#unitDialog').dialog('close');}
        });
        $('#ohrmFormComponent_Form').validate({
            rules: {
                txtName: { required: true, maxlength: 100 },
                txtDescription: { maxlength: 400 },
                txtUnit_Id: { maxlength: 100 }
            },
            messages: {
                txtName: {
                    required: lang_nameRequired,
                    maxlength: lang_max+"100"
                },
                txtDescription: {
                    maxlength: lang_max+"400"
                },
                txtUnit_Id: {
                    maxlength: lang_max+"100"
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
            url: '<?php echo public_path('index.php/admin/getSubunit'); ?>',
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
            url: '<?php echo public_path('index.php/admin/getSubunit'); ?>',
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
        $('<label id="lblParentNotice">'+lang_addNote +'<span class="boldText">' + nodeName + '</span></label>').insertAfter($('#txtDescription').next('br'));

        $('#hdnParent').val(nodeId);
        showForm();
        $("#ui-dialog-title-unitDialog").text(lang_addUnit)
        openDialog()
    }

    function deleteNode(nodeId) {
        $('#dltConfirmationMsg').text("")
        nodeName = $('#treeLink_edit_' + nodeId).html();
        $('#dltNodeId').attr('value', nodeId)
        $('#dltConfirmationMsg').append(nodeName+" "+lang_confirmationPart2)
        $("#dltDialog").dialog("open")
    }

    function saveNode() {

        if (!$('#ohrmFormComponent_Form').valid()) {
            return false;
        }

        $.ajax({
            async: false,
            url: '<?php echo public_path('index.php/admin/saveSubunit'); ?>',
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
            url: '<?php echo public_path('index.php/admin/viewCompanyStructureHtml'); ?>/seed/' + Math.random(),
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

</script>

<?php $tree->printJavascript(); ?>