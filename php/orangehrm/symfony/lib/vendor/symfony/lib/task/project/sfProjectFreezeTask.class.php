<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Freezes symfony libraries.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfProjectFreezeTask.class.php 14421 2009-01-02 03:48:52Z Carl.Vondrick $
 */
class sfProjectFreezeTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('symfony_data_dir', sfCommandArgument::REQUIRED, 'The symfony data directory'),
    ));

    $this->aliases = array('freeze');
    $this->namespace = 'project';
    $this->name = 'freeze';
    $this->briefDescription = 'Freezes symfony libraries';

    $this->detailedDescription = <<<EOF
The [project:freeze|INFO] task copies all the symfony core files to
the current project:

  [./symfony project:freeze /path/to/symfony/data/directory|INFO]

The task takes a mandatory argument of the path to the symfony data
directory.

The task also changes [config/config.php|COMMENT] to switch to the
embedded symfony files.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // check that the symfony librairies are not already freeze for this project
    if (is_readable(sfConfig::get('sf_lib_dir').'/symfony'))
    {
      throw new sfCommandException('You can only freeze when lib/symfony is empty.');
    }

    if (is_readable(sfConfig::get('sf_data_dir').'/symfony'))
    {
      throw new sfCommandException('You can only freeze when data/symfony is empty.');
    }

    if (is_readable(sfConfig::get('sf_web_dir').'/sf'))
    {
      throw new sfCommandException('You can only freeze when web/sf is empty.');
    }

    if (is_link(sfConfig::get('sf_web_dir').'/sf'))
    {
      $this->getFilesystem()->remove(sfConfig::get('sf_web_dir').'/sf');
    }

    $symfonyLibDir  = sfConfig::get('sf_symfony_lib_dir');
    $symfonyDataDir = $arguments['symfony_data_dir'];

    if (!is_readable($symfonyDataDir))
    {
      throw new sfCommandException(sprintf('The symfony data dir does not seem to be located at "%s".', $symfonyDataDir));
    }

    $this->logSection('freeze', sprintf('freezing lib found in "%s"', $symfonyLibDir));
    $this->logSection('freeze', sprintf('freezing data found in "%s"', $symfonyDataDir));

    $this->getFilesystem()->mkdirs('lib'.DIRECTORY_SEPARATOR.'symfony');
    $this->getFilesystem()->mkdirs('data'.DIRECTORY_SEPARATOR.'symfony');

    $finder = sfFinder::type('any')->exec(array($this, 'excludeTests'));
    $this->getFilesystem()->mirror($symfonyLibDir, sfConfig::get('sf_lib_dir').'/symfony', $finder);
    $this->getFilesystem()->mirror($symfonyDataDir, sfConfig::get('sf_data_dir').'/symfony', $finder);
    $this->getFilesystem()->rename(sfConfig::get('sf_data_dir').'/symfony/web/sf', sfConfig::get('sf_web_dir').'/sf');

    $publishAssets = new sfPluginPublishAssetsTask($this->dispatcher, $this->formatter);
    $publishAssets->setCommandApplication($this->commandApplication);

    // change symfony path in ProjectConfiguration.class.php
    $config = sfConfig::get('sf_config_dir').'/ProjectConfiguration.class.php';
    $content = file_get_contents($config);
    $content = str_replace('<?php', "<?php\n\n# FROZEN_SF_LIB_DIR: $symfonyLibDir", $content);
    $content = preg_replace('#(\'|")'.preg_quote($symfonyLibDir, '#').'#', "dirname(__FILE__).$1/../lib/symfony", $content);
    file_put_contents($config, $content);

    // re-publish assets
    $publishAssets->run(array(), array('--symfony-lib-dir='.sfConfig::get('sf_lib_dir').'/symfony'));
  }

  public function excludeTests($dir, $entry)
  {
    return false === strpos($dir.'/'.$entry, 'Plugin/test/');
  }
}
