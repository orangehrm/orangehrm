<?php

/**
 * sfMessageSource_File class file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * Copyright(c) 2004 by Qiang Xue. All rights reserved.
 *
 * To contact the author write to {@link mailto:qiang.xue@gmail.com Qiang Xue}
 * The latest version of PRADO can be obtained from:
 * {@link http://prado.sourceforge.net/}
 *
 * @author     Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version    $Id: sfMessageSource_File.class.php 9128 2008-05-21 00:58:19Z Carl.Vondrick $
 * @package    symfony
 * @subpackage i18n
 */

/**
 * sfMessageSource_File class.
 *
 * This is the base class for file based message sources like XLIFF or gettext.
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Fri Dec 24 16:18:44 EST 2004
 * @package    symfony
 * @subpackage i18n
 */
abstract class sfMessageSource_File extends sfMessageSource
{
  /**
   * Separator between culture name and source.
   * @var string
   */
  protected $dataSeparator = '.';

  /**
   * Constructor.
   *
   * @param string $source the directory where the messages are stored.
   * @see MessageSource::factory();
   */
  function __construct($source)
  {
    $this->source = (string) $source;
  }

  /**
   * Gets the last modified unix-time for this particular catalogue+variant.
   * Just use the file modified time.
   *
   * @param string $source catalogue+variant
   * @return int last modified in unix-time format.
   */
  public function getLastModified($source)
  {
    return is_file($source) ? filemtime($source) : 0;
  }

  /**
   * Gets the message file for a specific message catalogue and cultural variant.
   *
   * @param string $variant message catalogue
   * @return string full path to the message file.
   */
  public function getSource($variant)
  {
    return $this->source.'/'.$variant;
  }

  /**
   * Determines if the message file source is valid.
   *
   * @param string $source message file
   * @return boolean true if valid, false otherwise.
   */
  public function isValidSource($source)
  {
    return is_file($source);
  }

  /**
   * Gets all the variants of a particular catalogue.
   *
   * @param string $catalogue catalogue name
   * @return array list of all variants for this catalogue.
   */
  public function getCatalogueList($catalogue)
  {
    $variants = explode('_', $this->culture);
    $source = $catalogue.$this->dataExt;

    $catalogues = array($source);

    $variant = null;

    for ($i = 0, $max = count($variants); $i < $max; $i++)
    {
      if (strlen($variants[$i]) > 0)
      {
        $variant .= $variant ? '_'.$variants[$i] : $variants[$i];
        $catalogues[] = $catalogue.$this->dataSeparator.$variant.$this->dataExt;
      }
    }

    $byDir = $this->getCatalogueByDir($catalogue);
    $catalogues = array_merge($byDir, array_reverse($catalogues));

    return $catalogues;
  }

  /**
   * Traverses through the directory structure to find the catalogues.
   * This should only be called by getCatalogueList()
   *
   * @param string $catalogue a particular catalogue.
   * @return array a list of catalogues.
   * @see getCatalogueList()
   */
  protected function getCatalogueByDir($catalogue)
  {
    $variants = explode('_', $this->culture);
    $catalogues = array();

    $variant = null;

    for ($i = 0, $max = count($variants); $i < $max; $i++)
    {
      if (strlen($variants[$i]) > 0)
      {
        $variant .= $variant ? '_'.$variants[$i] : $variants[$i];
        $catalogues[] = $variant.'/'.$catalogue.$this->dataExt;
      }
    }

    return array_reverse($catalogues);
  }

  /**
   * Returns a list of catalogue and its culture ID.
   * E.g. array('messages', 'en_AU')
   *
   * @return array list of catalogues
   * @see getCatalogues()
   */
  public function catalogues()
  {
    return $this->getCatalogues();
  }

  /**
   * Returns a list of catalogue and its culture ID. This takes care
   * of directory structures.
   * E.g. array('messages', 'en_AU')
   *
   * @return array list of catalogues
   */
  protected function getCatalogues($dir = null, $variant = null)
  {
    $dir = $dir ? $dir : $this->getSource($variant);
    $files = scandir($dir);

    $catalogue = array();

    foreach ($files as $file)
    {
      if (is_dir($dir.'/'.$file) && preg_match('/^[a-z]{2}(_[A-Z]{2,3})?$/', $file))
      {
        $catalogue = array_merge($catalogue, $this->getCatalogues($dir.'/'.$file, $file));
      }

      $pos = strpos($file, $this->dataExt);
      if ($pos > 0 && substr($file, -1 * strlen($this->dataExt)) == $this->dataExt)
      {
        $name = substr($file, 0, $pos);
        $dot = strrpos($name, $this->dataSeparator);
        $culture = $variant;
        $cat = $name;
        if (is_int($dot))
        {
          $culture = substr($name, $dot + 1,strlen($name));
          $cat = substr($name, 0, $dot);
        }
        $details[0] = $cat;
        $details[1] = $culture;

        $catalogue[] = $details;
      }
    }
    sort($catalogue);

    return $catalogue;
  }

  public function getId()
  {
    return md5($this->source);
  }
}
