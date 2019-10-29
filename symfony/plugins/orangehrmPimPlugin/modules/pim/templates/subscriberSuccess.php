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
<div class="box">
        <div id="addSubscribe">
            <div class="head">
                <h1 id="heading"><?php echo __('Subscribe'); ?></h1>
            </div>
                <div class="inner">
                    <?php include_partial('global/flash_messages'); ?>
                    <?php if ($form->hasErrors()): ?>
                        <?php include_partial('global/form_errors', array('form' => $form)); ?>
                    <?php endif; ?>
                    <?php if(!$isSubscribed) { ?>
                    <form name="frmSubscribe" id="frmSubscribe" method="post" action="<?php echo url_for('pim/subscriber?empNumber=' . $empNumber); ?>">
                        <?php echo $form->renderHiddenFields(); ?>
                        <fieldset>
                            <ol>
                                <li>
                                    <?php echo $form['name']->renderLabel(__('Name') . ' <em>*</em>'); ?>
                                    <?php echo $form['name']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
                                </li>
                                <li>
                                    <?php echo $form['email']->renderLabel(__('Email') . ' <em>*</em>'); ?>
                                    <?php echo $form['email']->render(array("class" => "formInputText", "maxlength" => 50)); ?>
                                </li>
                                <li class="required">
				    <em>*</em><?php echo __(CommonMessages::REQUIRED_FIELD); ?>
				    <br>
				    <br>
				    <span>
					<?php echo __("By subscribing you agree to receive periodic email updates from OrangeHRM."); ?>
				    </span>
				</li>
                            </ol>
                            <p>
                                <input type="button" class="" name="btnSubscribe" id="btnSubscribe" value="<?php echo __("Subscribe"); ?>"/>
                            </p>
                        </fieldset>
                    </form>
                    <?php } else { ?>
                        <div class="message success">
                            <?php echo __("Successfully Subscribed"); ?>
                        </div>
                    <?php } ?>
                </div>


        </div>
</div>
<script type="text/javascript">
    var lang_languageRequired = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
    var lang_languageEmail = '<?php echo __js(ValidationMessages::EMAIL_INVALID); ?>';
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a separate js file
    $(document).ready(function() {

        $("#btnSubscribe").click(function() {
            $("#frmSubscribe").submit();
        });

        var subscribeValidator =
            $("#frmSubscribe").validate({
                rules: {
                    'subscriber[name]': {required: true},
                    'subscriber[email]': {required: true, email: true}
                },
                messages: {
                    'subscriber[name]': {required: lang_languageRequired},
                    'subscriber[email]': {required: lang_languageRequired, email: lang_languageEmail}
                }
            });
    });
    //]]>
</script>
