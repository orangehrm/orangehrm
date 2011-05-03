<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css')?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js')?>"></script>

<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js')?>

<script type="text/javascript">
    //<![CDATA[

    var fileModified = 0;
    var lang_addWorkExperience = "<?php echo __('Add Work Experience');?>";
    var lang_editWorkExperience = "<?php echo __('Edit Work Experience');?>";
    var lang_companyRequired = "<?php echo __("Company is required");?>";
    var lang_jobTitleRequired = "<?php echo __("Job Title is required");?>";
    var lang_invalidDate = "<?php echo __("Please enter a valid date in %format% format", array('%format%'=>$sf_user->getDateFormat())) ?>";
    var lang_commentLength = "<?php echo __('Comment length cannot exceed 200 characters');?>";
    var lang_fromDateLessToDate = "<?php echo __('From date should be before to date');?>";
    var lang_selectWrkExprToDelete = "<?php echo __('Select Work Experience From The List To Delete');?>";
    var lang_jobTitleMaxLength = "<?php echo __('Job Title length cannot exceed 120 characters');?>";
    var lang_companyMaxLength = "<?php echo __('Job Title length cannot exceed 100 characters');?>";

    var dateFormat  = '<?php echo $sf_user->getDateFormat();?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat());?>';
    var dateDisplayFormat = dateFormat.toUpperCase();
    //]]>
</script>

