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

<?php $browser = $_SERVER['HTTP_USER_AGENT']; ?>
<?php if (strstr($browser, "MSIE 8.0")): ?>
    <link href="<?php echo public_path('../../themes/orange/IE_style.css') ?>" rel="stylesheet" type="text/css"/>
<?php else: ?>
<?php echo stylesheet_tag('../orangehrmPimPlugin/css/viewReportToDetailsSuccess'); ?>
<?php endif; ?>
<?php
        use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
        use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
        use_javascript('../orangehrmPimPlugin/js/viewReportToDetailsSuccess');

        $numSupDetails = count($supDetails);
        $hasSupDetails = $numSupDetails > 0;
        $numSubDetails = count($subDetails);
        $hasSubDetails = $numSubDetails > 0;
        $allowDel = true;
        $allowEdit = true;
?>
    <style type="text/css">
        table.data-table tbody tr td {
            padding: 1px 0px;
        }
    </style>

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
        <td valign="top" width="1000">
            <div class="formpage2col" style="width: 1000px">
                <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
                    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
                </div>
                <?php if (!$essUserMode): 
                    if ($reportToPermissions->canRead()) { ?>
               
                    <div id="addPaneReportTo" style="width: 600px" style="display:none;">
                       
                        <div class="outerbox">
                        <?php if ($reportToPermissions->canRead() && (($reportToPermissions->canCreate()) || ($hasSupDetails && 
                                $reportToSupervisorPermission->canUpdate()) || ($hasSubDetails && $reportToSubordinatePermission->canUpdate()))) { ?>
                            <div class="mainHeading"><h2 id="reportToHeading"><?php echo __('Add Supervisor/Subordinate'); ?></h2></div>
                             
                            <form name="frmAddReportTo" id="frmAddReportTo" method="post" action="<?php echo url_for('pim/updateReportToDetail?empNumber=' . $empNumber); ?>">

                            <?php echo $form['_csrf_token']; ?>
                            <?php echo $form["empNumber"]->render(); ?>
                            <?php echo $form["previousRecord"]->render(); ?>
                            <div id="reportTo" class="reportTo">
                                <?php echo $form['type_flag']->render(); ?>
                                <br class="clear" />

                                <?php echo $form['supervisorName']->renderLabel(__('Name') . ' <span class="required">*</span>'); ?>
                                <?php echo $form['supervisorName']->render(array("class" => "txtBoxR name", "maxlength" => 92)); ?>
                                <?php echo $form['subordinateName']->renderLabel(__('Name') . ' <span class="required">*</span>'); ?>
                                <?php echo $form['subordinateName']->render(array("class" => "txtBoxR name", "maxlength" => 92)); ?>
                                <div id="name">
                                </div>
                                <br class="clear"/>

                                <?php echo $form['reportingMethodType']->renderLabel(__('Reporting Method') . ' <span class="required">*</span>'); ?>
                                <?php echo $form['reportingMethodType']->render(array("class" => "drpDownR", "maxlength" => 50)); ?>
                                <br class="clear"/>

                                <div id="pleaseSpecify">
                                    <?php echo $form['reportingMethod']->renderLabel(__('Please Specify') . ' <span class="required">*</span>'); ?>
                                    <?php echo $form['reportingMethod']->render(array("class" => "txtBoxR", "maxlength" => 50)); ?>
                                    <br class="clear"/>
                                </div>
                            </div>
                            <?php if (($reportToPermissions->canUpdate()) || $reportToPermissions->canCreate()) { ?>
                                        <div class="formbuttons">
                                            <input type="button" class="savebutton" name="btnSaveReportTo" id="btnSaveReportTo"
                                                   value="<?php echo __("Save"); ?>"
                                                   title="<?php echo __("Save"); ?>"
                                                   onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                                            <input type="button" id="btnCancel" class="cancelbutton" value="<?php echo __("Cancel"); ?>"/>
                                        </div>
                            <?php } ?>
                                </form>
                             <?php 
                             
                             } else { ?>
                            <div class="mainHeading"><h2 id="reportToHeading"><?php echo __('Assigned Supervisors/Subordinates'); ?></h2></div>
                            <span style="padding-top: 8px; padding-bottom: 5px;">&nbsp;&nbsp;<?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?> </span>
                            <?php } ?>
                            </div>
                   <?php if ((($hasSupDetails && $reportToSupervisorPermission->canUpdate()) || $reportToSupervisorPermission->canCreate()) || (($hasSubDetails && $reportToSubordinatePermission->canUpdate()) || $reportToSubordinatePermission->canCreate())) {?>
                  <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
                  <?php }?>
                        </div>
                <?php } 
                endif; ?>

                                    <div id="listReportToDetails">
                                        <table width="1000" cellspacing="0" cellpadding="0" class="data-table" id="report_list_table">
                                            <tr>
                                                
                                            <td valign="top">
                                                <?php if ($reportToSupervisorPermission->canRead()) { ?>
                                                <div class="outerbox" id="listReportToSupDetails" >
                                                    <form name="frmEmpDelSupervisors" id="frmEmpDelSupervisors" method="post" action="<?php echo url_for('pim/deleteReportToSupervisor?empNumber=' . $empNumber); ?>">
                                    <?php echo $deleteSupForm['_csrf_token']->render(); ?>
                                    <?php echo $deleteSupForm['empNumber']->render(); ?>

                                    <div class="mainHeading"><h2><?php echo __("Assigned Supervisors"); ?></h2></div>
                                    
                                    <?php if ($reportToSupervisorPermission->canCreate() || $reportToSupervisorPermission->canDelete()) { ?>
                                    <div class="actionbar" id="supListActions">
                                        <div class="supActionbuttons">
                                            <?php if ($reportToSupervisorPermission->canCreate() ) { ?>

                                                <input type="button" class="addbutton" id="btnAddSupervisorDetail" onmouseover="moverButton(this);" onmouseout="moutButton(this);" value="<?php echo __("Add"); ?>" title="<?php echo __("Add"); ?>"/>
                                            <?php } ?>
                                            <?php if ($reportToSupervisorPermission->canDelete()) {
                                            ?>

                                                <input type="button" class="delbutton" id="delSupBtn" onmouseover="moverButton(this);" onmouseout="moutButton(this);" value="<?php echo __("Delete"); ?>" title="<?php echo __("Delete"); ?>"/>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    
                                    <table  cellspacing="0" cellpadding="0" class="data-table" id="sup_list">
                                        <thead>
                                            <tr>
                                                <?php if ($reportToSupervisorPermission->canDelete()) { ?>
                                                <td class="check"><input type='checkbox' id='checkAllSup' class="checkboxSup" /></td>
                                                <?php }?>
                                                <td class="supName"><?php echo __("Name"); ?></td>
                                                <td class="supReportMethod"><?php echo __("Reporting Method"); ?></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $supRow = 0;
                                            foreach ($supDetails as $sup) {
                                                $cssClass = ($supRow % 2) ? 'even' : 'odd';
                                                echo '<tr class="' . $cssClass . '">';
                                                $supChkBoxValue = $sup->getSupervisorId() . " " . $empNumber . " " . $sup->getReportingMethodId();
                                                if ($reportToSupervisorPermission->canDelete()) {
                                                echo "<td class='check'><input type='checkbox' class='checkboxSup' name='chksupdel[]' value='" . $supChkBoxValue . "'/></td>";
                                                }else{
                                            ?>
                                            <input type='hidden' class='checkboxSup' name='chksupdel[]' value='<?php echo $supChkBoxValue; ?>'/>
                                            <?php 
                                                }
                                            $supervisor = $sup->getSupervisor();
                                            $terminationId = $supervisor->getTerminationId();
                                            $suffix = (!empty($terminationId)) ? " (".__('Past Employee').")" : "";
                                            $supName = $supervisor->getFirstName() . " " . $supervisor->getLastName() . $suffix; ?>
                                            <?php $supReportingMethodName = $sup->getReportingMethod()->getName(); ?>
                                            <?php if ($reportToSupervisorPermission->canUpdate()) { ?>
                                                <td class="supName" valign="top"><a href="#"><?php echo $supName; ?></a></td>
                                            <?php }else{?>
                                                <td class="supName" valign="top"><?php echo $supName; ?></td>
                                        <?php
                                            }
                                                echo "<td  class='supReportMethod' valigh='top'>" . __($supReportingMethodName) . "</td>";
                                                echo '</tr>';
                                                $supRow++;
                                            }
                                            if ($supRow == 0) { ?>
                                            <tr>
                                                <td colspan="3"><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                            </tr>
                                            <?php } ?>
                                            
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                                                <?php }?>
                                            </td>


                                            <td valign="top" width="5">
                                            </td>

                                            <td valign="top" >
                                                <?php if ($reportToSubordinatePermission->canRead()) { ?>
                                                <div class="outerbox" id="listReportToSubDetails">
                                                    <form name="frmEmpDelSubordinates" id="frmEmpDelSubordinates" method="post" action="<?php echo url_for('pim/deleteReportToSubordinate?empNumber=' . $empNumber); ?>">
                                                    <?php echo $deleteSubForm['_csrf_token']->render(); ?>
                                                    <?php echo $deleteSubForm['empNumber']->render(); ?>

                                                            <div class="mainHeading"><h2><?php echo __("Assigned Subordinates"); ?></h2></div>

                                                            <?php if ($reportToSubordinatePermission->canCreate() || $reportToSubordinatePermission->canDelete()) { ?>
                                                            <div class="actionbar" id="subListActions">
                                                                <div class="subActionbuttons">
                                                            <?php if ($reportToSubordinatePermission->canCreate() ) { ?>

                                                                <input type="button" class="addbutton" id="btnAddSubordinateDetail" onmouseover="moverButton(this);" onmouseout="moutButton(this);" value="<?php echo __("Add"); ?>" title="<?php echo __("Add"); ?>"/>
                                                            <?php } ?>
                                                            <?php if ($reportToSubordinatePermission->canDelete()) {
                                                            ?>

                                                                <input type="button" class="delbutton" id="delSubBtn" onmouseover="moverButton(this);" onmouseout="moutButton(this);" value="<?php echo __("Delete"); ?>" title="<?php echo __("Delete"); ?>"/>
                                                            <?php } ?>
                                                                </div>
                                                            </div>
                                                            <?php } ?>

                                                    <table  cellspacing="0" cellpadding="0" class="data-table" id="sub_list">
                                                        <thead>
                                                            <tr>
                                                                <?php if ($reportToSubordinatePermission->canDelete()) { ?>
                                                                <td class="check"><input type='checkbox' id='checkAllSub' class="checkboxSub" /></td>
                                                                <?php }?>
                                                                <td class="subName"><?php echo __("Name"); ?></td>
                                                                <td class="subReportMethod"><?php echo __("Reporting Method"); ?></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $subRow = 0;
                                                            foreach ($subDetails as $sub) {
                                                                $cssClass = ($subRow % 2) ? 'even' : 'odd';
                                                                echo '<tr class="' . $cssClass . '">';
                                                                $subChkBoxValue = $empNumber . " " . $sub->getSubordinateId() . " " . $sub->getReportingMethodId();
                                                                if ($reportToSubordinatePermission->canDelete()) {
                                                                echo "<td class='check'><input type='checkbox' class='checkboxSub' name='chksubdel[]' value='" . $subChkBoxValue . "'/></td>";
                                                                }else{
                                                            ?>
                                                            <input type='hidden' class='checkboxSub' name='chksubdel[]' value='<?php echo $subChkBoxValue; ?>'/>
                                                            <?php 
                                                                }
                                                            $subordinate = $sub->getSubordinate();
                                                            $terminationId = $subordinate->getTerminationId();
                                                            $suffix = (!empty($terminationId)) ? " (".__("Past Employee").")" : "";
                                                            $subName = $subordinate->getFirstName() . " " . $subordinate->getLastName() . $suffix; ?>
                                                            <?php $subReportingMethodName = $sub->getReportingMethod()->getName(); ?>
                                                            <?php if ($reportToSubordinatePermission->canUpdate()) { ?>
                                                            <td class="subName" valign="top"><a href="#"><?php echo $subName; ?></a></td>
                                                        <?php }else{?>
                                                                <td class="subName" valign="top"><?php echo $subName; ?></td>
                                                        <?php
                                                            }
                                                                echo "<td  class='subReportMethod' valigh='top'>" . __($subReportingMethodName) . "</td>";
                                                                echo '</tr>';
                                                                $subRow++;
                                                            }
                                                            if ($subRow == 0) { ?>
                                                            <tr>
                                                                <td colspan="3"><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                                            </tr>
                                                            <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </form>
                                                </div>
                                                <?php }?>
                                            </td>
                                            </tr>
                        </table>
                    </div>


                <?php echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => CustomField::SCREEN_REPORT_TO)); ?>
                <?php echo include_component('pim', 'attachments', array('empNumber' => $empNumber, 'screen' => EmployeeAttachment::SCREEN_REPORT_TO)); ?>

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
                                var typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
                                var addSupervisor = '<?php echo __("Add Supervisor"); ?>';
                                var addSubordinate = '<?php echo __("Add Subordinate"); ?>';
                                var deleteWarning = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
                                var editSupervisor = '<?php echo __("Edit Supervisor"); ?>';
                                var editSubordinate = '<?php echo __("Edit Subordinate"); ?>';
                                var nameIsRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                var reportingMethodIsRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                var reportingMethodTypeIsRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                var essMode = '<?php echo $essUserMode; ?>';
                                
                                var haveSupervisors = <?php echo $hasSupDetails ? 'true' : 'false';?>;
                                var haveSubordinates = <?php echo $hasSubDetails ? 'true' : 'false';?>;
                                var canUpdateSupervisors ='<?php echo $reportToSupervisorPermission->canUpdate();?>';
                                var canUpdateSubordinates = '<?php echo $reportToSubordinatePermission->canUpdate();?>';
                                var canCreateSupervisors = '<?php echo $reportToSupervisorPermission->canCreate(); ?>';
                                var canCreateSubordinates = '<?php echo $reportToSubordinatePermission->canCreate(); ?>';
                            //]]>
                            </script>