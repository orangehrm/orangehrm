<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
?>

<?php

function getSortClass($column, $sortField, $sortOrder) {
    
    if (isset($sortField) && isset($sortOrder) && $column == $sortField) {
        $class = ' headerSort';
        $class .= ($sortOrder == 'ASC')?'Up':'Down';
        return $class;
    }
    
    return '';
    
}

?>

<div id="customFieldAddPane" style="display: none;" class="box">

    <div class="head">
        <h1 id="heading"><?php echo __('Add Custom Field'); ?></h1>
    </div>

    <div class="inner">

        <form name="frmCustomField" id="frmCustomField" method="post" action="<?php echo url_for('pim/defineCustomField'); ?>">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form["field_num"]->render(); ?>

            <fieldset>

                <ol>

                    <li>
                        <?php echo $form['name']->renderLabel(__('Field Name') . ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(array("maxlength" => 250)); ?>
                    </li>

                    <li>
                        <?php echo $form['screen']->renderLabel(__('Screen') . ' <em>*</em>'); ?>
                        <?php echo $form['screen']->render(); ?>
                    </li>

                    <li>
                        <?php echo $form['type']->renderLabel(__('Type') . ' <em>*</em>'); ?>
                        <?php echo $form['type']->render(); ?>
                    </li>

                    <?php $showExtra = ($form->getValue('type') == CustomField::FIELD_TYPE_SELECT) ? 'block' : 'none'; ?>
                    <li style="display:<?php echo $showExtra; ?>;" id="selectOptions" class="fieldHelpContainer">
                        <?php echo $form['extra_data']->renderLabel(__('Select Options') . ' <em>*</em>'); ?>
                        <?php echo $form['extra_data']->render(); ?>
                        <label class="fieldHelpBottom"><?php echo __("Enter allowed options separated by commas"); ?></label>
                    </li>

                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>

                </ol>

                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="reset" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>

            </fieldset>

        </form>

    </div>

</div> <!-- End of customFieldAddPane -->


<div class="box miniList" id="customFieldListPane">

    <div class="head">
        <h1><?php echo __("Defined Custom Fields") ?></h1>
    </div>

    <div class="inner">

        <?php include_partial('global/flash_messages'); ?>

        <form name="standardView" id="standardView" method="post" action="<?php echo url_for('pim/deleteCustomFields') ?>">

            <?php
            echo $deleteForm['_csrf_token'];

            $fieldsInUse = count($listCustomField);
            $fieldsLeft = CustomField::MAX_FIELD_NUM - $fieldsInUse;
            $fieldsLeftMsg = '';
            if ($fieldsLeft == 0) {
                $fieldsLeftMsg = __("All customs fields are in use");
            } else {
                $fieldsLeftMsg = __("Remaining number of custom fields") . ": $fieldsLeft";
            }
            ?>
            <fieldset>
                <p id="listActions">
                    <input type="hidden" name="mode" id="mode" value=""></input>  

                    <?php if ($fieldsLeft > 0) { ?>                
                        <input type="button" class="" id="buttonAdd" value="<?php echo __("Add") ?>" />
                    <?php } ?>
                    <input type="button" class="delete" id="buttonRemove" value="<?php echo __('Delete'); ?>"/>
                    <span id="fieldsleft"><?php echo $fieldsLeftMsg; ?></span>
                </p>
            </fieldset>

            <table class="table hover" id="customFieldList">
                <thead>
                    <tr>
                        <th class="check" style="width:2%">
                            <?php if ($fieldsInUse > 0) { ?>
                                <input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />
                            <?php } ?>
                        </th>

                        <th class="fieldName header<?php echo getSortClass('name', $sortField, $sortOrder); ?>" style="width:35%">
                            <?php echo $sorter->sortLink('name', __('Custom Field Name'), '@customfield_list', ESC_RAW); ?>
                        </th>  	  
                        <th style="width:35%" class="header<?php echo getSortClass('screen', $sortField, $sortOrder); ?>">
                            <?php echo $sorter->sortLink('screen', __('Screen'), '@customfield_list', ESC_RAW); ?>
                        </th>
                        <th style="width:28%" class="header<?php echo getSortClass('type', $sortField, $sortOrder); ?>">
                            <?php echo $sorter->sortLink('type', __('Field Type'), '@customfield_list', ESC_RAW); ?>
                        </th>

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
                            <td class="check">
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
                                <input type="hidden" id="screen_<?php echo $fieldNum; ?>" value="<?php echo $screenId; ?>"/>
                            </td>
                            <td>
                                <?php
                                $type = $customField->getType();
                                $typeDesc = isset($fieldTypes[$type]) ? $fieldTypes[$type] : $type;
                                echo $typeDesc;
                                ?>
                                <input type="hidden" id="type_<?php echo $fieldNum; ?>" value="<?php echo $type; ?>"/>
                                <input type="hidden" id="extra_data_<?php echo $fieldNum; ?>" value="<?php echo $customField->getExtraData(); ?>"/>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </form>

    </div>

</div> <!-- End of list -->

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('Will be deleted from all employees'); ?></p>
        <br>
        <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">

    $(document).ready(function() {

        /* Handling loading view */

        var fieldsInUse = <?php echo count($listCustomField); ?>;

        if (fieldsInUse == 0) {
            $('#customFieldAddPane').css('display', 'block');
            $('#customFieldListPane').css('display', 'none');
            $('#btnCancel').css('display', 'none');
        } else {
            $('#customFieldAddPane').css('display', 'none');
            //$('#customFieldListPane').css('display', 'block');
        }


        
        hideextra();

        function hideextra() {
            if ($('#customField_type').val() == <?php echo CustomField::FIELD_TYPE_SELECT; ?>) {
                $('#selectOptions').show();
            } else {
                $('#selectOptions').hide();
            }
        }   
        
        $("#buttonRemove").attr('disabled', 'disabled');

        // When Click Main Tick box
        $("#allCheck").click(function() {
            if ($('#allCheck').attr('checked')){			
                $('.innercheckbox').attr('checked','checked');
                $("#buttonRemove").removeAttr('disabled');
            } else{
                $('.innercheckbox').removeAttr('checked');
                $("#buttonRemove").attr('disabled', 'disabled');
            }
        });

        $(".innercheckbox").click(function() {
            if($(this).attr('checked'))
            {
                $("#buttonRemove").removeAttr('disabled');	
            }else
            {
                $('#allCheck').removeAttr('checked');
                $("#buttonRemove").attr('disabled', 'disabled');
            }
            
            if($(".innercheckbox").is(':checked')) {
                $('#buttonRemove').removeAttr('disabled');
            } else {
                $('#buttonRemove').attr('disabled','disabled');
            }
        });

        //When click remove button
        $("#buttonRemove").click(function(event) {

            event.preventDefault();

            var checked = $('#customFieldList tbody input.checkbox:checked').length;

            if ( checked == 0) {
                $('#messagebar').text('<?php echo __(TopLevelMessages::SELECT_RECORDS) ?>').attr('class', 'messageBalloon_notice');
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
                
                $('#deleteConfModal').modal();
                return false;
            }
        });

        $('#dialogDeleteBtn').click(function() {
            $("#mode").attr('value', 'delete');
            $("#standardView").submit();
        });
	  	
        /* Valid From Date */
        $.validator.addMethod("validateExtra", function(value, element) {

            if ($('#customField_type').val() == <?php echo CustomField::FIELD_TYPE_SELECT; ?>) {
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
                'customField[name]': {required: '<?php echo __(ValidationMessages::REQUIRED); ?>'},
                'customField[type]': {required: '<?php echo __(ValidationMessages::REQUIRED); ?>'},
                'customField[screen]': {required: '<?php echo __(ValidationMessages::REQUIRED); ?>'},
                'customField[extra_data]' : {validateExtra: '<?php echo __(ValidationMessages::REQUIRED); ?>'}
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
            $('#customFieldList .check').show();
            addEditLinks();
            $('div#messagebar').text('').attr('class', '');            
            $(".paddingLeftRequired").hide();

            $('div.error').each(function(){
                $(this).hide();
            });

        });

        // Add a emergency contact
        $('#buttonAdd').click(function() {
            $("#heading").text("<?php echo __("Add Custom Field"); ?>");
            clearAddForm();

            // Hide list action buttons and checkbox
            $('#listActions').hide();
            $('#customFieldList .check').hide();
            removeEditLinks();
            $('div#messagebar').text('').attr('class', '');            
        
            hideextra();

            //
            //            // hide validation error messages
            //            $("label.errortd[generated='true']").css('display', 'none');
            $('#customFieldAddPane').css('display', 'block');

        });        
    
        $('#customFieldList tbody a').live('click', function() {
            $("#heading").text("<?php echo __("Edit Custom Field"); ?>");
        
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
            $('#customFieldList .check').hide();
            $('#customFieldAddPane').css('display', 'block');

        });

        $('#btnSave').click(function() {
            $('#frmCustomField').submit();
        });
                                
    });

</script>

