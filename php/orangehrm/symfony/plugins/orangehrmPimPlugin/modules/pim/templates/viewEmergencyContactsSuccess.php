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

use_stylesheet('../orangehrmPimPlugin/css/viewEmergencyContactsSuccess');
use_javascript('../orangehrmPimPlugin/js/viewEmergencyContactsSuccess');

$numContacts = count($emergencyContacts);
$haveContacts = $numContacts > 0;
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
<script type="text/javascript">
//<![CDATA[

var fileModified = 0;

//]]>
</script>
<?php // To be moved into layout ?>
<table cellspacing="0" cellpadding="0" border="0" >
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top">
        <?php include_partial('leftmenu', array('empNumber' => $empNumber, 'form' => $form));?></td>
        <td valign="top">

<div class="formpage2col">
    
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="addPaneEmgContact" <?php echo $haveContacts ? 'style="display:none;"' : '';?> >
<?php if ($emergencyContactPermissions->canCreate() || ($haveContacts && $emergencyContactPermissions->canUpdate())) { ?>
    <div class="outerbox">

    <div class="mainHeading"><h2 id="emergencyContactHeading"><?php echo __('Add Emergency Contact'); ?></h2></div>
    <form name="frmEmpEmgContact" id="frmEmpEmgContact" method="post" action="<?php echo url_for('pim/updateEmergencyContact?empNumber=' . $empNumber); ?>">

    <?php echo $form['_csrf_token']; ?>
    <?php echo $form["empNumber"]->render(); ?>
    <?php if ($emergencyContactPermissions->canRead()) { ?>
    <?php echo $form["seqNo"]->render(); ?>

    <?php echo $form['name']->renderLabel(__('Name') . ' <span class="required">*</span>'); ?>
    <?php echo $form['name']->render(array("class" => "formInputText", "maxlength" => 50)); ?>

    <?php echo $form['relationship']->renderLabel(__('Relationship') . ' <span class="required">*</span>'); ?>
    <?php echo $form['relationship']->render(array("class" => "formInputText", "maxlength" => 30)); ?>
    <br class="clear"/>

    <?php echo $form['homePhone']->renderLabel(__('Home Telephone')); ?>
    <?php echo $form['homePhone']->render(array("class" => "formInputText", "maxlength" => 25)); ?>

    <?php echo $form['mobilePhone']->renderLabel(__('Mobile')); ?>
    <?php echo $form['mobilePhone']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
    <br class="clear"/>

    <?php echo $form['workPhone']->renderLabel(__('Work Telephone')); ?>
    <?php echo $form['workPhone']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
    <br class="clear"/>
    <?php }?>
    
    
            <div class="formbuttons">
                <input type="button" class="savebutton" name="btnSaveEContact" id="btnSaveEContact"
                       value="<?php echo __("Save"); ?>"
                       title="<?php echo __("Save"); ?>"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                <?php if (($haveContacts) || ($haveContacts && $emergencyContactPermissions->canCreate()) || ($haveContacts && $emergencyContactPermissions->canUpdate())) { ?>
                <input type="button" id="btnCancel" class="cancelbutton" value="<?php echo __("Cancel"); ?>"/>
                <?php }?>
            </div>
    
    </form>
    </div>
<?php } ?>
</div>
    
