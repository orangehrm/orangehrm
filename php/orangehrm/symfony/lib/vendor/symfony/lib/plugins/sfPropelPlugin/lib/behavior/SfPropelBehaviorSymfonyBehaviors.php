<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Adds support for symfony's {@link sfMixer} behaviors.
 *
 * @package     sfPropelPlugin
 * @subpackage  behavior
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id: SfPropelBehaviorSymfonyBehaviors.php 28958 2010-04-01 13:56:17Z fabien $
 */
class SfPropelBehaviorSymfonyBehaviors extends SfPropelBehaviorBase
{
  public function preDelete()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF
foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}:delete:pre') as \$callable)
{
  if (call_user_func(\$callable, \$this, \$con))
  {
    \$con->commit();

    return;
  }
}

EOF;
  }

  public function postDelete()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF
foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}:delete:post') as \$callable)
{
  call_user_func(\$callable, \$this, \$con);
}

EOF;
  }

  public function preSave()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF
foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}:save:pre') as \$callable)
{
  if (is_integer(\$affectedRows = call_user_func(\$callable, \$this, \$con)))
  {
    \$con->commit();

    return \$affectedRows;
  }
}

EOF;
  }

  public function postSave()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF
foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}:save:post') as \$callable)
{
  call_user_func(\$callable, \$this, \$con, \$affectedRows);
}

EOF;
  }

  public function objectMethods()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF

/**
 * Calls methods defined via {@link sfMixer}.
 */
public function __call(\$method, \$arguments)
{
  if (!\$callable = sfMixer::getCallable('Base{$this->getTable()->getPhpName()}:'.\$method))
  {
    throw new sfException(sprintf('Call to undefined method Base{$this->getTable()->getPhpName()}::%s', \$method));
  }

  array_unshift(\$arguments, \$this);

  return call_user_func_array(\$callable, \$arguments);
}

EOF;
  }

  public function staticMethods()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF

/**
 * Returns the name of the hook to call from inside the supplied method.
 *
 * @param string \$method The calling method
 *
 * @return string A hook name for {@link sfMixer}
 *
 * @throws LogicException If the method name is not recognized
 */
static private function getMixerPreSelectHook(\$method)
{
  if (preg_match('/^do(Select|Count)(Join(All(Except)?)?|Stmt)?/', \$method, \$match))
  {
    return sprintf('Base{$this->getTable()->getPhpName()}Peer:%s:%1\$s', 'Count' == \$match[1] ? 'doCount' : \$match[0]);
  }

  throw new LogicException(sprintf('Unrecognized function "%s"', \$method));
}

EOF;
  }

  public function preSelect()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF
foreach (sfMixer::getCallables(self::getMixerPreSelectHook(__FUNCTION__)) as \$sf_hook)
{
  call_user_func(\$sf_hook, 'Base{$this->getTable()->getPhpName()}Peer', \$criteria, \$con);
}

EOF;
  }

  public function objectFilter(& $script)
  {
    if ($this->isDisabled())
    {
      return;
    }

    if ($this->getTable()->getAttribute('behaviors'))
    {
      $script .= $this->getBehaviorsInclude();
    }
  }

  public function peerFilter(& $script)
  {
    if ($this->isDisabled())
    {
      return;
    }

    $doInsertPre = <<<EOF
// symfony_behaviors behavior
    foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}Peer:doInsert:pre') as \$sf_hook)
    {
      if (false !== \$sf_hook_retval = call_user_func(\$sf_hook, 'Base{$this->getTable()->getPhpName()}Peer', \$values, \$con))
      {
        return \$sf_hook_retval;
      }
    }

EOF;
    $doUpdatePre = <<<EOF
// symfony_behaviors behavior
    foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}Peer:doUpdate:pre') as \$sf_hook)
    {
      if (false !== \$sf_hook_retval = call_user_func(\$sf_hook, 'Base{$this->getTable()->getPhpName()}Peer', \$values, \$con))
      {
        return \$sf_hook_retval;
      }
    }

EOF;

    // add doInsert and doUpdate hooks
    $class = new sfClassManipulator($script);
    $class->filterMethod('doInsert', array($this, 'filterDoInsert'));
    $class->wrapMethod('doInsert', $doInsertPre);
    $class->filterMethod('doUpdate', array($this, 'filterDoUpdate'));
    $class->wrapMethod('doUpdate', $doUpdatePre);

    $script = $class->getCode();

    // add symfony behavior configuration file
    if ($this->createBehaviorsFile())
    {
      $script .= $this->getBehaviorsInclude();
    }
  }

  /**
   * Filters the generated doInsert method.
   *
   * @param string $line
   *
   * @return string
   */
  public function filterDoInsert($line)
  {
    if (false !== strpos($line, 'return'))
    {
      $doInsertPost = <<<EOF
    // symfony_behaviors behavior
    foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}Peer:doInsert:post') as \$sf_hook)
    {
      call_user_func(\$sf_hook, 'Base{$this->getTable()->getPhpName()}Peer', \$values, \$con, \$pk);
    }


EOF;

      $line = $doInsertPost.$line;
    }

    return $line;
  }

  /**
   * Filters the generated doUpdate method.
   *
   * @param string $line
   *
   * @return string
   */
  public function filterDoUpdate($line)
  {
    if (false !== strpos($line, 'return'))
    {
      $replace = str_replace('return', '$ret =', $line);
      $doUpdatePost = <<<EOF

    // symfony_behaviors behavior
    foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}Peer:doUpdate:post') as \$sf_hook)
    {
      call_user_func(\$sf_hook, 'Base{$this->getTable()->getPhpName()}Peer', \$values, \$con, \$ret);
    }

    return \$ret;

EOF;

      $line = $replace.$doUpdatePost;
    }

    return $line;
  }

  /**
   * Creates the current model's behaviors configuration file.
   *
   * Any existing behaviors file will be either deleted or overwritten.
   *
   * @return boolean Returns true if the model has behaviors
   */
  protected function createBehaviorsFile()
  {
    $file = $this->getBehaviorsFilePath(true);

    if (file_exists($file))
    {
      unlink($file);
    }

    if ($behaviors = $this->getTable()->getAttribute('behaviors'))
    {
      $code = <<<EOF
<?php

sfPropelBehavior::add('{$this->getTable()->getPhpName()}', %s);

EOF;

      file_put_contents($file, sprintf($code, var_export(unserialize($behaviors), true)));
      return true;
    }
  }

  /**
   * Returns PHP code for including the current model's behaviors configuration file.
   *
   * @return string
   */
  protected function getBehaviorsInclude()
  {
    return <<<EOF

// symfony_behaviors behavior
include_once '{$this->getBehaviorsFilePath()}';

EOF;
  }

  /**
   * Returns the path to the current model's behaviors configuration file.
   *
   * @param boolean $absolute
   *
   * @return string
   */
  protected function getBehaviorsFilePath($absolute = false)
  {
    $base = $absolute ? sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR : '';
    return $base.ClassTools::getFilePath($this->getTable()->getPackage().'.om', sprintf('Base%sBehaviors', $this->getTable()->getPhpName()));
  }
}
