<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRoute represents a route.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfRoute.class.php 32939 2011-08-22 13:40:06Z fabien $
 */
class sfRoute implements Serializable
{
  protected
    $isBound           = false,
    $context           = null,
    $parameters        = null,
    $suffix            = null,
    $defaultParameters = array(),
    $defaultOptions    = array(),
    $compiled          = false,
    $options           = array(),
    $pattern           = null,
    $staticPrefix      = null,
    $regex             = null,
    $variables         = array(),
    $defaults          = array(),
    $requirements      = array(),
    $tokens            = array(),
    $customToken       = false;

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * variable_prefixes:                An array of characters that starts a variable name (: by default)
   *  * segment_separators:               An array of allowed characters for segment separators (/ and . by default)
   *  * variable_regex:                   A regex that match a valid variable name ([\w\d_]+ by default)
   *  * generate_shortest_url:            Whether to generate the shortest URL possible (true by default)
   *  * extra_parameters_as_query_string: Whether to generate extra parameters as a query string
   *
   * @param string $pattern       The pattern to match
   * @param array  $defaults      An array of default parameter values
   * @param array  $requirements  An array of requirements for parameters (regexes)
   * @param array  $options       An array of options
   */
  public function __construct($pattern, array $defaults = array(), array $requirements = array(), array $options = array())
  {
    $this->pattern      = trim($pattern);
    $this->defaults     = $defaults;
    $this->requirements = $requirements;
    $this->options      = $options;
  }

  /**
   * Binds the current route for a given context and parameters.
   *
   * @param array $context    The context
   * @param array $parameters The parameters
   */
  public function bind($context, $parameters)
  {
    $this->isBound    = true;
    $this->context    = $context;
    $this->parameters = $parameters;
  }

  /**
   * Returns true if the route is bound to context and parameters.
   *
   * @return Boolean true if theroute is bound to context and parameters, false otherwise
   */
  public function isBound()
  {
    return $this->isBound;
  }

  /**
   * Returns an array of parameters if the URL matches this route, false otherwise.
   *
   * @param  string  $url     The URL
   * @param  array   $context The context
   *
   * @return array   An array of parameters
   */
  public function matchesUrl($url, $context = array())
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    // check the static prefix uf the URL first. Only use the more expensive preg_match when it matches
    if ('' !== $this->staticPrefix  && 0 !== strpos($url, $this->staticPrefix))
    {
      return false;
    }
    if (!preg_match($this->regex, $url, $matches))
    {
      return false;
    }

    $defaults   = array_merge($this->getDefaultParameters(), $this->defaults);
    $parameters = array();

    // *
    if (isset($matches['_star']))
    {
      $parameters = $this->parseStarParameter($matches['_star']);
      unset($matches['_star'], $parameters['module'], $parameters['action']);
    }

    // defaults
    $parameters = $this->mergeArrays($defaults, $parameters);

    // variables
    foreach ($matches as $key => $value)
    {
      if (!is_int($key))
      {
        $parameters[$key] = urldecode($value);
      }
    }

