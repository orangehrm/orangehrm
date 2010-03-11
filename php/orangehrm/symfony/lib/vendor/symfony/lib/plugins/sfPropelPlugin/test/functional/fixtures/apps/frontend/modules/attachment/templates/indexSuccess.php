<form action="<?php echo url_for('attachment/'.sfContext::getInstance()->getActionName()) ?>" method="post" enctype="multipart/form-data">
  <table>
    <?php echo $form ?>
    <tr>
      <td colspan="2">
        <input type="submit" value="submit" />
      </td>
    </tr>
  </table>
</form>
