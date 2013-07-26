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
 */

if($attendancePermissios->canRead()){
?>

<style type="text/css">
    form ol li.checkbox label {
        width:35%
    }
</style>
    

<div class="box">
         
    <div class="head">
        <h1><?php echo __('Attendance Configuration'); ?></h1>
    </div>
        
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>
            
        <form  id="configureForm" action=""  method="post">
            <?php echo $form['_csrf_token']; ?>
            <fieldset>
                <ol>
                   <li class="checkbox">
                        <?php echo $form['configuration1']->renderLabel(__('Employee can change current time when punching in/out')); ?>
                        <?php echo $form['configuration1']->render(); ?>
                        </li>
                        
                                            
                    <li class="checkbox">
                        <?php echo $form['configuration2']->renderLabel(__('Employee can edit/delete own attendance records')); ?>
                        <?php echo $form['configuration2']->render(); ?>
                        </li>
                        
                    <li class="checkbox">
                         <?php echo $form['configuration3']->renderLabel(__('Supervisor can add/edit/delete attendance records of subordinates')); ?>
                         <?php echo $form['configuration3']->render(); ?>
                         </li>
                                                                 
                </ol>
                <p>
                    <?php if($attendancePermissios->canUpdate()){?>
                        <input type="submit" class="" id="btnSave" value="<?php echo __('Save'); ?>" />
                    <?php }?>
                </p>
                    
            </fieldset>
                
        </form>
            
    </div>
        
</div>
<?php }?>