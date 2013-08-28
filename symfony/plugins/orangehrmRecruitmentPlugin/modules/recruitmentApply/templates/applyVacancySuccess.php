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

<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/applyVacancySuccess')); ?>

<style type="text/css">
    #content {
        padding-top: 0;
    }
</style>

<div id="addCandidate" class="box">

        <div class="head"><h1 id="addCandidateHeading"><?php echo __("Apply for") . " " . $name; ?></h1></div>
        
        <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_after_main_heading_1')) ?>
        
        <div class="inner">
            
        <?php include_partial('global/flash_messages', array('prefix' => 'applyVacancy')); ?>
        
        <form name="frmAddCandidate" id="frmAddCandidate" method="post" enctype="multipart/form-data">

            <fieldset>
                
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form["vacancyList"]->render(); ?>

            <ol>
                
                <li>
                    <label><?php echo __('Description'); ?> <span  id="extend">[+]</span></label>
                    <div id="txtArea" style="width:100%;margin-left: 150px">
                        <?php echo $description ?>
                    </div>
                </li>
                
                <li class="line nameContainer">

                    <label class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
                    <ol class="fieldsInLine">
                        <li>
                            <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                            <?php echo $form['firstName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                        </li>
                        <li>
                            <div class="fieldDescription"><?php echo __('Middle Name'); ?></div>
                             <?php echo $form['middleName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                        </li>
                        <li>
                            <div class="fieldDescription"><em>*</em> <?php echo __('Last Name'); ?></div>
                            <?php echo $form['lastName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                        </li>
                    </ol>                        

                </li>                
                
            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_after_main_heading_2')) ?>
            
            <li>
                <?php echo $form['email']->renderLabel(__('Email'). ' <span class="required">*</span>'); ?>
                <?php echo $form['email']->render(array("class" => "formInputText")); ?>
            </li>
            
            <li>
                <?php echo $form['contactNo']->renderLabel(__('Contact No'), array("class " => "contactNoLable")); ?>
                <?php echo $form['contactNo']->render(array("class" => "contactNo")); ?>
            </li>
            
        </ol>
        <ol>    
            
            <li class="fieldHelpContainer">
            <?php if ($candidateId == "") : ?>
            
                <?php echo $form['resume']->renderLabel(__('Resume') . ' <span class="required">*</span>'); ?>
                <?php echo $form['resume']->render(array("class " => "duplexBox")); ?>
                <?php echo "<label class=\"fieldHelpBottom\">" . __(CommonMessages::FILE_LABEL_DOC) . "</label>"; ?>
            
            <?php else : ?>
                
                <?php echo $form['resume']->renderLabel(__('Resume')); ?>
                <?php echo "<span class=\"fileLink\">".__('Uploaded')."</span>"; ?>
            
            <?php endif; ?>
            </li>
            
            <li>
                <?php echo $form['keyWords']->renderLabel(__('Keywords'), array("class " => "keywrd")); ?>
                <?php echo $form['keyWords']->render(array("class" => "keyWords")); ?>
            </li>
            
            <li>
                <?php echo $form['comment']->renderLabel(__('Notes'), array("class " => "comment")); ?>
                <?php echo $form['comment']->render(array("class" => "formInputText","id" => "notes", "cols" => 43, "rows" => 4)); ?>
            </li>
            
            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_after_main_heading_3')) ?>
            
            <li class="required new">
                <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
            </li>
            
            </ol>
            
            <p>
                <input type="button" class="savebutton" name="btnSave" id="btnSave" value="<?php echo __("Submit"); ?>" /> <a id="backLink" href="<?php echo url_for('recruitmentApply/jobs') ?>"><?php echo __("Back to Job List"); ?></a>
            </p>
            
            </fieldset>

        </form>
        
        </div> <!-- inner -->
        
    </div>
    <div id="footer">
        <?php include_partial('global/copyright');?>
    </div>
<script type="text/javascript">
    //<![CDATA[
    var description	= '<?php $description; ?>';
    var vacancyId	= '<?php echo $vacancyId; ?>';
    var candidateId	= '<?php echo ($candidateId !="") ? $candidateId : 0;?>';
    var lang_firstNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_lastNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_emailRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_validEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    var lang_tooLargeInput = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 30)); ?>";
    var lang_commaSeparated = "<?php echo __("Enter comma separated words") . '...'; ?>";
    var lang_validPhoneNo = "<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>";
    var lang_noMoreThan250 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_resumeRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var linkForApplyVacancy = "<?php echo url_for('recruitmentApply/applyVacancy'); ?>";
    var linkForViewJobs = "<?php echo url_for('recruitmentApply/viewJobs'); ?>";
    var lang_back = "<?php echo __("Go to Job Page")?>";
	
</script>
