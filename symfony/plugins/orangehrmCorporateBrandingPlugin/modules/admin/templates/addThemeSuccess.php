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

use_stylesheet(plugin_web_path('orangehrmCorporateBrandingPlugin', 'css/spectrum.css'));
use_stylesheet(plugin_web_path('orangehrmCorporateBrandingPlugin', 'css/addThemeSuccess.css'));
use_javascript(plugin_web_path('orangehrmCorporateBrandingPlugin', 'js/vendor/spectrum.js'));
use_javascript(plugin_web_path('orangehrmCorporateBrandingPlugin', 'js/addThemeSuccess.js'));

?>
<div class = "box">
    <div class="head">
        <h1><?php echo __('Corporate Branding'); ?></h1>
    </div>

    <div class="inner" id="addThemeTbl">
        <div class="message success" id="message_div">
            <ol>
                <li id="message_body"></li>
            </ol>
            <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
        </div>

        <?php include_partial('global/flash_messages'); ?>
        <table>
            <tbody>
            <tr>
                <td style="width: 35%">
                    <form id="frmAddTheme" method="post" action="<?php echo url_for('admin/addTheme'); ?>" enctype="multipart/form-data"
                          onsubmit="$('#disable-screen').attr('class', 'overlay');$('#loading').attr('class', 'loading-class');" >
                        <fieldset>
                            <input id="hdnResetTheme" type="hidden" name="resetTheme" value="0">
                            <?php echo $form->renderHiddenFields(); ?>
                            <ol>
                                <li>
                                    <?php echo $form['primaryColor']->renderLabel(__('Primary Color')); ?>
                                    <?php echo $form['primaryColor']->render(array("id" => "primaryColor")); ?>
                                </li>
                                <li>
                                    <?php echo $form['secondaryColor']->renderLabel(__('Secondary Color')); ?>
                                    <?php echo $form['secondaryColor']->render(array("id" => "secondaryColor")); ?>
                                </li>
                                <li>
                                    <?php echo $form['buttonSuccessColor']->renderLabel(__('Primary Button Color')); ?>
                                    <?php echo $form['buttonSuccessColor']->render(array("id" => "buttonSuccessColor")); ?>
                                </li>
                                <li>
                                    <?php echo $form['buttonCancelColor']->renderLabel(__('Secondary Button Color')); ?>
                                    <?php echo $form['buttonCancelColor']->render(array("id" => "buttonCancelColor")); ?>
                                </li>
                                <li>
                                    <label for="mainLogo"><?php echo __("Client Logo") ?></label>
                                    <input type="file" accept="image/png, image/jpeg" id="file" name="file" />
                                </li>
                                <li>
                                    <label for="loginBanner"><?php echo __("Login Banner") ?></label>
                                    <input type="file" accept="image/png, image/jpeg" id="loginBanner" name="loginBanner" />
                                </li>
                                <li>
                                    <label for="socialMediaIcons"><?php echo __("Social Media Images") ?></label>
                                    <input type="checkbox" id="socialMediaIcons" name="socialMediaIcons" value="inline" <?php if($showChecked) echo "checked" ?>>
                                    <input type="hidden" id="socialMediaIconsHidden" name="socialMediaIcons" value="none" >
                                </li>
                            </ol>
                            <p>
                                <input type="button" class="" id="btnSave" value="<?php echo __("Publish"); ?>"  />
                                <input type="button" class="" id="btnReset" value="<?php echo __("Reset to Default"); ?>" />
                            </p>
                        </fieldset>
                    </form>
                </td>
                <td id="preview" style="border: 1px solid #dedede;position: relative; width: 65%; vertical-align: top; background-color: #EFEFEF; display: none">
                    <div id="preview-overlay" style="position: absolute; width: 100%; height: 100%; opacity: 0.1; z-index: 1000"></div>
                    <div class="head">
                        <h1><?php echo __("Preview") ?></h1>
                    </div>
                    <?php echo $sf_data->getRaw('searchForm'); ?>
                    <?php echo $sf_data->getRaw('searchResults'); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div id="disable-screen"></div>
<div id="loading"></div>

<script>
    var mainCssUrlAjax = '<?php echo url_for("admin/getCompiledCSS"); ?>';
    var langRequired = "<?php echo __js(ValidationMessages::REQUIRED);?>";
    var clientLogoMessage = "<?php echo __js('Try Again with an Image of Width %maxWidth% px and Height %maxHeight% px', ['%maxWidth%' => 300, '%maxHeight%' => 60]);?>";
    var loginBannerMessage = "<?php echo __js('Try Again with an Image of Width %maxWidth% px and Height %maxHeight% px', ['%maxWidth%' => 1024, '%maxHeight%' => 180]);?>";
    var langInvalidColor = "<?php echo __js('Invalid Color');?>";
</script>