    return $parameters;
  }

  /**
   * Returns true if the parameters matches this route, false otherwise.
   *
   * @param  mixed  $params  The parameters
   * @param  array  $context The context
   *
   * @return Boolean         true if the parameters matches this route, false otherwise.
   */
  public function matchesParameters($params, $context = array())
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    if (!is_array($params))
    {
      return false;
    }

    $defaults = $this->mergeArrays($this->getDefaultParameters(), $this->defaults);
    $tparams = $this->mergeArrays($defaults, $params);

    // all $variables must be defined in the $tparams array
    if (array_diff_key($this->variables, $tparams))
    {
      return false;
    }

    // check requirements
    foreach (array_keys($this->variables) as $variable)
    {
      if (!$tparams[$variable])
      {
        continue;
      }

      if (!preg_match('#'.$this->requirements[$variable].'#', $tparams[$variable]))
      {
        return false;
      }
    }

    // all $params must be in $variables or $defaults if there is no * in route
    if (!$this->options['extra_parameters_as_query_string'])
    {
      if (false === strpos($this->regex, '<_star>') && array_diff_key($params, $this->variables, $defaults))
      {
        return false;
      }
    }

    // check that $params does not override a default value that is not a variable
    foreach ($defaults as $key => $value)
    {
      if (!isset($this->variables[$key]) && $tparams[$key] != $value)
      {
        return false;
      }
    }

    return true;
  }

  /**
   * Generates a URL from the given parameters.
   *
   * @param  mixed   $params    The parameter values
   * @param  array   $context   The context
   * @param  Boolean $absolute  Whether to generate an absolute URL
   *
   * @return string The generated URL
   */
  public function generate($params, $context = array(), $absolute = false)
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    $url = $this->pattern;

    $defaults = $this->mergeArrays($this->getDefaultParameters(), $this->defaults);
    $tparams = $this->mergeArrays($defaults, $params);

    // all params must be given
    if ($diff = array_diff_key($this->variables, $tparams))
    {
      throw new InvalidArgumentException(sprintf('The "%s" route has some missing mandatory parameters (%s).', $this->pattern, implode(', ', $diff)));
    }

    if ($this->options['generate_shortest_url'] || $this->customToken)
    {
      $url = $this->generateWithTokens($tparams);
    }
    else
    {
      // replace variables
      $variables = $this->variables;
      uasort($variables, array('sfRoute', 'generateCompareVarsByStrlen'));
      foreach ($variables as $variable => $value)
      {
        $url = str_replace($value, urlencode($tparams[$variable]), $url);
      }

      if(!in_array($this->suffix, $this->options['segment_separators']))
      {
        $url .= $this->suffix;
      }
    }

    // replace extra parameters if the route contains *
    $url = $this->generateStarParameter($url, $defaults, $tparams);

    if ($this->options['extra_parameters_as_query_string'] && !$this->hasStarParameter())
    {
      // add a query string if needed
      if ($extra = array_diff_key($params, $this->variables, $defaults))
      {
        $url .= '?'.http_build_query($extra);
      }
    }

    return $url;
  }

  static private function generateCompareVarsByStrlen($a, $b)
  {
    return strlen($a) < strlen($b);
  }

  /**
   * Generates a URL for the given parameters by using the route tokens.
   *
   * @param array $parameters An array of parameters
   */
  protected function generateWithTokens($parameters)
  {
    $url = array();
    $optional = $this->options['generate_shortest_url'];
    $first = true;
    $tokens = array_reverse($this->tokens);
    foreach ($tokens as $token)
    {
      switch ($token[0])
      {
        case 'variable':
          if (!$optional || !isset($this->defaults[$token[3]]) || $parameters[$token[3]] != $this->defaults[$token[3]])
          {
            $url[] = urlencode($parameters[$token[3]]);
            $optional = false;
          }
          break;
        case 'text':
          $url[] = $token[2];
          $optional = false;
          break;
        case 'separator':
          if (false === $optional || $first)
          {
            $url[] = $token[2];
          }
          break;
        default:
          // handle custom tokens
          if ($segment = call_user_func_array(array($this, 'generateFor'.ucfirst(array_shift($token))), array_merge(array($optional, $parameters), $token)))
          {
            $url[] = $segment;
            $optional = false;
          }
          break;
      }

      $first = false;
    }

    $url = implode('', array_reverse($url));
    if (!$url)
    {
      $url = '/';
    }

    return $url;
  }

  /**
   * Returns the route parameters.
   *
   * @return array The route parameters
   */
  public function getParameters()
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    return $this->parameters;
  }

  /**
   * Returns the compiled pattern.
   *
   * @return string The compiled pattern
   */
  public function getPattern()
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    return $this->pattern;
  }

  /**
   * Returns the compiled regex.
   *
   * @return string The compiled regex
   */
  public function getRegex()
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    return $this->regex;
  }

  /**
   * Returns the compiled tokens.
   *
   * @return array The compiled tokens
   */
  public function getTokens()
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    return $this->tokens;
  }

  /**
   * Returns the compiled options.
   *
   * @return array The compiled options
   */
  public function getOptions()
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    return $this->options;
  }

  /**
   * Returns the compiled variables.
   *
   * @return array The compiled variables
   */
  public function getVariables()
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    return $this->variables;
  }

  /**
   * Returns the compiled defaults.
   *
   * @return array The compiled defaults
   */
  public function getDefaults()
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    return $this->defaults;
  }

  /**
   * Returns the compiled requirements.
   *
   * @return array The compiled requirements
   */
  public function getRequirements()
  {
    if (!$this->compiled)
    {
      $this->compile();
    }

    return $this->requirements;
  }

  /**
   * Compiles the current route instance.
   */
  public function compile()
  {
    if ($this->compiled)
    {
      return;
    }

    $this->initializeOptions();
    $this->fixRequirements();
    $this->fixDefaults();
    $this->fixSuffix();

    $this->compiled = true;
    $this->firstOptional = 0;
    $this->segments = array();

    $this->preCompile();

    $this->tokenize();

    // parse
    foreach ($this->tokens as $token)
    {
      call_user_func_array(array($this, 'compileFor'.ucfirst(array_shift($token))), $token);
    }

    $this->postCompile();

    $separator = '';
    if (count($this->tokens))
    {
      $lastToken = $this->tokens[count($this->tokens) - 1];
      $separator = 'separator' == $lastToken[0] ? $lastToken[2] : '';
    }

    $this->regex = "#^".implode("", $this->segments)."".preg_quote($separator, '#')."$#x";
  }

  /**
   * Pre-compiles a route.
   */
  protected function preCompile()
  {
    // a route must start with a slash
    if (empty($this->pattern) || '/' != $this->pattern[0])
    {
      $this->pattern = '/'.$this->pattern;
    }
  }

  /**
   * Post-compiles a route.
   */
  protected function postCompile()
  {
    // all segments after the last static segment are optional
    // be careful, the n-1 is optional only if n is empty
    for ($i = $this->firstOptional, $max = count($this->segments); $i < $max; $i++)
    {
      $this->segments[$i] = (0 == $i ? '/?' : '').str_repeat(' ', $i - $this->firstOptional).'(?:'.$this->segments[$i];
      $this->segments[] = str_repeat(' ', $max - $i - 1).')?';
    }

    $this->staticPrefix = '';
    foreach ($this->tokens as $token)
    {
      switch ($token[0])
      {
        case 'separator':
          break;
        case 'text':
          if ($token[2] !== '*')
          {
            // non-star text is static
            $this->staticPrefix .= $token[1].$token[2];
            break;
          }
        default:
          // everything else indicates variable parts. break switch and for loop
          break 2;
      }
    }
  }

  /**
   * Tokenizes the route.
   */
  protected function tokenize()
  {
    $this->tokens = array();
    $buffer = $this->pattern;
    $afterASeparator = false;
    $currentSeparator = '';

    // a route is an array of (separator + variable) or (separator + text) segments
    while (strlen($buffer))
    {
      if (false !== $this->tokenizeBufferBefore($buffer, $this->tokens, $afterASeparator, $currentSeparator))
      {
        // a custom token
        $this->customToken = true;
      }
      else if ($afterASeparator && preg_match('#^'.$this->options['variable_prefix_regex'].'('.$this->options['variable_regex'].')#', $buffer, $match))
      {
        // a variable
        $this->tokens[] = array('variable', $currentSeparator, $match[0], $match[1]);

        $currentSeparator = '';
        $buffer = substr($buffer, strlen($match[0]));
        $afterASeparator = false;
      }
      else if ($afterASeparator && preg_match('#^('.$this->options['text_regex'].')(?:'.$this->options['segment_separators_regex'].'|$)#', $buffer, $match))
      {
        // a text
        $this->tokens[] = array('text', $currentSeparator, $match[1], null);

        $currentSeparator = '';
        $buffer = substr($buffer, strlen($match[1]));
        $afterASeparator = false;
      }
      else if (!$afterASeparator && preg_match('#^/|^'.$this->options['segment_separators_regex'].'#', $buffer, $match))
      {
        // beginning of URL (^/) or a separator
        $this->tokens[] = array('separator', $currentSeparator, $match[0], null);

        $currentSeparator = $match[0];
        $buffer = substr($buffer, strlen($match[0]));
        $afterASeparator = true;
      }
      else if (false !== $this->tokenizeBufferAfter($buffer, $this->tokens, $afterASeparator, $currentSeparator))
      {
        // a custom token
        $this->customToken = true;
      }
      else
      {
        // parsing problem
        throw new InvalidArgumentException(sprintf('Unable to parse "%s" route near "%s".', $this->pattern, $buffer));
      }
    }
    
    // check for suffix
    if ($this->suffix)
    {
      // treat as a separator
      $this->tokens[] = array('separator', $currentSeparator, $this->suffix);
    }

  }

  /**
   * Tokenizes the buffer before default logic is applied.
   *
   * This method must return false if the buffer has not been parsed.
   *
   * @param string   $buffer           The current route buffer
   * @param array    $tokens           An array of current tokens
   * @param Boolean  $afterASeparator  Whether the buffer is just after a separator
   * @param string   $currentSeparator The last matched separator
   *
   * @return Boolean true if a token has been generated, false otherwise
   */
  protected function tokenizeBufferBefore(&$buffer, &$tokens, &$afterASeparator, &$currentSeparator)
  {
    return false;
  }

  /**
   * Tokenizes the buffer after default logic is applied.
   *
   * This method must return false if the buffer has not been parsed.
   *
   * @param string   $buffer           The current route buffer
   * @param array    $tokens           An array of current tokens
   * @param Boolean  $afterASeparator  Whether the buffer is just after a separator
   * @param string   $currentSeparator The last matched separator
   *
   * @return Boolean true if a token has been generated, false otherwise
   */
  protected function tokenizeBufferAfter(&$buffer, &$tokens, &$afterASeparator, &$currentSeparator)
  {
    return false;
  }

  protected function compileForText($separator, $text)
  {
    if ('*' == $text)
    {
      $this->segments[] = '(?:'.preg_quote($separator, '#').'(?P<_star>.*))?';
    }
    else
    {
      $this->firstOptional = count($this->segments) + 1;

      $this->segments[] = preg_quote($separator, '#').preg_quote($text, '#');
    }
  }

  protected function compileForVariable($separator, $name, $variable)
  {
    if (!isset($this->requirements[$variable]))
    {
      $this->requirements[$variable] = $this->options['variable_content_regex'];
    }

    $this->segments[] = preg_quote($separator, '#').'(?P<'.$variable.'>'.$this->requirements[$variable].')';
    $this->variables[$variable] = $name;

    if (!isset($this->defaults[$variable]))
    {
      $this->firstOptional = count($this->segments);
    }
  }

  protected function compileForSeparator($separator, $regexSeparator)
  {
  }

  public function getDefaultParameters()
  {
    return $this->defaultParameters;
  }

  public function setDefaultParameters($parameters)
  {
    $this->defaultParameters = $parameters;
  }

  public function getDefaultOptions()
  {
    return $this->defaultOptions;
  }

  public function setDefaultOptions($options)
  {
    $this->defaultOptions = $options;
  }

  protected function initializeOptions()
  {
    $this->options = array_merge(array(
      'suffix'                           => '',
      'variable_prefixes'                => array(':'),
      'segment_separators'               => array('/', '.'),
      'variable_regex'                   => '[\w\d_]+',
      'text_regex'                       => '.+?',
      'generate_shortest_url'            => true,
      'extra_parameters_as_query_string' => true,
    ), $this->getDefaultOptions(), $this->options);

    $preg_quote_hash = create_function('$a', 'return preg_quote($a, \'#\');');

    // compute some regexes
    $this->options['variable_prefix_regex'] = '(?:'.implode('|', array_map($preg_quote_hash, $this->options['variable_prefixes'])).')';

    if (count($this->options['segment_separators']))
    {
      $this->options['segment_separators_regex'] = '(?:'.implode('|', array_map($preg_quote_hash, $this->options['segment_separators'])).')';

      // as of PHP 5.3.0, preg_quote automatically quotes dashes "-" (see http://bugs.php.net/bug.php?id=47229)
      $preg_quote_hash_53 = create_function('$a', 'return str_replace(\'-\', \'\-\', preg_quote($a, \'#\'));');
      $this->options['variable_content_regex'] = '[^'.implode('',
          array_map(version_compare(PHP_VERSION, '5.3.0RC4', '>=') ? $preg_quote_hash : $preg_quote_hash_53, $this->options['segment_separators'])
        ).']+';
    }
    else
    {
      // use simplified regexes for case where no separators are used
      $this->options['segment_separators_regex'] = '()';
      $this->options['variable_content_regex']   = '.+';
    }
  }

  protected function parseStarParameter($star)
  {
    $parameters = array();
    $tmp = explode('/', $star);
    for ($i = 0, $max = count($tmp); $i < $max; $i += 2)
    {
      //dont allow a param name to be empty - #4173
      if (!empty($tmp[$i]))
      {
        $parameters[$tmp[$i]] = isset($tmp[$i + 1]) ? urldecode($tmp[$i + 1]) : true;
      }
    }

    return $parameters;
  }

  protected function hasStarParameter()
  {
    return false !== strpos($this->regex, '<_star>');
  }

  protected function generateStarParameter($url, $defaults, $parameters)
  {
    if (false === strpos($this->regex, '<_star>'))
    {
      return $url;
    }

    $tmp = array();
    foreach (array_diff_key($parameters, $this->variables, $defaults) as $key => $value)
    {
      if (is_array($value))
      {
        foreach ($value as $v)
        {
          $tmp[] = $key.'='.urlencode($v);
        }
      }
      else
      {
        $tmp[] = urlencode($key).'/'.urlencode($value);
      }
    }
    $tmp = implode('/', $tmp);
    if ($tmp)
    {
      $tmp = '/'.$tmp;
    }

    return preg_replace('#'.$this->options['segment_separators_regex'].'\*('.$this->options['segment_separators_regex'].'|$)#', "$tmp$1", $url);
  }

  protected function mergeArrays($arr1, $arr2)
  {
    foreach ($arr2 as $key => $value)
    {
      $arr1[$key] = $value;
    }

    return $arr1;
  }

  protected function fixDefaults()
  {
    foreach ($this->defaults as $key => $value)
    {
      if (ctype_digit($key))
      {
        $this->defaults[$value] = true;
      }
      else
      {
        $this->defaults[$key] = urldecode($value);
      }
    }
  }

  protected function fixRequirements()
  {
    foreach ($this->requirements as $key => $regex)
    {
      if (!is_string($regex))
      {
        continue;
      }

      if ('^' == $regex[0])
      {
        $regex = substr($regex, 1);
      }
      if ('$' == substr($regex, -1))
      {
        $regex = substr($regex, 0, -1);
      }

      $this->requirements[$key] = $regex;
    }
  }

  protected function fixSuffix()
  {
    $length = strlen($this->pattern);

    if ($length > 0 && '/' == $this->pattern[$length - 1])
    {
      // route ends by / (directory)
      $this->suffix = '/';
    }
    else if ($length > 0 && '.' == $this->pattern[$length - 1])
    {
      // route ends by . (no suffix)
      $this->suffix = '';
      $this->pattern = substr($this->pattern, 0, $length - 1);
    }
    else if (preg_match('#\.(?:'.$this->options['variable_prefix_regex'].$this->options['variable_regex'].'|'.$this->options['variable_content_regex'].')$#i', $this->pattern))
    {
      // specific suffix for this route
      // a . with a variable after or some chars without any separators
      $this->suffix = '';
    }
    else
    {
      $this->suffix = $this->options['suffix'];
    }
  }

  public function serialize()
  {
    // always serialize compiled routes
    $this->compile();
    // sfPatternRouting will always re-set defaultParameters, so no need to serialize them
    return serialize(array($this->tokens, $this->defaultOptions, $this->options, $this->pattern, $this->staticPrefix, $this->regex, $this->variables, $this->defaults, $this->requirements, $this->suffix, $this->customToken));
  }

  public function unserialize($data)
  {
    list($this->tokens, $this->defaultOptions, $this->options, $this->pattern, $this->staticPrefix, $this->regex, $this->variables, $this->defaults, $this->requirements, $this->suffix, $this->customToken) = unserialize($data);
    $this->compiled = true;
  }
}