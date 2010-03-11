<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class defines the interface for interacting with data, as well
 * as default implementations.
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfData.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */

abstract class sfData
{
  protected
    $deleteCurrentData = true,
    $object_references = array();

  /**
   * Sets a flag to indicate if the current data in the database
   * should be deleted before new data is loaded.
   *
   * @param boolean $boolean The flag value
   */
  public function setDeleteCurrentData($boolean)
  {
    $this->deleteCurrentData = $boolean;
  }

  /**
   * Gets the current value of the flag that indicates whether
   * current data is to be deleted or not.
   *
   * @returns boolean
   */
  public function getDeleteCurrentData()
  {
    return $this->deleteCurrentData;
  }

  /**
   * Loads data for the database from a YAML file
   *
   * @param string $file The path to the YAML file.
   */
  protected function doLoadDataFromFile($file)
  {
    // import new datas
    $data = sfYaml::load($file);

    $this->loadDataFromArray($data);
  }

  /**
   * Manages the insertion of data into the data source
   *
   * @param array $data The data to be inserted into the data source
   */
  abstract public function loadDataFromArray($data);

  /**
   * Manages reading all of the fixture data files and
   * loading them into the data source
   *
   * @param array $files The path names of the YAML data files
   */
  protected function doLoadData($files)
  {
    $this->object_references = array();
    $this->maps = array();

    foreach ($files as $file)
    {
      $this->doLoadDataFromFile($file);
    }
  }

  /**
   * Gets a list of one or more *.yml files and returns the list in an array.
   *
   * The returned array of files is sorted by alphabetical order.
   *
   * @param string|array $element A directory or file name or an array of directories and/or file names
   *                              If null, then defaults to 'sf_data_dir'/fixtures
   *
   * @return array A list of *.yml files
   *
   * @throws sfInitializationException If the directory or file does not exist.
   */
  public function getFiles($element = null)
  {
    if (is_null($element))
    {
      $element = sfConfig::get('sf_data_dir').'/fixtures';
    }

    $files = array();
    if (is_array($element))
    {
      foreach ($element as $e)
      {
        $files = array_merge($files, $this->getFiles($e));
      }
    }
    else if (is_file($element))
    {
      $files[] = $element;
    }
    else if (is_dir($element))
    {
      $files = sfFinder::type('file')->name('*.yml')->sort_by_name()->in($element);
    }
    else
    {
      throw new sfInitializationException(sprintf('You must give an array, a directory or a file to sfData::getFiles() (%s given).', $element));
    }

    $files = array_unique($files);
    sort($files);

    return $files;
  }
}
