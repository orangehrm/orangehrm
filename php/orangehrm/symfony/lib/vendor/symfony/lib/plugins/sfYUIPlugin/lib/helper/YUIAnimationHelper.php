<?php

/*
 * This file is part of the symfony package.
 * (c) 2006 Nick Winfield <enquiries@superhaggis.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wrapper for YUI Animation component.
 * 
 * @package    symfony
 * @subpackage helper
 * @author     Nick Winfield <enquiries@superhaggis.com>
 * @version    SVN: $Id: YUIAnimationHelper.php 2739 2006-11-17 15:42:19Z superhaggis $
 */

/**
 * Prepares a YAHOO.util.Anim call which can be called via
 * an event handler or callback.
 * 
 *   Possible 'Easing' values for $effect:
 *   
 *   'backBoth'        => Backtracks slightly, then reverses direction, 
 *                        overshoots end, then reverses and comes back 
 *                        to end.
 *   'backIn'          => Backtracks slightly, then reverses direction 
 *                        and moves to end.
 *   'backOut'         => Overshoots end, then reverses and comes back 
 *                        to end.
 *   'bounceBoth'      => Bounces off start and end.
 *   'bounceIn'        => Bounce off of start.
 *   'bounceOut'       => Bounces off end.
 *   'easeBoth'        => Begins slowly and decelerates towards end. 
 *                        (quadratic)
 *   'easeBothStrong'  => Begins slowly and decelerates towards end. 
 *                        (quartic)
 *   'easeIn'          => Begins slowly and accelerates towards end. 
 *                        (quadratic)
 *   'easeInStrong'    => Begins slowly and accelerates towards end. 
 *                        (quartic)
 *   'easeNone'        => Uniform speed between points.
 *   'easeOut'         => Begins quickly and decelerates towards end. 
 *                        (quadratic) - default effect.
 *   'easeOutStrong'   => Begins quickly and decelerates towards end. 
 *                        (quartic)
 *   'elasticBoth'     => Snap both elastic effect.
 *   'elasticIn'       => Snap in elastic effect.
 *   'elasticOut'      => Snap out elastic effect.
 *
 *   Possible indexes for $options:
 *
 *   'from_width'      => The width that the element should start at.
 *   'from_height'     => The height that the element should start at.
 *   'unit_width'      => Defaults to pixels (px) - unit of measurement 
 *                        for the specified width values.
 *   'unit_height'     => Defaults to pixels (px) - unit of measurement 
 *                        for the specified height values.
 *   'duration'        => The duration of the animation (in seconds; 
 *                        defaults to 1)
 *   'opacity_from'    => The opacity that the element should start at.
 *   'opacity_to'      => The opacity that the element should finish at.
 *   'fontsize_from'   => The size that the element's font should 
 *                        start at.
 *   'fontsize_to'     => The size that the element's font should
 *                        finish at.
 *   'fontsize_unit'   => The unit of measurement that the font should 
 *                        change by. (defaults to %)
 *
 *   Choose either of these width-specific options:
 *   'to_width'        => The width that the element should finish at.
 *   'by_width'        => The width that the element should change by.
 *
 *   Choose either of these height-specific options:
 *   'to_height'       => The height that the element should finish at.
 *   'by_height'       => The height that the element should change by.
 *   
 * Example usage:  
 *
 *   100x50px black box that expands to 400x200px after 2 seconds using
 *   the 'elasticOut' animation effect.  Opacity also changes from 100% 
 *   to 25% and font size changes from 100% to 250%.
 *
 *   <?php use_helper('YUIAnimation') ?>
 *   ...
 *   <div id="foo" style="color: #FFFFFF; background-color: #000000; 
 *        height: 50px; width: 100px">hello, world!</div>
 *   <?php echo link_to('click me!', '#', array(
 *     'onclick' => yui_animation('elasticOut', 'foo', array(
 *       'from_height' => '50',
 *       'from_width' => '100',
 *       'to_height' => '200',
 *       'to_width' => '400',
 *       'opacity_from' => '1',
 *       'opacity_to' => '0.25',
 *       'fontsize_from' => '100',
 *       'fontsize_to' => '250',
 *       'fontsize_unit' => '%',
 *       'duration' => '2',
 *     )),
 *   )) ?>
 *
 * @param string $effect
 * @param string $element
 * @param array $options
 * @return string $js
 */
