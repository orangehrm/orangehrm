<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A database behavior that adds default symfony behaviors.
 *
 * @package     sfPropelPlugin
 * @subpackage  behavior
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id: SfPropelBehaviorSymfony.php 23737 2009-11-09 23:23:25Z Kris.Wallsmith $
 */
class SfPropelBehaviorSymfony extends SfPropelBehaviorBase
{
  protected $parameters = array(
    'form'   => 'true',
    'filter' => 'true',
  );

  public function modifyDatabase()
  {
    foreach ($this->getDatabase()->getTables() as $table)
    {
      $behaviors = $table->getBehaviors();

      if (!isset($behaviors['symfony']))
      {
        $behavior = clone $this;
        $table->addBehavior($behavior);
      }

      // symfony behaviors
      if (!isset($behaviors['symfony_behaviors']) && $this->getBuildProperty('propel.builder.addBehaviors'))
      {
        $class = Propel::importClass($this->getBuildProperty('propel.behavior.symfony_behaviors.class'));
        $behavior = new $class();
        $behavior->setName('symfony_behaviors');
        $table->addBehavior($behavior);
      }

      // timestampable
      if (!isset($behaviors['symfony_timestampable']))
      {
        $parameters = array();
        foreach ($table->getColumns() as $column)
        {
          if (!isset($parameters['create_column']) && in_array($column->getName(), array('created_at', 'created_on')))
          {
            $parameters['create_column'] = $column->getName();
          }

          if (!isset($parameters['update_column']) && in_array($column->getName(), array('updated_at', 'updated_on')))
          {
            $parameters['update_column'] = $column->getName();
          }
        }

        if ($parameters)
        {
          $class = Propel::importClass($this->getBuildProperty('propel.behavior.symfony_timestampable.class'));
          $behavior = new $class();
          $behavior->setName('symfony_timestampable');
          $behavior->setParameters($parameters);
          $table->addBehavior($behavior);
        }
      }
    }
  }

  public function objectAttributes()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF

const PEER = '{$this->getTable()->getPhpName()}Peer';

EOF;
  }

  public function staticAttributes()
  {
    if ($this->isDisabled())
    {
      return;
    }

    $behaviors = $this->getTable()->getBehaviors();
    $isI18n = isset($behaviors['symfony_i18n']) ? 'true' : 'false';

    return <<<EOF

/**
 * Indicates whether the current model includes I18N.
 */
const IS_I18N = {$isI18n};

EOF;
  }

  public function staticMethods()
  {
    if ($this->isDisabled())
    {
      return;
    }

    $unices = array();
    foreach ($this->getTable()->getUnices() as $unique)
    {
      $unices[] = sprintf("array('%s')", implode("', '", $unique->getColumns()));
    }
    $unices = implode(', ', array_unique($unices));

    return <<<EOF

/**
 * Returns an array of arrays that contain columns in each unique index.
 *
 * @return array
 */
static public function getUniqueColumnNames()
{
  return array({$unices});
}

EOF;
  }
}
