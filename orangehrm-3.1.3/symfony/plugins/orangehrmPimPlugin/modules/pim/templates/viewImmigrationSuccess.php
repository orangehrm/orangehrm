<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>
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
    var recordsAsJSON = new Array();
    //]]>
</script>
<?php echo javascript_include_tag(plugin_web_path('orangehrmPimPlugin', 'js/viewImmigrationSuccess')); ?>

<div class="box pimPane">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
    
    <?php if ($immigrationPermission->canCreate() || ($havePassports && $immigrationPermission->canUpdate())) { ?>
    <div id="immigrationDataPane" class=""> 
        <div class="head">
            <h1 id="immigrationHeading"><?php echo __('Add Immigration'); ?></h1>
        </div>
        
        <div class="inner">
            <form name="frmEmpImmigration" id="frmEmpImmigration" method="post" action="<?php echo url_for('pim/viewImmigration'); ?>">
                <?php echo $form['_csrf_token']; ?>
                <?php echo $form['emp_number']->render(); ?>
                <?php echo $form['seqno']->render(); ?>
                <fieldset>
                    <ol>
                        <li class="radio">
                            <?php echo $form['type_flag']->renderLabel(__('Document') . ' <em>*</em>'); ?>
                            <?php echo $form['type_flag']->render(); ?>
                        </li>
                        <li>
                            <?php echo $form['number']->renderLabel(__('Number') . ' <em>*</em>'); ?>
                            <?php echo $form['number']->render(array("class" => "formInputText", "maxlength" => 30)); ?>
                        </li>
                        <li>
                            <?php echo $form['passport_issue_date']->renderLabel(__('Issued Date')); ?>
                            <?php echo $form['passport_issue_date']->render(array("class" => "formInputText")); ?>
                        </li>
                        <li>
                            <?php echo $form['passport_expire_date']->renderLabel(__('Expiry Date')); ?>
                            <?php echo $form['passport_expire_date']->render(array("class" => "formInputText")); ?>
                        </li>
                        <li>
                            <?php echo $form['i9_status']->renderLabel(__('Eligible Status')); ?>
                            <?php echo $form['i9_status']->render(array("class" => "formInputText", "maxlength" => 30)); ?>    
                        </li>
                        <li>
                            <?php echo $form['country']->renderLabel(__('Issued By')); ?>
                            <?php echo $form['country']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li>
                            <?php echo $form['i9_review_date']->renderLabel(__('Eligible Review Date')); ?>
                            <?php echo $form['i9_review_date']->render(array("class" => "formInputText")); ?>
                        </li>
                        <li class="largeTextBox">
                            <?php echo $form['comments']->renderLabel(__('Comments')); ?>
                            <?php echo $form['comments']->render(array("class" => "formInputText")); ?>
                        </li>
                        <li class="required">
                            <em>* </em><?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                    </ol>
                    <p>
                        <input type="button" class="" id="btnSave" value="<?php echo __("Save"); ?>" />
                        <input type="button" class="reset" id="btnCancel" value="<?php echo __("Cancel"); ?>" />
                    </p>
                </fieldset>
            </form>
        </div>
    </div> <!-- immigrationDataPane -->
    <?php } ?>
    
    <div class="miniList" id="immidrationList">
        <div class="head">
            <h1><?php echo __("Assigned Immigration Records"); ?></h1>
        </div>
        
        <div class="inner">
            <?php if ($immigrationPermission->canRead()) : ?>
            
            <?php include_partial('global/flash_messages', array('prefix' => 'immigration')); ?>
            
            <form name="frmImmigrationDelete" id="frmImmigrationDelete" method="post" action="<?php echo url_for('pim/deleteImmigration?empNumber=' . $empNumber); ?>">
                <?php echo $listForm ?>
                <p id="listActions">
                    <?php if ($immigrationPermission->canCreate()) { ?>
                    <input type="button" class="" id="btnAdd" value="<?php echo __("Add"); ?>" />
                    <?php } ?>
                    <?php if ($immigrationPermission->canDelete()) { ?>
                    <input type="button" class="delete" id="btnDelete" value="<?php echo __("Delete"); ?>" />
                    <?php } ?>
                </p>
                <table id="" class="table hover">
                    <thead>
                        <tr>
                            <?php if ($immigrationPermission->canDelete()) { ?>
                            <th class="check" style="width:2%"><input type="checkbox" id="immigrationCheckAll" class="checkbox"/></th>
                            <?php } ?>
                            <th><?php echo __('Document'); ?></th>
                            <th><?php echo __('Number'); ?></th>
                            <th><?php echo __('Issued By'); ?></th>
                            <th><?php echo __('Issued Date'); ?></th>
                            <th><?php echo __('Expiry Date'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!$havePassports) { ?>
                        <tr>
                            <?php if ($immigrationPermission->canDelete()) { ?>
                            <td class="check"></td>
                            <?php } ?>
                            <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php } else { ?>                        
                        <?php
                        $immigrationRecords = $form->empPassports;
                        $countries = $form->countries;
                        $row = 0;
                        foreach ($immigrationRecords as $record) :
                            $cssClass = ($row % 2) ? 'even' : 'odd';
                            ?>
                            <tr class="<?php echo $cssClass; ?>">
                            <!-- we make data available in hidden fields -->
                                <input type="hidden" id="type_flag_<?php echo $record->recordId; ?>" value="<?php echo $record->type; ?>" />
                                <input type="hidden" id="number_<?php echo $record->recordId; ?>" value="<?php echo htmlentities($record->number); ?>" />
                                <?php
                                $issuedDate = set_datepicker_date_format($record->issuedDate);
                                $expiryDate = set_datepicker_date_format($record->expiryDate);
                                $reviewDate = set_datepicker_date_format($record->reviewDate);
                                
                                $serializableRecord = new stdClass();
                                $serializableRecord->recordId = $record->recordId;
                                $serializableRecord->type = $record->type;
                                $serializableRecord->number = $record->number;
                                $serializableRecord->issuedDate = $issuedDate;
                                $serializableRecord->expiryDate = $expiryDate;
                                $serializableRecord->reviewDate = $reviewDate;
                                $serializableRecord->status = $record->status;
                                $serializableRecord->countryCode = $record->countryCode;
                                $serializableRecord->notes = $record->notes;
                                
                                ?>
                                <script type="text/javascript">
                                    <?php echo "recordsAsJSON[{$record->recordId}] = " . json_encode($serializableRecord) . ';'; ?>
                                </script>
                                <input type="hidden" id="passport_issue_date_<?php echo $record->recordId; ?>" value="<?php echo $issuedDate; ?>" />
                                <input type="hidden" id="passport_expire_date_<?php echo $record->recordId; ?>" value="<?php echo $expiryDate; ?>" />
                                <input type="hidden" id="i9_status_<?php echo $record->recordId; ?>" value="<?php echo htmlentities($record->status); ?>" />
                                <input type="hidden" id="country_<?php echo $record->recordId; ?>" value="<?php echo $record->countryCode; ?>" />
                                <input type="hidden" id="i9_review_date_<?php echo $record->recordId; ?>" value="<?php echo $reviewDate; ?>" />
                                <input type="hidden" id="comments_<?php echo $record->recordId; ?>" value="<?php echo htmlentities($record->notes); ?>" />                                
                                <!-- end of all data hidden fields -->
                                <?php if ($immigrationPermission->canDelete()) { ?>
                                <td class="check"><input type='checkbox' class='checkbox' name='chkImmigration[]' value='<?php echo $record->recordId; ?>' /></td>
                                <?php } else { ?>
                                <input type='hidden' class='checkbox' name='chkImmigrationUP[]' value='<?php echo $record->recordId; ?>' />
                                <?php } ?>
                                <td class="document">
                                    <?php if ($immigrationPermission->canUpdate()) { ?>
                                    <a href="#"><?php echo ($record->type == EmployeeImmigrationRecord::TYPE_PASSPORT) ? __("Passport") : __("Visa"); ?></a>
                                    <?php } else { ?>
                                    <?php echo ($record->type == EmployeeImmigrationRecord::TYPE_PASSPORT) ? __("Passport") : __("Visa"); ?>
                                    <?php } ?>
                                </td>
                                <td><?php echo htmlentities($record->number); ?></td>
                                <td><?php echo empty($record->countryCode) ? '' : __($countries[$record->countryCode]); ?></td>
                                <td><?php echo $issuedDate; ?></td>
                                <td><?php echo $expiryDate; ?></td>
                            </tr>
                            <?php $row++;
                        endforeach; ?>
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
    echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => CustomField::SCREEN_IMMIGRATION));
    echo include_component('pim', 'attachments', array('empNumber' => $empNumber, 'screen' => EmployeeAttachment::SCREEN_IMMIGRATION)); 
    ?>
    
</div> <!-- Box -->
