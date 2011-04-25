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
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<script type="text/javascript">
//<![CDATA[

var fileModified = 0;

//]]>
</script>
<?php

use_stylesheet('../orangehrmPimPlugin/css/viewDependentsSuccess');
use_javascript('../orangehrmPimPlugin/js/viewDependentsSuccess');

use_stylesheet('orangehrm.datepicker.css');
use_javascript('orangehrm.datepicker.js');

$numDependents = count($dependents);
$haveDependents = $numDependents > 0;
$allowEdit = true;
$allowDel = true;
?>
<?php if ($form->hasErrors()): ?>
<span class="error">
<?php
echo $form->renderGlobalErrors();

foreach($form->getWidgetSchema()->getPositions() as $widgetName) {
  echo $form[$widgetName]->renderError();
}
?>
</span>
<?php endif; ?>
<?php // To be moved into layout ?>
<table cellspacing="0" cellpadding="0" border="0" >
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2" height="30">&nbsp;<?php if($showBackButton) {?><input type="button" class="backbutton" value="<?php echo __("Back") ?>" onclick="navigateUrl('<?php echo url_for('pim/viewEmployeeList');?>')" /><?php }?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top">
        <?php include_partial('leftmenu', array('empNumber' => $empNumber, 'form' => $form));?></td>
        <td valign="top">

<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
            
<div class="formpage2col">
<div id="addPaneDependent" style="display:none;" >
<div class="outerbox">

    <div class="mainHeading"><h2 id="heading"><?php echo __('Add Dependent'); ?></h2></div>
    <form name="frmEmpDependent" id="frmEmpDependent" method="post" action="<?php echo url_for('pim/updateDependent?empNumber=' . $empNumber); ?>">

    <?php echo $form['_csrf_token']; ?>
    <?php echo $form["empNumber"]->render(); ?>
    <?php echo $form["seqNo"]->render(); ?>

    <?php echo $form['name']->renderLabel(__('Name') . ' <span class="required">*</span>'); ?>
    <?php echo $form['name']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
    <br class="clear"/>

    <?php echo $form['relationshipType']->renderLabel(__('Relationship') . ' <span class="required">*</span>'); ?>
    <?php echo $form['relationshipType']->render(array("class" => "formInputSelect")); ?>
    <br class="clear"/>

    <div id="relationshipDesc">
    <?php echo $form['relationship']->renderLabel(__('Please Sepecify') . ' <span class="required">*</span>'); ?>
    <?php echo $form['relationship']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
    <br class="clear"/>
    </div>

    <?php echo $form['dateOfBirth']->renderLabel(__('Date of Birth')); ?>
    <?php echo $form['dateOfBirth']->render(array("class" => "formDateInput")); ?>
    <input id="dobBtn" type="button" name="" value="  " class="calendarBtn" />
    <br class="clear"/>

    
    <?php if (($allowEdit)) { ?>
            <div class="formbuttons">
                <input type="button" class="savebutton" name="btnSaveDependent" id="btnSaveDependent"
                       value="<?php echo __("Save"); ?>"
                       title="<?php echo __("Save"); ?>"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                <input type="button" id="btnCancel" class="cancelbutton" value="<?php echo __("Cancel"); ?>"/>
            </div>
    <?php } ?>
    </form>
</div>
</div>

<div class="outerbox" id="listing">
<form name="frmEmpDelDependents" id="frmEmpDelDependents" method="post" action="<?php echo url_for('pim/deleteDependents?empNumber=' . $empNumber); ?>">
<?php echo $deleteForm['_csrf_token']->render(); ?>
<?php echo $deleteForm['empNumber']->render(); ?>

    <div class="mainHeading"><h2><?php echo __("Assigned Dependents"); ?></h2></div>

    <div class="actionbar" id="listActions">
            <div class="actionbuttons">
<?php if ($allowEdit) { ?>

                    <input type="button" class="addbutton" id="btnAddDependent" onmouseover="moverButton(this);" onmouseout="moutButton(this);" value="<?php echo __("Add"); ?>" title="<?php echo __("Add"); ?>"/>
            <?php } ?>
            <?php if ($allowDel) {
 ?>

                <input type="button" class="delbutton" id="delDependentBtn" onmouseover="moverButton(this);" onmouseout="moutButton(this);" value="<?php echo __("Delete"); ?>" title="<?php echo __("Delete"); ?>"/>
            <?php } ?>
        </div>
    </div>

    <table width="550" cellspacing="0" cellpadding="0" class="data-table" id="dependent_list">
        <thead>
            <tr>
                <td class="check"><input type='checkbox' id='checkAll' class="checkbox" /></td>
                <td class="dependentName"><?php echo __("Name"); ?></td>
                <td colspan="2"><?php echo __("Relationship"); ?></td>
                <td><?php echo __("Date of Birth"); ?></td>
            </tr>
        </thead>
        <tbody>
