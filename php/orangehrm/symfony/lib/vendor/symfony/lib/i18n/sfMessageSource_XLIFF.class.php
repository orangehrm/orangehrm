<?php

/**
 * sfMessageSource_XLIFF class file.
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
 * @version    $Id: sfMessageSource_XLIFF.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 * @package    symfony
 * @subpackage i18n
 */

/**
 * sfMessageSource_XLIFF class.
 *
 * Using XML XLIFF format as the message source for translation.
 * Details and example of XLIFF can be found in the following URLs.
 *
 * # http://www.opentag.com/xliff.htm
 * # http://www-106.ibm.com/developerworks/xml/library/x-localis2/
 *
 * See the MessageSource::factory() method to instantiate this class.
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Fri Dec 24 16:18:44 EST 2004
 * @package    symfony
 * @subpackage i18n
 */
class sfMessageSource_XLIFF extends sfMessageSource_File
{
  /**
   * Message data filename extension.
   * @var string
   */
  protected $dataExt = '.xml';

  /**
   * Loads the messages from a XLIFF file.
   *
   * @param string $filename  XLIFF file.
   * @return array|false An array of messages or false if there was a problem loading the file.
   */
  public function &loadData($filename)
  {
    libxml_use_internal_errors(true);
    if (!$xml = simplexml_load_file($filename))
    {
      $error = false;

      return $error;
    }
    libxml_use_internal_errors(false);

    $translationUnit = $xml->xpath('//trans-unit');

    $translations = array();

    foreach ($translationUnit as $unit)
    {
      $source = (string) $unit->source;
      $translations[$source][] = (string) $unit->target;
      $translations[$source][] = (string) $unit['id'];
      $translations[$source][] = (string) $unit->note;
    }

    return $translations;
  }

  /**
   * Creates and returns a new DOMDocument instance
   *
   * @param  string  $xml  XML string
   *
   * @return DOMDocument
   */
  protected function createDOMDocument($xml = null)
  {
    $domimp = new DOMImplementation();
    $doctype = $domimp->createDocumentType('xliff', '-//XLIFF//DTD XLIFF//EN', 'http://www.oasis-open.org/committees/xliff/documents/xliff.dtd');
    $dom = $domimp->createDocument('', '', $doctype);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;

    if (null !== $xml && is_string($xml))
    {
      // Add header for XML with UTF-8
      if (!preg_match('/<\?xml/', $xml))
      {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".$xml;
      }

      $dom->loadXML($xml);
    }

    return $dom;
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
    if (null === $catalogue)
    {
      $catalogue = 'messages';
    }

    foreach ($this->getCatalogueList($catalogue) as $variant)
    {
      $file = $this->getSource($variant);
      if (is_file($file))
      {
        return array($variant, $file);
      }
    }

    return false;
  }

  /**
   * Saves the list of untranslated blocks to the translation source.
   * If the translation was not found, you should add those
   * strings to the translation source via the <b>append()</b> method.
   *
   * @param string $catalogue the catalogue to add to
   * @return boolean true if saved successfuly, false otherwise.
   */
  public function save($catalogue = 'messages')
  {
    $messages = $this->untranslated;
    if (count($messages) <= 0)
    {
      return false;
    }

    $variants = $this->getVariants($catalogue);
    if ($variants)
    {
      list($variant, $filename) = $variants;
    }
    else
    {
      list($variant, $filename) = $this->createMessageTemplate($catalogue);
    }

    if (is_writable($filename) == false)
    {
      throw new sfException(sprintf("Unable to save to file %s, file must be writable.", $filename));
    }

    // create a new dom, import the existing xml
    $dom = $this->createDOMDocument();
    @$dom->load($filename);

    // find the body element
    $xpath = new DomXPath($dom);
    $body = $xpath->query('//body')->item(0);

    if (null === $body)
    {
      //create and try again
      $this->createMessageTemplate($catalogue);
      $dom->load($filename);
      $xpath = new DomXPath($dom);
      $body = $xpath->query('//body')->item(0);
    }

    // find the biggest "id" used
    $lastNodes = $xpath->query('//trans-unit[not(@id <= preceding-sibling::trans-unit/@id) and not(@id <= following-sibling::trans-unit/@id)]');
    if (null !== $last = $lastNodes->item(0))
    {
      $count = intval($last->getAttribute('id'));
    }
    else
    {
      $count = 0;
    }

    // for each message add it to the XML file using DOM
    foreach ($messages as $message)
    {
      $unit = $dom->createElement('trans-unit');
      $unit->setAttribute('id', ++$count);

      $source = $dom->createElement('source');
      $source->appendChild($dom->createTextNode($message));
      $target = $dom->createElement('target');
      $target->appendChild($dom->createTextNode(''));

      $unit->appendChild($source);
      $unit->appendChild($target);

      $body->appendChild($unit);
    }

    $fileNode = $xpath->query('//file')->item(0);
    $fileNode->setAttribute('date', @date('Y-m-d\TH:i:s\Z'));

    $dom = $this->createDOMDocument($dom->saveXML());

    // save it and clear the cache for this variant
    $dom->save($filename);
    if ($this->cache)
    {
      $this->cache->remove($variant.':'.$this->culture);
    }

    return true;
  }

