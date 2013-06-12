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

<div class="box">
    
    <div class="head">
        <h1><?php echo __('Configure PIM'); ?></h1>
    </div>
    
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>
        
        <form id="frmConfigPim" name="frmConfigPim" method="post" action="<?php echo url_for('pim/configurePim') ?>" >
            <?php echo $form['_csrf_token']; ?>
            <fieldset>
                
                
                
                <ol>
                    
                    <li>
                        <h2><?php echo __('Show Deprecated Fields'); ?></h2>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['chkDeprecateFields']->render(); ?>
                        <?php echo $form['chkDeprecateFields']->renderLabel(__('Show Nick Name, Smoker and Military Service in Personal Details')); ?>
                    </li>
                    
                </ol>
                
                <ol>
                    
                    <li>
                        <h2><?php echo __('Country Specific Information'); ?></h2>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['chkShowSSN']->render(); ?>
                        <?php echo $form['chkShowSSN']->renderLabel(__('Show SSN field in Personal Details')); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['chkShowSIN']->render(); ?>
                        <?php echo $form['chkShowSIN']->renderLabel(__('Show SIN field in Personal Details')); ?>
                    </li>
                    
                    <li class="checkbox">
                        <?php echo $form['chkShowTax']->render(); ?>
                        <?php echo $form['chkShowTax']->renderLabel(__('Show US Tax Exemptions menu')); ?>
                    </li>
                    
                </ol>
                
                <p>
                    <input type="button" class="" id="btnSave" value="<?php echo __("Edit"); ?>" tabindex="2" />
                </p>
                
            </fieldset>

        </form>
    
    </div>

</div>

<script type="text/javascript">
//<![CDATA[
//we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
$(document).ready(function() {
    $("#configPim_chkDeprecateFields").attr('disabled', 'disabled');
    $("#configPim_chkShowSSN").attr('disabled', 'disabled');
    $("#configPim_chkShowSIN").attr('disabled', 'disabled');
    $("#configPim_chkShowTax").attr('disabled', 'disabled');
    
    $("#btnSave").click(function() {
        if($("#btnSave").attr('value') == "<?php echo __("Edit"); ?>") {
            $("#btnSave").attr('value', "<?php echo __("Save"); ?>");
            $("#configPim_chkDeprecateFields").removeAttr('disabled');
            $("#configPim_chkShowSSN").removeAttr('disabled');
            $("#configPim_chkShowSIN").removeAttr('disabled');
            $("#configPim_chkShowTax").removeAttr('disabled');
            
            return;
        }

        if($("#btnSave").attr('value') == "<?php echo __("Save"); ?>") {
            $("#frmConfigPim").submit();
        }
    });
});
//]]>
</script>