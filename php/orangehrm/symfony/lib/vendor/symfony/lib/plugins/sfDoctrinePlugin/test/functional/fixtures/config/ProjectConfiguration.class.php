<?php

require_once dirname(__FILE__).'/../../../../../../autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enableAllPluginsExcept(array('sfPropelPlugin', 'sfCompat10Plugin'));
  }

  public function initializeDoctrine()
  {
    chdir(sfConfig::get('sf_root_dir'));

    $task = new sfDoctrineBuildAllTask($this->dispatcher, new sfFormatter());
    $task->run(array(), array('--env=test'));
  }

  public function loadFixtures($fixtures)
  {
    $path = sfConfig::get('sf_data_dir') . '/' . $fixtures;
    if ( ! file_exists($path)) {
      throw new sfException('Invalid data fixtures file');
    }
    chdir(sfConfig::get('sf_root_dir'));
    $task = new sfDoctrineLoadDataTask($this->dispatcher, new sfFormatter());
    $task->run(array(), array('--env=test', '--dir=' . $path));
  }

  public function configureDoctrine(Doctrine_Manager $manager)
  {
    $manager->setAttribute('validate', true);

    $options = array('baseClassName' => 'myDoctrineRecord');
    sfConfig::set('doctrine_model_builder_options', $options);
  }

  public function configureDoctrineConnection(Doctrine_Connection $connection)
  {
  }

  public function configureDoctrineConnectionDoctrine2(Doctrine_Connection $connection)
  {
    $connection->setAttribute('validate', false);
  }
}
