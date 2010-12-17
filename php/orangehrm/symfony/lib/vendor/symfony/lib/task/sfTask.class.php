<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract class for all tasks.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTask.class.php 30773 2010-08-27 19:27:41Z Kris.Wallsmith $
 */
abstract class sfTask
{
  protected
    $namespace           = '',
    $name                = null,
    $aliases             = array(),
    $briefDescription    = '',
    $detailedDescription = '',
    $arguments           = array(),
    $options             = array(),
    $dispatcher          = null,
    $formatter           = null;

  /**
   * Constructor.
   *
   * @param sfEventDispatcher $dispatcher  An sfEventDispatcher instance
   * @param sfFormatter       $formatter   An sfFormatter instance
   */
  public function __construct(sfEventDispatcher $dispatcher, sfFormatter $formatter)
  {
    $this->initialize($dispatcher, $formatter);

    $this->configure();
  }

  /**
   * Initializes the sfTask instance.
   *
   * @param sfEventDispatcher $dispatcher  A sfEventDispatcher instance
   * @param sfFormatter       $formatter   A sfFormatter instance
   */
  public function initialize(sfEventDispatcher $dispatcher, sfFormatter $formatter)
  {
    $this->dispatcher = $dispatcher;
    $this->formatter  = $formatter;
  }

  /**
   * Configures the current task.
   */
  protected function configure()
  {
  }

  /**
   * Returns the formatter instance.
   *
   * @return sfFormatter The formatter instance
   */
  public function getFormatter()
  {
    return $this->formatter;
  }

  /**
   * Sets the formatter instance.
   *
   * @param sfFormatter The formatter instance
   */
  public function setFormatter(sfFormatter $formatter)
  {
    $this->formatter = $formatter;
  }

  /**
   * Runs the task from the CLI.
   *
   * @param sfCommandManager $commandManager  An sfCommandManager instance
   * @param mixed            $options         The command line options
   *
   * @return integer 0 if everything went fine, or an error code
   */
  public function runFromCLI(sfCommandManager $commandManager, $options = null)
  {
    $commandManager->getArgumentSet()->addArguments($this->getArguments());
    $commandManager->getOptionSet()->addOptions($this->getOptions());

    return $this->doRun($commandManager, $options);
  }

  /**
   * Runs the task.
   *
   * @param array|string $arguments  An array of arguments or a string representing the CLI arguments and options
   * @param array        $options    An array of options
   *
   * @return integer 0 if everything went fine, or an error code
   */
  public function run($arguments = array(), $options = array())
  {
    $commandManager = new sfCommandManager(new sfCommandArgumentSet($this->getArguments()), new sfCommandOptionSet($this->getOptions()));

    if (is_array($arguments) && is_string(key($arguments)))
    {
      // index arguments by name for ordering and reference
      $indexArguments = array();
      foreach ($this->arguments as $argument)
      {
        $indexArguments[$argument->getName()] = $argument;
      }

      foreach ($arguments as $name => $value)
      {
        if (false !== $pos = array_search($name, array_keys($indexArguments)))
        {
          if ($indexArguments[$name]->isArray())
          {
            $value = join(' ', (array) $value);
            $arguments[$pos] = isset($arguments[$pos]) ? $arguments[$pos].' '.$value : $value;
          }
          else
          {
            $arguments[$pos] = $value;
          }

          unset($arguments[$name]);
        }
      }

      ksort($arguments);
    }

    // index options by name for reference
    $indexedOptions = array();
    foreach ($this->options as $option)
    {
      $indexedOptions[$option->getName()] = $option;
    }

    foreach ($options as $name => $value)
    {
      if (is_string($name))
      {
        if (false === $value || null === $value || (isset($indexedOptions[$name]) && $indexedOptions[$name]->isArray() && !$value))
        {
          unset($options[$name]);
          continue;
        }

        // convert associative array
        $value = true === $value ? $name : sprintf('%s=%s', $name, isset($indexedOptions[$name]) && $indexedOptions[$name]->isArray() ? join(' --'.$name.'=', (array) $value) : $value);
      }

      // add -- before each option if needed
      if (0 !== strpos($value, '--'))
      {
        $value = '--'.$value;
      }

      $options[] = $value;
      unset($options[$name]);
    }

    return $this->doRun($commandManager, is_string($arguments) ? $arguments : implode(' ', array_merge($arguments, $options)));
  }

  /**
   * Returns the argument objects.
   *
   * @return sfCommandArgument An array of sfCommandArgument objects.
   */
  public function getArguments()
  {
    return $this->arguments;
  }

