<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMessageSource_Aggregate aggregates several message source objects.
 *
 * @package    symfony
 * @subpackage i18n
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfMessageSource_Aggregate.class.php 13401 2008-11-27 11:18:39Z fabien $
 */
class sfMessageSource_Aggregate extends sfMessageSource
{
  protected
    $messageSources = array();

  /**
   * Constructor.
   *
   * The order of the messages sources in the array is important.
   * This class will take the first translation found in the message sources.
   *
   * @param array $messageSources An array of message sources.
   *
   * @see   MessageSource::factory();
   */
  function __construct($messageSources)
  {
    $this->messageSources = $messageSources;
  }

  public function setCulture($culture)
  {
    parent::setCulture($culture);

    foreach ($this->messageSources as $messageSource)
    {
      $messageSource->setCulture($culture);
    }
  }

  protected function getLastModified($sources)
  {
    $lastModified = time();
    foreach ($sources as $source)
    {
      $lastModified = min($lastModified, $source[0]->getLastModified($source[1]));
    }

    return $lastModified;
  }

  public function isValidSource($sources)
  {
    foreach ($sources as $source)
    {
      if (false === $source[0]->isValidSource($source[1]))
      {
        continue;
      }

      return true;
    }

    return false;
  }

  public function getSource($variant)
  {
    $sources = array();
    foreach ($this->messageSources as $messageSource)
    {
      $sources[] = array($messageSource, $messageSource->getSource(str_replace($messageSource->getId(), '', $variant)));
    }

    return $sources;
  }

  public function &loadData($sources)
  {
    $messages = array();
    foreach ($sources as $source)
    {
      if (false === $source[0]->isValidSource($source[1]))
      {
        continue;
      }

      $data = $source[0]->loadData($source[1]);
      if (is_array($data))
      {
        $messages = array_merge($data, $messages);
      }
    }

    return $messages;
  }

  public function getCatalogueList($catalogue)
  {
    $variants = array();
    foreach ($this->messageSources as $messageSource)
    {
      foreach ($messageSource->getCatalogueList($catalogue) as $variant)
      {
        $variants[] = $messageSource->getId().$variant;
      }
    }

    return $variants;
  }

  public function append($message)
  {
    // Append to the first message source only
    if (count($this->messageSources))
    {
      $this->messageSources[0]->append($message);
    }
  }

  public function update($text, $target, $comments, $catalogue = 'messages')
  {
    // Only update one message source
    foreach ($this->messageSources as $messageSource)
    {
      if ($messageSource->update($text, $target, $comments, $catalogue))
      {
        return true;
      }
    }

    return false;
  }

  public function delete($message, $catalogue = 'messages')
  {
    $retval = false;
    foreach ($this->messageSources as $messageSource)
    {
      if ($messageSource->delete($message, $catalogue))
      {
        $retval = true;
      }
    }

    return $retval;
  }

  public function save($catalogue = 'messages')
  {
    $retval = false;
    foreach ($this->messageSources as $messageSource)
    {
      if ($messageSource->save($catalogue))
      {
        $retval = true;
      }
    }

    return $retval;
  }

  public function getId()
  {
    $id = '';
    foreach ($this->messageSources as $messageSource)
    {
      $id .= $messageSource->getId();
    }

    return md5($id);
  }

  public function catalogues()
  {
    throw new sfException('The "catalogues()" method is not implemented for this message source.');
  }
}
