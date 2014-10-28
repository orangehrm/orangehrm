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
use_javascript(plugin_web_path('orangehrmPimPlugin', 'js/viewMembershipsSuccess'));

$numMemDetails = count($membershipDetails);
$hasMemDetails = $numMemDetails > 0;
$allowDel = true;
$allowEdit = true;
?>
<?php if ($form->hasErrors()): ?>
    <span class="error">
        <?php
        echo $form->renderGlobalErrors();

        foreach ($form->getWidgetSchema()->getPositions() as $widgetName) {
            echo $form[$widgetName]->renderError();
        }
        ?>
    </span>
<?php endif; ?>

<div class="box pimPane">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
        
    <?php if ($membershipPermissions->canCreate() || ($hasMemDetails && $membershipPermissions->canUpdate())) { ?>
        <div id="addPaneMembership">
            <div class="head">
                <h1 id="membershipHeading"><?php echo __('Add Membership'); ?></h1>
            </div>
                
            <div class="inner">
                <form name="frmEmpMembership" id="frmEmpMembership" method="post" 
                      action="<?php echo url_for('pim/updateMembership?empNumber=' . $empNumber); ?>" class="longLabels">
                    
                    <?php echo $form['_csrf_token']; ?>
                    <?php echo $form["empNumber"]->render(); ?>
                    <fieldset>
                        <ol>
                            <li>
                                <?php echo $form['membership']->renderLabel(__('Membership') . ' <em>*</em>'); ?>
                                <?php echo $form['membership']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                            </li>
                            <li>
                                <?php echo $form['subscriptionPaidBy']->renderLabel(__('Subscription Paid By')); ?>
                                <?php echo $form['subscriptionPaidBy']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                            </li>
                            <li>
                                <?php echo $form['subscriptionAmount']->renderLabel(__('Subscription Amount')); ?>
                                <?php echo $form['subscriptionAmount']->render(array("class" => "formInputM", "maxlength" => 13)); ?>
                            </li>
                            <li>
                                <?php echo $form['currency']->renderLabel(__('Currency')); ?>
                                <?php echo $form['currency']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                            </li>
                            <li>
                                <?php echo $form['subscriptionCommenceDate']->renderLabel(__('Subscription Commence Date')); ?>
                                <?php echo $form['subscriptionCommenceDate']->render(array("class" => "formDateInput")); ?>
                            </li>
                            <li>
                                <?php echo $form['subscriptionRenewalDate']->renderLabel(__('Subscription Renewal Date')); ?>
                                <?php echo $form['subscriptionRenewalDate']->render(array("class" => "formDateInput")); ?>
                            </li>
                            <li class="required">
                                <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                            </li>
                        </ol>
                        <p>
                            <input type="button" class="" name="btnSaveMembership" id="btnSaveMembership" value="<?php echo __("Save"); ?>" />
                            <input type="button" id="btnCancel" class="reset" value="<?php echo __("Cancel"); ?>"/>
                        </p>
                    </fieldset>
                </form>
            </div>
        </div> <!-- addPaneMembership -->
    <?php } ?>
        
    <div class="miniList" id="listMembershipDetails">
        <div class="head">
            <h1><?php echo __("Assigned Memberships"); ?></h1>
        </div>
            
        <div class="inner">
            
            <?php include_partial('global/flash_messages', array('prefix' => 'memberships')); ?>
            
            <?php if ($membershipPermissions->canRead()) : ?>
                    
                <?php include_partial('global/flash_messages'); ?>
                <form name="frmEmpDelMemberships" id="frmEmpDelMemberships" method="post" 
                      action="<?php echo url_for('pim/deleteMemberships?empNumber=' . $empNumber); ?>">
                    <?php echo $deleteForm['_csrf_token']->render(); ?>
                    <?php echo $deleteForm['empNumber']->render(); ?>
                    <p id="listActions">
                        <?php if ($membershipPermissions->canCreate()) { ?>
                            <input type="button" class="" id="btnAddMembershipDetail" value="<?php echo __("Add"); ?>"/>
                            <?php
                            $savedMemship = '';
                            foreach ($membershipDetails as $memshipDetail) {
                                $savedMemship = $savedMemship . '' . $memshipDetail->getMembership()->getId() . '^';
                            }
                        } ?>
                        <?php if ($membershipPermissions->canDelete() && $hasMemDetails) { ?>
                            <input type="button" class="delete" id="delMemsBtn" value="<?php echo __("Delete"); ?>" />
                        <?php } ?>
                    </p>
                    <table id="" class="table hover">
                        <thead>
                            <tr>
                                <?php if ($membershipPermissions->canDelete() && $hasMemDetails) { ?>
                                <th class="check" style="width:2%"><input type='checkbox' id='checkAllMem' class="checkboxMem" /></th>
                                <?php } else {?>
                                <input type='hidden' class='checkboxMem' id='checkAllMem' />
                                <?php } ?>
                                <th class="memshipCode"><?php echo __("Membership"); ?></th>
                                <th><?php echo __("Subscription Paid By"); ?></th>
                                <th class="memshipAmount"><?php echo __("Subscription Amount"); ?></th>
                                <th><?php echo __("Currency"); ?></th>
                                <th><?php echo __("Subscription Commence Date"); ?></th>
                                <th><?php echo __("Subscription Renewal Date"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$hasMemDetails) { ?>
                                <tr>
                                    <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                    <td colspan="5"></td>
                                </tr>
                            <?php } else { ?>    
                                <?php
                                $row = 0;
                                foreach ($membershipDetails as $memship) :
                                    $cssClass = ($row % 2) ? 'even' : 'odd';
                                    echo '<tr class="' . $cssClass . '">';
                                    $chkBoxValue = $empNumber . " " . $memship->membershipId;
                                    ?>
                                    <?php if ($membershipPermissions->canDelete()) { ?>
                                    <td class='check'>
                                        <input type='checkbox' class='checkboxMem' name='chkmemdel[]' value='<?php echo $chkBoxValue ?>'/>
                                    </td>
                                    <?php } else { ?>
                                        <input type='hidden' class='checkboxMem' value="<?php echo $chkBoxValue; ?>"/>
                                    <?php } ?>
                                    <?php $newMembership = $memship->getMembership(); ?>
                                    <td class="memshipCode" valign="top"><?php if ($membershipPermissions->canUpdate()) { ?>
                                        <a href="#"><?php echo $newMembership->name; ?></a>
                                        <?php } else {
                                            echo $newMembership;
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    echo "<td class='memship' valigh='top'>" . $memship->subscriptionPaidBy . '</td>';
                                    echo "<td  class='memshipAmount1' valigh='top'>" . $memship->subscriptionFee . '</td>';
                                    echo "<td class='memship'valigh='top'>" . $memship->subscriptionCurrency . '</td>';
                                    echo "<td class='memship'valigh='top'>" . set_datepicker_date_format($memship->subscriptionCommenceDate) . '</td>';
                                    echo "<td class='memship'valigh='top'>" . set_datepicker_date_format($memship->subscriptionRenewalDate) . '</td>';
                                    echo '</tr>';
                                    $row++;
                                endforeach;
                            } 
                            ?>
                        </tbody>
                    </table>
                </form>
                    
            <?php else : ?>
                <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
            <?php endif; ?>
        </div>
    </div> <!-- miniList-listMembershipDetails -->
        
    <?php 
    echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => CustomField::SCREEN_MEMBERSHIP));
    echo include_component('pim', 'attachments', array('empNumber' => $empNumber, 'screen' => EmployeeAttachment::SCREEN_MEMBERSHIP)); 
    ?>
    
</div> <!-- Box -->

<script type="text/javascript">
    //<![CDATA[
    
    var savedMemships = '<?php echo substr($savedMemship, 0, -1); ?>'; //to remove last special character occurance 
    var canUpdate = '<?php echo $membershipPermissions->canUpdate(); ?>';
    var fileModified = 0;
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var deleteError = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
    var addMembershipDetail = '<?php echo __("Add Membership"); ?>';
    var editMembershipDetail = '<?php echo __("Edit Membership"); ?>';
    var getMembershipsUrl = "<?php echo url_for('pim/getMemberships') ?>";
    var selectAMembership = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var validNumberMsg = '<?php echo __("Should be a number"); ?>';
    var dateError = '<?php echo __("Renewal date should be after commence date"); ?>';
    var lang_negativeAmount = "<?php echo __("Should be a positive number"); ?>";
    var lang_tooLargeAmount = "<?php echo __("Should be less than %amount%", array("%amount%" => '1000,000,000.00')); ?>";
    //]]>
</script>