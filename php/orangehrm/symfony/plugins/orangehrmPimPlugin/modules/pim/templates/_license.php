<?php  
if (($section == 'license') && isset($message) && isset($messageType)) {
    $tmpMsgClass = "messageBalloon_{$messageType}";
    $tmpMsg = $message;
} else {
    $tmpMsgClass = '';
    $tmpMsg = '';
}
$haveLicense = count($form->empLicenseList) > 0;
?>
<div id="licenseMessagebar" class="<?php echo $tmpMsgClass; ?>">
    <span style="font-weight: bold;"><?php echo $tmpMsg; ?></span>
</div>

<div class="sectionDiv" id="sectionLicense">
    <div style="float: left; width: 450px;"><h3><?php echo __('License'); ?></h3></div>
    <div id="actionLicense" style="float: left; margin-top: 20px; width: 335px; text-align: right">
        <?php if ($licensePermissions->canCreate() ) { ?>
        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addLicense" />&nbsp;
        <?php } ?>
        <?php if ($licensePermissions->canDelete() ) { ?>
        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delLicense" />
        <?php } ?>
    </div>

    <?php if ($licensePermissions->canRead() && (($licensePermissions->canCreate()) || ($licensePermissions->canUpdate() && $haveLicense))) { ?>
    <div class="outerbox" id="changeLicense" style="width:500px; float: left">
        <div class="mainHeading"><h4 id="headChangeLicense"><?php echo __('Add License'); ?></h4></div>
        <form id="frmLicense" action="<?php echo url_for('pim/saveDeleteLicense?empNumber=' . $empNumber . "&option=save"); ?>" method="post">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['emp_number']->render(); ?>

            <?php echo $form['code']->renderLabel(__('License Type') . ' <span class="required">*</span>'); ?>
            <?php echo $form['code']->render(array("class" => "formSelect")); ?>
            <span id="static_license_code" style="display:none;"></span>
            <br class="clear"/>

            <?php echo $form['license_no']->renderLabel(__('License Number')); ?>
            <?php echo $form['license_no']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
            <br class="clear"/>

            <?php echo $form['date']->renderLabel(__('Issued Date')); ?>
            <?php echo $form['date']->render(array("class" => "formInputText")); ?>
            <br class="clear"/>

            <?php echo $form['renewal_date']->renderLabel(__('Expiry Date')); ?>
            <?php echo $form['renewal_date']->render(array("class" => "formInputText")); ?>
            <br class="clear"/>


            <?php if (($haveLicense && $licensePermissions->canUpdate()) || $licensePermissions->canCreate()) { ?>
            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnLicenseSave" value="<?php echo __("Save"); ?>" />
                <input type="button" class="savebutton" id="btnLicenseCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
            <?php } ?>
        </form>
    </div>
    <?php } ?>
     <br class="clear" />
    <div class="paddingLeftRequired" id="licenseRequiredNote"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>

    <?php if ($licensePermissions->canRead()) { ?>
    <form id="frmDelLicense" action="<?php echo url_for('pim/saveDeleteLicense?empNumber=' . $empNumber . "&option=delete"); ?>" method="post">
        <div class="outerbox" id="tblLicense">
            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" border="0">
                <thead>
                <tr>
                    <?php if ($licensePermissions->canDelete()) { ?>
                        <td class="check"><input type="checkbox" id="licenseCheckAll" /></td>
                    <?php } else { ?>
                        <td></td>
                    <?php } ?>
                    <td><?php echo __('License Type');?></td>
                    <td><?php echo __('Issued Date');?></td>                    
                    <td><?php echo __('Expiry Date');?></td>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $licenses = $form->empLicenseList;//var_dump($licenses->toArray());die;
                    $row = 0;

                    foreach ($licenses as $license) {                        
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        //empty($license->from_date)
                        $startDate = set_datepicker_date_format($license->licenseIssuedDate);
                        $endDate = set_datepicker_date_format($license->licenseExpiryDate);
                        $licenseDesc = htmlspecialchars($license->License->name);
                        ?>
                    <tr class="<?php echo $cssClass;?>">
                <td class="check"><input type="hidden" id="code_<?php echo $license->licenseId;?>" value="<?php echo htmlspecialchars($license->licenseId); ?>" />
                <input type="hidden" id="code_desc_<?php echo $license->licenseId;?>" value="<?php echo $licenseDesc; ?>" />
                <input type="hidden" id="license_no_<?php echo $license->licenseId;?>" value="<?php echo htmlspecialchars($license->licenseNo); ?>" />
                <input type="hidden" id="start_date_<?php echo $license->licenseId;?>" value="<?php echo $startDate; ?>" />
                <input type="hidden" id="end_date_<?php echo $license->licenseId;?>" value="<?php echo $endDate; ?>" />

                <?php if ($licensePermissions->canDelete()) {?>
                    <input type="checkbox" class="chkbox" value="<?php echo $license->licenseId;?>" name="delLicense[]"/></td>
                <?php } else {?>
                    <input type="hidden" class="chkbox" value="<?php echo $license->licenseId;?>" name="delLicense[]"/>
                <?php }?>
                <td class="desc">
                <?php if ($licensePermissions->canUpdate()) { ?>
                    <a href="#" class="edit"><?php echo htmlspecialchars($licenseDesc);?></a>
                <?php } else {
                        echo htmlspecialchars($licenseDesc);
                      } ?>
                </td>
                <td><?php echo htmlspecialchars($startDate);?></td>
                <td><?php echo htmlspecialchars($endDate);?></td>
                <?php
                        $row++;
                    }

                    if ($row == 0) {
                    ?>
                        <tr>
                            <td colspan="6">&nbsp; <?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                        </tr>
<?php } ?>
                </tbody>
            </table>
        </div>
    </form>
    <?php } ?>

