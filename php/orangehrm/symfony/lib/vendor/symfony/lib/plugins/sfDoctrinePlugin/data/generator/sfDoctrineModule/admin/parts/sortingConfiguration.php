  public function getDefaultSort()
  {
<?php if ($sort = (isset($this->config['list']['sort']) ? $this->config['list']['sort'] : false)): ?>
<?php if (!is_array($sort)) $sort = array($sort, 'asc'); ?>
    return array('<?php echo $sort[0] ?>', '<?php echo isset($sort[1]) ? $sort[1] : 'asc' ?>');
<?php else: ?>
    return array(null, null);
<?php endif; ?>
<?php unset($this->config['list']['sort']) ?>
  }
