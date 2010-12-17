<?php

/**
 * Attachment form.
 *
 * @package    form
 * @subpackage attachment
 * @version    SVN: $Id: AttachmentForm.class.php 24068 2009-11-17 06:39:35Z Kris.Wallsmith $
 */
class AttachmentForm extends BaseAttachmentForm
{
  public function configure()
  {
    $this->widgetSchema['file'] = new sfWidgetFormInputFile();
    $this->validatorSchema['file'] = new sfValidatorFile(array(
      'path' => sfConfig::get('sf_cache_dir'),
      'mime_type_guessers' => array(),
    ));
  }
}
