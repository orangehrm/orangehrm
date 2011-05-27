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
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>
<?php
use_stylesheet('../orangehrmPimPlugin/css/viewMembershipsSuccess');
use_javascript('../orangehrmPimPlugin/js/viewMembershipsSuccess');

$allowEdit = true;
?>

<table cellspacing="0" cellpadding="0" border="0" >
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top">
            <?php include_partial('leftmenu', array('empNumber' => $empNumber, 'form' => $form)); ?></td>
        <td valign="top">
            <div class="formpage2col">
                <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
                    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
                </div>

                <div id="addPaneMembership" >
                    <div class="outerbox">

                        <div class="mainHeading"><h2 id="membershipHeading"><?php echo __('Add Memberships'); ?></h2></div>
                        <form name="frmEmpEmgContact" id="frmEmpMembership" method="post" action="<?php echo url_for('pim/updateMembership?empNumber=' . $empNumber); ?>">

                            <?php echo $form['_csrf_token']; ?>
                            <?php echo $form["empNumber"]->render(); ?>

                            <?php echo $form['membershipType']->renderLabel(__('Membership Type')); ?>
                            <?php echo $form['membershipType']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                            <br class="clear"/>

                            <?php echo $form['membership']->renderLabel(__('Membership')); ?>
                            <?php echo $form['membership']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                            <br class="clear"/>

                            <?php echo $form['subscriptionPaidBy']->renderLabel(__('Subscription Paid By')); ?>
                            <?php echo $form['subscriptionPaidBy']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                            <br class="clear"/>

                            <?php echo $form['subscriptionAmount']->renderLabel(__('Subscription Amount')); ?>
                            <?php echo $form['subscriptionAmount']->render(array("class" => "txtBox", "maxlength" => 50)); ?>
                            <br class="clear"/>

                            <?php echo $form['currency']->renderLabel(__('Currency')); ?>
                            <?php echo $form['currency']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                            <br class="clear"/>

                            <?php echo $form['subscriptionCommenceDate']->renderLabel(__('Subscription Commence Date')); ?>
                            <?php echo $form['subscriptionCommenceDate']->render(array("class" => "formDateInput")); ?>
                            <input id="cDateBtn" type="button" name="" value="  " class="calendarBtn" />
                            <br class="clear"/>

                            <?php echo $form['subscriptionRenewalDate']->renderLabel(__('Subscription Renewal Date')); ?>
                            <?php echo $form['subscriptionRenewalDate']->render(array("class" => "formDateInput")); ?>
                            <input id="rDateBtn" type="button" name="" value="  " class="calendarBtn" />
                            <br class="clear"/>

                            <?php if ($allowEdit) { ?>
                                <div class="formbuttons">
                                    <input type="button" class="savebutton" name="btnSaveMembership" id="btnSaveMembership"
                                           value="<?php echo __("Save"); ?>"
                                           title="<?php echo __("Save"); ?>"
                                           onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                                    <input type="button" id="btnCancel" class="cancelbutton" value="<?php echo __("Cancel"); ?>"/>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
                <?php echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => 'membership'));?>
                <?php echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => 'membership'));?>
            </div>
        </td>
        <!-- To be moved to layout file -->
        <td valign="top" style="text-align:left;">
        </td>
    </tr>
</table>

<script type="text/javascript">
    //<![CDATA[

    var fileModified = 0;
    var dateFormat	= '<?php echo $sf_user->getDateFormat(); ?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat()); ?>';
    var dateDisplayFormat = dateFormat.toUpperCase();
    //]]>
</script>