<?php echo stylesheet_tag('../orangehrmPimPlugin/css/viewQualificationsSuccess'); ?>
<!-- common table structure to be followed -->
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td width="5">&nbsp;</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top">
            <?php include_partial('leftmenu', array('empNumber' => $empNumber, 'form' => $workExperienceForm));?></td>
        <td valign="top">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td valign="top">
                        <!-- this space is for contents -->
                        <div id="mainDiv">
                            <div class="outerbox">
                                <div class="mainHeading"><h2><?php echo __('Qualifications'); ?></h2></div>

                                <!-- this is work experience section -->
                                <div id="workExpMessagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 630px;">
                                    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
                                </div>
                                
                                <div class="sectionDiv" id="sectionWorkExperience">
                                    <div><h3><?php echo __('Work Experience'); ?></h3></div>

                                    <div class="outerbox" id="changeWorkExperience" style="width:500px;">
                                        <div class="mainHeading"><h4 id="headChangeWorkExperience"><?php echo __('Add Work Experience'); ?></h4></div>
                                        <form id="frmWorkExperience" action="<?php echo url_for('pim/saveDeleteWorkExperience?empNumber=' . $empNumber . "&option=save"); ?>" method="post">

                                            <?php echo $workExperienceForm['_csrf_token']; ?>
                                            <?php echo $workExperienceForm['emp_number']->render(); ?>
                                            <?php echo $workExperienceForm["seqno"]->render(); ?>

                                            <?php echo $workExperienceForm['employer']->renderLabel(__('Company') . ' <span class="required">*</span>'); ?>
                                            <?php echo $workExperienceForm['employer']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                                            <br class="clear"/>

                                            <?php echo $workExperienceForm['jobtitle']->renderLabel(__('Job Title') . ' <span class="required">*</span>'); ?>
                                            <?php echo $workExperienceForm['jobtitle']->render(array("class" => "formInputText", "maxlength" => 120)); ?>
                                            <br class="clear"/>

                                            <?php echo $workExperienceForm['from_date']->renderLabel(__('From')); ?>
                                            <?php echo $workExperienceForm['from_date']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
                                            <input id="fromDateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
                                            <br class="clear"/>

                                            <?php echo $workExperienceForm['to_date']->renderLabel(__('To')); ?>
                                            <?php echo $workExperienceForm['to_date']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
                                            <input id="toDateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
                                            <br class="clear"/>

                                            <?php echo $workExperienceForm['comments']->renderLabel(__('Comment')); ?>
                                            <?php echo $workExperienceForm['comments']->render(array("class" => "formInputText")); ?>
                                            <br class="clear"/>

                                            <div class="formbuttons">
                                                <input type="button" class="savebutton" id="btnWorkExpSave" value="<?php echo __("Save"); ?>" />
                                                <input type="button" class="savebutton" id="btnWorkExpCancel" value="<?php echo __("Cancel"); ?>" />
                                            </div>
                                        </form>
                                    </div>
                                    <div class="smallText" id="workExpRequiredNote"><?php echo __('Fields marked with an asterisk')?> <span class="required">*</span> <?php echo __('are required.')?></div>
                                    <br />
                                    <div id="actionWorkExperience">
                                        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addWorkExperience" />&nbsp;
                                        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delWorkExperience" />
                                    </div>

                                    <form id="frmDelWorkExperience" action="<?php echo url_for('pim/saveDeleteWorkExperience?empNumber=' . $empNumber . "&option=delete"); ?>" method="post">
                                        <div class="outerbox" id="tblWorkExperience">
                                            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" border="0">
                                                <thead>
                                                    <tr>
                                                        <td class="check"><input type="checkbox" id="workCheckAll" /></td>
                                                        <td><?php echo __('Company');?></td>
                                                        <td><?php echo __('Job Title');?></td>
                                                        <td><?php echo __('From');?></td>
                                                        <td><?php echo __('To');?></td>
                                                        <td><?php echo __('Comment');?></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $workExperiences = $workExperienceForm->workExperiences;
                                                    $row = 0;
                                                    foreach ($workExperiences as $workExperience) {
                                                        $cssClass = ($row % 2) ? 'even' : 'odd';
                                                        //empty($workExperience->from_date)
                                                        $fromDate = ohrm_format_date($workExperience->from_date);
                                                        $toDate = ohrm_format_date($workExperience->to_date);
                                                        ?>
                                                    <tr class="<?php echo $cssClass;?>">
                                                <td class="check"><input type="hidden" id="employer_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->employer); ?>" />
                                                <input type="hidden" id="jobtitle_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->jobtitle); ?>" />
                                                <input type="hidden" id="fromDate_<?php echo $workExperience->seqno;?>" value="<?php echo $fromDate; ?>" />
                                                <input type="hidden" id="toDate_<?php echo $workExperience->seqno;?>" value="<?php echo $toDate; ?>" />
                                                <input type="hidden" id="comment_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->comments); ?>" />

                                                <input type="checkbox" class="chkbox1" value="<?php echo $workExperience->seqno;?>" name="delWorkExp[]"/></td>
                                                <td><a href="javascript: fillDataToWorkExperienceDataPane(<?php echo $workExperience->seqno;?>);"><?php echo htmlspecialchars($workExperience->employer);?></a></td>
                                                <td><?php echo htmlspecialchars($workExperience->jobtitle);?></td>
                                                <td><?php echo $fromDate;?></td>
                                                <td><?php echo $toDate;?></td>
                                                <td class="comments"><?php echo htmlspecialchars($workExperience->comments);?></td>
                                                </tr>
                                                    <?php $row++;
                                                }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>

                                </div>
                                <!-- this is education section -->
                                <?php include_partial('education',
                                        array('empNumber'=>$empNumber, 'form'=>$educationForm));?>

                                <!-- this is skills section -->
                                <?php include_partial('skill',
                                        array('empNumber'=>$empNumber, 'form'=>$skillForm));?>
                                
                                <!-- this is Languages section -->
                                <?php include_partial('language',
                                        array('empNumber'=>$empNumber, 'form'=>$languageForm));?>

                                <!-- this is Licenses section -->
                                <?php include_partial('license',
                                        array('empNumber'=>$empNumber, 'form'=>$licenseForm));?>
                                <br />
                            </div>
                        <?php echo include_component('pim', 'customFields', array('empNumber'=>$empNumber, 'screen' => 'qualifications'));?>
                        <?php echo include_component('pim', 'attachments', array('empNumber'=>$empNumber, 'screen' => 'qualifications'));?>
                            
                        </div>
                    </td>
                    <td valign="top" align="left">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php echo javascript_include_tag('../orangehrmPimPlugin/js/viewQualificationsSuccess'); ?>