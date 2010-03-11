<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormSelectDoubleList represents a multiple select displayed as a double list.
 *
 * This widget needs some JavaScript to work. So, you need to include:
 *
 *   /sfFormExtraPlugin/js/double_list.js
 *
 * You also need to add an "onsubmit" attribute to your form tag:
 *
 *   onsubmit="double_list_submit(this, 'double_list_select'); return true;"
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSelectDoubleList.class.php 11840 2008-09-29 09:23:27Z fabien $
 */
class sfWidgetFormSelectDoubleList extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:            An array of possible choices (required)
   *  * class:              The main class of the widget
   *  * class_select:       The class for the two select tags
   *  * label_unassociated: The label for unassociated
   *  * label_associated:   The label for associated
   *  * unassociate:        The HTML for the unassociate link
   *  * associate:          The HTML for the associate link
   *  * template:           The HTML template to use to render this widget
   *                        The available placeholders are:
   *                          * label_associated
   *                          * label_unassociated
   *                          * associate
   *                          * unassociate
   *                          * associated
   *                          * unassociated
   *                          * class
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('choices');

    $this->addOption('class', 'double_list');
    $this->addOption('class_select', 'double_list_select');
    $this->addOption('label_unassociated', 'Unassociated');
    $this->addOption('label_associated', 'Associated');
    $this->addOption('unassociate', '<img src="/sfFormExtraPlugin/images/next.png" alt="unassociate" />');
    $this->addOption('associate', '<img src="/sfFormExtraPlugin/images/previous.png" alt="associate" />');
    $this->addOption('template', <<<EOF
<div class="%class%">
  <div style="float: left">
    <div class="double_list_label">%label_associated%</div>
    %associated%
  </div>
  <div style="float: left; margin-top: 2em">
    %associate%
    <br />
    %unassociate%
  </div>
  <div style="float: left">
    <div class="double_list_label">%label_unassociated%</div>
    %unassociated%
  </div>
  <br style="clear: both" />
</div>
EOF
);
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (is_null($value))
    {
      $value = array();
    }

    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    $associated = array();
    $unassociated = array();
    foreach ($choices as $key => $option)
    {
      if (in_array(strval($key), $value))
      {
        $associated[$key] = $option;
      }
      else
      {
        $unassociated[$key] = $option;
      }
    }

    $size = isset($attributes['size']) ? $attributes['size'] : (isset($this->attributes['size']) ? $this->attributes['size'] : 10);

    $associatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $associated), array('size' => $size, 'class' => $this->getOption('class_select').'-selected'));
    $unassociatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $unassociated), array('size' => $size, 'class' => $this->getOption('class_select')));

    return strtr($this->getOption('template'), array(
      '%class%'              => $this->getOption('class'),
      '%label_associated%'   => $this->getOption('label_associated'),
      '%label_unassociated%' => $this->getOption('label_unassociated'),
      '%associate%'          => sprintf('<a href="#" onclick="%s">%s</a>', 'double_list_move(\'unassociated_'.$this->generateId($name).'\', \''.$this->generateId($name).'\'); return false;', $this->getOption('associate')),
      '%unassociate%'        => sprintf('<a href="#" onclick="%s">%s</a>', 'double_list_move(\''.$this->generateId($name).'\', \'unassociated_'.$this->generateId($name).'\'); return false;', $this->getOption('unassociate')),
      '%associated%'         => $associatedWidget->render($name),
      '%unassociated%'       => $unassociatedWidget->render('unassociated_'.$name),
    ));
  }

  public function __clone()
  {
    if ($this->getOption('choices') instanceof sfCallable)
    {
      $callable = $this->getOption('choices')->getCallable();
      if (is_array($callable))
      {
        $callable[0] = $this;
        $this->setOption('choices', new sfCallable($callable));
      }
    }
  }
}
