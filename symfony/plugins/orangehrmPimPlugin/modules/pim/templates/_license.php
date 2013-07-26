<?php  
$haveLicense = count($form->empLicenseList) > 0;
?>

<a name="license"></a>
<?php if ($licensePermissions->canCreate() || ($haveLicense && $licensePermissions->canUpdate())) { ?>
    <div id="changeLicense">
        <div class="head">
            <h1 id="headChangeLicense"><?php echo __('Add License'); ?></h1>
        </div>
            
        <div class="inner">
            <form id="frmLicense" action="<?php echo url_for('pim/saveDeleteLicense?empNumber=' . 
                    $empNumber . "&option=save"); ?>" method="post">
                <fieldset>
                    <ol>
                        <?php echo $form->render(); ?>
                        
                        <li class="required">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                    </ol>
                    <p>
                        <input type="button" class="" id="btnLicenseSave" value="<?php echo __("Save"); ?>" />
                        <input type="button" class="reset" id="btnLicenseCancel" value="<?php echo __("Cancel"); ?>" />
                    </p>
                </fieldset>
            </form>
        </div>
    </div> <!-- changeLicense -->
<?php } ?>
    
<div class="miniList" id="tblLicense">
    <div class="head">
        <h1><?php echo __("License"); ?></h1>
    </div>

    <div class="inner">

        <?php if ($licensePermissions->canRead()) : ?>

        <?php include_partial('global/flash_messages', array('prefix' => 'license')); ?>
        
            <form id="frmDelLicense" action="<?php echo url_for('pim/saveDeleteLicense?empNumber=' . 
                    $empNumber . "&option=delete"); ?>" method="post">
                <?php echo $listForm ?>
                <p id="actionLicense">
                    <?php if ($licensePermissions->canCreate()) { ?>
                    <input type="button" value="<?php echo __("Add"); ?>" class="" id="addLicense" />&nbsp;
                    <?php } ?>
                    <?php if ($licensePermissions->canDelete()) { ?>
                    <input type="button" value="<?php echo __("Delete"); ?>" class="delete" id="delLicense" />
                    <?php } ?>
                </p>
                <table id="" cellpadding="0" cellspacing="0" width="100%" class="table tablesorter">
                    <thead>
                        <tr>
                            <?php if ($licensePermissions->canDelete()) { ?>
                            <th class="check" width="2%"><input type="checkbox" id="licenseCheckAll" /></th>
                            <?php } ?>
                            <th><?php echo __('License Type'); ?></th>
                            <th><?php echo __('Issued Date'); ?></th>                    
                            <th><?php echo __('Expiry Date'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$haveLicense) { ?>
                            <tr>
                                <?php if ($licensePermissions->canDelete()) { ?>
                                <td class="check"></td>
                                <?php } ?>
                                <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>                        
                            <?php
                            $licenses = $form->empLicenseList;
                            $row = 0;

                            foreach ($licenses as $license) :
                                $cssClass = ($row % 2) ? 'even' : 'odd';
                                //empty($license->from_date)
                                $startDate = set_datepicker_date_format($license->licenseIssuedDate);
                                $endDate = set_datepicker_date_format($license->licenseExpiryDate);
                                $licenseDesc = htmlspecialchars($license->License->name);
                                ?>
                                <tr class="<?php echo $cssClass; ?>">
                                    <td class="check">
                                        <input type="hidden" id="code_desc_<?php echo $license->licenseId; ?>" 
                                               value="<?php echo $licenseDesc; ?>" />
                                        <input type="hidden" id="license_no_<?php echo $license->licenseId; ?>" 
                                               value="<?php echo htmlspecialchars($license->licenseNo); ?>" />
                                        <input type="hidden" id="start_date_<?php echo $license->licenseId; ?>" 
                                               value="<?php echo $startDate; ?>" />
                                        <input type="hidden" id="end_date_<?php echo $license->licenseId; ?>" 
                                               value="<?php echo $endDate; ?>" />
                                        <input type="hidden" id="code_<?php echo $license->licenseId; ?>" 
                                               value="<?php echo htmlspecialchars($license->licenseId); ?>" />
                                        <?php if ($licensePermissions->canDelete()) { ?>
                                        <input type="checkbox" class="chkbox" value="<?php echo $license->licenseId; ?>" 
                                               name="delLicense[]"/>
                                        <?php } else { ?>
                                        <input type="hidden" class="chkbox" value="<?php echo $license->licenseId; ?>" 
                                               name="delLicense[]"/>
                                        <?php } ?>
                                    </td>   
                                    <td class="desc">
                                        <?php if ($licensePermissions->canUpdate()) { ?>
                                        <a href="#" class="edit"><?php echo $licenseDesc; ?></a>
                                        <?php } else {
                                            echo $licenseDesc;
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($startDate); ?></td>
                                    <td><?php echo htmlspecialchars($endDate); ?></td>
                                </tr>
                                <?php
                                $row++;
                            endforeach;
                        } ?>
                    </tbody>
                </table>
            </form>

        <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
        <?php endif; ?>

    </div>
</div> <!-- miniList-tblLicense -->

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
    //To hide unchanged element into hide and show the value in span while editing
    $('#license_code').after('<span id="static_license_code" style="display:none;"></span>');

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
    if($("div#tblLicense .chkbox").length == 0) {
        //$("#tblLicense").hide();
        $('div#tblLicense .check').hide();
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

        //remove if disabled while edit
        $('#license_code').removeAttr('disabled');
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

        $('#license_code').
              append($("<option class='added'></option>").
              attr("value", code).
              text($("#code_desc_" + code).val())); 

        $('#license_code').val(code).hide();

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