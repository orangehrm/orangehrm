<?php

class sfRequestCompat10
{
  /**
   * Adds 1.0 compatibility methods to the request object.
   *
   * @param sfEvent $event The event
   *
   * @return Boolean true if the method has been found here, false otherwise
   */
  static public function call(sfEvent $event)
  {
    if (!in_array($event['method'], array(
      'getError', 'getErrorNames', 'getErrors', 'hasError', 'hasErrors', 'removeError', 'setError', 'setErrors',
      'getFile', 'getFileError', 'getFileName', 'getFileNames', 'getFilePath', 'getFileSize',
      'getFileType', 'hasFile', 'hasFileError', 'hasFileErrors', 'hasFiles', 'getFileValue', 'getFileValues',
      'getFileExtension', 'moveFile'
    )))
    {
      return false;
    }

    $event->setReturnValue(call_user_func_array(array('sfRequestCompat10', $event['method']), array_merge(array($event->getSubject()), $event['arguments'])));

    return true;
  }

  /**
   * Retrieves an error message.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  An error name
   *
   * @return string An error message, if the error exists, otherwise null
   */
  static public function getError($request, $name)
  {
    return $request->getAttribute('errors['.$name.']');
  }

  /**
   * Retrieves an array of error names.
   *
   * @param  sfRequest $request A request object
   *
   * @return array An indexed array of error names
   */
  static public function getErrorNames($request)
  {
    return array_keys($request->getAttribute('errors', array()));
  }

  /**
   * Retrieves an array of errors.
   *
   * @param  sfRequest $request A request object
   *
   * @return array An associative array of errors
   */
  static public function getErrors($request)
  {
    return $request->getAttribute('errors', array());
  }

  /**
   * Indicates whether or not an error exists.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  An error name
   *
   * @return bool true, if the error exists, otherwise false
   */
  static public function hasError($request, $name)
  {
    return $request->hasAttribute('errors['.$name.']');
  }

  /**
   * Indicates whether or not any errors exist.
   *
   * @param  sfRequest $request A request object
   *
   * @return bool true, if any error exist, otherwise false
   */
  static public function hasErrors($request)
  {
    return count($request->getAttribute('errors', array())) > 0;
  }

  /**
   * Removes an error.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  An error name
   *
   * @return string An error message, if the error was removed, otherwise null
   */
  static public function removeError($request, $name)
  {
    return $request->getAttributeHolder()->remove('errors['.$name.']');
  }

  /**
   * Sets an error.
   *
   * @param  sfRequest $request A request object
   * @param string $name     An error name
   * @param string $message  An error message
   *
   */
  static public function setError($request, $name, $message)
  {
    $errors = $request->getAttribute('errors', array());
    $errors[$name] = $message;

    $request->setAttribute('errors', $errors);
  }

  /**
   * Sets an array of errors
   *
   * If an existing error name matches any of the keys in the supplied
   * array, the associated message will be overridden.
   *
   * @param  sfRequest $request A request object
   * @param array $erros An associative array of errors and their associated messages
   *
   */
  static public function setErrors($request, $errors)
  {
    $request->setAttribute('errors', array_merge($request->getAttribute('errors', array()), $errors));
  }

  /**
   * Retrieves an array of file information.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return array An associative array of file information, if the file exists, otherwise null
   */
  static public function getFile($request, $name)
  {
    return self::hasFile($request, $name) ? self::getFileValues($request, $name) : null;
  }

  /**
   * Retrieves a file error.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return int One of the following error codes:
   *
   *             - <b>UPLOAD_ERR_OK</b>        (no error)
   *             - <b>UPLOAD_ERR_INI_SIZE</b>  (the uploaded file exceeds the
   *                                           upload_max_filesize directive
   *                                           in php.ini)
   *             - <b>UPLOAD_ERR_FORM_SIZE</b> (the uploaded file exceeds the
   *                                           MAX_FILE_SIZE directive that
   *                                           was specified in the HTML form)
   *             - <b>UPLOAD_ERR_PARTIAL</b>   (the uploaded file was only
   *                                           partially uploaded)
   *             - <b>UPLOAD_ERR_NO_FILE</b>   (no file was uploaded)
   */
  static public function getFileError($request, $name)
  {
    return self::hasFile($request, $name) ? self::getFileValue($request, $name, 'error') : UPLOAD_ERR_NO_FILE;
  }

  /**
   * Retrieves a file name.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file nam.
   *
   * @return string A file name, if the file exists, otherwise null
   */
  static public function getFileName($request, $name)
  {
    return self::hasFile($request, $name) ? self::getFileValue($request, $name, 'name') : null;
  }

  /**
   * Retrieves an array of file names.
   *
   * @param  sfRequest $request A request object
   *
   * @return array An indexed array of file names
   */
  static public function getFileNames($request)
  {
    return array_keys($_FILES);
  }

  /**
   * Retrieves a file path.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return string A file path, if the file exists, otherwise null
   */
  static public function getFilePath($request, $name)
  {
    return self::hasFile($request, $name) ? self::getFileValue($request, $name, 'tmp_name') : null;
  }

  /**
   * Retrieve a file size.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return int A file size, if the file exists, otherwise null
   */
  static public function getFileSize($request, $name)
  {
    return self::hasFile($request, $name) ? self::getFileValue($request, $name, 'size') : null;
  }

