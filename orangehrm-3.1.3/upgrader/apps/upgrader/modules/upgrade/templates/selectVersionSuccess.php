<?php use_javascript('jquery.js') ?>
<?php use_javascript('selectVersionSuccess.js') ?>
<div>
    
    <h2>Current Version Details</h2>
    
    <p>
        Select your current OrangeHRM version here. You can find the version at the bottom of OrangeHRM login page. OrangeHRM Upgrader only supports versions listed in the dropdown and selecting a different version would lead to an upgrader failure and a database corruption.
    </p>    
    
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
                        <input id="selectVersionSubmit" type="submit" value="Proceed"/>
                    </td>
                    <td>
                        
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