function yui_animation($effect, $element, $options = array())
{
  sfYUI::addComponent('yahoo');
  sfYUI::addComponent('dom');
  sfYUI::addComponent('event');
  sfYUI::addComponent('animation');
    
  $js  = "";
    
  $js .= "var " . $element . "_anim = new YAHOO.util.Anim('".$element."', { ";

  $dimensions = array();   
 
  if (isset($options['from_width']) || isset($options['to_width']) || isset($options['by_width']))
  {
    $width  = "";
    $width .= "width: { ";

    $width_attributes = array();

    if (isset($options['from_width']))
    {
      $from_width = "from: " . $options['from_width'];
      $width_attributes[] = $from_width;
    }
    
    if (isset($options['to_width']))
    {
      $to_width = "to: " . $options['to_width'];
      $width_attributes[] = $to_width;
    }
    elseif (isset($options['by_width']))
    {
      $by_width = "by: " . $options['by_width'];
      $width_attributes[] = $by_width;
    }
    
    if (isset($options['unit_width']))
    {
      $unit_width = "unit: '" . $options['unit_width'];
      $width_attributes[] = $unit_width;
    }

    $width .= join(', ', $width_attributes);

    $width .= " }";

    $dimensions[] = $width;
  }

  if (isset($options['from_height']) || isset($options['to_height']) || isset($options['by_height']))
  {
    $height  = "";
    $height .= "height: { ";

    $height_attributes = array();

    if (isset($options['from_height']))
    {
      $from_height = "from: " . $options['from_height'];
      $height_attributes[] = $from_height;
    }
    
    if (isset($options['to_height']))
    {
      $to_height = "to: " . $options['to_height'];
      $height_attributes[] = $to_height;
    }
    elseif (isset($options['by_height']))
    {
      $by_height = "by: " . $options['by_height'];
      $height_attributes[] = $by_height;
    }
    
    if (isset($options['unit_height']))
    {
      $unit_height = "unit: '" . $options['unit_height'];
      $height_attributes[] = $unit_height;
    }

    $height .= join(', ', $height_attributes);

    $height .= " }";

    $dimensions[] = $height;
  }

  if (isset($options['opacity_from']) || isset($options['opacity_to']))
  {
    $opacity  = "";
    $opacity .= "opacity: { ";

    $opacity_attributes = array();

    if (isset($options['opacity_from']))
    { 
      $opacity_from = "from: " . $options['opacity_from'];    
      $opacity_attributes[] = $opacity_from;
    }
    
    if (isset($options['opacity_to']))
    {
      $opacity_to = "to: " . $options['opacity_to'];
      $opacity_attributes[] = $opacity_to;
    }

    $opacity .= join(', ', $opacity_attributes);

    $opacity .= " }";

    $dimensions[] = $opacity;
  }

  if (isset($options['fontsize_from']) || isset($options['fontsize_to']))
  {
    $fontsize  = "";
    $fontsize .= "fontSize: { ";

    $fontsize_attributes = array();

    if (isset($options['fontsize_from']))
    {     
      $fontsize_from = "from: " . $options['fontsize_from'];
      $fontsize_attributes[] = $fontsize_from;
    }
    
    if (isset($options['fontsize_to']))
    {
      $fontsize_to = "to: " . $options['fontsize_to'];
      $fontsize_attributes[] = $fontsize_to;
    }

    if (isset($options['fontsize_unit']))
    {
      $fontsize_unit = "unit: " . $options['fontsize_unit'];
      $fontsize_attributes[] = $fontsize_unit;
    }
    else
    {
      $fontsize_unit = "unit: '%'";
      $fontsize_attributes[] = $fontsize_unit;
    }

    $fontsize .= join(', ', $fontsize_attributes);

    $fontsize .= " }";

    $dimensions[] = $fontsize;
  }

  $js .= join(', ', $dimensions);

  $js .= " }, ";
  $js .= (isset($options['duration'])) ? $options['duration'] : 1;
  $js .= ", ";

  $js .= "YAHOO.util.Easing." . $effect ."); " . $element . "_anim.animate();";
 
  return $js;
}

?>
