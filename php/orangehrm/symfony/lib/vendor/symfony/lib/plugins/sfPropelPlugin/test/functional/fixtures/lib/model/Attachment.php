<?php

/**
 * Subclass for representing a row from the 'attachment' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Attachment extends BaseAttachment
{
  public function generateFileFilename($file)
  {
    return 'uploaded'.$file->getExtension($file->getOriginalExtension());
  }
}