</div>
<script type="text/javascript">
    //<![CDATA[

    var fileModified = 0;
    var lang_addLicense = "<?php echo __('Add License');?>";
    var lang_editLicense = "<?php echo __('Edit License');?>";
    var lang_licenseRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_startDateAfterEndDate = "<?php echo __('Expiry date should be after issued date');?>";
    var lang_selectLicenseToDelete = "<?php echo __(TopLevelMessages::SELECT_RECORDS);?>";
    var lang_licenseNoMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50));?>";

    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    //]]>
</script>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {

var issuedDate = "";
    function addEditLinks() {
        // called here to avoid double adding links - When in edit mode and cancel is pressed.
        removeEditLinks();
        $('form#frmDelLicense table tbody td.desc').wrapInner('<a class="edit" href="#"/>');
    }

    function removeEditLinks() {
        $('form#frmDelLicense table tbody td.desc a').each(function(index) {
            $(this).parent().text($(this).text());
        });
    }
    
    //hide add section
    $("#changeLicense").hide();
    $("#licenseRequiredNote").hide();

    //hiding the data table if records are not available
    if($("div#tblLicense table.data-table .chkbox").length == 0) {
        //$("#tblLicense").hide();
        $("#editLicense").hide();
        $("#delLicense").hide();
    }

    //if check all button clicked
    $("#licenseCheckAll").click(function() {
        $("div#tblLicense .chkbox").removeAttr("checked");
        if($("#licenseCheckAll").attr("checked")) {
            $("div#tblLicense .chkbox").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $("div#tblLicense .chkbox").click(function() {
        $("#licenseCheckAll").removeAttr('checked');
        if($("div#tblLicense .chkbox").length == $("div#tblLicense .chkbox:checked").length) {
            $("#licenseCheckAll").attr('checked', 'checked');
        }
    });

    $("#addLicense").click(function() {

        removeEditLinks();
        clearMessageBar();
        $('div#changeLicense label.error').hide();        
        

        //changing the headings
        $("#headChangeLicense").text(lang_addLicense);
        $("div#tblLicense .chkbox").hide();
        $("#licenseCheckAll").hide();

        //hiding action button section
        $("#actionLicense").hide();

        $('#static_license_code').hide().val("");        
        $("#license_code").show().val("");
        $("#license_code option[class='added']").remove();
        $("#license_major").val("");
        $("#license_year").val("");
        $("#license_gpa").val("");
        $("#license_date").val(displayDateFormat);
        $("#license_renewal_date").val(displayDateFormat);

        //show add form
        $("#changeLicense").show();
        $("#licenseRequiredNote").show();
    });

    //clicking of delete button
    $("#delLicense").click(function(){

        clearMessageBar();

        if ($("div#tblLicense .chkbox:checked").length > 0) {
            $("#frmDelLicense").submit();
        } else {
            $("#licenseMessagebar").attr('class', 'messageBalloon_notice').text(lang_selectLicenseToDelete);
        }

    });

    $("#btnLicenseSave").click(function() {
        clearMessageBar();
        issuedDate = $('#license_date').val();
        $("#frmLicense").submit();
    });

    //form validation
    var licenseValidator =
        $("#frmLicense").validate({
        rules: {
            'license[code]': {required: true},
            'license[license_no]': {required: false, maxlength: 50},
            'license[date]': {valid_date: function(){return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat}}},
            'license[renewal_date]': {valid_date: function(){return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat}}, date_range: function() {return {format:datepickerDateFormat, displayFormat:displayDateFormat, fromDate:issuedDate}}}
        },
        messages: {
            'license[code]': {required: lang_licenseRequired},
            'license[license_no]': {maxlength: lang_licenseNoMaxLength},
            'license[date]': {valid_date: lang_invalidDate},
            'license[renewal_date]': {valid_date: lang_invalidDate, date_range:lang_startDateAfterEndDate}
        },

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.appendTo(element.prev('label'));
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));

        }
    });

    $("#btnLicenseCancel").click(function() {
        clearMessageBar();
        <?php if ($licensePermissions->canUpdate()){?>
            addEditLinks();
        <?php }?>

        licenseValidator.resetForm();
        
        $('div#changeLicense label.error').hide();

        $("div#tblLicense .chkbox").removeAttr("checked").show();
        
        //hiding action button section
        $("#actionLicense").show();
        $("#changeLicense").hide();
        $("#licenseRequiredNote").hide();        
        $("#licenseCheckAll").show();
        
        // remove any options already in use
        $("#license_code option[class='added']").remove();
        $('#static_license_code').hide().val("");

    });
    
    $('form#frmDelLicense a.edit').live('click', function(event) {
        event.preventDefault();
        clearMessageBar();

        //changing the headings
        $("#headChangeLicense").text(lang_editLicense);

        licenseValidator.resetForm();

        $('div#changeLicense label.error').hide();

        //hiding action button section
        $("#actionLicense").hide();

        //show add form
        $("#changeLicense").show();
        var code = $(this).closest("tr").find('input.chkbox:first').val();
        
        $('#static_license_code').text($("#code_desc_" + code).val()).show();

        // remove any options already in use
        $("#license_code option[class='added']").remove();

        $('#license_code').hide().
              append($("<option class='added'></option>").
              attr("value", code).
              text($("#code_desc_" + code).val())); 

        $('#license_code').val(code);

        $("#license_license_no").val($("#license_no_" + code).val());
        $("#license_date").val($("#start_date_" + code).val());
        $("#license_renewal_date").val($("#end_date_" + code).val());
        
        if ($("#license_date").val() == '') {
            $("#license_date").val(displayDateFormat);
        }
        if ($("#license_renewal_date").val() == '') {
            $("#license_renewal_date").val(displayDateFormat);
        }        

        $("#licenseRequiredNote").show();

        $("div#tblLicense .chkbox").hide();
        $("#licenseCheckAll").hide();        
    });
});

//]]>
</script>