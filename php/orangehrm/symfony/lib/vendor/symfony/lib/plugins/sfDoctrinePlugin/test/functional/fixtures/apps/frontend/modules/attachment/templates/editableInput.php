<form action="<?php echo url_for('attachment/editable?id='.$form->getObject()->id) ?>" method="post">
  <table>
    <?php echo $form ?>
  </table>
  <p><button type="submit">submit</button></p>
</form>
