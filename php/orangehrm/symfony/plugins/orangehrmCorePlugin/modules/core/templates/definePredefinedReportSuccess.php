<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_stylesheet('orangehrm.datepicker.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
use_javascript('../../../scripts/jquery/ui/ui.datepicker.js');
use_javascript('orangehrm.datepicker.js');
?>
<div id="defineReportContainer">
    <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
        <span style="font-weight: bold;"><?php echo isset($message) ? __($message) : ''; ?></span>
    </div>
    <div class="outerbox">
        <div class="maincontent">
            <div class="mainHeading">
                <h2><?php echo __("Define Report"); ?></h2>
            </div>

            <?php echo $form->renderFormTag(url_for('core/definePredefinedReport'), array('id' => 'defineReportForm')); ?>
            <fieldset id="name_fieldset">
                <?php
                $form->getWidgetSchema()->setFormFormatterName('list');
                echo $form['_csrf_token'];
                echo $form['report_id']->render();

                echo $form['report_name']->renderLabel(__("Report Name") . "<span class='required'> * </span>");
                echo $form['report_name']->render();
                echo $form['report_name']->renderError();
                ?>
            </fieldset>
            <fieldset id="criteria_selection">
                <?php
                echo $form['criteria_list']->renderLabel(__("Selection Criteria"));
                echo $form['criteria_list']->render();
                echo $form['criteria_list']->renderError();
                ?>
                <input type="button" class="savebutton" id="btnAddConstraint" value="<?php echo __("Add"); ?>"  />
            </fieldset>
            <fieldset id="criteria_fieldset_inactive">

                <ul id="filter_fields_inactive" style="display:none;">
                    <?php
                    foreach ($form->filterWidgets as $filterName => $label) {

                        if (!isset($form->selectedFilterWidgets[$filterName])) {
                            $formField = $form[$filterName];

                            echo "<li id='li_" . $filterName . "' >" . $formField->renderLabel() .
                            $formField->render() .
                            $formField->renderError() .
                            "</li>";
                        }
                    }
                    ?>
                </ul>
            </fieldset>
            <fieldset id="criteria_fieldset">
                <legend><?php echo __("Selected Criteria"); ?></legend>
                <ul id="filter_fields">
                    
                    <?php
                    
                    $requiredFilterNames = array();
                    
                    foreach ($form->requiredFilterWidgets as $widget) {

                        $formField = $form[$filterName];
                        echo "<li id='li_" . $filterName . "' class='requiredFilter'>" . $formField->renderLabel() .
                        $formField->render() .
                        $formField->renderError() .
                        "</li>";
                        $requiredFilterNames[] = $filterName;
                    }
                    
                    foreach ($form->selectedFilterWidgets as $filterName => $label) {

                        if (!in_array($filterName, $requiredFilterNames)) {
                        
                            $formField = $form[$filterName];
                            echo "<li id='li_" . $filterName . "' ><a href='#'>X</a>" . $formField->renderLabel() .
                            $formField->render() .
                            $formField->renderError() .
                            "</li>";
                        
                        }
                    }
                    
                    ?>
                </ul>                
            </fieldset>
            <fieldset id="display_field_selection">
                <?php
                    echo $form['display_groups']->renderLabel(__("Display Field Groups"));
                    echo $form['display_groups']->render();
                    echo $form['display_groups']->renderError();
                ?>
                    <input type="button" class="savebutton" id="btnAddDisplayGroup" value="<?php echo __("Add"); ?>"  />
                    <br />
                <?php
                    echo $form['display_field_list']->renderLabel(__("Display Fields"));
                    echo $form['display_field_list']->render();
                    echo $form['display_field_list']->renderError();
                ?>
                    <input type="button" class="savebutton" id="btnAddDisplayField" value="<?php echo __("Add"); ?>"  />
                </fieldset>
                <fieldset id="display_fieldset">
                    <legend><?php echo __("Display Fields"); ?></legend>

                    <ul id="display_groups">
                    <?php
                    foreach ($form->displayFieldGroups as $group => $fields) {
                        $groupId = str_replace('display_group_', '', $group);
                        $selected = in_array($groupId, $form->selectedDisplayFieldGroups);

                        // find if any of the fields in this group are selected.
                        $fieldIds = array();
                        foreach ($fields as $field) {
                            $fieldIds[] = str_replace('display_field_', '', $field);
                        }
                        $selectedGroupFields = array_intersect($fieldIds, $form->selectedDisplayFields);
                        $visible = count($selectedGroupFields) > 0 ? '' : 'style="display:none;"';

                        $groupAttrs = array();
                        if ($selected) {
                            $groupAttrs = array('checked' => 'checked');
                        }
                    ?>
                        <li <?php echo $visible; ?>><a href="#">X</a>
                        <?php
                        echo $form[$group]->renderLabel() . $form[$group]->render($groupAttrs) . $form[$group]->renderError();
                        ?>
                        <ul class="display_field_ul">
                            <?php
                            foreach ($fields as $field) {
                                $fieldId = str_replace('display_field_', '', $field);
                                $fieldSelected = in_array($fieldId, $form->selectedDisplayFields);
                                $visible = $fieldSelected ? '' : 'style="display:none"';
                            ?>
                                <li <?php echo $visible; ?>><a href="#">X</a>
                                <?php
                                $attrs = array('style' => 'display:none;');
                                if ($fieldSelected) {
                                    $attrs['checked'] = 'checked';
                                }
                                echo $form[$field]->renderLabel() . $form[$field]->render($attrs) . $form[$field]->renderError();
                                ?>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                        }
                    ?>
                    </ul>
                </fieldset>

                <div class="formbuttons">
                    <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Save"); ?>"  />
                    <input type="button" class="savebutton" id="btnCancel" value="<?php echo __("Cancel"); ?>" />
                </div>
                </form>
            </div>
        </div>
        <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
    </div>

    <style type="text/css">
        label {
            width: 220px;
        }
        
        ul#display_groups li {
            display: list-item;
        }

        ul#display_groups li label {
            display: inline-block;
            float: none;
            width: 270px;
        }

        ul#display_groups li ul {
            margin-left: 20px;
        }

        ul#filter_fields li {
            display: list-item;
        }

        ul#filter_fields li label {
            display: inline-block;
            float: none;
            width: 200px;
        }

        ul#filter_fields li a {
            display: inline-block;
            float: none;
            margin-left: 10px;
            color: red;
        }

        ul#filter_fields li input[type="text"] {
            padding-left: 0px;
        }

        ul#display_groups li a {
            display: inline-block;
            float: none;
            margin-left: 10px;
            color: red;
        }

        ul#filter_fields li ul.error_list {
            display: inline-block;
            float: none;
            margin-left: 10px;
        }
        
        ul#filter_fields li.requiredFilter {
            padding-left: 18px;
        }

        div#defineReportContainer {
            width: 900px;
        }

        fieldset#name_fieldset,
        #criteria_selection,
        #criteria_fieldset_inactive,
        #criteria_fieldset,
        #display_field_selection,
        #display_fieldset{
            border-color: #FAD163;
        }

        #report_report_name,
        #report_criteria_list,
        #btnAddConstraint,
        #report_display_groups,
        #btnAddDisplayGroup,
        #report_display_field_list,
        #btnAddDisplayField {
            margin-top: 10px;
        }

        .paddingLeftRequired{
            font-size: 8pt;
            padding-left: 15px;
            padding-top: 5px;
        }

        div.error{
            padding-left: 240px;
        }

        fieldset#name_fieldset ul.error_list li{
            padding-left: 140px;
        }

        select#report_criteria_list,
        select#report_display_groups,
        select#report_display_field_list{
            width: 150px;
            height: 18px;
        }
    </style>
    <script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    </script>
    
    <script type="text/javascript">
    $(document).ready(function() {

        var rDate = trim($("#joined_date_from").val());

            if (rDate == '') {
                $("#joined_date_from").val(datepickerDateFormat);
            }

        //Bind date picker
        daymarker.bindElement("#joined_date_from",
        {
            onSelect: function(date){

            },
            dateFormat : datepickerDateFormat
        });

        $('#joined_date_from_Button').click(function(){
            daymarker.show("#joined_date_from");
    });

        var rDate = trim($("#joined_date_to").val());
            if (rDate == '') {
                $("#joined_date_to").val(datepickerDateFormat);
            }

        //Bind date picker
        daymarker.bindElement("#joined_date_to",
        {
            onSelect: function(date){

            },
            dateFormat : datepickerDateFormat
        });

        $('#joined_date_to_Button').click(function(){
            daymarker.show("#joined_date_to");
    });

           
        $('#filter_fields_inactive').find(':input').attr('disabled', 'disabled');
    
        // update display fields
        updateDisplayGroupList();
        updateDisplayFieldList();
    
        function updateDisplayFieldList() {
            var selectedGroup = $('#report_display_groups option:selected').val();

            var li = $('#' + selectedGroup).parent();
            $('#report_display_field_list').find('option').remove();
            li.find('li:hidden').each(function() {

                var fieldId = $(this).find('input').attr('id');
                var label = $(this).children('label').text();

                $('#report_display_field_list').append('<option value="' + fieldId + '">' + label + '</option>');
            });
        
            var numFields = $('#report_display_field_list').find('option').length;
        
            if (numFields > 0) {
                $("#btnAddDisplayField").removeAttr('disabled');
            } else {
                $("#btnAddDisplayField").attr('disabled', 'disabled');
            }
        
        }
    
        function updateDisplayGroupList() {
            $('#report_display_groups option').remove();
            $('#display_groups ul.display_field_ul').each(function() {

                if ($(this).children('li:hidden').length > 0) {
                    var id = $(this).siblings('input').attr('id');
                    var label = $(this).siblings('label').text();

                    // Look for and remove "(Include Header)" text from label
                    var includeHeaderStart = label.indexOf('(');

                    label = label.substring(0, includeHeaderStart);
                    $('#report_display_groups').append('<option value="' + id + '">' + label + '</option>');
                }
            });
        
            if ($('#report_display_groups option').length > 0) {
                $("#btnAddDisplayGroup").removeAttr('disabled');
            }
        
        }
    
        $('#report_display_groups').change(function(){
            updateDisplayFieldList();
        });
        $('ul#filter_fields li a').live('click', function(event) {
            event.preventDefault();
        
            var li = $(this).parent();
            $(this).remove();
            li.children(':input').attr('disabled', 'disabled');

            var label = li.children('label').text();
            var value = li.attr('id').substr(3);
        
            var inputFields = li.find(':input:not([type=hidden])').length;
            if (inputFields > 1) {
                li.find(':input:not([type=hidden])').hide().val('');
            }
        
            // move to inactive list and add to drop down.
            li.appendTo($('#filter_fields_inactive'));
            $('#report_criteria_list').append("<option value='" + value + "'>" + label + "</option>");
        
            $("#btnAddConstraint").removeAttr('disabled');
        });
    
        $('ul#display_groups > li a').live('click', function(event) {
            event.preventDefault();
            var li = $(this).parent();
            li.find('li').each(function() {
                $(this).find('input').attr('checked', false);
                $(this).hide();
            });
            var groupInput = li.children('input');
            var groupId = groupInput.attr('id');
        
            $('#report_display_groups option[value=' + groupId + ']').show();
        
            groupInput.attr('checked', false);
            li.hide();
            updateDisplayGroupList();
            updateDisplayFieldList();
        });

        $('ul#display_groups ul.display_field_ul li a').live('click', function(event) {
            event.preventDefault();
            var li = $(this).parent();
            li.find('input').attr('checked', false);
            li.hide();
            var groupId = li.parents('ul.display_field_ul').parent().children('input').attr('id');
        
            if ($('#report_display_groups option[value=' + groupId + ']').length == 0) {
                updateDisplayGroupList();
            }

            updateDisplayFieldList();
        });


        $("#btnAddConstraint").click(function() {

        
            var selectedItem = $('#report_criteria_list option:selected').remove().val();
        
            var delLink = $('<a/>').attr('href', '#').text('X');
        
            /*var delLink = $('<a/>').attr('href', '#').text('X').click(function() {
        var li = $(this).parent();
        $(this).remove();
        li.children(':input').attr('disabled', 'disabled');
            
        var label = li.children('label').text();
            
        // move to inactive list and add to drop down.
        li.appendTo($('#filter_fields_inactive'));
        $('#report_criteria_list').append("<option value='" + selectedItem + "'>" + label + "</option>");
            
    });*/
                            
        
            var selectedLi = $('#li_' + selectedItem);
        
            /*
             * Note: We are first removing disabled attribute from options, because
             * if we first remove it from the select and later the options inside the select,
             * the default selection is not retained.
             */
            selectedLi.find('option').removeAttr('disabled');
            selectedLi.find(':input').removeAttr('disabled');
            selectedLi.find('select option:first-child').attr("selected", true);
        

            $('#' + selectedItem + "_comparision").show();
            selectedLi.prepend(delLink).appendTo($('#filter_fields'));
        
            if ($('#report_criteria_list option').length == 0) {
                $(this).attr('disabled', 'disabled');
            }
        
        });

        $("#btnAddDisplayGroup").click(function() {
               
            var selectedItem = $('#report_display_groups option:selected').remove();
            selectedItem.attr('selected', false);
            var nextChild = selectedItem.next();
            if (!nextChild) {
                nextChild = selectedItem.prev();
            }
        
            if (nextChild) {
                $('#report_display_groups').val(nextChild.val());
            } else {
                $('#report_display_groups').val('');
            }
        
            var selectedItemLi = $('#' + selectedItem.val());
            selectedItemLi.parent().show().find('li').show().find('input').attr('checked', true);
        
            updateDisplayFieldList();
            clearErrors();
        
            if ($('#report_display_groups option').length == 0) {
                $(this).attr('disabled', 'disabled');
            }
        });
    
        $("#btnAddDisplayField").click(function() {
              
            var selectedItem = $('#report_display_field_list option:selected').hide();
            selectedItem.attr('selected', false);
            var nextChild = selectedItem.next();
            if (!nextChild) {
                nextChild = selectedItem.prev();
            }
        
            if (nextChild) {
                $('#report_display_field_list').val(nextChild.val());
            }
        
            var selectedItemInput = $('#' + selectedItem.val());
            selectedItemInput.attr('checked', true);
            selectedItemInput.parent().show().parents('li').show();
        
            updateDisplayFieldList();
            clearErrors();
                
        });
    
        $("#btnSave").click(function() {

            /*var haveErrors = false;
    var reportName = $.trim($('#report_report_name').val());
    var numSelectedDisplayFields = $('ul.display_field_ul input:checked').length;
        
    if (reportName == '') {
            
    }
        
    if (numSelectedDisplayFields == 0) {}*/
            $("#defineReportForm").submit();

        });
    
        //form validation
        var reportValidator =
            $("#defineReportForm").validate({
            rules: {
                'report[report_name]': {required: true},
                'display_fields[]': {required: true, minlength: 1}
            },
            messages: {
                'report[report_name]': '<?php echo __(ValidationMessages::REQUIRED);?>',
                'display_fields[]': '<?php echo __(ValidationMessages::REQUIRED);?>'
            },
            errorElement : 'div',
            errorPlacement: function(error, element) {
                var elementId = element.attr('id');
                
                if (elementId == 'report_report_name') {
                    error.insertAfter(element);
                } else {
                    error.insertAfter('#btnAddDisplayField');
                }

            }
        });
    
        function clearErrors() {
            $('div.error[generated="true"]').remove();
        }

        $("#btnCancel").click(function(){
            navigateUrl("viewDefinedPredefinedReports");
        });
    });
</script>
