<div>
    <h2>Configure File</h2>
    <p>Conf file conf file</p>
</div>
<?php if ($sf_user->hasFlash('message')): ?>
  <?php echo $sf_user->getFlash('message') ?>
<?php endif; ?>
<div>
    <form action="<?php echo url_for('upgrade/executeConfChange');?>" method="post" name="configureFileForm" id="configureFileForm">
        <?php echo $form->renderHiddenFields();?>
        <table>
            <tbody>
                <table class="displayTable">
                    <tbody>
                        <tr>
                            <td>
                                <span>Conf.php</span>
                            </td>
                            <td>
                                <span><?php echo __($confFileCreted[0])?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>database.yml</span>
                            </td>
                            <td>
                                <span><?php echo __($confFileCreted[1])?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <tr>
                    <td>
                        <input type="submit" id="sumbitButton" name="sumbitButton" value="<?php echo __($buttonState)?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
