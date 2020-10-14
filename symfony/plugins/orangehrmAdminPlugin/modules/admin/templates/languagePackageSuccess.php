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

use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/languagePackageSuccess'));
use_stylesheet(plugin_web_path('orangehrmAdminPlugin', 'css/languagePackageSuccess'));

$hasFlash = $sf_user->hasFlash('form.warning');
?>

<div class="box <?php echo $hasFlash ? '' : 'div-hide'; ?>" id="divAddLanguagePackage">
    <div class="head">
        <h1><?php echo __('Add Language Package'); ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages', ['prefix' => 'form']); ?>
        <form id="frmAddLanguagePackage" name="frmAddLanguagePackage" method="post" action="<?php echo url_for('admin/languagePackage'); ?>">
            <fieldset>
                <ol>
                    <?php echo $form->render();?>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
            </fieldset>
            <p>
                <?php
                $actionButtons = $form->getActionButtons();
                foreach ($actionButtons as $button) {
                    echo $button->render(null), "\n";
                }
                ?>
            </p>
            <br><br>
            <p>
                <?php
                echo __('Users will require translate texts manually after creating the language package.');
                ?>
            </p>
        </form>
    </div>
</div>

<?php include_component('core', 'ohrmList'); ?>

<script type="text/javascript">
    var lang_Required = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
</script>
