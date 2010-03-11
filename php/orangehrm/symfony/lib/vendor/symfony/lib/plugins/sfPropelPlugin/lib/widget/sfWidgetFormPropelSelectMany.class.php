<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormPropelSelectMany represents a select HTML tag for a model where you can select multiple values.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormPropelSelectMany.class.php 7157 2008-01-23 02:51:45Z dwhittle $
 */
class sfWidgetFormPropelSelectMany extends sfWidgetFormPropelSelect
{
  /**
   * Constructor.
   *
   * @see sfWidgetFormPropelSelect
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('multiple', true);
  }
}
