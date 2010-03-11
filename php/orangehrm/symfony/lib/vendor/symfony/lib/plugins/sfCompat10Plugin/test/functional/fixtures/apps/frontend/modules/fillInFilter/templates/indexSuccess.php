<?php echo form_tag("fillin/update", "id=myform name=myform"); ?>
  <label for="first_name">First name:</label><?php echo input_tag('first_name') ?><br/>
  <label for="last_name">Last name:</label><?php echo input_tag('last_name') ?><br/>

  <?php echo submit_tag('update') ?>
</form>
