<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorDateTime validates a date and a time. It also converts the input value to a valid date.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorDateTime.class.php 5581 2007-10-18 13:56:14Z fabien $
 */
class sfValidatorDateTime extends sfValidatorDate
{
  /**
   * @see sfValidatorDate
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('with_time', true);
  }
}
