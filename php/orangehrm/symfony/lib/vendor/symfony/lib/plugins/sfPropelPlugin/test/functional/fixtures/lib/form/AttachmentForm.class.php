<?php

/**
 * Attachment form.
 *
 * @package    form
 * @subpackage attachment
 * @version    SVN: $Id: AttachmentForm.class.php 11445 2008-09-11 14:19:28Z fabien $
 */
class AttachmentForm extends BaseAttachmentForm
{
  public function configure()
  {
    $this->widgetSchema['file'] = new sfWidgetFormInputFile();

    $fileValidator = new sfValidatorFile(array('path' => sfConfig::get('sf_cache_dir')));
    $fileValidator->setOption('mime_type_guessers', array());
    $this->validatorSchema['file'] = $fileValidator;
  }
}
