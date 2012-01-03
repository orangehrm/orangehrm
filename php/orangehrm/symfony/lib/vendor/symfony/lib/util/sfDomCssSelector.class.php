<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDomCssSelector allows to navigate a DOM with CSS selector.
 *
 * Based on getElementsBySelector version 0.4 - Simon Willison, March 25th 2003
 * http://simon.incutio.com/archive/2003/03/25/getElementsBySelector
 *
 * Some methods based on the jquery library
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDomCssSelector.class.php 31893 2011-01-24 18:11:45Z fabien $
 */
class sfDomCssSelector implements Countable, Iterator
{
  public $nodes = array();

  private $count;

  public function __construct($nodes)
  {
    if (!is_array($nodes))
    {
      $nodes = array($nodes);
    }

    $this->nodes = $nodes;
  }

  public function getNodes()
  {
    return $this->nodes;
  }

  public function getNode()
  {
    return $this->nodes ? $this->nodes[0] : null;
  }

  public function getValue()
  {
    return $this->nodes[0]->nodeValue;
  }

  public function getValues()
  {
    $values = array();
    foreach ($this->nodes as $node)
    {
      $values[] = $node->nodeValue;
    }

    return $values;
  }

  public function matchSingle($selector)
  {
    $nodes = $this->getElements($selector);

    return $nodes ? new sfDomCssSelector($nodes[0]) : new sfDomCssSelector(array());
  }

  public function matchAll($selector)
  {
    $nodes = $this->getElements($selector);

    return $nodes ? new sfDomCssSelector($nodes) : new sfDomCssSelector(array());
  }

  protected function getElements($selector)
  {
    $nodes = array();
    foreach ($this->nodes as $node)
    {
      $result_nodes = $this->getElementsForNode($selector, $node);
      if ($result_nodes)
      {
        $nodes = array_merge($nodes, $result_nodes);
      }
    }

    foreach ($nodes as $node)
    {
      $node->removeAttribute('sf_matched');
    }

    return $nodes;
  }

  protected function getElementsForNode($selector, $root_node)
  {
    $all_nodes = array();
    foreach ($this->tokenize_selectors($selector) as $selector)
    {
      $nodes = array($root_node);
      foreach ($this->tokenize($selector) as $token)
      {
        $combinator = $token['combinator'];
        $selector = $token['selector'];

        $token = trim($token['name']);

        $pos = strpos($token, '#');
        if (false !== $pos && preg_match('/^[A-Za-z0-9]*$/', substr($token, 0, $pos)))
        {
          // Token is an ID selector
          $tagName = substr($token, 0, $pos);
          $id = substr($token, $pos + 1);
          $xpath = new DomXPath($root_node);
          $element = $xpath->query(sprintf("//*[@id = '%s']", $id))->item(0);
          if (!$element || ($tagName && strtolower($element->nodeName) != $tagName))
          {
            // tag with that ID not found
            return array();
          }

          // Set nodes to contain just this element
          $nodes = array($element);
          $nodes = $this->matchMultipleCustomSelectors($nodes, $selector);

          continue; // Skip to next token
        }

        $pos = strpos($token, '.');
        if (false !== $pos && preg_match('/^[A-Za-z0-9\*]*$/', substr($token, 0, $pos)))
        {
          // Token contains a class selector
          $tagName = substr($token, 0, $pos);
          if (!$tagName)
          {
            $tagName = '*';
          }
          $className = substr($token, $pos + 1);

          // Get elements matching tag, filter them for class selector
          $founds = $this->getElementsByTagName($nodes, $tagName, $combinator);
          $nodes = array();
          foreach ($founds as $found)
          {
            if (preg_match('/(^|\s+)'.$className.'($|\s+)/', $found->getAttribute('class')))
            {
              $nodes[] = $found;
            }
          }

          $nodes = $this->matchMultipleCustomSelectors($nodes, $selector);

          continue; // Skip to next token
        }

        // Code to deal with attribute selectors
        if (preg_match('/^(\w+|\*)(\[.+\])$/', $token, $matches))
        {
          $tagName = $matches[1] ? $matches[1] : '*';
          preg_match_all('/
            \[
              ([\w\-]+)             # attribute
              ([=~\|\^\$\*]?)       # modifier (optional)
              =?                    # equal (optional)
              (
                "([^"]*)"           # quoted value (optional)
                |
                ([^\]]*)            # non quoted value (optional)
              )
            \]
          /x', $matches[2], $matches, PREG_SET_ORDER);

          // Grab all of the tagName elements within current node
          $founds = $this->getElementsByTagName($nodes, $tagName, $combinator);
          $nodes = array();
          foreach ($founds as $found)
          {
            $ok = false;
            foreach ($matches as $match)
            {
              $attrName = $match[1];
              $attrOperator = $match[2];
              $attrValue = $match[4] === '' ? (isset($match[5]) ? $match[5] : '') : $match[4];

              switch ($attrOperator)
              {
                case '=': // Equality
                  $ok = $found->getAttribute($attrName) == $attrValue;
                  break;
                case '~': // Match one of space seperated words
                  $ok = preg_match('/\b'.preg_quote($attrValue, '/').'\b/', $found->getAttribute($attrName));
                  break;
                case '|': // Match start with value followed by optional hyphen
                  $ok = preg_match('/^'.preg_quote($attrValue, '/').'-?/', $found->getAttribute($attrName));
                  break;
                case '^': // Match starts with value
                  $ok = 0 === strpos($found->getAttribute($attrName), $attrValue);
                  break;
                case '$': // Match ends with value
                  $ok = $attrValue == substr($found->getAttribute($attrName), -strlen($attrValue));
                  break;
                case '*': // Match ends with value
                  $ok = false !== strpos($found->getAttribute($attrName), $attrValue);
                  break;
                default :
                  // Just test for existence of attribute
                  $ok = $found->hasAttribute($attrName);
              }

              if (false == $ok)
              {
                break;
              }
            }

            if ($ok)
            {
              $nodes[] = $found;
            }
          }

          continue; // Skip to next token
        }

        // If we get here, token is JUST an element (not a class or ID selector)
        $nodes = $this->getElementsByTagName($nodes, $token, $combinator);

        $nodes = $this->matchMultipleCustomSelectors($nodes, $selector);
      }

