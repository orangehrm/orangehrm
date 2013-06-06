  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $<?php echo $this->getSingularName() ?> = $form->save();

<?php if (isset($this->params['route_prefix']) && $this->params['route_prefix']): ?>
      $this->redirect('@<?php echo $this->getUrlForAction('edit') ?>?<?php echo $this->getPrimaryKeyUrlParams() ?>);
<?php else: ?>
      $this->redirect('<?php echo $this->getModuleName() ?>/edit?<?php echo $this->getPrimaryKeyUrlParams() ?>);
<?php endif; ?>
    }
  }
