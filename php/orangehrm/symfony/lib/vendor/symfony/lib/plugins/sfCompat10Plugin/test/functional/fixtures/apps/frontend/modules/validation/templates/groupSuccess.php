<?php echo use_helper('Validation') ?>

<ul class="errors">
<?php foreach ($sf_request->getErrorNames() as $name): ?>
  <li class="<?php echo $name ?>"><?php echo $sf_request->getError($name) ?></li>
<?php endforeach; ?>
</ul>

<?php echo form_tag('validation/group') ?>
  input1: <?php echo input_tag('input1') ?><br/>
  input2: <?php echo input_tag('input2') ?><br/>
  input3: <?php echo input_tag('input3') ?><br/>
  input4: <?php echo input_tag('input4') ?><br/>
  <?php echo submit_tag('submit') ?>
</form>