<?php if ($emergencyContactPermissions->canRead()) { ?>
<?php if ((!$haveContacts) && (!$emergencyContactPermissions->canCreate())) { ?>
                                <div class="outerbox">
                                    <div class="mainHeading"><h2><?php echo __("Assigned Emergency Contacts"); ?></h2></div>
                                    <span style="width: 500px; padding-left: 8px; padding-top: 3px;">
                                        <?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></span>
                                     </div>
                
<?php } else { ?>
    
<div class="outerbox" id="listEmegrencyContact">
<form name="frmEmpDelEmgContacts" id="frmEmpDelEmgContacts" method="post" action="<?php echo url_for('pim/deleteEmergencyContacts?empNumber=' . $empNumber); ?>">
<?php echo $deleteForm['_csrf_token']->render(); ?>
<?php echo $deleteForm['empNumber']->render(); ?>

    <div class="mainHeading"><h2><?php echo __("Assigned Emergency Contacts"); ?></h2></div>

    <div class="actionbar" id="listActions">
            <div class="actionbuttons">
            <?php if ($emergencyContactPermissions->canCreate()) { ?>

                    <input type="button" class="addbutton" id="btnAddContact" onmouseover="moverButton(this);" onmouseout="moutButton(this);" value="<?php echo __("Add"); ?>" title="<?php echo __("Add"); ?>"/>
            <?php } ?>
            <?php if ($emergencyContactPermissions->canDelete()) {
 ?>

                <input type="button" class="delbutton" id="delContactsBtn" onmouseover="moverButton(this);" onmouseout="moutButton(this);" value="<?php echo __("Delete"); ?>" title="<?php echo __("Delete"); ?>"/>
            <?php } ?>
        </div>
    </div>

    <table width="550" cellspacing="0" cellpadding="0" class="data-table" id="emgcontact_list">
        <thead>
            <tr>
                <?php if ($emergencyContactPermissions->canDelete()) { ?>
                <td class="check"><input type='checkbox' id='checkAll' class="checkbox" /></td>
                <?php }?>
                <td class="emgContactName"><?php echo __("Name"); ?></td>
                <td><?php echo __("Relationship"); ?></td>
                <td><?php echo __("Home Telephone"); ?></td>
                <td><?php echo __("Mobile"); ?></td>
                <td><?php echo __("Work Telephone"); ?></td>
            </tr>
        </thead>
        <tbody>
    <?php
            $row = 0;
            foreach ($emergencyContacts as $contact) {
                $cssClass = ($row % 2) ? 'even' : 'odd';
                echo '<tr class="' . $cssClass . '">';
                if ($emergencyContactPermissions->canDelete()) {
                    echo "<td class='check'><input type='checkbox' class='checkbox' name='chkecontactdel[]' value='" . $contact->seqno . "'/></td>";
                }
?>
        <td class="emgContactName" valign="top">
            <?php if ($emergencyContactPermissions->canUpdate()) { ?>
            <a href="#"><?php echo $contact->name; ?></a>
            <?php } else {
                    echo $contact->name; 
                }
                ?>
        </td>
        <input type='hidden' class='check' name='chkdependentUP[]' value="<?php echo $contact->seqno; ?>"/>
            <input type="hidden" id="relationship_<?php echo  $contact->seqno;?>" value="<?php echo $contact->relationship;?>" />
            <input type="hidden" id="homePhone_<?php echo  $contact->seqno;?>" value="<?php echo $contact->home_phone;?>" />
            <input type="hidden" id="mobilePhone_<?php echo  $contact->seqno;?>" value="<?php echo $contact->mobile_phone;?>" />
            <input type="hidden" id="workPhone_<?php echo  $contact->seqno;?>" value="<?php echo $contact->office_phone;?>" />
            <?php
                echo "<td valigh='top'>" . $contact->relationship . "</td>";
                echo "<td valigh='top'>" . $contact->home_phone . '</td>';
                echo "<td valigh='top'>" . $contact->mobile_phone . '</td>';
                echo "<td valigh='top'>" . $contact->office_phone . '</td>';
                echo '</tr>';
                $row++;
            } ?>
            </tbody>
        </table>
    </form>
</div>
<?php }
 }?>
 <?php if((($haveContacts && $emergencyContactPermissions->canUpdate()) || $emergencyContactPermissions->canCreate())) {?>
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
<?php }?>
<?php echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => CustomField::SCREEN_EMERGENCY_CONTACTS));?>
<?php echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => EmployeeAttachment::SCREEN_EMERGENCY_CONTACTS));?>
    
</div>
            </td>
            <!-- To be moved to layout file -->
            <td valign="top" style="text-align:left;">
            </td>
    </tr>