      foreach ($nodes as $node)
      {
        if (!$node->getAttribute('sf_matched'))
        {
          $node->setAttribute('sf_matched', true);
          $all_nodes[] = $node;
        }
      }
    }

    return $all_nodes;
  }

  protected function getElementsByTagName($nodes, $tagName, $combinator = ' ')
  {
    $founds = array();
    foreach ($nodes as $node)
    {
      switch ($combinator)
      {
        case ' ':
          // Descendant selector
          foreach ($node->getElementsByTagName($tagName) as $element)
          {
            $founds[] = $element;
          }
          break;
        case '>':
          // Child selector
          foreach ($node->childNodes as $element)
          {
            if ($tagName == $element->nodeName)
            {
              $founds[] = $element;
            }
          }
          break;
        case '+':
          // Adjacent selector
          $element = $node->nextSibling;
          if ($element && '#text' == $element->nodeName)
          {
            $element = $element->nextSibling;
          }

          if ($element && $tagName == $element->nodeName)
          {
            $founds[] = $element;
          }
          break;
        default:
          throw new Exception(sprintf('Unrecognized combinator "%s".', $combinator));
      }
    }

    return $founds;
  }

  protected function tokenize_selectors($selector)
  {
    // split tokens by , except in an attribute selector
    $tokens = array();
    $quoted = false;
    $token = '';
    for ($i = 0, $max = strlen($selector); $i < $max; $i++)
    {
      if (',' == $selector[$i] && !$quoted)
      {
        $tokens[] = trim($token);
        $token = '';
      }
      else if ('"' == $selector[$i])
      {
        $token .= $selector[$i];
        $quoted = $quoted ? false : true;
      }
      else
      {
        $token .= $selector[$i];
      }
    }
    if ($token)
    {
      $tokens[] = trim($token);
    }

    return $tokens;
  }

  protected function tokenize($selector)
  {
    // split tokens by space except if space is in an attribute selector
    $tokens = array();
    $combinators = array(' ', '>', '+');
    $quoted = false;
    $token = array('combinator' => ' ', 'name' => '');
    for ($i = 0, $max = strlen($selector); $i < $max; $i++)
    {
      if (in_array($selector[$i], $combinators) && !$quoted)
      {
        // remove all whitespaces around the combinator
        $combinator = $selector[$i];
        while (in_array($selector[$i + 1], $combinators))
        {
          if (' ' != $selector[++$i])
          {
            $combinator = $selector[$i];
          }
        }

        $tokens[] = $token;
        $token = array('combinator' => $combinator, 'name' => '');
      }
      else if ('"' == $selector[$i])
      {
        $token['name'] .= $selector[$i];
        $quoted = $quoted ? false : true;
      }
      else
      {
        $token['name'] .= $selector[$i];
      }
    }
    if ($token['name'])
    {
      $tokens[] = $token;
    }

    foreach ($tokens as &$token)
    {
      list($token['name'], $token['selector']) = $this->tokenize_selector_name($token['name']);
    }

    return $tokens;
  }

  protected function tokenize_selector_name($token_name)
  {
    // split custom selector
    $quoted = false;
    $name = '';
    $selector = '';
    $in_selector = false;
    for ($i = 0, $max = strlen($token_name); $i < $max; $i++)
    {
      if ('"' == $token_name[$i])
      {
        $quoted = $quoted ? false : true;
      }

      if (!$quoted && ':' == $token_name[$i])
      {
        $in_selector = true;
      }

      if ($in_selector)
      {
        $selector .= $token_name[$i];
      }
      else
      {
        $name .= $token_name[$i];
      }
    }

    return array($name, $selector);
  }

  protected function matchMultipleCustomSelectors($nodes, $selector)
  {
    if (!$selector)
    {
      return $nodes;
    }

    foreach ($this->split_custom_selector($selector) as $selector) {
      $nodes = $this->matchCustomSelector($nodes, $selector);
    }
    return $nodes;
  }

  protected function matchCustomSelector($nodes, $selector)
  {
    if (!$selector)
    {
      return $nodes;
    }

    $selector = $this->tokenize_custom_selector($selector);
    $matchingNodes = array();
    for ($i = 0, $max = count($nodes); $i < $max; $i++)
    {
      switch ($selector['selector'])
      {
        case 'contains':
          if (false !== strpos($nodes[$i]->textContent, $selector['parameter']))
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'nth-child':
          if ($nodes[$i] === $this->nth($nodes[$i]->parentNode->firstChild, (integer) $selector['parameter']))
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'first-child':
          if ($nodes[$i] === $this->nth($nodes[$i]->parentNode->firstChild))
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'last-child':
          if ($nodes[$i] === $this->nth($nodes[$i]->parentNode->lastChild, 1, 'previousSibling'))
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'lt':
          if ($i < (integer) $selector['parameter'])
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'gt':
          if ($i > (integer) $selector['parameter'])
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'odd':
          if ($i % 2)
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'even':
          if (0 == $i % 2)
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'nth':
        case 'eq':
          if ($i == (integer) $selector['parameter'])
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'first':
          if ($i == 0)
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        case 'last':
          if ($i == $max - 1)
          {
            $matchingNodes[] = $nodes[$i];
          }
          break;
        default:
          throw new Exception(sprintf('Unrecognized selector "%s".', $selector['selector']));
      }
    }

    return $matchingNodes;
  }

  protected function split_custom_selector($selectors)
  {
    if (!preg_match_all('/
      :
      (?:[a-zA-Z0-9\-]+)
      (?:
        \(
          (?:
            ("|\')(?:.*?)?\1
            |
            (?:.*?)
          )
        \)
      )?
    /x', $selectors, $matches, PREG_PATTERN_ORDER))
    {
      throw new Exception(sprintf('Unable to split custom selector "%s".', $selectors));
    }
    return $matches[0];
  }

  protected function tokenize_custom_selector($selector)
  {
    if (!preg_match('/
      ([a-zA-Z0-9\-]+)
      (?:
        \(
          (?:
            ("|\')(.*)?\2
            |
            (.*?)
          )
        \)
      )?
    /x', substr($selector, 1), $matches))
    {
      throw new Exception(sprintf('Unable to parse custom selector "%s".', $selector));
    }
    return array('selector' => $matches[1], 'parameter' => isset($matches[3]) ? ($matches[3] ? $matches[3] : $matches[4]) : '');
  }

  protected function nth($cur, $result = 1, $dir = 'nextSibling')
  {
    $num = 0;
    for (; $cur; $cur = $cur->$dir)
    {
      if (1 == $cur->nodeType)
      {
        ++$num;
      }

      if ($num == $result)
      {
        return $cur;
      }
    }
  }

  /**
   * Reset the array to the beginning (as required for the Iterator interface).
   */
  public function rewind()
  {
    reset($this->nodes);

    $this->count = count($this->nodes);
  }

  /**
   * Get the key associated with the current value (as required by the Iterator interface).
   *
   * @return string The key
   */
  public function key()
  {
    return key($this->nodes);
  }

  /**
   * Escapes and return the current value (as required by the Iterator interface).
   *
   * @return mixed The escaped value
   */
  public function current()
  {
    return current($this->nodes);
  }

  /**
   * Moves to the next element (as required by the Iterator interface).
   */
  public function next()
  {
    next($this->nodes);

    $this->count --;
  }

  /**
   * Returns true if the current element is valid (as required by the Iterator interface).
   *
   * The current element will not be valid if {@link next()} has fallen off the
   * end of the array or if there are no elements in the array and {@link
   * rewind()} was called.
   *
   * @return bool The validity of the current element; true if it is valid
   */
  public function valid()
  {
    return $this->count > 0;
  }

  /**
   * Returns the number of matching nodes (implements Countable).
   *
   * @param integer The number of matching nodes
   */
  public function count()
  {
    return count($this->nodes);
  }
}