<?php
            $row = 0;
            foreach ($dependents as $dependent) {
                $cssClass = ($row % 2) ? 'even' : 'odd';
                echo '<tr class="' . $cssClass . '">';
                echo "<td class='check'><input type='checkbox' class='checkbox' name='chkdependentdel[]' value='" . $dependent->seqno . "'/></td>";
?>
            <td class="dependentName"><a href="#"><?php echo $dependent->name; ?></a></td>
            <input type="hidden" id="relationType_<?php echo  $dependent->seqno;?>" value="<?php echo $dependent->relationship_type;?>" />
            <input type="hidden" id="relationship_<?php echo  $dependent->seqno;?>" value="<?php echo $dependent->relationship;?>" />
            <input type="hidden" id="dateOfBirth_<?php echo  $dependent->seqno;?>" value="<?php echo ohrm_format_date($dependent->date_of_birth);?>" />
            <td>
                <?php if ($dependent->relationship_type != 'other') { echo __($dependent->relationship_type); ?>
                <?php } else { echo $dependent->relationship; } ?>
            </td>
            <td></td>
            <?php
                echo '<td>' . ohrm_format_date($dependent->date_of_birth) .  '</td>';
                echo '</tr>';
                $row++;
            } ?>
            </tbody>
        </table>
    </form>
</div>
</div>
<div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>
<?php echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => 'dependents'));?>
            </td>
            <!-- To be moved to layout file -->
            <td valign="top" style="text-align:left;">
            </td>
    </tr>
