<h1>Form validation tests</h1>

<ul class="errors">
<?php foreach ($sf_request->getErrorNames() as $name): ?>
  <li class="<?php echo $name ?>"><?php echo $sf_request->getError($name) ?></li>
<?php endforeach; ?>
</ul>

<?php echo form_tag('validation/index') ?>
  <?php echo input_tag('fake') ?>
  <?php echo input_tag('id', 1) ?>
  <?php echo input_password_tag('password', '') ?>
  <?php echo input_tag('article[title]', 'title') ?>
  <?php echo textarea_tag('article[body]', 'body') ?>
  <?php echo submit_tag('submit') ?>
</form>
