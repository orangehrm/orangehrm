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
<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>

<?php
$numDependents = count($dependents);
$haveDependents = $numDependents > 0;
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

<div class="box pimPane">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
    
    <?php if ($dependentPermissions->canCreate() || ($haveDependents && $dependentPermissions->canUpdate())) { ?>
    <div id="addPaneDependent">
        <div class="head">
            <h1 id="heading"><?php echo __('Add Dependent'); ?></h1>
        </div>
        
        <div class="inner">
            <form name="frmEmpDependent" id="frmEmpDependent" method="post" action="<?php echo url_for('pim/updateDependent?empNumber=' . $empNumber); ?>">
                <?php echo $form['_csrf_token']; ?>
                <?php echo $form["empNumber"]->render(); ?>
                <?php echo $form["seqNo"]->render(); ?>
                <fieldset>
                    <ol>
                        <li>
                            <?php echo $form['name']->renderLabel(__('Name') . ' <em>*</em>'); ?>
                            <?php echo $form['name']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
                        </li>
                        <li>
                            <?php echo $form['relationshipType']->renderLabel(__('Relationship') . ' <em>*</em>'); ?>
                            <?php echo $form['relationshipType']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li id="relationshipDesc">
                            <?php echo $form['relationship']->renderLabel(__('Please Specify') . ' <em>*</em>'); ?>
                            <?php echo $form['relationship']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
                        </li>
                        <li>
                            <?php echo $form['dateOfBirth']->renderLabel(__('Date of Birth')); ?>
                            <?php echo $form['dateOfBirth']->render(array("class" => "formDateInput")); ?>    
                        </li>
                        <li class="required">
                            <em>*</em><?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                    </ol>
                    <p>
                        <input type="button" class="" name="btnSaveDependent" id="btnSaveDependent" value="<?php echo __("Save"); ?>"/>
                        <input type="button" id="btnCancel" class="reset" value="<?php echo __("Cancel"); ?>"/>
                    </p>
                </fieldset>
            </form>
        </div>
    </div> <!-- addPaneDependent -->
    <?php } ?>
    
    <div class="miniList" id="listing">
        <div class="head">
            <h1><?php echo __("Assigned Dependents"); ?></h1>
        </div>
        
        <div class="inner">
            <?php if ($dependentPermissions->canRead()) : ?>
            
            <?php include_partial('global/flash_messages', array('prefix' => 'viewDependents')); ?>
            
            <form name="frmEmpDelDependents" id="frmEmpDelDependents" method="post" action="<?php echo url_for('pim/deleteDependents?empNumber=' . $empNumber); ?>">
                <?php echo $deleteForm['_csrf_token']->render(); ?>
                <?php echo $deleteForm['empNumber']->render(); ?>
                <p id="listActions">
                    <?php if ($dependentPermissions->canCreate()) { ?>
                    <input type="button" class="" id="btnAddDependent" value="<?php echo __("Add"); ?>"/>
                    <?php } ?>
                    <?php if ($dependentPermissions->canDelete()) { ?>
                    <input type="button" class="delete" id="delDependentBtn" value="<?php echo __("Delete"); ?>"/>
                    <?php } ?>
                </p>
                <table id="dependent_list" class="table hover">
                    <thead>
                        <tr><?php if ($dependentPermissions->canDelete()) { ?>
                            <th class="check" style="width:2%"><input type='checkbox' id='checkAll' class="checkbox" /></th>
                            <?php } ?>
                            <th class="dependentName"><?php echo __("Name"); ?></th>
                            <th><?php echo __("Relationship"); ?></th>
                            <th><?php echo __("Date of Birth"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!$haveDependents) { ?>
                        <tr>
                            <?php if ($dependentPermissions->canDelete()) { ?>
                            <td class="check"></td>
                            <?php } ?>
                            <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php } else { ?>                        
                        <?php
                        $row = 0;
                        foreach ($dependents as $dependent) :
                            $cssClass = ($row % 2) ? 'even' : 'odd';
                            echo '<tr class="' . $cssClass . '">';
                            if ($dependentPermissions->canDelete()) {
                            echo "<td class='check'><input type='checkbox' class='checkbox' name='chkdependentdel[]' value='" . $dependent->seqno . "'/></td>";
                            } else {
                            ?>
                            <input type='hidden' class='checkbox' value="<?php echo $dependent->seqno; ?>"/>
                            <?php
                            }
                            ?>
                            <td class="dependentName">
                                <?php if ($dependentPermissions->canUpdate()) { ?>
                                <a href="#"><?php echo $dependent->name; ?></a>
                                <?php
                                } else {
                                echo $dependent->name;
                                }
                                ?>
                            </td>
                            <input type="hidden" id="relationType_<?php echo $dependent->seqno; ?>" value="<?php echo $dependent->relationship_type; ?>" />
                            <input type="hidden" id="relationship_<?php echo $dependent->seqno; ?>" value="<?php echo $dependent->relationship; ?>" />
                            <input type="hidden" id="dateOfBirth_<?php echo $dependent->seqno; ?>" value="<?php echo set_datepicker_date_format($dependent->date_of_birth); ?>" />
                            <td>
                                <?php if ($dependent->relationship_type != 'other') {
                                echo __($dependent->relationship_type); ?>
                                <?php } else {
                                echo $dependent->relationship;
                                } ?>
                            </td>
                            <?php
                            echo '<td>' . set_datepicker_date_format($dependent->date_of_birth) . '</td>';
                            echo '</tr>';
                            $row++;
                        endforeach;
                        ?>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
            <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
            <?php endif; ?>
        </div>
    </div> <!-- miniList -->
    
    <!-- Attachments & Custom Fields -->
    <?php
    echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => CustomField::SCREEN_DEPENDENTS));
    echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => EmployeeAttachment::SCREEN_DEPENDENTS));
    ?>
    
