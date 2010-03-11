<?php

/**
 * sfMessageSource_gettext class file.
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
 * @version    $Id: sfMessageSource_gettext.class.php 9128 2008-05-21 00:58:19Z Carl.Vondrick $
 * @package    symfony
 * @subpackage i18n
 */

/**
 * sfMessageSource_gettext class.
 * 
 * Using Gettext MO format as the message source for translation.
 * The gettext classes are based on PEAR's gettext MO and PO classes.
 * 
 * See the MessageSource::factory() method to instantiate this class.
 * 
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Fri Dec 24 16:18:44 EST 2004
 * @package    symfony
 * @subpackage i18n
 */
class sfMessageSource_gettext extends sfMessageSource_File
{ 
  /**
   * Message data filename extension.
   * @var string 
   */
  protected $dataExt = '.mo';

  /**
   * PO data filename extension
   * @var string 
   */
  protected $poExt = '.po';

  /**
   * Loads the messages from a MO file.
   *
   * @param string $filename MO file.
   * @return array of messages.
   */
  public function &loadData($filename)
  {
    $mo = TGettext::factory('MO',$filename);
    $mo->load();
    $result = $mo->toArray();

    $results = array();
    $count = 0;
    foreach ($result['strings'] as $source => $target)
    {
      $results[$source][] = $target;  //target
      $results[$source][] = $count++; //id
      $results[$source][] = '';       //comments
    }

    return $results;
  }

  /**
   * Gets the variant for a catalogue depending on the current culture.
   *
   * @param string $catalogue catalogue
   * @return string the variant. 
   * @see save()
   * @see update()
   * @see delete()
   */
  protected function getVariants($catalogue = 'messages')
  {
    if (empty($catalogue))
    {
      $catalogue = 'messages';
    }

    foreach ($this->getCatalogueList($catalogue) as $variant)
    {
      $file = $this->getSource($variant);
      $po = $this->getPOFile($file);
      if (is_file($file) || is_file($po))
      {
        return array($variant, $file, $po);
      }
    }

    return false;
  }

  protected function getPOFile($MOFile)
  {
    return substr($MOFile, 0, strlen($MOFile) - strlen($this->dataExt)).$this->poExt;
  }

  /**
   * Saves the list of untranslated blocks to the translation source. 
   * If the translation was not found, you should add those
   * strings to the translation source via the <b>append()</b> method.
   *
   * @param string $catalogue the catalogue to add to
   * @return boolean true if saved successfuly, false otherwise.   
   */
  function save($catalogue = 'messages')
  {
    $messages = $this->untranslated;

    if (count($messages) <= 0)
    {
      return false;
    }

    $variants = $this->getVariants($catalogue);

    if ($variants)
    {
      list($variant, $MOFile, $POFile) = $variants;
    }
    else
    {
      list($variant, $MOFile, $POFile) = $this->createMessageTemplate($catalogue);
    }

    if (is_writable($MOFile) == false)
    {
      throw new sfException(sprintf("Unable to save to file %s, file must be writable.", $MOFile));
    }
    if (is_writable($POFile) == false)
    {
      throw new sfException(sprintf("Unable to save to file %s, file must be writable.", $POFile));
    }

    // set the strings as untranslated.
    $strings = array();
    foreach ($messages as $message)
    {
      $strings[$message] = ''; 
    }

    // load the PO
    $po = TGettext::factory('PO',$POFile);
    $po->load();
    $result = $po->toArray();

    $existing = count($result['strings']);

    // add to strings to the existing message list
    $result['strings'] = array_merge($result['strings'],$strings);

    $new = count($result['strings']);

    if ($new > $existing)
    {
      // change the date 2004-12-25 12:26
      $result['meta']['PO-Revision-Date'] = @date('Y-m-d H:i:s');

      $po->fromArray($result);
      $mo = $po->toMO();
      if ($po->save() && $mo->save($MOFile))
      {
        if ($this->cache)
        {
          $this->cache->remove($variant.':'.$this->culture);
        }

        return true;
      }
      else
      {
        return false;
      }
    }

    return false;
  }

