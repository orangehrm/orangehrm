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

<?php 
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/viewSkillsSuccess'));
?>

<div class="box" id="saveFormDiv">
    
    <div class="head">
            <h1 id="saveFormHeading">Add Skill</h1>
    </div>
    
    <div class="inner">
        
        <form name="frmSave" id="frmSave" method="post" action="<?php echo url_for('admin/viewSkills'); ?>">
            
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['id']->render(); ?>
            
            <fieldset>
                
                <ol>
                    
                    <li>
                        <?php echo $form['name']->renderLabel(__('Name'). ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(array("class" => "block default editable valid", "maxlength" => 120)); ?>
                    </li>
                    
                    <li class="largeTextBox">
                        <?php echo $form['description']->renderLabel(__('Description')); ?>
                        <?php echo $form['description']->render(array("class" => "editable")); ?>
                    </li>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>   
                    
                </ol>
                
                <p>
                    <input type="button" id="btnSave" class="addbutton" name="btnSave" value="<?php echo __('Save');?>" />
                    <input type="button" id="btnCancel" class="btn reset" value="<?php echo __('Cancel'); ?>" />
                </p>
                
            </fieldset>

        </form>
    
    </div>   
    
</div> <!-- saveFormDiv -->

<!-- Listi view -->

<div id="recordsListDiv" class="box miniList">
    <div class="head">
            <h1><?php echo __('Skills'); ?></h1>
    </div>
    
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>
        
        <form name="frmList" id="frmList" method="post" action="<?php echo url_for('admin/deleteSkills'); ?>">
            <?php echo $listForm ?>
            <p id="listActions">
                <input type="button" class="addbutton" id="btnAdd" value="<?php echo __('Add'); ?>"/>
                <input type="button" class="delete" id="btnDel" value="<?php echo __('Delete'); ?>"/>
            </p>
            
            <table class="table hover" id="recordsListTable">
                <thead>
                    <tr>
                        <th class="check" style="width:2%"><input type="checkbox" id="checkAll" class="checkboxAtch" /></td>
                        <th style="width:40%"><?php echo __('Name'); ?></td>
                        <th style="width:58%"><?php echo __('Description'); ?></td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                    $row = 0;
                    foreach($records as $record) : 
                        $cssClass = ($row%2) ? 'even' : 'odd';
                    ?>
                    
                    <tr class="<?php echo $cssClass;?>">
                        <td class="check">
                            <input type="checkbox" class="checkboxAtch" name="chkListRecord[]" value="<?php echo $record->getId(); ?>" />
                        </td>
                        <td class="tdName tdValue">
                            <a href="#"><?php echo $record->getName(); ?></a>
                        </td>
                        <td class="tdValue">
                            <?php echo $record->getDescription(); ?> 
                        </td>
                    </tr>
                    
                    <?php 
                    $row++;
                    endforeach; 
                    ?>
                    
                    <?php if (count($records) == 0) : ?>
                    <tr class="<?php echo 'even';?>">
                        <td>
                            <?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                </tbody>
            </table>
        </form>
    </div>
</div> <!-- recordsListDiv -->    

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
    <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">
//<![CDATA[	    
 
    var recordsCount = <?php echo count($records);?>;
   
    var recordKeyId = "skill_id";
   
    var saveFormFieldIds = new Array();
    saveFormFieldIds[0] = "skill_name";
    saveFormFieldIds[1] = "skill_description";
    
    var urlForExistingNameCheck = '<?php echo url_for('admin/checkSkillNameExistence'); ?>';
    
    var lang_addFormHeading = "<?php echo __('Add Skill'); ?>";
    var lang_editFormHeading = "<?php echo __('Edit Skill'); ?>";
    
    var lang_nameIsRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_descLengthExceeded = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
    var lang_nameExists = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var skills = <?php echo str_replace('&#039;', "'", $form->getSkillListAsJson()) ?> ;
    var skillList = eval(skills);
    
//]]>	
</script> 