  /**
   * Updates the translation.
   *
   * @param string $text      the source string.
   * @param string $target    the new translation string.
   * @param string $comments  comments
   * @param string $catalogue the catalogue to save to.
   * @return boolean true if translation was updated, false otherwise.
   */
  public function update($text, $target, $comments, $catalogue = 'messages')
  {
    $variants = $this->getVariants($catalogue);
    if ($variants)
    {
      list($variant, $filename) = $variants;
    }
    else
    {
      return false;
    }

    if (is_writable($filename) == false)
    {
      throw new sfException(sprintf("Unable to update file %s, file must be writable.", $filename));
    }

    // create a new dom, import the existing xml
    $dom = $this->createDOMDocument();
    $dom->load($filename);

    // find the body element
    $xpath = new DomXPath($dom);
    $units = $xpath->query('//trans-unit');

    // for each of the existin units
    foreach ($units as $unit)
    {
      $found = false;
      $targetted = false;
      $commented = false;

      //in each unit, need to find the source, target and comment nodes
      //it will assume that the source is before the target.
      foreach ($unit->childNodes as $node)
      {
        // source node
        if ($node->nodeName == 'source' && $node->firstChild->wholeText == $text)
        {
          $found = true;
        }

        // found source, get the target and notes
        if ($found)
        {
          // set the new translated string
          if ($node->nodeName == 'target')
          {
            $node->nodeValue = $target;
            $targetted = true;
          }

          // set the notes
          if (!empty($comments) && $node->nodeName == 'note')
          {
            $node->nodeValue = $comments;
            $commented = true;
          }
        }
      }

      // append a target
      if ($found && !$targetted)
      {
        $targetNode = $dom->createElement('target');
        $targetNode->appendChild($dom->createTextNode($target));
        $unit->appendChild($targetNode);
      }

      // append a note
      if ($found && !$commented && !empty($comments))
      {
        $commentsNode = $dom->createElement('note');
        $commentsNode->appendChild($dom->createTextNode($comments));
        $unit->appendChild($commentsNode);
      }

      // finished searching
      if ($found)
      {
        break;
      }
    }

    $fileNode = $xpath->query('//file')->item(0);
    $fileNode->setAttribute('date', @date('Y-m-d\TH:i:s\Z'));

    if ($dom->save($filename) > 0)
    {
      if ($this->cache)
      {
        $this->cache->remove($variant.':'.$this->culture);
      }

      return true;
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
  public function delete($message, $catalogue='messages')
  {
    $variants = $this->getVariants($catalogue);
    if ($variants)
    {
      list($variant, $filename) = $variants;
    }
    else
    {
      return false;
    }

    if (is_writable($filename) == false)
    {
      throw new sfException(sprintf("Unable to modify file %s, file must be writable.", $filename));
    }

    // create a new dom, import the existing xml
    $dom = $this->createDOMDocument();
    $dom->load($filename);

    // find the body element
    $xpath = new DomXPath($dom);
    $units = $xpath->query('//trans-unit');

    // for each of the existin units
    foreach ($units as $unit)
    {
      //in each unit, need to find the source, target and comment nodes
      //it will assume that the source is before the target.
      foreach ($unit->childNodes as $node)
      {
        // source node
        if ($node->nodeName == 'source' && $node->firstChild->wholeText == $message)
        {
          // we found it, remove and save the xml file.
          $unit->parentNode->removeChild($unit);

          $fileNode = $xpath->query('//file')->item(0);
          $fileNode->setAttribute('date', @date('Y-m-d\TH:i:s\Z'));

          if ($dom->save($filename) > 0)
          {
            if (!empty($this->cache))
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
    }

    return false;
  }

  protected function createMessageTemplate($catalogue)
  {
    if (null === $catalogue)
    {
      $catalogue = 'messages';
    }

    $variants = $this->getCatalogueList($catalogue);
    $variant = array_shift($variants);
    $file = $this->getSource($variant);
    $dir = dirname($file);
    if (!is_dir($dir))
    {
      @mkdir($dir);
      @chmod($dir, 0777);
    }

    if (!is_dir($dir))
    {
      throw new sfException(sprintf("Unable to create directory %s.", $dir));
    }

    $dom = $this->createDOMDocument($this->getTemplate($catalogue));
    file_put_contents($file, $dom->saveXML());
    chmod($file, 0777);

    return array($variant, $file);
  }

  protected function getTemplate($catalogue)
  {
    $date = date('c');

    return <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xliff PUBLIC "-//XLIFF//DTD XLIFF//EN" "http://www.oasis-open.org/committees/xliff/documents/xliff.dtd" >
<xliff version="1.0">
  <file source-language="EN" target-language="{$this->culture}" datatype="plaintext" original="$catalogue" date="$date" product-name="$catalogue">
    <header />
    <body>
    </body>
  </file>
</xliff>
EOD;
  }
}
