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
 * @version    SVN: $Id: sfDoctrineDqlTask.class.php 24625 2009-12-01 00:05:40Z Kris.Wallsmith $
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
      new sfCommandArgument('parameter', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'Query parameter'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('show-sql', null, sfCommandOption::PARAMETER_NONE, 'Show the sql that would be executed'),
      new sfCommandOption('table', null, sfCommandOption::PARAMETER_NONE, 'Return results in table format'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'dql';
    $this->briefDescription = 'Execute a DQL query and view the results';

    $this->detailedDescription = <<<EOF
The [doctrine:dql|INFO] task executes a DQL query and displays the formatted
results:

  [./symfony doctrine:dql "FROM User"|INFO]

You can show the SQL that would be executed by using the [--show-sql|COMMENT] option:

  [./symfony doctrine:dql --show-sql "FROM User"|INFO]

Provide query parameters as additional arguments:

  [./symfony doctrine:dql "FROM User WHERE email LIKE ?" "%symfony-project.com"|INFO]
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
      ->parseDqlQuery($dql);

    $this->logSection('doctrine', 'executing dql query');
    $this->log(sprintf('DQL: %s', $dql));

    if ($options['show-sql'])
    {
      $this->log(sprintf('SQL: %s', $q->getSqlQuery($arguments['parameter'])));
    }

    $count = $q->count($arguments['parameter']);

    if ($count)
    {
      if (!$options['table'])
      {
        $results = $q->fetchArray($arguments['parameter']);

        $this->log(array(
          sprintf('found %s results', number_format($count)),
          sfYaml::dump($results, 4),
        ));
      }
      else
      {
        $results = $q->execute($arguments['parameter'], Doctrine_Core::HYDRATE_SCALAR);

        $headers = array();

        // calculate lengths
        foreach ($results as $result)
        {
          foreach ($result as $field => $value)
          {
            if (!isset($headers[$field]))
            {
              $headers[$field] = 0;
            }

            $headers[$field] = max($headers[$field], strlen($this->renderValue($value)));
          }
        }

        // print header
        $hdr = '|';
        $div = '+';

        foreach ($headers as $field => & $length)
        {
          if ($length < strlen($field))
          {
            $length = strlen($field);
          }

          $hdr .= ' '.str_pad($field, $length).' |';
          $div .= str_repeat('-', $length + 2).'+';
        }

        $this->log(array($div, $hdr, $div));

        // print results
        foreach ($results as $result)
        {
          $line = '|';
          foreach ($result as $field => $value)
          {
            $line .= ' '.str_pad($this->renderValue($value), $headers[$field]).' |';
          }
          $this->log($line);
        }

        $this->log($div);

        // find profiler
        if ($profiler = $q->getConnection()->getListener()->get('symfony_profiler'))
        {
          $events = $profiler->getQueryExecutionEvents();
          $event = array_pop($events);
          $this->log(sprintf('%s results (%s sec)', number_format($count), number_format($event->getElapsedSecs(), 2)));
        }
        else
        {
          $this->log(sprintf('%s results', number_format($count)));
        }

        $this->log('');
      }
    }
    else
    {
      $this->logSection('doctrine', 'no results found');
    }
  }

  /**
   * Renders the supplied value.
   *
   * @param string|null $value
   *
   * @return string
   */
  protected function renderValue($value)
  {
    return null === $value ? 'NULL' : $value;
  }
}
