<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormSchemaForEach duplicates a given widget multiple times in a widget schema.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSchemaForEach.class.php 9046 2008-05-19 08:13:51Z FabianLange $
 */
class sfWidgetFormSchemaForEach extends sfWidgetFormSchema
{
  /**
   * Constructor.
   *
   * @param sfWidgetFormSchema $widget      An sfWidgetFormSchema instance
   * @param integer            $count       The number of times to duplicate the widget
   * @param array              $options     An array of options
   * @param array              $attributes  An array of default HTML attributes
   * @param array              $labels      An array of HTML labels
   *
   * @see sfWidgetFormSchema
   */
  public function __construct(sfWidgetFormSchema $widget, $count, $options = array(), $attributes = array(), $labels = array())
  {
    parent::__construct(array_fill(0, $count, $widget), $options, $attributes, $labels);
  }
}