  /**
   * Retrieves a file type.
   *
   * This may not be accurate. This is the mime-type sent by the browser
   * during the upload.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return string A file type, if the file exists, otherwise null
   */
  static public function getFileType($request, $name)
  {
    return self::hasFile($request, $name) ? self::getFileValue($request, $name, 'type') : null;
  }

  /**
   * Indicates whether or not a file exists.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return bool true, if the file exists, otherwise false
   */
  static public function hasFile($request, $name)
  {
    if (preg_match('/^(.+?)\[(.+?)\]$/', $name, $match))
    {
      return isset($_FILES[$match[1]]['name'][$match[2]]);
    }
    else
    {
      return isset($_FILES[$name]);
    }
  }

  /**
   * Indicates whether or not a file error exists.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return bool true, if the file error exists, otherwise false
   */
  static public function hasFileError($request, $name)
  {
    return self::hasFile($request, $name) ? (self::getFileValue($request, $name, 'error') != UPLOAD_ERR_OK) : false;
  }

  /**
   * Indicates whether or not any file errors occured.
   *
   * @param  sfRequest $request A request object
   *
   * @return bool true, if any file errors occured, otherwise false
   */
  static public function hasFileErrors($request)
  {
    foreach (self::getFileNames($request) as $name)
    {
      if (self::hasFileError($request, $name) === true)
      {
        return true;
      }
    }

    return false;
  }

  /**
   * Indicates whether or not any files exist.
   *
   * @param  sfRequest $request A request object
   *
   * @return boolean true, if any files exist, otherwise false
   */
  static public function hasFiles($request)
  {
    return (count($_FILES) > 0);
  }

  /**
   * Retrieves a file value.
   *
   * @param  sfRequest $request A request object
   * @param string $name A file name
   * @param string $key Value to search in the file
   * 
   * @return string File value
   */
  static public function getFileValue($request, $name, $key)
  {
    if (preg_match('/^(.+?)\[(.+?)\]$/', $name, $match))
    {
      return $_FILES[$match[1]][$key][$match[2]];
    }
    else
    {
      return $_FILES[$name][$key];
    }
  }

  /**
   * Retrieves all the values from a file.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return array Associative list of the file values
   */
  static public function getFileValues($request, $name)
  {
    if (preg_match('/^(.+?)\[(.+?)\]$/', $name, $match))
    {
      return array(
        'name'     => $_FILES[$match[1]]['name'][$match[2]],
        'type'     => $_FILES[$match[1]]['type'][$match[2]],
        'tmp_name' => $_FILES[$match[1]]['tmp_name'][$match[2]],
        'error'    => $_FILES[$match[1]]['error'][$match[2]],
        'size'     => $_FILES[$match[1]]['size'][$match[2]],
      );
    }
    else
    {
      return $_FILES[$name];
    }
  }

  /**
   * Retrieves an extension for a given file.
   *
   * @param  sfRequest $request A request object
   * @param  string $name  A file name
   *
   * @return string Extension for the file
   */
  static public function getFileExtension($request, $name)
  {
    static $mimeTypes = null;

    $fileType = self::getFileType($request, $name);

    if (!$fileType)
    {
      return '.bin';
    }

    if (is_null($mimeTypes))
    {
      $mimeTypes = unserialize(file_get_contents(sfConfig::get('sf_symfony_lib_dir').'/plugins/sfCompat10Plugin/data/mime_types.dat'));
    }

    return isset($mimeTypes[$fileType]) ? '.'.$mimeTypes[$fileType] : '.bin';
  }

  /**
   * Moves an uploaded file.
   *
   * @param  sfRequest $request A request object
   * @param string $name      A file name
   * @param string $file      An absolute filesystem path to where you would like the
   *                          file moved. This includes the new filename as well, since
   *                          uploaded files are stored with random names
   * @param int    $fileMode  The octal mode to use for the new file
   * @param bool   $create    Indicates that we should make the directory before moving the file
   * @param int    $dirMode   The octal mode to use when creating the directory
   *
   * @return bool true, if the file was moved, otherwise false
   *
   * @throws <b>sfFileException</b> If a major error occurs while attempting to move the file
   */
  static public function moveFile($request, $name, $file, $fileMode = 0666, $create = true, $dirMode = 0777)
  {
    if (self::hasFile($request, $name) && self::getFileValue($request, $name, 'error') == UPLOAD_ERR_OK && self::getFileValue($request, $name, 'size') > 0)
    {
      // get our directory path from the destination filename
      $directory = dirname($file);

      if (!is_readable($directory))
      {
        $fmode = 0777;

        if ($create && !@mkdir($directory, $dirMode, true))
        {
          // failed to create the directory
          throw new sfFileException(sprintf('Failed to create file upload directory "%s".', $directory));
        }

        // chmod the directory since it doesn't seem to work on
        // recursive paths
        @chmod($directory, $dirMode);
      }
      else if (!is_dir($directory))
      {
        // the directory path exists but it's not a directory
        throw new sfFileException(sprintf('File upload path "%s" exists, but is not a directory.', $directory));
      }
      else if (!is_writable($directory))
      {
        // the directory isn't writable
        throw new sfFileException(sprintf('File upload path "%s" is not writable.', $directory));
      }

      if (@move_uploaded_file(self::getFileValue($request, $name, 'tmp_name'), $file))
      {
        // chmod our file
        @chmod($file, $fileMode);

        return true;
      }
    }

    return false;
  }
}