</div> <!-- Box -->

<script type="text/javascript">
    //<![CDATA[
    // Move to separate js after completing initial work
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))); ?>'

    $('#delDependentBtn').attr('disabled', 'disabled');

    function clearAddForm() {
        $('#dependent_seqNo').val('');
        $('#dependent_name').val('');
        $('#dependent_relationshipType').val('');
        $('#dependent_relationship').val('');
        $('#dependent_dateOfBirth').val(displayDateFormat);
        $('div#addPaneDependent label.error').hide();
        $('div#messagebar').hide();
    }

    function addEditLinks() {
        removeEditLinks();
        $('#dependent_list tbody td.dependentName').wrapInner('<a href="#"/>');
    }

    function removeEditLinks() {
        $('#dependent_list tbody td.dependentName a').each(function(index) {
            $(this).parent().text($(this).text());
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
        
        $('#addPaneDependent').hide();
        <?php  if (!$haveDependents){?>
        $(".check").hide();
        <?php } ?>
        
        $("#checkAll").click(function(){
            if($("#checkAll:checked").attr('value') == 'on') {
                $(".checkbox").attr('checked', 'checked');
            } else {
                $(".checkbox").removeAttr('checked');
            }
            
            if($('.checkbox:checkbox:checked').length > 0) {
                $('#delDependentBtn').removeAttr('disabled');
            } else {
                $('#delDependentBtn').attr('disabled', 'disabled');
            }
        });

        $(".checkbox").click(function() {
            $("#checkAll").removeAttr('checked');
            if(($(".checkbox").length - 1) == $(".checkbox:checked").length) {
                $("#checkAll").attr('checked', 'checked');
            }
            
            if($('.checkbox:checkbox:checked').length > 0) {
                $('#delDependentBtn').removeAttr('disabled');
            } else {
                $('#delDependentBtn').attr('disabled', 'disabled');
            }
        });

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
                dateOfBirth = displayDateFormat;
            }
            $('#dependent_dateOfBirth').val(dateOfBirth);

            $('div#messagebar').hide();
            hideShowRelationshipOther();
            // hide validation error messages

            $('#listActions').hide();
            $('#dependent_list .check').hide();
            $('#addPaneDependent').css('display', 'block');

            $(".paddingLeftRequired").show();
            $('#btnCancel').show();

        });

        $('#dependent_relationshipType').change(function() {
            hideShowRelationshipOther();
        });

        // Cancel in add pane
        $('#btnCancel').click(function() {
            clearAddForm();
            $('#addPaneDependent').css('display', 'none');
            $('#listActions').show();
            $('#dependent_list .check').show();
            <?php if ($dependentPermissions->canUpdate()){?>
            addEditLinks();
            <?php }?>
            $('div#messagebar').hide();
            $(".paddingLeftRequired").hide();
        });

        // Add a emergency contact
        $('#btnAddDependent').click(function() {
            $("#heading").text("<?php echo __("Add Dependent");?>");
            clearAddForm();

            // Hide list action buttons and checkbox
            $('#listActions').hide();
            $('#dependent_list .check').hide();
            removeEditLinks();
            $('div#messagebar').hide();
            
            hideShowRelationshipOther();

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
                        return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat}
                    }
                },
                maxlength:100
            },
            messages: {
                'dependent[name]': {
                    required:'<?php echo __(ValidationMessages::REQUIRED) ?>',
                    maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS,array('%amount%' => 100)) ?>'
                },
                'dependent[relationshipType]': {
                    required:'<?php echo __(ValidationMessages::REQUIRED) ?>'
                },
                'dependent[relationship]': {
                    required:'<?php echo __(ValidationMessages::REQUIRED) ?>',
                    maxlength:'<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS,array('%amount%' => 100));?>'
                },
                'dependent[dateOfBirth]' : {
                    valid_date: lang_validDateMsg
                }
            }
        });

        
        $('#delDependentBtn').click(function() {
            var checked = $('#frmEmpDelDependents input:checked').length;

            if (checked == 0) {
                $('div#messagebar').show();
                $("#messagebar").attr('class', "messageBalloon_notice");
                $("#messagebar").text('<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>');
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

