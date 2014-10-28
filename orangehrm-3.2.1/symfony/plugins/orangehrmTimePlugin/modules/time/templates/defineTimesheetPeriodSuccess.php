<?php /**

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
<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/defineTimesheetPeriod')); ?>

<div class="box">
    
    <div class="head">
        <h1 id="defineTimesheet"><?php echo __('Define Timesheet Period'); ?></h1>
    </div>
    
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>
        
        <form id="definePeriod" method="post" action="<?php echo url_for('time/defineTimesheetPeriod')?>">
            <?php echo $form['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li>
                            <?php echo $form['startingDays']->renderLabel(__('First Day of Week') . ' <em>*</em>'); ?>
                            <?php echo $form['startingDays']->render(array("maxlength" => 20)); ?>
                    </li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                </p>
            </fieldset>
        </form> 
        
    </div>
    
</div>
    
<script type="text/javascript">
    var lang_required = '<?php echo __(ValidationMessages::REQUIRED);?>';
</script>