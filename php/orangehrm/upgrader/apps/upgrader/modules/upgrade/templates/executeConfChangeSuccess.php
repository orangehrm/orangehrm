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
                <tr>
                    <td>
                        <input type="submit" value="<?php echo __("Start")?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
