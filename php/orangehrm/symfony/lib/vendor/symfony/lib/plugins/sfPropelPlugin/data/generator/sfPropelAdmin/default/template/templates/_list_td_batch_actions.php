<?php if ($this->getParameterValue('list.batch_actions')): ?>
<td>
[?php echo checkbox_tag('sf_admin_batch_selection[]', $<?php echo $this->getSingularName() ?>->getPrimaryKey(), 0, array('class' => 'sf_admin_batch_checkbox')) ?]
</td>
<?php endif; ?>
