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
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/messageBoxStyles'));
?>
<div class="message-box">
    <div class="mb-heading ac_over">
        <?php echo __($messageHeading); ?>
    </div>
    <div class="mb-body">
        <?php echo __($messageBody); ?>
    </div>
    <div class="mb-button-panel <?php echo $mbBtnPanelClass; ?>">
        <input type="button" class="submitBtn cancel mb-btn"  id="<?php echo $noBtnId; ?>" value="<?php echo __("No"); ?>" />
        <input type="button" class="submitBtn mb-btn"  id="<?php echo $yesBtnId; ?>" value="<?php echo __("Yes"); ?>" />
        <input type="button" class="submitBtn mb-btn <?php echo $okBtnClass; ?>" id="<?php echo $okBtnId; ?>" value="<?php echo __("Ok"); ?>" />
    </div>
</div>


