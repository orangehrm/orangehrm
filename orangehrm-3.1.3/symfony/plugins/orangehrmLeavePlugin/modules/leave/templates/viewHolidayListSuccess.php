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
?>
<?php 

use_javascripts_for_form($searchForm);
use_stylesheets_for_form($searchForm);
?>

<?php if($holidayPermissions->canRead()){?>
<div id="holiday-information" class="box toggableForm">
    
    <div class="head">
        <h1 id="searchHolidayHeading"><?php echo __('Holidays'); ?></h1>
    </div>
    
    <div class="inner">
         
        <form id="frmHolidaySearch" name="frmHolidaySearch" method="post" action="<?php echo url_for('leave/viewHolidayList') ?>" > 
            

            <fieldset>
                
                <ol>
                    <?php echo $searchForm->render(); ?>
                </ol>
               
                <p>
                    <input type="button" name="btnSearch" id="btnSearch" value="<?php echo __("Search") ?>" class="savebutton" />
                </p>
                
            </fieldset>
            
        </form>
        
        
    </div>
    
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
    
</div>

<div id="holidayList">
    <?php include_component('core', 'ohrmList'); ?>
</div>

<?php } ?>

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
    var defineHolidayUrl = '<?php echo url_for('leave/defineHoliday'); ?>';
    var lang_SelectHolidayToDelete = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';      
//]]>    
</script>