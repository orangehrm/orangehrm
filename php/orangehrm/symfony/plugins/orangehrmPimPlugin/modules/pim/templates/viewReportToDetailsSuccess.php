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

<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
use_stylesheet('../orangehrmPimPlugin/css/viewReportToDetailsSuccess');
use_javascript('../orangehrmPimPlugin/js/viewReportToDetailsSuccess');

//$numMemDetails = count($membershipDetails);
//$hasMemDetails = $numMemDetails > 0;
$allowDel = true;
$allowEdit = true;
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
        <td valign="top" width="1200">
            <div class="formpage2col">
                <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
                    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
                </div>

                <div id="addPaneReportTo">
                    <div class="outerbox">

                        <div class="mainHeading"><h2 id="reportToHeading"><?php echo __('Add Supervisor/Subordinate'); ?></h2></div>
                        <form name="frmAddReportTo" id="frmAddReportTo" method="post" action="<?php echo url_for('pim/updateReportToDetail?empNumber=' . $empNumber); ?>">

                            <?php echo $form['_csrf_token']; ?>
                            <?php echo $form["empNumber"]->render(); ?>
                            <?php echo $form["selectedEmployee"]->render(); ?>
                        <div>
                            <?php echo $form['type_flag']->render(); ?>
                            <br class="clear" />

                            <?php echo $form['name']->renderLabel(__('Name'). ' <span class="required">*</span>'); ?>
                            <?php echo $form['name']->render(array("class" => "txtBox", "maxlength" => 50)); ?>
                            <br class="clear"/>

                            <?php echo $form['reportingModeType']->renderLabel(__('Reporting Method'). ' <span class="required">*</span>'); ?>
                            <?php echo $form['reportingModeType']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                            <br class="clear"/>

                            <div id="pleaseSpecify">
                                <?php echo $form['reportingMethod']->renderLabel(__('Please Specify'). ' <span class="required">*</span>'); ?>
                                <?php echo $form['reportingMethod']->render(array("class" => "txtBox", "maxlength" => 50)); ?>
                                <br class="clear"/>
                            </div>
                         </div>
                            <?php if ($allowEdit) {
                            ?>
                                <div class="formbuttons">
                                    <input type="button" class="savebutton" name="btnSaveReportTo" id="btnSaveReportTo"
                                           value="<?php echo __("Save"); ?>"
                                           title="<?php echo __("Save"); ?>"
                                           onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                                    <input type="button" id="btnCancel" class="cancelbutton" value="<?php echo __("Cancel"); ?>"/>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>

               
                        <div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>
                        <?php echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => 'membership')); ?>
                        <?php echo include_component('pim', 'attachments', array('empNumber' => $empNumber, 'screen' => 'membership')); ?>
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
    var typeForHints = '<?php echo __("Type for hints") . "...";?>';
    var employees = <?php echo str_replace('&#039;',"'",$form->getEmployeeListAsJson())?> ;
   //]]>
</script>