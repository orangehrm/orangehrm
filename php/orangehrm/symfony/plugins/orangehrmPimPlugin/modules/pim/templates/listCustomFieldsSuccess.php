<?php 

use_stylesheet('../orangehrmPimPlugin/css/listCustomFieldsSuccess');
use_stylesheet(public_path('../../themes/orange/cssmessage'));
use_stylesheet(public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'));

use_javascript(public_path('../../scripts/jquery/ui/ui.core.js'));
use_javascript(public_path('../../scripts/jquery/ui/ui.dialog.js'));

$cssClass = '';


if (isset($messageType)) {
    $cssClass = "messageBalloon_{$messageType}";
}

                
?>
<div id="customFieldsOuter">
<div id="messagebar" class="<?php echo $cssClass;?>">
    <?php echo isset($message) ? $message : ''; ?>
</div>   

<div id="customFieldAddPane" style="display: none;">
  <div class="outerbox">
    <div class="mainHeading"><h2 id="heading"><?php echo __('Add Custom Field'); ?></h2></div>
    <form name="frmCustomField" id="frmCustomField" method="post" action="<?php echo url_for('pim/defineCustomField'); ?>">

    <?php echo $form['_csrf_token']; ?>
    <?php echo $form["field_num"]->render(); ?>

    <br class="clear"/>
    <?php echo $form['name']->renderLabel(__('Field Name') . ' <span class="required">*</span>'); ?>
    <?php echo $form['name']->render(array("class" => "formInputText", "maxlength" => 250)); ?>
    <br class="clear"/>

    <?php echo $form['screen']->renderLabel(__('Screen') . ' <span class="required">*</span>'); ?>
    <?php echo $form['screen']->render(array("class" => "formSelect")); ?>
    <br class="clear"/>

    <?php echo $form['type']->renderLabel(__('Type') . ' <span class="required">*</span>'); ?>
    <?php echo $form['type']->render(array("class" => "formSelect")); ?>
    <br class="clear"/>
    
    <?php $showExtra = ($form->getValue('type') == CustomFields::FIELD_TYPE_SELECT) ? 'block' : 'none';?>
    
    <div style="display:<?php echo $showExtra;?>;" id="selectOptions">
        
        <?php echo $form['extra_data']->renderLabel(__('Select Options') . ' <span class="required">*</span>'); ?>
        <?php echo $form['extra_data']->render(array("class" => "formInputText")); ?>
        <div class="fieldHint"><?php echo __("Enter allowed options separated by commas");?></div>
        <br class="clear"/>
    </div>
                        
    <div class="formbuttons">
        <input type="button" class="savebutton" name="btnSave" id="btnSave"
               value="<?php echo __("Save"); ?>"
               title="<?php echo __("Save"); ?>"
               onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
        <input type="button" id="btnCancel" class="cancelbutton" value="<?php echo __("Cancel"); ?>"/>
    </div>

    </form>
  </div>
  <div class="requiredNote"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>    

<br class="clear"/>

</div> <!-- End of customFieldAddPane -->


<div class="outerbox" id="customFieldListPane">
    <form name="standardView" id="standardView" method="post" action="<?php echo url_for('pim/deleteCustomFields') ?>">
        <?php echo $deleteForm['_csrf_token']; ?>
        <input type="hidden" name="mode" id="mode" value=""></input>    
    <div class="maincontent">       
        
        <div class="mainHeading"><h2><?php echo __("Defined Custom Fields") ?></h2></div>

        <div class="actionbar" id="listActions">
            <div class="actionbuttons">
<?php 
    $fieldsInUse = count($listCustomField);
    $fieldsLeft = CustomFields::MAX_FIELD_NUM - $fieldsInUse;
    $fieldsLeftMsg = '';
    
    if ($fieldsLeft == 0) {
        $fieldsLeftMsg = __("All customs fields are in use");
    } else if ($fieldsLeft == 1) {
        $fieldsLeftMsg = __("1 Custom field left");
    } else if ($fieldsLeft > 1) {
        $fieldsLeftMsg = $fieldsLeft . ' ' . __("Custom fields left");
    }
?>
<?php if ($fieldsLeft > 0 ) { ?>                
                <input type="button" class="plainbtn" id="buttonAdd"
                       value="<?php echo __("Add") ?>" />
<?php } ?>

                <input type="button" class="plainbtn" id="buttonRemove"
                       value="<?php echo __("Delete") ?>" />    
                
                <span id="fieldsleft"><?php echo $fieldsLeftMsg;?></span>

            </div>
            <div class="noresultsbar"></div>
            <div class="pagingbar"> </div>
            <br class="clear" />
        </div>

            <table cellpadding="0" cellspacing="0" class="data-table" id="customFieldList">
                <thead>
                    <tr>
                        <td class="fieldCheck">
                            <?php if ($fieldsInUse > 0) { ?>
                            <input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
                            <?php } ?>
                        </td>

                        <td scope="col" class="fieldName">
                            <?php echo $sorter->sortLink('name', __('Custom Field Name'), '@customfield_list', ESC_RAW); ?>
                        </td>  	  
                        <td scope="col">
                            <?php echo $sorter->sortLink('screen', __('Screen'), '@customfield_list', ESC_RAW); ?>
                        </td>
                        <td scope="col">
                            <?php echo $sorter->sortLink('type', __('Field Type'), '@customfield_list', ESC_RAW); ?>
                        </td>

                    </tr>
                </thead>

                <tbody>
                    <?php
                    $row = 0;
                    $screens = $form->getScreens();
                    $fieldTypes = $form->getFieldTypes();
                    
                    foreach ($listCustomField as $customField) {
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        $row = $row + 1;
                        $fieldNum = $customField->getFieldNum();
                        ?>
                        <tr class="<?php echo $cssClass ?>">
                            <td class="fieldCheck">
                                <input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' value='<?php echo $fieldNum ?>' />
                            </td>
                            <td class="fieldName">
                                <a href="#"><?php echo $customField->getName() ?></a>                                
                            </td>
                            <td>
                                <?php 
                                $screenId = $customField->getScreen();
                                echo isset($screens[$screenId]) ? $screens[$screenId] : $screenId;
                                ?>
                                <input type="hidden" id="screen_<?php echo $fieldNum;?>" value="<?php echo $screenId;?>"/>
                            </td>
                            <td>
                                <?php 
                                $type = $customField->getType();
                                $typeDesc = isset($fieldTypes[$type]) ? $fieldTypes[$type] : $type;
                                echo $typeDesc;
                                ?>
                                <input type="hidden" id="type_<?php echo $fieldNum;?>" value="<?php echo $type;?>"/>
                                <input type="hidden" id="extra_data_<?php echo $fieldNum;?>" value="<?php echo $customField->getExtraData();?>"/>
                            </td>


                        </tr>
                    <?php } ?>
                </tbody>
            </table>
    </div> <!-- End of maincontent -->
        </form>        
</div> <!-- End of outerbox -->

<div id="deleteConfirmation" title="OrangeHRM - Confirmation Required" style="display: none;">
    <span id="deleteConfirmMsg">Are you sure you want to delete selected custom field(s)?</span>
    <div class="dialogButtons">
        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __("Delete");?>" />
        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __("Cancel");?>" />
    </div>
</div>

</div> <!-- End of customFieldsOuter -->
    
<script type="text/javascript">

    $(document).ready(function() {

        /* Handling loading view */

        var fieldsInUse = <?php echo count($listCustomField); ?>;

        if (fieldsInUse == 0) {
            $('#customFieldAddPane').css('display', 'block');
            $('#customFieldListPane').css('display', 'none');
        } else {
            $('#customFieldAddPane').css('display', 'none');
            //$('#customFieldListPane').css('display', 'block');
        }


        
        hideextra();

        function hideextra() {
            if ($('#customField_type').val() == <?php echo CustomFields::FIELD_TYPE_SELECT;?>) {
                $('#selectOptions').show();
            } else {
                $('#selectOptions').hide();
            }
        }   

        // When Click Main Tick box
        $("#allCheck").click(function() {
            if ($('#allCheck').attr('checked')){
			
                $('.innercheckbox').attr('checked','checked');
            }else{
                $('.innercheckbox').removeAttr('checked');
            }
        });

        $(".innercheckbox").click(function() {
            if($(this).attr('checked'))
            {
			
            }else
            {
                $('#allCheck').removeAttr('checked');
            }
        });

        //When click remove button
        $("#buttonRemove").click(function(event) {

            event.preventDefault();

            var checked = $('#customFieldList tbody input.checkbox:checked').length;

            if ( checked == 0) {
                $('#messagebar').text('<?php echo __("Please Select At Least One Custom Field To Delete") ?>').attr('class', 'messageBalloon_notice');
            } else {
                $('#messagebar').text('').attr('class', ''); 
                
                var fields = '';
                $('#customFieldList tbody input.checkbox:checked').each(function(index) {
                    var name = $(this).parent().next().find('a').text().trim();
                    if (index == 0) {
                        fields = name;                      
                    } else {
                        fields = fields + ', ' + name;
                    }
                });
                
                var confirmMsg =  fields + " <?php echo __("will be deleted from all employees' records. Do you want to continue?");?>";
                if (checked == 1) {
                    confirmMsg = '<?php echo __('Field ');?>' + confirmMsg;
                }
                else {
                    confirmMsg = '<?php echo __('Fields ');?>' + confirmMsg;
                }
                
                $('span#deleteConfirmMsg').text(confirmMsg);
                
                $('#deleteConfirmation').dialog('open');
                return false;
            }
        });

        $("#deleteConfirmation").dialog({
            autoOpen: false,
            modal: true,
            width: 325,
            height: 50,
            position: 'middle',
            open: function() {
              $('#dialogCancelBtn').focus();
            }
        });

        $('#dialogDeleteBtn').click(function() {
            $("#mode").attr('value', 'delete');
            $("#standardView").submit();
        });
        
        $('#dialogCancelBtn').click(function() {
            $("#deleteConfirmation").dialog("close");
        });
	  	
    /* Valid From Date */
    $.validator.addMethod("validateExtra", function(value, element) {

        if ($('#customField_type').val() == <?php echo CustomFields::FIELD_TYPE_SELECT;?>) {
            var extraVal = $.trim($('#customField_extra_data').val());
            var len = extraVal.length;
            if (len == 0) {
                return false;
            }            
        }
        return true;
    });
    
    //form validation
    var formValidator =
        $("#frmCustomField").validate({
        rules: {
            'customField[name]': {required: true},
            'customField[type]': {required: true},
            'customField[screen]': {required: true},
            'customField[extra_data]': {validateExtra: true}
        },
        messages: {
            'customField[name]': {required: '<?php echo __('Please specify field name');?>'},
            'customField[type]': {required: '<?php echo __('Please select a field type');?>'},
            'customField[screen]': {required: '<?php echo __('Please select a screen');?>'},
            'customField[extra_data]' : {validateExtra: '<?php echo __('Please specify select options');?>'}
        },

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));

        }
    });
    
    $('#customField_type').change(function() {
        hideextra();        
    });    
    
    function clearAddForm() {
        $('#customField_field_num').val('');
        $('#customField_name').val('');
        $('#customField_type').val('');
        $('#customField_screen').val('');
        $('#customField_extra_data').val('');
        $('div#customFieldAddPane label.error').hide();
        $('div#messagebar').text('').attr('class', '');            
    }

    function addEditLinks() {
        removeEditLinks();
        $('#customFieldList tbody td.fieldName').wrapInner('<a href="#"/>');
    }

    function removeEditLinks() {
        $('#customFieldList tbody td.fieldName a').each(function(index) {
            $(this).parent().text($(this).text());
        });
    }
    
    $('#btnCancel').click(function() {
        clearAddForm();
        $('#customFieldAddPane').css('display', 'none');
        $('#listActions').show();
        $('#customFieldList td.fieldCheck').show();
        addEditLinks();
        $('div#messagebar').text('').attr('class', '');            
        $(".paddingLeftRequired").hide();

        $('div.error').each(function(){
            $(this).hide();
        });

    });

    // Add a emergency contact
    $('#buttonAdd').click(function() {
        $("#heading").text("<?php echo __("Add Custom Field");?>");
        clearAddForm();

        // Hide list action buttons and checkbox
        $('#listActions').hide();
        $('#customFieldList td.fieldCheck').hide();
        removeEditLinks();
        $('div#messagebar').text('').attr('class', '');            
        
        hideextra();

        //
        //            // hide validation error messages
        //            $("label.errortd[generated='true']").css('display', 'none');
        $('#customFieldAddPane').css('display', 'block');

    });        
    
    $('#customFieldList tbody a').live('click', function() {
        $("#heading").text("<?php echo __("Edit Custom Field");?>");
        
        var row = $(this).closest("tr");
        var fieldNo = row.find('input.checkbox:first').val();
        var name = $(this).text();
        var type = $("#type_" + fieldNo).val();
        var screen = $("#screen_" + fieldNo).val();
        var extraData = $("#extra_data_" + fieldNo).val();

        $('#customField_field_num').val(fieldNo);
        $('#customField_name').val(name);
        $('#customField_type').val(type);
        $('#customField_screen').val(screen);
        $('#customField_extra_data').val(extraData);


        $('div#messagebar').text('').attr('class', '');            
        hideextra();
        // hide validation error messages

        $('#listActions').hide();
        $('#customFieldList td.fieldCheck').hide();
        $('#customFieldAddPane').css('display', 'block');

    });

        $('#btnSave').click(function() {
            $('#frmCustomField').submit();
        });
                                
    });

</script>

