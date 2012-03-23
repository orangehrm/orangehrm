<div>
    <form action="<?php echo url_for('upgrade/getDatabaseInfo');?>" name="databaseInfoForm" method="post">
        <?php echo $form->renderHiddenFields();?>
        <table>
            <tbody>
                <tr>
                    <td>
                        <label><?php echo $form['host']->renderLabel();?></label>
                    </td>
                    <td>
                        <?php echo $form['host']->render();?>
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
                        <label><?php echo $form['user']->renderLabel();?></label>
                    </td>
                    <td>
                        <?php echo $form['user']->render();?>
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
                    </td>
                </tr>
                <tr>
                    <td>
                        
                    </td>
                    <td>
                        <input type="submit" value="Proceed"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
