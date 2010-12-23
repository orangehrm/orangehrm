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
<div class="formpageNarrow">

    <?php echo isset($templateMessage)?templateMessage($templateMessage):''; ?>

    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo __('Work Week'); ?></h2></div>

        <div id="errorDiv"> </div>
        <?php if($form->hasErrors())
        {?>
            <?php echo $form['select_1']->renderError() ?>
            <?php echo $form['select_2']->renderError() ?>
            <?php echo $form['select_3']->renderError() ?>
            <?php echo $form['select_4']->renderError() ?>
            <?php echo $form['select_5']->renderError() ?>
            <?php echo $form['select_6']->renderError() ?>
            <?php echo $form['select_7']->renderError() ?>
    <?php }?>

        <?php
        // check whether allowed to edit work week
        $disabled = "";

?>

        <form id="frmWorkWeek" name="frmWorkWeek" method="post" action="<?php echo url_for('coreLeave/defineWorkWeek') ?>" >
<?php echo $form['_csrf_token']->render()?>
            <?php echo $form['select_1']->renderLabel(); ?>
<?php echo $form['select_1']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>

            <?php echo $form['select_2']->renderLabel(); ?>
<?php echo $form['select_2']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>

            <?php echo $form['select_3']->renderLabel(); ?>
<?php echo $form['select_3']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>

            <?php echo $form['select_4']->renderLabel(); ?>

<?php echo $form['select_4']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>

            <?php echo $form['select_5']->renderLabel(); ?>

<?php echo $form['select_5']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>

            <?php echo $form['select_6']->renderLabel(); ?>

<?php echo $form['select_6']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>

            <?php echo $form['select_7']->renderLabel(); ?>

<?php echo $form['select_7']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>


            <div class="formbuttons">
                <input type="button" class="savebutton" id="saveBtn" value="<?php echo __('Edit'); ?>" />
                <input type="button" class="clearbutton" onclick="reset();" value="<?php echo __('Reset'); ?>" />
            </div>
        </form>
    </div>
    <script type="text/javascript">
        //<![CDATA[

        $(document).ready(function() {
            $(".formSelect").attr("disabled", "disabled");
            
            $("#saveBtn").click(function() {
                if($("#saveBtn").attr("value") == "Edit") {
                    $(".formSelect").removeAttr("disabled");
                    $("#saveBtn").attr("value", "Save");
                    return;
                }

                if($("#saveBtn").attr("value") == "Save") {
                    $("#frmWorkWeek").submit();
                    $(".formSelect").attr("disabled", "disabled");
                    return;
                }
            });
        });
        //]]>
    </script>
</div>
