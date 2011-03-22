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
    var lang_commentLength = "<?php echo __('Comment length cannot exceed 250 characters');?>";

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
        <td colspan="2" height="30">&nbsp;<?php if($showBackButton) {?><input type="button" class="backbutton" value="<?php echo __("Back") ?>" onclick="navigateUrl('<?php echo public_path('../../lib/controllers/CentralController.php?reqcode=EMP&VIEW=MAIN&sortField=0&sortOrder0=ASC');?>');" /><?php }?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <!-- this space is reserved for menus - dont use -->
        <td width="200" valign="top"><?php include_partial('leftmenu', array('empNumber' => $empNumber));?></td>
        <td valign="top">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td valign="top">
                        <!-- this space is for contents -->
                        <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 630px;">
                            <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
                        </div>
                        <div id="mainDiv">
                            <div class="outerbox">
                                <div class="mainHeading"><h2><?php echo __('Qualifications'); ?></h2></div>

                                <!-- this is work experience section -->
                                <div class="sectionDiv" id="sectionWorkExperience">
                                    <div><h3><?php echo __('Work Experience'); ?></h3></div>

                                    <div class="outerbox" id="changeWorkExperience" style="width:500px;">
                                        <div class="mainHeading"><h2 id="headChangeWorkExperience"><?php echo __('Add Work Experience'); ?></h2></div>
                                        <form id="frmWorkExperience" action="<?php echo url_for('pim/saveDeleteWorkExperience?empNumber=' . $empNumber . "&option=save"); ?>" method="post">

                                            <?php echo $workExperienceForm['_csrf_token']; ?>
                                            <?php echo $workExperienceForm['emp_number']->render(); ?>
                                            <?php echo $workExperienceForm["seqno"]->render(); ?>

                                            <?php echo $workExperienceForm['employer']->renderLabel(__('Company') . ' <span class="required">*</span>'); ?>
                                            <?php echo $workExperienceForm['employer']->render(array("class" => "formInputText", "maxlength" => 30)); ?>
                                            <br class="clear"/>

                                            <?php echo $workExperienceForm['jobtitle']->renderLabel(__('Job Title') . ' <span class="required">*</span>'); ?>
                                            <?php echo $workExperienceForm['jobtitle']->render(array("class" => "formInputText", "maxlength" => 30)); ?>
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
                                    <br />
                                    <div id="actionWorkExperience">
                                        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addWorkExperience" />&nbsp;
                                        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delWorkExperience" />
                                        <br /><br />
                                    </div>

                                    <form id="frmDelWorkExperience" action="<?php echo url_for('pim/saveDeleteWorkExperience?empNumber=' . $empNumber . "&option=delete"); ?>" method="post">
                                        <div class="outerbox">
                                            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" id="tblWorkExperience" border="0">
                                                <thead>
                                                    <tr>
                                                        <td><input type="checkbox" id="workCheckAll" /></td>
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
                                                        $fromDate = ohrm_format_date(date("Y-m-d", strtotime($workExperience->from_date)));
                                                        $toDate = ohrm_format_date(date("Y-m-d", strtotime($workExperience->to_date)));
                                                        ?>
                                                    <tr class="<?php echo $cssClass;?>">
                                                <input type="hidden" id="employer_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->employer); ?>" />
                                                <input type="hidden" id="jobtitle_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->jobtitle); ?>" />
                                                <input type="hidden" id="fromDate_<?php echo $workExperience->seqno;?>" value="<?php echo $fromDate; ?>" />
                                                <input type="hidden" id="toDate_<?php echo $workExperience->seqno;?>" value="<?php echo $toDate; ?>" />
                                                <input type="hidden" id="comment_<?php echo $workExperience->seqno;?>" value="<?php echo htmlspecialchars($workExperience->comments); ?>" />

                                                <td><input type="checkbox" class="chkbox1" value="<?php echo $workExperience->seqno;?>" name="delWorkExp[]"/></td>
                                                <td><a href="javascript: fillDataToWorkExperienceDataPane(<?php echo $workExperience->seqno;?>);"><?php echo htmlspecialchars($workExperience->employer);?></a></td>
                                                <td><?php echo htmlspecialchars($workExperience->jobtitle);?></td>
                                                <td><?php echo $fromDate;?></td>
                                                <td><?php echo $toDate;?></td>
                                                <td><?php echo htmlspecialchars($workExperience->comments);?></td>
                                                </tr>
                                                    <?php $row++;
                                                }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>

                                </div>

                                <!-- this is education section -->
                                <div class="sectionDiv" id="sectionEducation">
                                    <div><h3><?php echo __('Education'); ?></h3></div>

                                    <div class="outerbox" id="changeEducation" style="width:500px;">
                                        <div class="mainHeading"><h2 id="headChangeEducation"><?php echo __('Add Education'); ?></h2></div>
                                        <form id="frmEducation">
                                            <table border="0">
                                                <tr>
                                                    <td width="150">Education</td>
                                                    <td><select id="education">
                                                            <option value="">-- Select --</option>
                                                            <option value="1">B.SC In Mathematics</option>
                                                            <option value="2">B.SC In Psycology</option>
                                                        </select></td>
                                                </tr>
                                                <tr>
                                                    <td>Major/Specialization</td>
                                                    <td><input type="text" class="formInputText" id="major"/></td>
                                                </tr>
                                                <tr>
                                                    <td>GPA/Score</td>
                                                    <td><input type="text" class="formInputText" id="score" /></td>
                                                </tr>
                                                <tr>
                                                    <td>Start Date</td>
                                                    <td><input type="text" class="formInputText" id="startDate" /></td>
                                                </tr>
                                                <tr>
                                                    <td>End Date</td>
                                                    <td><input type="text" class="formInputText" id="endDate" /></td>
                                                </tr>
                                            </table>
                                            <div class="formbuttons">
                                                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Save"); ?>" />
                                                <input type="button" class="savebutton" id="btnEducationCancel" value="<?php echo __("Cancel"); ?>" />
                                            </div>
                                        </form>
                                    </div>
                                    <br />
                                    <div id="actionEducation">
                                        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addEducation" />&nbsp;
                                        <input type="button" value="<?php echo __("Edit");?>" class="savebutton" id="editEducation" />&nbsp;
                                        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delEducation" />
                                        <br /><br />
                                    </div>
                                    <div class="outerbox">
                                        <table width="100%" cellspacing="0" cellpadding="0" class="data-table" id="tblEducation">
                                            <thead>
                                                <tr>
                                                    <td><input type="checkbox" id="educationCheckAll" /></td>
                                                    <td><?php echo __('Education');?></td>
                                                    <td><?php echo __('Major/Specialization');?></td>
                                                    <td><?php echo __('Score');?></td>
                                                    <td><?php echo __('Start Date');?></td>
                                                    <td><?php echo __('End Date');?></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="odd">
                                                    <td><input type="checkbox" class="chkbox2" /></td>
                                                    <td>B.SC In Mathematics</td>
                                                    <td>Applied Math</td>
                                                    <td>3.5</td>
                                                    <td>2008-01-01</td>
                                                    <td>2008-12-31</td>
                                                </tr>
                                                <tr class="even">
                                                    <td><input type="checkbox" class="chkbox2" /></td>
                                                    <td>B.SC In Psycology</td>
                                                    <td>Child Psycology</td>
                                                    <td>4.3</td>
                                                    <td>2009-01-01</td>
                                                    <td>2009-12-31</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- this is skills section -->
                                <div class="sectionDiv" id="sectionSkills">
                                    <div><h3><?php echo __('Education'); ?></h3></div>

                                    <div class="outerbox" id="changeSkills" style="width:500px;">
                                        <div class="mainHeading"><h2 id="headChangeSkills"><?php echo __('Add Skills'); ?></h2></div>
                                        <form id="frmSkills">
                                            <table border="0">
                                                <tr>
                                                    <td width="150">Skill</td>
                                                    <td><select id="skills">
                                                            <option value="">-- Select --</option>
                                                            <option value="1">Driving Plane</option>
                                                            <option value="2">Flying Kite</option>
                                                        </select></td>
                                                </tr>
                                                <tr>
                                                    <td>Years of Experience</td>
                                                    <td><input type="text" class="formInputText" id="experience"/></td>
                                                </tr>
                                                <tr>
                                                    <td valign="top">Comment</td>
                                                    <td><textarea class="formInputText" cols="35" rows="5" id="skillcomment"></textarea></td>
                                                </tr>
                                            </table>
                                            <div class="formbuttons">
                                                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Save"); ?>" />
                                                <input type="button" class="savebutton" id="btnSkillsCancel" value="<?php echo __("Cancel"); ?>" />
                                            </div>
                                        </form>
                                    </div>
                                    <br />
                                    <div id="actionSkills">
                                        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addSkills" />&nbsp;
                                        <input type="button" value="<?php echo __("Edit");?>" class="savebutton" id="editSkills" />&nbsp;
                                        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delSkills" />
                                        <br /><br />
                                    </div>
                                    <div class="outerbox">
                                        <table width="100%" cellspacing="0" cellpadding="0" class="data-table" id="tblSkills">
                                            <thead>
                                                <tr>
                                                    <td><input type="checkbox" id="skillsCheckAll" /></td>
                                                    <td><?php echo __('Skills');?></td>
                                                    <td><?php echo __('Years of Experience');?></td>
                                                    <td><?php echo __('Comment');?></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="odd">
                                                    <td><input type="checkbox" class="chkbox3" /></td>
                                                    <td>Driving Plane</td>
                                                    <td>3</td>
                                                    <td>Starts way you looking me</td>
                                                </tr>
                                                <tr class="even">
                                                    <td><input type="checkbox" class="chkbox3" /></td>
                                                    <td>Flying Kites</td>
                                                    <td>4</td>
                                                    <td>Young for Ever</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- this is Languages section -->
                                <div class="sectionDiv" id="sectionLang">
                                    <div><h3><?php echo __('Languages'); ?></h3></div>

                                    <div class="outerbox" id="changeLang" style="width:500px;">
                                        <div class="mainHeading"><h2 id="headChangeLang"><?php echo __('Add Languages'); ?></h2></div>
                                        <form id="frmSkills">
                                            <table border="0">
                                                <tr>
                                                    <td width="150">Language</td>
                                                    <td><select id="language">
                                                            <option value="">-- Select --</option>
                                                            <option value="1">English</option>
                                                            <option value="2">Sinhala</option>
                                                            <option value="3">Tamil</option>
                                                        </select></td>
                                                </tr>
                                                <tr>
                                                    <td>Fluency</td>
                                                    <td><select id="fluency">
                                                            <option value="">-- Select --</option>
                                                            <option value="1">Writing</option>
                                                            <option value="2">Speaking</option>
                                                            <option value="3">Reading</option>
                                                        </select></td>
                                                </tr>
                                                <tr>
                                                    <td valign="top">Competency</td>
                                                    <td>
                                                        <select id="competency">
                                                            <option value="0">-- Select --</option>
                                                            <option value="1">Poor</option>
                                                            <option value="2">Basic</option>
                                                            <option value="3">Good</option>
                                                            <option value="4">Mother Tongue</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div class="formbuttons">
                                                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Save"); ?>" />
                                                <input type="button" class="savebutton" id="btnLangCancel" value="<?php echo __("Cancel"); ?>" />
                                            </div>
                                        </form>
                                    </div>
                                    <br />
                                    <div id="actionLang">
                                        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addLang" />&nbsp;
                                        <input type="button" value="<?php echo __("Edit");?>" class="savebutton" id="editLang" />&nbsp;
                                        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delLang" />
                                        <br /><br />
                                    </div>
                                    <div class="outerbox">
                                        <table width="100%" cellspacing="0" cellpadding="0" class="data-table" id="tblLang">
                                            <thead>
                                                <tr>
                                                    <td><input type="checkbox" id="langCheckAll" /></td>
                                                    <td><?php echo __('Language');?></td>
                                                    <td><?php echo __('Fluency');?></td>
                                                    <td><?php echo __('Competency');?></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="odd">
                                                    <td><input type="checkbox" class="chkbox4" /></td>
                                                    <td>English</td>
                                                    <td>Writing</td>
                                                    <td>Good</td>
                                                </tr>
                                                <tr class="even">
                                                    <td><input type="checkbox" class="chkbox4" /></td>
                                                    <td>English</td>
                                                    <td>Reading</td>
                                                    <td>Good</td>
                                                </tr>
                                                <tr class="odd">
                                                    <td><input type="checkbox" class="chkbox4" /></td>
                                                    <td>English</td>
                                                    <td>Speaking</td>
                                                    <td>Good</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- this is Licenses section -->
                                <div class="sectionDiv" id="sectionLicenses">
                                    <div><h3><?php echo __('Licenses'); ?></h3></div>

                                    <div class="outerbox" id="changeLicenses" style="width:500px;">
                                        <div class="mainHeading"><h2 id="headChangeLicenses"><?php echo __('Add Licenses'); ?></h2></div>
                                        <form id="frmLicenses">
                                            <table border="0">
                                                <tr>
                                                    <td width="150">Licenses</td>
                                                    <td><select id="license">
                                                            <option value="">-- Select --</option>
                                                            <option value="1">Driving License</option>
                                                            <option value="2">Pilot License</option>
                                                        </select></td>
                                                </tr>
                                                <tr>
                                                    <td>Start Date</td>
                                                    <td><input type="text" class="formInputText" id="licStartDate"/></td>
                                                </tr>
                                                <tr>
                                                    <td valign="top">End Date</td>
                                                    <td><input type="text" class="formInputText" id="licEndDate"/></td>
                                                </tr>
                                            </table>
                                            <div class="formbuttons">
                                                <input type="button" class="savebutton" id="btnSave" value="<?php echo __("Save"); ?>" />
                                                <input type="button" class="savebutton" id="btnLicensesCancel" value="<?php echo __("Cancel"); ?>" />
                                            </div>
                                        </form>
                                    </div>
                                    <br />
                                    <div id="actionLicenses">
                                        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addLicenses" />&nbsp;
                                        <input type="button" value="<?php echo __("Edit");?>" class="savebutton" id="editLicenses" />&nbsp;
                                        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delLicenses" />
                                        <br /><br />
                                    </div>
                                    <div class="outerbox">
                                        <table width="100%" cellspacing="0" cellpadding="0" class="data-table" id="tblLicenses">
                                            <thead>
                                                <tr>
                                                    <td><input type="checkbox" id="licensesCheckAll" /></td>
                                                    <td><?php echo __('Licenses');?></td>
                                                    <td><?php echo __('Start Date');?></td>
                                                    <td><?php echo __('End Date');?></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="even">
                                                    <td><input type="checkbox" class="chkbox5" /></td>
                                                    <td>Driving License</td>
                                                    <td>2007-06-12</td>
                                                    <td>2011-06-21</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br />
                            </div>
                        </div>
                    </td>
                    <td valign="top" align="left">
                        <?php include_partial('photo', array('empNumber' => $empNumber, 'fullName' => htmlentities($workExperienceForm->fullName)));?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php echo javascript_include_tag('../orangehrmPimPlugin/js/viewQualificationsSuccess'); ?>