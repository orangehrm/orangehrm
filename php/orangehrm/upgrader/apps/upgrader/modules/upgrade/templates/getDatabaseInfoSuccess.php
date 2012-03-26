<?php use_javascript('jquery.js') ?>
<?php use_javascript('jquery.validate.js') ?>
<?php use_javascript('getDatabaseInfoSuccess.js') ?>
<div>
    <h2>Database Information</h2>
    <p>Get database information</p>
</div>
<?php if ($sf_user->hasFlash('errorMessage')): ?>
    <div class="messageBalloon_warning">
    <?php echo '<span>'.$sf_user->getFlash('errorMessage').'</span>' ?>
    </div>
<?php endif; ?>
<div>
    <form action="<?php echo url_for('upgrade/getDatabaseInfo');?>" name="databaseInfoForm" id="databaseInfoForm" method="post">
        <?php echo $form->renderHiddenFields();?>
        <table>
            <tbody>
                <tr>
                    <td>
                        <label><?php echo $form['host']->renderLabel();?></label>
                    </td>
                    <td>
                        <?php echo $form['host']->render();?>
                        <div class="errorContainer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo $form['port']->renderLabel();?></label>
                    </td>
                    <td>
                        <?php echo $form['port']->render();?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo $form['username']->renderLabel();?></label>
                    </td>
                    <td>
                        <?php echo $form['username']->render();?>
                        <div class="errorContainer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo $form['password']->renderLabel();?></label>
                    </td>
                    <td>
                        <?php echo $form['password']->render();?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php echo $form['database_name']->renderLabel();?></label>
                    </td>
                    <td>
                        <?php echo $form['database_name']->render();?>
                        <div class="errorContainer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="Proceed"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
