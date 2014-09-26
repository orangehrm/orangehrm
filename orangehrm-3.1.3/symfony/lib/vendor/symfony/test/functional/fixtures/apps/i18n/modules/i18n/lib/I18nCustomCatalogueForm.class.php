<?php
class I18nCustomCatalogueForm extends I18nForm
{
  public function configure()
  {
    parent::configure();
    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('custom');
  }
}
