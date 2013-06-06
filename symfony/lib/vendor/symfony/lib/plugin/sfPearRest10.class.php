<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'PEAR/REST/10.php';

/**
 * sfPearRest10 interacts with a PEAR channel that supports REST 1.0.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPearRest10.class.php 5250 2007-09-24 08:11:50Z fabien $
 */
class sfPearRest10 extends PEAR_REST_10
{
  /**
   * @see PEAR_REST_10
   */
  public function __construct($config, $options = array())
  {
    $class = isset($options['base_class']) ? $options['base_class'] : 'sfPearRest';

    $this->_rest = new $class($config, $options);
  }
}