  /**
   * Adds an array of argument objects.
   *
   * @param array $arguments  An array of arguments
   */
  public function addArguments($arguments)
  {
    $this->arguments = array_merge($this->arguments, $arguments);
  }

  /**
   * Add an argument.
   *
   * This method always use the sfCommandArgument class to create an option.
   *
   * @see sfCommandArgument::__construct()
   */
  public function addArgument($name, $mode = null, $help = '', $default = null)
  {
    $this->arguments[] = new sfCommandArgument($name, $mode, $help, $default);
  }

  /**
   * Returns the options objects.
   *
   * @return sfCommandOption An array of sfCommandOption objects.
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Adds an array of option objects.
   *
   * @param array $options    An array of options
   */
  public function addOptions($options)
  {
    $this->options = array_merge($this->options, $options);
  }

  /**
   * Add an option.
   *
   * This method always use the sfCommandOption class to create an option.
   *
   * @see sfCommandOption::__construct()
   */
  public function addOption($name, $shortcut = null, $mode = null, $help = '', $default = null)
  {
    $this->options[] = new sfCommandOption($name, $shortcut, $mode, $help, $default);
  }

  /**
   * Returns the task namespace.
   *
   * @return string The task namespace
   */
  public function getNamespace()
  {
    return $this->namespace;
  }

  /**
   * Returns the task name
   *
   * @return string The task name
   */
  public function getName()
  {
    if ($this->name)
    {
      return $this->name;
    }

    $name = get_class($this);

    if ('sf' == substr($name, 0, 2))
    {
      $name = substr($name, 2);
    }

    if ('Task' == substr($name, -4))
    {
      $name = substr($name, 0, -4);
    }

    return str_replace('_', '-', sfInflector::underscore($name));
  }

  /**
   * Returns the fully qualified task name.
   *
   * @return string The fully qualified task name
   */
  final function getFullName()
  {
    return $this->getNamespace() ? $this->getNamespace().':'.$this->getName() : $this->getName();
  }

  /**
   * Returns the brief description for the task.
   *
   * @return string The brief description for the task
   */
  public function getBriefDescription()
  {
    return $this->briefDescription;
  }

  /**
   * Returns the detailed description for the task.
   *
   * It also formats special string like [...|COMMENT]
   * depending on the current formatter.
   *
   * @return string The detailed description for the task
   */
  public function getDetailedDescription()
  {
    return preg_replace('/\[(.+?)\|(\w+)\]/se', '$this->formatter->format("$1", "$2")', $this->detailedDescription);
  }

  /**
   * Returns the aliases for the task.
   *
   * @return array An array of aliases for the task
   */
  public function getAliases()
  {
    return $this->aliases;
  }

  /**
   * Returns the synopsis for the task.
   *
   * @return string The synopsis
   */
  public function getSynopsis()
  {
    $options = array();
    foreach ($this->getOptions() as $option)
    {
      $shortcut = $option->getShortcut() ? sprintf('-%s|', $option->getShortcut()) : '';
      $options[] = sprintf('['.($option->isParameterRequired() ? '%s--%s="..."' : ($option->isParameterOptional() ? '%s--%s[="..."]' : '%s--%s')).']', $shortcut, $option->getName());
    }

    $arguments = array();
    foreach ($this->getArguments() as $argument)
    {
      $arguments[] = sprintf($argument->isRequired() ? '%s' : '[%s]', $argument->getName().($argument->isArray() ? '1' : ''));

      if ($argument->isArray())
      {
        $arguments[] = sprintf('... [%sN]', $argument->getName());
      }
    }

    return sprintf('%%s %s %s %s', $this->getFullName(), implode(' ', $options), implode(' ', $arguments));
  }

  protected function process(sfCommandManager $commandManager, $options)
  {
    $commandManager->process($options);
    if (!$commandManager->isValid())
    {
      throw new sfCommandArgumentsException(sprintf("The execution of task \"%s\" failed.\n- %s", $this->getFullName(), implode("\n- ", $commandManager->getErrors())));
    }
  }

  protected function doRun(sfCommandManager $commandManager, $options)
  {
    $event = $this->dispatcher->filter(new sfEvent($this, 'command.filter_options', array('command_manager' => $commandManager)), $options);
    $options = $event->getReturnValue();

    $this->process($commandManager, $options);

    $event = new sfEvent($this, 'command.pre_command', array('arguments' => $commandManager->getArgumentValues(), 'options' => $commandManager->getOptionValues()));
    $this->dispatcher->notifyUntil($event);
    if ($event->isProcessed())
    {
      return $event->getReturnValue();
    }

    $ret = $this->execute($commandManager->getArgumentValues(), $commandManager->getOptionValues());

    $this->dispatcher->notify(new sfEvent($this, 'command.post_command'));

    return $ret;
  }

