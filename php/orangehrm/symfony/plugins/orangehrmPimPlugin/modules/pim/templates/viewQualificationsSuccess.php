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
    var lang_companyRequired = "<?php echo __(ValidationMessages::REQUIRED);?>";
    var lang_jobTitleRequired = "<?php echo __(ValidationMessages::REQUIRED);?>";
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))); ?>';
    var lang_commentLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 200));?>";
    var lang_fromDateLessToDate = "<?php echo __('To date should be after From date');?>";
    var lang_selectWrkExprToDelete = "<?php echo __(TopLevelMessages::SELECT_RECORDS);?>";
    var lang_jobTitleMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100));?>";
    var lang_companyMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100));?>";

    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
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
                            <?php  
                            if (($section == '') && isset($message) && isset($messageType)) {
                                $tmpMsgClass = "messageBalloon_{$messageType}";
                                $tmpMsg = $message;
                            } else {
                                $tmpMsgClass = '';
                                $tmpMsg = '';
                            }
                            ?>
                            <div id="mainMessagebar" class="<?php echo $tmpMsgClass; ?>">
                                <span style="font-weight: bold;"><?php echo $tmpMsg; ?></span>
                            </div>                            
                            
                            <div class="outerbox">
                                <div class="mainHeading"><h2><?php echo __('Qualifications'); ?></h2></div>

                                <!-- this is work experience section -->
                                <a name="workexperience"></a>
                                <?php  
                                if (($section == 'workexperience') && isset($message) && isset($messageType)) {
                                    $tmpMsgClass = "messageBalloon_{$messageType}";
                                    $tmpMsg = $message;
                                } else {
                                    $tmpMsgClass = '';
                                    $tmpMsg = '';
                                }
                                ?>
                                <div id="workExpMessagebar" class="<?php echo $tmpMsgClass; ?>">
                                    <span style="font-weight: bold;"><?php echo $tmpMsg; ?></span>
                                </div>                                 
                                
                                <div class="sectionDiv" id="sectionWorkExperience">
                                    <div style="float: left; width: 450px;"><h3><?php echo __('Work Experience'); ?></h3></div>
                                    <div id="actionWorkExperience" style="float: left; margin-top: 20px; width: 335px; text-align: right">
                                        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addWorkExperience" />&nbsp;
                                        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delWorkExperience" />
                                    </div>
                                    
                                    <div class="outerbox" id="changeWorkExperience" style="width:500px; float: left">
                                        <div class="mainHeading"><h4 id="headChangeWorkExperience"><?php echo __('Add Work Experience'); ?></h4></div>
                                        <form id="frmWorkExperience" action="<?php echo url_for('pim/saveDeleteWorkExperience?empNumber=' . $empNumber . "&option=save"); ?>" method="post">

                                            <?php echo $workExperienceForm['_csrf_token']; ?>
                                            <?php echo $workExperienceForm['emp_number']->render(); ?>
                                            <?php echo $workExperienceForm["seqno"]->render(); ?>

                                            <?php echo $workExperienceForm['employer']->renderLabel(__('Company') . ' <span class="required">*</span>'); ?>
                                            <?php echo $workExperienceForm['employer']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                                            <br class="clear"/>

                                            <?php echo $workExperienceForm['jobtitle']->renderLabel(__('Job Title') . ' <span class="required">*</span>'); ?>
                                            <?php echo $workExperienceForm['jobtitle']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                                            <br class="clear"/>

                                            <?php echo $workExperienceForm['from_date']->renderLabel(__('From')); ?>
                                            <?php echo $workExperienceForm['from_date']->render(array("class" => "formInputText")); ?>
                                            <br class="clear"/>

                                            <?php echo $workExperienceForm['to_date']->renderLabel(__('To')); ?>
                                            <?php echo $workExperienceForm['to_date']->render(array("class" => "formInputText")); ?>
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
                                    <br class="clear" />

                                    <div class="paddingLeftRequired" id="workExpRequiredNote"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
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
                                                        $fromDate = set_datepicker_date_format($workExperience->from_date);
                                                        $toDate = set_datepicker_date_format($workExperience->to_date);
                                                        ?>
                                                    <tr class="<?php echo $cssClass;?>">
                                                <td class="check"><input type="hidden" id="employer_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->employer); ?>" />
                                                <input type="hidden" id="jobtitle_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->jobtitle); ?>" />
                                                <input type="hidden" id="fromDate_<?php echo $workExperience->seqno;?>" value="<?php echo $fromDate; ?>" />
                                                <input type="hidden" id="toDate_<?php echo $workExperience->seqno;?>" value="<?php echo $toDate; ?>" />
                                                <input type="hidden" id="comment_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->comments); ?>" />

                                                <input type="checkbox" class="chkbox1" value="<?php echo $workExperience->seqno;?>" name="delWorkExp[]"/></td>
                                                <td class="name"><a class="edit" href="#"><?php echo htmlspecialchars($workExperience->employer);?></a></td>
                                                <td><?php echo htmlspecialchars($workExperience->jobtitle);?></td>
                                                <td><?php echo $fromDate;?></td>
                                                <td><?php echo $toDate;?></td>
                                                <td class="comments"><?php echo htmlspecialchars($workExperience->comments);?></td>
                                                </tr>
                                                    <?php $row++;
                                                }
                                                
                                                if ($row == 0) { ?>
                                                <tr>
                                                    <td colspan="6">&nbsp;</td>
                                                </tr>
                                                <?php } ?>
                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>

                                </div>
                                <!-- this is education section -->
                                <br class="clear" />
                                <a name="education"></a>
                                <?php include_partial('education',
                                        array('empNumber'=>$empNumber, 'form'=>$educationForm,
                                              'message'=>$message, 'messageType'=>$messageType,
                                              'section'=>$section));?>

                                <!-- this is skills section -->
                                <br class="clear" />
                                <a name="skill"></a>
                                <?php include_partial('skill',
                                        array('empNumber'=>$empNumber, 'form'=>$skillForm,
                                              'message'=>$message, 'messageType'=>$messageType,
                                              'section'=>$section));?>
                                
                                <!-- this is Languages section -->
                                <br class="clear" />
                                <a name="language"></a>
                                <?php include_partial('language',
                                        array('empNumber'=>$empNumber, 'form'=>$languageForm,
                                              'message'=>$message, 'messageType'=>$messageType,
                                              'section'=>$section));?>

                                <!-- this is Licenses section -->
                                <br class="clear" />
                                <a name="license"></a>
                                <?php include_partial('license',
                                        array('empNumber'=>$empNumber, 'form'=>$licenseForm,
                                              'message'=>$message, 'messageType'=>$messageType,
                                              'section'=>$section));?>
                                
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