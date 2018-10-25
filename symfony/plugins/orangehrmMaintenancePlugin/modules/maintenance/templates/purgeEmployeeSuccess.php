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
 * Boston, MA 02110-1301, USA
 */
use_javascript(plugin_web_path('orangehrmMaintenancePlugin', 'js/PassWordValidation'));
const MESSEGE = 'Users who seek access to their data, or who seek to correct,
 amend, or delete the given information should direct their requests to Data@orangehrm.com
  with the subject "Purge Records (Instance Identifier : ';
?>
<div class="box">
    <?php include_partial('global/flash_messages'); ?>

    <div class="head">
        <h1><?php echo __($header); ?></h1>
    </div>
    <div class="inner">
        <form id="frmPurgeEmployeeAuthenticate" method="post"
              action="">
            <div class="row">
                <fieldset>
                    <div class="input-field col s12 m12 l4">
                        <ol>
                            <?php echo $purgeAuthenticateForm->render(); ?>
                        </ol>
                    </div>
                </fieldset>
                <div class="input-field col s12 m12 l4">
                    <br>
                    <input type="submit" value=<?php echo __('Verify'); ?>>
                </div>
            </div>
        </form>
        <div id="Instance_Messege" style="padding-top: 0.75rem !important">
            <?php if (gettype($instanceId) == 'string') {
                echo __(MESSEGE . $instanceId . ')".');
            } ?>
        </div>
    </div>

</div>

