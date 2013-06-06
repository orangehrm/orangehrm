<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license informationation, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Create filter form classes for the current model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineBuildFiltersTask.class.php 23927 2009-11-14 16:10:57Z fabien $
 */
class sfDoctrineBuildFiltersTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('model-dir-name', null, sfCommandOption::PARAMETER_REQUIRED, 'The model dir name', 'model'),
      new sfCommandOption('filter-dir-name', null, sfCommandOption::PARAMETER_REQUIRED, 'The filter form dir name', 'filter'),
      new sfCommandOption('generator-class', null, sfCommandOption::PARAMETER_REQUIRED, 'The generator class', 'sfDoctrineFormFilterGenerator'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'build-filters';
    $this->briefDescription = 'Creates filter form classes for the current model';

    $this->detailedDescription = <<<EOF
The [doctrine:build-filters|INFO] task creates form filter classes from the schema:

  [./symfony doctrine:build-filters|INFO]

This task creates form filter classes based on the model. The classes are
created in [lib/doctrine/filter|COMMENT].

This task never overrides custom classes in [lib/doctrine/filter|COMMENT].
It only replaces base classes generated in [lib/doctrine/filter/base|COMMENT].
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('doctrine', 'generating filter form classes');
    $databaseManager = new sfDatabaseManager($this->configuration);
    $generatorManager = new sfGeneratorManager($this->configuration);
    $generatorManager->generate($options['generator-class'], array(
      'model_dir_name'  => $options['model-dir-name'],
      'filter_dir_name' => $options['filter-dir-name'],
    ));

    $properties = parse_ini_file(sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR.'properties.ini', true);

    $constants = array(
      'PROJECT_NAME' => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
      'AUTHOR_NAME'  => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here'
    );

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php');
    $this->getFilesystem()->replaceTokens($finder->in(sfConfig::get('sf_lib_dir').'/filter/'), '##', '##', $constants);

    $this->reloadAutoload();
  }
}