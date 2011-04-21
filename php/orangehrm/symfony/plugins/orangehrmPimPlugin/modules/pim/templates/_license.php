<div id="licenseMessagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 630px;">
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div class="sectionDiv" id="sectionLicense">
    <div><h3><?php echo __('License'); ?></h3></div>

    <div class="outerbox" id="changeLicense" style="width:500px;">
        <div class="mainHeading"><h2 id="headChangeLicense"><?php echo __('Add License'); ?></h2></div>
        <form id="frmLicense" action="<?php echo url_for('pim/saveDeleteLicense?empNumber=' . $empNumber . "&option=save"); ?>" method="post">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['emp_number']->render(); ?>

            <?php echo $form['code']->renderLabel(__('License Type') . ' <span class="required">*</span>'); ?>
            <?php echo $form['code']->render(array("class" => "formInputText")); ?>
            <span id="static_license_code" style="display:none;"></span>
            <br class="clear"/>

            <?php echo $form['license_no']->renderLabel(__('License Number')); ?>
            <?php echo $form['license_no']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
            <br class="clear"/>

            <?php echo $form['date']->renderLabel(__('Date')); ?>
            <?php echo $form['date']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
            <input id="licenseStartDateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
            <br class="clear"/>

            <?php echo $form['renewal_date']->renderLabel(__('End Date')); ?>
            <?php echo $form['renewal_date']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
            <input id="licenseEndDateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
            <br class="clear"/>


            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnLicenseSave" value="<?php echo __("Save"); ?>" />
                <input type="button" class="savebutton" id="btnLicenseCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
        </form>
    </div>
    <div class="smallText" id="licenseRequiredNote"><?php echo __('Fields marked with an asterisk')?>
        <span class="required">*</span> <?php echo __('are required.')?></div>
    <br />
    <div id="actionLicense">
        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addLicense" />&nbsp;
        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delLicense" />
        <br /><br />
    </div>

    <form id="frmDelLicense" action="<?php echo url_for('pim/saveDeleteLicense?empNumber=' . $empNumber . "&option=delete"); ?>" method="post">
        <div class="outerbox" id="tblLicense">
            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" border="0">
                <thead>
                <tr>
                    <td><input type="checkbox" id="licenseCheckAll" /></td>
                    <td><?php echo __('License Type');?></td>
                    <td><?php echo __('Start Date');?></td>                    
                    <td><?php echo __('End Date');?></td>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $licenses = $form->empLicenseList;//var_dump($licenses->toArray());die;
                    $row = 0;

                    foreach ($licenses as $license) {                        
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        //empty($license->from_date)
                        $startDate = ohrm_format_date($license->date);
                        $endDate = ohrm_format_date($license->renewal_date);
                        $licenseDesc = htmlspecialchars($license->license->licenses_desc);
                        ?>
                    <tr class="<?php echo $cssClass;?>">
                <td><input type="hidden" id="code_<?php echo $license->code;?>" value="<?php echo htmlspecialchars($license->code); ?>" />
                <input type="hidden" id="code_desc_<?php echo $license->code;?>" value="<?php echo $licenseDesc; ?>" />
                <input type="hidden" id="license_no_<?php echo $license->code;?>" value="<?php echo htmlspecialchars($license->license_no); ?>" />
                <input type="hidden" id="start_date_<?php echo $license->code;?>" value="<?php echo $startDate; ?>" />
                <input type="hidden" id="end_date_<?php echo $license->code;?>" value="<?php echo $endDate; ?>" />

                <input type="checkbox" class="chkbox" value="<?php echo $license->code;?>" name="delLicense[]"/></td>
                <td><a href="#" class="edit"><?php echo $licenseDesc;?></a></td>
                <td><?php echo htmlspecialchars($startDate);?></td>
                <td><?php echo htmlspecialchars($endDate);?></td>
                </tr>
                    <?php $row++;
                }?>
                </tbody>
            </table>
        </div>
    </form>

</div>
<script type="text/javascript">
    //<![CDATA[

    var fileModified = 0;
    var lang_addLicense = "<?php echo __('Add License');?>";
    var lang_editLicense = "<?php echo __('Edit License');?>";
    var lang_licenseRequired = "<?php echo __("License Type is required");?>";
    var lang_invalidDate = "<?php echo __("Please enter a valid date in %format% format", array('%format%'=>$sf_user->getDateFormat())) ?>";
    var lang_startDateAfterEndDate = "<?php echo __('Start date should be before end date');?>";
    var lang_selectLicenseToDelete = "<?php echo __('Please Select At Least One License Item To Delete');?>";
    var lang_licenseNoMaxLength = "<?php echo __('License number cannot exceed 50 characters in length');?>";

    var dateFormat  = '<?php echo $sf_user->getDateFormat();?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat());?>';
    var dateDisplayFormat = dateFormat.toUpperCase();
    //]]>
</script>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {

    //hide add section
    $("#changeLicense").hide();
    $("#licenseRequiredNote").hide();

    //hiding the data table if records are not available
    if($("div#tblLicense table.data-table .chkbox").length == 0) {
        $("#tblLicense").hide();
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
        $("#license_date").val("");
        $("#license_renewal_date").val("");

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

        $("#frmLicense").submit();
    });

    /* Valid From Date */
    $.validator.addMethod("validFromDate3", function(value, element) {

        var fromdate	=	$('#license_date').val();
        fromdate = (fromdate).split("-");

        var fromdateObj = new Date(parseInt(fromdate[0],10), parseInt(fromdate[1],10) - 1, parseInt(fromdate[2],10));
        var todate		=	$('#license_renewal_date').val();
        todate = (todate).split("-");
        var todateObj	=	new Date(parseInt(todate[0],10), parseInt(todate[1],10) - 1, parseInt(todate[2],10));

        if(fromdateObj > todateObj){
            return false;
        }
        else{
            return true;
        }
    });

    //form validation
    var licenseValidator =
        $("#frmLicense").validate({
        rules: {
            'license[code]': {required: true},
            'license[license_no]': {required: false, maxlength: 50},
            'license[date]': {valid_date: function(){return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}}, validFromDate3:true},
            'license[renewal_date]': {valid_date: function(){return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}}}
        },
        messages: {
            'license[code]': {required: lang_licenseRequired},
            'license[license_no]': {maxlength: lang_licenseNoMaxLength},
            'license[date]': {valid_date: lang_invalidDate, validFromDate3: lang_startDateAfterEndDate},
            'license[renewal_date]': {valid_date: lang_invalidDate}
        },

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));

        }
    });

    $("#btnLicenseCancel").click(function() {
        clearMessageBar();

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


    daymarker.bindElement("#license_date", {
        onSelect: function(date){
            $("#license_date").valid();
            },
            dateFormat:jsDateFormat
        });

    $('#licenseStartDateBtn').click(function() {
        daymarker.show("#license_date");
    });

    daymarker.bindElement("#license_renewal_date", {
        onSelect: function(date){
            $("#license_renewal_date").valid();
            },
            dateFormat:jsDateFormat
        });

    $('#licenseEndDateBtn').click(function() {
        daymarker.show("#license_renewal_date");
    });
    
    $('form#frmDelLicense a.edit').click(function() {
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
        
        $('#static_license_code').html($("#code_desc_" + code).val()).show();

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

        $("#licenseRequiredNote").show();

        $("div#tblLicense .chkbox").hide();
        $("#licenseCheckAll").hide();        
    });
});

//]]>
</script>