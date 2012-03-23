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
                    </td>
                    <td>
                        <input type="submit" value="Submit" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
