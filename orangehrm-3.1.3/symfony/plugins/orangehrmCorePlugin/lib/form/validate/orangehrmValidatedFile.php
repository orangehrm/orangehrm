<?php

/**
 * subclass of sfValidatorFile that allows you to set the filetype.
 */
class orangehrmValidatedFile extends sfValidatedFile {
    
    /**
    * Sets the file content type.
    *
    * @return string The content type
    */
    public function setType($fileType) {
        $this->type = $fileType;
    }
}