  /**
   * Deletes a particular message from the specified catalogue.
   *
   * @param string $message   the source message to delete.
   * @param string $catalogue the catalogue to delete from.
   * @return boolean true if deleted, false otherwise. 
   */
  function delete($message, $catalogue = 'messages')
  {
    $variants = $this->getVariants($catalogue);
    if ($variants)
    {
      list($variant, $MOFile, $POFile) = $variants;
    }
    else
    {
      return false;
    }

    if (is_writable($MOFile) == false)
    {
      throw new sfException(sprintf("Unable to modify file %s, file must be writable.", $MOFile));
    }

    if (is_writable($POFile) == false)
    {
      throw new sfException(sprintf("Unable to modify file %s, file must be writable.", $POFile));
    }

    $po = TGettext::factory('PO', $POFile);
    $po->load();
    $result = $po->toArray(); 

    foreach ($result['strings'] as $string => $value)
    {
      if ($string == $message)
      {
        $result['meta']['PO-Revision-Date'] = @date('Y-m-d H:i:s');
        unset($result['strings'][$string]);

        $po->fromArray($result);
        $mo = $po->toMO();
        if ($po->save() && $mo->save($MOFile))
        {
          if ($this->cache)
          {
            $this->cache->remove($variant.':'.$this->culture);
          }

          return true;
        }
        else
        {
          return false;
        }
      }
    }

    return false;
  }

  /**
   * Updates the translation.
   *
   * @param string $text      the source string.
   * @param string $target    the new translation string.
   * @param string $comments  comments
   * @param string $catalogue the catalogue of the translation.
   * @return boolean true if translation was updated, false otherwise.
   */
  function update($text, $target, $comments, $catalogue = 'messages')
  {
    $variants = $this->getVariants($catalogue);
    if ($variants)
    {
      list($variant, $MOFile, $POFile) = $variants;
    }
    else
    {
      return false;
    }

    if (is_writable($MOFile) == false)
    {
      throw new sfException(sprintf("Unable to update file %s, file must be writable.", $MOFile));
    }

    if (is_writable($POFile) == false)
    {
      throw new sfException(sprintf("Unable to update file %s, file must be writable.", $POFile));
    }

    $po = TGettext::factory('PO',$POFile);
    $po->load();
    $result = $po->toArray();

    foreach ($result['strings'] as $string => $value)
    {
      if ($string == $text)
      {
        $result['strings'][$string] = $target;
        $result['meta']['PO-Revision-Date'] = @date('Y-m-d H:i:s');

        $po->fromArray($result);
        $mo = $po->toMO();

        if ($po->save() && $mo->save($MOFile))
        {
          if ($this->cache)
          {
            $this->cache->remove($variant.':'.$this->culture);
          }

          return true;
        }
        else
        {
          return false;
        }
      }
    }

    return false;
  }

  protected function createMessageTemplate($catalogue)
  {
    if (is_null($catalogue))
    {
      $catalogue = 'messages';
    }

    $variants = $this->getCatalogueList($catalogue);
    $variant = array_shift($variants);
    $mo_file = $this->getSource($variant);
    $po_file = $this->getPOFile($mo_file);

    $dir = dirname($mo_file);
    if (!is_dir($dir))
    {
      @mkdir($dir);
      @chmod($dir, 0777);
    }

    if (!is_dir($dir))
    {
      throw new sfException(sprintf("Unable to create directory %s.", $dir));
    }

    $po = TGettext::factory('PO', $po_file);
    $result['meta']['PO-Revision-Date'] = date('Y-m-d H:i:s');
    $result['strings'] = array();

    $po->fromArray($result);
    $mo = $po->toMO();
    if ($po->save() && $mo->save($mo_file))
    {
      return array($variant, $mo_file, $po_file);
    }
    else
    {
      throw new sfException(sprintf("Unable to create file %s and %s.", $po_file, $mo_file));
    }
  }
}
