<?php if ($sf_user->hasFlash('message')): ?>
  <?php echo $sf_user->getFlash('message') ?>
<?php endif; ?>
<div>
    <form action="" method="post" name="folderInputForm" id="folderInputForm">
        <?php echo $form->renderHiddenFields();?>
        <table>
            <tbody>
                <tr>
                    <td>
                        <?php echo $form['folder_path']->renderLabel() ?>
                    </td>
                    <td>
                        <?php echo $form['folder_path']->render() ?>
                    </td>
                </tr>
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