</table>
<script type="text/javascript">
    //<![CDATA[

    // Move to separate js after completing initial work
    var dateFormat	= '<?php echo $sf_user->getDateFormat();?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat());?>';
    var dateDisplayFormat = dateFormat.toUpperCase();

    function clearAddForm() {
        $('#dependent_seqNo').val('');
        $('#dependent_name').val('');
        $('#dependent_relationshipType').val('');
        $('#dependent_relationship').val('');
        $('#dependent_dateOfBirth').val(dateDisplayFormat);
        $('div#addPaneDependent label.error').hide();
        $('div#messagebar').hide();
    }

    function addEditLinks() {
        $('#dependent_list tbody td.dependentName').wrapInner('<a href="#"/>');
    }

    function removeEditLinks() {
        $('#dependent_list tbody td.dependentName').each(function(index) {
            var linkContent = $(this).find('a').html();
            $(this).html(linkContent);

        });
    }

    function hideShowRelationshipOther() {
        if ($('#dependent_relationshipType').val() == 'child' || $('#dependent_relationshipType').val() == '') {
            $('#relationshipDesc').hide();
        } else {
            $('#relationshipDesc').show();
        }
    }

    $(document).ready(function() {
        
        if($(".checkbox").length > 1) {
            $("#addPaneDependent").hide();
            $("#listing").show();
            $(".paddingLeftRequired").hide();
        } else {
            $("#btnCancel").hide();
            $("#addPaneDependent").show();
            $("#listing").hide();
            $(".paddingLeftRequired").show();
        }
        
        $("#checkAll").click(function(){
            if($("#checkAll:checked").attr('value') == 'on') {
                $(".checkbox").attr('checked', 'checked');
            } else {
                $(".checkbox").removeAttr('checked');
            }
        });

        $(".checkbox").click(function() {
            $("#checkAll").removeAttr('checked');
            if(($(".checkbox").length - 1) == $(".checkbox:checked").length) {
                $("#checkAll").attr('checked', 'checked');
            }
        });

        //Load default Mask if empty
        var hDate = trim($("#dependent_dateOfBirth").val());
        if (hDate == '') {
            $("#dependent_dateOfBirth").val(dateDisplayFormat);
        }
        
        hideShowRelationshipOther();
        
        // Edit a emergency contact in the list
        $('#frmEmpDelDependents a').live('click', function() {
            $("#heading").text("<?php echo __("Edit Dependent");?>");
            var row = $(this).closest("tr");
            var seqNo = row.find('input.checkbox:first').val();
            var name = $(this).text();

            var relationshipType = $("#relationType_" + seqNo).val();
            var relationship = $("#relationship_" + seqNo).val();
            var dateOfBirth = $("#dateOfBirth_" + seqNo).val();

            $('#dependent_seqNo').val(seqNo);
            $('#dependent_name').val(name);
            $('#dependent_relationshipType').val(relationshipType);
            $('#dependent_relationship').val(relationship);

            if ($.trim(dateOfBirth) == '') {
                dateOfBirth = dateDisplayFormat;
            }
            $('#dependent_dateOfBirth').val(dateOfBirth);

            $('div#messagebar').hide();
            hideShowRelationshipOther();
            // hide validation error messages

            $('#listActions').hide();
            $('#dependent_list td.check').hide();
            $('#addPaneDependent').css('display', 'block');

            $(".paddingLeftRequired").show();

        });

        //Bind date picker
        daymarker.bindElement("#dependent_dateOfBirth",
            {onSelect: function(date){
                $("#dependent_dateOfBirth").valid();
                },
            dateFormat:jsDateFormat
            });

        $('#dobBtn').click(function(){
           daymarker.show("#dependent_dateOfBirth");
        });

        $('#dependent_relationshipType').change(function() {
            hideShowRelationshipOther();
        });

        // Cancel in add pane
        $('#btnCancel').click(function() {
            clearAddForm();
            $('#addPaneDependent').css('display', 'none');
            $('#listActions').show();
            $('#dependent_list td.check').show();
            addEditLinks();
            $('div#messagebar').hide();
            $(".paddingLeftRequired").hide();
        });

        // Add a emergency contact
        $('#btnAddDependent').click(function() {
            $("#heading").text("<?php echo __("Add Dependent");?>");
            clearAddForm();

            // Hide list action buttons and checkbox
            $('#listActions').hide();
            $('#dependent_list td.check').hide();
            removeEditLinks();
            $('div#messagebar').hide();
            
            hideShowRelationshipOther();

            //
            //            // hide validation error messages
            //            $("label.errortd[generated='true']").css('display', 'none');
            $('#addPaneDependent').css('display', 'block');

            $(".paddingLeftRequired").show();

        });

        /* Valid Contact Phone */
        $.validator.addMethod("validContactPhone", function(value, element) {

            if ( $('#dependent_homePhone').val() == '' && $('#dependent_mobilePhone').val() == '' &&
                    $('#dependent_workPhone').val() == '' )
                return false;
            else
                return true
        });
        
        $("#frmEmpDependent").validate({

            rules: {
                'dependent[name]' : {required:true, maxlength:100},
                'dependent[relationshipType]' : {required: true},
                'dependent[relationship]' : {required: function(element) {
                    return $('#dependent_relationshipType').val() == 'other';
                }},
                'dependent[dateOfBirth]' : {valid_date: function() {
                        return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}
                    }
                },
                maxlength:100
            },
            messages: {
                'dependent[name]': {
                    required:'<?php echo __("Name is required") ?>',
                    maxlength: '<?php echo __('Maximum character limit exceeded for') ?> <?php echo __('Name') ?>'
                },
                'dependent[relationshipType]': {
                    required:'<?php echo __("Relationship is required") ?>'
                },
                'dependent[relationship]': {
                    required:'<?php echo __("Please specify the relationship") ?>',
                    maxlength:'<?php echo __("Maximum character limit exceeded.");?>'
                },
                'dependent[dateOfBirth]' : {
                    valid_date: '<?php echo __("Please enter a valid date in %format% format", array('%format%'=>$sf_user->getDateFormat())) ?>'
                }
            },
            errorPlacement: function(error, element) {
                    error.appendTo( element.prev('label') );
                }


        });

        
        $('#delDependentBtn').click(function() {
            var checked = $('#frmEmpDelDependents input:checked').length;

            if (checked == 0) {
                $("#messagebar").attr('class', "messageBalloon_notice");
                $("#messagebar").text('<?php echo __("Select at least One Record to Delete"); ?>');
            } else {
                $('#frmEmpDelDependents').submit();
            }
        });

        $('#btnSaveDependent').click(function() {
            $('#frmEmpDependent').submit();
        });
});
//]]>
</script>

