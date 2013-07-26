<?php
echo stylesheet_tag(theme_path('css/orangehrm.datepicker.css'));
use_javascript('orangehrm.datepicker.js');
?>

<div class="box single">
   
    <div class="head">
        <h1><?php echo __("Define Report"); ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        
        <?php if(($reportPermissions->canCreate() && empty($reportId)) || ($reportPermissions->canUpdate() && $reportId > 0)){?>
        <?php echo $form->renderFormTag(url_for('core/definePredefinedReport'), array('id' => 'defineReportForm')); ?>
        <fieldset id="name_fieldset">
            <?php
            $form->getWidgetSchema()->setFormFormatterName('list');
            echo $form['_csrf_token'];
            echo $form['report_id']->render();
            ?>
            <ol>
                <li>
                <?php 
                    echo $form['report_name']->renderLabel(__("Report Name") . "<span class='required'> * </span>");
                    echo $form['report_name']->render();
                    echo $form['report_name']->renderError();
                ?>
                </li>
            </ol>
        </fieldset>
        <fieldset id="criteria_selection">
            <ol>
                <li>
                    <?php
                    echo $form['criteria_list']->renderLabel(__("Selection Criteria"));
                    echo $form['criteria_list']->render(array("class" => "drpDown", "maxlength" => 30));
                    echo $form['criteria_list']->renderError();
                    ?>
                   <a class="fieldHelpRight" id="btnAddConstraint"><?php echo __("Add"); ?></a>
                </li>
            </ol>
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
            <ol id="filter_fields">
                <li>
            <?php echo __("Selected Criteria"); ?>
                </li>
            
                <?php
                
                $requiredFilterNames = array();
                
                foreach ($form->requiredFilterWidgets as $widget) {
    
                    $filterName = $widget->getName();
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
                        echo "<li id='li_" . $filterName . "' ><a href=\"#\" class=\"closeText\">X</a>" . $formField->renderLabel() .
                        $formField->render() .
                        $formField->renderError() .
                        "</li>";
                    
                    }
                }
                
                ?>
        </ol>
        </fieldset>
        <fieldset id="display_field_selection">
            <ol>
                <li>
            <?php
                echo $form['display_groups']->renderLabel(__("Display Field Groups"));
                echo $form['display_groups']->render();
                echo $form['display_groups']->renderError();
            ?>
                <a class="fieldHelpRight" id="btnAddDisplayGroup"><?php echo __("Add"); ?></a>
                <br />
                </li>
                <li>
            <?php
                echo $form['display_field_list']->renderLabel(__("Display Fields"));
                echo $form['display_field_list']->render();
                echo $form['display_field_list']->renderError();
            ?>
                <a class="fieldHelpRight" id="btnAddDisplayField"><?php echo __("Add"); ?></a>
                </li>
                </ol>
            </fieldset>
            <fieldset id="display_fieldset">
                <ol id="display_groups">
                <li>
                    <?php echo __("Display Fields"); ?>
                </li>
    
                
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
                    <li <?php echo $visible; ?>><a href="#" class="closeText">X</a>
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
                            <li <?php echo $visible; ?>><a href="#" class="closeText">X</a>
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
                <li class="required line">
                      <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                </li>
                </ol>
            </fieldset>
         
        
        <div class="formbuttons">
            <input type="button" id="btnSave" value="<?php echo __("Save"); ?>"  />
            <input type="button" class="cancel" id="btnCancel" value="<?php echo __("Cancel"); ?>" />
        </div>
        <?php }?>
        </form>
        </div>
    </div>


    <style type="text/css">
        label {
            width: 220px !important;
        }
        
        ul#display_groups li {
            display: list-item;
        }
        
        .error_list li {
            margin-left: 240px;
        }

        .box a {
            margin-right: 10px;
            float: left;
        }
        
        .display_field_ul {
            margin-left: 10px;
        }
        
        #display_field_selection label, #name_fieldset label, #criteria_selection label {
            width: 240px !important;
        }
        
        form ol li span.validation-error {
            left: 240px;
        }
        
        #li_include label {
            margin-left: 20px;
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

        .paddingLeftRequired{
            font-size: 8pt;
            padding-left: 15px;
            padding-top: 5px;
        }

        input[type="checkbox"] {
            width: 14px;
        }
        
        #joined_date_from, #joined_date_to, #age_group_value1, #age_group_value2, #service_period_value1, #service_period_value2 {
            margin-left: 20px;
        }
        #age_group_value1, #age_group_value2, #service_period_value1, #service_period_value2 {
            width: 192px;
        }

    </style>
    <script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    </script>
    
    <script type="text/javascript">
    $(document).ready(function() {

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
        $('ol#filter_fields li a').live('click', function(event) {
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
    
        $('ol#display_groups > li a').live('click', function(event) {
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

        $('ol#display_groups ul.display_field_ul li a').live('click', function(event) {
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
        
        var delLink = $('<a/>').attr('href', '#').text('X').addClass('closeText');
        
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
            ignore: [], // reset the ignore list. by default hidden fields are ignored when validating
            rules: {
                'report[report_name]': {required: true},
                'display_fields[]': {required: true, minlength: 1}
            },
            messages: {
                'report[report_name]': '<?php echo __(ValidationMessages::REQUIRED);?>',
                'display_fields[]': '<?php echo __(ValidationMessages::REQUIRED);?>'
            },
            errorElement : 'span',
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
            window.location.href = 'viewDefinedPredefinedReports';
        });
    });
</script>
