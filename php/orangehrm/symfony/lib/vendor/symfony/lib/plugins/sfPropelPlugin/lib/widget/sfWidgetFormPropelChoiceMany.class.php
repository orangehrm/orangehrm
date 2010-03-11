<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormPropelChoice represents a choice widget for a model where you can select multiple values.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormPropelChoiceMany.class.php 11540 2008-09-14 15:23:55Z fabien $
 */
class sfWidgetFormPropelChoiceMany extends sfWidgetFormPropelChoice
{
  /**
   * Constructor.
   *
   * @see sfWidgetFormPropelChoice
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('multiple', true);
  }
}
