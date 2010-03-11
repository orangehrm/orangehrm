<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Creates database for current model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineDqlTask.class.php 14213 2008-12-19 21:03:13Z Jonathan.Wage $
 */
class sfDoctrineDqlTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('dql_query', sfCommandArgument::REQUIRED, 'The DQL query to execute', null),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('show-sql', null, sfCommandOption::PARAMETER_NONE, 'Show the sql that would be executed'),
    ));

    $this->aliases = array('doctrine-dql');
    $this->namespace = 'doctrine';
    $this->name = 'dql';
    $this->briefDescription = 'Execute a DQL query and view the results';

    $this->detailedDescription = <<<EOF
The [doctrine:data-dql|INFO] task executes a DQL query and display the formatted results:

  [./symfony doctrine:dql "FROM User u"|INFO]

You can show the SQL that would be executed by using the [--dir|COMMENT] option:

  [./symfony doctrine:dql --show-sql "FROM User u"|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $dql = $arguments['dql_query'];

    $q = Doctrine_Query::create()
      ->parseQuery($dql);

    $this->logSection('doctrine', 'executing dql query');

    echo sprintf('DQL: %s', $dql) . "\n";

    if ($options['show-sql']) {
      echo sprintf('SQL: %s', $q->getSql()) . "\n";
    }

    $count = $q->count();

    if ($count)
    {
      echo sprintf('found %s results', $count) . "\n";

      $results = $q->fetchArray();
      $yaml = sfYaml::dump($results, 4);
      $lines = explode("\n", $yaml);
      foreach ($lines as $line)
      {
        echo $line . "\n";
      }
    } else {
      $this->logSection('doctrine', 'no results found');
    }
  }
}