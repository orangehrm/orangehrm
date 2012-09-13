<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>
<?php
$empPassportDetails;
$numContacts = count($empPassportDetails);
$havePassports = $numContacts>0;

?>
<script type="text/javascript">
    //<![CDATA[
    var lang_numberRequired = "<?php echo __(ValidationMessages::REQUIRED);?>";
    var lang_issuedGreaterExpiry = "<?php echo __('Expiry date should be after issued date'); ?>";
    var lang_editImmigrationHeading = "<?php echo __('Edit Immigration');?>";
    var lang_addImmigrationHeading = "<?php echo __('Add Immigration');?>";
    var lang_commentLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250));?>";
    var lang_deleteErrorMsg = "<?php echo __(TopLevelMessages::SELECT_RECORDS);?>";
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>'
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var fileModified = 0;
    var havePassports = '<?php echo $havePassports;?>';
    var canUpdate = '<?php echo $immigrationPermission->canUpdate();?>';

    //]]>
</script>

<?php echo stylesheet_tag('../orangehrmPimPlugin/css/viewImmigrationSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmPimPlugin/js/viewImmigrationSuccess'); ?>


<!-- common table structure to be followed -->
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top">
        <?php include_partial('leftmenu', array('empNumber' => $empNumber, 'form' => $form));?></td>
        <td valign="top">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td valign="top">
                        <!-- this space is for contents -->
                        <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 630px;">
                            <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
                        </div>
                        <div class="formpage2col">
                             <?php if ($immigrationPermission->canRead() && (($immigrationPermission->canCreate()) || ($immigrationPermission->canUpdate() && $havePassports))) { ?>
                            <div id="immigrationDataPane">
                                <div class="outerbox">
                                    <div class="mainHeading"><h2 id="immigrationHeading"><?php echo __('Add Immigration'); ?></h2></div>
                                    <form name="frmEmpImmigration" id="frmEmpImmigration" method="post" action="<?php echo url_for('pim/viewImmigration'); ?>">
                                        <?php echo $form['_csrf_token']; ?>
                                        <?php echo $form['emp_number']->render();?>
                                        <?php // if (($immigrationPermission->canRead() && $immigrationPermission->canCreate()) || ($immigrationPermission->canRead() && $immigrationPermission->canUpdate() && $havePassports)) { ?>
                                       <?php echo $form['seqno']->render();?>
                                        <div>
                                            <?php echo $form['type_flag']->renderLabel(__('Document') . ' <span class="required">*</span>'); ?>
                                            <?php echo $form['type_flag']->render(); ?>
                                            <br class="clear" />

                                            <?php echo $form['number']->renderLabel(__('Number') . ' <span class="required">*</span>'); ?>
                                            <?php echo $form['number']->render(array("class" => "formInputText", "maxlength" => 30)); ?>
                                            <br class="clear" />

                                            <?php echo $form['passport_issue_date']->renderLabel(__('Issued Date')); ?>
                                            <?php echo $form['passport_issue_date']->render(array("class" => "formInputText")); ?>
                                            <br class="clear" />

                                            <?php echo $form['passport_expire_date']->renderLabel(__('Expiry Date')); ?>
                                            <?php echo $form['passport_expire_date']->render(array("class" => "formInputText")); ?>
                                            <br class="clear" />

                                            <?php echo $form['i9_status']->renderLabel(__('Eligibility Status')); ?>
                                            <?php echo $form['i9_status']->render(array("class" => "formInputText", "maxlength" => 30)); ?>
                                            <br class="clear" />

                                            <?php echo $form['country']->renderLabel(__('Issued By')); ?>
                                            <?php echo $form['country']->render(array("class" => "formSelect")); ?>
                                            <br class="clear" />

                                            <?php echo $form['i9_review_date']->renderLabel(__('Eligibility Review Date')); ?>
                                            <?php echo $form['i9_review_date']->render(array("class" => "formInputText")); ?>
                                            <br class="clear" />

                                            <?php echo $form['comments']->renderLabel(__('Comments')); ?>
                                            <?php echo $form['comments']->render(array("class" => "formInputText")); ?>
                                            <br class="clear" />
                                            
                                        </div>
                                        <?php if (($havePassports&& $immigrationPermission->canUpdate()) || $immigrationPermission->canCreate()) { ?>
                                        <div class="formbuttons">
                                            <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Save"); ?>" />
                                            <?php if (($havePassports) || ($havePassports && $immigrationPermission->canCreate()) || ($havePassports && $immigrationPermission->canUpdate())) { ?>
                                            <input type="button" class="savebutton" id="btnCancel" value="<?php echo __("Cancel"); ?>" />
                                            <?php }?>
                                        </div>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>
                            <?php }?>
                            
                            <?php if ($immigrationPermission->canRead()) { ?>
                            <?php if ((!$havePassports) && (!$immigrationPermission->canCreate())) { ?>
                                <div class="outerbox">
                                    <div class="mainHeading"><h2><?php echo __("Assigned Immigration Documents"); ?></h2></div>
                                    <span style="width: 500px; padding-left: 8px; padding-top: 3px;">
                                        <?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></span>
                                     </div>
                
                        <?php } else { ?>
                            
                            <div class="outerbox" id="immidrationList">
                                <form name="frmImmigrationDelete" id="frmImmigrationDelete" method="post" action="<?php echo url_for('pim/deleteImmigration?empNumber=' . $empNumber); ?>">
                                    <div class="mainHeading"><h2><?php echo __("Assigned Immigration Documents"); ?></h2></div>

                                    <div class="actionbar" id="listActions">
                                        <div class="actionbuttons">
                                            <?php if ($immigrationPermission->canCreate() ) { ?>
                                            <input type="button" id="btnAdd" value="<?php echo __("Add");?>" class="addbutton" />
                                            <?php } ?>
                                            <?php if ($immigrationPermission->canDelete() ) { ?>
                                            <input type="button" id="btnDelete" value="<?php echo __("Delete");?>" class="delbutton" />
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <table width="550" cellspacing="0" cellpadding="0" class="data-table">
                                        <thead>
                                            <tr>
                                                <?php if ($immigrationPermission->canDelete()) { ?>
                                                <td class="check"><input type="checkbox" id="immigrationCheckAll" class="checkbox"/></td>
                                                <?php }?>
                                                <td><?php echo __('Document');?></td>
                                                <td><?php echo __('Document No');?></td>
                                                <td><?php echo __('Issued By');?></td>
                                                <td><?php echo __('Issued Date');?></td>
                                                <td><?php echo __('Date of Expiry');?></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $immigrationRecords = $form->empPassports;
                                            $countries = $form->countries;
                                            $row = 0;
                                            foreach ($immigrationRecords as $record) {
                                                $cssClass = ($row % 2) ? 'even' : 'odd'; ?>

                                            <tr class="<?php echo $cssClass;?>">
                                                <!-- we make data available in hidden fields -->
                                                <input type="hidden" id="type_flag_<?php echo $record->recordId;?>" value="<?php echo $record->type; ?>" />
                                                <input type="hidden" id="number_<?php echo $record->recordId;?>" value="<?php echo htmlentities($record->number); ?>" />
                                                <?php
                                                    $issuedDate = set_datepicker_date_format($record->issuedDate);
                                                    $expiryDate = set_datepicker_date_format($record->expiryDate);
                                                    $reviewDate = set_datepicker_date_format($record->reviewDate);
                                                ?>
                                                <input type="hidden" id="passport_issue_date_<?php echo $record->recordId;?>" value="<?php echo $issuedDate; ?>" />
                                                <input type="hidden" id="passport_expire_date_<?php echo $record->recordId;?>" value="<?php echo $expiryDate; ?>" />
                                                <input type="hidden" id="i9_status_<?php echo $record->recordId;?>" value="<?php echo htmlentities($record->status); ?>" />
                                                <input type="hidden" id="country_<?php echo $record->recordId;?>" value="<?php echo $record->countryCode; ?>" />
                                                <input type="hidden" id="i9_review_date_<?php echo $record->recordId;?>" value="<?php echo $reviewDate; ?>" />
                                                <input type="hidden" id="comments_<?php echo $record->recordId;?>" value="<?php echo htmlentities($record->notes); ?>" />

                                                <!-- end of all data hidden fields -->
                                                <?php if ($immigrationPermission->canDelete()) {?>
                                                <td class="check"><input type='checkbox' class='checkbox' name='chkImmigration[]' value='<?php echo $record->recordId;?>' /></td>
                                                <?php }else{
                                                    ?>
                                                <input type='hidden' class='checkbox' name='chkImmigrationUP[]' value='<?php echo $record->recordId;?>' />
                                                <?php
                                                    
                                                }
                                                    
                                                    ?>
                                                <td class="document">
                                                    <?php if ($immigrationPermission->canUpdate()) { ?>
                                                        <a href="#"><?php echo ($record->type == EmployeeImmigrationRecord::TYPE_PASSPORT)? __("Passport"):__("Visa");?></a>
                                                    <?php }else{?>
                                                        <?php echo ($record->type == EmployeeImmigrationRecord::TYPE_PASSPORT)? __("Passport"):__("Visa");?>
                                                    <?php }?>
                                                </td>
                                                <td><?php echo $record->number;?></td>
                                                <td><?php echo empty($record->countryCode)?'':__($countries[$record->countryCode]); ?></td>
                                                <td><?php echo $issuedDate;?></td>
                                                <td><?php echo $expiryDate;?></td>
                                            </tr>
                                            <?php $row++; } ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                            <?php }?>
                            <?php }?>
                            <?php if((($havePassports && $immigrationPermission->canUpdate()) || $immigrationPermission->canCreate())) {?>
                            <div class="paddingLeftRequired" <?php echo $havePassports ? 'style="display:none;"' : '';?>><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
                       <?php }?>
                        <?php echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => CustomField::SCREEN_IMMIGRATION));?>
                        <?php echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => EmployeeAttachment::SCREEN_IMMIGRATION));?>
                            
                        </div>
                    </td>
                    <td valign="top" align="left">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>