  /**
   * Logs a message.
   *
   * @param mixed $messages  The message as an array of lines of a single string
   */
  public function log($messages)
  {
    if (!is_array($messages))
    {
      $messages = array($messages);
    }

    $this->dispatcher->notify(new sfEvent($this, 'command.log', $messages));
  }

  /**
   * Logs a message in a section.
   *
   * @param string  $section  The section name
   * @param string  $message  The message
   * @param int     $size     The maximum size of a line
   * @param string  $style    The color scheme to apply to the section string (INFO, ERROR, or COMMAND)
   */
  public function logSection($section, $message, $size = null, $style = 'INFO')
  {
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection($section, $message, $size, $style))));
  }

  /**
   * Logs a message as a block of text.
   *
   * @param string|array $messages The message to display in the block
   * @param string       $style    The style to use
   */
  public function logBlock($messages, $style)
  {
    if (!is_array($messages))
    {
      $messages = array($messages);
    }

    $style = str_replace('_LARGE', '', $style, $count);
    $large = (Boolean) $count;

    $len = 0;
    $lines = array();
    foreach ($messages as $message)
    {
      $lines[] = sprintf($large ? '  %s  ' : ' %s ', $message);
      $len = max($this->strlen($message) + ($large ? 4 : 2), $len);
    }

    $messages = $large ? array(str_repeat(' ', $len)) : array();
    foreach ($lines as $line)
    {
      $messages[] = $line.str_repeat(' ', $len - $this->strlen($line));
    }
    if ($large)
    {
      $messages[] = str_repeat(' ', $len);
    }

    foreach ($messages as $message)
    {
      $this->log($this->formatter->format($message, $style));
    }
  }

  /**
   * Asks a question to the user.
   *
   * @param string|array $question The question to ask
   * @param string       $style    The style to use (QUESTION by default)
   * @param string       $default  The default answer if none is given by the user
   *
   * @param string       The user answer
   */
  public function ask($question, $style = 'QUESTION', $default = null)
  {
    if (false === $style)
    {
      $this->log($question);
    }
    else
    {
      $this->logBlock($question, null === $style ? 'QUESTION' : $style);
    }

    $ret = trim(fgets(STDIN));

    return $ret ? $ret : $default;
  }

  /**
   * Asks a confirmation to the user.
   *
   * The question will be asked until the user answer by nothing, yes, or no.
   *
   * @param string|array $question The question to ask
   * @param string       $style    The style to use (QUESTION by default)
   * @param Boolean      $default  The default answer if the user enters nothing
   *
   * @param Boolean      true if the user has confirmed, false otherwise
   */
  public function askConfirmation($question, $style = 'QUESTION', $default = true)
  {
    $answer = 'z';
    while ($answer && !in_array(strtolower($answer[0]), array('y', 'n')))
    {
      $answer = $this->ask($question, $style);
    }

    if (false === $default)
    {
      return $answer && 'y' == strtolower($answer[0]);
    }
    else
    {
      return !$answer || 'y' == strtolower($answer[0]);
    }
  }

  /**
   * Asks for a value and validates the response.
   *
   * Available options:
   *
   *  * value:    A value to try against the validator before asking the user
   *  * attempts: Max number of times to ask before giving up (false by default, which means infinite)
   *  * style:    Style for question output (QUESTION by default)
   *
   * @param   string|array    $question
   * @param   sfValidatorBase $validator
   * @param   array           $options
   *
   * @return  mixed
   */
  public function askAndValidate($question, sfValidatorBase $validator, array $options = array())
  {
    if (!is_array($question))
    {
      $question = array($question);
    }

    $options = array_merge(array(
      'value'    => null,
      'attempts' => false,
      'style'    => 'QUESTION',
    ), $options);

    // does the provided value passes the validator?
    if ($options['value'])
    {
      try
      {
        return $validator->clean($options['value']);
      }
      catch (sfValidatorError $error)
      {
      }
    }

    // no, ask the user for a valid user
    $error = null;
    while (false === $options['attempts'] || $options['attempts']--)
    {
      if (null !== $error)
      {
        $this->logBlock($error->getMessage(), 'ERROR');
      }

      $value = $this->ask($question, $options['style'], null);

      try
      {
        return $validator->clean($value);
      }
      catch (sfValidatorError $error)
      {
      }
    }

    throw $error;
  }

  /**
   * Returns an XML representation of a task.
   *
   * @return string An XML string representing the task
   */
  public function asXml()
  {
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    $dom->appendChild($taskXML = $dom->createElement('task'));
    $taskXML->setAttribute('id', $this->getFullName());
    $taskXML->setAttribute('namespace', $this->getNamespace() ? $this->getNamespace() : '_global');
    $taskXML->setAttribute('name', $this->getName());

    $taskXML->appendChild($usageXML = $dom->createElement('usage'));
    $usageXML->appendChild($dom->createTextNode(sprintf($this->getSynopsis(), '')));

    $taskXML->appendChild($descriptionXML = $dom->createElement('description'));
    $descriptionXML->appendChild($dom->createTextNode(implode("\n ", explode("\n", $this->getBriefDescription()))));

    $taskXML->appendChild($helpXML = $dom->createElement('help'));
    $help = $this->detailedDescription;
    $help = str_replace(array('|COMMENT', '|INFO'), array('|strong', '|em'), $help);
    $help = preg_replace('/\[(.+?)\|(\w+)\]/s', '<$2>$1</$2>', $help);
    $helpXML->appendChild($dom->createTextNode(implode("\n ", explode("\n", $help))));

    $taskXML->appendChild($aliasesXML = $dom->createElement('aliases'));
    foreach ($this->getAliases() as $alias)
    {
      $aliasesXML->appendChild($aliasXML = $dom->createElement('alias'));
      $aliasXML->appendChild($dom->createTextNode($alias));
    }

    $taskXML->appendChild($argumentsXML = $dom->createElement('arguments'));
    foreach ($this->getArguments() as $argument)
    {
      $argumentsXML->appendChild($argumentXML = $dom->createElement('argument'));
      $argumentXML->setAttribute('name', $argument->getName());
      $argumentXML->setAttribute('is_required', $argument->isRequired() ? 1 : 0);
      $argumentXML->setAttribute('is_array', $argument->isArray() ? 1 : 0);
      $argumentXML->appendChild($helpXML = $dom->createElement('description'));
      $helpXML->appendChild($dom->createTextNode($argument->getHelp()));

      $argumentXML->appendChild($defaultsXML = $dom->createElement('defaults'));
      $defaults = is_array($argument->getDefault()) ? $argument->getDefault() : ($argument->getDefault() ? array($argument->getDefault()) : array());
      foreach ($defaults as $default)
      {
        $defaultsXML->appendChild($defaultXML = $dom->createElement('default'));
        $defaultXML->appendChild($dom->createTextNode($default));
      }
    }

    $taskXML->appendChild($optionsXML = $dom->createElement('options'));
    foreach ($this->getOptions() as $option)
    {
      $optionsXML->appendChild($optionXML = $dom->createElement('option'));
      $optionXML->setAttribute('name', '--'.$option->getName());
      $optionXML->setAttribute('shortcut', $option->getShortcut() ? '-'.$option->getShortcut() : '');
      $optionXML->setAttribute('accept_parameter', $option->acceptParameter() ? 1 : 0);
      $optionXML->setAttribute('is_parameter_required', $option->isParameterRequired() ? 1 : 0);
      $optionXML->setAttribute('is_multiple', $option->isArray() ? 1 : 0);
      $optionXML->appendChild($helpXML = $dom->createElement('description'));
      $helpXML->appendChild($dom->createTextNode($option->getHelp()));

      if ($option->acceptParameter())
      {
        $optionXML->appendChild($defaultsXML = $dom->createElement('defaults'));
        $defaults = is_array($option->getDefault()) ? $option->getDefault() : ($option->getDefault() ? array($option->getDefault()) : array());
        foreach ($defaults as $default)
        {
          $defaultsXML->appendChild($defaultXML = $dom->createElement('default'));
          $defaultXML->appendChild($dom->createTextNode($default));
        }
      }
    }

    return $dom->saveXml();
  }

  /**
   * Executes the current task.
   *
   * @param array    $arguments  An array of arguments
   * @param array    $options    An array of options
   *
   * @return integer 0 if everything went fine, or an error code
   */
   abstract protected function execute($arguments = array(), $options = array());

   protected function strlen($string)
   {
     return function_exists('mb_strlen') ? mb_strlen($string) : strlen($string);
   }
}
