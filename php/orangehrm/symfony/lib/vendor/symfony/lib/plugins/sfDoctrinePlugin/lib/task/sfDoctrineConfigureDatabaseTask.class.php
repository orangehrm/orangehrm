<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Configures the database connection.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineConfigureDatabaseTask.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfDoctrineConfigureDatabaseTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('dsn', sfCommandArgument::REQUIRED, 'The database dsn'),
      new sfCommandArgument('username', sfCommandArgument::OPTIONAL, 'The database username', 'root'),
      new sfCommandArgument('password', sfCommandArgument::OPTIONAL, 'The database password'),
    ));

    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'The environment', 'all'),
      new sfCommandOption('name', null, sfCommandOption::PARAMETER_OPTIONAL, 'The connection name', 'doctrine'),
      new sfCommandOption('class', null, sfCommandOption::PARAMETER_OPTIONAL, 'The database class name', 'sfDoctrineDatabase'),
      new sfCommandOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
    ));

    $this->namespace = 'configure';
    $this->name = 'database';

    $this->briefDescription = 'Configure database DSN';

    $this->detailedDescription = <<<EOF
The [configure:database|INFO] task configures the database DSN
for a project:

  [./symfony configure:database mysql:host=localhost;dbname=example root mYsEcret|INFO]

By default, the task change the configuration for all environment. If you want
to change the dsn for a specific environment, use the [env|COMMENT] option:

  [./symfony configure:database --env=dev mysql:host=localhost;dbname=example_dev root mYsEcret|INFO]

To change the configuration for a specific application, use the [app|COMMENT] option:

  [./symfony configure:database --app=frontend mysql:host=localhost;dbname=example root mYsEcret|INFO]

You can also specify the connection name and the database class name:

  [./symfony configure:database --name=main --class=ProjectDatabase mysql:host=localhost;dbname=example root mYsEcret|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // update databases.yml
    if (null !== $options['app'])
    {
      $file = sfConfig::get('sf_apps_dir').'/'.$options['app'].'/config/databases.yml';
    }
    else
    {
      $file = sfConfig::get('sf_config_dir').'/databases.yml';
    }

    $config = file_exists($file) ? sfYaml::load($file) : array();

    $config[$options['env']][$options['name']] = array(
      'class' => $options['class'],
      'param' => array_merge(isset($config[$options['env']][$options['name']]['param']) ? $config[$options['env']][$options['name']]['param'] : array(), array('dsn' => $arguments['dsn'], 'username' => $arguments['username'], 'password' => $arguments['password'])),
    );

    file_put_contents($file, sfYaml::dump($config, 4));
  }
}
