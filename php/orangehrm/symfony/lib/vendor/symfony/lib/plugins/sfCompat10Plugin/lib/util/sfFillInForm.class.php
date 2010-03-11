<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFillInForm.class.php 9588 2008-06-15 07:15:36Z FabianLange $
 */
class sfFillInForm
{
  protected
    $converters = array(),
    $skipFields = array(),
    $types      = array('text', 'checkbox', 'radio', 'hidden', 'password');

  public function addConverter($callable, $fields)
  {
    foreach ((array) $fields as $field)
    {
      $this->converters[$field][] = $callable;
    }
  }

  public function setSkipFields($fields)
  {
    $this->skipFields = $fields;
  }

  public function setTypes($types)
  {
    $this->types = $types;
  }

  /**
   * fills in the values and returns HTML. This is a non validating tolerant mode.
   *
   * @return HTML with values filled in
   */
  public function fillInHtml($html, $formName, $formId, $values)
  {
    $dom = new DomDocument('1.0', sfConfig::get('sf_charset', 'UTF-8'));

    $noHead = strpos($html,'<head') === false;
    if ($noHead){
      // loadHTML needs the conent-type meta for the charset
      $html = '<meta http-equiv="Content-Type" content="text/html; charset='.sfConfig::get('sf_charset').'"/>'.$html;
    }

    @$dom->loadHTML($html);
    $dom = $this->fillInDom($dom, $formName, $formId, $values);

    if($noHead){
      // remove the head element that was created by adding the meta tag.
      $headElement = $dom->getElementsByTagName('head')->item(0);
      if ($headElement)
      {
        $dom->getElementsByTagName('html')->item(0)->removeChild($headElement);
      }
    }
    return $dom->saveHTML();
  }

  /**
   * fills in the values and returns XHTML. This is same as XML but stripts the XML Prolog.
   *
   * @return XHTML without prolog with values filled in
   */
  public function fillInXhtml($xml, $formName, $formId, $values)
  {
    $xhtml = $this->fillInXml($xml, $formName, $formId, $values);
    $prolog_regexp = '/^' . preg_quote('<?xml version="1.0"?>') . '\s*/';
    return preg_replace($prolog_regexp, '', $xhtml);
  }

  /**
   * fills in the values and returns XHTML. It can only work correctly on validating XHTML.
   *
   * @return XHTML including XML prolog with values filled in
   */
  public function fillInXml($xml, $formName, $formId, $values)
  {
    $dom = new DomDocument('1.0', sfConfig::get('sf_charset', 'UTF-8'));
    if (strpos($xml,'<!DOCTYPE') === false)
    {
      $xml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '.
             '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
             $xml;
    }
    @$dom->loadXML($xml);

    $dom = $this->fillInDom($dom, $formName, $formId, $values);

    return $dom->saveXML();
  }

  public function fillInDom($dom, $formName, $formId, $values)
  {
    $xpath = new DomXPath($dom);
    if ($dom->documentElement && $dom->documentElement->namespaceURI)
    {
      $xpath->registerNamespace('xhtml', $dom->documentElement->namespaceURI);
      $ns = 'xhtml:';
    }
    else
    {
      $ns = '';
    }

    // find our form
    if ($formName)
    {
      $xpath_query = '//'.$ns.'form[@name="'.$formName.'"]';
    }
    elseif ($formId)
    {
      $xpath_query = '//'.$ns.'form[@id="'.$formId.'"]';
    }
    else
    {
      $xpath_query = '//'.$ns.'form';
    }

    $form = $xpath->query($xpath_query)->item(0);
    if (!$form)
    {
      if (!$formName && !$formId)
      {
        throw new sfException('No form found in this page.');
      }
      else
      {
        throw new sfException(sprintf('The form "%s" cannot be found.', $formName ? $formName : $formId));
      }
    }

    $query = 'descendant::'.$ns.'input[@name and (not(@type)';
    foreach ($this->types as $type)
    {
      $query .= ' or @type="'.$type.'"';
    }
    $query .= ')] | descendant::'.$ns.'textarea[@name] | descendant::'.$ns.'select[@name]';

    foreach ($xpath->query($query, $form) as $element)
    {
      $name  = (string) $element->getAttribute('name');
      $value = (string) $element->getAttribute('value');
      $type  = (string) $element->getAttribute('type');

      // skip fields
      if (!$this->hasValue($values, $name) || in_array($name, $this->skipFields))
      {
        continue;
      }

      if ($element->nodeName == 'input')
      {
        if ($type == 'checkbox' || $type == 'radio')
        {
          // checkbox and radio
          $element->removeAttribute('checked');
          if (is_array($this->getValue($values, $name)) && ($this->hasValue($values, $name) || !$element->hasAttribute('value')))
          {
            if (in_array($value, $this->getValue($values, $name)))
            {
              $element->setAttribute('checked', 'checked');
            }
          }
          else if ($this->hasValue($values, $name) && ($this->getValue($values, $name) == $value || !$element->hasAttribute('value')))
          {
            $element->setAttribute('checked', 'checked');
          }
        }
        else
        {
          // text input
          $element->removeAttribute('value');
          $element->setAttribute('value', $this->escapeValue($this->getValue($values, $name, true), $name));
        }
      }
      else if ($element->nodeName == 'textarea')
      {
        $el = $element->cloneNode(false);
        $el->appendChild($dom->createTextNode($this->escapeValue($this->getValue($values, $name, true), $name)));
        $element->parentNode->replaceChild($el, $element);
      }
      else if ($element->nodeName == 'select')
      {
        // if the name contains [] it is part of an array that needs to be shifted
        $value    = $this->getValue($values, $name, strpos($name,'[]') !== false);
        $multiple = $element->hasAttribute('multiple');
        foreach ($xpath->query('descendant::'.$ns.'option', $element) as $option)
        {
          $option->removeAttribute('selected');
          if ($multiple && is_array($value))
          {
            if (in_array($option->getAttribute('value'), $value))
            {
              $option->setAttribute('selected', 'selected');
            }
          }
          else if ($value == $option->getAttribute('value'))
          {
            $option->setAttribute('selected', 'selected');
          }
        }
      }
    }

    return $dom;
  }

  protected function hasValue($values, $name)
  {
    if (array_key_exists($name, $values))
    {
      return true;
    }

    return sfToolkit::hasArrayValueForPath($values, $name);
  }

  // use reference to values so that arrays can be shifted.
  protected function getValue(&$values, $name, $shiftArray = false)
  {
    if (array_key_exists($name, $values))
    {
      $return = &$values[$name];
    } else {
      $return = &sfToolkit::getArrayValueForPathByRef($values, $name);
    }

    if ($shiftArray && is_array($return))
    {
      // we need to remove the first element from the array. Therefore we need a reference
      return array_shift($return);
    }
    return $return;
  }

  protected function escapeValue($value, $name)
  {
    if (function_exists('iconv') && strtolower(sfConfig::get('sf_charset')) != 'utf-8')
    {
      $new_value = iconv(sfConfig::get('sf_charset'), 'UTF-8', $value);
      if (false !== $new_value)
      {
        $value = $new_value;
      }
    }

    if (isset($this->converters[$name]))
    {
      foreach ($this->converters[$name] as $callable)
      {
        $value = call_user_func($callable, $value);
      }
    }

    return $value;
  }
}