</table>
<script type="text/javascript">
    //<![CDATA[

    // Move to separate js after completing initial work
    
    function clearAddForm() {
        $('#emgcontacts_seqNo').val('');
        $('#emgcontacts_name').val('');
        $('#emgcontacts_relationship').val('');
        $('#emgcontacts_homePhone').val('');
        $('#emgcontacts_mobilePhone').val('');
        $('#emgcontacts_workPhone').val('');
        $('div#addPaneEmgContact label.error').hide();
        $('div#messagebar').hide();
    }

    function addEditLinks() {
        // called here to avoid double adding links - When in edit mode and cancel is pressed.
        removeEditLinks();
        $('#emgcontact_list tbody td.emgContactName').wrapInner('<a href="#"/>');
    }

    function removeEditLinks() {
        $('#emgcontact_list tbody td.emgContactName a').each(function(index) {
            $(this).parent().text($(this).text());
        });
    }

    $(document).ready(function() {
        
        <?php if (!$haveContacts) {  ?>
            $("#listEmegrencyContact").hide();
        <?php }else{?>
            $(".paddingLeftRequired").hide();
        <?php }?>
        
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
        // Edit a emergency contact in the list
        $('#frmEmpDelEmgContacts a').live('click', function() {

            var row = $(this).closest("tr");
            var seqNo = row.find('input.check:first').val();
            var name = $(this).text();
            var relationship = $("#relationship_" + seqNo).val();
            var homePhone = $("#homePhone_" + seqNo).val();
            var mobilePhone = $("#mobilePhone_" + seqNo).val();
            var workPhone = $("#workPhone_" + seqNo).val();
            
            $('#emgcontacts_seqNo').val(seqNo);
            $('#emgcontacts_name').val(name);
            $('#emgcontacts_relationship').val(relationship);
            $('#emgcontacts_homePhone').val(homePhone);
            $('#emgcontacts_mobilePhone').val(mobilePhone);
            $('#emgcontacts_workPhone').val(workPhone);

            $(".paddingLeftRequired").show();
            $("#emergencyContactHeading").text("<?php echo __("Edit Emergency Contact");?>");
            $('div#messagebar').hide();
            // hide validation error messages

            $('#listActions').hide();
            $('#emgcontact_list td.check').hide();
            $('#addPaneEmgContact').css('display', 'block');

        });

        // Cancel in add pane
        $('#btnCancel').click(function() {
            clearAddForm();
            $('#addPaneEmgContact').css('display', 'none');
            $('#listActions').show();
            $('#emgcontact_list td.check').show();
            <?php  if ($emergencyContactPermissions->canUpdate()){?>
            addEditLinks();
            <?php }?>
            $('div#messagebar').hide();
            $(".paddingLeftRequired").hide();
        });

        // Add a emergency contact
        $('#btnAddContact').click(function() {
            $("#emergencyContactHeading").text("<?php echo __("Add Emergency Contact");?>");
            $(".paddingLeftRequired").show();
            clearAddForm();

            // Hide list action buttons and checkbox
            $('#listActions').hide();
            $('#emgcontact_list td.check').hide();
            removeEditLinks();
            $('div#messagebar').hide();

            //
            //            // hide validation error messages
            //            $("label.errortd[generated='true']").css('display', 'none');
            $('#addPaneEmgContact').css('display', 'block');
        });

        /* Valid Contact Phone */
        $.validator.addMethod("validContactPhone", function(value, element) {
            if ( $('#emgcontacts_homePhone').val() == '' && $('#emgcontacts_mobilePhone').val() == '' &&
                    $('#emgcontacts_workPhone').val() == '' )
                return false;
            else
                return true
        });
        
        $("#frmEmpEmgContact").validate({

            rules: {
                'emgcontacts[name]' : {required:true, maxlength:100},
                'emgcontacts[relationship]' : {required: true, maxlength:100},
                'emgcontacts[homePhone]' : {phone: true, validContactPhone:true, maxlength:100},
                'emgcontacts[mobilePhone]' : {phone: true, maxlength:100},
                'emgcontacts[workPhone]' : {phone: true, maxlength:100}
            },
            messages: {
                'emgcontacts[name]': {
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                    maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS,array('%amount%' => 100)); ?>'
                },
                'emgcontacts[relationship]': {
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                    maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS,array('%amount%' => 100)); ?>'
                },
                'emgcontacts[homePhone]' : {
                    phone:'<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>',
                    validContactPhone:'<?php echo __('At least one phone number is required'); ?>',
                    maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS,array('%amount%' => 100)); ?>'
                },
                'emgcontacts[mobilePhone]' : {
                	phone:'<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>',
                	maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS,array('%amount%' => 100)); ?>'

                },
                'emgcontacts[workPhone]' : {
                	phone:'<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>',
                	maxlength: '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS,array('%amount%' => 100)); ?>'
                }
            },
            errorPlacement: function(error, element) {
                    error.appendTo( element.prev('label') );
                }


        });

        
        $('#delContactsBtn').click(function() {
            var checked = $('#frmEmpDelEmgContacts input:checked').length;

            if (checked == 0) {
                $("#messagebar").attr("class", "messageBalloon_notice");
                $("#messagebar").text("<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>");
            } else {
                $('#frmEmpDelEmgContacts').submit();
            }
        });

        $('#btnSaveEContact').click(function() {
            $('#frmEmpEmgContact').submit();
        });
});
//]]>
</script>

