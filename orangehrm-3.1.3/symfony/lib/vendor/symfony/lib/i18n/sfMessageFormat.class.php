<?php

/**
 * sfMessageFormat class file.
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
 * @version    $Id: sfMessageFormat.class.php 24622 2009-11-30 23:49:47Z FabianLange $
 * @package    symfony
 * @subpackage i18n
 */

/**
 * sfMessageFormat class.
 * 
 * Format a message, that is, for a particular message find the 
 * translated message. The following is an example using 
 * a SQLite database to store the translation message. 
 * Create a new message format instance and echo "Hello"
 * in simplified Chinese. This assumes that the world "Hello"
 * is translated in the database.
 *
 * <code>
 *  $source = sfMessageSource::factory('SQLite', 'sqlite://messages.db');
 *  $source->setCulture('zh_CN');
 *  $source->setCache(new sfMessageCache('./tmp'));
 *
 *  $formatter = new sfMessageFormat($source); 
 *  
 *  echo $formatter->format('Hello');
 * </code>
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Fri Dec 24 20:46:16 EST 2004
 * @package    symfony
 * @subpackage i18n
 */
class sfMessageFormat
{
  /**
   * The message source.
   * @var sfMessageSource
   */
  protected $source;

  /**
   * A list of loaded message catalogues.
   * @var array
   */
  protected $catalogues = array();

  /**
   * The translation messages.
   * @var array
   */
  protected $messages = array();

  /**
   * A list of untranslated messages.
   * @var array
   */
  protected $untranslated = array();

  /**
   * The prefix and suffix to append to untranslated messages.
   * @var array
   */
  protected $postscript = array('', '');

  /**
   * Set the default catalogue.
   * @var string 
   */
  public $catalogue;

  /**
   * Output encoding charset
   * @var string
   */
  protected $charset = 'UTF-8';

  /**
   * Constructor.
   * Create a new instance of sfMessageFormat using the messages
   * from the supplied message source.
   *
   * @param sfMessageSource $source   the source of translation messages.
   * @param string          $charset  for the message output.
   */
  function __construct(sfIMessageSource $source, $charset = 'UTF-8')
  {
    $this->source = $source;
    $this->setCharset($charset);
  }

  /**
   * Sets the charset for message output.
   *
   * @param string $charset charset, default is UTF-8
   */
  public function setCharset($charset)
  {
    $this->charset = $charset;
  }

  /**
   * Gets the charset for message output. Default is UTF-8.
   *
   * @return string charset, default UTF-8
   */
  public function getCharset()
  {
    return $this->charset;
  }
  
  /**
   * Loads the message from a particular catalogue. A listed
   * loaded catalogues is kept to prevent reload of the same
   * catalogue. The load catalogue messages are stored
   * in the $this->message array.
   *
   * @param string $catalogue message catalogue to load.
   */
  protected function loadCatalogue($catalogue)
  {
    if (in_array($catalogue, $this->catalogues))
    {
      return;
    }

    if ($this->source->load($catalogue))
    {
      $this->messages[$catalogue] = $this->source->read();
      $this->catalogues[] = $catalogue;
    }
  }

  /**
   * Formats the string. That is, for a particular string find
   * the corresponding translation. Variable subsitution is performed
   * for the $args parameter. A different catalogue can be specified
   * using the $catalogue parameter.
   * The output charset is determined by $this->getCharset();
   *
   * @param string  $string     the string to translate.
   * @param array   $args       a list of string to substitute.
   * @param string  $catalogue  get the translation from a particular message
   * @param string  $charset    charset, the input AND output charset catalogue.
   * @return string translated string.
   */
  public function format($string, $args = array(), $catalogue = null, $charset = null)
  {
    // make sure that objects with __toString() are converted to strings
    $string = (string) $string;
    if (empty($charset))
    {
      $charset = $this->getCharset();
    }

    $s = $this->formatString(sfToolkit::I18N_toUTF8($string, $charset), $args, $catalogue);

    return sfToolkit::I18N_toEncoding($s, $charset);
  }

  /**
   * Do string translation.
   *
   * @param string  $string     the string to translate.
   * @param array   $args       a list of string to substitute.
   * @param string  $catalogue  get the translation from a particular message catalogue.
   * @return string translated string.
   */
  protected function formatString($string, $args = array(), $catalogue = null)
  {
    if (empty($args))
    {
      $args = array();
    }

    if (empty($catalogue))
    {
      $catalogue = empty($this->catalogue) ? 'messages' : $this->catalogue;
    }

    $this->loadCatalogue($catalogue);

    foreach ($this->messages[$catalogue] as $variant)
    {
      // we found it, so return the target translation
      if (isset($variant[$string]))
      {
        $target = $variant[$string]; 

        // check if it contains only strings.
        if (is_array($target))
        {
          $target = array_shift($target);
        }

        // found, but untranslated
        if (empty($target))
        {
          return $this->postscript[0].$this->replaceArgs($string, $args).$this->postscript[1];
        }
        return $this->replaceArgs($target, $args);
      }
    }

    // well we did not find the translation string.
    $this->source->append($string);

    return $this->postscript[0].$this->replaceArgs($string, $args).$this->postscript[1];
  }

  protected function replaceArgs($string, $args)
  {
    // replace object with strings
    foreach ($args as $key => $value)
    {
      if (is_object($value) && method_exists($value, '__toString'))
      {
        $args[$key] = $value->__toString();
      }
    }

    return strtr($string, $args);
  }

  /**
   * Gets the message source.
   *
   * @return MessageSource 
   */
  function getSource()
  {
    return $this->source;
  }
  
  /**
   * Sets the prefix and suffix to append to untranslated messages.
   * e.g. $postscript=array('[T]','[/T]'); will output 
   * "[T]Hello[/T]" if the translation for "Hello" can not be determined.
   *
   * @param array $postscript first element is the prefix, second element the suffix.
   */
  function setUntranslatedPS($postscript)
  {
    if (is_array($postscript) && count($postscript) >= 2)
    {
      $this->postscript[0] = $postscript[0];
      $this->postscript[1] = $postscript[1];
    }
  }
}
