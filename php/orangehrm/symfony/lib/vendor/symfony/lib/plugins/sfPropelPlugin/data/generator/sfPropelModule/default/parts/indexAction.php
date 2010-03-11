  public function executeIndex(sfWebRequest $request)
  {
<?php if (isset($this->params['with_propel_route']) && $this->params['with_propel_route']): ?>
    $this-><?php echo $this->getPluralName() ?> = $this->getRoute()->getObjects();
<?php else: ?>
    $this-><?php echo $this->getPluralName() ?> = <?php echo constant($this->getModelClass().'::PEER') ?>::doSelect(new Criteria());
<?php endif; ?>
  }
