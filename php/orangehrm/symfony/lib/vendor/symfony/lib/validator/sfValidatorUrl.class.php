<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorUrl validates Urls.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorUrl.class.php 22149 2009-09-18 14:09:53Z Kris.Wallsmith $
 */
class sfValidatorUrl extends sfValidatorRegex
{
  const REGEX_URL_FORMAT = '~^
      (%s)://                                 # protocol
      (
        ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
          |                                   #  or
        \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
      )
      (:[0-9]+)?                              # a port (optional)
      (/?|/\S+)                               # a /, nothing or a / with something
    $~ix';

  /**
   * Available options:
   *
   *  * protocols: An array of acceptable URL protocols (http, https, ftp and ftps by default)
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorRegex
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addOption('protocols', array('http', 'https', 'ftp', 'ftps'));
    $this->setOption('pattern', new sfCallable(array($this, 'generateRegex')));
  }

  /**
   * Generates the current validator's regular expression.
   *
   * @return string
   */
  public function generateRegex()
  {
    return sprintf(self::REGEX_URL_FORMAT, implode('|', $this->getOption('protocols')));
  }
}
