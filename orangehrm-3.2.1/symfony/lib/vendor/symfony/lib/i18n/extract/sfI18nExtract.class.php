<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage i18n
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfI18nExtract.class.php 9128 2008-05-21 00:58:19Z Carl.Vondrick $
 */
abstract class sfI18nExtract
{
  protected
    $currentMessages = array(),
    $newMessages     = array(),
    $allSeenMessages = array(),
    $culture         = null,
    $parameters      = array(),
    $i18n            = null;

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct(sfI18N $i18n, $culture, $parameters = array())
  {
    $this->initialize($i18n, $culture, $parameters);
  }

  /**
   * Initializes the current extract object.
   *
   * @param sfI18N $i18n        A sfI18N instance
   * @param string $culture     The culture
   * @param array  $parameters  An array of parameters
   */
  function initialize(sfI18N $i18n, $culture, $parameters = array())
  {
    $this->allSeenMessages = array();
    $this->newMessages = array();
    $this->currentMessages = array();

    $this->culture = $culture;
    $this->parameters = $parameters;

    $this->i18n = $i18n;

    $this->configure();

    $this->loadMessageSources();
    $this->loadCurrentMessages();
  }

  /**
   * Configures the current extract object.
   */
  public function configure()
  {
  }

  /**
   * Extracts i18n strings.
   *
   * This class must be implemented by subclasses.
   */
  abstract public function extract();

  /**
   * Saves the new messages.
   *
   * Current limitations:
   *  - For file backends (XLIFF and gettext), it only saves in the "most global" file
   */
  public function saveNewMessages()
  {
    $messageSource = $this->i18n->getMessageSource();
    foreach ($this->getNewMessages() as $message)
    {
      $messageSource->append($message);
    }

    $messageSource->save();
  }

  /**
   * Deletes old messages.
   *
   * Current limitations:
   *  - For file backends (XLIFF and gettext), it only deletes in the "most global" file
   */
  public function deleteOldMessages()
  {
    $messageSource = $this->i18n->getMessageSource();
    foreach ($this->getOldMessages() as $message)
    {
      $messageSource->delete($message);
    }
  }

  /**
   * Gets the new i18n strings.
   *
   * @return array An array of i18n strings
   */
  final public function getNewMessages()
  {
    return array_diff($this->getAllSeenMessages(), $this->getCurrentMessages());
  }

  /**
   * Gets the current i18n strings.
   *
   * @return array An array of i18n strings
   */
  public function getCurrentMessages()
  {
    return $this->currentMessages;
  }

  /**
   * Gets all i18n strings seen during the extraction process.
   *
   * @return array An array of i18n strings
   */
  public function getAllSeenMessages()
  {
    return $this->allSeenMessages;
  }

  /**
   * Gets old i18n strings.
   *
   * This returns all strings that weren't seen during the extraction process
   * and are in the current messages.
   *
   * @return array An array of i18n strings
   */
  final public function getOldMessages()
  {
    return array_diff($this->getCurrentMessages(), $this->getAllSeenMessages());
  }

  /**
   * Loads message sources objects and sets the culture.
   */
  protected function loadMessageSources()
  {
    $this->i18n->getMessageSource()->setCulture($this->culture);
    $this->i18n->getMessageSource()->load();
  }

  /**
   * Loads messages already saved in the message sources.
   */
  protected function loadCurrentMessages()
  {
    $this->currentMessages = array();
    foreach ($this->i18n->getMessageSource()->read() as $catalogue => $translations)
    {
      foreach ($translations as $key => $values)
      {
        $this->currentMessages[] = $key;
      }
    }
  }

  /**
   * Extracts i18n strings from PHP files.
   *
   * @param string $dir The PHP full path name
   */
  protected function extractFromPhpFiles($dir)
  {
    $phpExtractor = new sfI18nPhpExtractor();

    $files = sfFinder::type('file')->name('*.php');
    $messages = array();
    foreach ($files->in($dir) as $file)
    {
      $messages = array_merge($messages, $phpExtractor->extract(file_get_contents($file)));
    }

    $this->updateMessages($messages);
  }

  /**
   * Updates the internal arrays with new messages.
   *
   * @param array $messages An array of new i18n strings
   */
  protected function updateMessages($messages)
  {
    $this->allSeenMessages = array_unique(array_merge($this->allSeenMessages, $messages));
  }
}
