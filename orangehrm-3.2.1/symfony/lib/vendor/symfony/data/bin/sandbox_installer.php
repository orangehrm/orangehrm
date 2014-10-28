<?php

$this->installDir(dirname(__FILE__).'/sandbox_skeleton');

$this->logSection('install', 'add symfony CLI for Windows users');
$this->getFilesystem()->copy(dirname(__FILE__).'/symfony.bat', sfConfig::get('sf_root_dir').'/symfony.bat');

$this->logSection('install', 'add LICENSE');
$this->getFilesystem()->copy(dirname(__FILE__).'/../../LICENSE', sfConfig::get('sf_root_dir').'/LICENSE');

$this->logSection('install', 'default to sqlite');
$this->runTask('configure:database', sprintf("'sqlite:%s/sandbox.db'", sfConfig::get('sf_data_dir')));

$this->logSection('install', 'create an application');
$this->runTask('generate:app', 'frontend');

$this->logSection('install', 'publish assets');
$this->runTask('plugin:publish-assets');

$this->logSection('install', 'fix sqlite database permissions');
touch(sfConfig::get('sf_data_dir').'/sandbox.db');
chmod(sfConfig::get('sf_data_dir'), 0777);
chmod(sfConfig::get('sf_data_dir').'/sandbox.db', 0777);

$this->logSection('install', 'add an empty file in empty directories');
$seen = array();
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(sfConfig::get('sf_root_dir')), RecursiveIteratorIterator::CHILD_FIRST) as $path => $item)
{
  if ($item->isDir() && !$item->isLink() && !isset($seen[$path]))
  {
    touch($item->getRealPath().'/.sf');
  }

  $seen[$item->getPath()] = true;
}
