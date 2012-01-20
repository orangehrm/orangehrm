[?php if ($value): ?]
  [?php echo image_tag(sfConfig::get('sf_admin_module_web_dir').'/images/tick.png', array('alt' => __('Checked', array(), 'sf_admin'), 'title' => __('Checked', array(), 'sf_admin'))) ?]
[?php else: ?]
  &nbsp;
[?php endif; ?]
