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

use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/languageCustomizationSuccess'));
use_stylesheet(plugin_web_path('orangehrmAdminPlugin', 'css/languageCustomizationSuccess'));
?>

<div class="box toggableForm twoColumn">
    <div class="head">
        <h1><?php echo __("Translate Language Package") ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages', ['prefix' => 'search']); ?>

        <form id="frmTranslateLanguageSearch" name="frmTranslateLanguageSearch" method="post" action="<?php echo url_for('admin/languageCustomization') . '?langId='. $langId; ?>">
            <fieldset>
                <ol class="no-border-bottom">
                    <li>
                        <?php echo $form['langPackage']->renderLabel(); ?>
                        <?php echo $form['langPackage']->render(); ?>
                    </li>
                </ol>
                <ol class="no-border-bottom">
                    <li>
                        <?php echo $form['sourceLang']->renderLabel(); ?>
                        <?php echo $form['sourceLang']->render(); ?>
                    </li>
                </ol>
                <ol class="no-border-bottom">
                    <li>
                        <?php echo $form['group']->renderLabel(); ?>
                        <?php echo $form['group']->render(); ?>
                    </li>
                </ol>
                <ol>
                    <li>
                        <?php echo $form['sourceText']->renderLabel(); ?>
                        <?php echo $form['sourceText']->render(); ?>
                    </li>
                    <li>
                        <?php echo $form['translatedText']->renderLabel(); ?>
                        <?php echo $form['translatedText']->render(); ?>
                    </li>
                    <li class="line radio">
                        <?php echo $form['translated']->renderLabel(); ?>
                        <?php echo $form['translated']->render(); ?>
                    </li>
                    <?php echo $form->renderHiddenFields(); ?>
                </ol>
                <p>
                    <input type="button" id="searchBtn" value="<?php echo __("Search") ?>" name="_search"/>
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset"/>
                </p>
            </fieldset>
        </form>
    </div>
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div>

<?php include_component('core', 'ohrmList'); ?>

<script>
    var lang_edit = "<?php echo __js("Edit"); ?>";
    var lang_save = "<?php echo __js("Save"); ?>";
    var lang_LengthExceeded = "<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";

    // Override global submitPage
    function submitPage(pageNumber) {
        var baseUrl = location.href;
        var urlSuffix = '';

        if (baseUrl.match(/pageNo=\d{1,}/)) {
            baseUrl = baseUrl.replace(/pageNo=\d{1,}/, 'pageNo=' + pageNumber);
        } else {
            baseUrl = baseUrl.replace(/#$/, '');
            urlSuffix = '&pageNo=' + pageNumber;
        }

        location.href = baseUrl + urlSuffix;
    }
</script>
