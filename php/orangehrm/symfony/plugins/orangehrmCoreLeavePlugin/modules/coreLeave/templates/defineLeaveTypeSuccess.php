<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js')?>"></script>
<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/defineLeaveTypeSuccess'); ?>
<div class="formpage">

    <?php echo $form->getMessage(); ?>

    <div class="outerbox" style="width:auto;">

        <div class="mainHeading">
            <h2 class="paddingLeft"><span id="editStatus"><?php echo __('Add'); ?></span><?php echo __(' Leave Type'); ?></h2>
        </div>

        <form name="frmLeaveType" id="frmLeaveType" action="defineLeaveType" method="post">
        
        <?php echo $form['hdnLeaveTypeId']->render(); ?>
        <?php echo $form['hdnOriginalLeaveTypeName']->render(); ?>
        <?php echo $form['hdnUndeleteId']->render(); ?>

            <table class="outerMost">
        <tr valign="top">
            <td width="70">
        <?php echo __('Name') . " <span class=required>*</span>"; ?>
        </td>
        <td>
            <?php echo $form['txtLeaveTypeName']->render(); ?>
            <div>
                <?php echo $form['txtLeaveTypeName']->renderError(); ?>
                <div class="errorHolder"></div>
            </div>

            <?php echo $form['hdnSavingMode']->render(); ?>
            <?php echo $form['_csrf_token']; ?>

        </td>
        </tr>
        </table>

        <div class="formbuttons paddingLeft">
            <input type="button" id="saveButton" value="<?php echo __('Save'); ?>" class="savebutton" />
            <input type="reset"  id="resetButton" value="<?php echo __('Reset'); ?>" class="savebutton" />
            <input type="button" id="backButton" value="<?php echo __('Back'); ?>" class="savebutton" />
        </div>

    </form>

    </div>
</div>
<div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>
<div id="undeleteDialog" title="OrangeHRM - Confirmation Required"  style="display:none;">

This is a deleted leave type. Reactivate it?
<br /><br />
<strong>Yes</strong> - Leave type will be undeleted<br />
<strong>No</strong> - A new leave type will be created with same name<br />
<strong>Cancel</strong> - Will take no action
<br /><br />
<div class="dialogButtons">
<input type="button" id="undeleteYes" class="savebutton" value="Yes" />
<input type="button" id="undeleteNo" class="savebutton" value="No" />
<input type="button" id="undeleteCancel" class="savebutton" value="Cancel" />
</div>
</div> <!-- undeleteDialog -->

<script type="text/javascript">

$(document).ready(function(){
    if($('#leaveType_hdnSavingMode').val() == "update") {
        $("#editStatus").html("Edit");
    }

    $("#resetButton").click(function() {
        
    });

    $('#saveButton').click(function(){

        if (validate()) {

           if (isDeletedLeaveType()) {
                $("#undeleteDialog").dialog("open");
            } else {
                $('#frmLeaveType').submit();
            }
        }
        
    });

    $('#leaveType_txtLeaveTypeName').change(function() {
        $(".error").empty();
        validate();
    });
    
    function validate() {

        var errorMessage;
        var element = $('#leaveType_txtLeaveTypeName');

        if ($.trim(element.val()) == '') {
            errorMessage = '<?php echo __('Please provide a leave type name'); ?>';
            showErrorMessages(element, errorMessage);
            return false;
        }
        else if (leaveTypeExists()) {
            var showLeaveExistsError = false;

            if ($('#leaveType_hdnSavingMode').val() == 'update') {
                var originalName  = $.trim($("#leaveType_hdnOriginalLeaveTypeName").val());
                var updatedName   = $.trim($("#leaveType_txtLeaveTypeName").val());
                if (originalName.toLowerCase() != updatedName.toLowerCase()) {
                    showLeaveExistsError = true;
                }
            } else {
                showLeaveExistsError = true;
            }
            if ( showLeaveExistsError ) {
                errorMessage = '<?php echo __('This leave type exists'); ?>';
                showErrorMessages(element, errorMessage);
                return false;
            }
        }

        return true;        
    }

    function showErrorMessages(element, errorMessage) {

        errorDisplay = element.siblings('div');
        errorDisplay.empty();
        errorDisplay.attr('class', "error errMessageMargine");
        errorDisplay.append(errorMessage);

    }

    $("#resetButton").click(function() {
        $(".error").empty();
    });

    $('#backButton').click(function(){
        window.location.href = '<?php echo url_for('coreLeave/leaveTypeList'); ?>';
    });

    /* Populating available leave types array */

    var activeLeaveTypes = new Array();

    <?php

    for ($i=0; $i<count($form->activeTypesArray); $i++) {
        echo "activeLeaveTypes[$i] = \"".str_replace('"', '\"', $form->activeTypesArray[$i])."\";";
    }

    ?>

    /* Removing current leave type name in edit mode
     * Othersie, can't edit and save a leave type */

    var removeTypeName = $('#leaveType_txtLeaveTypeName').val();

    if ($('#leaveType_hdnSavingMode').val() == 'update') {

        activeLeaveTypes = jQuery.grep(activeLeaveTypes, function(value){
            return value != removeTypeName;
        });

    }
    
    function leaveTypeExists() {

        var leaveTypeExists = false;
        var i;
        var leaveTypeName = $.trim($("#leaveType_txtLeaveTypeName").val()).toLowerCase();

        for (i=0; i<activeLeaveTypes.length; i++) {
            if (leaveTypeName == activeLeaveTypes[i].toLowerCase()) {
                leaveTypeExists = true;
                break;
            }
        }
        
        return leaveTypeExists;
    }

    if (leaveTypeExists) {

    }    

    /* Checking for deleted leave types: Begins */

    var deletedTypeIds = new Array();
    var deletedTypeNames = new Array();

    <?php
    for ($i = 0; $i < count($form->deletedTypesArray); $i++) {
        echo "deletedTypeIds[$i] = '" . $form->deletedTypesArray[$i]['id'] . "';";
        echo "deletedTypeNames[$i] = \"" . str_replace('"', '\"', $form->deletedTypesArray[$i]['name']) . "\";";
    }
    ?>

    function isDeletedLeaveType() {

        if ($("#hdnEditMode").val() == "yes" &&
            $("#leaveType_hdnOriginalLeaveTypeName").val() ==
            $("#leaveType_txtLeaveTypeName").val()) {

            return false;
        }

        for (i=0; i<deletedTypeNames.length; i++) {
            if (deletedTypeNames[i].toLowerCase() == $.trim($('#leaveType_txtLeaveTypeName').val()).toLowerCase()) {
                return deletedTypeIds[i];
            }
        }
        return false;
    }

    // undeleteDialog
    $("#undeleteDialog").dialog({
        autoOpen: false,
        modal: true,
        width: 355,
        height:160,
        position: 'middle'
    });

    $("#undeleteYes").click(function(){
        $('#leaveType_hdnUndeleteId').val(isDeletedLeaveType());
        $('#leaveType_hdnSavingMode').val('undelete');
        $('#leaveType_txtLeaveTypeName').attr('disabled', false);
        $('#frmLeaveType').submit();
    });

    $("#undeleteNo").click(function(){
        $('#leaveType_hdnUndeleteId').val('');
        $('#leaveType_txtLeaveTypeName').attr('disabled', false);
        $('#frmLeaveType').submit();
    });

    $("#undeleteCancel").click(function(){
        $("#undeleteDialog").dialog("close");
    });

    /* Checking for deleted leave types: Ends */


});

</script>