<div>
    <form action="<?php echo url_for('upgrade/calculateIncrementNumbers');?>" name="versionInfoForm" method="post">
        <?php echo $form->renderHiddenFields();?>
        <table>
            <tbody>
                <tr>
                    <td>
                        <label><?php echo $form['version']->renderLabel();?></label>
                    </td>
                    <td>
                        <?php echo $form['version']->render();?>
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
